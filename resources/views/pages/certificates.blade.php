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
<main>
    <div style="position: relative; background: #FFFFFF;">
        @include('partials.header', ['t' => $t, 'lang' => $lang])
    </div>

    <section class="certificates">
        <div class="certificates__inner">

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
                <span class="breadcrumbs__current">{{ $t['nav']['certificates'] ?? '' }}</span>
            </nav>

            <h1 class="certificates__title">{{ $t['nav']['certificates'] ?? '' }}</h1>

            @if ($certificates->isEmpty())
                <div class="certificates__empty">
                    <p>{{ $t['certificates']['empty'] ?? 'No certificates available' }}</p>
                </div>
            @else
                <div class="certificates__grid">
                    @foreach ($certificates->chunk(3) as $row)
                        <div class="certificates__row">
                            @foreach ($row as $cert)
                                <div class="certificates__item">
                                    <img src="{{ $cert->image_url }}" alt="{{ certTitle($cert, $lang) }}" class="certificates__image" />
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            @endif

        </div>
    </section>

    <div style="padding-top: 8.9vh;"></div>

    @include('partials.footer', ['t' => $t, 'lang' => $lang])
</main>
@endsection

@push('styles')
<style>
    .certificates { width: 100%; background: #FFFFFF; padding: 0 6vw; }
    .certificates__inner { display: flex; flex-direction: column; align-items: flex-start; gap: 50px; width: 100%; margin: 0 auto; }
    .breadcrumbs { display: flex; flex-direction: row; align-items: center; gap: 10px; }
    .breadcrumbs__item { display: flex; align-items: center; justify-content: center; width: 20px; height: 20px; transition: opacity 0.2s; }
    .breadcrumbs__item:hover { opacity: 0.7; }
    .breadcrumbs__separator { flex-shrink: 0; }
    .breadcrumbs__current { font-family: 'Montserrat', sans-serif; font-weight: 400; font-size: 13px; line-height: 16px; color: #2B2B2B; }
    .certificates__title { font-family: 'Montserrat', sans-serif; font-weight: 500; font-size: 48px; line-height: 110%; text-align: center; color: #000000; margin: 0; width: 100%; }
    .certificates__grid { display: flex; flex-direction: column; align-items: center; gap: 30px; width: 100%; margin: 0 auto; }
    .certificates__row { display: flex; flex-direction: row; align-items: center; justify-content: center; gap: 29px; width: 100%; }
    .certificates__item { width: 100%; max-width: 450px; height: 505px; flex: 1 1 450px; overflow: hidden; }
    @media (min-width: 1500px) { .certificates__item { flex: 0 0 450px; } }
    .certificates__image { width: 100%; height: 100%; object-fit: contain; display: block; }
    .certificates__empty { width: 100%; text-align: center; padding: 60px 20px; color: #666; font-family: 'Montserrat', sans-serif; font-size: 18px; }
    @media (max-width: 768px) {
        .certificates__inner { gap: 35px; }
        .certificates__title { font-size: 32px; }
        .certificates__grid { gap: 20px; }
        .certificates__row { flex-direction: column; gap: 20px; }
        .certificates__item { width: 100%; height: auto; aspect-ratio: 450 / 505; }
    }
</style>
@endpush
