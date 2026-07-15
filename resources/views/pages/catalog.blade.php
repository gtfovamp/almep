@extends('layouts.app')

@php
    $loc = function ($m, $base) use ($lang) {
        if (!$m) return '';
        $en = $base.'_en'; $az = $base.'_az';
        if ($lang === 'en') return $m->$en ?: $m->$base;
        if ($lang === 'az') return $m->$az ?: $m->$base;
        return $m->$base;
    };

    // $mode: 'categories' | 'subcategories'
    $pageTitle = $mode === 'subcategories' && isset($category)
        ? $loc($category, 'name')
        : ($t['catalog']['title'] ?? 'Каталог');
    $pageDesc = $mode === 'subcategories' && isset($category) ? $loc($category, 'description') : '';
    $title = $pageTitle . ' — Almep Trading';

    // Склонение "товар" для RU
    $plural = function ($n) use ($lang, $t) {
        if ($lang !== 'ru') return $n.' '.($t['catalog']['items'] ?? 'items');
        $n10 = $n % 10; $n100 = $n % 100;
        if ($n10 === 1 && $n100 !== 11) $w = 'товар';
        elseif ($n10 >= 2 && $n10 <= 4 && ($n100 < 10 || $n100 >= 20)) $w = 'товара';
        else $w = 'товаров';
        return $n.' '.$w;
    };
@endphp

@section('content')
<main class="site-main">
    <div style="position: relative; background: #FFFFFF;">
        @include('partials.header', ['t' => $t, 'lang' => $lang])
    </div>

    <section class="catalog-page">
      <div class="catalog-page__inner">

        {{-- Хлебные крошки --}}
        <nav class="breadcrumbs" aria-label="breadcrumb">
          <a href="/{{ $lang }}" class="breadcrumbs__item" aria-label="{{ $t['nav']['home'] ?? 'Home' }}">
            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" aria-hidden="true">
              <path d="M2.5 7.5L10 2.5L17.5 7.5V16.25C17.5 16.5815 17.3683 16.8995 17.1339 17.1339C16.8995 17.3683 16.5815 17.5 16.25 17.5H3.75C3.41848 17.5 3.10054 17.3683 2.86612 17.1339C2.6317 16.8995 2.5 16.5815 2.5 16.25V7.5Z" stroke="#696969" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
              <path d="M7.5 17.5V10H12.5V17.5" stroke="#696969" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </a>
          <svg width="4" height="8" viewBox="0 0 4 8" fill="none" class="breadcrumbs__separator" aria-hidden="true"><path d="M1 1L3 4L1 7" stroke="#706F6F" stroke-width="1"/></svg>
          @if ($mode === 'subcategories' && isset($category))
            <a href="/{{ $lang }}/catalog" class="breadcrumbs__link">{{ $t['catalog']['title'] ?? 'Каталог' }}</a>
            <svg width="4" height="8" viewBox="0 0 4 8" fill="none" class="breadcrumbs__separator" aria-hidden="true"><path d="M1 1L3 4L1 7" stroke="#706F6F" stroke-width="1"/></svg>
            <span class="breadcrumbs__current" aria-current="page">{{ $pageTitle }}</span>
          @else
            <span class="breadcrumbs__current" aria-current="page">{{ $t['catalog']['title'] ?? 'Каталог' }}</span>
          @endif
        </nav>

        <h1 class="catalog-page__title">{{ $pageTitle }}</h1>

        {{-- Интро категории (фото + описание) --}}
        @if ($pageDesc || (isset($category) && $category->image_url))
          <div class="catalog-intro">
            @if (isset($category) && $category->image_url)
              <div class="catalog-intro__image"><img src="{{ $category->image_url }}" alt="{{ $pageTitle }}" loading="lazy" /></div>
            @endif
            @if ($pageDesc)
              <div class="catalog-intro__text"><p>{{ $pageDesc }}</p></div>
            @endif
          </div>
        @endif

        {{-- Сетка категорий / подкатегорий --}}
        @if ($items->count())
          @if ($mode === 'subcategories')
            <h2 class="catalog-page__subtitle">{{ $t['catalog']['subcategories'] ?? 'Подкатегории' }}</h2>
          @endif
          <div class="cat-grid">
            @foreach ($items as $it)
              @php
                $href = $mode === 'categories'
                    ? '/'.$lang.'/catalog/'.$it->id
                    : '/'.$lang.'/products?subcategory='.$it->id;
                $cnt = $it->products_count ?? ($mode === 'categories' ? ($it->all_products_count ?? 0) : $it->products()->count());
                $name = $loc($it, 'name');
              @endphp
              <a href="{{ $href }}" class="cat-card">
                <div class="cat-card__image">
                  @if ($it->image_url)
                    <img src="{{ $it->image_url }}" alt="{{ $name }}" loading="lazy" />
                  @else
                    <span class="cat-card__noimg">ALMEP</span>
                  @endif
                </div>
                <div class="cat-card__body">
                  <h3 class="cat-card__title">{{ $name }}</h3>
                  <span class="cat-card__count">{{ $plural($cnt) }}</span>
                </div>
              </a>
            @endforeach
          </div>
        @else
          <div class="catalog-page__empty">
            <p>{{ $t['catalog']['empty'] ?? 'В этом разделе пока нет товаров.' }}</p>
          </div>
        @endif

      </div>
    </section>

    @include('partials.footer', ['t' => $t, 'lang' => $lang])
