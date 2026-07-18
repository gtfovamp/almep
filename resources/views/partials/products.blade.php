{{--
    ═══════════════════════════════════════════════════════════════
    PRODUCTS SECTION — Almep Trading
    Портировано из Astro-компонента, стиль/подход согласован с
    partials/hero.blade.php, partials/about.blade.php,
    partials/services.blade.php (fluid CSS через clamp(), без Tailwind).

    На мобильных — горизонтальная карусель с драгом за кастомный
    скроллбар и подсветкой активной (центральной) карточки — логика
    и вёрстка сохранены как в оригинале, TS-типизация убрана.

    Ожидает переменные: $t (массив переводов), $lang (текущий язык)

    $t['products']['title']
    $t['products']['download_catalog']
    $t['products']['items']        — массив: [ ['title'=>.., 'description'=>.., 'href'=>..], ... ]
    $t['products']['learn_more']
    $t['products']['btn_catalog']
    ═══════════════════════════════════════════════════════════════
--}}
@php
    $productsTitle   = $t['products']['title']            ?? 'Продукция';
    $downloadCatalog = $t['products']['download_catalog']  ?? 'Скачать каталог';
    $items           = $t['products']['items']             ?? [];
    $learnMore       = $t['products']['learn_more']        ?? 'Подробнее';
    $btnCatalog      = $t['products']['btn_catalog']        ?? 'Перейти в каталог';
@endphp

<section class="products">
    <div class="products__inner">

        <h2 class="products__title">{{ $productsTitle }}</h2>

        <div class="products__content">

            {{-- Скачать каталог --}}
            <a href="/catalog.pdf" class="products__download" download>
                {{ $downloadCatalog }}
                <svg width="18" height="19" viewBox="0 0 18 19" fill="none">
                    <path d="M9 1V14M9 14L3 8M9 14L15 8"
                        stroke="#000000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </a>

            {{-- Сетка --}}
            <div class="products__grid" id="productsGrid">
                @foreach($items as $index => $product)
                    @php
                        $image = asset('assets/images/product-' . ($index + 1) . '.png');
                        $href  = '/' . $lang . ($product['href'] ?? '');
                    @endphp
                    <a href="{{ $href }}" class="products__card">

                        {{-- Фото (всегда видно) --}}
                        <div class="products__card-img-wrap">
                            <img
                                src="{{ $image }}"
                                alt="{{ $product['title'] ?? '' }}"
                                class="products__card-img"
                                loading="lazy"
                            >
                        </div>

                        {{-- Тёмный оверлей поверх фото (появляется на hover) --}}
                        <div class="products__card-overlay"></div>

                        {{-- Дефолтная подпись (снизу, скрывается на hover) --}}
                        <div class="products__card-caption">
                            <span class="products__card-title">{{ $product['title'] ?? '' }}</span>
                            <svg width="27" height="29" viewBox="0 0 27 29" fill="none">
                                <path d="M3 26L24 5M24 5H8M24 5V21"
                                    stroke="#003F8D" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>

                        {{-- Hover контент (появляется на hover) --}}
                        <div class="products__card-hover-body">
                            <div class="products__card-hover-top">
                                <h3 class="products__card-hover-title">{{ $product['title'] ?? '' }}</h3>
                                <p class="products__card-hover-desc">{{ $product['description'] ?? '' }}</p>
                            </div>
                            <div class="products__card-hover-footer">
                                <span class="products__card-hover-link">{{ $learnMore }}</span>
                                <svg class="products__card-hover-arrow" width="27" height="29" viewBox="0 0 27 29" fill="none">
                                    <path d="M3 26L24 5M24 5H8M24 5V21"
                                        stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                        </div>

                    </a>
                @endforeach
            </div>

            {{-- Кнопка --}}
            <div class="products__cta">
                <div class="products__scrollbar" id="productsScrollbar">
                    <div class="products__scrollbar-track"></div>
                    <div class="products__scrollbar-thumb" id="productsScrollbarThumb"></div>
                </div>
                <a href="/{{ $lang }}/products" class="products__btn">{{ $btnCatalog }}</a>
            </div>

        </div>
    </div>
