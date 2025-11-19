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
    use WithFileUploads;

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
        "proveedores_seleccionados" => [], // ← CAMBIAR a array
        "id_unidad" => "",
        "precio_unitario" => "",
        "cantidad_por_unidad" => "",
    ];

    public $categorias = [];
    public $proveedores = [];
    public $unidades = [];
    public $codigoBarras;
    public $imagenProducto;
    public $modalEditar = false;
    public $productoEditar = [
        'id_producto' => null,
        'nombre_producto' => '',
        'descripcion' => '',
        'id_categoria_producto' => '',
        'proveedores_seleccionados' => [], // ← CAMBIAR a array
        'id_unidad' => '',
        'ruta_imagen' => null,
        'precio_unitario' => '',
        'cantidad_por_unidad' => '',
        'estado' => '',
    ];
    public $imagenEditar;


    public function mount()
    {
        $this->categorias = CategoriaProducto::where('estado', 'activo')->get();
        $this->proveedores = Proveedor::where('estado', 'activo')->get();
        $this->unidades = Unidades::where('estado', 'activo')->get();
        $this->generarCodigoBarras();
    }

    public function updated($propertyName)
    {

        if (str_starts_with($propertyName, 'producto.proveedores_seleccionados')) {
            // Dar tiempo a Alpine.js para convertir el string a array
            usleep(100000); // 100ms de delay

            // Convertir a array antes de validar
            $this->convertirProveedoresAArray();
        }


        // Validar solo los campos del producto en tiempo real
        if (str_starts_with($propertyName, 'producto.')) {
            $this->validateOnly($propertyName, $this->getValidationRules(), (new ProductoRequest)->messages());
        }

        // Validar campos del productoEditar en tiempo real
        if (str_starts_with($propertyName, 'productoEditar.')) {
            $rules = $this->getEdicionValidationRules();
            $this->validateOnly($propertyName, $rules, $this->getEdicionMessages());
        }
    }

    private function getEdicionValidationRules()
    {
        $rules = [
            'productoEditar.nombre_producto' => 'required|string|max:255',
            'productoEditar.descripcion' => 'nullable|string|max:1000',
            'productoEditar.id_categoria_producto' => 'required|exists:categoria_productos,id_categoria_producto',
            'productoEditar.proveedores_seleccionados' => 'required|array|min:1', // ← CAMBIAR
            'productoEditar.proveedores_seleccionados.*' => 'exists:proveedores,id_proveedor', // ← NUEVO
            'productoEditar.id_unidad' => 'required|exists:unidades,id_unidad',
            'productoEditar.precio_unitario' => 'required|numeric|min:1',
            'productoEditar.estado' => 'required|in:activo,inactivo',
        ];

        // Validación condicional para cantidad_por_unidad en edición
        if ($this->unidadEditarRequiereCantidad()) {
            $rules['productoEditar.cantidad_por_unidad'] = 'required|integer|min:1';
        } else {
            $rules['productoEditar.cantidad_por_unidad'] = 'nullable|integer|min:0';
        }

        return $rules;
    }

    /**
     * Obtener mensajes para edición
     */
    private function getEdicionMessages()
    {
        return [
            'productoEditar.nombre_producto.required' => 'El nombre del producto es obligatorio.',
            'productoEditar.nombre_producto.max' => 'El nombre no puede tener más de 255 caracteres.',
            'productoEditar.descripcion.max' => 'La descripción no puede tener más de 1000 caracteres.',
            'productoEditar.id_categoria_producto.required' => 'Debe seleccionar una categoría.',
            'productoEditar.id_categoria_producto.exists' => 'La categoría seleccionada no es válida.',
            'productoEditar.proveedores_seleccionados.required' => 'Debe seleccionar al menos un proveedor.', // ← ACTUALIZAR
            'productoEditar.proveedores_seleccionados.min' => 'Debe seleccionar al menos un proveedor.', // ← NUEVO
            'productoEditar.proveedores_seleccionados.*.exists' => 'Uno de los proveedores seleccionados no es válido.', // ← NUEVO
            'productoEditar.id_unidad.required' => 'La unidad es obligatoria.',
            'productoEditar.id_unidad.exists' => 'La unidad seleccionada no es válida.',
            'productoEditar.precio_unitario.required' => 'El precio unitario es obligatorio.',
            'productoEditar.precio_unitario.numeric' => 'El precio unitario debe ser un número',
            'productoEditar.precio_unitario.min' => 'El precio unitario debe ser mayor a 0',
            'productoEditar.estado.required' => 'El estado es obligatorio.',
            'productoEditar.estado.in' => 'El estado debe ser activo o inactivo.',
            'productoEditar.cantidad_por_unidad.required' => 'La cantidad contenida en la unidad es obligatoria para este tipo de unidad.',
            'productoEditar.cantidad_por_unidad.integer' => 'La cantidad debe ser un número entero.',
            'productoEditar.cantidad_por_unidad.min' => 'La cantidad debe ser al menos 1.',
        ];
    }


    #[\Livewire\Attributes\On('unidadesUpdated')]
    #[\Livewire\Attributes\On('categoriaUpdated')]
    #[\Livewire\Attributes\On('proveedoresUpdated')]
    public function refreshData()
    {
        $this->unidades = Unidades::where('estado', 'activo')->get();
        $this->categorias = CategoriaProducto::where('estado', 'activo')->get();
        $this->proveedores = Proveedor::where('estado', 'activo')->get();
    }

    // ✅ Método para verificar si la unidad seleccionada requiere cantidad
    public function unidadRequiereCantidad($unidadId = null)
    {
        if (!$unidadId) {
            $unidadId = $this->producto['id_unidad'];
        }

        if (!$unidadId) return false;

        $unidad = Unidades::find($unidadId);
        return $unidad ? $unidad->contiene_unidades : false;
    }

    // ✅ Para el modal de edición
    public function unidadEditarRequiereCantidad($unidadId = null)
    {
        if (!$unidadId) {
            $unidadId = $this->productoEditar['id_unidad'];
        }

        if (!$unidadId) return false;

        $unidad = Unidades::find($unidadId);
        return $unidad ? $unidad->contiene_unidades : false;
    }

    private function generarPrefijoCategoria($nombreCategoria): string
    {
        $palabras = preg_split('/\s+/', trim($nombreCategoria));
        $prefijo = '';

        foreach ($palabras as $palabra) {
            $prefijo .= strtoupper(substr($palabra, 0, 1));
            if (strlen($prefijo) >= 2) break;
        }

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
            $codigo = "P." . $prefijo . "-" . random_int(1000, 9999);
        } while (Producto::where('codigo_barras', $codigo)->exists());

        $this->codigoBarras = $codigo;
    }

    public function guardar()
    {
        $this->convertirProveedoresAArray();

        // ✅ Validación actualizada con reglas dinámicas
        $validatedData = Validator::make(
            ['producto' => $this->producto],
            $this->getValidationRules(),
            (new ProductoRequest)->messages()
        )->validate();

        try {
            // Validar que no exista un producto con el mismo nombre (case insensitive)
            $nombreNormalizado = strtolower(trim($this->producto['nombre_producto']));
            $existeProducto = Producto::whereRaw('LOWER(TRIM(nombre_producto)) = ?', [$nombreNormalizado])
                ->exists();

            if ($existeProducto) {
                $this->dispatch('notify', title: 'Error', description: 'Ya existe un producto con ese nombre.', type: 'error');
                return;
            }

            DB::transaction(function () {
                $this->generarCodigoBarras();
                $imagenPath = null;
                if ($this->imagenProducto) {
                    $imagenPath = $this->imagenProducto->store('productos', 'public');
                }

                // Convertir valores vacíos a null
                $cantidadPorUnidad = $this->producto["cantidad_por_unidad"] === '' ? null : $this->producto["cantidad_por_unidad"];

                // Crear el producto
                $producto = Producto::create([
                    "nombre_producto" => $this->producto["nombre_producto"],
                    "descripcion" => $this->producto["descripcion"],
                    "id_categoria_producto" => $this->producto["id_categoria_producto"],
                    "ruta_imagen" => $imagenPath,
                    "codigo_barras" => $this->codigoBarras,
                    "id_unidad" => $this->producto["id_unidad"] ?? null,
                    "cantidad_por_unidad" => $cantidadPorUnidad, // ← USAR LA VARIABLE CONVERSION
                    "fecha_registro" => now(),
                    "fecha_actualizacion" => now(),
                    "precio_unitario" => $this->producto["precio_unitario"] ?? null,
                ]);

                // Sincronizar proveedores (relación muchos a muchos)
                if (!empty($this->producto['proveedores_seleccionados'])) {
                    $producto->proveedores()->sync($this->producto['proveedores_seleccionados']);
                }
            });

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

    private function convertirProveedoresAArray()
    {
        if (is_string($this->producto['proveedores_seleccionados'])) {
            try {
                $proveedoresArray = json_decode($this->producto['proveedores_seleccionados'], true);
                $this->producto['proveedores_seleccionados'] = is_array($proveedoresArray) ? $proveedoresArray : [];
            } catch (\Exception $e) {
                $this->producto['proveedores_seleccionados'] = [];
            }
        }

        // Asegurarse de que siempre sea un array
        if (!is_array($this->producto['proveedores_seleccionados'])) {
            $this->producto['proveedores_seleccionados'] = [];
        }
    }

    // ✅ Método para obtener reglas de validación dinámicas
    private function getValidationRules()
    {
        $rules = (new ProductoRequest)->rules();

        // Actualizar regla para proveedores (ahora es array)
        $rules['producto.proveedores_seleccionados'] = 'required|array|min:1';
        $rules['producto.proveedores_seleccionados.*'] = 'exists:proveedores,id_proveedor';

        // ✅ Agregar validación condicional para cantidad_por_unidad
        if ($this->unidadRequiereCantidad()) {
            $rules['producto.cantidad_por_unidad'] = 'required|integer|min:1';
        } else {
            $rules['producto.cantidad_por_unidad'] = 'nullable|integer|min:0';
        }

        return $rules;
    }

    // Método para resetear el formulario
    public function resetForm()
    {
        $this->producto = [
            "nombre_producto" => "",
            "descripcion" => "",
            "id_categoria_producto" => "",
            "proveedores_seleccionados" => [], // ← Array vacío
            "id_unidad" => "",
            "precio_unitario" => "",
            "cantidad_por_unidad" => "",
        ];

        $this->imagenProducto = null;
        $this->codigoBarras = null;
        $this->resetErrorBag();
        $this->resetValidation();
        $this->mount();
    }

    #[\Livewire\Attributes\On('editarProducto')]
    public function abrirModalEditar($productoId)
    {
        $producto = Producto::with('proveedores')->findOrFail($productoId);

        // ✅ Asegurar que proveedores_seleccionados sea un array
        $proveedoresArray = $producto->proveedores->pluck('id_proveedor')->toArray();

        $this->productoEditar = [
            'id_producto' => $producto->id_producto,
            'nombre_producto' => $producto->nombre_producto,
            'descripcion' => $producto->descripcion,
            'id_categoria_producto' => $producto->id_categoria_producto,
            'proveedores_seleccionados' => $proveedoresArray, // ← Ya es array
            'id_unidad' => $producto->id_unidad,
            'ruta_imagen' => $producto->ruta_imagen,
            'cantidad_por_unidad' => $producto->cantidad_por_unidad,
            'estado' => $producto->estado,
            'precio_unitario' => $producto->precio_unitario,
        ];

        $this->imagenEditar = null;
        $this->modalEditar = true;

        // ✅ Limpiar errores de validación al abrir el modal
        $this->resetErrorBag();
        $this->resetValidation();

        // Forzar actualización para que Alpine.js reciba los datos actualizados
        $this->dispatch('modalEditarAbierto');
    }

    public function updatedModalEditar($value)
    {
        if ($value) {
            // Cuando el modal se abre, limpiar cualquier error de validación
            $this->resetErrorBag();
            $this->resetValidation();
        }
    }

    public function actualizarProducto()
    {
        $this->convertirProveedoresEditarAArray();

        $rules = [
            'nombre_producto' => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:1000',
            'id_categoria_producto' => 'required|exists:categoria_productos,id_categoria_producto',
            'proveedores_seleccionados' => 'required|array|min:1',
            'proveedores_seleccionados.*' => 'exists:proveedores,id_proveedor',
            'id_unidad' => 'required|exists:unidades,id_unidad',
            'imagenEditar' => 'nullable|image|max:2048',
            "cantidad_por_unidad" => "nullable|integer|min:0",
            'precio_unitario' => 'required|numeric|min:1',
            'estado' => 'required|in:activo,inactivo',
        ];

        // ✅ Validación condicional para edición
        if ($this->unidadEditarRequiereCantidad()) {
            $rules['cantidad_por_unidad'] = 'required|integer|min:1';
        } else {
            $rules['cantidad_por_unidad'] = 'nullable|integer|min:0';
        }

        $validatedData = Validator::make(array_merge($this->productoEditar, [
            'imagenEditar' => $this->imagenEditar,
        ]), $rules)->validate();

        try {
            // Validar que no exista otro producto con el mismo nombre (excluyendo el actual)
            $nombreNormalizado = strtolower(trim($this->productoEditar['nombre_producto']));
            $existeProducto = Producto::whereRaw('LOWER(TRIM(nombre_producto)) = ?', [$nombreNormalizado])
                ->where('id_producto', '!=', $this->productoEditar['id_producto'])
                ->exists();

            if ($existeProducto) {
                $this->dispatch('notify', title: 'Error', description: 'Ya existe otro producto con ese nombre.', type: 'error');
                return;
            }

            DB::transaction(function () use ($validatedData) {
                $producto = Producto::findOrFail($this->productoEditar['id_producto']);

                // Si hay nueva imagen, borrar la anterior
                if ($this->imagenEditar) {
                    if ($producto->ruta_imagen && Storage::disk('public')->exists($producto->ruta_imagen)) {
                        Storage::disk('public')->delete($producto->ruta_imagen);
                    }
                    $imagenPath = $this->imagenEditar->store('productos', 'public');
                    $producto->ruta_imagen = $imagenPath;
                }

                // Convertir valores vacíos a null
                $cantidadPorUnidad = $validatedData['cantidad_por_unidad'] === '' ? null : $validatedData['cantidad_por_unidad'];

                $producto->update([
                    'nombre_producto' => $validatedData['nombre_producto'],
                    'descripcion' => $validatedData['descripcion'],
                    'id_categoria_producto' => $validatedData['id_categoria_producto'],
                    'id_unidad' => $validatedData['id_unidad'],
                    'cantidad_por_unidad' => $cantidadPorUnidad, // ← USAR LA VARIABLE CONVERSION
                    'precio_unitario' => $validatedData['precio_unitario'],
                    "fecha_actualizacion" => now(),
                    'estado' => $validatedData['estado'],
                ]);

                // Sincronizar proveedores (relación muchos a muchos)
                $producto->proveedores()->sync($validatedData['proveedores_seleccionados']);
            });

            $this->dispatch('productoRegistrado');
            $this->dispatch('notify', title: 'Success', description: 'Producto actualizado correctamente.', type: 'success');
            $this->modalEditar = false;
        } catch (Exception $e) {
            Log::error('Error al actualizar el producto', ['error' => $e->getMessage()]);
            $this->dispatch('notify', title: 'Error', description: 'Error al actualizar el producto: ' . $e->getMessage(), type: 'error');
        }
    }

    private function convertirProveedoresEditarAArray()
    {
        if (is_string($this->productoEditar['proveedores_seleccionados'])) {
            try {
                $proveedoresArray = json_decode($this->productoEditar['proveedores_seleccionados'], true);
                $this->productoEditar['proveedores_seleccionados'] = is_array($proveedoresArray) ? $proveedoresArray : [];
            } catch (\Exception $e) {
                $this->productoEditar['proveedores_seleccionados'] = [];
            }
        }

        if (!is_array($this->productoEditar['proveedores_seleccionados'])) {
            $this->productoEditar['proveedores_seleccionados'] = [];
        }
    }

    public function render()
    {
        return view('livewire.mantenimiento.productos.productos');
    }
}
