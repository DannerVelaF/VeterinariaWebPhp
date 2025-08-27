<?php

namespace App\Livewire\Mantenimiento\Productos;

use App\Http\Requests\ProductoRequest;
use App\Models\CategoriaProducto;
use App\Models\Producto;
use App\Models\Proveedor;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;
use Illuminate\Support\Str;

class Productos extends Component
{
    public $producto = [
        "nombre_producto" => "",
        "descripcion" => "",
        "precio_unitario" => "",
        "stock" => 0,
        "id_categoria_producto" => "",
        "id_proveedor" => "",
    ];

    public $categorias = [];
    public $proveedores = [];
    public $codigoBarras;


    public function mount()
    {
        $this->categorias = CategoriaProducto::where('estado', 'activo')->get();
        $this->proveedores = Proveedor::where('estado', 'activo')->get();
        $this->generarCodigoBarras();
    }
    private function generarCodigoBarras()
    {
        do {
            $codigo = "PROD-" . strtoupper(Str::random(8));
        } while (Producto::where('codigo_barras', $codigo)->exists());

        $this->codigoBarras = $codigo;
    }

    public function guardar()
    {

        $validatedData = Validator::make(
            [
                'producto' => $this->producto,
            ],
            (new ProductoRequest)->rules(),
            (new ProductoRequest)->messages()
        )->validate();

        $codigoBarras = "PROD-" . strtoupper(Str::random(8));

        try {
            // Generar código de barras único
            while (Producto::where('codigo_barras', $this->codigoBarras)->exists()) {
                $this->generarCodigoBarras();
            }

            $producto = Producto::create([
                "nombre_producto" => $this->producto["nombre_producto"],
                "descripcion" => $this->producto["descripcion"],
                "precio_unitario" => $this->producto["precio_unitario"],
                "stock" => $this->producto["stock"],
                "id_categoria_producto" => $this->producto["id_categoria_producto"],
                "id_proveedor" => $this->producto["id_proveedor"],
                "codigo_barras" => $codigoBarras,
            ]);

            if ($producto) {
                $this->dispatch('productoRegistrado');

                session()->flash('success', '✅ Producto registrado con éxito. Código de: ' . $codigoBarras);
                $this->resetForm();
            }
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
            "precio_unitario" => "",
            "stock" => 0,
            "id_categoria_producto" => "",
            "id_proveedor" => "",
        ];

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
