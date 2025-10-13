<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LoginResponse extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id_persona" => $this->id_persona,
            "nombre" => $this->nombre,
            "apellido_paterno" => $this->apellido_paterno,
            "apellido_materno" => $this->apellido_materno,
            "correo" => $this->correo_electronico_personal,
            "usuario" => [
                "id_usuario" => $this->user->id_usuario,
                "usuario" => $this->user->usuario,
                "estado" => $this->user->estado,
            ],
        ];
    }
}
