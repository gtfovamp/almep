@extends('layouts.app')

@php
    $title = ($t['news']['title'] ?? $t['nav']['news'] ?? 'Новости') . ' — Almep Trading';

    $newsField = function ($item, $lang, $base) {
        $en = $base.'_en'; $az = $base.'_az';
        if ($lang === 'en') return $item->$en ?: $item->$base;
        if ($lang === 'az') return $item->$az ?: $item->$base;
        return $item->$base;
    };
    $newsBlocks = function ($item) {
        $b = $item->blocks ?? null;
        if (is_string($b)) { $b = json_decode($b, true) ?: []; }
        return is_array($b) ? $b : [];
    };
    $newsText = function ($blocks, $lang) {
        $parts = [];
        foreach ($blocks as $b) {
            $type = $b['type'] ?? '';
            if (!in_array($type, ['paragraph', 'text', 'heading'])) continue;
            $d = $b['data'] ?? [];
            $parts[] = $lang === 'en' ? ($d['text_en'] ?? $d['text_ru'] ?? '')
                     : ($lang === 'az' ? ($d['text_az'] ?? $d['text_ru'] ?? '') : ($d['text_ru'] ?? ''));
        }
        return trim(implode(' ', $parts));
    };
    // Обрезаем аккуратно по границе слова и используем типографское многоточие,
    // а не пять точек подряд — так текст выглядит опрятнее в карточках любой ширины.
    $newsExcerpt = function ($blocks, $lang, $max = 150) use ($newsText) {
        $text = $newsText(array_filter($blocks, fn($b) => in_array(($b['type'] ?? ''), ['paragraph','text'])), $lang);
        if (mb_strlen($text) <= $max) return $text;
        $cut = mb_substr($text, 0, $max);
        $lastSpace = mb_strrpos($cut, ' ');
        if ($lastSpace !== false) { $cut = mb_substr($cut, 0, $lastSpace); }
        return rtrim($cut, " .,;:") . '…';
    };
    $newsReadTime = function ($blocks, $lang) use ($newsText) {
        $words = preg_split('/\\s+/', $newsText($blocks, $lang), -1, PREG_SPLIT_NO_EMPTY);
        return max(1, (int) ceil(count($words) / 200));
    };
    $newsDate = function ($val) {
        if (empty($val)) return '';
        try { return \Carbon\Carbon::parse($val)->format('d.m.Y'); } catch (\Throwable $e) { return ''; }
    };
@endphp

