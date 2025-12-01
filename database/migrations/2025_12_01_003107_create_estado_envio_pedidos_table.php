<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('estado_envio_pedidos', function (Blueprint $table) {
            $table->id("id_estado_envio_pedido");
            $table->string("nombre_estado_envio_pedido", 40);
            $table->timestamp("fecha_registro")->useCurrent();
            $table->timestamp("fecha_actualizacion")->useCurrentOnUpdate()->nullable();
        });

        // Corregimos el nombre de la columna en el array
        $estados = ['pendiente', 'asignado', 'en_ruta', 'entregado', 'fallido'];

        foreach ($estados as $estado) {
            DB::table("estado_envio_pedidos")->insert([
                'nombre_estado_envio_pedido' => $estado,
                'fecha_registro' => now(),
                'fecha_actualizacion' => now()
            ]);
        }

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estado_envio_pedidos');
    }
};
