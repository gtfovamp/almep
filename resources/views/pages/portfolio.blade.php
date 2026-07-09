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
          <span class="breadcrumbs__current">{{ $t['portfolio']['title'] ?? 'Портфолио' }}</span>
        </nav>

        <h1 class="portfolio-page__title">{{ $t['portfolio']['title'] ?? 'Портфолио' }}</h1>

        @if($portfolioItems->total() === 0)
          <div class="portfolio-page__empty">
            <p>{{ $t['portfolio']['no_projects'] ?? 'Проекты скоро появятся' }}</p>
          </div>
        @else
          <div class="portfolio-page__grid">
            @foreach($portfolioItems as $item)
              @php
                $pTitle = $lang === 'en' ? ($item->title_en ?: $item->title) : ($lang === 'az' ? ($item->title_az ?: $item->title) : $item->title);
              @endphp
              <div class="portfolio__card">
                <img src="{{ $item->image_url }}" alt="{{ $pTitle }}" class="portfolio__card-img" loading="lazy" />
                <div class="portfolio__card-caption">
                  <span class="portfolio__card-title">{{ $pTitle }}</span>
                  <span class="portfolio__card-year">{{ $item->year }}</span>
                </div>
              </div>
            @endforeach
          </div>
        @endif

        @if($portfolioItems->hasPages())
          @php
            $cur = $portfolioItems->currentPage();
            $last = $portfolioItems->lastPage();
          @endphp
          <div class="pagination">
            <a href="{{ $cur > 1 ? $portfolioItems->previousPageUrl() : '#' }}"
               class="pagination__arrow {{ $cur === 1 ? 'pagination__arrow--disabled' : '' }}"
               aria-label="{{ $t['portfolio']['aria_prev'] ?? 'Previous page' }}">
              <svg width="37" height="19" viewBox="0 0 37 19" fill="none" aria-hidden="true">
                <path d="M14 1L1 9.5L14 18" stroke="currentColor" stroke-width="1"/>
                <line x1="1" y1="9.5" x2="36.5" y2="9.5" stroke="currentColor" stroke-width="1"/>
              </svg>
            </a>
            <div class="pagination__numbers">
              @for($pageNum = 1; $pageNum <= $last; $pageNum++)
                <a href="{{ $portfolioItems->url($pageNum) }}"
                   class="pagination__number {{ $pageNum === $cur ? 'pagination__number--active' : '' }}">{{ $pageNum }}</a>
              @endfor
            </div>
            <a href="{{ $cur < $last ? $portfolioItems->nextPageUrl() : '#' }}"
               class="pagination__arrow {{ $cur === $last ? 'pagination__arrow--disabled' : '' }}"
               aria-label="{{ $t['portfolio']['aria_next'] ?? 'Next page' }}">
              <svg width="37" height="19" viewBox="0 0 37 19" fill="none" aria-hidden="true">
                <path d="M23 1L36 9.5L23 18" stroke="currentColor" stroke-width="1"/>
                <line x1="0.5" y1="9.5" x2="36" y2="9.5" stroke="currentColor" stroke-width="1"/>
              </svg>
            </a>
          </div>
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

