<?php

namespace App\Http\Controllers;

use App\Http\Resources\LoginResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Persona;
use App\Models\Clientes;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function registro(Request $request)
    {
        try {
            DB::beginTransaction();

            // 🔹 Crear persona
            $persona = Persona::create([
                "id_tipo_documento" => $request->id_tipo_documento,
                "numero_documento" => $request->numeroDocumento,
                "nombre" => $request->nombre,
                "apellido_paterno" => $request->apellidoPaterno,
                "apellido_materno" => $request->apellidoMaterno,
                "fecha_nacimiento" => $request->fechaNacimiento,
                "nacionalidad" => $request->nacionalidad,
                "correo_electronico_personal" => $request->correo,
                "numero_telefono_personal" => $request->telefono,
                "fecha_registro" => now(),
            ]);

            // 🔹 Crear cliente (relacionado a persona)
            $cliente = Clientes::create([
                "id_persona" => $persona->id_persona,
                "fecha_creacion" => now(),
            ]);

            // 🔹 Crear usuario (relacionado a cliente/persona)
            $usuario = User::create([
                "usuario" => $request->usuario,
                "contrasena" => Hash::make($request->contrasena),
                "estado" => "activo",
                "fecha_creacion" => now(),
                "id_persona" => $persona->id_persona,
            ]);

            DB::commit();

            $token = Auth::login($usuario);

            return response()->json([
                'message' => 'Usuario registrado correctamente',
                'persona' => $persona,
                'usuario' => $usuario,
                'token' => $token
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Error al registrar el usuario',
                'detalle' => $e->getMessage()
            ], 500);
        }
    }

    public function inicioSesion(Request $request)
    {
        try {
            // Validación
            $validate = Validator::make($request->all(), [
                'correo' => 'required|email',
                'contrasena' => 'required',
            ]);

            if ($validate->fails()) {
                return response()->json([
                    'error' => 'Datos incorrectos',
                    'detalle' => $validate->errors()
                ], 401);
            }

            // Buscar persona y su usuario
            $persona = Persona::where("correo_electronico_personal", $request->correo)->first();

            if (!$persona || !$persona->user) {
                return response()->json(['error' => 'Usuario no encontrado'], 404);
            }

            // Verificar contraseña
            if (!Hash::check($request->contrasena, $persona->user->contrasena)) {
                return response()->json(['error' => 'Contraseña incorrecta'], 401);
            }

            // Generar JWT
            $token = JWTAuth::fromUser($persona->user);

            // Retornar el Resource
            return (new LoginResponse($persona))
                ->additional(['token' => $token]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al iniciar sesión',
                'detalle' => $e->getMessage()
            ], 500);
        }
    }
}
