<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Proveedor;
use Illuminate\Http\Request;

class ProveedorController extends Controller
{
    public function index(Request $request)
    {
        $query = Proveedor::query();

        if ($request->has('buscar')) {
            $buscar = $request->input('buscar');
            $query->where('razon_social', 'like', "%{$buscar}%")
                  ->orWhere('contacto_nombre', 'like', "%{$buscar}%")
                  ->orWhere('contacto_email', 'like', "%{$buscar}%");
        }

        if ($request->has('activo')) {
            $query->where('activo', $request->boolean('activo'));
        }

        $proveedores = $query->paginate($request->input('per_page', 15));
        return response()->json($proveedores);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'razon_social' => 'required|string|max:200',
            'contacto_nombre' => 'nullable|string|max:200',
            'contacto_telefono' => 'nullable|string|max:20',
            'contacto_email' => 'nullable|email|max:100',
            'direccion' => 'nullable|string',
            'activo' => 'nullable|boolean'
        ]);

        $proveedor = Proveedor::create($validated);
        return response()->json($proveedor, 201);
    }

    public function show($id)
    {
        $proveedor = Proveedor::find($id);
        if (!$proveedor) {
            return response()->json(['message' => 'Proveedor no encontrado'], 404);
        }
        return response()->json($proveedor);
    }

    public function update(Request $request, $id)
    {
        $proveedor = Proveedor::find($id);
        if (!$proveedor) {
            return response()->json(['message' => 'Proveedor no encontrado'], 404);
        }

        $validated = $request->validate([
            'razon_social' => 'required|string|max:200',
            'contacto_nombre' => 'nullable|string|max:200',
            'contacto_telefono' => 'nullable|string|max:20',
            'contacto_email' => 'nullable|email|max:100',
            'direccion' => 'nullable|string',
            'activo' => 'nullable|boolean'
        ]);

        $proveedor->update($validated);
        return response()->json($proveedor);
    }

    public function destroy($id)
    {
        $proveedor = Proveedor::find($id);
        if (!$proveedor) {
            return response()->json(['message' => 'Proveedor no encontrado'], 404);
        }

        if ($proveedor->compras()->count() > 0) {
            $proveedor->update(['activo' => false]);
            return response()->json([
                'message' => 'El proveedor tiene compras registradas. Se ha cambiado su estado a inactivo.'
            ]);
        }

        $proveedor->delete();
        return response()->json(['message' => 'Proveedor eliminado correctamente']);
    }
}
