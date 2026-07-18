{{--
    ═══════════════════════════════════════════════════════════════
    CONTACTS SECTION — Almep Trading
    Реализовано по Figma-раскладке:
      Заголовок
      └ [ Инфо-блок 448px | Фото/карта 930px ]   (ряд, gap 33)
          Инфо-блок:
            [Адрес] [Телефон]                     (ряд, gap 19)
            [График работы] [E-mail]               (ряд, gap 85)
      Кнопка "Запросить консультацию" (330×85)

    Фикс. пиксели Figma переведены в адаптивные единицы (clamp/%),
    но сохранены пропорции и порядок блоков.

    Ожидает переменные: $t (переводы), $lang (текущий язык)
    Использование:
        @include('partials.contacts', ['t' => $t, 'lang' => $lang])

    Ключи переводов:
    $t['contacts']['title']
    $t['contacts']['address_label']  / address_value
    $t['contacts']['phone_label']    / phone_value
    $t['contacts']['schedule_label'] / schedule_days / schedule_hours
    $t['contacts']['email_label']    / email_value
    $t['contacts']['cta_label']      — текст кнопки ("Запросить консультацию")
    $t['contacts']['company_name'] / city / office_label / popup_address
    $t['contacts']['zoom_in'] / zoom_out
    ═══════════════════════════════════════════════════════════════

    ВАЖНО: в Figma справа стоит статичное фото офиса ("image 23"), но
    т.к. файла фото нет — оставил интерактивную карту (Leaflet) в том
    же слоте с теми же пропорциями. Если нужно именно фото — пришли
    файл, поменяю .contacts__map-wrapper на <img>.

    Кнопка внизу рассчитана на открытие модалки консультации:
    добавлен data-атрибут data-open-modal="consultation" —
    подключи свой обработчик (или пришли ConsultationController /
    разметку модалки, и я привяжу вызов напрямую).
--}}
@php
    $c = $t['contacts'] ?? [];
@endphp

<section class="contacts">
    <div class="contacts__inner">

        <h2 class="contacts__title">{{ $c['title'] ?? 'Контакты' }}</h2>

        <div class="contacts__body">

            <div class="contacts__row-top">

                <!-- Левая часть: Информация -->
                <div class="contacts__left">

                    <!-- Ряд 1: Адрес + Телефон -->
                    <div class="contacts__row">
                        <div class="contacts__block">
                            <h3 class="contacts__label">{{ $c['address_label'] ?? 'Адрес' }}</h3>
                            <p class="contacts__value contacts__value--address">{{ $c['address_value'] ?? '' }}</p>
                        </div>

                        <div class="contacts__block">
                            <h3 class="contacts__label">{{ $c['phone_label'] ?? 'Телефон' }}</h3>
                            <a href="tel:{{ preg_replace('/\s/', '', $c['phone_value'] ?? '') }}" class="contacts__value contacts__value--dark contacts__value--link">
                                {{ $c['phone_value'] ?? '' }}
                            </a>
                        </div>
                    </div>

                    <!-- Ряд 2: График работы + E-mail -->
                    <div class="contacts__row">
                        <div class="contacts__block">
                            <h3 class="contacts__label">{{ $c['schedule_label'] ?? 'График работы' }}</h3>
                            <div class="contacts__schedule">
                                <p class="contacts__value contacts__value--address">{{ $c['schedule_days'] ?? '' }}</p>
                                <p class="contacts__value contacts__value--address">{{ $c['schedule_hours'] ?? '' }}</p>
                            </div>
                        </div>

                        <div class="contacts__block">
                            <h3 class="contacts__label">{{ $c['email_label'] ?? 'E-mail' }}</h3>
                            <a href="mailto:{{ $c['email_value'] ?? '' }}" class="contacts__value contacts__value--dark contacts__value--link">
                                {{ $c['email_value'] ?? '' }}
                            </a>
                        </div>
                    </div>

                </div>

                <!-- Правая часть: Фото/карта -->
                <div class="contacts__map-wrapper">
                    <div class="contacts__map-overlay-top"></div>
                    <div class="contacts__map-overlay-bottom"></div>
                    <div class="contacts__map" id="contactsMap"></div>

                    <div class="contacts-map-controls">
                        <button class="contacts-map-controls__btn" id="contactsZoomIn" aria-label="{{ $c['zoom_in'] ?? 'Zoom in' }}">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                                <path d="M8 2V14M2 8H14" stroke="currentColor" stroke-width="2" stroke-linecap="round"></path>
                            </svg>
                        </button>
                        <div class="contacts-map-controls__divider"></div>
                        <button class="contacts-map-controls__btn" id="contactsZoomOut" aria-label="{{ $c['zoom_out'] ?? 'Zoom out' }}">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                                <path d="M2 8H14" stroke="currentColor" stroke-width="2" stroke-linecap="round"></path>
                            </svg>
                        </button>
                    </div>

                    <div class="contacts-map-badge">
                        <div class="contacts-map-badge__dot"></div>
                        <div class="contacts-map-badge__text">
                            <span class="contacts-map-badge__title">{{ $c['company_name'] ?? '' }}</span>
                            <span class="contacts-map-badge__sub">{{ $c['city'] ?? '' }}</span>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Кнопка -->
            <button type="button" class="contacts__cta-btn" data-open-modal="consultation">
                {{ $t['contacts']['btn_consultation'] ?? 'Запросить консультацию' }}
            </button>

        </div>
    </div>
