@extends('layouts.app')

@php
    $title = ($t['about_page']['title'] ?? $t['nav']['about'] ?? 'О компании') . ' — Almep Trading';
@endphp

@section('content')
<main class="site-main">
    <div style="position: relative; background: #FFFFFF;">
        @include('partials.header', ['t' => $t, 'lang' => $lang])
    </div>

    {{-- Оборачиваем всю страницу для действия переменных --}}
    <div class="about-page">
        {{-- Единый контейнер с общим gap — все секции внутри будут идти с одинаковым отступом --}}
        <div class="about-page__inner">
            {{-- Хлебные крошки --}}
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
                <span class="breadcrumbs__current" aria-current="page">{{ $t['nav']['about'] ?? 'О компании' }}</span>
            </nav>

            {{-- Секция 1: Видео --}}
            <section class="about-company">
                <div class="about-company__content">
                    <h1 class="about-company__title">{{ $t['about_page']['title'] }}</h1>
                    <div class="about-company__video" id="videoContainer">
                        <div class="about-company__video-overlay" id="videoOverlay">
                            <img
                                src="{{ asset('assets/images/cover.png') }}"
                                alt="{{ $t['about_page']['title'] }}"
                                class="about-company__video-bg"
                            />
                            <button class="about-company__play-btn" id="playBtn" aria-label="{{ $t['about_page']['video_play'] ?? 'Смотреть видео' }}">
                                <svg width="120" height="120" viewBox="0 0 120 120" fill="none" aria-hidden="true">
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
            </section>

            {{-- Секция 2: Детали --}}
            <section class="about-details">
                <div class="about-details__block">
                    <div class="about-details__text">
                        <p class="about-details__paragraph">{{ $t['about_page']['section1_text'] }}</p>
                    </div>
                    <div class="about-details__image">
                        <img src="{{ asset('assets/images/about-us-1.png') }}" alt="{{ $t['about_page']['title'] }}" />
                    </div>
                </div>

                <div class="about-details__content">
                    <div class="about-details__block">
                        <div class="about-details__image">
                            <img src="{{ asset('assets/images/about-us-2.png') }}" alt="{{ $t['about_page']['section2_title'] }}" />
                        </div>
                        <div class="about-details__text">
                            <p class="about-details__paragraph">{{ $t['about_page']['section2_title'] }}</p>
                            <div class="about-details__list">
                                <p class="about-details__paragraph">{{ $t['about_page']['section2_intro'] }}</p>
                                <div class="about-details__items">
                                    <p class="about-details__paragraph">{{ $t['about_page']['section2_berent'] }}</p>
                                    <p class="about-details__paragraph">{{ $t['about_page']['section2_sensh'] }}</p>
                                    <p class="about-details__paragraph">{{ $t['about_page']['section2_ekf'] }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="about-details__block">
                        <div class="about-details__text">
                            <p class="about-details__paragraph">{{ $t['about_page']['section3_title'] }}</p>
                            <div class="about-details__list">
                                <div class="about-details__items">
                                    <p class="about-details__paragraph">{{ $t['about_page']['section3_supply'] }}</p>
                                    <p class="about-details__paragraph">{{ $t['about_page']['section3_expertise'] }}</p>
                                    <p class="about-details__paragraph">{{ $t['about_page']['section3_quality'] }}</p>
                                </div>
                                <p class="about-details__paragraph">{{ $t['about_page']['section3_conclusion'] }}</p>
                            </div>
                        </div>
                        <div class="about-details__image">
                            <img src="{{ asset('assets/images/about-us-3.png') }}" alt="{{ $t['about_page']['section3_title'] }}" />
                        </div>
                    </div>
                </div>

                <a href="/{{ $lang }}/contacts" class="about-details__btn">
                    {{ $t['about_page']['btn_consultation'] }}
                </a>
            </section>

            {{-- Секция 3: Преимущества --}}
            <section class="about-advantages">
                <div class="about-advantages__inner">
                    <div class="about-advantages__card">
                        <div class="about-advantages__icon">
                            <img src="{{ asset('assets/icons/about-us-1.svg') }}" alt="{{ $t['about_page']['advantage1_title'] }}" />
                        </div>
                        <h3 class="about-advantages__title">{{ $t['about_page']['advantage1_title'] }}</h3>
                        <p class="about-advantages__text">{{ $t['about_page']['advantage1_text'] }}</p>
                    </div>
                    <div class="about-advantages__card">
                        <div class="about-advantages__icon">
                            <img src="{{ asset('assets/icons/about-us-2.svg') }}" alt="{{ $t['about_page']['advantage2_title'] }}" />
                        </div>
                        <h3 class="about-advantages__title">{{ $t['about_page']['advantage2_title'] }}</h3>
                        <p class="about-advantages__text">{{ $t['about_page']['advantage2_text'] }}</p>
                    </div>
                    <div class="about-advantages__card">
                        <div class="about-advantages__icon">
                            <img src="{{ asset('assets/icons/about-us-3.svg') }}" alt="{{ $t['about_page']['advantage3_title'] }}" />
                        </div>
                        <h3 class="about-advantages__title">{{ $t['about_page']['advantage3_title'] }}</h3>
                        <p class="about-advantages__text">{{ $t['about_page']['advantage3_text'] }}</p>
                    </div>
                    <div class="about-advantages__card">
                        <div class="about-advantages__icon">
                            <img src="{{ asset('assets/icons/about-us-4.svg') }}" alt="{{ $t['about_page']['advantage4_title'] }}" />
                        </div>
                        <h3 class="about-advantages__title">{{ $t['about_page']['advantage4_title'] }}</h3>
                        <p class="about-advantages__text">{{ $t['about_page']['advantage4_text'] }}</p>
                    </div>
                </div>
            </section>
        </div>{{-- конец .about-page__inner --}}

        {{-- Фоновая секция — отдельно, чтобы тянулась на всю ширину, с отступом сверху как у остальных --}}
        <section class="about-background">
            <div class="about-background__image" aria-hidden="true"></div>
            <div class="about-background__content">
                <h2 class="about-background__title">{{ $t['about_page']['final_title'] }}</h2>
                <div class="about-background__text-wrapper">
                    <div class="about-background__text-block">
                        <p class="about-background__text">{{ $t['about_page']['final_subtitle'] }}</p>
                        <div class="about-background__description">
                            <p class="about-background__text">{{ $t['about_page']['final_text'] }}</p>
                        </div>
                    </div>
                    <a href="/{{ $lang }}/contacts" class="about-background__btn">
                        {{ $t['about_page']['btn_consultation'] }}
                    </a>
                </div>
            </div>
        </section>
    </div>{{-- конец .about-page --}}

    @include('partials.footer', ['t' => $t, 'lang' => $lang])
</main>
@endsection

@push('styles')
<style>
    /* ── Базовый адаптивный слой ── */
    .site-main {
        display: flex;
        flex-direction: column;
        min-height: 100vh;
        overflow-x: clip;
    }
    .site-main > section {
        flex: 0 0 auto;
    }
    .site-main > section:first-of-type {
        flex: 1 0 auto;
    }
    .site-main img,
    .site-main iframe,
    .site-main video {
        max-width: 100%;
    }
    .site-main *,
    .site-main *::before,
    .site-main *::after {
        box-sizing: border-box;
    }

    /* ── Токены страницы (точно как в news-page) ── */
    .about-page {
        --accent: #1C508F;
        --text: #000000;
        --text-muted: #696969;
        --breadcrumb: #2B2B2B;
        --side-pad: var(--hdr-px, clamp(16px, 6vw, 115px));
        --v-unit: var(--hdr-py, clamp(12px, 2.9vh, 28px));
        --section-gap: clamp(40px, 0vh, 96px);   /* ключевой отступ между всеми блоками */
        --container-max: 1600px;
        --radius-md: 8px;
        --radius-lg: 12px;

        width: 100%;
        background: #FFFFFF;
        padding: calc(var(--v-unit) * 1) var(--side-pad) calc(var(--v-unit) * 3.2);
    }

    /* ── Единый контейнер, формирующий вертикальный ритм ── */
    .about-page__inner {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        width: 100%;
        margin: 0 auto;
        gap: var(--section-gap);      /* 👈 вот эти отступы между секциями, как в news */
    }

    /* Хлебные крошки — стили общие с news */
    .breadcrumbs {
        display: flex;
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

    /* ── Секция 1: Видео ── */
    .about-company {
        width: 100%;
    }
    .about-company__content {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: clamp(40px, 6vw, 60px);
        width: 100%;
    }
    .about-company__title {
        font-family: 'Montserrat', sans-serif;
        font-weight: 500;
        font-size: clamp(32px, 5vw, 56px);
        line-height: 110%;
        text-align: center;
        color: var(--text);
        margin: 0;
        width: 100%;
    }
    .about-company__video {
        position: relative;
        width: 100%;
        max-width: 1400px;
        margin: 0 auto;
        aspect-ratio: 16 / 9;
        background: #000000;
        overflow: hidden;
        border-radius: var(--radius-lg);
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
        width: clamp(100px, 15vw, 204px);
        height: clamp(100px, 15vw, 204px);
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
        width: 60%;
        height: 60%;
    }

    /* Plyr – глобальные стили (видеоплеер) */
    .plyr {
        width: 100%;
        height: 100%;
    }
    .plyr--video {
        background: #000000;
    }
    .plyr__control--overlaid {
        background: #1C508F !important;
        border: none !important;
    }
    .plyr__control--overlaid:hover {
        background: #174480 !important;
    }
    .plyr__controls {
        background: linear-gradient(to top, rgba(0, 0, 0, 0.8), transparent) !important;
    }
    .plyr__control:hover,
    .plyr__control[aria-expanded="true"] {
        background: #1C508F !important;
    }
    .plyr__menu__container .plyr__control[role="menuitemradio"][aria-checked="true"]::before {
        background: #1C508F !important;
    }
    .plyr--full-ui input[type="range"] {
        color: #1C508F !important;
    }
    .plyr__progress__buffer {
        color: rgba(28, 80, 143, 0.3) !important;
    }

    /* ── Секция 2: Детали ── */
    .about-details {
        width: 100%;
        display: flex;
        flex-direction: column;
        gap: var(--section-gap);   /* внутренний ритм между блоками деталей и кнопкой */
    }
    .about-details__content {
        display: flex;
        flex-direction: column;
        gap: clamp(30px, 5vw, 50px);
    }
    .about-details__block {
        display: flex;
        flex-direction: row;
        align-items: center;
        gap: clamp(20px, 3vw, 30px);
        width: 100%;
    }
    .about-details__text {
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: clamp(18px, 2.5vw, 25px);
    }
    .about-details__paragraph {
        font-family: 'Montserrat', sans-serif;
        font-weight: 400;
        font-size: clamp(16px, 1.4vw, 20px);
        line-height: 130%;
        letter-spacing: -0.01em;
        color: var(--text);
        margin: 0;
    }
    .about-details__list {
        display: flex;
        flex-direction: column;
        gap: clamp(14px, 2vw, 20px);
    }
    .about-details__items {
        display: flex;
        flex-direction: column;
        gap: clamp(14px, 2vw, 20px);
    }
    .about-details__image {
        width: calc(50% - 15px);
        aspect-ratio: 4 / 3;
        background: #D2D2D2;
        border-radius: var(--radius-md);
        overflow: hidden;
        flex-shrink: 0;
    }
    .about-details__image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .about-details__btn,
    .about-background__btn {
        display: flex;
        align-items: center;
        justify-content: center;
        width: clamp(280px, 25vw, 380px);
        height: clamp(64px, 8vw, 85px);
        background: var(--accent);
        box-shadow: 0px 0px 4px rgba(0, 0, 0, 0.3);
        border-radius: 9px;
        font-family: 'Montserrat', sans-serif;
        font-weight: 400;
        font-size: clamp(16px, 1.4vw, 20px);
        line-height: 130%;
        text-align: center;
        letter-spacing: -0.01em;
        color: #FFFFFF;
        text-decoration: none;
        transition: background 0.2s, transform 0.2s;
        align-self: flex-start;
    }
    .about-details__btn:hover,
    .about-background__btn:hover {
        background: #174480;
        transform: translateY(-2px);
    }

    /* ── Секция 3: Преимущества ── */
    .about-advantages {
        width: 100%;
    }
    .about-advantages__inner {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(min(260px, 100%), 1fr));
        gap: clamp(24px, 3vw, 48px);
        width: 100%;
    }
    .about-advantages__card {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: clamp(20px, 2.5vw, 32px);
        text-align: center;
    }
    .about-advantages__icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: clamp(70px, 8vw, 90px);
        height: clamp(70px, 8vw, 90px);
        background: #FFFFFF;
        box-shadow: 0px 0px 4px rgba(0, 0, 0, 0.3);
        border-radius: 50%;
        flex-shrink: 0;
    }
    .about-advantages__icon img {
        width: 60%;
        height: 60%;
        object-fit: contain;
    }
    .about-advantages__title {
        font-family: 'Montserrat', sans-serif;
        font-weight: 500;
        font-size: clamp(18px, 1.8vw, 28px);
        line-height: 110%;
        letter-spacing: -0.01em;
        color: var(--text);
        margin: 0;
    }
    .about-advantages__text {
        font-family: 'Montserrat', sans-serif;
        font-weight: 400;
        font-size: clamp(15px, 1.3vw, 20px);
        line-height: 130%;
        letter-spacing: -0.01em;
        color: var(--text);
        margin: 0;
    }

    /* ── Фоновая секция ── */
    .about-background {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: flex-start;
        min-height: clamp(600px, 70vh, 1100px);
        margin-top: var(--section-gap);            /* отступ сверху точно как между секциями */
        margin-left: calc(-1 * var(--side-pad));
        margin-right: calc(-1 * var(--side-pad));
        padding-left: var(--side-pad);
        padding-right: var(--side-pad);
        overflow: hidden;
    }
    .about-background__image {
        position: absolute;
        inset: 0;
        background: linear-gradient(0deg, rgba(0, 0, 0, 0.65), rgba(0, 0, 0, 0.65)),
                    url('/assets/images/about-us-bg.png') center / cover no-repeat;
    }
    .about-background__content {
        position: relative;
        z-index: 1;
        display: flex;
        flex-direction: column;
        gap: clamp(30px, 4vw, 50px);
        max-width: 690px;
        width: 100%;
    }
    .about-background__title {
        font-family: 'Montserrat', sans-serif;
        font-weight: 500;
        font-size: clamp(22px, 3vw, 36px);
        line-height: 110%;
        letter-spacing: -0.01em;
        color: #FFFFFF;
        margin: 0;
    }
    .about-background__text-wrapper {
        display: flex;
        flex-direction: column;
        gap: clamp(30px, 4vw, 50px);
    }
    .about-background__text-block {
        display: flex;
        flex-direction: column;
        gap: clamp(14px, 2vw, 20px);
    }
    .about-background__text {
        font-family: 'Montserrat', sans-serif;
        font-weight: 400;
        font-size: clamp(16px, 1.3vw, 20px);
        line-height: 130%;
        letter-spacing: -0.01em;
        color: #FFFFFF;
        margin: 0;
    }
    .about-background__description {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    /* ── Адаптивные доработки под мобильные ── */
    @media (max-width: 768px) {
        .about-details__block {
            flex-direction: column;
        }
        .about-details__image {
            width: 100%;
            aspect-ratio: 16 / 9;
        }
        .about-advantages__inner {
            grid-template-columns: 1fr;
        }
        .about-company__play-btn svg {
            width: 50%;
            height: 50%;
        }
        .about-page {
            --section-gap: clamp(32px, 8vh, 64px);
            padding: calc(var(--v-unit) * 1) 16px calc(var(--v-unit) * 2.5);
        }
        .about-background {
            min-height: 500px;
            margin-top: var(--section-gap);
            margin-left: -16px;
            margin-right: -16px;
            padding-left: 16px;
            padding-right: 16px;
        }
    }

    /* Фокус для доступности */
    .breadcrumbs__item:focus-visible,
    .about-details__btn:focus-visible,
    .about-background__btn:focus-visible,
    .about-company__play-btn:focus-visible {
        outline: 2px solid var(--accent);
        outline-offset: 3px;
    }

    @media (prefers-reduced-motion: reduce) {
        .about-company__play-btn,
        .about-details__btn,
        .about-background__btn {
            transition: none;
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