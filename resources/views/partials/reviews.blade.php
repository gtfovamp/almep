{{--
    ═══════════════════════════════════════════════════════════════
    REVIEWS SLIDER SECTION (главная страница) — Almep Trading
    Портировано из Figma-макета ("Отзывы") в Blade, в стиле
    остальных partials проекта (news.blade.php / partners.blade.php).

    Ожидает переменные:
      $t        — массив переводов
      $lang     — текущий язык ('ru' | 'en' | 'az')
      $reviews  — Eloquent-коллекция моделей Testimonial (последние N
                  одобренных отзывов)

    $t['reviews']['title']            — заголовок секции ("Отзывы")
    $t['reviews']['all_reviews']      — текст ссылки "Все отзывы"
    $t['reviews']['aria_next']
    $t['reviews']['aria_prev']
    $t['reviews']['aria_slide']
    $t['reviews']['btn_consultation'] — текст кнопки консультации (мобилка)

    ─── Как получить $reviews в контроллере (для index()) ───

    $data['reviews'] = \App\Models\Testimonial::query()
        ->where('approved', 1)
        ->orderByDesc('id')
        ->limit(9)
        ->get();
    ═══════════════════════════════════════════════════════════════
--}}
@php
    // ─── Поле на нужном языке (fallback на базовое поле, как в news/blog) ───
    $reviewField = function ($item, $lang, $base) {
        $en = $base . '_en';
        $az = $base . '_az';
        if ($lang === 'en') return $item->$en ?: $item->$base;
        if ($lang === 'az') return $item->$az ?: $item->$base;
        return $item->$base;
    };

    $reviewsRaw = collect($reviews ?? []);

    $reviewItems = $reviewsRaw->map(function ($item) use ($reviewField, $lang) {
        return [
            'id'   => $item->id,
            'name' => $reviewField($item, $lang, 'name'),
            'text' => $reviewField($item, $lang, 'text'),
        ];
    })->values();

    // ─── Разбиваем по 3 карточки на слайд, как в News ───
    $reviewSlides = $reviewItems->chunk(3)->values();
@endphp

@if($reviewSlides->isNotEmpty())
<section class="reviews">
  <div class="reviews__inner">

    <h2 class="reviews__title">{{ $t['reviews']['title'] ?? 'Отзывы' }}</h2>

    <div class="reviews__slider-wrap">

      <div class="reviews__slider" id="reviewsSlider">
        @foreach($reviewSlides as $slideIndex => $slide)
          <div class="reviews__slide {{ $slideIndex === 0 ? 'reviews__slide--active' : '' }}">
            <div class="reviews__grid">
              @foreach($slide as $item)
                <div class="reviews__card">
                  <span class="reviews__card-quote" aria-hidden="true">&ldquo;</span>

                  <div class="reviews__card-body">
                    <p class="reviews__card-text">{{ $item['text'] }}</p>
                    <span class="reviews__card-line" aria-hidden="true"></span>
                    <h3 class="reviews__card-name">{{ $item['name'] }}</h3>
                  </div>
                </div>
              @endforeach
            </div>
          </div>
        @endforeach
      </div>

      {{-- Стрелка вправо --}}
      <button class="reviews__arrow reviews__arrow--right" id="reviewsArrowRight" aria-label="{{ $t['reviews']['aria_next'] ?? '' }}">
        <svg width="23" height="30" viewBox="0 0 23 30" fill="none">
          <path d="M3 3L20 15L3 27" stroke="#000000" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </button>

      {{-- Стрелка влево --}}
      <button class="reviews__arrow reviews__arrow--left" id="reviewsArrowLeft" aria-label="{{ $t['reviews']['aria_prev'] ?? '' }}">
        <svg width="23" height="30" viewBox="0 0 23 30" fill="none">
          <path d="M20 3L3 15L20 27" stroke="#000000" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </button>

    </div>

    {{-- Нижняя панель: точки + ссылка "Все отзывы" --}}
    <div class="reviews__bottom">

      <div class="reviews__dots" id="reviewsDots">
        @foreach($reviewSlides as $i => $slide)
          <button
            class="reviews__dot {{ $i === 0 ? 'reviews__dot--active' : '' }}"
            data-index="{{ $i }}"
            aria-label="{{ ($t['reviews']['aria_slide'] ?? '') . ' ' . ($i + 1) }}"
          ></button>
        @endforeach
      </div>

      <div class="reviews__scrollbar" id="reviewsScrollbar">
        <div class="reviews__scrollbar-track"></div>
        <div class="reviews__scrollbar-thumb" id="reviewsScrollbarThumb"></div>
      </div>

      <a href="/{{ $lang }}/reviews" class="reviews__more">
        <span class="reviews__more-text">{{ $t['reviews']['all_reviews'] ?? 'Все отзывы' }}</span>
        <span class="reviews__link-arrow" aria-hidden="true"></span>
      </a>
    </div>

    {{-- Кнопка консультации (только на мобилке) --}}
    <div class="reviews__cta">
      <button type="button" class="reviews__btn" data-open-consultation>
        {{ $t['reviews']['btn_consultation'] ?? 'Запросить консультацию' }}
      </button>
    </div>

  </div>
