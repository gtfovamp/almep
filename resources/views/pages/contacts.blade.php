@extends('layouts.app')

@php
    $title = ($t['contacts_page']['title'] ?? $t['nav']['contacts'] ?? 'Контакты') . ' — Almep Trading';
@endphp

@section('content')
<main class="site-main">
<div style="position: relative; background: #FFFFFF;">
      @include('partials.header', ['t' => $t, 'lang' => $lang])
    </div>

    <!-- Секция Контакты -->
    <section class="contacts-page">
      <div class="contacts-page__inner">

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
          <span class="breadcrumbs__current">{{ $t['contacts']['title'] }}</span>
        </nav>

        <!-- Основной контент -->
        <div class="contacts-page__content">
          
          <!-- Левая часть: Заголовок и Информация -->
          <div class="contacts-page__left">
            <!-- Заголовок -->
            <h1 class="contacts-page__title">{{ $t['contacts']['title'] }}</h1>

            <!-- Информация -->
            <div class="contacts-page__info">
              
              <!-- Адрес -->
              <div class="contacts-page__block">
                <h2 class="contacts-page__label">
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                    <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z" stroke="#1c508f" stroke-width="2" fill="none"/>
                    <circle cx="12" cy="9" r="2.5" stroke="#1c508f" stroke-width="2" fill="none"/>
                  </svg>
                  {{ $t['contacts']['address_label'] }}
                </h2>
                <p class="contacts-page__value">{{ $t['contacts']['address_value'] }}</p>
              </div>

              <!-- Телефон -->
              <div class="contacts-page__block">
                <h2 class="contacts-page__label">
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                    <path d="M6.6 10.8c1.4 2.8 3.8 5.1 6.6 6.6l2.2-2.2c.3-.3.7-.4 1-.2 1.1.4 2.3.6 3.6.6.6 0 1 .4 1 1V20c0 .6-.4 1-1 1-9.4 0-17-7.6-17-17 0-.6.4-1 1-1h3.5c.6 0 1 .4 1 1 0 1.3.2 2.5.6 3.6.1.3 0 .7-.2 1L6.6 10.8z" stroke="#1c508f" stroke-width="2" fill="none"/>
                  </svg>
                  {{ $t['contacts']['phone_label'] }}
                </h2>
                <a 
                  href="tel:{{ preg_replace('/\s/', '', $t['contacts']['phone_value'] ?? '') }}"
                  class="contacts-page__value contacts-page__value--link"
                >
                  {{ $t['contacts']['phone_value'] }}
                </a>
              </div>

              <!-- Email -->
              <div class="contacts-page__block">
                <h2 class="contacts-page__label">
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                    <rect x="2" y="4" width="20" height="16" rx="2" stroke="#1c508f" stroke-width="2" fill="none"/>
                    <path d="M2 8l10 6 10-6" stroke="#1c508f" stroke-width="2" fill="none"/>
                  </svg>
                  {{ $t['contacts']['email_label'] }}
                </h2>
                <a 
                  href="mailto:{{ $t['contacts']['email_value'] ?? '' }}"
                  class="contacts-page__value contacts-page__value--link"
                >
                  {{ $t['contacts']['email_value'] }}
                </a>
              </div>

              <!-- График работы -->
              <div class="contacts-page__block">
                <h2 class="contacts-page__label">
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                    <circle cx="12" cy="12" r="10" stroke="#1c508f" stroke-width="2" fill="none"/>
                    <path d="M12 6v6l4 2" stroke="#1c508f" stroke-width="2" stroke-linecap="round"/>
                  </svg>
                  {{ $t['contacts']['schedule_label'] }}
                </h2>
                <div class="contacts-page__schedule">
                  <p class="contacts-page__value">{{ $t['contacts']['schedule_days'] }}</p>
                  <p class="contacts-page__value">{{ $t['contacts']['schedule_hours'] }}</p>
                </div>
              </div>

            </div>
          </div>

          <!-- Правая часть: Карта -->
          <div class="contacts-page__map-wrapper">
            <div class="contacts-page__map-overlay-top"></div>
            <div class="contacts-page__map-overlay-bottom"></div>
            <div class="contacts-page__map" id="map"></div>

            <!-- Кастомные контролы зума -->
            <div class="map-controls">
              <button
                class="map-controls__btn"
                id="zoom-in"
                aria-label="{{ $t['contacts']['zoom_in'] }}"
              >
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                  <path d="M8 2V14M2 8H14" stroke="currentColor" stroke-width="2" stroke-linecap="round"></path>
                </svg>
              </button>
              <div class="map-controls__divider"></div>
              <button
                class="map-controls__btn"
                id="zoom-out"
                aria-label="{{ $t['contacts']['zoom_out'] }}"
              >
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                  <path d="M2 8H14" stroke="currentColor" stroke-width="2" stroke-linecap="round"></path>
                </svg>
              </button>
            </div>

            <!-- Бейдж локации -->
            <div class="map-badge">
              <div class="map-badge__dot"></div>
              <div class="map-badge__text">
                <span class="map-badge__title">{{ $t['contacts']['company_name'] }}</span>
                <span class="map-badge__sub">{{ $t['contacts']['city'] }}</span>
              </div>
            </div>
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

