<h1 align="center">
  Juga en Equipo
</h1>

<p align="center">
    Es una Red Social para jugadores de videojuegos E-Sports, formacion de equipos, creacion de torneos y primer
    contacto profesional.
</p>

Requerimientos
---------------

* PHP 8.3
* PHP extensions: mysql, pdo-mysql, amqp
* Requerimientos usuales en aplicaciones [symfony](https://symfony.com/doc/current/setup.html#symfony-tech-requirements)
* Composer, Latest 2.x
* MariaDB 11
* Rabbitmq

Instalación
--------------
* Clonar el proyecto desde el repositorio
* Ejecutar `composer install` en la raíz del proyecto para instalar las dependencias

Configuración de entorno
---------------------------
* Copiar archivo `.env.template` a `.env` y completar datos del entorno.
* Crear la carpeta `var` en la raiz del proyecto. 

Detalles sobre la configuracion del entorno
-----------------------------------
* El formato para setear emails en symfony (con dsn) es el siguiente
  MAILER_DSN=`smtp://<user>:<pass>@<server>:<port>`

Inicilizacion de la Base de datos para una instalación limpia
-----------------------------------
* Crear la estructura de la base de datos. Ejecutar `php bin/console doctrine:migrations:migrate`
* Si se desea cargar la base de datos con data inicial. Ejecutar `php bin/console doctrine:fixtures:load --append`


Generar las claves SSL si no existen
----------------------
Ejecutar `php bin/console lexik:jwt:generate-keypair`

Se almacenaran en `config/jwt/private.pem` y `config/jwt/public.pem`

> Opciones disponibles:
> * `--skip-if-exists` para omitir la creación si ya existe.
> * `--overwrite` sobreescribira las claves existentes.

Iniciar worker para servicios asincronos 
----------------------
* `php bin/console messenger:consume -vvv`

Por el momento solo se utiliza para el envio de emails.

> Revisar [documentación](https://symfony.com/doc/current/messenger.html) de symfony messenger para despliegue produccion

Uso
-----

* Para un uso local. Ejecutar `php -S localhost:8000 -t public/` o `symfony serve` (en caso de tener instalado el [cli de symfony](https://symfony.com/download) ) en la raíz del directorio.
* En el navegador ir a http://localhost:8000/ en la ruta deseada, tambien se puede testear mediante postman (swagger a confimar)
* Para [configurar el servidor](https://symfony.com/doc/current/setup/web_server_configuration.html) web se recomienda ver la documentación oficial de symfony.
* Para más [información con respecto a despliegues](https://symfony.com/doc/current/deployment.html) en aplicaciones symfony puede ver la documentación