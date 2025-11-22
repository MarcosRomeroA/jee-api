#!/bin/bash

set -e
set -x

# Crear directorio para claves JWT si no existe
mkdir -p /var/www/html/config/jwt

# Ejecutar el comando para generar las claves JWT
echo "Generando claves JWT..."
php /var/www/html/bin/console lexik:jwt:generate-keypair --skip-if-exists

# Ajustar permisos de las claves JWT para que www-data pueda leerlas
echo "Ajustando permisos de claves JWT..."
chown www-data:www-data /var/www/html/config/jwt/private.pem 2>/dev/null || true
chown www-data:www-data /var/www/html/config/jwt/public.pem 2>/dev/null || true
chmod 600 /var/www/html/config/jwt/private.pem 2>/dev/null || true
chmod 644 /var/www/html/config/jwt/public.pem 2>/dev/null || true

# Ejecutar migraciones
echo "Ejecutando migraciones..."
php /var/www/html/bin/console doctrine:migrations:migrate -n

# Ejecutar el comando por defecto para iniciar los servicios
echo "Iniciando PHP-FPM y Nginx..."
exec "$@"
