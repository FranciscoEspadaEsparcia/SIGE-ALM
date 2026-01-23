<?php

namespace App\Http\Controllers;

use App\Models\Articulo;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ArticuloController extends Controller
{
    /**
     * HU-02: Consultar artículos (listado)
     * Soporta búsqueda simple por ?search=
     */
    public function index(Request $request)
    {
        $search = $request->query('search');

        $query = Articulo::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                  ->orWhere('codigo', 'like', "%{$search}%");
            });
        }

        // Para evidencias y consumo rápido: paginación
        $articulos = $query->orderByDesc('id')->paginate(20);

        return response()->json($articulos);
    }

    /**
     * HU-05: Ver detalle de artículo
     */
    public function show($id)
    {
        $articulo = Articulo::findOrFail($id);
        return response()->json($articulo);
    }

    /**
     * HU-01: Crear artículo
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'codigo' => ['required', 'string', 'max:50', 'unique:articulos,codigo'],
            'nombre' => ['required', 'string', 'max:150'],
            'descripcion' => ['nullable', 'string'],
            'stock_actual' => ['nullable', 'integer', 'min:0'],
            'stock_minimo' => ['nullable', 'integer', 'min:0'],
            'activo' => ['nullable', 'boolean'],
            'id_categoria' => ['required', 'integer', 'exists:categorias,id'],
            'id_proveedor_preferente' => ['required', 'integer', 'exists:proveedores,id']
        ]);

        // Defaults coherentes
        $data['stock_actual'] = $data['stock_actual'] ?? 0;
        $data['stock_minimo'] = $data['stock_minimo'] ?? 0;
        $data['activo'] = $data['activo'] ?? true;

        $articulo = Articulo::create($data);

        return response()->json($articulo, 201);
    }

    /**
     * HU-03: Editar artículo
     */
    public function update(Request $request, $id)
    {
        $articulo = Articulo::findOrFail($id);

        $data = $request->validate([
            'codigo' => [
                'sometimes', 'required', 'string', 'max:50',
                Rule::unique('articulos', 'codigo')->ignore($articulo->id),
            ],
            'nombre' => ['sometimes', 'required', 'string', 'max:150'],
            'descripcion' => ['sometimes', 'nullable', 'string'],
            'stock_actual' => ['sometimes', 'integer', 'min:0'],
            'stock_minimo' => ['sometimes', 'integer', 'min:0'],
            'activo' => ['sometimes', 'boolean'],
            'id_categoria' => ['sometimes', 'nullable', 'integer', 'exists:categorias,id'],
            'id_proveedor_preferente' => ['sometimes', 'nullable', 'integer', 'exists:proveedores,id'],
        ]);

        $articulo->update($data);

        return response()->json($articulo);
    }

    /**
     * HU-04: Eliminar artículo
     * Recomendado: bloquear si tiene movimientos asociados
     */
    public function destroy($id)
    {
        $articulo = Articulo::findOrFail($id);

        if ($articulo->movimientos()->exists()) {
            return response()->json([
                'message' => 'No se puede eliminar el artículo porque tiene movimientos asociados.'
            ], 409);
        }

        $articulo->delete();

        return response()->json(null, 204);
    }

    /**
     * Sprint 1: Consulta de stock
     * GET /api/articulos/stock?id_articulo=1
     */
    public function consultaStock($id)
{
    $articulo = Articulo::findOrFail($id);

    return response()->json([
        'id_articulo' => $articulo->id,
        'stock_actual' => $articulo->stock_actual
    ]);
}


}
