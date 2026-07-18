{{--
    ═══════════════════════════════════════════════════════════════
    WHY US SECTION (иконки + прогресс-бары + фото) — Almep Trading
    Портировано из Astro-компонента, стиль/подход согласован с
    остальными партиалами (fluid CSS через clamp(), без Tailwind).

    ВНИМАНИЕ: если у тебя уже есть файл partials/whyus.blade.php
    с тремя карточками-иконками (без прогресс-баров и фото) — это,
    судя по всему, другая секция. Сохрани этот файл под тем именем,
    которое реально подключено в pages/index.blade.php для ЭТОГО
    контента (иконки + прогресс-бары + фото справа), чтобы не
    перезаписать другую секцию.

    Ожидает переменные: $t (массив переводов), $lang (текущий язык)

    $t['whyus']['title']
    $t['whyus']['features']         — массив: [ ['title'=>.., 'description'=>..], ... ]
    $t['whyus']['stats']            — массив: [ ['label'=>.., 'value'=>'95%'], ... ]
    $t['whyus']['btn_consultation']
    $t['whyus']['photo_alt']
    ═══════════════════════════════════════════════════════════════
--}}
@php
    $whyusTitle  = $t['whyus']['title']            ?? 'Почему выбирают нас';
    $btnConsult  = $t['whyus']['btn_consultation']  ?? 'Запросить консультацию';
    $photoAlt    = $t['whyus']['photo_alt']         ?? $whyusTitle;

    $features = collect($t['whyus']['features'] ?? [])
        ->map(function ($f, $index) {
            return [
                'icon'        => 'assets/icons/whyus-' . ($index + 1) . '.svg',
                'title'       => $f['title'] ?? '',
                'description' => $f['description'] ?? '',
            ];
        })
        ->all();

    $stats = collect($t['whyus']['stats'] ?? [])
        ->map(function ($s) {
            $value   = $s['value'] ?? '0%';
            $percent = (int) str_replace('%', '', $value);
            $percent = max(0, min(100, $percent));

            return [
                'label'   => $s['label'] ?? '',
                'value'   => $value,
                'percent' => $percent,
            ];
        })
        ->all();
@endphp

