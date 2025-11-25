<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\LoginResponse;
use App\Mail\PasswordResetMail;
use App\Models\Clientes;
use App\Models\Persona;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{


    public function registro(Request $request)
    {
        try {
            DB::beginTransaction();

            // 🔹 BUSCAR PERSONA EXISTENTE
            $persona = Persona::where('numero_documento', $request->numeroDocumento)
                ->where('id_tipo_documento', $request->id_tipo_documento)
                ->with('trabajador', 'user') // Cargar relaciones importantes
                ->first();

            if (!$persona) {
                // 🔹 CASO 1: Persona NUEVA - Crear todo desde cero
                $persona = Persona::create([
                    "id_tipo_documento" => $request->id_tipo_documento,
                    "numero_documento" => $request->numeroDocumento,
                    "nombre" => $request->nombre,
                    "apellido_paterno" => $request->apellidoPaterno,
                    "apellido_materno" => $request->apellidoMaterno,
                    "fecha_nacimiento" => $request->fechaNacimiento,
                    "sexo" => $request->sexo,
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

                // Verificar si es trabajador
                $esTrabajador = $persona->trabajador != null;
                $tieneUsuario = $persona->user != null;

                // Datos básicos que siempre se pueden actualizar
                $datosActualizacion = [
                    "nombre" => $request->nombre,
                    "apellido_paterno" => $request->apellidoPaterno,
                    "apellido_materno" => $request->apellidoMaterno,
                    "fecha_nacimiento" => $request->fechaNacimiento,
                    "nacionalidad" => $request->nacionalidad,
                    "fecha_actualizacion" => now(),
                ];

                // 🔹 Para trabajadores, permitir actualizar contacto
                if ($esTrabajador) {
                    $datosActualizacion["correo_electronico_personal"] = $request->correo;
                    $datosActualizacion["numero_telefono_personal"] = $request->telefono;

                    Log::info("Actualizando datos de trabajador para registro como cliente", [
                        'id_persona' => $persona->id_persona,
                        'correo_anterior' => $persona->correo_electronico_personal,
                        'correo_nuevo' => $request->correo,
                        'telefono_anterior' => $persona->numero_telefono_personal,
                        'telefono_nuevo' => $request->telefono
                    ]);
                } else {
                    // Para no trabajadores, actualizar todo
                    $datosActualizacion["correo_electronico_personal"] = $request->correo;
                    $datosActualizacion["numero_telefono_personal"] = $request->telefono;
                }

                $persona->update($datosActualizacion);

                // 🔹 Crear relación CLIENTE solo si no existe
                $clienteExistente = Clientes::where('id_persona', $persona->id_persona)->first();
                if (!$clienteExistente) {
                    Clientes::create([
                        "id_persona" => $persona->id_persona,
                        "fecha_creacion" => now(),
                    ]);

                    Log::info("Cliente creado para persona existente", [
                        'id_persona' => $persona->id_persona,
                        'es_trabajador' => $esTrabajador
                    ]);
                }
            }

            // 🔹 MANEJO DE USUARIO - LÓGICA CORREGIDA
            $usuarioExistente = User::where('id_persona', $persona->id_persona)->first();
            $esTrabajador = $persona->trabajador != null;

            if ($usuarioExistente) {
                // 🔹 CASO: Ya tiene usuario (TRABAJADOR) - Actualizar contraseña si se proporciona
                if ($esTrabajador) {
                    Log::info("Trabajador con usuario existente - actualizando datos", [
                        'id_persona' => $persona->id_persona,
                        'usuario_existente' => $usuarioExistente->usuario
                    ]);

                    // Verificar que el usuario enviado sea el mismo que el existente
                    if ($usuarioExistente->usuario !== $request->usuario) {
                        DB::rollBack();
                        return response()->json([
                            'error' => 'Debe usar su usuario actual del sistema: ' . $usuarioExistente->usuario
                        ], 400);
                    }

                    // Actualizar contraseña si se proporciona una nueva
                    if ($request->contrasena) {
                        $usuarioExistente->update([
                            "contrasena" => Hash::make($request->contrasena),
                            "fecha_actualizacion" => now(),
                        ]);

                        Log::info("Contraseña actualizada para trabajador", [
                            'usuario' => $usuarioExistente->usuario
                        ]);
                    }

                    $usuario = $usuarioExistente;

                } else {
                    // 🔹 CASO: Usuario común ya registrado - ERROR
                    DB::rollBack();
                    return response()->json([
                        'error' => 'Esta persona ya tiene un usuario registrado en el sistema',
                        'usuario_existente' => $usuarioExistente->usuario
                    ], 400);
                }

            } else {
                // 🔹 CASO: No tiene usuario - Crear nuevo
                $usuario = User::create([
                    "usuario" => $request->usuario,
                    "contrasena" => Hash::make($request->contrasena),
                    "estado" => "activo",
                    "fecha_creacion" => now(),
                    "id_persona" => $persona->id_persona,
                ]);

                Log::info("Nuevo usuario creado", [
                    'id_persona' => $persona->id_persona,
                    'usuario' => $request->usuario,
                    'es_trabajador' => $esTrabajador
                ]);
            }

            // 🔹 SOLO CREAR RELACIÓN CLIENTE (sin asignar roles)
            // La relación cliente ya se creó arriba en la sección de persona existente

            DB::commit();

            // 🔹 Iniciar sesión automáticamente
            $token = Auth::login($usuario);

            return response()->json([
                'message' => 'Usuario registrado correctamente',
                'es_trabajador' => $esTrabajador,
                'usuario_existente' => $usuarioExistente ? true : false,
                'persona' => [
                    'id' => $persona->id_persona,
                    'nombre_completo' => $persona->nombre . ' ' . $persona->apellido_paterno . ' ' . $persona->apellido_materno,
                    'correo' => $persona->correo_electronico_personal,
                    'telefono' => $persona->numero_telefono_personal
                ],
                'usuario' => [
                    'usuario' => $usuario->usuario,
                    'estado' => $usuario->estado
                ],
                'token' => $token
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error en registro de usuario: ' . $e->getMessage(), [
                'request' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Error al registrar el usuario',
                'detalle' => $e->getMessage()
            ], 500);
        }
    }

    public function inicioSesion(Request $request)
    {
        try {
            // Validación más flexible
            $validate = Validator::make($request->all(), [
                'credencial' => 'required|string', // Cambiado de 'correo' a 'credencial'
                'contrasena' => 'required',
            ]);

            if ($validate->fails()) {
                return response()->json([
                    'error' => 'Datos incorrectos',
                    'detalle' => $validate->errors()
                ], 401);
            }

            $credencial = $request->credencial;

            // Determinar si es email o usuario
            $esEmail = filter_var($credencial, FILTER_VALIDATE_EMAIL);

            // Buscar persona según el tipo de credencial
            if ($esEmail) {
                // Buscar por correo electrónico
                $persona = Persona::where("correo_electronico_personal", $credencial)
                    ->with(['user', 'cliente'])
                    ->first();
            } else {
                // Buscar por nombre de usuario
                $persona = Persona::whereHas('user', function ($query) use ($credencial) {
                    $query->where('usuario', $credencial);
                })
                    ->with(['user', 'cliente'])
                    ->first();
            }

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
                    'error' => 'Usuario no encontrado'
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
                ->with("user", "cliente", "trabajador")
                ->first();

            if (!$persona) {
                Log::info("Documento disponible para registro completo");
                return response()->json([
                    "existe" => false,
                    "message" => "Documento disponible para registro"
                ], 200);
            }

            $tieneUsuario = $persona->user != null;
            $esCliente = $persona->cliente != null;
            $esTrabajador = $persona->trabajador != null;

            Log::info("Estado - Usuario: " . ($tieneUsuario ? 'Sí' : 'No') .
                ", Cliente: " . ($esCliente ? 'Sí' : 'No') .
                ", Trabajador: " . ($esTrabajador ? 'Sí' : 'No'));

            // 📊 CASO 1: Tiene usuario Y es cliente
            if ($tieneUsuario && $esCliente) {
                Log::info("El documento ya cuenta con un usuario creado y es cliente");
                return response()->json([
                    "existe" => true,
                    "sin_usuario" => false,
                    "es_cliente" => true,
                    "es_trabajador" => $esTrabajador,
                    "message" => "El documento ya está registrado en el sistema",
                    "user" => $persona->user,
                    "correo" => $persona->correo_electronico_personal,
                ], 200);
            }

            // 📊 CASO 2: Tiene usuario pero NO es cliente (TRABAJADOR CON USUARIO)
            if ($tieneUsuario && !$esCliente && $esTrabajador) {
                Log::info("Trabajador con usuario existente - necesita registrarse como cliente");
                return response()->json([
                    "existe" => true,
                    "sin_usuario" => false,
                    "es_cliente" => false,
                    "es_trabajador" => true,
                    "message" => "Usted es trabajador. Complete su registro como cliente usando su usuario existente.",
                    "user" => $persona->user,
                    "persona" => $persona->only([
                        'id_persona', 'nombre', 'apellido_paterno', 'apellido_materno',
                        'correo_electronico_personal', 'numero_telefono_personal',
                        'fecha_nacimiento', 'nacionalidad', 'sexo'
                    ]),
                    "correo" => $persona->correo_electronico_personal,
                    "accion_requerida" => "registro_trabajador_cliente"
                ], 200);
            }

            // 📊 CASO 3: No tiene usuario pero SÍ es trabajador
            if (!$tieneUsuario && $esTrabajador) {
                Log::info("Trabajador sin usuario - registro completo");
                return response()->json([
                    "existe" => true,
                    "sin_usuario" => true,
                    "es_cliente" => $esCliente,
                    "es_trabajador" => true,
                    "message" => "Usted es trabajador. Complete su registro para acceder al ecommerce.",
                    "persona" => $persona->only([
                        'id_persona', 'nombre', 'apellido_paterno', 'apellido_materno',
                        'fecha_nacimiento', 'sexo', 'nacionalidad', 'correo_electronico_personal',
                        'correo_electronico_secundario', 'numero_telefono_personal', 'numero_telefono_secundario'
                    ]),
                    "correo" => $persona->correo_electronico_personal,
                    "accion_requerida" => "registro_trabajador_completo"
                ], 200);
            }

            // 📊 CASO 4: No tiene usuario (persona común)
            Log::info("Persona existe pero sin usuario del sistema");
            return response()->json([
                "existe" => true,
                "sin_usuario" => true,
                "es_cliente" => $esCliente,
                "es_trabajador" => $esTrabajador,
                "message" => "Documento registrado pero sin usuario del sistema",
                "persona" => $persona,
                "correo" => $persona->correo_electronico_personal,
                "accion_requerida" => "completar_usuario"
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error en verificarDniExistente: ' . $e->getMessage());
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

            $user = User::where("usuario", $request->usuario)
                ->with('persona.trabajador') // Cargar relación de trabajador
                ->first();

            if ($user) {
                // Verificar si es trabajador
                $esTrabajador = $user->persona && $user->persona->trabajador != null;

                // Verificar si pertenece al mismo documento (si se proporciona)
                $mismoDocumento = false;
                if ($request->has('documento') && $user->persona) {
                    $mismoDocumento = $user->persona->numero_documento === $request->documento;
                }

                return response()->json([
                    "existe" => true,
                    "es_trabajador" => $esTrabajador,
                    "es_mismo_documento" => $mismoDocumento,
                    "message" => $esTrabajador && $mismoDocumento ?
                        "Puede usar su usuario existente como trabajador" :
                        "El nombre de usuario ya está en uso"
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
