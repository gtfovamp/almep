{{--
    ═══════════════════════════════════════════════════════════════
    HERO SECTION — Almep Trading
    Ожидает переменные: $t (массив переводов), $lang (текущий язык)

    $t['hero']['title']            — заголовок
    $t['hero']['description']      — описание
    $t['hero']['btn_consultation'] — текст кнопки 1 (заявка)
    $t['hero']['btn_catalog']      — текст кнопки 2 (каталог)
    ═══════════════════════════════════════════════════════════════
--}}
@php
    $heroTitle   = $t['hero']['title']            ?? 'Almep Trading — Ваш стратегический партнёр в мире профессиональных решений';
    $heroDesc    = $t['hero']['description']       ?? 'Almep Trading занимается организацией поставок и дистрибуции высококачественных технических и электротехнических решений для бизнеса.';
    $btnConsult  = $t['hero']['btn_consultation']  ?? 'Запросить консультацию';
    $btnCatalog  = $t['hero']['btn_catalog']       ?? 'Перейти в каталог';
@endphp

<section class="hero">
    {{-- Фон + затемнение --}}
    <div class="hero__bg" style="background-image:url('{{ asset('assets/images/hero-bg.png') }}')"></div>
    <div class="hero__overlay"></div>

    {{-- Контент --}}
    <div class="hero__inner">
        <div class="hero__text">
            <h1 class="hero__title">{{ $heroTitle }}</h1>
            <p class="hero__desc">{{ $heroDesc }}</p>
        </div>

        <div class="hero__actions">
            <a href="/{{ $lang }}/contacts" class="hero__btn hero__btn--primary">
                {{ $btnConsult }}
            </a>
            <a href="/{{ $lang }}/products" class="hero__btn hero__btn--ghost">
                {{ $btnCatalog }}
            </a>
        </div>
    </div>
</section>

<style>
/* ─── Токены (согласованы с Header.blade.php) ───────────────── */
.hero {
    --blue:  #1C508F;
    --dblue: #174480;
    --tr:    .25s ease;
    --hero-px: clamp(16px, 6vw, 115px);

    position: relative;
    isolation: isolate;
    width: 100%;
    min-height: 100vh;
    /* безопасная минимальная высота на маленьких экранах, чтобы контент не сжимался в кашу */
    min-height: clamp(560px, 100svh, 980px);
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    color: #fff;
}

/* ─── Фон ────────────────────────────────────────────────────── */
.hero__bg {
    position: absolute;
    inset: 0;
    z-index: 0;
    background-position: center;
    background-size: cover;
    background-repeat: no-repeat;
    /* без transform: scale — на некоторых браузерах масштабирование даёт субпиксельное
       сглаживание и картинка выглядит слегка размытой */
}
.hero__overlay {
    position: absolute;
    inset: 0;
    z-index: 1;
    /* точное значение из Figma: чистый чёрный 58%, без синего оттенка и без градиента */
    background: rgba(0, 0, 0, 0.58);
}

/* ─── Контентный блок ────────────────────────────────────────── */
.hero__inner {
    position: relative;
    z-index: 2;
    width: 100%;
    max-width: 1180px; /* 1110 контента + запас на паддинг */
    margin: 0 auto;
    padding: clamp(90px, 14vh, 160px) var(--hero-px) clamp(40px, 6vh, 80px);
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: clamp(24px, 3.6vh, 35px); /* 35px — как во Frame 427319059 из Figma */
    text-align: center;
    box-sizing: border-box;
}

.hero__text {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: clamp(16px, 2.2vh, 22px);
    max-width: 1119px; /* точная ширина текстового блока из Figma */
    width: 100%;
}

.hero__title {
    margin: 0;
    font-family: 'Montserrat', sans-serif;
    font-weight: 500; /* как в Figma, не 600 */
    font-size: clamp(24px, 3.6vw, 48px);
    line-height: 1.1; /* 110% из Figma */
    letter-spacing: -0.04em; /* точное значение из Figma */
    color: #fff;
    text-wrap: balance;
}

.hero__desc {
    margin: 0;
    font-family: 'Montserrat', sans-serif;
    font-weight: 400;
    font-size: clamp(14px, 1.4vw, 18px);
    line-height: 1.3; /* 130% из Figma */
    letter-spacing: -0.01em;
    color: #fff; /* в Figma описание тоже чистый белый, без прозрачности */
    max-width: 1110px; /* точная ширина из Figma */
    width: 100%;
    text-wrap: pretty;
}

/* ─── Кнопки ─────────────────────────────────────────────────── */
.hero__actions {
    display: flex;
    flex-direction: column;
    align-items: stretch;
    gap: 14px;
    width: 100%;
    max-width: 340px;
}
@media (min-width: 560px) {
    .hero__actions {
        flex-direction: row;
        justify-content: center;
        align-items: center;
        max-width: none;
        gap: 30px; /* точное значение из Figma (Frame 427319058) */
    }
}

.hero__btn {
    display: flex;
    align-items: center;
    justify-content: center;
    box-sizing: border-box;
    width: 100%;
    height: clamp(52px, 8vh, 85px); /* 85px — высота из Figma на десктопе */
    padding: 0 24px;
    border-radius: 9px;
    font-family: 'Montserrat', sans-serif;
    font-weight: 400; /* как в Figma */
    font-size: clamp(14px, 1.3vw, 18px);
    line-height: 1.3;
    letter-spacing: -0.01em;
    text-decoration: none;
    white-space: nowrap;
    cursor: pointer;
    transition: background var(--tr), border-color var(--tr), transform var(--tr), box-shadow var(--tr);
}
@media (min-width: 560px) {
    .hero__btn--primary { width: 330px; } /* точная ширина из Figma */
    .hero__btn--ghost    { width: 327px; }
}

.hero__btn--primary {
    background: var(--blue);
    color: #fff;
    box-shadow: 0px 0px 4px rgba(0,0,0,.3); /* точное значение из Figma */
}
.hero__btn--primary:hover  { background: var(--dblue); }
.hero__btn--primary:active { transform: translateY(1px); }

.hero__btn--ghost {
    background: transparent; /* в Figma без затемнения фона */
    color: #fff;
    border: 1px solid #fff;
}
.hero__btn--ghost:hover  { background: rgba(255,255,255,.1); }
.hero__btn--ghost:active { transform: translateY(1px); }

/* ─── Фокус для доступности ─────────────────────────────────── */
.hero__btn:focus-visible {
    outline: 2px solid #fff;
    outline-offset: 3px;
}

/* ─── Узкие экраны (< 380px) — подстраховка от переполнения ─── */
@media (max-width: 380px) {
    .hero__inner { padding-left: 14px; padding-right: 14px; }
    .hero__title { font-size: 22px; }
}

</style>