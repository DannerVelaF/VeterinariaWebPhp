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
            $table->id("id_inventario_movimiento")
                ->comment("Llave primaria del movimiento de inventario. Identificador único.");

            /* $table->enum("tipo_movimiento", ["ajuste", "entrada", "salida"])
                ->default("entrada")
                ->comment("Tipo de movimiento de inventario. Valores: ajuste, entrada, salida."); */
            $table->unsignedBigInteger("id_tipo_movimiento")
                ->comment("Llave foránea al tipo de movimiento de inventario.");
            $table->foreign("id_tipo_movimiento")
                ->references("id_tipo_movimiento")
                ->on("tipo_movimientos")
                ->onDelete("restrict");

            $table->integer("cantidad_movimiento")
                ->comment("Cantidad de productos que entran o salen en este movimiento.");

            $table->integer("stock_resultante")
                ->comment("Stock resultante luego de aplicar este movimiento.");

            /* $table->enum("ubicacion", ["almacen", "mostrador"])
                ->default("almacen")
                ->comment("Ubicación del stock afectado. Valores: almacen o mostrador."); */

            $table->unsignedBigInteger("id_tipo_ubicacion")
                ->comment("Llave foránea a la ubicación del stock afectado.");
            $table->foreign("id_tipo_ubicacion")
                ->references("id_tipo_ubicacion")
                ->on("tipo_ubicacion")
                ->onDelete("restrict");
            
            $table->text("motivo")
                ->nullable()
                ->comment("Motivo o descripción del movimiento de inventario.");

            $table->unsignedBigInteger("id_lote")
                ->nullable()
                ->comment("Llave foránea al lote afectado por el movimiento.");
            $table->foreign("id_lote")
                ->references("id_lote")
                ->on("lotes")
                ->onDelete("set null");

            $table->unsignedBigInteger("id_trabajador")
                ->comment("Llave foránea al trabajador responsable del movimiento.");
            $table->foreign("id_trabajador")
                ->references("id_trabajador")
                ->on("trabajadores")
                ->onDelete("restrict");

            $table->string('tipo_movimiento_asociado', 50)
                ->nullable()
                ->comment("Tipo del movimiento asociado en caso de relaciones polimórficas o movimientos vinculados.");

            $table->unsignedBigInteger('id_movimiento_asociado')
                ->nullable()
                ->comment("ID del movimiento asociado en caso de relaciones polimórficas o movimientos vinculados.");

            $table->timestamp("fecha_movimiento")
                ->comment("Fecha y hora en que se realizó el movimiento.");

            $table->timestamp("fecha_registro")
                ->useCurrent()
                ->comment("Fecha de creación del registro del movimiento.");

            $table->timestamp("fecha_actualizacion")
                ->nullable()
                ->comment("Fecha de la última actualización del registro.");
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
