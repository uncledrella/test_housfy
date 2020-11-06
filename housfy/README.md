# Housefy test

Junto a este documento se encuentran los siguientes archivos y directorios:
  - */housfy*, que contiene el proyecto de Laravel
  - */laradock*, que contiene todos los archivos necesarios para correr la aplicación en Docker
  - *Housfy-Test-API-REST.postman_collection.json*, es una collección de Postman con las peticiones necesarias para probar el CRUD
  
## Instalación

En primer lugar, es necesario ubicar las dos carpetas en el directorio de trabajo deseado. En este caso vamos a suponer que se trata de la carpeta *`~/projects/test-miguelnicolas`* (si se quisiera cambiar, habría que sustituir esta ruta por la deseada en todos los comandos)

En una ventana de Terminal, ejecutaremos los siguientes comandos
```sh
cd ~/projects/test-miguelnicolas/housfy # nos ubicamos dentro del directorio del proyecto
sudo chmod -R 777 storage bootstrap/cache # da permisos de escritura al directorio de cache
cd ../laradock # ahora nos ubicamos dentro del directorio de configuración de Docker
docker-compose up -d nginx mysql redis # Construirá el entorno y levantará los contenedores mínimos requeridos. Puede tardar un rato la primera vez
```
Ahora será necesario crear una base de datos. Ejecutamos los siguientes comandos:
```sh
docker-compose exec mysql bash # entramos en el contenedor de MySQL
mysql -uroot -proot # una vez dentro, nos logueamos como usuario Root
```
Una vez dentro del entorno MySQL, ejecutaremos la *query* de para crear la base de datos con nombre **housfy**
`CREATE DATABASE IF NOT EXISTS my_database DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;`

y saldremos del entorno y del contenedor:
```sh
exit # salir del entorno MySQL
exit # salir del contenedor
```
Con esto ya estaría creada la base de datos. Ahora crearemos las tablas y los registros de prueba. Para ello, ejecutaremos la migración:
```sh
docker-compose exec workspace bash # entrar en el contenedor del entorno de trabajo
php artisan migrate:fresh --seed # una vez dentro, lanzaremos la migración
exit # salir del contenedor
```
Ahora ya tendríamos una aplicación funcional. Si se quiere ver la base de datos en phpMyAdmin, habrá que arrancar el contenedor de Docker necesario:
```sh
docker-compose up -d phpmyadmin
```
y entrar desde el navegador a través de la URL http://localhost:8081/ (*mysql* / *root* / *root*)

## Redis
Redis es el encargado de la cache y de la cola que la llena. Si se requiere ver los registros cacheados, habrá que ejecutar el contenedor de su interfaz gráfica:
```sh
docker-compose up -d redis-webui
```
y entrar desde el navegador a través de la URL http://localhost:9987/
```sh
docker-compose up -d redis-webui
```
Para que la cola de cacheado se procese, tendremos que dejar ejecutando Horizon en otra ventana de terminal:
```sh
docker-compose exec workspace bash # entrar en el contenedor del entorno de trabajo
php artisan horizon
```
Se puede ver el estado de la cola a través de la URL http://localhost/horizon

## API Endpoints
La aplicación corre directamente bajo http://localhost. Se recomienda utilizar Postman para probarlo

| Method | URI | Parámetros | |
| ------ | ------ | ------ | ------ |
|GET | /office | | obtener listado de oficinas |
|GET | /office/1 | | obtener ficha oficina con ID 1 |
|POST | /office | name, address | crear nueva oficina |
|PUT | /office/1 | name, address | modificar oficina con ID 1 |
|DELETE | /office/1 |  | eliminar oficina con ID 1 |

## Tests
Se pueden ejecutar los tests de la siguiente manera:
```sh
docker-compose exec workspace bash # entrar en el contenedor del entorno de trabajo
php artisan test # ejecutar tests
```