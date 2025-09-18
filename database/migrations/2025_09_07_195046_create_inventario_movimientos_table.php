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
        Schema::create('inventario_movimientos', function (Blueprint $table) {
            $table->id();
            $table->enum("tipo_movimiento", ["ajuste", "entrada", "salida"])->default("entrada");
            $table->integer("cantidad_movimiento");
            $table->integer("stock_resultante");
            $table->timestamp("fecha_movimiento");
            $table->timestamp("fecha_registro");
            $table->enum("ubicacion", ["almacen", "mostrador"])->default("almacen");
            $table->unsignedBigInteger("id_lote")->nullable();
            $table->foreign("id_lote")->references("id")->on("lotes");

            $table->unsignedBigInteger("id_trabajador");
            $table->foreign("id_trabajador")->references("id")->on("trabajadores");

            $table->morphs('movimentable');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventario_movimientos');
    }
};
