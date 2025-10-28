<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('colores', function (Blueprint $table) {
            $table->id("id_color")->comment("Llave primaria de la tabla colores. Identificador único del color.");

            $table->string('nombre_color', 150)
                ->comment("Nombre del color.");

            $table->timestamp("fecha_registro")
                ->useCurrent()
                ->comment("Fecha de creación del registro. Se asigna automáticamente al insertar.");

            $table->timestamp("fecha_actualizacion")
                ->nullable()
                ->comment("Fecha de última actualización/modificación del registro. Puede ser nula si nunca se actualizó.");
        });

        // Insertar datos iniciales de colores
        DB::table('colores')->insert([
            [
                'nombre_color' => 'Marrón',
                'fecha_registro' => now(),
                'fecha_actualizacion' => now()
            ],
            [
                'nombre_color' => 'Blanco',
                'fecha_registro' => now(),
                'fecha_actualizacion' => now()
            ],
            [
                'nombre_color' => 'Negro',
                'fecha_registro' => now(),
                'fecha_actualizacion' => now()
            ],
            [
                'nombre_color' => 'Beige',
                'fecha_registro' => now(),
                'fecha_actualizacion' => now()
            ],
            [
                'nombre_color' => 'Gris',
                'fecha_registro' => now(),
                'fecha_actualizacion' => now()
            ],
            [
                'nombre_color' => 'Crema',
                'fecha_registro' => now(),
                'fecha_actualizacion' => now()
            ],
            [
                'nombre_color' => 'Amarillo',
                'fecha_registro' => now(),
                'fecha_actualizacion' => now()
            ],
            [
                'nombre_color' => 'Dorado',
                'fecha_registro' => now(),
                'fecha_actualizacion' => now()
            ],
            [
                'nombre_color' => 'Naranja',
                'fecha_registro' => now(),
                'fecha_actualizacion' => now()
            ],
            [
                'nombre_color' => 'Rojo',
                'fecha_registro' => now(),
                'fecha_actualizacion' => now()
            ],
            [
                'nombre_color' => 'Atigrado',
                'fecha_registro' => now(),
                'fecha_actualizacion' => now()
            ],
            [
                'nombre_color' => 'Manchado',
                'fecha_registro' => now(),
                'fecha_actualizacion' => now()
            ],
            [
                'nombre_color' => 'Merlé',
                'fecha_registro' => now(),
                'fecha_actualizacion' => now()
            ],
            [
                'nombre_color' => 'Azul',
                'fecha_registro' => now(),
                'fecha_actualizacion' => now()
            ],
            [
                'nombre_color' => 'Plateado',
                'fecha_registro' => now(),
                'fecha_actualizacion' => now()
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('colores');
    }
};
