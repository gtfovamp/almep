{{--
    ═══════════════════════════════════════════════════════════════
    PARTNERS SECTION (логотипы партнёров) — Almep Trading
    Вёрстка приведена в соответствие с макетом Figma (Frame 427319701):
    заголовок → ряд логотипов → ссылка "Все отзывы" (справа под рядом)
    → кнопка консультации (слева, фикс. ширины на десктопе).

    Все фиксированные px из макета (48px заголовок, 186px гэп между
    логотипами, 330×85 кнопка и т.д.) переведены в clamp(), чтобы
    секция плавно масштабировалась между 1408px-макетом и мобилкой,
    а не просто переключалась по одному брейкпоинту.

    Ожидает переменные:
      $t         — массив переводов
      $lang      — текущий язык ('ru' | 'en' | 'az')
      $partners  — Eloquent-коллекция моделей Partner (->get())

    $t['partners']['title']
    $t['partners']['all_reviews']
    $t['partners']['btn_consultation']

    ─── Как получить $partners в контроллере ───

    $data['partners'] = \App\Models\Partner::query()
        ->orderBy('order_index')
        ->get();
    ═══════════════════════════════════════════════════════════════
--}}
@php
    $partnersTitle   = $t['partners']['title']            ?? 'Партнёры';
    $allReviews      = $t['partners']['all_reviews']       ?? 'Все отзывы';
    $btnConsultation = $t['partners']['btn_consultation']  ?? 'Запросить консультацию';

    // name_ru в таблице нет (fillable: name, name_en, name_az) —
    // для 'ru' поле само уйдёт в null и сработает фоллбэк на name.
    $nameField = 'name_' . $lang;

    $rawPartners = collect($partners ?? []);

    $logos = $rawPartners->map(function ($item) use ($nameField) {
        if (is_array($item)) {
            return [
                'image' => $item['image'] ?? ($item['image_url'] ?? ''),
                'name'  => $item[$nameField] ?? ($item['name'] ?? ''),
            ];
        }

        return [
            'image' => $item->image_url,
            'name'  => $item->{$nameField} ?: $item->name,
        ];
    })->all();
@endphp

@if(count($logos))
<section class="partners">
    <div class="partners__inner">

        <h2 class="partners__title">{{ $partnersTitle }}</h2>

        <div class="partners__content">

            <div class="partners__top">

                <div class="partners__logos">
                    @foreach($logos as $logo)
                        <div class="partners__logo">
                            <img
                                src="{{ $logo['image'] }}"
                                alt="{{ $logo['name'] }}"
                                loading="lazy"
                            >
                        </div>
                    @endforeach
                </div>
                <a href="/{{ $lang }}/partners" class="partners__link">
                    <span class="partners__link-text">{{ $allReviews }}</span>
                    <span class="partners__link-arrow" aria-hidden="true"></span>
                </a>
            </div>

            <button type="button" class="partners__btn" data-open-consultation>
                {{ $btnConsultation }}
            </button>

        </div>
    </div>
</section>

<style>
/* ─── Токены ──────────────────────────────────────────────────── */
.partners {
    --blue:  #1C508F;
    --dblue: #174480;
    --partners-px: clamp(16px, 6vw, 115px);

    width: 100%;
    background: #fff;
    box-sizing: border-box;
}
.partners *, .partners *::before, .partners *::after { box-sizing: border-box; }

.partners__inner {
    width: 100%;
    margin: 0 auto;
    padding: clamp(40px, 6vh, 90px) var(--partners-px);
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: clamp(28px, 4.5vh, 50px);
}

.partners__title {
    margin: 0;
    width: 100%;
    font-family: 'Montserrat', sans-serif;
    font-weight: 500;
    font-size: clamp(26px, 3.6vw, 48px);
    line-height: 1.1;
    color: #000;
}

.partners__content {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: clamp(24px, 3.5vh, 40px);
    width: 100%;
}

