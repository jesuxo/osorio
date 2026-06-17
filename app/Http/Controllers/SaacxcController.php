<?php

namespace App\Http\Controllers;

use App\Models\Saacxc;
use App\Models\Saipacxc;
use App\Models\Sapagcxc;
use App\Models\Sasucursal;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SaacxcController extends Controller
{
    public function saacxc(Request $request, $id = null)
    {
        if (!isset($id))
            $id = '';

        $comercial = session('comercialid');

        if (!$comercial) {
            session(['comercialid' => 1]);
            $comercial = 1;
        }

        $cxcprocesos = Saacxc::with('cliente')->whereRaw("descargar > 0")->orderBy('id','desc')->get();

        $sucursales = Sasucursal::where("fk_comercial", $comercial)->orderBy('descrip')->get();

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

        return view('saacxc', compact('cxcprocesos', 'fecha1','fecha2', 'fechasreport', 'id', 'sucursales', 'sucursalselected', 'comercial') );
    }

    public function cuentaxcobrar(Request $request)
    {
        $sucursalid       = str_replace("300","",$request->sucursal);
        $cuentasporcobrar = $request->cuentasporcobrar;
        $cuentasporcobrar = json_decode($cuentasporcobrar);

        if(isset($cuentasporcobrar)){
            foreach ($cuentasporcobrar as $cxc){

                if(isset($cxc->NroUnico)){
                    $record = Saacxc::where(['NroUnico'=>  $cxc->NroUnico, 'fk_sucursal'=> $cxc->fk_sucursal])->first();

                    if($cxc->NroUnico > 0){

                        // Eliminar registros antiguos de saipacxc si existen
                        $oldtarjetas = Saipacxc::where([
                            'NroPpal'     => $cxc->NroUnico,
                            'fk_sucursal' => $cxc->fk_sucursal
                        ])->get();

                        if(isset($oldtarjetas) and count($oldtarjetas)>0){
                            foreach ($oldtarjetas as $oldtarjeta){
                                $oldtarjeta->delete();
                            }
                        }

                        // Eliminar registros antiguos de sapagcxc si existen
                        $oldpagoscxc = Sapagcxc::where([
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
                        $record = new Saacxc();
                    }

                    if( isset($cxc->tarjetas)){
                        foreach ($cxc->tarjetas as $tarjeta){
                            $newtar  = new Saipacxc();
                            $auxitem = (array) $tarjeta;
                            $newtar->fill($auxitem);
                            $newtar->created_at = $cxc->FechaT;
                            $newtar->updated_at = $cxc->FechaT;
                            $newtar->save();
                        }
                    }

                    if (isset($cxc->pagosxcxc)) {
                        foreach ($cxc->pagosxcxc as $pagoFactura) {
                            $newPagoFactura = new Sapagcxc();
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

    public function index()
    {
        //
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show(Saacxc $saacxc)
    {
        //
    }

    public function edit(Saacxc $saacxc)
    {
        //
    }

    public function update(Request $request, Saacxc $saacxc)
    {
        //
    }

    public function destroy(Saacxc $saacxc)
    {
        //
    }
}
