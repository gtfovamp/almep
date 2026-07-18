{{--
    Партиал секции "Бесплатная консультация" (главная страница).

    В отличие от partials/consultation-modal.blade.php, это НЕ попап,
    а обычная секция, встроенная прямо в разметку страницы (перед
    футером, судя по макету). Использует те же ключи переводов
    $t['consultation'][...], что и модалка — так что если модалка
    у тебя уже подключена и переводы заполнены, тут ничего добавлять
    не нужно.

    ПОДКЛЮЧЕНИЕ:
        @includeIf('partials.consultation-section', ['t'=>$t,'lang'=>$lang])

    Форма отправляется на тот же POST /api/consultations, что и в модалке.
    Если поля на бэке называются иначе — поправь name="" у input'ов
    и ключи в payload в скрипте ниже.

    Фон — картинка + тёмный градиент (как .footer__bg в footer.blade.php).
    По умолчанию ждёт файл /assets/images/consultation-bg.png — если
    у тебя другое имя/путь, поправь background-image в стилях.

    ─── Раскладка (по референсу макета) ───
    Имя и E-mail — в один ряд, узкие поля.
    Телефон — под ними, той же ширины, в левой колонке.
    Кнопка — под телефоном, той же ширины.
    Контакты (телефон/email + соцсети) — отдельным блоком справа,
    прижаты к низу формы.
--}}
@if(!empty($t['consultation']))
<section class="consult">
    <div class="consult__bg"></div>

    <div class="consult__inner">

        <h2 class="consult__title">{{ $t['consultation']['title'] ?? 'Бесплатная консультация' }}</h2>

        <p class="consult__text">
            {{ $t['consultation']['section_text'] ?? $t['consultation']['text'] ?? 'Ниже оставьте свои контакты, чтобы мы смогли связаться с вами для полной консультации' }}
        </p>

        <div class="consult__row">

            <form class="consult-form" id="consultSectionForm" novalidate>
                <div class="consult-form__grid">

                    <label class="consult-form__field">
                        <span class="consult-form__label">{{ $t['consultation']['name_label'] ?? 'Ваше имя' }}</span>
                        <input type="text" name="name" required autocomplete="name"
                               class="consult-form__input"
                               placeholder="{{ $t['consultation']['name_placeholder'] ?? 'Ваше имя' }}" />
                    </label>

                    <label class="consult-form__field">
                        <span class="consult-form__label">{{ $t['consultation']['email_label'] ?? 'E-mail' }}</span>
                        <input type="email" name="email" required autocomplete="email"
                               class="consult-form__input"
                               placeholder="{{ $t['consultation']['email_placeholder'] ?? 'E-mail' }}" />
                    </label>

                    <label class="consult-form__field consult-form__field--phone">
                        <span class="consult-form__label">{{ $t['consultation']['phone_label'] ?? 'Ваш номер телефона' }}</span>
                        <input
                            type="tel"
                            name="phone"
                            required
                            autocomplete="tel"
                            class="consult-form__input"
                            placeholder="{{ $t['consultation']['phone_label'] ?? 'Ваш номер телефона' }}"
                            data-consult-phone-mask
                        />
                    </label>

                    <button type="submit" class="consult-form__submit">
                        <span class="consult-form__submit-text">{{ $t['consultation']['submit'] ?? 'Отправить' }}</span>
                        <span class="consult-form__spinner" aria-hidden="true"></span>
                    </button>

                </div>

                <div class="consult-form__status" data-consult-status role="status" aria-live="polite"></div>
            </form>

            <div class="consult__contacts">
                <div class="consult__contacts-info">
                    <a href="tel:{{ str_replace(' ', '', $t['consultation']['phone'] ?? '') }}" class="consult__phone">
                        {{ $t['consultation']['phone'] ?? '' }}
                    </a>
                    <a href="mailto:{{ $t['consultation']['email'] ?? '' }}" class="consult__email">
                        {{ $t['consultation']['email'] ?? '' }}
                    </a>
                </div>

                <div class="consult__socials">
                    <a href="#" class="consult__social-link" aria-label="{{ $t['consultation']['social_instagram'] ?? '' }}">
                        <img src="{{ asset('assets/icons/instagram.svg') }}" alt="{{ $t['consultation']['social_instagram'] ?? '' }}" width="24" height="24" />
                    </a>
                    <a href="#" class="consult__social-link" aria-label="{{ $t['consultation']['social_youtube'] ?? '' }}">
                        <img src="{{ asset('assets/icons/youtube.svg') }}" alt="{{ $t['consultation']['social_youtube'] ?? '' }}" width="24" height="24" />
                    </a>
                    <a href="#" class="consult__social-link" aria-label="{{ $t['consultation']['social_facebook'] ?? '' }}">
                        <img src="{{ asset('assets/icons/facebook.svg') }}" alt="{{ $t['consultation']['social_facebook'] ?? '' }}" width="24" height="24" />
                    </a>
                </div>
            </div>

        </div>

    </div>
