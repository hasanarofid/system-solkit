<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ResolveTenant
{
    /**
     * Handle an incoming request and resolve tenant by domain, slug, or header.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $tenant = null;

        // 1. Check from route parameter
        $slug = $request->route('tenant_slug') ?? $request->route('tenant');
        if ($slug) {
            $tenant = Tenant::with('storeSetting')->where('slug', $slug)->where('is_active', true)->first();
        }

        // 2. Check from header (API calls)
        if (!$tenant && $request->hasHeader('X-Tenant-Slug')) {
            $tenant = Tenant::with('storeSetting')->where('slug', $request->header('X-Tenant-Slug'))->where('is_active', true)->first();
        }

        // 3. Check from custom domain or subdomain
        if (!$tenant) {
            $host = $request->getHost();
            $tenant = Tenant::with('storeSetting')
                ->where('custom_domain', $host)
                ->where('is_active', true)
                ->first();

            if (!$tenant && str_contains($host, '.')) {
                $subdomain = explode('.', $host)[0];
                if (!in_array($subdomain, ['system', 'www', 'localhost', 'app', 'admin', 'api'])) {
                    $tenant = Tenant::with('storeSetting')
                        ->where('slug', $subdomain)
                        ->where('is_active', true)
                        ->first();
                }
            }
        }

        // 4. Fallback to authenticated user tenant (if in admin panel)
        if (!$tenant && auth()->check() && auth()->user()->tenant_id) {
            $tenant = auth()->user()->tenant()->with('storeSetting')->first();
        }

        if ($tenant) {
            app()->instance('current_tenant', $tenant);
            app()->instance('current_tenant_id', $tenant->id);
            view()->share('currentTenant', $tenant);
        }

        return $next($request);
    }
}