@section('content')
<main class="site-main">
    <div style="position: relative; background: #FFFFFF;">
        @include('partials.header', ['t' => $t, 'lang' => $lang])
    </div>

    <section class="news-page">
      <div class="news-page__inner">

        <nav class="breadcrumbs" aria-label="breadcrumb">
          <a href="/{{ $lang }}" class="breadcrumbs__item" aria-label="{{ $t['nav']['home'] ?? 'Home' }}">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                        <path d="M2.5 7.5L10 2.5L17.5 7.5V16.25C17.5 16.5815 17.3683 16.8995 17.1339 17.1339C16.8995 17.3683 16.5815 17.5 16.25 17.5H3.75C3.41848 17.5 3.10054 17.3683 2.86612 17.1339C2.6317 16.8995 2.5 16.5815 2.5 16.25V7.5Z" stroke="#696969" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M7.5 17.5V10H12.5V17.5" stroke="#696969" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </a>
                <svg width="4" height="8" viewBox="0 0 4 8" fill="none" class="breadcrumbs__separator" aria-hidden="true">
                    <path d="M1 1L3 4L1 7" stroke="#706F6F" stroke-width="1"/>
                </svg>
          <span class="breadcrumbs__current" aria-current="page">{{ $t['news']['title'] ?? 'Новости' }}</span>
        </nav>

        @php
          $page = $news->currentPage();
          $items = $news->items();
          $featured = ($page === 1 && count($items) > 0) ? $items[0] : null;
          $grid = ($page === 1 && count($items) > 0) ? array_slice($items, 1) : $items;
        @endphp

        @if($featured)
          <section class="latest-news">
            <h1 class="latest-news__title">{{ $t['news']['title'] ?? 'Новости' }}</h1>
            @php
              $fTitle = $newsField($featured, $lang, 'title');
              $fBlocks = $newsBlocks($featured);
            @endphp
            <div class="featured-news">
              <div class="featured-news__image">
                <img src="{{ $featured->cover_image_url }}" alt="{{ $fTitle }}" loading="lazy" width="690" height="390" />
              </div>
              <div class="featured-news__content">
                <div class="featured-news__info">
                  <span class="news-badge">{{ $t['news']['category'] ?? '' }}</span>
                  <h2 class="featured-news__title">{{ $fTitle }}</h2>
                  <p class="featured-news__excerpt">{{ $newsExcerpt($fBlocks, $lang) }}</p>
                  <div class="news-meta">
                    <span class="news-meta__item"><svg width="24" height="24" viewBox="0 0 28 28" fill="none" aria-hidden="true"><rect x="3" y="6" width="22" height="19" rx="2" stroke="#676767" stroke-width="1.5"/><path d="M3 11H25" stroke="#676767" stroke-width="1.5"/><path d="M8 3V6" stroke="#676767" stroke-width="1.5" stroke-linecap="round"/><path d="M20 3V6" stroke="#676767" stroke-width="1.5" stroke-linecap="round"/></svg>{{ $newsDate($featured->published_at) }}</span>
                    <span class="news-meta__item"><svg width="24" height="24" viewBox="0 0 29 29" fill="none" aria-hidden="true"><circle cx="14.5" cy="14.5" r="10" stroke="#676767" stroke-width="1.5"/><path d="M14.5 8V14.5L18.5 18.5" stroke="#676767" stroke-width="1.5" stroke-linecap="round"/></svg>{{ $newsReadTime($fBlocks, $lang) }} {{ $t['news']['min_read'] ?? 'мин' }}</span>
                  </div>
                </div>
                <a href="/{{ $lang }}/news/{{ $featured->id }}" class="read-more">
                  <span>{{ $t['news']['learn_more'] ?? '' }}</span><svg width="27" height="20" viewBox="0 0 27 20" fill="none" aria-hidden="true"><path d="M0.013702 10.394L26.1767 9.68691M26.1767 9.68691L16.8428 0.353097M26.1767 9.68691L16.9401 18.9235" stroke="#1C508F"/></svg>
                </a>
              </div>
            </div>
          </section>
        @endif

        <section class="all-news">
          @if($page > 1 || !$featured)
            <h2 class="all-news__title">{{ $t['news']['title'] ?? 'Новости' }}</h2>
          @endif

          @if(count($grid) === 0 && !$featured)
            <div class="news-page__empty">
              <svg width="48" height="48" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 4h16v16H4z" stroke="#A7A7A7" stroke-width="1.2"/><path d="M8 9h8M8 13h5" stroke="#A7A7A7" stroke-width="1.2" stroke-linecap="round"/></svg>
              <p>{{ $t['news']['no_news'] ?? 'Новости скоро появятся' }}</p>
            </div>
          @else
            <div class="news-grid">
              @foreach($grid as $item)
                @php $iTitle = $newsField($item, $lang, 'title'); $iBlocks = $newsBlocks($item); @endphp
                <article class="news-card">
                  <div class="news-card__image">
                    <img src="{{ $item->cover_image_url }}" alt="{{ $iTitle }}" loading="lazy" width="450" height="300" />
                  </div>
                  <div class="news-card__content">
                    <span class="news-badge">{{ $t['news']['category'] ?? '' }}</span>
                    <h3 class="news-card__title">{{ $iTitle }}</h3>
                    <div class="news-meta">
                      <span class="news-meta__item"><svg width="20" height="20" viewBox="0 0 28 28" fill="none" aria-hidden="true"><rect x="3" y="6" width="22" height="19" rx="2" stroke="#676767" stroke-width="1.5"/><path d="M3 11H25" stroke="#676767" stroke-width="1.5"/><path d="M8 3V6" stroke="#676767" stroke-width="1.5" stroke-linecap="round"/><path d="M20 3V6" stroke="#676767" stroke-width="1.5" stroke-linecap="round"/></svg>{{ $newsDate($item->published_at) }}</span>
                      <span class="news-meta__item"><svg width="20" height="20" viewBox="0 0 29 29" fill="none" aria-hidden="true"><circle cx="14.5" cy="14.5" r="10" stroke="#676767" stroke-width="1.5"/><path d="M14.5 8V14.5L18.5 18.5" stroke="#676767" stroke-width="1.5" stroke-linecap="round"/></svg>{{ $newsReadTime($iBlocks, $lang) }} {{ $t['news']['min_read'] ?? 'мин' }}</span>
                    </div>
                    <p class="news-card__excerpt">{{ $newsExcerpt($iBlocks, $lang) }}</p>
                    <a href="/{{ $lang }}/news/{{ $item->id }}" class="read-more">
                      <span>{{ $t['news']['learn_more'] ?? '' }}</span><svg width="22" height="16" viewBox="0 0 27 20" fill="none" aria-hidden="true"><path d="M0.013702 10.394L26.1767 9.68691M26.1767 9.68691L16.8428 0.353097M26.1767 9.68691L16.9401 18.9235" stroke="#1C508F"/></svg>
                    </a>
                  </div>
                </article>
              @endforeach
            </div>
          @endif

          @if($news->hasPages())
            @php $cur = $news->currentPage(); $last = $news->lastPage(); @endphp
            <nav class="pagination" aria-label="{{ $t['news']['aria_pagination'] ?? 'Пагинация' }}">
              <a href="{{ $cur > 1 ? $news->previousPageUrl() : '#' }}"
                 class="pagination__arrow {{ $cur === 1 ? 'pagination__arrow--disabled' : '' }}"
                 aria-label="{{ $t['news']['aria_prev'] ?? 'Previous page' }}"
                 @if($cur === 1) aria-disabled="true" tabindex="-1" @endif>
                <svg width="28" height="14" viewBox="0 0 37 19" fill="none" aria-hidden="true"><path d="M14 1L1 9.5L14 18" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/><line x1="1" y1="9.5" x2="36.5" y2="9.5" stroke="currentColor" stroke-width="1.4"/></svg>
              </a>
              <div class="pagination__numbers">
                @for($pageNum = 1; $pageNum <= $last; $pageNum++)
                  <a href="{{ $news->url($pageNum) }}"
                     class="pagination__number {{ $pageNum === $cur ? 'pagination__number--active' : '' }}"
                     @if($pageNum === $cur) aria-current="page" @endif>{{ $pageNum }}</a>
                @endfor
              </div>
              <a href="{{ $cur < $last ? $news->nextPageUrl() : '#' }}"
                 class="pagination__arrow {{ $cur === $last ? 'pagination__arrow--disabled' : '' }}"
                 aria-label="{{ $t['news']['aria_next'] ?? 'Next page' }}"
                 @if($cur === $last) aria-disabled="true" tabindex="-1" @endif>
                <svg width="28" height="14" viewBox="0 0 37 19" fill="none" aria-hidden="true"><path d="M23 1L36 9.5L23 18" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/><line x1="0.5" y1="9.5" x2="36" y2="9.5" stroke="currentColor" stroke-width="1.4"/></svg>
              </a>
            </nav>
          @endif
        </section>

      </div>
    </section>

    @include('partials.footer', ['t' => $t, 'lang' => $lang])
