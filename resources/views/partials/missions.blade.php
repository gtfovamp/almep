{{--
    ═══════════════════════════════════════════════════════════════
    MISSION SECTION — Almep Trading
    Портировано из Astro-компонента, стиль/подход согласован с
    остальными партиалами (fluid CSS через clamp(), без Tailwind).

    ВАЖНО: в оригинале расстояния между блоками задавались через
    `gap: 30vw` и `gap: 12.9vw` — это привязка к ширине ЭКРАНА, а не
    контейнера. На широких мониторах (ultrawide, 2K/4K) эти отступы
    становятся огромными и текст/цифры разъезжаются в разные углы
    экрана. Заменил на `justify-content: space-between` внутри
    контейнера с max-width — раскладка масштабируется предсказуемо
    на любой ширине.

    Ожидает переменные: $t (массив переводов), $lang (текущий язык)

    $t['mission']['mission_title'] / mission_text
    $t['mission']['goals_title']   / goals_text
    $t['mission']['stats']         — массив: [ ['number'=>.., 'text'=>..], ... ]
    $t['mission']['btn_catalog']
    ═══════════════════════════════════════════════════════════════
--}}
@php
    $missionTitle = $t['mission']['mission_title'] ?? '';
    $missionText  = $t['mission']['mission_text']  ?? '';
    $goalsTitle   = $t['mission']['goals_title']    ?? '';
    $goalsText    = $t['mission']['goals_text']     ?? '';
    $stats        = $t['mission']['stats']          ?? [];
    $btnCatalog   = $t['mission']['btn_catalog']    ?? 'Перейти в каталог';
@endphp

<section class="mission">
    <div class="mission__inner">

        {{-- Миссия + Цели --}}
        <div class="mission__top">

            <div class="mission__block mission__block--left">
                <h2 class="mission__title">{{ $missionTitle }}</h2>
                <p class="mission__text">{{ $missionText }}</p>
            </div>

            <div class="mission__block mission__block--right">
                <h2 class="mission__title mission__title--right">{{ $goalsTitle }}</h2>
                <p class="mission__text mission__text--right">{{ $goalsText }}</p>
            </div>

        </div>

        {{-- Три блока со скобками --}}
        <div class="mission__stats">
            @foreach($stats as $item)
                <div class="mission__stat">

                    <div class="mission__brackets">
                        <span class="mission__bracket mission__bracket--tl"></span>
                        <span class="mission__bracket mission__bracket--tr"></span>
                    </div>

                    <div class="mission__stat-body">
                        <span class="mission__stat-number">{{ $item['number'] ?? '' }}</span>
                        <p class="mission__stat-text">{{ $item['text'] ?? '' }}</p>
                    </div>

                    <div class="mission__brackets mission__brackets--bottom">
                        <span class="mission__bracket mission__bracket--bl"></span>
                        <span class="mission__bracket mission__bracket--br"></span>
                    </div>

                </div>
            @endforeach
        </div>

        {{-- Кнопка --}}
        <div class="mission__cta">
            <a href="/{{ $lang }}/products" class="mission__btn">{{ $btnCatalog }}</a>
        </div>

    </div>
</section>

<style>
/* ─── Токены (согласованы с остальными секциями) ─────────────── */
.mission {
    --blue:  #1C508F;
    --dblue: #174480;
    --mission-px: clamp(16px, 6vw, 115px);

    width: 100%;
    background: #fff;
    box-sizing: border-box;
}
.mission *, .mission *::before, .mission *::after { box-sizing: border-box; }

.mission__inner {
    width: 100%;
    margin: 0 auto;
    padding: clamp(40px, 6vh, 90px) var(--mission-px);
    display: flex;
    flex-direction: column;
    align-items: stretch;
    gap: clamp(28px, 3.6vh, 40px);
}

/* ─── Верхний блок: Миссия + Цели ────────────────────────────── */
.mission__top {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: clamp(28px, 3.5vh, 35px);
    width: 100%;
}

.mission__block {
    display: flex;
    flex-direction: column;
    gap: clamp(18px, 2vw, 25px);
    width: 100%;
}

