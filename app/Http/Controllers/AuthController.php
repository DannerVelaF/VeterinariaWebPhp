<?php

namespace App\Http\Controllers;

use App\Http\Resources\LoginResponse;
use App\Mail\PasswordResetMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Persona;
use App\Models\Clientes;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Str;

class AuthController extends Controller
{


    public function registro(Request $request)
    {
        try {
            DB::beginTransaction();

            // 🔹 BUSCAR PERSONA EXISTENTE
            $persona = Persona::where('numero_documento', $request->numeroDocumento)
                ->where('id_tipo_documento', $request->id_tipo_documento)
                ->first();

            if (!$persona) {
                // 🔹 CASO 1: Persona NUEVA
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

                // Crear cliente para persona nueva
                Clientes::create([
                    "id_persona" => $persona->id_persona,
                    "fecha_creacion" => now(),
                ]);
            } else {
                // 🔹 CASO 2: Persona EXISTENTE - Actualizar datos
                $persona->update([
                    "nombre" => $request->nombre,
                    "apellido_paterno" => $request->apellidoPaterno,
                    "apellido_materno" => $request->apellidoMaterno,
                    "fecha_nacimiento" => $request->fechaNacimiento,
                    "nacionalidad" => $request->nacionalidad,
                    "correo_electronico_personal" => $request->correo,
                    "numero_telefono_personal" => $request->telefono,
                    "fecha_actualizacion" => now(),
                ]);
            }

            // 🔹 VERIFICAR que no tenga usuario ya registrado
            $usuarioExistente = User::where('id_persona', $persona->id_persona)->first();
            if ($usuarioExistente) {
                return response()->json([
                    'error' => 'Esta persona ya tiene un usuario registrado'
                ], 400);
            }

            // 🔹 Crear usuario
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
            $persona = Persona::where("correo_electronico_personal", $request->correo)
                ->with(['user', 'cliente'])
                ->first();

            if (!$persona || !$persona->user) {
                return response()->json(['error' => 'Usuario no encontrado'], 404);
            }

            // Verificar contraseña
            if (!Hash::check($request->contrasena, $persona->user->contrasena)) {
                return response()->json(['error' => 'Contraseña incorrecta'], 401);
            }

            if (!$persona->cliente) {
                Log::error("Error en cliente");
                return response()->json([
                    'error' => 'Acceso restringido. Solo clientes pueden acceder al ecommerce.'
                ], 403);
            }

            // Actualizamos ultimo login
            $persona->user->update([
                "ultimo_login" => now()
            ]);

            // Generar JWT
            $token = JWTAuth::fromUser($persona->user);

            // Retornar el Resource
            return (new LoginResponse($persona))
                ->additional(['token' => $token]);

        } catch (\Exception $e) {
            Log::error("Error al iniciar sesión", ["error" => $e->getMessage()]);
            return response()->json([
                'error' => 'Error al iniciar sesión',
                'detalle' => $e->getMessage()
            ], 500);
        }
    }

    public function recuperarContrasena(Request $request)
    {
        try {
            // 🔹 CORRECCIÓN: Usar 'email' en lugar de 'correo'
            $validate = Validator::make($request->all(), [
                'email' => 'required|email', // Cambiado de 'correo' a 'email'
            ]);

            if ($validate->fails()) {
                return response()->json([
                    'error' => 'Correo electrónico inválido',
                    'detalle' => $validate->errors()
                ], 400);
            }

            // 🔹 CORRECCIÓN: Usar $request->email
            $correo = $request->email; // Cambiado de $request->correo a $request->email

            // 🔹 CORRECCIÓN: Buscar persona por correo
            $persona = Persona::where("correo_electronico_personal", $correo)->first();

            // 🔹 CORRECCIÓN: Siempre enviar la misma respuesta por seguridad
            // pero procesar el envío si existe
            if ($persona) {
                $token = Str::random(60);

                DB::table('password_reset_tokens')->updateOrInsert(
                    ['email' => $correo],
                    [
                        'token' => Hash::make($token),
                        'created_at' => Carbon::now()
                    ]
                );

                $resetLink = "http://localhost:5173/reset-password?token=" . $token . "&email=" . urlencode($correo);

                // 🔹 CORRECCIÓN: Log antes de enviar
                Log::info('Enviando correo de recuperación', ['correo' => $correo, "resetLink" => $resetLink]);

                // Enviar el correo
                Mail::to($correo)->send(new PasswordResetMail($resetLink));
            } else {
                Log::info('Solicitud de recuperación para correo no existente', ['correo' => $correo]);
            }

            // 🔹 CORRECCIÓN: Siempre devolver el mismo mensaje por seguridad
            return response()->json([
                'message' => 'Si el correo existe, recibirás un enlace de recuperación'
            ], 200);
        } catch (\Exception $e) {

            return response()->json([
                'message' => 'Si el correo existe, recibirás un enlace de recuperación'
            ], 200);
        }
    }

    // Agrega estos métodos después de recuperarContrasena

