<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductoResponse extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id_producto' => $this->id_producto,
            'nombre_producto' => $this->nombre_producto,
            'descripcion' => $this->descripcion,
            'precio_unitario' => (float) $this->precio_unitario,
            'ruta_imagen' => $this->ruta_imagen,
            'codigo_barras' => $this->codigo_barras,
            'stock_actual' => $this->stock_actual, // 🔹 INCLUIR EL ACCESSOR
            'categoria_producto' => $this->categoria_producto ? [
                'id_categoria_producto' => $this->categoria_producto->id_categoria_producto,
                'nombre_categoria_producto' => $this->categoria_producto->nombre_categoria_producto
            ] : null,
            'unidad' => $this->unidad ? [
                'id_unidad' => $this->unidad->id_unidad,
                'nombre_unidad' => $this->unidad->nombre_unidad
            ] : null,
            'fecha_registro' => $this->fecha_registro,
            'fecha_actualizacion' => $this->fecha_actualizacion
        ];
    }
}
