# Deployment Guide

## Overview

This guide covers setting up the application for development and production environments.

---

## Requirements

### System Requirements

| Component | Minimum | Recommended |
|-----------|---------|-------------|
| PHP | 8.2 | 8.3+ |
| PostgreSQL | 14 | 15+ |
| Composer | 2.5 | 2.7+ |
| Node.js | 18 (optional) | 20+ |

### PHP Extensions

```
- BCMath
- Ctype
- cURL
- DOM
- Fileinfo
- JSON
- Mbstring
- OpenSSL
- PCRE
- PDO
- PDO_PGSQL
- Tokenizer
- XML
```

---

## Local Development Setup

### 1. Clone Repository

```bash
git clone <repository-url>
cd architecture
```

### 2. Install Dependencies

```bash
composer install
```

### 3. Environment Configuration

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 4. Configure Environment Variables

Edit `.env` file:

```env
APP_NAME="Laravel API"
APP_ENV=local
APP_KEY=base64:generated-key-here
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password

# API Authentication
API_TOKEN=your-secure-api-token-here
API_TOKEN_HEADER=x-api-token

# Sanctum
SANCTUM_STATEFUL_DOMAINS=localhost:8000
```

### 5. Database Setup

```bash
# Create database
createdb your_database

# Run migrations
php artisan migrate

# Seed database (optional)
php artisan db:seed
```

### 6. Start Development Server

```bash
php artisan serve
```

Application will be available at `http://localhost:8000`

---

## Production Deployment

### 1. Server Preparation

```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install PHP 8.3
sudo apt install php8.3 php8.3-fpm php8.3-cli php8.3-pgsql \
    php8.3-mbstring php8.3-xml php8.3-curl php8.3-zip

# Install PostgreSQL
sudo apt install postgresql postgresql-contrib

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Install Nginx
sudo apt install nginx
```

### 2. Application Deployment

```bash
# Clone to web directory
cd /var/www
git clone <repository-url> api
cd api

# Install dependencies (no dev)
composer install --no-dev --optimize-autoloader

# Set permissions
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache

# Configure environment
cp .env.example .env
php artisan key:generate
# Edit .env with production values
```

### 3. Production Environment Variables

```env
APP_NAME="Your API"
APP_ENV=production
APP_KEY=base64:your-production-key
APP_DEBUG=false
APP_URL=https://api.yourdomain.com

# Database
DB_CONNECTION=pgsql
DB_HOST=your-db-host
DB_PORT=5432
DB_DATABASE=production_db
DB_USERNAME=production_user
DB_PASSWORD=secure-password

# API Token (generate secure token)
API_TOKEN=your-production-api-token

# Logging
LOG_CHANNEL=stack
LOG_LEVEL=error

# Cache
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

# Redis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
```

### 4. Optimization Commands

```bash
# Cache configuration
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views (if applicable)
php artisan view:cache

# Optimize autoloader
composer dump-autoload --optimize
```

### 5. Nginx Configuration

Create `/etc/nginx/sites-available/api`:

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name api.yourdomain.com;
    root /var/www/api/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

Enable site:

```bash
sudo ln -s /etc/nginx/sites-available/api /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

### 6. SSL Certificate (Let's Encrypt)

```bash
sudo apt install certbot python3-certbot-nginx
sudo certbot --nginx -d api.yourdomain.com
```

### 7. Database Migration

```bash
php artisan migrate --force
```

---

## Docker Deployment

### docker-compose.yml

```yaml
version: '3.8'

services:
  app:
    build:
      context: .
      dockerfile: Dockerfile
    container_name: api-app
    restart: unless-stopped
    working_dir: /var/www
    volumes:
      - ./:/var/www
      - ./docker/php/local.ini:/usr/local/etc/php/conf.d/local.ini
    networks:
      - api-network
    depends_on:
      - db

  nginx:
    image: nginx:alpine
    container_name: api-nginx
    restart: unless-stopped
    ports:
      - "8000:80"
    volumes:
      - ./:/var/www
      - ./docker/nginx/conf.d:/etc/nginx/conf.d
    networks:
      - api-network

  db:
    image: postgres:15-alpine
    container_name: api-db
    restart: unless-stopped
    environment:
      POSTGRES_DB: ${DB_DATABASE}
      POSTGRES_USER: ${DB_USERNAME}
      POSTGRES_PASSWORD: ${DB_PASSWORD}
    volumes:
      - dbdata:/var/lib/postgresql/data
    ports:
      - "5432:5432"
    networks:
      - api-network

networks:
  api-network:
    driver: bridge

volumes:
  dbdata:
    driver: local
```

### Dockerfile

```dockerfile
FROM php:8.3-fpm

# Install dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    zip \
    unzip

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_pgsql mbstring exif pcntl bcmath

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy application
COPY . .

# Install dependencies
RUN composer install --no-dev --optimize-autoloader

# Set permissions
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

EXPOSE 9000
CMD ["php-fpm"]
```

### Running with Docker

```bash
# Build and start
docker-compose up -d --build

# Run migrations
docker-compose exec app php artisan migrate

# View logs
docker-compose logs -f app
```

---

## CI/CD Pipeline

### GitHub Actions Example

Create `.github/workflows/deploy.yml`:

```yaml
name: Deploy

on:
  push:
    branches: [main]

jobs:
  test:
    runs-on: ubuntu-latest

    services:
      postgres:
        image: postgres:15
        env:
          POSTGRES_USER: test
          POSTGRES_PASSWORD: test
          POSTGRES_DB: test
        ports:
          - 5432:5432
        options: >-
          --health-cmd pg_isready
          --health-interval 10s
          --health-timeout 5s
          --health-retries 5

    steps:
      - uses: actions/checkout@v4

      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.3'
          extensions: pdo, pdo_pgsql, mbstring

      - name: Install Dependencies
        run: composer install --no-interaction --prefer-dist

      - name: Copy Environment
        run: cp .env.example .env

      - name: Generate Key
        run: php artisan key:generate

      - name: Run Tests
        env:
          DB_CONNECTION: pgsql
          DB_HOST: 127.0.0.1
          DB_PORT: 5432
          DB_DATABASE: test
          DB_USERNAME: test
          DB_PASSWORD: test
        run: php artisan test

  deploy:
    needs: test
    runs-on: ubuntu-latest
    if: github.ref == 'refs/heads/main'

    steps:
      - name: Deploy to Server
        uses: appleboy/ssh-action@v1.0.0
        with:
          host: ${{ secrets.SERVER_HOST }}
          username: ${{ secrets.SERVER_USER }}
          key: ${{ secrets.SERVER_SSH_KEY }}
          script: |
            cd /var/www/api
            git pull origin main
            composer install --no-dev --optimize-autoloader
            php artisan migrate --force
            php artisan config:cache
            php artisan route:cache
            sudo systemctl reload php8.3-fpm
```

---

## Maintenance

### Common Commands

```bash
# Clear all caches
php artisan optimize:clear

# View logs
tail -f storage/logs/laravel.log

# Queue worker
php artisan queue:work

# Maintenance mode
php artisan down --secret="your-secret"
php artisan up
```

### Health Check Endpoint

Add to routes for monitoring:

```php
Route::get('/health', fn() => response()->json(['status' => 'ok']));
```

### Backup Database

```bash
# Backup
pg_dump -U username database_name > backup.sql

# Restore
psql -U username database_name < backup.sql
```
