# === Stage 1: build de assets con Node ===
FROM node:20-alpine AS nodebuild
WORKDIR /app

# Copia lo mínimo para poder instalar deps y construir
COPY package*.json vite.config.* ./
RUN npm ci

# Copia fuentes necesarias para Vite
COPY resources resources
COPY public public

# (Si usas Tailwind/PostCSS, también necesitas estos archivos si existen)
# COPY postcss.config.js tailwind.config.js ./

RUN npm run build

# === Stage 2: PHP-FPM + extensiones + Nginx ===
FROM php:8.2-fpm-alpine

# Paquetes del sistema y extensiones requeridas
RUN apk add --no-cache nginx supervisor bash git curl icu-dev \
    libpng libjpeg-turbo libjpeg-turbo-dev libpng-dev postgresql-dev

RUN docker-php-ext-configure gd --with-jpeg && \
    docker-php-ext-install -j$(nproc) gd pdo pdo_pgsql intl

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . /var/www/html

# COPIA los assets construidos por Vite al contenedor final
COPY --from=nodebuild /app/public/build /var/www/html/public/build

# Nginx + Supervisor + entrypoint
COPY ./deploy/nginx.conf /etc/nginx/nginx.conf
COPY ./deploy/supervisord.conf /etc/supervisord.conf
COPY ./deploy/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Permisos Laravel
RUN mkdir -p storage bootstrap/cache && \
    chown -R www-data:www-data /var/www/html && \
    chmod -R 775 storage bootstrap/cache

EXPOSE 10000
CMD ["/bin/sh","-lc","/usr/local/bin/entrypoint.sh"]
