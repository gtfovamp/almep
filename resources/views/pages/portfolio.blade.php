@extends('layouts.app')

@php
    $title = ($t['portfolio']['title'] ?? $t['nav']['portfolio'] ?? 'Портфолио') . ' — Almep Trading';
@endphp

@section('content')
<main class="site-main">
    <div style="position: relative; background: #FFFFFF;">
        @include('partials.header', ['t' => $t, 'lang' => $lang])
    </div>

    <section class="portfolio-page">
      <div class="portfolio-page__inner">

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
          <span class="breadcrumbs__current" aria-current="page">{{ $t['portfolio']['title'] ?? 'Портфолио' }}</span>
        </nav>

        <div class="portfolio-page__header">
          <h1 class="portfolio-page__title">{{ $t['portfolio']['title'] ?? 'Портфолио' }}</h1>
          <p class="portfolio-page__subtitle">
            {{ $t['portfolio']['subtitle'] ?? 'Реализованные проекты и поставки — примеры нашей работы с промышленными и коммерческими объектами' }}
          </p>
        </div>

        @if($portfolioItems->total() === 0)
          <div class="portfolio-page__empty">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" aria-hidden="true">
              <path d="M4 4h16v16H4z" stroke="#A7A7A7" stroke-width="1.2"/>
              <path d="M8 9h8M8 13h5" stroke="#A7A7A7" stroke-width="1.2" stroke-linecap="round"/>
            </svg>
            <p>{{ $t['portfolio']['no_projects'] ?? 'Проекты скоро появятся' }}</p>
          </div>
        @else
          <div class="portfolio-page__grid">
            @foreach($portfolioItems as $item)
              @php
                $pTitle = $lang === 'en' ? ($item->title_en ?: $item->title) : ($lang === 'az' ? ($item->title_az ?: $item->title) : $item->title);
              @endphp
              <article class="portfolio__card">
                <div class="portfolio__card-image">
                  <img src="{{ $item->image_url }}" alt="{{ $pTitle }}" loading="lazy" width="450" height="300" />
                </div>
                <div class="portfolio__card-caption">
                  <span class="portfolio__card-title">{{ $pTitle }}</span>
                  <span class="portfolio__card-year">{{ $item->year }}</span>
                </div>
              </article>
            @endforeach
          </div>
        @endif

        @if($portfolioItems->hasPages())
          @php
            $cur = $portfolioItems->currentPage();
            $last = $portfolioItems->lastPage();
          @endphp
          <nav class="pagination" aria-label="{{ $t['portfolio']['aria_pagination'] ?? 'Пагинация' }}">
            <a href="{{ $cur > 1 ? $portfolioItems->previousPageUrl() : '#' }}"
               class="pagination__arrow {{ $cur === 1 ? 'pagination__arrow--disabled' : '' }}"
               aria-label="{{ $t['portfolio']['aria_prev'] ?? 'Previous page' }}"
               @if($cur === 1) aria-disabled="true" tabindex="-1" @endif>
              <svg width="28" height="14" viewBox="0 0 37 19" fill="none" aria-hidden="true">
                <path d="M14 1L1 9.5L14 18" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
                <line x1="1" y1="9.5" x2="36.5" y2="9.5" stroke="currentColor" stroke-width="1.4"/>
              </svg>
            </a>
            <div class="pagination__numbers">
              @for($pageNum = 1; $pageNum <= $last; $pageNum++)
                <a href="{{ $portfolioItems->url($pageNum) }}"
                   class="pagination__number {{ $pageNum === $cur ? 'pagination__number--active' : '' }}"
                   @if($pageNum === $cur) aria-current="page" @endif>{{ $pageNum }}</a>
              @endfor
            </div>
            <a href="{{ $cur < $last ? $portfolioItems->nextPageUrl() : '#' }}"
               class="pagination__arrow {{ $cur === $last ? 'pagination__arrow--disabled' : '' }}"
               aria-label="{{ $t['portfolio']['aria_next'] ?? 'Next page' }}"
               @if($cur === $last) aria-disabled="true" tabindex="-1" @endif>
              <svg width="28" height="14" viewBox="0 0 37 19" fill="none" aria-hidden="true">
                <path d="M23 1L36 9.5L23 18" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
                <line x1="0.5" y1="9.5" x2="36" y2="9.5" stroke="currentColor" stroke-width="1.4"/>
              </svg>
            </a>
          </nav>
        @endif

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

  /* ── Токены страницы — те же, что и на news/about/services/partners,
     чтобы страница не выделялась из общей сетки сайта ── */
  .portfolio-page {
    --accent: #1C508F;
    --text: #000000;
    --text-muted: #676767;
    --breadcrumb: #2B2B2B;
    --card-shadow: 0px 0px 4px rgba(0, 0, 0, 0.3);
    --card-shadow-hover: 0 10px 28px rgba(28, 80, 143, 0.16);
    --radius-md: 10px;

    --side-pad: var(--hdr-px, clamp(16px, 6vw, 115px));
    --v-unit: var(--hdr-py, clamp(12px, 2.9vh, 28px));
    --section-gap: clamp(40px, 6vh, 60px);

    width: 100%;
    background: #FFFFFF;
    padding: calc(var(--v-unit) * 1) var(--side-pad) calc(var(--v-unit) * 3.2);
  }

  .portfolio-page__inner {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    width: 100%;
    margin: 0 auto;
    gap: var(--section-gap);
  }

  /* Хлебные крошки */
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

  /* Заголовок + подзаголовок */
  .portfolio-page__header {
    display: flex;
    flex-direction: column;
    gap: 16px;
    width: 100%;
    max-width: 800px;
  }

  .portfolio-page__title {
    font-family: 'Montserrat', sans-serif;
    font-weight: 500;
    font-size: clamp(28px, 4vw, 48px);
    line-height: 110%;
    color: var(--text);
    margin: 0;
  }

  .portfolio-page__subtitle {
    font-family: 'Montserrat', sans-serif;
    font-weight: 400;
    font-size: clamp(15px, 1.4vw, 18px);
    line-height: 130%;
    letter-spacing: -0.01em;
    color: var(--text-muted);
    margin: 0;
  }

  /* Пустое состояние — как на странице новостей */
  .portfolio-page__empty {
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

  /* ═══════════════════════════════════════════════
     СЕТКА — 3 колонки (× 2 строки на страницу),
     плавно схлопывается в 2, потом в 1
  ═══════════════════════════════════════════════ */
  .portfolio-page__grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: clamp(20px, 2.5vw, 25px);
    width: 100%;
  }

  @media (max-width: 1024px) {
    .portfolio-page__grid { grid-template-columns: repeat(2, 1fr); }
  }
  @media (max-width: 620px) {
    .portfolio-page__grid { grid-template-columns: 1fr; }
  }

  /* ═══════════════════════════════════════════════
     КАРТОЧКА — подпись всегда видна (не только по hover),
     чтобы страница не выглядела пустой
  ═══════════════════════════════════════════════ */
  .portfolio__card {
    display: flex;
    flex-direction: column;
    background: #FFFFFF;
    box-shadow: var(--card-shadow);
    border-radius: var(--radius-md);
    overflow: hidden;
    transition: transform 0.25s ease, box-shadow 0.25s ease;
  }

  .portfolio__card:hover {
    transform: translateY(-4px);
    box-shadow: var(--card-shadow-hover);
  }

  .portfolio__card-image {
    width: 100%;
    aspect-ratio: 450 / 300;
    background: #D8D8D8;
    overflow: hidden;
  }

  .portfolio__card-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
    transition: transform 0.35s ease;
  }

  .portfolio__card:hover .portfolio__card-image img {
    transform: scale(1.05);
  }

  .portfolio__card-caption {
    display: flex;
    flex-direction: row;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    padding: clamp(16px, 2vw, 22px) clamp(16px, 2.2vw, 24px);
  }

  .portfolio__card-title {
    font-family: 'Montserrat', sans-serif;
    font-weight: 500;
    font-size: clamp(16px, 1.5vw, 20px);
    line-height: 130%;
    letter-spacing: -0.01em;
    color: var(--text);
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
  }

  .portfolio__card-year {
    font-family: 'Montserrat', sans-serif;
    font-weight: 500;
    font-size: clamp(15px, 1.3vw, 18px);
    line-height: 110%;
    letter-spacing: -0.01em;
    color: var(--accent);
    white-space: nowrap;
    flex-shrink: 0;
  }

  /* ═══════════════════════════════════════════════
     ПАГИНАЦИЯ — как на странице новостей
  ═══════════════════════════════════════════════ */
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
  .pagination__arrow:focus-visible,
  .pagination__number:focus-visible {
    outline: 2px solid var(--accent);
    outline-offset: 3px;
  }

  @media (prefers-reduced-motion: reduce) {
    .portfolio__card,
    .portfolio__card-image img,
    .pagination__number {
      transition: none !important;
    }
    .portfolio__card:hover { transform: none; }
  }

  /* ── Мобильные доводки ── */
  @media (max-width: 768px) {
    .portfolio-page {
      --section-gap: clamp(32px, 8vh, 48px);
    }
    .portfolio__card-caption {
      flex-direction: column;
      align-items: flex-start;
      gap: 6px;
    }
  }

  @media (max-width: 480px) {
    .pagination__number { min-width: 36px; height: 36px; font-size: 14px; }
  }
</style>
@endpush