<?php

namespace App\Http\Controllers\Ventas;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductoResponse;
use App\Models\CategoriaProducto;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

    public function countProductosCategorias()
    {
        try {
            $categorias = DB::table('productos as p')
                ->join('categoria_productos as cp', 'p.id_categoria_producto', '=', 'cp.id_categoria_producto')
                ->where('p.estado', 'activo')
                ->select(
                    'cp.id_categoria_producto as id_categoria_producto',
                    'cp.nombre_categoria_producto as nombre_categoria',
                    DB::raw('COUNT(p.id_producto) as cantidad_productos')
                )
                ->groupBy('cp.id_categoria_producto', 'cp.nombre_categoria_producto')
                ->get();

            return response()->json($categorias);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al obtener categorías',
                'detalle' => $e->getMessage()
            ], 500);
        }
    }

    public function getProductosDestacados()
    {
        try {
            $productos = Producto::with(['categoria_producto', 'unidad', 'lotes'])
                ->where('estado', 'activo')
                ->whereHas('lotes', function ($query) {
                    $query->where('estado', 'activo')
                        ->where(function ($q) {
                            $q->where('cantidad_almacenada', '>', 0)
                                ->orWhere('cantidad_mostrada', '>', 0);
                        });
                })
                ->orderBy('fecha_registro', 'desc')
                ->limit(6)
                ->get();

            return response()->json($productos);
        } catch (\Exception $e) {
            Log::error('Error al obtener productos destacados', ['error' => $e->getMessage()]);
            return response()->json([
                'error' => 'Error al obtener productos destacados',
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
