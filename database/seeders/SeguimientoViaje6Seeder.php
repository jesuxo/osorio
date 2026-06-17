<?php
// database/seeders/SeguimientoViaje6Seeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CwseguimientoViaje;
use Carbon\Carbon;

class SeguimientoViaje6Seeder extends Seeder
{
    public function run(): void
    {
        $viajeId = 6; // ID del viaje

        // Ruta: CDMX a Monterrey
        $ubicaciones = [
            // Salida de CDMX
            [
                'latitud' => 19.4326,
                'longitud' => -99.1332,
                'ubicacion_texto' => 'Centro de CDMX',
                'velocidad' => 0,
                'kilometraje_total' => 0,
                'nivel_combustible' => 100,
                'temperatura_motor' => 85,
                'estado_motor' => 'encendido',
                'fecha_hora' => Carbon::now()->subHours(48)->setTime(8, 0, 0),
            ],
            // Salida de la ciudad
            [
                'latitud' => 19.5362,
                'longitud' => -99.1974,
                'ubicacion_texto' => 'Periférico Norte, CDMX',
                'velocidad' => 60,
                'kilometraje_total' => 15,
                'nivel_combustible' => 98,
                'temperatura_motor' => 87,
                'estado_motor' => 'encendido',
                'fecha_hora' => Carbon::now()->subHours(48)->setTime(9, 30, 0),
            ],
            // Llegada a Tepotzotlán
            [
                'latitud' => 19.7137,
                'longitud' => -99.2232,
                'ubicacion_texto' => 'Tepotzotlán, Estado de México',
                'velocidad' => 80,
                'kilometraje_total' => 45,
                'nivel_combustible' => 95,
                'temperatura_motor' => 88,
                'estado_motor' => 'encendido',
                'fecha_hora' => Carbon::now()->subHours(48)->setTime(11, 0, 0),
            ],
            // Caseta de cobro
            [
                'latitud' => 20.0583,
                'longitud' => -99.3174,
                'ubicacion_texto' => 'Caseta Tula, Hidalgo',
                'velocidad' => 40,
                'kilometraje_total' => 95,
                'nivel_combustible' => 92,
                'temperatura_motor' => 89,
                'estado_motor' => 'encendido',
                'fecha_hora' => Carbon::now()->subHours(48)->setTime(13, 0, 0),
            ],
            // Parada a comer
            [
                'latitud' => 20.4879,
                'longitud' => -99.2212,
                'ubicacion_texto' => 'Ixmiquilpan, Hidalgo',
                'velocidad' => 0,
                'kilometraje_total' => 165,
                'nivel_combustible' => 85,
                'temperatura_motor' => 70,
                'estado_motor' => 'apagado',
                'fecha_hora' => Carbon::now()->subHours(48)->setTime(15, 30, 0),
            ],
            // Reanudación del viaje
            [
                'latitud' => 20.4879,
                'longitud' => -99.2212,
                'ubicacion_texto' => 'Ixmiquilpan, Hidalgo',
                'velocidad' => 0,
                'kilometraje_total' => 165,
                'nivel_combustible' => 85,
                'temperatura_motor' => 75,
                'estado_motor' => 'encendido',
                'fecha_hora' => Carbon::now()->subHours(48)->setTime(16, 30, 0),
            ],
            // Avance por Hidalgo
            [
                'latitud' => 20.6748,
                'longitud' => -99.1901,
                'ubicacion_texto' => 'Zimapán, Hidalgo',
                'velocidad' => 70,
                'kilometraje_total' => 210,
                'nivel_combustible' => 80,
                'temperatura_motor' => 88,
                'estado_motor' => 'encendido',
                'fecha_hora' => Carbon::now()->subHours(48)->setTime(18, 0, 0),
            ],
            // Llegada a primer hotel
            [
                'latitud' => 21.1672,
                'longitud' => -99.0022,
                'ubicacion_texto' => 'Jacala, Hidalgo',
                'velocidad' => 0,
                'kilometraje_total' => 280,
                'nivel_combustible' => 70,
                'temperatura_motor' => 65,
                'estado_motor' => 'apagado',
                'fecha_hora' => Carbon::now()->subHours(48)->setTime(20, 0, 0),
            ],

            // Día 2 - Mañana
            [
                'latitud' => 21.1672,
                'longitud' => -99.0022,
                'ubicacion_texto' => 'Jacala, Hidalgo',
                'velocidad' => 0,
                'kilometraje_total' => 280,
                'nivel_combustible' => 70,
                'temperatura_motor' => 60,
                'estado_motor' => 'encendido',
                'fecha_hora' => Carbon::now()->subHours(24)->setTime(7, 0, 0),
            ],
            // Carga de combustible
            [
                'latitud' => 21.3850,
                'longitud' => -99.0201,
                'ubicacion_texto' => 'Tamazunchale, San Luis Potosí',
                'velocidad' => 0,
                'kilometraje_total' => 330,
                'nivel_combustible' => 100,
                'temperatura_motor' => 82,
                'estado_motor' => 'encendido',
                'fecha_hora' => Carbon::now()->subHours(24)->setTime(9, 30, 0),
            ],
            // Avance por SLP
            [
                'latitud' => 21.9825,
                'longitud' => -99.0103,
                'ubicacion_texto' => 'Ciudad Valles, San Luis Potosí',
                'velocidad' => 85,
                'kilometraje_total' => 420,
                'nivel_combustible' => 90,
                'temperatura_motor' => 89,
                'estado_motor' => 'encendido',
                'fecha_hora' => Carbon::now()->subHours(24)->setTime(12, 0, 0),
            ],
            // Límite con Nuevo León
            [
                'latitud' => 23.1875,
                'longitud' => -99.8634,
                'ubicacion_texto' => 'Entrada a Nuevo León',
                'velocidad' => 80,
                'kilometraje_total' => 590,
                'nivel_combustible' => 75,
                'temperatura_motor' => 90,
                'estado_motor' => 'encendido',
                'fecha_hora' => Carbon::now()->subHours(24)->setTime(15, 0, 0),
            ],
            // Parada técnica
            [
                'latitud' => 24.0167,
                'longitud' => -99.7500,
                'ubicacion_texto' => 'Linares, Nuevo León',
                'velocidad' => 0,
                'kilometraje_total' => 700,
                'nivel_combustible' => 60,
                'temperatura_motor' => 85,
                'estado_motor' => 'apagado',
                'fecha_hora' => Carbon::now()->subHours(24)->setTime(17, 0, 0),
            ],
            // Reanudación
            [
                'latitud' => 24.0167,
                'longitud' => -99.7500,
                'ubicacion_texto' => 'Linares, Nuevo León',
                'velocidad' => 0,
                'kilometraje_total' => 700,
                'nivel_combustible' => 60,
                'temperatura_motor' => 70,
                'estado_motor' => 'encendido',
                'fecha_hora' => Carbon::now()->subHours(24)->setTime(18, 0, 0),
            ],
            // Última parada antes de Monterrey
            [
                'latitud' => 25.3866,
                'longitud' => -100.1080,
                'ubicacion_texto' => 'Allende, Nuevo León',
                'velocidad' => 75,
                'kilometraje_total' => 830,
                'nivel_combustible' => 45,
                'temperatura_motor' => 89,
                'estado_motor' => 'encendido',
                'fecha_hora' => Carbon::now()->subHours(24)->setTime(20, 30, 0),
            ],

            // Día 3 - Hoy (últimas ubicaciones)
            [
                'latitud' => 25.6516,
                'longitud' => -100.2900,
                'ubicacion_texto' => 'Entrada a Monterrey',
                'velocidad' => 60,
                'kilometraje_total' => 890,
                'nivel_combustible' => 35,
                'temperatura_motor' => 88,
                'estado_motor' => 'encendido',
                'fecha_hora' => Carbon::now()->subHours(6)->setTime(7, 0, 0),
            ],
            [
                'latitud' => 25.6866,
                'longitud' => -100.3161,
                'ubicacion_texto' => 'Zona Centro, Monterrey',
                'velocidad' => 40,
                'kilometraje_total' => 895,
                'nivel_combustible' => 32,
                'temperatura_motor' => 87,
                'estado_motor' => 'encendido',
                'fecha_hora' => Carbon::now()->subHours(5)->setTime(8, 0, 0),
            ],
            [
                'latitud' => 25.7092,
                'longitud' => -100.3506,
                'ubicacion_texto' => 'San Pedro Garza García',
                'velocidad' => 30,
                'kilometraje_total' => 898,
                'nivel_combustible' => 30,
                'temperatura_motor' => 86,
                'estado_motor' => 'encendido',
                'fecha_hora' => Carbon::now()->subHours(4)->setTime(9, 0, 0),
            ],
            [
                'latitud' => 25.6524,
                'longitud' => -100.2874,
                'ubicacion_texto' => 'Zona Valle, Monterrey',
                'velocidad' => 20,
                'kilometraje_total' => 902,
                'nivel_combustible' => 28,
                'temperatura_motor' => 85,
                'estado_motor' => 'encendido',
                'fecha_hora' => Carbon::now()->subHours(3)->setTime(10, 0, 0),
            ],
            [
                'latitud' => 25.6327,
                'longitud' => -100.2860,
                'ubicacion_texto' => 'Cerca del destino',
                'velocidad' => 15,
                'kilometraje_total' => 905,
                'nivel_combustible' => 25,
                'temperatura_motor' => 84,
                'estado_motor' => 'encendido',
                'fecha_hora' => Carbon::now()->subHours(2)->setTime(11, 0, 0),
            ],
            // Última ubicación registrada
            [
                'latitud' => 25.6144,
                'longitud' => -100.2800,
                'ubicacion_texto' => 'Concesionario Honda, Monterrey',
                'velocidad' => 0,
                'kilometraje_total' => 910,
                'nivel_combustible' => 22,
                'temperatura_motor' => 75,
                'estado_motor' => 'apagado',
                'fecha_hora' => Carbon::now()->subHour()->setTime(12, 30, 0),
            ],
        ];

        // Insertar todas las ubicaciones
        foreach ($ubicaciones as $ubicacion) {
            CwseguimientoViaje::create(array_merge(
                ['viaje_id' => $viajeId],
                $ubicacion
            ));
        }

        $this->command->info('Datos de seguimiento para viaje #6 insertados correctamente');
    }
}
