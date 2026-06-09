# API Papelería - Sistema de Gestión

Backend para la gestión de ventas, compras e inventario, desarrollado con **Laravel** y autenticación segura mediante **Laravel Sanctum**.

## Requisitos Previos
* PHP 8.2+
* Composer
* Docker Desktop instalado y en ejecución
* MySQL

## Configuración Inicial
#### 1 Instalar dependencias:

```Bash
composer install
```

#### 2 Configurar entorno:
Crear tu archivo de variables de entorno

```Bash
cp .env.example .env
php artisan key:generate
```
### 3 Configurar archivo .env
Ingresar al rchivo .env y modificar los datos
```php
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=db_papeleria
DB_USERNAME=root
DB_PASSWORD=
```

## Infraestructura (Docker)
este proyecto para estudio utiliza contenedores para estandarizar el entorno:

#### 4 Levantar servicios
```Bash
docker-composer up -d
```
#### 5 Inicializar Base de Datos
Una vezz activados los contenedores, ejecuta las migraciones y seeders

```Bash
php artisan migrate --seed
```

## Autenticación (Laravel Sanctum)
**Para acceder a rutas protegidas: (usar postman u otro)**

**Login:** POST /api/login
Conten-type:application/json

**Uso de Token:** Incluye en tus peticiones el header: Authorization: Bearer {TOKEN}.

## Endpoint Principales
| Método | Endpoint | Acceso|
|--------|----------|-------|
|POST|/api/login|Público|
|POST|/api/logout|Protegido|
|GET|/api/user|Protegido|
|GET/POST/PUT/DELETE|/api/productos	|Protegido|
|POST|/api/ventas|Protegido|

#### Pruebas
Ejecuta el conjunto de tests de integración para asegurar la estabilidad:

```Bash
php artisan test
```