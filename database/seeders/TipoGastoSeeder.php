<?php
// database/seeders/TipoGastoSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cwtipogasto;

class TipoGastoSeeder extends Seeder
{
    public function run(): void
    {
        $tipos = [
            ['nombre' => 'Combustible', 'categoria' => 'Operativo', 'descripcion' => 'Gasolina, diésel, etc.'],
            ['nombre' => 'Peaje', 'categoria' => 'Operativo', 'descripcion' => 'Casetas de cobro en carreteras'],
            ['nombre' => 'Hospedaje', 'categoria' => 'Chofer', 'descripcion' => 'Hoteles para el chofer durante el viaje'],
            ['nombre' => 'Comida', 'categoria' => 'Chofer', 'descripcion' => 'Alimentación del chofer'],
            ['nombre' => 'Mantenimiento', 'categoria' => 'Operativo', 'descripcion' => 'Reparaciones y mantenimiento del camión'],
            ['nombre' => 'Alquiler Camión', 'categoria' => 'Fijo', 'descripcion' => 'Pago por alquiler de camiones no propios'],
            ['nombre' => 'Lavado', 'categoria' => 'Operativo', 'descripcion' => 'Lavado y limpieza del camión'],
            ['nombre' => 'Llanta', 'categoria' => 'Operativo', 'descripcion' => 'Compra o reparación de llantas'],
            ['nombre' => 'Seguro', 'categoria' => 'Fijo', 'descripcion' => 'Pólizas de seguro del camión'],
            ['nombre' => 'Otros', 'categoria' => 'General', 'descripcion' => 'Gastos varios no categorizados'],
        ];

        foreach ($tipos as $tipo) {
            Cwtipogasto::create($tipo);
        }
    }
}
