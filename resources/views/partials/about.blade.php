{{--
    ═══════════════════════════════════════════════════════════════
    ABOUT SECTION — Almep Trading
    Портировано из Astro-компонента, стиль/подход согласован с
    partials/hero.blade.php (fluid CSS через clamp(), без Tailwind).

    Ожидает переменные: $t (массив переводов), $lang (текущий язык)

    $t['about']['title']
    $t['about']['intro_1'] / intro_2
    $t['about']['feature_quality_title'] / feature_quality_text
    $t['about']['feature_optimization_title'] / feature_optimization_text
    $t['about']['partners_intro']
    $t['about']['partner_berent'] / partner_ekf / partner_sensh
    $t['about']['conclusion']
    $t['about']['btn_consultation']
    $t['nav']['products']
    ═══════════════════════════════════════════════════════════════
--}}
@php
    $aboutTitle   = $t['about']['title']                    ?? 'О компании';
    $intro1       = $t['about']['intro_1']                  ?? '';
    $intro2       = $t['about']['intro_2']                  ?? '';
    $featQTitle   = $t['about']['feature_quality_title']      ?? '';
    $featQText    = $t['about']['feature_quality_text']       ?? '';
    $featOTitle   = $t['about']['feature_optimization_title'] ?? '';
    $featOText    = $t['about']['feature_optimization_text']  ?? '';
    $partnersIntro= $t['about']['partners_intro']            ?? '';
    $partnerBerent= $t['about']['partner_berent']            ?? '';
    $partnerEkf   = $t['about']['partner_ekf']               ?? '';
    $partnerSensh = $t['about']['partner_sensh']             ?? '';
    $conclusion   = $t['about']['conclusion']                ?? '';
    $btnConsult   = $t['about']['btn_consultation']           ?? 'Запросить консультацию';
    $navProducts  = $t['nav']['products']                     ?? '';
@endphp

<section class="about">
    <div class="about__inner">

        <h2 class="about__title">{{ $aboutTitle }}</h2>

        <div class="about__body">

            {{-- Блок 1: фото слева + текст справа --}}
            <div class="about__row">
                <div class="about__photo">
                    <img src="{{ asset('assets/images/about-1.png') }}" alt="{{ $aboutTitle }}" loading="lazy">
                </div>

                <div class="about__col">
                    <div class="about__text-group">
                        <p class="about__p">{{ $intro1 }}</p>
                        <p class="about__p">{{ $intro2 }}</p>
                    </div>

                    <div class="about__features">
                        <div class="about__feature">
                            <div class="about__feature-icon">
                                <img src="{{ asset('assets/icons/guarantee.svg') }}" alt="{{ $featQTitle }}">
                            </div>
                            <h4 class="about__feature-title">{{ $featQTitle }}</h4>
                            <p class="about__feature-text">{{ $featQText }}</p>
                        </div>

                        <div class="about__feature">
                            <div class="about__feature-icon">
                                <img src="{{ asset('assets/icons/workflow.svg') }}" alt="{{ $featOTitle }}">
                            </div>
                            <h4 class="about__feature-title">{{ $featOTitle }}</h4>
                            <p class="about__feature-text">{{ $featOText }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Блок 2: текст слева + фото справа (на мобильных фото сверху) --}}
            <div class="about__row about__row--reverse">
                <div class="about__photo about__photo--order-first">
                    <img src="{{ asset('assets/images/about-2.png') }}" alt="{{ $aboutTitle }} {{ $navProducts }}" loading="lazy">
                </div>

                <div class="about__col about__col--order-last">
                    <p class="about__p">{{ $partnersIntro }}</p>
                    <ul class="about__list">
                        <li>{{ $partnerBerent }}</li>
                        <li>{{ $partnerEkf }}</li>
                        <li>{{ $partnerSensh }}</li>
                    </ul>
                    <p class="about__p">{{ $conclusion }}</p>
                </div>
            </div>

        </div>

        <button type="button" class="hero__btn hero__btn--primary" data-open-consultation>
                {{ $btnConsult }}
        </button>

    </div>
</section>

<style>
/* ─── Токены (согласованы с Header.blade.php / Hero.blade.php) ─ */
.about {
    --blue:  #1C508F;
    --dblue: #174480;
    --grey:  #B0B0B0;
    --icon-bg: #4D77AA;
    --tr: .2s ease;
    --about-px: clamp(16px, 6vw, 115px);

    width: 100%;
    background: #fff;
    box-sizing: border-box;
}
.about *, .about *::before, .about *::after { box-sizing: border-box; }

.about__inner {
    width: 100%;
    margin: 0 auto;
    padding: clamp(48px, 8vh, 100px) var(--about-px);
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: clamp(28px, 4vh, 40px);
}

/* ─── Заголовок ──────────────────────────────────────────────── */
.about__title {
    margin: 0;
    width: 100%;
    font-family: 'Montserrat', sans-serif;
    font-weight: 500;
    font-size: clamp(24px, 3.2vw, 40px);
    line-height: 1.1;
    color: #000;
    text-wrap: balance;
}

