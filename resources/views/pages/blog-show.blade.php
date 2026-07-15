{{--
    Ожидаемые переменные из контроллера:
    - $item      : модель новости (id, title/title_en/title_az, cover_image_url, blocks (json), published_at)
    - $related   : Collection|array из 2-3 других новостей (та же модель), без текущей
    - $t, $lang  : как на остальных страницах

    Пример роута и метода контроллера — в самом низу файла, в комментарии.
--}}
@extends('layouts.app')

@php
    $newsField = function ($item, $lang, $base) {
        $en = $base.'_en'; $az = $base.'_az';
        if ($lang === 'en') return $item->$en ?: $item->$base;
        if ($lang === 'az') return $item->$az ?: $item->$base;
        return $item->$base;
    };

    $newsBlocks = function ($item) {
        $b = $item->blocks ?? null;
        if (is_string($b)) { $b = json_decode($b, true) ?: []; }
        return is_array($b) ? $b : [];
    };

    // Структура блока в БД: ['type' => 'heading'|'text'|'image'|'quote', 'content' => ['ru'=>..,'en'=>..,'az'=>..]]
    $newsBlockText = function (array $block, string $lang): string {
        $content = $block['content'] ?? null;
        if (!is_array($content)) return is_string($content) ? trim($content) : '';
        return trim((string) ($content[$lang] ?? $content['ru'] ?? ''));
    };

    $newsExcerpt = function ($blocks, $lang, $max = 110) use ($newsBlockText) {
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

    $newsReadTime = function ($blocks, $lang) use ($newsBlockText) {
        $all = implode(' ', array_map(fn($b) => $newsBlockText($b, $lang), $blocks));
        $words = preg_split('/\s+/', $all, -1, PREG_SPLIT_NO_EMPTY);
        return max(1, (int) ceil(count($words) / 200));
    };

    $newsMinuteLabel = function (int $mins) use ($t) {
        return $mins === 1
            ? ($t['blog']['minute'] ?? 'минуты')
            : ($t['blog']['minutes'] ?? 'минут');
    };

    $newsDate = function ($val) {
        if (empty($val)) return '';
        try { return \Carbon\Carbon::parse($val)->format('d.m.Y'); } catch (\Throwable $e) { return ''; }
    };

    // Рендер одного блока контента в HTML. Текст экранируем сами (e()),
    // поэтому наружу отдаём уже готовую безопасную строку через {!! !!}.
    $renderBlock = function (array $block, string $lang) use ($newsBlockText) {
        $type = $block['type'] ?? 'paragraph';
        $text = $newsBlockText($block, $lang);

        if ($type === 'heading') {
            return $text !== '' ? '<h2 class="article-body__heading">'.e($text).'</h2>' : '';
        }

        if ($type === 'image') {
            $content = $block['content'] ?? [];
            $src = $block['url'] ?? (is_array($content) ? ($content['url'] ?? null) : null);
            if (!$src) return '';
            $alt = e($text !== '' ? $text : '');
            return '<figure class="article-body__figure"><img src="'.e($src).'" alt="'.$alt.'" loading="lazy" /></figure>';
        }

        if ($type === 'quote') {
            return $text !== '' ? '<blockquote class="article-body__quote">'.e($text).'</blockquote>' : '';
        }

        // paragraph / text / фолбэк
        return $text !== '' ? '<p class="article-body__paragraph">'.e($text).'</p>' : '';
    };

    $itemTitle = $newsField($item, $lang, 'title');
    $itemBlocks = $newsBlocks($item);
    $itemMins = $newsReadTime($itemBlocks, $lang);
    $title = $itemTitle . ' — Almep Trading';

    $related = collect($related ?? [])->values();
@endphp

@section('content')
<main class="site-main">
    <div style="position: relative; background: #FFFFFF;">
        @include('partials.header', ['t' => $t, 'lang' => $lang])
    </div>

    <section class="article-page">
      <div class="article-page__inner">

        <nav class="breadcrumbs" aria-label="breadcrumb">
          <a href="/{{ $lang }}" class="breadcrumbs__item" aria-label="{{ $t['nav']['home'] ?? 'Home' }}">
            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                <path d="M2.5 7.5L10 2.5L17.5 7.5V16.25C17.5 16.5815 17.3683 16.8995 17.1339 17.1339C16.8995 17.3683 16.5815 17.5 16.25 17.5H3.75C3.41848 17.5 3.10054 17.3683 2.86612 17.1339C2.6317 16.8995 2.5 16.5815 2.5 16.25V7.5Z" stroke="#696969" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M7.5 17.5V10H12.5V17.5" stroke="#696969" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </a>
          <svg width="4" height="8" viewBox="0 0 4 8" fill="none" class="breadcrumbs__separator" aria-hidden="true">
              <path d="M1 1L3 4L1 7" stroke="#706F6F" stroke-width="1"/>
          </svg>
          <a href="/{{ $lang }}/blog" class="breadcrumbs__link">{{ $t['blog']['title'] ?? 'Блог' }}</a>
          <svg width="4" height="8" viewBox="0 0 4 8" fill="none" class="breadcrumbs__separator" aria-hidden="true">
              <path d="M1 1L3 4L1 7" stroke="#706F6F" stroke-width="1"/>
          </svg>
          <span class="breadcrumbs__current" aria-current="page">{{ $itemTitle }}</span>
        </nav>

        <div class="article-page__body">

          {{-- ── Колонка статьи ── --}}
          <article class="article-page__main">
            <div class="article-page__head">
              <span class="news-badge">{{ $t['blog']['category_company'] ?? '' }}</span>
              <h1 class="article-page__title">{{ $itemTitle }}</h1>
              <div class="news-meta">
                <span class="news-meta__item"><svg width="24" height="24" viewBox="0 0 28 28" fill="none" aria-hidden="true"><rect x="3" y="6" width="22" height="19" rx="2" stroke="#676767" stroke-width="1.5"/><path d="M3 11H25" stroke="#676767" stroke-width="1.5"/><path d="M8 3V6" stroke="#676767" stroke-width="1.5" stroke-linecap="round"/><path d="M20 3V6" stroke="#676767" stroke-width="1.5" stroke-linecap="round"/></svg>{{ $newsDate($item->published_at) }}</span>
                <span class="news-meta__item"><svg width="24" height="24" viewBox="0 0 29 29" fill="none" aria-hidden="true"><circle cx="14.5" cy="14.5" r="10" stroke="#676767" stroke-width="1.5"/><path d="M14.5 8V14.5L18.5 18.5" stroke="#676767" stroke-width="1.5" stroke-linecap="round"/></svg>{{ $itemMins }} {{ $newsMinuteLabel($itemMins) }}</span>
              </div>
            </div>

            @if($item->cover_image_url)
              <div class="article-page__hero">
                <img src="{{ $item->cover_image_url }}" alt="{{ $itemTitle }}" loading="lazy" width="840" height="380" />
              </div>
            @endif

            <div class="article-body">
              @foreach($itemBlocks as $block)
                {!! $renderBlock($block, $lang) !!}
              @endforeach
            </div>
          </article>

          {{-- ── Разделитель между колонками (виден только на десктопе) ── --}}
          <div class="article-page__divider" aria-hidden="true"></div>

          {{-- ── Сайдбар ── --}}
          <aside class="article-page__sidebar">

            @if($related->isNotEmpty())
              <div class="article-sidebar__block">
                <h2 class="article-sidebar__title">{{ $t['blog']['other_news'] ?? 'Другие новости' }}</h2>
                <div class="side-news-list">
                  @foreach($related as $r)
                    @php
                      $rTitle = $newsField($r, $lang, 'title');
                      $rBlocks = $newsBlocks($r);
                      $rMins = $newsReadTime($rBlocks, $lang);
                    @endphp
                    <article class="side-news-card">
                      <a href="/{{ $lang }}/blog/{{ $r->id }}" class="side-news-card__stretched-link" aria-label="{{ $rTitle }}"></a>
                      <div class="side-news-card__image">
                        <img src="{{ $r->cover_image_url }}" alt="{{ $rTitle }}" loading="lazy" width="450" height="300" />
                      </div>
                      <div class="side-news-card__content">
                        <h3 class="side-news-card__title">{{ $rTitle }}</h3>
                        <p class="side-news-card__excerpt">{{ $newsExcerpt($rBlocks, $lang) }}</p>
                        <div class="news-meta">
                          <span class="news-meta__item"><svg width="18" height="18" viewBox="0 0 28 28" fill="none" aria-hidden="true"><rect x="3" y="6" width="22" height="19" rx="2" stroke="#676767" stroke-width="1.5"/><path d="M3 11H25" stroke="#676767" stroke-width="1.5"/><path d="M8 3V6" stroke="#676767" stroke-width="1.5" stroke-linecap="round"/><path d="M20 3V6" stroke="#676767" stroke-width="1.5" stroke-linecap="round"/></svg>{{ $newsDate($r->published_at) }}</span>
                          <span class="news-meta__item"><svg width="18" height="18" viewBox="0 0 29 29" fill="none" aria-hidden="true"><circle cx="14.5" cy="14.5" r="10" stroke="#676767" stroke-width="1.5"/><path d="M14.5 8V14.5L18.5 18.5" stroke="#676767" stroke-width="1.5" stroke-linecap="round"/></svg>{{ $rMins }} {{ $newsMinuteLabel($rMins) }}</span>
                        </div>
                        <a href="/{{ $lang }}/blog/{{ $r->id }}" class="read-more">
                          <span>{{ $t['blog']['learn_more'] ?? '' }}</span><svg width="22" height="16" viewBox="0 0 27 20" fill="none" aria-hidden="true"><path d="M0.013702 10.394L26.1767 9.68691M26.1767 9.68691L16.8428 0.353097M26.1767 9.68691L16.9401 18.9235" stroke="#1C508F"/></svg>
                        </a>
                      </div>
                    </article>
                  @endforeach
                </div>
              </div>
            @endif

            {{-- Блок подписки — форма без бэкенда, action замени на свой роут --}}
            <div class="article-sidebar__block article-sidebar__subscribe">
              <h2 class="article-sidebar__title article-sidebar__title--center">{{ $t['blog']['subscribe_title'] ?? 'Будьте в курсе новостей' }}</h2>
              <p class="article-sidebar__subscribe-text">{{ $t['blog']['subscribe_text'] ?? 'Подпишитесь на новости и получайте актуальную информацию первыми' }}</p>
              <form class="subscribe-form" action="#" method="POST">
                @csrf
                <input type="email" name="email" required class="subscribe-form__input" placeholder="{{ $t['blog']['email_placeholder'] ?? 'Ваш E-mail' }}" />
                <button type="submit" class="subscribe-form__button">{{ $t['blog']['subscribe_button'] ?? 'Подписаться' }}</button>
              </form>
            </div>

          </aside>
        </div>

      </div>
    </section>

    @include('partials.footer', ['t' => $t, 'lang' => $lang])
</main>
@endsection

@push('styles')
<style>
    .site-main { display: flex; flex-direction: column; min-height: 100vh; overflow-x: clip; }
    .site-main > section { flex: 0 0 auto; }
    .site-main > section:first-of-type { flex: 1 0 auto; }
    .site-main img, .site-main iframe, .site-main video { max-width: 100%; }
    .site-main *, .site-main *::before, .site-main *::after { box-sizing: border-box; }

  /* ── Токены — идентичны news-page, чтобы страница садилась в ту же сетку ── */
  .article-page {
    --accent: #1C508F;
    --badge-bg: #A4C5EE;
    --badge-text: #003F8D;
    --text: #000000;
    --text-muted: #676767;
    --text-secondary: #706F6F;
    --breadcrumb: #2B2B2B;
    --card-shadow: 0px 0px 4px rgba(0, 0, 0, 0.3);
    --card-shadow-hover: 0 10px 28px rgba(28, 80, 143, 0.16);
    --radius-lg: 8px;
    --radius-md: 7px;
    --radius-pill: 17px;

    --side-pad: var(--hdr-px, clamp(16px, 6vw, 115px));
    --v-unit: var(--hdr-py, clamp(12px, 2.9vh, 28px));
    --section-gap: clamp(40px, 0vh, 96px);
    --container-max: 1600px;

    width: 100%;
    background: #FFFFFF;
    padding: calc(var(--v-unit) * 1) var(--side-pad) calc(var(--v-unit) * 3.2);
  }

  .article-page__inner {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    width: 100%;
    max-width: var(--container-max);
    margin: 0 auto;
    gap: var(--section-gap);
  }

  /* Breadcrumbs — те же классы/размеры, что на странице списка */
  .breadcrumbs { display: flex; flex-direction: row; align-items: center; gap: 10px; flex-wrap: wrap; }
  .breadcrumbs__item { display: flex; align-items: center; justify-content: center; width: 20px; height: 20px; border-radius: 4px; transition: opacity 0.2s; }
  .breadcrumbs__item:hover { opacity: 0.7; }
  .breadcrumbs__separator { flex-shrink: 0; }
  .breadcrumbs__link { font-family: 'Montserrat', sans-serif; font-weight: 400; font-size: 13px; line-height: 16px; color: var(--text-secondary); text-decoration: none; transition: opacity 0.2s; }
  .breadcrumbs__link:hover { opacity: 0.7; }
  .breadcrumbs__current {
    font-family: 'Montserrat', sans-serif; font-weight: 400; font-size: 13px; line-height: 16px; color: var(--breadcrumb);
    max-width: 50vw; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
  }

  /* Бейдж и мета — идентичны списку новостей */
  .news-badge {
    display: inline-flex; align-items: center; justify-content: center; padding: 10px 18px;
    background: var(--badge-bg); border-radius: var(--radius-pill);
    font-family: 'Raleway', sans-serif; font-weight: 400; font-size: 14px; line-height: 110%;
    color: var(--badge-text); white-space: nowrap;
  }
  .news-meta { display: flex; flex-direction: row; align-items: center; flex-wrap: wrap; gap: 10px 20px; }
  .news-meta__item { display: inline-flex; align-items: center; gap: 8px; font-family: 'Raleway', sans-serif; font-weight: 400; font-size: 14px; line-height: 110%; color: var(--text-muted); white-space: nowrap; }
  .news-meta__item svg { flex-shrink: 0; }

  .read-more { display: inline-flex; flex-direction: row; align-items: center; gap: 11px; text-decoration: none; transition: opacity 0.2s, gap 0.2s; border-radius: 4px; }
  .read-more:hover { opacity: 0.75; gap: 15px; }
  .read-more span { font-family: 'Montserrat', sans-serif; font-weight: 500; font-size: clamp(15px, 1.4vw, 18px); line-height: 110%; color: var(--accent); }
  .read-more svg { flex-shrink: 0; }

  /* ── Двухколоночное тело страницы ── */
  .article-page__body {
    display: flex;
    flex-direction: row;
    align-items: flex-start;
    gap: clamp(30px, 4vw, 90px);
    width: 100%;
  }

  .article-page__main {
    flex: 1 1 640px;
    min-width: 0;
    display: flex;
    flex-direction: column;
    gap: clamp(28px, 4vw, 50px);
  }

  .article-page__divider {
    align-self: stretch;
    width: 1px;
    background: #E3E3E3;
    flex: 0 0 1px;
  }

  .article-page__sidebar {
    flex: 0 0 380px;
    width: 380px;
    max-width: 100%;
    min-width: 0;
    display: flex;
    flex-direction: column;
    gap: clamp(36px, 4vw, 60px);
  }

  /* ── Заголовок статьи ── */
  .article-page__head {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: clamp(16px, 2vw, 25px);
    width: 100%;
  }

  .article-page__title {
    font-family: 'Montserrat', sans-serif;
    font-weight: 500;
    font-size: clamp(26px, 3.2vw, 40px);
    line-height: 115%;
    letter-spacing: -0.01em;
    color: var(--text);
    margin: 0;
  }

  .article-page__hero {
    width: 100%;
    aspect-ratio: 840 / 380;
    background: #D8D8D8;
    border-radius: var(--radius-lg);
    overflow: hidden;
  }

  .article-page__hero img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
  }

  /* ── Тело статьи (рендер блоков) ── */
  .article-body {
    display: flex;
    flex-direction: column;
    gap: clamp(24px, 3vw, 32px);
    width: 100%;
  }

  .article-body__paragraph {
    font-family: 'Montserrat', sans-serif;
    font-weight: 400;
    font-size: clamp(15px, 1.4vw, 18px);
    line-height: 130%;
    letter-spacing: -0.01em;
    color: var(--text);
    margin: 0;
  }

  .article-body__heading {
    font-family: 'Montserrat', sans-serif;
    font-weight: 500;
    font-size: clamp(20px, 2.2vw, 26px);
    line-height: 120%;
    letter-spacing: -0.01em;
    color: var(--text);
    margin: 0;
  }

  .article-body__figure {
    margin: 0;
    width: 100%;
    border-radius: var(--radius-lg);
    overflow: hidden;
    background: #E9E9E9;
  }

  .article-body__figure img {
    width: 100%;
    height: auto;
    display: block;
  }

  .article-body__quote {
    margin: 0;
    padding: 20px 26px;
    border-left: 3px solid var(--accent);
    background: #F5F8FC;
    border-radius: 0 var(--radius-md) var(--radius-md) 0;
    font-family: 'Montserrat', sans-serif;
    font-style: italic;
    font-weight: 400;
    font-size: clamp(15px, 1.4vw, 18px);
    line-height: 130%;
    color: var(--text);
  }

  /* ── Сайдбар: "Другие новости" ── */
  .article-sidebar__block {
    display: flex;
    flex-direction: column;
    gap: clamp(24px, 3vw, 30px);
    width: 100%;
  }

  .article-sidebar__title {
    font-family: 'Montserrat', sans-serif;
    font-weight: 500;
    font-size: clamp(22px, 2.4vw, 26px);
    line-height: 110%;
    color: var(--text);
    margin: 0;
  }

  .article-sidebar__title--center { text-align: center; width: 100%; }

  .side-news-list {
    display: flex;
    flex-direction: column;
    gap: clamp(20px, 2.5vw, 26px);
    width: 100%;
  }

  .side-news-card {
    position: relative;
    background: #FFFFFF;
    box-shadow: var(--card-shadow);
    border-radius: var(--radius-md);
    overflow: hidden;
    transition: transform 0.25s ease, box-shadow 0.25s ease;
  }

  .side-news-card:hover {
    transform: translateY(-3px);
    box-shadow: var(--card-shadow-hover);
  }

  .side-news-card__stretched-link { position: absolute; inset: 0; z-index: 1; }

  .side-news-card__image {
    position: relative;
    z-index: 2;
    pointer-events: none;
    width: 100%;
    aspect-ratio: 450 / 260;
    background: #D8D8D8;
    overflow: hidden;
  }

  .side-news-card__image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
    transition: transform 0.35s ease;
  }

  .side-news-card:hover .side-news-card__image img { transform: scale(1.05); }

  .side-news-card__content {
    position: relative;
    z-index: 2;
    pointer-events: none;
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: 10px;
    padding: clamp(16px, 2vw, 20px) clamp(16px, 2vw, 22px);
  }

  .side-news-card__content .read-more { pointer-events: auto; margin-top: 4px; }

  .side-news-card__title {
    font-family: 'Montserrat', sans-serif;
    font-weight: 500;
    font-size: clamp(16px, 1.6vw, 18px);
    line-height: 130%;
    letter-spacing: -0.01em;
    color: var(--text);
    margin: 0;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
  }

  .side-news-card__excerpt {
    font-family: 'Montserrat', sans-serif;
    font-weight: 400;
    font-size: clamp(13px, 1.2vw, 14px);
    line-height: 130%;
    letter-spacing: -0.01em;
    color: var(--text-muted);
    margin: 0;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
  }

  /* ── Блок подписки ── */
  .article-sidebar__subscribe {
    align-items: center;
    text-align: center;
    padding-top: clamp(24px, 3vw, 36px);
    border-top: 1px solid #E3E3E3;
  }

  .article-sidebar__subscribe-text {
    font-family: 'Montserrat', sans-serif;
    font-weight: 400;
    font-size: clamp(14px, 1.4vw, 16px);
    line-height: 130%;
    letter-spacing: -0.01em;
    color: var(--text);
    margin: 0;
  }

  .subscribe-form {
    display: flex;
    flex-direction: column;
    gap: 16px;
    width: 100%;
  }

  .subscribe-form__input {
    width: 100%;
    height: 56px;
    padding: 0 20px;
    border: 1px solid #000000;
    border-radius: 4px;
    font-family: 'Montserrat', sans-serif;
    font-weight: 400;
    font-size: 16px;
    color: var(--text);
    background: #fff;
  }

  .subscribe-form__input::placeholder { color: #8A8A8A; }

  .subscribe-form__input:focus-visible {
    outline: 2px solid var(--accent);
    outline-offset: 2px;
  }

  .subscribe-form__button {
    width: 100%;
    height: 56px;
    border: none;
    border-radius: 9px;
    background: var(--accent);
    box-shadow: var(--card-shadow);
    color: #FFFFFF;
    font-family: 'Montserrat', sans-serif;
    font-weight: 400;
    font-size: 16px;
    cursor: pointer;
    transition: opacity 0.2s;
  }

  .subscribe-form__button:hover { opacity: 0.9; }
  .subscribe-form__button:focus-visible { outline: 2px solid var(--accent); outline-offset: 3px; }

  /* Клавиатурная доступность */
  .breadcrumbs__item:focus-visible,
  .read-more:focus-visible,
  .side-news-card__stretched-link:focus-visible {
    outline: 2px solid var(--accent);
    outline-offset: 3px;
  }

  @media (prefers-reduced-motion: reduce) {
    .side-news-card, .side-news-card__image img { transition: none !important; }
    .side-news-card:hover { transform: none; }
  }

  /* ── Точки перелома — те же, что в списке новостей, для единой сетки ── */
  @media (max-width: 1024px) {
    .article-page__body { flex-direction: column; }
    .article-page__divider { display: none; }
    .article-page__sidebar { width: 100%; flex-basis: auto; }
  }

  @media (max-width: 480px) {
    .news-badge { font-size: 13px; padding: 8px 14px; }
    .news-meta__item { font-size: 13px; }
    .breadcrumbs__current { max-width: 65vw; }
    .subscribe-form__input,
    .subscribe-form__button { height: 50px; font-size: 15px; }
  }
</style>
@endpush

{{--
    Пример роута (routes/web.php):

    Route::get('/{lang}/news/{id}', [SiteController::class, 'show'])
        ->where('lang', 'ru|en|az')
        ->name('news.show');

    Пример метода контроллера:

    public function show(string $lang, int $id)
    {
        $item = News::findOrFail($id);
        $related = News::where('id', '!=', $id)
            ->latest('published_at')
            ->limit(3)
            ->get();

        return view('news.show', [
            'item' => $item,
            'related' => $related,
            't' => app(SiteI18n::class)->for($lang),
            'lang' => $lang,
        ]);
    }
--}}