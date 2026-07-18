{{--
    ═══════════════════════════════════════════════════════════════
    NEWS SLIDER SECTION (главная страница) — Almep Trading
    Портировано из Astro-компонента (news.astro) в Blade,
    в стиле остальных partials проекта (partners.blade.php и т.п.).

    Логика из Astro-фронтматтера (расчёт времени чтения, вырезка
    excerpt, чтение блоков контента, "time ago") перенесена в PHP
    и оформлена так же, как в resources/views/news/index.blade.php
    (те же самые $newsBlockText / $newsExcerpt / $newsReadTime,
    только адаптированные под карточки слайдера).

    Ожидает переменные:
      $t     — массив переводов
      $lang  — текущий язык ('ru' | 'en' | 'az')
      $news  — Eloquent-коллекция моделей News (последние N новостей)

    $t['news']['title']
    $t['news']['category_company']
    $t['news']['date_alt']
    $t['news']['time_alt']
    $t['news']['learn_more']
    $t['news']['read_news']
    $t['news']['btn_consultation']
    $t['news']['aria_next']
    $t['news']['aria_prev']
    $t['news']['aria_slide']
    $t['news']['minute']   — fallback-текст "минута" (ед. число)
    $t['news']['minutes']  — fallback-текст "минут" (мн. число)

    ─── Как получить $news в контроллере ───

    $data['news'] = \App\Models\News::query()
        ->orderByDesc('published_at')
        ->limit(9)
        ->get();
    ═══════════════════════════════════════════════════════════════
--}}
@php
    // ─── Поле на нужном языке (title_ru нет в таблице — фоллбэк на title) ───
    $newsField = function ($item, $lang, $base) {
        $en = $base . '_en';
        $az = $base . '_az';
        if ($lang === 'en') return $item->$en ?: $item->$base;
        if ($lang === 'az') return $item->$az ?: $item->$base;
        return $item->$base;
    };

    // ─── Блоки контента (JSON-колонка) ───
    $newsBlocks = function ($item) {
        $b = $item->blocks ?? null;
        if (is_string($b)) { $b = json_decode($b, true) ?: []; }
        return is_array($b) ? $b : [];
    };

    // Структура блока в БД: ['type' => 'heading'|'text'|.., 'content' => ['ru'=>.., 'en'=>.., 'az'=>..]]
    $newsBlockText = function (array $block, string $lang): string {
        $content = $block['content'] ?? null;
        if (!is_array($content)) return is_string($content) ? trim($content) : '';
        return trim((string) ($content[$lang] ?? $content['ru'] ?? ''));
    };

    // ─── Вырезка (excerpt) — режем по границе слова, добавляем типографское "…" ───
    $newsExcerpt = function ($blocks, $lang, $max = 150) use ($newsBlockText) {
        $textBlocks = array_filter($blocks, fn($b) => in_array(($b['type'] ?? ''), ['paragraph', 'text']));
        $parts = array_filter(array_map(fn($b) => $newsBlockText($b, $lang), $textBlocks));
        $text = trim(implode(' ', $parts));
        if ($text === '') return '';
        if (mb_strlen($text) <= $max) return $text;
        $cut = mb_substr($text, 0, $max);
        $lastSpace = mb_strrpos($cut, ' ');
        if ($lastSpace !== false) { $cut = mb_substr($cut, 0, $lastSpace); }
        return rtrim($cut, " .,;:") . '…';
    };

    // ─── Время чтения (~200 слов/мин) ───
    $newsReadTime = function ($blocks, $lang) use ($newsBlockText) {
        $all = implode(' ', array_map(fn($b) => $newsBlockText($b, $lang), $blocks));
        $words = preg_split('/\s+/', trim($all), -1, PREG_SPLIT_NO_EMPTY);
        return max(1, (int) ceil(count($words) / 200));
    };

    // ─── Склонение "минута/минуты/минут" под каждый язык ───
    $newsMinuteLabel = function (int $mins, string $lang) use ($t) {
        if ($lang === 'ru') {
            if ($mins === 1) return 'минута';
            if ($mins >= 2 && $mins <= 4) return 'минуты';
            return $t['news']['minutes'] ?? 'минут';
        }
        if ($lang === 'en') {
            return $mins === 1 ? ($t['news']['minute'] ?? 'minute') : ($t['news']['minutes'] ?? 'minutes');
        }
        // az
        return 'dəqiqə';
    };

    $newsFormatReadTime = function (int $mins, string $lang) use ($newsMinuteLabel) {
        return trim($mins . ' ' . $newsMinuteLabel($mins, $lang));
    };

    // ─── Обычная дата (d.m.Y с учётом локали) ───
    $newsDate = function ($val, string $lang) {
        if (empty($val)) return '';
        try {
            $locale = $lang === 'ru' ? 'ru_RU' : ($lang === 'az' ? 'az_AZ' : 'en_US');
            return \Carbon\Carbon::parse($val)->locale($locale)->translatedFormat('d.m.Y');
        } catch (\Throwable $e) {
            return '';
        }
    };

    // ─── "N дней назад" — аналог timeAgo() из lib/time-helpers.ts ───
    $newsTimeAgo = function ($val, string $lang) {
        if (empty($val)) return '';
        try {
            $locale = $lang === 'ru' ? 'ru' : ($lang === 'az' ? 'az' : 'en');
            \Carbon\Carbon::setLocale($locale);
            return \Carbon\Carbon::parse($val)->diffForHumans();
        } catch (\Throwable $e) {
            return '';
        }
    };

    $newsCategory = $t['news']['category_company'] ?? '';

    // ─── Собираем нормализованный список карточек ───
    $newsRaw = collect($news ?? []);

    $newsItems = $newsRaw->map(function ($item) use (
        $newsField, $newsBlocks, $newsExcerpt, $newsReadTime,
        $newsFormatReadTime, $newsDate, $newsTimeAgo, $newsCategory, $lang
    ) {
        $blocks = $newsBlocks($item);
        $mins   = $newsReadTime($blocks, $lang);

        return [
            'id'               => $item->id,
            'title'            => $newsField($item, $lang, 'title'),
            'excerpt'          => $newsExcerpt($blocks, $lang),
            'image'            => $item->cover_image_url,
            'link'             => "/{$lang}/news/{$item->id}",
            'category'         => $newsCategory,
            'date'             => $newsDate($item->published_at, $lang),
            'timeAgo'          => $newsTimeAgo($item->published_at, $lang),
            'readTimeFormatted'=> $newsFormatReadTime($mins, $lang),
        ];
    })->values();

    // ─── Разбиваем по 3 карточки на слайд ───
    $newsSlides = $newsItems->chunk(3)->values();