</section>

<style>
/* ─── Токены (согласованы с Hero/About/Services) ────────────── */
.products {
    --blue:  #1C508F;
    --dblue: #174480;
    --products-px: clamp(16px, 6vw, 115px);

    width: 100%;
    background: #fff;
    box-sizing: border-box;
}
.products *, .products *::before, .products *::after { box-sizing: border-box; }

.products__inner {
    width: 100%;
    margin: 0 auto;
    padding: clamp(40px, 6vh, 90px) var(--products-px);
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: clamp(28px, 3.6vh, 40px);
}

.products__title {
    margin: 0;
    width: 100%;
    font-family: 'Montserrat', sans-serif;
    font-weight: 500;
    font-size: clamp(24px, 3.2vw, 40px);
    line-height: 1.1;
    color: #000;
}

.products__content {
    display: flex;
    flex-direction: column;
    gap: clamp(28px, 3.5vh, 40px);
    width: 100%;
}

/* ─── Скачать каталог ────────────────────────────────────────── */
.products__download {
    display: flex;
    flex-direction: row;
    align-items: center;
    gap: 11px;
    font-family: 'Montserrat', sans-serif;
    font-weight: 500;
    font-size: clamp(16px, 1.6vw, 20px);
    line-height: 1.1;
    letter-spacing: -0.01em;
    color: #000;
    text-decoration: none;
    transition: opacity .2s;
}
.products__download:hover { opacity: .7; }
.products__download svg { flex-shrink: 0; }

/* ─── Сетка (мобильная карусель по умолчанию) ───────────────── */
.products__grid {
    display: flex;
    flex-direction: row;
    overflow-x: auto;
    overflow-y: hidden;
    scroll-snap-type: x mandatory;
    -webkit-overflow-scrolling: touch;
    scrollbar-width: none;
    -ms-overflow-style: none;
    gap: 12px;
    width: 100%;
    padding: 0 calc(50% - 137px);
}
.products__grid::-webkit-scrollbar { display: none; }

/* ─── Карточка ───────────────────────────────────────────────── */
.products__card {
    position: relative;
    width: 274px;
    height: 440px;
    flex-shrink: 0;
    scroll-snap-align: center;
    border-radius: 7px;
    overflow: hidden;
    display: block;
    cursor: pointer;
    text-decoration: none;
    transform: scale(0.85);
    opacity: .6;
    transition: transform .3s ease, opacity .3s ease;
}
.products__card.is-active { transform: scale(1); opacity: 1; }

/* ─── Фото ───────────────────────────────────────────────────── */
.products__card-img-wrap { position: absolute; inset: 0; z-index: 0; }
.products__card-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
    transition: transform .4s ease;
}
.products__card:hover .products__card-img,
.products__card.is-active .products__card-img { transform: scale(1.04); }

/* ─── Тёмный оверлей ─────────────────────────────────────────── */
.products__card-overlay {
    position: absolute;
    inset: 0;
    z-index: 1;
    background: rgba(0, 0, 0, 0.61);
    opacity: 0;
    transition: opacity .35s ease;
}
.products__card:hover .products__card-overlay,
.products__card.is-active .products__card-overlay { opacity: 1; }

/* ─── Дефолтная подпись ──────────────────────────────────────── */
.products__card-caption {
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 124px;
    background: rgba(255, 255, 255, 0.86);
    display: flex;
    flex-direction: row;
    align-items: flex-start;
    justify-content: space-between;
    padding: 20px;
    z-index: 2;
    opacity: 1;
    transform: translateY(0);
    transition: opacity .3s ease, transform .3s ease;
}
.products__card:hover .products__card-caption,
.products__card.is-active .products__card-caption { opacity: 0; transform: translateY(10px); }

