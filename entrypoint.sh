#!/bin/bash

set -e
set -x

# Ejecutar el comando para generar las claves JWT
echo "Generando claves JWT..."
php /var/www/html/bin/console lexik:jwt:generate-keypair --skip-if-exists

# Ejecutar migraciones
echo "Ejecutando migraciones..."
php /var/www/html/bin/console doctrine:migrations:migrate -n

# Ejecutar el comando por defecto para iniciar Apache
echo "Iniciando Apache..."
exec "$@"