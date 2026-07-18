{{--
    ═══════════════════════════════════════════════════════════════
    SERVICES SECTION — Almep Trading
    Портировано из Astro-компонента, стиль/подход согласован с
    partials/hero.blade.php и partials/about.blade.php
    (fluid CSS через clamp(), без Tailwind).

    Ожидает переменные: $t (массив переводов), $lang (текущий язык)

    $t['services']['title']
    $t['services']['items']       — массив: [ ['title'=>.., 'description'=>.., 'number'=>..], ... ]
    $t['services']['btn_catalog']
    ═══════════════════════════════════════════════════════════════
--}}
@php
    $servicesTitle = $t['services']['title']       ?? 'Услуги';
    $items         = $t['services']['items']        ?? [];
    $btnCatalog    = $t['services']['btn_catalog']   ?? 'Перейти в каталог';
@endphp

<section class="services">
    <div class="services__inner">
        <h2 class="services__title">{{ $servicesTitle }}</h2>

        <div class="services__content">
            <div class="services__grid">
                @foreach($items as $index => $service)
                    @php
                        $image = asset('assets/images/service-' . ($index + 1) . '.png');
                    @endphp
                    <div class="services__card">

                        {{-- Фон: фото + оверлей --}}
                        <div class="services__bg">
                            <img src="{{ $image }}" alt="{{ $service['title'] ?? '' }}" loading="lazy">
                            <div class="services__overlay"></div>
                        </div>

                        {{-- Контент --}}
                        <div class="services__body">
                            <div class="services__top">
                                <h3 class="services__card-title">{{ $service['title'] ?? '' }}</h3>
                                <p class="services__card-desc">{{ $service['description'] ?? '' }}</p>
                            </div>
                            <div class="services__footer">
                                <span class="services__number">{{ $service['number'] ?? '' }}</span>

                                {{-- Стрелка диагональная (обычный стейт, чёрная) --}}
                                <svg class="arrow-diagonal" width="27" height="27" viewBox="0 0 27 27" fill="none">
                                    <path d="M3 24L24 3M24 3H8M24 3V19" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>

                                {{-- Стрелка вниз (hover, белая) --}}
                                <svg class="arrow-down" width="27" height="29" viewBox="0 0 27 29" fill="none">
                                    <path d="M13.5 1V28M13.5 28L2 16.5M13.5 28L25 16.5" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                        </div>

                    </div>
                @endforeach
            </div>

            <div class="services__cta">
                <a href="/{{ $lang }}/products" class="services__btn">{{ $btnCatalog }}</a>
            </div>
        </div>
    </div>
</section>

<style>
/* ─── Токены (согласованы с Hero/About) ─────────────────────── */
.services {
    --blue:  #1C508F;
    --dblue: #174480;
    --tr: .3s ease;
    --services-px: clamp(16px, 6vw, 115px);

    width: 100%;
    background: #fff;
    box-sizing: border-box;
}
.services *, .services *::before, .services *::after { box-sizing: border-box; }

.services__inner {
    width: 100%;
    margin: 0 auto;
    padding: clamp(40px, 6vh, 90px) var(--services-px);
    display: flex;
    flex-direction: column;
    gap: clamp(28px, 3.6vh, 40px);
}

.services__title {
    margin: 0;
    font-family: 'Montserrat', sans-serif;
    font-weight: 500;
    font-size: clamp(24px, 3.2vw, 40px);
    line-height: 1.1;
    color: #000;
}

.services__content {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: clamp(28px, 3.5vh, 40px);
    width: 100%;
}

/* ─── Сетка карточек ─────────────────────────────────────────── */
.services__grid {
    display: flex;
    flex-direction: column;
    gap: clamp(20px, 2.5vw, 30px);
    width: 100%;
}

/* ─── Карточка ───────────────────────────────────────────────── */
.services__card {
    position: relative;
    width: 100%;
    height: clamp(260px, 32vw, 315px);
    border-radius: 7px;
    overflow: hidden;
    background: #fff;
    box-shadow: 0px 0px 4px rgba(0, 0, 0, 0.3);
    cursor: pointer;
}