.products__card-title {
    font-family: 'Montserrat', sans-serif;
    font-weight: 600;
    font-size: 18px;
    line-height: 1.3;
    letter-spacing: -0.01em;
    color: #003F8D;
    max-width: 204px;
}
.products__card-caption svg { width: 24px; height: 26px; flex-shrink: 0; }

/* ─── Hover контент ──────────────────────────────────────────── */
.products__card-hover-body {
    position: absolute;
    inset: 0;
    z-index: 3;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    padding: 45px 15px 30px;
    opacity: 0;
    transform: translateY(8px);
    transition: opacity .35s ease .05s, transform .35s ease .05s;
}
.products__card:hover .products__card-hover-body,
.products__card.is-active .products__card-hover-body { opacity: 1; transform: translateY(0); }

.products__card-hover-top {
    display: flex;
    flex-direction: column;
    gap: 27px;
}
.products__card-hover-title {
    margin: 0;
    font-family: 'Montserrat', sans-serif;
    font-weight: 600;
    font-size: 18px;
    line-height: 1.3;
    letter-spacing: -0.01em;
    color: #fff;
}
.products__card-hover-desc {
    margin: 0;
    font-family: 'Montserrat', sans-serif;
    font-weight: 400;
    font-size: 16px;
    line-height: 1.3;
    letter-spacing: -0.01em;
    color: #fff;
}
.products__card-hover-footer {
    display: flex;
    flex-direction: row;
    align-items: center;
    justify-content: space-between;
    width: 100%;
}
.products__card-hover-link {
    font-family: 'Raleway', sans-serif;
    font-weight: 400;
    font-size: 18px;
    line-height: 1.3;
    color: #fff;
}
.products__card-hover-arrow { flex-shrink: 0; }

/* ─── CTA / скроллбар / кнопка ───────────────────────────────── */
.products__cta {
    width: 100%;
    display: flex;
    flex-direction: column;
    gap: 35px;
}

