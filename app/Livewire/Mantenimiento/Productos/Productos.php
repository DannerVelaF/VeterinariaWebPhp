<?php

namespace App\Livewire\Mantenimiento\Productos;

use App\Http\Requests\ProductoRequest;
use App\Models\CategoriaProducto;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\Unidades;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;
use Illuminate\Support\Str;
use Livewire\WithFileUploads;

class Productos extends Component
{
    use WithFileUploads; // ✅ Agregar esta línea
    protected $listeners = [
        'unidadesUpdated' => 'refreshData',
        'categoriaUpdated' => 'refreshData',
        'proveedoresUpdated' => 'refreshData',
    ];
    public $producto = [
        "nombre_producto" => "",
        "descripcion" => "",
        "ruta_imagen" => "",
        "id_categoria_producto" => "",
        "id_proveedor" => "",
        "id_unidad" => "",
        "precio_unitario" => "",
    ];

    public $categorias = [];
    public $proveedores = [];
    public $unidades = [];
    public $codigoBarras;
    public $imagenProducto; // propiedad separada para el archivo
    public $modalEditar = false;
    public $productoEditar = [
        'id_producto' => null,
        'nombre_producto' => '',
        'descripcion' => '',
        'id_categoria_producto' => '',
        'id_proveedor' => '',
        'id_unidad' => '',
        'ruta_imagen' => null,
        'precio_unitario' => '',
        'estado' => '',
    ]; // array para editar
    public $imagenEditar;

    public function mount()
    {
        $this->categorias = CategoriaProducto::where('estado', 'activo')->get();
        $this->proveedores = Proveedor::where('estado', 'activo')->get();
        $this->unidades = Unidades::all();
        $this->generarCodigoBarras();
    }

    #[\Livewire\Attributes\On('unidadesUpdated')]
    #[\Livewire\Attributes\On('categoriaUpdated')]
    #[\Livewire\Attributes\On('proveedoresUpdated')]
    public function refreshData()
    {
        $this->unidades = Unidades::all();
        $this->categorias = CategoriaProducto::where('estado', 'activo')->get();
        $this->proveedores = Proveedor::where('estado', 'activo')->get();
    }


    private function generarPrefijoCategoria($nombreCategoria): string
    {
        $palabras = preg_split('/\s+/', trim($nombreCategoria)); // separar por espacios
        $prefijo = '';

        foreach ($palabras as $palabra) {
            $prefijo .= strtoupper(substr($palabra, 0, 1)); // tomar inicial
            if (strlen($prefijo) >= 2) break; // solo dos letras máximo
        }

        // si solo tenía una palabra → usa hasta 2 letras
        if (strlen($prefijo) < 2) {
            $prefijo = strtoupper(substr($nombreCategoria, 0, 2));
        }

        return $prefijo;
    }

    private function generarCodigoBarras()
    {
        $categoria = CategoriaProducto::find($this->producto['id_categoria_producto']);

        $prefijo = $categoria
            ? $this->generarPrefijoCategoria($categoria->nombre_categoria_producto)
            : 'XX';

        do {
            // Genera un número de 4 dígitos aleatorio
            $codigo = "P." . $prefijo . "-" . random_int(1000, 9999);
        } while (Producto::where('codigo_barras', $codigo)->exists());

        $this->codigoBarras = $codigo;
    }

    public function guardar()
    {
        // Validación
        $validatedData = Validator::make(
            ['producto' => $this->producto],
            (new ProductoRequest)->rules(),
            (new ProductoRequest)->messages()
        )->validate();

        try {
            DB::transaction(function () {
                $this->generarCodigoBarras();
                $imagenPath = null;
                if ($this->imagenProducto) {
                    $imagenPath = $this->imagenProducto->store('productos', 'public');
                }
                Producto::create([
                    "nombre_producto" => $this->producto["nombre_producto"],
                    "descripcion" => $this->producto["descripcion"],
                    "id_categoria_producto" => $this->producto["id_categoria_producto"],
                    "ruta_imagen" => $imagenPath,
                    "id_proveedor" => $this->producto["id_proveedor"],
                    "codigo_barras" => $this->codigoBarras,
                    "id_unidad" => $this->producto["id_unidad"] ?? null,
                    "fecha_registro" => now(),
                    "fecha_actualizacion" => now(),
                    "precio_unitario" => $this->producto["precio_unitario"] ?? null,
                ]);
            });

            // Si llegamos aquí, todo se guardó correctamente
            $this->dispatch('productoRegistrado');
            $this->dispatch(
                'notify',
                title: 'Success',
                description: 'Producto registrado correctamente.',
                type: 'success'
            );
            $this->resetForm();
        } catch (Exception $e) {
            $this->dispatch('notify', title: 'Error', description: 'Error al registrar el producto: ' . $e->getMessage(), type: 'error');
            Log::error('Error al registrar el producto', ['error' => $e->getMessage()]);
        }
    }

