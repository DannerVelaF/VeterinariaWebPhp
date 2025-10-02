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
        Schema::create('ubigeos', function (Blueprint $table) {
            $table->string('codigo_ubigeo', 6)
                ->primary()
                ->comment("Código único del ubigeo según INEI (6 dígitos: 2 para departamento, 2 para provincia, 2 para distrito).");

            $table->string('departamento', 100)
                ->comment("Nombre del departamento al que pertenece el ubigeo. Máx. 100 caracteres.");

            $table->string('provincia', 100)
                ->comment("Nombre de la provincia al que pertenece el ubigeo. Máx. 100 caracteres.");

            $table->string('distrito', 100)
                ->comment("Nombre del distrito correspondiente al ubigeo. Máx. 100 caracteres.");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ubigeos');
    }
};
