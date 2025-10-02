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
        Schema::create('trabajadores', function (Blueprint $table) {
            $table->id("id_trabajador")
                ->comment("Llave primaria. Identificador único del trabajador en la empresa.");

            $table->date("fecha_ingreso")
                ->comment("Fecha en la que el trabajador ingresó a la empresa.");

            $table->date("fecha_salida")
                ->nullable()
                ->comment("Fecha en la que el trabajador salió de la empresa, si corresponde.");

            $table->decimal("salario", 12, 2)
                ->comment("Salario bruto del trabajador con precisión de hasta 12 dígitos, 2 decimales.");

            $table->string('numero_seguro_social', 50)
                ->unique()
                ->comment("Número de seguro social del trabajador. Máximo 50 caracteres, único por persona.");

            // Relación con personas
            $table->unsignedBigInteger("id_persona")
                ->comment("Llave foránea que referencia a la persona asociada a este trabajador.");
            $table->foreign("id_persona")
                ->references("id_persona")
                ->on('personas')
                ->onDelete("cascade");

            // Relación con puesto de trabajo
            $table->unsignedBigInteger("id_puesto_trabajo")
                ->comment("Llave foránea que referencia al puesto que ocupa el trabajador.");
            $table->foreign("id_puesto_trabajo")
                ->references("id_puesto_trabajo")
                ->on('puesto_trabajadores')
                ->onDelete("restrict");

            // Relación con estado de trabajador
            $table->unsignedBigInteger('id_estado_trabajador')
                ->comment("Llave foránea que referencia al estado actual del trabajador.");
            $table->foreign("id_estado_trabajador")
                ->references("id_estado_trabajador")
                ->on("estado_trabajadores")
                ->onDelete("restrict");

            $table->timestamp("fecha_registro")
                ->useCurrent()
                ->comment("Fecha en que se creó el registro del trabajador.");

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
        Schema::dropIfExists('trabajadores');
    }
};
