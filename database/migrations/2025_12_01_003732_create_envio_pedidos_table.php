<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('envio_pedidos', function (Blueprint $table) {
            $table->id("id_envio_pedido")->comment("Llave primaria");

            $table->unsignedBigInteger("id_venta")->comment("FK Ventas");
            $table->foreign("id_venta")->references("id_venta")->on("ventas")->onDelete("cascade");

            $table->unsignedBigInteger("id_direccion")->comment("FK Direcciones");
            $table->foreign("id_direccion")->references("id_direccion")->on("direcciones")->onDelete("cascade");

            $table->unsignedBigInteger("id_estado_envio_pedido")->comment("FK Estado");
            $table->foreign("id_estado_envio_pedido")->references("id_estado_envio_pedido")->on("estado_envio_pedidos")->onDelete("cascade");

            $table->unsignedBigInteger("id_trabajador")->nullable()->comment("Transportista asignado");
            $table->foreign("id_trabajador")->references("id_trabajador")->on("trabajadores")->onDelete("set null");

            $table->dateTime('fecha_programada')->nullable();
            $table->dateTime('fecha_entrega_real')->nullable();
            $table->string('foto_evidencia')->nullable();
            $table->text('observaciones_entrega')->nullable();

            $table->timestamp("fecha_registro")->useCurrent();
            $table->timestamp("fecha_actualizacion")->useCurrentOnUpdate()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('envio_pedidos');
    }
};
