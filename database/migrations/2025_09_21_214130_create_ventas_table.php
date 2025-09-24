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
        Schema::create('ventas', function (Blueprint $table) {
            $table->id();
            $table->date("fecha_venta");
            $table->double("subtotal", 12, 2);
            $table->double("total", 12, 2);
            $table->double("descuento", 12, 2);
            $table->double("impuesto", 12, 2);
            $table->text("observacion")->nullable();
            $table->enum("estado", ["pendiente", "entregado", "cancelado"]);

            $table->unsignedBigInteger("id_cliente");
            $table->foreign("id_cliente")->references("id")->on("clientes")->onDelete("cascade");

            $table->unsignedBigInteger("id_trabajador");
            $table->foreign("id_trabajador")->references("id")->on("trabajadores")->onDelete("cascade");

            $table->timestamp("fecha_registro");
            $table->timestamp("fecha_actualizacion")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ventas');
    }
};
