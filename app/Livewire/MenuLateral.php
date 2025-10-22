<?php

namespace App\Livewire;

use App\Models\modulo;
use Livewire\Component;

class MenuLateral extends Component
{
    public $modulos;

    public function mount()
    {
        $user = auth()->user();
        if (!$user || !$user->rol) {
            $this->modulos = collect();
            return;
        }

        $rol = $user->rol;

        if ("Administrador" != $rol->nombre_rol) {
            // Obtener solo módulos del rol y opciones activas
            $modulos = $rol->modulos()
                ->where('estado', 'activo')
                ->whereHas('opciones', function ($query) use ($user) {
                    $query->where('estado', 'activo')
                        ->whereHas('permiso', function ($q) use ($user) {
                            $q->whereIn('nombre_permiso', $user->permisos->pluck('nombre_permiso'));
                        });
                })
                ->with(['opciones' => function ($query) use ($user) {
                    $query->where('estado', 'activo')
                        ->whereHas('permiso', function ($q) use ($user) {
                            $q->whereIn('nombre_permiso', $user->permisos->pluck('nombre_permiso'));
                        });
                }])
                ->get();


            // Filtrar opciones según permisos del usuario
            $this->modulos = $modulos->map(function ($modulo) use ($user) {
                $modulo->opciones = $modulo->opciones->filter(function ($opcion) use ($user) {
                    return $opcion->permiso && $user->tienePermiso($opcion->permiso->nombre_permiso);
                })->values(); // resetear claves de la colección
                return $modulo;
            });

            // Opcional: quitar módulos que quedaron sin opciones y no deberían mostrarse
            $this->modulos = $this->modulos->filter(function ($modulo) {
                return $modulo->opciones->count() > 0;
            })->values();
        } else {
            // Si es admin, obtener todos los módulos y opciones activas
            $this->modulos = modulo::where('estado', 'activo')
                ->with(['opciones' => function ($query) {
                    $query->where('estado', 'activo');
                }])
                ->get();
        }
    }


    public function render()
    {
        return view('livewire.menu-lateral');
    }
}
