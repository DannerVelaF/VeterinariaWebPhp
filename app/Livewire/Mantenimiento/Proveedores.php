<?php

namespace App\Livewire\Mantenimiento;

use App\Models\Direccion;
use App\Models\Proveedor;
use App\Models\Ubigeo;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class Proveedores extends Component
{
    public $proveedor = [
        'nombre' => '',
        'ruc' => '',
        'telefono' => '',
        'correo' => '',
        'pais' => '',
    ];

    public $direccion = [
        'zona' => '',
        'tipo_calle' => '',
        'nombre_calle' => '',
        'numero' => '',
        'codigo_postal' => '',
        'referencia' => '',
        'codigo_ubigeo' => ''
    ];

    public $departamentos = [];
    public $provincias = [];
    public $distritos = [];

    public $departamentoSeleccionado = '';
    public $provinciaSeleccionada = '';

    public function mount()
    {
        $this->departamentos = Ubigeo::select("departamento")->distinct()->pluck('departamento')->toArray();
    }

    public function updatedDepartamentoSeleccionado($value)
    {
        $this->provincias = [];
        $this->distritos = [];
        $this->provinciaSeleccionada = '';
        $this->direccion['codigo_ubigeo'] = '';

        if (!empty($value)) {
            $this->provincias = Ubigeo::where('departamento', $value)
                ->select('provincia')
                ->distinct()
                ->pluck('provincia')
                ->toArray();
        }
    }

    public function updatedProvinciaSeleccionada($value)
    {
        $this->distritos = [];
        $this->direccion['codigo_ubigeo'] = '';

        if (!empty($value) && !empty($this->departamentoSeleccionado)) {
            $this->distritos = Ubigeo::where('departamento', $this->departamentoSeleccionado)
                ->where('provincia', $value)
                ->get();
        }
    }

    public function guardar()
    {
        $this->validate([
            'proveedor.nombre' => 'required|string|max:255',
            'proveedor.ruc' => 'required|digits:11|unique:proveedores,ruc',
            'proveedor.telefono' => 'nullable|string|max:15',
            'proveedor.correo' => 'nullable|email|max:255',
            'direccion.codigo_ubigeo' => 'required|exists:ubigeos,codigo_ubigeo',
        ]);

        try {
            Log::info('Registrando proveedor');
            $direccion = Direccion::create($this->direccion);

            $proveedor = Proveedor::create(array_merge($this->proveedor, [
                'id_direccion' => $direccion->id
            ]));

            if ($proveedor) {
                session()->flash('success', 'Proveedor registrado con éxito');
                $this->reset();
                $this->mount();
                Log::info('Proveedor registrado con éxito');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Error al registrar el proveedor: ' . $e->getMessage());
            Log::info('Error al registrar el proveedor: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.mantenimiento.proveedores');
    }
}
