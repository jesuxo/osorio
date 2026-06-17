<?php

namespace App\Http\Controllers;

use App\Models\Cwviajemoto;
use App\Models\Saclie;
use App\Models\Saprov;
use App\Models\Saprovsucursal;
use App\Models\Saprod;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaprovController extends Controller
{
    /**
     * Listado principal de proveedores
     */
    public function index(Request $request)
    {
        $busqueda = (isset($request->busqueda))? $request->busqueda :  '';
        $codprov  = (isset($request->codprov))? $request->codprov :  '';
        $tab      = (isset($request->tab))? $request->tab :  'tab1';

        $proveedores = [];
        $proveedor = null;
        $pagosPendientes = [];
        $pagosRealizados = [];
        $resumenPagos = [];


        if($busqueda != '') {
            $busqueda = str_replace("\"", "", $busqueda);
            $busqueda = str_replace("'", "", $busqueda);
            $busqueda = str_replace("*", " ", $busqueda);
            $vector = explode(" ", $busqueda);

            if ($vector) {
                $numerito = 0;
                $cadena = '';
                foreach ($vector as $value) {
                    if ($numerito > 0) {
                        $cadena .= ' AND ';
                    }
                    $cadena .= "(codprov like '%$value%' or descrip like '%$value%' or id3 like '%$value%')";
                    $numerito++;
                }
            }

            $proveedores = Saprov::whereRaw($cadena)->orderBy('descrip', 'asc')->limit(60)->get();
        }


        // Si hay código seleccionado, cargar datos del proveedor
        if ($codprov != '') {
            $proveedor = Saprov::where('codprov', $codprov)->first();

            if ($proveedor) {
                // Cargar pagos pendientes de este proveedor (viajes donde paga)
                $pagosPendientes = Cwviajemoto::with(['viaje', 'cliente'])
                    ->where('proveedor_paga', true)
                    ->where('proveedor_codprov', $proveedor->codprov)
                    ->where('estado_conciliacion', 'pendiente')
                    ->orderBy('created_at', 'desc')
                    ->get();

                // Cargar pagos realizados (conciliados)
                $pagosRealizados = Cwviajemoto::with(['viaje', 'cliente'])
                    ->where('proveedor_paga', true)
                    ->where('proveedor_codprov', $proveedor->codprov)
                    ->where('estado_conciliacion', 'conciliado')
                    ->orderBy('fecha_conciliacion', 'desc')
                    ->limit(20)
                    ->get();

                // Resumen de pagos por mes
                $resumenPagos = Cwviajemoto::select(
                    DB::raw('YEAR(created_at) as anio'),
                    DB::raw('MONTH(created_at) as mes'),
                    DB::raw('SUM(monto_esperado_cliente) as total_pendiente'),
                    DB::raw('SUM(CASE WHEN estado_conciliacion = "conciliado" THEN monto_real_cliente ELSE 0 END) as total_pagado')
                )
                    ->where('proveedor_paga', true)
                    ->where('proveedor_codprov', $proveedor->codprov)
                    ->groupBy('anio', 'mes')
                    ->orderBy('anio', 'desc')
                    ->orderBy('mes', 'desc')
                    ->limit(6)
                    ->get();
            }
        }

        return view('proveedores.index', compact(
            'proveedores',
            'proveedor',
            'busqueda',
            'codprov',
            'tab',
            'pagosPendientes',
            'pagosRealizados',
            'resumenPagos'
        ));
    }


    /**
     * JSON para DataTables o listados AJAX
     */
    public function json(Request $request)
    {
        $user = User::where('id', auth()->user()->id)
            ->with(['sucursales.sucursal.saprovsucursales.proveedor'])
            ->first();

        $proveedores = [];
        $sucursales = [];

        foreach ($user->sucursales as $rel) {
            array_push($sucursales, $rel->sucursal);
            if (isset($rel->sucursal->saprovsucursales)) {
                foreach ($rel->sucursal->saprovsucursales as $relproveedor) {
                    array_push($proveedores, $relproveedor->proveedor);
                }
            }
        }

        $all = [];
        foreach ($proveedores as $item) {
            list($fecha, $hora) = explode(" ", $item->created_at);
            list($y, $m, $d) = explode("-", $fecha);

            $aux = [
                "id" => $item->id,
                "codprov" => $item->codprov,
                "descrip" => $item->descrip,
                "telef" => trim($item->telef . " " . $item->movil),
                "email" => $item->email,
                "direccion" => trim($item->direc1 . " " . $item->direc2),
                "representante" => $item->represent,
                "date" => "$y-$m-$d",
                "datelabel" => "$d/$m/$y",
                "activo" => $item->activo
            ];

            array_push($all, $aux);
        }

        return response()->json($all);
    }

    public function list(Request $request)
    {
        $sucursalid = str_replace("300","",$request->sucursal);
        $proveedores   = $request->proveedores;
        $proveedores   = json_decode($proveedores);

        if(isset($proveedores))
            foreach ($proveedores as $proveedor){

                $aux = Saprov::where(['codprov' => $proveedor->codprov])->first();
                if(!$aux){
                    $new = new Saprov();


                    $new->id3           = ($proveedor->id3)       ?$proveedor->id3        : '';
                    $new->fax           = ($proveedor->fax)       ?$proveedor->fax        : '';
                    $new->clase         = ($proveedor->clase)     ?$proveedor->clase      : '';
                    $new->telef         = ($proveedor->telef)     ?$proveedor->telef      : '';
                    $new->movil         = ($proveedor->movil)     ?$proveedor->movil      : '';
                    $new->email         = ($proveedor->email)     ?$proveedor->email      : '';
                    $new->direc1        = ($proveedor->direc1)    ?$proveedor->direc1     : '';
                    $new->direc2        = ($proveedor->direc2)    ?$proveedor->direc2     : '';
                    $new->activo        = ($proveedor->activo)    ?$proveedor->activo     : 0;
                    $new->codprov       = $proveedor->codprov;
                    $new->tipoprv       = ($proveedor->tipoprv)   ?$proveedor->tipoprv    : 0;
                    $new->tipoid3       = ($proveedor->tipoid3)   ?$proveedor->tipoid3    : 0;
                    $new->tipoid        = ($proveedor->tipoid)    ?$proveedor->tipoid     : 0;
                    $new->descrip       = ($proveedor->descrip)   ?$proveedor->descrip    : '';
                    $new->represent     = ($proveedor->represent) ?$proveedor->represent  : '';
                    $new->zipcode       = ($proveedor->zipcode)   ?$proveedor->zipcode    : '';
                    $new->blockdesc     = ($proveedor->blockdesc) ?$proveedor->blockdesc  : 0;
                    $new->observa       = ($proveedor->observa)   ?$proveedor->observa    : '';

                    $new->save();

                    $rel              = new Saprovsucursal();
                    $rel->codprov     = $proveedor->codprov;
                    $rel->fk_sucursal = $sucursalid;
                    $rel->save();
                }else{
                    $aux = Saprovsucursal::where(['codprov' => $proveedor->codprov, 'fk_sucursal'=>$sucursalid])->first();
                    if(!$aux){
                        $rel              = new Saprovsucursal();
                        $rel->codprov     = $proveedor->codprov;
                        $rel->fk_sucursal = $sucursalid;
                        $rel->save();
                    }
                }
            }

        $proveedores = Saprov::whereRaw("codprov not in (select codprov from saprovsucursal where fk_sucursal=$sucursalid )")->get();


        return response()->json(['success'=>'success', 'newproveedores' => $proveedores]);
    }

    public function saprovsucursal(Request $request)
    {
        $sucursalid = str_replace("300", "", $request->sucursal);
        $proveedores = $request->proveedores;
        $proveedores = json_decode($proveedores);

        if (isset($proveedores))
            foreach ($proveedores as $proveedor){
                $aux = Saprovsucursal::where(['codprov' => $proveedor->codprov, 'fk_sucursal'=>$sucursalid])->first();
                if(!$aux){
                    $rel              = new Saprovsucursal();
                    $rel->codprov     = $proveedor->codprov;
                    $rel->fk_sucursal = $sucursalid;
                    $rel->save();
                }
            }

        return response()->json(['success'=>'success']);
    }

    public function create()
    {
        return view('proveedores.create');
    }

    /**
     * Guardar nuevo proveedor
     */
    public function store(Request $request)
    {
        $request->validate([
            'codprov' => 'required|string|unique:saprov,codprov',
            'descrip' => 'required|string|max:255',
            'id3' => 'nullable|string|max:50',
            'telef' => 'nullable|string|max:50',
            'movil' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'direc1' => 'nullable|string|max:255',
            'direc2' => 'nullable|string|max:255',
            'represent' => 'nullable|string|max:255',
            'clase' => 'nullable|string|max:50',
        ]);

        DB::beginTransaction();

        try {
            $proveedor = Saprov::create([
                'codprov' => $request->codprov,
                'descrip' => $request->descrip,
                'id3' => $request->id3,
                'telef' => $request->telef,
                'movil' => $request->movil,
                'email' => $request->email,
                'direc1' => $request->direc1,
                'direc2' => $request->direc2,
                'represent' => $request->represent,
                'clase' => $request->clase,
                'activo' => 1
            ]);

            DB::commit();

            return redirect()->route('proveedores.index', ['codprov' => $proveedor->codprov, 'tab' => 'tab1'])
                ->with('success', 'Proveedor creado exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error al crear proveedor: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Mostrar detalle de proveedor
     */
    public function show($id)
    {
        $proveedor = Saprov::findOrFail($id);

        // Obtener pagos pendientes de este proveedor (viajes donde paga)
        $pagosPendientes = \App\Models\Cwviajemoto::with(['viaje', 'cliente'])
            ->where('proveedor_paga', true)
            ->where('proveedor_codprov', $proveedor->codprov)
            ->where('estado_conciliacion', 'pendiente')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $totalPendiente = $pagosPendientes->sum('monto_esperado_cliente');

        return view('proveedores.show', compact('proveedor', 'pagosPendientes', 'totalPendiente'));
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit($id)
    {
        $proveedor = Saprov::findOrFail($id);
        return view('proveedores.edit', compact('proveedor'));
    }

    /**
     * Actualizar proveedor
     */
    public function proveedoresupdate(Request $request)
    {
        $request->validate([
            'codprov'   => 'required|string|exists:saprov,codprov',
            'descrip'   => 'required|string|max:255',
            'id3'       => 'nullable|string|max:50',
            'telef'     => 'nullable|string|max:50',
            'movil'     => 'nullable|string|max:50',
            'email'     => 'nullable|email|max:255',
            'direc1'    => 'nullable|string|max:255',
            'direc2'    => 'nullable|string|max:255',
            'represent' => 'nullable|string|max:255',
            'clase'     => 'nullable|string|max:50',
            'activo'    => 'nullable|boolean',
        ]);



        $proveedor = Saprov::where('codprov', $request->codprov)->firstOrFail();

        $proveedor->fill($request->all());
        $proveedor->activo = $request->has('activo') ? 1 : 0;
        $proveedor->save();

        return redirect()->route('proveedores.index', ['codprov' => $proveedor->codprov, 'tab' => $request->tab ?? 'tab1'])
            ->with('success', 'Proveedor actualizado exitosamente');
    }

    /**
     * Marcar un pago como conciliado
     */
    public function marcarPagado(Request $request)
    {
        $request->validate([
            'pago_id' => 'required|exists:cwviaje_motos,id',
            'monto_real' => 'required|numeric|min:0',
            'fecha_pago' => 'required|date',
            'notas' => 'nullable|string'
        ]);

        $pago = Cwviajemoto::findOrFail($request->pago_id);

        // Obtener el proveedor antes de actualizar
        $codprov = $pago->proveedor_codprov;

        $diferencia = $request->monto_real - ($pago->monto_esperado_cliente ?? 0);
        $estado = abs($diferencia) < 0.01 ? 'conciliado' : 'discrepancia';

        $pago->update([
            'monto_real_cliente' => $request->monto_real,
            'diferencia' => $diferencia,
            'estado_conciliacion' => $estado,
            'notas_conciliacion' => $request->notas,
            'fecha_conciliacion' => $request->fecha_pago,
            'conciliado_por' => auth()->id()
        ]);

        // Redireccionar de vuelta al proveedor con la pestaña activa
        return redirect()->route('proveedores.index', [
            'codprov' => $codprov,
            'tab' => 'tab1' // O la pestaña que quieras mostrar
        ])->with('success', 'Pago registrado correctamente');
    }

    /**
     * Eliminar proveedor (soft delete o desactivar)
     */
    public function destroy($id)
    {
        $proveedor = Saprov::findOrFail($id);

        // Verificar si tiene pagos pendientes
        $pagosPendientes = \App\Models\Cwviajemoto::where('proveedor_paga', true)
            ->where('proveedor_codprov', $proveedor->codprov)
            ->where('estado_conciliacion', 'pendiente')
            ->exists();

        if ($pagosPendientes) {
            return redirect()->back()
                ->with('error', 'No se puede eliminar porque tiene pagos pendientes. Desactívelo en su lugar.');
        }

        // En lugar de eliminar, desactivar
        $proveedor->update(['activo' => 0]);

        return redirect()->route('proveedores.index')
            ->with('success', 'Proveedor desactivado correctamente');
    }

    /**
     * Reporte de pagos pendientes por proveedor
     */
    public function pagosPendientes(Request $request)
    {
        $proveedorId = $request->get('proveedor');
        $fechaInicio = $request->get('fecha_inicio', now()->startOfMonth()->format('Y-m-d'));
        $fechaFin = $request->get('fecha_fin', now()->format('Y-m-d'));

        $query = \App\Models\Cwviajemoto::with(['viaje', 'cliente', 'proveedor'])
            ->where('proveedor_paga', true)
            ->where('estado_conciliacion', 'pendiente')
            ->whereHas('viaje', function($q) use ($fechaInicio, $fechaFin) {
                $q->whereBetween('fecha_inicio', [
                    Carbon::parse($fechaInicio)->startOfDay(),
                    Carbon::parse($fechaFin)->endOfDay()
                ]);
            });

        if ($proveedorId && $proveedorId !== 'todos') {
            $query->where('proveedor_codprov', $proveedorId);
        }

        $pagos = $query->orderBy('created_at', 'desc')->paginate(20);

        $proveedores = Saprov::where('activo', 1)->orderBy('descrip')->get();

        $totales = [
            'monto' => $pagos->sum('monto_esperado_cliente'),
            'registros' => $pagos->total(),
        ];

        return view('proveedores.pagos-pendientes', compact('pagos', 'proveedores', 'totales', 'proveedorId', 'fechaInicio', 'fechaFin'));
    }
}
