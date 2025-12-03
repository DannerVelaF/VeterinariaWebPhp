<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DocumentoController;
use App\Http\Controllers\Ventas\TransaccionPagoController;
use App\Http\Controllers\User\UbigeoController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\Ventas\ProductosController;
use App\Http\Controllers\Ventas\VentasController;
use App\Http\Middleware\VerifyToken;
use App\Mail\ConfirmarCorreoMail;
use App\Models\Tipo_documento as TipoDocumento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;

Route::prefix('/v1')->group(function () {

    Route::get('/tipoDocumento', function (Request $request) {
        $tipo_documento = TipoDocumento::all();
        return response()->json($tipo_documento);
    });

    Route::get('/consultar-dni', [DocumentoController::class, 'consultarDNI']);
    Route::get('/consultar-ruc', [DocumentoController::class, 'consultarRUC']);

    Route::post('/verificarCorreo', function (Request $request) {
        $correo = $request->input('correo'); // o $request->correo
        $code = rand(100000, 999999);

        Mail::to($correo)->send(new ConfirmarCorreoMail($code));
        Log::info("Código enviado", ['code' => $code]);
        return response()->json(['message' => 'Código enviado correctamente', "code" => $code]);
    });

    Route::prefix('/auth')->group(function () {
        Route::post('/registro', [AuthController::class, "registro"]);
        Route::post("/login", [AuthController::class, "inicioSesion"]);
        Route::post("/forgot-password", [AuthController::class, "recuperarContrasena"]);
        Route::post("/verify-reset-token", [AuthController::class, "verifyResetToken"]);
        Route::post("/reset-password", [AuthController::class, "resetPassword"]);
        Route::post("/verificar-documento", [AuthController::class, "verificarDniExistente"]);
        Route::post("/verificar-usuario", [AuthController::class, "verificarUsuarioExistente"]);
    });


    Route::middleware([VerifyToken::class])->group(function () {
        Route::prefix('perfil')->group(function () {
            Route::get("/{id_usuario}", [UserController::class, "perfil"]);
            Route::put("/{id_usuario}", [UserController::class, "actualizarPerfil"]);
            Route::patch("/{id_usuario}", [UserController::class, "actualizarPerfilParcial"]);
            Route::get("/{id_usuario}/direccion", [UserController::class, "direccion"]);
            Route::post("/{id_usuario}/direccion/guardar", [UserController::class, "guardarDireccion"]);
        });
        Route::prefix('ubigeos')->group(function () {
            Route::get('/departamentos', [UbigeoController::class, 'getDepartamentos']);
            Route::get('/provincias/{departamento}', [UbigeoController::class, 'getProvincias']);
            Route::get('/distritos/{provincia}', [UbigeoController::class, 'getDistritos']);
            Route::get('/{codigo_ubigeo}', [UbigeoController::class, 'getUbigeo']);
        });

        Route::prefix("ventas")->group(function () {
            Route::post("/registrar", [VentasController::class, "registrarVenta"]);
            ROute::get("/obtenerPedidos/{id_usuario}", [VentasController::class, "pedidosCliente"]);
        });

        Route::get("metodos-pago", [VentasController::class, "obtenerMetodosPago"]);
        Route::get("/productos", [ProductosController::class, "index"]);
        Route::get("/categorias-productos", [ProductosController::class, "categorias"]);
        Route::get('/productos-categorias', [ProductosController::class, 'countProductosCategorias']);
        Route::get('/productos-destacados', [ProductosController::class, 'getProductosDestacados']);

        Route::prefix('transacciones-pago')->group(function () {
            Route::post('/subir-comprobante', [TransaccionPagoController::class, 'subirComprobante']);
            Route::get('/venta/{idVenta}', [TransaccionPagoController::class, 'obtenerPorVenta']);
            Route::get('/{idTransaccion}/comprobante', [TransaccionPagoController::class, 'obtenerComprobante']);
            Route::put('/{idTransaccion}/estado', [TransaccionPagoController::class, 'actualizarEstado']);
            Route::delete('/{idTransaccion}/comprobante', [TransaccionPagoController::class, 'eliminarComprobante']);
        });
    });
});
