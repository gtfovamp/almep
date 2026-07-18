{{--
    ═══════════════════════════════════════════════════════════════
    BLOG SECTION (главная страница) — Almep Trading
    Портировано из Figma-макета ("Блог", 1599×750, тёмный блок с
    фоновым фото и белой карточкой поверх) в Blade, в стиле
    остальных partials проекта (news.blade.php / partners.blade.php).

    Ожидает переменные:
      $t     — массив переводов
      $lang  — текущий язык ('ru' | 'en' | 'az')
      $blog  — Eloquent-модель последней записи блога (одна запись,
               блок на главной показывает один featured-пост)

    $t['blog']['title']       — заголовок секции ("Блог")
    $t['blog']['read_more']   — текст ссылки ("Читать дальше")
    $t['blog']['image_alt']   — alt для фонового изображения

    ─── Как получить $blog в контроллере ───

    $data['blog'] = \App\Models\Blog::query()
        ->orderByDesc('published_at')
        ->first();
    ═══════════════════════════════════════════════════════════════
--}}
@php
    // ─── Поле на нужном языке (title_ru нет в таблице — фоллбэк на title) ───
    $blogField = function ($item, $lang, $base) {
        $en = $base . '_en';
        $az = $base . '_az';
        if ($lang === 'en') return $item->$en ?: $item->$base;
        if ($lang === 'az') return $item->$az ?: $item->$base;
        return $item->$base;
    };

    // ─── Блоки контента (JSON-колонка), как в news.blade.php ───
    $blogBlocks = function ($item) {
        $b = $item->blocks ?? null;
        if (is_string($b)) { $b = json_decode($b, true) ?: []; }
        return is_array($b) ? $b : [];
    };

    $blogBlockText = function (array $block, string $lang): string {
        $content = $block['content'] ?? null;
        if (!is_array($content)) return is_string($content) ? trim($content) : '';
        return trim((string) ($content[$lang] ?? $content['ru'] ?? ''));
    };

    // ─── Вырезка (excerpt) под большую карточку (~320 символов) ───
    $blogExcerpt = function ($blocks, $lang, $max = 320) use ($blogBlockText) {
        $textBlocks = array_filter($blocks, fn($b) => in_array(($b['type'] ?? ''), ['paragraph', 'text']));
        $parts = array_filter(array_map(fn($b) => $blogBlockText($b, $lang), $textBlocks));
        $text = trim(implode(' ', $parts));
        if ($text === '') return '';
        if (mb_strlen($text) <= $max) return $text;
        $cut = mb_substr($text, 0, $max);
        $lastSpace = mb_strrpos($cut, ' ');
        if ($lastSpace !== false) { $cut = mb_substr($cut, 0, $lastSpace); }
        return rtrim($cut, " .,;:") . '…';
    };

    $blogItem = null;
    if (!empty($blog)) {
        $blocks = $blogBlocks($blog);
        $blogItem = [
            'title'   => $blogField($blog, $lang, 'title'),
            'excerpt' => $blogExcerpt($blocks, $lang),
            'image'   => $blog->cover_image_url,
            'link'    => "/{$lang}/blog/{$blog->id}",
        ];
    }
@endphp

@if($blogItem)
<section class="blog">
  <div class="blog__inner">

    <div class="blog__bg" style="background-image: url('/assets/images/blog.png');" role="img" aria-label="{{ $t['blog']['image_alt'] ?? $blogItem['title'] }}"></div>
    <div class="blog__overlay" aria-hidden="true"></div>

    <div class="blog__card">
      <div class="blog__card-inner">

        <h2 class="blog__title">{{ $t['blog']['title'] ?? 'Блог' }}</h2>

        <div class="blog__post">
          <h3 class="blog__post-title">{{ $blogItem['title'] }}</h3>
          <p class="blog__post-excerpt">{{ $blogItem['excerpt'] }}</p>

          <a href="{{ $blogItem['link'] }}" class="blog__post-link">
            <span class="blog__post-link-text">{{ $t['blog']['read_more'] ?? 'Читать дальше' }}</span>
            <span class="blog__post-link-line" aria-hidden="true"></span>
          </a>
        </div>

      </div>
    </div>

  </div>
