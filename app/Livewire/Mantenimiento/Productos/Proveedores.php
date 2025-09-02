<?php

namespace App\Livewire\Mantenimiento\Productos;

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
        'correo_electronico_empresa' => '',
        'telefono_contacto' => '',
        'telefono_secundario' => '',
        'correo_electronico_encargado' => '',
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
    protected $messages = [
        'proveedor.nombre.required' => 'El nombre del proveedor es obligatorio.',
        'proveedor.nombre.max' => 'El nombre no puede tener más de 255 caracteres.',
        'proveedor.ruc.required' => 'El RUC es obligatorio.',
        'proveedor.ruc.digits' => 'El RUC debe tener exactamente 11 dígitos.',
        'proveedor.ruc.unique' => 'Este RUC ya está registrado.',
        'proveedor.telefono.max' => 'El teléfono no puede tener más de 15 caracteres.',
        'proveedor.correo_electronico_empresa.email' => 'Ingrese un correo electrónico válido.',
        'proveedor.correo_electronico_empresa.max' => 'El correo no puede tener más de 255 caracteres.',
        'proveedor.correo_electronico_encargado.email' => 'Ingrese un correo electrónico válido.',
        'proveedor.correo_electronico_encargado.max' => 'El correo no puede tener más de 255 caracteres.',
        'proveedor.telefono_contacto.max' => 'El teléfono de contacto no puede tener más de 15 caracteres.',
        'proveedor.telefono_secundario.max' => 'El teléfono secundario no puede tener más de 15 caracteres.',
        'proveedor.pais.required' => 'Debe seleccionar un país.',
        'proveedor.pais.in' => 'Debe seleccionar un país válido.',
        'direccion.codigo_ubigeo.required' => 'Debe seleccionar un distrito.',
        'direccion.codigo_ubigeo.exists' => 'El distrito seleccionado no es válido.',
        'direccion.tipo_calle.max' => 'El tipo de calle no puede tener más de 50 caracteres.',
        'direccion.nombre_calle.max' => 'El nombre de calle no puede tener más de 255 caracteres.',
        'direccion.numero.max' => 'El número no puede tener más de 10 caracteres.',
        'direccion.zona.max' => 'La zona no puede tener más de 255 caracteres.',
        'direccion.codigo_postal.max' => 'El código postal no puede tener más de 10 caracteres.',
        'direccion.referencia.max' => 'La referencia no puede tener más de 255 caracteres.',
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
        // Validaciones completas
        $validatedData = $this->validate([
            // Validaciones del proveedor
            'proveedor.nombre' => 'required|string|max:255',
            'proveedor.ruc' => 'required|digits:11|unique:proveedores,ruc',
            'proveedor.correo_electronico_empresa' => 'nullable|unique:proveedores,correo_electronico_empresa|email|max:255',
            'proveedor.telefono_contacto' => 'nullable|string|max:15',
            'proveedor.telefono_secundario' => 'nullable|string|max:15',
            'proveedor.correo_electronico_encargado' => 'nullable|email|max:255',
            'proveedor.pais' => 'required|string|in:peru,colombia',

            // Validaciones de la dirección
            'direccion.codigo_ubigeo' => 'required|exists:ubigeos,codigo_ubigeo',
            'direccion.tipo_calle' => 'nullable|string|max:50',
            'direccion.nombre_calle' => 'nullable|string|max:255',
            'direccion.numero' => 'nullable|string|max:10',
            'direccion.zona' => 'nullable|string|max:255',
            'direccion.codigo_postal' => 'nullable|string|max:10',
            'direccion.referencia' => 'nullable|string|max:255',
        ]);

        try {
            Log::info('Registrando proveedor', ['data' => $validatedData]);

            // Crear dirección
            $direccion = Direccion::create($this->direccion);

            // Crear proveedor
            $proveedor = Proveedor::create(array_merge($this->proveedor, [
                'id_direccion' => $direccion->id
            ]));

            if ($proveedor) {
                $this->dispatch('proveedorRegistrado');

                session()->flash('success', 'Proveedor registrado con éxito');
                $this->resetForm();
                Log::info('Proveedor registrado con éxito', ['proveedor_id' => $proveedor->id]);
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Error al registrar el proveedor: ' . $e->getMessage());
            Log::error('Error al registrar el proveedor', [
                'error' => $e->getMessage(),
                'data' => $this->proveedor
            ]);
        }
    }

    // Método para resetear el formulario
    public function resetForm()
    {
        $this->proveedor = [
            'nombre' => '',
            'ruc' => '',
            'correo_electronico_empresa' => '',
            'telefono_contacto' => '',
            'telefono_secundario' => '',
            'correo_electronico_encargado' => '',
            'pais' => '',
        ];

        $this->direccion = [
            'zona' => '',
            'tipo_calle' => '',
            'nombre_calle' => '',
            'numero' => '',
            'codigo_postal' => '',
            'referencia' => '',
            'codigo_ubigeo' => ''
        ];

        $this->departamentoSeleccionado = '';
        $this->provinciaSeleccionada = '';
        $this->provincias = [];
        $this->distritos = [];

        // Recargar departamentos
        $this->mount();
    }



    public function render()
    {
        return view('livewire.mantenimiento.productos.proveedores');
    }
}
