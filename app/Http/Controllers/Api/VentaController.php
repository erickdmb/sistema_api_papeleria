<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Venta;
use App\Models\VentaDetalle;
use App\Models\Producto;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class VentaController extends Controller
{
    public function index(Request $request)
    {
        $relations = ['cliente', 'metodoPago'];

        if ($request->boolean('include_details', false)) {
            $relations[] = 'detalles.producto';
        }

        $query = Venta::with($relations);

        if ($request->has('fecha')) {
            $query->whereDate('fecha', $request->input('fecha'));
        }

        if ($request->has('estado')) {
            $query->where('estado', $request->input('estado'));
        }

        if ($request->boolean('all', false) || $request->input('paginate') === 'false') {
            $ventas = $query->orderBy('id_venta', 'desc')->get();
        } else {
            $ventas = $query->orderBy('id_venta', 'desc')->paginate($request->input('per_page', 15));
        }

        return response()->json($ventas);
    }

    public function show($id)
    {
        $venta = Venta::with(['cliente', 'metodoPago', 'detalles.producto'])->find($id);
        if (!$venta) {
            return response()->json(['message' => 'Venta no encontrada'], 404);
        }
        return response()->json($venta);
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_cliente' => 'required|exists:clientes,id_cliente',
            'id_metodo_pago' => 'required|exists:metodos_pago,id_metodo_pago',
            'detalles' => 'required|array|min:1',
            'detalles.*.id_producto' => 'required|exists:productos,id_producto',
            'detalles.*.cantidad' => 'required|integer|min:1',
        ]);

        try {
            return DB::transaction(function () use ($request) {
                $now = Carbon::now();
                $subtotal_venta = 0;

                // 1. Validar existencias y calcular totales de forma preliminar
                $productos_procesados = [];
                foreach ($request->input('detalles') as $detalle) {
                    $producto = Producto::lockForUpdate()->find($detalle['id_producto']);
                    
                    if (!$producto->activo) {
                        throw new \Exception("El producto '{$producto->nombre}' no está activo.");
                    }

                    if ($producto->stock_actual < $detalle['cantidad']) {
                        throw new \Exception("Stock insuficiente para el producto: '{$producto->nombre}'. Stock disponible: {$producto->stock_actual}.");
                    }

                    $subtotal_producto = $producto->precio_venta * $detalle['cantidad'];
                    $subtotal_venta += $subtotal_producto;

                    $productos_procesados[] = [
                        'producto' => $producto,
                        'cantidad' => $detalle['cantidad'],
                        'precio_unitario' => $producto->precio_venta,
                        'subtotal' => $subtotal_producto
                    ];
                }

                // 2. Registrar la venta (cabecera)
                $venta = Venta::create([
                    'fecha' => $now->toDateString(),
                    'hora' => $now->toTimeString(),
                    'id_cliente' => $request->input('id_cliente'),
                    'id_metodo_pago' => $request->input('id_metodo_pago'),
                    'subtotal' => $subtotal_venta,
                    'total' => $subtotal_venta, // Asumiendo total = subtotal (sin impuestos detallados o igv incluido)
                    'estado' => 'COMPLETADA'
                ]);

                // 3. Registrar los detalles y actualizar stock
                foreach ($productos_procesados as $item) {
                    $producto = $item['producto'];
                    
                    // Crear detalle
                    VentaDetalle::create([
                        'id_venta' => $venta->id_venta,
                        'id_producto' => $producto->id_producto,
                        'cantidad' => $item['cantidad'],
                        'precio_unitario' => $item['precio_unitario'],
                        'subtotal' => $item['subtotal']
                    ]);

                    // Descontar stock
                    $producto->stock_actual -= $item['cantidad'];
                    $producto->save();
                }

                // 4. Actualizar estadísticas del cliente
                $cliente = Cliente::find($request->input('id_cliente'));
                $cliente->total_compras += $subtotal_venta;
                $cliente->ultima_compra = $now->toDateString();
                $cliente->save();

                return response()->json($venta->load('detalles'), 201);
            });
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al procesar la venta.',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    public function anular($id)
    {
        try {
            return DB::transaction(function () use ($id) {
                $venta = Venta::lockForUpdate()->find($id);
                if (!$venta) {
                    return response()->json(['message' => 'Venta no encontrada'], 404);
                }

                if ($venta->estado === 'ANULADA') {
                    return response()->json(['message' => 'La venta ya se encuentra anulada'], 422);
                }

                // 1. Devolver el stock a los productos
                $detalles = VentaDetalle::where('id_venta', $venta->id_venta)->get();
                foreach ($detalles as $detalle) {
                    $producto = Producto::lockForUpdate()->find($detalle->id_producto);
                    $producto->stock_actual += $detalle->cantidad;
                    $producto->save();
                }

                // 2. Anular la venta
                $venta->estado = 'ANULADA';
                $venta->save();

                // 3. Restar el acumulado del cliente
                $cliente = Cliente::find($venta->id_cliente);
                $cliente->total_compras = max(0, $cliente->total_compras - $venta->total);
                
                // Recalcular la fecha de última compra activa
                $ultimaVentaActiva = Venta::where('id_cliente', $venta->id_cliente)
                    ->where('estado', 'COMPLETADA')
                    ->orderBy('fecha', 'desc')
                    ->first();
                
                $cliente->ultima_compra = $ultimaVentaActiva ? $ultimaVentaActiva->fecha : null;
                $cliente->save();

                return response()->json([
                    'message' => 'Venta anulada correctamente. Stock restablecido.',
                    'venta' => $venta
                ]);
            });
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al anular la venta.',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    public function detalles($id)
    {
        $venta = Venta::find($id);
        if (!$venta) {
            return response()->json(['message' => 'Venta no encontrada'], 404);
        }

        $detalles = $venta->detalles()->with('producto')->get();
        return response()->json($detalles);
    }
}
