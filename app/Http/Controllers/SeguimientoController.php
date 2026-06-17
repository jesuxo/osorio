<?php
// app/Http/Controllers/SeguimientoController.php

namespace App\Http\Controllers;

use App\Models\Cwviaje;
use App\Models\CwseguimientoViaje;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SeguimientoController extends Controller
{
    /**
     * Store a new ubicacion.
     */
    public function store(Request $request, $viajeId)
    {
        $viaje = Cwviaje::findOrFail($viajeId);

        $validator = Validator::make($request->all(), [
            'latitud' => 'required|numeric|between:-90,90',
            'longitud' => 'required|numeric|between:-180,180',
            'ubicacion_texto' => 'nullable|string|max:255',
            'velocidad' => 'nullable|numeric|min:0',
            'kilometraje_total' => 'nullable|numeric|min:0',
            'nivel_combustible' => 'nullable|numeric|between:0,100',
            'temperatura_motor' => 'nullable|numeric',
            'estado_motor' => 'nullable|in:encendido,apagado',
            'etapa_id' => 'nullable|exists:cwetapas_viaje,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $seguimiento = $viaje->seguimientos()->create([
            'latitud' => $request->latitud,
            'longitud' => $request->longitud,
            'ubicacion_texto' => $request->ubicacion_texto,
            'velocidad' => $request->velocidad,
            'kilometraje_total' => $request->kilometraje_total,
            'nivel_combustible' => $request->nivel_combustible,
            'temperatura_motor' => $request->temperatura_motor,
            'estado_motor' => $request->estado_motor,
            'etapa_id' => $request->etapa_id,
            'fecha_hora' => now(),
        ]);

        // Si el viaje está en estado planeado y recibimos seguimiento, pasarlo a en_curso
        if ($viaje->estado === 'planeado') {
            $viaje->update(['estado' => 'en_curso']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Ubicación registrada',
            'seguimiento' => $seguimiento
        ]);
    }

    /**
     * API: Get latest ubicacion.
     */
    public function apiUltimaUbicacion($viajeId)
    {

        $viaje = Cwviaje::with('ultimoSeguimiento')->find($viajeId);

        if (!$viaje || !$viaje->ultimoSeguimiento) {
            return response()->json([
                'success' => false,
                'message' => 'No hay información de ubicación'
            ], 404);
        }

        $ultimo = $viaje->ultimoSeguimiento;

        return response()->json([
            'success' => true,
            'lat' => $ultimo->latitud,
            'lng' => $ultimo->longitud,
            'texto' => $ultimo->ubicacion_texto,
            'fecha' => $ultimo->fecha_hora->format('d/m/Y H:i:s'),
            'fecha_raw' => $ultimo->fecha_hora->toISOString(),
            'velocidad' => $ultimo->velocidad,
            'combustible' => $ultimo->nivel_combustible,
            'kilometraje' => $ultimo->kilometraje_total,
            'temperatura' => $ultimo->temperatura_motor,
            'estado_motor' => $ultimo->estado_motor,
        ]);
    }

    /**
     * API: Get ubicacion history.
     */
    public function apiHistorial($viajeId, Request $request)
    {
        $viaje = Cwviaje::findOrFail($viajeId);

        $limit = $request->get('limit', 20);
        $horas = $request->get('horas', 24);

        $query = $viaje->seguimientos()
            ->where('fecha_hora', '>=', now()->subHours($horas))
            ->orderBy('fecha_hora', 'desc'); // Más recientes primero

        if ($request->has('etapa_id')) {
            $query->where('etapa_id', $request->etapa_id);
        }

        $seguimientos = $query->limit($limit)->get();

        return response()->json([
            'success' => true,
            'viaje_id' => $viaje->id,
            'total' => $seguimientos->count(),
            'historial' => $seguimientos->map(function($s) {
                return [
                    'latitud' => $s->latitud,
                    'longitud' => $s->longitud,
                    'fecha_hora' => $s->fecha_hora->format('d/m/Y H:i:s'),
                    'fecha_raw' => $s->fecha_hora->toISOString(),
                    'velocidad' => $s->velocidad,
                    'combustible' => $s->nivel_combustible,
                    'ubicacion' => $s->ubicacion_texto,
                ];
            })
        ]);
    }

    /**
     * Get route summary.
     */
    public function resumenRuta($viajeId)
    {
        $viaje = Cwviaje::with('seguimientos')->findOrFail($viajeId);
        $seguimientos = $viaje->seguimientos()->orderBy('fecha_hora')->get();

        if ($seguimientos->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No hay datos de seguimiento para este viaje'
            ], 404);
        }

        $inicio = $seguimientos->first();
        $fin = $seguimientos->last();

        // Calcular distancia total aproximada (usando fórmula de Haversine)
        $distanciaTotal = 0;
        for ($i = 1; $i < $seguimientos->count(); $i++) {
            $distanciaTotal += $this->calcularDistancia(
                $seguimientos[$i-1]->latitud,
                $seguimientos[$i-1]->longitud,
                $seguimientos[$i]->latitud,
                $seguimientos[$i]->longitud
            );
        }

        $duracion = $inicio->fecha_hora->diffInHours($fin->fecha_hora);

        return response()->json([
            'success' => true,
            'viaje_id' => $viaje->id,
            'inicio' => [
                'ubicacion' => $inicio->ubicacion_texto ?? 'Desconocido',
                'fecha' => $inicio->fecha_hora->format('d/m/Y H:i:s'),
                'fecha_raw' => $inicio->fecha_hora->toISOString(),
                'coordenadas' => [
                    'lat' => $inicio->latitud,
                    'lng' => $inicio->longitud
                ],
            ],
            'fin' => [
                'ubicacion' => $fin->ubicacion_texto ?? 'Desconocido',
                'fecha' => $fin->fecha_hora->format('d/m/Y H:i:s'),
                'fecha_raw' => $fin->fecha_hora->toISOString(),
                'coordenadas' => [
                    'lat' => $fin->latitud,
                    'lng' => $fin->longitud
                ],
            ],
            'resumen' => [
                'distancia_km' => round($distanciaTotal, 2),
                'duracion_horas' => round($duracion, 1),
                'duracion_texto' => $this->formatearDuracion($duracion),
                'puntos_registrados' => $seguimientos->count(),
                'velocidad_promedio' => $duracion > 0 ? round($distanciaTotal / $duracion, 1) : 0,
            ],
            'ruta' => $seguimientos->map(function($s) {
                return [
                    'lat' => $s->latitud,
                    'lng' => $s->longitud,
                    'fecha' => $s->fecha_hora->format('d/m/Y H:i:s'),
                    'fecha_raw' => $s->fecha_hora->toISOString(),
                    'velocidad' => $s->velocidad,
                    'ubicacion' => $s->ubicacion_texto,
                ];
            })
        ]);
    }
    private function formatearDuracion($horas)
    {
        $horasEnteras = floor($horas);
        $minutos = round(($horas - $horasEnteras) * 60);

        if ($horasEnteras > 0 && $minutos > 0) {
            return "{$horasEnteras}h {$minutos}m";
        } elseif ($horasEnteras > 0) {
            return "{$horasEnteras}h";
        } else {
            return "{$minutos}m";
        }
    }

    /**
     * Calculate distance between two points using Haversine formula.
     */
    private function calcularDistancia($lat1, $lon1, $lat2, $lon2)
    {
        if (!$lat1 || !$lon1 || !$lat2 || !$lon2) {
            return 0;
        }

        $radioTierra = 6371; // km

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat/2) * sin($dLat/2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon/2) * sin($dLon/2);

        $c = 2 * atan2(sqrt($a), sqrt(1-$a));

        return $radioTierra * $c;
    }
}
