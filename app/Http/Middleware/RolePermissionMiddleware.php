<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RolePermissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $role = null, $permission = null)
    {
        $user = Auth::user();

        if (!$user) {
            abort(403, 'No autorizado');
        }

        // Verificar rol
        if ($role && !$user->hasRole($role)) {
            abort(403, 'No tienes el rol requerido');
        }

        // Verificar permiso
        if ($permission && !$user->can($permission)) {
            abort(403, 'No tienes el permiso requerido');
        }

        return $next($request);
    }
}
