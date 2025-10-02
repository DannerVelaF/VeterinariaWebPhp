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
        Schema::create('personas', function (Blueprint $table) {
            $table->id("id_persona")
                ->comment("Llave primaria de la tabla personas. Identificador único de cada persona.");

            $table->string("numero_documento", 20)
                ->comment("Número de documento de identidad. Ejemplos: DNI (8 dígitos), Pasaporte, Carnet de extranjería. Máx. 20 caracteres.");

            $table->string("nombre", 100)
                ->comment("Nombres de la persona. Máx. 100 caracteres.");

            $table->string("apellido_paterno", 100)
                ->comment("Apellido paterno de la persona. Máx. 100 caracteres.");

            $table->string("apellido_materno", 100)
                ->comment("Apellido materno de la persona. Máx. 100 caracteres.");

            $table->date("fecha_nacimiento")
                ->comment("Fecha de nacimiento de la persona en formato YYYY-MM-DD.");

            $table->enum("sexo", ["M", "F", "Otro"])
                ->comment("Sexo de la persona. Valores: 'M' (masculino), 'F' (femenino), 'Otro'.");

            $table->string("nacionalidad", 50)
                ->comment("Nacionalidad de la persona. Ejemplo: Peruana, Chilena. Máx. 50 caracteres.");

            $table->string("correo_electronico_personal", 150)
                ->unique()
                ->comment("Correo electrónico personal de la persona. Debe ser único. Máx. 150 caracteres.");

            $table->string("correo_electronico_secundario", 150)
                ->nullable()
                ->comment("Correo electrónico secundario de la persona. Campo opcional. Máx. 150 caracteres.");

            $table->string("numero_telefono_personal", 20)
                ->comment("Número de teléfono personal (ejemplo: celular). Máx. 20 caracteres.");

            $table->string("numero_telefono_secundario", 20)
                ->nullable()
                ->comment("Número de teléfono secundario de la persona. Campo opcional. Máx. 20 caracteres.");

            $table->unsignedBigInteger('id_direccion')
                ->comment("Llave foránea hacia la tabla direcciones. Relaciona a la persona con su dirección.");

            $table->foreign('id_direccion')
                ->references('id_direccion')
                ->on('direcciones')
                ->onUpdate('cascade')
                ->onDelete('restrict');

            $table->unsignedBigInteger("id_tipo_documento")
                ->nullable()
                ->comment("Llave foránea hacia la tabla tipo_documentos. Indica el tipo de documento asociado a la persona.");

            $table->foreign("id_tipo_documento")
                ->references("id_tipo_documento")
                ->on("tipo_documentos")
                ->onUpdate("cascade")
                ->onDelete("set null");

            $table->timestamp("fecha_registro")
                ->useCurrent()
                ->comment("Fecha de creación del registro. Se asigna automáticamente al insertar.");

            $table->timestamp("fecha_actualizacion")
                ->nullable()
                ->comment("Fecha de la última actualización/modificación del registro. Puede ser nula si nunca se actualizó.");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personas');
    }
};
