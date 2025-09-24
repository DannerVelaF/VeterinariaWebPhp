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
        Schema::create('lotes', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger("producto_id");
            $table->foreign("producto_id")->references("id")->on("productos");

            $table->string("codigo_lote");

            $table->decimal("cantidad_mostrada", 12, 2)->nullable();
            $table->decimal("cantidad_almacenada", 12, 2)->nullable();
            $table->decimal("cantidad_vendida", 12, 2)->nullable();

            $table->decimal("precio_compra", 12, 2)->nullable(); // Precio de compra al proveedor registrado

            $table->date("fecha_recepcion");
            $table->date("fecha_vencimiento")->nullable();

            $table->enum("estado", ["activo", "vendido", "devuelto"])->default("activo");
            $table->text("observacion")->nullable();

            $table->timestamp("fecha_registro");
            $table->timestamp("fecha_actualizacion")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lotes');
    }
};