    /**
     * Verificar token de recuperación
     */
    public function verifyResetToken(Request $request)
    {
        try {
            $validate = Validator::make($request->all(), [
                'email' => 'required|email',
                'token' => 'required|string'
            ]);

            if ($validate->fails()) {
                return response()->json([
                    'valid' => false,
                    'message' => 'Datos inválidos'
                ], 400);
            }

            $record = DB::table('password_reset_tokens')->where('email', $request->email)->first();

            if (!$record) {
                return response()->json([
                    'valid' => false,
                    'message' => 'Enlace inválido o expirado'
                ], 200);
            }

            // Verificar expiración (1 hora)
            if (Carbon::parse($record->created_at)->addHour()->isPast()) {
                DB::table('password_reset_tokens')->where('email', $request->email)->delete();
                return response()->json([
                    'valid' => false,
                    'message' => 'El enlace ha expirado'
                ], 200);
            }

            // Verificar token
            $isValid = Hash::check($request->token, $record->token);

            return response()->json([
                'valid' => $isValid,
                'message' => $isValid ? 'Token válido' : 'Token inválido'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'valid' => false,
                'message' => 'Error al verificar el enlace'
            ], 500);
        }
    }

    /**
     * Restablecer contraseña
     */
    public function resetPassword(Request $request)
    {
        try {
            DB::beginTransaction();

            $validate = Validator::make($request->all(), [
                'email' => 'required|email',
                'token' => 'required|string',
                'password' => 'required|string|min:8|confirmed'
            ]);

            if ($validate->fails()) {
                return response()->json([
                    'error' => 'Datos inválidos',
                    'detalle' => $validate->errors()
                ], 400);
            }

            // Buscar el token
            $record = DB::table('password_reset_tokens')->where('email', $request->email)->first();

            if (!$record) {
                return response()->json([
                    'message' => 'Enlace inválido o ya utilizado'
                ], 400);
            }

            // Verificar expiración
            if (Carbon::parse($record->created_at)->addHour()->isPast()) {
                DB::table('password_reset_tokens')->where('email', $request->email)->delete();
                return response()->json([
                    'message' => 'El enlace ha expirado'
                ], 400);
            }

            // Verificar token
            if (!Hash::check($request->token, $record->token)) {
                return response()->json([
                    'message' => 'Enlace inválido'
                ], 400);
            }

            // Buscar la persona y su usuario
            $persona = Persona::where('correo_electronico_personal', $request->email)->first();

            if (!$persona || !$persona->user) {
                return response()->json([
                    'message' => 'Usuario no encontrado'
                ], 404);
            }

            // Actualizar contraseña
            $persona->user->update([
                'contrasena' => Hash::make($request->password)
            ]);

            // Eliminar token (hacerlo de un solo uso)
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();

            DB::commit();


            return response()->json([
                'message' => 'Contraseña restablecida correctamente'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'error' => 'Error al restablecer la contraseña',
                'detalle' => $e->getMessage()
            ], 500);
        }
    }

    public function verificarDniExistente(Request $request)
    {
        try {
            $validate = Validator::make($request->all(), [
                'dni' => 'required|string',
                'tipo_documento_id' => 'required|integer'
            ]);

            if ($validate->fails()) {
                return response()->json([
                    "message" => "El dni y tipo de documento son requeridos",
                    "errors" => $validate->errors()
                ], 400);
            }

            $persona = Persona::where("numero_documento", $request->dni)
                ->where("id_tipo_documento", $request->tipo_documento_id)
                ->with("user")
                ->first();

            if (!$persona) {
                // No existe la persona - disponible para registro completo
                Log::info("Documento disponible para registro completo");
                return response()->json([
                    "existe" => false,
                    "message" => "Documento disponible para registro"
                ], 200);
            }

            if ($persona->user != null) {
                // Persona existe Y tiene usuario - ya está registrado en el sistema
                Log::info("El documento ya cuenta con un usuario creado");
                return response()->json([
                    "existe" => true,
                    "sin_usuario" => false, // ← IMPORTANTE: false porque SÍ tiene usuario
                    "message" => "El documento ya está registrado en el sistema",
                    "user" => $persona->user,
                    "correo" => $persona->correo_electronico_personal
                ], 200);
            } else {
                // Persona existe pero NO tiene usuario - caso especial
                Log::info("Persona existe pero sin usuario del sistema");
                return response()->json([
                    "existe" => true,
                    "sin_usuario" => true, // ← IMPORTANTE: true porque NO tiene usuario
                    "message" => "Documento registrado pero sin usuario del sistema",
                    "persona" => $persona,
                    "correo" => $persona->correo_electronico_personal
                ], 200);
            }

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al buscar el usuario existente',
                'detalle' => $e->getMessage()
            ], 500);
        }
    }

    public function verificarUsuarioExistente(Request $request)
    {
        try {
            $validate = Validator::make($request->all(), [
                'usuario' => 'required|string'
            ]);

            if ($validate->fails()) {
                return response()->json([
                    "message" => "El usuario es requerido",
                    "errors" => $validate->errors()
                ], 400);
            }

            // 🔹 CORRECCIÓN: El campo correcto es "usuario" no "nombre_usuario"
            $user = User::where("usuario", $request->usuario)->first();

            if ($user) {
                return response()->json([
                    "existe" => true,
                    "message" => "El nombre de usuario ya está en uso"
                ], 200);
            }

            return response()->json([
                "existe" => false,
                "message" => "Nombre de usuario disponible"
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al buscar el usuario existente',
                'detalle' => $e->getMessage()
            ], 500);
        }
    }

}
