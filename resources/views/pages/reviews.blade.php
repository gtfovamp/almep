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
          <span class="breadcrumbs__current">{{ $t['reviews']['title'] ?? 'Отзывы' }}</span>
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
                  <div class="reviews__quote">"</div>
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
            <div class="pagination">
              <a href="{{ $cur > 1 ? $reviews->previousPageUrl() : '#' }}"
                 class="pagination__arrow {{ $cur === 1 ? 'pagination__arrow--disabled' : '' }}" aria-label="{{ $t['reviews']['aria_prev'] ?? 'Previous page' }}">
                <svg width="37" height="19" viewBox="0 0 37 19" fill="none" aria-hidden="true">
                  <path d="M14 1L1 9.5L14 18" stroke="currentColor" stroke-width="1"/>
                  <line x1="1" y1="9.5" x2="36.5" y2="9.5" stroke="currentColor" stroke-width="1"/>
                </svg>
              </a>
              <div class="pagination__numbers">
                @for($pageNum = 1; $pageNum <= $last; $pageNum++)
                  <a href="{{ $reviews->url($pageNum) }}" class="pagination__number {{ $pageNum === $cur ? 'pagination__number--active' : '' }}">{{ $pageNum }}</a>
                @endfor
              </div>
              <a href="{{ $cur < $last ? $reviews->nextPageUrl() : '#' }}"
                 class="pagination__arrow {{ $cur === $last ? 'pagination__arrow--disabled' : '' }}" aria-label="{{ $t['reviews']['aria_next'] ?? 'Next page' }}">
                <svg width="37" height="19" viewBox="0 0 37 19" fill="none" aria-hidden="true">
                  <path d="M23 1L36 9.5L23 18" stroke="currentColor" stroke-width="1"/>
                  <line x1="0.5" y1="9.5" x2="36" y2="9.5" stroke="currentColor" stroke-width="1"/>
                </svg>
              </a>
            </div>
          @endif

          <div class="reviews__actions">
            <a href="/{{ $lang }}#reviews" class="reviews__btn reviews__btn--outline">{{ $t['reviews']['all_reviews'] ?? '' }}</a>
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

.reviews {
    width: 100%;
    background: #FFFFFF;
    padding: 0 6vw;
  }

  .reviews__inner {
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

  .reviews__content {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 50px;
    width: 100%;
  }

  /* Заголовок */
  .reviews__title {
    font-family: 'Montserrat', sans-serif;
    font-weight: 500;
    font-size: 48px;
    line-height: 110%;
    text-align: center;
    color: #000000;
    margin: 0;
    width: 100%;
  }

  /* Статистика */
  .reviews__stats {
    display: flex;
    flex-direction: row;
    align-items: center;
    gap: 145px;
    justify-content: center;
  }

  .reviews__stat {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 25px;
  }

  .reviews__stat-value {
    font-family: 'Raleway', sans-serif;
    font-weight: 400;
    font-size: 45px;
    line-height: 110%;
    text-align: center;
    color: #000000;
  }

  .reviews__stat-label {
    font-family: 'Montserrat', sans-serif;
    font-weight: 400;
    font-size: 18px;
    line-height: 130%;
    letter-spacing: -0.01em;
    color: #000000;
  }

  /* Сетка отзывов */
  .reviews__grid {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 30px;
    width: 100%;
  }

  .reviews__row {
    display: flex;
    flex-direction: row;
    align-items: flex-start;
    gap: 30px;
    width: 100%;
    justify-content: center;
  }

  .reviews__card {
    position: relative;
    width: 100%;
    max-width: 450px;
    height: 420px;
    background: #FFFFFF;
    border: 1px solid rgba(0, 0, 0, 0.2);
    border-radius: 7px;
    flex: 1 1 450px;
    box-sizing: border-box;
    padding: 25px;
    display: flex;
    flex-direction: column;
  }

  .reviews__quote {
    font-family: 'Raleway', sans-serif;
    font-weight: 400;
    font-size: 128px;
    line-height: 110%;
    color: #000000;
    user-select: none;
    pointer-events: none;
    margin: 0;
    height: 91px;
  }

  .reviews__card-content {
    display: flex;
    flex-direction: column;
    gap: 22px;
    flex: 1;
    margin-top: 0;
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
    font-size: 18px;
    line-height: 130%;
    letter-spacing: -0.01em;
    color: #000000;
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
    font-size: 24px;
    line-height: 110%;
    letter-spacing: -0.01em;
    color: #000000;
    margin: 0;
  }

  .reviews__empty {
    width: 100%;
    text-align: center;
    padding: 60px 20px;
    color: #666;
    font-family: 'Montserrat', sans-serif;
    font-size: 18px;
  }

  /* Пагинация */
  .pagination {
    display: flex;
    flex-direction: row;
    justify-content: center;
    align-items: center;
    padding: 0px;
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
    align-items: flex-end;
    padding: 0px;
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
    font-style: normal;
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

  /* Кнопки */
  .reviews__actions {
    display: flex;
    flex-direction: row;
    justify-content: space-between;
    align-items: center;
    gap: 752px;
    width: 100%;
  }

  .reviews__btn {
    width: 330px;
    height: 85px;
    border-radius: 9px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: 'Montserrat', sans-serif;
    font-weight: 400;
    font-size: 18px;
    line-height: 130%;
    text-align: center;
    letter-spacing: -0.01em;
    text-decoration: none;
    transition: all 0.2s;
  }

  .reviews__btn--outline {
    border: 1px solid #1C508F;
    color: #1C508F;
    background: transparent;
  }

  .reviews__btn--outline:hover {
    background: #f5f5f5;
  }

  .reviews__btn--primary {
    background: #1C508F;
    box-shadow: 0px 0px 4px rgba(0, 0, 0, 0.3);
    color: #FFFFFF;
  }

  .reviews__btn--primary:hover {
    background: #174480;
  }

  /* Мобильная версия */
  @media (max-width: 768px) {
    .reviews__inner {
      gap: 35px;
    }

    .reviews__title {
      font-size: 32px;
    }

    .reviews__stats {
      flex-direction: column;
      gap: 40px;
    }

    .reviews__row {
      flex-direction: column;
      align-items: center;
    }

    .reviews__card {
      max-width: 100%;
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

    .reviews__actions {
      flex-direction: column;
      gap: 20px;
    }

    .reviews__btn {
      width: 100%;
      max-width: 330px;
    }
  }

  /* ── Адаптивная сетка отзывов (заменяет фикс. ряды по 3) ── */
  .reviews__grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 30px; width: 100%; }
  @media (max-width: 1024px) { .reviews__grid { grid-template-columns: repeat(2, 1fr); gap: 24px; } }
  @media (max-width: 640px) { .reviews__grid { grid-template-columns: 1fr; gap: 20px; } }

</style>
@endpush