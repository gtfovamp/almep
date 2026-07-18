{{--
    Партиал попапа "Бесплатная консультация".

    ПОДКЛЮЧЕНИЕ (один раз на сайт, обычно перед </body> в layouts/app.blade.php):
        @include('partials.consultation-modal')

    ОТКРЫТИЕ ИЗ ЛЮБОГО МЕСТА САЙТА — просто повесь атрибут data-open-consultation
    на любую кнопку или ссылку, JS сам её подхватит:
        <button type="button" data-open-consultation>Бесплатная консультация</button>
        <a href="#" data-open-consultation>Запросить консультацию</a>

    Либо программно из своего JS:
        window.AlmepConsultation.open();

    Форма отправляется через fetch на POST /api/consultations (это уже
    существующий у тебя публичный роут ConsultationController::store).
    Если поля на бэке называются иначе — поправь name="" у input'ов ниже
    и ключи в объекте payload в скрипте.

    @once оборачивает всё содержимое партиала: если случайно подключишь
    его дважды на одной странице, второй раз просто ничего не выведется
    и не будет дублей id/скриптов.
--}}
@once
<div
    class="consultation-modal"
    id="consultationModal"
    role="dialog"
    aria-modal="true"
    aria-labelledby="consultationModalTitle"
    hidden
