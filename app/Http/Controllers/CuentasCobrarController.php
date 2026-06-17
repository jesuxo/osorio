<?php
// app/Http/Controllers/CuentasCobrarController.php

namespace App\Http\Controllers;

use App\Models\Cxchistorial;
use App\Models\Saclie;
use App\Models\Cwviajemoto;
use App\Models\Cwviaje;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CuentasCobrarController extends Controller
{
    /**
     * Página principal de cuentas por cobrar (excluyendo V15184480)
     */

    public function index(Request $request)
    {
        $clienteId = $request->get('cliente');
        $estado = $request->get('estado', 'pendientes');
        $fechaInicio = $request->get('fecha_inicio');
        $fechaFin = $request->get('fecha_fin');

        // Obtener todos los clientes para el filtro (EXCLUYENDO V15184480)
        $clientes = Saclie::where('transporte', 1)
            ->where('codclie', '!=', 'V15184480') // 🚫 EXCLUIDO
            ->orderBy('descrip')
            ->get(['codclie', 'descrip']);

        // Query base (EXCLUYENDO V15184480)
        $query = Cwviajemoto::with(['viaje', 'cliente'])
            ->whereHas('viaje', function($q) {
                $q->whereNotNull('id');
            })
            ->where('cliente_codclie', '!=', 'V15184480'); // 🚫 EXCLUIDO

        // Aplicar filtros
        if ($clienteId && $clienteId !== 'todos') {
            $query->where('cliente_codclie', $clienteId);
        }

        if ($estado === 'pendientes') {
            $query->where('facturado', false);
        } elseif ($estado === 'facturadas') {
            $query->where('facturado', true);
        }

        if ($fechaInicio) {
            $query->whereHas('viaje', function($q) use ($fechaInicio) {
                $q->whereDate('fecha_inicio', '>=', $fechaInicio);
            });
        }

        if ($fechaFin) {
            $query->whereHas('viaje', function($q) use ($fechaFin) {
                $q->whereDate('fecha_inicio', '<=', $fechaFin);
            });
        }

        // Obtener resultados paginados
        $motosPendientes = $query->orderBy('created_at', 'desc')->paginate(20);

        // Calcular totales (EXCLUYENDO V15184480)
        $totales = [
            'pendiente' => Cwviajemoto::where('facturado', false)
                ->where('cliente_codclie', '!=', 'V15184480') // 🚫 EXCLUIDO
                ->sum(DB::raw('cantidad * precio_por_moto')),

            'facturado' => Cwviajemoto::where('facturado', true)
                ->where('cliente_codclie', '!=', 'V15184480') // 🚫 EXCLUIDO
                ->sum(DB::raw('cantidad * precio_por_moto')),

            'total_motos_pendientes' => Cwviajemoto::where('facturado', false)
                ->where('cliente_codclie', '!=', 'V15184480') // 🚫 EXCLUIDO
                ->sum('cantidad'),

            'total_motos_facturadas' => Cwviajemoto::where('facturado', true)
                ->where('cliente_codclie', '!=', 'V15184480') // 🚫 EXCLUIDO
                ->sum('cantidad'),
        ];

        // Resumen por cliente (solo pendientes, EXCLUYENDO V15184480)
        $resumenClientes = Cwviajemoto::with('cliente')
            ->where('facturado', false)
            ->where('cliente_codclie', '!=', 'V15184480') // 🚫 EXCLUIDO
            ->get()
            ->groupBy('cliente_codclie')
            ->map(function($items, $codclie) {
                $cliente = $items->first()->cliente;
                return [
                    'codclie' => $codclie,
                    'cliente' => $cliente ? $cliente->descrip : 'Sin cliente',
                    'total_motos' => $items->sum('cantidad'),
                    'total_pagar' => $items->sum(function($m) {
                        return $m->cantidad * $m->precio_por_moto;
                    }),
                    'cantidad_registros' => $items->count()
                ];
            })->sortByDesc('total_pagar');

            return view('viajes.cxc.index', compact(
            'motosPendientes',
            'clientes',
            'totales',
            'resumenClientes',
            'clienteId',
            'estado',
            'fechaInicio',
            'fechaFin'
        ));
    }

    /**
     * Facturar una moto específica (con validación)
     */
    public function facturarMoto($id)
    {
        $moto = Cwviajemoto::findOrFail($id);

        if ($moto->cliente_codclie === 'V15184480') {
            return response()->json([
                'success' => false,
                'message' => 'Este cliente no requiere facturación'
            ], 422);
        }

        if ($moto->facturado) {
            return response()->json([
                'success' => false,
                'message' => 'Esta moto ya está facturada'
            ], 422);
        }

        DB::beginTransaction();
        try {
            $moto->update([
                'facturado' => true,
                'fecha_facturacion' => now()
            ]);

            // 📝 REGISTRAR EN EL HISTORIAL
            Cxchistorial::create([
                'cliente_codclie' => $moto->cliente_codclie,
                'viaje_moto_id' => $moto->id,
                'viaje_id' => $moto->viaje_id,
                'modelo_moto' => $moto->modelo_moto,
                'cantidad' => $moto->cantidad,
                'monto' => $moto->cantidad * $moto->precio_por_moto,
                'tipo' => 'cobro',
                'fecha_hora' => now(),
                'usuario_id' => auth()->id(),
                'observaciones' => 'Facturación manual desde módulo CxC'
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Moto facturada correctamente',
                'moto' => $moto
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al facturar: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Facturar todas las motos pendientes de un cliente (con validación)
     */
    public function facturarCliente($codclie)
    {
        if ($codclie === 'V15184480') {
            return response()->json([
                'success' => false,
                'message' => 'Este cliente no requiere facturación'
            ], 422);
        }

        $motos = Cwviajemoto::where('cliente_codclie', $codclie)
            ->where('facturado', false)
            ->get();

        if ($motos->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No hay motos pendientes para este cliente'
            ], 422);
        }

        DB::beginTransaction();
        try {
            $totalFacturado = 0;
            $cantidadMotos = 0;
            $historialEntries = [];

            foreach ($motos as $moto) {
                $moto->update([
                    'facturado' => true,
                    'fecha_facturacion' => now()
                ]);

                $monto = $moto->cantidad * $moto->precio_por_moto;
                $totalFacturado += $monto;
                $cantidadMotos += $moto->cantidad;

                // 📝 REGISTRAR CADA MOTO EN EL HISTORIAL
                $historialEntries[] = [
                    'cliente_codclie' => $moto->cliente_codclie,
                    'viaje_moto_id' => $moto->id,
                    'viaje_id' => $moto->viaje_id,
                    'modelo_moto' => $moto->modelo_moto,
                    'cantidad' => $moto->cantidad,
                    'monto' => $monto,
                    'tipo' => 'cobro',
                    'fecha_hora' => now(),
                    'usuario_id' => auth()->id(),
                    'observaciones' => 'Facturación masiva desde módulo CxC',
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }

            // Insertar todos los registros de una vez
            Cxchistorial::insert($historialEntries);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Cliente facturado correctamente. Total: $" . number_format($totalFacturado, 2),
                'total' => $totalFacturado,
                'cantidad_motos' => $cantidadMotos
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al facturar: ' . $e->getMessage()
            ], 500);
        }
    }

    public function historialCliente($codclie)
    {
        $cliente = Saclie::where('codclie', $codclie)->firstOrFail();

        $historial = Cxchistorial::with(['viaje', 'usuario'])
            ->where('cliente_codclie', $codclie)
            ->orderBy('fecha_hora', 'desc')
            ->paginate(20);

        $saldoPendiente = Cwviajemoto::where('cliente_codclie', $codclie)
            ->where('facturado', false)
            ->sum(DB::raw('cantidad * precio_por_moto'));

        $totalFacturado = Cxchistorial::where('cliente_codclie', $codclie)
            ->where('tipo', 'cobro')
            ->sum('monto');

        return view('viajes.cxc.historial-cliente', compact('cliente', 'historial', 'saldoPendiente', 'totalFacturado'));
    }

    /**
     * Revertir facturación de una moto (con validación)
     */
    public function revertirFactura($id)
    {
        $moto = Cwviajemoto::findOrFail($id);

        if ($moto->cliente_codclie === 'V15184480') {
            return response()->json([
                'success' => false,
                'message' => 'Este cliente no maneja facturación'
            ], 422);
        }

        if (!$moto->facturado) {
            return response()->json([
                'success' => false,
                'message' => 'Esta moto no está facturada'
            ], 422);
        }

        DB::beginTransaction();
        try {
            $moto->update([
                'facturado' => false,
                'fecha_facturacion' => null
            ]);

            // 📝 REGISTRAR LA REVERSIÓN EN EL HISTORIAL
            Cxchistorial::create([
                'cliente_codclie' => $moto->cliente_codclie,
                'viaje_moto_id' => $moto->id,
                'viaje_id' => $moto->viaje_id,
                'modelo_moto' => $moto->modelo_moto,
                'cantidad' => $moto->cantidad,
                'monto' => $moto->cantidad * $moto->precio_por_moto,
                'tipo' => 'reversion',
                'fecha_hora' => now(),
                'usuario_id' => auth()->id(),
                'observaciones' => 'Reversión de factura'
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Factura revertida correctamente',
                'moto' => $moto
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al revertir: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener resumen por cliente (con validación)
     */
    public function resumenCliente($codclie)
    {
        // 🚫 Validar que no sea V15184480
        if ($codclie === 'V15184480') {
            return response()->json([
                'success' => false,
                'message' => 'Cliente sin información de facturación'
            ], 404);
        }

        $motos = Cwviajemoto::with('viaje')
            ->where('cliente_codclie', $codclie)
            ->get();

        $resumen = [
            'pendientes' => [
                'cantidad' => $motos->where('facturado', false)->sum('cantidad'),
                'total' => $motos->where('facturado', false)->sum(function($m) {
                    return $m->cantidad * $m->precio_por_moto;
                })
            ],
            'facturadas' => [
                'cantidad' => $motos->where('facturado', true)->sum('cantidad'),
                'total' => $motos->where('facturado', true)->sum(function($m) {
                    return $m->cantidad * $m->precio_por_moto;
                })
            ],
            'detalle' => $motos->map(function($moto) {
                return [
                    'id' => $moto->id,
                    'viaje_folio' => $moto->viaje->folio ?? $moto->viaje_id,
                    'ruta' => $moto->viaje->origen . ' → ' . $moto->viaje->destino,
                    'fecha' => $moto->viaje->fecha_inicio->format('d/m/Y'),
                    'modelo' => $moto->modelo_moto,
                    'cantidad' => $moto->cantidad,
                    'precio' => $moto->precio_por_moto,
                    'total' => $moto->cantidad * $moto->precio_por_moto,
                    'facturado' => $moto->facturado,
                    'fecha_facturacion' => $moto->fecha_facturacion?->format('d/m/Y')
                ];
            })
        ];

        return response()->json($resumen);
    }
}
