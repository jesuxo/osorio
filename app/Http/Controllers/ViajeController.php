<?php
// app/Http/Controllers/ViajeController.php

namespace App\Http\Controllers;

use App\Models\Cwviaje;
use App\Models\Cwcamion;
use App\Models\Cwchofer;
use App\Models\CwetapaViaje;
use App\Models\Cwviajemoto;
use App\Models\Cwgasto;
use App\Models\Cwtipogasto;
use App\Models\Saclie;
use App\Models\Saprov;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class ViajeController extends Controller
{
    /**
     * Display a listing of the resource (TODO-EN-UNO).
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $estado = $request->get('estado', 'todos');
        $fecha_desde = $request->get('fecha_desde');
        $fecha_hasta = $request->get('fecha_hasta');
        $camion_id = $request->get('camion_id');
        $chofer_id = $request->get('chofer_id');

        // Query base con relaciones
        $query = Cwviaje::with([
            'camion',
            'chofer',
            'motosTransportadas',
            'etapas' => function($q) {
                $q->orderBy('orden');
            },
            'gastos.tipoGasto'
        ])
            ->withCount(['motosTransportadas as total_motos' => function($q) {
                $q->select(DB::raw('COALESCE(SUM(cantidad), 0)'));
            }])
            ->withCount(['gastos as total_gastos' => function($q) {
                $q->select(DB::raw('COALESCE(SUM(monto), 0)'));
            }]);

        // Aplicar filtros
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('folio', 'like', "%{$search}%")
                    ->orWhere('origen', 'like', "%{$search}%")
                    ->orWhere('destino', 'like', "%{$search}%")
                    ->orWhereHas('camion', function($cq) use ($search) {
                        $cq->where('placa', 'like', "%{$search}%")
                            ->orWhere('marca', 'like', "%{$search}%");
                    })
                    ->orWhereHas('chofer', function($cq) use ($search) {
                        $cq->where(DB::raw("CONCAT(nombre, ' ', apellido)"), 'like', "%{$search}%");
                    });
            });
        }

        if ($estado && $estado != 'todos') {
            $query->where('estado', $estado);
        }

        if ($fecha_desde) {
            $query->whereDate('fecha_inicio', '>=', Carbon::parse($fecha_desde));
        }

        if ($fecha_hasta) {
            $query->whereDate('fecha_inicio', '<=', Carbon::parse($fecha_hasta));
        }

        if ($camion_id) {
            $query->where('camion_id', $camion_id);
        }

        if ($chofer_id) {
            $query->where('chofer_id', $chofer_id);
        }

        // Obtener viajes paginados
        $viajes = $query->orderBy('id', 'desc')->paginate(10);

        // Calcular ingresos y ganancias para cada viaje
        foreach ($viajes as $viaje) {
            $viaje->ingreso_total = $viaje->motosTransportadas->sum(function($moto) {
                return $moto->cantidad * $moto->precio_por_moto;
            });
            $viaje->ganancia_neta = $viaje->ingreso_total - ($viaje->total_gastos ?? 0);
            $viaje->progreso = $this->calcularProgreso($viaje);
        }

        // Datos para los filtros
        $camiones = Cwcamion::where('activo', true)->orderBy('placa')->get();
        $choferes = Cwchofer::where('activo', true)->orderBy('nombre')->get();

        // Estadísticas generales
        $estadisticas = [
            'total' => Cwviaje::count(),
            'completados' => Cwviaje::where('estado', 'completado')->count(),
            'en_curso'    => Cwviaje::where('estado', 'en_curso')->count(),
            'planeados'   => Cwviaje::where('estado', 'planeado')->count(),
            'cancelados'  => Cwviaje::where('estado', 'cancelado')->count(),
        ];

        return view('viajes.index', compact(
            'viajes',
            'search',
            'estado',
            'fecha_desde',
            'fecha_hasta',
            'camion_id',
            'chofer_id',
            'camiones',
            'choferes',
            'estadisticas'
        ));
    }

    /**
     * Show form for creating new viaje (modal)
     */
    public function create()
    {
        $camiones = Cwcamion::where('activo', true)->orderBy('placa')->get();
        $choferes = Cwchofer::where('activo', true)->orderBy('nombre')->get();

        return response()->json([
            'html' => view('viajes.partials.create-modal', compact('camiones', 'choferes'))->render()
        ]);
    }

    /**
     * Store a newly created viaje
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            //'folio' => 'nullable|string|max:50|unique:cwviajes,folio',
            'camion_id'    => 'required|exists:cwcamiones,id',
            'chofer_id'    => 'required|exists:cwchoferes,id',
            'fecha_inicio' => 'required|date',
            'fecha_fin'    => 'nullable|date|after_or_equal:fecha_inicio',
            'origen'       => 'required|string|max:255',
            'destino'      => 'required|string|max:255',
            'distancia_km' => 'nullable|numeric|min:0',
            'estado'       => 'required|in:planeado,en_curso,completado,cancelado',
            'notas'        => 'nullable|string',

            // Datos de etapas (opcional)
            'etapas'                        => 'nullable|array',
            'etapas.*.nombre'               => 'required|string',
            'etapas.*.ubicacion'            => 'required|string',
            'etapas.*.kilometraje_estimado' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();

        try {
            $folio = $this->generarFolio();

            $data = $request->except('etapas');
            $data['folio'] = $folio;

            $viaje = Cwviaje::create($data);

            // Crear etapas si se proporcionaron
            if ($request->has('etapas') && count($request->etapas) > 0) {
                foreach ($request->etapas as $orden => $etapaData) {
                    $viaje->etapas()->create([
                        'nombre' => $etapaData['nombre'],
                        'ubicacion' => $etapaData['ubicacion'],
                        'kilometraje_estimado' => $etapaData['kilometraje_estimado'] ?? null,
                        'orden' => $orden + 1,
                        'estado' => 'pendiente',
                    ]);
                }
            } else {
                // Crear etapas básicas por defecto
                $viaje->etapas()->createMany([
                    [
                        'nombre' => 'Inicio Viaje',
                        'ubicacion' => "ubicacion",
                        'orden'  => 1,
                        'estado' => 'pendiente',
                    ],
                    [
                        'nombre' => 'Carga de motos',
                        'ubicacion' => $request->origen,
                        'orden' => 2,
                        'estado' => 'pendiente',
                    ],
                    [
                        'nombre' => 'Descarga de motos',
                        'ubicacion' => $request->destino,
                        'orden' => 3,
                        'estado' => 'pendiente',
                    ],
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Viaje  {$folio}  creado exitosamente",
                'viaje' => $viaje->load(['camion', 'chofer', 'etapas'])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error al crear el viaje: ' . $e->getMessage()], 500);
        }
    }

    private function generarFolio(): string
    {
        $anio = date('Y');
        $mes = date('m');

        // Contar viajes del mes actual
        $count = Cwviaje::whereYear('created_at', $anio)
                ->whereMonth('created_at', $mes)
                ->count() + 1;

        $folio = sprintf('VIA-%s-%s-%03d', $anio, $mes, $count);

        return $folio;
    }

    // app/Http/Controllers/ViajeController.php

    /**
     * Update a moto in viaje.
     */
    // app/Http/Controllers/ViajeController.php

    /**
     * Update a moto in viaje.
     */
    public function updateMoto(Request $request, $id, $motoId)
    {
        $viaje = Cwviaje::findOrFail($id);
        $moto = Cwviajemoto::where('viaje_id', $viaje->id)
            ->where('id', $motoId)
            ->firstOrFail();

        $validator = Validator::make($request->all(), [
            'cliente_codclie' => 'required|string|exists:saclie,codclie',
            'modelo_moto' => 'required|string|max:255',
            'cantidad' => 'required|integer|min:1',
            'precio_por_moto' => 'required|numeric|min:0',
            'proveedor_paga' => 'nullable|boolean',
            'proveedor_codprov' => 'nullable|string|exists:saprov,codprov',
            'monto_transporte_proveedor' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();

        try {
            $data = [
                'cliente_codclie' => $request->cliente_codclie,
                'modelo_moto' => $request->modelo_moto,
                'cantidad' => $request->cantidad,
                'precio_por_moto' => $request->precio_por_moto,
            ];

            // Manejar proveedor - IMPORTANTE: Verificar si se marca o desmarca
            if ($request->has('proveedor_paga') && $request->proveedor_paga) {
                $transporte = $request->monto_transporte_proveedor ?? 0;
                $retencion = $transporte * 0.3; // 30%
                $descuento = $transporte - $retencion;

                $data['proveedor_paga'] = true;
                $data['proveedor_codprov'] = $request->proveedor_codprov;
                $data['monto_transporte_proveedor'] = $transporte;
                $data['retencion_proveedor'] = $retencion;
                $data['descuento_aplicado_cliente'] = $descuento;
                $data['monto_esperado_cliente'] = $descuento;
                $data['estado_conciliacion'] = 'pendiente';
            } else {
                // Si se desmarca, limpiar todos los campos de proveedor
                $data['proveedor_paga'] = false;
                $data['proveedor_codprov'] = null;
                $data['monto_transporte_proveedor'] = null;
                $data['retencion_proveedor'] = null;
                $data['descuento_aplicado_cliente'] = null;
                $data['monto_esperado_cliente'] = null;
                $data['estado_conciliacion'] = null;
            }

            $moto->update($data);

            DB::commit();

            // Cargar relaciones para devolver datos completos
            $moto->load(['cliente', 'proveedor']);

            return response()->json([
                'success' => true,
                'message' => 'Moto actualizada correctamente',
                'moto' => $moto
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error al actualizar: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Show motos admin view.
     */
    public function adminMotos($id)
    {
        $viaje = Cwviaje::with('motosTransportadas.cliente')->findOrFail($id);

        // Obtener clientes activos de saclie
        $clientes = Saclie::where('transporte', 1)->orderBy('descrip')->get(['codclie', 'descrip']);

        $proveedores = Saprov::where('activo', 1)->orderBy('descrip')->get(['codprov', 'descrip']);

        $html = view('viajes.partials.motos-admin-modal', compact('viaje', 'clientes', 'proveedores'))->render();

        return response()->json([
            'success' => true,
            'html' => $html
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $viaje = Cwviaje::find($id);
        $viaje->load([
            'camion',
            'chofer',
            'motosTransportadas',
            'etapas' => function($q) { $q->orderBy('orden'); },
            'gastos.tipoGasto',
            'gastos.registrador'
        ]);

        // Calcular ingresos y gastos
        $viaje->ingreso_total = $viaje->motosTransportadas->sum(function($moto) {
            return $moto->cantidad * $moto->precio_por_moto;
        });

        $viaje->gasto_total = $viaje->gastos->sum('monto');
        $viaje->ganancia_neta = $viaje->ingreso_total - $viaje->gasto_total;

        // Agrupar gastos por tipo
        $gastosPorTipo = $viaje->gastos->groupBy('tipoGasto.nombre')->map(function($gastos) {
            return [
                'total' => $gastos->sum('monto'),
                'cantidad' => $gastos->count()
            ];
        });

        // Progreso del viaje
        $progreso = [
            'total' => $viaje->etapas->count(),
            'completadas' => $viaje->etapas->where('estado', 'completado')->count(),
            'en_curso' => $viaje->etapas->where('estado', 'en_curso')->count(),
            'pendientes' => $viaje->etapas->where('estado', 'pendiente')->count(),
            'porcentaje' => $this->calcularProgreso($viaje)
        ];

        return response()->json([
            'html' => view('viajes.partials.show-modal', compact('viaje', 'gastosPorTipo', 'progreso'))->render()
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $viaje = Cwviaje::find($id);

        $viaje->load(['camion', 'chofer', 'etapas' => function($q) { $q->orderBy('orden'); }]);
        $camiones = Cwcamion::where('activo', true)->orderBy('placa')->get();
        $choferes = Cwchofer::where('activo', true)->orderBy('nombre')->get();

        return response()->json([
            'html' => view('viajes.partials.edit-modal', compact('viaje', 'camiones', 'choferes'))->render()
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $viaje = Cwviaje::find($id);

        $validator = Validator::make($request->all(), [
            'folio' => 'nullable|string|max:50|unique:cwviajes,folio,' . $viaje->id,
            'camion_id' => 'required|exists:cwcamiones,id',
            'chofer_id' => 'required|exists:cwchoferes,id',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'origen' => 'required|string|max:255',
            'destino' => 'required|string|max:255',
            'distancia_km' => 'nullable|numeric|min:0',
            'estado' => 'required|in:planeado,en_curso,completado,cancelado',
            'notas' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $viaje->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Viaje actualizado exitosamente'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $viaje = Cwviaje::find($id);

        // Verificar si puede eliminarse
        if ($viaje->estado === 'en_curso') {
            return response()->json([
                'error' => 'No se puede eliminar un viaje en curso'
            ], 422);
        }

        $viaje->delete();

        return response()->json([
            'success' => true,
            'message' => 'Viaje eliminado correctamente'
        ]);
    }

    /**
     * Show form to add motos to viaje.
     */
    public function formMotos($id)
    {
        $viaje = Cwviaje::find($id);
        return response()->json([
            'html' => view('viajes.partials.motos-form', compact('viaje'))->render()
        ]);
    }

    /**
     * Add motos to viaje.
     */
    public function agregarMotos(Request $request, $id)
    {
        $viaje = Cwviaje::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'motos' => 'required|array|min:1',
            'motos.*.cliente_codclie' => 'required|string|exists:saclie,codclie',
            'motos.*.modelo_moto' => 'required|string|max:255',
            'motos.*.cantidad' => 'required|integer|min:1',
            'motos.*.precio_por_moto' => 'required|numeric|min:0',
            'motos.*.proveedor_paga' => 'nullable|boolean',
            'motos.*.proveedor_codprov' => 'nullable|string|exists:saprov,codprov',
            'motos.*.monto_transporte_proveedor' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();

        try {
            $motosCreadas = [];

            foreach ($request->motos as $motoData) {
                $data = [
                    'cliente_codclie' => $motoData['cliente_codclie'],
                    'modelo_moto' => $motoData['modelo_moto'],
                    'cantidad' => $motoData['cantidad'],
                    'precio_por_moto' => $motoData['precio_por_moto'],
                    'facturado' => false
                ];

                if (isset($motoData['proveedor_paga']) && $motoData['proveedor_paga']) {
                    $transporte = $motoData['monto_transporte_proveedor'] ?? 0;
                    $retencion = $transporte * 0.3; // 30%
                    $descuento = $transporte - $retencion;

                    $data['proveedor_paga'] = true;
                    $data['proveedor_codprov'] = $motoData['proveedor_codprov'];
                    $data['monto_transporte_proveedor'] = $transporte;
                    $data['retencion_proveedor'] = $retencion;
                    $data['descuento_aplicado_cliente'] = $descuento;
                    $data['monto_esperado_cliente'] = $descuento;
                    $data['estado_conciliacion'] = 'pendiente';


                }

                $moto = $viaje->motosTransportadas()->create($data);
                $motosCreadas[] = $moto->load('cliente', 'proveedor');
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Motos agregadas al viaje',
                'motos' => $motosCreadas,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error al agregar motos: ' . $e->getMessage()], 500);
        }
    }

    public function verViaje($id)
    {
        $viaje = Cwviaje::with([
            'camion',
            'chofer',
            'motosTransportadas',
            'etapas' => function($q) { $q->orderBy('orden'); },
            'gastos.tipoGasto'
        ])->findOrFail($id);

        return view('viajes.show-page', compact('viaje'));
    }

    /**
     * Delete a moto from viaje.
     */
    public function eliminarMoto($id, $motoId)
    {
        $viaje = Cwviaje::find($id);
        $moto = Cwviajemoto::where('viaje_id', $viaje->id)->where('id', $motoId)->first();

        if (!$moto) {
            return response()->json(['error' => 'Moto no encontrada'], 404);
        }

        $moto->delete();

        return response()->json([
            'success' => true,
            'message' => 'Moto eliminada del viaje'
        ]);
    }

    /**
     * Show form to add gasto to viaje.
     */
    public function formGastos($id)
    {
        $viaje = Cwviaje::find($id);
        $tiposGasto = Cwtipogasto::where('activo', true)->orderBy('nombre')->get();

        return response()->json([
            'html' => view('viajes.partials.gastos-form', compact('viaje', 'tiposGasto'))->render()
        ]);
    }

    /**
     * Add gasto to viaje.
     */
    public function agregarGasto(Request $request, $id)
    {
        try {
            $viaje = Cwviaje::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'tipo_gasto_id' => 'required|exists:cwtipogastos,id',
                'concepto' => 'required|string|max:255',
                'monto' => 'required|numeric|min:0',
                'fecha_gasto' => 'required|date',
                'proveedor' => 'nullable|string|max:255',
                'metodo_pago' => 'nullable|string|max:50',
                'referencia_pago' => 'nullable|string|max:100',
                'es_viatico' => 'nullable|boolean',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            DB::beginTransaction();

            $data = [
                'tipo_gasto_id'   => $request->tipo_gasto_id,
                'concepto'        => $request->concepto,
                'moneda_original' => $request->moneda_original,
                'monto_original'  => $request->monto_original,
                'tasa_cambio'     => $request->tasa_cambio, // ← Se guarda la tasa
                'monto'           => $request->monto,
                'fecha_gasto'     => $request->fecha_gasto,
                'gastable_id'     => $viaje->id,
                'gastable_type'   => Cwviaje::class,
                'proveedor'       => $request->proveedor,
                'metodo_pago'     => $request->metodo_pago,
                'referencia_pago' => $request->referencia_pago,
                'es_viatico'      => $request->boolean('es_viatico'),
                'registrado_por'  => auth()->id(),
            ];

            $gasto = Cwgasto::create($data);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Gasto agregado correctamente',
                'gasto' => $gasto->load('tipoGasto')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'error' => 'Error al agregar gasto: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show etapa management view.
     */
    public function gestionarEtapas($id)
    {
        $viaje = Cwviaje::with(['etapas' => function($q) {
            $q->orderBy('orden');
        }])->find($id);

        if (!$viaje) {
            return response()->json([
                'error' => 'Viaje no encontrado'
            ], 404);
        }

        // Verificar que la vista existe
        if (!view()->exists('viajes.partials.etapas-modal')) {
            return response()->json([
                'error' => 'La vista de etapas no existe'
            ], 500);
        }

        try {
            // Renderizar la vista y devolver el HTML
            $html = view('viajes.partials.etapas-modal', compact('viaje'))->render();

            return response()->json([
                'success' => true,
                'html' => $html
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al cargar la vista: ' . $e->getMessage()
            ], 500);
        }
    }


    public function testSeguimiento($id)
    {
        return response()->json([
            'success' => true,
            'message' => 'Prueba de seguimiento',
            'viaje_id' => $id,
            'html' => '<div class="alert alert-success">Vista de prueba cargada correctamente</div>'
        ]);
    }

    /**
     * Show seguimiento view.
     */
    public function seguimiento($id)
    {
        $viaje = Cwviaje::with([
            'camion',
            'chofer',
            'etapas' => function($q) {
                $q->orderBy('orden');
            },
            'puntosSeguimientoMapa'
        ])->find($id);

        if (!$viaje) {
            return response()->json(['error' => 'Viaje no encontrado'], 404);
        }

        $ultimoSeguimiento = $viaje->ultimoSeguimiento;

        // Formatear puntos para JSON
        $puntos = $viaje->puntosSeguimientoMapa->map(function($p) {
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
        });

        $html = view('viajes.partials.seguimiento-modal', compact('viaje', 'ultimoSeguimiento', 'puntos'))->render();

        return response()->json([
            'success' => true,
            'html' => $html
        ]);
    }

    public function puntosSeguimiento($id)
    {
        $viaje = Cwviaje::with('puntosSeguimientoMapa')->findOrFail($id);

        $puntos = $viaje->puntosSeguimientoMapa->map(function($p) {
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
        });

        return response()->json([
            'success' => true,
            'puntos' => $puntos
        ]);
    }

    /**
     * Change viaje estado.
     */
    public function cambiarEstado(Request $request, $id)
    {
        $viaje = Cwviaje::find($id);

        $validator = Validator::make($request->all(), [
            'estado' => 'required|in:planeado,en_curso,completado,cancelado'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $viaje->estado = $request->estado;

        if ($request->estado === 'en_curso' && !$viaje->fecha_inicio) {
            $viaje->fecha_inicio = now();
        }

        if ($request->estado === 'completado') {
            $viaje->fecha_fin = now();
        }

        $viaje->save();

        return response()->json([
            'success' => true,
            'message' => 'Estado del viaje actualizado'
        ]);
    }

    /**
     * Complete viaje.
     */
    public function completar($id)
    {
        $viaje = Cwviaje::find($id);

        if ($viaje->estado === 'completado') {
            return response()->json(['error' => 'El viaje ya está completado'], 422);
        }

        $viaje->estado = 'completado';
        $viaje->fecha_fin = now();
        $viaje->save();

        // Completar todas las etapas pendientes
        $viaje->etapas()->whereIn('estado', ['pendiente', 'en_curso'])->update([
            'estado' => 'completado',
            'fecha_real_fin' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Viaje completado exitosamente'
        ]);
    }

    /**
     * Cancel viaje.
     */
    public function cancelar(Request $request, $id)
    {
        $viaje = Cwviaje::find($id);
        $validator = Validator::make($request->all(), [
            'motivo_cancelacion' => 'required|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $viaje->estado = 'cancelado';
        $viaje->notas = $viaje->notas . "\n\nCANCELADO: " . $request->motivo_cancelacion;
        $viaje->save();

        return response()->json([
            'success' => true,
            'message' => 'Viaje cancelado'
        ]);
    }

    /**
     * Get statistics for dashboard.
     */
    public function estadisticas()
    {
        $now = now();
        $inicioMes = $now->copy()->startOfMonth();
        $inicioSemana = $now->copy()->startOfWeek();

        $estadisticas = [
            'resumen' => [
                'total' => Cwviaje::count(),
                'completados' => Cwviaje::where('estado', 'completado')->count(),
                'en_curso' => Cwviaje::where('estado', 'en_curso')->count(),
                'planeados' => Cwviaje::where('estado', 'planeado')->count(),
                'cancelados' => Cwviaje::where('estado', 'cancelado')->count(),
            ],
            'semana' => [
                'viajes' => Cwviaje::whereBetween('fecha_inicio', [$inicioSemana, $now])->count(),
                'completados' => Cwviaje::whereBetween('fecha_inicio', [$inicioSemana, $now])
                    ->where('estado', 'completado')->count(),
            ],
            'mes' => [
                'viajes' => Cwviaje::whereBetween('fecha_inicio', [$inicioMes, $now])->count(),
                'ingresos' => $this->calcularIngresosPeriodo($inicioMes, $now),
                'gastos' => $this->calcularGastosPeriodo($inicioMes, $now),
            ],
            'viajes_por_estado' => Cwviaje::select('estado', DB::raw('count(*) as total'))
                ->groupBy('estado')
                ->get()
                ->pluck('total', 'estado'),
            'top_choferes' => Cwviaje::select('chofer_id', DB::raw('count(*) as total_viajes'))
                ->where('estado', 'completado')
                ->with('chofer:id,nombre,apellido')
                ->groupBy('chofer_id')
                ->orderByDesc('total_viajes')
                ->limit(5)
                ->get()
                ->map(function($item) {
                    return [
                        'nombre' => $item->chofer ? $item->chofer->nombre_completo : 'N/A',
                        'viajes' => $item->total_viajes
                    ];
                }),
        ];

        // Calcular ganancia del mes
        $estadisticas['mes']['ganancia'] = $estadisticas['mes']['ingresos'] - $estadisticas['mes']['gastos'];

        return response()->json($estadisticas);
    }

    /**
     * Reporte por período.
     */
    public function reportePeriodo(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $inicio = Carbon::parse($request->fecha_inicio);
        $fin = Carbon::parse($request->fecha_fin);

        $viajes = Cwviaje::with(['camion', 'chofer', 'motosTransportadas', 'gastos'])
            ->whereBetween('fecha_inicio', [$inicio, $fin])
            ->orderBy('fecha_inicio')
            ->get();

        $reporte = [
            'periodo' => [
                'inicio' => $inicio->format('d/m/Y'),
                'fin' => $fin->format('d/m/Y'),
            ],
            'resumen' => [
                'total_viajes' => $viajes->count(),
                'completados' => $viajes->where('estado', 'completado')->count(),
                'cancelados' => $viajes->where('estado', 'cancelado')->count(),
            ],
            'financiero' => [
                'ingresos' => 0,
                'gastos' => 0,
                'ganancia' => 0,
            ],
            'viajes_por_dia' => [],
            'detalle' => []
        ];

        foreach ($viajes as $viaje) {
            $ingreso = $viaje->motosTransportadas->sum(function($m) {
                return $m->cantidad * $m->precio_por_moto;
            });
            $gasto = $viaje->gastos->sum('monto');

            $reporte['financiero']['ingresos'] += $ingreso;
            $reporte['financiero']['gastos'] += $gasto;

            $fecha = $viaje->fecha_inicio->format('Y-m-d');
            if (!isset($reporte['viajes_por_dia'][$fecha])) {
                $reporte['viajes_por_dia'][$fecha] = 0;
            }
            $reporte['viajes_por_dia'][$fecha]++;

            $reporte['detalle'][] = [
                'id' => $viaje->id,
                'folio' => $viaje->folio,
                'fecha' => $viaje->fecha_inicio->format('d/m/Y'),
                'ruta' => $viaje->origen . ' → ' . $viaje->destino,
                'camion' => $viaje->camion->placa ?? 'N/A',
                'chofer' => $viaje->chofer->nombre_completo ?? 'N/A',
                'estado' => $viaje->estado,
                'ingreso' => $ingreso,
                'gasto' => $gasto,
                'ganancia' => $ingreso - $gasto
            ];
        }

        $reporte['financiero']['ganancia'] = $reporte['financiero']['ingresos'] - $reporte['financiero']['gastos'];

        return response()->json($reporte);
    }

    /**
     * Reporte de rentabilidad.
     */
    public function reporteRentabilidad(Request $request)
    {
        $anio = $request->get('anio', now()->year);

        $viajes = Cwviaje::with(['motosTransportadas', 'gastos'])
            ->whereYear('fecha_inicio', $anio)
            ->where('estado', 'completado')
            ->get();

        $rentabilidad = [
            'anio' => $anio,
            'total_ingresos' => 0,
            'total_gastos' => 0,
            'total_ganancia' => 0,
            'margen_promedio' => 0,
            'por_mes' => [],
            'por_camion' => [],
            'por_chofer' => [],
        ];

        // Inicializar meses
        for ($i = 1; $i <= 12; $i++) {
            $rentabilidad['por_mes'][$i] = [
                'mes' => $i,
                'nombre' => Carbon::create()->month($i)->format('F'),
                'viajes' => 0,
                'ingresos' => 0,
                'gastos' => 0,
                'ganancia' => 0
            ];
        }

        foreach ($viajes as $viaje) {
            $ingreso = $viaje->motosTransportadas->sum(function($m) {
                return $m->cantidad * $m->precio_por_moto;
            });
            $gasto = $viaje->gastos->sum('monto');
            $ganancia = $ingreso - $gasto;

            $rentabilidad['total_ingresos'] += $ingreso;
            $rentabilidad['total_gastos'] += $gasto;
            $rentabilidad['total_ganancia'] += $ganancia;

            $mes = $viaje->fecha_inicio->month;
            $rentabilidad['por_mes'][$mes]['viajes']++;
            $rentabilidad['por_mes'][$mes]['ingresos'] += $ingreso;
            $rentabilidad['por_mes'][$mes]['gastos'] += $gasto;
            $rentabilidad['por_mes'][$mes]['ganancia'] += $ganancia;

            // Por camión
            $camionId = $viaje->camion_id;
            if (!isset($rentabilidad['por_camion'][$camionId])) {
                $rentabilidad['por_camion'][$camionId] = [
                    'camion' => $viaje->camion->placa ?? 'N/A',
                    'viajes' => 0,
                    'ingresos' => 0,
                    'gastos' => 0,
                    'ganancia' => 0
                ];
            }
            $rentabilidad['por_camion'][$camionId]['viajes']++;
            $rentabilidad['por_camion'][$camionId]['ingresos'] += $ingreso;
            $rentabilidad['por_camion'][$camionId]['gastos'] += $gasto;
            $rentabilidad['por_camion'][$camionId]['ganancia'] += $ganancia;

            // Por chofer
            $choferId = $viaje->chofer_id;
            if (!isset($rentabilidad['por_chofer'][$choferId])) {
                $rentabilidad['por_chofer'][$choferId] = [
                    'chofer' => $viaje->chofer->nombre_completo ?? 'N/A',
                    'viajes' => 0,
                    'ingresos' => 0,
                    'gastos' => 0,
                    'ganancia' => 0
                ];
            }
            $rentabilidad['por_chofer'][$choferId]['viajes']++;
            $rentabilidad['por_chofer'][$choferId]['ingresos'] += $ingreso;
            $rentabilidad['por_chofer'][$choferId]['gastos'] += $gasto;
            $rentabilidad['por_chofer'][$choferId]['ganancia'] += $ganancia;
        }

        if ($rentabilidad['total_ingresos'] > 0) {
            $rentabilidad['margen_promedio'] = round(($rentabilidad['total_ganancia'] / $rentabilidad['total_ingresos']) * 100, 2);
        }

        // Ordenar por ganancia
        $rentabilidad['por_camion'] = collect($rentabilidad['por_camion'])->sortByDesc('ganancia')->values();
        $rentabilidad['por_chofer'] = collect($rentabilidad['por_chofer'])->sortByDesc('ganancia')->values();

        return response()->json($rentabilidad);
    }

    /**
     * Quick search for autocomplete.
     */
    public function buscarRapido(Request $request)
    {
        $term = $request->get('q', '');

        if (strlen($term) < 2) {
            return response()->json([]);
        }

        $viajes = Cwviaje::with(['camion', 'chofer'])
            ->where(function($q) use ($term) {
                $q->where('folio', 'like', "%{$term}%")
                    ->orWhere('origen', 'like', "%{$term}%")
                    ->orWhere('destino', 'like', "%{$term}%")
                    ->orWhereHas('camion', function($cq) use ($term) {
                        $cq->where('placa', 'like', "%{$term}%");
                    })
                    ->orWhereHas('chofer', function($cq) use ($term) {
                        $cq->where(DB::raw("CONCAT(nombre, ' ', apellido)"), 'like', "%{$term}%");
                    });
            })
            ->limit(10)
            ->get()
            ->map(function($viaje) {
                return [
                    'id' => $viaje->id,
                    'text' => "Viaje #{$viaje->folio} - {$viaje->origen} → {$viaje->destino}",
                    'folio' => $viaje->folio,
                    'ruta' => "{$viaje->origen} → {$viaje->destino}",
                    'camion' => $viaje->camion->placa ?? 'N/A',
                    'chofer' => $viaje->chofer->nombre_completo ?? 'N/A',
                    'estado' => $viaje->estado
                ];
            });

        return response()->json($viajes);
    }

    /**
     * API: Get viaje details.
     */
    public function apiDetalles($id)
    {
        $viaje = Cwviaje::find($id);

        $viaje->load([
            'camion',
            'chofer',
            'motosTransportadas',
            'etapas' => function($q) { $q->orderBy('orden'); },
            'gastos.tipoGasto'
        ]);

        $viaje->ingreso_total = $viaje->motosTransportadas->sum(function($m) {
            return $m->cantidad * $m->precio_por_moto;
        });
        $viaje->gasto_total = $viaje->gastos->sum('monto');
        $viaje->ganancia_neta = $viaje->ingreso_total - $viaje->gasto_total;

        return response()->json($viaje);
    }

    /**
     * API: Get viaje etapas.
     */
    public function apiEtapas($id)
    {
        $viaje = Cwviaje::find($id);
        $etapas = $viaje->etapas()->orderBy('orden')->get()->map(function($etapa) {
            return [
                'id' => $etapa->id,
                'nombre' => $etapa->nombre,
                'estado' => $etapa->estado,
                'estado_color' => $etapa->estado_color,
                'ubicacion' => $etapa->ubicacion,
                'kilometraje' => $etapa->kilometraje,
                'fecha_inicio' => $etapa->fecha_real_inicio?->format('d/m/Y H:i'),
                'fecha_fin' => $etapa->fecha_real_fin?->format('d/m/Y H:i'),
            ];
        });

        return response()->json($etapas);
    }

    /**
     * API: Get viaje gastos.
     */
    public function apiGastos($id)
    {
        $viaje = Cwviaje::find($id);
        $gastos = $viaje->gastos()->with('tipoGasto')->orderBy('fecha_gasto', 'desc')->get();
        return response()->json($gastos);
    }

    /**
     * API: Get viaje motos.
     */
    public function apiMotos($id)
    {
        $viaje = Cwviaje::find($id);
        $motos = $viaje->motosTransportadas;
        $total = $motos->sum('cantidad');
        $ingreso = $motos->sum(function($m) {
            return $m->cantidad * $m->precio_por_moto;
        });

        return response()->json([
            'items' => $motos,
            'total_motos' => $total,
            'ingreso_total' => $ingreso
        ]);
    }

    /**
     * Export viajes to Excel.
     */
    public function exportarExcel(Request $request)
    {
        // Aquí implementarías la lógica de exportación a Excel
        // Podrías usar Laravel Excel o generar un CSV

        $viajes = Cwviaje::with(['camion', 'chofer'])
            ->when($request->fecha_desde, function($q) use ($request) {
                $q->whereDate('fecha_inicio', '>=', $request->fecha_desde);
            })
            ->when($request->fecha_hasta, function($q) use ($request) {
                $q->whereDate('fecha_inicio', '<=', $request->fecha_hasta);
            })
            ->when($request->estado && $request->estado != 'todos', function($q) use ($request) {
                $q->where('estado', $request->estado);
            })
            ->orderBy('fecha_inicio', 'desc')
            ->get();

        // Generar CSV
        $filename = "viajes_" . now()->format('Y-m-d_His') . ".csv";
        $handle = fopen('php://temp', 'w+');

        // Headers
        fputcsv($handle, [
            'ID', 'Folio', 'Fecha', 'Origen', 'Destino', 'Camión', 'Chofer',
            'Estado', 'Distancia (km)', 'Notas', 'Creado'
        ]);

        // Data
        foreach ($viajes as $viaje) {
            fputcsv($handle, [
                $viaje->id,
                $viaje->folio,
                $viaje->fecha_inicio->format('d/m/Y'),
                $viaje->origen,
                $viaje->destino,
                $viaje->camion ? $viaje->camion->placa : 'N/A',
                $viaje->chofer ? $viaje->chofer->nombre_completo : 'N/A',
                $viaje->estado,
                $viaje->distancia_km,
                $viaje->notas,
                $viaje->created_at->format('d/m/Y H:i')
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
     * Export viajes to PDF.
     */
    public function exportarPdf(Request $request)
    {
        // Aquí implementarías la lógica de exportación a PDF
        // Podrías usar dompdf o barryvdh/laravel-dompdf

        $viajes = Cwviaje::with(['camion', 'chofer'])
            ->when($request->fecha_desde, function($q) use ($request) {
                $q->whereDate('fecha_inicio', '>=', $request->fecha_desde);
            })
            ->when($request->fecha_hasta, function($q) use ($request) {
                $q->whereDate('fecha_inicio', '<=', $request->fecha_hasta);
            })
            ->orderBy('fecha_inicio', 'desc')
            ->get();

        $pdf = \PDF::loadView('viajes.pdf.reporte', compact('viajes'));

        return $pdf->download('viajes_' . now()->format('Y-m-d') . '.pdf');
    }

    /**
     * Helper: Calcular progreso del viaje.
     */
    private function calcularProgreso($id): int
    {
        $viaje = Cwviaje::find($id);
        $viaje->load(['etapas']);
        $totalEtapas = (isset($viaje->etapas)) ? $viaje->etapas->count() : 0;

        if ($totalEtapas === 0) {
            return 0;
        }

        $completadas = $viaje->etapas->where('estado', 'completado')->count();
        return round(($completadas / $totalEtapas) * 100);
    }

    /**
     * Helper: Calcular ingresos en período.
     */
    private function calcularIngresosPeriodo($inicio, $fin): float
    {
        $viajes = Cwviaje::with('motosTransportadas')
            ->whereBetween('fecha_inicio', [$inicio, $fin])
            ->where('estado', 'completado')
            ->get();

        return $viajes->sum(function($viaje) {
            return $viaje->motosTransportadas->sum(function($moto) {
                return $moto->cantidad * $moto->precio_por_moto;
            });
        });
    }

    /**
     * Helper: Calcular gastos en período.
     */
    private function calcularGastosPeriodo($inicio, $fin): float
    {
        return Cwgasto::whereHasMorph('gastable', [Cwviaje::class])
            ->whereBetween('fecha_gasto', [$inicio, $fin])
            ->sum('monto');
    }

    public function adminEtapas($id)
    {
        $viaje = Cwviaje::with(['etapas' => function($q) {
            $q->orderBy('orden');
        }])->findOrFail($id);

        $html = view('viajes.partials.etapas-admin-modal', compact('viaje'))->render();

        return response()->json([
            'success' => true,
            'html' => $html
        ]);
    }

    public function generarTokenSeguimiento($id)
    {
        try {
            $token = encrypt($id);
            return response()->json([
                'success' => true,
                'token' => $token
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al generar token'
            ], 500);
        }
    }


    public function guardarAnticipo(Request $request, $id)
    {
        $viaje = Cwviaje::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'anticipo' => 'required|numeric|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $viaje->anticipo_chofer = $request->anticipo;
        $viaje->save();

        return response()->json([
            'success' => true,
            'message' => 'Anticipo guardado correctamente'
        ]);
    }

    public function updateGasto(Request $request, $id, $gastoId)
    {
        $viaje = Cwviaje::findOrFail($id);
        $gasto = Cwgasto::where('gastable_id', $viaje->id)
            ->where('gastable_type', Cwviaje::class)
            ->where('id', $gastoId)
            ->firstOrFail();

        $validator = Validator::make($request->all(), [
            'tipo_gasto_id' => 'required|exists:cwtipogastos,id',
            'concepto' => 'required|string|max:255',
            'monto' => 'required|numeric|min:0',
            'fecha_gasto' => 'required|date',
            'proveedor' => 'nullable|string|max:255',
            'metodo_pago' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $gasto->update([
            'tipo_gasto_id' => $request->tipo_gasto_id,
            'concepto' => $request->concepto,
            'monto' => $request->monto,
            'fecha_gasto' => $request->fecha_gasto,
            'proveedor' => $request->proveedor,
            'metodo_pago' => $request->metodo_pago,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Gasto actualizado correctamente',
            'gasto' => $gasto->load('tipoGasto')
        ]);
    }

    public function adminGastos($id)
    {
        try {

            $viaje = Cwviaje::with(['gastos.tipoGasto'])->findOrFail($id);
            $tiposGasto = Cwtipogasto::where('activo', true)->orderBy('nombre')->get();
   if (!view()->exists('viajes.partials.gastos-admin-modal')) {
                 return response()->json([
                    'success' => false,
                    'error' => 'La vista de gastos no existe'
                ], 500);
            }

            $html = view('viajes.partials.gastos-admin-modal', compact('viaje', 'tiposGasto'))->render();

            return response()->json([
                'success' => true,
                'html' => $html
            ]);

        } catch (\Exception $e) {
             return response()->json([
                'success' => false,
                'error' => 'Error al cargar: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deleteGasto($id, $gastoId)
    {
        $viaje = Cwviaje::findOrFail($id);
        $gasto = Cwgasto::where('gastable_id', $viaje->id)
            ->where('gastable_type', Cwviaje::class)
            ->where('id', $gastoId)
            ->firstOrFail();

        // Eliminar comprobante si existe
        if ($gasto->comprobante) {
            Storage::disk('public')->delete($gasto->comprobante);
        }

        $gasto->delete();

        return response()->json([
            'success' => true,
            'message' => 'Gasto eliminado correctamente'
        ]);
    }


    public function facturarMoto($id, $motoId)
    {
        $viaje = Cwviaje::findOrFail($id);
        $moto = Cwviajemoto::where('viaje_id', $viaje->id)
            ->where('id', $motoId)
            ->firstOrFail();

        $moto->update([
            'facturado' => true,
            'fecha_facturacion' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Moto facturada correctamente'
        ]);
    }

    public function facturarCliente($id, $codclie)
    {
        $viaje = Cwviaje::findOrFail($id);

        $motos = Cwviajemoto::where('viaje_id', $viaje->id)
            ->where('cliente_codclie', $codclie)
            ->where('facturado', false)
            ->get();

        foreach ($motos as $moto) {
            $moto->update([
                'facturado' => true,
                'fecha_facturacion' => now()
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Cliente facturado correctamente',
            'total' => $motos->sum(function($m) {
                return $m->cantidad * $m->precio_por_moto;
            })
        ]);
    }

}
