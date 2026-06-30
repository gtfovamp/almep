<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

/*
|--------------------------------------------------------------------------
| Универсальный бутстрап (локально + cPanel)
|--------------------------------------------------------------------------
| Определяем, где лежит ядро Laravel. Поддерживаются две структуры:
|   1) cPanel:    public_html/index.php  →  ../laravel/   (код в соседней папке laravel/)
|   2) Локально:  public/index.php       →  ../          (обычная структура Laravel)
*/

$candidates = [
    __DIR__ . '/../laravel',  // cPanel (код в laravel/)
    __DIR__ . '/..',          // стандартная локальная структура
];

$base = null;
foreach ($candidates as $path) {
    if (is_file($path . '/vendor/autoload.php') && is_file($path . '/bootstrap/app.php')) {
        $base = $path;
        break;
    }
}

if ($base === null) {
    http_response_code(500);
    exit(
        "Не найден Laravel (vendor/autoload.php + bootstrap/app.php).\n" .
        "Проверял пути:\n  - " . implode("\n  - ", $candidates) . "\n\n" .
        "Локально запусти: composer install в корне проекта.\n" .
        "На cPanel убедись, что код развёрнут в папке laravel/ рядом с public_html/."
    );
}

// Режим обслуживания (artisan down)
if (file_exists($maintenance = $base . '/storage/framework/maintenance.php')) {
    require $maintenance;
}

require $base . '/vendor/autoload.php';

/** @var Application $app */
$app = require_once $base . '/bootstrap/app.php';

$app->handleRequest(Request::capture());
