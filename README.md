# AllWeb URL Shortener

Laravel-приложение для сокращения ссылок с пользовательскими пространствами, доменами, статистикой, API и административной панелью на AdminLTE.

## Стек

- PHP 8.4
- Laravel 13
- Apache 2
- MySQL 8.4
- Node.js 22 / Vite
- Docker Compose

## Требования

Для локального запуска через Docker нужны:

- Docker
- Docker Compose v2
- свободные порты `8090` и `3308`

Порты по умолчанию:

- приложение: `http://localhost:8090`
- MySQL на хосте: `127.0.0.1:3308`
- MySQL внутри docker-сети: `mysql:3306`

## Docker-структура

Основные файлы окружения:

- `docker-compose.yml` - сервисы `app` и `mysql`
- `docker/php/Dockerfile` - PHP 8.4 + Apache + расширения PHP + Node.js
- `docker/php/entrypoint.sh` - первичная подготовка контейнера
- `docker/apache/vhost.conf` - Apache VirtualHost с `public` как document root
- `docker/php/php.ini` - PHP-настройки
- `docker/mysql/my.cnf` - MySQL charset/collation
- `.env.docker` - шаблон окружения для Docker

## Быстрый запуск

1. Скопировать docker-env, если `.env` еще нет:

```bash
cp .env.docker .env
```

Если `.env` отсутствует, entrypoint контейнера также попробует создать его из `.env.docker` автоматически.

2. Собрать и запустить контейнеры:

```bash
docker compose up -d --build
```

3. Установить зависимости и подготовить приложение.

При первом запуске `docker/php/entrypoint.sh` автоматически:

- установит Composer-зависимости, если нет `vendor`
- создаст `APP_KEY`, если он пустой
- создаст storage symlink
- установит npm-зависимости, если нет `node_modules`
- соберет frontend assets, если нет `public/build/manifest.json`

Если нужно выполнить команды вручную:

```bash
docker compose exec app composer install
docker compose exec app php artisan key:generate
docker compose exec app php artisan storage:link
docker compose exec app npm install
docker compose exec app npm run build
```

4. Выполнить миграции:

```bash
docker compose exec app php artisan migrate
```

5. Открыть приложение:

```text
http://localhost:8090
```

## Настройки окружения

Docker-настройки базы данных в `.env`:

```dotenv
APP_URL=http://localhost:8090

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=allweb_urlshort
DB_USERNAME=allweb
DB_PASSWORD=secret
```

Значения MySQL из `docker-compose.yml`:

```dotenv
MYSQL_DATABASE=allweb_urlshort
MYSQL_USER=allweb
MYSQL_PASSWORD=secret
MYSQL_ROOT_PASSWORD=rootsecret
```

Для подключения к MySQL с хоста используйте:

```text
host: 127.0.0.1
port: 3308
database: allweb_urlshort
user: allweb
password: secret
```

## Полезные команды

Запустить контейнеры:

```bash
docker compose up -d
```

Остановить контейнеры:

```bash
docker compose down
```

Остановить контейнеры и удалить volume с данными MySQL:

```bash
docker compose down -v
```

Посмотреть статус контейнеров:

```bash
docker compose ps
```

Посмотреть логи приложения:

```bash
docker compose logs -f app
```

Посмотреть логи MySQL:

```bash
docker compose logs -f mysql
```

Зайти в shell app-контейнера:

```bash
docker compose exec app bash
```

Выполнить artisan-команду:

```bash
docker compose exec app php artisan <command>
```

Очистить Laravel-кэш:

```bash
docker compose exec app php artisan optimize:clear
```

Собрать frontend assets:

```bash
docker compose exec app npm run build
```

Запустить Vite dev server внутри контейнера:

```bash
docker compose exec app npm run dev -- --host 0.0.0.0
```

## Миграции и сиды

Выполнить миграции:

```bash
docker compose exec app php artisan migrate
```

Пересоздать базу с миграциями:

```bash
docker compose exec app php artisan migrate:fresh
```

Если в проекте добавлены сидеры:

```bash
docker compose exec app php artisan db:seed
```

Или вместе:

```bash
docker compose exec app php artisan migrate:fresh --seed
```

## Тесты

Запуск тестов:

```bash
docker compose exec app php artisan test
```

Composer script:

```bash
docker compose exec app composer test
```

## Работа без Docker

Для запуска без Docker нужны:

- PHP 8.4 с расширениями `bcmath`, `exif`, `gd`, `intl`, `opcache`, `pdo_mysql`, `zip`
- Composer 2
- Node.js 22+
- MySQL 8.4+
- Apache или другой web server с document root на `public`

Базовые команды:

```bash
cp .env.example .env
composer install
php artisan key:generate
npm install
npm run build
php artisan migrate
php artisan serve
```

## Troubleshooting

Если приложение не открывается:

```bash
docker compose ps
docker compose logs app
```

Если MySQL еще не готов:

```bash
docker compose logs mysql
```

Если изменились `.env`, routes, config или views:

```bash
docker compose exec app php artisan optimize:clear
```

Если сломалась сборка frontend assets:

```bash
docker compose exec app npm install
docker compose exec app npm run build
```

Если нужно полностью пересоздать окружение с чистой БД:

```bash
docker compose down -v
docker compose up -d --build
docker compose exec app php artisan migrate
```

## Важные замечания

- Не коммитьте реальные секреты в `.env`.
- Для локального Docker-окружения используйте `.env.docker`.
- Для production-окружения обязательно отключите `APP_DEBUG`, настройте реальные `APP_URL`, SMTP, Stripe credentials и безопасные пароли БД.
- `storage` и `bootstrap/cache` должны быть доступны на запись web-пользователю.
