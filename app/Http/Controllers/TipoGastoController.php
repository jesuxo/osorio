<?php
// app/Http/Controllers/TipoGastoController.php

namespace App\Http\Controllers;

use App\Models\Cwtipogasto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TipoGastoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $activo = $request->get('activo');

        $query = Cwtipogasto::query();

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                    ->orWhere('categoria', 'like', "%{$search}%")
                    ->orWhere('descripcion', 'like', "%{$search}%");
            });
        }

        if ($activo !== null) {
            $query->where('activo', $activo === 'true');
        }

        $tiposGasto = $query->orderBy('nombre')->paginate(15);

        return view('tipo-gastos.index', compact('tiposGasto'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categorias = $this->getCategorias();
        return view('tipo-gastos.create', compact('categorias'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:100|unique:cwtipogastos,nombre',
            'categoria' => 'nullable|string|max:50',
            'descripcion' => 'nullable|string',
            'activo' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        Cwtipogasto::create([
            'nombre' => $request->nombre,
            'categoria' => $request->categoria,
            'descripcion' => $request->descripcion,
            'activo' => $request->has('activo'),
        ]);

        return redirect()->route('tipo-gastos.index')
            ->with('success', 'Tipo de gasto creado exitosamente');
    }

    /**
     * Display the specified resource.
     */
    public function show(Cwtipogasto $tipoGasto)
    {
        $tipoGasto->load(['gastos' => function($q) {
            $q->latest()->limit(10);
        }]);

        $estadisticas = [
            'total_gastos' => $tipoGasto->gastos()->count(),
            'monto_total' => $tipoGasto->gastos()->sum('monto'),
            'promedio' => $tipoGasto->gastos()->avg('monto'),
            'ultimo_gasto' => $tipoGasto->gastos()->latest()->first(),
        ];

        return view('tipo-gastos.show', compact('tipoGasto', 'estadisticas'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Cwtipogasto $tipoGasto)
    {
        $categorias = $this->getCategorias();
        return view('tipo-gastos.edit', compact('tipoGasto', 'categorias'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Cwtipogasto $tipoGasto)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:100|unique:cwtipogastos,nombre,' . $tipoGasto->id,
            'categoria' => 'nullable|string|max:50',
            'descripcion' => 'nullable|string',
            'activo' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $tipoGasto->update([
            'nombre' => $request->nombre,
            'categoria' => $request->categoria,
            'descripcion' => $request->descripcion,
            'activo' => $request->has('activo'),
        ]);

        return redirect()->route('tipo-gastos.index')
            ->with('success', 'Tipo de gasto actualizado exitosamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cwtipogasto $tipoGasto)
    {
        // Verificar si tiene gastos asociados
        if ($tipoGasto->gastos()->count() > 0) {
            return redirect()->route('tipo-gastos.index')
                ->with('error', 'No se puede eliminar porque tiene gastos asociados. Puede desactivarlo en su lugar.');
        }

        $tipoGasto->delete();

        return redirect()->route('tipo-gastos.index')
            ->with('success', 'Tipo de gasto eliminado correctamente');
    }

    /**
     * Toggle active status.
     */
    public function toggleActivo(Cwtipogasto $tipoGasto)
    {
        $tipoGasto->activo = !$tipoGasto->activo;
        $tipoGasto->save();

        $estado = $tipoGasto->activo ? 'activado' : 'desactivado';

        return redirect()->back()
            ->with('success', "Tipo de gasto {$estado} correctamente");
    }

    /**
     * Get list for select dropdown (API).
     */
    public function lista(Request $request)
    {
        $query = Cwtipogasto::where('activo', true)->orderBy('nombre');

        if ($request->has('search')) {
            $query->where('nombre', 'like', "%{$request->search}%");
        }

        $tipos = $query->get(['id', 'nombre', 'categoria']);

        return response()->json($tipos);
    }

    /**
     * Get available categories.
     */
    private function getCategorias(): array
    {
        return [
            'Operativo' => 'Operativo',
            'Administrativo' => 'Administrativo',
            'Mantenimiento' => 'Mantenimiento',
            'Combustible' => 'Combustible',
            'Viaje' => 'Viaje',
            'Chofer' => 'Chofer',
            'Camión' => 'Camión',
            'Impuestos' => 'Impuestos',
            'Seguros' => 'Seguros',
            'Otros' => 'Otros',
        ];
    }
}
