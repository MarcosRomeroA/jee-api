#!/bin/bash

# Ejecutar el comando para generar las claves JWT
php /var/www/html/bin/console lexik:jwt:generate-keypair --skip-if-exists

# Ejecutar el comando por defecto para iniciar Apache
exec "$@"