>
    {{-- ... вёрстка модалки до формы без изменений ... --}}
    <div class="consultation-modal__overlay" data-consultation-close></div>
    <div class="consultation-modal__panel">
        <button type="button" class="consultation-modal__close" data-consultation-close aria-label="{{ $t['consultation']['close'] ?? 'Закрыть' }}">
            <svg width="18" height="18" viewBox="0 0 18 18" fill="none" aria-hidden="true">
                <path d="M1 1L17 17M17 1L1 17" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
            </svg>
        </button>

        <div class="consultation-modal__body">
            <span class="consultation-modal__eyebrow">{{ $t['consultation']['eyebrow'] ?? 'Almep Trading' }}</span>
            <h2 class="consultation-modal__title" id="consultationModalTitle">
                {{ $t['consultation']['title'] ?? 'Бесплатная консультация' }}
            </h2>
            <p class="consultation-modal__text">
                {{ $t['consultation']['text'] ?? 'Оставьте свои контакты, и мы свяжемся с вами для полной консультации' }}
            </p>

            <form class="consultation-form" id="consultationForm" novalidate>
                <div class="consultation-form__row">
                    <label class="consultation-form__field">
                        <span class="consultation-form__label">{{ $t['consultation']['name_label'] ?? 'Ваше имя' }}</span>
                        <input type="text" name="name" required autocomplete="name"
                               class="consultation-form__input"
                               placeholder="{{ $t['consultation']['name_placeholder'] ?? 'Ваше имя' }}" />
                    </label>

                    <label class="consultation-form__field">
                        <span class="consultation-form__label">{{ $t['consultation']['email_label'] ?? 'E-mail' }}</span>
                        <input type="email" name="email" required autocomplete="email"
                               class="consultation-form__input"
                               placeholder="{{ $t['consultation']['email_placeholder'] ?? 'E-mail' }}" />
                    </label>

                    <label class="consultation-form__field">
                        <span class="consultation-form__label">{{ $t['consultation']['phone_label'] ?? 'Ваш номер телефона' }}</span>
                        <input
                            type="tel"
                            name="phone"
                            required
                            autocomplete="tel"
                            class="consultation-form__input"
                            placeholder="+994 50 123 45 67"
                            data-phone-mask
                        />
                    </label>
                </div>

                <button type="submit" class="consultation-form__submit">
                    <span class="consultation-form__submit-text">{{ $t['consultation']['submit'] ?? 'Запросить консультацию' }}</span>
                    <span class="consultation-form__spinner" aria-hidden="true"></span>
                </button>

                <p class="consultation-form__note">
                    {{ $t['consultation']['note'] ?? 'Отправляя форму, вы соглашаетесь на обработку персональных данных' }}
                </p>

                <div class="consultation-form__status" data-consultation-status role="status" aria-live="polite"></div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
    .consultation-modal {
    --accent: #1C508F;
    --accent-dark: #123863;
    --radius-lg: 8px;
    --radius-md: 7px;
    --card-shadow: 0px 0px 4px rgba(0, 0, 0, 0.3);

    position: fixed;
    inset: 0;
    z-index: 1000;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: clamp(16px, 4vw, 40px);
  }

  .consultation-modal[hidden] { display: none; }

  .consultation-modal__overlay {
    position: absolute;
    inset: 0;
    background: rgba(8, 15, 26, 0.6);
    backdrop-filter: blur(2px);
    opacity: 0;
    transition: opacity 0.25s ease;
  }

  .consultation-modal.is-open .consultation-modal__overlay { opacity: 1; }

  .consultation-modal__panel {
    position: relative;
    width: 100%;
    max-width: 560px;
    max-height: calc(100vh - 32px);
    overflow-y: auto;
    background: linear-gradient(155deg, #0F2A4D 0%, #0A1E38 100%);
    border-radius: var(--radius-lg);
    box-shadow: 0 24px 60px rgba(0, 0, 0, 0.35);
    padding: clamp(28px, 4vw, 48px);
    transform: translateY(16px) scale(0.98);
    opacity: 0;
    transition: transform 0.28s ease, opacity 0.28s ease;
  }

  .consultation-modal.is-open .consultation-modal__panel {
    transform: translateY(0) scale(1);
    opacity: 1;
  }

  .consultation-modal__close {
    position: absolute;
    top: clamp(16px, 2.5vw, 24px);
    right: clamp(16px, 2.5vw, 24px);
    width: 36px;
    height: 36px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: rgba(255, 255, 255, 0.08);
    border: none;
    border-radius: 50%;
    color: #FFFFFF;
    cursor: pointer;
    transition: background-color 0.2s, transform 0.2s;
  }

  .consultation-modal__close:hover {
    background: rgba(255, 255, 255, 0.16);
    transform: rotate(90deg);
  }

  .consultation-modal__body {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: clamp(20px, 2.5vw, 28px);
  }

  .consultation-modal__eyebrow {
    display: inline-flex;
    align-items: center;
    padding: 8px 16px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 17px;
    font-family: 'Raleway', sans-serif;
    font-weight: 400;
    font-size: 13px;
    letter-spacing: 0.02em;
    text-transform: uppercase;
    color: #A4C5EE;
  }

  .consultation-modal__title {
    margin: 0;
    font-family: 'Montserrat', sans-serif;
    font-weight: 500;
    font-size: clamp(26px, 3.4vw, 34px);
    line-height: 110%;
    color: #FFFFFF;
  }

  .consultation-modal__text {
    margin: -12px 0 0;
    font-family: 'Montserrat', sans-serif;
    font-weight: 400;
    font-size: clamp(15px, 1.4vw, 17px);
    line-height: 130%;
    letter-spacing: -0.01em;
    color: rgba(255, 255, 255, 0.78);
  }

  /* ── Форма ── */
  .consultation-form {
    display: flex;
    flex-direction: column;
    gap: 18px;
    width: 100%;
  }

  .consultation-form__row {
    display: flex;
    flex-direction: column;
    gap: 14px;
    width: 100%;
  }

  .consultation-form__field {
    display: flex;
    flex-direction: column;
    width: 100%;
  }

  /* Лейбл виден только для скринридеров/фокуса — сама форма визуально
     держится на плейсхолдерах, как в оригинальном макете. */
  .consultation-form__label {
    position: absolute;
    width: 1px; height: 1px;
    overflow: hidden;
    clip: rect(0 0 0 0);
    white-space: nowrap;
  }

  .consultation-form__input {
    box-sizing: border-box;
    width: 100%;
    height: 56px;
    padding: 0 18px;
    background: rgba(255, 255, 255, 0.04);
    border: 1px solid rgba(255, 255, 255, 0.35);
    border-radius: 5px;
    font-family: 'Montserrat', sans-serif;
    font-weight: 400;
    font-size: 16px;
    letter-spacing: -0.01em;
    color: #FFFFFF;
    transition: border-color 0.2s, background-color 0.2s;
  }

  .consultation-form__input::placeholder { color: rgba(255, 255, 255, 0.55); }

  .consultation-form__input:hover { border-color: rgba(255, 255, 255, 0.55); }

  .consultation-form__input:focus-visible {
    outline: none;
    border-color: #A4C5EE;
    background: rgba(255, 255, 255, 0.08);
  }

  .consultation-form__input:invalid:not(:placeholder-shown) {
    border-color: #E0857A;
  }

  .consultation-form__submit {
    position: relative;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    width: 100%;
    height: 58px;
    margin-top: 4px;
    background: var(--accent);
    box-shadow: var(--card-shadow);
    border: none;
    border-radius: 9px;
    font-family: 'Montserrat', sans-serif;
    font-weight: 500;
    font-size: 17px;
    letter-spacing: -0.01em;
    color: #FFFFFF;
    cursor: pointer;
    transition: background-color 0.2s, opacity 0.2s, transform 0.15s;
  }

  .consultation-form__submit:hover { background: var(--accent-dark); }
  .consultation-form__submit:active { transform: scale(0.99); }

  .consultation-form__submit:focus-visible {
    outline: 2px solid #A4C5EE;
    outline-offset: 3px;
  }

  .consultation-form__submit:disabled {
    opacity: 0.7;
    cursor: not-allowed;
  }

  .consultation-form__spinner {
    display: none;
    width: 16px;
    height: 16px;
    border: 2px solid rgba(255, 255, 255, 0.4);
    border-top-color: #FFFFFF;
    border-radius: 50%;
    animation: consultation-spin 0.7s linear infinite;
  }

  .consultation-form.is-submitting .consultation-form__spinner { display: inline-block; }
  .consultation-form.is-submitting .consultation-form__submit-text { opacity: 0.85; }

  @keyframes consultation-spin {
    to { transform: rotate(360deg); }
  }

  .consultation-form__note {
    margin: 0;
    font-family: 'Raleway', sans-serif;
    font-weight: 400;
    font-size: 12px;
    line-height: 140%;
    color: rgba(255, 255, 255, 0.5);
  }

  .consultation-form__status {
    display: none;
    font-family: 'Montserrat', sans-serif;
    font-weight: 500;
    font-size: 14px;
    line-height: 140%;
    padding: 14px 16px;
    border-radius: var(--radius-md);
  }

  .consultation-form__status.is-visible { display: block; }

  .consultation-form__status--success {
    background: rgba(66, 179, 122, 0.15);
    color: #7FE0AE;
  }

  .consultation-form__status--error {
    background: rgba(224, 133, 122, 0.15);
    color: #F0A79C;
  }

  /* Пока идёт отправка — блокируем повторные клики визуально */
  .consultation-form.is-submitting .consultation-form__input,
  .consultation-form.is-success .consultation-form__input {
    pointer-events: none;
    opacity: 0.6;
  }

  body.consultation-modal-open { overflow: hidden; }

  @media (prefers-reduced-motion: reduce) {
    .consultation-modal__overlay,
    .consultation-modal__panel,
    .consultation-modal__close,
    .consultation-form__submit {
      transition: none !important;
    }
    .consultation-form__spinner { animation: none; }
  }

  @media (max-width: 640px) {
    .consultation-modal { padding: 0; align-items: flex-end; }

    .consultation-modal__panel {
      max-width: 100%;
      width: 100%;
      max-height: 92vh;
      border-radius: 20px 20px 0 0;
      transform: translateY(100%);
    }

    .consultation-modal.is-open .consultation-modal__panel { transform: translateY(0); }
  }
</style>
@endpush

@push('scripts')
<script>
(function () {
  function setupPhoneMask(input) {
    if (!input) return;
    function formatPhone(value) {
      var cleaned = value.replace(/[^\d+]/g, '');
      if (!cleaned.startsWith('+')) {
        if (cleaned.startsWith('994')) {
          cleaned = '+' + cleaned;
        } else {
          if (cleaned.length > 0) {
            cleaned = '+994' + cleaned;
          }
        }
      }

      var digits = cleaned.replace(/^\+994/, '').replace(/\D/g, '');
      digits = digits.substring(0, 9);

      var formatted = '+994';
      if (digits.length > 0) {
        formatted += ' ' + digits.substring(0, 2);
      }
      if (digits.length > 2) {
        formatted += ' ' + digits.substring(2, 5);
      }
      if (digits.length > 5) {
        formatted += ' ' + digits.substring(5, 7);
      }
      if (digits.length > 7) {
        formatted += ' ' + digits.substring(7, 9);
      }

      return formatted.trim();
    }

    function getCursorPosition(formatted, oldFormatted, oldCursor) {
      
      var oldRaw = oldFormatted.replace(/[^\d+]/g, '');
      var newRaw = formatted.replace(/[^\d+]/g, '');
      if (oldCursor === 0) return 0;

      var prefixOld = oldFormatted.substring(0, oldCursor);
      var digitsBefore = prefixOld.replace(/[^\d]/g, '').length;

      var count = 0;
      for (var i = 0; i < formatted.length; i++) {
        if (/\d/.test(formatted[i]) || formatted[i] === '+') {
          count++;
        }
        if (count === digitsBefore + 1) {
          return i + 1;
        }
      }
      return formatted.length; 
    }

    input.addEventListener('input', function (e) {
      var oldValue = input.value;
      var oldCursor = input.selectionStart;
      var formatted = formatPhone(oldValue);

      if (formatted !== oldValue) {
        input.value = formatted;
        var newCursor = getCursorPosition(formatted, oldValue, oldCursor);
        input.setSelectionRange(newCursor, newCursor);
      }
    });

    input.addEventListener('blur', function () {
      if (input.value.trim() === '+994') {
        input.value = '';
      }
    });

    input.addEventListener('paste', function (e) {
      setTimeout(function () {
        var formatted = formatPhone(input.value);
        if (formatted !== input.value) {
          input.value = formatted;
        }
        // Ставим курсор в конец
        var len = formatted.length;
        input.setSelectionRange(len, len);
      }, 0);
    });

    input.addEventListener('focus', function () {
      if (input.value.trim() === '') {
        input.value = '+994 ';
        input.setSelectionRange(5, 5);
      }
    });
  }

  // ──────────────────────────────────────────────
  // 2. Основная логика модального окна
  // ──────────────────────────────────────────────
  var modal = document.getElementById('consultationModal');
  if (!modal) return;

  var form = document.getElementById('consultationForm');
  var statusEl = form.querySelector('[data-consultation-status]');
  var submitBtn = form.querySelector('.consultation-form__submit');
  var phoneInput = form.querySelector('[data-phone-mask]');
  var lastFocused = null;

  // Инициализация маски
  setupPhoneMask(phoneInput);

  function open() {
    lastFocused = document.activeElement;
    modal.hidden = false;
    requestAnimationFrame(function () { modal.classList.add('is-open'); });
    document.body.classList.add('consultation-modal-open');
    var firstInput = form.querySelector('input');
    if (firstInput) setTimeout(function () { firstInput.focus(); }, 150);
    document.addEventListener('keydown', onKeydown);
  }

  function close() {
    modal.classList.remove('is-open');
    document.body.classList.remove('consultation-modal-open');
    document.removeEventListener('keydown', onKeydown);
    setTimeout(function () {
      modal.hidden = true;
      if (lastFocused && typeof lastFocused.focus === 'function') lastFocused.focus();
    }, 280);
  }

  function onKeydown(e) {
    if (e.key === 'Escape') close();
  }

  document.addEventListener('click', function (e) {
    var trigger = e.target.closest('[data-open-consultation]');
    if (trigger) {
      e.preventDefault();
      open();
      return;
    }
    if (e.target.closest('[data-consultation-close]')) close();
  });

  function setStatus(type, text) {
    statusEl.textContent = text;
    statusEl.className = 'consultation-form__status is-visible consultation-form__status--' + type;
  }

  function clearStatus() {
    statusEl.textContent = '';
    statusEl.className = 'consultation-form__status';
  }

  form.addEventListener('submit', function (e) {
    e.preventDefault();
    if (form.classList.contains('is-submitting')) return;
    clearStatus();

    var data = new FormData(form);
    var rawPhone = data.get('phone') ? data.get('phone').toString().trim() : '';
    var cleanPhone = rawPhone.replace(/[^\d+]/g, '');
    var phoneRegex = /^\+994\d{9}$/;
    if (!phoneRegex.test(cleanPhone)) {
      setStatus('error', '{{ $t['consultation']['error_phone'] ?? 'Введите корректный номер в формате +994 XX XXX XX XX' }}');
      return;
    }

    var payload = {
      name: (data.get('name') || '').toString().trim(),
      email: (data.get('email') || '').toString().trim(),
      phone: cleanPhone,
    };

    if (!payload.name || !payload.email || !payload.phone) {
      setStatus('error', '{{ $t['consultation']['error_required'] ?? 'Заполните все поля' }}');
      return;
    }

    form.classList.add('is-submitting');
    submitBtn.disabled = true;

    fetch('/api/consultations', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
      body: JSON.stringify(payload),
    })
      .then(function (res) {
        if (!res.ok) throw new Error('request_failed');
        return res.json().catch(function () { return {}; });
      })
      .then(function () {
        form.classList.remove('is-submitting');
        form.classList.add('is-success');
        setStatus('success', '{{ $t['consultation']['success'] ?? 'Спасибо! Мы свяжемся с вами в ближайшее время' }}');
        submitBtn.disabled = true;
        setTimeout(function () {
          close();
          setTimeout(function () {
            form.reset();
            form.classList.remove('is-success');
            submitBtn.disabled = false;
            clearStatus();
          }, 320);
        }, 1800);
      })
      .catch(function () {
        form.classList.remove('is-submitting');
        submitBtn.disabled = false;
        setStatus('error', '{{ $t['consultation']['error'] ?? 'Не удалось отправить. Попробуйте ещё раз' }}');
      });
  });
})();
</script>
@endpush
@endonce