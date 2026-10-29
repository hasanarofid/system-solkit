<?php

use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\ProductApiController;
use App\Http\Controllers\StorefrontController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Auth endpoints
    Route::post('/auth/login', [AuthApiController::class, 'login']);

    // Public / Storefront API endpoints
    Route::get('/products', [ProductApiController::class, 'index']);
    Route::get('/products/{id}', [ProductApiController::class, 'show']);
    Route::post('/checkout', [StorefrontController::class, 'checkout']);
    Route::post('/chat', [StorefrontController::class, 'chat']);

    // GEO endpoints
    Route::get('/geo/llms.txt', [StorefrontController::class, 'llmsTxt']);
    Route::get('/geo/sitemap.xml', [StorefrontController::class, 'sitemapXml']);
    Route::get('/geo/robots.txt', [StorefrontController::class, 'robotsTxt']);

    // Protected / Tenant Admin endpoints
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/auth/me', [AuthApiController::class, 'me']);
        Route::post('/products', [ProductApiController::class, 'store']);
        Route::put('/products/{id}', [ProductApiController::class, 'update']);
        Route::delete('/products/{id}', [ProductApiController::class, 'destroy']);
    });
});
