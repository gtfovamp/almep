<?php

use App\Http\Controllers\Web\SiteController;
use App\Support\SiteI18n;
use Illuminate\Support\Facades\Route;

Route::prefix('{lang}')
    ->where(['lang' => implode('|', SiteI18n::LANGS)])
    ->group(function () {

        Route::get('/', [SiteController::class, 'index'])->name('site.home');

        Route::get('/about', [SiteController::class, 'about'])->name('site.about');
        Route::get('/structure', [SiteController::class, 'structure'])->name('site.structure');
        Route::get('/services', [SiteController::class, 'services'])->name('site.services');
        Route::get('/partners', [SiteController::class, 'partners'])->name('site.partners');
        Route::get('/portfolio', [SiteController::class, 'portfolio'])->name('site.portfolio');
        Route::get('/reviews', [SiteController::class, 'reviews'])->name('site.reviews');
        Route::get('/certificates', [SiteController::class, 'certificates'])->name('site.certificates');
        Route::get('/contacts', [SiteController::class, 'contacts'])->name('site.contacts');

        Route::get('/news', [SiteController::class, 'news'])->name('site.news');
        Route::get('/news/{id}', [SiteController::class, 'newsShow'])
            ->whereNumber('id')->name('site.news.show');

        Route::get('/blog', [SiteController::class, 'blog'])->name('site.blog');
        Route::get('/blog/{id}', [SiteController::class, 'blogShow'])
            ->whereNumber('id')->name('site.blog.show');

        Route::get('/catalog', [SiteController::class, 'catalog'])->name('site.catalog');
        Route::get('/catalog/{category}', [SiteController::class, 'catalog'])
            ->whereNumber('category')->name('site.catalog.category');
        Route::get('/products', [SiteController::class, 'products'])->name('site.products');
        Route::get('/products/{id}', [SiteController::class, 'product'])
            ->whereNumber('id')->name('site.product');

    });

Route::get('/', function () {
    return redirect('/' . SiteI18n::DEFAULT_LANG);
});
