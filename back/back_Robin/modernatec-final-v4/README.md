# BACK END MODERNATEC

### Resumen del codigo de esta sección. 

En este sitio se encuentra el backend desarrollado para la funcionalidad del aplicativo para tener conocimientos de todas las rutas a detalle de lo que hace cada ruta, puede acceder al link de documentación de postman que estamos dejando más abajo en la documentación.

A grandes rasgos, este apartado contiene el desarrollo para realizar la ssiguientes funciones:
- un registro de invitados. 
- registro de usuarios con roles y caracteristicas propias de usuarios del sistema. 
- inicio de sesión completo(con recuperación de contraseña).
- implementación de la librería JWT para el manejo de sesiones.
- CRUD para agendamiento de citas (con envio de correos).
- Importación de base de datos al aplicativo a travez de excel.
- Exportacion de registros de excel(libreria laravel excel).


### Requisitos paqueteria

| Nombre Paquete | Sitio oficial 
| ------ | ------ 
| Node JS | [https://nodejs.org/es/][Obligatorio] |
| Composer | [https://getcomposer.org/][Obligatorio]
|postman|[https://www.postman.com/][Opcional]
| Visual estudio coode | [https://code.visualstudio.com/][opcional]
| Xampp | [https://www.apachefriends.org/es/index.html][opcional]


### Librerias:

| Libreria | Sitio oficial |Instalador|
| ------ | ------ |-------|
| JWT | [https://jwt.io/] |composer require tymon/jwt-auth:dev-develop
| Laravel Excel | [https://laravel-excel.com/] |https://laravel-excel.com/

### Framework

| Framework | Sitio oficial |Instalador|
| ------ | ------ |-------|
| Laravel | https://laravel.com/ |composer create-project laravel/laravel example-app

### Rutas 

En este código se pueden encontrar diferentes rutas desarrolladas para tener un buen funcionamiento del aplicativo, para saber de como utilizarlas te puedes dirigir a la siguiente ruta:

https://documenter.getpostman.com/view/17244908/UVktoso5

### Instalación
* Primer paso clonamos el repositorio del github
```sh
git clone https://github.com/310pepe/modernatec.git
```

* accedemos a la carpeta que contiene el backend con la terminal de comandos y colocamos el siguiente comando 
```sh
composer install 
```

* Luego colocamos el siguiente comando para instalar una llave a nuestro proyecto. 
```sh
php artisan key generate 
```

* en el archivo .env colocamos toda la informacion de la base de datos 

> DB_CONNECTION=mysql
> DB_HOST=127.0.0.1
> DB_PORT=3306
> DB_DATABASE=baseDatos
> DB_USERNAME=root
> DB_PASSWORD=root

* en el archivo .env colocamos toda la informacion de la base de datos 

> DB_CONNECTION=mysql
> DB_HOST=127.0.0.1
> DB_PORT=3306
> DB_DATABASE=baseDatos
> DB_USERNAME=root
> DB_PASSWORD=root

* Luego colocamos las varibles de entorno para el envio de correos 

> MAIL_MAILER=smtp
> MAIL_HOST=smtp.gmail.com
> MAIL_PORT=587
> MAIL_USERNAME=Correo@gmail.com
> MAIL_PASSWORD=Password
> MAIL_ENCRYPTION=tls
> MAIL_FROM_ADDRESS=CorreodeEnvio
> MAIL_FROM_NAME="${APP_NAME}" /* Nombre de la aplicacion */

* Por ultimo corremos el servidor con el comando 
```sh
php artisan serve 
```

# ¡¡ Listo ya puesdes hacer todas tus pruebas !!!. 














