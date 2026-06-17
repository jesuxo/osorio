<?php
// database/seeders/CamionSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cwcamion;

class CamionSeeder extends Seeder
{
    public function run(): void
    {
        $camiones = [
            [
                'marca' => 'International',
                'modelo' => 'ProStar',
                'placa' => 'ABC-123',
                'capacidad_motos' => 8,
                'tipo' => 'propio',
                'activo' => true,
            ],
            [
                'marca' => 'Freightliner',
                'modelo' => 'Cascadia',
                'placa' => 'XYZ-789',
                'capacidad_motos' => 10,
                'tipo' => 'propio',
                'activo' => true,
            ],
            [
                'marca' => 'Kenworth',
                'modelo' => 'T680',
                'placa' => 'DEF-456',
                'capacidad_motos' => 6,
                'tipo' => 'alquilado',
                'costo_alquiler' => 1500.00,
                'activo' => true,
            ],
            [
                'marca' => 'Volvo',
                'modelo' => 'VNL 860',
                'placa' => 'GHI-789',
                'capacidad_motos' => 8,
                'tipo' => 'alquilado',
                'costo_alquiler' => 1800.00,
                'activo' => false,
            ],
        ];

        foreach ($camiones as $camion) {
            Cwcamion::create($camion);
        }
    }
}
