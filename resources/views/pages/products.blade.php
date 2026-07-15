@extends('layouts.app')

@php
    // Локализованное поле: name / name_en / name_az
    $loc = function ($m, $base) use ($lang) {
        if (!$m) return '';
        $en = $base.'_en'; $az = $base.'_az';
        if ($lang === 'en') return $m->$en ?: $m->$base;
        if ($lang === 'az') return $m->$az ?: $m->$base;
        return $m->$base;
    };

    $catTitle = $subcategory ? $loc($subcategory, 'name') : ($t['catalog']['title'] ?? 'Каталог');
    $catDesc  = $subcategory ? $loc($subcategory, 'description') : '';
    $title    = $catTitle . ' — Almep Trading';

    $total = method_exists($products, 'total') ? $products->total() : $products->count();
    // Склонение "товар/товара/товаров" для RU
    if ($lang === 'ru') {
        $n10 = $total % 10; $n100 = $total % 100;
        if ($n10 === 1 && $n100 !== 11)                    $word = 'товар';
        elseif ($n10 >= 2 && $n10 <= 4 && ($n100 < 10 || $n100 >= 20)) $word = 'товара';
        else                                               $word = 'товаров';
        $foundLabel = 'Найден'.($word==='товар'?'':($word==='товара'?'о':'о')).' '.$total.' '.$word;
        $foundLabel = ($n10===1 && $n100!==11 ? 'Найден ' : 'Найдено ').$total.' '.$word;
    } else {
        $foundLabel = ($t['catalog']['found'] ?? 'Found').' '.$total;
    }
@endphp

@section('content')
<div style="position: relative; background: #FFFFFF;">
        @include('partials.header', ['t' => $t, 'lang' => $lang])
    </div>
<main class="site-main catalog-page">
  
  <div class="container">

    {{-- Хлебные крошки --}}
    <nav class="breadcrumbs" aria-label="breadcrumb">
      <a href="/{{ $lang }}" class="breadcrumbs__link">{{ $t['nav']['home'] ?? 'Главная' }}</a>
      <span class="breadcrumbs__sep">/</span>
      <a href="/{{ $lang }}#catalog" class="breadcrumbs__link">{{ $t['catalog']['title'] ?? 'Каталог' }}</a>
      @if ($subcategory && $subcategory->category)
        <span class="breadcrumbs__sep">/</span>
        <span class="breadcrumbs__link">{{ $loc($subcategory->category, 'name') }}</span>
      @endif
      <span class="breadcrumbs__sep">/</span>
      <span class="breadcrumbs__current" aria-current="page">{{ $catTitle }}</span>
    </nav>

    <h1 class="catalog-title">{{ $catTitle }}</h1>

    {{-- Шапка подкатегории: изображение + описание --}}
    @if ($subcategory && ($catDesc || $subcategory->image_url))
      <section class="catalog-intro">
        @if ($subcategory->image_url)
          <div class="catalog-intro__image">
            <img src="{{ $subcategory->image_url }}" alt="{{ $catTitle }}" loading="lazy" />
          </div>
        @endif
        @if ($catDesc)
          <div class="catalog-intro__text"><p>{{ $catDesc }}</p></div>
        @endif
      </section>
    @endif

    {{-- Панель: количество + сортировка --}}
    <div class="catalog-toolbar">
      <span class="catalog-toolbar__count">{{ $foundLabel }}</span>
      <form method="get" class="catalog-sort" id="catalogSort">
        @if(request('subcategory'))<input type="hidden" name="subcategory" value="{{ request('subcategory') }}">@endif
        @if(request('type'))<input type="hidden" name="type" value="{{ request('type') }}">@endif
        <label class="catalog-sort__label" for="sortSelect">{{ $t['catalog']['sort'] ?? 'Сортировать:' }}</label>
        <select name="sort" id="sortSelect" class="catalog-sort__select" onchange="document.getElementById('catalogSort').submit()">
          <option value="default" {{ request('sort')==='default'||!request('sort') ? 'selected' : '' }}>{{ $t['catalog']['sort_default'] ?? 'По умолчанию' }}</option>
          <option value="name"    {{ request('sort')==='name'    ? 'selected' : '' }}>{{ $t['catalog']['sort_name'] ?? 'По названию' }}</option>
        </select>
      </form>
    </div>

    {{-- Сетка товаров --}}
    @if ($total > 0)
      <ul class="product-grid">
        @foreach ($products as $product)
          @php
            $img = $product->images->firstWhere('is_primary', true) ?? $product->images->first();
            $imgUrl = $img ? ($img->image_url ?: $img->url) : null;
            $pName = $loc($product, 'name');
          @endphp
          <li class="product-card">
            <a href="/{{ $lang }}/products/{{ $product->id }}" class="product-card__link" aria-label="{{ $pName }}"></a>
            <div class="product-card__image">
              @if ($imgUrl)
                <img src="{{ $imgUrl }}" alt="{{ $pName }}" loading="lazy" />
              @else
                <span class="product-card__noimg" aria-hidden="true">ALMEP</span>
              @endif
              @unless ($product->in_stock)
                <span class="product-card__badge">{{ $t['catalog']['out_of_stock'] ?? 'Под заказ' }}</span>
              @endunless
            </div>
            <div class="product-card__body">
              <h3 class="product-card__title">{{ $pName }}</h3>
              @if ($product->article)
                <div class="product-card__article">
                  <span class="product-card__article-label">{{ $t['catalog']['article'] ?? 'Артикул:' }}</span>
                  <span class="product-card__article-value">{{ $product->article }}</span>
                </div>
              @endif
            </div>
          </li>
        @endforeach
      </ul>

      {{-- Пагинация --}}
      @if (method_exists($products, 'hasPages') && $products->hasPages())
        @php $cur = $products->currentPage(); $last = $products->lastPage(); @endphp
        <nav class="pagination" aria-label="{{ $t['catalog']['aria_pagination'] ?? 'Пагинация' }}">
          <a href="{{ $cur > 1 ? $products->previousPageUrl() : '#' }}"
             class="pagination__arrow {{ $cur <= 1 ? 'is-disabled' : '' }}"
             aria-label="{{ $t['catalog']['aria_prev'] ?? 'Назад' }}" {{ $cur <= 1 ? 'tabindex=-1 aria-disabled=true' : '' }}>‹</a>
          @for ($p = 1; $p <= $last; $p++)
            <a href="{{ $products->url($p) }}" class="pagination__number {{ $p === $cur ? 'is-active' : '' }}">{{ $p }}</a>
          @endfor
          <a href="{{ $cur < $last ? $products->nextPageUrl() : '#' }}"
             class="pagination__arrow {{ $cur >= $last ? 'is-disabled' : '' }}"
             aria-label="{{ $t['catalog']['aria_next'] ?? 'Вперёд' }}" {{ $cur >= $last ? 'tabindex=-1 aria-disabled=true' : '' }}>›</a>
        </nav>
      @endif
    @else
      <div class="catalog-empty">
        <p>{{ $t['catalog']['empty'] ?? 'В этой категории пока нет товаров.' }}</p>
        <a href="/{{ $lang }}#catalog" class="catalog-empty__link">{{ $t['catalog']['back'] ?? 'Вернуться в каталог' }}</a>
      </div>
    @endif

  </div>
