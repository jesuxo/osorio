<?php
// app/Http/Controllers/EtapaController.php

namespace App\Http\Controllers;

use App\Models\CwetapaViaje;
use App\Models\Cwviaje;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class EtapaController extends Controller
{
    /**
     * Store a newly created etapa.
     */
    // app/Http/Controllers/EtapaController.php

    /**
     * Store a newly created etapa.
     */
    public function store(Request $request, $viajeId)
    {
        $viaje = Cwviaje::findOrFail($viajeId);

        $validator = Validator::make($request->all(), [
            'nombre'               => 'required|string|max:255',
            'descripcion'          => 'nullable|string',
            'ubicacion'            => 'required|string|max:255',
            'latitud'              => 'nullable|numeric|between:-90,90',
            'longitud'             => 'nullable|numeric|between:-180,180',
            'kilometraje_estimado' => 'nullable|numeric|min:0',
            //'fecha_estimada_inicio' => 'nullable|date',
            //'fecha_estimada_fin' => 'nullable|date|after_or_equal:fecha_estimada_inicio',
            //'orden' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();

        try {
            // Verificar que no exista otra etapa con el mismo orden
            $existe = $viaje->etapas()->where('orden', $request->orden)->exists();
            if ($existe) {
                // Reordenar etapas existentes
                $viaje->etapas()->where('orden', '>=', $request->orden)->increment('orden');
            }

            $etapa = $viaje->etapas()->create([
                'nombre' => $request->nombre,
                'descripcion' => $request->descripcion,
                'ubicacion' => $request->ubicacion,
                'latitud' => $request->latitud,
                'longitud' => $request->longitud,
                'kilometraje_estimado' => $request->kilometraje_estimado,
                'fecha_estimada_inicio' => $request->fecha_estimada_inicio,
                'fecha_estimada_fin' => $request->fecha_estimada_fin,
                'orden' => $request->orden,
                'estado' => 'pendiente',
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Etapa creada exitosamente',
                'etapa' => $etapa
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error al crear la etapa: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Update the specified etapa.
     */
    public function update(Request $request, $id)
    {
        $etapa = CwetapaViaje::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'orden' => 'required|integer|min:1',
            'nombre' => 'required|string|max:255',
            'ubicacion' => 'required|string|max:255',
            'kilometraje_estimado' => 'nullable|numeric|min:0',
            'fecha_estimada_inicio' => 'nullable|date',
            'fecha_estimada_fin' => 'nullable|date|after_or_equal:fecha_estimada_inicio',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Verificar si el orden cambió
        if ($etapa->orden != $request->orden) {
            // Reordenar otras etapas
            $viaje = $etapa->viaje;

            if ($request->orden < $etapa->orden) {
                // Moviendo hacia arriba
                $viaje->etapas()
                    ->whereBetween('orden', [$request->orden, $etapa->orden - 1])
                    ->increment('orden');
            } else {
                // Moviendo hacia abajo
                $viaje->etapas()
                    ->whereBetween('orden', [$etapa->orden + 1, $request->orden])
                    ->decrement('orden');
            }
        }

        $etapa->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Etapa actualizada correctamente',
            'etapa' => $etapa->fresh()
        ]);
    }

    /**
     * Change etapa estado.
     */
    public function cambiarEstado(Request $request, $id)
    {
        $etapa = CwetapaViaje::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'estado' => 'required|in:pendiente,en_curso,completado',
            'kilometraje_real' => 'nullable|numeric|min:0',
            'notas' => 'nullable|string',
            'foto_evidencia' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();

        try {
            $data = ['estado' => $request->estado];

            // Registrar fechas según el estado
            if ($request->estado === 'en_curso' && !$etapa->fecha_real_inicio) {
                $data['fecha_real_inicio'] = now();
            }

            if ($request->estado === 'completado' && !$etapa->fecha_real_fin) {
                $data['fecha_real_fin'] = now();
            }

            if ($request->has('kilometraje_real')) {
                $data['kilometraje_real'] = $request->kilometraje_real;
            }

            if ($request->has('notas')) {
                $data['notas'] = $request->notas;
            }

            // Subir foto de evidencia
            if ($request->hasFile('foto_evidencia')) {
                // Eliminar foto anterior si existe
                if ($etapa->foto_evidencia) {
                    Storage::disk('public')->delete($etapa->foto_evidencia);
                }

                $path = $request->file('foto_evidencia')->store('etapas/evidencias', 'public');
                $data['foto_evidencia'] = $path;
            }

            $etapa->update($data);

            // Si se completa la etapa, verificar si es la última
            if ($request->estado === 'completado') {
                $viaje = $etapa->viaje;
                $ultimaEtapa = $viaje->etapas()->orderBy('orden', 'desc')->first();

                if ($ultimaEtapa && $ultimaEtapa->id === $etapa->id) {
                    $viaje->update([
                        'estado' => 'completado',
                        'fecha_fin' => now(),
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Estado de etapa actualizado',
                'etapa' => $etapa->fresh(),
                'progreso' => $etapa->viaje->progreso
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error al actualizar etapa: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified etapa.
     */
    public function destroy($id)
    {
        $etapa = CwetapaViaje::findOrFail($id);

        // Verificar si la etapa ya fue completada
        if ($etapa->estado === 'completado') {
            return response()->json([
                'error' => 'No se puede eliminar una etapa completada'
            ], 422);
        }

        // Eliminar foto si existe
        if ($etapa->foto_evidencia) {
            Storage::disk('public')->delete($etapa->foto_evidencia);
        }

        $etapa->delete();

        // Reordenar etapas restantes
        $viaje = $etapa->viaje;
        $etapasRestantes = $viaje->etapas()->orderBy('orden')->get();
        foreach ($etapasRestantes as $index => $e) {
            $e->update(['orden' => $index + 1]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Etapa eliminada'
        ]);
    }

    /**
     * Upload evidence photo for etapa.
     */
    public function uploadEvidencia(Request $request, $id)
    {
        $etapa = CwetapaViaje::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'foto' => 'required|image|mimes:jpeg,png,jpg|max:5120', // 5MB max
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Eliminar foto anterior si existe
        if ($etapa->foto_evidencia) {
            Storage::disk('public')->delete($etapa->foto_evidencia);
        }

        $path = $request->file('foto')->store('etapas/evidencias', 'public');
        $etapa->update(['foto_evidencia' => $path]);

        return response()->json([
            'success' => true,
            'message' => 'Evidencia subida correctamente',
            'foto_url' => asset('storage/' . $path)
        ]);
    }

    /**
     * Reorder etapas.
     */
    public function reordenar(Request $request, $id)
    {
        $etapa = CwetapaViaje::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'orden' => 'required|array',
            'orden.*.id' => 'required|exists:cwetapas_viaje,id',
            'orden.*.orden' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();

        try {
            foreach ($request->orden as $item) {
                CwetapaViaje::where('id', $item['id'])
                    ->where('viaje_id', $viaje->id)
                    ->update(['orden' => $item['orden']]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Etapas reordenadas'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error al reordenar etapas'], 500);
        }
    }

    public function index($id)
    {
        $viaje = Cwviaje::with(['etapas' => function($q) {
            $q->orderBy('orden');
        }])->findOrFail($id);

        return response()->json([
            'success' => true,
            'etapas' => $viaje->etapas,
            'progreso' => $viaje->progreso
        ]);
    }
}
