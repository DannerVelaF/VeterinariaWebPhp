<?php

namespace App\Livewire\Mantenimiento\Productos;

use App\Models\Direccion;
use App\Models\Proveedor;
use App\Models\Ubigeo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class Proveedores extends Component
{
    public $proveedor = [
        'nombre_proveedor' => '',
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
        'proveedor.nombre_proveedor.required' => 'El nombre del proveedor es obligatorio.',
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
    public $modalEditar = false;
    public $proveedorEncontrado = true;
    public $proveedorSeleccionado;
    public $proveedorEditar = [
        'id_proveedor' => null,
        'nombre_proveedor' => '',
        'ruc' => '',
        'telefono_contacto' => '',
        'correo_electronico_empresa' => '',
        'telefono_secundario' => '',
        'correo_electronico_encargado' => '',
        'pais' => '',
        'id_direccion' => null,
        'direccion' => [
            'tipo_calle' => '',
            'nombre_calle' => '',
            'numero' => '',
            'referencia' => '',
            'codigo_postal' => '',
            'zona' => '',
            'codigo_ubigeo' => '',
        ],
    ]; // array para editar
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
        $validatedData = $this->validate([
            'proveedor.nombre_proveedor' => 'required|string|max:255',
            'proveedor.ruc' => 'required|digits:11|unique:proveedores,ruc',
            'proveedor.correo_electronico_empresa' => 'nullable|unique:proveedores,correo_electronico_empresa|email|max:255',
            'proveedor.telefono_contacto' => 'nullable|string|max:15',
            'proveedor.telefono_secundario' => 'nullable|string|max:15',
            'proveedor.correo_electronico_encargado' => 'nullable|email|max:255',
            'proveedor.pais' => 'required|string|in:peru,colombia',
            'direccion.codigo_ubigeo' => 'required|exists:ubigeos,codigo_ubigeo',
            'direccion.tipo_calle' => 'nullable|string|max:50',
            'direccion.nombre_calle' => 'nullable|string|max:255',
            'direccion.numero' => 'nullable|string|max:10',
            'direccion.zona' => 'nullable|string|max:255',
            'direccion.codigo_postal' => 'nullable|string|max:10',
            'direccion.referencia' => 'nullable|string|max:255',
        ]);

        try {
            DB::transaction(function () use ($validatedData, &$proveedor) {
                $direccion = Direccion::create($validatedData['direccion']);

                $proveedor = Proveedor::create(array_merge($validatedData['proveedor'], [
                    'id_direccion' => $direccion->id_direccion
                ]));
            });

            // Si llegamos aquí, todo se guardó correctamente
            $this->dispatch('notify', title: 'Success', description: 'Proveedor creado correctamente.', type: 'success');
            $this->proveedorEncontrado = true;
            $this->resetForm();
            $this->dispatch('proveedoresUpdated');
        } catch (\Exception $e) {
            $this->dispatch('notify', title: 'Error', description: 'Error al registrar el proveedor.', type: 'error');
            Log::error('Error al registrar proveedor', ['error' => $e->getMessage()]);
        }
    }

    public function buscarRuc()
    {
        $ruc = $this->proveedor['ruc'];

        if (strlen($ruc) !== 11) {
            $this->dispatch('notify', title: 'Error', description: 'El RUC debe tener exactamente 11 dígitos.', type: 'error');
            return;
        }

        try {

            $response = Http::withHeaders([
                "content-type" => "application/json",
                'Authorization' => "Bearer " . env("DECOLECTA_API_KEY"),
            ])->withOptions(['verify' => false])
                ->get("https://api.decolecta.com/v1/sunat/ruc", [

                    'numero' => $ruc
                ]);



            if ($response->successful()) {
                $data = $response->json();
                if (!empty($data['razon_social'])) {
                    $this->proveedor['nombre_proveedor'] = $data['razon_social'];
                    $this->dispatch('notify', title: 'Success', description: 'Razón social cargada desde SUNAT.', type: 'success');
                } else {
                    $this->dispatch('notify', title: 'Error', description: 'No se encontró la razón social para este RUC.', type: 'error');
                    $this->proveedorEncontrado = false;
                }
            } else {
                $this->dispatch('notify', title: 'Error', description: 'Error al consultar el RUC. Intente nuevamente.', type: 'error');
                $this->proveedorEncontrado = false;
            }
        } catch (\Exception $e) {
            $this->dispatch('notify', title: 'Error', description: 'Error al conectar con la API: ' . $e->getMessage(), type: 'error');
            $this->proveedorEncontrado = false;
        }
    }
    #[\Livewire\Attributes\On('editarProveedor')]
    public function abrirModalEditar($proveedorId)
    {
        $this->proveedorSeleccionado = Proveedor::findOrFail($proveedorId);

        $this->proveedorEditar = [
            'id_proveedor' => $proveedorId,
            'nombre_proveedor' => $this->proveedorSeleccionado->nombre_proveedor,
            'ruc' => $this->proveedorSeleccionado->ruc,
            'telefono_contacto' => $this->proveedorSeleccionado->telefono_contacto,
            'correo_electronico_empresa' => $this->proveedorSeleccionado->correo_electronico_empresa,
            'telefono_secundario' => $this->proveedorSeleccionado->telefono_secundario,
            'correo_electronico_encargado' => $this->proveedorSeleccionado->correo_electronico_encargado,
            'pais' => $this->proveedorSeleccionado->pais,
            'id_direccion' => $this->proveedorSeleccionado->id_direccion,
            'direccion' => [
                'tipo_calle' => $this->proveedorSeleccionado->direccion->tipo_calle,
                'nombre_calle' => $this->proveedorSeleccionado->direccion->nombre_calle,
                'numero' => $this->proveedorSeleccionado->direccion->numero,
                'referencia' => $this->proveedorSeleccionado->direccion->referencia,
                'codigo_postal' => $this->proveedorSeleccionado->direccion->codigo_postal,
                'zona' => $this->proveedorSeleccionado->direccion->zona,
                'codigo_ubigeo' => $this->proveedorSeleccionado->direccion->codigo_ubigeo,
            ],
        ];

        $this->modalEditar = true;
    }

    public function actualizarProveedor()
    {
        try {
            DB::transaction(function () {
                $proveedor = Proveedor::findOrFail($this->proveedorEditar['id_proveedor']);
                $proveedor->update([
                    'nombre_proveedor' => $this->proveedorEditar['nombre_proveedor'],
                    'telefono_contacto' => $this->proveedorEditar['telefono_contacto'],
                    'telefono_secundario' => $this->proveedorEditar['telefono_secundario'],
                    'correo_electronico_empresa' => $this->proveedorEditar['correo_electronico_empresa'],
                    'correo_electronico_encargado' => $this->proveedorEditar['correo_electronico_encargado'],
                    'pais' => $this->proveedorEditar['pais'],
                    'fecha_actualizacion' => now(),
                ]);

                // Actualizar dirección
                $direccion = Direccion::findOrFail($proveedor->id_direccion);
                $direccion->update([
                    'tipo_calle' => $this->proveedorEditar['direccion']['tipo_calle'],
                    'nombre_calle' => $this->proveedorEditar['direccion']['nombre_calle'],
                    'numero' => $this->proveedorEditar['direccion']['numero'],
                    'zona' => $this->proveedorEditar['direccion']['zona'],
                    'codigo_postal' => $this->proveedorEditar['direccion']['codigo_postal'],
                    'referencia' => $this->proveedorEditar['direccion']['referencia'],
                    'codigo_ubigeo' => $this->proveedorEditar['direccion']['codigo_ubigeo'],
                    'fecha_actualizacion' => now(),
                ]);
            });

            $this->dispatch('notify', title: 'Success', description: 'Proveedor actualizado correctamente.', type: 'success');
            $this->modalEditar = false;
            $this->emit('proveedoresUpdated'); // Para refrescar la tabla
        } catch (\Exception $e) {
            $this->dispatch('notify', title: 'Error', description: 'Error al actualizar el proveedor: ' . $e->getMessage(), type: 'error');
            Log::error('Error al actualizar proveedor', ['error' => $e->getMessage()]);
        }
    }

    // Método para resetear el formulario
    public function resetForm()
    {
        $this->proveedor = [
            'nombre_proveedor' => '',
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
