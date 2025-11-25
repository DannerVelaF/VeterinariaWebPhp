<?php

namespace App\Http\Controllers\Ventas;

use App\Http\Controllers\Controller;
use App\Models\TransaccionPago;
use App\Models\Ventas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class TransaccionPagoController extends Controller
{
    const ESTADO_PENDIENTE = 'pendiente';
    const ESTADO_CONFIRMADO = 'confirmado';
    const ESTADO_RECHAZADO = 'rechazado';

    /**
     * Subir comprobante (solo recepcionar - estado permanece como pendiente)
     */
    public function subirComprobante(Request $request)
    {
        try {
            DB::beginTransaction();

            $validator = Validator::make($request->all(), [
                'id_venta' => 'required|exists:ventas,id_venta',
                'comprobante' => 'required|file|mimes:jpeg,png,jpg,pdf|max:5120', // 5MB
                'referencia' => 'nullable|string|max:100',
                'fecha_pago' => 'nullable|date',
                'datos_adicionales' => 'nullable|json',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error de validación',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Buscar la transacción de pago existente
            $transaccion = TransaccionPago::where('id_venta', $request->id_venta)
                ->where('estado', self::ESTADO_PENDIENTE)
                ->first();

            if (!$transaccion) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontró una transacción de pago pendiente para esta venta'
                ], 404);
            }

            // Verificar que la venta existe
            $venta = Ventas::findOrFail($request->id_venta);

            // Subir el archivo del comprobante
            if ($request->hasFile('comprobante')) {
                $file = $request->file('comprobante');
                $fileName = 'comprobante_' . time() . '_' . $venta->id_venta . '.' . $file->getClientOriginalExtension();
                $filePath = $file->storeAs('comprobantes_pago', $fileName, 'public');

                // Preparar datos adicionales
                $datosAdicionales = [];
                if ($request->datos_adicionales) {
                    $datosAdicionales = json_decode($request->datos_adicionales, true);
                }

                // Agregar información del archivo a datos adicionales
                $datosAdicionales['archivo'] = [
                    'nombre_original' => $file->getClientOriginalName(),
                    'tipo' => $file->getClientOriginalExtension(),
                    'tamaño' => $file->getSize(),
                    'fecha_subida' => now()->toISOString(),
                ];

                // Actualizar la transacción de pago (MANTENER ESTADO PENDIENTE)
                $transaccion->update([
                    'referencia' => $request->referencia,
                    'fecha_pago' => $request->fecha_pago ?: now(),
                    'comprobante_url' => Storage::url($filePath),
                    'datos_adicionales' => json_encode($datosAdicionales),
                    // NO cambiar estado a confirmado - se mantiene pendiente para verificación
                    'estado' => self::ESTADO_PENDIENTE,
                ]);

                // La venta sigue pendiente hasta que el personal verifique
                // $venta->update(['id_estado_venta' => ID_ESTADO_PENDIENTE]);

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Comprobante subido exitosamente. Espera la verificación del personal.',
                    'data' => [
                        'transaccion' => $transaccion->fresh(),
                        'comprobante_url' => $transaccion->comprobante_url
                    ]
                ], 200);
            }

            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'No se pudo subir el comprobante'
            ], 400);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al subir el comprobante',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Actualizar estado de la transacción (para administradores)
     * Aquí sí se cambia el estado de la venta cuando se confirma
     */
    public function actualizarEstado(Request $request, $idTransaccion)
    {
        try {
            DB::beginTransaction();

            $validator = Validator::make($request->all(), [
                'estado' => 'required|in:' . self::ESTADO_CONFIRMADO . ',' . self::ESTADO_RECHAZADO . ',' . self::ESTADO_PENDIENTE,
                'observaciones' => 'nullable|string|max:500',
                'id_estado_venta' => 'nullable|exists:estados_venta,id_estado_venta', // Nuevo campo para actualizar estado de venta
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error de validación',
                    'errors' => $validator->errors()
                ], 422);
            }

            $transaccion = TransaccionPago::with('venta')->findOrFail($idTransaccion);

            // Preparar datos adicionales
            $datosAdicionales = $transaccion->datos_adicionales ? json_decode($transaccion->datos_adicionales, true) : [];
            $datosAdicionales['revision'] = [
                'estado_anterior' => $transaccion->estado,
                'estado_nuevo' => $request->estado,
                'fecha_revision' => now()->toISOString(),
                'observaciones' => $request->observaciones,
                'revisado_por' => auth()->id() ?? null,
            ];

            $transaccion->update([
                'estado' => $request->estado,
                'datos_adicionales' => json_encode($datosAdicionales),
            ]);

            // Actualizar estado de la venta si se proporciona
            if ($request->id_estado_venta && $transaccion->venta) {
                $transaccion->venta->update([
                    'id_estado_venta' => $request->id_estado_venta
                ]);
            }

            // O lógica automática: si se confirma el pago, cambiar estado de venta a "confirmado"
            if ($request->estado === self::ESTADO_CONFIRMADO && $transaccion->venta) {
                // Aquí defines el ID del estado "confirmado" o "completado" para ventas
                // $estadoVentaConfirmado = 2; // Ejemplo
                // $transaccion->venta->update(['id_estado_venta' => $estadoVentaConfirmado]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Estado actualizado exitosamente',
                'data' => [
                    'transaccion' => $transaccion->fresh(),
                    'venta' => $transaccion->venta->fresh()
                ]
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el estado',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    /**
     * Obtener transacción de pago por ID de venta
     */
    public function obtenerPorVenta($idVenta)
    {
        try {
            $transaccion = TransaccionPago::where('id_venta', $idVenta)
                ->with(['venta', 'metodoPago'])
                ->first();

            if (!$transaccion) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontró transacción de pago para esta venta'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $transaccion
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener la transacción',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener comprobante (descargar archivo)
     */
    public function obtenerComprobante($idTransaccion)
    {
        try {
            $transaccion = TransaccionPago::findOrFail($idTransaccion);

            if (!$transaccion->comprobante_url) {
                return response()->json([
                    'success' => false,
                    'message' => 'No hay comprobante disponible'
                ], 404);
            }

            // Extraer el path del storage de la URL
            $path = str_replace('/storage/', '', $transaccion->comprobante_url);

            if (!Storage::disk('public')->exists($path)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Archivo no encontrado'
                ], 404);
            }

            $filePath = Storage::disk('public')->path($path);
            $headers = [
                'Content-Type' => $this->getContentType(pathinfo($path, PATHINFO_EXTENSION)),
            ];

            return response()->file($filePath, $headers);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el comprobante',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar comprobante (mantener la transacción)
     */
    public function eliminarComprobante($idTransaccion)
    {
        try {
            DB::beginTransaction();

            $transaccion = TransaccionPago::findOrFail($idTransaccion);

            if ($transaccion->comprobante_url) {
                // Extraer el path del storage de la URL
                $path = str_replace('/storage/', '', $transaccion->comprobante_url);

                // Eliminar archivo físico
                if (Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                }

                // Actualizar transacción
                $transaccion->update([
                    'comprobante_url' => null,
                    'estado' => self::ESTADO_PENDIENTE,
                    'fecha_pago' => null,
                    'referencia' => null,
                ]);

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Comprobante eliminado exitosamente',
                    'data' => $transaccion->fresh()
                ], 200);
            }

            return response()->json([
                'success' => false,
                'message' => 'No hay comprobante para eliminar'
            ], 400);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el comprobante',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Helper para obtener content type
     */
    private function getContentType($fileExtension)
    {
        $contentTypes = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'pdf' => 'application/pdf',
        ];

        return $contentTypes[strtolower($fileExtension)] ?? 'application/octet-stream';
    }
}
