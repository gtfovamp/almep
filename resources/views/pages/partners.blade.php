@extends('layouts.app')

@php
    $title = ($t['nav']['partners'] ?? 'Партнёры') . ' — Almep Trading';

    $getName = function ($p, $lang) {
        if ($lang === 'en' && !empty($p['name_en'])) return $p['name_en'];
        if ($lang === 'az' && !empty($p['name_az'])) return $p['name_az'];
        return $p['name_ru'] ?? $p['name'] ?? '';
    };
    $getDescription = function ($p, $lang) {
        if ($lang === 'en' && !empty($p['description_en'])) return $p['description_en'];
        if ($lang === 'az' && !empty($p['description_az'])) return $p['description_az'];
        return $p['description_ru'] ?? $p['description'] ?? '';
    };
    $partners = $partners ?? [];
@endphp

@section('content')
<main class="site-main">
    <div style="position: relative; background: #FFFFFF;">
        @include('partials.header', ['t' => $t, 'lang' => $lang])
    </div>

    {{-- Единый контейнер: те же токены отступов/ритма, что и на about / services --}}
    <div class="partners-page">
        <div class="partners-page__inner">

            <!-- Хлебные крошки -->
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
                <span class="breadcrumbs__current" aria-current="page">{{ $t['nav']['partners'] }}</span>
            </nav>

            <!-- Секция: Партнёры -->
            <section class="partners">
                <div class="partners__content">

                    <h1 class="partners__title">{{ $t['partners_page']['title'] }}</h1>

                    <!-- Адаптивная сетка партнёров -->
                    <div class="partners__grid">
                        @foreach($partners as $partner)
                            <div class="partners__card">
                                <div class="partners__card-content">
                                    <img src="{{ $partner['image_url'] }}" alt="{{ $getName($partner, $lang) }}" class="partners__logo" loading="lazy" />
                                    @php($__d = $getDescription($partner, $lang))
                                    @if($__d)
                                        <p class="partners__description">{{ $__d }}</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <a href="/{{ $lang }}#consultation" class="partners__cta">
                        {{ $t['partners_page']['become_partner_title'] }}
                    </a>

                </div>
            </section>

            <!-- Секция: Польза партнёрства -->
            <section class="benefits">
                <h2 class="benefits__title">{{ $t['partners_page']['benefits_title'] }}</h2>

                <div class="benefits__grid">
                    <div class="benefits__item">
                        <div class="benefits__icon">
                            <img src="{{ asset('assets/icons/partners-1.svg') }}" alt="{{ $t['partners_page']['benefit_1_title'] }}" />
                        </div>
                        <h3 class="benefits__item-title">{{ $t['partners_page']['benefit_1_title'] }}</h3>
                        <p class="benefits__item-text">{{ $t['partners_page']['benefit_1_text'] }}</p>
                    </div>

                    <div class="benefits__item">
                        <div class="benefits__icon">
                            <img src="{{ asset('assets/icons/partners-2.svg') }}" alt="{{ $t['partners_page']['benefit_2_title'] }}" />
                        </div>
                        <h3 class="benefits__item-title">{{ $t['partners_page']['benefit_2_title'] }}</h3>
                        <p class="benefits__item-text">{{ $t['partners_page']['benefit_2_text'] }}</p>
                    </div>

                    <div class="benefits__item">
                        <div class="benefits__icon">
                            <img src="{{ asset('assets/icons/partners-3.svg') }}" alt="{{ $t['partners_page']['benefit_3_title'] }}" />
                        </div>
                        <h3 class="benefits__item-title">{{ $t['partners_page']['benefit_3_title'] }}</h3>
                        <p class="benefits__item-text">{{ $t['partners_page']['benefit_3_text'] }}</p>
                    </div>

                    <div class="benefits__item">
                        <div class="benefits__icon">
                            <img src="{{ asset('assets/icons/partners-4.svg') }}" alt="{{ $t['partners_page']['benefit_4_title'] }}" />
                        </div>
                        <h3 class="benefits__item-title">{{ $t['partners_page']['benefit_4_title'] }}</h3>
                        <p class="benefits__item-text">{{ $t['partners_page']['benefit_4_text'] }}</p>
                    </div>
                </div>
            </section>

            <!-- Секция: Станьте партнёром -->
            <section class="become-partner">
                <div class="become-partner__inner">
                    <h2 class="become-partner__title">{{ $t['partners_page']['become_partner_title'] }}</h2>

                    <div class="become-partner__form-wrapper">
                        <p class="become-partner__subtitle">{{ $t['partners_page']['become_partner_subtitle'] }}</p>

                        <form class="become-partner__form">
                            <div class="become-partner__inputs">
                                <input type="text" placeholder="{{ $t['partners_page']['form_name'] }}" class="become-partner__input" required />
                                <input type="email" placeholder="{{ $t['partners_page']['form_email'] }}" class="become-partner__input" required />
                                <input type="tel" placeholder="{{ $t['partners_page']['form_phone'] }}" class="become-partner__input" required />
                            </div>

                            <button type="submit" class="become-partner__submit">
                                {{ $t['partners_page']['form_submit'] }}
                            </button>
                        </form>
                    </div>
                </div>
            </section>

        </div>
    </div>

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

    /* ── Токены страницы (единая сетка отступов, как на about / services) ── */
    .partners-page {
        --accent: #1C508F;
        --accent-hover: #174480;
        --text: #000000;
        --text-muted: #666666;
        --breadcrumb: #2B2B2B;
        --side-pad: var(--hdr-px, clamp(16px, 6vw, 115px));
        --v-unit: var(--hdr-py, clamp(12px, 2.9vh, 28px));
        --section-gap: clamp(56px, 9vh, 110px);
        --radius-sm: 7px;
        --radius-md: 9px;

        width: 100%;
        background: #FFFFFF;
        padding: calc(var(--v-unit) * 1) var(--side-pad) calc(var(--v-unit) * 3.2);
    }

    .partners-page__inner {
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

    /* ═══════════════════════════════════════════════
       Секция: Партнёры
    ═══════════════════════════════════════════════ */
    .partners { width: 100%; }

    .partners__content {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: clamp(40px, 6vw, 60px);
        width: 100%;
    }

    .partners__title {
        font-family: 'Montserrat', sans-serif;
        font-weight: 500;
        font-size: clamp(32px, 5vw, 48px);
        line-height: 110%;
        text-align: center;
        color: var(--text);
        margin: 0;
        width: 100%;
    }

    /* Сетка 2×2 как в макете (карточки 690×330, gap 30px), плавно схлопывается в 1 колонку */
    .partners__grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: clamp(16px, 3vw, 30px);
        width: 100%;
        max-width: 1412px;
        margin: 0 auto;
    }

    .partners__card {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        aspect-ratio: 690 / 330;   /* сохраняет пропорции карточки из макета на любой ширине */
        background: #FFFFFF;
        box-shadow: 0px 0px 4px rgba(0, 0, 0, 0.3);
        border-radius: var(--radius-sm);
        padding: clamp(20px, 4vw, 40px);
        transition: box-shadow 0.25s ease, transform 0.25s ease;
    }
    .partners__card:hover {
        box-shadow: 0 10px 28px rgba(28, 80, 143, 0.18);
        transform: translateY(-3px);
    }

    .partners__card-content {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: clamp(16px, 2.5vw, 30px);
        max-width: 492px;
        text-align: center;
    }

    .partners__logo {
        max-width: clamp(140px, 18vw, 218px);
        max-height: 60px;
        width: auto;
        height: auto;
        object-fit: contain;
    }

    .partners__description {
        font-family: 'Montserrat', sans-serif;
        font-weight: 400;
        font-size: clamp(15px, 1.3vw, 18px);
        line-height: 130%;
        text-align: center;
        letter-spacing: -0.01em;
        color: var(--text);
        margin: 0;
    }

    .partners__cta,
    .become-partner__submit {
        display: flex;
        align-items: center;
        justify-content: center;
        width: clamp(220px, 25vw, 330px);
        height: clamp(64px, 8vw, 85px);
        background: var(--accent);
        box-shadow: 0px 0px 4px rgba(0, 0, 0, 0.3);
        border-radius: var(--radius-md);
        border: none;
        font-family: 'Montserrat', sans-serif;
        font-weight: 400;
        font-size: clamp(16px, 1.4vw, 18px);
        line-height: 130%;
        text-align: center;
        letter-spacing: -0.01em;
        color: #FFFFFF;
        text-decoration: none;
        cursor: pointer;
        transition: background 0.2s ease, transform 0.2s ease;
    }
    .partners__cta:hover,
    .become-partner__submit:hover {
        background: var(--accent-hover);
        transform: translateY(-2px);
    }

    /* ═══════════════════════════════════════════════
       Секция: Польза партнёрства
    ═══════════════════════════════════════════════ */
    .benefits {
        width: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: clamp(40px, 6vw, 60px);
    }

    .benefits__title {
        font-family: 'Montserrat', sans-serif;
        font-weight: 500;
        font-size: clamp(28px, 4vw, 48px);
        line-height: 110%;
        text-align: center;
        color: var(--text);
        margin: 0;
        width: 100%;
    }

    .benefits__grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(min(240px, 100%), 1fr));
        gap: clamp(28px, 3vw, 30px);
        width: 100%;
    }

    .benefits__item {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: clamp(20px, 2.5vw, 32px);
        text-align: center;
    }

    .benefits__icon {
        width: clamp(70px, 8vw, 90px);
        height: clamp(70px, 8vw, 90px);
        background: #FFFFFF;
        box-shadow: 0px 0px 4px rgba(0, 0, 0, 0.3);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .benefits__icon img {
        width: 60%;
        height: 60%;
        object-fit: contain;
    }

    .benefits__item-title {
        font-family: 'Montserrat', sans-serif;
        font-weight: 500;
        font-size: clamp(20px, 2vw, 30px);
        line-height: 110%;
        text-align: center;
        letter-spacing: -0.01em;
        color: var(--text);
        margin: 0;
    }

    .benefits__item-text {
        font-family: 'Montserrat', sans-serif;
        font-weight: 400;
        font-size: clamp(15px, 1.3vw, 18px);
        line-height: 130%;
        text-align: center;
        letter-spacing: -0.01em;
        color: var(--text);
        margin: 0;
    }

    /* ═══════════════════════════════════════════════
       Секция: Станьте партнёром
    ═══════════════════════════════════════════════ */
    .become-partner { width: 100%; display: flex; justify-content: center; }

    .become-partner__inner {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: clamp(40px, 6vw, 60px);
        width: 100%;
        max-width: 690px;
    }

    .become-partner__title {
        font-family: 'Montserrat', sans-serif;
        font-weight: 500;
        font-size: clamp(28px, 4vw, 48px);
        line-height: 110%;
        text-align: center;
        color: var(--text);
        margin: 0;
    }

    .become-partner__form-wrapper {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: clamp(30px, 4vw, 50px);
        width: 100%;
    }

    .become-partner__subtitle {
        font-family: 'Raleway', sans-serif;
        font-weight: 400;
        font-size: clamp(16px, 1.4vw, 18px);
        line-height: 150%;
        text-align: center;
        color: var(--text);
        margin: 0;
    }

    .become-partner__form {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: clamp(30px, 4vw, 50px);
        width: 100%;
    }

    /* Плавная адаптивная раскладка полей — без ручных breakpoint'ов */
    .become-partner__inputs {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(min(220px, 100%), 1fr));
        gap: clamp(16px, 3vw, 30px);
        width: 100%;
    }

    .become-partner__input {
        box-sizing: border-box;
        width: 100%;
        height: clamp(56px, 7vw, 60px);
        border: 1px solid #000000;
        border-radius: 6px;
        padding: 0 20px;
        font-family: 'Montserrat', sans-serif;
        font-weight: 400;
        font-size: clamp(15px, 1.4vw, 18px);
        line-height: 130%;
        letter-spacing: -0.01em;
        color: #151515;
        background: #FFFFFF;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }
    .become-partner__input::placeholder { color: #767676; }
    .become-partner__input:focus {
        outline: none;
        border-color: var(--accent);
        box-shadow: 0 0 0 3px rgba(28, 80, 143, 0.15);
    }

    /* На средних экранах 2 колонки становятся слишком узкими для карточки 690×330 —
       сохраняем пропорции макета, пока это уместно, а дальше отпускаем aspect-ratio */
    @media (max-width: 900px) {
        .partners__card {
            aspect-ratio: auto;
            min-height: 280px;
        }
    }

    /* ── Адаптивные доработки под мобильные ── */
    @media (max-width: 768px) {
        .partners-page {
            --section-gap: clamp(40px, 8vh, 72px);
            padding: calc(var(--v-unit) * 1) 16px calc(var(--v-unit) * 2.5);
        }
        .partners__content,
        .benefits,
        .become-partner__inner,
        .become-partner__form-wrapper,
        .become-partner__form {
            gap: clamp(28px, 6vw, 40px);
        }
        .partners__grid {
            grid-template-columns: 1fr;
        }
        .partners__card {
            min-height: 260px;
        }
    }

    /* Фокус для доступности */
    .breadcrumbs__item:focus-visible,
    .partners__cta:focus-visible,
    .become-partner__submit:focus-visible {
        outline: 2px solid var(--accent);
        outline-offset: 3px;
    }

    @media (prefers-reduced-motion: reduce) {
        .partners__card,
        .partners__cta,
        .become-partner__submit,
        .become-partner__input {
            transition: none;
        }
    }
</style>
@endpush