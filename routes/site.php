<?php

use App\Http\Controllers\Web\SiteController;
use App\Support\SiteI18n;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Публичные страницы сайта (ru/en/az)
|--------------------------------------------------------------------------
| Добавь этот блок в свой routes/web.php (или require его оттуда).
| Middleware 'lang' ниже — просто пример ограничения {lang} через where(),
| полноценный middleware для App::setLocale() можно добавить отдельно.
*/

Route::prefix('{lang}')
    ->where(['lang' => implode('|', SiteI18n::LANGS)])
    ->group(function () {
        Route::get('/certificates', [SiteController::class, 'certificates'])->name('site.certificates');

        Route::get('/about', [SiteController::class, 'about'])->name('site.about');
        Route::get('/structure', [SiteController::class, 'structure'])->name('site.structure');

        Route::get('/services', [SiteController::class, 'services'])->name('site.services');

        Route::get('/partners', [SiteController::class, 'partners'])->name('site.partners');

        Route::get('/portfolio', [SiteController::class, 'portfolio'])->name('site.portfolio');

        Route::get('/reviews', [SiteController::class, 'reviews'])->name('site.reviews');

        Route::get('/news', [SiteController::class, 'news'])->name('site.news');
        // /{lang}/news/{id} — отдельный роут, идёт ПОСЛЕ /news, конфликтов нет,
        // т.к. Laravel матчит по количеству сегментов и точному совпадению '/news'.
        Route::get('/news/{id}', [SiteController::class, 'newsShow'])
            ->whereNumber('id')
            ->name('site.news.show');

        Route::get('/contacts', [SiteController::class, 'contacts'])->name('site.contacts');

        // TODO (следующие фазы конверсии):
        // Route::get('/', [SiteController::class, 'index'])->name('site.home');
    });

// Редирект с корня на язык по умолчанию (аналог Astro getStaticPaths по LANGS)
Route::get('/', function () {
    return redirect('/' . SiteI18n::DEFAULT_LANG);
});