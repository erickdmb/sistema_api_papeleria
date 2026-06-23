# API Papeleria

API RESTful para gestion de inventario, ventas y compras de una papeleria, desarrollada con Laravel.

## Requisitos

- PHP 8.3+
- Composer
- MySQL 8.0 (o Docker)
- Node.js 18+ (solo para compilar assets de Laravel)

## Instalacion

1. Clonar el repositorio y entrar al directorio:

```bash
git clone <repo-url> api-papeleria
cd api-papeleria
```

2. Instalar dependencias de PHP:

```bash
composer install
```

3. Copiar el archivo de entorno y generar la clave de la aplicacion:

```bash
cp .env.example .env
php artisan key:generate
```

4. Configurar la base de datos en `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=db_papeleria
DB_USERNAME=root
DB_PASSWORD=
```

5. Iniciar la base de datos (usando Docker):

```bash
docker-compose up -d
```

O configurar manualmente un servidor MySQL local con los datos del paso 4.

6. Ejecutar migraciones y seeders (poblado inicial):

```bash
php artisan migrate:fresh --seed
```

Esto crea las tablas y carga datos de prueba: categorias, productos, clientes, proveedores, metodos de pago, y un usuario administrador.

7. Iniciar el servidor de desarrollo:

```bash
php artisan serve
```

La API estara disponible en `http://127.0.0.1:8000`.

## Autenticacion

El sistema usa Laravel Sanctum con tokens Bearer.

### Iniciar sesion

```bash
POST /api/login
Content-Type: application/json
Accept: application/json

{
    "email": "admin@papeleria.com",
    "password": "admin123"
}
```

Respuesta exitosa:

```json
{
    "access_token": "1|...",
    "token_type": "Bearer",
    "user": { "id": 1, "name": "Admin Papeleria", "email": "admin@papeleria.com" }
}
```

Incluir el token en todas las peticiones protegidas:

```
Authorization: Bearer 1|...
Accept: application/json
```

## Endpoints

### Autenticacion

| Metodo | Endpoint | Acceso | Descripcion |
|--------|----------|--------|-------------|
| POST | /api/login | Publico | Iniciar sesion |
| POST | /api/logout | Protegido | Cerrar sesion |
| GET | /api/user | Protegido | Datos del usuario autenticado |

### Catalogos

| Metodo | Endpoint | Descripcion |
|--------|----------|-------------|
| GET/POST | /api/categorias | Listar / Crear categorias |
| GET/PUT/DELETE | /api/categorias/{id} | Ver / Editar / Eliminar categoria |
| GET/POST | /api/productos | Listar / Crear productos |
| GET/PUT/DELETE | /api/productos/{id} | Ver / Editar / Eliminar producto |
| GET/POST | /api/clientes | Listar / Crear clientes |
| GET/PUT/DELETE | /api/clientes/{id} | Ver / Editar / Eliminar cliente |
| GET/POST | /api/proveedores | Listar / Crear proveedores |
| GET/PUT/DELETE | /api/proveedores/{id} | Ver / Editar / Eliminar proveedor |
| GET/POST | /api/metodos-pago | Listar / Crear metodos de pago |

### Ventas

| Metodo | Endpoint | Descripcion |
|--------|----------|-------------|
| GET | /api/ventas | Listar ventas. Parametros: `?all=true`, `?include_details=true` |
| POST | /api/ventas | Registrar venta (descuenta stock) |
| GET | /api/ventas/{id} | Detalle de venta con productos |
| GET | /api/ventas/{id}/detalles | Detalles de productos de una venta |
| POST | /api/ventas/{id}/anular | Anular venta (devuelve stock) |

### Compras

| Metodo | Endpoint | Descripcion |
|--------|----------|-------------|
| GET | /api/compras | Listar compras. Parametros: `?all=true`, `?include_details=true` |
| POST | /api/compras | Registrar compra (aumenta stock) |
| GET | /api/compras/{id} | Detalle de compra con productos |
| POST | /api/compras/{id}/anular | Anular compra (retira stock) |

### Reportes

| Metodo | Endpoint | Descripcion |
|--------|----------|-------------|
| GET | /api/inventario/bajo-stock | Productos con stock menor o igual al minimo |
| GET | /api/inventario/resumen | Resumen financiero del inventario |

### Filtros de productos

- `?buscar=texto` - Busca por nombre, SKU o marca
- `?categoria_id=1` - Filtra por categoria
- `?bajo_stock=1` - Solo productos con stock bajo
- `?all=true` - Todos los registros (sin paginacion)
- `?activo=true` - Solo productos activos

## Pruebas

```bash
php artisan test
```

## Notas

- Las rutas son publicas por simplicidad (sin middleware de autenticacion en CRUD). Solo `/api/logout` y `/api/user` requieren token.
- La paginacion por defecto es de 15 registros. Usar `?all=true` para obtener todos.
