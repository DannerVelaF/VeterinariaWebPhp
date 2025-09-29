<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('detalle_ventas', function (Blueprint $table) {
            $table->id("id_detalle_venta");
            $table->integer("cantidad");
            $table->decimal("precio_unitario", 12, 2);
            $table->decimal("subtotal", 12, 2);
            $table->enum("tipo_item", ["producto", "servicio"]);
            $table->string('motivo_salida')->nullable();

            $table->unsignedBigInteger("id_venta");
            $table->foreign("id_venta")->references("id_venta")->on("ventas")->onDelete("cascade");

            $table->unsignedBigInteger("id_producto")->nullable();
            $table->foreign("id_producto")->references("id_producto")->on("productos")->onDelete("cascade");

            $table->unsignedBigInteger("id_servicio")->nullable();
            $table->foreign("id_servicio")->references("id_servicio")->on("servicios")->onDelete("cascade");

            $table->timestamp("fecha_registro")->useCurrent();
            $table->timestamp("fecha_actualizacion")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalle_ventas');
    }
};
