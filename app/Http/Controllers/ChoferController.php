<?php
// app/Http/Controllers/ChoferController.php

namespace App\Http\Controllers;

use App\Models\Cwchofer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ChoferController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $estado = $request->get('estado'); // activo/inactivo
        $trashed = $request->get('trashed', 'without'); // with, without, only

        $query = Cwchofer::query();

        // Filtro de eliminados
        if ($trashed === 'only') {
            $query->onlyTrashed();
        } elseif ($trashed === 'with') {
            $query->withTrashed();
        }

        // Búsqueda por término
        if ($search) {
            $query->buscar($search);
        }

        // Filtro por estado
        if ($estado && $estado != 'todos') {
            $query->where('activo', $estado === 'activo');
        }

        $choferes = $query->orderBy('created_at', 'desc')->paginate(10);
        $choferes->appends($request->all());

        // Estadísticas rápidas
        $estadisticas = [
            'total' => Cwchofer::count(),
            'activos' => Cwchofer::where('activo', true)->count(),
            'inactivos' => Cwchofer::where('activo', false)->count(),
            'ocultos' => Cwchofer::onlyTrashed()->count(),
        ];

        return view('choferes.index', compact('choferes', 'search', 'estado', 'trashed', 'estadisticas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('choferes.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'licencia' => 'required|string|max:50|unique:cwchoferes,licencia',
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255|unique:cwchoferes,email',
            'direccion' => 'nullable|string',
            'fecha_nacimiento' => 'nullable|date|before:today',
            'fecha_ingreso' => 'nullable|date',
            'tipo_sangre' => 'nullable|string|max:5',
            'contacto_emergencia_nombre' => 'nullable|string|max:255',
            'contacto_emergencia_telefono' => 'nullable|string|max:20',
            'observaciones_medicas' => 'nullable|string',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'activo' => 'sometimes|boolean',
        ], [
            'licencia.unique' => 'Ya existe un chofer con esta licencia.',
            'email.unique' => 'Ya existe un chofer con este email.',
            'foto.max' => 'La foto no debe pesar más de 2MB.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->except('foto');

        // Manejar la subida de la foto
        if ($request->hasFile('foto')) {
            $path = $request->file('foto')->store('choferes', 'public');
            $data['foto'] = $path;
        }

        $data['activo'] = $request->has('activo');

        $chofer = Cwchofer::create($data);

        return redirect()->route('choferes.index')
            ->with('success', "Chofer {$chofer->nombre_completo} creado exitosamente.");
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $chofer = Cwchofer::find($id);
        // Cargar viajes recientes
        $chofer->load(['viajes' => function ($query) {
            $query->with('camion', 'motosTransportadas')
                ->latest()
                ->limit(10);
        }]);

        // Estadísticas del chofer
        $estadisticas = [
            'total_viajes' => $chofer->getTotalViajes(),
            'viajes_completados' => $chofer->getViajesCompletados(),
            'total_motos' => $chofer->getTotalMotosTransportadas(),
            'ultimo_viaje' => $chofer->getUltimoViaje(),
            'disponible' => $chofer->estaDisponible(),
        ];

        // Resumen financiero (si hay viajes)
        $financiero = null;
        if ($chofer->viajes->count() > 0) {
            $financiero = [
                'total_ingresos' => $chofer->viajes->sum->ingreso_total,
                'total_gastos' => $chofer->viajes->sum->gasto_total,
                'total_ganancias' => $chofer->viajes->sum->ganancia_neta,
            ];
        }

        return view('choferes.show', compact('chofer', 'estadisticas', 'financiero'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $chofer = Cwchofer::find($id);
        return view('choferes.edit', compact('chofer'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $chofer = Cwchofer::find($id);

        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'licencia' => 'required|string|max:50|unique:cwchoferes,licencia,' . $chofer->id,
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255|unique:cwchoferes,email,' . $chofer->id,
            'direccion' => 'nullable|string',
            'fecha_nacimiento' => 'nullable|date|before:today',
            'fecha_ingreso' => 'nullable|date',
            'tipo_sangre' => 'nullable|string|max:5',
            'contacto_emergencia_nombre' => 'nullable|string|max:255',
            'contacto_emergencia_telefono' => 'nullable|string|max:20',
            'observaciones_medicas' => 'nullable|string',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'activo' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->except('foto');

        // Manejar la subida de la foto
        if ($request->hasFile('foto')) {
            // Eliminar foto anterior si existe
            if ($chofer->foto) {
                Storage::disk('public')->delete($chofer->foto);
            }
            $path = $request->file('foto')->store('choferes', 'public');
            $data['foto'] = $path;
        }

        $data['activo'] = $request->has('activo');

        $chofer->update($data);

        return redirect()->route('choferes.show', $chofer)
            ->with('success', "Chofer actualizado exitosamente.");
    }

    /**
     * Remove the specified resource from storage (Soft Delete).
     */
    public function destroy($id)
    {
        $chofer = Cwchofer::find($id);

        // Verificar si tiene viajes activos
        $viajesActivos = $chofer->viajes()
            ->whereIn('estado', ['en_curso', 'planeado'])
            ->exists();

        if ($viajesActivos) {
            return redirect()->route('choferes.index')
                ->with('error', 'No se puede ocultar el chofer porque tiene viajes en curso o planeados.');
        }

        $chofer->delete();

        return redirect()->route('choferes.index')
            ->with('success', "Chofer {$chofer->nombre_completo} ocultado correctamente.");
    }

    /**
     * Restore the specified soft deleted resource.
     */
    public function restore($id)
    {
        $chofer = Cwchofer::withTrashed()->findOrFail($id);
        $chofer->restore();

        return redirect()->route('choferes.index')
            ->with('success', "Chofer {$chofer->nombre_completo} restaurado correctamente.");
    }

    /**
     * Force delete the specified resource.
     */
    public function forceDelete($id)
    {
        $chofer = Cwchofer::withTrashed()->findOrFail($id);

        // Verificar si tiene viajes asociados
        if ($chofer->viajes()->count() > 0) {
            return redirect()->route('choferes.index', ['trashed' => 'only'])
                ->with('error', 'No se puede eliminar permanentemente porque tiene viajes asociados.');
        }

        // Eliminar foto si existe
        if ($chofer->foto) {
            Storage::disk('public')->delete($chofer->foto);
        }

        $nombre = $chofer->nombre_completo;
        $chofer->forceDelete();

        return redirect()->route('choferes.index', ['trashed' => 'only'])
            ->with('success', "Chofer {$nombre} eliminado permanentemente.");
    }

    /**
     * Toggle the active status.
     */
    public function toggleActivo($id)
    {
        $chofer = Cwchofer::find($id);
        $chofer->activo = !$chofer->activo;
        $chofer->save();

        $estado = $chofer->activo ? 'activado' : 'desactivado';

        return redirect()->back()
            ->with('success', "Chofer {$estado} correctamente.");
    }
}
