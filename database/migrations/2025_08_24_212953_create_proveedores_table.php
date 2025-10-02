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
        Schema::create('proveedores', function (Blueprint $table) {
            $table->id("id_proveedor")
                ->comment("Llave primaria de la tabla proveedores. Identificador único del proveedor.");

            $table->string('nombre_proveedor', 150)
                ->comment("Razón social o nombre comercial del proveedor. Máx. 150 caracteres.");

            $table->string('ruc', 11)
                ->unique()
                ->comment("Número de RUC del proveedor (11 dígitos únicos).");

            $table->string('correo_electronico_empresa', 150)
                ->nullable()
                ->comment("Correo electrónico principal de la empresa proveedora. Campo opcional. Máx. 150 caracteres.");

            $table->string("telefono_contacto", 20)
                ->nullable()
                ->comment("Teléfono principal de contacto del proveedor. Campo opcional. Máx. 20 caracteres.");

            $table->string('pais', 100)
                ->nullable()
                ->comment("País de origen del proveedor. Campo opcional. Máx. 100 caracteres.");

            $table->enum('estado', ['activo', 'inactivo'])
                ->default('activo')
                ->comment("Estado actual del proveedor. Valores permitidos: 'activo', 'inactivo'.");

            $table->string("telefono_secundario", 20)
                ->nullable()
                ->comment("Teléfono secundario de contacto. Campo opcional. Máx. 20 caracteres.");

            $table->string("correo_electronico_encargado", 150)
                ->nullable()
                ->comment("Correo electrónico del encargado o persona de contacto del proveedor. Campo opcional. Máx. 150 caracteres.");

            $table->unsignedBigInteger("id_direccion")
                ->nullable()
                ->comment("Llave foránea hacia la tabla direcciones. Relaciona al proveedor con su dirección física.");

            $table->foreign('id_direccion')
                ->references('id_direccion')
                ->on('direcciones')
                ->onUpdate('cascade')
                ->onDelete('set null');

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
        Schema::dropIfExists('proveedores');
    }
};
