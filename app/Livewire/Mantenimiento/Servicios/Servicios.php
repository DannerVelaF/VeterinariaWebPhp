<?php

namespace App\Livewire\Mantenimiento\Servicios;
use App\Models\Servicio;

use App\Models\CategoriaServicio;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Exception;

class Servicios extends Component
{    
    protected $listeners = [
        'categoriaUpdated' => 'refreshData',
    ];

    public $servicio = [
        "nombre_servicio" => "",
        "descripcion" => "",
        "duracion_estimada" => "",
        "precio_unitario" => "",
        "id_categoria_servicio" => "",
    ];

    public $categorias = [];

    public $modalEditar = false;
    
    public $servicioEditar = [
        'id_servicio' => null,
        'nombre_servicio' => '',
        'descripcion' => '',
        'duracion_estimada' => '',
        'precio_unitario' => '',
        'id_categoria_servicio' => '',
    ]; // array para editar

    public function mount()
    {
        $this->categorias = CategoriaServicio::where('estado', 'activo')->get();
    }

    #[\Livewire\Attributes\On('categoriaUpdated')]
    public function refreshData()
    {
        $this->categorias = CategoriaServicio::where('estado', 'activo')->get();
    }

    public function guardar ()
    {
        // Validación
        $validatedData = $this->validate([
            'servicio.nombre_servicio' => 'required|string|max:255',
            'servicio.descripcion' => 'nullable|string|max:1000',
            'servicio.duracion_estimada' => 'nullable|numeric|min:0',
            'servicio.precio_unitario' => 'required|numeric|min:0',
            'servicio.id_categoria_servicio' => 'required|exists:categoria_servicios,id_categoria_servicio',
        ]);

        try {
            DB::transaction(function () use ($validatedData) {
                Servicio::create([
                    'nombre_servicio' => $validatedData['servicio']['nombre_servicio'],
                    'descripcion' => $validatedData['servicio']['descripcion'] ?? null,
                    'duracion_estimada' => $validatedData['servicio']['duracion_estimada'] ?? null,
                    'precio_unitario' => $validatedData['servicio']['precio_unitario'],
                    'id_categoria_servicio' => $validatedData['servicio']['id_categoria_servicio'],
                ]);
            });

            // Si llegamos aquí, todo se guardó correctamente
            $this->dispatch('servicioRegistrado');
            session()->flash('success', 'Servicio registrado con éxito');
            $this->resetForm();
            $this->dispatch('servicioUpdated');
        } catch (\Exception $e) {
            session()->flash('error', 'Error al registrar el servicio: ' . $e->getMessage());
            Log::error('Error al registrar servicio', ['error' => $e->getMessage()]);
        }
    }

    public function resetForm()
    {
        $this->servicio = [
            'nombre_servicio' => '',
            'descripcion' => '',
            'duracion_estimada' => '',
            'precio_unitario' => '',
            'id_categoria_servicio' => '',
        ];

        $this->mount();
    }

    #[\Livewire\Attributes\On('editarServicio')]
    public function abrirModalEditar($servicioId)
    {
        $servicio = Servicio::findOrFail($servicioId);

        $this->servicioEditar = [
            'id_servicio' => $servicio->id_servicio,
            'nombre_servicio' => $servicio->nombre_servicio,
            'descripcion' => $servicio->descripcion,
            'duracion_estimada' => $servicio->duracion_estimada,
            'precio_unitario' => $servicio->precio_unitario,
            'id_categoria_servicio' => $servicio->id_categoria_servicio,
        ];

        $this->modalEditar = true;
    }

    public function actualizarServicio()
    {
        $validatedData = $this->validate([
            'servicioEditar.nombre_servicio' => 'required|string|max:255',
            'servicioEditar.descripcion' => 'nullable|string|max:1000',
            'servicioEditar.duracion_estimada' => 'nullable|numeric|min:0',
            'servicioEditar.precio_unitario' => 'required|numeric|min:0',
            'servicioEditar.id_categoria_servicio' => 'required|exists:categoria_servicios,id_categoria_servicio',
        ]);

        try {
            DB::transaction(function () use ($validatedData) {
                $servicio = Servicio::findOrFail($this->servicioEditar['id_servicio']);
                $servicio->update([
                    'nombre_servicio' => $validatedData['servicioEditar']['nombre_servicio'],
                    'descripcion' => $validatedData['servicioEditar']['descripcion'] ?? null,
                    'duracion_estimada' => $validatedData['servicioEditar']['duracion_estimada'] ?? null,
                    'precio_unitario' => $validatedData['servicioEditar']['precio_unitario'],
                    'id_categoria_servicio' => $validatedData['servicioEditar']['id_categoria_servicio'],
                ]);
            });

            // Si llegamos aquí, todo se guardó correctamente
            $this->dispatch('servicioActualizado');
            session()->flash('success', '✅ Servicio actualizado con éxito.');
            $this->modalEditar = false;
            $this->dispatch('servicioUpdated');
        } catch (Exception $e) {
            session()->flash('error', 'Error al actualizar el servicio: ' . $e->getMessage());
        }
    }

    public function render ()
    {
        return view('livewire.mantenimiento.servicios.servicios');
    }

}