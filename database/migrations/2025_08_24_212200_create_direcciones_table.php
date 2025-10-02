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
        Schema::create('direcciones', function (Blueprint $table) {
            $table->id("id_direccion")
                ->comment("Llave primaria de la tabla direcciones. Identificador único de la dirección.");

            $table->string('zona', 100)
                ->nullable()
                ->comment("Zona, sector, barrio o urbanización. Campo opcional. Máx. 100 caracteres.");

            $table->string('tipo_calle', 50)
                ->nullable()
                ->comment("Tipo de vía (ejemplo: Calle, Avenida, Jirón, Pasaje). Campo opcional. Máx. 50 caracteres.");

            $table->string('nombre_calle', 150)
                ->comment("Nombre de la vía principal. Ejemplo: Av. La Marina. Máx. 150 caracteres.");

            $table->string('numero', 20)
                ->nullable()
                ->comment("Número de la dirección. Ejemplo: 123, S/N. Campo opcional. Máx. 20 caracteres.");

            $table->string('codigo_postal', 10)
                ->nullable()
                ->comment("Código postal asociado a la dirección. Campo opcional. Máx. 10 caracteres.");

            $table->string('referencia', 200)
                ->nullable()
                ->comment("Referencia adicional para ubicar la dirección (ejemplo: cerca al parque principal). Campo opcional. Máx. 200 caracteres.");

            $table->string('codigo_ubigeo', 6)
                ->comment("Código único del ubigeo (INEI). Relaciona la dirección con su ubicación geográfica (departamento, provincia, distrito).");

            $table->foreign('codigo_ubigeo')
                ->references('codigo_ubigeo')
                ->on('ubigeos')
                ->onUpdate('cascade')
                ->onDelete('restrict');

            $table->timestamp("fecha_registro")
                ->useCurrent()
                ->comment("Fecha de creación del registro. Se asigna automáticamente al insertar.");

            $table->timestamp("fecha_actualizacion")
                ->nullable()
                ->comment("Fecha de última actualización/modificación del registro. Puede ser nula si nunca se actualizó.");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('direcciones');
    }
};