</main>
@endsection

@push('styles')
<style>
  .catalog-page .container { max-width: 1280px; margin: 0 auto; padding: 0 24px; }
  .catalog-page { padding: 40px 0 80px; }

  /* Breadcrumbs */
  .breadcrumbs { display: flex; flex-wrap: wrap; align-items: center; gap: 8px; font-size: 13px; color: #8a8f98; margin-bottom: 28px; }
  .breadcrumbs__link { color: #8a8f98; text-decoration: none; transition: color .2s; }
  .breadcrumbs__link:hover { color: #1C508F; }
  .breadcrumbs__sep { color: #c7ccd4; }
  .breadcrumbs__current { color: #1a1b2a; }

  .catalog-title { font-family: 'Montserrat', sans-serif; font-weight: 600; font-size: clamp(26px, 4vw, 40px); color: #1a1b2a; margin: 0 0 28px; }

  /* Intro block */
  .catalog-intro { display: grid; grid-template-columns: minmax(0, 360px) 1fr; gap: 40px; align-items: start; margin-bottom: 44px; }
  .catalog-intro__image { background: #fff; border: 1px solid #ececf0; border-radius: 8px; padding: 28px; display: flex; align-items: center; justify-content: center; }
  .catalog-intro__image img { width: 100%; height: auto; max-height: 280px; object-fit: contain; }
  .catalog-intro__text { font-size: 15px; line-height: 1.7; color: #4a4f5a; }
  .catalog-intro__text p { margin: 0; }

  /* Toolbar */
  .catalog-toolbar { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 16px; padding-bottom: 18px; border-bottom: 1px solid #ececf0; margin-bottom: 32px; }
  .catalog-toolbar__count { font-size: 15px; color: #1a1b2a; font-weight: 500; }
  .catalog-sort { display: flex; align-items: center; gap: 10px; }
  .catalog-sort__label { font-size: 14px; color: #8a8f98; }
  .catalog-sort__select { font-family: inherit; font-size: 14px; color: #1a1b2a; border: 1px solid #d7dbe2; border-radius: 4px; padding: 8px 32px 8px 12px; background: #fff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%231a1b2a' stroke-width='1.5' fill='none' stroke-linecap='round'/%3E%3C/svg%3E") no-repeat right 12px center; appearance: none; cursor: pointer; }
  .catalog-sort__select:focus { outline: none; border-color: #1C508F; }

  /* Product grid */
  .product-grid { list-style: none; margin: 0; padding: 0; display: grid; grid-template-columns: repeat(auto-fill, minmax(230px, 1fr)); gap: 24px; }
  .product-card { position: relative; background: #fff; border: 1px solid #ececf0; border-radius: 8px; overflow: hidden; display: flex; flex-direction: column; transition: box-shadow .25s, transform .25s, border-color .25s; }
  .product-card:hover { box-shadow: 0 12px 30px rgba(16,42,80,.10); transform: translateY(-3px); border-color: #dfe4ec; }
  .product-card__link { position: absolute; inset: 0; z-index: 2; }
  .product-card__image { position: relative; aspect-ratio: 1 / 1; display: flex; align-items: center; justify-content: center; padding: 24px; background: #fafafb; }
  .product-card__image img { max-width: 100%; max-height: 100%; object-fit: contain; }
  .product-card__noimg { font-family: 'Montserrat', sans-serif; font-weight: 700; letter-spacing: 2px; color: #d3d7de; font-size: 22px; }
  .product-card__badge { position: absolute; top: 12px; left: 12px; background: #1C508F; color: #fff; font-size: 11px; font-weight: 600; padding: 4px 10px; border-radius: 3px; z-index: 1; }
  .product-card__body { padding: 16px 18px 20px; display: flex; flex-direction: column; gap: 8px; flex: 1; }
  .product-card__title { font-family: 'Montserrat', sans-serif; font-size: 14px; font-weight: 600; line-height: 1.45; color: #1a1b2a; margin: 0; }
  .product-card__article { font-size: 12px; color: #9aa0aa; margin-top: auto; }
  .product-card__article-label { color: #b6bbc4; }

  /* Pagination */
  .pagination { display: flex; align-items: center; justify-content: center; gap: 8px; margin-top: 48px; }
  .pagination__arrow, .pagination__number { min-width: 42px; height: 42px; display: inline-flex; align-items: center; justify-content: center; border: 1px solid #e2e6ec; border-radius: 6px; color: #1a1b2a; text-decoration: none; font-size: 15px; transition: all .2s; background: #fff; }
  .pagination__number:hover, .pagination__arrow:not(.is-disabled):hover { border-color: #1C508F; color: #1C508F; }
  .pagination__number.is-active { background: #1C508F; border-color: #1C508F; color: #fff; }
  .pagination__arrow.is-disabled { opacity: .4; pointer-events: none; }

  /* Empty */
  .catalog-empty { text-align: center; padding: 60px 20px; color: #8a8f98; }
  .catalog-empty__link { display: inline-block; margin-top: 16px; color: #1C508F; text-decoration: none; font-weight: 600; }

  @media (max-width: 860px) {
    .catalog-intro { grid-template-columns: 1fr; gap: 24px; }
    .catalog-intro__image { max-width: 320px; }
  }
  @media (max-width: 600px) {
    .catalog-page .container { padding: 0 16px; }
    .product-grid { grid-template-columns: repeat(2, 1fr); gap: 14px; }
    .product-card__body { padding: 12px 12px 16px; }
    .catalog-toolbar { flex-direction: column; align-items: flex-start; }
  }
  @media (max-width: 380px) {
    .product-grid { grid-template-columns: 1fr; }
  }
</style>
@endpush


@push('styles')
<style>
  .catalog-page .container { max-width: 1280px; margin: 0 auto; padding: 0 24px; }
  .catalog-page { padding: 40px 0 80px; }

  /* Breadcrumbs */
  .breadcrumbs { display: flex; flex-wrap: wrap; align-items: center; gap: 8px; font-size: 13px; color: #8a8f98; margin-bottom: 28px; }
  .breadcrumbs__link { color: #8a8f98; text-decoration: none; transition: color .2s; }
  .breadcrumbs__link:hover { color: #1C508F; }
  .breadcrumbs__sep { color: #c7ccd4; }
  .breadcrumbs__current { color: #1a1b2a; }

  .catalog-title { font-family: 'Montserrat', sans-serif; font-weight: 600; font-size: clamp(26px, 4vw, 40px); color: #1a1b2a; margin: 0 0 28px; }

  /* Intro block */
  .catalog-intro { display: grid; grid-template-columns: minmax(0, 360px) 1fr; gap: 40px; align-items: start; margin-bottom: 44px; }
  .catalog-intro__image { background: #fff; border: 1px solid #ececf0; border-radius: 8px; padding: 28px; display: flex; align-items: center; justify-content: center; }
  .catalog-intro__image img { width: 100%; height: auto; max-height: 280px; object-fit: contain; }
  .catalog-intro__text { font-size: 15px; line-height: 1.7; color: #4a4f5a; }
  .catalog-intro__text p { margin: 0; }

  /* Toolbar */
  .catalog-toolbar { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 16px; padding-bottom: 18px; border-bottom: 1px solid #ececf0; margin-bottom: 32px; }
  .catalog-toolbar__count { font-size: 15px; color: #1a1b2a; font-weight: 500; }
  .catalog-sort { display: flex; align-items: center; gap: 10px; }
  .catalog-sort__label { font-size: 14px; color: #8a8f98; }
  .catalog-sort__select { font-family: inherit; font-size: 14px; color: #1a1b2a; border: 1px solid #d7dbe2; border-radius: 4px; padding: 8px 32px 8px 12px; background: #fff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%231a1b2a' stroke-width='1.5' fill='none' stroke-linecap='round'/%3E%3C/svg%3E") no-repeat right 12px center; appearance: none; cursor: pointer; }
  .catalog-sort__select:focus { outline: none; border-color: #1C508F; }

  /* Product grid */
  .product-grid { list-style: none; margin: 0; padding: 0; display: grid; grid-template-columns: repeat(auto-fill, minmax(230px, 1fr)); gap: 24px; }
  .product-card { position: relative; background: #fff; border: 1px solid #ececf0; border-radius: 8px; overflow: hidden; display: flex; flex-direction: column; transition: box-shadow .25s, transform .25s, border-color .25s; }
  .product-card:hover { box-shadow: 0 12px 30px rgba(16,42,80,.10); transform: translateY(-3px); border-color: #dfe4ec; }
  .product-card__link { position: absolute; inset: 0; z-index: 2; }
  .product-card__image { position: relative; aspect-ratio: 1 / 1; display: flex; align-items: center; justify-content: center; padding: 24px; background: #fafafb; }
  .product-card__image img { max-width: 100%; max-height: 100%; object-fit: contain; }
  .product-card__noimg { font-family: 'Montserrat', sans-serif; font-weight: 700; letter-spacing: 2px; color: #d3d7de; font-size: 22px; }
  .product-card__badge { position: absolute; top: 12px; left: 12px; background: #1C508F; color: #fff; font-size: 11px; font-weight: 600; padding: 4px 10px; border-radius: 3px; z-index: 1; }
  .product-card__body { padding: 16px 18px 20px; display: flex; flex-direction: column; gap: 8px; flex: 1; }
  .product-card__title { font-family: 'Montserrat', sans-serif; font-size: 14px; font-weight: 600; line-height: 1.45; color: #1a1b2a; margin: 0; }
  .product-card__article { font-size: 12px; color: #9aa0aa; margin-top: auto; }
  .product-card__article-label { color: #b6bbc4; }

  /* Pagination */
  .pagination { display: flex; align-items: center; justify-content: center; gap: 8px; margin-top: 48px; }
  .pagination__arrow, .pagination__number { min-width: 42px; height: 42px; display: inline-flex; align-items: center; justify-content: center; border: 1px solid #e2e6ec; border-radius: 6px; color: #1a1b2a; text-decoration: none; font-size: 15px; transition: all .2s; background: #fff; }
  .pagination__number:hover, .pagination__arrow:not(.is-disabled):hover { border-color: #1C508F; color: #1C508F; }
  .pagination__number.is-active { background: #1C508F; border-color: #1C508F; color: #fff; }
  .pagination__arrow.is-disabled { opacity: .4; pointer-events: none; }

  /* Empty */
  .catalog-empty { text-align: center; padding: 60px 20px; color: #8a8f98; }
  .catalog-empty__link { display: inline-block; margin-top: 16px; color: #1C508F; text-decoration: none; font-weight: 600; }

  @media (max-width: 860px) {
    .catalog-intro { grid-template-columns: 1fr; gap: 24px; }
    .catalog-intro__image { max-width: 320px; }
  }
  @media (max-width: 600px) {
    .catalog-page .container { padding: 0 16px; }
    .product-grid { grid-template-columns: repeat(2, 1fr); gap: 14px; }
    .product-card__body { padding: 12px 12px 16px; }
    .catalog-toolbar { flex-direction: column; align-items: flex-start; }
  }
  @media (max-width: 380px) {
    .product-grid { grid-template-columns: 1fr; }
  }
</style>
@endpush