@extends('layouts.app')

@php
    $title = ($t['about_page']['title'] ?? $t['nav']['about'] ?? 'О компании') . ' — Almep Trading';
@endphp

@section('content')
<main class="site-main">
<div style="position: relative; background: #FFFFFF;">
      @include('partials.header', ['t' => $t, 'lang' => $lang])
    </div>

    <!-- Секция О компании -->
    <section class="about-company">
      <div class="about-company__inner">

        <!-- Хлебные крошки -->
        <nav class="breadcrumbs">
          <a href="/{{ $lang }}" class="breadcrumbs__item">
            <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
              <path d="M2.5 7.5L10 2.5L17.5 7.5V16.25C17.5 16.5815 17.3683 16.8995 17.1339 17.1339C16.8995 17.3683 16.5815 17.5 16.25 17.5H3.75C3.41848 17.5 3.10054 17.3683 2.86612 17.1339C2.6317 16.8995 2.5 16.5815 2.5 16.25V7.5Z" stroke="#696969" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
              <path d="M7.5 17.5V10H12.5V17.5" stroke="#696969" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </a>
          <svg width="4" height="8" viewBox="0 0 4 8" fill="none" class="breadcrumbs__separator">
            <path d="M1 1L3 4L1 7" stroke="#706F6F" stroke-width="1"/>
          </svg>
          <span class="breadcrumbs__current">{{ $t['nav']['about'] }}</span>
        </nav>

        <!-- Контент -->
        <div class="about-company__content">

          <!-- Заголовок -->
          <h1 class="about-company__title">{{ $t['about_page']['title'] }}</h1>

          <!-- Видео секция -->
          <div class="about-company__video" id="videoContainer">
            <div class="about-company__video-overlay" id="videoOverlay">
              <img
                src="{{ asset('assets/images/cover.png') }}"
                alt="{{ $t['about_page']['title'] }}"
                class="about-company__video-bg"
              />
              <button class="about-company__play-btn" id="playBtn" aria-label="{{ $t['about_page']['video_play'] }}">
                <svg width="120" height="120" viewBox="0 0 120 120" fill="none">
                  <polygon points="45,30 45,90 90,60" fill="#D9D9D9"/>
                </svg>
              </button>
            </div>
            <iframe
              id="youtubePlayer"
              width="100%"
              height="100%"
              src=""
              frameborder="0"
              allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
              allowfullscreen
              style="display: none; position: absolute; inset: 0;"
            ></iframe>
          </div>

        </div>

      </div>
    </section>

    <!-- Второй экран о компании -->
    <section class="about-details">
      <div class="about-details__inner">

        <!-- Блок 1: Текст слева, изображение справа -->
        <div class="about-details__block">
          <div class="about-details__text">
            <p class="about-details__paragraph">
              {{ $t['about_page']['section1_text'] }}
            </p>
          </div>
          <div class="about-details__image">
            <img src="{{ asset('assets/images/about-us-1.png') }}" alt="{{ $t['about_page']['title'] }}" />
          </div>
        </div>

        <!-- Блок 2: Изображение слева, текст справа -->
        <div class="about-details__content">
          <div class="about-details__block">
            <div class="about-details__image">
              <img src="{{ asset('assets/images/about-us-2.png') }}" alt="{{ $t['about_page']['section2_title'] }}" />
            </div>
            <div class="about-details__text">
              <p class="about-details__paragraph">{{ $t['about_page']['section2_title'] }}</p>
              <div class="about-details__list">
                <p class="about-details__paragraph">
                  {{ $t['about_page']['section2_intro'] }}
                </p>
                <div class="about-details__items">
                  <p class="about-details__paragraph">
                    {{ $t['about_page']['section2_berent'] }}
                  </p>
                  <p class="about-details__paragraph">
                    {{ $t['about_page']['section2_sensh'] }}
                  </p>
                  <p class="about-details__paragraph">
                    {{ $t['about_page']['section2_ekf'] }}
                  </p>
                </div>
              </div>
            </div>
          </div>

          <!-- Блок 3: Текст слева, изображение справа -->
          <div class="about-details__block">
            <div class="about-details__text">
              <p class="about-details__paragraph">{{ $t['about_page']['section3_title'] }}</p>
              <div class="about-details__list">
                <div class="about-details__items">
                  <p class="about-details__paragraph">
                    {{ $t['about_page']['section3_supply'] }}
                  </p>
                  <p class="about-details__paragraph">
                    {{ $t['about_page']['section3_expertise'] }}
                  </p>
                  <p class="about-details__paragraph">
                    {{ $t['about_page']['section3_quality'] }}
                  </p>
                </div>
                <p class="about-details__paragraph">
                  {{ $t['about_page']['section3_conclusion'] }}
                </p>
              </div>
            </div>
            <div class="about-details__image">
              <img src="{{ asset('assets/images/about-us-3.png') }}" alt="{{ $t['about_page']['section3_title'] }}" />
            </div>
          </div>
        </div>

        <!-- Кнопка -->
        <a href="/{{ $lang }}/contacts" class="about-details__btn">
          {{ $t['about_page']['btn_consultation'] }}
        </a>

      </div>
    </section>

    <!-- Третий экран - Преимущества -->
    <section class="about-advantages">
      <div class="about-advantages__inner">

        <!-- Карточка 1 -->
        <div class="about-advantages__card">
          <div class="about-advantages__icon">
            <img src="{{ asset('assets/icons/about-us-1.svg') }}" alt="{{ $t['about_page']['advantage1_title'] }}" />
          </div>
          <h3 class="about-advantages__title">{{ $t['about_page']['advantage1_title'] }}</h3>
          <p class="about-advantages__text">
            {{ $t['about_page']['advantage1_text'] }}
          </p>
        </div>

        <!-- Карточка 2 -->
        <div class="about-advantages__card">
          <div class="about-advantages__icon">
            <img src="{{ asset('assets/icons/about-us-2.svg') }}" alt="{{ $t['about_page']['advantage2_title'] }}" />
          </div>
          <h3 class="about-advantages__title">{{ $t['about_page']['advantage2_title'] }}</h3>
          <p class="about-advantages__text">
            {{ $t['about_page']['advantage2_text'] }}
          </p>
        </div>

        <!-- Карточка 3 -->
        <div class="about-advantages__card">
          <div class="about-advantages__icon">
            <img src="{{ asset('assets/icons/about-us-3.svg') }}" alt="{{ $t['about_page']['advantage3_title'] }}" />
          </div>
          <h3 class="about-advantages__title">{{ $t['about_page']['advantage3_title'] }}</h3>
          <p class="about-advantages__text">
            {{ $t['about_page']['advantage3_text'] }}
          </p>
        </div>

        <!-- Карточка 4 -->
        <div class="about-advantages__card">
          <div class="about-advantages__icon">
            <img src="{{ asset('assets/icons/about-us-4.svg') }}" alt="{{ $t['about_page']['advantage4_title'] }}" />
          </div>
          <h3 class="about-advantages__title">{{ $t['about_page']['advantage4_title'] }}</h3>
          <p class="about-advantages__text">
            {{ $t['about_page']['advantage4_text'] }}
          </p>
        </div>

      </div>
    </section>

    <!-- Секция с фоновым изображением -->
    <section class="about-background">
      <div class="about-background__image"></div>
      <div class="about-background__content">
        <h2 class="about-background__title">{{ $t['about_page']['final_title'] }}</h2>
        <div class="about-background__text-wrapper">
          <div class="about-background__text-block">
            <p class="about-background__text">
              {{ $t['about_page']['final_subtitle'] }}
            </p>
            <div class="about-background__description">
              <p class="about-background__text">
                {{ $t['about_page']['final_text'] }}
              </p>
            </div>
          </div>
          <a href="/{{ $lang }}/contacts" class="about-background__btn">
            {{ $t['about_page']['btn_consultation'] }}
          </a>
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

