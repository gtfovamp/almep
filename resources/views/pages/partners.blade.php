@extends('layouts.app')

@php
    $title = ($t['nav']['partners'] ?? 'Партнёры') . ' — Almep Trading';

    $getName = function ($p, $lang) {
        if ($lang === 'en' && !empty($p['name_en'])) return $p['name_en'];
        if ($lang === 'az' && !empty($p['name_az'])) return $p['name_az'];
        return $p['name_ru'] ?? $p['name'] ?? '';
    };
    $getDescription = function ($p, $lang) {
        if ($lang === 'en' && !empty($p['description_en'])) return $p['description_en'];
        if ($lang === 'az' && !empty($p['description_az'])) return $p['description_az'];
        return $p['description_ru'] ?? $p['description'] ?? '';
    };
    $partners = $partners ?? [];
@endphp

@section('content')
<main class="site-main">
<div style="position: relative; background: #FFFFFF;">
      @include('partials.header', ['t' => $t, 'lang' => $lang])
    </div>

    <!-- Секция Партнеры -->
    <section class="partners">
      <div class="partners__inner">

        <!-- Хлебные крошки -->
        <nav class="breadcrumbs">
          <a href="/{{ $lang }}" class="breadcrumbs__item">
            <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
              <path d="M2.5 7.5L10 2.5L17.5 7.5V16.25C17.5 16.5815 17.3683 16.8995 17.1339 17.1339C16.8995 17.3683 16.5815 17.5 16.25 17.5H3.75C3.41848 17.5 3.10054 17.3683 2.86612 17.1339C2.6317 16.8995 2.5 16.5815 2.5 16.25V7.5Z" stroke="#696969" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
              <path d="M7.5 17.5V10H12.5V17.5" stroke="#696969" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </a>
          <svg width="4" height="8" viewBox="0 0 4 8" fill="none" class="breadcrumbs__separator">
            <path d="M1 1L3 4L1 7" stroke="#706F6F" stroke-width="1"/>
          </svg>
          <span class="breadcrumbs__current">{{ $t['nav']['partners'] }}</span>
        </nav>

        <!-- Контент -->
        <div class="partners__content">

          <!-- Заголовок -->
          <h1 class="partners__title">{{ $t['partners_page']['title'] }}</h1>

          <!-- Сетка партнеров 2x2 -->
          <div class="partners__grid">
            @foreach($partners as $partner)
              <div class="partners__card">
                <div class="partners__card-content">
                  <img src="{{ $partner['image_url'] }}" alt="{{ $getName($partner, $lang) }}" class="partners__logo" loading="lazy" />
                  @php($__d = $getDescription($partner, $lang))
                  @if($__d)
                    <p class="partners__description">{{ $__d }}</p>
                  @endif
                </div>
              </div>
            @endforeach
          </div>

          <!-- Кнопка консультации -->
          <a href="/{{ $lang }}#consultation" class="partners__cta">
            {{ $t['partners_page']['become_partner_title'] }}
          </a>

        </div>

      </div>
    </section>

    <!-- Секция "Польза партнерства" -->
    <section class="benefits">
      <div class="benefits__inner">
        <h2 class="benefits__title">{{ $t['partners_page']['benefits_title'] }}</h2>

        <div class="benefits__grid">
          <div class="benefits__item">
            <div class="benefits__icon">
              <img src="{{ asset('assets/icons/partners-1.svg') }}" alt="{{ $t['partners_page']['benefit_1_title'] }}" />
            </div>
            <h3 class="benefits__item-title">{{ $t['partners_page']['benefit_1_title'] }}</h3>
            <p class="benefits__item-text">{{ $t['partners_page']['benefit_1_text'] }}</p>
          </div>

          <div class="benefits__item">
            <div class="benefits__icon">
              <img src="{{ asset('assets/icons/partners-2.svg') }}" alt="{{ $t['partners_page']['benefit_2_title'] }}" />
            </div>
            <h3 class="benefits__item-title">{{ $t['partners_page']['benefit_2_title'] }}</h3>
            <p class="benefits__item-text">{{ $t['partners_page']['benefit_2_text'] }}</p>
          </div>

          <div class="benefits__item">
            <div class="benefits__icon">
              <img src="{{ asset('assets/icons/partners-3.svg') }}" alt="{{ $t['partners_page']['benefit_3_title'] }}" />
            </div>
            <h3 class="benefits__item-title">{{ $t['partners_page']['benefit_3_title'] }}</h3>
            <p class="benefits__item-text">{{ $t['partners_page']['benefit_3_text'] }}</p>
          </div>

          <div class="benefits__item">
            <div class="benefits__icon">
              <img src="{{ asset('assets/icons/partners-4.svg') }}" alt="{{ $t['partners_page']['benefit_4_title'] }}" />
            </div>
            <h3 class="benefits__item-title">{{ $t['partners_page']['benefit_4_title'] }}</h3>
            <p class="benefits__item-text">{{ $t['partners_page']['benefit_4_text'] }}</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Секция "Станьте партнером" -->
    <section class="become-partner">
      <div class="become-partner__inner">
        <h2 class="become-partner__title">{{ $t['partners_page']['become_partner_title'] }}</h2>

        <div class="become-partner__form-wrapper">
          <p class="become-partner__subtitle">{{ $t['partners_page']['become_partner_subtitle'] }}</p>

          <form class="become-partner__form">
            <div class="become-partner__inputs">
              <input type="text" placeholder="{{ $t['partners_page']['form_name'] }}" class="become-partner__input" required />
              <input type="email" placeholder="{{ $t['partners_page']['form_email'] }}" class="become-partner__input" required />
              <input type="tel" placeholder="{{ $t['partners_page']['form_phone'] }}" class="become-partner__input" required />
            </div>

            <button type="submit" class="become-partner__submit">
              {{ $t['partners_page']['form_submit'] }}
            </button>
          </form>
        </div>
      </div>
    </section>

    @include('partials.footer', ['t' => $t, 'lang' => $lang])
