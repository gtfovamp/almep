{{--
    ═══════════════════════════════════════════════════════════════
    PORTFOLIO SECTION (главная страница) — Almep Trading
    Портировано из Astro-компонента (там данные тянулись напрямую
    из Cloudflare D1 через env.DB — здесь партиал сам локализует
    и раскладывает по слайдам модели App\Models\Portfolio).

    Стиль/подход согласован с остальными партиалами
    (fluid CSS через clamp(), без Tailwind).

    Ожидает переменные:
      $t               — массив переводов
      $lang            — текущий язык ('ru' | 'en' | 'az')
      $portfolioItems  — Eloquent-коллекция моделей Portfolio
                          (НЕ paginate() — просто ->get(), т.к. это
                          клиентский слайдер, а не страница со списком)

    $t['portfolio']['title']
    $t['portfolio']['aria_next'] / aria_prev / aria_slide
    $t['portfolio']['learn_more']
    $t['portfolio']['btn_consultation']

    ─── Как получить $portfolioItems в контроллере ───

    // в SiteController::index() (главная страница):
    $data['portfolioItems'] = \App\Models\Portfolio::query()
        ->orderBy('order_index')
        ->limit(9)
        ->get();
    ═══════════════════════════════════════════════════════════════
--}}
@php
    $portfolioTitle   = $t['portfolio']['title']            ?? 'Портфолио';
    $ariaNext         = $t['portfolio']['aria_next']         ?? 'Следующий слайд';
    $ariaPrev         = $t['portfolio']['aria_prev']         ?? 'Предыдущий слайд';
    $ariaSlide        = $t['portfolio']['aria_slide']        ?? 'Слайд';
    $learnMore        = $t['portfolio']['learn_more']        ?? 'Смотреть все проекты';
    $btnConsultation  = $t['portfolio']['btn_consultation']  ?? 'Запросить консультацию';

    // Поддерживаем и Eloquent-коллекцию, и обычный массив/paginator —
    // на случай, если контроллер передаст что угодно из перечисленного.
    $rawItems = $portfolioItems ?? collect();
    if ($rawItems instanceof \Illuminate\Pagination\AbstractPaginator) {
        $rawItems = $rawItems->getCollection();
    }
    $rawItems = collect($rawItems);

    $titleField = 'title_' . $lang;

    $items = $rawItems->map(function ($item) use ($titleField) {
        // $item может быть моделью Portfolio или уже готовым массивом
        if (is_array($item)) {
            return [
                'image' => $item['image'] ?? ($item['image_url'] ?? ''),
                'title' => $item['title'] ?? '',
                'year'  => $item['year'] ?? '',
            ];
        }

        return [
            'image' => $item->image_url,
            'title' => $item->{$titleField} ?: $item->title,
            'year'  => $item->year,
        ];
    })->all();

    $slides = array_chunk($items, 6);
@endphp