.portfolio-page {
    width: 100%;
    background: #FFFFFF;
    padding: 0 6vw;
  }

  .portfolio-page__inner {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: 50px;
    width: 100%;
    margin: 0 auto;
  }

  /* Хлебные крошки */
  .breadcrumbs {
    display: flex;
    flex-direction: row;
    align-items: center;
    gap: 10px;
  }

  .breadcrumbs__item {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 20px;
    height: 20px;
    transition: opacity 0.2s;
  }

  .breadcrumbs__item:hover {
    opacity: 0.7;
  }

  .breadcrumbs__separator {
    flex-shrink: 0;
  }

  .breadcrumbs__current {
    font-family: 'Montserrat', sans-serif;
    font-weight: 400;
    font-size: 13px;
    line-height: 16px;
    color: #2B2B2B;
  }

  /* Заголовок */
  .portfolio-page__title {
    font-family: 'Montserrat', sans-serif;
    font-weight: 500;
    font-size: 48px;
    line-height: 110%;
    color: #000000;
    margin: 0;
  }

  .portfolio-page__empty {
    width: 100%;
    text-align: center;
    padding: 80px 20px;
    color: #666;
    font-family: 'Montserrat', sans-serif;
    font-size: 18px;
  }

  /* ═══════════════════════════════════════════════
     СЕТКА — 3 колонки, строки по 280px
  ═══════════════════════════════════════════════ */
  .portfolio-page__grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 25px;
    width: 100%;
  }

  /* ═══════════════════════════════════════════════
     КАРТОЧКА — 1 в 1 с оригинальным компонентом
  ═══════════════════════════════════════════════ */
  .portfolio__card {
    position: relative;
    width: 100%;
    height: 280px;
    background: #FFFFFF;
    box-shadow: 0px 0px 4px rgba(0, 0, 0, 0.3);
    border-radius: 10px;
    overflow: hidden;
    flex-shrink: 0;
  }

  .portfolio__card-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
    transition: transform 0.3s ease;
  }

  .portfolio__card:hover .portfolio__card-img {
    transform: scale(1.03);
  }

  .portfolio__card-caption {
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 111px;
    background: rgba(255, 255, 255, 0.88);
    display: flex;
    flex-direction: row;
    justify-content: space-between;
    align-items: flex-start;
    padding: 26px 15px 0;
    gap: 42px;
    box-sizing: border-box;
    opacity: 0;
    transform: translateY(100%);
    transition: opacity 0.3s ease, transform 0.3s ease;
  }

  .portfolio__card:hover .portfolio__card-caption {
    opacity: 1;
    transform: translateY(0);
  }

  .portfolio__card-title {
    font-family: 'Montserrat', sans-serif;
    font-weight: 500;
    font-size: 24px;
    line-height: 110%;
    letter-spacing: -0.01em;
    color: #1C508F;
    flex: 1;
  }

  .portfolio__card-year {
    font-family: 'Montserrat', sans-serif;
    font-weight: 500;
    font-size: 24px;
    line-height: 110%;
    letter-spacing: -0.01em;
    color: #1C508F;
    white-space: nowrap;
  }

  /* ═══════════════════════════════════════════════
     ПАГИНАЦИЯ — как в странице отзывов
  ═══════════════════════════════════════════════ */
  .pagination {
    display: flex;
    flex-direction: row;
    justify-content: center;
    align-items: center;
    gap: 50px;
    width: 100%;
    height: 43px;
  }

  .pagination__arrow {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 36.5px;
    height: 19px;
    color: #000000;
    text-decoration: none;
    transition: opacity 0.2s;
    flex-shrink: 0;
  }

  .pagination__arrow:hover:not(.pagination__arrow--disabled) {
    opacity: 0.7;
  }

  .pagination__arrow--disabled {
    opacity: 0.3;
    pointer-events: none;
    cursor: default;
  }

  .pagination__numbers {
    display: flex;
    flex-direction: row;
    align-items: center;
    gap: 10px;
    height: 43px;
  }

  .pagination__number {
    box-sizing: border-box;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 43px;
    height: 43px;
    border: 1px solid transparent;
    border-radius: 10px;
    font-family: 'Montserrat', sans-serif;
    font-weight: 500;
    font-size: 16px;
    line-height: 20px;
    color: #000000;
    text-decoration: none;
    transition: all 0.2s;
  }

  .pagination__number:hover {
    background: #f5f5f5;
  }

  .pagination__number--active {
    border: 1px solid #1C508F;
    color: #1C508F;
    font-weight: 700;
  }

  .pagination__number--active:hover {
    background: transparent;
  }

  /* ═══════════════════════════════════════════════
     МОБИЛЬНАЯ ВЕРСИЯ
  ═══════════════════════════════════════════════ */
  @media (max-width: 768px) {
    .portfolio-page__inner {
      gap: 35px;
      padding: 0 23px;
    }

    .portfolio-page__title {
      font-size: 24px;
    }

    .portfolio-page__grid {
      grid-template-columns: 1fr;
      gap: 20px;
    }

    .portfolio__card {
      height: 330px;
    }

    /* На мобиле капшн всегда виден */
    .portfolio__card-caption {
      height: 166px;
      padding: 15px;
      flex-direction: column;
      justify-content: flex-start;
      align-items: flex-start;
      gap: 10px;
      opacity: 1;
      transform: translateY(0);
    }

    .portfolio__card-title {
      font-size: 20px;
    }

    .portfolio__card-year {
      font-size: 20px;
    }

    .pagination {
      gap: 30px;
    }

    .pagination__numbers {
      gap: 8px;
    }

    .pagination__number {
      width: 38px;
      height: 38px;
      font-size: 14px;
    }
  }
</style>
@endpush