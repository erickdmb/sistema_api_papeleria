<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Compra;
use App\Models\CompraDetalle;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CompraController extends Controller
{
    public function index(Request $request)
    {
        $relations = ['proveedor'];

        if ($request->boolean('include_details', false)) {
            $relations[] = 'detalles.producto';
        }

        $query = Compra::with($relations);

        if ($request->has('fecha')) {
            $query->whereDate('fecha', $request->input('fecha'));
        }

        if ($request->has('estado')) {
            $query->where('estado', $request->input('estado'));
        }

        if ($request->boolean('all', false) || $request->input('paginate') === 'false') {
            $compras = $query->orderBy('id_compra', 'desc')->get();
        } else {
            $compras = $query->orderBy('id_compra', 'desc')->paginate($request->input('per_page', 15));
        }

        return response()->json($compras);
    }

    public function show($id)
    {
        $compra = Compra::with(['proveedor', 'detalles.producto'])->find($id);
        if (!$compra) {
            return response()->json(['message' => 'Compra no encontrada'], 404);
        }
        return response()->json($compra);
    }

    public function store(Request $request)
    {
        $request->validate([
            'proveedor_id' => 'required|exists:proveedores,id_proveedor',
            'ruc' => 'nullable|string|max:50',
            'detalles' => 'required|array|min:1',
            'detalles.*.id_producto' => 'required|exists:productos,id_producto',
            'detalles.*.cantidad' => 'required|integer|min:1',
            'detalles.*.precio_unitario' => 'required|numeric|min:0',
        ]);

        try {
            return DB::transaction(function () use ($request) {
                $now = Carbon::now();
                $total_compra = 0;

                // 1. Calcular totales preliminares
                $detalles_procesados = [];
                foreach ($request->input('detalles') as $detalle) {
                    $producto = Producto::lockForUpdate()->find($detalle['id_producto']);
                    
                    if (!$producto->activo) {
                        throw new \Exception("El producto '{$producto->nombre}' no está activo.");
                    }

                    $subtotal_producto = $detalle['precio_unitario'] * $detalle['cantidad'];
                    $total_compra += $subtotal_producto;

                    $detalles_procesados[] = [
                        'producto' => $producto,
                        'cantidad' => $detalle['cantidad'],
                        'precio_unitario' => $detalle['precio_unitario'],
                        'subtotal' => $subtotal_producto
                    ];
                }

                // 2. Registrar cabecera de compra
                $compra = Compra::create([
                    'fecha' => $now->toDateString(),
                    'proveedor_id' => $request->input('proveedor_id'),
                    'ruc' => $request->input('ruc'),
                    'total' => $total_compra,
                    'estado' => 'REGISTRADA'
                ]);

                // 3. Registrar detalles y actualizar stock + precio de compra del producto
                foreach ($detalles_procesados as $item) {
                    $producto = $item['producto'];

                    CompraDetalle::create([
                        'id_compra' => $compra->id_compra,
                        'id_producto' => $producto->id_producto,
                        'cantidad' => $item['cantidad'],
                        'precio_unitario' => $item['precio_unitario'],
                        'subtotal' => $item['subtotal']
                    ]);

                    // Incrementar stock y actualizar costo de compra en el catálogo
                    $producto->stock_actual += $item['cantidad'];
                    $producto->precio_compra = $item['precio_unitario'];
                    $producto->save();
                }

                return response()->json($compra->load('detalles'), 201);
            });
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al registrar la compra.',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    public function anular($id)
    {
        try {
            return DB::transaction(function () use ($id) {
                $compra = Compra::lockForUpdate()->find($id);
                if (!$compra) {
                    return response()->json(['message' => 'Compra no encontrada'], 404);
                }

                if ($compra->estado === 'ANULADA') {
                    return response()->json(['message' => 'La compra ya se encuentra anulada'], 422);
                }

                $detalles = CompraDetalle::where('id_compra', $compra->id_compra)->get();

                // 1. Validar que tengamos suficiente stock para revertir la compra
                foreach ($detalles as $detalle) {
                    $producto = Producto::lockForUpdate()->find($detalle->id_producto);
                    if ($producto->stock_actual < $detalle->cantidad) {
                        throw new \Exception("No se puede anular la compra. El stock actual de '{$producto->nombre}' ({$producto->stock_actual}) es menor a la cantidad a retirar ({$detalle->cantidad}).");
                    }
                }

                // 2. Retirar stock
                foreach ($detalles as $detalle) {
                    $producto = Producto::lockForUpdate()->find($detalle->id_producto);
                    $producto->stock_actual -= $detalle->cantidad;
                    $producto->save();
                }

                // 3. Cambiar estado a anulada
                $compra->estado = 'ANULADA';
                $compra->save();

                return response()->json([
                    'message' => 'Compra anulada correctamente. Stock retirado.',
                    'compra' => $compra
                ]);
            });
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al anular la compra.',
                'error' => $e->getMessage()
            ], 422);
        }
    }
}
