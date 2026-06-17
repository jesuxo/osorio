<?php
// app/Http/Controllers/GastoController.php

namespace App\Http\Controllers;

use App\Models\Cwgasto;
use App\Models\Cwtipogasto;
use App\Models\Cwviaje;
use App\Models\Cwcamion;
use App\Models\Cwchofer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class GastoController extends Controller
{
    /**
     * Display a listing of gastos.
     */
    public function index(Request $request)
    {
        $query = Cwgasto::with(['tipoGasto', 'gastable', 'registrador']);

        // Filtros
        if ($request->filled('fecha_desde')) {
            $query->whereDate('fecha_gasto', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->whereDate('fecha_gasto', '<=', $request->fecha_hasta);
        }

        if ($request->filled('tipo_gasto_id')) {
            $query->where('tipo_gasto_id', $request->tipo_gasto_id);
        }

        if ($request->filled('entidad_tipo')) {
            $tipoMap = [
                'viaje' => Cwviaje::class,
                'camion' => Cwcamion::class,
                'chofer' => Cwchofer::class,
            ];

            if (isset($tipoMap[$request->entidad_tipo])) {
                $query->where('gastable_type', $tipoMap[$request->entidad_tipo]);
            }
        }

        if ($request->filled('entidad_id')) {
            $query->where('gastable_id', $request->entidad_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('concepto', 'like', "%{$search}%")
                    ->orWhere('descripcion', 'like', "%{$search}%")
                    ->orWhere('proveedor', 'like', "%{$search}%")
                    ->orWhere('referencia_pago', 'like', "%{$search}%");
            });
        }

        // Ordenamiento
        $orderBy = $request->get('order_by', 'fecha_gasto');
        $orderDir = $request->get('order_dir', 'desc');
        $query->orderBy($orderBy, $orderDir);

        $gastos = $query->paginate(20)->withQueryString();

        // Totales para resumen
        $totales = [
            'total' => $gastos->sum('monto'),
            'por_tipo' => $gastos->groupBy('tipoGasto.nombre')
                ->map(function($items) {
                    return [
                        'total' => $items->sum('monto'),
                        'cantidad' => $items->count()
                    ];
                })
        ];

        // Datos para filtros
        $tiposGasto = Cwtipogasto::where('activo', true)->orderBy('nombre')->get();

        // Estadísticas adicionales
        $estadisticas = [
            'total_periodo' => $query->count(),
            'monto_promedio' => $gastos->avg('monto'),
            'gasto_mas_alto' => $gastos->max('monto'),
            'gasto_mas_bajo' => $gastos->min('monto'),
        ];

        return view('gastos.index', compact('gastos', 'tiposGasto', 'totales', 'estadisticas'));
    }

    /**
     * Show form for creating new gasto.
     */
    public function create(Request $request)
    {
        $tiposGasto = Cwtipogasto::where('activo', true)->orderBy('nombre')->get();

        // Si viene un viaje_id, precargar esa entidad
        $viaje = null;
        $camion = null;
        $chofer = null;

        if ($request->filled('viaje_id')) {
            $viaje = Cwviaje::with(['camion', 'chofer'])->find($request->viaje_id);
        }

        if ($request->filled('camion_id')) {
            $camion = Cwcamion::find($request->camion_id);
        }

        if ($request->filled('chofer_id')) {
            $chofer = Cwchofer::find($request->chofer_id);
        }

        return view('gastos.create', compact('tiposGasto', 'viaje', 'camion', 'chofer'));
    }

    /**
     * Store a newly created gasto.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tipo_gasto_id' => 'required|exists:cwtipogastos,id',
            'concepto' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'monto' => 'required|numeric|min:0',
            'fecha_gasto' => 'required|date',
            'gastable_type' => 'required|string|in:viaje,camion,chofer',
            'gastable_id' => 'required|integer',
            'proveedor' => 'nullable|string|max:255',
            'metodo_pago' => 'nullable|string|max:50',
            'referencia_pago' => 'nullable|string|max:100',
            'deducible_impuestos' => 'boolean',
            'comprobante' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120', // 5MB
            'notas_internas' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Mapear tipo a clase completa
        $tipoMap = [
            'viaje' => Cwviaje::class,
            'camion' => Cwcamion::class,
            'chofer' => Cwchofer::class,
        ];

        $gastableType = $tipoMap[$request->gastable_type];

        // Verificar que la entidad existe
        $entidad = $gastableType::find($request->gastable_id);
        if (!$entidad) {
            return redirect()->back()
                ->with('error', 'La entidad seleccionada no existe')
                ->withInput();
        }

        DB::beginTransaction();

        try {
            $data = $request->except('comprobante');
            $data['gastable_type'] = $gastableType;
            $data['registrado_por'] = auth()->id();
            $data['deducible_impuestos'] = $request->has('deducible_impuestos');

            // Subir comprobante
            if ($request->hasFile('comprobante')) {
                $path = $request->file('comprobante')->store('gastos/comprobantes', 'public');
                $data['comprobante'] = $path;
            }

            $gasto = Cwgasto::create($data);

            DB::commit();

            $redirectUrl = $request->input('redirect_to', route('gastos.index'));

            return redirect($redirectUrl)
                ->with('success', 'Gasto registrado exitosamente. Folio: #' . $gasto->id);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error al registrar gasto: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified gasto.
     */
    public function show(Cwgasto $gasto)
    {
        $gasto->load(['tipoGasto', 'gastable', 'registrador']);

        return view('gastos.show', compact('gasto'));
    }

    /**
     * Show the form for editing the specified gasto.
     */
    public function edit(Cwgasto $gasto)
    {
        $tiposGasto = Cwtipogasto::where('activo', true)->orderBy('nombre')->get();

        return view('gastos.edit', compact('gasto', 'tiposGasto'));
    }

    /**
     * Update the specified gasto.
     */
    public function update(Request $request, Cwgasto $gasto)
    {
        $validator = Validator::make($request->all(), [
            'tipo_gasto_id' => 'required|exists:cwtipogastos,id',
            'concepto' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'monto' => 'required|numeric|min:0',
            'fecha_gasto' => 'required|date',
            'proveedor' => 'nullable|string|max:255',
            'metodo_pago' => 'nullable|string|max:50',
            'referencia_pago' => 'nullable|string|max:100',
            'deducible_impuestos' => 'boolean',
            'comprobante' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'notas_internas' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();

        try {
            $data = $request->except('comprobante');
            $data['deducible_impuestos'] = $request->has('deducible_impuestos');

            // Subir nuevo comprobante
            if ($request->hasFile('comprobante')) {
                // Eliminar comprobante anterior
                if ($gasto->comprobante) {
                    Storage::disk('public')->delete($gasto->comprobante);
                }

                $path = $request->file('comprobante')->store('gastos/comprobantes', 'public');
                $data['comprobante'] = $path;
            }

            $gasto->update($data);

            DB::commit();

            return redirect()->route('gastos.show', $gasto)
                ->with('success', 'Gasto actualizado exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error al actualizar gasto: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified gasto.
     */
    public function destroy(Cwgasto $gasto)
    {
        // Verificar permisos o reglas de negocio
        if ($gasto->created_at->diffInDays(now()) > 30) {
            return redirect()->back()
                ->with('error', 'No se pueden eliminar gastos con más de 30 días de antigüedad');
        }

        DB::beginTransaction();

        try {
            // Eliminar comprobante
            if ($gasto->comprobante) {
                Storage::disk('public')->delete($gasto->comprobante);
            }

            $gasto->delete();

            DB::commit();

            return redirect()->route('gastos.index')
                ->with('success', 'Gasto eliminado correctamente');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error al eliminar gasto: ' . $e->getMessage());
        }
    }

    /**
     * Download comprobante.
     */
    public function downloadComprobante(Cwgasto $gasto)
    {
        if (!$gasto->comprobante) {
            return redirect()->back()
                ->with('error', 'El gasto no tiene comprobante');
        }

        $path = storage_path('app/public/' . $gasto->comprobante);

        if (!file_exists($path)) {
            return redirect()->back()
                ->with('error', 'El archivo de comprobante no existe');
        }

        return response()->download($path, 'comprobante_' . $gasto->id . '.pdf');
    }

    /**
     * Reporte de gastos.
     */
    public function reporte(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'tipo_gasto_id' => 'nullable|exists:cwtipogastos,id',
            'agrupar_por' => 'nullable|in:tipo,entidad,proveedor,metodo_pago',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator);
        }

        $inicio = \Carbon\Carbon::parse($request->fecha_inicio);
        $fin = \Carbon\Carbon::parse($request->fecha_fin);
        $agruparPor = $request->get('agrupar_por', 'tipo');

        $query = Cwgasto::with(['tipoGasto', 'gastable'])
            ->whereBetween('fecha_gasto', [$inicio, $fin]);

        if ($request->filled('tipo_gasto_id')) {
            $query->where('tipo_gasto_id', $request->tipo_gasto_id);
        }

        $gastos = $query->get();

        $reporte = [
            'periodo' => [
                'inicio' => $inicio->format('d/m/Y'),
                'fin' => $fin->format('d/m/Y'),
            ],
            'resumen' => [
                'total_gastos' => $gastos->count(),
                'monto_total' => $gastos->sum('monto'),
                'monto_promedio' => $gastos->avg('monto'),
                'monto_minimo' => $gastos->min('monto'),
                'monto_maximo' => $gastos->max('monto'),
            ],
            'agrupado' => [],
        ];

        // Agrupar según criterio
        switch ($agruparPor) {
            case 'tipo':
                $reporte['agrupado'] = $gastos->groupBy('tipoGasto.nombre')
                    ->map(function($items) {
                        return [
                            'cantidad' => $items->count(),
                            'total' => $items->sum('monto'),
                            'porcentaje' => 0, // Se calculará después
                        ];
                    });
                break;

            case 'entidad':
                $reporte['agrupado'] = $gastos->groupBy(function($gasto) {
                    return class_basename($gasto->gastable_type) . ' #' . $gasto->gastable_id;
                })->map(function($items) {
                    return [
                        'cantidad' => $items->count(),
                        'total' => $items->sum('monto'),
                        'entidad' => $items->first()->gastable,
                    ];
                });
                break;

            case 'proveedor':
                $reporte['agrupado'] = $gastos->groupBy('proveedor')
                    ->map(function($items, $proveedor) {
                        return [
                            'proveedor' => $proveedor ?: 'Sin proveedor',
                            'cantidad' => $items->count(),
                            'total' => $items->sum('monto'),
                        ];
                    });
                break;

            case 'metodo_pago':
                $reporte['agrupado'] = $gastos->groupBy('metodo_pago')
                    ->map(function($items, $metodo) {
                        return [
                            'metodo' => $metodo ?: 'No especificado',
                            'cantidad' => $items->count(),
                            'total' => $items->sum('monto'),
                        ];
                    });
                break;
        }

        // Calcular porcentajes si aplica
        if ($agruparPor === 'tipo') {
            $total = $reporte['resumen']['monto_total'];
            $reporte['agrupado'] = $reporte['agrupado']->map(function($item) use ($total) {
                $item['porcentaje'] = $total > 0 ? round(($item['total'] / $total) * 100, 2) : 0;
                return $item;
            });
        }

        // Gastos por día
        $reporte['por_dia'] = $gastos->groupBy(function($gasto) {
            return $gasto->fecha_gasto->format('Y-m-d');
        })->map(function($items, $fecha) {
            return [
                'fecha' => $fecha,
                'cantidad' => $items->count(),
                'total' => $items->sum('monto'),
            ];
        })->sortKeys();

        return view('gastos.reporte', compact('reporte', 'agruparPor'));
    }

    /**
     * Export gastos to Excel.
     */
    public function exportarExcel(Request $request)
    {
        $query = Cwgasto::with(['tipoGasto', 'gastable']);

        if ($request->filled('fecha_desde')) {
            $query->whereDate('fecha_gasto', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->whereDate('fecha_gasto', '<=', $request->fecha_hasta);
        }

        if ($request->filled('tipo_gasto_id')) {
            $query->where('tipo_gasto_id', $request->tipo_gasto_id);
        }

        $gastos = $query->orderBy('fecha_gasto', 'desc')->get();

        // Generar CSV
        $filename = "gastos_" . now()->format('Y-m-d_His') . ".csv";
        $handle = fopen('php://temp', 'w+');

        // Headers
        fputcsv($handle, [
            'ID',
            'Fecha',
            'Tipo Gasto',
            'Concepto',
            'Monto',
            'Entidad',
            'Proveedor',
            'Método Pago',
            'Referencia',
            'Registrado Por',
            'Deducible',
        ]);

        // Data
        foreach ($gastos as $gasto) {
            $entidad = class_basename($gasto->gastable_type);
            $entidadId = $gasto->gastable_id;

            fputcsv($handle, [
                $gasto->id,
                $gasto->fecha_gasto->format('d/m/Y'),
                $gasto->tipoGasto->nombre ?? 'N/A',
                $gasto->concepto,
                $gasto->monto,
                "{$entidad} #{$entidadId}",
                $gasto->proveedor ?? 'N/A',
                $gasto->metodo_pago ?? 'N/A',
                $gasto->referencia_pago ?? 'N/A',
                $gasto->registrador?->name ?? 'Sistema',
                $gasto->deducible_impuestos ? 'Sí' : 'No',
            ]);
        }

        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);

        return response($content)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    /**
     * Estadísticas de gastos.
     */
    public function estadisticas(Request $request)
    {
        $anio = $request->get('anio', now()->year);

        $gastos = Cwgasto::with('tipoGasto')
            ->whereYear('fecha_gasto', $anio)
            ->get();

        $estadisticas = [
            'anio' => $anio,
            'total_anual' => $gastos->sum('monto'),
            'promedio_mensual' => $gastos->sum('monto') / 12,
            'total_gastos' => $gastos->count(),

            'por_mes' => [],
            'por_tipo' => [],
            'top_proveedores' => [],
            'tendencia' => [],
        ];

        // Inicializar meses
        for ($i = 1; $i <= 12; $i++) {
            $estadisticas['por_mes'][$i] = [
                'mes' => $i,
                'nombre' => \Carbon\Carbon::create()->month($i)->format('F'),
                'total' => 0,
                'cantidad' => 0,
            ];
        }

        foreach ($gastos as $gasto) {
            $mes = $gasto->fecha_gasto->month;

            // Por mes
            $estadisticas['por_mes'][$mes]['total'] += $gasto->monto;
            $estadisticas['por_mes'][$mes]['cantidad']++;

            // Por tipo
            $tipo = $gasto->tipoGasto->nombre ?? 'Otros';
            if (!isset($estadisticas['por_tipo'][$tipo])) {
                $estadisticas['por_tipo'][$tipo] = [
                    'total' => 0,
                    'cantidad' => 0,
                ];
            }
            $estadisticas['por_tipo'][$tipo]['total'] += $gasto->monto;
            $estadisticas['por_tipo'][$tipo]['cantidad']++;

            // Top proveedores
            if ($gasto->proveedor) {
                if (!isset($estadisticas['top_proveedores'][$gasto->proveedor])) {
                    $estadisticas['top_proveedores'][$gasto->proveedor] = 0;
                }
                $estadisticas['top_proveedores'][$gasto->proveedor] += $gasto->monto;
            }
        }

        // Ordenar top proveedores
        arsort($estadisticas['top_proveedores']);
        $estadisticas['top_proveedores'] = array_slice($estadisticas['top_proveedores'], 0, 10);

        return response()->json($estadisticas);
    }
}
