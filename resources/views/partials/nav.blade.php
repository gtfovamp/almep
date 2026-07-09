@php
    // $links, $lang, $t передаются из header.blade.php
    $currentPath = request()->path(); // 'ru/about' и т.п. (без ведущего слэша)
    $currentPath = '/' . ltrim($currentPath, '/');
    $isStructureActive = str_contains($currentPath, '/structure');
@endphp

<nav class="nav">
    <ul class="nav__list">
        @foreach ($links as $link)
            @php
                $isActive = $currentPath === $link['href']
                    || (!empty($link['dropdown']) && (
                        str_contains($currentPath, '/about')
                        || str_contains($currentPath, '/structure')
                        || str_contains($currentPath, '/certificates')
                        || str_contains($currentPath, '/blog')
                    ));
            @endphp
            <li class="nav__item {{ $isActive ? 'nav__item--active' : '' }} {{ !empty($link['dropdown']) ? 'nav__item--has-dropdown' : '' }}">
                <a href="{{ $link['href'] }}" class="nav__link">
                    {{ $link['label'] }}
                    @if (!empty($link['dropdown']))
                        <svg class="nav__arrow" width="15" height="8" viewBox="0 0 15 8" fill="none">
                            <path d="M1 1L7.5 7L14 1" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                        </svg>
                    @endif
                </a>

                {{-- Подчёркивание активного пункта --}}
                @if ($isActive)
                    <span class="nav__underline"></span>
                @endif

                {{-- Дропдаун для "О компании" --}}
                @if (!empty($link['dropdown']) && !empty($lang))
                    <div class="nav__dropdown">
                        <ul class="nav__dropdown-list">
                            <li>
                                <a href="/{{ $lang }}/about"
                                   class="nav__dropdown-link {{ (str_contains($currentPath, '/about') && !str_contains($currentPath, '/structure') && !str_contains($currentPath, '/certificates') && !str_contains($currentPath, '/blog')) ? 'nav__dropdown-link--active' : '' }}">
                                    {{ $t['nav']['about_short'] ?? 'О нас' }}
                                </a>
                            </li>
                            <li>
                                <a href="/{{ $lang }}/structure"
                                   class="nav__dropdown-link {{ $isStructureActive ? 'nav__dropdown-link--active' : '' }}">
                                    {{ $t['nav']['structure'] ?? 'Структура компании' }}
                                </a>
                            </li>
                            <li>
                                <a href="/{{ $lang }}/certificates"
                                   class="nav__dropdown-link {{ str_contains($currentPath, '/certificates') ? 'nav__dropdown-link--active' : '' }}">
                                    {{ $t['nav']['certificates'] ?? 'Сертификаты и лицензии' }}
                                </a>
                            </li>
                            <li>
                                <a href="/{{ $lang }}/blog"
                                   class="nav__dropdown-link {{ str_contains($currentPath, '/blog') ? 'nav__dropdown-link--active' : '' }}">
                                    {{ $t['nav']['blog'] ?? 'Блог' }}
                                </a>
                            </li>
                        </ul>
                    </div>
                @endif
            </li>
        @endforeach
    </ul>
</nav>

<style>
    .nav__list { display: flex; flex-direction: row; align-items: center; gap: 40px; list-style: none; padding: 0; margin: 0; }
    .nav__item { display: flex; flex-direction: column; align-items: flex-start; gap: 5px; position: relative; }
    .nav__link { display: flex; flex-direction: row; align-items: center; gap: 5px; font-family: 'Montserrat', sans-serif; font-weight: 400; font-size: 16px; line-height: 110%; letter-spacing: -0.01em; color: #1A1A1A; white-space: nowrap; transition: color 0.2s, opacity 0.2s; }
    /* На главной шапка лежит поверх тёмного героя — ссылки белые. На внутренних страницах фон светлый — ссылки тёмные (иначе текст сливался с белым фоном). */
    .hdr--home .nav__link { color: #FFFFFF; }
    .nav__link:hover { color: #1C508F; opacity: 1; }
    .hdr--home .nav__link:hover { color: #FFFFFF; opacity: 0.8; }
    .nav__item--active .nav__link { color: #1A1A1A; font-weight: 500; }
    .hdr--home .nav__item--active .nav__link { color: #FFFFFF; }
    .nav__item--active .nav__link { font-weight: 500; }
    .nav__underline { display: block; width: 42px; height: 0px; border: 1px solid #003F8D; align-self: flex-end; }
    .hdr--home .nav__underline { border-color: #FFFFFF; }
    .nav__arrow { transition: transform 0.2s ease; }
    .nav__dropdown { position: absolute; top: 100%; left: 0; width: 234px; background: #FFFFFF; box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.15); opacity: 0; visibility: hidden; transform: translateY(-4px); transition: opacity 0.2s ease, transform 0.2s ease, visibility 0.2s; z-index: 200; padding: 15px; }
    .nav__item--has-dropdown:hover .nav__dropdown { opacity: 1; visibility: visible; transform: translateY(0); }
    .nav__item--has-dropdown:hover .nav__arrow { transform: rotate(180deg); }
    .nav__dropdown-list { list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 0; }
    .nav__dropdown-link { display: block; padding: 15px 10px; font-family: 'Raleway', sans-serif; font-weight: 400; font-size: 16px; line-height: 110%; color: #000000; white-space: nowrap; transition: color 0.15s; background: #FFFFFF; }
    .nav__dropdown-link:hover { color: #1C508F; }
    .nav__dropdown-link--active { background: #E7E7E7; }
</style>