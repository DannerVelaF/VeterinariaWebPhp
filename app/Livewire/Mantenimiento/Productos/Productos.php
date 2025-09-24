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
use Illuminate\Support\Facades\Validator;
use Livewire\Component;
use Illuminate\Support\Str;

class Productos extends Component
{
    public $producto = [
        "nombre_producto" => "",
        "descripcion" => "",
        "id_categoria_producto" => "",
        "id_proveedor" => "",
    ];

    public $categorias = [];
    public $proveedores = [];
    public $unidades = [];
    public $codigoBarras;


    public function mount()
    {
        $this->categorias = CategoriaProducto::where('estado', 'activo')->get();
        $this->proveedores = Proveedor::where('estado', 'activo')->get();
        $this->unidades = Unidades::all();
        $this->generarCodigoBarras();
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
            ? $this->generarPrefijoCategoria($categoria->nombre)
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

                Producto::create([
                    "nombre_producto" => $this->producto["nombre_producto"],
                    "descripcion" => $this->producto["descripcion"],
                    "id_categoria_producto" => $this->producto["id_categoria_producto"],
                    "id_proveedor" => $this->producto["id_proveedor"],
                    "codigo_barras" => $this->codigoBarras,
                    "id_unidad" => $this->producto["id_unidad"] ?? null,
                    "fecha_registro" => now()
                ]);
            });

            // Si llegamos aquí, todo se guardó correctamente
            $this->dispatch('productoRegistrado');
            session()->flash('success', '✅ Producto registrado con éxito. Código de: ' . $this->codigoBarras);
            $this->resetForm();
        } catch (Exception $e) {
            session()->flash('error', 'Error al registrar el producto: ' . $e->getMessage());
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

        $this->codigoBarras = null;

        $this->mount();
    }

    #[\Livewire\Attributes\On('mostrarDescripcion')]
    public function mostrarDescripcion(string $descripcion): void
    {
        $this->dispatch('open-modal', descripcion: $descripcion);
    }

    public function render()
    {
        return view('livewire.mantenimiento.productos.productos');
    }
}
