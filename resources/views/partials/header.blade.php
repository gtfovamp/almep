@php
    $catalogData     = $catalogData ?? ['categories' => [], 'subcategories' => []];
    $currentPath     = '/' . ltrim(request()->path(), '/');
    $pathWithoutLang = preg_replace('#^/(ru|en|az)(?=/|$)#', '', $currentPath);
    $isHomePage      = ($pathWithoutLang === '' || $pathWithoutLang === '/');
    $navLinks = [
        ['label' => $t['nav']['home']      ?? '', 'href' => "/{$lang}",            'active' => $isHomePage],
        ['label' => $t['nav']['about']     ?? '', 'href' => "/{$lang}/about",      'dropdown' => true,
         'active' => str_contains($pathWithoutLang, '/about')
                  || str_contains($pathWithoutLang, '/structure')
                  || str_contains($pathWithoutLang, '/certificates')
                  || str_contains($pathWithoutLang, '/blog')],
        ['label' => $t['nav']['services']  ?? '', 'href' => "/{$lang}/services",   'active' => str_contains($pathWithoutLang, '/services')],
        ['label' => $t['nav']['products']  ?? '', 'href' => "/{$lang}/products",   'active' => str_contains($pathWithoutLang, '/products')],
        ['label' => $t['nav']['partners']  ?? '', 'href' => "/{$lang}/partners",   'active' => str_contains($pathWithoutLang, '/partners')],
        ['label' => $t['nav']['portfolio'] ?? '', 'href' => "/{$lang}/portfolio",  'active' => str_contains($pathWithoutLang, '/portfolio')],
        ['label' => $t['nav']['news']      ?? '', 'href' => "/{$lang}/news",       'active' => str_contains($pathWithoutLang, '/news')],
        ['label' => $t['nav']['reviews']   ?? '', 'href' => "/{$lang}/reviews",    'active' => str_contains($pathWithoutLang, '/reviews')],
        ['label' => $t['nav']['contacts']  ?? '', 'href' => "/{$lang}/contacts",   'active' => str_contains($pathWithoutLang, '/contacts')],
    ];
    $aboutSubLinks = [
        ['label' => $t['nav']['about_short']  ?? 'О нас',
         'href'  => "/{$lang}/about",
         'active' => str_contains($pathWithoutLang, '/about')
                  && !str_contains($pathWithoutLang, '/structure')
                  && !str_contains($pathWithoutLang, '/certificates')
                  && !str_contains($pathWithoutLang, '/blog')],
        ['label' => $t['nav']['structure']    ?? 'Структура компании',
         'href'  => "/{$lang}/structure",
         'active' => str_contains($pathWithoutLang, '/structure')],
        ['label' => $t['nav']['certificates'] ?? 'Сертификаты и лицензии',
         'href'  => "/{$lang}/certificates",
         'active' => str_contains($pathWithoutLang, '/certificates')],
        ['label' => $t['nav']['blog']         ?? 'Блог',
         'href'  => "/{$lang}/blog",
         'active' => str_contains($pathWithoutLang, '/blog')],
    ];
    $langLabels       = ['ru' => 'RU', 'en' => 'EN', 'az' => 'AZ'];
    $currentLangLabel = $langLabels[$lang] ?? strtoupper($lang);
    $otherLangs       = collect([
        ['code' => 'ru', 'label' => 'RU'],
        ['code' => 'en', 'label' => 'EN'],
        ['code' => 'az', 'label' => 'AZ'],
    ])->filter(fn($l) => $l['code'] !== $lang)->values();
    $phoneRaw   = $t['header']['phone'] ?? '';
    $phoneClean = preg_replace_callback('/^(\+)?(.*)$/', fn($m) =>
        ($m[1] ?? '') . preg_replace('/\D/', '', $m[2]), $phoneRaw);
    $socials = [
        'instagram' => $t['social']['instagram'] ?? 'https://instagram.com',
        'youtube'   => $t['social']['youtube']   ?? 'https://youtube.com',
        'facebook'  => $t['social']['facebook']  ?? 'https://facebook.com',
    ];
@endphp

<header
    class="hdr {{ $isHomePage ? 'hdr--home' : 'hdr--dark' }}"
    data-lang="{{ $lang }}"
    data-path="{{ $pathWithoutLang }}"
