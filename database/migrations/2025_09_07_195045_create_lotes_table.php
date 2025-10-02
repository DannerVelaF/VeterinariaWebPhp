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
            $table->id("id_lote")
                ->comment("Llave primaria. Identificador único del lote de productos.");

            $table->unsignedBigInteger("id_producto")
                ->comment("Llave foránea hacia la tabla productos. Indica a qué producto pertenece el lote.");
            $table->foreign("id_producto")
                ->references("id_producto")
                ->on("productos")
                ->onDelete("restrict");

            $table->string("codigo_lote", 50)
                ->unique()
                ->comment("Código único del lote. Máximo 50 caracteres.");

            $table->decimal("cantidad_mostrada", 12, 2)
                ->nullable()
                ->comment("Cantidad del lote que se muestra en la vitrina o área de venta. Puede ser nula.");

            $table->decimal("cantidad_almacenada", 12, 2)
                ->nullable()
                ->comment("Cantidad del lote que se almacena en el almacén. Puede ser nula.");

            $table->decimal("cantidad_vendida", 12, 2)
                ->nullable()
                ->comment("Cantidad del lote que ha sido vendida. Puede ser nula.");

            $table->decimal("precio_compra", 12, 2)
                ->nullable()
                ->comment("Precio de compra al proveedor registrado. Puede ser nulo si no aplica.");

            $table->date("fecha_recepcion")
                ->comment("Fecha en que se recibió el lote del proveedor.");

            $table->date("fecha_vencimiento")
                ->nullable()
                ->comment("Fecha de vencimiento del lote, si aplica. Puede ser nula.");

            $table->enum("estado", ["activo", "vendido", "devuelto"])
                ->default("activo")
                ->comment("Estado actual del lote. Valores: activo, vendido, devuelto.");

            $table->text("observacion")
                ->nullable()
                ->comment("Observaciones adicionales del lote. Campo opcional.");

            $table->timestamp("fecha_registro")
                ->useCurrent()
                ->comment("Fecha de creación del registro del lote.");

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
        Schema::dropIfExists('lotes');
    }
};
