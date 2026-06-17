<?php
// database/seeders/ViajeSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cwviaje;
use App\Models\Cwviajemoto;
use App\Models\CwetapaViaje;
use App\Models\Cwgasto;
use App\Models\CwseguimientoViaje;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ViajeSeeder extends Seeder
{
    public function run(): void
    {
        // Obtener IDs de camiones y choferes activos
        $camiones = DB::table('cwcamiones')->where('activo', true)->pluck('id')->toArray();
        $choferes = DB::table('cwchoferes')->where('activo', true)->pluck('id')->toArray();
        $tiposGasto = DB::table('cwtipogastos')->pluck('id')->toArray();

        if (empty($camiones) || empty($choferes)) {
            $this->command->warn('No hay camiones o choferes activos. Ejecuta primero CamionSeeder y ChoferSeeder.');
            return;
        }

        // Viaje 1: Completado - Ruta CDMX a Guadalajara
        $viaje1 = Cwviaje::create([
            'folio' => 'VIA-2024-001',
            'camion_id' => $camiones[0],
            'chofer_id' => $choferes[0],
            'fecha_inicio' => Carbon::now()->subDays(15),
            'fecha_fin' => Carbon::now()->subDays(12),
            'origen' => 'Ciudad de México',
            'destino' => 'Guadalajara, Jalisco',
            'distancia_km' => 540.5,
            'estado' => 'completado',
            'notas' => 'Viaje sin contratiempos. Entrega a tiempo.',
            'created_at' => Carbon::now()->subDays(15),
            'updated_at' => Carbon::now()->subDays(12),
        ]);

        // Motos del viaje 1
        Cwviajemoto::insert([
            [
                'viaje_id' => $viaje1->id,
                'modelo_moto' => 'Honda CBR 600RR',
                'cantidad' => 3,
                'precio_por_moto' => 3500.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'viaje_id' => $viaje1->id,
                'modelo_moto' => 'Yamaha R6',
                'cantidad' => 2,
                'precio_por_moto' => 3800.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'viaje_id' => $viaje1->id,
                'modelo_moto' => 'Kawasaki Ninja 650',
                'cantidad' => 2,
                'precio_por_moto' => 3200.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Etapas del viaje 1
        CwetapaViaje::insert([
            [
                'viaje_id' => $viaje1->id,
                'nombre' => 'Carga de motos',
                'descripcion' => 'Carga en almacén CDMX',
                'orden' => 1,
                'estado' => 'completado',
                'ubicacion' => 'Ciudad de México',
                'kilometraje_estimado' => 0,
                'kilometraje_real' => 0,
                'fecha_real_inicio' => Carbon::now()->subDays(15)->setTime(8, 0),
                'fecha_real_fin' => Carbon::now()->subDays(15)->setTime(10, 30),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'viaje_id' => $viaje1->id,
                'nombre' => 'Traslado a Guadalajara',
                'descripcion' => 'Ruta por autopista',
                'orden' => 2,
                'estado' => 'completado',
                'ubicacion' => 'En ruta',
                'kilometraje_estimado' => 540,
                'kilometraje_real' => 540.5,
                'fecha_real_inicio' => Carbon::now()->subDays(15)->setTime(11, 0),
                'fecha_real_fin' => Carbon::now()->subDays(13)->setTime(9, 30),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'viaje_id' => $viaje1->id,
                'nombre' => 'Descarga en concesionario',
                'descripcion' => 'Entrega en Honda Guadalajara',
                'orden' => 3,
                'estado' => 'completado',
                'ubicacion' => 'Guadalajara, Jalisco',
                'kilometraje_estimado' => 0,
                'kilometraje_real' => 0,
                'fecha_real_inicio' => Carbon::now()->subDays(12)->setTime(10, 0),
                'fecha_real_fin' => Carbon::now()->subDays(12)->setTime(13, 0),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Gastos del viaje 1
        Cwgasto::insert([
            [
                'tipo_gasto_id' => $tiposGasto[array_search('Combustible', array_column($tiposGasto, 'nombre'))] ?? 1,
                'concepto' => 'Carga de diésel',
                'descripcion' => 'Gasolinera PEMAX',
                'monto' => 4200.50,
                'fecha_gasto' => Carbon::now()->subDays(14),
                'gastable_id' => $viaje1->id,
                'gastable_type' => 'App\Models\Cwviaje',
                'proveedor' => 'PEMAX',
                'metodo_pago' => 'tarjeta_credito',
                'referencia_pago' => 'TXN-123456',
                'deducible_impuestos' => true,
                'registrado_por' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tipo_gasto_id' => $tiposGasto[array_search('Peaje', array_column($tiposGasto, 'nombre'))] ?? 2,
                'concepto' => 'Casetas autopista',
                'descripcion' => 'Tramos: CDMX-Querétaro, Querétaro-Guadalajara',
                'monto' => 1850.00,
                'fecha_gasto' => Carbon::now()->subDays(14),
                'gastable_id' => $viaje1->id,
                'gastable_type' => 'App\Models\Cwviaje',
                'proveedor' => 'CAPUFE',
                'metodo_pago' => 'efectivo',
                'referencia_pago' => null,
                'deducible_impuestos' => true,
                'registrado_por' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tipo_gasto_id' => $tiposGasto[array_search('Hospedaje', array_column($tiposGasto, 'nombre'))] ?? 6,
                'concepto' => 'Hotel para chofer',
                'descripcion' => 'Hotel Holiday Inn Querétaro',
                'monto' => 1200.00,
                'fecha_gasto' => Carbon::now()->subDays(14),
                'gastable_id' => $viaje1->id,
                'gastable_type' => 'App\Models\Cwviaje',
                'proveedor' => 'Holiday Inn',
                'metodo_pago' => 'tarjeta_credito',
                'referencia_pago' => 'HOT-789',
                'deducible_impuestos' => true,
                'registrado_por' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tipo_gasto_id' => $tiposGasto[array_search('Comida', array_column($tiposGasto, 'nombre'))] ?? 7,
                'concepto' => 'Alimentación chofer',
                'descripcion' => 'Comidas durante el viaje',
                'monto' => 850.00,
                'fecha_gasto' => Carbon::now()->subDays(14),
                'gastable_id' => $viaje1->id,
                'gastable_type' => 'App\Models\Cwviaje',
                'proveedor' => null,
                'metodo_pago' => 'efectivo',
                'referencia_pago' => null,
                'deducible_impuestos' => false,
                'registrado_por' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Seguimiento del viaje 1 (simulado)
        $this->crearSeguimientoSimulado($viaje1->id, [
            ['lat' => 19.4326, 'lng' => -99.1332, 'fecha' => Carbon::now()->subDays(15)->setTime(10, 0)],
            ['lat' => 19.5937, 'lng' => -99.2401, 'fecha' => Carbon::now()->subDays(15)->setTime(11, 30)],
            ['lat' => 20.5888, 'lng' => -100.3899, 'fecha' => Carbon::now()->subDays(14)->setTime(8, 0)],
            ['lat' => 20.9671, 'lng' => -101.7168, 'fecha' => Carbon::now()->subDays(14)->setTime(14, 0)],
            ['lat' => 21.1526, 'lng' => -101.7115, 'fecha' => Carbon::now()->subDays(13)->setTime(9, 0)],
            ['lat' => 20.6597, 'lng' => -103.3496, 'fecha' => Carbon::now()->subDays(13)->setTime(16, 0)],
            ['lat' => 20.6751, 'lng' => -103.3474, 'fecha' => Carbon::now()->subDays(12)->setTime(9, 0)],
        ]);

        // Viaje 2: En curso - Ruta Monterrey a CDMX
        $viaje2 = Cwviaje::create([
            'folio' => 'VIA-2024-002',
            'camion_id' => $camiones[1],
            'chofer_id' => $choferes[1],
            'fecha_inicio' => Carbon::now()->subDays(2),
            'fecha_fin' => null,
            'origen' => 'Monterrey, Nuevo León',
            'destino' => 'Ciudad de México',
            'distancia_km' => 910.0,
            'estado' => 'en_curso',
            'notas' => 'Viaje con carga de motos deportivas. Cliente urgente.',
            'created_at' => Carbon::now()->subDays(2),
            'updated_at' => Carbon::now(),
        ]);

        // Motos del viaje 2
        Cwviajemoto::insert([
            [
                'viaje_id' => $viaje2->id,
                'modelo_moto' => 'BMW S1000RR',
                'cantidad' => 2,
                'precio_por_moto' => 5500.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'viaje_id' => $viaje2->id,
                'modelo_moto' => 'Ducati Panigale V4',
                'cantidad' => 1,
                'precio_por_moto' => 6500.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'viaje_id' => $viaje2->id,
                'modelo_moto' => 'Suzuki GSX-R1000',
                'cantidad' => 2,
                'precio_por_moto' => 4800.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Etapas del viaje 2
        CwetapaViaje::insert([
            [
                'viaje_id' => $viaje2->id,
                'nombre' => 'Carga de motos',
                'descripcion' => 'Carga en distribuidor BMW Monterrey',
                'orden' => 1,
                'estado' => 'completado',
                'ubicacion' => 'Monterrey, Nuevo León',
                'kilometraje_estimado' => 0,
                'kilometraje_real' => 0,
                'fecha_real_inicio' => Carbon::now()->subDays(2)->setTime(8, 0),
                'fecha_real_fin' => Carbon::now()->subDays(2)->setTime(11, 30),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'viaje_id' => $viaje2->id,
                'nombre' => 'Traslado a CDMX',
                'descripcion' => 'Ruta por autopista Monterrey-CDMX',
                'orden' => 2,
                'estado' => 'en_curso',
                'ubicacion' => 'En ruta',
                'kilometraje_estimado' => 910,
                'kilometraje_real' => 450,
                'fecha_real_inicio' => Carbon::now()->subDays(2)->setTime(12, 0),
                'fecha_real_fin' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'viaje_id' => $viaje2->id,
                'nombre' => 'Descarga en CDMX',
                'descripcion' => 'Entrega en concesionario CDMX',
                'orden' => 3,
                'estado' => 'pendiente',
                'ubicacion' => 'Ciudad de México',
                'kilometraje_estimado' => 0,
                'kilometraje_real' => null,
                'fecha_real_inicio' => null,
                'fecha_real_fin' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Gastos del viaje 2
        Cwgasto::insert([
            [
                'tipo_gasto_id' => $tiposGasto[array_search('Combustible', array_column($tiposGasto, 'nombre'))] ?? 1,
                'concepto' => 'Primera carga de diésel',
                'descripcion' => 'Gasolinera Monterrey',
                'monto' => 3800.00,
                'fecha_gasto' => Carbon::now()->subDays(2),
                'gastable_id' => $viaje2->id,
                'gastable_type' => 'App\Models\Cwviaje',
                'proveedor' => 'PEMAX',
                'metodo_pago' => 'tarjeta_credito',
                'referencia_pago' => 'TXN-789012',
                'deducible_impuestos' => true,
                'registrado_por' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tipo_gasto_id' => $tiposGasto[array_search('Peaje', array_column($tiposGasto, 'nombre'))] ?? 2,
                'concepto' => 'Peajes primer tramo',
                'descripcion' => 'Monterrey - Saltillo',
                'monto' => 450.00,
                'fecha_gasto' => Carbon::now()->subDays(1),
                'gastable_id' => $viaje2->id,
                'gastable_type' => 'App\Models\Cwviaje',
                'proveedor' => 'CAPUFE',
                'metodo_pago' => 'efectivo',
                'referencia_pago' => null,
                'deducible_impuestos' => true,
                'registrado_por' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Seguimiento del viaje 2 (en curso)
        $this->crearSeguimientoSimulado($viaje2->id, [
            ['lat' => 25.6866, 'lng' => -100.3161, 'fecha' => Carbon::now()->subDays(2)->setTime(11, 0)],
            ['lat' => 25.4230, 'lng' => -100.9919, 'fecha' => Carbon::now()->subDays(2)->setTime(14, 0)],
            ['lat' => 24.7967, 'lng' => -101.1514, 'fecha' => Carbon::now()->subDays(2)->setTime(18, 0)],
            ['lat' => 23.7477, 'lng' => -101.2773, 'fecha' => Carbon::now()->subDays(1)->setTime(8, 0)],
            ['lat' => 22.7673, 'lng' => -101.2635, 'fecha' => Carbon::now()->subDays(1)->setTime(12, 0)],
            ['lat' => 22.1509, 'lng' => -101.0021, 'fecha' => Carbon::now()->subDays(1)->setTime(16, 0)],
            ['lat' => 21.8853, 'lng' => -100.9870, 'fecha' => Carbon::now()->setTime(9, 0)],
            ['lat' => 21.1606, 'lng' => -100.9321, 'fecha' => Carbon::now()->setTime(12, 0)],
        ]);

        // Viaje 3: Planeado
        $viaje3 = Cwviaje::create([
            'folio' => 'VIA-2024-003',
            'camion_id' => $camiones[2],
            'chofer_id' => $choferes[2],
            'fecha_inicio' => Carbon::now()->addDays(5),
            'fecha_fin' => Carbon::now()->addDays(8),
            'origen' => 'Guadalajara, Jalisco',
            'destino' => 'Tijuana, Baja California',
            'distancia_km' => 2150.0,
            'estado' => 'planeado',
            'notas' => 'Viaje largo con múltiples paradas. Cliente solicita entregas en ruta.',
            'created_at' => Carbon::now()->subDays(1),
            'updated_at' => Carbon::now(),
        ]);

        // Motos del viaje 3
        Cwviajemoto::insert([
            [
                'viaje_id' => $viaje3->id,
                'modelo_moto' => 'Harley Davidson Street 750',
                'cantidad' => 4,
                'precio_por_moto' => 4200.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'viaje_id' => $viaje3->id,
                'modelo_moto' => 'Indian Scout',
                'cantidad' => 2,
                'precio_por_moto' => 4800.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Etapas del viaje 3
        CwetapaViaje::insert([
            [
                'viaje_id' => $viaje3->id,
                'nombre' => 'Carga en Guadalajara',
                'descripcion' => 'Carga de motos custom',
                'orden' => 1,
                'estado' => 'pendiente',
                'ubicacion' => 'Guadalajara, Jalisco',
                'kilometraje_estimado' => 0,
                'kilometraje_real' => null,
                'fecha_estimada_inicio' => Carbon::now()->addDays(5)->setTime(8, 0),
                'fecha_estimada_fin' => Carbon::now()->addDays(5)->setTime(11, 0),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'viaje_id' => $viaje3->id,
                'nombre' => 'Parada 1 - Mazatlán',
                'descripcion' => 'Entrega de 2 motos Harley',
                'orden' => 2,
                'estado' => 'pendiente',
                'ubicacion' => 'Mazatlán, Sinaloa',
                'kilometraje_estimado' => 650,
                'kilometraje_real' => null,
                'fecha_estimada_inicio' => Carbon::now()->addDays(6)->setTime(8, 0),
                'fecha_estimada_fin' => Carbon::now()->addDays(6)->setTime(12, 0),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'viaje_id' => $viaje3->id,
                'nombre' => 'Parada 2 - Hermosillo',
                'descripcion' => 'Entrega de 2 motos Indian',
                'orden' => 3,
                'estado' => 'pendiente',
                'ubicacion' => 'Hermosillo, Sonora',
                'kilometraje_estimado' => 850,
                'kilometraje_real' => null,
                'fecha_estimada_inicio' => Carbon::now()->addDays(7)->setTime(10, 0),
                'fecha_estimada_fin' => Carbon::now()->addDays(7)->setTime(14, 0),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'viaje_id' => $viaje3->id,
                'nombre' => 'Destino final - Tijuana',
                'descripcion' => 'Entrega de motos restantes',
                'orden' => 4,
                'estado' => 'pendiente',
                'ubicacion' => 'Tijuana, Baja California',
                'kilometraje_estimado' => 650,
                'kilometraje_real' => null,
                'fecha_estimada_inicio' => Carbon::now()->addDays(8)->setTime(9, 0),
                'fecha_estimada_fin' => Carbon::now()->addDays(8)->setTime(13, 0),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Viaje 4: Cancelado
        $viaje4 = Cwviaje::create([
            'folio' => 'VIA-2024-004',
            'camion_id' => $camiones[3],
            'chofer_id' => $choferes[3],
            'fecha_inicio' => Carbon::now()->subDays(10),
            'fecha_fin' => Carbon::now()->subDays(8),
            'origen' => 'Puebla, Puebla',
            'destino' => 'Veracruz, Veracruz',
            'distancia_km' => 380.0,
            'estado' => 'cancelado',
            'notas' => 'Cancelado por condiciones climáticas adversas. Cliente reprogramó.',
            'created_at' => Carbon::now()->subDays(10),
            'updated_at' => Carbon::now()->subDays(8),
        ]);

        // Motos del viaje 4
        Cwviajemoto::insert([
            [
                'viaje_id' => $viaje4->id,
                'modelo_moto' => 'Yamaha MT-07',
                'cantidad' => 3,
                'precio_por_moto' => 2900.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Etapas del viaje 4 (canceladas)
        CwetapaViaje::insert([
            [
                'viaje_id' => $viaje4->id,
                'nombre' => 'Carga en Puebla',
                'descripcion' => 'Carga programada',
                'orden' => 1,
                'estado' => 'pendiente',
                'ubicacion' => 'Puebla, Puebla',
                'kilometraje_estimado' => 0,
                'kilometraje_real' => null,
                'fecha_estimada_inicio' => Carbon::now()->subDays(10)->setTime(8, 0),
                'fecha_estimada_fin' => Carbon::now()->subDays(10)->setTime(10, 0),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'viaje_id' => $viaje4->id,
                'nombre' => 'Traslado a Veracruz',
                'descripcion' => 'Ruta cancelada',
                'orden' => 2,
                'estado' => 'pendiente',
                'ubicacion' => 'En ruta',
                'kilometraje_estimado' => 380,
                'kilometraje_real' => null,
                'fecha_estimada_inicio' => Carbon::now()->subDays(10)->setTime(11, 0),
                'fecha_estimada_fin' => Carbon::now()->subDays(8)->setTime(16, 0),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Gasto de cancelación
        Cwgasto::insert([
            [
                'tipo_gasto_id' => $tiposGasto[array_search('Multas', array_column($tiposGasto, 'nombre'))] ?? 14,
                'concepto' => 'Penalización por cancelación',
                'descripcion' => 'Cargo por cancelación de último momento',
                'monto' => 1500.00,
                'fecha_gasto' => Carbon::now()->subDays(8),
                'gastable_id' => $viaje4->id,
                'gastable_type' => 'App\Models\Cwviaje',
                'proveedor' => 'Cliente',
                'metodo_pago' => 'transferencia',
                'referencia_pago' => 'CANC-001',
                'deducible_impuestos' => false,
                'registrado_por' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Función auxiliar para crear seguimientos simulados
     */
    private function crearSeguimientoSimulado($viajeId, array $puntos)
    {
        foreach ($puntos as $index => $punto) {
            CwseguimientoViaje::create([
                'viaje_id' => $viajeId,
                'latitud' => $punto['lat'],
                'longitud' => $punto['lng'],
                'ubicacion_texto' => "Punto {$index} de seguimiento",
                'velocidad' => rand(60, 100),
                'kilometraje_total' => $index * 100,
                'nivel_combustible' => rand(20, 95),
                'temperatura_motor' => rand(80, 95),
                'estado_motor' => 'encendido',
                'fecha_hora' => $punto['fecha'],
                'created_at' => $punto['fecha'],
                'updated_at' => $punto['fecha'],
            ]);
        }
    }
}
