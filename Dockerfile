FROM php:8.3-fpm-alpine

# Instalar Nginx, Supervisor y extensiones PHP necesarias
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
    $PHPIZE_DEPS \
    && docker-php-ext-install xml pdo pdo_mysql opcache zip \
    && pecl install amqp-2.1.2 \
    && docker-php-ext-enable amqp \
    && apk del $PHPIZE_DEPS

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

# Copiar los archivos del proyecto Symfony al contenedor
COPY . /var/www/html

# Instalar dependencias de composer (incluir dev ya que usamos esto también en desarrollo)
RUN composer install --optimize-autoloader --no-interaction

# Preparar caché de producción durante el build
RUN php /var/www/html/bin/console cache:clear --env=prod --no-warmup \
    && php /var/www/html/bin/console cache:warmup --env=prod

# Configurar permisos y crear directorios necesarios
RUN mkdir -p /var/www/html/var/log \
    && chown -R www-data:www-data /var/www/html/var \
    && chmod -R 775 /var/www/html/var \
    && chown -R www-data:www-data /var/www/html/public \
    && mkdir -p /var/log/nginx /run/nginx \
    && chown -R www-data:www-data /var/log/nginx \
    && chown -R nginx:nginx /run/nginx

# Exponer el puerto 80 para Nginx
EXPOSE 80

# Configurar el entrypoint y comando de inicio
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["/bin/sh", "-c", "php-fpm -D && supervisord -c /etc/supervisord.conf && nginx -g 'daemon off;'"]