</section>

<style>
/* ─── Токены ──────────────────────────────────────────────────── */
.contacts {
    --blue:  #1C508F;
    --dblue: #174480;
    --contacts-px: clamp(16px, 6vw, 115px);
    --text: #000000;
    --text-value: #151515;

    width: 100%;
    background: #fff;
    box-sizing: border-box;
}
.contacts *, .contacts *::before, .contacts *::after { box-sizing: border-box; }

.contacts__inner {
    width: 100%;
    margin: 0 auto;
    padding: clamp(40px, 6vh, 90px) var(--contacts-px);
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: clamp(32px, 5vh, 60px);
}

.contacts__title {
    margin: 0;
    width: 100%;
    font-family: 'Montserrat', sans-serif;
    font-weight: 500;
    font-size: clamp(28px, 3.2vw, 48px);
    line-height: 110%;
    color: var(--text);
}

.contacts__body {
    width: 100%;
    display: flex;
    flex-direction: column;
    gap: clamp(32px, 4vh, 50px);
}

/* ─── Верхний ряд: инфо + фото/карта ─────────────────────────── */
.contacts__row-top {
    width: 100%;
    display: flex;
    flex-direction: row;
    align-items: flex-start;
    gap: clamp(24px, 2.4vw, 33px);
}

/* ─── Левая часть: инфо-блоки ─────────────────────────────────── */
.contacts__left {
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
    gap: clamp(28px, 3.5vw, 50px);
    width: clamp(320px, 32vw, 448px);
    flex-shrink: 0;
}

.contacts__row {
    display: flex;
    flex-direction: row;
    flex-wrap: wrap;
    align-items: flex-start;
    gap: clamp(20px, 3vw, 50px) clamp(28px, 6vw, 85px);
    width: 100%;
}

.contacts__block {
    display: flex;
    flex-direction: column;
    gap: clamp(12px, 1.6vw, 25px);
    flex: 1 1 140px;
    min-width: 130px;
}

.contacts__label {
    margin: 0;
    font-family: 'Montserrat', sans-serif;
    font-weight: 500;
    font-size: clamp(17px, 1.6vw, 20px);
    line-height: 120%;
    color: var(--text);
}

