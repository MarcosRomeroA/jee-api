FROM php:8.3-fpm-alpine

# Instalar Nginx, Supervisor y extensiones PHP necesarias
# Primero instalamos las librerías runtime de GD (permanentes)
RUN apk add --no-cache \
    nginx \
    supervisor \
    libxml2-dev \
    libzip-dev \
    rabbitmq-c \
    rabbitmq-c-dev \
    unzip \
    git \
    bash \
    # Librerías runtime para GD (permanentes)
    libpng \
    libjpeg-turbo \
    libwebp \
    freetype

# Instalar dependencias de desarrollo, compilar extensiones y limpiar
RUN apk add --no-cache --virtual .build-deps \
    $PHPIZE_DEPS \
    libpng-dev \
    libjpeg-turbo-dev \
    libwebp-dev \
    freetype-dev \
    && docker-php-ext-configure gd \
    --with-jpeg \
    --with-webp \
    --with-freetype \
    && docker-php-ext-install xml pdo pdo_mysql opcache zip gd \
    && pecl install amqp-2.1.2 \
    && docker-php-ext-enable amqp \
    && apk del .build-deps

# Establecer la zona horaria
ENV TZ=${TZ}

# COPY php.ini y php-fpm.conf
COPY ./docker/php/php.ini /usr/local/etc/php/php.ini
COPY ./docker/php/php-fpm.conf /usr/local/etc/php-fpm.d/www.conf

# Copiar configuración de Nginx (Alpine usa http.d en lugar de sites-available)
COPY ./docker/nginx/nginx.conf /etc/nginx/nginx.conf
COPY ./docker/nginx/default.conf /etc/nginx/http.d/default.conf

# Copiar configuración de Supervisor
COPY ./docker/supervisor/messenger-worker.conf /etc/supervisor.d/messenger-worker.ini

# Instalar Composer (usar la versión latest que existe)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copiar entrypoint
COPY ./entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Crear directorios del sistema (como root)
RUN mkdir -p /var/log/nginx /run/nginx \
    && chown -R nginx:nginx /run/nginx \
    && chown -R www-data:www-data /var/log/nginx

# Copiar los archivos del proyecto Symfony al contenedor
COPY --chown=www-data:www-data . /var/www/html

# Cambiar a usuario www-data para instalar dependencias y generar cache
USER www-data
WORKDIR /var/www/html

# Instalar dependencias de composer
RUN composer install --optimize-autoloader --no-interaction

# Preparar caché de producción durante el build
RUN php bin/console cache:clear --env=prod --no-warmup \
    && php bin/console cache:warmup --env=prod

# Volver a root para el entrypoint
USER root

# Exponer el puerto 80 para Nginx
EXPOSE 80

# Configurar el entrypoint y comando de inicio
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["/bin/sh", "-c", "php-fpm -D && supervisord -c /etc/supervisord.conf && nginx -g 'daemon off;'"]