>
    {{-- ╔══════════════════════════════════════════════════════╗ --}}
    {{-- ║  ROW 1 — Logo · Catalog · Search · Lang · Tel · Burger ║ --}}
    {{-- ╚══════════════════════════════════════════════════════╝ --}}
    <div class="hdr__top">

        {{-- ── LEFT: Logo + Catalog ── --}}
        <div class="hdr__left">
            <a href="/{{ $lang }}" class="hdr__logo-link" aria-label="Almep Trading">
                <img
                    id="headerLogo"
                    src="{{ $isHomePage ? asset('assets/icons/logo.svg') : asset('assets/icons/logo-white.svg') }}"
                    alt="Almep Trading"
                    width="162" height="56"
                    class="hdr__logo"
                />
            </a>

            <button
                type="button"
                class="js-catalog-trigger hdr__catalog-btn"
                aria-haspopup="dialog"
                aria-expanded="false"
                aria-controls="catalogPopup"
            >
                <svg class="hdr__catalog-icon" width="18" height="14" viewBox="0 0 18 14" fill="none" aria-hidden="true">
                    <path d="M1 1h16M1 7h16M1 13h16" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                </svg>
                <span class="hdr__catalog-text">{{ $t['header']['catalog'] ?? 'Каталог' }}</span>
            </button>
        </div>

        {{-- ── RIGHT: Search · Lang · Phone · Burger ── --}}
        <div class="hdr__controls">

            {{-- Search icon → dropdown --}}
            <div class="hdr__search-wrap" id="headerSearchWrap"
                 data-lang="{{ $lang }}"
                 data-placeholder="{{ asset('assets/placeholder.png') }}">
                <button
                    type="button"
                    id="searchIconBtn"
                    class="hdr__search-icon-btn"
                    aria-label="{{ $t['header']['search_placeholder'] ?? 'Поиск' }}"
                    aria-expanded="false"
                    aria-controls="searchDropdown"
                >
                    <img src="{{ asset('assets/icons/search.svg') }}" alt="" width="25" height="25" class="hdr__icon" />
                </button>

                <div
                    id="searchDropdown"
                    class="hdr__search-dropdown"
                    role="dialog"
                    aria-hidden="true"
                    aria-label="{{ $t['header']['search_placeholder'] ?? 'Поиск' }}"
                >
                    <div class="hdr__search-row">
                        <input
                            type="search"
                            id="searchInput"
                            class="hdr__search-input"
                            placeholder="{{ $t['header']['search_placeholder'] ?? 'Найти товар, артикул или категорию' }}"
                            autocomplete="off"
                            aria-label="{{ $t['header']['search_placeholder'] ?? 'Поиск' }}"
                            aria-autocomplete="list"
                            aria-controls="searchResults"
                            aria-expanded="false"
                        />
                        <button
                            type="button"
                            id="searchSubmitBtn"
                            class="hdr__search-submit"
                            aria-label="Найти"
                        >
                            <img src="{{ asset('assets/icons/search.svg') }}" alt="" width="25" height="25" />
                        </button>
                    </div>
                    <div class="hdr__search-divider"></div>
                    <div
                        id="searchResults"
                        class="hdr__search-results"
                        role="listbox"
                        aria-live="polite"
                        aria-hidden="true"
                    ></div>
                </div>
            </div>

            {{-- Language --}}
            <div class="hdr__lang" id="langSwitcher">
                <button
                    type="button"
                    id="langSwitcherBtn"
                    class="hdr__lang-btn"
                    aria-haspopup="listbox"
                    aria-expanded="false"
                    aria-controls="langDropdown"
                    aria-label="Язык: {{ $currentLangLabel }}"
                >
                    <img src="{{ asset('assets/icons/globe.svg') }}" alt="" width="28" height="28" class="hdr__icon" />
                    <span class="hdr__lang-label">{{ $currentLangLabel }}</span>
                    <svg class="hdr__lang-arrow" width="15" height="8" viewBox="0 0 15 8"
                         fill="none" aria-hidden="true">
                        <path d="M1 1L7.5 7L14 1" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                    </svg>
                </button>

                <div id="langDropdown" class="hdr__lang-dropdown"
                     role="listbox" aria-hidden="true">
                    {{-- current lang header inside dropdown --}}
                    <div class="hdr__lang-current">
                        <img src="{{ asset('assets/icons/globe.svg') }}" alt="" width="28" height="28" />
                        <span>{{ $currentLangLabel }}</span>
                    </div>
                    <div class="hdr__lang-divider"></div>
                    <ul class="hdr__lang-list" role="presentation">
                        @foreach ($otherLangs as $l)
                            <li role="option">
                                <a href="/{{ $l['code'] }}{{ $pathWithoutLang ?: '/' }}"
                                   class="hdr__lang-option"
                                   hreflang="{{ $l['code'] }}"
                                   data-lang="{{ $l['code'] }}">
                                    {{ $l['label'] }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            {{-- Phone --}}
            @if ($phoneRaw)
                <a href="tel:{{ $phoneClean }}" class="hdr__phone"
                   aria-label="Телефон: {{ $phoneRaw }}">
                    {{ $phoneRaw }}
                </a>
            @endif

            {{-- Burger --}}
            <button
                type="button"
                id="burgerBtn"
                class="hdr__burger"
                aria-label="{{ $t['header']['menu'] ?? 'Меню' }}"
                aria-expanded="false"
                aria-controls="mobileMenu"
            >
                <span class="hdr__burger-box" aria-hidden="true">
                    <span class="hdr__burger-line"></span>
                    <span class="hdr__burger-line"></span>
                    <span class="hdr__burger-line"></span>
                </span>
            </button>
        </div>
    </div>

    {{-- ╔══════════════════════════════════════════════════════╗ --}}
    {{-- ║  ROW 2 — Nav + Socials (desktop only)                  ║ --}}
    {{-- ╚══════════════════════════════════════════════════════╝ --}}
    <div class="hdr__bottom">
        @include('partials.nav', ['links' => $navLinks, 'lang' => $lang, 't' => $t])

        <div class="hdr__socials">
            <a href="{{ $socials['instagram'] }}" class="hdr__social"
               target="_blank" rel="noopener noreferrer" aria-label="Instagram">
                <img src="{{ asset('assets/icons/instagram.svg') }}" alt="Instagram"
                     width="30" height="30" loading="lazy" class="hdr__icon" />
            </a>
            <a href="{{ $socials['youtube'] }}" class="hdr__social"
               target="_blank" rel="noopener noreferrer" aria-label="YouTube">
                <img src="{{ asset('assets/icons/youtube.svg') }}" alt="YouTube"
                     width="30" height="30" loading="lazy" class="hdr__icon" />
            </a>
            <a href="{{ $socials['facebook'] }}" class="hdr__social"
               target="_blank" rel="noopener noreferrer" aria-label="Facebook">
                <img src="{{ asset('assets/icons/facebook.svg') }}" alt="Facebook"
                     width="32" height="32" loading="lazy" class="hdr__icon" />
            </a>
        </div>
    </div>

    {{-- ╔══════════════════════════════════════════════════════╗ --}}
    {{-- ║  MOBILE MENU                                          ║ --}}
    {{-- ╚══════════════════════════════════════════════════════╝ --}}
    <div id="mobileMenuBackdrop" class="mob-backdrop" aria-hidden="true"></div>
    <nav id="mobileMenu" class="mob-menu" role="dialog"
         aria-modal="true" aria-hidden="true" aria-label="Меню">
        <div class="mob-menu__head">
            <a href="/{{ $lang }}" aria-label="Almep Trading">
                <img src="{{ asset('assets/icons/logo-white.svg') }}" alt="Almep Trading"
                     width="130" height="45" class="mob-menu__logo" />
            </a>
            <button type="button" id="mobileMenuClose" class="mob-menu__close"
                    aria-label="{{ $t['header']['close'] ?? 'Закрыть' }}">
                <svg width="18" height="18" viewBox="0 0 18 18" fill="none" aria-hidden="true">
                    <path d="M1 1l16 16M17 1L1 17" stroke="#2B2B2B"
                          stroke-width="1.8" stroke-linecap="round"/>
                </svg>
            </button>
        </div>
        <div class="mob-menu__body">
            {{-- Mobile search --}}
            <div class="mob-menu__search-wrap">
                <input type="search" id="mobileSearchInput"
                       class="mob-menu__search-input"
                       placeholder="{{ $t['header']['search_placeholder'] ?? 'Поиск…' }}"
                       autocomplete="off" />
                <img src="{{ asset('assets/icons/search.svg') }}" alt="" width="20" height="20"
                     class="mob-menu__search-icon" aria-hidden="true" />
            </div>

            <ul class="mob-menu__list" role="list">
                @foreach ($navLinks as $link)
                    @php $isActive = $link['active'] ?? false; @endphp
                    @if (!empty($link['dropdown']))
                        <li role="listitem">
                            <details class="mob-menu__details"
                                @if($isActive || collect($aboutSubLinks)->contains('active', true)) open @endif>
                                <summary class="mob-menu__link mob-menu__summary {{ $isActive ? 'is-active' : '' }}">
                                    {{ $link['label'] }}
                                    <svg class="mob-menu__chevron" width="12" height="7"
                                         viewBox="0 0 12 7" fill="none" aria-hidden="true">
                                        <path d="M1 1l5 5 5-5" stroke="currentColor"
                                              stroke-width="1.6" stroke-linecap="round"/>
                                    </svg>
                                </summary>
                                <ul class="mob-menu__sublist" role="list">
                                    @foreach ($aboutSubLinks as $sub)
                                        <li role="listitem">
                                            <a href="{{ $sub['href'] }}"
                                               class="mob-menu__sublink {{ $sub['active'] ? 'is-active' : '' }}"
                                               @if($sub['active']) aria-current="page" @endif>
                                                {{ $sub['label'] }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </details>
                        </li>
                    @else
                        <li role="listitem">
                            <a href="{{ $link['href'] }}"
                               class="mob-menu__link {{ $isActive ? 'is-active' : '' }}"
                               @if($isActive) aria-current="page" @endif>
                                {{ $link['label'] }}
                            </a>
                        </li>
                    @endif
                @endforeach
            </ul>

            <button type="button"
                    class="js-catalog-trigger mob-menu__catalog-btn"
                    aria-haspopup="dialog" aria-controls="catalogPopup">
                {{ $t['header']['catalog'] ?? 'Каталог' }}
            </button>

            <hr class="mob-menu__hr" />

            @if ($phoneRaw)
                <a href="tel:{{ $phoneClean }}" class="mob-menu__phone">{{ $phoneRaw }}</a>
            @endif

            <div class="mob-menu__langs" role="group" aria-label="Язык">
                <span class="mob-menu__lang is-active" aria-current="location">{{ $currentLangLabel }}</span>
                @foreach ($otherLangs as $l)
                    <a href="/{{ $l['code'] }}{{ $pathWithoutLang ?: '/' }}"
                       class="mob-menu__lang" hreflang="{{ $l['code'] }}">
                        {{ $l['label'] }}
                    </a>
                @endforeach
            </div>

            <div class="mob-menu__socials" role="list" aria-label="Социальные сети">
                <a href="{{ $socials['instagram'] }}" class="mob-menu__social"
                   target="_blank" rel="noopener noreferrer" aria-label="Instagram" role="listitem">
                    <img src="{{ asset('assets/icons/instagram.svg') }}" alt="Instagram" width="26" height="26"/>
                </a>
                <a href="{{ $socials['youtube'] }}" class="mob-menu__social"
                   target="_blank" rel="noopener noreferrer" aria-label="YouTube" role="listitem">
                    <img src="{{ asset('assets/icons/youtube.svg') }}" alt="YouTube" width="26" height="26"/>
                </a>
                <a href="{{ $socials['facebook'] }}" class="mob-menu__social"
                   target="_blank" rel="noopener noreferrer" aria-label="Facebook" role="listitem">
                    <img src="{{ asset('assets/icons/facebook.svg') }}" alt="Facebook" width="26" height="26"/>
                </a>
            </div>
        </div>
    </nav>

    {{-- ╔══════════════════════════════════════════════════════╗ --}}
    {{-- ║  CATALOG POPUP  (логика из файла 1)                   ║ --}}
    {{-- ╚══════════════════════════════════════════════════════╝ --}}
    <div id="catalogPopup" class="cat-popup"
         role="dialog" aria-modal="true" aria-hidden="true"
         aria-label="{{ $t['header']['catalog'] ?? 'Каталог' }}">
        <div class="cat-popup__shell">
            <aside class="cat-popup__sidebar" id="catSidebar"></aside>
            <div class="cat-popup__divider"></div>
            <section class="cat-popup__content" id="catContent">
                <div class="cat-popup__content-head">
                    <h2 class="cat-popup__title" id="catTitle"></h2>
                    <button type="button" id="catalogClose"
                            class="cat-popup__close" aria-label="Закрыть каталог">
                        <svg width="20" height="20" viewBox="0 0 16 16"
                             fill="none" aria-hidden="true">
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                  d="M4.11 2.697L2.698 4.11 6.586 8l-3.89
                                     3.89 1.415 1.413L8 9.414l3.89 3.89
                                     1.413-1.415L9.414 8l3.89-3.89-1.415
                                     -1.413L8 6.586l-3.89-3.89z"
                                  fill="#003F8D"/>
                        </svg>
                    </button>
                </div>
                <div class="cat-popup__grid" id="catGrid"></div>
            </section>
        </div>
    </div>
</header>

{{-- ═══════════════════════════════════════════════════════════ --}}
{{--  CSS                                                        --}}
{{-- ═══════════════════════════════════════════════════════════ --}}
<style>
/* ─── Design tokens ─────────────────────────────────────────── */
:root {
    --blue:   #1C508F;
    --dblue:  #003F8D;
    --text:   #1A1A1A;
    --gray:   #636363;
    --line:   #ECECEC;
    --tr:     .18s ease;
    --hdr-px: clamp(16px, 6vw, 115px);
    --hdr-py: clamp(12px, 2.9vh, 28px);
    --header-height: 76px;
}

/* ─── Header shell ──────────────────────────────────────────── */
.hdr {
    position: absolute;
    top: 0; left: 0; right: 0;
    z-index: 300;
    max-width: 1920px;
    margin: 0 auto;
    padding: var(--hdr-py) var(--hdr-px);
    display: flex;
    flex-direction: column;
    gap: clamp(10px, 1.6vh, 20px);
}
.hdr--dark {
    position: relative;
    background: #fff;
    border-bottom: 1px solid var(--line);
}
.hdr--home {
    background: rgba(10, 20, 35, .55);
}
@supports (backdrop-filter: blur(1px)) or (-webkit-backdrop-filter: blur(1px)) {
    .hdr--home {
        background: rgba(10, 20, 35, .38);
        -webkit-backdrop-filter: blur(10px) saturate(140%);
        backdrop-filter: blur(10px) saturate(140%);
    }
}

/* ─── Top row ───────────────────────────────────────────────── */
.hdr__top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: clamp(10px, 1.8vw, 24px);
    width: 100%;
}

/* Left group: logo + catalog button */
.hdr__left {
    display: flex;
    align-items: center;
    gap: clamp(12px, 1.8vw, 31px);
    flex-shrink: 0;
}

/* Logo */
.hdr__logo-link { display: flex; flex-shrink: 0; }
.hdr__logo {
    width: clamp(100px, 9vw, 162px);
    height: auto;
    object-fit: contain;
    display: block;
}

/* Catalog button — hidden on mobile, visible sm+ */
.hdr__catalog-btn {
    display: none;
    align-items: center;
    justify-content: center;
    gap: 8px;
    flex-shrink: 0;
    width: 150px;
    height: 46px;
    background: var(--blue);
    color: #fff;
    border: none;
    border-radius: 3px;
    font-family: 'Montserrat', sans-serif;
    font-size: 15px;
    font-weight: 500;
    cursor: pointer;
    white-space: nowrap;
    transition: background var(--tr);
}
.hdr__catalog-btn:hover  { background: #174480; }
.hdr__catalog-btn:active { background: #123761; }
@media (min-width: 640px) {
    .hdr__catalog-btn { display: flex; }
}
@media (min-width: 1024px) {
    .hdr__catalog-btn { width: 164px; height: 50px; font-size: 16px; }
}

/* ─── Right controls ─────────────────────────────────────────── */
.hdr__controls {
    display: flex;
    align-items: center;
    gap: clamp(10px, 1.4vw, 20px);
    flex-shrink: 0;
}

/* Shared icon style */
.hdr__icon {
    display: block;
    object-fit: contain;
    pointer-events: none;
    /* white on home, dark on dark handled below */
    filter: brightness(0) invert(1);
}
.hdr--dark .hdr__icon { filter: brightness(0) invert(0); }

/* ─── Search (icon → dropdown) ──────────────────────────────── */
.hdr__search-wrap { position: relative; }

.hdr__search-icon-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 28px; height: 28px;
    background: transparent;
    border: none;
    cursor: pointer;
    padding: 0;
    flex-shrink: 0;
    transition: opacity var(--tr);
}
.hdr__search-icon-btn:hover { opacity: .75; }

/* Dropdown panel */
.hdr__search-dropdown {
    position: absolute;
    top: calc(100% + 12px);
    right: 0;
    width: min(570px, calc(100vw - 2rem));
    background: #fff;
    border: 1px solid #000;
    box-shadow: 0 8px 24px rgba(0,0,0,.14);
    opacity: 0;
    visibility: hidden;
    transform: translateY(-6px);
    transition: opacity var(--tr), visibility var(--tr), transform var(--tr);
    z-index: 500;
    display: flex;
    flex-direction: column;
}
.hdr__search-dropdown.is-open {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
}

/* Input row inside dropdown */
.hdr__search-row {
    display: flex;
    align-items: center;
    height: 49px;
    padding: 0 16px;
    gap: 10px;
    flex-shrink: 0;
    box-sizing: border-box;
}
.hdr__search-input {
    flex: 1;
    min-width: 0;
    height: 100%;
    border: none;
    outline: none;
    background: transparent;
    font-family: 'Raleway', sans-serif;
    font-size: 15px;
    font-weight: 400;
    color: var(--text);
    -webkit-appearance: none;
    appearance: none;
}
.hdr__search-input::placeholder { color: var(--gray); }
.hdr__search-input::-webkit-search-decoration,
.hdr__search-input::-webkit-search-cancel-button { -webkit-appearance: none; }

.hdr__search-submit {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 28px; height: 28px;
    flex-shrink: 0;
    background: transparent;
    border: none;
    cursor: pointer;
    padding: 0;
    transition: opacity var(--tr);
}
.hdr__search-submit:hover { opacity: .7; }
.hdr__search-submit img { display: block; pointer-events: none; }

/* Thin divider inside dropdown */
.hdr__search-divider {
    width: calc(100% - 44px);
    height: 1px;
    background: #000;
    margin: 0 22px;
    flex-shrink: 0;
}

/* Results list */
.hdr__search-results {
    display: flex;
    flex-direction: column;
    gap: 0;
    padding: 8px 0;
    max-height: 400px;
    overflow-y: auto;
}

/* Result item */
.hdr__result-item {
    display: flex;
    align-items: center;
    gap: 19px;
    padding: 10px 20px;
    text-decoration: none;
    border-bottom: 1px solid #F0F0F0;
    transition: background var(--tr);
}
.hdr__result-item:last-child { border-bottom: none; }
.hdr__result-item:hover { background: #F5F8FF; }
.hdr__result-thumb {
    width: 49px; height: 41px;
    background: #D9D9D9;
    flex-shrink: 0;
    object-fit: cover;
}
.hdr__result-title {
    font-family: 'Montserrat', sans-serif;
    font-size: 16px;
    font-weight: 400;
    color: var(--text);
    line-height: 1.3;
    flex: 1;
}
.hdr__result-msg {
    padding: 20px;
    text-align: center;
    font-family: 'Montserrat', sans-serif;
    font-size: 14px;
    color: var(--gray);
}
.hdr__result-msg--error { color: #DC2626; }

/* ─── Language switcher ──────────────────────────────────────── */
.hdr__lang { position: relative; }

.hdr__lang-btn {
    display: flex;
    align-items: center;
    gap: 8px;
    background: transparent;
    border: none;
    cursor: pointer;
    padding: 4px 0;
    color: #fff;
    transition: opacity var(--tr);
}
.hdr__lang-btn:hover { opacity: .8; }
.hdr--dark .hdr__lang-btn { color: var(--text); }

.hdr__lang-label {
    font-family: 'Raleway', sans-serif;
    font-size: 15px;
    font-weight: 500;
    color: inherit;
    display: none;
    min-width: 24px;
    text-align: center;
}
@media (min-width: 480px) { .hdr__lang-label { display: block; } }

.hdr__lang-arrow {
    color: currentColor;
    display: none;
    transition: transform var(--tr);
    flex-shrink: 0;
}
@media (min-width: 480px) { .hdr__lang-arrow { display: block; } }
.hdr__lang-btn[aria-expanded="true"] .hdr__lang-arrow { transform: rotate(180deg); }

/* Dropdown */
.hdr__lang-dropdown {
    position: absolute;
    top: calc(100% + 8px);
    right: 0;
    width: 140px;
    background: #fff;
    border-radius: 5px;
    box-shadow: 0 4px 12px rgba(0,0,0,.25);
    opacity: 0;
    visibility: hidden;
    transform: translateY(-4px);
    transition: opacity var(--tr), visibility var(--tr), transform var(--tr);
    z-index: 500;
    pointer-events: none;
}
.hdr__lang-dropdown.is-open {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
    pointer-events: auto;
}

/* Current lang row inside dropdown */
.hdr__lang-current {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 14px 17px 0;
}
.hdr__lang-current img { width: 28px; height: 28px; object-fit: contain; }
.hdr__lang-current span {
    font-family: 'Montserrat', sans-serif;
    font-size: 15px;
    color: #000;
}

/* Thin divider */
.hdr__lang-divider {
    width: 86px;
    height: 1px;
    background: rgba(0,0,0,.3);
    margin: 10px auto 0;
}

/* Options list */
.hdr__lang-list {
    list-style: none;
    margin: 0;
    padding: 11px 0 14px;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 11px;
}
.hdr__lang-option {
    display: block;
    padding: 4px 0;
    font-family: 'Montserrat', sans-serif;
    font-size: 15px;
    font-weight: 400;
    color: var(--gray);
    text-decoration: none;
    text-align: center;
    transition: color var(--tr);
    white-space: nowrap;
}
.hdr__lang-option:hover { color: var(--blue); }

/* ─── Phone ──────────────────────────────────────────────────── */
.hdr__phone {
    font-family: 'Montserrat', sans-serif;
    font-weight: 600;
    font-size: 15px;
    color: #fff;
    text-decoration: none;
    white-space: nowrap;
    display: none;
    transition: opacity var(--tr);
}
.hdr__phone:hover { opacity: .75; }
.hdr--dark .hdr__phone { color: var(--text); }
@media (min-width: 860px) { .hdr__phone { display: block; } }

/* ─── Burger ─────────────────────────────────────────────────── */
.hdr__burger {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 36px; height: 36px;
    background: transparent;
    border: none;
    cursor: pointer;
    padding: 0;
    flex-shrink: 0;
    margin-right: -4px;
}
@media (min-width: 1024px) { .hdr__burger { display: none; } }

.hdr__burger-box {
    position: relative;
    display: block;
    width: 24px; height: 16px;
}
.hdr__burger-line {
    position: absolute;
    left: 0; width: 100%; height: 2px;
    border-radius: 2px;
    background: #fff;
    transition: transform .25s ease, opacity .2s ease, top .25s ease;
}
.hdr--dark .hdr__burger-line { background: var(--text); }
.hdr__burger-line:nth-child(1) { top: 0; }
.hdr__burger-line:nth-child(2) { top: 7px; }
.hdr__burger-line:nth-child(3) { top: 14px; }
.hdr__burger.is-active .hdr__burger-line:nth-child(1) { top:7px; transform:rotate(45deg); }
.hdr__burger.is-active .hdr__burger-line:nth-child(2) { opacity:0; }
.hdr__burger.is-active .hdr__burger-line:nth-child(3) { top:7px; transform:rotate(-45deg); }

/* ─── Bottom row: nav + socials ─────────────────────────────── */
.hdr__bottom {
    display: none;
    align-items: center;
    justify-content: space-between;
    gap: 20px;
}
@media (min-width: 1024px) {
    .hdr__bottom { display: flex; }
}

.hdr__socials {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-shrink: 0;
}
.hdr__social {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    border-radius: 6px;
    transition: opacity var(--tr), border-color var(--tr);
}
.hdr__social img { width: 30px; height: 30px; object-fit: contain; }
.hdr__social:hover { opacity: .7; }
/* На главной шапка лежит поверх тёмного героя — рамки и иконки соцсетей белые */
.hdr--home .hdr__social { border-color: rgba(255,255,255,.7); }
.hdr--home .hdr__social img { filter: brightness(0) invert(1); }

/* ─── Mobile menu backdrop ───────────────────────────────────── */
.mob-backdrop {
    position: fixed; inset: 0;
    background: rgba(0,0,0,.48);
    opacity: 0; visibility: hidden;
    transition: opacity .28s ease, visibility .28s ease;
    z-index: 450;
}
.mob-backdrop.is-open { opacity: 1; visibility: visible; }

/* ─── Mobile menu panel ──────────────────────────────────────── */
.mob-menu {
    position: fixed;
    top: 0; right: 0; bottom: 0;
    width: min(360px, 88vw);
    background: #fff;
    transform: translateX(105%);
    transition: transform .3s ease;
    z-index: 460;
    display: flex; flex-direction: column;
    box-shadow: -6px 0 32px rgba(0,0,0,.12);
}
.mob-menu.is-open { transform: translateX(0); }

.mob-menu__head {
    display: flex; align-items: center; justify-content: space-between;
    padding: 18px 20px 16px;
    border-bottom: 1px solid var(--line);
    flex-shrink: 0;
}
.mob-menu__logo { width: 120px; height: auto; object-fit: contain; }
.mob-menu__close {
    display: flex; align-items: center; justify-content: center;
    width: 38px; height: 38px;
    background: transparent; border: none; cursor: pointer;
    padding: 0; margin: -6px; border-radius: 50%;
    transition: background var(--tr);
}
.mob-menu__close:hover { background: #F2F2F2; }

.mob-menu__body {
    flex: 1; min-height: 0; overflow-y: auto;
    -webkit-overflow-scrolling: touch;
    padding: 16px 20px 40px;
    display: flex; flex-direction: column; gap: 20px;
}

/* Mobile search */
.mob-menu__search-wrap {
    position: relative;
    display: flex; align-items: center;
    height: 44px;
    flex-shrink: 0;
    border: 1px solid #C0C0C0;
    border-radius: 4px;
}
.mob-menu__search-input {
    flex: 1; min-width: 0;
    height: 100%;
    padding: 0 40px 0 14px;
    border: none; outline: none; border-radius: 4px;
    font-family: 'Raleway', sans-serif;
    font-size: 15px; color: var(--text);
    background: transparent;
    -webkit-appearance: none; appearance: none;
    box-sizing: border-box;
}
.mob-menu__search-input::placeholder { color: var(--gray); }
.mob-menu__search-input::-webkit-search-decoration,
.mob-menu__search-input::-webkit-search-cancel-button { -webkit-appearance: none; }
.mob-menu__search-icon {
    position: absolute; right: 12px;
    pointer-events: none; opacity: .5;
}

/* Nav list */
.mob-menu__list { list-style: none; margin: 0; padding: 0; display: flex; flex-direction: column; }
.mob-menu__link {
    display: flex; align-items: center; justify-content: space-between;
    padding: 14px 2px;
    font-family: 'Montserrat', sans-serif;
    font-size: 16px; font-weight: 400; color: var(--text);
    text-decoration: none;
    border-bottom: 1px solid #F2F2F2;
    cursor: pointer;
    transition: color var(--tr);
}
.mob-menu__link.is-active { color: var(--blue); font-weight: 600; }

.mob-menu__summary { list-style: none; -webkit-tap-highlight-color: transparent; }
.mob-menu__summary::-webkit-details-marker { display: none; }
.mob-menu__details[open] .mob-menu__summary { color: var(--blue); font-weight: 600; }

.mob-menu__chevron { color: #9A9A9A; transition: transform var(--tr); flex-shrink: 0; }
.mob-menu__details[open] .mob-menu__chevron { transform: rotate(180deg); color: var(--blue); }

.mob-menu__sublist { list-style: none; margin: 0; padding: 4px 0 6px 14px; display: flex; flex-direction: column; }
.mob-menu__sublink {
    display: block; padding: 10px 2px;
    font-family: 'Raleway', sans-serif; font-size: 14px;
    color: #5B5B5B; text-decoration: none;
    transition: color var(--tr);
}
.mob-menu__sublink:hover { color: var(--blue); }
.mob-menu__sublink.is-active { color: var(--blue); font-weight: 600; }

/* Catalog btn inside mob menu */
.mob-menu__catalog-btn {
    display: flex; align-items: center; justify-content: center;
    height: 50px; width: 100%;
    background: var(--blue); color: #fff;
    font-family: 'Montserrat', sans-serif; font-size: 16px; font-weight: 500;
    border: none; border-radius: 6px; cursor: pointer;
    transition: background var(--tr);
}
.mob-menu__catalog-btn:hover  { background: #174480; }
.mob-menu__catalog-btn:active { background: #123761; }

.mob-menu__hr { border: none; border-top: 1px solid #EFEFEF; margin: 0; }

.mob-menu__phone {
    font-family: 'Montserrat', sans-serif; font-weight: 700; font-size: 17px;
    color: var(--text); text-decoration: none; transition: color var(--tr);
}
.mob-menu__phone:hover { color: var(--blue); }

.mob-menu__langs { display: flex; gap: 8px; flex-wrap: wrap; }
.mob-menu__lang {
    display: flex; align-items: center; justify-content: center;
    width: 46px; height: 36px; border-radius: 6px;
    background: #F2F2F2;
    font-family: 'Montserrat', sans-serif; font-size: 13px; font-weight: 500;
    color: #5B5B5B; text-decoration: none;
    transition: background var(--tr), color var(--tr);
    border: none; cursor: pointer;
}
.mob-menu__lang:hover { background: #E0E8F5; color: var(--blue); }
.mob-menu__lang.is-active { background: var(--blue); color: #fff; pointer-events: none; }

.mob-menu__socials { display: flex; gap: 20px; align-items: center; }
.mob-menu__social { display: flex; align-items: center; justify-content: center; transition: opacity var(--tr); }
.mob-menu__social:hover { opacity: .7; }
.mob-menu__social img { width: 26px; height: 26px; object-fit: contain; }

/* ─── Catalog popup (identical to file 1) ────────────────────── */
.cat-popup {
    position: fixed;
    top: var(--header-height, 76px);
    left: 0; right: 0; bottom: 0;
    z-index: 400;
    background: rgba(0,0,0,.45);
    opacity: 0; visibility: hidden;
    transition: opacity .25s ease, visibility .25s ease;
    overflow-y: auto;
    overscroll-behavior: contain;
}
.cat-popup.is-open { opacity: 1; visibility: visible; }

.cat-popup__shell {
    display: flex;
    min-height: 100%;
    max-width: 1920px;
    margin: 0 auto;
    background: #fff;
}
.cat-popup__sidebar {
    width: 293px;
    flex-shrink: 0;
    background: #fff;
    padding: clamp(20px,4vh,40px) 0;
    display: flex; flex-direction: column; gap: 4px;
    position: sticky; top: 0;
    align-self: flex-start;
    max-height: calc(100vh - var(--header-height, 76px));
    overflow-y: auto;
    padding-left: var(--hdr-px);
}
.cat-popup__divider {
    width: 1px; background: #767676;
    flex-shrink: 0; align-self: stretch;
    margin: 0 44px;
}
.cat-popup__content {
    flex: 1; min-width: 0;
    padding: clamp(20px,4vh,40px) var(--hdr-px) 60px 0;
    overflow-y: auto;
    max-height: calc(100vh - var(--header-height, 76px));
}
.cat-popup__content-head {
    display: flex; align-items: flex-start;
    justify-content: space-between; gap: 16px;
    margin-bottom: clamp(24px, 4vh, 60px);
}
.cat-popup__title {
    font-family: 'Montserrat', sans-serif;
    font-weight: 600; font-size: clamp(22px, 2.5vw, 40px);
    line-height: 1.1; color: var(--dblue); margin: 0;
}
.cat-popup__close {
    display: flex; align-items: center; justify-content: center;
    width: 48px; height: 48px;
    background: transparent; border: none; cursor: pointer; padding: 0;
    border-radius: 50%; flex-shrink: 0;
    transition: background var(--tr), opacity var(--tr);
}
.cat-popup__close:hover { background: #F0F4FB; }
.cat-popup__grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: clamp(20px, 3vw, 50px) clamp(24px, 8vw, 146px);
}

.cat-btn {
    width: 100%; min-height: 63px;
    display: flex; align-items: center;
    padding: 12px 16px;
    background: transparent; border: none;
    border-radius: 4px; cursor: pointer;
    text-align: left;
    transition: background var(--tr);
}
.cat-btn__text {
    font-family: 'Montserrat', sans-serif;
    font-weight: 600; font-size: clamp(14px, 1.1vw, 18px);
    line-height: 1.3; letter-spacing: -0.01em;
    color: #000; transition: color var(--tr);
    display: -webkit-box;
    -webkit-line-clamp: 2; -webkit-box-orient: vertical;
    overflow: hidden;
}
.cat-btn:hover .cat-btn__text { color: var(--dblue); }
.cat-btn.is-active { background: #DEDEDE; }
.cat-btn.is-active .cat-btn__text { color: var(--dblue); }

.cat-sub-item { display: flex; flex-direction: column; gap: 12px; }
.cat-sub-item__link {
    display: flex; align-items: center;
    gap: clamp(10px, 1vw, 15px);
    text-decoration: none;
}
.cat-sub-item__thumb {
    width: clamp(80px, 8vw, 120px);
    height: clamp(80px, 8vw, 120px);
    border-radius: 21px; background: #C5C5C5;
    flex-shrink: 0; overflow: hidden;
}
.cat-sub-item__thumb img { width: 100%; height: 100%; object-fit: cover; display: block; }
.cat-sub-item__name {
    font-family: 'Montserrat', sans-serif;
    font-weight: 500; font-size: clamp(16px, 1.5vw, 24px);
    line-height: 1.2; letter-spacing: -0.01em;
    color: #000; transition: color var(--tr);
}
.cat-sub-item__link:hover .cat-sub-item__name { color: var(--blue); }
.cat-sub-item__types {
    list-style: none; margin: 0;
    padding: 0 0 0 clamp(90px, 9vw, 135px);
    display: flex; flex-direction: column; gap: 4px;
}
.cat-sub-item__type-link {
    display: block; padding: 4px 0;
    font-family: 'Montserrat', sans-serif;
    font-size: clamp(13px, 1vw, 16px); font-weight: 400;
    color: #5B5B5B; text-decoration: none;
    line-height: 1.3; transition: color var(--tr);
}
.cat-sub-item__type-link:hover { color: var(--blue); }
.cat-empty {
    font-family: 'Montserrat', sans-serif;
    font-size: 15px; color: var(--gray);
    padding: 40px; text-align: center;
    grid-column: 1/-1;
}

@media (max-width: 1023px) {
    .cat-popup__shell { flex-direction: column; }
    .cat-popup__sidebar {
        width: 100%; max-height: none;
        position: relative;
        padding: 12px 16px;
        flex-direction: row; flex-wrap: wrap;
        gap: 6px;
        border-bottom: 1px solid #E0E0E0;
    }
    .cat-popup__divider { display: none; }
    .cat-popup__content { max-height: none; padding: 20px 16px 40px; }
    .cat-btn { width: auto; min-height: 0; padding: 8px 14px; border-radius: 6px; }
    .cat-popup__grid { grid-template-columns: 1fr 1fr; gap: 20px; }
}
@media (max-width: 599px) {
    .cat-popup__grid { grid-template-columns: 1fr; }
    .cat-sub-item__thumb { width: 72px; height: 72px; border-radius: 14px; }
    .cat-sub-item__types { padding-left: 82px; }
}

/* ─── Focus styles ───────────────────────────────────────────── */
:where(.hdr, .cat-popup, .mob-menu) :where(a, button):focus-visible {
    outline: 2px solid var(--blue);
    outline-offset: 3px;
    border-radius: 3px;
}
</style>

{{-- ═══════════════════════════════════════════════════════════ --}}
{{--  JAVASCRIPT  (полностью из файла 1)                         --}}
{{-- ═══════════════════════════════════════════════════════════ --}}
<script>
(function () {
    'use strict';

    /* ── Catalog data ── */
    var RAW_CATS = @json($catalogData['categories'] ?? []);
    var RAW_SUBS = @json($catalogData['subcategories'] ?? []);
    window.__catalogData = (function() {
        var cats = Array.isArray(RAW_CATS) ? RAW_CATS : (RAW_CATS && typeof RAW_CATS === 'object' ? Object.values(RAW_CATS) : []);
        var subs = Array.isArray(RAW_SUBS) ? RAW_SUBS : (RAW_SUBS && typeof RAW_SUBS === 'object' ? Object.values(RAW_SUBS) : []);
        return cats.map(function(cat) {
            var c = Object.assign({}, cat);
            c.subs = subs.filter(function(s) { return String(s.category_id) === String(c.id); });
            return c;
        });
    })();

    var LANG        = (document.querySelector('.hdr') || {}).dataset && document.querySelector('.hdr').dataset.lang || 'ru';
    var PLACEHOLDER = '{{ asset("assets/placeholder.png") }}';
    var I18N = {
        noResults: @js($t['header']['no_results']   ?? 'Ничего не найдено'),
        error:     @js($t['header']['search_error'] ?? 'Ошибка загрузки')
    };

    function esc(s) {
        return String(s||'')
            .replace(/&/g,'&amp;').replace(/</g,'&lt;')
            .replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }
    function localName(obj, field) {
        field = field || 'name';
        if (LANG==='az' && obj[field+'_az']) return obj[field+'_az'];
        if (LANG==='en' && obj[field+'_en']) return obj[field+'_en'];
        return obj[field] || '';
    }
    function get(id) { return document.getElementById(id); }

    /* ── Header height CSS var ── */
    var hdrEl = document.querySelector('.hdr');
    function setHdrH() {
        var h = hdrEl ? hdrEl.offsetHeight : 76;
        document.documentElement.style.setProperty('--header-height', h+'px');
    }
    setHdrH();
    window.addEventListener('load', setHdrH);
    var _rt;
    window.addEventListener('resize', function(){ clearTimeout(_rt); _rt=setTimeout(setHdrH,120); });

    /* ── Scroll lock ── */
    var _slc = 0;
    function lockScroll(on) {
        _slc = Math.max(0, _slc + (on ? 1 : -1));
        document.body.style.overflow = _slc > 0 ? 'hidden' : '';
    }

    /* ════════════════════════════════════════════════════════════
       CATALOG POPUP  (логика из файла 1 без изменений)
       ════════════════════════════════════════════════════════════ */
    var catPopup   = get('catalogPopup');
    var catSidebar = get('catSidebar');
    var catGrid    = get('catGrid');
    var catTitle   = get('catTitle');
    var catClose   = get('catalogClose');

    function buildSidebar() {
        if (!catSidebar) return;
        catSidebar.innerHTML = '';
        var data = window.__catalogData || [];
        if (!data.length) {
            catSidebar.innerHTML = '<p style="padding:20px;color:#636363;font-family:Montserrat,sans-serif;font-size:14px">Категорий нет</p>';
            return;
        }
        data.forEach(function(cat, idx) {
            var btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'cat-btn' + (idx===0 ? ' is-active' : '');
            btn.setAttribute('data-cat-id', cat.id);
            btn.setAttribute('aria-selected', idx===0 ? 'true' : 'false');
            var span = document.createElement('span');
            span.className = 'cat-btn__text';
            span.textContent = localName(cat);
            btn.appendChild(span);
            btn.addEventListener('click', function() { activateCat(cat.id); });
            catSidebar.appendChild(btn);
        });
    }

    function renderSubs(subs) {
        if (!catGrid) return;
        if (!subs || !subs.length) {
            catGrid.innerHTML = '<p class="cat-empty">Подкатегорий нет</p>';
            return;
        }
        catGrid.innerHTML = subs.map(function(sub) {
            var subId   = sub.id || 0;
            var subName = localName(sub);
            var imgHtml = sub.image_url
                ? '<img src="'+esc(sub.image_url)+'" alt="'+esc(subName)+'" width="120" height="120" loading="lazy" />'
                : '';
            var types     = Array.isArray(sub.types) ? sub.types : [];
            var typesHtml = types.length
                ? '<ul class="cat-sub-item__types" role="list">'
                    + types.map(function(t){
                        var tid=t.id||0, tname=localName(t);
                        return '<li role="listitem"><a href="/'+LANG+'/products?subcategory='+subId+'&type='+tid
                            +'" class="cat-sub-item__type-link">'+esc(tname)+'</a></li>';
                    }).join('')
                    +'</ul>'
                : '';
            return '<div class="cat-sub-item">'
                +'<a href="/'+LANG+'/products?subcategory='+subId+'" class="cat-sub-item__link">'
                +'<div class="cat-sub-item__thumb">'+imgHtml+'</div>'
                +'<span class="cat-sub-item__name">'+esc(subName)+'</span>'
                +'</a>'
                +typesHtml
                +'</div>';
        }).join('');
    }

    function activateCat(catId) {
        var data = window.__catalogData || [];
        var cat  = data.find(function(c){ return String(c.id)===String(catId); });
        if (!cat) return;
        if (catSidebar) {
            catSidebar.querySelectorAll('.cat-btn').forEach(function(b) {
                var active = b.dataset.catId == catId;
                b.classList.toggle('is-active', active);
                b.setAttribute('aria-selected', active ? 'true' : 'false');
            });
        }
        if (catTitle) catTitle.textContent = localName(cat);
        renderSubs(cat.subs || []);
        if (catGrid) catGrid.scrollTop = 0;
    }

    var _catalogBuilt = false;
    function openCatalog() {
        if (!catPopup) return;
        setHdrH();
        if (!_catalogBuilt) { buildSidebar(); _catalogBuilt = true; }
        var data = window.__catalogData || [];
        if (data.length) activateCat(data[0].id);
        catPopup.classList.add('is-open');
        catPopup.setAttribute('aria-hidden','false');
        document.querySelectorAll('.js-catalog-trigger').forEach(function(b){
            b.setAttribute('aria-expanded','true');
        });
        lockScroll(true);
    }
    function closeCatalog() {
        if (!catPopup || !catPopup.classList.contains('is-open')) return;
        catPopup.classList.remove('is-open');
        catPopup.setAttribute('aria-hidden','true');
        document.querySelectorAll('.js-catalog-trigger').forEach(function(b){
            b.setAttribute('aria-expanded','false');
        });
        lockScroll(false);
    }

    catClose  && catClose.addEventListener('click', closeCatalog);
    catPopup  && catPopup.addEventListener('click', function(e){
        if (e.target === catPopup) closeCatalog();
    });
    document.querySelectorAll('.js-catalog-trigger').forEach(function(t){
        t.addEventListener('click', function(e){
            e.preventDefault();
            catPopup && catPopup.classList.contains('is-open') ? closeCatalog() : openCatalog();
        });
    });

    /* ════════════════════════════════════════════════════════════
       MOBILE MENU  (логика из файла 1 без изменений)
       ════════════════════════════════════════════════════════════ */
    var burgerBtn   = get('burgerBtn');
    var mobMenu     = get('mobileMenu');
    var mobBackdrop = get('mobileMenuBackdrop');
    var mobClose    = get('mobileMenuClose');

    window.closeMobileMenu = function() {
        if (!mobMenu) return;
        mobMenu.classList.remove('is-open');
        mobBackdrop && mobBackdrop.classList.remove('is-open');
        burgerBtn && (burgerBtn.classList.remove('is-active'),
                      burgerBtn.setAttribute('aria-expanded','false'));
        mobMenu.setAttribute('aria-hidden','true');
        mobMenu.querySelectorAll('details').forEach(function(d){ d.removeAttribute('open'); });
        lockScroll(false);
    };
    function openMobileMenu() {
        if (!mobMenu) return;
        closeCatalog();
        mobMenu.classList.add('is-open');
        mobBackdrop && mobBackdrop.classList.add('is-open');
        burgerBtn && (burgerBtn.classList.add('is-active'),
                      burgerBtn.setAttribute('aria-expanded','true'));
        mobMenu.setAttribute('aria-hidden','false');
        lockScroll(true);
        setTimeout(function(){
            var f = Array.from(mobMenu.querySelectorAll('a,button,input,[tabindex]:not([tabindex="-1"])'))
                        .filter(function(el){ return el.offsetParent!==null; })[0];
            f && f.focus();
        }, 60);
    }
    burgerBtn   && burgerBtn.addEventListener('click', function(){
        burgerBtn.classList.contains('is-active') ? window.closeMobileMenu() : openMobileMenu();
    });
    mobClose    && mobClose.addEventListener('click', window.closeMobileMenu);
    mobBackdrop && mobBackdrop.addEventListener('click', window.closeMobileMenu);
    mobMenu && mobMenu.querySelectorAll('a').forEach(function(a){
        a.addEventListener('click', function(){
            var href=a.getAttribute('href')||'';
            if (a.target==='_blank'||href.startsWith('http')||
                href.startsWith('tel:')||href.startsWith('mailto:')||
                a.classList.contains('mob-menu__social')) return;
            window.closeMobileMenu();
        });
    });
    window.matchMedia('(min-width:1024px)').addEventListener('change', function(e){
        if (e.matches) window.closeMobileMenu();
    });

    /* ════════════════════════════════════════════════════════════
       SEARCH DROPDOWN  (icon → dropdown, как во втором файле,
       но API-логика из первого)
       ════════════════════════════════════════════════════════════ */
    var searchWrap   = get('headerSearchWrap');
    var searchIconBtn= get('searchIconBtn');
    var searchDropdown = get('searchDropdown');
    var searchInput  = get('searchInput');
    var searchSubmit = get('searchSubmitBtn');
    var searchRes    = get('searchResults');
    var mobSearch    = get('mobileSearchInput');
    var searchLang   = (searchWrap && searchWrap.dataset.lang) || LANG;

    function openSearchDropdown() {
        if (!searchDropdown) return;
        searchDropdown.classList.add('is-open');
        searchDropdown.setAttribute('aria-hidden','false');
        searchIconBtn && searchIconBtn.setAttribute('aria-expanded','true');
        setTimeout(function(){ searchInput && searchInput.focus(); }, 60);
    }
    function closeSearchDropdown() {
        if (!searchDropdown) return;
        searchDropdown.classList.remove('is-open');
        searchDropdown.setAttribute('aria-hidden','true');
        searchIconBtn && searchIconBtn.setAttribute('aria-expanded','false');
    }

    searchIconBtn && searchIconBtn.addEventListener('click', function(e){
        e.stopPropagation();
        searchDropdown && searchDropdown.classList.contains('is-open')
            ? closeSearchDropdown()
            : openSearchDropdown();
    });
    searchDropdown && searchDropdown.addEventListener('click', function(e){ e.stopPropagation(); });
    document.addEventListener('click', function(e){
        if (searchWrap && !searchWrap.contains(e.target)) closeSearchDropdown();
    });

    function isSafeUrl(url) {
        if (typeof url !== 'string' || !url) return false;
        return url.startsWith('/') || /^https?:\/\//i.test(url);
    }
    function buildResultItem(item) {
        var a = document.createElement('a');
        a.href = isSafeUrl(item.url) ? item.url : '#';
        a.className = 'hdr__result-item';
        a.setAttribute('role','option');
        var img = document.createElement('img');
        img.src = isSafeUrl(item.image) ? item.image : PLACEHOLDER;
        img.alt = ''; img.loading = 'lazy';
        img.className = 'hdr__result-thumb';
        img.width = 49; img.height = 41;
        var span = document.createElement('span');
        span.className = 'hdr__result-title';
        span.textContent = item.title || '';
        a.appendChild(img); a.appendChild(span);
        return a;
    }
    function showMsg(text, isError) {
        if (!searchRes) return;
        searchRes.innerHTML = '';
        var p = document.createElement('p');
        p.className = 'hdr__result-msg' + (isError ? ' hdr__result-msg--error' : '');
        p.textContent = text;
        searchRes.appendChild(p);
    }
    function doSearch(query) {
        if (!searchRes) return;
        if (!query || query.length < 2) { searchRes.innerHTML=''; return; }
        fetch('/api/search?q='+encodeURIComponent(query)+'&lang='+encodeURIComponent(searchLang))
            .then(function(r){ if(!r.ok) throw new Error(r.status); return r.json(); })
            .then(function(data){
                searchRes.innerHTML = '';
                if (Array.isArray(data.results) && data.results.length) {
                    data.results.forEach(function(item){ searchRes.appendChild(buildResultItem(item)); });
                } else {
                    showMsg(I18N.noResults);
                }
            })
            .catch(function(){ showMsg(I18N.error, true); });
    }
    var _st;
    searchInput && searchInput.addEventListener('input', function(){
        clearTimeout(_st);
        _st = setTimeout(function(){ doSearch(searchInput.value.trim()); }, 300);
    });
    searchInput && searchInput.addEventListener('keydown', function(e){
        if (e.key==='Enter') { clearTimeout(_st); doSearch(searchInput.value.trim()); }
        if (e.key==='Escape') closeSearchDropdown();
    });
    searchSubmit && searchSubmit.addEventListener('click', function(){
        clearTimeout(_st); doSearch(searchInput ? searchInput.value.trim() : '');
    });
    mobSearch && mobSearch.addEventListener('keydown', function(e){
        if (e.key==='Enter' && mobSearch.value.trim().length>=2)
            window.location.href = '/'+LANG+'/products?q='+encodeURIComponent(mobSearch.value.trim());
    });

    /* ════════════════════════════════════════════════════════════
       LANGUAGE SWITCHER  (dropdown из второго файла)
       ════════════════════════════════════════════════════════════ */
    var langBtn  = get('langSwitcherBtn');
    var langDrop = get('langDropdown');

    function toggleLang(force) {
        if (!langBtn || !langDrop) return;
        var next = typeof force==='boolean' ? force : !langDrop.classList.contains('is-open');
        langDrop.classList.toggle('is-open', next);
        langDrop.setAttribute('aria-hidden', String(!next));
        langBtn.setAttribute('aria-expanded', String(next));
    }
    langBtn  && langBtn.addEventListener('click', function(e){ e.stopPropagation(); toggleLang(); });
    langDrop && langDrop.addEventListener('click', function(e){ e.stopPropagation(); });
    document.addEventListener('click', function(){ toggleLang(false); });
    window.__closeLang = function(){ toggleLang(false); };

    /* ── Global Escape handler ── */
    document.addEventListener('keydown', function(e){
        if (e.key!=='Escape') return;
        if (catPopup      && catPopup.classList.contains('is-open'))      { closeCatalog(); return; }
        if (searchDropdown&& searchDropdown.classList.contains('is-open')){ closeSearchDropdown(); return; }
        if (langDrop      && langDrop.classList.contains('is-open'))      { toggleLang(false); return; }
        if (mobMenu       && mobMenu.classList.contains('is-open'))       { window.closeMobileMenu(); }
    });
})();
</script>