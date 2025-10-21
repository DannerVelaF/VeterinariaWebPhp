<?php

namespace App\Livewire\Configuracion;

use App\Models\Modulo;
use App\Models\Modulo_roles;
use App\Models\Roles;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Modulos extends Component
{
    public $modulos, $roles;

    // Para crear/editar
    public $modalVisible = false;
    public $modulo_id = null;
    public $nombre_modulo;
    public $rolesSeleccionados = [];

    // Para asignar nuevos roles
    public $modalRolesVisible = false;
    public $moduloSeleccionado;
    public $rolesNuevos = [];

    public function mount()
    {
        $this->modulos = Modulo::with('roles')->get();
        $this->roles = Roles::where('estado', 'activo')->get();
    }

    // Abrir modal crear/editar
    public function abrirModal($moduloId = null)
    {
        $this->resetValidation();
        $this->reset(['nombre_modulo', 'rolesSeleccionados', 'modulo_id']);

        if ($moduloId) {
            $modulo = Modulo::with('roles')->find($moduloId);
            $this->modulo_id = $modulo->id_modulo;
            $this->nombre_modulo = $modulo->nombre_modulo;
            $this->rolesSeleccionados = $modulo->roles->pluck('id_rol')->toArray();
        }

        $this->modalVisible = true;
    }

    public function guardar()
    {

        $mensajes = [
            'nombre_modulo.unique' => 'El nombre del módulo ya existe.',
            'nombre_modulo.required' => 'El nombre del módulo es obligatorio.',
            'nombre_modulo.max' => 'El nombre del módulo no puede tener más de 100 caracteres.',
            'rolesSeleccionados.required' => 'Debe seleccionar al menos un rol.',
        ];

        $this->validate([
            'nombre_modulo' => 'required|string|max:100|unique:modulos,nombre_modulo,' . $this->modulo_id . ',id_modulo',
            'rolesSeleccionados' => 'required|array|min:1',
        ], $mensajes);

        $modulo = $this->modulo_id ? Modulo::find($this->modulo_id) : new Modulo();

        $modulo->nombre_modulo = $this->nombre_modulo;
        $modulo->estado = 'activo';
        $modulo->id_usuario_registro = Auth::id();
        $modulo->fecha_registro = now();
        $modulo->save();

        // Sincronizar roles
        $modulo->roles()->sync($this->rolesSeleccionados);

        $this->modalVisible = false;
        $this->mount();
        $this->dispatch(
            'notify',
            title: 'Success',
            description: 'Módulo guardado correctamente.',
            type: 'success'
        );
    }

    // Modal para asignar roles adicionales
    public function abrirModalRoles($moduloId)
    {
        $this->resetValidation();
        $this->moduloSeleccionado = Modulo::with('roles')->find($moduloId);
        $this->rolesNuevos = [];
        $this->modalRolesVisible = true;
    }

    public function asignarRoles()
    {
        $this->validate([
            'rolesNuevos' => 'required|array|min:1',
        ]);

        foreach ($this->rolesNuevos as $rolId) {
            Modulo_roles::firstOrCreate([
                'id_modulo' => $this->moduloSeleccionado->id_modulo,
                'id_rol' => $rolId,
            ], [
                'fecha_registro' => now(),
            ]);
        }

        $this->modalRolesVisible = false;
        $this->mount();
        $this->dispatch('notify', title: 'Success', description: 'Roles asignados correctamente.', type: 'success');
    }

    public function render()
    {
        return view('livewire.configuracion.modulos');
    }
}
