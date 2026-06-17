<?php
// app/Http/Controllers/ReporteGastosController.php

namespace App\Http\Controllers;

use App\Models\Cwgasto;
use App\Models\Cwviaje;
use App\Models\Cwcamion;
use App\Models\Cwchofer;
use App\Models\Cwtipogasto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReporteGastosController extends Controller
{
    /**
     * Página principal de reportes
     */
    public function index()
    {
        $tiposGasto = Cwtipogasto::where('activo', true)->orderBy('nombre')->get();
        $camiones = Cwcamion::orderBy('placa')->get();
        $choferes = Cwchofer::orderBy('nombre')->get();

        return view('reportes.gastos.index', compact('tiposGasto', 'camiones', 'choferes'));
    }

    /**
     * Obtener datos para el reporte (API)
     */
    public function datos(Request $request)
    {
        $fechaInicio = $request->get('fecha_inicio', now()->startOfMonth());
        $fechaFin = $request->get('fecha_fin', now()->endOfMonth());
        $tipoGastoId = $request->get('tipo_gasto_id');
        $camionId = $request->get('camion_id');
        $choferId = $request->get('chofer_id');
        $viajeId = $request->get('viaje_id');
        $agruparPor = $request->get('agrupar_por', 'tipo');

        // Consulta base
        $query = Cwgasto::with(['tipoGasto', 'gastable'])
            ->whereBetween('fecha_gasto', [$fechaInicio, $fechaFin]);

        if ($tipoGastoId) {
            $query->where('tipo_gasto_id', $tipoGastoId);
        }

        if ($viajeId) {
            $query->where('gastable_id', $viajeId)
                ->where('gastable_type', Cwviaje::class);
        }

        // Filtros por entidad
        if ($camionId) {
            $query->whereHasMorph('gastable', [Cwviaje::class, Cwcamion::class], function($q, $type) use ($camionId) {
                if ($type === Cwviaje::class) {
                    $q->where('camion_id', $camionId);
                } elseif ($type === Cwcamion::class) {
                    $q->where('id', $camionId);
                }
            });
        }

        if ($choferId) {
            $query->whereHasMorph('gastable', [Cwviaje::class], function($q) use ($choferId) {
                $q->where('chofer_id', $choferId);
            });
        }

        $gastos = $query->get();

        // Resumen general
        $resumen = [
            'total_gastos' => $gastos->count(),
            'monto_total' => $gastos->sum('monto'),
            'monto_promedio' => $gastos->avg('monto'),
            'monto_minimo' => $gastos->min('monto'),
            'monto_maximo' => $gastos->max('monto'),
            'periodo' => [
                'inicio' => Carbon::parse($fechaInicio)->format('d/m/Y'),
                'fin' => Carbon::parse($fechaFin)->format('d/m/Y'),
            ]
        ];

        $total = $resumen['monto_total'];

        // Gastos por tipo
        $gastosPorTipo = $gastos->groupBy('tipoGasto.nombre')
            ->map(function($items) use ($total) {
                $totalTipo = $items->sum('monto');
                return [
                    'total' => $totalTipo,
                    'cantidad' => $items->count(),
                    'porcentaje' => $total > 0 ? round(($totalTipo / $total) * 100, 2) : 0
                ];
            })
            ->sortByDesc('total')
            ->toArray();

        // Gastos por día
        $gastosPorDia = $gastos->groupBy(function($gasto) {
            return $gasto->fecha_gasto->format('Y-m-d');
        })->map(function($items, $fecha) {
            return [
                'fecha' => $fecha,
                'fecha_formateada' => Carbon::parse($fecha)->format('d/m/Y'),
                'total' => $items->sum('monto'),
                'cantidad' => $items->count()
            ];
        })->sortKeys()->values()->toArray();

        // Gastos por viaje
        $gastosPorViaje = $gastos->filter(function($gasto) {
            return $gasto->gastable_type === Cwviaje::class;
        })->groupBy('gastable_id')
            ->map(function($items) {
                $viaje = $items->first()->gastable;
                return [
                    'id' => $viaje->id ?? null,
                    'folio' => $viaje->folio ?? 'N/A',
                    'ruta' => $viaje ? ($viaje->origen . ' → ' . $viaje->destino) : 'N/A',
                    'total' => $items->sum('monto'),
                    'cantidad' => $items->count()
                ];
            })->sortByDesc('total')
            ->take(10)
            ->values()
            ->toArray();

        // Top 10 gastos más grandes
        $topGastos = $gastos->sortByDesc('monto')
            ->take(10)
            ->values()
            ->map(function($gasto) {
                $descripcion = '';

                if ($gasto->gastable) {
                    if ($gasto->gastable_type === Cwviaje::class) {
                        $descripcion = 'Viaje: ' . ($gasto->gastable->folio ?? '#' . $gasto->gastable->id);
                    } elseif ($gasto->gastable_type === Cwcamion::class) {
                        $descripcion = 'Camión: ' . ($gasto->gastable->placa ?? 'N/A');
                    } elseif ($gasto->gastable_type === Cwchofer::class) {
                        $descripcion = 'Chofer: ' . ($gasto->gastable->nombre_completo ?? 'N/A');
                    }
                }

                return [
                    'id' => $gasto->id,
                    'fecha' => $gasto->fecha_gasto->format('d/m/Y'),
                    'tipo' => $gasto->tipoGasto->nombre ?? 'N/A',
                    'concepto' => $gasto->concepto,
                    'monto' => $gasto->monto,
                    'entidad' => $descripcion,
                    'proveedor' => $gasto->proveedor,
                ];
            })
            ->toArray(); // toArray() final

        // Gastos por mes
        $gastosPorMes = $gastos->groupBy(function($gasto) {
            return $gasto->fecha_gasto->format('Y-m');
        })->map(function($items, $mes) {
            return [
                'mes' => $mes,
                'nombre' => Carbon::parse($mes . '-01')->format('M Y'),
                'total' => $items->sum('monto'),
                'cantidad' => $items->count()
            ];
        })->sortKeys()->values()->toArray();

        return response()->json([
            'success' => true,
            'resumen' => $resumen,
            'por_tipo' => $gastosPorTipo,
            'por_dia' => $gastosPorDia,
            'por_viaje' => $gastosPorViaje,
            'por_mes' => $gastosPorMes,
            'top_gastos' => $topGastos,
        ]);
    }

    /**
     * Exportar reporte a PDF
     */
    public function exportarPdf(Request $request)
    {
        $data = $this->obtenerDatosExportacion($request);
        $pdf = \PDF::loadView('reportes.gastos.pdf', $data);

        return $pdf->download('reporte-gastos-' . now()->format('Y-m-d') . '.pdf');
    }

    /**
     * Exportar reporte a Excel
     */
    public function exportarExcel(Request $request)
    {
        $data = $this->obtenerDatosExportacion($request);

        // Generar CSV
        $filename = 'reporte-gastos-' . now()->format('Y-m-d') . '.csv';
        $handle = fopen('php://temp', 'w+');

        // Headers
        fputcsv($handle, ['Fecha', 'Tipo', 'Concepto', 'Monto', 'Entidad', 'Proveedor']);

        foreach ($data['gastos'] as $gasto) {
            fputcsv($handle, [
                $gasto->fecha_gasto->format('d/m/Y'),
                $gasto->tipoGasto->nombre ?? 'N/A',
                $gasto->concepto,
                $gasto->monto,
                $data['entidades'][$gasto->id] ?? 'N/A',
                $gasto->proveedor ?? 'N/A',
            ]);
        }

        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);

        return response($content)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    private function obtenerDatosExportacion(Request $request)
    {
        $fechaInicio = $request->get('fecha_inicio', now()->startOfMonth());
        $fechaFin = $request->get('fecha_fin', now()->endOfMonth());

        $gastos = Cwgasto::with(['tipoGasto', 'gastable'])
            ->whereBetween('fecha_gasto', [$fechaInicio, $fechaFin])
            ->orderBy('fecha_gasto')
            ->get();

        $entidades = [];
        foreach ($gastos as $gasto) {
            if ($gasto->gastable) {
                if ($gasto->gastable_type === Cwviaje::class) {
                    $entidades[$gasto->id] = 'Viaje: ' . ($gasto->gastable->folio ?? '#' . $gasto->gastable->id);
                } elseif ($gasto->gastable_type === Cwcamion::class) {
                    $entidades[$gasto->id] = 'Camión: ' . ($gasto->gastable->placa ?? 'N/A');
                } elseif ($gasto->gastable_type === Cwchofer::class) {
                    $entidades[$gasto->id] = 'Chofer: ' . ($gasto->gastable->nombre_completo ?? 'N/A');
                }
            }
        }

        return [
            'gastos' => $gastos,
            'entidades' => $entidades,
            'fecha_inicio' => Carbon::parse($fechaInicio)->format('d/m/Y'),
            'fecha_fin' => Carbon::parse($fechaFin)->format('d/m/Y'),
        ];
    }
}
