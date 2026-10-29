<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Tenant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class ProductManagerController extends Controller
{
    protected function getActiveTenant(): Tenant
    {
        if (auth()->check() && auth()->user()->tenant_id) {
            return auth()->user()->tenant;
        }

        return Tenant::firstOrCreate(
            ['slug' => 'solkit-store'],
            [
                'name' => 'Solkit Smart Store',
                'phone_whatsapp' => '6281234567890',
                'email' => 'sales@solkit.tech',
                'address' => 'Jakarta, Indonesia',
                'is_active' => true,
            ]
        );
    }

    public function index(Request $request): Response
    {
        $tenant = $this->getActiveTenant();

        $query = Product::where('tenant_id', $tenant->id)
            ->with('category:id,name')
            ->orderBy('created_at', 'desc');

        if ($request->has('search')) {
            $s = $request->query('search');
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                    ->orWhere('sku', 'like', "%{$s}%");
            });
        }

        $products = $query->paginate(20)->withQueryString();
        $categories = Category::where('tenant_id', $tenant->id)->orWhereNull('tenant_id')->get();

        return Inertia::render('Admin/Products/Index', [
            'tenant' => $tenant,
            'products' => $products,
            'categories' => $categories,
            'filters' => $request->only(['search']),
        ]);
    }

    public function create(): Response
    {
        $tenant = $this->getActiveTenant();
        $categories = Category::where('tenant_id', $tenant->id)->orWhereNull('tenant_id')->get();

        return Inertia::render('Admin/Products/Form', [
            'tenant' => $tenant,
            'categories' => $categories,
            'product' => null,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $tenant = $this->getActiveTenant();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'sku' => 'nullable|string|max:50',
            'price' => 'required|numeric|min:0',
            'compare_at_price' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'weight_grams' => 'nullable|integer|min:0',
            'short_description' => 'nullable|string|max:500',
            'description' => 'nullable|string',
            'thumbnail' => 'nullable|string',
            'is_featured' => 'boolean',
            'status' => 'required|in:draft,published,archived',
        ]);

        $validated['tenant_id'] = $tenant->id;
        $validated['slug'] = Str::slug($validated['name']) . '-' . Str::random(5);

        Product::create($validated);

        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil ditambahkan.');
    }

    public function edit(Product $product): Response
    {
        $tenant = $this->getActiveTenant();
        $categories = Category::where('tenant_id', $tenant->id)->orWhereNull('tenant_id')->get();

        return Inertia::render('Admin/Products/Form', [
            'tenant' => $tenant,
            'categories' => $categories,
            'product' => $product,
        ]);
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'sku' => 'nullable|string|max:50',
            'price' => 'required|numeric|min:0',
            'compare_at_price' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'weight_grams' => 'nullable|integer|min:0',
            'short_description' => 'nullable|string|max:500',
            'description' => 'nullable|string',
            'thumbnail' => 'nullable|string',
            'is_featured' => 'boolean',
            'status' => 'required|in:draft,published,archived',
        ]);

        $product->update($validated);

        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();
        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil dihapus.');
    }
}
