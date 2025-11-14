<?php
namespace App\Traits;

trait ApiResponse
{
    /**
     * Respuesta exitosa
     */
    protected function successResponse($data = null, string $message = '', int $code = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data
        ], $code);
    }

    /**
     * Respuesta de error
     */
    protected function errorResponse(string $message = '', $errors = null, int $code = 400)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors
        ], $code);
    }

    /**
     * Respuesta de recurso no encontrado
     */
    protected function notFoundResponse(string $message = 'Recurso no encontrado')
    {
        return $this->errorResponse($message, null, 404);
    }

    /**
     * Respuesta de validación fallida
     */
    protected function validationErrorResponse($errors)
    {
        return $this->errorResponse('Error de validación', $errors, 422);
    }

    /**
     * Respuesta de servidor interno
     */
    protected function serverErrorResponse(string $message = 'Error interno del servidor')
    {
        return $this->errorResponse($message, null, 500);
    }

    /**
     * Respuesta de no autorizado
     */
    protected function unauthorizedResponse(string $message = 'No autorizado')
    {
        return $this->errorResponse($message, null, 401);
    }

    /**
     * Respuesta de prohibido
     */
    protected function forbiddenResponse(string $message = 'Prohibido')
    {
        return $this->errorResponse($message, null, 403);
    }
}