/* ─── Верхний блок: логотипы + ссылка (прижаты вправо) ────────── */
.partners__top {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: clamp(24px, 3.5vh, 40px);
    width: 100%;
}

.partners__logos {
    display: flex;
    flex-direction: row;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    column-gap: clamp(24px, 8vw, 186px);
    row-gap: clamp(20px, 3vh, 32px);
    width: 100%;
}

.partners__logo {
    display: flex;
    align-items: center;
    justify-content: center;
    flex: 0 0 auto;
}

.partners__logo img {
    display: block;
    height: clamp(44px, 7vw, 106px);
    width: auto;
    max-width: 100%;
    object-fit: contain;
}

/* ─── Ссылка "Все отзывы" (диамант-стрелка из макета) ─────────── */
.partners__link {
    display: flex;
    flex-direction: row;
    align-items: center;
    gap: 11px;
    text-decoration: none;
    transition: opacity 0.2s ease;
}

.partners__link:hover { opacity: 0.8; }

.partners__link-text {
    font-family: 'Montserrat', sans-serif;
    font-weight: 400;
    font-size: clamp(15px, 1.7vw, 24px);
    line-height: 1.1;
    color: var(--blue);
    white-space: nowrap;
}

.partners__link-arrow {
    position: relative;
    flex-shrink: 0;
    width: clamp(16px, 1.8vw, 24px);
    height: clamp(10px, 1.2vw, 14px);
}

.partners__link-arrow::before,
.partners__link-arrow::after {
    content: "";
    position: absolute;
    background: var(--blue);
}

/* линия стрелки */
.partners__link-arrow::before {
    top: 50%;
    left: 0;
    width: 100%;
    height: clamp(1.5px, 0.15vw, 2px);
    transform: translateY(-50%);
}

/* остриё стрелки */
.partners__link-arrow::after {
    top: 50%;
    right: 0;
    width: clamp(8px, 0.9vw, 12px);
    height: clamp(8px, 0.9vw, 12px);
    border-top: clamp(1.5px, 0.15vw, 2px) solid var(--blue);
    border-right: clamp(1.5px, 0.15vw, 2px) solid var(--blue);
    background: transparent;
    transform: translateY(-50%) rotate(45deg);
}

/* ─── Кнопка консультации ────────────────────────────────────── */
.partners__btn {
    display: flex;
    align-items: center;
    justify-content: center;
    box-sizing: border-box;
    width: 100%;
    height: clamp(62px, 8vh, 85px);
    background: var(--blue);
    box-shadow: 0px 0px 4px rgba(0, 0, 0, 0.3);
    border-radius: 9px;
    border: none;
    cursor: pointer;
    font-family: 'Montserrat', sans-serif;
    font-weight: 400;
    font-size: clamp(15px, 1.4vw, 18px);
    line-height: 1.3;
    letter-spacing: -0.01em;
    color: #fff;
    text-decoration: none;
    transition: background .2s ease;
}
.partners__btn:hover { background: var(--dblue); }
.partners__btn:focus-visible { outline: 2px solid var(--dblue); outline-offset: 3px; }

/* ═══════════════════════════════════════════════════════════════
   ДЕСКТОП / ПЛАНШЕТ (≥ 769px) — как в макете: кнопка слева,
   фиксированной ширины, не на всю строку
   ═══════════════════════════════════════════════════════════════ */
@media (min-width: 769px) {
    .partners__btn { width: clamp(260px, 24vw, 330px); }
}

/* ─── Мобильная версия (≤ 768px) ─────────────────────────────── */
@media (max-width: 768px) {
    .partners__top { align-items: center; }

    .partners__logos {
        justify-content: center;
        column-gap: clamp(28px, 10vw, 48px);
    }

    .partners__link { align-self: flex-end; }
}

/* ─── Подстраховка от переполнения на очень узких экранах ────── */
@media (max-width: 380px) {
    .partners__inner { padding-left: 14px; padding-right: 14px; }
    .partners__title { font-size: 22px; }
}
</style>
@endif