</section>

<style>
  /* ===== Токены (та же сетка, что и в news.blade.php / partners.blade.php) ===== */
  .blog {
    --blog-px: clamp(16px, 6vw, 115px);

    width: 100%;
    background: #000000;
    box-sizing: border-box;
  }
  .blog *, .blog *::before, .blog *::after { box-sizing: border-box; }

  .blog__inner {
    position: relative;
    width: 100%;
    min-height: clamp(480px, 47vw, 750px);
    overflow: hidden;
    display: flex;
    align-items: center;
  }

  /* ===== Фоновое изображение (справа) ===== */
  .blog__bg {
    position: absolute;
    inset: 0;
    background-size: cover;
    background-position: center right;
    background-repeat: no-repeat;
  }

  /* ===== Белая карточка поверх фото ===== */
  .blog__card {
    position: relative;
    z-index: 2;
    width: 100%;
    padding: 0 var(--blog-px);
  }

  .blog__card-inner {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: clamp(28px, 3.5vw, 50px);
    width: min(932px, 100%);
    padding: clamp(28px, 3.5vw, 55px);
    background: #FFFFFF;
    border-radius: 17px;
  }

  /* ===== Заголовок секции ("Блог") ===== */
  .blog__title {
    margin: 0;
    font-family: 'Montserrat', sans-serif;
    font-weight: 500;
    font-size: clamp(26px, 3.2vw, 48px);
    line-height: 1.1;
    color: #0E0E0E;
  }

  /* ===== Блок поста ===== */
  .blog__post {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: clamp(18px, 1.6vw, 20px);
    width: 100%;
  }

  .blog__post-title {
    margin: 0;
    font-family: 'Montserrat', sans-serif;
    font-weight: 500;
    font-size: clamp(19px, 1.6vw, 24px);
    line-height: 1.1;
    letter-spacing: -0.01em;
    color: #0E0E0E;
  }

  .blog__post-excerpt {
    margin: 0;
    font-family: 'Montserrat', sans-serif;
    font-weight: 400;
    font-size: clamp(15px, 1.2vw, 18px);
    line-height: 1.3;
    letter-spacing: -0.01em;
    color: #0E0E0E;
  }

  /* ===== Ссылка "Читать дальше" (подчёркивание линией, как в макете) ===== */
  .blog__post-link {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: 5px;
    text-decoration: none;
    transition: opacity 0.2s ease;
  }

  .blog__post-link:hover {
    opacity: 0.7;
  }

  .blog__post-link-text {
    font-family: 'Montserrat', sans-serif;
    font-weight: 400;
    font-size: clamp(15px, 1.2vw, 18px);
    line-height: 1.3;
    letter-spacing: -0.01em;
    color: #0E0E0E;
  }

  .blog__post-link-line {
    width: 149px;
    max-width: 100%;
    height: 0;
    border-top: 1px solid #0E0E0E;
  }

  /* === МОБИЛЬНАЯ ВЕРСИЯ === */
  @media (max-width: 768px) {
    .blog__inner {
      min-height: auto;
      padding: 40px 0;
    }

    .blog__bg {
      position: absolute;
      inset: 0;
    }

    .blog__overlay {
      background: linear-gradient(
        180deg,
        rgba(0, 0, 0, 0.55) 0%,
        rgba(0, 0, 0, 0.88) 55%,
        rgba(0, 0, 0, 0.92) 100%
      );
    }

    .blog__card {
      padding: 0 16px;
    }

    .blog__card-inner {
      width: 100%;
      padding: 24px 18px;
      border-radius: 14px;
      gap: 24px;
    }

    .blog__title {
      font-size: 24px;
    }

    .blog__post {
      gap: 16px;
    }

    .blog__post-title {
      font-size: 18px;
      line-height: 1.3;
    }

    .blog__post-excerpt {
      font-size: 16px;
    }

    .blog__post-link-text {
      font-size: 16px;
    }
  }
</style>
@endif