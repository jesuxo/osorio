<?php
// database/seeders/ChoferSeeder.php

namespace Database\Seeders;

use App\Models\Cwchofer;
use Illuminate\Database\Seeder;

class ChoferSeeder extends Seeder
{
    public function run(): void
    {
        $choferes = [
            [
                'nombre' => 'Juan',
                'apellido' => 'Pérez',
                'licencia' => 'LIC-001',
                'telefono' => '555-1234',
                'email' => 'juan.perez@email.com',
                'fecha_nacimiento' => '1985-05-15',
                'fecha_ingreso' => '2020-01-10',
                'tipo_sangre' => 'O+',
                'contacto_emergencia_nombre' => 'María Pérez',
                'contacto_emergencia_telefono' => '555-5678',
                'activo' => true,
            ],
            [
                'nombre' => 'Carlos',
                'apellido' => 'López',
                'licencia' => 'LIC-002',
                'telefono' => '555-4321',
                'email' => 'carlos.lopez@email.com',
                'fecha_nacimiento' => '1990-08-22',
                'fecha_ingreso' => '2021-03-15',
                'tipo_sangre' => 'A+',
                'contacto_emergencia_nombre' => 'Ana López',
                'contacto_emergencia_telefono' => '555-8765',
                'activo' => true,
            ],
            [
                'nombre' => 'Miguel',
                'apellido' => 'García',
                'licencia' => 'LIC-003',
                'telefono' => '555-9876',
                'email' => 'miguel.garcia@email.com',
                'fecha_nacimiento' => '1982-11-30',
                'fecha_ingreso' => '2019-06-20',
                'tipo_sangre' => 'B-',
                'contacto_emergencia_nombre' => 'Laura García',
                'contacto_emergencia_telefono' => '555-5432',
                'activo' => false,
            ],
        ];

        foreach ($choferes as $chofer) {
            Cwchofer::create($chofer);
        }
    }
}
