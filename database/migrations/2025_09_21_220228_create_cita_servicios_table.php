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
        Schema::create('cita_servicios', function (Blueprint $table) {
            $table->id("id_cita_servicio")
                ->comment("Llave primaria. Identificador único del registro de servicio aplicado a una cita.");

            $table->unsignedBigInteger("id_cita")
                ->comment("Llave foránea hacia la tabla citas. Indica la cita asociada.");
            $table->foreign("id_cita")
                ->references("id_cita")
                ->on("citas")
                ->onDelete("cascade");

            $table->unsignedBigInteger("id_servicio")
                ->comment("Llave foránea hacia la tabla servicios. Indica el servicio aplicado.");
            $table->foreign("id_servicio")
                ->references("id_servicio")
                ->on("servicios")
                ->onDelete("restrict");

            $table->decimal("precio_aplicado", 12, 2)
                ->comment("Precio unitario aplicado del servicio en esta cita.");

            $table->integer("cantidad")
                ->comment("Cantidad de veces que se aplicó el servicio en la cita.");

            $table->text("diagnostico")
                ->nullable()
                ->comment("Diagnóstico realizado durante la cita. Campo opcional.");

            $table->text("medicamentos")
                ->nullable()
                ->comment("Medicamentos recetados o aplicados durante la cita. Campo opcional.");

            $table->text("recomendaciones")
                ->nullable()
                ->comment("Recomendaciones dadas al cliente sobre la mascota. Campo opcional.");

            $table->timestamp("fecha_registro")
                ->useCurrent()
                ->comment("Fecha de creación del registro del servicio de la cita.");

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
        Schema::dropIfExists('cita_servicios');
    }
};