</main>
@endsection

@push('styles')
<style>
    /* ── Слой адаптивной безопасности (общий для всех страниц) ── */
    .site-main { display: flex; flex-direction: column; min-height: 100vh; overflow-x: clip; }
    .site-main > section { flex: 0 0 auto; }
    .site-main > section:first-of-type { flex: 1 0 auto; }
    .site-main img, .site-main iframe, .site-main video { max-width: 100%; }
    .site-main *, .site-main *::before, .site-main *::after { box-sizing: border-box; }

.partners {
    width: 100%;
    background: #FFFFFF;
    padding: 0 6vw;
  }

  .partners__inner {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: 50px;
    width: 100%;
    margin: 0 auto;
  }

  .breadcrumbs {
    display: flex;
    flex-direction: row;
    align-items: center;
    gap: 10px;
  }

  .breadcrumbs__item {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 20px;
    height: 20px;
    transition: opacity 0.2s;
  }

  .breadcrumbs__item:hover {
    opacity: 0.7;
  }

  .breadcrumbs__separator {
    flex-shrink: 0;
  }

  .breadcrumbs__current {
    font-family: 'Montserrat', sans-serif;
    font-weight: 400;
    font-size: 13px;
    line-height: 16px;
    color: #2B2B2B;
  }

  .partners__content {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: 60px;
    width: 100%;
  }

  /* Заголовок */
  .partners__title {
    font-family: 'Montserrat', sans-serif;
    font-weight: 500;
    font-size: 48px;
    line-height: 110%;
    text-align: center;
    color: #000000;
    margin: 0;
    width: 100%;
  }

  /* Сетка партнеров */
  .partners__grid {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 30px;
    width: 100%;
  }

  .partners__row {
    display: flex;
    flex-direction: row;
    align-items: center;
    gap: 30px;
    width: 100%;
    justify-content: space-between;
  }

  .partners__card {
    width: 100%;
    max-width: 690px;
    height: 330px;
    background: #FFFFFF;
    box-shadow: 0px 0px 4px rgba(0, 0, 0, 0.3);
    border-radius: 7px;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 40px;
    flex: 1 1 690px;
  }

  .partners__card-content {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 30px;
    max-width: 492px;
  }

  .partners__logo {
    max-width: 218px;
    max-height: 60px;
    object-fit: contain;
  }

  .partners__description {
    font-family: 'Montserrat', sans-serif;
    font-weight: 400;
    font-size: 18px;
    line-height: 130%;
    text-align: center;
    letter-spacing: -0.01em;
    color: #000000;
    margin: 0;
  }

  .partners__cta {
    width: 330px;
    height: 85px;
    background: #1C508F;
    box-shadow: 0px 0px 4px rgba(0, 0, 0, 0.3);
    border-radius: 9px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: 'Montserrat', sans-serif;
    font-weight: 400;
    font-size: 18px;
    line-height: 130%;
    text-align: center;
    letter-spacing: -0.01em;
    color: #FFFFFF;
    text-decoration: none;
    transition: background 0.2s;
  }

  .partners__cta:hover {
    background: #174480;
  }

  /* Секция "Польза партнерства" */
  .benefits {
    width: 100%;
    background: #FFFFFF;
    padding: 0 6vw;
  }

  .benefits__inner {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 60px;
    width: 100%;
    margin: 0 auto;
  }

  .benefits__title {
    font-family: 'Montserrat', sans-serif;
    font-weight: 500;
    font-size: 48px;
    line-height: 110%;
    text-align: center;
    color: #000000;
    margin: 0;
    width: 100%;
  }

  .benefits__grid {
    display: flex;
    flex-direction: row;
    align-items: flex-start;
    gap: 30px;
    width: 100%;
    justify-content: space-between;
  }

  .benefits__item {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 32px;
    width: 100%;
    max-width: 328px;
    flex: 1 1 328px;
  }

  .benefits__icon {
    width: 90px;
    height: 90px;
    background: #FFFFFF;
    box-shadow: 0px 0px 4px rgba(0, 0, 0, 0.3);
    border-radius: 58.5px;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .benefits__icon img {
    width: 57px;
    height: 57px;
    object-fit: contain;
  }

  .benefits__item-title {
    font-family: 'Montserrat', sans-serif;
    font-weight: 500;
    font-size: 30px;
    line-height: 110%;
    text-align: center;
    letter-spacing: -0.01em;
    color: #000000;
    margin: 0;
  }

  .benefits__item-text {
    font-family: 'Montserrat', sans-serif;
    font-weight: 400;
    font-size: 18px;
    line-height: 130%;
    text-align: center;
    letter-spacing: -0.01em;
    color: #000000;
    margin: 0;
  }

  /* Секция "Станьте партнером" */
  .become-partner {
    width: 100%;
    background: #FFFFFF;
    padding: 0 6vw;
  }

  .become-partner__inner {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 60px;
    width: 100%;
    max-width: 690px;
    margin: 0 auto;
  }

  .become-partner__title {
    font-family: 'Montserrat', sans-serif;
    font-weight: 500;
    font-size: 48px;
    line-height: 110%;
    text-align: center;
    color: #000000;
    margin: 0;
  }

  .become-partner__form-wrapper {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 50px;
    width: 100%;
  }

  .become-partner__subtitle {
    font-family: 'Raleway', sans-serif;
    font-weight: 400;
    font-size: 18px;
    line-height: 150%;
    text-align: center;
    color: #000000;
    margin: 0;
  }

  .become-partner__form {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 50px;
    width: 100%;
  }

  .become-partner__inputs {
    display: flex;
    flex-direction: row;
    align-items: flex-start;
    gap: 30px;
    width: 100%;
    flex-wrap: wrap;
  }

  .become-partner__input {
    box-sizing: border-box;
    width: 330px;
    height: 60px;
    border: 1px solid #000000;
    padding: 18px 20px;
    font-family: 'Montserrat', sans-serif;
    font-weight: 400;
    font-size: 18px;
    line-height: 130%;
    letter-spacing: -0.01em;
    color: #151515;
  }

  .become-partner__input::placeholder {
    color: #151515;
  }

  .become-partner__submit {
    width: 330px;
    height: 85px;
    background: #1C508F;
    box-shadow: 0px 0px 4px rgba(0, 0, 0, 0.3);
    border-radius: 9px;
    border: none;
    font-family: 'Montserrat', sans-serif;
    font-weight: 400;
    font-size: 18px;
    line-height: 130%;
    text-align: center;
    letter-spacing: -0.01em;
    color: #FFFFFF;
    cursor: pointer;
    transition: background 0.2s;
  }

  .become-partner__submit:hover {
    background: #174480;
  }

  /* Мобильная версия */
  @media (max-width: 768px) {
    .partners__inner {
      gap: 35px;
    }

    .partners__title,
    .benefits__title,
    .become-partner__title {
      font-size: 32px;
    }

    .partners__row {
      flex-direction: column;
    }

    .partners__card {
      max-width: 100%;
      height: auto;
      min-height: 330px;
    }

    .benefits__grid {
      flex-direction: column;
      align-items: center;
    }

    .become-partner__inputs {
      flex-direction: column;
      align-items: center;
    }

    .become-partner__input {
      width: 100%;
      max-width: 330px;
    }
  }

  /* ── Адаптивная сетка партнёров (заменяет костыль 2×2 из .map) ── */
  .partners__grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 30px; width: 100%; }
  .partners__card { display: flex; align-items: center; justify-content: center; }
  .partners__card-content { display: flex; flex-direction: column; align-items: center; gap: 16px; text-align: center; width: 100%; }
  .partners__logo { max-width: 100%; height: auto; object-fit: contain; }
  @media (max-width: 900px) { .partners__grid { gap: 20px; } }
  @media (max-width: 640px) { .partners__grid { grid-template-columns: 1fr; gap: 16px; } }

</style>
@endpush