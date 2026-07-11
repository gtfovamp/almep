@extends('layouts.app')

@php
    $title = ($t['services']['title'] ?? $t['nav']['services'] ?? 'Услуги') . ' — Almep Trading';
@endphp

@section('content')
<main class="site-main">
<div style="position: relative; background: #FFFFFF;">
      @include('partials.header', ['t' => $t, 'lang' => $lang])
    </div>

    <!-- Секция Услуги -->
    <section class="services-page">
      <div class="services-page__inner">

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
          <span class="breadcrumbs__current">{{ $t['services']['title'] }}</span>
        </nav>

        <!-- Заголовок и описание -->
        <div class="services-page__header">
          <h1 class="services-page__title">{{ $t['services']['title'] }}</h1>
          <p class="services-page__subtitle">
            {{ $t['services']['page_subtitle'] ?? 'Мы предлагаем полный спектр услуг по поставке электрооборудования и комплектующих для промышленных и коммерческих объектов' }}
          </p>
        </div>

        <!-- Сетка услуг -->
        <div class="services-page__grid">
          @foreach($t['services']['items'] as $index => $service)
            <div class="service-card" data-index="{{ $index + 1 }}">
              
              <!-- Фон с изображением -->
              <div class="service-card__bg">
                <img src="{{ asset('assets/images/service-'.($index + 1).'.png') }}" alt="{{ $service['title'] }}" />
                <div class="service-card__overlay"></div>
              </div>

              <!-- Контент -->
              <div class="service-card__content">
                
                <!-- Номер услуги -->
                <div class="service-card__number">{{ $service['number'] }}</div>

                <!-- Заголовок -->
                <h2 class="service-card__title">{{ $service['title'] }}</h2>

                <!-- Описание -->
                <p class="service-card__description">{{ $service['description'] }}</p>

                <!-- Список деталей -->
                <ul class="service-card__list">
                  @foreach(($service['details'] ?? []) as $detail)
                    <li class="service-card__list-item">
                      <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                        <path d="M13.3334 4L6.00002 11.3333L2.66669 8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                      </svg>
                      {{ $detail }}
                    </li>
                  @endforeach
                </ul>

                <!-- Кнопка -->
                <a href="/{{ $lang }}/contacts" class="service-card__btn">
                  {{ $t['services']['btn_order'] ?? 'Заказать услугу' }}
                  <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                    <path d="M4 10H16M16 10L10 4M16 10L10 16" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                  </svg>
                </a>

              </div>

            </div>
          @endforeach
        </div>

        <!-- Блок преимуществ (используем данные из WhyUs) -->
        <div class="advantages">
          <h2 class="advantages__title">
            {{ $t['whyus']['title'] }}
          </h2>
          
          <div class="advantages__grid">
            @foreach($t['whyus']['features'] as $advIndex => $advantage)
              <div class="advantage-item">
                <div class="advantage-item__icon">
                  <img src="{{ asset('assets/icons/whyus-'.($advIndex + 1).'.svg') }}" alt="{{ $advantage['title'] }}" />
                </div>
                <h3 class="advantage-item__title">
                  {{ $advantage['title'] }}
                </h3>
                <p class="advantage-item__text">
                  {{ $advantage['description'] }}
                </p>
              </div>
            @endforeach
          </div>
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

