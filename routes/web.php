<?php

use Livewire\Volt\Volt;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\TwoFactorAuthentication;
use App\Livewire\Mantenimiento\Productos\Registro as MantenimientoProductos;
use App\Livewire\Mantenimiento\Trabajadores\Registro as MantenimientoTrabajadores;
use App\Livewire\Mantenimiento\Usuarios\Registro as MantenimientoUsuarios;
Use App\Livewire\Mantenimiento\Servicios\Registro as MantenimientoServicios;

Use App\Livewire\Mantenimiento\Mascotas\Registro as MantenimientoMascotas;

use App\Livewire\Inventario\Registro as RegistrarInventario;
use App\Livewire\Compras\Registro as RegistroCompras;
use App\Livewire\Mantenimiento\Clientes\Registro as RegistroClientes;
use App\Livewire\Ventas\RegistroVenta;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

Route::middleware('guest')->group(function () {
    Route::get('/login', Login::class)->name('login');
});
Route::get('/login/two-factor', TwoFactorAuthentication::class)->name('two.factor');

Route::get('/', function () {
    return redirect()->route(auth()->check() ? 'ventas' : 'login');
});

// Rutas protegidas por autenticación
Route::middleware('auth')->group(function () {

    // Logout correcto
    Route::get('/logout', function () {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect()->route('login');
    })->name('logout');

    // Ventas
    Route::get('/ventas', RegistroVenta::class)->name('ventas');

    // Inventario
    Route::get('/inventario', RegistrarInventario::class)->name('inventario.registro');

    // Compras
    Route::get('/compras', RegistroCompras::class)->name('compras');

    Route::get('/clientes', RegistroCompras::class)->name('clientes');


    // Mantenimiento
    Route::prefix('mantenimiento')->group(function () {
        Route::get('/productos', MantenimientoProductos::class)->name('productos');
        Route::get('/trabajadores', MantenimientoTrabajadores::class)->name('trabajadores');
        Route::get('/usuarios', MantenimientoUsuarios::class)->name('usuarios');
        Route::get('/servicios', MantenimientoServicios::class)->name('servicios');
        Route::get('/mascotas', MantenimientoMascotas::class)->name('mascotas');

    });
});
