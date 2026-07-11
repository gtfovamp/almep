@extends('layouts.app')

@php
    $title = ($t['reviews']['title'] ?? $t['nav']['reviews'] ?? 'Отзывы') . ' — Almep Trading';
@endphp

@section('content')
<main class="site-main">
    <div style="position: relative; background: #FFFFFF;">
        @include('partials.header', ['t' => $t, 'lang' => $lang])
    </div>

    <section class="reviews">
      <div class="reviews__inner">

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
          <span class="breadcrumbs__current" aria-current="page">{{ $t['reviews']['title'] ?? 'Отзывы' }}</span>
        </nav>

        <div class="reviews__content">

          <h1 class="reviews__title">{{ $t['reviews']['title'] ?? 'Отзывы' }}</h1>

          <div class="reviews__stats">
            <div class="reviews__stat">
              <div class="reviews__stat-value">98%</div>
              <div class="reviews__stat-label">{{ $t['whyus']['stats'][0]['label'] ?? '' }}</div>
            </div>
            <div class="reviews__stat">
              <div class="reviews__stat-value">4.9/5</div>
              <div class="reviews__stat-label">{{ $t['reviews']['stat_rating'] ?? '' }}</div>
            </div>
            <div class="reviews__stat">
              <div class="reviews__stat-value">{{ $reviews->total() }}</div>
              <div class="reviews__stat-label">{{ $t['reviews']['stat_reviews'] ?? '' }}</div>
            </div>
          </div>

          @if($reviews->total() === 0)
            <div class="reviews__empty">
              <svg width="48" height="48" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M4 4h16v16H4z" stroke="#A7A7A7" stroke-width="1.2"/>
                <path d="M8 9h8M8 13h5" stroke="#A7A7A7" stroke-width="1.2" stroke-linecap="round"/>
              </svg>
              <p>{{ $t['reviews']['no_reviews'] ?? 'Отзывы скоро появятся' }}</p>
            </div>
          @else
            <div class="reviews__grid">
              @foreach($reviews as $review)
                @php
                  $rText = $lang === 'en' ? ($review->text_en ?: $review->text) : ($lang === 'az' ? ($review->text_az ?: $review->text) : $review->text);
                  $rAuthor = $lang === 'en' ? ($review->name_en ?: $review->name) : ($lang === 'az' ? ($review->name_az ?: $review->name) : $review->name);
                @endphp
                <div class="reviews__card">
                  <div class="reviews__quote" aria-hidden="true">"</div>
                  <div class="reviews__card-content">
                    <div class="reviews__text-wrapper">
                      <p class="reviews__text">{{ $rText }}</p>
                      <div class="reviews__divider"></div>
                    </div>
                    <div class="reviews__author">{{ $rAuthor }}</div>
                  </div>
                </div>
              @endforeach
            </div>
          @endif

          @if($reviews->hasPages())
            @php $cur = $reviews->currentPage(); $last = $reviews->lastPage(); @endphp
            <nav class="pagination" aria-label="{{ $t['reviews']['aria_pagination'] ?? 'Пагинация' }}">
              <a href="{{ $cur > 1 ? $reviews->previousPageUrl() : '#' }}"
                 class="pagination__arrow {{ $cur === 1 ? 'pagination__arrow--disabled' : '' }}"
                 aria-label="{{ $t['reviews']['aria_prev'] ?? 'Previous page' }}"
                 @if($cur === 1) aria-disabled="true" tabindex="-1" @endif>
                <svg width="28" height="14" viewBox="0 0 37 19" fill="none" aria-hidden="true">
                  <path d="M14 1L1 9.5L14 18" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
                  <line x1="1" y1="9.5" x2="36.5" y2="9.5" stroke="currentColor" stroke-width="1.4"/>
                </svg>
              </a>
              <div class="pagination__numbers">
                @for($pageNum = 1; $pageNum <= $last; $pageNum++)
                  <a href="{{ $reviews->url($pageNum) }}"
                     class="pagination__number {{ $pageNum === $cur ? 'pagination__number--active' : '' }}"
                     @if($pageNum === $cur) aria-current="page" @endif>{{ $pageNum }}</a>
                @endfor
              </div>
              <a href="{{ $cur < $last ? $reviews->nextPageUrl() : '#' }}"
                 class="pagination__arrow {{ $cur === $last ? 'pagination__arrow--disabled' : '' }}"
                 aria-label="{{ $t['reviews']['aria_next'] ?? 'Next page' }}"
                 @if($cur === $last) aria-disabled="true" tabindex="-1" @endif>
                <svg width="28" height="14" viewBox="0 0 37 19" fill="none" aria-hidden="true">
                  <path d="M23 1L36 9.5L23 18" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
                  <line x1="0.5" y1="9.5" x2="36" y2="9.5" stroke="currentColor" stroke-width="1.4"/>
                </svg>
              </a>
            </nav>
          @endif

          <div class="reviews__actions">
            <a href="/{{ $lang }}#consultation" class="reviews__btn reviews__btn--primary">{{ $t['reviews']['btn_consultation'] ?? '' }}</a>
          </div>

        </div>
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

  /* ── Токены страницы — те же, что и на news/about/services/partners/portfolio ── */
  .reviews {
    --accent: #1C508F;
    --accent-hover: #174480;
    --text: #000000;
    --text-muted: #666666;
    --breadcrumb: #2B2B2B;
    --card-shadow-hover: 0 10px 28px rgba(28, 80, 143, 0.16);

    --side-pad: var(--hdr-px, clamp(16px, 6vw, 115px));
    --v-unit: var(--hdr-py, clamp(12px, 2.9vh, 28px));
    --section-gap: clamp(40px, 6vh, 60px);

    width: 100%;
    background: #FFFFFF;
    padding: calc(var(--v-unit) * 1) var(--side-pad) calc(var(--v-unit) * 3.2);
  }

  .reviews__inner {
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

  .reviews__content {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: clamp(36px, 5vw, 50px);
    width: 100%;
  }

  /* Заголовок */
  .reviews__title {
    font-family: 'Montserrat', sans-serif;
    font-weight: 500;
    font-size: clamp(32px, 5vw, 48px);
    line-height: 110%;
    text-align: center;
    color: var(--text);
    margin: 0;
    width: 100%;
  }

  /* ═══════════════════════════════════════════════
     Статистика — плавно оборачивается, без фикс. gap
  ═══════════════════════════════════════════════ */
  .reviews__stats {
    display: flex;
    flex-direction: row;
    flex-wrap: wrap;
    align-items: center;
    justify-content: center;
    gap: clamp(40px, 8vw, 145px);
    width: 100%;
  }

  .reviews__stat {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: clamp(14px, 2vw, 25px);
  }

  .reviews__stat-value {
    font-family: 'Raleway', sans-serif;
    font-weight: 400;
    font-size: clamp(30px, 4vw, 45px);
    line-height: 110%;
    text-align: center;
    color: var(--text);
  }

  .reviews__stat-label {
    font-family: 'Montserrat', sans-serif;
    font-weight: 400;
    font-size: clamp(15px, 1.4vw, 18px);
    line-height: 130%;
    letter-spacing: -0.01em;
    color: var(--text);
    text-align: center;
  }

  /* Пустое состояние */
  .reviews__empty {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 16px;
    width: 100%;
    padding: 60px 20px;
    color: var(--text-muted);
    font-family: 'Montserrat', sans-serif;
    font-size: 16px;
    text-align: center;
  }

  /* ═══════════════════════════════════════════════
     Сетка отзывов — 3 колонки, плавно схлопывается
  ═══════════════════════════════════════════════ */
  .reviews__grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(min(340px, 100%), 1fr));
    gap: clamp(20px, 2.5vw, 30px);
    width: 100%;
  }

  .reviews__card {
    position: relative;
    width: 100%;
    height: 100%;
    min-height: clamp(340px, 30vw, 420px);
    background: #FFFFFF;
    border: 1px solid rgba(0, 0, 0, 0.15);
    border-radius: 7px;
    box-sizing: border-box;
    padding: clamp(20px, 3vw, 25px);
    display: flex;
    flex-direction: column;
    transition: box-shadow 0.25s ease, transform 0.25s ease, border-color 0.25s ease;
  }

  .reviews__card:hover {
    box-shadow: var(--card-shadow-hover);
    border-color: rgba(28, 80, 143, 0.25);
    transform: translateY(-3px);
  }

  .reviews__quote {
    font-family: 'Raleway', sans-serif;
    font-weight: 400;
    font-size: clamp(80px, 10vw, 128px);
    line-height: 110%;
    color: var(--text);
    user-select: none;
    pointer-events: none;
    margin: 0;
    height: clamp(60px, 8vw, 91px);
  }

  .reviews__card-content {
    display: flex;
    flex-direction: column;
    gap: clamp(16px, 2vw, 22px);
    flex: 1;
    height: 100%;
  }

  .reviews__text-wrapper {
    display: flex;
    flex-direction: column;
    flex: 1;
  }

  .reviews__text {
    font-family: 'Montserrat', sans-serif;
    font-weight: 400;
    font-size: clamp(15px, 1.4vw, 18px);
    line-height: 130%;
    letter-spacing: -0.01em;
    color: var(--text);
    margin: 0;
    overflow: hidden;
    display: -webkit-box;
    -webkit-line-clamp: 8;
    -webkit-box-orient: vertical;
    flex: 1;
  }

  .reviews__divider {
    width: 100%;
    height: 3px;
    background: #000000;
    margin: 20px 0 0 0;
    flex-shrink: 0;
  }

  .reviews__author {
    font-family: 'Montserrat', sans-serif;
    font-weight: 500;
    font-size: clamp(18px, 1.8vw, 24px);
    line-height: 110%;
    letter-spacing: -0.01em;
    color: var(--text);
    margin: 0;
  }

  /* ═══════════════════════════════════════════════
     Пагинация — как на news/portfolio
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

  /* ═══════════════════════════════════════════════
     Кнопки внизу — было gap: 752px (баг из Figma,
     ломал вёрстку на любом экране уже до 1500px)
  ═══════════════════════════════════════════════ */
  .reviews__actions {
    display: flex;
    flex-direction: row;
    flex-wrap: wrap;
    justify-content: center;
    align-items: center;
    gap: clamp(20px, 4vw, 40px);
    width: 100%;
  }

  .reviews__btn {
    width: clamp(220px, 25vw, 330px);
    height: clamp(64px, 8vw, 85px);
    border-radius: 9px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: 'Montserrat', sans-serif;
    font-weight: 400;
    font-size: clamp(16px, 1.4vw, 18px);
    line-height: 130%;
    text-align: center;
    letter-spacing: -0.01em;
    text-decoration: none;
    transition: all 0.2s;
  }

  .reviews__btn--outline {
    border: 1px solid var(--accent);
    color: var(--accent);
    background: transparent;
  }

  .reviews__btn--outline:hover {
    background: #f5f5f5;
  }

  .reviews__btn--primary {
    background: var(--accent);
    box-shadow: 0px 0px 4px rgba(0, 0, 0, 0.3);
    color: #FFFFFF;
  }

  .reviews__btn--primary:hover {
    background: var(--accent-hover);
    transform: translateY(-2px);
  }

  /* Клавиатурная доступность */
  .breadcrumbs__item:focus-visible,
  .reviews__btn:focus-visible,
  .pagination__arrow:focus-visible,
  .pagination__number:focus-visible {
    outline: 2px solid var(--accent);
    outline-offset: 3px;
  }

  @media (prefers-reduced-motion: reduce) {
    .reviews__card,
    .reviews__btn,
    .pagination__number {
      transition: none !important;
    }
    .reviews__card:hover,
    .reviews__btn--primary:hover { transform: none; }
  }

  /* ── Мобильные доводки ── */
  @media (max-width: 768px) {
    .reviews {
      --section-gap: clamp(32px, 8vh, 48px);
    }
    .reviews__btn {
      width: 100%;
      max-width: 330px;
    }
  }
</style>
@endpush