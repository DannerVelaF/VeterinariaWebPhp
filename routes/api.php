<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Ventas\VentasController;
use App\Http\Middleware\VerifyToken;
use App\Mail\ConfirmarCorreoMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Tipo_documento as TipoDocumento;
use App\Models\Ubigeo;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

Route::prefix('/v1')->group(function () {

    Route::get('/tipoDocumento', function (Request $request) {
        $tipo_documento = TipoDocumento::all();
        return response()->json($tipo_documento);
    });

    Route::get("/ubigeos", function (Request $request) {
        $ubigeos = Ubigeo::all();

        return response()->json($ubigeos);
    });

    Route::post('/verificarCorreo', function (Request $request) {
        $correo = $request->input('correo'); // o $request->correo
        $code = rand(100000, 999999);

        Mail::to($correo)->send(new ConfirmarCorreoMail($code));

        return response()->json(['message' => 'Código enviado correctamente', "code" => $code]);
    });

    Route::prefix('/auth')->group(function () {
        Route::post('/registro', [AuthController::class, "registro"]);
        Route::post("/login", [AuthController::class, "inicioSesion"]);
        Route::post("/forgot-password", [AuthController::class, "recuperarContrasena"]);
        Route::post("/verify-reset-token", [AuthController::class, "verifyResetToken"]);
        Route::post("/reset-password", [AuthController::class, "resetPassword"]);
    });

    Route::get('/ventas', [VentasController::class, "registrarVenta"]);


    Route::middleware([VerifyToken::class])->group(function () {});
});