/* ═══════════════════════════════════════════════
     ОСНОВНАЯ СЕКЦИЯ
  ═══════════════════════════════════════════════ */
  .contacts-page {
    width: 100%;
    background: #FFFFFF;
    padding: 0 6vw;
  }

  .contacts-page__inner {
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

  /* ═══════════════════════════════════════════════
     ОСНОВНОЙ КОНТЕНТ
  ═══════════════════════════════════════════════ */
  .contacts-page__content {
    display: flex;
    flex-direction: row;
    align-items: flex-start;
    gap: 60px;
    width: 100%;
  }

  /* Левая часть */
  .contacts-page__left {
    display: flex;
    flex-direction: column;
    gap: 50px;
    width: 450px;
    flex-shrink: 0;
  }

  /* Заголовок */
  .contacts-page__title {
    font-family: 'Montserrat', sans-serif;
    font-weight: 500;
    font-size: 48px;
    line-height: 110%;
    color: #000000;
    margin: 0;
  }

  /* Информация */
  .contacts-page__info {
    display: flex;
    flex-direction: column;
    gap: 40px;
  }

  .contacts-page__block {
    display: flex;
    flex-direction: column;
    gap: 15px;
  }

  .contacts-page__label {
    display: flex;
    align-items: center;
    gap: 10px;
    font-family: 'Montserrat', sans-serif;
    font-weight: 500;
    font-size: 20px;
    line-height: 24px;
    color: #000000;
    margin: 0;
  }

  .contacts-page__label svg {
    flex-shrink: 0;
  }

  .contacts-page__value {
    font-family: 'Montserrat', sans-serif;
    font-weight: 400;
    font-size: 16px;
    line-height: 130%;
    letter-spacing: -0.01em;
    color: #151515;
    margin: 0;
    text-decoration: none;
  }

  .contacts-page__value--link {
    color: #1c508f;
    transition: opacity 0.2s ease;
  }

  .contacts-page__value--link:hover {
    opacity: 0.7;
  }

  .contacts-page__schedule {
    display: flex;
    flex-direction: column;
    gap: 5px;
  }

  /* ═══════════════════════════════════════════════
     КАРТА
  ═══════════════════════════════════════════════ */
  .contacts-page__map-wrapper {
    position: relative;
    flex: 1;
    height: 600px;
    border-radius: 20px;
    overflow: hidden;
    box-shadow:
      0 2px 4px rgba(28, 80, 143, 0.06),
      0 8px 24px rgba(28, 80, 143, 0.12),
      0 24px 64px rgba(28, 80, 143, 0.1);
    opacity: 0;
    transform: translateY(16px);
    transition:
      opacity 0.6s ease,
      transform 0.6s ease;
  }

  .contacts-page__map-wrapper.is-loaded {
    opacity: 1;
    transform: translateY(0);
  }

  .contacts-page__map-wrapper::after {
    content: "";
    position: absolute;
    inset: 0;
    border-radius: 20px;
    border: 1.5px solid rgba(28, 80, 143, 0.15);
    pointer-events: none;
    z-index: 10;
  }

  .contacts-page__map-overlay-top {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 80px;
    background: linear-gradient(to bottom, rgba(240, 245, 255, 0.35), transparent);
    pointer-events: none;
    z-index: 5;
    border-radius: 20px 20px 0 0;
  }

  .contacts-page__map-overlay-bottom {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 80px;
    background: linear-gradient(to top, rgba(240, 245, 255, 0.3), transparent);
    pointer-events: none;
    z-index: 5;
    border-radius: 0 0 20px 20px;
  }

  .contacts-page__map {
    width: 100%;
    height: 100%;
    z-index: 0;
  }

  /* ═══════════════════════════════════════════════
     КОНТРОЛЫ КАРТЫ
  ═══════════════════════════════════════════════ */
  .map-controls {
    position: absolute;
    top: 20px;
    right: 20px;
    z-index: 20;
    display: flex;
    flex-direction: column;
    align-items: center;
    background: rgba(255, 255, 255, 0.92);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    border-radius: 12px;
    border: 1px solid rgba(28, 80, 143, 0.12);
    box-shadow:
      0 4px 16px rgba(0, 0, 0, 0.08),
      0 1px 4px rgba(0, 0, 0, 0.06);
    overflow: hidden;
  }

  .map-controls__btn {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: transparent;
    border: none;
    cursor: pointer;
    color: #1c508f;
    transition:
      background 0.15s ease,
      color 0.15s ease;
  }

  .map-controls__btn:hover {
    background: rgba(28, 80, 143, 0.08);
    color: #174480;
  }

  .map-controls__btn:active {
    background: rgba(28, 80, 143, 0.15);
  }

  .map-controls__divider {
    width: 24px;
    height: 1px;
    background: rgba(28, 80, 143, 0.12);
  }

  /* Бейдж локации */
  .map-badge {
    position: absolute;
    bottom: 20px;
    right: 20px;
    z-index: 20;
    display: flex;
    align-items: center;
    gap: 10px;
    background: rgba(255, 255, 255, 0.92);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    border-radius: 12px;
    border: 1px solid rgba(28, 80, 143, 0.12);
    box-shadow:
      0 4px 16px rgba(0, 0, 0, 0.08),
      0 1px 4px rgba(0, 0, 0, 0.04);
    padding: 10px 16px;
  }

  .map-badge__dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background: #1c508f;
    box-shadow: 0 0 0 3px rgba(28, 80, 143, 0.2);
    flex-shrink: 0;
    animation: badgePulse 2.5s ease-in-out infinite;
  }

  @keyframes badgePulse {
    0%, 100% {
      box-shadow: 0 0 0 3px rgba(28, 80, 143, 0.2);
    }
    50% {
      box-shadow: 0 0 0 6px rgba(28, 80, 143, 0.08);
    }
  }

  .map-badge__text {
    display: flex;
    flex-direction: column;
    gap: 1px;
  }

  .map-badge__title {
    font-family: 'Montserrat', sans-serif;
    font-weight: 600;
    font-size: 13px;
    color: #151515;
    line-height: 1.3;
  }

  .map-badge__sub {
    font-family: 'Montserrat', sans-serif;
    font-weight: 400;
    font-size: 11px;
    color: #6b7280;
    line-height: 1.3;
  }

  /* ═══════════════════════════════════════════════
     МАРКЕР И ПОПАП
  ═══════════════════════════════════════════════ */
  :global(.pulse-marker) {
    position: relative;
    width: 60px;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  :global(.pulse-marker__ring) {
    position: absolute;
    border-radius: 50%;
    background: rgba(28, 80, 143, 0.15);
    animation: pulseRing 2.4s ease-out infinite;
  }

  :global(.pulse-marker__ring--1) {
    width: 60px;
    height: 60px;
    animation-delay: 0s;
  }

  :global(.pulse-marker__ring--2) {
    width: 44px;
    height: 44px;
    animation-delay: 0.3s;
    background: rgba(28, 80, 143, 0.2);
  }

  :global(.pulse-marker__ring--3) {
    width: 30px;
    height: 30px;
    animation-delay: 0.6s;
    background: rgba(28, 80, 143, 0.25);
  }

  @keyframes pulseRing {
    0% {
      transform: scale(0.7);
      opacity: 1;
    }
    70% {
      transform: scale(1.1);
      opacity: 0.3;
    }
    100% {
      transform: scale(1.2);
      opacity: 0;
    }
  }

  :global(.pulse-marker__core) {
    position: absolute;
    width: 36px;
    height: 36px;
    background: linear-gradient(135deg, #1c508f 0%, #2d6bb5 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow:
      0 4px 12px rgba(28, 80, 143, 0.5),
      0 0 0 3px rgba(255, 255, 255, 0.9);
    z-index: 2;
  }

  :global(.modern-popup-wrapper .leaflet-popup-content-wrapper) {
    padding: 0;
    border-radius: 16px;
    box-shadow:
      0 8px 32px rgba(0, 0, 0, 0.12),
      0 2px 8px rgba(0, 0, 0, 0.08);
    border: 1px solid rgba(28, 80, 143, 0.1);
    overflow: hidden;
  }

  :global(.modern-popup-wrapper .leaflet-popup-content) {
    margin: 0;
    width: 240px !important;
  }

  :global(.modern-popup-wrapper .leaflet-popup-tip-container) {
    margin-top: -1px;
  }

  :global(.modern-popup-wrapper .leaflet-popup-tip) {
    box-shadow: none;
    background: #fff;
  }

  :global(.modern-popup) {
    font-family: 'Montserrat', sans-serif;
    overflow: hidden;
  }

  :global(.modern-popup__header) {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 14px 16px 12px;
    background: linear-gradient(135deg, #1c508f 0%, #2563b0 100%);
  }

  :global(.modern-popup__icon) {
    width: 32px;
    height: 32px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }

  :global(.modern-popup__title) {
    font-weight: 600;
    font-size: 14px;
    color: #ffffff;
    line-height: 1.3;
  }

  :global(.modern-popup__sub) {
    font-weight: 400;
    font-size: 11px;
    color: rgba(255, 255, 255, 0.7);
    line-height: 1.3;
  }

  :global(.modern-popup__divider) {
    height: 1px;
    background: rgba(28, 80, 143, 0.08);
  }

  :global(.modern-popup__row) {
    display: flex;
    align-items: center;
    gap: 9px;
    padding: 9px 16px;
    font-size: 12px;
    color: #374151;
    font-weight: 400;
    line-height: 1.4;
    border-bottom: 1px solid rgba(0, 0, 0, 0.04);
    transition: background 0.15s ease;
  }

  :global(.modern-popup__row:last-child) {
    border-bottom: none;
  }

  :global(.modern-popup__row:hover) {
    background: rgba(28, 80, 143, 0.04);
  }

  .contacts-page__map :global(.leaflet-control-zoom) {
    display: none;
  }

  .contacts-page__map :global(.leaflet-control-attribution) {
    font-size: 10px;
    background: rgba(255, 255, 255, 0.7);
    backdrop-filter: blur(4px);
    border-radius: 6px 0 0 0;
    padding: 3px 6px;
    color: #9ca3af;
  }

  .contacts-page__map :global(.leaflet-control-attribution a) {
    color: #1c508f;
  }

  /* ═══════════════════════════════════════════════
     МОБИЛЬНАЯ ВЕРСИЯ
  ═══════════════════════════════════════════════ */
  @media (max-width: 768px) {
    .contacts-page__inner {
      gap: 35px;
      padding: 0 20px;
    }

    .contacts-page__content {
      flex-direction: column;
      gap: 40px;
    }

    .contacts-page__left {
      width: 100%;
      gap: 35px;
    }

    .contacts-page__title {
      font-size: 32px;
    }

    .contacts-page__info {
      gap: 30px;
    }

    .contacts-page__map-wrapper {
      width: 100%;
      height: 400px;
    }

    .map-controls {
      top: 10px;
      right: 10px;
    }

    .map-badge {
      bottom: 10px;
      right: 10px;
      padding: 8px 12px;
    }
  }
</style>
@endpush

@push('scripts')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
const companyName = @json($t['contacts']['company_name'] ?? '');
const officeLabel = @json($t['contacts']['office_label'] ?? '');
const popupAddress = @json($t['contacts']['popup_address'] ?? '');
const phoneValue = @json($t['contacts']['phone_value'] ?? '');
const scheduleText = @json(trim(($t['contacts']['schedule_days'] ?? '') . ' ' . ($t['contacts']['schedule_hours'] ?? '')));
document.addEventListener("DOMContentLoaded", () => {
        const bakuCenter = [40.4093, 49.8671];

        const map = L.map("map", {
            center: bakuCenter,
            zoom: 12,
            scrollWheelZoom: false,
            zoomControl: false,
            attributionControl: false,
        });

        L.tileLayer(
            "https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png",
            {
                subdomains: "abcd",
                maxZoom: 19,
            },
        ).addTo(map);

        document.getElementById("zoom-in").addEventListener("click", () => {
            map.zoomIn();
        });
        document.getElementById("zoom-out").addEventListener("click", () => {
            map.zoomOut();
        });

        const pulseIcon = L.divIcon({
            className: "",
            html: `
            <div class="pulse-marker">
                <div class="pulse-marker__ring pulse-marker__ring--1"></div>
                <div class="pulse-marker__ring pulse-marker__ring--2"></div>
                <div class="pulse-marker__ring pulse-marker__ring--3"></div>
                <div class="pulse-marker__core">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none">
                        <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z" fill="white"/>
                    </svg>
                </div>
            </div>
        `,
            iconSize: [60, 60],
            iconAnchor: [30, 30],
            popupAnchor: [0, -36],
        });

        const marker = L.marker(bakuCenter, { icon: pulseIcon }).addTo(map);

        marker
            .bindPopup(
                `
        <div class="modern-popup">
            <div class="modern-popup__header">
                <div class="modern-popup__icon">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                        <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z" fill="white"/>
                    </svg>
                </div>
                <div>
                    <div class="modern-popup__title">${companyName}</div>
                    <div class="modern-popup__sub">${officeLabel}</div>
                </div>
            </div>
            <div class="modern-popup__divider"></div>
            <div class="modern-popup__row">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none">
                    <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z" stroke="#1c508f" stroke-width="2" fill="none"/>
                </svg>
                <span>${popupAddress}</span>
            </div>
            <div class="modern-popup__row">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none">
                    <path d="M6.6 10.8c1.4 2.8 3.8 5.1 6.6 6.6l2.2-2.2c.3-.3.7-.4 1-.2 1.1.4 2.3.6 3.6.6.6 0 1 .4 1 1V20c0 .6-.4 1-1 1-9.4 0-17-7.6-17-17 0-.6.4-1 1-1h3.5c.6 0 1 .4 1 1 0 1.3.2 2.5.6 3.6.1.3 0 .7-.2 1L6.6 10.8z" stroke="#1c508f" stroke-width="2" fill="none"/>
                </svg>
                <span>${phoneValue}</span>
            </div>
            <div class="modern-popup__row">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none">
                    <rect x="2" y="4" width="20" height="16" rx="2" stroke="#1c508f" stroke-width="2" fill="none"/>
                    <path d="M2 8l10 6 10-6" stroke="#1c508f" stroke-width="2" fill="none"/>
                </svg>
                <span>${scheduleText}</span>
            </div>
        </div>
    `,
                {
                    className: "modern-popup-wrapper",
                    maxWidth: 240,
                    offset: [0, -10],
                },
            )
            .openPopup();

        fetch(
            "https://nominatim.openstreetmap.org/search?city=Baku&country=Azerbaijan&polygon_geojson=1&format=json",
        )
            .then((res) => res.json())
            .then((data) => {
                if (data && data[0] && data[0].geojson) {
                    L.geoJSON(data[0].geojson, {
                        style: {
                            color: "transparent",
                            fillColor: "#1c508f",
                            fillOpacity: 0.07,
                        },
                    }).addTo(map);

                    L.geoJSON(data[0].geojson, {
                        style: {
                            color: "#1c508f",
                            weight: 2.5,
                            opacity: 0.45,
                            fillOpacity: 0,
                            dashArray: "0",
                            lineCap: "round",
                            lineJoin: "round",
                        },
                    }).addTo(map);

                    L.geoJSON(data[0].geojson, {
                        style: {
                            color: "#4a90d9",
                            weight: 1,
                            opacity: 0.6,
                            fillOpacity: 0,
                            dashArray: "8 12",
                            lineCap: "round",
                        },
                    }).addTo(map);
                }
            })
            .catch((err) => console.warn("Границы не загружены:", err));

        setTimeout(() => {
            document
                .querySelector(".contacts-page__map-wrapper")
                .classList.add("is-loaded");
        }, 300);
    });
</script>
@endpush