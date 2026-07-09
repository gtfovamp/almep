@extends('layouts.app')

@php
    $title = ($t['nav']['structure'] ?? 'Структура') . ' — Almep Trading';
@endphp

@section('content')
<main class="site-main">
    <div style="position: relative; background: #FFFFFF;">
        @include('partials.header', ['t' => $t, 'lang' => $lang])
    </div>

    <section class="page page--structure">
        <div class="page__inner">

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
                <span class="breadcrumbs__current">{{ $t['nav']['structure'] ?? 'Структура' }}</span>
            </nav>

            <h1 class="page__title">{{ $t['structure']['title'] ?? 'Структура компании' }}</h1>

            <div class="structure__diagram">
                <img src="{{ asset('assets/images/structure-'.$lang.'.svg') }}"
                     alt="{{ $t['structure']['title'] ?? 'Структура компании' }}"
                     class="structure__image" loading="lazy" />
            </div>

        </div>
    </section>

    @include('partials.footer', ['t' => $t, 'lang' => $lang])
</main>
@endsection

@push('styles')
<style>
    /* main занимает всю высоту — футер всегда внизу, без распорок-костылей */
    .site-main { display: flex; flex-direction: column; min-height: 100vh; }
    .site-main > .page { flex: 1 0 auto; padding-top: clamp(140px, 20vh, 220px); padding-bottom: clamp(50px, 8vh, 90px); }
    /* ── Общий каркас страницы (адаптивный) ── */
    .page { width: 100%; background: #FFFFFF; padding: 0 clamp(16px, 6vw, 115px); box-sizing: border-box; }
    .page__inner { display: flex; flex-direction: column; align-items: flex-start; gap: clamp(28px, 4vw, 50px); width: 100%; max-width: 1410px; margin: 0 auto; }
    .breadcrumbs { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
    .breadcrumbs__item { display: flex; align-items: center; justify-content: center; width: 20px; height: 20px; transition: opacity .2s; }
    .breadcrumbs__item:hover { opacity: .7; }
    .breadcrumbs__separator { flex-shrink: 0; }
    .breadcrumbs__current { font-family: 'Montserrat', sans-serif; font-weight: 400; font-size: 13px; line-height: 16px; color: #2B2B2B; }
    .page__title { font-family: 'Montserrat', sans-serif; font-weight: 600; font-size: clamp(28px, 5vw, 48px); line-height: 1.1; color: #1A1A1A; margin: 0; }
    .structure__diagram { width: 100%; display: flex; justify-content: center; }
    .structure__image { width: 100%; height: auto; max-width: 1410px; object-fit: contain; }
</style>
@endpush
