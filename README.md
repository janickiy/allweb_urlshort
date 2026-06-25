# AllWeb URL Shortener

A Laravel application for shortening links with user spaces, custom domains, statistics, an API, and an AdminLTE-based admin panel.

## Stack

- PHP 8.4
- Laravel 13
- Apache 2
- MySQL 8.4
- Node.js 22 / Vite
- Docker Compose

## Requirements

To run the project locally with Docker, you need:

- Docker
- Docker Compose v2
- available ports `8090` and `3308`

Default ports:

- application: `http://localhost:8090`
- MySQL on the host: `127.0.0.1:3308`
- MySQL inside the Docker network: `mysql:3306`

## Docker Structure

Main environment files:

- `docker-compose.yml` - `app` and `mysql` services
- `docker/php/Dockerfile` - PHP 8.4 + Apache + PHP extensions + Node.js
- `docker/php/entrypoint.sh` - initial container setup
- `docker/apache/vhost.conf` - Apache VirtualHost with `public` as the document root
- `docker/php/php.ini` - PHP settings
- `docker/mysql/my.cnf` - MySQL charset/collation settings
- `.env.docker` - Docker environment template

## Quick Start

1. Copy the Docker environment file if `.env` does not exist yet:

```bash
cp .env.docker .env
```

If `.env` is missing, the container entrypoint will also try to create it from `.env.docker` automatically.

2. Build and start the containers:

```bash
docker compose up -d --build
```

3. Install dependencies and prepare the application.

On the first run, `docker/php/entrypoint.sh` automatically:

- installs Composer dependencies if `vendor` does not exist
- generates `APP_KEY` if it is empty
- creates the storage symlink
- installs npm dependencies if `node_modules` does not exist
- builds frontend assets if `public/build/manifest.json` does not exist

If you need to run the commands manually:

```bash
docker compose exec app composer install
docker compose exec app php artisan key:generate
docker compose exec app php artisan storage:link
docker compose exec app npm install
docker compose exec app npm run build
```

4. Run migrations:

```bash
docker compose exec app php artisan migrate
```

5. Open the application:

```text
http://localhost:8090
```

## Environment Settings

Docker database settings in `.env`:

```dotenv
APP_URL=http://localhost:8090

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=allweb_urlshort
DB_USERNAME=allweb
DB_PASSWORD=secret
```

MySQL values from `docker-compose.yml`:

```dotenv
MYSQL_DATABASE=allweb_urlshort
MYSQL_USER=allweb
MYSQL_PASSWORD=secret
MYSQL_ROOT_PASSWORD=rootsecret
```

To connect to MySQL from the host, use:

```text
host: 127.0.0.1
port: 3308
database: allweb_urlshort
user: allweb
password: secret
```

## Useful Commands

Start the containers:

```bash
docker compose up -d
```

Stop the containers:

```bash
docker compose down
```

Stop the containers and remove the MySQL data volume:

```bash
docker compose down -v
```

View container status:

```bash
docker compose ps
```

View application logs:

```bash
docker compose logs -f app
```

View MySQL logs:

```bash
docker compose logs -f mysql
```

Open a shell in the app container:

```bash
docker compose exec app bash
```

Run an Artisan command:

```bash
docker compose exec app php artisan <command>
```

Clear the Laravel cache:

```bash
docker compose exec app php artisan optimize:clear
```

Build frontend assets:

```bash
docker compose exec app npm run build
```

Run the Vite dev server inside the container:

```bash
docker compose exec app npm run dev -- --host 0.0.0.0
```

## Migrations and Seeders

Run migrations:

```bash
docker compose exec app php artisan migrate
```

Recreate the database with migrations:

```bash
docker compose exec app php artisan migrate:fresh
```

If seeders have been added to the project:

```bash
docker compose exec app php artisan db:seed
```

Or run everything together:

```bash
docker compose exec app php artisan migrate:fresh --seed
```

## Tests

Run tests:

```bash
docker compose exec app php artisan test
```

Composer script:

```bash
docker compose exec app composer test
```

## Running Without Docker

To run the project without Docker, you need:

- PHP 8.4 with the `bcmath`, `exif`, `gd`, `intl`, `opcache`, `pdo_mysql`, and `zip` extensions
- Composer 2
- Node.js 22+
- MySQL 8.4+
- Apache or another web server with `public` as the document root

Basic commands:

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

If the application does not open:

```bash
docker compose ps
docker compose logs app
```

If MySQL is not ready yet:

```bash
docker compose logs mysql
```

If `.env`, routes, config, or views have changed:

```bash
docker compose exec app php artisan optimize:clear
```

If the frontend asset build is broken:

```bash
docker compose exec app npm install
docker compose exec app npm run build
```

If you need to fully recreate the environment with a clean database:

```bash
docker compose down -v
docker compose up -d --build
docker compose exec app php artisan migrate
```

## Important Notes

- Do not commit real secrets in `.env`.
- Use `.env.docker` for the local Docker environment.
- For production, make sure to disable `APP_DEBUG`, configure the real `APP_URL`, SMTP, Stripe credentials, and secure database passwords.
- `storage` and `bootstrap/cache` must be writable by the web user.
