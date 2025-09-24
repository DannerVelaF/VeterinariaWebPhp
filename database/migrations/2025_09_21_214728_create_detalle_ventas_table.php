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
            $table->id();
            $table->integer("cantidad");
            $table->double("precio_unitario", 12, 2);
            $table->double("subtotal", 12, 2);
            $table->enum("tipo_item", ["producto", "servicio"]);

            $table->unsignedBigInteger("id_venta");
            $table->foreign("id_venta")->references("id")->on("ventas")->onDelete("cascade");

            $table->unsignedBigInteger("id_producto")->nullable();
            $table->foreign("id_producto")->references("id")->on("productos")->onDelete("cascade");

            $table->unsignedBigInteger("id_servicio")->nullable();
            $table->foreign("id_servicio")->references("id")->on("servicios")->onDelete("cascade");

            $table->timestamp("fecha_registro");
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