.contacts__value {
    margin: 0;
    font-family: 'Montserrat', sans-serif;
    font-weight: 400;
    font-size: clamp(15px, 1.3vw, 18px);
    line-height: 130%;
    letter-spacing: -0.01em;
    text-decoration: none;
    color: var(--text-value);
}
.contacts__value--dark { color: #000000; }

.contacts__value--link { transition: opacity .2s ease; }
.contacts__value--link:hover { opacity: .65; }

.contacts__schedule {
    display: flex;
    flex-direction: column;
    gap: 2px;
}

/* ─── Правая часть: фото/карта ───────────────────────────────── */
.contacts__map-wrapper {
    position: relative;
    flex: 1 1 auto;
    min-width: 0;
    aspect-ratio: 930 / 484;
    max-height: clamp(320px, 45vw, 484px);
    border-radius: 20px;
    overflow: hidden;
    box-shadow:
        0 2px 4px rgba(28, 80, 143, 0.06),
        0 8px 24px rgba(28, 80, 143, 0.12),
        0 24px 64px rgba(28, 80, 143, 0.1);
    opacity: 0;
    transform: translateY(16px);
    transition: opacity 0.6s ease, transform 0.6s ease;
}
.contacts__map-wrapper.is-loaded { opacity: 1; transform: translateY(0); }

.contacts__map-wrapper::after {
    content: "";
    position: absolute;
    inset: 0;
    border-radius: 20px;
    border: 1.5px solid rgba(28, 80, 143, 0.15);
    pointer-events: none;
    z-index: 10;
}

.contacts__map-overlay-top {
    position: absolute; top: 0; left: 0; right: 0; height: 80px;
    background: linear-gradient(to bottom, rgba(240, 245, 255, 0.35), transparent);
    pointer-events: none; z-index: 5; border-radius: 20px 20px 0 0;
}
.contacts__map-overlay-bottom {
    position: absolute; bottom: 0; left: 0; right: 0; height: 80px;
    background: linear-gradient(to top, rgba(240, 245, 255, 0.3), transparent);
    pointer-events: none; z-index: 5; border-radius: 0 0 20px 20px;
}
.contacts__map { width: 100%; height: 100%; z-index: 0; }

/* ─── Контролы карты ─────────────────────────────────────────── */
.contacts-map-controls {
    position: absolute; top: 20px; right: 20px; z-index: 20;
    display: flex; flex-direction: column; align-items: center;
    background: rgba(255, 255, 255, 0.92);
    backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px);
    border-radius: 12px; border: 1px solid rgba(28, 80, 143, 0.12);
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08), 0 1px 4px rgba(0, 0, 0, 0.06);
    overflow: hidden;
}
.contacts-map-controls__btn {
    width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;
    background: transparent; border: none; cursor: pointer; color: #1c508f;
    transition: background 0.15s ease, color 0.15s ease;
}
.contacts-map-controls__btn:hover { background: rgba(28, 80, 143, 0.08); color: #174480; }
.contacts-map-controls__btn:active { background: rgba(28, 80, 143, 0.15); }
.contacts-map-controls__divider { width: 24px; height: 1px; background: rgba(28, 80, 143, 0.12); }

/* ─── Бейдж локации ──────────────────────────────────────────── */
.contacts-map-badge {
    position: absolute; bottom: 20px; right: 20px; z-index: 20;
    display: flex; align-items: center; gap: 10px;
    background: rgba(255, 255, 255, 0.92);
    backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px);
    border-radius: 12px; border: 1px solid rgba(28, 80, 143, 0.12);
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08), 0 1px 4px rgba(0, 0, 0, 0.04);
    padding: 10px 16px; max-width: calc(100% - 40px);
}
.contacts-map-badge__dot {
    width: 10px; height: 10px; border-radius: 50%; background: #1c508f;
    box-shadow: 0 0 0 3px rgba(28, 80, 143, 0.2); flex-shrink: 0;
    animation: contactsBadgePulse 2.5s ease-in-out infinite;
}
@keyframes contactsBadgePulse {
    0%, 100% { box-shadow: 0 0 0 3px rgba(28, 80, 143, 0.2); }
    50% { box-shadow: 0 0 0 6px rgba(28, 80, 143, 0.08); }
}
.contacts-map-badge__text { display: flex; flex-direction: column; gap: 1px; min-width: 0; }
.contacts-map-badge__title {
    font-family: 'Montserrat', sans-serif; font-weight: 600; font-size: 13px; color: #151515;
    line-height: 1.3; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.contacts-map-badge__sub { font-family: 'Montserrat', sans-serif; font-weight: 400; font-size: 11px; color: #6b7280; line-height: 1.3; }

/* ─── Маркер и попап ─────────────────────────────────────────── */
.contacts-pulse-marker { position: relative; width: 60px; height: 60px; display: flex; align-items: center; justify-content: center; }
.contacts-pulse-marker__ring { position: absolute; border-radius: 50%; background: rgba(28, 80, 143, 0.15); animation: contactsPulseRing 2.4s ease-out infinite; }
.contacts-pulse-marker__ring--1 { width: 60px; height: 60px; animation-delay: 0s; }
.contacts-pulse-marker__ring--2 { width: 44px; height: 44px; animation-delay: 0.3s; background: rgba(28, 80, 143, 0.2); }
.contacts-pulse-marker__ring--3 { width: 30px; height: 30px; animation-delay: 0.6s; background: rgba(28, 80, 143, 0.25); }
@keyframes contactsPulseRing {
    0% { transform: scale(0.7); opacity: 1; }
    70% { transform: scale(1.1); opacity: 0.3; }
    100% { transform: scale(1.2); opacity: 0; }
}
.contacts-pulse-marker__core {
    position: absolute; width: 36px; height: 36px;
    background: linear-gradient(135deg, #1c508f 0%, #2d6bb5 100%);
    border-radius: 50%; display: flex; align-items: center; justify-content: center;
    box-shadow: 0 4px 12px rgba(28, 80, 143, 0.5), 0 0 0 3px rgba(255, 255, 255, 0.9);
    z-index: 2;
}

.contacts-modern-popup-wrapper .leaflet-popup-content-wrapper {
    padding: 0; border-radius: 16px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12), 0 2px 8px rgba(0, 0, 0, 0.08);
    border: 1px solid rgba(28, 80, 143, 0.1); overflow: hidden;
}
.contacts-modern-popup-wrapper .leaflet-popup-content { margin: 0; width: 240px !important; }
.contacts-modern-popup-wrapper .leaflet-popup-tip-container { margin-top: -1px; }
.contacts-modern-popup-wrapper .leaflet-popup-tip { box-shadow: none; background: #fff; }

.contacts-modern-popup { font-family: 'Montserrat', sans-serif; overflow: hidden; }
.contacts-modern-popup__header {
    display: flex; align-items: center; gap: 12px; padding: 14px 16px 12px;
    background: linear-gradient(135deg, #1c508f 0%, #2563b0 100%);
}
.contacts-modern-popup__icon {
    width: 32px; height: 32px; background: rgba(255, 255, 255, 0.2); border-radius: 8px;
    display: flex; align-items: center; justify-content: center; flex-shrink: 0;
}
.contacts-modern-popup__title { font-weight: 600; font-size: 14px; color: #ffffff; line-height: 1.3; }
.contacts-modern-popup__sub { font-weight: 400; font-size: 11px; color: rgba(255, 255, 255, 0.7); line-height: 1.3; }
.contacts-modern-popup__divider { height: 1px; background: rgba(28, 80, 143, 0.08); }
.contacts-modern-popup__row {
    display: flex; align-items: center; gap: 9px; padding: 9px 16px; font-size: 12px;
    color: #374151; font-weight: 400; line-height: 1.4;
    border-bottom: 1px solid rgba(0, 0, 0, 0.04); transition: background 0.15s ease;
}
.contacts-modern-popup__row:last-child { border-bottom: none; }
.contacts-modern-popup__row:hover { background: rgba(28, 80, 143, 0.04); }

.contacts__map .leaflet-control-zoom { display: none; }
.contacts__map .leaflet-control-attribution {
    font-size: 10px; background: rgba(255, 255, 255, 0.7); backdrop-filter: blur(4px);
    border-radius: 6px 0 0 0; padding: 3px 6px; color: #9ca3af;
}
.contacts__map .leaflet-control-attribution a { color: #1c508f; }

/* ─── Кнопка "Запросить консультацию" ────────────────────────── */
.contacts__cta-btn {
    display: inline-flex;
    align-self: flex-start;
    align-items: center;
    justify-content: center;
    height: clamp(60px, 7vw, 85px);
    width: min(330px, 100%);
    padding: 0 clamp(24px, 3vw, 40px);
    background: var(--blue);
    box-shadow: 0px 0px 4px rgba(0, 0, 0, 0.3);
    border-radius: 9px;
    border: none;
    cursor: pointer;
    font-family: 'Montserrat', sans-serif;
    font-weight: 400;
    font-size: clamp(15px, 1.3vw, 18px);
    line-height: 130%;
    letter-spacing: -0.01em;
    text-align: center;
    color: #FFFFFF;
    transition: background .2s ease;
}
.contacts__cta-btn:hover { background: var(--dblue); }
.contacts__cta-btn:focus-visible { outline: 2px solid var(--dblue); outline-offset: 3px; }

/* ─── Клавиатурная доступность ───────────────────────────────── */
.contacts__value--link:focus-visible,
.contacts-map-controls__btn:focus-visible {
    outline: 2px solid var(--blue);
    outline-offset: 3px;
}

@media (prefers-reduced-motion: reduce) {
    .contacts__map-wrapper,
    .contacts__value--link,
    .contacts-map-controls__btn,
    .contacts__cta-btn { transition: none !important; }
    .contacts-map-badge__dot,
    .contacts-pulse-marker__ring { animation: none !important; }
}

/* ─── Адаптив ────────────────────────────────────────────────── */
@media (max-width: 900px) {
    .contacts__row-top {
        flex-direction: column;
    }
    .contacts__left {
        width: 100%;
        order: 2;
    }
    .contacts__map-wrapper {
        width: 100%;
        order: 1;
        aspect-ratio: 16 / 9;
        max-height: clamp(280px, 60vw, 420px);
    }
}

@media (max-width: 560px) {
    .contacts__row {
        flex-direction: column;
        gap: clamp(20px, 5vw, 28px);
    }
    .contacts__block { min-width: 0; }
    .contacts-map-controls { top: 10px; right: 10px; }
    .contacts-map-badge { bottom: 10px; right: 10px; padding: 8px 12px; }
    .contacts__cta-btn { width: 100%; min-width: 0; }
}

@media (max-width: 380px) {
    .contacts__inner { padding-left: 14px; padding-right: 14px; }
}
</style>

<script>
(function () {
    function initContactsMap() {
        var mapEl = document.getElementById('contactsMap');
        if (!mapEl || mapEl.dataset.mapInit === '1') return;

        function boot() {
            mapEl.dataset.mapInit = '1';

            var companyName   = @json($c['company_name'] ?? '');
            var officeLabel   = @json($c['office_label'] ?? '');
            var popupAddress  = @json($c['popup_address'] ?? '');
            var phoneValue    = @json($c['phone_value'] ?? '');
            var scheduleText  = @json(trim(($c['schedule_days'] ?? '') . ' ' . ($c['schedule_hours'] ?? '')));

            var bakuCenter = [40.4093, 49.8671];

            var map = L.map('contactsMap', {
                center: bakuCenter,
                zoom: 12,
                scrollWheelZoom: false,
                zoomControl: false,
                attributionControl: false,
            });

            L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
                subdomains: 'abcd',
                maxZoom: 19,
            }).addTo(map);

            var zoomInBtn = document.getElementById('contactsZoomIn');
            var zoomOutBtn = document.getElementById('contactsZoomOut');
            if (zoomInBtn) zoomInBtn.addEventListener('click', function () { map.zoomIn(); });
            if (zoomOutBtn) zoomOutBtn.addEventListener('click', function () { map.zoomOut(); });

            var pulseIcon = L.divIcon({
                className: '',
                html:
                    '<div class="contacts-pulse-marker">' +
                        '<div class="contacts-pulse-marker__ring contacts-pulse-marker__ring--1"></div>' +
                        '<div class="contacts-pulse-marker__ring contacts-pulse-marker__ring--2"></div>' +
                        '<div class="contacts-pulse-marker__ring contacts-pulse-marker__ring--3"></div>' +
                        '<div class="contacts-pulse-marker__core">' +
                            '<svg width="14" height="14" viewBox="0 0 24 24" fill="none">' +
                                '<path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z" fill="white"/>' +
                            '</svg>' +
                        '</div>' +
                    '</div>',
                iconSize: [60, 60],
                iconAnchor: [30, 30],
                popupAnchor: [0, -36],
            });

            var marker = L.marker(bakuCenter, { icon: pulseIcon }).addTo(map);

            marker.bindPopup(
                '<div class="contacts-modern-popup">' +
                    '<div class="contacts-modern-popup__header">' +
                        '<div class="contacts-modern-popup__icon">' +
                            '<svg width="16" height="16" viewBox="0 0 24 24" fill="none">' +
                                '<path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z" fill="white"/>' +
                            '</svg>' +
                        '</div>' +
                        '<div>' +
                            '<div class="contacts-modern-popup__title">' + companyName + '</div>' +
                            '<div class="contacts-modern-popup__sub">' + officeLabel + '</div>' +
                        '</div>' +
                    '</div>' +
                    '<div class="contacts-modern-popup__divider"></div>' +
                    '<div class="contacts-modern-popup__row">' +
                        '<svg width="13" height="13" viewBox="0 0 24 24" fill="none">' +
                            '<path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z" stroke="#1c508f" stroke-width="2" fill="none"/>' +
                        '</svg>' +
                        '<span>' + popupAddress + '</span>' +
                    '</div>' +
                    '<div class="contacts-modern-popup__row">' +
                        '<svg width="13" height="13" viewBox="0 0 24 24" fill="none">' +
                            '<path d="M6.6 10.8c1.4 2.8 3.8 5.1 6.6 6.6l2.2-2.2c.3-.3.7-.4 1-.2 1.1.4 2.3.6 3.6.6.6 0 1 .4 1 1V20c0 .6-.4 1-1 1-9.4 0-17-7.6-17-17 0-.6.4-1 1-1h3.5c.6 0 1 .4 1 1 0 1.3.2 2.5.6 3.6.1.3 0 .7-.2 1L6.6 10.8z" stroke="#1c508f" stroke-width="2" fill="none"/>' +
                        '</svg>' +
                        '<span>' + phoneValue + '</span>' +
                    '</div>' +
                    '<div class="contacts-modern-popup__row">' +
                        '<svg width="13" height="13" viewBox="0 0 24 24" fill="none">' +
                            '<rect x="2" y="4" width="20" height="16" rx="2" stroke="#1c508f" stroke-width="2" fill="none"/>' +
                            '<path d="M2 8l10 6 10-6" stroke="#1c508f" stroke-width="2" fill="none"/>' +
                        '</svg>' +
                        '<span>' + scheduleText + '</span>' +
                    '</div>' +
                '</div>',
                {
                    className: 'contacts-modern-popup-wrapper',
                    maxWidth: 240,
                    offset: [0, -10],
                }
            ).openPopup();

            fetch('https://nominatim.openstreetmap.org/search?city=Baku&country=Azerbaijan&polygon_geojson=1&format=json')
                .then(function (res) { return res.json(); })
                .then(function (data) {
                    if (data && data[0] && data[0].geojson) {
                        L.geoJSON(data[0].geojson, { style: { color: 'transparent', fillColor: '#1c508f', fillOpacity: 0.07 } }).addTo(map);
                        L.geoJSON(data[0].geojson, { style: { color: '#1c508f', weight: 2.5, opacity: 0.45, fillOpacity: 0, dashArray: '0', lineCap: 'round', lineJoin: 'round' } }).addTo(map);
                        L.geoJSON(data[0].geojson, { style: { color: '#4a90d9', weight: 1, opacity: 0.6, fillOpacity: 0, dashArray: '8 12', lineCap: 'round' } }).addTo(map);
                    }
                })
                .catch(function (err) { console.warn('Границы не загружены:', err); });

            setTimeout(function () {
                var wrapper = mapEl.closest('.contacts__map-wrapper');
                if (wrapper) wrapper.classList.add('is-loaded');
            }, 300);
        }

        if (typeof L === 'undefined') {
            if (!document.getElementById('leaflet-css')) {
                var link = document.createElement('link');
                link.id = 'leaflet-css';
                link.rel = 'stylesheet';
                link.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
                document.head.appendChild(link);
            }
            var script = document.createElement('script');
            script.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
            script.onload = boot;
            document.head.appendChild(script);
        } else {
            boot();
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initContactsMap);
    } else {
        initContactsMap();
    }
})();
</script>