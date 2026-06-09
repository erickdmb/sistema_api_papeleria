<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use Illuminate\Http\Request;

class CategoriaController extends Controller
{
    public function index()
    {
        $categorias = Categoria::all();
        return response()->json($categorias);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:50|unique:categorias,nombre',
            'descripcion' => 'nullable|string|max:255',
            'estado' => 'nullable|boolean'
        ]);

        $categoria = Categoria::create($validated);
        return response()->json($categoria, 201);
    }

    public function show($id)
    {
        $categoria = Categoria::find($id);
        if (!$categoria) {
            return response()->json(['message' => 'Categoría no encontrada'], 404);
        }
        return response()->json($categoria);
    }

    public function update(Request $request, $id)
    {
        $categoria = Categoria::find($id);
        if (!$categoria) {
            return response()->json(['message' => 'Categoría no encontrada'], 404);
        }

        $validated = $request->validate([
            'nombre' => 'required|string|max:50|unique:categorias,nombre,' . $id . ',id_categoria',
            'descripcion' => 'nullable|string|max:255',
            'estado' => 'nullable|boolean'
        ]);

        $categoria->update($validated);
        return response()->json($categoria);
    }

    public function destroy($id)
    {
        $categoria = Categoria::find($id);
        if (!$categoria) {
            return response()->json(['message' => 'Categoría no encontrada'], 404);
        }

        if ($categoria->productos()->count() > 0) {
            $categoria->update(['estado' => false]);
            return response()->json([
                'message' => 'La categoría tiene productos asociados. Se ha cambiado su estado a inactivo.',
                'categoria' => $categoria
            ]);
        }

        $categoria->delete();
        return response()->json(['message' => 'Categoría eliminada correctamente']);
    }
}
