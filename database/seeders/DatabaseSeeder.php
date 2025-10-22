<?php

namespace Database\Seeders;

use App\Livewire\Configuracion\Modulos;
use App\Models\Direccion;
use App\Models\EstadoTrabajadores;
use App\Models\Persona;
use App\Models\PuestoTrabajador;
use App\Models\Tipo_documento;
use App\Models\Trabajador;
use App\Models\User;
use App\Models\Clientes;
use App\Models\modulo;
use App\Models\Modulo_opcion;
use App\Models\Modulo_roles;
use App\Models\Roles;
use App\Models\Roles_permisos;
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
            ['nombre_estado_trabajador' => 'Activo'],
            ['nombre_estado_trabajador' => 'Inactivo'],
            ['nombre_estado_trabajador' => 'Vacaciones'],
            ['nombre_estado_trabajador' => 'Licencia'],
            ['nombre_estado_trabajador' => 'Suspendido'],
        ];

        foreach ($estados as $estado) {
            EstadoTrabajadores::firstOrCreate($estado);
        }


        $ubigeo = \App\Models\Ubigeo::firstOrCreate(
            ['codigo_ubigeo' => '150101'], // campos únicos
            [ // valores por defecto
                'departamento' => 'Lima',
                'provincia' => 'Lima',
                'distrito' => 'Lima',
            ]
        );

        PuestoTrabajador::create([
            'nombre_puesto' => 'Puesto 1',
        ]);

        // Crear un tipo de documento
        $tipoDocumento = Tipo_documento::firstOrCreate(
            ['nombre_tipo_documento' => 'DNI'],
            [
                'fecha_registro' => now(),
                'fecha_actualizacion' => now(),
            ]
        );

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
            "numero_documento" => 12345655,
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

        $rol =  Roles::create([
            'nombre_rol' => 'Administrador',
            'fecha_registro' => now(),
            'fecha_actualizacion' => now(),
            'estado' => 'activo',
        ]);

        // Crear usuario vinculado a persona
        User::create([
            'usuario' => 'danner',
            'contrasena' =>  Hash::make('danner123456'),
            "estado" => "activo",
            "id_persona" => $persona->id_persona,
            'id_rol' => $rol->id_rol,
        ]);

        // Cliente 1
        $direccion1 = Direccion::create([
            'zona' => 'Norte',
            'tipo_calle' => 'Calle',
            'nombre_calle' => 'Los Olivos',
            'numero' => '120',
            'codigo_postal' => '15002',
            'referencia' => 'Frente al mercado',
            'codigo_ubigeo' => $ubigeo->codigo_ubigeo,
        ]);

        $persona1 = Persona::create([
            "numero_documento" => 76543210,
            'nombre' => 'María',
            'apellido_paterno' => 'López',
            'apellido_materno' => 'Ramírez',
            'fecha_nacimiento' => '1992-03-12',
            'sexo' => 'F',
            'nacionalidad' => 'Peruana',
            "correo_electronico_personal" => "maria.lopez@example.com",
            "numero_telefono_personal" => "987654321",
            'id_tipo_documento' => $tipoDocumento->id_tipo_documento,
            'id_direccion' => $direccion1->id_direccion,
        ]);

        Clientes::create([
            "id_persona" => $persona1->id_persona,
            "fecha_registro" => now(),
            "fecha_actualizacion" => now(),
        ]);

        // Cliente 2
        $direccion2 = Direccion::create([
            'zona' => 'Sur',
            'tipo_calle' => 'Jr.',
            'nombre_calle' => 'San Martín',
            'numero' => '456',
            'codigo_postal' => '15003',
            'referencia' => 'Cerca al colegio',
            'codigo_ubigeo' => $ubigeo->codigo_ubigeo,
        ]);

        $persona2 = Persona::create([
            "numero_documento" => 99887766,
            'nombre' => 'Carlos',
            'apellido_paterno' => 'Torres',
            'apellido_materno' => 'Fernández',
            'fecha_nacimiento' => '1988-09-22',
            'sexo' => 'M',
            'nacionalidad' => 'Peruana',
            "correo_electronico_personal" => "carlos.torres@example.com",
            "numero_telefono_personal" => "956123789",
            'id_tipo_documento' => $tipoDocumento->id_tipo_documento,
            'id_direccion' => $direccion2->id_direccion,
        ]);

        Clientes::create([
            "id_persona" => $persona2->id_persona,
            "fecha_registro" => now(),
            "fecha_actualizacion" => now(),
        ]);

        // Cliente 3
        $direccion3 = Direccion::create([
            'zona' => 'Este',
            'tipo_calle' => 'Av.',
            'nombre_calle' => 'La Paz',
            'numero' => '999',
            'codigo_postal' => '15004',
            'referencia' => 'Al costado del hospital',
            'codigo_ubigeo' => $ubigeo->codigo_ubigeo,
        ]);

        $persona3 = Persona::create([
            "numero_documento" => 55667788,
            'nombre' => 'Lucía',
            'apellido_paterno' => 'García',
            'apellido_materno' => 'Vega',
            'fecha_nacimiento' => '1995-07-15',
            'sexo' => 'F',
            'nacionalidad' => 'Peruana',
            "correo_electronico_personal" => "lucia.garcia@example.com",
            "numero_telefono_personal" => "944333222",
            'id_tipo_documento' => $tipoDocumento->id_tipo_documento,
            'id_direccion' => $direccion3->id_direccion,
        ]);

        Clientes::create([
            "id_persona" => $persona3->id_persona,
            "fecha_registro" => now(),
            "fecha_actualizacion" => now(),
        ]);

        $permisos = collect([
            "crear-usuarios",
            "crear-roles",
            "crear-permisos",
            "crear-modulos",
            "crear-modulo-opciones",
        ]);

        foreach ($permisos as $permisoNombre) {
            $permiso =  \App\Models\Permiso::create([
                'nombre_permiso' => $permisoNombre,
                'fecha_registro' => now(),
                'fecha_actualizacion' => now(),
                'estado' => 'activo',
            ]);

            Roles_permisos::create([
                'id_rol' => $rol->id_rol,
                'id_permiso' => $permiso->id_permiso,
                'fecha_registro' => now(),
                'fecha_actualizacion' => now(),
            ]);
        }


        $modulo =  modulo::create([
            'nombre_modulo' => 'Configuración',
            'fecha_registro' => now(),
            'fecha_actualizacion' => now(),
            'estado' => 'activo',
            'id_usuario_registro' => 1,
        ]);

        $Modulo_opcion =  Modulo_opcion::create([
            'nombre_opcion' => 'Crear modulos',
            'ruta_laravel' => 'configuracion.modulos',
            'orden' => 1,
            'id_modulo' => $modulo->id_modulo,
            'id_permiso' => '4',
            'id_opcion_padre' => null,
            'estado' => 'activo',
            'fecha_registro' => now(),
            'fecha_actualizacion' => now(),
            'id_usuario_registro' => 1,
        ]);

        $Modulo_opcion =  Modulo_opcion::create([
            'nombre_opcion' => 'Crear Opciones de Módulos',
            'ruta_laravel' => 'configuracion.opciones',
            'orden' => 1,
            'id_modulo' => $modulo->id_modulo,
            'id_permiso' => '5',
            'id_opcion_padre' => null,
            'estado' => 'activo',
            'fecha_registro' => now(),
            'fecha_actualizacion' => now(),
            'id_usuario_registro' => 1,
        ]);

        $Modulo_roles = Modulo_roles::create([
            "id_modulo" => $modulo->id_modulo,
            "id_rol" => $rol->id_rol,
            "fecha_registro" => now(),
            "fecha_actualizacion" => now(),
        ]);
    }
}
