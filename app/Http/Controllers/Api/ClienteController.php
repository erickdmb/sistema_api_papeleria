<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    public function index(Request $request)
    {
        $query = Cliente::query();

        if ($request->has('buscar')) {
            $buscar = $request->input('buscar');
            $query->where('nombre', 'like', "%{$buscar}%")
                  ->orWhere('telefono', 'like', "%{$buscar}%")
                  ->orWhere('email', 'like', "%{$buscar}%");
        }

        if ($request->boolean('all', false) || $request->input('paginate') === 'false') {
            $clientes = $query->get();
        } else {
            $clientes = $query->paginate($request->input('per_page', 15));
        }
        return response()->json($clientes);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:200',
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
        ]);

        $cliente = Cliente::create($validated);
        return response()->json($cliente, 201);
    }

    public function show($id)
    {
        $cliente = Cliente::find($id);
        if (!$cliente) {
            return response()->json(['message' => 'Cliente no encontrado'], 404);
        }
        return response()->json($cliente);
    }

    public function update(Request $request, $id)
    {
        $cliente = Cliente::find($id);
        if (!$cliente) {
            return response()->json(['message' => 'Cliente no encontrado'], 404);
        }

        $validated = $request->validate([
            'nombre' => 'required|string|max:200',
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
        ]);

        $cliente->update($validated);
        return response()->json($cliente);
    }

    public function destroy($id)
    {
        $cliente = Cliente::find($id);
        if (!$cliente) {
            return response()->json(['message' => 'Cliente no encontrado'], 404);
        }

        if ($cliente->ventas()->count() > 0) {
            return response()->json([
                'message' => 'No se puede eliminar el cliente porque tiene ventas asociadas.'
            ], 422);
        }

        $cliente->delete();
        return response()->json(['message' => 'Cliente eliminado correctamente']);
    }

    public function ventas($id)
    {
        $cliente = Cliente::find($id);
        if (!$cliente) {
            return response()->json(['message' => 'Cliente no encontrado'], 404);
        }

        $ventas = $cliente->ventas()->with(['metodoPago', 'detalles.producto'])->orderBy('id_venta', 'desc')->get();
        return response()->json($ventas);
    }
}
