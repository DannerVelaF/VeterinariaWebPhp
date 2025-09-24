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
        Schema::create('detalle_compras', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger("id_compra");
            $table->foreign("id_compra")->references("id")->on("compras");

            $table->unsignedBigInteger('id_producto');
            $table->foreign("id_producto")->references("id")->on("productos");
            $table->enum("estado", ["pendiente", "recibido", "pagado", "cancelado"])->default("pendiente");
            $table->decimal("cantidad", 12, 2);
            $table->decimal("precio_unitario", 12, 2);
            $table->decimal("sub_total", 12, 2);

            $table->timestamp("fecha_registro");
            $table->timestamp("fecha_actualizacion")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalle_compras');
    }
};
