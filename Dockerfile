# Stage 1: Build Frontend Assets (Vite + Tailwind)
FROM node:20-alpine AS frontend
WORKDIR /app
COPY package*.json ./
RUN npm ci
COPY . .
RUN npm run build

# Stage 2: Production PHP Application
FROM php:8.3-fpm-alpine

# Install System Dependencies & Required Extensions
RUN apk add --no-cache \
    nginx \
    supervisor \
    curl \
    libpng-dev \
    libzip-dev \
    zip \
    unzip \
    oniguruma-dev \
    mariadb-client

RUN docker-php-ext-install pdo pdo_mysql mbstring zip exif pcntl bcmath opcache

# Copy Composer binary
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy Project Files
COPY . .

# Copy Compiled Assets from Stage 1
COPY --from=frontend /app/public/build ./public/build

# Install PHP Production Dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Configure Nginx, Supervisor, and Entrypoint
COPY docker/nginx.conf /etc/nginx/http.d/default.conf
COPY docker/supervisord.conf /etc/supervisord.conf
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Ensure storage & cache permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 8000

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
