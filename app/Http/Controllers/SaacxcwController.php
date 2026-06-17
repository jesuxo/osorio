<?php

namespace App\Http\Controllers;

use App\Models\Saacxcw;
use App\Models\Saipacxcw;
use App\Models\Sapagcxcw;
use App\Models\Sasucursal;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SaacxcwController extends Controller
{
    public function index()
    {
        //
    }

    public function saacxcw($id = null)
    {
        if(!isset($id))
            $id = '';

        $comercial  = session('comercialid') ;

        if(!$comercial) {
            session(['comercialid' => 1]);
            $comercial = 1;
        }

        $sucursales = Sasucursal::where("fk_comercial", $comercial)->orderBy('descrip')->get();

        $sucursalselected = '';
        foreach ($sucursales as $sucursal){
            if($sucursal->id == $id){
                $sucursalselected = $sucursal;
                break;
            }
        }

        return view('saacxcw', compact('sucursales', 'sucursalselected', 'comercial') );
    }

    public function cxclist(Request $request)
    {
        $codclie = $request->codclie;
        return view('saacxclistado', compact('codclie'))->render();
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

    public function show(Saacxcw $saacxcw)
    {
        //
    }

    public function edit(Saacxcw $saacxcw)
    {
        //
    }

    public function update(Request $request, Saacxcw $saacxcw)
    {
        //
    }

    public function destroy(Saacxcw $saacxcw)
    {
        //
    }
}
