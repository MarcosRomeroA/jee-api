FROM php:8.3-apache

# Instalar la extensión XML y otras extensiones necesarias
RUN apt-get update && apt-get install -y \
    libxml2-dev \
    unzip \
    git \
    && docker-php-ext-install xml \
    && docker-php-ext-install pdo pdo_mysql

# Habilitar el módulo de reescritura de Apache
RUN a2enmod rewrite

# Cambiar el puerto en el que Apache escucha
RUN sed -i 's/Listen 80/Listen 8081/' /etc/apache2/ports.conf

# Instalar Composer
COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

# Copiar vhost
COPY ./docker/apache/vhost.conf /etc/apache2/sites-enabled/000-default.conf

# Copiar los archivos del proyecto Symfony al contenedor
COPY . /var/www/html

# Establecer los permisos correctos
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Copiar el script de entrada al contenedor
COPY entrypoint.sh /usr/local/bin/entrypoint.sh

# Dar permisos de ejecución al script de entrada
RUN chmod +x /usr/local/bin/entrypoint.sh

# Exponer el puerto 8081 para Apache
EXPOSE 8081

# Configurar el script de entrada como el comando de inicio
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]

# Configurar el comando de inicio
CMD ["apache2-foreground"]