    // Método para resetear el formulario
    public function resetForm()
    {
        $this->producto = [
            "nombre_producto" => "",
            "descripcion" => "",
            "id_categoria_producto" => "",
            "id_proveedor" => "",
            "id_unidad" => "",
        ];

        $this->imagenProducto = null;
        $this->codigoBarras = null;

        $this->mount();
    }

    #[\Livewire\Attributes\On('editarProducto')]
    public function abrirModalEditar($productoId)
    {
        $producto = Producto::findOrFail($productoId);

        $this->productoEditar = [
            'id_producto' => $producto->id_producto,
            'nombre_producto' => $producto->nombre_producto,
            'descripcion' => $producto->descripcion,
            'id_categoria_producto' => $producto->id_categoria_producto,
            'id_proveedor' => $producto->id_proveedor,
            'id_unidad' => $producto->id_unidad,
            'ruta_imagen' => $producto->ruta_imagen,
            'estado' => $producto->estado,
            'precio_unitario' => $producto->precio_unitario,
        ];

        $this->imagenEditar = null;
        $this->modalEditar = true;
    }

    public function actualizarProducto()
    {
        $validatedData = Validator::make(array_merge($this->productoEditar, [
            'imagenEditar' => $this->imagenEditar,
        ]), [
            'nombre_producto' => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:1000',
            'id_categoria_producto' => 'required|exists:categoria_productos,id_categoria_producto',
            'id_proveedor' => 'required|exists:proveedores,id_proveedor',
            'id_unidad' => 'required|exists:unidades,id_unidad',
            'imagenEditar' => 'nullable|image|max:2048',
            'precio_unitario' => 'required|numeric|min:1',
            'estado' => 'required|in:activo,inactivo',
        ])->validate();

        try {
            DB::transaction(function () use ($validatedData) {
                $producto = Producto::findOrFail($this->productoEditar['id_producto']);

                // Si hay nueva imagen, borrar la anterior
                if ($this->imagenEditar) {
                    if ($producto->ruta_imagen && Storage::disk('public')->exists($producto->ruta_imagen)) {
                        Storage::disk('public')->delete($producto->ruta_imagen);
                    }
                    // Guardar la nueva imagen
                    $imagenPath = $this->imagenEditar->store('productos', 'public');
                    $producto->ruta_imagen = $imagenPath;
                }

                $producto->update([
                    'nombre_producto' => $validatedData['nombre_producto'],
                    'descripcion' => $validatedData['descripcion'],
                    'id_categoria_producto' => $validatedData['id_categoria_producto'],
                    'id_proveedor' => $validatedData['id_proveedor'],
                    'id_unidad' => $validatedData['id_unidad'],
                    'precio_unitario' => $validatedData['precio_unitario'],
                    "fecha_actualizacion" => now(),
                    'estado' => $validatedData['estado'],
                ]);
            });

            $this->dispatch('productoRegistrado'); // refresca tabla
            $this->dispatch('notify', title: 'Success', description: 'Producto actualizado correctamente.', type: 'success');
            $this->modalEditar = false;
        } catch (Exception $e) {
            Log::error('Error al actualizar el producto', ['error' => $e->getMessage()]);
            $this->dispatch('notify', title: 'Error', description: 'Error al actualizar el producto: ' . $e->getMessage(), type: 'error');
        }
    }

    public function render()
    {
        return view('livewire.mantenimiento.productos.productos');
    }
}
