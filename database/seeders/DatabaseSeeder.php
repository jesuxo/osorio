<?php
// database/seeders/DatabaseSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            TipoGastoSeeder::class,
            CamionSeeder::class,
            ChoferSeeder::class,
            ViajeSeeder::class, // Este debe ir después de los anteriores
        ]);
    }
}