</main>

@push('styles')
<style>
  .site-main { --side-pad: clamp(16px, 6vw, 115px); --accent: #1C508F; --accent-dark: #003F8D; }
  .catalog-page, .product-page { background: #FBFBFB; padding: clamp(28px, 4vw, 56px) 0 clamp(56px, 8vw, 110px); }
  .catalog-page__inner, .product-page__inner { max-width: 1410px; margin: 0 auto; padding: 0 var(--side-pad); }

  /* Breadcrumbs */
  .breadcrumbs { display: flex; flex-wrap: wrap; align-items: center; gap: 10px; margin-bottom: clamp(24px, 3vw, 40px); }
  .breadcrumbs__item { display: inline-flex; align-items: center; }
  .breadcrumbs__link { font-size: 15px; color: #706F6F; text-decoration: none; transition: color .2s; }
  .breadcrumbs__link:hover { color: var(--accent); }
  .breadcrumbs__separator { flex: 0 0 auto; opacity: .6; }
  .breadcrumbs__current { font-size: 15px; color: #252525; max-width: 60vw; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }

  /* H1 / H2 */
  .catalog-page__title, .product-page__title { font-family: 'Montserrat', sans-serif; font-weight: 500; font-size: clamp(28px, 4vw, 48px); line-height: 1.1; color: #252525; margin: 0 0 clamp(24px, 3vw, 40px); }
  .catalog-page__subtitle { font-family: 'Montserrat', sans-serif; font-weight: 500; font-size: clamp(22px, 2.5vw, 32px); color: #252525; margin: clamp(20px,3vw,40px) 0 24px; }

  /* Intro block */
  .catalog-intro { display: grid; grid-template-columns: minmax(0, 420px) 1fr; gap: clamp(24px, 4vw, 60px); align-items: center; margin-bottom: clamp(36px, 5vw, 70px); }
  .catalog-intro__image { background: #fff; border: 1px solid #ECECEC; border-radius: 12px; padding: 32px; display: flex; align-items: center; justify-content: center; }
  .catalog-intro__image img { width: 100%; height: auto; max-height: 320px; object-fit: contain; }
  .catalog-intro__text { font-size: clamp(15px, 1.2vw, 18px); line-height: 1.6; color: #252525; }
  .catalog-intro__text p { margin: 0; }

  /* ── Category / subcategory grid ── */
  .cat-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: clamp(20px, 2.5vw, 40px); }
  .cat-card { display: flex; flex-direction: column; background: #fff; border: 1px solid #ECECEC; border-radius: 12px; overflow: hidden; text-decoration: none; transition: box-shadow .25s, transform .25s, border-color .25s; }
  .cat-card:hover { box-shadow: 0 16px 40px rgba(16,42,80,.12); transform: translateY(-4px); border-color: #dfe4ec; }
  .cat-card__image { aspect-ratio: 4 / 3; background: #F1F1F1; display: flex; align-items: center; justify-content: center; padding: 28px; }
  .cat-card__image img { max-width: 100%; max-height: 100%; object-fit: contain; }
  .cat-card__noimg { font-family: 'Montserrat', sans-serif; font-weight: 700; letter-spacing: 2px; color: #cfd3da; font-size: 26px; }
  .cat-card__body { padding: 20px 24px 26px; display: flex; flex-direction: column; gap: 10px; }
  .cat-card__title { font-family: 'Montserrat', sans-serif; font-weight: 500; font-size: clamp(18px, 1.6vw, 24px); line-height: 1.25; color: #252525; margin: 0; }
  .cat-card__count { font-size: 16px; color: #7D7D7D; }

  /* Toolbar */
  .catalog-toolbar { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 16px; margin-bottom: clamp(28px, 3vw, 44px); }
  .catalog-toolbar__count { font-size: clamp(16px, 1.4vw, 20px); color: #252525; font-weight: 500; }
  .catalog-sort { display: flex; align-items: center; gap: 10px; }
  .catalog-sort__label { font-size: 15px; color: #706F6F; }
  .catalog-sort__select { font-family: inherit; font-size: 15px; color: #252525; border: none; border-bottom: 1px solid #252525; border-radius: 0; padding: 6px 28px 6px 4px; background: transparent url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%23252525' stroke-width='1.5' fill='none' stroke-linecap='round'/%3E%3C/svg%3E") no-repeat right 4px center; appearance: none; cursor: pointer; }
  .catalog-sort__select:focus { outline: none; border-color: var(--accent); }

  /* ── Product grid ── */
  .product-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: clamp(20px, 2.5vw, 40px); }
  .product-card { position: relative; display: flex; flex-direction: column; gap: 14px; text-decoration: none; transition: transform .25s; }
  .product-card:hover { transform: translateY(-4px); }
  .product-card__image { position: relative; aspect-ratio: 1 / 1; display: flex; align-items: center; justify-content: center; padding: 24px; background: #fff; border: 1px solid #000; border-radius: 7px; box-shadow: 0 6px 18px rgba(0,0,0,.06); transition: box-shadow .25s; }
  .product-card:hover .product-card__image { box-shadow: 0 16px 34px rgba(0,0,0,.12); }
  .product-card__image img { max-width: 100%; max-height: 100%; object-fit: contain; }
  .product-card__noimg { font-family: 'Montserrat', sans-serif; font-weight: 700; letter-spacing: 2px; color: #d3d7de; font-size: 22px; }
  .product-card__badge { position: absolute; top: 12px; left: 12px; background: var(--accent); color: #fff; font-size: 11px; font-weight: 600; padding: 4px 10px; border-radius: 3px; }
  .product-card__body { display: flex; flex-direction: column; gap: 8px; padding: 0 4px; }
  .product-card__title { font-family: 'Montserrat', sans-serif; font-weight: 500; font-size: clamp(15px, 1.3vw, 18px); line-height: 1.4; color: #252525; margin: 0; }
  .product-card__article { font-size: 14px; color: #7D7D7D; }
  .product-card__article span { color: #A7A7A7; }

  /* Pagination */
  .pagination { display: flex; align-items: center; justify-content: center; gap: 8px; margin-top: clamp(40px, 5vw, 64px); }
  .pagination__arrow, .pagination__number { min-width: 44px; height: 44px; display: inline-flex; align-items: center; justify-content: center; border: 1px solid #E2E6EC; border-radius: 6px; color: #252525; text-decoration: none; font-size: 15px; background: #fff; transition: all .2s; }
  .pagination__number:hover, .pagination__arrow:not(.is-disabled):hover { border-color: var(--accent); color: var(--accent); }
  .pagination__number.is-active { background: var(--accent); border-color: var(--accent); color: #fff; }
  .pagination__arrow.is-disabled { opacity: .4; pointer-events: none; }

  .catalog-page__empty, .product-page__empty { text-align: center; padding: 60px 20px; color: #7D7D7D; }

  /* ── Product detail ── */
  .product-page__article { display: flex; align-items: center; gap: 8px; font-size: 15px; color: #7D7D7D; margin: -20px 0 32px; }
  .product-page__article strong { color: #252525; font-weight: 600; }
  .product-page__copy { border: none; background: none; cursor: pointer; color: #A7A7A7; font-size: 15px; padding: 2px 4px; transition: color .2s; }
  .product-page__copy:hover, .product-page__copy.is-copied { color: var(--accent); }
  .product-hero { display: grid; grid-template-columns: minmax(0, 560px) 1fr; gap: clamp(32px, 4vw, 64px); align-items: start; margin-bottom: clamp(40px, 5vw, 64px); }
  .product-gallery__main { background: #fff; border: 1px solid #000; border-radius: 7px; aspect-ratio: 4 / 3; display: flex; align-items: center; justify-content: center; padding: 40px; box-shadow: 0 6px 18px rgba(0,0,0,.06); }
  .product-gallery__main img { max-width: 100%; max-height: 100%; object-fit: contain; }
  .product-gallery__noimg { font-family: 'Montserrat', sans-serif; font-weight: 700; letter-spacing: 3px; font-size: 30px; color: #d3d7de; }
  .product-gallery__controls { display: flex; align-items: center; gap: 14px; margin-top: 20px; }
  .product-gallery__thumbs { list-style: none; margin: 0; padding: 0; display: flex; gap: 14px; overflow-x: auto; flex: 1; }
  .product-gallery__thumb { width: 92px; height: 92px; flex: 0 0 auto; background: #fff; border: 1px solid #D9D9D9; border-radius: 7px; padding: 8px; cursor: pointer; transition: border-color .2s; }
  .product-gallery__thumb img { width: 100%; height: 100%; object-fit: contain; }
  .product-gallery__thumb.is-active { border-color: var(--accent); }
  .product-gallery__arrow { width: 46px; height: 46px; flex: 0 0 auto; border: 1px solid #252525; border-radius: 50%; background: #fff; font-size: 22px; color: #252525; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all .2s; }
  .product-gallery__arrow:hover { border-color: var(--accent); color: var(--accent); }
  .product-info__desc { font-size: clamp(15px, 1.2vw, 18px); line-height: 1.7; color: #252525; margin-bottom: 32px; }
  .product-info__desc p { margin: 0; }
  .product-info__cta { display: inline-block; background: var(--accent); color: #fff; font-weight: 600; font-size: 15px; text-decoration: none; padding: 15px 36px; border-radius: 4px; transition: background .2s; }
  .product-info__cta:hover { background: var(--accent-dark); }

  .product-specs { border-top: 1px solid #E6E6E6; padding-top: clamp(32px, 4vw, 48px); margin-bottom: clamp(40px, 5vw, 64px); }
  .product-specs__title { font-family: 'Montserrat', sans-serif; font-weight: 500; font-size: clamp(22px, 2.4vw, 32px); color: #252525; margin: 0 0 28px; }
  .product-specs__table { width: 100%; border-collapse: collapse; max-width: 820px; }
  .product-specs__row { border-bottom: 1px solid #EFEFEF; }
  .product-specs__key { text-align: left; font-weight: 400; font-size: 15px; color: #7D7D7D; padding: 16px 24px 16px 0; vertical-align: top; width: 45%; }
  .product-specs__val { font-size: 15px; color: #252525; padding: 16px 0; font-weight: 500; }

  .product-related { border-top: 1px solid #E6E6E6; padding-top: clamp(32px, 4vw, 48px); }
  .product-related__title { font-family: 'Montserrat', sans-serif; font-weight: 500; font-size: clamp(22px, 2.4vw, 32px); color: #252525; margin: 0 0 28px; }

  @media (max-width: 900px) {
    .catalog-intro { grid-template-columns: 1fr; }
    .catalog-intro__image { max-width: 420px; }
    .product-hero { grid-template-columns: 1fr; }
    .product-gallery__main { max-width: 560px; }
  }
  @media (max-width: 640px) {
    .cat-grid { grid-template-columns: repeat(2, 1fr); gap: 14px; }
    .cat-card__body { padding: 14px 14px 18px; }
    .product-grid { grid-template-columns: repeat(2, 1fr); gap: 14px; }
    .product-card__image { padding: 16px; }
    .catalog-toolbar { flex-direction: column; align-items: flex-start; }
    .product-specs__row { display: grid; grid-template-columns: 1fr; gap: 2px; padding: 12px 0; }
    .product-specs__key, .product-specs__val { width: 100%; padding: 0; }
  }
  @media (max-width: 400px) { .cat-grid, .product-grid { grid-template-columns: 1fr; } }
</style>
@endpush
@endsection