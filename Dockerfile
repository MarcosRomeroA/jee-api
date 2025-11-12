FROM php:8.3-fpm-alpine

# Instalar Nginx y extensiones PHP necesarias
RUN apk add --no-cache \
    nginx \
    libxml2-dev \
    libzip-dev \
    unzip \
    git \
    bash \
    && docker-php-ext-install xml \
    && docker-php-ext-install pdo pdo_mysql \
    && docker-php-ext-install opcache \
    && docker-php-ext-install zip

# Establecer la zona horaria
ENV TZ=${TZ}

# COPY php.ini y php-fpm.conf
COPY ./docker/php/php.ini /usr/local/etc/php/php.ini
COPY ./docker/php/php-fpm.conf /usr/local/etc/php-fpm.d/www.conf

# Copiar configuración de Nginx (Alpine usa http.d en lugar de sites-available)
COPY ./docker/nginx/nginx.conf /etc/nginx/nginx.conf
COPY ./docker/nginx/default.conf /etc/nginx/http.d/default.conf

# Instalar Composer (usar la versión latest que existe)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copiar entrypoint
COPY ./entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Copiar los archivos del proyecto Symfony al contenedor
COPY . /var/www/html

# Instalar dependencias de composer (incluir dev ya que usamos esto también en desarrollo)
RUN composer install --optimize-autoloader --no-interaction

# Configurar permisos y crear directorios necesarios
RUN chown -R www-data:www-data /var/www/html/var \
    && chmod -R 755 /var/www/html/var \
    && chown -R www-data:www-data /var/www/html/public \
    && mkdir -p /var/log/nginx /run/nginx \
    && chown -R www-data:www-data /var/log/nginx \
    && chown -R nginx:nginx /run/nginx

# Exponer el puerto 80 para Nginx
EXPOSE 80

# Configurar el entrypoint y comando de inicio
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["/bin/sh", "-c", "php-fpm -D && nginx -g 'daemon off;'"]