/* ═══════════════════════════════════════════════
     ОСНОВНАЯ СЕКЦИЯ
  ═══════════════════════════════════════════════ */
  .services-page {
    /* 👇 те же токены отступов, что и на странице about-page / в шапке (--hdr-px),
       чтобы боковые поля совпадали по сетке на всех страницах */
    --side-pad: var(--hdr-px, clamp(16px, 6vw, 115px));
    --v-unit: var(--hdr-py, clamp(12px, 2.9vh, 28px));

    width: 100%;
    background: #FFFFFF;
    padding: calc(var(--v-unit) * 1) var(--side-pad) calc(var(--v-unit) * 3.2);
  }

  .services-page__inner {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: 50px;
    width: 100%;
    margin: 0 auto;
  }

  /* Хлебные крошки */
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

  /* ═══════════════════════════════════════════════
     ЗАГОЛОВОК
  ═══════════════════════════════════════════════ */
  .services-page__header {
    display: flex;
    flex-direction: column;
    gap: 20px;
    width: 100%;
    max-width: 800px;
  }

  .services-page__title {
    font-family: 'Montserrat', sans-serif;
    font-weight: 500;
    font-size: 48px;
    line-height: 110%;
    color: #000000;
    margin: 0;
  }

  .services-page__subtitle {
    font-family: 'Montserrat', sans-serif;
    font-weight: 400;
    font-size: 18px;
    line-height: 130%;
    letter-spacing: -0.01em;
    color: #666666;
    margin: 0;
  }

  /* ═══════════════════════════════════════════════
     СЕТКА УСЛУГ
  ═══════════════════════════════════════════════ */
  .services-page__grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 30px;
    width: 100%;
  }

  /* ═══════════════════════════════════════════════
     КАРТОЧКА УСЛУГИ
  ═══════════════════════════════════════════════ */
  .service-card {
    position: relative;
    background: #FFFFFF;
    border: 1px solid rgba(0, 0, 0, 0.1);
    border-radius: 12px;
    overflow: hidden;
    transition: all 0.3s ease;
    min-height: 480px;
  }

  .service-card:hover {
    box-shadow: 0 12px 40px rgba(28, 80, 143, 0.15);
    transform: translateY(-4px);
  }

  /* Фон */
  .service-card__bg {
    position: absolute;
    inset: 0;
    z-index: 1;
    opacity: 0;
    transition: opacity 0.4s ease;
  }

  .service-card__bg img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }

  .service-card__overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(180deg, rgba(0, 0, 0, 0.3) 0%, rgba(0, 0, 0, 0.7) 100%);
  }

  .service-card:hover .service-card__bg {
    opacity: 1;
  }

  /* Контент */
  .service-card__content {
    position: relative;
    z-index: 2;
    display: flex;
    flex-direction: column;
    gap: 20px;
    padding: 35px 30px;
    height: 100%;
  }

  /* Номер */
  .service-card__number {
    font-family: 'Raleway', sans-serif;
    font-weight: 400;
    font-size: 18px;
    line-height: 110%;
    color: #1C508F;
    transition: color 0.3s ease;
  }

  .service-card:hover .service-card__number {
    color: #FFFFFF;
  }

  /* Заголовок */
  .service-card__title {
    font-family: 'Montserrat', sans-serif;
    font-weight: 500;
    font-size: 24px;
    line-height: 110%;
    letter-spacing: -0.01em;
    color: #000000;
    margin: 0;
    transition: color 0.3s ease;
  }

  .service-card:hover .service-card__title {
    color: #FFFFFF;
  }

  /* Описание */
  .service-card__description {
    font-family: 'Montserrat', sans-serif;
    font-weight: 400;
    font-size: 16px;
    line-height: 130%;
    letter-spacing: -0.01em;
    color: #666666;
    margin: 0;
    transition: color 0.3s ease;
  }

  .service-card:hover .service-card__description {
    color: rgba(255, 255, 255, 0.9);
  }

  /* Список */
  .service-card__list {
    display: flex;
    flex-direction: column;
    gap: 12px;
    list-style: none;
    padding: 0;
    margin: 0;
    flex: 1;
  }

  .service-card__list-item {
    display: flex;
    align-items: center;
    gap: 10px;
    font-family: 'Montserrat', sans-serif;
    font-weight: 400;
    font-size: 14px;
    line-height: 130%;
    color: #333333;
    transition: color 0.3s ease;
  }

  .service-card__list-item svg {
    flex-shrink: 0;
    color: #1C508F;
    transition: color 0.3s ease;
  }

  .service-card:hover .service-card__list-item {
    color: #FFFFFF;
  }

  .service-card:hover .service-card__list-item svg {
    color: #FFFFFF;
  }

  /* Кнопка */
  .service-card__btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 14px 24px;
    background: transparent;
    border: 1.5px solid #1C508F;
    border-radius: 8px;
    font-family: 'Montserrat', sans-serif;
    font-weight: 500;
    font-size: 15px;
    color: #1C508F;
    text-decoration: none;
    transition: all 0.3s ease;
    align-self: flex-start;
  }

  .service-card__btn svg {
    transition: transform 0.3s ease;
  }

  .service-card__btn:hover {
    background: #1C508F;
    color: #FFFFFF;
  }

  .service-card__btn:hover svg {
    transform: translateX(4px);
  }

  .service-card:hover .service-card__btn {
    background: #FFFFFF;
    border-color: #FFFFFF;
    color: #1C508F;
  }

  .service-card:hover .service-card__btn:hover {
    background: rgba(255, 255, 255, 0.9);
  }

  /* ═══════════════════════════════════════════════
     ПРЕИМУЩЕСТВА (из WhyUs)
  ═══════════════════════════════════════════════ */
  .advantages {
    display: flex;
    flex-direction: column;
    gap: 40px;
    width: 100%;
    padding: 60px 0;
  }

  .advantages__title {
    font-family: 'Montserrat', sans-serif;
    font-weight: 500;
    font-size: 40px;
    line-height: 110%;
    color: #000000;
    margin: 0;
    text-align: center;
  }

  .advantages__grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 30px;
  }

  .advantage-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 25px;
    padding: 35px 25px;
    background: #FFFFFF;
    border-radius: 12px;
    transition: all 0.3s ease;
    text-align: center;
  }

  .advantage-item:hover {
    box-shadow: 0 8px 32px rgba(28, 80, 143, 0.15);
    transform: translateY(-4px);
  }

  .advantage-item__icon {
    width: 80px;
    height: 80px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #FFFFFF;
    border-radius: 50%;
    box-shadow: 0px 0px 4px rgba(0, 0, 0, 0.3);
    flex-shrink: 0;
  }

  .advantage-item__icon img {
    width: 50px;
    height: 50px;
    object-fit: contain;
  }

  .advantage-item__title {
    font-family: 'Montserrat', sans-serif;
    font-weight: 500;
    font-size: 20px;
    line-height: 110%;
    letter-spacing: -0.01em;
    text-align: center;
    color: #000000;
    margin: 0;
  }

  .advantage-item__text {
    font-family: 'Montserrat', sans-serif;
    font-weight: 400;
    font-size: 16px;
    line-height: 130%;
    letter-spacing: -0.01em;
    text-align: center;
    color: #000000;
    margin: 0;
  }

  /* ═══════════════════════════════════════════════
     CTA БЛОК
  ═══════════════════════════════════════════════ */
  .cta-block {
    width: 100%;
    padding: 60px 80px;
    background: linear-gradient(135deg, #1C508F 0%, #2563b0 100%);
    border-radius: 16px;
    margin-top: 20px;
  }

  .cta-block__content {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 30px;
    text-align: center;
  }

  .cta-block__title {
    font-family: 'Montserrat', sans-serif;
    font-weight: 500;
    font-size: 36px;
    line-height: 110%;
    color: #FFFFFF;
    margin: 0;
  }

  .cta-block__text {
    font-family: 'Montserrat', sans-serif;
    font-weight: 400;
    font-size: 18px;
    line-height: 130%;
    color: rgba(255, 255, 255, 0.9);
    margin: 0;
    max-width: 600px;
  }

  .cta-block__buttons {
    display: flex;
    gap: 20px;
    margin-top: 10px;
  }

  .cta-block__btn {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 18px 36px;
    border-radius: 8px;
    font-family: 'Montserrat', sans-serif;
    font-weight: 500;
    font-size: 16px;
    text-decoration: none;
    transition: all 0.3s ease;
  }

  .cta-block__btn--primary {
    background: #FFFFFF;
    color: #1C508F;
  }

  .cta-block__btn--primary:hover {
    background: #F0F0F0;
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
  }

  .cta-block__btn--secondary {
    background: transparent;
    border: 2px solid #FFFFFF;
    color: #FFFFFF;
  }

  .cta-block__btn--secondary:hover {
    background: rgba(255, 255, 255, 0.1);
  }

  /* ═══════════════════════════════════════════════
     МОБИЛЬНАЯ ВЕРСИЯ
  ═══════════════════════════════════════════════ */
  @media (max-width: 768px) {
    /* боковые поля продолжают браться из --side-pad (clamp сам уменьшается),
       поэтому .services-page__inner больше не переопределяет padding вручную */
    .services-page__inner {
      gap: 35px;
    }

    .services-page__title {
      font-size: 32px;
    }

    .services-page__subtitle {
      font-size: 16px;
    }

    .services-page__grid {
      grid-template-columns: 1fr;
      gap: 25px;
    }

    .service-card {
      min-height: 420px;
    }

    .service-card__content {
      padding: 30px 25px;
    }

    .service-card__title {
      font-size: 20px;
    }

    .advantages {
      padding: 40px 0;
    }

    .advantages__title {
      font-size: 24px;
    }

    .advantages__grid {
      grid-template-columns: 1fr;
      gap: 25px;
    }

    .advantage-item {
      padding: 30px 20px;
      gap: 20px;
    }

    .advantage-item__icon {
      width: 90px;
      height: 90px;
    }

    .advantage-item__icon img {
      width: 57px;
      height: 57px;
    }

    .cta-block {
      padding: 40px 30px;
    }

    .cta-block__title {
      font-size: 28px;
    }

    .cta-block__text {
      font-size: 16px;
    }

    .cta-block__buttons {
      flex-direction: column;
      width: 100%;
    }

    .cta-block__btn {
      width: 100%;
    }
  }
</style>
@endpush