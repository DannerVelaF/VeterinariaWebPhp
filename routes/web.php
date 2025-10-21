<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// 🔹 Auth
use App\Livewire\Auth\Login;
use App\Livewire\Auth\TwoFactorAuthentication;

// 🔹 Páginas principales
use App\Livewire\Inicio;
use App\Livewire\Ventas\RegistroVenta;

// 🔹 Módulos de gestión
use App\Livewire\Mantenimiento\Productos\Registro as MantenimientoProductos;
use App\Livewire\Mantenimiento\Trabajadores\Registro as MantenimientoTrabajadores;
use App\Livewire\Mantenimiento\Servicios\Registro as MantenimientoServicios;
use App\Livewire\Mantenimiento\Mascotas\Registro as MantenimientoMascotas;

// 🔹 Usuarios y permisos
use App\Livewire\Mantenimiento\Usuarios\Usuarios;
use App\Livewire\Mantenimiento\Usuarios\Roles;
use App\Livewire\Mantenimiento\Usuarios\Permisos;

// 🔹 Otros módulos
use App\Livewire\Inventario\Registro as RegistrarInventario;
use App\Livewire\Compras\Registro as RegistroCompras;

// 🔹 Configuración
use App\Livewire\Configuracion\Modulos;
use App\Livewire\Configuracion\ModuloOpcion;
use App\Livewire\Inventario\Entradas;
use App\Livewire\Mantenimiento\Configuracion\Configuracion;
use App\Livewire\Mantenimiento\Mascotas\Clientes;
use App\Livewire\Mantenimiento\Productos\Categoria;
use App\Livewire\Mantenimiento\Productos\Productos;
use App\Livewire\Mantenimiento\Productos\Proveedores;
use App\Livewire\Mantenimiento\Productos\Unidades;
use App\Livewire\Mantenimiento\Trabajadores\Puestos;
use App\Livewire\Mantenimiento\Trabajadores\Trabajadores;
use App\Livewire\Mantenimiento\Trabajadores\Turnos;
use App\Livewire\Mantenimiento\Trabajadores\Ubigeos;
use App\Models\Trabajador;

/*
|--------------------------------------------------------------------------
| Rutas Públicas (sin autenticación)
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/login', Login::class)->name('login');
});

Route::get('/login/two-factor', TwoFactorAuthentication::class)->name('two.factor');

/*
|--------------------------------------------------------------------------
| Redirección base
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return redirect()->route(auth()->check() ? 'inicio' : 'login');
});


/*
|--------------------------------------------------------------------------
| Rutas Autenticadas (solo login)
|--------------------------------------------------------------------------
| /inicio se puede acceder sin verificación de permisos adicionales
*/
Route::middleware('auth')->group(function () {

    // 🔹 Página principal (sin middleware de permisos)
    Route::get('/inicio', Inicio::class)->name('inicio');

    // 🔹 Logout
    Route::get('/logout', function () {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect()->route('login');
    })->name('logout');

    /*
    |--------------------------------------------------------------------------
    | Rutas con Autenticación + Verificación de Permisos
    |--------------------------------------------------------------------------
    */
    Route::middleware('permiso')->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Ventas
        |--------------------------------------------------------------------------
        */
        Route::get('/ventas', RegistroVenta::class)->name('ventas');

        /*
        |--------------------------------------------------------------------------
        | Inventario
        |--------------------------------------------------------------------------
        */
        Route::get('/inventario', RegistrarInventario::class)->name('inventario.registro');
        Route::prefix('inventario')->group(function () {
            Route::get('/entradas', Entradas::class)->name('inventario.entradas');
        });
        /*
        |--------------------------------------------------------------------------
        | Compras y Clientes
        |--------------------------------------------------------------------------
        */
        Route::get('/compras', RegistroCompras::class)->name('compras');
        Route::get('/clientes', Clientes::class)->name('clientes');

        /*
        |--------------------------------------------------------------------------
        | Configuración del sistema
        |--------------------------------------------------------------------------
        */
        Route::prefix('configuracion')->group(function () {
            Route::get('/modulos', Modulos::class)->name('configuracion.modulos');
            Route::get('/opciones', ModuloOpcion::class)->name('configuracion.opciones');
        });

        /*
        |--------------------------------------------------------------------------
        | Mantenimiento
        |--------------------------------------------------------------------------
        */
        Route::prefix('mantenimiento')->group(function () {
            Route::prefix("productos")->group(function () {
                Route::get('/', Productos::class)->name('mantenimiento.productos');
                Route::get("/categorias", Categoria::class)->name('mantenimiento.productos.categorias');
                Route::get("/proveedores", Proveedores::class)->name('mantenimiento.productos.proveedores');
                Route::get("/unidades", Unidades::class)->name('mantenimiento.productos.unidades');
            });

            Route::prefix('trabajadores')->group(function () {
                Route::get('/', Trabajadores::class)->name('mantenimiento.trabajadores');
                Route::get('/puestos', Puestos::class)->name('mantenimiento.trabajadores.puestos');
                Route::get('/turnos', Turnos::class)->name('mantenimiento.trabajadores.turnos');
                Route::get('/ubigeos', Ubigeos::class)->name('mantenimiento.trabajadores.ubigeos');
            });

            Route::get('/servicios', MantenimientoServicios::class)->name('servicios');
            Route::get('/mascotas', MantenimientoMascotas::class)->name('mascotas');

            // Usuarios, roles y permisos
            Route::get('/usuarios', Usuarios::class)->name('mantenimiento.usuarios');
            Route::get('/roles', Roles::class)->name('mantenimiento.roles');
            Route::get('/permisos', Permisos::class)->name('mantenimiento.permisos');
        });
    });
});
