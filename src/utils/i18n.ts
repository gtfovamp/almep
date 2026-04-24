export const LANGS = ['ru', 'en', 'az'] as const;
export type Lang = typeof LANGS[number];
export const DEFAULT_LANG: Lang = 'ru';

import ru from '../i18n/ru.json';
import en from '../i18n/en.json';
import az from '../i18n/az.json';

const translations = { ru, en, az };

export function getTranslations(lang: Lang) {
  return translations[lang] ?? translations[DEFAULT_LANG];
}

export function getLangFromUrl(url: URL): Lang {
  const [, lang] = url.pathname.split('/');
  return LANGS.includes(lang as Lang) ? (lang as Lang) : DEFAULT_LANG;
}

export function getLangLabel(lang: Lang): string {
  const labels = { ru: 'RU', en: 'EN', az: 'AZ' };
  return labels[lang];
}