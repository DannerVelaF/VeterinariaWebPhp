<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cajas', function (Blueprint $table) {
            $table->id('id_caja')->comment('Identificador único de la caja');
            $table->foreignId('id_trabajador')->constrained('trabajadores', 'id_trabajador')->comment('ID del trabajador responsable de la caja');
            $table->decimal('monto_inicial', 10, 2)->comment('Monto inicial con el que se abre la caja');
            $table->decimal('monto_final', 10, 2)->nullable(false)->comment('Monto final al cerrar la caja');
            $table->decimal('ventas_efectivo', 10, 2)->default(0)->comment('Total de ventas realizadas en efectivo');
            $table->decimal('ventas_tarjeta', 10, 2)->default(0)->comment('Total de ventas realizadas con tarjeta');
            $table->decimal('ventas_transferencia', 10, 2)->default(0)->comment('Total de ventas realizadas por transferencia');
            $table->decimal('total_ventas', 10, 2)->default(0)->comment('Suma total de todas las ventas');
            $table->decimal('diferencia', 10, 2)->default(0)->comment('Diferencia entre el monto teórico y el real');
            $table->enum('estado', ['abierta', 'cerrada'])->default('abierta')->comment('Estado actual de la caja: abierta o cerrada');
            $table->text('observaciones')->nullable()->comment('Observaciones y comentarios sobre el cierre de caja');
            $table->timestamp('fecha_apertura')->useCurrent()->comment('Fecha y hora de apertura de la caja');
            $table->timestamp('fecha_cierre')->nullable()->comment('Fecha y hora de cierre de la caja');
            $table->timestamp("fecha_registro")->useCurrent()->comment('Fecha de creación del registro en el sistema');
            $table->timestamp("fecha_actualizacion")
                ->nullable()
                ->comment('Fecha de la última actualización del registro');
        });
    }

    /**
     * Reverse the migrations.
     * //         */
    public function down(): void
    {
        Schema::dropIfExists('cajas');
    }
};
