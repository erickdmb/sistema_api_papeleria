<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MetodoPago;
use Illuminate\Http\Request;

class MetodoPagoController extends Controller
{
    public function index()
    {
        $metodos = MetodoPago::all();
        return response()->json($metodos);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:50|unique:metodos_pago,nombre',
            'descripcion' => 'nullable|string|max:100',
            'activo' => 'nullable|boolean'
        ]);

        $metodo = MetodoPago::create($validated);
        return response()->json($metodo, 201);
    }

    public function show($id)
    {
        $metodo = MetodoPago::find($id);
        if (!$metodo) {
            return response()->json(['message' => 'Método de pago no encontrado'], 404);
        }
        return response()->json($metodo);
    }

    public function update(Request $request, $id)
    {
        $metodo = MetodoPago::find($id);
        if (!$metodo) {
            return response()->json(['message' => 'Método de pago no encontrado'], 404);
        }

        $validated = $request->validate([
            'nombre' => 'required|string|max:50|unique:metodos_pago,nombre,' . $id . ',id_metodo_pago',
            'descripcion' => 'nullable|string|max:100',
            'activo' => 'nullable|boolean'
        ]);

        $metodo->update($validated);
        return response()->json($metodo);
    }
}