/* ─── Фон карточки ───────────────────────────────────────────── */
.services__bg {
    position: absolute;
    inset: 0;
    z-index: 1;
    opacity: 0;
    transition: opacity 0.4s ease;
}
.services__bg img {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.services__overlay {
    position: absolute;
    inset: 0;
    background: rgba(0, 0, 0, 0.56);
}

/* ─── Контент карточки ───────────────────────────────────────── */
.services__body {
    position: absolute;
    inset: 0;
    z-index: 2;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    padding: clamp(24px, 3vw, 26px) clamp(20px, 2.5vw, 25px) clamp(20px, 2.5vw, 20px);
}

.services__top {
    display: flex;
    flex-direction: column;
    gap: clamp(14px, 1.8vw, 20px);
}

.services__card-title {
    margin: 0;
    font-family: 'Montserrat', sans-serif;
    font-weight: 500;
    font-size: clamp(19px, 1.6vw, 20px);
    line-height: 1.1;
    letter-spacing: -0.01em;
    color: #000;
    transition: color var(--tr);
}

/* Описание — скрыто по умолчанию, появляется по hover */
.services__card-desc {
    margin: 0;
    font-family: 'Montserrat', sans-serif;
    font-weight: 400;
    font-size: clamp(15px, 1.2vw, 16px);
    line-height: 1.3;
    letter-spacing: -0.01em;
    color: #fff;
    max-width: 100%;
    opacity: 0;
    transform: translateY(8px);
    transition: opacity .35s ease .05s, transform .35s ease .05s;
}

/* ─── Футер карточки ─────────────────────────────────────────── */
.services__footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: relative;
}

.services__number {
    font-family: 'Raleway', sans-serif;
    font-weight: 400;
    font-size: clamp(18px, 1.6vw, 20px);
    line-height: 1.1;
    color: #000;
    transition: color var(--tr);
}

/* ─── Стрелки ────────────────────────────────────────────────── */
.arrow-diagonal {
    opacity: 1;
    flex-shrink: 0;
    transition: opacity .3s ease;
}
.arrow-down {
    opacity: 0;
    flex-shrink: 0;
    position: absolute;
    right: 0;
    bottom: 0;
    transition: opacity .3s ease;
}

/* ─── Hover-состояние ────────────────────────────────────────── */
.services__card:hover .services__bg          { opacity: 1; }
.services__card:hover .services__card-title  { color: #fff; }
.services__card:hover .services__number      { color: #fff; }
.services__card:hover .services__card-desc   { opacity: 1; transform: translateY(0); }
.services__card:hover .arrow-diagonal        { opacity: 0; }
.services__card:hover .arrow-down            { opacity: 1; }

/* На тач-устройствах без hover — раскрываем карточку по тапу/фокусу,
   чтобы описание не оставалось недоступным на телефонах и планшетах */
.services__card:focus-within .services__bg         { opacity: 1; }
.services__card:focus-within .services__card-title { color: #fff; }
.services__card:focus-within .services__number     { color: #fff; }
.services__card:focus-within .services__card-desc  { opacity: 1; transform: translateY(0); }
.services__card:focus-within .arrow-diagonal       { opacity: 0; }
.services__card:focus-within .arrow-down           { opacity: 1; }

/* ─── Кнопка ─────────────────────────────────────────────────── */
.services__cta { width: 100%; }

.services__btn {
    display: flex;
    align-items: center;
    justify-content: center;
    box-sizing: border-box;
    width: 100%;
    height: clamp(60px, 8vh, 85px);
    padding: 0 24px;
    background: var(--blue);
    box-shadow: 0px 0px 4px rgba(0, 0, 0, 0.3);
    border-radius: 9px;
    font-family: 'Montserrat', sans-serif;
    font-weight: 400;
    font-size: clamp(15px, 1.2vw, 17px);
    line-height: 1.3;
    letter-spacing: -0.01em;
    color: #fff;
    text-decoration: none;
    white-space: nowrap;
    transition: background .2s ease;
}
.services__btn:hover  { background: var(--dblue); }
.services__btn:focus-visible { outline: 2px solid var(--dblue); outline-offset: 3px; }

/* ─── Планшет/десктоп (≥ 769px): сетка 2×2 ──────────────────── */
@media (min-width: 769px) {
    .services__grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        grid-auto-rows: clamp(200px, 22vw, 220px);
        gap: clamp(18px, 2vw, 25px);
    }
    .services__card { height: 100%; }

    .services__btn { width: 300px; }
}

/* ─── Мелкая подстраховка от переполнения на очень узких экранах ─ */
@media (max-width: 380px) {
    .services__inner { padding-left: 14px; padding-right: 14px; }
    .services__title { font-size: 22px; }
}
</style>