@if(count($slides))
<section class="portfolio">
    <div class="portfolio__inner">

        <h2 class="portfolio__title">{{ $portfolioTitle }}</h2>

        <div class="portfolio__slider-wrap">

            <div class="portfolio__slider" id="portfolioSlider">
                @foreach($slides as $slideIndex => $slide)
                    <div class="portfolio__slide {{ $slideIndex === 0 ? 'portfolio__slide--active' : '' }}">
                        <div class="portfolio__grid">
                            @foreach($slide as $item)
                                <div class="portfolio__card">
                                    <img
                                        src="{{ $item['image'] }}"
                                        alt="{{ $item['title'] }}"
                                        class="portfolio__card-img"
                                        loading="lazy"
                                    >
                                    <div class="portfolio__card-caption">
                                        <span class="portfolio__card-title">{{ $item['title'] }}</span>
                                        <span class="portfolio__card-year">{{ $item['year'] }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            @if(count($slides) > 1)
                <button class="portfolio__arrow portfolio__arrow--right" id="arrowRight" aria-label="{{ $ariaNext }}">
                    <svg width="23" height="30" viewBox="0 0 23 30" fill="none">
                        <path d="M3 3L20 15L3 27" stroke="#000000" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>

                <button class="portfolio__arrow portfolio__arrow--left is-hidden" id="arrowLeft" aria-label="{{ $ariaPrev }}" disabled>
                    <svg width="23" height="30" viewBox="0 0 23 30" fill="none">
                        <path d="M20 3L3 15L20 27" stroke="#000000" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
            @endif

        </div>

        <div class="portfolio__bottom">

            <div class="portfolio__scrollbar" id="portfolioScrollbar">
                <div class="portfolio__scrollbar-track"></div>
                <div class="portfolio__scrollbar-thumb" id="portfolioScrollbarThumb"></div>
            </div>

            <div></div>

            @if(count($slides) > 1)
                <div class="portfolio__dots" id="portfolioDots">
                    @foreach($slides as $i => $slide)
                        <button
                            class="portfolio__dot {{ $i === 0 ? 'portfolio__dot--active' : '' }}"
                            data-index="{{ $i }}"
                            aria-label="{{ $ariaSlide }} {{ $i + 1 }}"
                        ></button>
                    @endforeach
                </div>
            @endif

            <a href="/{{ $lang }}/portfolio" class="portfolio__more">
                {{ $learnMore }}
                <svg width="27" height="20" viewBox="0 0 27 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M0.013702 10.394L26.1767 9.68691M26.1767 9.68691L16.8428 0.353097M26.1767 9.68691L16.9401 18.9235" stroke="#1C508F"/>
                </svg>
            </a>

        </div>

        <button type="button" class="hero__btn hero__btn--primary" data-open-consultation>
                {{ $btnConsultation }}
        </button>
    </div>
</section>

<style>
/* ─── Токены (согласованы с остальными секциями) ─────────────── */
.portfolio {
    --blue:  #1C508F;
    --dblue: #174480;
    --portfolio-px: clamp(16px, 6vw, 115px);

    width: 100%;
    background: #fff;
    box-sizing: border-box;
}
.portfolio *, .portfolio *::before, .portfolio *::after { box-sizing: border-box; }

.portfolio__inner {
    width: 100%;
    margin: 0 auto;
    padding: clamp(40px, 6vh, 90px) var(--portfolio-px);
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: clamp(28px, 3.6vh, 40px);
}

.portfolio__title {
    margin: 0;
    width: 100%;
    font-family: 'Montserrat', sans-serif;
    font-weight: 500;
    font-size: clamp(24px, 3.2vw, 40px);
    line-height: 1.1;
    color: #000;
}

/* ─── Слайдер ────────────────────────────────────────────────── */
.portfolio__slider-wrap {
    position: relative;
    width: 100%;
}

.portfolio__slider { width: 100%; overflow: hidden; }

.portfolio__slide { display: none; }
.portfolio__slide--active {
    display: flex;
    overflow-x: auto;
    overflow-y: hidden;
    scroll-snap-type: x mandatory;
    -webkit-overflow-scrolling: touch;
    scrollbar-width: none;
    -ms-overflow-style: none;
}
.portfolio__slide--active::-webkit-scrollbar { display: none; }

.portfolio__grid {
    display: flex;
    flex-direction: row;
    gap: 11px;
    width: 100%;
    padding: 1px;
} 

/* ─── Карточка ───────────────────────────────────────────────── */
.portfolio__card {
    position: relative;
    width: 274px;
    height: 330px;
    flex-shrink: 0;
    scroll-snap-align: start;
    background: #fff;
    box-shadow: 0px 0px 4px rgba(0, 0, 0, 0.3);
    border-radius: 7px;
    overflow: hidden;
}
.portfolio__card-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
    transition: transform .3s ease;
}
.portfolio__card:hover .portfolio__card-img { transform: scale(1.03); }

.portfolio__card-caption {
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 166px;
    background: rgba(255, 255, 255, 0.88);
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
    align-items: flex-start;
    padding: 15px;
    gap: clamp(20px, 4vw, 42px);
    opacity: 1;
    transform: translateY(0);
    transition: opacity .3s ease, transform .3s ease;
}

