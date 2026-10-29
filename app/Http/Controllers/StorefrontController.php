<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Tenant;
use App\Services\AiSalesAgentService;
use App\Services\GeoService;
use App\Services\WhatsAppCheckoutService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class StorefrontController extends Controller
{
    public function __construct(
        protected GeoService $geoService,
        protected WhatsAppCheckoutService $checkoutService,
        protected AiSalesAgentService $aiSalesAgent
    ) {}

    /**
     * Resolve the active tenant from context or parameter.
     */
    protected function getTenant(?string $slug = null): Tenant
    {
        if (app()->bound('current_tenant')) {
            return app('current_tenant');
        }

        if ($slug) {
            $tenant = Tenant::with(['storeSetting', 'categories'])->where('slug', $slug)->first();
            if ($tenant) {
                return $tenant;
            }
        }

        // Fallback to first active tenant or 404
        $tenant = Tenant::with(['storeSetting', 'categories'])->where('is_active', true)->first();
        if (!$tenant) {
            abort(404, 'Storefront tidak ditemukan.');
        }

        return $tenant;
    }

    /**
     * Render the instant Link-Commerce Storefront.
     */
    public function index(Request $request, ?string $tenant_slug = null): InertiaResponse
    {
        $tenant = $this->getTenant($tenant_slug);
        $setting = $tenant->storeSetting;

        $categorySlug = $request->query('category');
        $search = $request->query('search');

        $query = Product::where('tenant_id', $tenant->id)
            ->where('status', 'published')
            ->orderBy('is_featured', 'desc')
            ->orderBy('order', 'asc');

        if ($categorySlug) {
            $query->whereHas('category', function ($q) use ($categorySlug) {
                $q->where('slug', $categorySlug);
            });
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $products = $query->paginate(24)->withQueryString();
        $categories = $tenant->categories()->where('is_active', true)->get();
        $schemaJsonLd = $this->geoService->generateSchemaOrgJsonLd($tenant);

        return Inertia::render('Storefront/Shop', [
            'tenant' => $tenant,
            'storeSetting' => $setting,
            'products' => $products,
            'categories' => $categories,
            'schemaJsonLd' => $schemaJsonLd,
            'filters' => [
                'category' => $categorySlug,
                'search' => $search,
            ],
        ]);
    }

    /**
     * Detail Product view with Schema.org JSON-LD.
     */
    public function showProduct(Request $request, string $tenant_slug, string $product_slug): InertiaResponse
    {
        $tenant = $this->getTenant($tenant_slug);
        $product = Product::where('tenant_id', $tenant->id)
            ->where('slug', $product_slug)
            ->firstOrFail();

        $schemaJsonLd = $this->geoService->generateSchemaOrgJsonLd($tenant, $product);

        return Inertia::render('Storefront/ProductDetail', [
            'tenant' => $tenant,
            'storeSetting' => $tenant->storeSetting,
            'product' => $product,
            'schemaJsonLd' => $schemaJsonLd,
        ]);
    }

    /**
     * Instant WhatsApp Checkout endpoint.
     */
    public function checkout(Request $request, ?string $tenant_slug = null): JsonResponse
    {
        $tenant = $this->getTenant($tenant_slug);

        $validated = $request->validate([
            'customer_name' => 'required|string|max:100',
            'customer_phone' => 'required|string|max:30',
            'customer_email' => 'nullable|email|max:100',
            'customer_address' => 'nullable|string|max:500',
            'notes' => 'nullable|string|max:300',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|integer',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        try {
            $result = $this->checkoutService->processCheckout($tenant, $validated);
            return response()->json([
                'success' => true,
                'data' => $result,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * AI Sales Consultant Chat Endpoint.
     */
    public function chat(Request $request, ?string $tenant_slug = null): JsonResponse
    {
        $tenant = $this->getTenant($tenant_slug);

        $validated = $request->validate([
            'message' => 'required|string|max:1000',
            'history' => 'nullable|array',
        ]);

        $response = $this->aiSalesAgent->respond(
            $tenant,
            $validated['message'],
            $validated['history'] ?? []
        );

        return response()->json([
            'success' => true,
            'data' => $response,
        ]);
    }

    /**
     * GEO Module: llms.txt Generator.
     */
    public function llmsTxt(?string $tenant_slug = null): Response
    {
        $tenant = $this->getTenant($tenant_slug);
        $content = $this->geoService->generateLlmsTxt($tenant);

        return response($content, 200, [
            'Content-Type' => 'text/plain; charset=UTF-8',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }

    /**
     * GEO Module: sitemap.xml Generator.
     */
    public function sitemapXml(?string $tenant_slug = null): Response
    {
        $tenant = $this->getTenant($tenant_slug);
        $content = $this->geoService->generateSitemapXml($tenant);

        return response($content, 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }

    /**
     * GEO Module: robots.txt Generator.
     */
    public function robotsTxt(?string $tenant_slug = null): Response
    {
        $tenant = $this->getTenant($tenant_slug);
        $content = $this->geoService->generateRobotsTxt($tenant);

        return response($content, 200, [
            'Content-Type' => 'text/plain; charset=UTF-8',
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }
}
