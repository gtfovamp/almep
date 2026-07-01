@php
    // Ожидается: $t (массив переводов), $lang (текущий язык)
    // $catalogData передаётся из контроллера (категории/подкатегории для каталога)
    $catalogData = $catalogData ?? ['categories' => [], 'subcategories' => []];

    $currentPath = '/' . ltrim(request()->path(), '/');
    $pathWithoutLang = preg_replace('#^/(ru|en|az)#', '', $currentPath) ?: '/';
    $isHomePage = $pathWithoutLang === '/';

    $navLinks = [
        ['label' => $t['nav']['home'] ?? '', 'href' => "/{$lang}", 'active' => $pathWithoutLang === '/'],
        ['label' => $t['nav']['about'] ?? '', 'href' => "/{$lang}/about", 'dropdown' => true],
        ['label' => $t['nav']['services'] ?? '', 'href' => "/{$lang}/services"],
        ['label' => $t['nav']['products'] ?? '', 'href' => "/{$lang}/products"],
        ['label' => $t['nav']['partners'] ?? '', 'href' => "/{$lang}/partners"],
        ['label' => $t['nav']['portfolio'] ?? '', 'href' => "/{$lang}/portfolio"],
        ['label' => $t['nav']['news'] ?? '', 'href' => "/{$lang}/news"],
        ['label' => $t['nav']['reviews'] ?? '', 'href' => "/{$lang}/reviews"],
        ['label' => $t['nav']['contacts'] ?? '', 'href' => "/{$lang}/contacts"],
    ];

    $langLabels = ['ru' => 'RU', 'en' => 'EN', 'az' => 'AZ'];
    $currentLangLabel = $langLabels[$lang] ?? strtoupper($lang);
    $otherLangs = collect([
        ['code' => 'ru', 'label' => 'RU'],
        ['code' => 'en', 'label' => 'EN'],
        ['code' => 'az', 'label' => 'AZ'],
    ])->filter(fn ($l) => $l['code'] !== $lang);
@endphp

<header
    class="absolute top-0 left-0 right-0 z-[300] max-w-[1920px] mx-auto {{ $isHomePage ? '' : 'header--dark' }}"
    style="padding-left: 6vw; padding-right: 6vw; padding-top: 2.9vh; padding-bottom: 2.9vh;"