.portfolio__card-title {
    font-family: 'Montserrat', sans-serif;
    font-weight: 500;
    font-size: 20px;
    line-height: 1.1;
    letter-spacing: -0.01em;
    color: var(--blue);
    order: 0;
}
.portfolio__card-year {
    font-family: 'Montserrat', sans-serif;
    font-weight: 500;
    font-size: 20px;
    line-height: 1.1;
    letter-spacing: -0.01em;
    color: var(--blue);
    white-space: nowrap;
    order: 1;
    align-self: flex-start;
}

/* ─── Стрелки (только там, где есть куда листать) ────────────── */
.portfolio__arrow {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    width: 76px;
    height: 76px;
    background: #F1F1F1;
    border-radius: 50%;
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background .2s ease;
    z-index: 10;
}
.portfolio__arrow:hover { background: #E0E0E0; }
.portfolio__arrow--right { right: -38px; }
.portfolio__arrow--left  { left: -38px; }
.portfolio__arrow.is-hidden { opacity: 0; pointer-events: none; }

/* ─── Низ секции: скроллбар / точки / ссылка ─────────────────── */
.portfolio__bottom {
    width: 100%;
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: 35px;
    position: relative;
}

.portfolio__scrollbar {
    display: block;
    position: relative;
    width: 100%;
    height: 13px;
}
.portfolio__scrollbar-track {
    position: absolute;
    width: 100%;
    height: 7px;
    left: 0;
    top: 3px;
    background: #C0C0C0;
    border-radius: 100px;
}
.portfolio__scrollbar-thumb {
    position: absolute;
    width: 94px;
    height: 13px;
    left: 0;
    top: 0;
    background: var(--blue);
    border-radius: 100px;
    cursor: pointer;
    transition: left .1s ease-out;
}

.portfolio__dots { display: none; }
.portfolio__dot {
    width: 17px;
    height: 17px;
    border-radius: 50%;
    border: none;
    background: #D9D9D9;
    cursor: pointer;
    padding: 0;
    transition: background .2s ease;
}
.portfolio__dot--active { background: var(--blue); }

.portfolio__more {
    order: -1;
    align-self: flex-end;
    display: flex;
    flex-direction: row;
    align-items: center;
    gap: 11px;
    font-family: 'Montserrat', sans-serif;
    font-weight: 400;
    font-size: 16px;
    line-height: 1.3;
    color: var(--blue);
    text-decoration: none;
    transition: opacity .2s;
}
.portfolio__more:hover { opacity: .75; }
.portfolio__more svg { flex-shrink: 0; width: 18.5px; height: 13.44px; }

/* ─── Кнопка ─────────────────────────────────────────────────── */
.portfolio__cta { width: 100%; }
.portfolio__btn {
    display: flex;
    align-items: center;
    justify-content: center;
    box-sizing: border-box;
    width: 100%;
    height: 85px;
    background: var(--blue);
    box-shadow: 0px 0px 4px rgba(0, 0, 0, 0.3);
    border-radius: 9px;
    font-family: 'Montserrat', sans-serif;
    font-weight: 400;
    font-size: 16px;
    line-height: 1.3;
    letter-spacing: -0.01em;
    color: #fff;
    text-decoration: none;
    transition: background .2s ease;
}
.portfolio__btn:hover { background: var(--dblue); }
.portfolio__btn:focus-visible { outline: 2px solid var(--dblue); outline-offset: 3px; }

/* ═══════════════════════════════════════════════════════════════
   ДЕСКТОП / ПЛАНШЕТ (≥ 769px) — статичная сетка 3×2, стрелки, точки
   ═══════════════════════════════════════════════════════════════ */
@media (min-width: 769px) {
    .portfolio__slide--active {
        overflow: visible;
        scroll-snap-type: none;
    }
    .portfolio__grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        /* Жёстко 2 ряда всегда, даже если в слайде 1 карточка из 6 —
           чтобы высота секции не "прыгала" между слайдами/кол-вом записей. */
        grid-template-rows: repeat(2, clamp(230px, 26vw, 280px));
        gap: clamp(18px, 2vw, 25px);
    }
    .portfolio__card { width: 100%; height: 100%; border-radius: 10px; }

    .portfolio__card-caption {
        height: 111px;
        flex-direction: row;
        justify-content: space-between;
        align-items: flex-start;
        padding: 26px 15px 0;
        gap: 42px;
        opacity: 0;
        transform: translateY(100%);
    }
    .portfolio__card:hover .portfolio__card-caption { opacity: 1; transform: translateY(0); }

    .portfolio__card-title,
    .portfolio__card-year { font-size: clamp(20px, 1.9vw, 24px); }
    .portfolio__card-title { flex: 1; }

    .portfolio__bottom { flex-direction: row; justify-content: space-between; align-items: center; }
    .portfolio__scrollbar { display: none; }

    .portfolio__dots {
        display: flex;
        flex-direction: row;
        align-items: center;
        gap: 10px;
        position: absolute;
        left: 50%;
        transform: translateX(-50%);
    }

    .portfolio__more { order: 0; align-self: center; font-size: clamp(18px, 1.9vw, 24px); }
    .portfolio__more svg { width: 27px; height: 20px; }

    .portfolio__btn { width: 300px; height: 70px; font-size: clamp(15px, 1.2vw, 17px); }
}