@endphp

@if($newsSlides->isNotEmpty())
<section class="news">
  <div class="news__inner">

    <h2 class="news__title">{{ $t['news']['title'] ?? 'Новости' }}</h2>

    <div class="news__slider-wrap">

      <div class="news__slider" id="newsSlider">
        @foreach($newsSlides as $slideIndex => $slide)
          <div class="news__slide {{ $slideIndex === 0 ? 'news__slide--active' : '' }}">
            <div class="news__grid">
              @foreach($slide as $item)
                <div class="news__card">

                  {{-- Картинка --}}
                  <div class="news__card-img-wrap">
                    <img
                      src="{{ $item['image'] }}"
                      alt="{{ $item['title'] }}"
                      class="news__card-img"
                      loading="lazy"
                    >
                  </div>

                  {{-- Контент --}}
                  <div class="news__card-body">

                    <div class="news__card-meta-wrap">

                      {{-- Категория --}}
                      <div class="news__card-category">
                        <span class="news__card-category-text">{{ $item['category'] }}</span>
                      </div>

                      {{-- Дата и время публикации --}}
                      <div class="news__card-meta">
                        <div class="news__card-date">
                          <img src="/assets/icons/calendar.svg" alt="{{ $t['news']['date_alt'] ?? '' }}" class="news__card-icon" width="28" height="28">
                          <span class="news__card-meta-text">{{ $item['date'] }}</span>
                        </div>
                        <div class="news__card-time">
                          <img src="/assets/icons/time.svg" alt="{{ $t['news']['time_alt'] ?? '' }}" class="news__card-icon" width="29" height="29">
                          <span class="news__card-meta-text">{{ $item['timeAgo'] }}</span>
                        </div>
                      </div>

                    </div>

                    {{-- Текст --}}
                    <div class="news__card-content">
                      <h3 class="news__card-title">{{ $item['title'] }}</h3>
                      <p class="news__card-excerpt">{{ $item['excerpt'] }}</p>
                    </div>

                    {{-- Ссылка --}}
                    <a href="{{ $item['link'] }}" class="news__card-link">
                      <span class="news__card-link-text">{{ $t['news']['learn_more'] ?? '' }}</span>
                      <span class="news__link-arrow" aria-hidden="true"></span>
                    </a>

                  </div>

                </div>
              @endforeach
            </div>
          </div>
        @endforeach
      </div>

      {{-- Стрелка вправо --}}
      <button class="news__arrow news__arrow--right" id="newsArrowRight" aria-label="{{ $t['news']['aria_next'] ?? '' }}">
        <svg width="23" height="30" viewBox="0 0 23 30" fill="none">
          <path d="M3 3L20 15L3 27" stroke="#000000" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </button>

      {{-- Стрелка влево --}}
      <button class="news__arrow news__arrow--left" id="newsArrowLeft" aria-label="{{ $t['news']['aria_prev'] ?? '' }}">
        <svg width="23" height="30" viewBox="0 0 23 30" fill="none">
          <path d="M20 3L3 15L20 27" stroke="#000000" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </button>

    </div>

    {{-- Нижняя панель: точки + ссылка --}}
    <div class="news__bottom">

      <div class="news__dots" id="newsDots">
        @foreach($newsSlides as $i => $slide)
          <button
            class="news__dot {{ $i === 0 ? 'news__dot--active' : '' }}"
            data-index="{{ $i }}"
            aria-label="{{ ($t['news']['aria_slide'] ?? '') . ' ' . ($i + 1) }}"
          ></button>
        @endforeach
      </div>

      <div class="news__scrollbar" id="newsScrollbar">
        <div class="news__scrollbar-track"></div>
        <div class="news__scrollbar-thumb" id="newsScrollbarThumb"></div>
      </div>

      <a href="/{{ $lang }}/news" class="news__more">
        <span class="news__more-text">{{ $t['news']['read_news'] ?? '' }}</span>
        <span class="news__link-arrow" aria-hidden="true"></span>
      </a>
    </div>
            <button type="button" class="partners__btn" data-open-consultation>
                {{ 
                    $t['news']['btn_consultation']
                 }}
            </button>
  </div>

