<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    public function index(Request $request)
    {
        $query = Producto::with('categoria');

        if ($request->has('buscar')) {
            $buscar = $request->input('buscar');
            $query->where(function($q) use ($buscar) {
                $q->where('nombre', 'like', "%{$buscar}%")
                  ->orWhere('sku', 'like', "%{$buscar}%")
                  ->orWhere('marca', 'like', "%{$buscar}%");
            });
        }

        if ($request->has('categoria_id')) {
            $query->where('categoria_id', $request->input('categoria_id'));
        }

        if ($request->has('bajo_stock') && $request->input('bajo_stock') == '1') {
            $query->whereColumn('stock_actual', '<=', 'stock_minimo');
        }

        if ($request->has('activo')) {
            $query->where('activo', $request->boolean('activo'));
        }

        $productos = $query->paginate($request->input('per_page', 15));
        return response()->json($productos);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:200',
            'descripcion' => 'nullable|string',
            'categoria_id' => 'required|exists:categorias,id_categoria',
            'precio_compra' => 'required|numeric|min:0',
            'precio_venta' => 'required|numeric|min:0|gte:precio_compra',
            'stock_actual' => 'nullable|integer|min:0',
            'stock_minimo' => 'nullable|integer|min:0',
            'sku' => 'required|string|max:50|unique:productos,sku',
            'marca' => 'nullable|string|max:100',
            'color' => 'nullable|string|max:50',
            'tamaño' => 'nullable|string|max:50',
            'activo' => 'nullable|boolean'
        ]);

        $producto = Producto::create($validated);
        return response()->json($producto, 201);
    }

    public function show($id)
    {
        $producto = Producto::with('categoria')->find($id);
        if (!$producto) {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }
        return response()->json($producto);
    }

    public function update(Request $request, $id)
    {
        $producto = Producto::find($id);
        if (!$producto) {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }

        $validated = $request->validate([
            'nombre' => 'required|string|max:200',
            'descripcion' => 'nullable|string',
            'categoria_id' => 'required|exists:categorias,id_categoria',
            'precio_compra' => 'required|numeric|min:0',
            'precio_venta' => 'required|numeric|min:0|gte:precio_compra',
            'stock_actual' => 'nullable|integer|min:0',
            'stock_minimo' => 'nullable|integer|min:0',
            'sku' => 'required|string|max:50|unique:productos,sku,' . $id . ',id_producto',
            'marca' => 'nullable|string|max:100',
            'color' => 'nullable|string|max:50',
            'tamaño' => 'nullable|string|max:50',
            'activo' => 'nullable|boolean'
        ]);

        $producto->update($validated);
        return response()->json($producto);
    }

    public function destroy($id)
    {
        $producto = Producto::find($id);
        if (!$producto) {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }

        $hasVentas = $producto->ventaDetalles()->count() > 0;
        $hasCompras = $producto->compraDetalles()->count() > 0;

        if ($hasVentas || $hasCompras) {
            $producto->update(['activo' => false]);
            return response()->json([
                'message' => 'El producto tiene transacciones registradas. Se ha cambiado su estado a inactivo.',
                'producto' => $producto
            ]);
        }

        $producto->delete();
        return response()->json(['message' => 'Producto eliminado correctamente']);
    }
}