.products__scrollbar {
    display: block;
    position: relative;
    width: 100%;
    height: 13px;
}
.products__scrollbar-track {
    position: absolute;
    width: 100%;
    height: 7px;
    left: 0;
    top: 3px;
    background: #C0C0C0;
    border-radius: 100px;
}
.products__scrollbar-thumb {
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

.products__btn {
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
.products__btn:hover { background: var(--dblue); }
.products__btn:focus-visible { outline: 2px solid var(--dblue); outline-offset: 3px; }

/* ═══════════════════════════════════════════════════════════════
   ДЕСКТОП / ПЛАНШЕТ (≥ 769px) — статичная сетка 3×2 вместо карусели
   ═══════════════════════════════════════════════════════════════ */
@media (min-width: 769px) {
    .products__download { gap: 11px; }
    .products__download svg { width: 18px; height: 19px; transform: none; }

    .products__grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        grid-auto-rows: clamp(280px, 30vw, 350px);
        gap: clamp(18px, 2vw, 25px);
        overflow: visible;
        padding: 0;
    }

    .products__card {
        width: 100%;
        height: 100%;
        transform: none;
        opacity: 1;
    }
    .products__card.is-active { transform: none; }

    .products__card-caption { height: 123px; padding: 20px 28px; }
    .products__card-title { font-size: clamp(18px, 1.6vw, 20px); font-weight: 500; max-width: 100%; }

    .products__card-hover-body { padding: 50px 25px 35px; }
    .products__card-hover-top { gap: 20px; }
    .products__card-hover-title { font-size: clamp(18px, 1.6vw, 20px); font-weight: 500; }

    .products__cta { flex-direction: row; }
    .products__scrollbar { display: none; }

    .products__btn { width: 300px; height: 70px; font-size: clamp(15px, 1.2vw, 17px); }
}

/* ─── Широкий десктоп (≥ 1024px): полноценная сетка 3×2 ─────── */
@media (min-width: 1024px) {
    .products__grid { grid-template-columns: repeat(3, 1fr); }
}

/* ─── Мелкая подстраховка от переполнения на очень узких экранах ─ */
@media (max-width: 380px) {
    .products__inner { padding-left: 14px; padding-right: 14px; }
    .products__title { font-size: 22px; }
}
</style>

<script>
(function () {
    function initProductsScrollbar() {
        if (window.innerWidth > 768) return;

        var grid = document.getElementById('productsGrid');
        var scrollbarThumb = document.getElementById('productsScrollbarThumb');
        var scrollbar = document.getElementById('productsScrollbar');
        var cards = grid ? grid.querySelectorAll('.products__card') : [];

        if (!grid || !scrollbarThumb || !scrollbar || !cards.length) return;
        if (grid.dataset.scrollbarInit === '1') return;
        grid.dataset.scrollbarInit = '1';

        function updateThumbPosition() {
            var scrollLeft = grid.scrollLeft;
            var scrollWidth = grid.scrollWidth;
            var clientWidth = grid.clientWidth;

            if (scrollWidth <= clientWidth) return;

            var scrollableWidth = scrollWidth - clientWidth;
            var scrollbarWidth = scrollbar.clientWidth;
            var thumbWidth = scrollbarThumb.clientWidth;
            var maxThumbPosition = scrollbarWidth - thumbWidth;

            var thumbPosition = (scrollLeft / scrollableWidth) * maxThumbPosition;
            scrollbarThumb.style.left = thumbPosition + 'px';
        }

        function updateActiveCard() {
            var gridRect = grid.getBoundingClientRect();
            var centerX = gridRect.left + gridRect.width / 2;

            cards.forEach(function (card) {
                var cardRect = card.getBoundingClientRect();
                var cardCenterX = cardRect.left + cardRect.width / 2;
                var distance = Math.abs(centerX - cardCenterX);

                if (distance < cardRect.width / 2) {
                    card.classList.add('is-active');
                } else {
                    card.classList.remove('is-active');
                }
            });
        }

        grid.addEventListener('scroll', function () {
            updateThumbPosition();
            updateActiveCard();
        });

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

        function handleMove(clientX) {
            if (!isDragging) return;

            var deltaX = clientX - startX;
            var scrollbarWidth = scrollbar.clientWidth;
            var thumbWidth = scrollbarThumb.clientWidth;
            var maxThumbPosition = scrollbarWidth - thumbWidth;

            var newLeft = startLeft + deltaX;
            newLeft = Math.max(0, Math.min(newLeft, maxThumbPosition));

            var scrollWidth = grid.scrollWidth;
            var clientWidth = grid.clientWidth;
            var scrollableWidth = scrollWidth - clientWidth;

            var scrollPosition = (newLeft / maxThumbPosition) * scrollableWidth;
            grid.scrollLeft = scrollPosition;
        }

        document.addEventListener('mousemove', function (e) { handleMove(e.clientX); });
        document.addEventListener('touchmove', function (e) { handleMove(e.touches[0].clientX); });

        document.addEventListener('mouseup', function () { isDragging = false; });
        document.addEventListener('touchend', function () { isDragging = false; });

        scrollbar.addEventListener('click', function (e) {
            if (e.target === scrollbarThumb) return;

            var rect = scrollbar.getBoundingClientRect();
            var clickX = e.clientX - rect.left;
            var thumbWidth = scrollbarThumb.clientWidth;
            var scrollbarWidth = scrollbar.clientWidth;
            var maxThumbPosition = scrollbarWidth - thumbWidth;

            var newLeft = Math.max(0, Math.min(clickX - thumbWidth / 2, maxThumbPosition));

            var scrollWidth = grid.scrollWidth;
            var clientWidth = grid.clientWidth;
            var scrollableWidth = scrollWidth - clientWidth;

            var scrollPosition = (newLeft / maxThumbPosition) * scrollableWidth;
            grid.scrollLeft = scrollPosition;
        });

        updateThumbPosition();
        updateActiveCard();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initProductsScrollbar);
    } else {
        initProductsScrollbar();
    }

    window.addEventListener('resize', initProductsScrollbar);
})();
</script>