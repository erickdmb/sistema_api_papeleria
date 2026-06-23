<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CategoriaController;
use App\Http\Controllers\Api\ProductoController;
use App\Http\Controllers\Api\ClienteController;
use App\Http\Controllers\Api\ProveedorController;
use App\Http\Controllers\Api\MetodoPagoController;
use App\Http\Controllers\Api\VentaController;
use App\Http\Controllers\Api\CompraController;
use App\Http\Controllers\Api\ReporteController;
use App\Http\Controllers\Api\AuthController;

// Rutas Públicas de Autenticación
Route::post('/login', [AuthController::class, 'login']);

// Rutas Protegidas de Autenticación y Perfil
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('/logout', [AuthController::class, 'logout']);
});
// Rutas de Inventario y Reportes (Deberían ir antes de los recursos genéricos si chocan, pero las rutas son distintas)
Route::get('/inventario/bajo-stock', [ReporteController::class, 'bajoStock']);
Route::get('/inventario/resumen', [ReporteController::class, 'resumen']);

// Recursos estándar del sistema
Route::apiResource('categorias', CategoriaController::class);
Route::apiResource('productos', ProductoController::class);
Route::apiResource('clientes', ClienteController::class);
Route::apiResource('proveedores', ProveedorController::class);
Route::apiResource('metodos-pago', MetodoPagoController::class)->parameters([
    'metodos-pago' => 'id_metodo_pago' // Forzar el nombre del parámetro si se actualiza
]);

// Rutas transaccionales de compras
Route::get('compras', [CompraController::class, 'index']);
Route::post('compras', [CompraController::class, 'store']);
Route::get('compras/{id}', [CompraController::class, 'show']);
Route::post('compras/{id}/anular', [CompraController::class, 'anular']);

// Rutas transaccionales de ventas
Route::get('ventas', [VentaController::class, 'index']);
Route::post('ventas', [VentaController::class, 'store']);
Route::get('ventas/{id}', [VentaController::class, 'show']);
Route::post('ventas/{id}/anular', [VentaController::class, 'anular']);
Route::get('ventas/{id}/detalles', [VentaController::class, 'detalles']);

// Ruta para historial de ventas de un cliente específico
Route::get('clientes/{id}/ventas', [ClienteController::class, 'ventas']);

