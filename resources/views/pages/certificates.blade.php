@extends('layouts.app')

@php
    $title = ($t['nav']['certificates'] ?? '') . ' — Almep Trading';

    function certTitle($cert, $lang) {
        if ($lang === 'en' && !empty($cert->title_en)) return $cert->title_en;
        if ($lang === 'az' && !empty($cert->title_az)) return $cert->title_az;
        return $cert->title;
    }
@endphp

@section('content')
<main class="site-main">
    <div style="position: relative; background: #FFFFFF;">
        @include('partials.header', ['t' => $t, 'lang' => $lang])
    </div>

    <section class="certificates-page">
      <div class="certificates-page__inner">

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
          <span class="breadcrumbs__current" aria-current="page">{{ $t['nav']['certificates'] ?? '' }}</span>
        </nav>

        <section class="certificates-list">
          <h1 class="certificates-list__title">{{ $t['nav']['certificates'] ?? '' }}</h1>

          @if ($certificates->isEmpty())
            <div class="certificates-page__empty">
              <svg width="48" height="48" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 4h16v16H4z" stroke="#A7A7A7" stroke-width="1.2"/><path d="M8 9h8M8 13h5" stroke="#A7A7A7" stroke-width="1.2" stroke-linecap="round"/></svg>
              <p>{{ $t['certificates']['empty'] ?? 'No certificates available' }}</p>
            </div>
          @else
            <div class="certificates-grid">
              @foreach ($certificates as $cert)
                <article class="certificate-card">
                  <div class="certificate-card__image">
                    <img src="{{ $cert->image_url }}" alt="{{ certTitle($cert, $lang) }}" loading="lazy" width="450" height="505" />
                  </div>
                </article>
              @endforeach
            </div>
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

  /* ── Токены страницы сертификатов ── */
  .certificates-page {
    --accent: #1C508F;
    --text: #000000;
    --text-muted: #676767;
    --breadcrumb: #2B2B2B;
    --card-shadow: 0px 0px 4px rgba(0, 0, 0, 0.3);
    --card-shadow-hover: 0 10px 28px rgba(28, 80, 143, 0.16);
    --radius-md: 7px;

    /* Те же паддинги, что и у хедера / страницы новостей, чтобы контент
       садился краями ровно на те же вертикали по всему сайту. */
    --side-pad: var(--hdr-px, clamp(16px, 6vw, 115px));
    --v-unit: var(--hdr-py, clamp(12px, 2.9vh, 28px));
    --section-gap: clamp(40px, 0vh, 96px);
    --container-max: 1600px;

    width: 100%;
    background: #FFFFFF;
    padding: calc(var(--v-unit) * 1) var(--side-pad) calc(var(--v-unit) * 3.2);
  }

  .certificates-page__inner {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    width: 100%;
    margin: 0 auto;
    gap: var(--section-gap);
  }

  /* Breadcrumbs — идентичны странице новостей */
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

  /* Список сертификатов */
  .certificates-list {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: clamp(28px, 4vw, 50px);
    width: 100%;
  }

  .certificates-list__title {
    font-family: 'Montserrat', sans-serif;
    font-weight: 500;
    font-size: clamp(28px, 4vw, 48px);
    line-height: 110%;
    color: var(--text);
    margin: 0;
    text-align: center;

    width: 100%;
  }

  /* Сетка карточек: подбирает число колонок под ширину экрана сама,
     без ручного разбиения на строки по 3 и без резких «скачков». */
  .certificates-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(min(320px, 100%), 1fr));
    gap: clamp(20px, 2.5vw, 30px);
    width: 100%;
  }

  .certificate-card {
    background: #FFFFFF;
    box-shadow: var(--card-shadow);
    border-radius: var(--radius-md);
    overflow: hidden;
    transition: transform 0.25s ease, box-shadow 0.25s ease;
  }

  .certificate-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--card-shadow-hover);
  }

  .certificate-card__image {
    width: 100%;
    aspect-ratio: 450 / 505;
    background: #D8D8D8;
    overflow: hidden;
  }

  .certificate-card__image img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    display: block;
    transition: transform 0.35s ease;
  }

  .certificate-card:hover .certificate-card__image img { transform: scale(1.05); }

  /* Пустое состояние */
  .certificates-page__empty {
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

  /* Клавиатурная доступность */
  .breadcrumbs__item:focus-visible {
    outline: 2px solid var(--accent);
    outline-offset: 3px;
  }

  @media (prefers-reduced-motion: reduce) {
    .certificate-card,
    .certificate-card__image img {
      transition: none !important;
    }
    .certificate-card:hover { transform: none; }
  }

  /* ── Точки перелома для тонкой доводки на мобильных ── */
  @media (max-width: 480px) {
    .certificates-list__title { font-size: 28px; }
  }
</style>
@endpush