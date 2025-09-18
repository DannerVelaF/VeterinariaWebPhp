<?php

use Livewire\Volt\Volt;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\TwoFactorAuthentication;
use App\Livewire\Mantenimiento\Productos\Registro as MantenimientoProductos;
use App\Livewire\Mantenimiento\Trabajadores\Registro as MantenimientoTrabajadores;
use App\Livewire\Mantenimiento\Usuarios\Registro as MantenimientoUsuarios;
use App\Livewire\Inventario\Registro as RegistrarInventario;
use App\Livewire\Compras\Registro as RegistroCompras;
use App\Livewire\Ventas\RegistroVenta;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

Route::get("/login", Login::class)->name("login");
Route::get("/login/two-factor", TwoFactorAuthentication::class)->name("two.factor");
Route::get("/logout", function () {
    Session::forget('user');
    return redirect()->route('login');
})->name("logout");

Route::get("/", function () {
    if (Session::has("user")) {
        return redirect()->route('ventas');
    }
    return redirect()->route("login");
});

Route::middleware('auth')->group(function () {

    Route::get("/ventas", RegistroVenta::class)
        ->name("ventas");
    Route::get("/mantenimiento", RegistrarInventario::class)
        ->name("inventario.registro");

    Route::get("/compras", RegistroCompras::class)
        ->name("compras");


    Route::group(['prefix' => 'mantenimiento'], function () {
        Route::get("/productos", MantenimientoProductos::class)
            ->name("productos");
        Route::get("/trabajadores", MantenimientoTrabajadores::class)
            ->name("trabajadores");
        Route::get("/usuarios", MantenimientoUsuarios::class)
            ->name("usuarios");
    });



    Route::get("/logout", function () {
        Session::forget('user');
        return redirect()->route('login');
    })->name("logout");
});
