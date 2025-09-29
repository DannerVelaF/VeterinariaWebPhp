<?php

namespace Database\Seeders;

use App\Models\Direccion;
use App\Models\EstadoTrabajadores;
use App\Models\Persona;
use App\Models\PuestoTrabajador;
use App\Models\Tipo_documento;
use App\Models\Trabajador;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $estados = [
            ['nombre' => 'Activo'],
            ['nombre' => 'Inactivo'],
            ['nombre' => 'Vacaciones'],
            ['nombre' => 'Licencia'],
            ['nombre' => 'Suspendido'],
        ];

        foreach ($estados as $estado) {
            EstadoTrabajadores::firstOrCreate($estado);
        }


        $ubigeo = \App\Models\Ubigeo::create([
            'codigo_ubigeo' => '150101',
            'departamento' => 'Lima',
            'provincia' => 'Lima',
            'distrito' => 'Lima',
        ]);

        PuestoTrabajador::create([
            'nombre' => 'Puesto 1',
        ]);

        // Crear un tipo de documento
        $tipoDocumento = Tipo_documento::create([
            'nombre' => 'DNI',
        ]);

        // Crear dirección de ejemplo (requerida por la FK)
        $direccion = Direccion::create([
            'zona' => 'Centro',
            'tipo_calle' => 'Av.',
            'nombre_calle' => 'Siempre Viva',
            'numero' => '742',
            'codigo_postal' => '15001',
            'referencia' => 'Cerca al parque',
            'codigo_ubigeo' => $ubigeo->codigo_ubigeo, // ahora sí existe
        ]);

        // Crear persona
        $persona = Persona::create([
            "numero_documento" => 12345678,
            'nombre' => 'Juan',
            'apellido_paterno' => 'Pérez',
            'apellido_materno' => 'Gómez',
            'fecha_nacimiento' => '1990-01-01',
            'sexo' => 'M', // en minúsculas porque tu enum lo define así
            'nacionalidad' => 'Peruana',
            "correo_electronico_personal" => "alejandrovela09@gmail.com",
            "correo_electronico_secundario" => null,
            "numero_telefono_personal" => "999888777",
            "numero_telefono_secundario" => null,
            'id_tipo_documento' => $tipoDocumento->id_tipo_documento,
            'id_direccion' => $direccion->id_direccion,
        ]);

        $trabajo = Trabajador::create([
            "id_persona" => $persona->id_persona,
            "id_puesto_trabajo" => 1,
            "id_estado_trabajador" => 1,
            "fecha_ingreso" => "2023-01-01",
            "numero_seguro_social" => "123456789",
            "salario" => 2500.00,
        ]);

        // Crear usuario vinculado a persona
        User::create([
            'usuario' => 'user',
            'contrasena' =>  Hash::make('user'),
            "estado" => "activo",
            "id_persona" => $persona->id_persona,
        ]);
    }
}
