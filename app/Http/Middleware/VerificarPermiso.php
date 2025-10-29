<?php

namespace App\Http\Middleware;

use App\Models\Modulo_opcion;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerificarPermiso
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()
                ->route('login')
                ->with('error', 'Por favor inicia sesión para acceder a esta sección.');
        }

        // 🔴 VERIFICAR SI EL USUARIO ESTÁ ACTIVO
        if ($user->estado !== 'activo') {
            Auth::logout(); // Cerrar sesión del usuario inactivo

            return redirect()
                ->route('login')
                ->with('error', 'Tu cuenta está inactiva. Por favor contacta con el administrador.');
        }



        $rutaActual = $request->route()->getName();

        // Buscar la opción asociada a la ruta
        $opcion = Modulo_opcion::where('ruta_laravel', $rutaActual)
            ->where('estado', 'activo')
            ->first();

        // 🚫 Si no existe la opción o no tiene permiso asignado, bloquear
        if (!$opcion || !$opcion->permiso) {
            return redirect()
                ->route('inicio')
                ->with('error', 'Acceso denegado: esta ruta no tiene permiso asignado.');
        }

        $permisoRequerido = $opcion->permiso->nombre_permiso;

        // 🚫 Si el usuario no tiene el permiso requerido, bloquear
        if (!$user->tienePermiso($permisoRequerido)) {
            return redirect()
                ->route('inicio')
                ->with('error', 'No tienes permiso para acceder a esta sección.');
        }

        // ✅ Permitir acceso
        return $next($request);
    }
}
