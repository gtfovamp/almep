@php
    /**
     * Catalog Popup Partial
     *
     * @var string       $lang
     * @var array        $categories     – коллекция категорий из БД
     * @var array        $subcategories  – коллекция подкатегорий из БД
     */

    $categoriesWithSubs = collect($categories)->map(function ($cat) use ($subcategories) {
        return array_merge((array) $cat, [
            'subs' => collect($subcategories)
                ->filter(fn($s) => (int)($s['category_id'] ?? $s->category_id ?? 0) === (int)($cat['id'] ?? $cat->id ?? 0))
                ->values()
                ->toArray(),
        ]);
    })->values()->toArray();

    $firstCat = $categoriesWithSubs[0] ?? null;

    function catalogLocalName(mixed $item, string $lang, string $field = 'name'): string {
        $item = (array) $item;
        if ($lang === 'az' && !empty($item["{$field}_az"])) return $item["{$field}_az"];
        if ($lang === 'en' && !empty($item["{$field}_en"])) return $item["{$field}_en"];
        return $item[$field] ?? '';
    }
@endphp

<div
    id="catalogPopup"
    class="catalog-popup"
    role="dialog"
    aria-modal="true"
    aria-hidden="true"
    aria-label="{{ __('Каталог') }}"
    style="top: var(--header-height, 80px);"
>
    <div class="catalog-popup__layout">

        {{-- ── Левая колонка: категории ── --}}
        <aside class="catalog-popup__sidebar" aria-label="Категории">
            <nav class="catalog-popup__nav">
                @foreach ($categoriesWithSubs as $index => $cat)
                    @php
                        $catId   = $cat['id']   ?? 0;
                        $catName = catalogLocalName($cat, $lang);
                        $imgUrl  = $cat['image_url'] ?? '';
                    @endphp
                    <button
                        type="button"
                        class="catalog-cat-btn {{ $index === 0 ? 'is-active' : '' }}"
                        data-cat-id="{{ $catId }}"
                        aria-selected="{{ $index === 0 ? 'true' : 'false' }}"
                    >
                        <span class="catalog-cat-btn__text">{{ $catName }}</span>
                    </button>
                @endforeach
            </nav>
        </aside>

        {{-- ── Вертикальный разделитель ── --}}
        <div class="catalog-popup__divider" role="separator" aria-hidden="true"></div>

        {{-- ── Правая колонка: подкатегории ── --}}
        <section class="catalog-popup__content" id="catalogContent" aria-label="Подкатегории">

            {{-- Шапка: заголовок + кнопка закрытия --}}
            <div class="catalog-popup__content-header">
                <h2 class="catalog-popup__title" id="catalogTitle">
                    {{ $firstCat ? catalogLocalName($firstCat, $lang) : '' }}
                </h2>
                <button
                    type="button"
                    id="catalogClose"
                    class="catalog-popup__close"
                    aria-label="Закрыть каталог"
                >
                    <svg width="36" height="36" viewBox="0 0 16 16" fill="none"
                         xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path fill-rule="evenodd" clip-rule="evenodd"
                              d="M4.11 2.697L2.698 4.11 6.586 8l-3.89 3.89
                                 1.415 1.413L8 9.414l3.89 3.89 1.413-1.415L9.414
                                 8l3.89-3.89-1.415-1.413L8 6.586l-3.89-3.89z"
                              fill="#003F8D"/>
                    </svg>
                </button>
            </div>

            {{-- Сетка подкатегорий (рендерится JS, начальное состояние — первая категория) --}}
            <div class="catalog-popup__grid" id="catalogSubsGrid" role="list">
                @if ($firstCat)
                    @foreach ($firstCat['subs'] as $sub)
                        @php
                            $subId   = $sub['id']        ?? 0;
                            $subName = catalogLocalName($sub, $lang);
                            $subImg  = $sub['image_url'] ?? '';
                            $types   = $sub['types']     ?? [];
                        @endphp
                        <div class="catalog-sub-item" role="listitem">
                            <a
                                href="/{{ $lang }}/products?subcategory={{ $subId }}"
                                class="catalog-sub-item__link"
                            >
                                <div class="catalog-sub-item__thumb">
                                    @if ($subImg)
                                        <img src="{{ $subImg }}" alt="{{ $subName }}"
                                             width="120" height="120" loading="lazy"
                                             class="catalog-sub-item__img" />
                                    @endif
                                </div>
                                <span class="catalog-sub-item__name">{{ $subName }}</span>
                            </a>

                            @if (!empty($types))
                                <ul class="catalog-sub-item__types" role="list">
                                    @foreach ($types as $type)
                                        @php
                                            $typeId   = $type['id'] ?? 0;
                                            $typeName = catalogLocalName($type, $lang);
                                        @endphp
                                        <li role="listitem">
                                            <a
                                                href="/{{ $lang }}/products?subcategory={{ $subId }}&type={{ $typeId }}"
                                                class="catalog-sub-item__type-link"
                                            >{{ $typeName }}</a>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    @endforeach
                @endif
            </div>
        </section>
    </div>