/* ─── Широкий десктоп (≥ 1024px): полноценная сетка 3×2 ─────── */
@media (min-width: 1024px) {
    .portfolio__grid { grid-template-columns: repeat(3, 1fr); }
}

@media (min-width: 1400px) {
    .portfolio__bottom { max-width: 1410px; }
}

/* ─── Мелкая подстраховка от переполнения на очень узких экранах ─ */
@media (max-width: 380px) {
    .portfolio__inner { padding-left: 14px; padding-right: 14px; }
    .portfolio__title { font-size: 22px; }
}
</style>

<script>
(function () {
    var slides = document.querySelectorAll('.portfolio__slide');
    var dots = document.querySelectorAll('.portfolio__dot');
    var arrowRight = document.getElementById('arrowRight');
    var arrowLeft = document.getElementById('arrowLeft');

    var current = 0;
    // total берём из реального числа отрендеренных слайдов (ровно
    // столько, сколько получилось через array_chunk($items, 6) в blade) —
    // это единственный источник истины, дальше только сверяемся с ним.
    var total = slides.length;

    function updateArrowsState() {
        var atStart = current === 0;
        var atEnd   = current >= total - 1;

        if (arrowLeft) {
            arrowLeft.classList.toggle('is-hidden', atStart);
            arrowLeft.disabled = atStart; // нативный disabled — клик физически невозможен,
        }                                  // даже если CSS-класс по какой-то причине не сработал
        if (arrowRight) {
            arrowRight.classList.toggle('is-hidden', atEnd);
            arrowRight.disabled = atEnd;
        }
    }

    function goTo(index) {
        // Жёсткий guard: никогда не выходим за реальные границы массива слайдов,
        // даже если что-то извне попытается вызвать goTo с некорректным индексом.
        if (index < 0 || index > total - 1 || index === current) return;

        slides[current].classList.remove('portfolio__slide--active');
        if (dots[current]) dots[current].classList.remove('portfolio__dot--active');

        current = index;

        slides[current].classList.add('portfolio__slide--active');
        if (dots[current]) dots[current].classList.add('portfolio__dot--active');

        updateArrowsState();
        initMobileScrollbar();
    }

    // Начальное состояние стрелок считается той же функцией, что и все
    // последующие переходы — исключает рассинхронизацию класса и disabled.
    updateArrowsState();

    if (arrowRight) {
        arrowRight.addEventListener('click', function () {
            if (current < total - 1) goTo(current + 1);
        });
    }
    if (arrowLeft) {
        arrowLeft.addEventListener('click', function () {
            if (current > 0) goTo(current - 1);
        });
    }

    dots.forEach(function (dot, i) {
        dot.addEventListener('click', function () { goTo(i); });
    });

    // Мобильный скроллбар для активного слайда
    function initMobileScrollbar() {
        if (window.innerWidth > 768) return;

        var activeSlide = document.querySelector('.portfolio__slide--active');
        var scrollbarThumb = document.getElementById('portfolioScrollbarThumb');
        var scrollbar = document.getElementById('portfolioScrollbar');

        if (!activeSlide || !scrollbarThumb || !scrollbar) return;

        function updateThumbPosition() {
            var scrollLeft = activeSlide.scrollLeft;
            var scrollWidth = activeSlide.scrollWidth;
            var clientWidth = activeSlide.clientWidth;

            if (scrollWidth <= clientWidth) return;

            var scrollableWidth = scrollWidth - clientWidth;
            var scrollbarWidth = scrollbar.clientWidth;
            var thumbWidth = scrollbarThumb.clientWidth;
            var maxThumbPosition = scrollbarWidth - thumbWidth;

            var thumbPosition = (scrollLeft / scrollableWidth) * maxThumbPosition;
            scrollbarThumb.style.left = thumbPosition + 'px';
        }

        if (activeSlide.dataset.scrollbarInit !== '1') {
            activeSlide.dataset.scrollbarInit = '1';
            activeSlide.addEventListener('scroll', updateThumbPosition);
        }

        if (scrollbar.dataset.dragInit !== '1') {
            scrollbar.dataset.dragInit = '1';

            var isDragging = false;
            var startX = 0;
            var startLeft = 0;

            scrollbarThumb.addEventListener('mousedown', function (e) {
                isDragging = true;
                startX = e.clientX;
                startLeft = parseInt(scrollbarThumb.style.left || '0', 10);
                e.preventDefault();
            });

            scrollbarThumb.addEventListener('touchstart', function (e) {
                isDragging = true;
                startX = e.touches[0].clientX;
                startLeft = parseInt(scrollbarThumb.style.left || '0', 10);
                e.preventDefault();
            });

            var handleMove = function (clientX) {
                if (!isDragging) return;

                var active = document.querySelector('.portfolio__slide--active');
                if (!active) return;

                var deltaX = clientX - startX;
                var scrollbarWidth = scrollbar.clientWidth;
                var thumbWidth = scrollbarThumb.clientWidth;
                var maxThumbPosition = scrollbarWidth - thumbWidth;

                var newLeft = startLeft + deltaX;
                newLeft = Math.max(0, Math.min(newLeft, maxThumbPosition));

                var scrollWidth = active.scrollWidth;
                var clientWidth = active.clientWidth;
                var scrollableWidth = scrollWidth - clientWidth;

                var scrollPosition = (newLeft / maxThumbPosition) * scrollableWidth;
                active.scrollLeft = scrollPosition;
            };

            document.addEventListener('mousemove', function (e) { handleMove(e.clientX); });
            document.addEventListener('touchmove', function (e) { handleMove(e.touches[0].clientX); });
            document.addEventListener('mouseup', function () { isDragging = false; });
            document.addEventListener('touchend', function () { isDragging = false; });

            scrollbar.addEventListener('click', function (e) {
                if (e.target === scrollbarThumb) return;

                var active = document.querySelector('.portfolio__slide--active');
                if (!active) return;

                var rect = scrollbar.getBoundingClientRect();
                var clickX = e.clientX - rect.left;
                var thumbWidth = scrollbarThumb.clientWidth;
                var scrollbarWidth = scrollbar.clientWidth;
                var maxThumbPosition = scrollbarWidth - thumbWidth;

                var newLeft = Math.max(0, Math.min(clickX - thumbWidth / 2, maxThumbPosition));

                var scrollWidth = active.scrollWidth;
                var clientWidth = active.clientWidth;
                var scrollableWidth = scrollWidth - clientWidth;

                var scrollPosition = (newLeft / maxThumbPosition) * scrollableWidth;
                active.scrollLeft = scrollPosition;
            });
        }

        updateThumbPosition();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initMobileScrollbar);
    } else {
        initMobileScrollbar();
    }

    window.addEventListener('resize', initMobileScrollbar);
})();
</script>
@endif