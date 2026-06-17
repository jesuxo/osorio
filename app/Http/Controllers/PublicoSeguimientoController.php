<?php
// app/Http/Controllers/PublicoSeguimientoController.php

namespace App\Http\Controllers;

use App\Models\Cwviaje;
use App\Models\CwetapaViaje;
use App\Models\CwseguimientoViaje;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class PublicoSeguimientoController extends Controller
{
    public function index($token)
    {
        try {
            $id = decrypt($token);
            $viaje = Cwviaje::with(['camion', 'chofer', 'etapas' => function($q) {
                $q->orderBy('orden');
            }])->findOrFail($id);

            $ultimoSeguimiento = $viaje->ultimoSeguimiento;
            $puntosMapa = $viaje->puntosSeguimientoMapa;

            return view('publico.seguimiento', compact('viaje', 'ultimoSeguimiento', 'token', 'puntosMapa'));

        } catch (\Exception $e) {
            abort(404, 'Enlace no válido');
        }
    }

    public function actualizarUbicacion(Request $request, $token)
    {
        try {
            $id = decrypt($token);
            $viaje = Cwviaje::findOrFail($id);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Token no válido'], 404);
        }

        $validator = Validator::make($request->all(), [
            'latitud' => 'required|numeric|between:-90,90',
            'longitud' => 'required|numeric|between:-180,180',
            'ubicacion_texto' => 'nullable|string|max:255',
            'velocidad' => 'nullable|numeric|min:0',
            'nivel_combustible' => 'nullable|numeric|between:0,100',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();

        try {
            $seguimiento = $viaje->seguimientos()->create([
                'latitud' => $request->latitud,
                'longitud' => $request->longitud,
                'ubicacion_texto' => $request->ubicacion_texto,
                'velocidad' => $request->velocidad,
                'nivel_combustible' => $request->nivel_combustible,
                'tipo_punto' => 'manual',
                'fecha_hora' => now(),
            ]);

            $viajeActualizado = false;
            if ($viaje->estado === 'planeado') {
                $viaje->update(['estado' => 'en_curso']);
                $viajeActualizado = true;
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Ubicación actualizada',
                'viaje_actualizado' => $viajeActualizado,
                'viaje_estado' => $viaje->fresh()->estado,
                'seguimiento' => $seguimiento
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error al guardar ubicación'], 500);
        }
    }

    public function cambiarEstadoEtapa(Request $request, $token, $etapaId)
    {
        try {
            $id = decrypt($token);
            $viaje = Cwviaje::findOrFail($id);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Token no válido'], 404);
        }

        $etapa = CwetapaViaje::where('id', $etapaId)
            ->where('viaje_id', $viaje->id)
            ->firstOrFail();

        $validator = Validator::make($request->all(), [
            'estado' => 'required|in:en_curso,completado',
            'kilometraje_inicio' => 'required_if:estado,en_curso|numeric|min:0',
            'kilometraje_real' => 'nullable|numeric|min:0',
            'latitud' => 'required|numeric|between:-90,90',
            'longitud' => 'required|numeric|between:-180,180',
            'ubicacion_texto' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();

        try {
            $data = ['estado' => $request->estado];
            $cambios = [];
            $viajeActualizado = false;

            if ($request->estado === 'en_curso') {
                if ($viaje->estado === 'planeado') {
                    $viaje->update(['estado' => 'en_curso']);
                    $viajeActualizado = true;
                    $cambios[] = 'Viaje marcado como en curso';
                }

                if (!$etapa->fecha_real_inicio) {
                    $data['fecha_real_inicio'] = now();
                    $data['kilometraje_inicio'] = $request->kilometraje_inicio;
                    $data['latitud_inicio'] = $request->latitud;
                    $data['longitud_inicio'] = $request->longitud;
                    $data['ubicacion_texto_inicio'] = $request->ubicacion_texto;

                    // Guardar punto de seguimiento para inicio de etapa
                    $viaje->seguimientos()->create([
                        'latitud' => $request->latitud,
                        'longitud' => $request->longitud,
                        'ubicacion_texto' => $request->ubicacion_texto,
                        'tipo_punto' => 'inicio_etapa',
                        'etapa_asociada_id' => $etapa->id,
                        'kilometraje_total' => $request->kilometraje_inicio,
                        'fecha_hora' => now(),
                    ]);
                }
            }

            if ($request->estado === 'completado' && !$etapa->fecha_real_fin) {
                $data['fecha_real_fin'] = now();
                $data['latitud_fin'] = $request->latitud;
                $data['longitud_fin'] = $request->longitud;
                $data['ubicacion_texto_fin'] = $request->ubicacion_texto;

                if ($request->has('kilometraje_real')) {
                    $data['kilometraje_real'] = $request->kilometraje_real;
                }

                // Guardar punto de seguimiento para fin de etapa
                $viaje->seguimientos()->create([
                    'latitud' => $request->latitud,
                    'longitud' => $request->longitud,
                    'ubicacion_texto' => $request->ubicacion_texto,
                    'tipo_punto' => 'fin_etapa',
                    'etapa_asociada_id' => $etapa->id,
                    'kilometraje_total' => $request->kilometraje_real ?? $etapa->kilometraje_inicio,
                    'fecha_hora' => now(),
                ]);
            }

            $etapa->update($data);

            $etapasPendientes = $viaje->etapas()->whereIn('estado', ['pendiente', 'en_curso'])->count();

            if ($etapasPendientes === 0 && $viaje->estado !== 'completado') {
                $viaje->update([
                    'estado' => 'completado',
                    'fecha_fin' => now(),
                ]);
                $viajeActualizado = true;
                $cambios[] = 'Viaje completado';
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Estado actualizado',
                'cambios' => $cambios,
                'viaje_actualizado' => $viajeActualizado,
                'viaje_estado' => $viaje->fresh()->estado,
                'etapa' => $etapa->fresh()
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error al actualizar: ' . $e->getMessage()], 500);
        }
    }

    public function getInfoViaje($token)
    {
        try {
            $id = decrypt($token);
            $viaje = Cwviaje::with([
                'camion',
                'chofer',
                'etapas' => function($q) {
                    $q->orderBy('orden');
                },
                'ultimoSeguimiento',
                'puntosSeguimientoMapa'
            ])->findOrFail($id);

            return response()->json([
                'success' => true,
                'viaje' => [
                    'id' => $viaje->id,
                    'estado' => $viaje->estado,
                    'progreso' => $viaje->progreso,
                    'etapa_actual' => $viaje->etapa_actual,
                    'ultima_ubicacion' => $viaje->ultimoSeguimiento,
                ],
                'etapas' => $viaje->etapas->map(function($e) {
                    return [
                        'id' => $e->id,
                        'nombre' => $e->nombre,
                        'ubicacion' => $e->ubicacion,
                        'orden' => $e->orden,
                        'kilometraje_estimado' => $e->kilometraje_estimado,
                        'kilometraje_inicio' => $e->kilometraje_inicio,
                        'kilometraje_real' => $e->kilometraje_real,
                        'kilometraje_recorrido' => $e->kilometraje_inicio && $e->kilometraje_real
                            ? $e->kilometraje_real - $e->kilometraje_inicio
                            : null,
                        'estado' => $e->estado,
                        'fecha_real_inicio' => $e->fecha_real_inicio?->format('d/m/Y H:i'),
                        'fecha_real_fin' => $e->fecha_real_fin?->format('d/m/Y H:i'),
                        'latitud_inicio' => $e->latitud_inicio,
                        'longitud_inicio' => $e->longitud_inicio,
                        'ubicacion_texto_inicio' => $e->ubicacion_texto_inicio,
                        'latitud_fin' => $e->latitud_fin,
                        'longitud_fin' => $e->longitud_fin,
                        'ubicacion_texto_fin' => $e->ubicacion_texto_fin,
                    ];
                }),
                'puntos_seguimiento' => $viaje->puntosSeguimientoMapa->map(function($p) {
                    return [
                        'id' => $p->id,
                        'latitud' => $p->latitud,
                        'longitud' => $p->longitud,
                        'ubicacion_texto' => $p->ubicacion_texto,
                        'tipo_punto' => $p->tipo_punto,
                        'tipo_texto' => $p->tipo_texto,
                        'color' => $p->color_marker,
                        'icono' => $p->icon_marker,
                        'kilometraje' => $p->kilometraje_total,
                        'fecha_hora' => $p->fecha_hora->format('d/m/Y H:i:s'),
                        'fecha_raw' => $p->fecha_hora->toISOString(),
                    ];
                })
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Token no válido'], 404);
        }
    }
}
