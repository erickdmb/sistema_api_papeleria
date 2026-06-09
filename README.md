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

## Infraestructura (Docker)
este proyecto para estudio utiliza contenedores para estandarizar el entorno:

#### 3 Levantar servicios
```Bash
docker-composer up -d
```
#### 4 Inicializar Base de Datos
Una vezz activados los contenedores, ejecuta las migraciones y seeders

```Bash
docker-compose exec app php artisan migrate --seed
```

## Autenticación (Laravel Sanctum)
Para acceder a rutas protegidas:
Login: POST /api/login
Header requerido: Accept: application/json
Uso de Token: Incluye en tus peticiones el header: Authorization: Bearer {TOKEN}.

## Endpoint Principales
Método	Endpoint	Acceso
POST	/api/login	Público
POST	/api/logout	Protegido
GET	/api/user	Protegido
GET/POST/PUT/DELETE	/api/productos	Protegido
POST	/api/ventas	Protegido

#### Pruebas
Ejecuta el conjunto de tests de integración para asegurar la estabilidad:

```Bash
docker-compose exec app php artisan test
```