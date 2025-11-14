<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\PerfilRequest;
use App\Models\Direccion;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{

    use ApiResponse;

    /**
     * @param $id_usuario
     * @return \Illuminate\Http\JsonResponse
     * Obtiene la informaciòn del perfil
     */
    public function perfil($id_usuario) // Recibe el parámetro de la ruta
    {
        try {
            // Validar el ID directamente
            if (!is_numeric($id_usuario) || $id_usuario < 1) {
                return $this->validationErrorResponse(['id_usuario' => ['El ID de usuario debe ser un número válido']]);
            }

            $user = User::where("id_usuario", $id_usuario)
                ->with("persona")
                ->first();

            if (!$user) {
                return $this->notFoundResponse("Usuario no encontrado");
            }

            return $this->successResponse($user, "Información del usuario obtenida correctamente");

        } catch (\Exception $e) {
            Log::error('Error en perfil: ' . $e->getMessage());
            return $this->serverErrorResponse();
        }
    }

    public function direccion($id_usuario)
    {
        try {
            if (!is_numeric($id_usuario) || $id_usuario < 1) {
                return $this->validationErrorResponse(['id_usuario' => ['El ID de usuario debe ser un número válido']]);
            }

            $user = User::where("id_usuario", $id_usuario)
                ->with(["persona.direccion.ubigeo"])
                ->first();// Cargar persona y su dirección                ->first();

            if (!$user) {
                return $this->notFoundResponse("Usuario no encontrado");
            }

            // Verificar si el usuario tiene dirección
            if (!$user->persona->direccion) {
                return $this->successResponse(
                    null,
                    "El usuario no tiene dirección registrada",
                    200
                );
            }

            return $this->successResponse(
                [
                    'direccion' => $user->persona->direccion,
                ],
                "Dirección obtenida correctamente"
            );

        } catch (\Exception $e) {
            Log::error('Error obteniendo dirección - Usuario ID: ' . $id_usuario . ' - Error: ' . $e->getMessage());
            return $this->serverErrorResponse('Error al obtener la dirección');
        }
    }


    /**
     * Guardar o actualizar dirección del usuario
     */
    public function guardarDireccion($id_usuario, Request $request)
    {
        try {
            if (!is_numeric($id_usuario) || $id_usuario < 1) {
                return $this->validationErrorResponse(['id_usuario' => ['El ID de usuario debe ser un número válido']]);
            }

            $user = User::where("id_usuario", $id_usuario)
                ->with("persona")
                ->first();

            if (!$user) {
                return $this->notFoundResponse("Usuario no encontrado");
            }


            $validator = Validator::make($request->all(), [
                'zona' => 'nullable|string|max:100',
                'tipo_calle' => 'required|string|max:10',
                'nombre_calle' => 'required|string|max:255',
                'numero' => 'required|string|max:20',
                'codigo_postal' => 'nullable|string|max:10',
                'referencia' => 'nullable|string|max:500',
                'codigo_ubigeo' => 'required|string|exists:ubigeos,codigo_ubigeo'
            ], [
                'tipo_calle.required' => 'El tipo de calle es obligatorio',
                'nombre_calle.required' => 'El nombre de calle es obligatorio',
                'numero.required' => 'El número es obligatorio',
                'codigo_ubigeo.required' => 'Debe seleccionar un distrito',
                'codigo_ubigeo.exists' => 'El ubigeo seleccionado no es válido'
            ]);

            if ($validator->fails()) {
                return $this->validationErrorResponse($validator->errors());
            }

            DB::beginTransaction();

            try {
                $persona = $user->persona;

                // Verificar si la persona existe
                if (!$persona) {
                    throw new \Exception("La persona no existe para este usuario");
                }

                if ($persona->id_direccion) {
                    // Actualizar dirección existente
                    $direccion = Direccion::find($persona->id_direccion);

                    if (!$direccion) {
                        throw new \Exception("No se encontró la dirección con ID: " . $persona->id_direccion);
                    }

                    $direccion->update($request->all());
                    $mensaje = "Dirección actualizada correctamente";

                } else {
                    // Crear nueva dirección

                    $direccion = Direccion::create($request->all());


                    $persona->id_direccion = $direccion->id_direccion;
                    $persona->save();

                    $mensaje = "Dirección creada correctamente";
                }

                DB::commit();
                // Recargar la dirección con las relaciones
                $direccion->refresh();
                $direccion->load('ubigeo');

                return $this->successResponse(
                    ['direccion' => $direccion],
                    $mensaje
                );

            } catch (\Exception $e) {
                DB::rollBack();

                return $this->serverErrorResponse('Error al guardar la dirección: ' . $e->getMessage());
            }

        } catch (\Exception $e) {
            return $this->serverErrorResponse('Error al guardar la dirección');
        }
    }

    /**
     * @param $id_usuario
     * @param PerfilRequest $request
     * @return \Illuminate\Http\JsonResponse
     * Actualiza la información del usuario en base de datos
     */
    public function actualizarPerfil($id_usuario, PerfilRequest $request)
    {
        try {
            if (!is_numeric($id_usuario) || $id_usuario < 1) {
                return $this->validationErrorResponse([
                    'id_usuario' => ['El ID de usuario debe ser un número válido']
                ]);
            }

            $user = User::where("id_usuario", $id_usuario)
                ->with("persona")
                ->first();

            if (!$user) {
                return $this->notFoundResponse("Usuario no encontrado");
            }

            DB::beginTransaction();

            try {
                $updatedFields = [];
                $hasUpdates = false;

                // Actualizar campos del usuario
                if ($request->has('usuario')) {
                    $user->usuario = $request->usuario;
                    $updatedFields[] = 'usuario';
                    $hasUpdates = true;
                }

                // Actualizar campos de la persona
                $persona = $user->persona;
                $personaUpdated = false;

                if ($persona) {
                    $personaFields = [
                        'id_tipo_documento',
                        'numero_documento',
                        'nombre',
                        'apellido_paterno',
                        'apellido_materno',
                        'fecha_nacimiento',
                        'sexo',
                        'nacionalidad',
                        'correo_electronico_personal',
                        'correo_electronico_secundario',
                        'numero_telefono_personal',
                        'numero_telefono_secundario'
                    ];

                    foreach ($personaFields as $field) {
                        if ($request->has("persona.{$field}")) {
                            $persona->$field = $request->input("persona.{$field}");
                            $personaUpdated = true;
                            $updatedFields[] = "persona.{$field}";
                        }
                    }

                    // También verificar campos directos (sin el prefijo persona.)
                    $directFields = [
                        'correo_electronico_personal',
                        'correo_electronico_secundario',
                        'numero_telefono_personal',
                        'numero_telefono_secundario'
                    ];

                    foreach ($directFields as $field) {
                        if ($request->has($field)) {
                            $persona->$field = $request->$field;
                            $personaUpdated = true;
                            $updatedFields[] = $field;
                        }
                    }

                    if ($personaUpdated) {
                        $persona->fecha_actualizacion = now();
                        $persona->save();
                        $hasUpdates = true;
                    }
                }

                // Actualizar fecha_actualizacion del usuario si hubo cambios
                if ($hasUpdates) {
                    $persona->fecha_actualizacion = now();
                    $persona->save();
                    $user->fecha_actualizacion = now();
                    $user->save();
                }

                DB::commit();

                $user->load('persona');

                Log::info("Perfil actualizado - Usuario ID: {$id_usuario}, Campos: " . implode(', ', $updatedFields));

                return $this->successResponse(
                    $user,
                    "Perfil actualizado correctamente"
                );

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Error en actualizarPerfil - Transacción: ' . $e->getMessage());
                return $this->serverErrorResponse('Error al actualizar el perfil');
            }

        } catch (\Exception $e) {
            Log::error('Error en actualizarPerfil: ' . $e->getMessage());
            return $this->serverErrorResponse('Error al actualizar el perfil');
        }
    }

    /**
     * @param $id_usuario
     * @param PerfilRequest $request
     * @return \Illuminate\Http\JsonResponse
     * Actualiza la información del perfil de manera parcial
     */
    public function actualizarPerfilParcial($id_usuario, PerfilRequest $request)
    {
        try {
            if (!is_numeric($id_usuario) || $id_usuario < 1) {
                return $this->validationErrorResponse([
                    'id_usuario' => ['El ID de usuario debe ser un número válido']
                ]);
            }

            $user = User::where("id_usuario", $id_usuario)
                ->with("persona")
                ->first();

            if (!$user) {
                return $this->notFoundResponse("Usuario no encontrado");
            }

            DB::beginTransaction();

            try {
                $updatedFields = [];
                $hasUpdates = false;

                // Actualizar usuario
                if ($request->has('usuario')) {
                    $user->usuario = $request->usuario;
                    $updatedFields[] = 'usuario';
                    $hasUpdates = true;
                }

                // Actualizar persona
                $persona = $user->persona;
                $personaUpdated = false;

                if ($persona) {
                    // Campos anidados (persona.campo)
                    $nestedFields = [
                        'id_tipo_documento',
                        'numero_documento',
                        'nombre',
                        'apellido_paterno',
                        'apellido_materno',
                        'fecha_nacimiento',
                        'sexo',
                        'nacionalidad',
                        'correo_electronico_personal',
                        'correo_electronico_secundario',
                        'numero_telefono_personal',
                        'numero_telefono_secundario'
                    ];

                    foreach ($nestedFields as $field) {
                        $nestedField = "persona.{$field}";
                        if ($request->has($nestedField)) {
                            $persona->$field = $request->input($nestedField);
                            $personaUpdated = true;
                            $updatedFields[] = $nestedField;
                        }
                    }

                    // Campos directos (sin prefijo persona.)
                    $directFields = [
                        'correo_electronico_personal',
                        'correo_electronico_secundario',
                        'numero_telefono_personal',
                        'numero_telefono_secundario'
                    ];

                    foreach ($directFields as $field) {
                        if ($request->has($field)) {
                            $persona->$field = $request->$field;
                            $personaUpdated = true;
                            $updatedFields[] = $field;
                        }
                    }

                    if ($personaUpdated) {
                        $persona->fecha_actualizacion = now();
                        $persona->save();
                        $hasUpdates = true;
                    }
                }

                // Actualizar fecha_actualizacion del usuario si hubo cambios
                if ($hasUpdates) {

                    $persona->fecha_actualizacion = now();
                    $persona->save();
                    $user->fecha_actualizacion = now();
                    $user->save();

                }

                DB::commit();

                $user->load('persona');

                Log::info("Perfil actualizado (PATCH) - Usuario ID: {$id_usuario}, Campos: " . implode(', ', $updatedFields));

                return $this->successResponse(
                    $user,
                    "Perfil actualizado correctamente"
                );

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Error actualizando perfil (PATCH) - Transacción: ' . $e->getMessage());
                return $this->serverErrorResponse('Error al actualizar el perfil');
            }

        } catch (\Exception $e) {
            Log::error('Error actualizando perfil (PATCH) - Usuario ID: ' . $id_usuario . ' - Error: ' . $e->getMessage());
            return $this->serverErrorResponse('Error al actualizar el perfil');
        }
    }
}
