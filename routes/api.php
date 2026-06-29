<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\SubcategoryController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ProductTypeController;
use App\Http\Controllers\Api\ProductImageController;
use App\Http\Controllers\Api\ProductSpecificationController;
use App\Http\Controllers\Api\BlogController;
use App\Http\Controllers\Api\NewsController;
use App\Http\Controllers\Api\CertificateController;
use App\Http\Controllers\Api\PartnerController;
use App\Http\Controllers\Api\PortfolioController;
use App\Http\Controllers\Api\TestimonialController;
use App\Http\Controllers\Api\ConsultationController;
use App\Http\Controllers\Api\Admin\AuthController;

/*
|--------------------------------------------------------------------------
| PUBLIC API (no auth) — for the website front-end
|--------------------------------------------------------------------------
*/
Route::prefix('')->group(function () {
    // Catalog
    Route::get('categories', [CategoryController::class, 'index']);
    Route::get('categories/{id}', [CategoryController::class, 'show']);

    Route::get('subcategories', [SubcategoryController::class, 'index']);
    Route::get('subcategories/{id}', [SubcategoryController::class, 'show']);

    Route::get('product-types', [ProductTypeController::class, 'index']);
    Route::get('product-types/{id}', [ProductTypeController::class, 'show']);

    Route::get('products', [ProductController::class, 'index']);
    Route::get('products/{id}', [ProductController::class, 'show']);
    Route::get('products/{product}/images', [ProductImageController::class, 'index']);
    Route::get('products/{product}/specifications', [ProductSpecificationController::class, 'index']);

    // Content
    Route::get('blog', [BlogController::class, 'publicIndex']);
    Route::get('blog/{id}', [BlogController::class, 'publicShow']);
    Route::get('news', [NewsController::class, 'publicIndex']);
    Route::get('news/{id}', [NewsController::class, 'publicShow']);

    Route::get('certificates', [CertificateController::class, 'index']);
    Route::get('certificates/{id}', [CertificateController::class, 'show']);

    Route::get('partners', [PartnerController::class, 'index']);
    Route::get('partners/{id}', [PartnerController::class, 'show']);

    Route::get('portfolio', [PortfolioController::class, 'index']);
    Route::get('portfolio/{id}', [PortfolioController::class, 'show']);

    Route::get('testimonials', [TestimonialController::class, 'publicIndex']);

    // Public submissions
    Route::post('consultations', [ConsultationController::class, 'store']);
    Route::post('testimonials', [TestimonialController::class, 'publicStore']);

    // Admin login (public endpoint)
    Route::post('admin/login', [AuthController::class, 'login']);
});

/*
|--------------------------------------------------------------------------
| ADMIN API (token auth via admin_sessions)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->middleware('admin.auth')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('me', [AuthController::class, 'me']);

    // Admin users
    Route::get('admin-users', [AuthController::class, 'listUsers']);
    Route::post('admin-users', [AuthController::class, 'createUser']);
    Route::put('admin-users/{id}', [AuthController::class, 'updateUser']);
    Route::delete('admin-users/{id}', [AuthController::class, 'deleteUser']);

    // Categories
    Route::post('categories/reorder', [CategoryController::class, 'reorder']);
    Route::apiResource('categories', CategoryController::class)->except(['index', 'show']);
    Route::get('categories', [CategoryController::class, 'index']);
    Route::get('categories/{id}', [CategoryController::class, 'show']);

    // Subcategories
    Route::post('subcategories/reorder', [SubcategoryController::class, 'reorder']);
    Route::apiResource('subcategories', SubcategoryController::class)->except(['index', 'show']);
    Route::get('subcategories', [SubcategoryController::class, 'index']);
    Route::get('subcategories/{id}', [SubcategoryController::class, 'show']);

    // Product types
    Route::apiResource('product-types', ProductTypeController::class)->except(['index', 'show']);
    Route::get('product-types', [ProductTypeController::class, 'index']);
    Route::get('product-types/{id}', [ProductTypeController::class, 'show']);

    // Products
    Route::post('products/reorder', [ProductController::class, 'reorder']);
    Route::apiResource('products', ProductController::class)->except(['index', 'show']);
    Route::get('products', [ProductController::class, 'index']);
    Route::get('products/{id}', [ProductController::class, 'show']);

    // Product images
    Route::get('products/{product}/images', [ProductImageController::class, 'index']);
    Route::post('products/{product}/images', [ProductImageController::class, 'store']);
    Route::put('product-images/{id}', [ProductImageController::class, 'update']);
    Route::delete('product-images/{id}', [ProductImageController::class, 'destroy']);

    // Product specifications
    Route::get('products/{product}/specifications', [ProductSpecificationController::class, 'index']);
    Route::post('products/{product}/specifications', [ProductSpecificationController::class, 'store']);
    Route::post('products/{product}/specifications/bulk', [ProductSpecificationController::class, 'bulkStore']);
    Route::put('product-specifications/{id}', [ProductSpecificationController::class, 'update']);
    Route::delete('product-specifications/{id}', [ProductSpecificationController::class, 'destroy']);

    // Blog
    Route::post('blog/reorder', [BlogController::class, 'reorder']);
    Route::apiResource('blog', BlogController::class);

    // News
    Route::post('news/reorder', [NewsController::class, 'reorder']);
    Route::apiResource('news', NewsController::class);

    // Certificates
    Route::post('certificates/reorder', [CertificateController::class, 'reorder']);
    Route::apiResource('certificates', CertificateController::class);

    // Partners
    Route::post('partners/reorder', [PartnerController::class, 'reorder']);
    Route::apiResource('partners', PartnerController::class);

    // Portfolio
    Route::post('portfolio/reorder', [PortfolioController::class, 'reorder']);
    Route::apiResource('portfolio', PortfolioController::class);

    // Testimonials
    Route::post('testimonials/reorder', [TestimonialController::class, 'reorder']);
    Route::patch('testimonials/{id}/approve', [TestimonialController::class, 'approve']);
    Route::patch('testimonials/{id}/unapprove', [TestimonialController::class, 'unapprove']);
    Route::apiResource('testimonials', TestimonialController::class);

    // Consultations (view + delete only)
    Route::get('consultations', [ConsultationController::class, 'index']);
    Route::get('consultations/{id}', [ConsultationController::class, 'show']);
    Route::delete('consultations/{id}', [ConsultationController::class, 'destroy']);
});
