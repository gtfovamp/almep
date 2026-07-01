<?php

namespace App\Support;

class SiteI18n
{
    /** Поддерживаемые языки. Пока реально заполнен только 'en' — добавь ru/az.json, когда будут готовы. */
    public const LANGS = ['ru', 'en', 'az'];

    public const DEFAULT_LANG = 'en';

    protected static array $cache = [];

    /**
     * Возвращает массив переводов для языка (аналог getTranslations(lang) в utils/i18n.ts).
     * Если файла для языка нет — откатывается на en.json.
     */
    public static function get(string $lang): array
    {
        if (!in_array($lang, self::LANGS, true)) {
            $lang = self::DEFAULT_LANG;
        }

        if (isset(self::$cache[$lang])) {
            return self::$cache[$lang];
        }

        $path = resource_path("lang/{$lang}.json");

        if (!is_file($path)) {
            // ru.json / az.json ещё не добавлены — используем en.json как запасной вариант
            $path = resource_path('lang/en.json');
        }

        $data = json_decode(file_get_contents($path), true) ?? [];

        return self::$cache[$lang] = $data;
    }

    public static function isValidLang(string $lang): bool
    {
        return in_array($lang, self::LANGS, true);
    }

    /** RU/EN/AZ label для переключателя языка */
    public static function getLangLabel(string $lang): string
    {
        return match ($lang) {
            'ru' => 'RU',
            'en' => 'EN',
            'az' => 'AZ',
            default => strtoupper($lang),
        };
    }
}