</section>

<style>
  /* ===== Токены (та же сетка, что и в news.blade.php) ===== */
  .reviews {
    --blue:  #1C508F;
    --dblue: #174480;
    --reviews-px: clamp(16px, 6vw, 115px);

    width: 100%;
    background: #FFFFFF;
    box-sizing: border-box;
  }
  .reviews *, .reviews *::before, .reviews *::after { box-sizing: border-box; }

  .reviews__inner {
    width: 100%;
    margin: 0 auto;
    padding: clamp(40px, 6vh, 90px) var(--reviews-px);
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: clamp(28px, 4.5vh, 60px);
  }

  /* ===== Заголовок ===== */
  .reviews__title {
    margin: 0;
    width: 100%;
    font-family: 'Montserrat', sans-serif;
    font-weight: 500;
    font-size: clamp(26px, 3.6vw, 48px);
    line-height: 1.1;
    color: #000;
  }

  /* ===== Слайдер ===== */
  .reviews__slider-wrap {
    position: relative;
    width: 100%;
  }

  .reviews__slider {
    width: 100%;
    overflow: hidden;
  }

  .reviews__slide {
    display: none;
  }

  .reviews__slide--active {
    display: block;
  }

  /* ===== Сетка карточек ===== */
  .reviews__grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 30px;
    padding: 1px;
  }

  /* ===== Карточка ===== */
  .reviews__card {
    position: relative;
    display: flex;
    flex-direction: column;
    width: 100%;
    min-height: 420px;
    background: #FFFFFF;
    box-shadow: 0px 0px 4px rgba(0, 0, 0, 0.3);
    border-radius: 7px;
    padding: 25px;
    overflow: hidden;
  }

  /* ===== Кавычка ===== */
  .reviews__card-quote {
    font-family: 'Raleway', sans-serif;
    font-weight: 400;
    font-size: 128px;
    line-height: 1.1;
    color: #000000;
    height: 91px;
    flex-shrink: 0;
  }

  /* ===== Тело карточки ===== */
  .reviews__card-body {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: 22px;
    flex: 1;
    margin-top: 22px;
  }

  .reviews__card-text {
    margin: 0;
    width: 100%;
    font-family: 'Montserrat', sans-serif;
    font-weight: 400;
    font-size: 18px;
    line-height: 130%;
    letter-spacing: -0.01em;
    color: #000000;
    flex: 1;
    display: -webkit-box;
    -webkit-line-clamp: 6;
    -webkit-box-orient: vertical;
    overflow: hidden;
  }

  .reviews__card-line {
    display: block;
    width: 100%;
    height: 0;
    border-top: 1px solid #000000;
    flex-shrink: 0;
  }

  .reviews__card-name {
    margin: 0;
    width: 100%;
    font-family: 'Montserrat', sans-serif;
    font-weight: 500;
    font-size: 24px;
    line-height: 110%;
    letter-spacing: -0.01em;
    color: #000000;
  }

  /* ===== Стрелка ссылок (reviews__more) ─────────
     Тот же приём, что и в news__link-arrow / partners__link-arrow. ─── */
  .reviews__link-arrow {
    position: relative;
    flex-shrink: 0;
    width: clamp(16px, 1.8vw, 24px);
    height: clamp(10px, 1.2vw, 14px);
  }

  .reviews__link-arrow::before,
  .reviews__link-arrow::after {
    content: "";
    position: absolute;
    background: currentColor;
  }

  .reviews__link-arrow::before {
    top: 50%;
    left: 0;
    width: 100%;
    height: clamp(1.5px, 0.15vw, 2px);
    transform: translateY(-50%);
  }

  .reviews__link-arrow::after {
    top: 50%;
    right: 0;
    width: clamp(8px, 0.9vw, 12px);
    height: clamp(8px, 0.9vw, 12px);
    border-top: clamp(1.5px, 0.15vw, 2px) solid currentColor;
    border-right: clamp(1.5px, 0.15vw, 2px) solid currentColor;
    background: transparent;
    transform: translateY(-50%) rotate(45deg);
  }

  /* ===== Стрелки слайдера (круглые кнопки prev/next) ===== */
  .reviews__arrow {
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
    transition: background 0.2s ease;
    z-index: 10;
  }

  .reviews__arrow:hover {
    background: #E0E0E0;
  }

  .reviews__arrow--right {
    right: -38px;
  }

  .reviews__arrow--left {
    left: -38px;
  }

  .reviews__arrow--left.is-hidden,
  .reviews__arrow--right.is-hidden {
    opacity: 0;
    pointer-events: none;
  }

  /* ===== Нижняя панель ===== */
  .reviews__bottom {
    display: flex;
    flex-direction: row;
    justify-content: space-between;
    align-items: center;
    width: 100%;
    max-width: 1410px;
    position: relative;
  }

  /* ===== Точки ===== */
  .reviews__dots {
    display: flex;
    flex-direction: row;
    align-items: center;
    gap: 10px;
    position: absolute;
    left: 50%;
    transform: translateX(-50%);
  }

  .reviews__dot {
    width: 17px;
    height: 17px;
    border-radius: 50%;
    border: none;
    background: #D9D9D9;
    cursor: pointer;
    padding: 0;
    transition: background 0.2s ease;
  }

  .reviews__dot--active {
    background: var(--blue);
  }

  /* ===== Ссылка "Все отзывы" ===== */
  .reviews__more {
    display: flex;
    flex-direction: row;
    align-items: center;
    gap: 11px;
    color: var(--blue);
    text-decoration: none;
    transition: opacity 0.2s ease;
  }

  .reviews__more:hover {
    opacity: 0.75;
  }

  .reviews__more-text {
    font-family: 'Montserrat', sans-serif;
    font-weight: 400;
    font-size: 24px;
    line-height: 110%;
    color: inherit;
  }

  /* ===== Скроллбар (мобилка) — скрыт на десктопе ===== */
  .reviews__scrollbar { display: none; }

  /* Кнопка консультации (скрыта на десктопе) */
  .reviews__cta {
    display: none;
  }

  /* === МОБИЛЬНАЯ ВЕРСИЯ === */
  @media (max-width: 768px) {
    .reviews__inner {
      width: 100%;
      gap: 45px;
    }

    .reviews__title {
      font-size: 24px;
      line-height: 110%;
    }

    .reviews__slider-wrap {
      width: 100%;
      overflow: visible;
    }

    .reviews__slider {
      overflow: visible;
    }

    .reviews__slide {
      display: none;
    }

    .reviews__slide--active {
      display: flex;
      overflow-x: auto;
      overflow-y: hidden;
      scroll-snap-type: x mandatory;
      -webkit-overflow-scrolling: touch;
      scrollbar-width: none;
      -ms-overflow-style: none;
    }

    .reviews__slide--active::-webkit-scrollbar {
      display: none;
    }

    .reviews__grid {
      display: flex;
      flex-direction: row;
      gap: 15px;
      grid-template-columns: none;
      padding: 0;
    }

    .reviews__card {
      width: 274px;
      min-height: 380px;
      flex-shrink: 0;
      scroll-snap-align: start;
      padding: 20px;
    }

    .reviews__card-quote {
      font-size: 90px;
      height: 65px;
    }

    .reviews__card-body {
      gap: 16px;
      margin-top: 16px;
    }

    .reviews__card-text {
      font-size: 16px;
      line-height: 130%;
      -webkit-line-clamp: 7;
    }

    .reviews__card-name {
      font-size: 18px;
      line-height: 130%;
    }

    .reviews__arrow {
      display: none;
    }

    .reviews__bottom {
      width: 100%;
      flex-direction: column;
      gap: 35px;
      align-items: flex-end;
    }

    .reviews__scrollbar {
      display: block;
      position: relative;
      width: 100%;
      height: 13px;
      order: 1;
    }

    .reviews__scrollbar-track {
      position: absolute;
      width: 100%;
      height: 7px;
      left: 0;
      top: 3px;
      background: #C0C0C0;
      border-radius: 100px;
    }

    .reviews__scrollbar-thumb {
      position: absolute;
      width: 94px;
      height: 13px;
      left: 0;
      top: 0;
      background: var(--blue);
      border-radius: 100px;
      cursor: pointer;
      transition: left 0.1s ease-out;
    }

    .reviews__dots {
      display: none;
    }

    .reviews__more {
      order: 0;
      flex-shrink: 0;
      color: #000000;
    }

    .reviews__more-text {
      font-size: 16px;
      line-height: 130%;
    }

    .reviews__cta {
      display: block;
      width: 100%;
    }

    .reviews__btn {
      display: flex;
      align-items: center;
      justify-content: center;
      width: 100%;
      height: 85px;
      background: var(--blue);
      box-shadow: 0px 0px 4px rgba(0, 0, 0, 0.3);
      border-radius: 9px;
      border: none;
      cursor: pointer;
      font-family: 'Montserrat', sans-serif;
      font-weight: 400;
      font-size: 16px;
      line-height: 130%;
      letter-spacing: -0.01em;
      color: #FFFFFF;
      transition: background 0.2s ease;
    }

    .reviews__btn:hover {
      background: var(--dblue);
    }
  }
