<?php

namespace App\Livewire\Configuracion;

use App\Models\modulo;
use App\Models\modulo_opcion;
use App\Models\Permiso;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ModuloOpcion extends Component
{
    public $modulos;
    public $opcionesPadre;
    public $permisos;

    public $id_modulo;
    public $nombre_opcion;
    public $ruta_laravel;
    public $orden;
    public $id_permiso;
    public $id_opcion_padre;
    public $modalVisible = false;
    public $opcion_id; // Para editar

    public function mount()
    {
        $this->modulos = modulo::where('estado', 'activo')->get();
        $this->permisos = Permiso::where('estado', 'activo')->get();
        $this->opcionesPadre = collect();
    }

    public function updatedIdModulo($value)
    {
        $this->opcionesPadre = modulo_opcion::where('id_modulo', $value)
            ->whereNull('id_opcion_padre')
            ->where('estado', 'activo')
            ->get();
    }

    public function abrirModal($opcionId = null)
    {
        $this->resetValidation();
        $this->reset(['nombre_opcion', 'ruta_laravel', 'orden', 'id_permiso', 'id_opcion_padre']);
        $this->opcion_id = null;

        if ($opcionId) {
            $this->opcion_id = $opcionId;
            $opcion = modulo_opcion::findOrFail($opcionId);
            $this->id_modulo = $opcion->id_modulo;
            $this->nombre_opcion = $opcion->nombre_opcion;
            $this->ruta_laravel = $opcion->ruta_laravel;
            $this->orden = $opcion->orden;
            $this->id_permiso = $opcion->id_permiso;
            $this->id_opcion_padre = $opcion->id_opcion_padre;
            $this->updatedIdModulo($this->id_modulo);
        }

        $this->modalVisible = true;
    }

    public function guardar()
    {
        $mensajes = [
            'nombre_opcion.unique' => 'El nombre de la opción ya existe.',
            'nombre_opcion.required' => 'El nombre de la opción es obligatorio.',
            'ruta_laravel.required' => 'La ruta de la opción es obligatoria.',
            'ruta_laravel.max' => 'La ruta de la opción no puede tener más de 50 caracteres.',
            "orden.required" => 'El orden de la opción es obligatorio.',
            "orden.integer" => 'El orden de la opción debe ser un número entero.',
            'id_permiso.required' => 'El permiso es obligatorio.',
            'id_permiso.exists' => 'El permiso seleccionado no es válido.',
        ];

        $this->validate([
            'id_modulo' => 'required|exists:modulos,id_modulo',
            'nombre_opcion' => 'required|string|max:100|unique:modulo_opciones,nombre_opcion,' . $this->id_modulo . ',id_opcion_padre',
            'ruta_laravel' => 'required|string|max:50',
            'orden' => 'required|integer',
            'id_permiso' => 'required|exists:permisos,id_permiso',
        ], $mensajes);


        if ($this->opcion_id) {
            $opcion = modulo_opcion::findOrFail($this->opcion_id);
            $opcion->update([
                'id_modulo' => $this->id_modulo,
                'nombre_opcion' => $this->nombre_opcion,
                'ruta_laravel' => $this->ruta_laravel,
                'orden' => $this->orden,
                'id_permiso' => $this->id_permiso,
                'id_opcion_padre' => $this->id_opcion_padre,
            ]);
        } else {
            modulo_opcion::create([
                'id_modulo' => $this->id_modulo,
                'nombre_opcion' => $this->nombre_opcion,
                'ruta_laravel' => $this->ruta_laravel,
                'orden' => $this->orden,
                'id_permiso' => $this->id_permiso,
                'id_opcion_padre' => $this->id_opcion_padre,
                'estado' => 'activo',
                'id_usuario_registro' => Auth::id(),
                'fecha_registro' => now(),
            ]);
            session()->flash('success', 'Opción creada correctamente.');
        }

        $this->dispatch('notify', title: 'Success', description: 'Opción actualizada correctamente.', type: 'success');
        $this->modalVisible = false;
        $this->reset(['nombre_opcion', 'ruta_laravel', 'orden', 'id_permiso', 'id_opcion_padre', 'opcion_id']);
    }

    public function anular($opcionId)
    {
        $opcion = modulo_opcion::findOrFail($opcionId);
        $opcion->estado = 'inactivo';
        $opcion->save();

        $this->dispatch('notify', title: 'Success', description: 'Opción anulada correctamente.', type: 'success');
    }

    public function resetForm()
    {
        $this->reset([
            'nombre_opcion' => '',
            'ruta_laravel' => '',
            'orden' => '',
            'id_permiso' => '',
            'id_opcion_padre' => '',
            'estado' => 'activo',
            'id_usuario_registro' => Auth::id(),
            'fecha_registro' => now(),
            'fecha_actualizacion' => now(),
        ]);
    }


    public function render()
    {
        $opciones = modulo_opcion::with('subopciones', 'modulo', 'permiso')
            ->whereNull('id_opcion_padre')
            ->where('estado', 'activo')
            ->get();

        return view('livewire.configuracion.modulo-opcion', [
            'opciones' => $opciones
        ]);
    }
}