>
    <div class="flex flex-col gap-6 lg:gap-[33px]">
        {{-- Top Section --}}
        <div class="flex flex-col sm:flex-row justify-between items-center w-full gap-4 sm:gap-0 bg-black/30 sm:bg-transparent p-4 sm:p-0 rounded-lg sm:rounded-none backdrop-blur-sm sm:backdrop-blur-none">
            {{-- Left Side --}}
            <div class="flex flex-row items-center gap-4 lg:gap-[31px] w-full sm:w-auto justify-between sm:justify-start">
                <a href="/{{ $lang }}" class="flex-shrink-0">
                    <img
                        src="{{ $isHomePage ? asset('assets/icons/logo.svg') : asset('assets/icons/logo-white.svg') }}"
                        alt="Almep Trading"
                        class="w-[120px] h-[42px] sm:w-[140px] sm:h-[48px] lg:w-[162px] lg:h-[56px] object-contain"
                    />
                </a>
                <a href="/{{ $lang }}/products"
                   class="flex items-center justify-center w-[140px] sm:w-[150px] lg:w-[164px] h-[44px] sm:h-[46px] lg:h-[50px] bg-[#1C508F] hover:bg-[#174480] transition-colors rounded-sm font-['Montserrat'] font-normal text-sm sm:text-[15px] lg:text-base leading-[110%] text-white">
                    {{ $t['header']['catalog'] ?? '' }}
                </a>
            </div>

            {{-- Right Side --}}
            <div class="flex flex-row items-center gap-3 sm:gap-4 lg:gap-5 w-full sm:w-auto justify-end">
                {{-- Search --}}
                <div class="relative cursor-pointer" id="headerSearch">
                    <div class="flex items-center justify-center w-6 h-6 lg:w-[25px] lg:h-[25px]">
                        <img src="{{ asset('assets/icons/search.svg') }}" alt="Поиск" class="w-full h-full object-contain brightness-0 invert" />
                    </div>

                    <div class="fixed top-0 left-0 right-0 mx-auto w-[calc(100%-2rem)] sm:w-[500px] lg:w-[570px] max-h-[80vh] lg:max-h-[512px] bg-white border border-black opacity-0 invisible transition-all duration-200 z-[300] flex flex-col" id="searchDropdown">
                        <div class="flex flex-row justify-between items-center px-4 lg:px-5 py-3 lg:py-[15px] gap-2 lg:gap-[10px] h-[49px] box-border">
                            <input type="text"
                                   class="flex-1 font-['Raleway'] font-normal text-sm lg:text-base leading-[110%] text-black border-none outline-none bg-transparent placeholder:text-[#636363]"
                                   placeholder="{{ $t['header']['search_placeholder'] ?? '' }}"
                                   id="searchInput" />
                            <button class="w-6 h-6 lg:w-[25px] lg:h-[25px] p-0 bg-transparent border-none cursor-pointer flex-shrink-0" aria-label="Поиск">
                                <img src="{{ asset('assets/icons/search.svg') }}" alt="Поиск" class="w-full h-full object-contain" />
                            </button>
                        </div>

                        <div class="w-[calc(100%-2.75rem)] lg:w-[calc(100%-44px)] h-px bg-black mx-4 lg:mx-[22px]"></div>

                        <div class="p-4 lg:p-5 flex flex-col gap-3 lg:gap-[15px] max-h-[60vh] lg:max-h-[400px] overflow-y-auto" id="searchResults">
                            {{-- Результаты поиска добавляются динамически --}}
                        </div>
                    </div>
                </div>

                {{-- Language Switcher --}}
                <div class="relative cursor-pointer" id="langSwitcher">
                    <div class="flex flex-row items-center gap-2 lg:gap-[10px] h-6 lg:h-7">
                        <img src="{{ asset('assets/icons/globe.svg') }}" alt="Язык" class="w-6 h-6 lg:w-7 lg:h-7 object-contain brightness-0 invert" />
                        <span class="font-['Raleway'] font-normal text-sm lg:text-base leading-[110%] text-white min-w-[24px] lg:min-w-7 text-center hidden sm:inline">{{ $currentLangLabel }}</span>
                        <svg class="transition-transform duration-200 flex-shrink-0 hidden sm:block" width="15" height="8" viewBox="0 0 15 8" fill="none">
                            <path d="M1 1L7.5 7L14 1" stroke="white" stroke-width="1.5" stroke-linecap="round"></path>
                        </svg>
                    </div>

                    <div class="absolute top-0 right-0 w-[140px] lg:w-[160px] bg-white shadow-[0px_4px_4px_rgba(0,0,0,0.25)] rounded-[5px] opacity-0 invisible -translate-y-1 transition-all duration-200 z-[300]" id="langDropdown">
                        <div class="flex flex-row items-center gap-2 lg:gap-[10px] px-3 lg:px-[17px] pt-3 lg:pt-[14px]">
                            <img src="{{ asset('assets/icons/globe.svg') }}" alt="Язык" class="w-6 h-6 lg:w-7 lg:h-7 object-contain" />
                            <span class="font-['Montserrat'] font-normal text-sm lg:text-base leading-[110%] text-black">{{ $currentLangLabel }}</span>
                            <svg width="15" height="8" viewBox="0 0 15 8" fill="none" class="ml-auto">
                                <path d="M1 1L7.5 7L14 1" stroke="black" stroke-width="1.5" stroke-linecap="round"></path>
                            </svg>
                        </div>

                        <div class="w-[70px] lg:w-[86px] h-px bg-black/30 mx-auto mt-2 lg:mt-[10px]"></div>

                        <ul class="list-none p-0 pt-2 lg:pt-[11px] pb-3 lg:pb-4 m-0 flex flex-col items-center gap-2 lg:gap-[11px]">
                            @foreach ($otherLangs as $l)
                                <li>
                                    <a href="/{{ $l['code'] }}{{ $pathWithoutLang }}"
                                       class="font-['Montserrat'] font-normal text-sm lg:text-base leading-[110%] text-center text-[#636363] hover:text-[#1C508F] transition-colors cursor-pointer bg-transparent border-none p-0 w-7 block no-underline"
                                       data-lang="{{ $l['code'] }}">
                                        {{ $l['label'] }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                <a href="tel:{{ str_replace(' ', '', $t['header']['phone'] ?? '') }}"
                   class="font-['Montserrat'] font-semibold text-sm lg:text-base leading-[110%] text-white whitespace-nowrap hidden md:block">
                    {{ $t['header']['phone'] ?? '' }}
                </a>
            </div>
        </div>

        {{-- Bottom Section --}}
        <div class="hidden lg:flex flex-row justify-between items-center w-full h-[37px]">
            @include('partials.nav', ['links' => $navLinks, 'lang' => $lang, 't' => $t])
            <div class="flex flex-row items-center gap-3 lg:gap-[15px]">
                <a href="https://www.instagram.com/almepengineeringgroup/" class="flex items-center justify-center transition-opacity hover:opacity-75" aria-label="Instagram" target="_blank" rel="noopener noreferrer">
                    <img src="{{ asset('assets/icons/instagram.svg') }}" alt="Instagram" class="w-7 lg:w-[30px] h-7 lg:h-[30px] object-contain brightness-0 invert" />
                </a>
                <a href="https://www.youtube.com/@almepengineeringgroup" class="flex items-center justify-center transition-opacity hover:opacity-75" aria-label="YouTube" target="_blank" rel="noopener noreferrer">
                    <img src="{{ asset('assets/icons/youtube.svg') }}" alt="YouTube" class="w-7 lg:w-[30px] h-7 lg:h-[30px] object-contain brightness-0 invert" />
                </a>
                <a href="https://www.facebook.com/almepengineeringgroup" class="flex items-center justify-center transition-opacity hover:opacity-75" aria-label="Facebook" target="_blank" rel="noopener noreferrer">
                    <img src="{{ asset('assets/icons/facebook.svg') }}" alt="Facebook" class="w-7 lg:w-8 h-7 lg:h-8 object-contain brightness-0 invert" />
                </a>
            </div>
        </div>
    </div>

    {{-- TODO: заглушка попапа каталога — исходный CatalogPopup (pages/catalog.astro) не был предоставлен.
         Компонент должен рендерить $catalogData['categories'] / ['subcategories'] в виде выезжающей панели
         с id="catalogPopup" и классом is-open при открытии (см. скрипт ниже). Доделай сам. --}}
    <div id="catalogPopup" class="catalog-popup">
        {{-- <x-catalog-popup :lang="$lang" :categories="$catalogData['categories']" :subcategories="$catalogData['subcategories']" /> --}}
    </div>
</header>

<script>
    window.__catalogData = @json($catalogData['categories'] ?? []).map((cat) => ({
        ...cat,
        subs: (@json($catalogData['subcategories'] ?? [])).filter((sub) => sub.category_id === cat.id)
    }));

    function initCatalogTrigger() {
        document.querySelectorAll('a[href*="products"]').forEach((link) => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const header = document.querySelector('header');
                if (header) {
                    document.documentElement.style.setProperty('--header-height', header.offsetHeight + 'px');
                }
                document.getElementById('catalogPopup')?.classList.add('is-open');
                document.body.style.overflow = 'hidden';
                const firstCat = window.__catalogData?.[0];
                if (firstCat) {
                    document.dispatchEvent(new CustomEvent('catalogOpen', { detail: firstCat }));
                }
            });
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                document.getElementById('catalogPopup')?.classList.remove('is-open');
                document.body.style.overflow = '';
            }
        });

        document.getElementById('catalogPopup')?.addEventListener('click', (e) => {
            if (e.target === e.currentTarget) {
                e.currentTarget.classList.remove('is-open');
                document.body.style.overflow = '';
            }
        });
    }

    document.addEventListener('DOMContentLoaded', initCatalogTrigger);

    // === SEARCH ===
    function initSearch() {
        const searchContainer = document.getElementById('headerSearch');
        const searchInput = document.getElementById('searchInput');
        const searchDropdown = document.getElementById('searchDropdown');
        const searchResults = document.getElementById('searchResults');

        if (!searchContainer || !searchInput || !searchDropdown || !searchResults) return;

        searchContainer.addEventListener('click', (e) => {
            e.stopPropagation();
            searchDropdown.classList.toggle('opacity-0');
            searchDropdown.classList.toggle('invisible');
            if (!searchDropdown.classList.contains('opacity-0')) {
                setTimeout(() => searchInput.focus(), 100);
            }
        });

        document.addEventListener('click', () => {
            searchDropdown.classList.add('opacity-0', 'invisible');
        });

        let searchTimeout;
        searchInput.addEventListener('input', (e) => {
            clearTimeout(searchTimeout);
            const query = e.target.value.trim();

            if (query.length < 2) {
                searchResults.innerHTML = '';
                return;
            }

            searchTimeout = setTimeout(async () => {
                try {
                    const response = await fetch(`/api/search?q=${encodeURIComponent(query)}`);
                    const data = await response.json();

                    if (data.results && data.results.length > 0) {
                        searchResults.innerHTML = data.results.map((item) => `
                            <a href="${item.url}" class="header__search-result-item">
                                <img src="${item.image || '/assets/placeholder.png'}" alt="${item.title}" class="header__search-result-image" />
                                <span class="header__search-result-title">${item.title}</span>
                            </a>
                        `).join('');
                    } else {
                        searchResults.innerHTML = '<div class="py-5 text-center text-[#636363]">Ничего не найдено</div>';
                    }
                } catch (error) {
                    console.error('Search error:', error);
                    searchResults.innerHTML = '';
                }
            }, 300);
        });
    }

    // === LANGUAGE SWITCHER ===
    function initLangSwitcher() {
        const langSwitcher = document.getElementById('langSwitcher');
        const langDropdown = document.getElementById('langDropdown');

        if (!langSwitcher || !langDropdown) return;

        langSwitcher.addEventListener('click', (e) => {
            e.stopPropagation();
            langDropdown.classList.toggle('opacity-0');
            langDropdown.classList.toggle('invisible');
            langDropdown.classList.toggle('-translate-y-1');
        });

        document.addEventListener('click', () => {
            langDropdown.classList.add('opacity-0', 'invisible', '-translate-y-1');
        });
    }

    initSearch();
    initLangSwitcher();