</style>

<script>
  (function () {
    var reviewsSlides   = document.querySelectorAll('.reviews__slide');
    var reviewsDots      = document.querySelectorAll('.reviews__dot');
    var reviewsArrowRight = document.getElementById('reviewsArrowRight');
    var reviewsArrowLeft  = document.getElementById('reviewsArrowLeft');

    var reviewsCurrent = 0;
    var reviewsTotal    = reviewsSlides.length;

    function reviewsGoTo(index) {
      reviewsSlides[reviewsCurrent].classList.remove('reviews__slide--active');
      reviewsDots[reviewsCurrent].classList.remove('reviews__dot--active');

      reviewsCurrent = index;

      reviewsSlides[reviewsCurrent].classList.add('reviews__slide--active');
      reviewsDots[reviewsCurrent].classList.add('reviews__dot--active');

      if (reviewsArrowLeft)  reviewsArrowLeft.classList.toggle('is-hidden', reviewsCurrent === 0);
      if (reviewsArrowRight) reviewsArrowRight.classList.toggle('is-hidden', reviewsCurrent === reviewsTotal - 1);
    }

    // Инициализация
    if (reviewsArrowLeft) reviewsArrowLeft.classList.add('is-hidden');
    if (reviewsTotal <= 1 && reviewsArrowRight) reviewsArrowRight.classList.add('is-hidden');

    if (reviewsArrowRight) {
      reviewsArrowRight.addEventListener('click', function () {
        if (reviewsCurrent < reviewsTotal - 1) reviewsGoTo(reviewsCurrent + 1);
      });
    }

    if (reviewsArrowLeft) {
      reviewsArrowLeft.addEventListener('click', function () {
        if (reviewsCurrent > 0) reviewsGoTo(reviewsCurrent - 1);
      });
    }

    reviewsDots.forEach(function (dot, i) {
      dot.addEventListener('click', function () { reviewsGoTo(i); });
    });

    // Мобильный скроллбар для Reviews
    function initReviewsScrollbar() {
      if (window.innerWidth > 768) return;

      var activeSlide    = document.querySelector('.reviews__slide--active');
      var scrollbarThumb  = document.getElementById('reviewsScrollbarThumb');
      var scrollbar       = document.getElementById('reviewsScrollbar');

      if (!activeSlide || !scrollbarThumb || !scrollbar) return;

      function updateThumbPosition() {
        var scrollLeft  = activeSlide.scrollLeft;
        var scrollWidth = activeSlide.scrollWidth;
        var clientWidth = activeSlide.clientWidth;

        if (scrollWidth <= clientWidth) return;

        var scrollableWidth  = scrollWidth - clientWidth;
        var scrollbarWidth   = scrollbar.clientWidth;
        var thumbWidth        = scrollbarThumb.clientWidth;
        var maxThumbPosition  = scrollbarWidth - thumbWidth;

        var thumbPosition = (scrollLeft / scrollableWidth) * maxThumbPosition;
        scrollbarThumb.style.left = thumbPosition + 'px';
      }

      activeSlide.addEventListener('scroll', updateThumbPosition);

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

        var deltaX           = clientX - startX;
        var scrollbarWidth   = scrollbar.clientWidth;
        var thumbWidth        = scrollbarThumb.clientWidth;
        var maxThumbPosition  = scrollbarWidth - thumbWidth;

        var newLeft = startLeft + deltaX;
        newLeft = Math.max(0, Math.min(newLeft, maxThumbPosition));

        var scrollWidth      = activeSlide.scrollWidth;
        var clientWidth      = activeSlide.clientWidth;
        var scrollableWidth  = scrollWidth - clientWidth;

        var scrollPosition = (newLeft / maxThumbPosition) * scrollableWidth;
        activeSlide.scrollLeft = scrollPosition;
      }

      document.addEventListener('mousemove', function (e) { handleMove(e.clientX); });
      document.addEventListener('touchmove', function (e) { handleMove(e.touches[0].clientX); });

      document.addEventListener('mouseup', function () { isDragging = false; });
      document.addEventListener('touchend', function () { isDragging = false; });

      scrollbar.addEventListener('click', function (e) {
        if (e.target === scrollbarThumb) return;

        var rect          = scrollbar.getBoundingClientRect();
        var clickX         = e.clientX - rect.left;
        var thumbWidth      = scrollbarThumb.clientWidth;
        var scrollbarWidth  = scrollbar.clientWidth;
        var maxThumbPosition = scrollbarWidth - thumbWidth;

        var newLeft = Math.max(0, Math.min(clickX - thumbWidth / 2, maxThumbPosition));

        var scrollWidth      = activeSlide.scrollWidth;
        var clientWidth      = activeSlide.clientWidth;
        var scrollableWidth  = scrollWidth - clientWidth;

        var scrollPosition = (newLeft / maxThumbPosition) * scrollableWidth;
        activeSlide.scrollLeft = scrollPosition;
      });

      updateThumbPosition();
    }

    initReviewsScrollbar();

    window.addEventListener('resize', function () {
      initReviewsScrollbar();
    });
  })();
</script>
@endif