/* ─── Тело секции ────────────────────────────────────────────── */
.about__body {
    width: 100%;
    display: flex;
    flex-direction: column;
    gap: clamp(32px, 4.5vh, 48px);
}

/* ─── Ряд: фото + текст ──────────────────────────────────────── */
.about__row {
    width: 100%;
    display: flex;
    flex-direction: column;
    align-items: stretch;
    gap: clamp(24px, 3vw, 28px);
}

.about__photo {
    flex: 0 0 auto;
    width: 100%;
    height: clamp(220px, 32vw, 400px);
    border-radius: 10px;
    overflow: hidden;
    background: var(--grey);
    position: relative;
}
.about__photo img {
    display: block;
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.about__col {
    flex: 1 1 0%;
    min-width: 0;
    display: flex;
    flex-direction: column;
    gap: clamp(20px, 2.5vh, 24px);
}

.about__text-group {
    display: flex;
    flex-direction: column;
    gap: clamp(14px, 1.6vh, 16px);
}

.about__p {
    margin: 0;
    font-family: 'Montserrat', sans-serif;
    font-weight: 400;
    font-size: clamp(15px, 1.1vw, 17px);
    line-height: 1.3;
    letter-spacing: -0.01em;
    color: #000;
    text-wrap: pretty;
}

.about__list {
    margin: 0;
    padding-left: 1.2em;
    display: flex;
    flex-direction: column;
    gap: clamp(10px, 1.2vh, 14px);
    list-style: disc;
}
.about__list li {
    font-family: 'Montserrat', sans-serif;
    font-weight: 400;
    font-size: clamp(15px, 1.1vw, 17px);
    line-height: 1.3;
    letter-spacing: -0.01em;
    color: #000;
}

/* ─── Карточки преимуществ ───────────────────────────────────── */
.about__features {
    width: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: clamp(28px, 3.5vh, 32px);
}

.about__feature {
    flex: 1 1 0%;
    width: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    gap: clamp(16px, 2vh, 20px);
}

.about__feature-icon {
    flex-shrink: 0;
    width: clamp(72px, 8vw, 90px);
    height: clamp(72px, 8vw, 90px);
    border-radius: 50%;
    background: var(--icon-bg);
    display: flex;
    align-items: center;
    justify-content: center;
}
.about__feature-icon img {
    width: 60%;
    height: 60%;
    object-fit: contain;
    filter: brightness(0) invert(1);
}

.about__feature-title {
    margin: 0;
    font-family: 'Montserrat', sans-serif;
    font-weight: 500;
    font-size: clamp(17px, 1.2vw, 18px);
    line-height: 1.1;
    letter-spacing: -0.01em;
    color: #000;
}

.about__feature-text {
    margin: 0;
    font-family: 'Montserrat', sans-serif;
    font-weight: 400;
    font-size: clamp(15px, 1.1vw, 16px);
    line-height: 1.3;
    letter-spacing: -0.01em;
    color: #000;
}

/* ─── Кнопка ──────────────────────────────────────────────────── */
.about__actions { width: 100%; }

.about__btn {
    display: flex;
    align-items: center;
    justify-content: center;
    box-sizing: border-box;
    width: 100%;
    height: clamp(60px, 8vh, 75px);
    padding: 0 24px;
    border-radius: 9px;
    background: var(--blue);
    color: #fff;
    box-shadow: 0px 0px 4px rgba(0,0,0,.3);
    text-decoration: none;
    font-family: 'Montserrat', sans-serif;
    font-weight: 400;
    font-size: clamp(15px, 1.2vw, 17px);
    line-height: 1.3;
    letter-spacing: -0.01em;
    white-space: nowrap;
    cursor: pointer;
    transition: background var(--tr), transform var(--tr);
}
.about__btn:hover  { background: var(--dblue); }
.about__btn:active { transform: translateY(1px); }
.about__btn:focus-visible { outline: 2px solid var(--dblue); outline-offset: 3px; }

/* ─── Планшет (≥ 640px) ──────────────────────────────────────── */
@media (min-width: 640px) {
    .about__features { flex-direction: row; align-items: flex-start; }
    .about__feature { text-align: left; align-items: flex-start; }
}

/* ─── Десктоп (≥ 1024px) ─────────────────────────────────────── */
@media (min-width: 1024px) {
    .about__row { flex-direction: row; align-items: flex-start; }

    .about__photo { width: 580px; flex: 0 0 580px; }

    /* второй ряд: на десктопе текст слева, фото справа */
    .about__row--reverse .about__photo--order-first { order: 2; }
    .about__row--reverse .about__col--order-last     { order: 1; }

    .about__btn { width: 300px; }
}

/* ─── Мелкая подстраховка от переполнения на очень узких экранах ─ */
@media (max-width: 380px) {
    .about__inner { padding-left: 14px; padding-right: 14px; }
    .about__title { font-size: 22px; }
}
</style>