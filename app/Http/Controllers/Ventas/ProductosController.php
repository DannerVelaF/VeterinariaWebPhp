<?php

namespace App\Http\Controllers\Ventas;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductoResponse;
use App\Models\CategoriaProducto;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProductosController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Producto::with(['categoria_producto', 'unidad', 'lotes']) // 🔹 AGREGAR 'lotes'
                ->where('estado', 'activo');

            // Filtro por categoría
            if ($request->has('categoria_id')) {
                $query->where('id_categoria_producto', $request->categoria_id);
            }

            // Filtro por búsqueda
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('nombre_producto', 'like', "%{$search}%")
                        ->orWhere('descripcion', 'like', "%{$search}%");
                });
            }

            $productos = $query->get();

            Log::info('Productos obtenidos', ['productos' => $productos]);

            return ProductoResponse::collection($productos);
        } catch (\Exception $e) {
            Log::error('Error al obtener productos', ['error' => $e->getMessage()]);
            return response()->json([
                'error' => 'Error al obtener productos',
                'detalle' => $e->getMessage()
            ], 500);
        }
    }

    public function categorias()
    {
        try {
            $categorias = CategoriaProducto::where('estado', 'activo')->get();
            return response()->json($categorias);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al obtener categorías',
                'detalle' => $e->getMessage()
            ], 500);
        }
    }
}
