<?php
// app/Http/Controllers/CamionController.php

namespace App\Http\Controllers;

use App\Models\Cwcamion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CamionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Obtenemos el término de búsqueda si existe
        $search  = $request->get('search');
        $tipo    = $request->get('tipo'); // Filtro por tipo (propio/alquilado)
        $estado  = $request->get('estado'); // Filtro por activo/inactivo
        $trashed = $request->get('trashed', 'without'); // nuevo filtro: with, without, only

        // Construimos la consulta
        $query = Cwcamion::query();

        // Aplicar filtros
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('marca', 'like', "%{$search}%")
                    ->orWhere('modelo', 'like', "%{$search}%")
                    ->orWhere('placa', 'like', "%{$search}%");
            });
        }

        // Aplicar filtro de eliminados
        if ($trashed === 'only') {
            $query->onlyTrashed(); // Solo eliminados
        } elseif ($trashed === 'with') {
            $query->withTrashed(); // Incluir eliminados
        } // else: 'without' solo no eliminados (por defecto)

        if ($tipo && $tipo != 'todos') {
            $query->where('tipo', $tipo);
        }

        if ($estado && $estado != 'todos') {
            $query->where('activo', $estado === 'activo');
        }

        // Ordenar y paginar
        $camiones = $query->orderBy('created_at', 'desc')->paginate(10);

        // Mantener los filtros en la paginación
        $camiones->appends($request->all());

        return view('camiones.index', compact('camiones', 'search', 'tipo', 'estado', 'trashed'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('camiones.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validación de los datos
        $validator = Validator::make($request->all(), [
            'marca'           => 'required|string|max:100',
            'modelo'          => 'required|string|max:100',
            'placa'           => 'required|string|max:20|unique:cwcamiones,placa',
            'capacidad_motos' => 'required|integer|min:1',
            'tipo'            => 'required|in:propio,alquilado',
            'costo_alquiler'  => 'nullable|required_if:tipo,alquilado|numeric|min:0',
            'notas'           => 'nullable|string',
            'activo'          => 'sometimes|boolean',
        ], [
            'placa.unique'               => 'Ya existe un camión con esta placa.',
            'costo_alquiler.required_if' => 'El costo de alquiler es obligatorio para camiones alquilados.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Crear el camión
        $camion = Cwcamion::create([
            'marca'           => $request->marca,
            'modelo'          => $request->modelo,
            'placa'           => strtoupper($request->placa), // Guardamos la placa en mayúsculas
            'capacidad_motos' => $request->capacidad_motos,
            'tipo'            => $request->tipo,
            'costo_alquiler'  => $request->tipo === 'alquilado' ? $request->costo_alquiler : null,
            'notas'           => $request->notas,
            'activo'          => $request->has('activo') ? true : false,
        ]);

        return redirect()->route('camiones.index')
            ->with('success', "Camión {$camion->marca} {$camion->modelo} con placa {$camion->placa} creado exitosamente.");
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $camion = Cwcamion::find($id);

        // Cargar los viajes del camión para mostrarlos en el detalle
        $camion->load(['viajes' => function ($query) {
            $query->latest()->limit(10); // Últimos 10 viajes
        }]);

        // Estadísticas del camión
        $estadisticas = [
            'total_viajes' => $camion->viajes()->count(),
            'viajes_completados' => $camion->viajes()->where('estado', 'completado')->count(),
            'total_motos_transportadas' => $camion->viajes()
                ->with('motosTransportadas')
                ->get()
                ->sum(function ($viaje) {
                    return $viaje->motosTransportadas->sum('cantidad');
                }),
            'ultimo_viaje' => $camion->viajes()->latest()->first(),
        ];

        return view('camiones.show', compact('camion', 'estadisticas'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $camion = Cwcamion::find($id);

        return view('camiones.edit', compact('camion'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $camion = Cwcamion::find($id);

        // Validación de los datos
        $validator = Validator::make($request->all(), [
            'marca'           => 'required|string|max:100',
            'modelo'          => 'required|string|max:100',
            'placa'           => 'required|string|max:20|unique:cwcamiones,placa,' . $camion->id,
            'capacidad_motos' => 'required|integer|min:1|max:1000',
            'tipo'            => 'required|in:propio,alquilado',
            'costo_alquiler'  => 'nullable|required_if:tipo,alquilado|numeric|min:0',
            'notas'           => 'nullable|string',
            'activo'          => 'sometimes|boolean',
        ], [
            'placa.unique'               => 'Ya existe otro camión con esta placa.',
            'costo_alquiler.required_if' => 'El costo de alquiler es obligatorio para camiones alquilados.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Actualizar el camión
        $camion->update([
            'marca' => $request->marca,
            'modelo' => $request->modelo,
            'placa' => strtoupper($request->placa),
            'capacidad_motos' => substr($request->capacidad_motos,0,10),
            'tipo' => $request->tipo,
            'costo_alquiler' => $request->tipo === 'alquilado' ? $request->costo_alquiler : null,
            'notas' => $request->notas,
            'activo' => $request->has('activo') ? true : false,
        ]);

        return redirect()->route('camiones.show', $camion)
            ->with('success', "Camión actualizado exitosamente.");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $camion = Cwcamion::find($id);

        // Verificar si tiene viajes activos (opcional, puedes permitir eliminar igual)
        $viajesActivos = $camion->viajes()
            ->whereIn('estado', ['en_curso', 'planeado'])
            ->exists();

        if ($viajesActivos) {
            return redirect()->route('camiones.index')
                ->with('error', 'No se puede ocultar el camión porque tiene viajes en curso o planeados.');
        }

        // Soft delete: oculta el registro
        $camion->delete();

        return redirect()->route('camiones.index')
            ->with('success', "Camión con placa {$camion->placa} ocultado correctamente.");
    }

    public function restore($id)
    {
        $camion = Cwcamion::withTrashed()->findOrFail($id);
        $camion->restore();

        return redirect()->route('camiones.index')
            ->with('success', "Camión con placa {$camion->placa} restaurado correctamente.");
    }

    public function forceDelete($id)
    {
        $camion = Cwcamion::withTrashed()->findOrFail($id);

        // Verificar si tiene viajes asociados
        if ($camion->viajes()->count() > 0) {
            return redirect()->route('camiones.index', ['trashed' => 'only'])
                ->with('error', 'No se puede eliminar permanentemente porque tiene viajes asociados.');
        }

        $placa = $camion->placa;
        $camion->forceDelete(); // Eliminación permanente

        return redirect()->route('camiones.index', ['trashed' => 'only'])
            ->with('success', "Camión con placa {$placa} eliminado permanentemente.");
    }

    // ... resto de métodos (create, store, show, edit, update, toggleActivo)


    /**
     * Toggle the active status of the specified resource.
     */
    public function toggleActivo(Cwcamion $camion)
    {
        $camion->activo = !$camion->activo;
        $camion->save();

        $estado = $camion->activo ? 'activado' : 'desactivado';

        return redirect()->back()
            ->with('success', "Camión {$estado} correctamente.");
    }
}