</div>

{{-- ─────────────────────────────────────────────────────────── --}}
{{-- JSON-данные для JS (без вывода в разметку напрямую)         --}}
{{-- ─────────────────────────────────────────────────────────── --}}
<script>
(function () {
    'use strict';

    /* ── Данные из PHP → JS ── */
    var CATALOG_DATA = @json($categoriesWithSubs);
    var LANG         = @js($lang);

    /* ── Утилита локализации ── */
    function localName(obj, field) {
        field = field || 'name';
        if (LANG === 'az' && obj[field + '_az']) return obj[field + '_az'];
        if (LANG === 'en' && obj[field + '_en']) return obj[field + '_en'];
        return obj[field] || '';
    }

    /* ── Элементы ── */
    var popup     = document.getElementById('catalogPopup');
    var grid      = document.getElementById('catalogSubsGrid');
    var titleEl   = document.getElementById('catalogTitle');
    var closeBtn  = document.getElementById('catalogClose');
    var catBtns   = document.querySelectorAll('.catalog-cat-btn');

    if (!popup || !grid || !titleEl) return;

    /* ── Рендер подкатегорий ── */
    function renderSubs(subs) {
        if (!subs || !subs.length) {
            grid.innerHTML = '<p class="catalog-popup__empty">Подкатегорий нет</p>';
            return;
        }

        grid.innerHTML = subs.map(function (sub) {
            var subId   = sub.id   || 0;
            var subName = localName(sub);
            var subImg  = sub.image_url || '';
            var types   = Array.isArray(sub.types) ? sub.types : [];

            var thumbHtml = subImg
                ? '<img src="' + escHtml(subImg) + '" alt="' + escHtml(subName) + '" width="120" height="120" loading="lazy" class="catalog-sub-item__img" />'
                : '';

            var typesHtml = types.length
                ? '<ul class="catalog-sub-item__types" role="list">'
                    + types.map(function (t) {
                        var tId   = t.id || 0;
                        var tName = localName(t);
                        return '<li role="listitem">'
                            + '<a href="/' + LANG + '/products?subcategory=' + subId + '&type=' + tId
                            + '" class="catalog-sub-item__type-link">' + escHtml(tName) + '</a>'
                            + '</li>';
                    }).join('')
                    + '</ul>'
                : '';

            return '<div class="catalog-sub-item" role="listitem">'
                + '<a href="/' + LANG + '/products?subcategory=' + subId + '" class="catalog-sub-item__link">'
                + '<div class="catalog-sub-item__thumb">' + thumbHtml + '</div>'
                + '<span class="catalog-sub-item__name">' + escHtml(subName) + '</span>'
                + '</a>'
                + typesHtml
                + '</div>';
        }).join('');
    }

    /* ── Экранирование HTML ── */
    function escHtml(str) {
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    /* ── Активация категории ── */
    function activateCategory(catId) {
        var cat = CATALOG_DATA.find(function (c) { return String(c.id) === String(catId); });
        if (!cat) return;

        catBtns.forEach(function (btn) {
            var active = btn.dataset.catId == catId;
            btn.classList.toggle('is-active', active);
            btn.setAttribute('aria-selected', active ? 'true' : 'false');
        });

        titleEl.textContent = localName(cat);
        renderSubs(cat.subs || []);

        // Scroll content to top
        var content = document.getElementById('catalogContent');
        if (content) content.scrollTop = 0;
    }

    /* ── Привязка кнопок категорий ── */
    catBtns.forEach(function (btn) {
        btn.addEventListener('click', function () {
            activateCategory(btn.dataset.catId);
        });
    });

    /* ── Закрытие ── */
    function closeCatalog() {
        popup.classList.remove('is-open');
        popup.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
        document.querySelectorAll('.js-catalog-trigger').forEach(function (b) {
            b.setAttribute('aria-expanded', 'false');
        });
    }

    closeBtn && closeBtn.addEventListener('click', closeCatalog);

    /* ── Клик по фону (за popup__layout) закрывает ── */
    popup.addEventListener('click', function (e) {
        if (e.target === popup) closeCatalog();
    });

    /* ── Открытие из хедера ── */
    document.addEventListener('catalogOpen', function () {
        // Активируем первую категорию при открытии
        if (CATALOG_DATA.length) {
            activateCategory(CATALOG_DATA[0].id);
        }
    });

    /* ── Инициализация: рендерим первую категорию сразу ── */
    if (CATALOG_DATA.length) {
        renderSubs(CATALOG_DATA[0].subs || []);
    }

    /* ── Expose для хедера ── */
    window.__closeCatalog = closeCatalog;
})();
</script>