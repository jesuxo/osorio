<?php

namespace App\Http\Controllers;

use App\Models\Saacxcw;
use App\Models\Saipacxcw;
use App\Models\Sapagcxcw;
use App\Models\Sasucursal;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaacxcwController extends Controller
{
    public function index()
    {
        //
    }

    public function saacxcw(Request $request, $id = null)
    {
        $arraysucursales = auth()->user()->getSucursalesIdsComercialActual();
        $arraysucursales = implode(",",$arraysucursales);

        if (!isset($id))
            $id = '';

        $comercial = session('comercialid');

        if (!$comercial) {
            session(['comercialid' => 1]);
            $comercial = 1;
        }

        // Obtener el filtro de codoper desde la request
        $filtroCodoper = $request->input('codoper', '');

        // Query base para los procesos
        $cxcprocesosQuery = Saacxcw::with('cliente')
            ->whereIn('tipocxc', [98, 99])
            ->whereRaw("descargar > 0 and fk_sucursal in ($arraysucursales)");

        $cxcprocesos = $cxcprocesosQuery->orderBy('id','desc')->get();

        // Obtener todos los operadores disponibles (para el select)
        $operadores = DB::table('saoper')
            ->select('codoper', 'descrip')
            ->where('activo', 1) // si tienes campo activo
            ->where('comercial', $comercial) // para la comercial
            ->orderBy('descrip')
            ->get();

        $sucursales = Sasucursal::where("fk_comercial", $comercial)->whereRaw("id in ($arraysucursales)")->orderBy('descrip')->get();

        $sucursalselected = '';
        foreach ($sucursales as $sucursal) {
            if ($sucursal->id == $id) {
                $sucursalselected = $sucursal;
                break;
            }
        }

        $fechasreport = (isset($request->fechasreport)) ? $request->fechasreport : '';

        $fechasaux = str_replace(' ', '', $fechasreport);
        $fecha1 = '';
        $fecha2 = '';
        $d1 = $m1 = $y1 = '';
        $d2 = $m2 = $y2 = '';

        if (strpos($fechasaux, "to")){
            list($fecha1, $fecha2) = explode("to", $fechasaux);
            list($d1, $m1, $y1) = explode("/", $fecha1); $fecha1 = "$y1-$m1-$d1";
            list($d2, $m2, $y2) = explode("/", $fecha2); $fecha2 = "$y2-$m2-$d2";
        }else {
            if($fechasreport  != ''){
                list($d1, $m1, $y1) = explode("/", $fechasreport);
                $fecha1 = "$d1/$m1/$y1";
                $fecha2 = "$d1/$m1/$y1";
                $fechasreport = "$fecha1 to $fecha2";
                $fecha1 = "$y1-$m1-$d1";
                $fecha2 = "$y1-$m1-$d1";
            }
        }

        return view('saacxcw', compact(
            'cxcprocesos',
            'fecha1','fecha2',
            'fechasreport',
            'id',
            'sucursales',
            'sucursalselected',
            'comercial',
            'operadores',
            'filtroCodoper'
        ));
    }

    public function procesarPagoWeb(Request $request)
    {
        try {
            $codclie = $request->codclie;
            $fecha1  = $request->fecha1;
            $fecha2  = $request->fecha2;
            $observ  = (isset($request->observacion)) ? $request->observacion : '';

            $tasaBs   = $request->tasa_abono;
            $tasaPeso = $request->tasa_peso;

            // ============================================================
            // 1. RECOLECTAR TODOS LOS MONTOS DE PAGO
            // ============================================================
            $totalPago = 0;

            // Efectivo en USD
            $efectivo_usd = isset($request->efectivo_usd) ? floatval($request->efectivo_usd) : 0;
            $totalPago += $efectivo_usd;

            // Efectivo en Bs (convertido a USD)
            $efectivo_bs = isset($request->efectivo_bs) ? floatval($request->efectivo_bs) : 0;
            $totalPago += $efectivo_bs / $tasaBs;

            // Efectivo en Pesos (convertido a USD)
            $efectivo_pesos = isset($request->efectivo_pesos) ? floatval($request->efectivo_pesos) : 0;
            $totalPago += $efectivo_pesos / $tasaPeso;

            // Instrumentos en Bs (transferencias bancarias en Bs)
            $instrumentos_bs = isset($request->instrumentos_bs) ? $request->instrumentos_bs : [];
            $monto_instrumentos_bs = 0;
            foreach ($instrumentos_bs as $inst) {
                $monto = floatval($inst['monto']);
                $monto_instrumentos_bs += $monto;
                $totalPago += $monto / $tasaBs;
            }

            // Instrumentos en USD (transferencias en USD)
            $instrumentos_usd = isset($request->instrumentos_usd) ? $request->instrumentos_usd : [];
            $monto_instrumentos_usd = 0;
            foreach ($instrumentos_usd as $inst) {
                $monto = floatval($inst['monto']);
                $monto_instrumentos_usd += $monto;
                $totalPago += $monto;
            }

            // Instrumentos en Pesos (transferencias en COP)
            $instrumentos_pesos = isset($request->instrumentos_pesos) ? $request->instrumentos_pesos : [];
            $monto_instrumentos_pesos = 0;
            foreach ($instrumentos_pesos as $inst) {
                $monto = floatval($inst['monto']);
                $monto_instrumentos_pesos += $monto;
                $totalPago += $monto / $tasaPeso;
            }

            // Guardar los totales globales para usarlos en la distribución
            $monto_instrumentos_bs_total = $monto_instrumentos_bs;
            $monto_instrumentos_usd_total = $monto_instrumentos_usd;
            $monto_instrumentos_pesos_total = $monto_instrumentos_pesos;

            $montoabonar = $totalPago;

            // ============================================================
            // 2. OBTENER LAS FACTURAS DEL CLIENTE AGRUPADAS POR SUCURSAL
            // ============================================================
            $datafechas = '';
            if (isset($fecha1) && isset($fecha2) && $fecha1 != '' && $fecha2 != '') {
                $datafechas = " and (c.fechat >= '$fecha1 00:00:00.00' and c.fechat <= '$fecha2 23:59:22') ";
            }

            $sqlcostoinv = "
            SELECT d.id as sucu, d.descrip as sucursal, c.fechat,
                'FACT' AS tipo,
                c.numerod as numero,
                a.descrip AS cliente,
                date_format(c.fechat,'%d/%m/%Y') fecha,
                (c.montodolares) AS credito,
                (c.montodolares - (c.saldo / c.tasadolar)) AS abonado,
                a.codclie,
                (c.saldo / c.tasadolar) AS saldo
            FROM saclie AS a
            JOIN saacxcw AS c ON c.codclie = a.codclie
            JOIN sasucursal AS d ON d.id = c.fk_sucursal
            WHERE c.Saldo > 10
                AND c.tipocxc IN (10)
                AND c.tasadolar > 0
                AND c.codclie = '$codclie'
                $datafechas

            UNION

            SELECT d.id as sucu, d.descrip as sucursal, c.fechat,
                'N.DEB' AS tipo,
                c.numerod as numero,
                a.descrip AS cliente,
                date_format(c.fechat,'%d/%m/%Y') fecha,
                (c.montodolares) AS credito,
                (c.montodolares - (c.saldo / c.tasadolar)) AS abonado,
                a.codclie,
                (c.saldo / c.tasadolar) AS saldo
            FROM saclie AS a
            JOIN saacxcw AS c ON c.codclie = a.codclie
            JOIN sasucursal AS d ON d.id = c.fk_sucursal
            WHERE c.Saldo > 10
                AND c.tipocxc IN (20)
                AND c.tasadolar > 0
                AND c.codclie = '$codclie'
                $datafechas

            ORDER BY 1
        ";

            $saldocxc = DB::select($sqlcostoinv);
            $arraysucursal = [];

            foreach ($saldocxc as $cxc) {
                if (!isset($arraysucursal[$cxc->sucu])) {
                    $arraysucursal[$cxc->sucu] = [
                        'descrip' => $cxc->sucursal,
                        'saldo_total' => 0
                    ];
                }
                $arraysucursal[$cxc->sucu]['saldo_total'] += $cxc->saldo;
            }

            // ============================================================
            // 3. DISTRIBUIR EL PAGO ENTRE LAS SUCURSALES
            // ============================================================
            $resta = $montoabonar;

            // Variables para llevar el resto de cada tipo de pago
            $resta_efectivo_usd       = $efectivo_usd;
            $resta_efectivo_bs        = $efectivo_bs;
            $resta_efectivo_pesos     = $efectivo_pesos;
            $resta_instrumentos_bs    = $monto_instrumentos_bs;
            $resta_instrumentos_usd   = $monto_instrumentos_usd;
            $resta_instrumentos_pesos = $monto_instrumentos_pesos;

            foreach ($arraysucursal as $sucursalId => $item) {
                $saldoSucursal = $item['saldo_total'];

                // Determinar cuánto del total se aplica a esta sucursal
                $montoParaSucursal = $saldoSucursal;
                if ($saldoSucursal > $resta) {
                    $montoParaSucursal = $resta;
                }

                if ($montoParaSucursal <= 0) {
                    continue;
                }

                // Calcular el porcentaje que representa esta sucursal del total a pagar
                $porcentaje = $montoParaSucursal / $montoabonar;

                // Distribuir cada tipo de pago según el porcentaje
                $monto_efectivo_usd           = round($resta_efectivo_usd * $porcentaje, 2);
                $monto_efectivo_bs            = round($resta_efectivo_bs * $porcentaje, 2);
                $monto_efectivo_pesos         = round($resta_efectivo_pesos * $porcentaje, 2);
                $monto_instrumentos_bs_suc    = round($resta_instrumentos_bs * $porcentaje, 2);
                $monto_instrumentos_usd_suc   = round($resta_instrumentos_usd * $porcentaje, 2);
                $monto_instrumentos_pesos_suc = round($resta_instrumentos_pesos * $porcentaje, 2);

                // Actualizar los restos
                $resta_efectivo_usd       -= $monto_efectivo_usd;
                $resta_efectivo_bs        -= $monto_efectivo_bs;
                $resta_efectivo_pesos     -= $monto_efectivo_pesos;
                $resta_instrumentos_bs    -= $monto_instrumentos_bs_suc;
                $resta_instrumentos_usd   -= $monto_instrumentos_usd_suc;
                $resta_instrumentos_pesos -= $monto_instrumentos_pesos_suc;

                // Actualizar el resto del monto total
                $resta -= $montoParaSucursal;

                // ============================================================
                // 4. CREAR REGISTRO DE PAGO PARA LA SUCURSAL (TIPO 99)
                // ============================================================
                $newcxc           = new Saacxcw();
                $newcxc->tipocxc  = 99;
                $newcxc->nrounico = 0;
                $newcxc->NroRegi  = 0;
                $newcxc->codesta  = 'web';
                $newcxc->CodUsua  = 'web';
                $newcxc->NumeroD  = 'web';
                $newcxc->NumeroN  = '';
                $newcxc->codoper  = 'web';
                $newcxc->codclie  = $codclie;
                $newcxc->codvend  = '01';
                $newcxc->document = substr($observ, 0, 40) != '' ? substr($observ, 0, 40) : '';
                $newcxc->Notas1   = substr($observ, 40, 60) != '' ? substr($observ, 40, 60) : '';
                $newcxc->Notas2   = substr($observ, 100, 60) != '' ? substr($observ, 100, 60) : '';
                $newcxc->Notas3   = substr($observ, 160, 60) != '' ? substr($observ, 160, 60) : '';
                $newcxc->descargar = 1;
                $newcxc->EsUnPago  = 1;
                $newcxc->xdev      = 0;
                $newcxc->fk_transaccion = 0;

                // Montos en Bs (convertidos)
                $montoTotalBs      = ($monto_efectivo_usd * $tasaBs) + $monto_efectivo_bs + ($monto_instrumentos_usd_suc * $tasaBs) + $monto_instrumentos_bs_suc;
                $newcxc->Monto     = $montoTotalBs;
                $newcxc->MontoNeto = $montoTotalBs;
                $newcxc->MtoTax    = 0;
                $newcxc->Saldo     = 0;
                $newcxc->SaldoOrg  = 0;
                $newcxc->BaseImpo  = 0;
                $newcxc->TExento   = $montoTotalBs;

                // Pagos en efectivo
                $newcxc->CancelE   = $monto_efectivo_bs;      // Efectivo en Bs
                $newcxc->dolares   = $monto_efectivo_usd;     // Efectivo en USD
                $newcxc->pesos     = $monto_efectivo_pesos;     // Efectivo en Pesos

                // Pagos con instrumentos (transferencias)
                $newcxc->CancelT     = $monto_instrumentos_bs_suc;   // Instrumentos en Bs
                $newcxc->dolar_tranf = $monto_instrumentos_usd_suc; // Instrumentos en USD
                $newcxc->peso_tranf  = $monto_instrumentos_pesos_suc; // Instrumentos en Pesos

                $newcxc->CancelA    = 0;
                $newcxc->CancelC    = 0;
                $newcxc->euros      = 0;
                $newcxc->tasadolar  = $tasaBs;
                $newcxc->tasapeso   = $tasaPeso;
                $newcxc->tasaeuro   = 0;
                $newcxc->cancelaUSD = 0;
                $newcxc->FechaI     = Carbon::now();
                $newcxc->FechaE     = Carbon::now();
                $newcxc->FechaT     = Carbon::now();
                $newcxc->FechaV     = Carbon::now();
                $newcxc->montodolares = $montoParaSucursal;
                $newcxc->fk_sucursal  = $sucursalId;
                $newcxc->save();

                // ============================================================
                // 5. REGISTRAR DETALLE DE INSTRUMENTOS DE PAGO (SAIPACXC)
                // ============================================================

                // Instrumentos en Bs
                if ($monto_instrumentos_bs_suc > 0 && count($instrumentos_bs) > 0) {
                    $this->distribuirInstrumentosPorSucursal(
                        $newcxc->id, $codclie, $sucursalId,
                        $instrumentos_bs, $monto_instrumentos_bs_suc, $monto_instrumentos_bs_total,
                        'bs', $tasaBs
                    );
                }

                // Instrumentos en USD
                if ($monto_instrumentos_usd_suc > 0 && count($instrumentos_usd) > 0) {
                    $this->distribuirInstrumentosPorSucursal(
                        $newcxc->id, $codclie, $sucursalId,
                        $instrumentos_usd, $monto_instrumentos_usd_suc, $monto_instrumentos_usd_total,
                        'usd', $tasaBs
                    );
                }

                // Instrumentos en Pesos
                if ($monto_instrumentos_pesos_suc > 0 && count($instrumentos_pesos) > 0) {
                    $this->distribuirInstrumentosPorSucursal(
                        $newcxc->id, $codclie, $sucursalId,
                        $instrumentos_pesos, $monto_instrumentos_pesos_suc, $monto_instrumentos_pesos_total,
                        'pesos', $tasaPeso
                    );
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Pago procesado correctamente',
                'total_pagado' => $montoabonar
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function descargar(Request $request)
    {
        $sucursalid = str_replace("300", "", $request->sucursal);
        $comercialid = session('comercialid');

        // Buscar pagos pendientes de descargar (tipo 99 que aún no han sido procesados)
        $saacxcw = Saacxcw::selectRaw('id, montodolares, fk_sucursal, codclie,
                                  CancelE, CancelT, dolares, dolar_tranf, pesos, peso_tranf, euros,
                                  tasadolar, tasapeso')
            ->whereRaw("descargar = 1 AND tipocxc = 99 AND montodolares > 0 AND fk_sucursal = $sucursalid")
            ->first();

        $auxsaacxc = [];

        if ($saacxcw) {
            // Obtener los instrumentos de pago asociados a este pago
            $instrumentos = DB::table('saipacxcw')
                ->where('NroPpal', $saacxcw->id)
                ->select('CodPago', 'Descrip', 'Monto', 'dolares', 'pesos')
                ->get();

            $auxsaacxc['saacxc'][] = [
                'id'            => $saacxcw->id,
                'montodolares'  => $saacxcw->montodolares,
                'fk_sucursal'   => $saacxcw->fk_sucursal,
                'codclie'       => $saacxcw->codclie,
                // Datos del pago desglosado
                'cancele'       => $saacxcw->CancelE,      // Efectivo Bs
                'cancelt'       => $saacxcw->CancelT,      // Transferencias Bs (instrumentos)
                'dolares'       => $saacxcw->dolares,      // Efectivo USD
                'dolar_tranf'   => $saacxcw->dolar_tranf,  // Transferencias USD
                'pesos'         => $saacxcw->pesos,        // Efectivo COP
                'peso_tranf'    => $saacxcw->peso_tranf,   // Transferencias COP
                'euros'         => $saacxcw->euros,        // Efectivo EUR
                'tasadolar'     => $saacxcw->tasadolar,    // Tasa Bs/USD
                'tasa_peso'     => $saacxcw->tasapeso,     // Tasa COP/USD
                'instrumentos'  => $instrumentos
            ];
        }

        return response()->json([
            'success' => true,
            'auxsaacxc' => $auxsaacxc
        ]);
    }

    public function descargadoDescuento(Request $request)
    {
        $sucursalid = str_replace("300","",$request->sucursal);
        $idcxc = $request->idcxc;

        $saacxcw = Saacxcw::find($idcxc);
        if(isset($saacxcw)){
            $saacxcw->descargar = 2;
            $saacxcw->save();
        }

        return response()->json(['success' => 'success', 'updated' => 1], 200);
    }

    public function descargado(Request $request)
    {
        $sucursalid = str_replace("300","",$request->sucursal);
        $idcxc = $request->idcxc;

        $saacxcw = Saacxcw::find($idcxc);
        if(isset($saacxcw)){
            $saacxcw->descargar = 2;
            $saacxcw->save();
        }

        return response()->json(['success' => 'success', 'updated' => 1], 200);
    }

    public function descargarDescuento(Request $request)
    {
        $sucursalid = str_replace("300", "", $request->sucursal);
        $comercialid = session('comercialid');

        // Buscar pagos pendientes de descargar (tipo 99 que aún no han sido procesados)
        $saacxcw = Saacxcw::selectRaw('id, montodolares, fk_sucursal, codclie, numeron, NroRegi as nrounico, codesta, document, notas1, notas2')
            ->whereRaw("descargar = 1 AND tipocxc = 98 AND montodolares > 0 AND fk_sucursal = $sucursalid")
            ->first();

        $auxsaacxc = [];

        if ($saacxcw) {

            $cxc = DB::selectOne("
                                SELECT c.tipocxc, a.descrip as cliente
                                FROM saacxc c
                                JOIN saclie a ON a.codclie = c.CodClie
                                WHERE c.nrounico        = $saacxcw->nrounico
                                      AND c.fk_sucursal = $saacxcw->fk_sucursal
                                      AND c.Saldo       > 10
            ");

            $auxsaacxc['saacxc'][] = [
                'id'            => $saacxcw->id,
                'montodolares'  => $saacxcw->montodolares,
                'fk_sucursal'   => $saacxcw->fk_sucursal,
                'codclie'       => $saacxcw->codclie,
                'nrounico'      => $saacxcw->nrounico,
                'numeron'       => $saacxcw->numeron,
                'document'      => $saacxcw->document,
                'notas1'        => $saacxcw->notas1,
                'notas2'        => $saacxcw->notas2,
                'tipocxc'       => $cxc->tipocxc
            ];
        }

        return response()->json([
            'success' => true,
            'auxsaacxc' => $auxsaacxc
        ]);
    }

    public function aplicarDescuento(Request $request)
    {
        try {
            $numerod        = $request->numerod;
            $nrounico       = $request->nrounico;
            $fksucu         = $request->fksucu;
            $montoDescuento = floatval($request->monto);
            $motivo         = $request->motivo;
            $tasaBs         = 1; // Obtener tasa actual

            $factura = DB::selectOne("
                                SELECT c.*, a.descrip as cliente
                                FROM saacxcw c
                                JOIN saclie a ON a.codclie = c.CodClie
                                WHERE c.nrounico        = $nrounico
                                      AND c.fk_sucursal = $fksucu
                                      AND c.Saldo       > 10
        ");

            if (!$factura) {
                return response()->json(['success' => false, 'message' => 'Factura no encontrada o ya está pagada'], 404);
            }

            $saldoActualUSD = $factura->Saldo / $factura->tasadolar;

            if ($montoDescuento > $saldoActualUSD) {
                return response()->json(['success' => false, 'message' => 'El descuento no puede exceder el saldo de la factura'], 400);
            }

            // Crear registro de descuento (tipo 98 para descuentos)
            $descuento = new Saacxcw();
            $descuento->tipocxc     = 98;  // Tipo 98 para descuentos/devoluciones
            $descuento->nrounico    = 0;
            $descuento->NroRegi     = $nrounico;
            $descuento->codesta     = 'web';
            $descuento->CodUsua     = 'web';
            $descuento->NumeroD     = 'web';
            $descuento->NumeroN     = $numerod;
            $descuento->codoper     = 'web';
            $descuento->codclie     = $factura->CodClie;
            $descuento->codvend     = '01';
            $descuento->document    = substr($motivo, 0, 40);
            $descuento->Notas1      = substr($motivo, 40, 60);
            $descuento->Notas2      = "Descuento factura: $numerod";
            $descuento->Notas3      = '';
            $descuento->descargar   = 1;
            $descuento->EsUnPago    = 0;  // No es un pago, es un descuento
            $descuento->xdev        = 1;  // Marcar como devolución/descuento
            $descuento->fk_transaccion = 0;

            // Montos en Bs
            $montoBs                = $montoDescuento;
            $descuento->Monto       = $montoBs;        // Negativo porque es un descuento
            $descuento->MontoNeto   = $montoBs;
            $descuento->MtoTax      = 0;
            $descuento->Saldo       = 0;
            $descuento->SaldoOrg    = 0;
            $descuento->BaseImpo    = 0;
            $descuento->TExento     = $montoBs;

            // El descuento se aplica como un "abono negativo"
            $descuento->CancelE     = 0;
            $descuento->dolares     = 0;

            $descuento->CancelT     = 0;
            $descuento->dolar_tranf = 0;
            $descuento->pesos       = 0;
            $descuento->peso_tranf  = 0;
            $descuento->euros       = 0;
            $descuento->CancelA     = 0;
            $descuento->CancelC     = 0;

            $descuento->tasadolar    = 0;
            $descuento->tasapeso     = 0;
            $descuento->tasaeuro     = 0;
            $descuento->cancelaUSD   = 0;
            $descuento->montodolares = $montoDescuento;
            $descuento->fk_sucursal  = $fksucu;
            $descuento->FechaI       = Carbon::now();
            $descuento->FechaE       = Carbon::now();
            $descuento->FechaT       = Carbon::now();
            $descuento->FechaV       = Carbon::now();
            $descuento->save();

            return response()->json([
                'success' => true,
                'message' => 'Descuento aplicado correctamente',
                'monto'   => $montoDescuento,
                'factura' => $numerod
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getInstrumentosPago(Request $request)
    {
        $comercial = session('comercialid');
        if (!$comercial) {
            session(['comercialid' => 1]);
            $comercial = 1;
        }

        $instrumentosBs = DB::table('satarj')
            ->where('activo', 1)
            ->where('bs', 1)
            ->where('comercial', $comercial)
            ->select('codtarj', 'descrip', 'clase')
            ->get();

        $instrumentosDolares = DB::table('satarj')
            ->where('activo', 1)
            ->where('dolares', 1)
            ->where('comercial', $comercial)
            ->select('codtarj', 'descrip', 'clase')
            ->get();

        $instrumentosPesos = DB::table('satarj')
            ->where('activo', 1)
            ->where('pesos', 1)
            ->where('comercial', $comercial)
            ->select('codtarj', 'descrip', 'clase')
            ->get();

        // Obtener tasa de cambio actual
        $tasaCambio = 480; //$this->getCurrentExchangeRate();
        $tasaPeso   = 4000; //$this->getCurrentPesoRate();

        return response()->json([
            'success'            => true,
            'instrumentos_bs'    => $instrumentosBs,
            'instrumentos_usd'   => $instrumentosDolares,
            'instrumentos_pesos' => $instrumentosPesos,
            'tasa_cambio'        => $tasaCambio,
            'tasa_peso'          => $tasaPeso
        ]);
    }

    public function cxclist(Request $request)
    {
        $codclie = $request->codclie;
        $fecha1  = (isset($request->fecha1))? $request->fecha1 : '';
        $fecha2  = (isset($request->fecha2))? $request->fecha2 : '';

        $datafechas = '';

        if(isset($fecha1) and isset($fecha2) and $fecha1 != '' and $fecha2 != ''){
            $datafechas = " and  (c.fechat >= '$fecha1 00:00:00.00' and c.fechat <= '$fecha2 23:59:22') ";
        }

        $sqlcostoinv = "
                                                       SELECT d.descrip as sucursal, c.fechat,
                                                        'FACT' AS tipo,
                                                        c.fk_sucursal,
                                                        c.numerod  as numero,
                                                        c.nrounico as nrounico,
                                                        a.descrip  AS cliente,
                                                        date_format(c.fechat,'%d/%m/%Y') fecha,
                                                         (c.montodolares) AS credito,
                                                         (c.montodolares - (c.saldo / c.tasadolar)) AS abonado,
                                                        a.codclie,
                                                         (c.saldo / c.tasadolar) AS saldo
                                                    FROM
                                                        saclie AS a
                                                    JOIN
                                                        saacxcw AS c
                                                        ON c.codclie = a.codclie
                                                    JOIN
                                                        sasucursal AS d
                                                        ON d.id = c.fk_sucursal
                                                    WHERE
                                                        c.Saldo > 10
                                                        AND c.tipocxc IN (10)
                                                        AND c.tasadolar > 0
                                                        AND c.codclie = '$codclie'
                                                        $datafechas

                                                        UNION

                                                        SELECT  d.descrip as sucursal, c.fechat,
                                                        'N.DEB' AS tipo,
                                                         c.fk_sucursal,
                                                        c.numerod  as numero,
                                                        c.nrounico as nrounico,
                                                        a.descrip AS cliente,
                                                        date_format(c.fechat,'%d/%m/%Y') fecha,
                                                         (c.montodolares) AS credito,
                                                         (c.montodolares - (c.saldo / c.tasadolar)) AS abonado,
                                                        a.codclie,
                                                         (c.saldo / c.tasadolar) AS saldo
                                                    FROM
                                                        saclie AS a
                                                    JOIN
                                                        saacxcw AS c
                                                        ON c.codclie = a.codclie
                                                    JOIN
                                                        sasucursal AS d
                                                        ON d.id = c.fk_sucursal
                                                    WHERE
                                                        c.Saldo > 10
                                                        AND c.tipocxc IN (20)
                                                        AND c.tasadolar > 0
                                                        AND c.codclie = '$codclie'
                                                        $datafechas

                                                    ORDER BY  2

                                                          ";

        $saldocxc = \Illuminate\Support\Facades\DB::select($sqlcostoinv);

        $vista  = view('saacxclistado', compact( 'saldocxc', 'codclie', 'fecha1', 'fecha2'))->render();

        return response()->json(['success' => 'success', 'updated' => 1, 'vista' => $vista], 200);
    }

    private function distribuirInstrumentosPorSucursal($pagoId, $codclie, $sucursalId, $instrumentos, $montoTotalInstrumentosSucursal, $montoInstrumentosGlobal, $tipo, $tasa)
    {
        foreach ($instrumentos as $inst) {
            $montoInstrumento = floatval($inst['monto']);
            // Calcular qué porcentaje de este instrumento va a esta sucursal
            $porcentajeInstrumento = $montoInstrumento / $montoInstrumentosGlobal;
            $montoParaSucursal = round($montoTotalInstrumentosSucursal * $porcentajeInstrumento, 2);

            if ($montoParaSucursal > 0) {
                $saipacxc = new Saipacxcw();
                $saipacxc->NroPpal = $pagoId;
                $saipacxc->NroUnico = 0;
                $saipacxc->CodPago = $inst['cod_pago'];
                $saipacxc->codclie = $codclie;
                $saipacxc->Descrip = isset($inst['referencia']) ? $inst['referencia'] : '';
                $saipacxc->fk_sucursal = $sucursalId;

                // Asignar según el tipo de moneda
                if ($tipo == 'bs') {
                    $saipacxc->Monto = $montoParaSucursal;
                    $saipacxc->dolares = 0;
                    $saipacxc->pesos = 0;
                } elseif ($tipo == 'usd') {
                    $saipacxc->Monto = 0;
                    $saipacxc->dolares = $montoParaSucursal;
                    $saipacxc->pesos = 0;
                } else { // pesos
                    $saipacxc->Monto = 0;
                    $saipacxc->dolares = 0;
                    $saipacxc->pesos = $montoParaSucursal;
                }

                $saipacxc->save();
            }
        }
    }

    public function cuentaxcobrar(Request $request)
    {
        $sucursalid       = str_replace("300","",$request->sucursal);
        $cuentasporcobrar = $request->cuentasporcobrar;
        $cuentasporcobrar = json_decode($cuentasporcobrar);

        if(isset($cuentasporcobrar)){
            foreach ($cuentasporcobrar as $cxc){

                if(isset($cxc->NroUnico)){
                    $record = Saacxcw::where(['NroUnico'=>  $cxc->NroUnico, 'fk_sucursal'=> $cxc->fk_sucursal])->first();

                    if($cxc->NroUnico > 0){

                        // Eliminar registros antiguos de saipacxc si existen
                        $oldtarjetas = Saipacxcw::where([
                            'NroPpal'     => $cxc->NroUnico,
                            'fk_sucursal' => $cxc->fk_sucursal
                        ])->get();

                        if(isset($oldtarjetas) and count($oldtarjetas)>0){
                            foreach ($oldtarjetas as $oldtarjeta){
                                $oldtarjeta->delete();
                            }
                        }

                        // Eliminar registros antiguos de sapagcxc si existen
                        $oldpagoscxc = Sapagcxcw::where([
                            'NroPpal'     => $cxc->NroUnico,
                            'fk_sucursal' => $cxc->fk_sucursal
                        ])->get();

                        if(isset($oldpagoscxc) and count($oldpagoscxc)>0){
                            foreach ($oldpagoscxc as $oldpago) {
                                $oldpago->delete();
                            }
                        }
                    }

                    if(!isset($record->id)) {
                        $record = new Saacxcw();
                    }

                    if( isset($cxc->tarjetas)){
                        foreach ($cxc->tarjetas as $tarjeta){
                            $newtar  = new Saipacxcw();
                            $auxitem = (array) $tarjeta;
                            $newtar->fill($auxitem);
                            $newtar->created_at = $cxc->FechaT;
                            $newtar->updated_at = $cxc->FechaT;
                            $newtar->save();
                        }
                    }

                    if (isset($cxc->pagosxcxc)) {
                        foreach ($cxc->pagosxcxc as $pagoFactura) {
                            $newPagoFactura = new Sapagcxcw();
                            $auxPago = (array) $pagoFactura;
                            $newPagoFactura->fill($auxPago);
                            $newPagoFactura->fk_sucursal = $cxc->fk_sucursal;
                            $newPagoFactura->NroPpal     = $cxc->NroUnico; // Relación con el pago
                            $newPagoFactura->FechaE      = $cxc->FechaT ?? Carbon::now();
                            $newPagoFactura->created_at  = $cxc->FechaT ?? Carbon::now();
                            $newPagoFactura->updated_at  = $cxc->FechaT ?? Carbon::now();
                            $newPagoFactura->save();
                        }
                    }

                    $aux = (array) $cxc;
                    $record->fill($aux) ;
                    $record->fk_sucursal = $cxc->fk_sucursal;
                    $record->save();
                }
            }
        }

        return response()->json(['success' => 'success', 'updated' => 1], 200);
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show(Saacxcw $saacxcww)
    {
        //
    }

    public function edit(Saacxcw $saacxcww)
    {
        //
    }

    public function update(Request $request, Saacxcw $saacxcww)
    {
        //
    }

    public function destroy(Saacxcw $saacxcww)
    {
        //
    }
}
