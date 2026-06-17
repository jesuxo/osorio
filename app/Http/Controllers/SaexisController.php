<?php

namespace App\Http\Controllers;

use App\Models\Saexis;
use Illuminate\Http\Request;

class SaexisController extends Controller
{
    public function existencias(Request $request)
    {
        $productos = $request->productos;
        $productos = json_decode($productos);

        try{
            if(isset($productos)){
                foreach ($productos as $prd){

                    if(isset($prd->codprod)){
                        $existen = Saexis::where(['codprod'=>  $prd->codprod, 'codubic'=> $prd->codubic, 'fk_sucursal'=> $prd->fk_sucursal])->first();
                        if(isset($existen->id)){
                            $existen->existen = $prd->existen;
                            $existen->save();
                        }else{
                            $existen = new Saexis();
                            $existen->existen = $prd->existen;
                            $existen->codprod = $prd->codprod;
                            $existen->codubic = $prd->codubic;
                            $existen->fk_sucursal = $prd->fk_sucursal;
                            $existen->save();
                        }
                    }
                }
            }

            return response()->json(['success' => 'success', 'updated' => 1], 200);
        }catch (\Exception $e){

            return response()->json(['error' => 'error'], 304);
        }
    }
}
