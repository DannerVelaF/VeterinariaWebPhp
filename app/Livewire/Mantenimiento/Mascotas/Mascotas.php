<?php

namespace App\Livewire\Mantenimiento\Mascotas;

use App\Models\Mascota;
use App\Models\Clientes;
use App\Models\Raza;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;

class Mascotas extends Component
{
    protected $listeners = [
        'clientesUpdated' => 'refreshData',
        'razasUpdated' => 'refreshData',
    ];

    public $mascota = [
        "id_cliente" => "",
        "id_raza" => "",
        "nombre_mascota" => "",
        "fecha_nacimiento" => "",
        "sexo" => "",
        "color_primario" => "",
        "peso_actual" => "",
        "observacion" => "",
    ];

    public $clientes = [];
    public $razas = [];
    public $modalEditar = false;

    public $mascotaEditar = [
        "id_mascota" => null,
        "id_cliente" => "",
        "id_raza" => "",
        "nombre_mascota" => "",
        "fecha_nacimiento" => "",
        "sexo" => "",
        "color_primario" => "",
        "peso_actual" => "",
        "observacion" => "",
    ];

    public function mount()
    {
        $this->clientes = Clientes::all();
        $this->razas = Raza::all();
    }

    #[\Livewire\Attributes\On('clientesUpdated')]
    #[\Livewire\Attributes\On('razasUpdated')]
    public function refreshData()
    {
        $this->clientes = Clientes::all();
        $this->razas = Raza::all();
    }

    public function guardar()
    {
        $validatedData = Validator::make($this->mascota, [
            'id_cliente' => 'required|exists:clientes,id_cliente',
            'id_raza' => 'required|exists:razas,id_raza',
            'nombre_mascota' => 'required|string|max:100',
            'fecha_nacimiento' => 'nullable|date',
            'sexo' => 'required|in:M,H',
            'color_primario' => 'nullable|string|max:50',
            'peso_actual' => 'nullable|numeric|min:0',
            'observacion' => 'nullable|string|max:255',
        ])->validate();

        try {
            DB::transaction(function () use ($validatedData) {
                Mascota::create([
                    'id_cliente' => $validatedData['id_cliente'],
                    'id_raza' => $validatedData['id_raza'],
                    'nombre_mascota' => $validatedData['nombre_mascota'],
                    'fecha_nacimiento' => $validatedData['fecha_nacimiento'],
                    'sexo' => $validatedData['sexo'],
                    'color_primario' => $validatedData['color_primario'],
                    'peso_actual' => $validatedData['peso_actual'],
                    'observacion' => $validatedData['observacion'],
                    'fecha_creacion' => now(),
                    'fecha_actualizacion' => now(),
                ]);
            });

            $this->dispatch('mascotaRegistrada');
            session()->flash('success', '✅ Mascota registrada con éxito.');
            $this->resetForm();
        } catch (\Exception $e) {
            Log::error('Error al registrar la mascota', ['error' => $e->getMessage()]);
            session()->flash('error', 'Error al registrar la mascota: ' . $e->getMessage());
        }
    }

    public function resetForm()
    {
        $this->mascota = [
            "id_cliente" => "",
            "id_raza" => "",
            "nombre_mascota" => "",
            "fecha_nacimiento" => "",
            "sexo" => "",
            "color_primario" => "",
            "peso_actual" => "",
            "observacion" => "",
        ];
        $this->mount();
    }

    #[\Livewire\Attributes\On('editarMascota')]
    public function abrirModalEditar($mascotaId)
    {
        $mascota = Mascota::findOrFail($mascotaId);

        $this->mascotaEditar = [
            'id_mascota' => $mascota->id_mascota,
            'id_cliente' => $mascota->id_cliente,
            'id_raza' => $mascota->id_raza,
            'nombre_mascota' => $mascota->nombre_mascota,
            'fecha_nacimiento' => $mascota->fecha_nacimiento,
            'sexo' => $mascota->sexo,
            'color_primario' => $mascota->color_primario,
            'peso_actual' => $mascota->peso_actual,
            'observacion' => $mascota->observacion,
        ];

        $this->modalEditar = true;
    }

    public function actualizarMascota()
    {
        $validatedData = Validator::make($this->mascotaEditar, [
            'id_cliente' => 'required|exists:clientes,id_cliente',
            'id_raza' => 'required|exists:razas,id_raza',
            'nombre_mascota' => 'required|string|max:100',
            'fecha_nacimiento' => 'nullable|date',
            'sexo' => 'required|in:M,H',
            'color_primario' => 'nullable|string|max:50',
            'peso_actual' => 'nullable|numeric|min:0',
            'observacion' => 'nullable|string|max:255',
        ])->validate();

        try {
            DB::transaction(function () use ($validatedData) {
                $mascota = Mascota::findOrFail($this->mascotaEditar['id_mascota']);
                $mascota->update([
                    'id_cliente' => $validatedData['id_cliente'],
                    'id_raza' => $validatedData['id_raza'],
                    'nombre_mascota' => $validatedData['nombre_mascota'],
                    'fecha_nacimiento' => $validatedData['fecha_nacimiento'],
                    'sexo' => $validatedData['sexo'],
                    'color_primario' => $validatedData['color_primario'],
                    'peso_actual' => $validatedData['peso_actual'],
                    'observacion' => $validatedData['observacion'],
                    'fecha_actualizacion' => now(),
                ]);
            });

            $this->dispatch('mascotaRegistrada');
            session()->flash('success', '✅ Mascota actualizada correctamente.');
            $this->modalEditar = false;
        } catch (\Exception $e) {
            Log::error('Error al actualizar la mascota', ['error' => $e->getMessage()]);
            session()->flash('error', 'Error al actualizar la mascota: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.mantenimiento.mascotas.mascotas');
    }
}