</script>

<style>
    .header--dark { background: #FFFFFF; position: relative; }
    .header--dark img[src*="search.svg"],
    .header--dark img[src*="globe.svg"],
    .header--dark img[src*="instagram.svg"],
    .header--dark img[src*="youtube.svg"],
    .header--dark img[src*="facebook.svg"] { filter: brightness(0) invert(0) !important; }
    .header--dark .font-\[\'Raleway\'\],
    .header--dark .font-\[\'Montserrat\'\],
    .header--dark a:not([href*="products"]) span,
    .header--dark span:not([href*="products"] *) { color: #000000 !important; }
    .header--dark a[href*="products"] { color: #FFFFFF !important; }
    .header--dark .nav__link { color: #000000 !important; }
    .header--dark .nav__arrow path { stroke: #000000 !important; }
    .header--dark svg path { stroke: #000000 !important; }
    .header--dark .bg-black\/30 { background: transparent !important; }

    .header__search-result-item { display: flex; flex-direction: row; align-items: center; gap: 19px; cursor: pointer; text-decoration: none; transition: opacity .2s; padding: 12px 0; }
    .header__search-result-item:hover { opacity: .7; }
    .header__search-result-image { width: 49px; height: 41px; background: #D9D9D9; flex-shrink: 0; object-fit: cover; }
    .header__search-result-title { font-family: 'Montserrat'; font-weight: 400; font-size: 18px; line-height: 130%; letter-spacing: -0.01em; color: #000; flex: 1; }
</style>
