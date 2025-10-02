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
        Schema::create('compras', function (Blueprint $table) {
            $table->id("id_compra")
                ->comment("Llave primaria. Identificador único de la compra.");

            // Proveedor
            $table->unsignedBigInteger("id_proveedor")
                ->comment("Llave foránea hacia el proveedor de la compra.");
            $table->foreign("id_proveedor")
                ->references("id_proveedor")
                ->on("proveedores")
                ->onDelete("restrict");

            // Trabajador responsable
            $table->unsignedBigInteger('id_trabajador')
                ->comment("Llave foránea al trabajador que registró la compra.");
            $table->foreign("id_trabajador")
                ->references("id_trabajador")
                ->on("trabajadores")
                ->onDelete("restrict");

            $table->string("codigo", 50)
                ->unique()
                ->comment("Código interno único de la compra. Máximo 50 caracteres.");

            $table->string("numero_factura", 50)
                ->comment("Número de factura proporcionado por el proveedor. Máximo 50 caracteres.");

            $table->date("fecha_compra")
                ->comment("Fecha en que se realizó la compra.");

            $table->decimal("cantidad_total", 12, 2)
                ->comment("Cantidad total de productos comprados en esta compra.");

            $table->decimal('total', 12, 2)
                ->comment("Monto total de la compra en moneda local.");

            $table->text("observacion")
                ->nullable()
                ->comment("Observaciones adicionales sobre la compra. Campo opcional.");

            $table->enum("estado", ["pendiente", "aprobado", "recibido",  "pagado", "cancelado"])
                ->default("pendiente")
                ->comment("Estado de la compra. Valores permitidos: pendiente, aprobado, recibido, pagado, cancelado.");

            $table->unsignedBigInteger("id_usuario_aprobador")
                ->nullable()
                ->comment("Usuario que aprobó la compra. Puede ser nulo si aún no ha sido aprobada.");
            $table->foreign("id_usuario_aprobador")
                ->references("id_usuario")
                ->on("usuarios")
                ->onDelete("set null");

            $table->timestamp("fecha_registro")
                ->useCurrent()
                ->comment("Fecha de creación del registro de compra.");

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
        Schema::dropIfExists('compras');
    }
};
