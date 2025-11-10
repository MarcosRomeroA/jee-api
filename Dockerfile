FROM php:8.3-apache-bookworm

# Instalar la extensión XML y otras extensiones necesarias
RUN apt-get update && apt-get install -y \
    libxml2-dev \
    unzip \
    git \
    && docker-php-ext-install xml \
    && docker-php-ext-install pdo pdo_mysql

# Estable la zona horaria
ENV TZ=${TZ}

# Habilitar el módulo de reescritura de Apache
RUN a2enmod rewrite

# COPY php.ini
COPY ./docker/php/php.ini /usr/local/etc/php/php.ini

# Instalar Composer
COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

# Copiar vhost
COPY ./docker/apache/vhost.conf /etc/apache2/sites-enabled/000-default.conf

# Copiar entrypoint
COPY ./entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Copiar los archivos del proyecto Symfony al contenedor
COPY . /var/www/html

# Instalar dependencias de composer
RUN composer install --no-dev --optimize-autoloader --no-interaction

RUN chown -R www-data:www-data /var/www/html/var \
    && chmod -R 755 /var/www/html/var

# Exponer el puerto 80 para Apache
EXPOSE 80

# Configurar el entrypoint y comando de inicio
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["apache2-foreground"]