</section>

<style>
  /* ===== Токены (та же сетка, что и в partners.blade.php) ===== */
  .news {
    --blue:  #1C508F;
    --dblue: #174480;
    --news-px: clamp(16px, 6vw, 115px);

    width: 100%;
    background: #FFFFFF;
    box-sizing: border-box;
  }
  .news *, .news *::before, .news *::after { box-sizing: border-box; }

  .news__inner {
    width: 100%;
    margin: 0 auto;
    padding: clamp(40px, 6vh, 90px) var(--news-px);
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: clamp(28px, 4.5vh, 50px);
  }

  /* ===== Заголовок ===== */
  .news__title {
    margin: 0;
    width: 100%;
    font-family: 'Montserrat', sans-serif;
    font-weight: 500;
    font-size: clamp(26px, 3.6vw, 48px);
    line-height: 1.1;
    color: #000;
  }

  /* ===== Слайдер ===== */
  .news__slider-wrap {
    position: relative;
    width: 100%;
  }

  .news__slider {
    width: 100%;
    overflow: hidden;
  }

  .news__slide {
    display: none;
  }

  .news__slide--active {
    display: block;
  }

  /* ===== Сетка карточек ===== */
  .news__grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 25px;
    padding: 0;
  }

  /* ===== Карточка ===== */
  .news__card {
    display: flex;
    flex-direction: column;
    width: 100%;
    min-height: 580px;
    background: #FFFFFF;
    border: 1px solid rgba(0, 0, 0, 0.2);
    border-radius: 7px;
    overflow: hidden;
  }

  /* ===== Картинка ===== */
  .news__card-img-wrap {
    width: 100%;
    height: 250px;
    overflow: hidden;
    flex-shrink: 0;
  }

  .news__card-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
    transition: transform 0.3s ease;
  }

  .news__card:hover .news__card-img {
    transform: scale(1.03);
  }

  /* ===== Тело карточки ===== */
  .news__card-body {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: 12px;
    padding: 20px 25px;
    flex: 1;
  }

  /* ===== Мета-обёртка ===== */
  .news__card-meta-wrap {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: 12px;
    width: 100%;
  }

  /* ===== Категория ===== */
  .news__card-category {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 160px;
    height: 36px;
    background: #A4C5EE;
    border-radius: 17px;
    flex-shrink: 0;
  }

  .news__card-category-text {
    font-family: 'Raleway', sans-serif;
    font-weight: 400;
    font-size: 14px;
    line-height: 110%;
    color: #003F8D;
  }

  /* ===== Дата + время ===== */
  .news__card-meta {
    display: flex;
    flex-direction: row;
    justify-content: space-between;
    align-items: center;
    width: 100%;
  }

  .news__card-date,
  .news__card-time {
    display: flex;
    flex-direction: row;
    align-items: center;
    gap: 10px;
  }

  .news__card-icon {
    flex-shrink: 0;
    object-fit: contain;
    width: 24px;
    height: 24px;
  }

  .news__card-meta-text {
    font-family: 'Raleway', sans-serif;
    font-weight: 400;
    font-size: 14px;
    line-height: 110%;
    color: #676767;
    white-space: nowrap;
  }

  /* ===== Контент ===== */
  .news__card-content {
    display: flex;
    flex-direction: column;
    gap: 3px;
    width: 100%;
  }

  .news__card-title {
    font-family: 'Montserrat', sans-serif;
    font-weight: 500;
    font-size: 18px;
    line-height: 110%;
    letter-spacing: -0.01em;
    color: #000000;
    margin: 0;
  }

  .news__card-excerpt {
    font-family: 'Montserrat', sans-serif;
    font-weight: 400;
    font-size: 16px;
    line-height: 130%;
    letter-spacing: -0.01em;
    color: #000000;
    margin: 0;
  }

  /* ===== Ссылка "Узнать больше" ===== */
  .news__card-link {
    display: flex;
    flex-direction: row;
    align-items: center;
    gap: 11px;
    color: var(--blue);
    text-decoration: none;
    margin-top: auto;
    transition: opacity 0.2s ease;
  }

  .news__card-link:hover {
    opacity: 0.75;
  }

  .news__card-link-text {
    font-family: 'Raleway', sans-serif;
    font-weight: 500;
    font-size: 20px;
    line-height: 110%;
    color: inherit;
  }

  /* ===== Стрелка ссылок (news__card-link / news__more) ─────────
     Тот же приём, что и в partners__link-arrow: линия + уголок
     через currentColor, поэтому цвет наследуется от родителя без
     отдельных override'ов под мобилку. ────────────────────────── */
  .news__link-arrow {
    position: relative;
    flex-shrink: 0;
    width: clamp(16px, 1.8vw, 24px);
    height: clamp(10px, 1.2vw, 14px);
  }

  .news__link-arrow::before,
  .news__link-arrow::after {
    content: "";
    position: absolute;
    background: currentColor;
  }

  .news__link-arrow::before {
    top: 50%;
    left: 0;
    width: 100%;
    height: clamp(1.5px, 0.15vw, 2px);
    transform: translateY(-50%);
  }

  .news__link-arrow::after {
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
  .news__arrow {
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

  .news__arrow:hover {
    background: #E0E0E0;
  }

  .news__arrow--right {
    right: -38px;
  }

  .news__arrow--left {
    left: -38px;
  }

  .news__arrow--left.is-hidden,
  .news__arrow--right.is-hidden {
    opacity: 0;
    pointer-events: none;
  }

  /* ===== Нижняя панель ===== */
  .news__bottom {
    display: flex;
    flex-direction: row;
    justify-content: space-between;
    align-items: center;
    width: 100%;
    max-width: 1410px;
    position: relative;
  }

  /* ===== Точки ===== */
  .news__dots {
    display: flex;
    flex-direction: row;
    align-items: center;
    gap: 10px;
    position: absolute;
    left: 50%;
    transform: translateX(-50%);
  }

  .news__dot {
    width: 17px;
    height: 17px;
    border-radius: 50%;
    border: none;
    background: #D9D9D9;
    cursor: pointer;
    padding: 0;
    transition: background 0.2s ease;
  }

  .news__dot--active {
    background: var(--blue);
  }

  /* ===== Ссылка "Читать новости" ===== */
  .news__more {
    display: flex;
    flex-direction: row;
    align-items: center;
    gap: 11px;
    color: var(--blue);
    text-decoration: none;
    transition: opacity 0.2s ease;
  }

  .news__more:hover {
    opacity: 0.75;
  }

  .news__more-text {
    font-family: 'Montserrat', sans-serif;
    font-weight: 400;
    font-size: 20px;
    line-height: 110%;
    color: inherit;
  }

  /* Кнопка консультации (скрыта на десктопе) */
  .news__cta {
    display: none;
  }

  /* === МОБИЛЬНАЯ ВЕРСИЯ === */
  @media (max-width: 768px) {
    .news__inner {
      width: 100%;
      gap: 45px;
    }

    .news__title {
      font-size: 24px;
      line-height: 110%;
    }

    .news__slider-wrap {
      width: 100%;
      overflow: visible;
    }

    .news__slider {
      overflow: visible;
    }

    .news__slide {
      display: none;
    }

    .news__slide--active {
      display: flex;
      overflow-x: auto;
      overflow-y: hidden;
      scroll-snap-type: x mandatory;
      -webkit-overflow-scrolling: touch;
      scrollbar-width: none;
      -ms-overflow-style: none;
    }

    .news__slide--active::-webkit-scrollbar {
      display: none;
    }

    .news__grid {
      display: flex;
      flex-direction: row;
      gap: 15px;
      grid-template-columns: none;
      padding: 0;
    }

    .news__card {
      width: 274px;
      min-height: 684px;
      flex-shrink: 0;
      scroll-snap-align: start;
    }

    .news__card-img-wrap {
      height: 300px;
    }

    .news__card-body {
      padding: 20px 15px;
      gap: 19px;
    }

    .news__card-meta-wrap {
      gap: 15px;
    }

    .news__card-category {
      width: 161px;
      height: 37.72px;
      border-radius: 15.64px;
    }

    .news__card-category-text {
      font-size: 14.72px;
      line-height: 110%;
    }

    .news__card-meta {
      gap: 15px;
      flex-wrap: wrap;
    }

    .news__card-date,
    .news__card-time {
      gap: 10px;
    }

    .news__card-icon {
      width: 25px;
      height: 25px;
    }

    .news__card-meta-text {
      font-size: 16px;
      line-height: 110%;
    }

    .news__card-content {
      gap: 25px;
    }

    .news__card-title {
      font-size: 18px;
      line-height: 130%;
    }

    .news__card-excerpt {
      font-size: 16px;
      line-height: 130%;
    }

    .news__card-link {
      gap: 11px;
    }

    .news__card-link-text {
      font-size: 16px;
      line-height: 130%;
    }

    .news__arrow {
      display: none;
    }

    .news__bottom {
      width: 100%;
      flex-direction: column;
      gap: 35px;
      align-items: flex-end;
    }

    .news__scrollbar {
      display: block;
      position: relative;
      width: 100%;
      height: 13px;
      order: 1;
    }

    .news__scrollbar-track {
      position: absolute;
      width: 100%;
      height: 7px;
      left: 0;
      top: 3px;
      background: #C0C0C0;
      border-radius: 100px;
    }

    .news__scrollbar-thumb {
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

    .news__dots {
      display: none;
    }

    .news__more {
      order: 0;
      flex-shrink: 0;
      color: #000000;
    }

    .news__more-text {
      font-size: 16px;
      line-height: 130%;
    }

    .news__cta {
      display: block;
      width: 100%;
    }

    .news__btn {
      display: flex;
      align-items: center;
      justify-content: center;
      width: 100%;
      height: 85px;
      background: var(--blue);
      box-shadow: 0px 0px 4px rgba(0, 0, 0, 0.3);
      border-radius: 9px;
      font-family: 'Montserrat', sans-serif;
      font-weight: 400;
      font-size: 16px;
      line-height: 130%;
      letter-spacing: -0.01em;
      color: #FFFFFF;
      text-decoration: none;
      transition: background 0.2s ease;
    }

    .news__btn:hover {
      background: var(--dblue);
    }
  }
</style>

<script>
  (function () {
    var newsSlides     = document.querySelectorAll('.news__slide');
    var newsDots        = document.querySelectorAll('.news__dot');
    var newsArrowRight   = document.getElementById('newsArrowRight');
    var newsArrowLeft    = document.getElementById('newsArrowLeft');

    var newsCurrent = 0;
    var newsTotal    = newsSlides.length;

    function newsGoTo(index) {
      newsSlides[newsCurrent].classList.remove('news__slide--active');
      newsDots[newsCurrent].classList.remove('news__dot--active');

      newsCurrent = index;

      newsSlides[newsCurrent].classList.add('news__slide--active');
      newsDots[newsCurrent].classList.add('news__dot--active');

      if (newsArrowLeft)  newsArrowLeft.classList.toggle('is-hidden', newsCurrent === 0);
      if (newsArrowRight) newsArrowRight.classList.toggle('is-hidden', newsCurrent === newsTotal - 1);
    }

    // Инициализация
    if (newsArrowLeft) newsArrowLeft.classList.add('is-hidden');
    if (newsTotal <= 1 && newsArrowRight) newsArrowRight.classList.add('is-hidden');

    if (newsArrowRight) {
      newsArrowRight.addEventListener('click', function () {
        if (newsCurrent < newsTotal - 1) newsGoTo(newsCurrent + 1);
      });
    }

    if (newsArrowLeft) {
      newsArrowLeft.addEventListener('click', function () {
        if (newsCurrent > 0) newsGoTo(newsCurrent - 1);
      });
    }

    newsDots.forEach(function (dot, i) {
      dot.addEventListener('click', function () { newsGoTo(i); });
    });

    // Мобильный скроллбар для News
    function initNewsScrollbar() {
      if (window.innerWidth > 768) return;

      var activeSlide     = document.querySelector('.news__slide--active');
      var scrollbarThumb   = document.getElementById('newsScrollbarThumb');
      var scrollbar        = document.getElementById('newsScrollbar');

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

      // Синхронизация при скролле контента
      activeSlide.addEventListener('scroll', updateThumbPosition);

      // Драг скроллбара
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

      // Клик по треку
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

    initNewsScrollbar();

    window.addEventListener('resize', function () {
      initNewsScrollbar();
    });
  })();
</script>
@endif