/* ========== БАЗОВЫЕ СТИЛИ (Full HD и выше) ========== */
  .about-company {
    width: 100%;
    background: #FFFFFF;
    padding: 0 6vw 0;
  }

  .about-company__inner {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: 50px;
    width: 100%;
    margin: 0 auto;
  }

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

  .about-company__content {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 60px;
    width: 100%;
  }

  .about-company__title {
    font-family: 'Montserrat', sans-serif;
    font-weight: 500;
    font-size: 48px;
    line-height: 110%;
    text-align: center;
    color: #000000;
    margin: 0;
    width: 100%;
  }

  /* Видео */
  .about-company__video {
    position: relative;
    width: 100%;
    max-height: 70vh;
    margin: 0 auto;
    aspect-ratio: 16 / 9; 
    background: #000000;
    overflow: hidden;
    border-radius: 12px;
  }

  .about-company__video-overlay {
    position: absolute;
    inset: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 2;
    transition: opacity 0.3s;
  }

  .about-company__video-overlay.hidden {
    display: none;
  }

  .about-company__video-bg {
    position: absolute;
    width: 100%;
    height: 100%;
    object-fit: cover;
    filter: brightness(0.4);
  }

  .about-company__play-btn {
    position: relative;
    z-index: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 204px;
    height: 204px;
    background: #FFFFFF;
    border-radius: 50%;
    border: none;
    cursor: pointer;
    transition: transform 0.2s, box-shadow 0.2s;
    padding: 0;
  }

  .about-company__play-btn:hover {
    transform: scale(1.05);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.3);
  }

  .about-company__play-btn svg {
    transform: translateX(5px);
  }

  /* Plyr (один раз) */
  :global(.plyr) {
    width: 100%;
    height: 100%;
  }

  :global(.plyr--video) {
    background: #000000;
  }

  :global(.plyr__control--overlaid) {
    background: #1C508F !important;
    border: none !important;
  }

  :global(.plyr__control--overlaid:hover) {
    background: #174480 !important;
  }

  :global(.plyr__controls) {
    background: linear-gradient(to top, rgba(0, 0, 0, 0.8), transparent) !important;
  }

  :global(.plyr__control:hover),
  :global(.plyr__control[aria-expanded="true"]) {
    background: #1C508F !important;
  }

  :global(.plyr__menu__container .plyr__control[role="menuitemradio"][aria-checked="true"]::before) {
    background: #1C508F !important;
  }

  :global(.plyr--full-ui input[type="range"]) {
    color: #1C508F !important;
  }

  :global(.plyr__progress__buffer) {
    color: rgba(28, 80, 143, 0.3) !important;
  }

  /* Второй экран */
  .about-details {
    width: 100%;
    background: #FFFFFF;
    padding: 0 6vw;
  }

  .about-details__inner {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: 60px;
    width: 100%;
    margin: 0 auto;
  }

  .about-details__content {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 50px;
    width: 100%;
  }

  .about-details__block {
    display: flex;
    flex-direction: row;
    align-items: center;
    gap: 25px;
    width: 100%;
  }

  .about-details__text {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: 25px;
    flex: 1;
  }

  .about-details__paragraph {
    font-family: 'Montserrat', sans-serif;
    font-weight: 400;
    font-size: 18px;
    line-height: 130%;
    letter-spacing: -0.01em;
    color: #000000;
    margin: 0;
  }

  .about-details__list {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: 20px;
    width: 100%;
  }

  .about-details__items {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: 20px;
    width: 100%;
  }

  .about-details__image {
    width: calc(50% - 12.5px);
    height: 440px;
    background: #D2D2D2;
    border-radius: 8px;
    overflow: hidden;
    flex-shrink: 0;
  }

  .about-details__image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }

  .about-details__btn {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 330px;
    height: 85px;
    background: #1C508F;
    box-shadow: 0px 0px 4px rgba(0, 0, 0, 0.3);
    border-radius: 9px;
    font-family: 'Montserrat', sans-serif;
    font-weight: 400;
    font-size: 18px;
    line-height: 130%;
    text-align: center;
    letter-spacing: -0.01em;
    color: #FFFFFF;
    text-decoration: none;
    transition: background 0.2s;
  }

  .about-details__btn:hover {
    background: #174480;
  }

  /* Преимущества */
  .about-advantages {
    width: 100%;
    background: #FFFFFF;
    padding: 0 6vw;
  }

  .about-advantages__inner {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 32px;
    width: 100%;
    margin: 0 auto;
  }

  .about-advantages__card {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 32px;
  }

  .about-advantages__icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 90px;
    height: 90px;
    background: #FFFFFF;
    box-shadow: 0px 0px 4px rgba(0, 0, 0, 0.3);
    border-radius: 50%;
    flex-shrink: 0;
  }

  .about-advantages__icon img {
    width: 57px;
    height: 57px;
    object-fit: contain;
  }

  .about-advantages__title {
    font-family: 'Montserrat', sans-serif;
    font-weight: 500;
    font-size: 24px;
    line-height: 110%;
    text-align: center;
    letter-spacing: -0.01em;
    color: #000000;
    margin: 0;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
  }

  .about-advantages__text {
    font-family: 'Montserrat', sans-serif;
    font-weight: 400;
    font-size: 18px;
    line-height: 130%;
    text-align: center;
    letter-spacing: -0.01em;
    color: #000000;
    margin: 0;
  }

  /* Фоновая секция */
  .about-background {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: flex-start;
    min-height: 972px;
    padding-left: 6vw;
    padding-right: 6vw;
    overflow: hidden;
  }

  .about-background__image {
    position: absolute;
    inset: 0;
    background: linear-gradient(0deg, rgba(0, 0, 0, 0.65), rgba(0, 0, 0, 0.65)), url('/assets/images/about-us-bg.png');
    background-size: cover;
    background-position: center;
  }

  .about-background__content {
    position: relative;
    z-index: 1;
    display: flex;
    flex-direction: column;
    gap: 50px;
    max-width: 690px;
    width: 100%;
  }

  .about-background__title {
    font-family: 'Montserrat', sans-serif;
    font-weight: 500;
    font-size: 30px;
    line-height: 110%;
    letter-spacing: -0.01em;
    color: #FFFFFF;
    margin: 0;
  }

  .about-background__text-wrapper {
    display: flex;
    flex-direction: column;
    gap: 50px;
  }

  .about-background__text-block {
    display: flex;
    flex-direction: column;
    gap: 20px;
  }

  .about-background__text {
    font-family: 'Montserrat', sans-serif;
    font-weight: 400;
    font-size: 18px;
    line-height: 130%;
    letter-spacing: -0.01em;
    color: #FFFFFF;
    margin: 0;
  }

  .about-background__description {
    display: flex;
    flex-direction: column;
    gap: 50px;
  }

  .about-background__btn {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 330px;
    height: 85px;
    background: #1C508F;
    box-shadow: 0px 0px 4px rgba(0, 0, 0, 0.3);
    border-radius: 9px;
    font-family: 'Montserrat', sans-serif;
    font-weight: 400;
    font-size: 18px;
    line-height: 130%;
    text-align: center;
    letter-spacing: -0.01em;
    color: #FFFFFF;
    text-decoration: none;
    transition: background 0.2s;
  }

  .about-background__btn:hover {
    background: #174480;
  }

  /* ========== MACBOOK (от 1921px) ========== */
  @media (min-width: 1921px) {
    .about-company__title {
      font-size: 56px;
    }
    .about-company__video {
      max-width: 1400px;
    }
    .about-details__image {
      height: 520px;
    }
    .about-details__paragraph {
      font-size: 20px;
    }
    .about-details__btn {
      width: 380px;
    }
    .about-advantages__inner {
      gap: 48px;
    }
    .about-advantages__title {
      font-size: 28px;
    }
    .about-advantages__text {
      font-size: 20px;
    }
    .about-advantages__icon {
      width: 100px;
      height: 100px;
    }
    .about-background {
      min-height: 1100px;
    }
    .about-background__title {
      font-size: 36px;
    }
    .about-background__text {
      font-size: 20px;
    }
    .about-background__btn {
      width: 380px;
      font-size: 20px;
    }
  }

  /* ========== ПЛАНШЕТЫ (до 1024px) ========== */
  @media (max-width: 1024px) {
    .about-advantages__inner {
      grid-template-columns: repeat(2, 1fr);
      gap: 36px;
    }
    .about-details__image {
      height: 350px;
    }
    .about-background {
      min-height: 700px;
    }
  }

  /* ========== МОБИЛЬНЫЕ (до 768px) ========== */
  @media (max-width: 768px) {
    .about-company,
    .about-details,
    .about-advantages {
      padding: 0 16px;
    }
    .about-company__inner {
      gap: 35px;
    }
    .about-company__content {
      gap: 45px;
    }
    .about-company__title {
      font-size: 32px;
    }
    .about-company__play-btn {
      width: 150px;
      height: 150px;
    }
    .about-company__play-btn svg {
      width: 90px;
      height: 90px;
    }
    .about-details__block {
      flex-direction: column;
      gap: 35px;
    }
    .about-details__image {
      width: 100%;
      height: 300px;
    }
    .about-details__paragraph {
      font-size: 16px;
    }
    .about-details__btn {
      width: 100%;
      font-size: 16px;
    }
    .about-advantages__inner {
      grid-template-columns: 1fr;
      gap: 45px;
    }
    .about-advantages__title {
      font-size: 20px;
    }
    .about-advantages__text {
      font-size: 16px;
    }
    .about-background {
      min-height: 600px;
      padding-left: 16px;
      padding-right: 16px;
    }
    .about-background__title {
      font-size: 24px;
    }
    .about-background__text {
      font-size: 16px;
    }
    .about-background__btn {
      width: 100%;
      font-size: 16px;
    }
  }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const playBtn = document.getElementById('playBtn');
    const videoOverlay = document.getElementById('videoOverlay');
    const youtubePlayer = document.getElementById('youtubePlayer');
    playBtn?.addEventListener('click', () => {
        if (!youtubePlayer) return;
        youtubePlayer.src = 'https://www.youtube.com/embed/4TwUone9FQU?autoplay=1&rel=0&modestbranding=1';
        youtubePlayer.style.display = 'block';
        videoOverlay?.classList.add('hidden');
    });
});
</script>
@endpush
