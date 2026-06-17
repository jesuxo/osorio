<?php
// app/Http/Controllers/GastoController.php

namespace App\Http\Controllers;

use App\Models\Cwcamion;
use App\Models\Cwchofer;
use App\Models\Cwgasto;
use App\Models\Cwtipogasto;
use App\Models\Cwviaje;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ViajegastoController extends Controller
{
    public function index(Request $request)
    {
        $query = Cwgasto::with(['tipoGasto', 'gastable']);

        // Filtros
        if ($request->filled('fecha_inicio')) {
            $query->whereDate('fecha_gasto', '>=', $request->fecha_inicio);
        }

        if ($request->filled('fecha_fin')) {
            $query->whereDate('fecha_gasto', '<=', $request->fecha_fin);
        }

        if ($request->filled('tipo_gasto_id')) {
            $query->where('tipo_gasto_id', $request->tipo_gasto_id);
        }

        if ($request->filled('entidad_tipo')) {
            $query->where('gastable_type', 'App\\Models\\' . $request->entidad_tipo);
        }

        $gastos = $query->latest('fecha_gasto')->paginate(20);
        $tiposGasto = Cwtipogasto::activos()->get();

        // Totales para el resumen
        $totales = [
            'total' => $gastos->sum('monto'),
            'por_tipo' => $gastos->groupBy('tipoGasto.nombre')->map->sum('monto')
        ];

        return view('gastos.index', compact('gastos', 'tiposGasto', 'totales'));
    }

    public function create(Request $request)
    {
        $tiposGasto = Cwtipogasto::activos()->get();

        // Si viene un viaje_id, pre-seleccionamos esa entidad
        $viajeId  = $request->get('viaje_id');
        $camionId = $request->get('camion_id');
        $choferId = $request->get('chofer_id');

        $viaje  = $viajeId ? Cwviaje::find($viajeId) : null;
        $camion = $camionId ? Cwcamion::find($camionId) : null;
        $chofer = $choferId ? Cwchofer::find($choferId) : null;

        return view('gastos.create', compact('tiposGasto', 'viaje', 'camion', 'chofer'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tipo_gasto_id'       => 'required|exists:tipo_gastos,id',
            'concepto'            => 'required|string|max:255',
            'descripcion'         => 'nullable|string',
            'monto'               => 'required|numeric|min:0',
            'fecha_gasto'         => 'required|date',
            'gastable_type'       => 'required|string|in:viaje,camion,chofer',
            'gastable_id'         => 'required|integer',
            'proveedor'           => 'nullable|string|max:255',
            'metodo_pago'         => 'nullable|string|max:50',
            'referencia_pago'     => 'nullable|string|max:100',
            'deducible_impuestos' => 'boolean',
            'comprobante'         => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048'
        ]);

        // Convertir el tipo a nombre completo de clase
        $modelMap = [
            'viaje'  => Cwviaje::class,
            'camion' => Cwcamion::class,
            'chofer' => Cwchofer::class,
        ];

        $validated['gastable_type'] = $modelMap[$validated['gastable_type']];

        // Subir comprobante si existe
        if ($request->hasFile('comprobante')) {
            $path = $request->file('comprobante')->store('comprobantes', 'public');
            $validated['comprobante'] = $path;
        }

        $validated['registrado_por'] = auth()->id();

        Cwgasto::create($validated);

        $redirectUrl = $request->input('redirect_to', route('gastos.index'));

        return redirect($redirectUrl)->with('success', 'Gasto registrado correctamente.');
    }

    public function show(Cwgasto $gasto)
    {
        $gasto->load(['tipoGasto', 'gastable', 'registrador']);
        return view('gastos.show', compact('gasto'));
    }

    public function edit(Cwgasto $gasto)
    {
        $tiposGasto = Cwtipogasto::activos()->get();
        return view('gastos.edit', compact('gasto', 'tiposGasto'));
    }

    public function update(Request $request, Cwgasto $gasto)
    {
        $validated = $request->validate([
            'tipo_gasto_id'       => 'required|exists:tipo_gastos,id',
            'concepto'            => 'required|string|max:255',
            'descripcion'         => 'nullable|string',
            'monto'               => 'required|numeric|min:0',
            'fecha_gasto'         => 'required|date',
            'proveedor'           => 'nullable|string|max:255',
            'metodo_pago'         => 'nullable|string|max:50',
            'referencia_pago'     => 'nullable|string|max:100',
            'deducible_impuestos' => 'boolean',
        ]);

        // No permitimos cambiar la entidad asociada (gastable)

        $gasto->update($validated);

        return redirect()->route('gastos.show', $gasto)->with('success', 'Gasto actualizado correctamente.');
    }

    public function destroy(Cwgasto $gasto)
    {
        $gasto->delete();
        return redirect()->route('gastos.index')->with('success', 'Gasto eliminado correctamente.');
    }

    // Método para reporte de gastos
    public function reporte(Request $request)
    {
        $fechaInicio = $request->get('fecha_inicio', now()->startOfMonth());
        $fechaFin = $request->get('fecha_fin', now()->endOfMonth());

        // Gastos agrupados por tipo
        $gastosPorTipo = Cwgasto::with('tipoGasto')
            ->whereBetween('fecha_gasto', [$fechaInicio, $fechaFin])
            ->get()
            ->groupBy('tipoGasto.nombre')
            ->map(function ($gastos) {
                return [
                    'total' => $gastos->sum('monto'),
                    'cantidad' => $gastos->count(),
                    'porcentaje' => 0 // Calcularemos después
                ];
            });

        // Gastos por entidad (viajes, camiones, choferes)
        $gastosPorEntidad = Cwgasto::whereBetween('fecha_gasto', [$fechaInicio, $fechaFin])
            ->get()
            ->groupBy('gastable_type')
            ->map(function ($gastos, $tipo) {
                $nombreTipo = class_basename($tipo);
                return [
                    'entidad' => $nombreTipo,
                    'total' => $gastos->sum('monto'),
                    'cantidad' => $gastos->count()
                ];
            });

        // Calcular porcentajes para gastos por tipo
        $totalGeneral  = $gastosPorTipo->sum('total');
        $gastosPorTipo = $gastosPorTipo->map(function ($item) use ($totalGeneral) {
            $item['porcentaje'] = $totalGeneral > 0 ? round(($item['total'] / $totalGeneral) * 100, 2) : 0;
            return $item;
        });

        return view('gastos.reporte', compact(
            'fechaInicio', 'fechaFin', 'gastosPorTipo', 'gastosPorEntidad', 'totalGeneral'
        ));
    }
}