.mission__title {
    margin: 0;
    font-family: 'Montserrat', sans-serif;
    font-weight: 500;
    font-size: clamp(24px, 3.2vw, 40px);
    line-height: 1.1;
    color: #000;
    text-align: center;
}

.mission__text {
    margin: 0;
    font-family: 'Montserrat', sans-serif;
    font-weight: 400;
    font-size: clamp(15px, 1.1vw, 16px);
    line-height: 1.3;
    letter-spacing: -0.01em;
    color: #000;
    text-align: center;
    width: 100%;
}

/* ─── Три блока со скобками ──────────────────────────────────── */
.mission__stats {
    display: flex;
    flex-direction: column;
    align-items: stretch;
    gap: clamp(28px, 4vh, 35px);
    width: 100%;
}

.mission__stat {
    display: flex;
    flex-direction: column;
    align-items: center;
    position: relative;
    width: 100%;
}

.mission__brackets {
    display: flex;
    flex-direction: row;
    justify-content: space-between;
    align-items: center;
    width: 100%;
    height: clamp(30px, 3vw, 38px);
}

.mission__bracket {
    display: block;
    width: clamp(30px, 3vw, 38px);
    height: clamp(30px, 3vw, 38px);
    border-color: #000;
    border-style: solid;
    border-width: 0;
}
.mission__bracket--tl { border-top-width: 1px; border-left-width: 1px; }
.mission__bracket--tr { border-top-width: 1px; border-right-width: 1px; }
.mission__bracket--bl { border-bottom-width: 1px; border-left-width: 1px; }
.mission__bracket--br { border-bottom-width: 1px; border-right-width: 1px; }

.mission__stat-body {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: clamp(20px, 2.5vh, 25px);
    padding: 10px 0;
    width: 100%;
}

.mission__stat-number {
    margin: 0;
    font-family: 'Montserrat', sans-serif;
    font-weight: 600;
    font-size: clamp(24px, 2.9vw, 38px);
    line-height: 1.1;
    text-align: center;
    color: var(--blue);
    width: 100%;
}

.mission__stat-text {
    margin: 0;
    font-family: 'Montserrat', sans-serif;
    font-weight: 400;
    font-size: clamp(15px, 1.1vw, 16px);
    line-height: 1.3;
    letter-spacing: -0.01em;
    text-align: center;
    color: #000;
    max-width: 100%;
}

/* ─── Кнопка ─────────────────────────────────────────────────── */
.mission__cta { width: 100%; }

.mission__btn {
    display: flex;
    align-items: center;
    justify-content: center;
    box-sizing: border-box;
    width: 100%;
    height: clamp(60px, 8vh, 85px);
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
    transition: background .2s ease;
}
.mission__btn:hover { background: var(--dblue); }
.mission__btn:focus-visible { outline: 2px solid var(--dblue); outline-offset: 3px; }

/* ═══════════════════════════════════════════════════════════════
   ДЕСКТОП / ПЛАНШЕТ (≥ 769px)
   ═══════════════════════════════════════════════════════════════ */
@media (min-width: 769px) {
    .mission__top {
        flex-direction: row;
        align-items: center;
        justify-content: space-between;
    }

    .mission__block { width: auto; }
    .mission__block--left  { align-items: flex-start; max-width: 420px; }
    .mission__block--right { align-items: flex-end;   max-width: 340px; }

    .mission__title,
    .mission__text { text-align: left; width: auto; }
    .mission__title--right,
    .mission__text--right { text-align: right; width: 100%; }

    .mission__stats {
        flex-direction: row;
        align-items: stretch;
        justify-content: space-between;
        gap: clamp(24px, 4vw, 60px);
    }
    .mission__stat { flex: 1 1 0; width: auto; }

    .mission__btn { width: 300px; height: 70px; }
}

/* ─── Мелкая подстраховка от переполнения на очень узких экранах ─ */
@media (max-width: 380px) {
    .mission__inner { padding-left: 14px; padding-right: 14px; }
    .mission__title { font-size: 22px; }
}
</style>