<section class="whyus">
    <div class="whyus__inner">

        <h2 class="whyus__title">{{ $whyusTitle }}</h2>

        <div class="whyus__content">

            {{-- Верхний ряд: иконки --}}
            <div class="whyus__features">
                @foreach($features as $f)
                    <div class="whyus__feature">
                        <div class="whyus__icon-wrap">
                            <img src="{{ asset($f['icon']) }}" alt="{{ $f['title'] }}" class="whyus__icon" loading="lazy">
                        </div>
                        <div class="whyus__feature-body">
                            <h3 class="whyus__feature-title">{{ $f['title'] }}</h3>
                            <p class="whyus__feature-desc">{{ $f['description'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Нижний блок: прогрессбары + фото --}}
            <div class="whyus__bottom">

                <div class="whyus__left">

                    <div class="whyus__stats">
                        @foreach($stats as $s)
                            <div class="whyus__stat">
                                <div class="whyus__stat-header">
                                    <span class="whyus__stat-label">{{ $s['label'] }}</span>
                                    <span class="whyus__stat-value">{{ $s['value'] }}</span>
                                </div>
                                <div class="whyus__bar-track">
                                    <div class="whyus__bar-fill" style="--bar-percent: {{ $s['percent'] }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <a href="/{{ $lang }}/contacts" class="whyus__btn">{{ $btnConsult }}</a>

                </div>

                <div class="whyus__photo-wrap">
                    <img src="{{ asset('assets/images/why-us.png') }}" alt="{{ $photoAlt }}" class="whyus__photo" loading="lazy">
                </div>

            </div>
        </div>
    </div>
</section>

<style>
/* ─── Токены (согласованы с остальными секциями) ─────────────── */
.whyus {
    --blue:  #1C508F;
    --dblue: #174480;
    --whyus-px: clamp(16px, 6vw, 115px);

    width: 100%;
    background: #fff;
    box-sizing: border-box;
}
.whyus *, .whyus *::before, .whyus *::after { box-sizing: border-box; }

.whyus__inner {
    width: 100%;
    margin: 0 auto;
    padding: clamp(40px, 6vh, 90px) var(--whyus-px);
    display: flex;
    flex-direction: column;
    gap: clamp(28px, 3.6vh, 40px);
}

.whyus__title {
    margin: 0;
    font-family: 'Montserrat', sans-serif;
    font-weight: 500;
    font-size: clamp(24px, 3.2vw, 40px);
    line-height: 1.1;
    color: #000;
}

.whyus__content {
    display: flex;
    flex-direction: column;
    gap: clamp(28px, 3.5vh, 40px);
    width: 100%;
}

/* ─── Верхний ряд иконок ─────────────────────────────────────── */
.whyus__features {
    display: flex;
    flex-direction: column;
    align-items: stretch;
    gap: clamp(28px, 3.5vh, 35px);
    width: 100%;
}

.whyus__feature {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    flex: 1;
    gap: clamp(18px, 2.2vh, 25px);
}

.whyus__icon-wrap {
    width: clamp(80px, 7vw, 90px);
    height: clamp(80px, 7vw, 90px);
    border-radius: 50%;
    background: #fff;
    box-shadow: 0px 0px 4px rgba(0, 0, 0, 0.3);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.whyus__icon { width: 56%; height: 56%; object-fit: contain; }

.whyus__feature-body {
    display: flex;
    flex-direction: column;
    gap: 6px;
    width: 100%;
}

.whyus__feature-title {
    margin: 0;
    font-family: 'Montserrat', sans-serif;
    font-weight: 500;
    font-size: clamp(20px, 1.9vw, 20px);
    line-height: 1.1;
    letter-spacing: -0.01em;
    color: #000;
    width: 100%;
}

.whyus__feature-desc {
    margin: 0;
    font-family: 'Montserrat', sans-serif;
    font-weight: 400;
    font-size: clamp(15px, 1.1vw, 16px);
    line-height: 1.3;
    letter-spacing: -0.01em;
    color: #000;
    width: 100%;
}

/* ─── Нижний блок ────────────────────────────────────────────── */
.whyus__bottom {
    display: flex;
    flex-direction: column;
    align-items: stretch;
    gap: clamp(28px, 4vh, 35px);
    width: 100%;
}

.whyus__left {
    display: flex;
    flex-direction: column;
    gap: clamp(28px, 3.5vh, 40px);
    flex: 1;
    flex-shrink: 0;
}

/* ─── Прогрессбары ───────────────────────────────────────────── */
.whyus__stats {
    display: flex;
    flex-direction: column;
    gap: clamp(24px, 3vh, 30px);
    width: 100%;
}

.whyus__stat {
    display: flex;
    flex-direction: column;
    gap: 10px;
    width: 100%;
}

.whyus__stat-header {
    display: flex;
    flex-direction: row;
    justify-content: space-between;
    align-items: center;
    width: 100%;
}

.whyus__stat-label,
.whyus__stat-value {
    font-family: 'Montserrat', sans-serif;
    font-weight: 400;
    font-size: clamp(16px, 1.4vw, 20px);
    line-height: 1.1;
    letter-spacing: -0.01em;
    color: #000;
}

.whyus__bar-track {
    position: relative;
    width: 100%;
    height: 8px;
    background: #E1E1E1;
    border-radius: 4px;
    overflow: hidden;
}

.whyus__bar-fill {
    position: absolute;
    left: 0;
    top: 0;
    height: 100%;
    width: var(--bar-percent, 0%);
    background: var(--blue);
    border-radius: 4px;
    transform-origin: left;
    animation: whyusBarGrow 1s ease forwards;
}

@keyframes whyusBarGrow {
    from { transform: scaleX(0); }
    to   { transform: scaleX(1); }
}

/* Уважаем настройку "меньше анимаций" на устройстве пользователя */
@media (prefers-reduced-motion: reduce) {
    .whyus__bar-fill { animation: none; transform: none; }
}

/* ─── Кнопка ─────────────────────────────────────────────────── */
.whyus__btn {
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
    flex-shrink: 0;
    transition: background .2s ease;
}
.whyus__btn:hover { background: var(--dblue); }
.whyus__btn:focus-visible { outline: 2px solid var(--dblue); outline-offset: 3px; }

/* ─── Фото ───────────────────────────────────────────────────── */
.whyus__photo-wrap {
    width: 100%;
    height: clamp(220px, 30vw, 420px);
    border-radius: 8px;
    overflow: hidden;
    flex-shrink: 0;
    background: #dfdfdf;
}
.whyus__photo {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}

/* ═══════════════════════════════════════════════════════════════
   ДЕСКТОП / ПЛАНШЕТ (≥ 769px)
   ═══════════════════════════════════════════════════════════════ */
@media (min-width: 769px) {
    .whyus__features { flex-direction: row; align-items: flex-start; gap: clamp(16px, 2vw, 19px); }

    .whyus__bottom { flex-direction: row; align-items: center; gap: clamp(40px, 6vw, 80px); }
    .whyus__left  { width: auto; }
    .whyus__btn   { width: 300px; height: 70px; }

    .whyus__photo-wrap { flex: 1; }
}

/* ─── Мелкая подстраховка от переполнения на очень узких экранах ─ */
@media (max-width: 380px) {
    .whyus__inner { padding-left: 14px; padding-right: 14px; }
    .whyus__title { font-size: 22px; }
}

/* ═══════════════════════════════════════════════════════════════
   МОБИЛЬНАЯ ВЕРСИЯ (≤ 768px) — отдельная, более компактная
   и "оформленная" компоновка, а не просто уменьшенный десктоп:
   фичи — горизонтальные карточки на подложке, статистика — единая
   панель-стекло, фото — с мягкой тенью и более крупным радиусом.
   ═══════════════════════════════════════════════════════════════ */
@media (max-width: 768px) {
    .whyus__inner { gap: 32px; }
    .whyus__content { gap: 28px; }

    /* Фичи: карточка-строка (иконка слева, текст справа) вместо
       вертикального центрированного блока — компактнее и понятнее
       листается пальцем сверху вниз. */
    .whyus__features { gap: 12px; }

    .whyus__feature {
        flex-direction: row;
        align-items: center;
        text-align: left;
        gap: 16px;
        background: #F6F8FB;
        border-radius: 16px;
        padding: 16px;
    }

    .whyus__icon-wrap {
        width: 60px;
        height: 60px;
        box-shadow: 0 1px 4px rgba(28, 80, 143, 0.18);
    }

    .whyus__feature-body { gap: 4px; }

    .whyus__feature-title {
        font-size: 16px;
        text-align: left;
    }

    .whyus__feature-desc {
        font-size: 14px;
        line-height: 1.4;
        text-align: left;
        color: #4B5563;
    }

    /* Статистика: единая панель вместо голых цифр на белом фоне —
       визуально группирует три показателя в один "блок доверия". */
    .whyus__stats {
        background: #F6F8FB;
        border-radius: 16px;
        padding: 20px 18px;
        gap: 22px;
    }

    .whyus__stat + .whyus__stat {
        padding-top: 22px;
        border-top: 1px solid rgba(28, 80, 143, 0.1);
    }

    .whyus__stat-label,
    .whyus__stat-value { font-size: 15px; }
    .whyus__stat-value { color: var(--blue); font-weight: 500; }

    /* Фото: более крупный радиус и мягкая тень, чтобы не выглядело
       плоской вставкой между блоками. */
    .whyus__photo-wrap {
        height: 220px;
        border-radius: 18px;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
    }

    .whyus__btn { border-radius: 12px; }
}

/* Совсем узкие экраны — иконку и паддинги карточки чуть уменьшаем,
   чтобы заголовок фичи не переносился криво в 2 слова на строку */
@media (max-width: 360px) {
    .whyus__feature { padding: 14px; gap: 12px; }
    .whyus__icon-wrap { width: 52px; height: 52px; }
}
</style>