@extends('layouts.app')

@php
    $title = ($t['nav']['structure'] ?? 'Структура') . ' — Almep Trading';
@endphp

@section('content')
<main class="site-main">
    <div style="position: relative; background: #FFFFFF;">
        @include('partials.header', ['t' => $t, 'lang' => $lang])
    </div>

    <div class="structure-page">
        <div class="structure-page__inner">
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
                <span class="breadcrumbs__current" aria-current="page">{{ $t['nav']['structure'] ?? 'Структура' }}</span>
            </nav>

            <h1 class="structure-page__title">{{ $t['nav']['structure'] ?? 'Структура компании' }}</h1>

            <div class="structure-page__diagram">
                <img src="{{ asset('assets/images/structure-'.$lang.'.svg') }}"
                     alt="{{ $t['structure']['title'] ?? 'Структура компании' }}"
                     class="structure-page__image"
                     loading="lazy" />
            </div>
        </div>
    </div>

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
    .site-main img {
        max-width: 100%;
    }
    .site-main *,
    .site-main *::before,
    .site-main *::after {
        box-sizing: border-box;
    }

    /* ── Токены страницы ── */
    .structure-page {
        --accent: #1C508F;
        --text: #000000;
        --breadcrumb: #2B2B2B;
        --side-pad: var(--hdr-px, clamp(16px, 6vw, 115px));
        --v-unit: var(--hdr-py, clamp(12px, 2.9vh, 28px));
        --section-gap: clamp(40px, 0vh, 96px);

        width: 100%;
        background: #FFFFFF;
        padding: calc(var(--v-unit) * 1) var(--side-pad) calc(var(--v-unit) * 3.2);
    }

    /* ── Контейнер — без ограничения ширины ── */
    .structure-page__inner {
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

    /* Заголовок */
    .structure-page__title {
    font-family: 'Montserrat', sans-serif;
    font-weight: 500;
    font-size: clamp(28px, 5vw, 48px);
    line-height: 1.1;
    color: #1A1A1A;
    margin: 0;
    text-align: center;
    width: 100%;
    }

    /* Диаграмма — занимает всю доступную ширину */
    .structure-page__diagram {
        width: 100%;
    }
    .structure-page__image {
        width: 100%;
        height: auto;
        display: block;
    }

    /* Accessibility */
    .breadcrumbs__item:focus-visible {
        outline: 2px solid var(--accent);
        outline-offset: 3px;
    }

    /* Мобильные */
    @media (max-width: 768px) {
        .structure-page {
            --section-gap: clamp(32px, 8vh, 64px);
            padding: calc(var(--v-unit) * 1) 16px calc(var(--v-unit) * 2.5);
        }
    }
</style>
@endpush