<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthWebController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CategoryWebController;
use App\Http\Controllers\Admin\SubcategoryWebController;
use App\Http\Controllers\Admin\ProductTypeWebController;
use App\Http\Controllers\Admin\ProductWebController;
use App\Http\Controllers\Admin\PartnerWebController;
use App\Http\Controllers\Admin\PortfolioWebController;
use App\Http\Controllers\Admin\CertificateWebController;
use App\Http\Controllers\Admin\TestimonialWebController;
use App\Http\Controllers\Admin\BlogWebController;
use App\Http\Controllers\Admin\NewsWebController;
use App\Http\Controllers\Admin\ConsultationWebController;
use App\Http\Controllers\Admin\ImageUploadController;

Route::get('/', fn() => redirect('/admin'));

// ---- Auth (public) ----
Route::get('/admin/login', [AuthWebController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [AuthWebController::class, 'login'])->name('admin.login.post');
Route::post('/admin/logout', [AuthWebController::class, 'logout'])->name('admin.logout');

// ---- Protected admin panel ----
Route::prefix('admin')->middleware('admin.session')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('admin.dashboard');

    // Async image upload (gallery + block editor)
    Route::post('upload-image', [ImageUploadController::class, 'upload']);

    $resources = [
        'categories'     => CategoryWebController::class,
        'subcategories'  => SubcategoryWebController::class,
        'product-types'  => ProductTypeWebController::class,
        'products'       => ProductWebController::class,
        'partners'       => PartnerWebController::class,
        'portfolio'      => PortfolioWebController::class,
        'certificates'   => CertificateWebController::class,
        'testimonials'   => TestimonialWebController::class,
        'blog'           => BlogWebController::class,
        'news'           => NewsWebController::class,
    ];
    foreach ($resources as $slug => $controller) {
        Route::get($slug, [$controller, 'index']);
        Route::get($slug . '/create', [$controller, 'create']);
        Route::post($slug, [$controller, 'store']);
        Route::get($slug . '/{id}/edit', [$controller, 'edit']);
        Route::put($slug . '/{id}', [$controller, 'update']);
        Route::delete($slug . '/{id}', [$controller, 'destroy']);
    }

    // Consultations: list + delete only
    Route::get('consultations', [ConsultationWebController::class, 'index']);
    Route::delete('consultations/{id}', [ConsultationWebController::class, 'destroy']);
});