</main>
@endsection

@push('styles')
<style>
    /* ── Слой адаптивной безопасности (общий для всех страниц) ── */
    .site-main { display: flex; flex-direction: column; min-height: 100vh; overflow-x: clip; }
    .site-main > section { flex: 0 0 auto; }
    .site-main > section:first-of-type { flex: 1 0 auto; }
    .site-main img, .site-main iframe, .site-main video { max-width: 100%; }
    .site-main *, .site-main *::before, .site-main *::after { box-sizing: border-box; }

  /* ── Токены страницы новостей ── */
  .news-page {
    --accent: #1C508F;
    --badge-bg: #A4C5EE;
    --badge-text: #003F8D;
    --text: #000000;
    --text-muted: #676767;
    --text-secondary: #706F6F;
    --breadcrumb: #2B2B2B;
    --card-shadow: 0px 0px 4px rgba(0, 0, 0, 0.3);
    --card-shadow-hover: 0 10px 28px rgba(28, 80, 143, 0.16);
    --radius-lg: 8px;
    --radius-md: 7px;
    --radius-pill: 17px;

    /* Паддинги берём из той же сетки, что и хедер (var(--hdr-px/--hdr-py)),
       чтобы контент садился краями ровно на те же вертикали, что и хедер,
       а не жил в своей отдельной сетке отступов. Фолбэки — на случай,
       если переменные хедера ещё не объявлены на этой странице. */
    --side-pad: var(--hdr-px, clamp(16px, 6vw, 115px));
    --v-unit: var(--hdr-py, clamp(12px, 2.9vh, 28px));
    --section-gap: clamp(40px, 0vh, 96px);
    --container-max: 1600px;

    width: 100%;
    background: #FFFFFF;
    padding: calc(var(--v-unit) * 1) var(--side-pad) calc(var(--v-unit) * 3.2);
  }

  .news-page__inner {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    width: 100%;
    margin: 0 auto;
    gap: var(--section-gap);
  }

  /* Breadcrumbs */
  .breadcrumbs {
    display: flex;
    flex-direction: row;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
  }

  .breadcrumbs__item {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 20px;
    height: 20px;
    border-radius: 4px;
    transition: opacity 0.2s;
  }

  .breadcrumbs__item:hover { opacity: 0.7; }

  .breadcrumbs__separator { flex-shrink: 0; }

  .breadcrumbs__current {
    font-family: 'Montserrat', sans-serif;
    font-weight: 400;
    font-size: 13px;
    line-height: 16px;
    color: var(--breadcrumb);
  }

  /* Latest News */
  .latest-news {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: clamp(28px, 4vw, 50px);
    width: 100%;
  }

  .latest-news__title,
  .all-news__title {
    font-family: 'Montserrat', sans-serif;
    font-weight: 500;
    font-size: clamp(28px, 4vw, 48px);
    line-height: 110%;
    color: var(--text);
    margin: 0;
    width: 100%;
  }

  .featured-news {
    display: flex;
    flex-direction: row;
    flex-wrap: wrap;
    align-items: stretch;
    gap: clamp(24px, 3vw, 30px);
    width: 100%;
  }

  .featured-news__image {
    flex: 1 1 420px;
    min-width: 300px;
    max-width: 100%;
    aspect-ratio: 690 / 390;
    background: #D8D8D8;
    border-radius: var(--radius-lg);
    overflow: hidden;
  }

  .featured-news__image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
  }

  .featured-news__content {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    justify-content: center;
    gap: clamp(20px, 3vw, 30px);
    flex: 1 1 420px;
    min-width: 300px;
  }

  .featured-news__info {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: clamp(16px, 2vw, 25px);
    width: 100%;
  }

  .news-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 10px 18px;
    background: var(--badge-bg);
    border-radius: var(--radius-pill);
    font-family: 'Raleway', sans-serif;
    font-weight: 400;
    font-size: 14px;
    line-height: 110%;
    color: var(--badge-text);
    white-space: nowrap;
  }

  .news-meta {
    display: flex;
    flex-direction: row;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px 20px;
  }

  .news-meta__item {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-family: 'Raleway', sans-serif;
    font-weight: 400;
    font-size: 14px;
    line-height: 110%;
    color: var(--text-muted);
    white-space: nowrap;
  }

  .news-meta__item svg { flex-shrink: 0; }

  .featured-news__title {
    font-family: 'Montserrat', sans-serif;
    font-weight: 500;
    font-size: clamp(20px, 2.2vw, 24px);
    line-height: 110%;
    letter-spacing: -0.01em;
    color: var(--text);
    margin: 0;
  }

  .featured-news__excerpt {
    font-family: 'Montserrat', sans-serif;
    font-weight: 400;
    font-size: clamp(15px, 1.4vw, 18px);
    line-height: 130%;
    letter-spacing: -0.01em;
    color: var(--text);
    margin: 0;
  }

  .read-more {
    display: inline-flex;
    flex-direction: row;
    align-items: center;
    gap: 11px;
    text-decoration: none;
    transition: opacity 0.2s, gap 0.2s;
    border-radius: 4px;
  }

  .read-more:hover { opacity: 0.75; gap: 15px; }

  .read-more span {
    font-family: 'Montserrat', sans-serif;
    font-weight: 500;
    font-size: clamp(16px, 1.6vw, 20px);
    line-height: 110%;
    color: var(--accent);
  }

  .read-more svg { flex-shrink: 0; }

  /* All News */
  .all-news {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: clamp(28px, 4vw, 50px);
    width: 100%;
  }

  /* Сетка карточек: сама подбирает число колонок под ширину экрана,
     без резких «скачков» на промежуточных разрешениях. */
  .news-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(min(320px, 100%), 1fr));
    gap: clamp(20px, 2.5vw, 30px);
    width: 100%;
  }

  .news-card {
    background: #FFFFFF;
    box-shadow: var(--card-shadow);
    border-radius: var(--radius-md);
    overflow: hidden;
    display: flex;
    flex-direction: column;
    height: 100%;
    transition: transform 0.25s ease, box-shadow 0.25s ease;
  }

  .news-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--card-shadow-hover);
  }

  .news-card__image {
    width: 100%;
    aspect-ratio: 450 / 300;
    background: #D8D8D8;
    overflow: hidden;
  }

  .news-card__image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
    transition: transform 0.35s ease;
  }

  .news-card:hover .news-card__image img { transform: scale(1.05); }

  .news-card__content {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    padding: clamp(18px, 2.5vw, 25px) clamp(18px, 2.5vw, 30px);
    gap: 12px;
    flex: 1;
  }

  .news-card__title {
    font-family: 'Montserrat', sans-serif;
    font-weight: 500;
    font-size: clamp(17px, 1.6vw, 20px);
    line-height: 130%;
    letter-spacing: -0.01em;
    color: var(--text);
    margin: 0;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
  }

  .news-card__excerpt {
    font-family: 'Montserrat', sans-serif;
    font-weight: 400;
    font-size: clamp(14px, 1.3vw, 16px);
    line-height: 130%;
    letter-spacing: -0.01em;
    color: var(--text);
    margin: 0;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .news-card .read-more { margin-top: auto; padding-top: 4px; }
  .news-card .read-more span { font-family: 'Raleway', sans-serif; font-weight: 500; font-size: clamp(15px, 1.4vw, 18px); }

  /* Пустое состояние */
  .news-page__empty {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 16px;
    width: 100%;
    padding: 64px 20px;
    color: var(--text-muted);
    font-family: 'Montserrat', sans-serif;
    font-size: 16px;
    text-align: center;
  }

  /* Pagination */
  .pagination {
    display: flex;
    flex-direction: row;
    align-items: center;
    justify-content: center;
    flex-wrap: wrap;
    gap: clamp(16px, 4vw, 50px);
    width: 100%;
    margin-top: 10px;
  }

  .pagination__arrow {
    background: none;
    border: none;
    cursor: pointer;
    padding: 8px;
    display: inline-flex;
    align-items: center;
    color: var(--text);
    transition: opacity 0.2s;
    text-decoration: none;
    border-radius: 6px;
  }

  .pagination__arrow:hover { opacity: 0.7; }

  .pagination__arrow--disabled {
    opacity: 0.3;
    cursor: not-allowed;
    pointer-events: none;
  }

  .pagination__numbers {
    display: flex;
    flex-direction: row;
    align-items: center;
    flex-wrap: wrap;
    justify-content: center;
    gap: 8px;
    max-width: 100%;
  }

  .pagination__number {
    min-width: 40px;
    height: 40px;
    padding: 0 10px;
    background: transparent;
    border: 1px solid transparent;
    border-radius: 10px;
    font-family: 'Montserrat', sans-serif;
    font-weight: 500;
    font-size: 15px;
    line-height: 20px;
    color: var(--text);
    cursor: pointer;
    transition: background-color 0.2s, border-color 0.2s, color 0.2s;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
  }

  .pagination__number:hover:not(.pagination__number--active) { background: #f0f0f0; }

  .pagination__number--active {
    border-color: var(--accent);
    color: var(--accent);
    font-weight: 700;
    cursor: default;
  }

  /* Клавиатурная доступность */
  .breadcrumbs__item:focus-visible,
  .read-more:focus-visible,
  .pagination__arrow:focus-visible,
  .pagination__number:focus-visible {
    outline: 2px solid var(--accent);
    outline-offset: 3px;
  }

  @media (prefers-reduced-motion: reduce) {
    .news-card,
    .news-card__image img,
    .read-more,
    .pagination__number {
      transition: none !important;
    }
    .news-card:hover { transform: none; }
  }

  /* ── Точки перелома для тонкой доводки типографики/раскладки ── */
  @media (max-width: 1024px) {
    .featured-news__image { flex-basis: 100%; }
    .featured-news__content { flex-basis: 100%; }
  }

  @media (max-width: 480px) {
    .news-badge { font-size: 13px; padding: 8px 14px; }
    .news-meta__item { font-size: 13px; }
    .pagination__number { min-width: 36px; height: 36px; font-size: 14px; }
  }
</style>
@endpush