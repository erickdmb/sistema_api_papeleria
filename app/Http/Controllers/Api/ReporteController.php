<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use App\Models\Venta;
use App\Models\Compra;
use Illuminate\Http\Request;

class ReporteController extends Controller
{
    public function bajoStock()
    {
        $productos = Producto::with('categoria')
            ->whereColumn('stock_actual', '<=', 'stock_minimo')
            ->where('activo', true)
            ->get();

        return response()->json([
            'count' => $productos->count(),
            'productos' => $productos
        ]);
    }

    public function resumen()
    {
        // Totales de productos
        $totalProductos = Producto::count();
        $totalProductosActivos = Producto::where('activo', true)->count();
        
        // Stock total e inventarios valorizados
        $stockActualTotal = Producto::where('activo', true)->sum('stock_actual');
        
        $valorizadoCompra = Producto::where('activo', true)
            ->selectRaw('SUM(stock_actual * precio_compra) as total')
            ->first()->total ?? 0;

        $valorizadoVenta = Producto::where('activo', true)
            ->selectRaw('SUM(stock_actual * precio_venta) as total')
            ->first()->total ?? 0;

        $margenProyectado = $valorizadoVenta - $valorizadoCompra;

        // Productos bajo stock
        $bajoStockCount = Producto::where('activo', true)
            ->whereColumn('stock_actual', '<=', 'stock_minimo')
            ->count();

        // Ventas y compras del mes/totales
        $totalVentasCompletadas = Venta::where('estado', 'COMPLETADA')->sum('total');
        $cantidadVentasCompletadas = Venta::where('estado', 'COMPLETADA')->count();
        
        $totalComprasRegistradas = Compra::where('estado', 'REGISTRADA')->sum('total');
        $cantidadComprasRegistradas = Compra::where('estado', 'REGISTRADA')->count();

        return response()->json([
            'resumen_inventario' => [
                'total_productos_catalogo' => $totalProductos,
                'total_productos_activos' => $totalProductosActivos,
                'stock_fisico_total' => (int)$stockActualTotal,
                'valor_inventario_compra' => round((float)$valorizadoCompra, 2),
                'valor_inventario_venta' => round((float)$valorizadoVenta, 2),
                'ganancia_proyectada_stock' => round((float)$margenProyectado, 2),
                'productos_bajo_stock' => $bajoStockCount,
            ],
            'resumen_transacciones' => [
                'ventas_completadas_acumuladas' => round((float)$totalVentasCompletadas, 2),
                'cantidad_ventas' => $cantidadVentasCompletadas,
                'compras_proveedores_acumuladas' => round((float)$totalComprasRegistradas, 2),
                'cantidad_compras' => $cantidadComprasRegistradas,
            ]
        ]);
    }
}