</section>

<style>
    .consult {
        --accent: #1C508F;
        --accent-dark: #123863;

        position: relative;
        width: 100%;
        overflow: hidden;
        background: #BCBCBC;
    }
    .consult *, .consult *::before, .consult *::after { box-sizing: border-box; }

    .consult__bg {
        position: absolute;
        inset: 0;
        background:
            linear-gradient(0deg, rgba(0, 0, 0, 0.73), rgba(0, 0, 0, 0.73)),
            url('/assets/images/consultation-bg.png') center center / cover no-repeat;
        z-index: 0;
    }

    .consult__inner {
        position: relative;
        z-index: 1;
        width: 100%;
        margin: 0 auto;
        padding: clamp(90px, 12vw, 160px) clamp(16px, 6vw, 95px);
        display: flex;
        flex-direction: column;
        align-items: flex-start;
    }

    .consult__title {
        margin: 0;
        max-width: 760px;
        width: 100%;
        font-family: 'Montserrat', sans-serif;
        font-weight: 500;
        font-size: clamp(30px, 3.6vw, 48px);
        line-height: 110%;
        color: #FFFFFF;
    }

    .consult__text {
        margin: 26px 0 0;
        max-width: 520px;
        font-family: 'Montserrat', sans-serif;
        font-weight: 400;
        font-size: 18px;
        line-height: 130%;
        letter-spacing: -0.01em;
        color: #FFFFFF;
    }

    /* ── Ряд: форма слева, контакты справа, прижаты к низу ── */
    .consult__row {
        display: flex;
        flex-direction: row;
        flex-wrap: wrap;
        align-items: flex-end;
        justify-content: space-between;
        gap: 24px;
        width: 100%;
        margin-top: 34px;
    }

    /* ── Форма: узкая 2-колоночная сетка ── */
    .consult-form { flex: 0 1 auto; }

    .consult-form__grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(180px, 270px));
        gap: 16px 18px;
    }

    .consult-form__field {
        display: flex;
        flex-direction: column;
        box-sizing: border-box;
        width: 100%;
        height: 56px;
        border: 1px solid rgba(255, 255, 255, 0.7);
        border-radius: 6px;
        padding: 0 20px;
        justify-content: center;
        transition: border-color 0.2s;
    }

    .consult-form__field:focus-within { border-color: #A4C5EE; }

    .consult-form__label {
        position: absolute;
        width: 1px; height: 1px;
        overflow: hidden;
        clip: rect(0 0 0 0);
        white-space: nowrap;
    }

    .consult-form__input {
        width: 100%;
        border: none;
        background: transparent;
        outline: none;
        font-family: 'Montserrat', sans-serif;
        font-weight: 400;
        font-size: 16px;
        letter-spacing: -0.01em;
        color: #FFFFFF;
    }

    .consult-form__input::placeholder { color: rgba(255, 255, 255, 0.85); }

    .consult-form__submit {
        position: relative;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        width: 100%;
        height: 56px;
        background: var(--accent);
        box-shadow: 0px 0px 4px rgba(0, 0, 0, 0.3);
        border: none;
        border-radius: 9px;
        font-family: 'Montserrat', sans-serif;
        font-weight: 400;
        font-size: 17px;
        letter-spacing: -0.01em;
        color: #FFFFFF;
        cursor: pointer;
        transition: background-color 0.2s, opacity 0.2s;
    }

    .consult-form__submit:hover { background: var(--accent-dark); }
    .consult-form__submit:disabled { opacity: 0.7; cursor: not-allowed; }

    .consult-form__spinner {
        display: none;
        width: 14px;
        height: 14px;
        border: 2px solid rgba(255, 255, 255, 0.4);
        border-top-color: #FFFFFF;
        border-radius: 50%;
        animation: consult-spin 0.7s linear infinite;
    }

    .consult-form.is-submitting .consult-form__spinner { display: inline-block; }
    .consult-form.is-submitting .consult-form__submit-text { opacity: 0.85; }

    @keyframes consult-spin { to { transform: rotate(360deg); } }

    .consult-form__status {
        display: none;
        margin-top: 14px;
        font-family: 'Montserrat', sans-serif;
        font-weight: 500;
        font-size: 13px;
        line-height: 140%;
        padding: 12px 14px;
        border-radius: 6px;
        max-width: 460px;
    }

    .consult-form__status.is-visible { display: block; }
    .consult-form__status--success { background: rgba(66, 179, 122, 0.18); color: #B8F0D2; }
    .consult-form__status--error { background: rgba(224, 133, 122, 0.18); color: #F0C4BC; }

    .consult-form.is-submitting .consult-form__input,
    .consult-form.is-success .consult-form__input {
        pointer-events: none;
        opacity: 0.6;
    }

    /* ── Контакты + соцсети (справа, прижаты к низу формы) ── */
    .consult__contacts {
        display: flex;
        flex-direction: row;
        align-items: flex-start;
        gap: 20px;
        flex: 0 0 auto;
    }

    .consult__contacts-info {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .consult__phone,
    .consult__email {
        font-family: 'Montserrat', sans-serif;
        font-weight: 600;
        font-size: 17px;
        line-height: 120%;
        color: #FFFFFF;
        white-space: nowrap;
        transition: opacity 0.2s;
    }

    .consult__phone:hover,
    .consult__email:hover { opacity: 0.75; }

    .consult__socials {
        display: flex;
        flex-direction: row;
        align-items: center;
        gap: 12px;
    }

    .consult__social-link {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 42px;
        height: 42px;
        border: 1px solid rgba(255, 255, 255, 0.7);
        border-radius: 50%;
        transition: opacity 0.2s, border-color 0.2s;
    }

    .consult__social-link:hover { opacity: 0.75; border-color: #FFFFFF; }

    .consult__social-link img {
        width: 18px;
        height: 18px;
        object-fit: contain;
        filter: brightness(0) invert(1);
    }

    /* === МОБИЛЬНАЯ ВЕРСИЯ === */
    @media (max-width: 768px) {
        .consult__inner { padding: 36px 20px; }

        .consult__title { font-size: 22px; }

        .consult__text { max-width: 100%; font-size: 14px; margin-top: 12px; }

        .consult__row {
            flex-direction: column;
            align-items: flex-start;
            gap: 24px;
            margin-top: 18px;
        }

        .consult-form { width: 100%; }

        .consult-form__grid {
            grid-template-columns: 1fr;
            gap: 12px;
        }

        .consult-form__field--phone { grid-column: 1; }

        .consult__contacts { width: 100%; }
    }
</style>

<script>
(function () {
    var form = document.getElementById('consultSectionForm');
    if (!form) return;

    var statusEl = form.querySelector('[data-consult-status]');
    var submitBtn = form.querySelector('.consult-form__submit');
    var phoneInput = form.querySelector('[data-consult-phone-mask]');

    function formatPhone(value) {
        var cleaned = value.replace(/[^\d+]/g, '');
        if (!cleaned.startsWith('+')) {
            cleaned = cleaned.startsWith('994') ? ('+' + cleaned) : (cleaned.length > 0 ? ('+994' + cleaned) : cleaned);
        }
        var digits = cleaned.replace(/^\+994/, '').replace(/\D/g, '').substring(0, 9);
        var formatted = '+994';
        if (digits.length > 0) formatted += ' ' + digits.substring(0, 2);
        if (digits.length > 2) formatted += ' ' + digits.substring(2, 5);
        if (digits.length > 5) formatted += ' ' + digits.substring(5, 7);
        if (digits.length > 7) formatted += ' ' + digits.substring(7, 9);
        return formatted.trim();
    }

    if (phoneInput) {
        phoneInput.addEventListener('input', function () {
            var formatted = formatPhone(phoneInput.value);
            if (formatted !== phoneInput.value) {
                phoneInput.value = formatted;
                phoneInput.setSelectionRange(formatted.length, formatted.length);
            }
        });

        phoneInput.addEventListener('focus', function () {
            if (phoneInput.value.trim() === '') {
                phoneInput.value = '+994 ';
                phoneInput.setSelectionRange(5, 5);
            }
        });

        phoneInput.addEventListener('blur', function () {
            if (phoneInput.value.trim() === '+994') phoneInput.value = '';
        });

        phoneInput.addEventListener('paste', function () {
            setTimeout(function () {
                var formatted = formatPhone(phoneInput.value);
                phoneInput.value = formatted;
                phoneInput.setSelectionRange(formatted.length, formatted.length);
            }, 0);
        });
    }

    function setStatus(type, text) {
        statusEl.textContent = text;
        statusEl.className = 'consult-form__status is-visible consult-form__status--' + type;
    }

    function clearStatus() {
        statusEl.textContent = '';
        statusEl.className = 'consult-form__status';
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
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
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
                    form.reset();
                    form.classList.remove('is-success');
                    submitBtn.disabled = false;
                    clearStatus();
                }, 2200);
            })
            .catch(function () {
                form.classList.remove('is-submitting');
                submitBtn.disabled = false;
                setStatus('error', '{{ $t['consultation']['error'] ?? 'Не удалось отправить. Попробуйте ещё раз' }}');
            });
    });
})();
</script>
@endif