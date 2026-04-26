# Almep Trading - Инструкция по настройке

## Шаг 1: Создать D1 базу данных

```bash
npx wrangler d1 create almep-db
```

После выполнения команды вы получите `database_id`. Скопируйте его и замените `placeholder-will-be-created` в `wrangler.jsonc`:

```jsonc
"d1_databases": [
  {
    "binding": "DB",
    "database_name": "almep-db",
    "database_id": "ВАШ_DATABASE_ID_ЗДЕСЬ"
  }
]
```

## Шаг 2: Применить миграции

```bash
npx wrangler d1 execute almep-db --local --file=./migrations/0001_create_portfolio.sql
npx wrangler d1 execute almep-db --local --file=./migrations/0002_seed_portfolio.sql
```

Для продакшена (после деплоя):

```bash
npx wrangler d1 execute almep-db --remote --file=./migrations/0001_create_portfolio.sql
npx wrangler d1 execute almep-db --remote --file=./migrations/0002_seed_portfolio.sql
```

## Шаг 3: Локальная разработка

```bash
npm run dev
```

Сайт будет доступен на `http://localhost:4321`

## Шаг 4: Деплой на Cloudflare Pages

```bash
npm run build
npx wrangler pages deploy dist
```

## Доступ к админке

После деплоя админка будет доступна по адресу:
- Локально: `http://localhost:4321/admin/portfolio`
- Продакшен: `https://ваш-домен.pages.dev/admin/portfolio`

⚠️ **ВАЖНО**: Сейчас админка не защищена паролем. Рекомендуется добавить аутентификацию через Cloudflare Access или базовую HTTP-авторизацию.

## Структура проекта

- `/src/pages/api/portfolio/` - API endpoints для CRUD операций
- `/src/pages/admin/portfolio.astro` - Админ-панель
- `/src/pages/images/[...key].ts` - Прокси для изображений из R2
- `/migrations/` - SQL миграции для D1
- `wrangler.jsonc` - Конфигурация Cloudflare

## Что изменилось

1. ✅ Astro переключен на hybrid режим с Cloudflare адаптером
2. ✅ Настроен D1 для хранения данных портфолио
3. ✅ Настроен KV для хранения изображений (до 25MB на файл)
4. ✅ Созданы API endpoints (GET, POST, PUT, DELETE)
5. ✅ Создана админка для управления портфолио
6. ✅ Секция Portfolio теперь загружает данные из API
7. ✅ Поддержка мультиязычности в портфолио
