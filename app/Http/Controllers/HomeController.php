<?php

namespace App\Http\Controllers;

use App\Models\Saacxcw;
use App\Models\Safact;
use App\Models\Sainsta;
use App\Models\Saitemcom;
use App\Models\Saitemfac;
use App\Models\Saoper;
use App\Models\Sasucursal;
use App\Models\Savend;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function fechasistema($y,$m,$d)
    {
        $fechasistema = "$y-$m-$d";
        session(['fechasistema' => "$fechasistema"]);
        return redirect()->route('index');
    }

    public function reporterepuestos(Request $request)
    {
        $arraysucursales = auth()->user()->getSucursalesIdsComercialActual();
        $arraysucursales = implode(",",$arraysucursales);

        $viewtype = (isset($request->viewtype))? $request->viewtype : 'cantidad'; // 'cantidad' o 'monto'
        $dd= 0;
        $opers = Saoper::query()
            ->where('repvta', 1)
            ->get();
        $saoper = [];
        foreach ($opers as $oper) {
            $saoper[$oper->codoper] = ["descrip" => $oper->descrip, "montovta" => 0, "productos" => []];
        }

        $inspadre = (isset($request->inspadre) and $request->inspadre >0) ? $request->inspadre: 0;
        $codalte  = '45';

        if($inspadre>0){
            $instanciapadre = Sainsta::where('codinst',$inspadre)->first();
            if($instanciapadre){ $codalte = $instanciapadre->codalte; }
        }

        $comercialid = session('comercialid');
        if(!$comercialid) {
            session(['comercialid' => 1]);
            $comercialid = 1;
        }

        $fechasreport = $request->fechasreport;
        $fechashoy    =  Carbon::now()->format('d/m/Y');
        $nofilterdate = 0;
        if(!$fechasreport) {
            $nofilterdate = 1;
            $fechasreport = $fechashoy;
        }

        $fechasaux = str_replace(' ','',$fechasreport);
        $fec1 = $fec2 = '';

        if(strpos($fechasaux,"to"))
            list($fec1, $fec2) = explode("to",$fechasaux);
        else {
            if(!$nofilterdate) {
                list($d1, $m1, $y1) = explode("/", $fechasreport);
                $fec1 = "$d1/$m1/$y1";
                $fec2 = $fec1;
                $fechasreport = "$fec1 to $fec2";
            }else{
                list($d1, $m1, $y1) = explode("/", $fechasreport);
                $fec1 = "$d1/$m1/$y1";
                $fec2 = "$d1/$m1/$y1";
                $fechasreport = "$fec1 to $fec2";
            }
        }

        $fecha1 = $fec1;
        $fecha2 = $fec2;

        list($d1,$m1,$y1) = explode("/",$fec1);
        list($d2,$m2,$y2) = explode("/",$fec2);

        $fec1 = "$y1-$m1-$d1";
        $fec2 = "$y2-$m2-$d2";

        $txtsalidas = " (Cantidad* Signo) as salidas, ";
        if($viewtype == 'monto')
            $txtsalidas = " (Cantidad  * costodoriginal  * Signo) as salidas , ";


        $topprod = Saitemfac::query()
            ->whereIn('TipoFac', ['Z', 'W'])
            ->whereRaw("fk_sucursal in ($arraysucursales)")
            ->where('esserv', 0)
            ->whereBetween('FechaE', ["{$fec1} 00:00:00.00", "{$fec2} 23:59:59.00"])
            ->whereHas('sucursal.comercial', fn($q) => $q->where('fk_comercial', $comercialid))
            ->whereHas('producto.instancia', fn($q) => $q->where('codalte', 'LIKE', "{$codalte}%"))
            ->selectRaw("CodItem,  $txtsalidas  fk_sucursal, NumeroD, TipoFac")

            ->orderByDesc('salidas')
            ->with([
                'factura.sucursal.comercial',
                'producto' => fn($q) => $q->select(['codprod', 'descrip', 'codinst']),
                'producto.instancia.padre',
                'sucursal'
            ])
            ->get();

        $ventaProductos = [];
        $ventaOperacion = [];
        $ventaSucursal  = [];
        $ventaSucuProd  = [];
        $productos      = [];
        $instancias     = [];
        $sucursales     = [];
        $ventaInstancia = [];
        $instanciaspadre= [];
        $ventainspadre  = [];
        $ventainsprodsucu= [];

        foreach ($topprod as $prod){

            $codoper = (isset($prod->factura->CodOper))? $prod->factura->CodOper: '';

            if(!isset($saoper[$codoper]['productos'][$prod->fk_sucursal][$prod->CodItem]))
                $saoper[$codoper]['productos'][$prod->fk_sucursal][$prod->CodItem] = 0;

            if(!isset($saoper[$codoper]['montovta']))
                $saoper[$codoper]['montovta'] = 0;

            $saoper[$codoper]['productos'][$prod->fk_sucursal][$prod->CodItem] += $prod->salidas;
            $saoper[$codoper]['montovta']  += $prod->salidas;

            if(!isset($prod->producto->descrip) or !isset($prod->producto->instancia->padre->codinst)) {
                $dd= 1;
                print_r($prod);
            }else {


                if (!isset($instanciaspadre[$prod->producto->instancia->padre->codinst])) $instanciaspadre[$prod->producto->instancia->padre->codinst] = $prod->producto->instancia->padre->descrip;
                if (!isset($ventainspadre[$prod->producto->instancia->padre->codinst])) $ventainspadre[$prod->producto->instancia->padre->codinst] = 0;
                if (!isset($ventainsprodsucu[$prod->producto->codinst][$prod->fk_sucursal])) $ventainsprodsucu[$prod->producto->codinst][$prod->fk_sucursal] = 0;

                if (!isset($instancias[$prod->producto->codinst])) $instancias[$prod->producto->codinst] = $prod->producto->instancia->descrip;
                if (!isset($ventaInstancia[$prod->producto->codinst])) $ventaInstancia[$prod->producto->codinst] = 0;

            }
            if(!isset($productos[$prod->CodItem]))        $productos[$prod->CodItem] = (isset($prod->producto->descrip)) ? $prod->producto->descrip : '';
            if(!isset($ventaProductos[$prod->CodItem]))   $ventaProductos[$prod->CodItem] = 0;

            if(!isset($sucursales[$prod->fk_sucursal]))   $sucursales[$prod->fk_sucursal] = $prod->sucursal->descrip;
            if(!isset($ventaSucursal[$prod->fk_sucursal]))$ventaSucursal[$prod->fk_sucursal] = 0;

            if(!isset($ventaSucuProd[$prod->CodItem][$prod->fk_sucursal]))$ventaSucuProd[$prod->CodItem][$prod->fk_sucursal] = 0;

            $ventaProductos[$prod->CodItem]           += $prod->salidas;
            $ventaSucursal[$prod->fk_sucursal]        += $prod->salidas;
            if(isset($prod->producto->instancia->padre->codinst))
              $ventaInstancia[$prod->producto->codinst] += $prod->salidas;

            $ventaSucuProd[$prod->CodItem][$prod->fk_sucursal]              += $prod->salidas;
            if(isset($prod->producto->instancia->padre->codinst)){
                $ventainsprodsucu[$prod->producto->codinst][$prod->fk_sucursal] += $prod->salidas;
                $ventainspadre[$prod->producto->instancia->padre->codinst]      += $prod->salidas;
            }
        }

        if($dd) dd('<br> productos por arreglar');
        return view('reporteRepuestos', compact( 'viewtype','saoper', 'inspadre', 'ventainsprodsucu', 'ventainspadre', 'instanciaspadre', 'ventaSucuProd', 'fechasreport', 'productos', 'instancias', 'sucursales', 'ventaInstancia', 'ventaSucursal', 'ventaProductos', 'fecha1', 'fecha2'));
    }

    public function reportelubricantes(Request $request)
    {
        $arraysucursales = auth()->user()->getSucursalesIdsComercialActual();
        $arraysucursales = implode(",",$arraysucursales);

        $viewtype = (isset($request->viewtype))? $request->viewtype : 'cantidad'; // 'cantidad' o 'monto'

        $opers = Saoper::query()
            ->where('repvta', 1)
            ->get();
        $saoper = [];
        $saoper[''] = ["descrip" => '..', "montovta" => 0, "productos" => []];
        foreach ($opers as $oper) {
            $saoper[$oper->codoper] = ["descrip" => $oper->descrip, "montovta" => 0, "productos" => []];
        }

        $inspadre = (isset($request->inspadre) and $request->inspadre >0) ? $request->inspadre: 0;
        $codalte  = '03.';

        if($inspadre>0){
            $instanciapadre = Sainsta::where('codinst',$inspadre)->first();
            if($instanciapadre){ $codalte = $instanciapadre->codalte; }
        }

        $comercialid = session('comercialid');
        if(!$comercialid) {
            session(['comercialid' => 1]);
            $comercialid = 1;
        }

        $fechasreport = $request->fechasreport;
        $fechashoy    =  Carbon::now()->format('d/m/Y');
        $nofilterdate = 0;
        if(!$fechasreport) {
            $nofilterdate = 1;
            $fechasreport = $fechashoy;
        }

        $fechasaux = str_replace(' ','',$fechasreport);
        $fec1 = $fec2 = '';

        if(strpos($fechasaux,"to"))
            list($fec1, $fec2) = explode("to",$fechasaux);
        else {
            if(!$nofilterdate) {
                list($d1, $m1, $y1) = explode("/", $fechasreport);
                $fec1 = "$d1/$m1/$y1";
                $fec2 = $fec1;
                $fechasreport = "$fec1 to $fec2";
            }else{
                list($d1, $m1, $y1) = explode("/", $fechasreport);
                $fec1 = "$d1/$m1/$y1";
                $fec2 = "$d1/$m1/$y1";
                $fechasreport = "$fec1 to $fec2";
            }
        }

        $fecha1 = $fec1;
        $fecha2 = $fec2;

        list($d1,$m1,$y1) = explode("/",$fec1);
        list($d2,$m2,$y2) = explode("/",$fec2);

        $fec1 = "$y1-$m1-$d1";
        $fec2 = "$y2-$m2-$d2";

        $txtsalidas = " (Cantidad* Signo) as salidas, ";
        if($viewtype == 'monto')
            $txtsalidas = " (Cantidad  * costodoriginal  * Signo) as salidas , ";

        $items = Saitemfac::query()
            ->whereIn('TipoFac', ['Z', 'W'])
            ->whereRaw("fk_sucursal in ($arraysucursales)")
            ->where('esserv', 0)
            ->whereBetween('FechaE', ["{$fec1} 00:00:00.00", "{$fec2} 23:59:59.00"])
            ->whereHas('sucursal.comercial', fn($q) => $q->where('fk_comercial', $comercialid))
            ->whereHas('producto.instancia', fn($q) => $q->where('codalte', 'LIKE', "{$codalte}%"))
            ->selectRaw("CodItem,  $txtsalidas   fk_sucursal, NumeroD, TipoFac")
            ->orderByDesc('salidas')
            ->with([
                'factura.sucursal.comercial',
                'producto' => fn($q) => $q->select(['codprod', 'descrip', 'codinst']),
                'producto.instancia.padre',
                'sucursal'
            ])
            ->get();

        $ventaProductos = [];
        $ventaSucursal  = [];
        $ventaSucuProd  = [];
        $productos      = [];
        $instancias     = [];
        $sucursales     = [];
        $saoperprods    = [];
        $ventaInstancia = [];
        $instanciaspadre= [];
        $ventainspadre  = [];
        $ventainsprodsucu= [];

        foreach ($items as $prod){


            $codoper = (isset($prod->factura->CodOper))? $prod->factura->CodOper: '';

            if(!isset($saoper[$codoper]))
                $saoper[$codoper] = ["descrip" => $codoper, "montovta" => 0, "productos" => []];

            if(!isset($saoper[$codoper]['productos'][$prod->fk_sucursal][$prod->CodItem]))
                $saoper[$codoper]['productos'][$prod->fk_sucursal][$prod->CodItem] = 0;

            if(!isset($saoper[$codoper]['montovta']))
                $saoper[$codoper]['montovta'] = 0;

            if(!isset($saoperprods[$prod->CodItem]) and isset($prod->producto->descrip))
                $saoperprods[$prod->CodItem] = $prod->producto->descrip;


            $saoper[$codoper]['productos'][$prod->fk_sucursal][$prod->CodItem] += $prod->salidas;
            $saoper[$codoper]['montovta']  += $prod->salidas;

            if(!isset($prod->producto))
                dd($prod);
            if(!isset($prod->producto->instancia))
                dd($prod);
            if(!isset($prod->producto->instancia->padre))
                dd($prod);

            if(!isset($instanciaspadre[$prod->producto->instancia->padre->codinst])) $instanciaspadre[$prod->producto->instancia->padre->codinst] = $prod->producto->instancia->padre->descrip;
            if(!isset($ventainspadre[$prod->producto->instancia->padre->codinst])) $ventainspadre[$prod->producto->instancia->padre->codinst] = 0;
            if(!isset($ventainsprodsucu[$prod->producto->codinst][$prod->fk_sucursal])) $ventainsprodsucu[$prod->producto->codinst][$prod->fk_sucursal] = 0;

            if(!isset($instancias[$prod->producto->codinst]))     $instancias[$prod->producto->codinst] = $prod->producto->instancia->descrip;
            if(!isset($ventaInstancia[$prod->producto->codinst])) $ventaInstancia[$prod->producto->codinst] = 0;

            if(!isset($productos[$prod->CodItem]))        $productos[$prod->CodItem] = $prod->producto->descrip;
            if(!isset($ventaProductos[$prod->CodItem]))   $ventaProductos[$prod->CodItem] = 0;

            if(!isset($sucursales[$prod->fk_sucursal]))   $sucursales[$prod->fk_sucursal] = $prod->sucursal->descrip;
            if(!isset($ventaSucursal[$prod->fk_sucursal]))$ventaSucursal[$prod->fk_sucursal] = 0;

            if(!isset($ventaSucuProd[$prod->CodItem][$prod->fk_sucursal]))$ventaSucuProd[$prod->CodItem][$prod->fk_sucursal] = 0;

            $ventaProductos[$prod->CodItem]           += $prod->salidas;
            $ventaSucursal[$prod->fk_sucursal]        += $prod->salidas;
            $ventaInstancia[$prod->producto->codinst] += $prod->salidas;

            $ventaSucuProd[$prod->CodItem][$prod->fk_sucursal]              += $prod->salidas;
            $ventainsprodsucu[$prod->producto->codinst][$prod->fk_sucursal] += $prod->salidas;
            $ventainspadre[$prod->producto->instancia->padre->codinst]      += $prod->salidas;

        }

        return view('reporteLubricantes', compact( 'viewtype', 'saoperprods','saoper', 'inspadre', 'ventainsprodsucu', 'ventainspadre', 'instanciaspadre', 'ventaSucuProd', 'fechasreport', 'productos', 'instancias', 'sucursales', 'ventaInstancia', 'ventaSucursal', 'ventaProductos', 'fecha1', 'fecha2'));
    }

    public function reportemotos(Request $request)
    {
        $arraysucursales = auth()->user()->getSucursalesIdsComercialActual();
        $arraysucursales = implode(",",$arraysucursales);

        $inspadre = (isset($request->inspadre) and $request->inspadre >0) ? $request->inspadre: 0;
        $codalte  = '01.';

        if($inspadre>0){
            $instanciapadre = Sainsta::where('codinst',$inspadre)->first();
            if($instanciapadre){ $codalte = $instanciapadre->codalte; }
        }

        $comercialid = session('comercialid');
        if(!$comercialid) {
            session(['comercialid' => 1]);
            $comercialid = 1;
        }

        $fechasreport = $request->fechasreport;
        $fechashoy    =  Carbon::now()->format('d/m/Y');
        $nofilterdate = 0;
        if(!$fechasreport) {
            $nofilterdate = 1;
            $fechasreport = $fechashoy;
        }

        $fechasaux = str_replace(' ','',$fechasreport);
        $fec1 = $fec2 = '';

        if(strpos($fechasaux,"to"))
            list($fec1, $fec2) = explode("to",$fechasaux);
        else {
            if(!$nofilterdate) {
                list($d1, $m1, $y1) = explode("/", $fechasreport);
                $fec1 = "$d1/$m1/$y1";
                $fec2 = $fec1;
                $fechasreport = "$fec1 to $fec2";
            }else{
                list($d1, $m1, $y1) = explode("/", $fechasreport);
                $fec1 = "$d1/$m1/$y1";
                $fec2 = "$d1/$m1/$y1";
                $fechasreport = "$fec1 to $fec2";
            }
        }

        $fecha1 = $fec1;
        $fecha2 = $fec2;

        list($d1,$m1,$y1) = explode("/",$fec1);
        list($d2,$m2,$y2) = explode("/",$fec2);

        $fec1 = "$y1-$m1-$d1";
        $fec2 = "$y2-$m2-$d2";

        $items = Saitemfac::query()
            ->whereIn('TipoFac', ['Z', 'W'])
            ->where('esserv', 0)
            ->whereRaw("fk_sucursal in ($arraysucursales)")
            ->whereBetween('FechaE', ["{$fec1} 00:00:00.00", "{$fec2} 23:59:59.00"])
            ->whereHas('sucursal.comercial', fn($q) => $q->where('fk_comercial', $comercialid))
            ->whereHas('producto.instancia', fn($q) => $q->where('codalte', 'LIKE', "{$codalte}%"))
            ->selectRaw("CodItem, sum(costod * Signo) as costod, sum(Cantidad * Signo) as salidas, fk_sucursal, NumeroD, TipoFac")
            ->groupBy(['CodItem', 'fk_sucursal', 'NumeroD', 'TipoFac'])
            ->orderByDesc('salidas')
            ->with([
                'factura' => function($q) {
                    $q->with(['cliente' => function($q) {
                        $q->select(['CodClie', 'descrip']); // Añade los campos que necesites
                    }]);
                },
                'factura.sucursal.comercial',
                'producto' => fn($q) => $q->select(['codprod', 'descrip', 'codinst']),
                'producto.instancia.padre',
                'sucursal'
            ])
            ->get();

        $ventaProductos = [];
        $ventaSucursal  = [];
        $ventaSucursalClientes = [];
        $ventaSucuProd  = [];
        $productos      = [];
        $instancias     = [];
        $sucursales     = [];
        $ventaInstancia = [];
        $instanciaspadre= [];
        $ventainspadre  = [];
        $ventainsprodsucu= [];

        foreach ($items as $prod){

           /* if(!isset($prod->producto->instancia->padre->codinst))
                dd($prod->producto);*/
            if(!isset($instanciaspadre[$prod->producto->instancia->padre->codinst])) $instanciaspadre[$prod->producto->instancia->padre->codinst] = $prod->producto->instancia->padre->descrip;
            if(!isset($ventainspadre[$prod->producto->instancia->padre->codinst])) $ventainspadre[$prod->producto->instancia->padre->codinst] = 0;
            if(!isset($ventainsprodsucu[$prod->producto->codinst][$prod->fk_sucursal])) $ventainsprodsucu[$prod->producto->codinst][$prod->fk_sucursal] = 0;

            if(!isset($instancias[$prod->producto->codinst]))     $instancias[$prod->producto->codinst] = $prod->producto->instancia->descrip;
            if(!isset($ventaInstancia[$prod->producto->codinst])) $ventaInstancia[$prod->producto->codinst] = 0;

            if(!isset($productos[$prod->CodItem]))        $productos[$prod->CodItem] = $prod->producto->descrip;
            if(!isset($ventaProductos[$prod->CodItem]))   $ventaProductos[$prod->CodItem] = 0;

            if(!isset($sucursales[$prod->fk_sucursal]))   $sucursales[$prod->fk_sucursal] = $prod->sucursal->descrip;
            if(!isset($ventaSucursal[$prod->fk_sucursal]))$ventaSucursal[$prod->fk_sucursal] = 0;

            if(!isset($ventaSucuProd[$prod->CodItem][$prod->fk_sucursal]))$ventaSucuProd[$prod->CodItem][$prod->fk_sucursal] = 0;

            $ventaProductos[$prod->CodItem]           += $prod->salidas;
            $ventaSucursal[$prod->fk_sucursal]        += $prod->salidas;
            $ventaInstancia[$prod->producto->codinst] += $prod->salidas;

            if(!isset($prod->factura->CodClie)){
                dd($prod);
            }

            $indicecheck  =  $prod->factura->CodClie.$prod->NumeroD.$prod->CodItem.$prod->salidas;

            if(!isset($ventaSucursalClientes[$prod->fk_sucursal]['clientes'][$indicecheck])){
                $ventaSucursalClientes[$prod->fk_sucursal]['clientes'][$indicecheck]['fecha']    = $prod->factura->fechaformat;
                $ventaSucursalClientes[$prod->fk_sucursal]['clientes'][$indicecheck]['cliente']  = (isset($prod->factura->cliente->descrip))? $prod->factura->cliente->descrip : 'Cliente faltante codclie:'.$prod->factura->CodClie;
                $ventaSucursalClientes[$prod->fk_sucursal]['clientes'][$indicecheck]['CodClie']  = (isset($prod->factura->CodClie))?$prod->factura->CodClie : '';
                $ventaSucursalClientes[$prod->fk_sucursal]['clientes'][$indicecheck]['NumeroD']  = $prod->NumeroD;
                $ventaSucursalClientes[$prod->fk_sucursal]['clientes'][$indicecheck]['CodItem']  = $prod->CodItem;
                $ventaSucursalClientes[$prod->fk_sucursal]['clientes'][$indicecheck]['producto'] = $prod->producto->descrip;
                $ventaSucursalClientes[$prod->fk_sucursal]['clientes'][$indicecheck]['cantidad'] = $prod->salidas;
                $ventaSucursalClientes[$prod->fk_sucursal]['clientes'][$indicecheck]['costod']   = $prod->costod;
            }

            $ventaSucuProd[$prod->CodItem][$prod->fk_sucursal]              += $prod->salidas;
            $ventainsprodsucu[$prod->producto->codinst][$prod->fk_sucursal] += $prod->salidas;
            $ventainspadre[$prod->producto->instancia->padre->codinst]      += $prod->salidas;

        }

        return view('reporteMotos', compact( 'inspadre', 'ventaSucursalClientes', 'ventainsprodsucu', 'ventainspadre', 'instanciaspadre', 'ventaSucuProd', 'fechasreport', 'productos', 'instancias', 'sucursales', 'ventaInstancia', 'ventaSucursal', 'ventaProductos', 'fecha1', 'fecha2'));
    }

    public function reporteCompraMotos(Request $request)
    {
        $arraysucursales = auth()->user()->getSucursalesIdsComercialActual();
        $arraysucursales = implode(",",$arraysucursales);

        $inspadre = (isset($request->inspadre) and $request->inspadre >0) ? $request->inspadre: 0;
        $codalte  = '01.';

        if($inspadre>0){
            $instanciapadre = Sainsta::where('codinst',$inspadre)->first();
            if($instanciapadre){ $codalte = $instanciapadre->codalte; }
        }

        $comercialid = session('comercialid');
        if(!$comercialid) {
            session(['comercialid' => 1]);
            $comercialid = 1;
        }

        $fechasreport = $request->fechasreport;
        $fechashoy    =  Carbon::now()->format('d/m/Y');
        $nofilterdate = 0;
        if(!$fechasreport) {
            $nofilterdate = 1;
            $fechasreport = $fechashoy;
        }

        $fechasaux = str_replace(' ','',$fechasreport);
        $fec1 = $fec2 = '';

        if(strpos($fechasaux,"to"))
            list($fec1, $fec2) = explode("to",$fechasaux);
        else {
            if(!$nofilterdate) {
                list($d1, $m1, $y1) = explode("/", $fechasreport);
                $fec1 = "$d1/$m1/$y1";
                $fec2 = $fec1;
                $fechasreport = "$fec1 to $fec2";
            }else{
                list($d1, $m1, $y1) = explode("/", $fechasreport);
                $fec1 = "$d1/$m1/$y1";
                $fec2 = "$d1/$m1/$y1";
                $fechasreport = "$fec1 to $fec2";
            }
        }

        $fecha1 = $fec1;
        $fecha2 = $fec2;

        list($d1,$m1,$y1) = explode("/",$fec1);
        list($d2,$m2,$y2) = explode("/",$fec2);

        $fec1 = "$y1-$m1-$d1";
        $fec2 = "$y2-$m2-$d2";

        $items = Saitemcom::query()
            ->whereIn('tipocom', ['U', 'Y'])
            ->whereRaw("fk_sucursal in ($arraysucursales)")
            ->whereBetween('created_at', ["{$fec1} 00:00:00.00", "{$fec2} 23:59:59.00"])
            ->whereHas('sucursal.comercial', fn($q) => $q->where('fk_comercial', $comercialid))
            ->whereHas('producto.instancia', fn($q) => $q->where('codalte', 'LIKE', "{$codalte}%"))
            ->selectRaw("coditem,   sum(Cantidad * Signo) as entradas, fk_sucursal, numerod, tipocom")
            ->groupBy(['coditem', 'fk_sucursal', 'numerod', 'tipocom'])
            ->orderByDesc('entradas')
            ->with([
                'compra' => function($q) {
                    $q->with(['proveedor' => function($q) {
                        $q->select(['codprov', 'descrip']); // Añade los campos que necesites
                    }]);
                },
                'compra.sucursal.comercial',
                'producto' => fn($q) => $q->select(['codprod', 'descrip', 'codinst']),
                'producto.instancia.padre',
                'sucursal'
            ])
            ->get();


        $ventaProductos = [];
        $ventaSucursal  = [];
        $ventaSucursalProveedor = [];
        $ventaSucuProd  = [];
        $productos      = [];
        $instancias     = [];
        $sucursales     = [];
        $ventaInstancia = [];
        $instanciaspadre= [];
        $ventainspadre  = [];
        $ventainsprodsucu= [];

        foreach ($items as $prod){

           if(!isset($prod->producto->instancia))
                 dd($prod);
            if(!isset($instanciaspadre[$prod->producto->instancia->padre->codinst])) $instanciaspadre[$prod->producto->instancia->padre->codinst] = $prod->producto->instancia->padre->descrip;
            if(!isset($ventainspadre[$prod->producto->instancia->padre->codinst])) $ventainspadre[$prod->producto->instancia->padre->codinst] = 0;
            if(!isset($ventainsprodsucu[$prod->producto->codinst][$prod->fk_sucursal])) $ventainsprodsucu[$prod->producto->codinst][$prod->fk_sucursal] = 0;

            if(!isset($instancias[$prod->producto->codinst]))     $instancias[$prod->producto->codinst] = $prod->producto->instancia->descrip;
            if(!isset($ventaInstancia[$prod->producto->codinst])) $ventaInstancia[$prod->producto->codinst] = 0;

            if(!isset($productos[$prod->coditem]))        $productos[$prod->coditem] = $prod->producto->descrip;
            if(!isset($ventaProductos[$prod->coditem]))   $ventaProductos[$prod->coditem] = 0;

            if(!isset($sucursales[$prod->fk_sucursal]))   $sucursales[$prod->fk_sucursal] = $prod->sucursal->descrip;
            if(!isset($ventaSucursal[$prod->fk_sucursal]))$ventaSucursal[$prod->fk_sucursal] = 0;

            if(!isset($ventaSucuProd[$prod->coditem][$prod->fk_sucursal]))$ventaSucuProd[$prod->coditem][$prod->fk_sucursal] = 0;

            $ventaProductos[$prod->coditem]           += $prod->entradas;
            $ventaSucursal[$prod->fk_sucursal]        += $prod->entradas;
            $ventaInstancia[$prod->producto->codinst] += $prod->entradas;

            if(!isset($prod->compra->codprov)){
                dd($prod);
             }
            $indicecheck  = $prod->compra->codprov.$prod->numerod.$prod->coditem.$prod->entradas;

            if(!isset($ventaSucursalProveedor[$prod->fk_sucursal]['proveedores'][$indicecheck])){
                $ventaSucursalProveedor[$prod->fk_sucursal]['proveedores'][$indicecheck]['fecha']    = $prod->compra->fechaformat;
                $ventaSucursalProveedor[$prod->fk_sucursal]['proveedores'][$indicecheck]['proveedor']  = (isset($prod->compra->proveedor->descrip))? $prod->compra->proveedor->descrip : 'Proveedor faltante codprov:'.$prod->compra->codprov;
                $ventaSucursalProveedor[$prod->fk_sucursal]['proveedores'][$indicecheck]['codprov']  = (isset($prod->compra->codprov))?$prod->compra->codprov : '';
                $ventaSucursalProveedor[$prod->fk_sucursal]['proveedores'][$indicecheck]['NumeroD']  = $prod->numerod;
                $ventaSucursalProveedor[$prod->fk_sucursal]['proveedores'][$indicecheck]['CodItem']  = $prod->coditem;
                $ventaSucursalProveedor[$prod->fk_sucursal]['proveedores'][$indicecheck]['producto'] = $prod->producto->descrip;
                $ventaSucursalProveedor[$prod->fk_sucursal]['proveedores'][$indicecheck]['cantidad'] = $prod->entradas;
               // $ventaSucursalProveedor[$prod->fk_sucursal]['proveedores'][$indicecheck]['costod']   = $prod->costod;
            }

            $ventaSucuProd[$prod->coditem][$prod->fk_sucursal]              += $prod->entradas;
            $ventainsprodsucu[$prod->producto->codinst][$prod->fk_sucursal] += $prod->entradas;
            $ventainspadre[$prod->producto->instancia->padre->codinst]      += $prod->entradas;

        }

        // Convertir a array de arrays para ordenar
        $tempArray = [];

        foreach ($ventainsprodsucu as $instanciaId => $sucursalesaux) {
            $tempArray[$instanciaId] = [
                'id' => $instanciaId,
                'sucursalesaux' => $sucursalesaux,
                'total' => array_sum($sucursalesaux) // Suma total para ordenar
            ];
        }

// Ordenar por total descendente
        uasort($tempArray, function($a, $b) {
            return $b['total'] <=> $a['total'];
        });

// Reconstruir manteniendo solo sucursales
        $ventainsprodsucuOrdenado = [];
        foreach ($tempArray as $item) {
            $ventainsprodsucuOrdenado[$item['id']] = $item['sucursalesaux'];
        }


        $instanciasOrdenadas = [];

        foreach ($ventainsprodsucuOrdenado as $indexinst => $sucursalesData) {
            if (isset($instancias[$indexinst])) {
                $instanciasOrdenadas[$indexinst] = $instancias[$indexinst];
            }
        }

        uasort($ventaProductos, function($a, $b) {
            return $b <=> $a; // Orden descendente
        });

        $productosOrdenados = [];
        foreach (array_keys($ventaProductos) as $indexprod) {
            if (isset($productos[$indexprod])) {
                $productosOrdenados[$indexprod] = $productos[$indexprod];
            }
        }

        $ventaSucuProdOrdenado = [];
        foreach (array_keys($ventaProductos) as $indexprod) {
            if (isset($ventaSucuProd[$indexprod])) {
                $ventaSucuProdOrdenado[$indexprod] = $ventaSucuProd[$indexprod];
            }
        }




        return view('reporteCompraMotos', compact( 'inspadre', 'ventaSucursalProveedor',
            'ventainsprodsucuOrdenado', 'ventainspadre', 'instanciaspadre', 'ventaSucuProdOrdenado', 'fechasreport', 'productosOrdenados',
            'instanciasOrdenadas', 'sucursales', 'ventaInstancia', 'ventaSucursal', 'ventaProductos', 'fecha1', 'fecha2'));
    }

    public function resumenVentas(Request $request)
    {
        $arraysucursales = auth()->user()->getSucursalesIdsComercialActual();
        $arraysucursales = implode(",",$arraysucursales);

        $comercialid = session('comercialid');
        if(!$comercialid) {
            session(['comercialid' => 1]);
            $comercialid = 1;
        }

        $fechasreport = $request->fechasreport;
        $fechashoy    =  Carbon::now()->format('d/m/Y');
        $nofilterdate = 0;

        if(!$fechasreport) {
            $nofilterdate = 1;
            $fechasreport = $fechashoy;
        }

        $fechasaux = str_replace(' ','',$fechasreport);
        $fec1 = $fec2 = '';

        if(strpos($fechasaux,"to"))
            list($fec1, $fec2) = explode("to",$fechasaux);
        else {
            if(!$nofilterdate) {
                list($d1, $m1, $y1) = explode("/", $fechasreport);
                $fec1 = "$d1/$m1/$y1";
                $fec2 = $fec1;
                $fechasreport = "$fec1 to $fec2";
            }else{
                list($d1, $m1, $y1) = explode("/", $fechasreport);
                $fec1 = "$d1/$m1/$y1";
                $fec2 = "$d1/$m1/$y1";
                $fechasreport = "$fec1 to $fec2";
            }
        }

        list($d1,$m1,$y1) = explode("/",$fec1);
        list($d2,$m2,$y2) = explode("/",$fec2);

        $fec1 = "$y1-$m1-$d1";
        $fec2 = "$y2-$m2-$d2";

        $costoinven   = [];
        $unidadesvendidas = '';
        $contado      = 0;
        $credito      = 0;
        $facturas     = 0;
        $devoluciones = 0;
        $sucursales   = [];
        $cxc          = '';


        $contado = $credito =  $facturas = $devoluciones = $unidadesvendidas = 0;
        $sucursales  = [];

        if(isset($items))
            $unidadesvendidas =  $items->tantos;


        $ventas = Safact::selectRaw("
                                                fk_sucursal,
                                                tipofac,
                                                count(*) as tantas,
                                                sum(((contado + credito) * Signo) / tasa_dolar) as totalventa,
                                                sum((credito * Signo) / tasa_dolar) as credito,
                                                sum((contado * Signo) / tasa_dolar) as contado
                                            ")
            ->with([
                'sucursal.comercial:id',
            ])
            ->whereIn('TipoFac', ['W', 'Z'])
            ->whereRaw("fk_sucursal in ($arraysucursales)")
            ->whereBetween('fechat', ["{$fec1} 00:00:00", "{$fec2} 23:59:59"])
            ->whereHas('sucursal.comercial', function ($q) use ($comercialid) {
                $q->where('fk_comercial', $comercialid);
            })
            ->groupBy(['fk_sucursal', 'tipofac'])
            ->get();

        foreach($ventas as $venta){

            if($venta->tipofac == 'A' or $venta->tipofac == 'Z'){
                $facturas += $venta->tantas;
            }
            if($venta->tipofac == 'B' or $venta->tipofac == 'W'){
                $devoluciones += $venta->tantas;
            }

            $contado += number_format($venta->contado, 2, '.', '');
            $credito += number_format($venta->credito, 2, '.', '');

            if(!isset($sucursales[$venta->fk_sucursal]['descrip'])) {
                $sucursales[$venta->fk_sucursal]['descrip'] = $venta->sucursal->descrip;
                $sucursales[$venta->fk_sucursal]['id']           = $venta->fk_sucursal;
                $sucursales[$venta->fk_sucursal]['total']        = 0;
                $sucursales[$venta->fk_sucursal]['contado']      = 0;
                $sucursales[$venta->fk_sucursal]['credito']      = 0;
                $sucursales[$venta->fk_sucursal]['facturas']     = 0;
                $sucursales[$venta->fk_sucursal]['tcobranzas']   = 0;
                $sucursales[$venta->fk_sucursal]['devoluciones'] = 0;
                $sucursales[$venta->fk_sucursal]['cobranzas']    = 0;
            }

            $sucursales[$venta->fk_sucursal]['total']   = $venta->contado + $venta->credito;
            $sucursales[$venta->fk_sucursal]['contado'] += number_format($venta->contado,2,'.','');
            $sucursales[$venta->fk_sucursal]['credito'] += number_format($venta->credito,2,'.','');

            if($venta->tipofac == 'A' or $venta->tipofac == 'Z'){
                $sucursales[$venta->fk_sucursal]['facturas'] = $venta->tantas;
            }
            if($venta->tipofac == 'B' or $venta->tipofac == 'W'){
                $sucursales[$venta->fk_sucursal]['devoluciones'] = $venta->tantas;
            }
        }

        // MODIFICADO: Agregar totales de cobranzas (monto y cantidad)
        $cobranzas = Saacxcw::selectRaw("
            count(*) as tantas,
            sum(montodolares) as cobranza,
            fk_sucursal
        ")
            ->whereRaw("fk_sucursal in ($arraysucursales)")
            ->with([
                'sucursal.comercial:id',
            ])
            ->whereRaw("tipocxc <> 99 and (tipocxc = 50 or EsUnPago = 1)")
            ->whereBetween('fechat', ["{$fec1} 00:00:00", "{$fec2} 23:59:59"])
            ->whereHas('sucursal.comercial', function ($q) use ($comercialid) {
                $q->where('fk_comercial', $comercialid);
            })
            ->groupBy(['fk_sucursal'])
            ->get();

        // Inicializar totales de cobranzas
        $totalCobranzasMonto = 0;
        $totalCobranzasCantidad = 0;

        foreach($cobranzas as $cobranza){
            if(!isset($sucursales[$cobranza->fk_sucursal]['descrip'])) {
                $sucursales[$cobranza->fk_sucursal]['descrip']      = $cobranza->sucursal->descrip;
                $sucursales[$cobranza->fk_sucursal]['id']           = $cobranza->sucursal->id;
                $sucursales[$cobranza->fk_sucursal]['total']        = 0;
                $sucursales[$cobranza->fk_sucursal]['contado']      = 0;
                $sucursales[$cobranza->fk_sucursal]['credito']      = 0;
                $sucursales[$cobranza->fk_sucursal]['facturas']     = 0;
                $sucursales[$cobranza->fk_sucursal]['tcobranzas']   = 0;
                $sucursales[$cobranza->fk_sucursal]['cobranzas']    = 0;
                $sucursales[$cobranza->fk_sucursal]['devoluciones'] = 0;
            }
            $sucursales[$cobranza->fk_sucursal]['tcobranzas'] += $cobranza->tantas;
            $sucursales[$cobranza->fk_sucursal]['cobranzas']  += $cobranza->cobranza;

            // Acumular totales
            $totalCobranzasMonto += $cobranza->cobranza;
            $totalCobranzasCantidad += $cobranza->tantas;
        }

        sort($sucursales);

        $montos = DB::table('saipavta as a')
            ->select([
                'b.clase',
                DB::raw("SUM(CASE a.tipofac WHEN 'A' THEN a.monto WHEN 'B' THEN (a.monto * -1) ELSE 0 END) as bs"),
                DB::raw("SUM(CASE a.tipofac WHEN 'Z' THEN a.monto WHEN 'W' THEN (a.monto * -1) ELSE 0 END) as bs"),
            ])
            ->whereRaw("a.fk_sucursal in ($arraysucursales)")
            ->join('satarj as b', 'a.CodPago', '=', 'b.codtarj')
            ->join('sasucursal as c', 'a.fk_sucursal', '=', 'c.id')
            ->where('b.bs', 1)
            ->where('b.comercial', $comercialid)
            ->where('c.fk_comercial', $comercialid)
            ->whereExists(function($query) use ($comercialid) {
                $query->select(DB::raw(1))
                    ->from('sasucursal as s')
                    ->whereColumn('s.id', 'a.fk_sucursal')
                    ->where('s.fk_comercial', $comercialid);
            })
            ->whereBetween('a.fechae', [
                Carbon::parse($fec1)->startOfDay()->format('Y-m-d H:i:s'),
                Carbon::parse($fec2)->endOfDay()->format('Y-m-d H:i:s')
            ])
            ->groupBy('b.clase');

        if(isset($fksucursal) and $fksucursal != '' and $fksucursal > 0){
            $montos = $montos->where('a.fk_sucursal', $fksucursal);
        }

        $montos = $montos->get();

        $clases     = [];
        $listado    = [];
        $listadousd = [];

        if(isset($montos) and count($montos)> 0) {
            foreach ($montos as $monto) {
                if(!isset($clases[$monto->clase])){
                    $clases[$monto->clase] = $monto->clase;
                }
                if(!isset($listado[$monto->clase]))
                    $listado[$monto->clase] = 0;
                $listado[$monto->clase] += $monto->bs;
            }
        }

        $montos = DB::table('saipacxcw as a')
            ->select([
                'b.clase',
                DB::raw('SUM(a.monto) as bs')
            ])
            ->whereRaw("c.id in ($arraysucursales)")
            ->join('satarj as b', 'a.CodPago', '=', 'b.codtarj')
            ->join('sasucursal as c', 'a.fk_sucursal', '=', 'c.id')
            ->where('c.fk_comercial', $comercialid)
            ->where('b.bs', 1)
            ->where('b.comercial', $comercialid)
            ->whereRaw("a.created_at >= '$fec1 00:00:00' and a.created_at <= '$fec2 23:59:00'")
            ->groupBy('b.clase');

        if(isset($fksucursal) and $fksucursal != '' and $fksucursal > 0){
            $montos = $montos->where('a.fk_sucursal', $fksucursal);
        }

        $montos = $montos->get();

        if(isset($montos) and count($montos)> 0) {
            foreach ($montos as $monto) {
                if(!isset($clases[$monto->clase])){
                    $clases[$monto->clase] = $monto->clase;
                }
                if(!isset($listado[$monto->clase]))
                    $listado[$monto->clase] = 0;
                $listado[$monto->clase] += $monto->bs;
            }
        }

        $montos = DB::table('saipavta as a')
            ->select([
                'b.clase',
                DB::raw("SUM(CASE a.tipofac WHEN 'A' THEN a.dolares WHEN 'B' THEN (a.dolares * -1) ELSE 0 END) as dolares"),
                DB::raw("SUM(CASE a.tipofac WHEN 'Z' THEN a.dolares WHEN 'W' THEN (a.dolares * -1) ELSE 0 END) as dolares")
            ])
            ->whereRaw("a.fk_sucursal in ($arraysucursales)")
            ->join('satarj as b', 'a.codpago', '=', 'b.codtarj')
            ->join('sasucursal as c', function ($join) use ($comercialid) {
                $join->on('a.fk_sucursal', '=', 'c.id')
                    ->where('c.fk_comercial', '=', $comercialid);
            })
            ->where('b.dolares', 1)
            ->where('b.comercial', $comercialid)
            ->whereBetween('a.fechae', [
                Carbon::parse($fec1)->startOfDay()->format('Y-m-d H:i:s'),
                Carbon::parse($fec2)->endOfDay()->format('Y-m-d H:i:s')
            ])
            ->groupBy('b.clase');

        if(isset($fksucursal) and $fksucursal != '' and $fksucursal > 0){
            $montos = $montos->where('a.fk_sucursal', $fksucursal);
        }

        $montos = $montos->get();

        if (isset($montos)) {
            foreach ($montos as $monto) {
                if (!isset($clases[$monto->clase])) {
                    $clases[$monto->clase] = $monto->clase;
                }
                if (!isset($listadousd[$monto->clase]))
                    $listadousd[$monto->clase] = 0;
                $listadousd[$monto->clase] += $monto->dolares;
            }
        }

        $montos = DB::table('saipacxcw as a')
            ->select([
                'b.clase',
                DB::raw('SUM(a.dolares) as dolares')
            ])
            ->whereRaw("c.id in ($arraysucursales)")
            ->join('satarj as b', 'a.CodPago', '=', 'b.codtarj')
            ->join('sasucursal as c', 'a.fk_sucursal', '=', 'c.id')
            ->where('c.fk_comercial', $comercialid)
            ->where('b.comercial', $comercialid)
            ->where('b.dolares', 1)
            ->whereRaw("a.created_at >= '$fec1 00:00:00' and a.created_at <= '$fec2 23:59:00'")
            ->groupBy('b.clase')
            ->get();

        if (isset($montos)) {
            foreach ($montos as $monto) {
                if (!isset($clases[$monto->clase])) {
                    $clases[$monto->clase] = $monto->clase;
                }
                if (!isset($listadousd[$monto->clase]))
                    $listadousd[$monto->clase] = 0;
                $listadousd[$monto->clase] += $monto->dolares;
            }
        }

        ksort($clases);

        Session::put('lang', 'sp');
        Session::save();

        return view('resumenVentas', compact(
            'fechasreport',
            'clases',
            'listado',
            'listadousd',
            'costoinven',
            'unidadesvendidas',
            'contado',
            'credito',
            'facturas',
            'devoluciones',
            'sucursales',
            'cxc',
            // NUEVAS VARIABLES ENVIADAS
            'totalCobranzasMonto',
            'totalCobranzasCantidad'
        ));
    }

    public function reporteVentasVendedor(Request $request)
    {
        $arraysucursales = auth()->user()->getSucursalesIdsComercialActual();
        $arraysucursales = implode(",",$arraysucursales);

        $comercialid = session('comercialid');
        if(!$comercialid) {
            session(['comercialid' => 1]);
            $comercialid = 1;
        }

        $codalteExcluir = '153.'; //

        // Fechas
        $fechasreport = $request->fechasreport;
        $fechashoy = Carbon::now()->format('d/m/Y');
        $nofilterdate = 0;
        if(!$fechasreport) {
            $nofilterdate = 1;
            $fechasreport = $fechashoy;
        }

        $fechasaux = str_replace(' ','',$fechasreport);
        $fec1 = $fec2 = '';

        if(strpos($fechasaux,"to"))
            list($fec1, $fec2) = explode("to",$fechasaux);
        else {
            if(!$nofilterdate) {
                list($d1, $m1, $y1) = explode("/", $fechasreport);
                $fec1 = "$d1/$m1/$y1";
                $fec2 = $fec1;
                $fechasreport = "$fec1 to $fec2";
            } else {
                list($d1, $m1, $y1) = explode("/", $fechasreport);
                $fec1 = "$d1/$m1/$y1";
                $fec2 = "$d1/$m1/$y1";
                $fechasreport = "$fec1 to $fec2";
            }
        }

        $fecha1 = $fec1;
        $fecha2 = $fec2;

        list($d1,$m1,$y1) = explode("/",$fec1);
        list($d2,$m2,$y2) = explode("/",$fec2);

        $fec1 = "$y1-$m1-$d1";
        $fec2 = "$y2-$m2-$d2";

        // Obtener el filtro de sucursal
        $fksucursal = $request->fksucursal ?? '';

        // Obtener todas las sucursales del comercial (para el filtro)
        $sucursalesFiltro = Sasucursal::where('fk_comercial', $comercialid)->orderBy('descrip')->get();

        // Obtener las categorías principales (nivel 1) - estas son las que queremos mostrar
        $categoriasPrincipales = Sainsta::where('nivel', 1)
            ->where('insPadre', 0)
            ->where('comercial', $comercialid)
            ->orderBy('descrip')
            ->get();

        // Obtener todos los vendedores activos
        $vendedores = Savend::where('comercial', $comercialid)->orderBy('descrip')->get();

        // Construir la consulta base para obtener los datos con la categoría principal
        $query = Saitemfac::from('saitemfac as ifac')
            ->select(
                'ifac.fk_sucursal',
                'f.codvend',
                'v.descrip as vendedor_nombre',
                'iprincipal.descrip as categoria_principal',
                DB::raw('SUM(ifac.cantidad * ifac.signo) as unidades'),
                DB::raw('SUM(ifac.costodoriginal * ifac.cantidad * ifac.signo) as monto_usd')
            )
            ->join('safact as f', function ($join) {
                $join->on('ifac.fk_sucursal', '=', 'f.fk_sucursal')
                    ->on('ifac.NumeroD', '=', 'f.NumeroD')
                    ->on('ifac.TipoFac', '=', 'f.TipoFac');
            })
            ->join('savend as v', function ($join) use ($comercialid) {
                $join->on('f.codvend', '=', 'v.codvend')
                    ->where('v.comercial', '=', $comercialid);
            })
            ->join('saprod as p', function ($join) use ($comercialid) {
                $join->on('ifac.CodItem', '=', 'p.codprod')
                    ->where('p.comercial', '=', $comercialid);
            })
            ->join('sainsta as i', function ($join) use ($comercialid) {
                $join->on('p.codinst', '=', 'i.codinst')
                    ->where('i.comercial', '=', $comercialid);
            })
            ->join('sainsta as iprincipal', function ($join) use ($comercialid) {
                // Unir con la instancia principal (nivel 1) usando el codalte
                $join->on('i.codalte', 'LIKE', DB::raw('CONCAT(iprincipal.codalte, \'%\')'))
                    ->where('iprincipal.nivel', '=', 1)
                    ->where('iprincipal.insPadre', '=', 0)
                    ->where('iprincipal.comercial', '=', $comercialid);
            })
            ->whereRaw("f.fk_sucursal in ($arraysucursales)")
            ->whereIn('ifac.TipoFac', ['Z', 'W']) // Facturas de venta
            ->where('ifac.esserv', 0)
            ->whereBetween('ifac.FechaE', [$fec1 . ' 00:00:00', $fec2 . ' 23:59:59'])
            ->groupBy('ifac.fk_sucursal', 'f.codvend', 'v.descrip', 'iprincipal.descrip')
            ->orderBy('ifac.fk_sucursal')
            ->orderBy('v.descrip')
            ->orderBy('iprincipal.descrip');

        // Aplicar filtro de sucursal si se seleccionó
        if(!empty($fksucursal) && $fksucursal > 0) {
            $query->where('ifac.fk_sucursal', $fksucursal);
        }

        $query = $query->where('i.codalte', 'NOT LIKE', $codalteExcluir . '%');

        $resultados = $query->get();

        // Reorganizar los datos
        $data = [];
        foreach ($resultados as $row) {
            $sucId = $row->fk_sucursal;
            $vendId = $row->codvend;
            $categoria = $row->categoria_principal;

            // Obtener nombre de sucursal
            $sucursalObj = Sasucursal::find($sucId);
            $sucursalNombre = $sucursalObj ? $sucursalObj->descrip : 'Sucursal ' . $sucId;

            if (!isset($data[$sucId])) {
                $data[$sucId] = [
                    'nombre' => $sucursalNombre,
                    'vendedores' => []
                ];
            }
            if (!isset($data[$sucId]['vendedores'][$vendId])) {
                $data[$sucId]['vendedores'][$vendId] = [
                    'nombre' => $row->vendedor_nombre,
                    'categorias' => []
                ];
            }
            $data[$sucId]['vendedores'][$vendId]['categorias'][$categoria] = [
                'unidades' => $row->unidades,
                'monto' => $row->monto_usd
            ];
        }

        // Completar con todas las categorías principales para cada vendedor
        $dataCompleta = [];
        foreach ($data as $sucId => $sucDataOriginal) {
            $dataCompleta[$sucId] = [
                'nombre' => $sucDataOriginal['nombre'],
                'vendedores' => []
            ];

            foreach ($vendedores as $vendedor) {
                $vendId = $vendedor->codvend;

                // Obtener categorías del vendedor si existen
                $categoriasData = isset($sucDataOriginal['vendedores'][$vendId]['categorias'])
                    ? $sucDataOriginal['vendedores'][$vendId]['categorias']
                    : [];

                // Crear array con todas las categorías principales (inicializar en 0)
                $categoriasCompletas = [];
                foreach ($categoriasPrincipales as $cat) {
                    $catNombre = $cat->descrip;
                    if (isset($categoriasData[$catNombre])) {
                        $categoriasCompletas[$catNombre] = $categoriasData[$catNombre];
                    } else {
                        $categoriasCompletas[$catNombre] = ['unidades' => 0, 'monto' => 0];
                    }
                }

                // Solo agregar vendedores que tengan al menos una categoría con ventas
                $tieneVentas = false;
                foreach ($categoriasCompletas as $catData) {
                    if ($catData['unidades'] != 0 || $catData['monto'] != 0) {
                        $tieneVentas = true;
                        break;
                    }
                }

                if ($tieneVentas) {
                    $dataCompleta[$sucId]['vendedores'][$vendId] = [
                        'nombre' => $vendedor->descrip,
                        'categorias' => $categoriasCompletas
                    ];
                }
            }

            // Si la sucursal no tiene vendedores con ventas, eliminarla del resultado
            if (empty($dataCompleta[$sucId]['vendedores'])) {
                unset($dataCompleta[$sucId]);
            }
        }

        // Ordenar vendedores por nombre dentro de cada sucursal
        foreach ($dataCompleta as &$sucData) {
            uasort($sucData['vendedores'], function($a, $b) {
                return strcasecmp($a['nombre'], $b['nombre']);
            });
        }

        // Totales generales
        $totalGeneral = [
            'unidades' => 0,
            'monto' => 0
        ];
        foreach ($resultados as $row) {
            $totalGeneral['unidades'] += $row->unidades;
            $totalGeneral['monto'] += $row->monto_usd;
        }

        return view('reporteVentasVendedor', compact(
            'fechasreport',
            'fecha1',
            'fecha2',
            'dataCompleta',
            'categoriasPrincipales',
            'totalGeneral',
            'sucursalesFiltro',
            'fksucursal'
        ));
    }

    public function reporteventa(Request $request)
    {
        $arraysucursales = auth()->user()->getSucursalesIdsComercialActual();
        $arraysucursales = implode(",",$arraysucursales);

        $comercialid = session('comercialid');
        if(!$comercialid) {
            session(['comercialid' => 1]);
            $comercialid = 1;
        }

        $instancias = Sainsta::selectRaw("Descrip as label, descrip, id, nivel, codinst, codalte")
            ->whereRaw("nivel=1 AND insPadre=0 AND tipoins=0 and comercial = $comercialid")
            ->orderBy('descrip','asc')
            ->get();

        $allsucursales = Sasucursal::where('fk_comercial', $comercialid)
                        ->whereRaw("id in ($arraysucursales)")
                        ->orderBy('descrip','asc')->get();

        $fechasreport = $request->fechasreport ?? '';
        $fksucursal   = $request->fksucursal ?? '';
        $fkestacion   = $request->fkestacion ?? '';

        $fechashoy    = Carbon::now()->format('d/m/Y');
        $nofilterdate = 0;
        if(!$fechasreport) {
            $nofilterdate = 1;
            $fechasreport = $fechashoy;
        }

        $fechasaux = str_replace(' ','',$fechasreport);
        $fec1 = $fec2 = '';

        if(strpos($fechasaux,"to"))
            list($fec1, $fec2) = explode("to",$fechasaux);
        else {
            if(!$nofilterdate) {
                list($d1, $m1, $y1) = explode("/", $fechasreport);
                $fec1 = "$d1/$m1/$y1";
                $fec2 = $fec1;
                $fechasreport = "$fec1 to $fec2";
            }else{
                list($d1, $m1, $y1) = explode("/", $fechasreport);
                $fec1 = "$d1/$m1/$y1";
                $fec2 = "$d1/$m1/$y1";
                $fechasreport = "$fec1 to $fec2";
            }
        }

        $fecha1 = $fec1;
        $fecha2 = $fec2;

        list($d1,$m1,$y1) = explode("/",$fec1);
        list($d2,$m2,$y2) = explode("/",$fec2);

        $fec1 = "$y1-$m1-$d1";
        $fec2 = "$y2-$m2-$d2";

        $results = DB::table('safact as f')
            ->select([
                'f.fk_sucursal',
                'f.numerod',
                'f.codesta',
                'b.coditem',
                'd.codinst',
                DB::raw('((f.dolares-f.vuelto_dolares)*f.Signo) as dolares'),
                DB::raw('(f.pesos*f.Signo) as pesos'),
                DB::raw('(f.peso_tranf*f.Signo) as peso_tranf'),
                DB::raw('(f.euros*f.Signo) as euros'),
                DB::raw('(f.dolar_transf*f.Signo) as transf'),
                DB::raw('((f.cancele-f.efectivosumado-f.vuelto_cancele)*f.Signo) as cancele'),
                DB::raw('((f.vuelto_cancele)*f.Signo) as vuelto_cancele'),
                DB::raw('(f.mtotax*f.Signo) as mtotax'),
                DB::raw('(f.TGravable*f.Signo) as montobase'),
                DB::raw('((f.cancelt-f.tarjetasumado)*f.Signo) as cancelt'),
                DB::raw('(f.texento*f.Signo) as texentofact'),
                'e.descrip as instancia',
                'e.codalte',
                DB::raw('((f.mtototal*f.Signo)/f.tasa_dolar) as mtototal'),
                DB::raw('(((f.contado+f.credito)*f.Signo)/f.tasa_dolar) as totalventa'),
                DB::raw('((f.credito*f.Signo)/f.tasa_dolar) as credito'),
                DB::raw('((f.contado*f.Signo)/f.tasa_dolar) as contado'),
                DB::raw('(b.costodoriginal * b.cantidad * f.signo) as precioventa'),
                'a.descrip',
                DB::raw('(b.Cantidad*f.signo) as cant'),
                DB::raw('(b.Cantidad*(b.preciod)*f.signo) as preciod'),
                DB::raw('(((b.costodoriginal - b.preciod) * f.signo) * b.cantidad) as resta'),
                DB::raw('(((b.costodoriginal - b.preciod)/b.costodoriginal) * f.signo * b.cantidad) as utilidad'),
                DB::raw('((b.costodoriginal) * f.signo * b.cantidad) as basesuma'),
                'g.codvend',
                'g.descrip as vendedor',
                DB::raw('(b.costodoriginal * b.cantidad * f.Signo) AS venta')
            ])
            ->join('saitemfac as b', function($join) {
                $join->on('b.fk_sucursal', '=', 'f.fk_sucursal')
                    ->on('b.numerod', '=', 'f.numerod')
                    ->on('b.tipofac', '=', 'f.tipofac')
                    ->where('b.nrolineac', '=', 0)
                    ->where('b.EsServ', '=', 0)
                    ->where('b.costodoriginal', '>', 0);
            })
            ->join('saprod as d', function($join) use ($comercialid) {
                $join->on('d.codprod', '=', 'b.coditem')
                    ->where('d.comercial', $comercialid);
            })
            ->join('sainsta as e', function($join) use ($comercialid) {
                $join->on('e.CodInst', '=', 'd.CodInst')
                    ->where('e.TipoIns', '=', 0)
                    ->where('e.comercial', '=', $comercialid);
            })
            ->join('sasucursal as a', function($join) use ($comercialid, $fksucursal) {
                $join->on('a.id', '=', 'f.fk_sucursal')
                    ->where('a.fk_comercial', '=', $comercialid);
                if($fksucursal > 0) {
                    $join->where('a.id', '=', $fksucursal);
                }
            })
            ->join('savend as g', function($join) use ($comercialid) {
                $join->on('g.codvend', '=', 'f.codvend')
                    ->where('g.comercial', '=', $comercialid);
            })
            ->whereIn('f.tipofac', ['Z', 'W'])
            ->whereRaw("f.fk_sucursal in ($arraysucursales)");

        // Filtro por estación si se seleccionó
        if(!empty($fkestacion)) {
            $results = $results->where('f.codesta', $fkestacion);
        }

        $results = $results->whereBetween('f.FechaE', ["$y1-$m1-$d1 00:00:00", "$y2-$m2-$d2 23:58:22"])
            ->orderBy('f.numerod')
            ->get();

        $checkfact  = [];
        $sucursales = [];
        $arrayinsta = [];
        $listado    = [];
        $resultsven = [];
        $vendedores = [];
        $vsucursal  = [];

        if(isset($results)) {
            foreach ($results as $venta) {
                if(!isset($sucursales[$venta->fk_sucursal])){
                    $sucursales[$venta->fk_sucursal] = $venta->descrip;
                }

                if(!isset($vendedores[$venta->codvend])){
                    $vendedores[$venta->codvend]['descrip'] = $venta->vendedor;
                    $vendedores[$venta->codvend]['cant']    = 0;
                    $vendedores[$venta->codvend]['venta']   = 0;
                }

                $vendedores[$venta->codvend]['cant']    += $venta->cant;
                $vendedores[$venta->codvend]['venta']   += $venta->venta;

                if(!isset($vsucursal[$venta->fk_sucursal])){
                    $vsucursal[$venta->fk_sucursal]['descrip'] = $venta->descrip;
                    $vsucursal[$venta->fk_sucursal]['cant']    = 0;
                    $vsucursal[$venta->fk_sucursal]['venta']   = 0;
                }

                $vsucursal[$venta->fk_sucursal]['cant']    += $venta->cant;
                $vsucursal[$venta->fk_sucursal]['venta']   += $venta->venta;

                if(!isset($checkfact[$venta->numerod])){
                    $checkfact[$venta->numerod] = 1;

                    if(!isset($listado[$venta->fk_sucursal]['dolares']))
                        $listado[$venta->fk_sucursal]['dolares'] =0;
                    $listado[$venta->fk_sucursal]['dolares'] += $venta->dolares;

                    if(!isset($listado[$venta->fk_sucursal]['pesos']))
                        $listado[$venta->fk_sucursal]['pesos'] =0;
                    $listado[$venta->fk_sucursal]['pesos']   += $venta->pesos;

                    if(!isset($listado[$venta->fk_sucursal]['peso_tranf']))
                        $listado[$venta->fk_sucursal]['peso_tranf'] =0;
                    $listado[$venta->fk_sucursal]['peso_tranf'] += $venta->peso_tranf;

                    if(!isset($listado[$venta->fk_sucursal]['euros']))
                        $listado[$venta->fk_sucursal]['euros'] =0;
                    $listado[$venta->fk_sucursal]['euros']   += $venta->euros;

                    if(!isset($listado[$venta->fk_sucursal]['transf']))
                        $listado[$venta->fk_sucursal]['transf'] =0;
                    $listado[$venta->fk_sucursal]['transf']  += $venta->transf;

                    if(!isset($listado[$venta->fk_sucursal]['cancele']))
                        $listado[$venta->fk_sucursal]['cancele'] =0;
                    $listado[$venta->fk_sucursal]['cancele'] += $venta->cancele;

                    if(!isset($listado[$venta->fk_sucursal]['vuelto_cancele']))
                        $listado[$venta->fk_sucursal]['vuelto_cancele'] =0;
                    $listado[$venta->fk_sucursal]['vuelto_cancele'] += $venta->vuelto_cancele;

                    if(!isset($listado[$venta->fk_sucursal]['cancelt']))
                        $listado[$venta->fk_sucursal]['cancelt'] =0;
                    $listado[$venta->fk_sucursal]['cancelt'] += $venta->cancelt;

                    if(!isset($listado[$venta->fk_sucursal]['credito']))
                        $listado[$venta->fk_sucursal]['credito'] =0;
                    $listado[$venta->fk_sucursal]['credito'] += $venta->credito;

                    if(!isset($listado[$venta->fk_sucursal]['totalventa']))
                        $listado[$venta->fk_sucursal]['totalventa'] =0;
                    $listado[$venta->fk_sucursal]['totalventa'] += $venta->totalventa;
                }

                foreach ($instancias as $instancia) {
                    $len = strlen($instancia->codalte);
                    if(substr($venta->codalte,0, $len) == $instancia->codalte){

                        if(!isset($arrayinsta[$instancia->codinst]))
                            $arrayinsta[$instancia->codinst] = [];

                        $arrayinsta[$instancia->codinst]['descrip'] = $instancia->descrip;
                        $arrayinsta[$instancia->codinst]['codalte'] = $instancia->codalte;

                        if(!isset($arrayinsta[$instancia->codinst]['cant']))
                            $arrayinsta[$instancia->codinst]['cant'] = 0;
                        if(!isset($arrayinsta[$instancia->codinst]['resta']))
                            $arrayinsta[$instancia->codinst]['resta'] = 0;
                        if(!isset($arrayinsta[$instancia->codinst]['preciod']))
                            $arrayinsta[$instancia->codinst]['preciod'] = 0;
                        if(!isset($arrayinsta[$instancia->codinst]['basesuma']))
                            $arrayinsta[$instancia->codinst]['basesuma'] = 0;
                        if(!isset($arrayinsta[$instancia->codinst]['precioventa']))
                            $arrayinsta[$instancia->codinst]['precioventa'] = 0;

                        $arrayinsta[$instancia->codinst]['cant']        += $venta->cant;
                        $arrayinsta[$instancia->codinst]['resta']       += $venta->resta;
                        $arrayinsta[$instancia->codinst]['preciod']     += $venta->preciod;
                        $arrayinsta[$instancia->codinst]['basesuma']    += $venta->basesuma;
                        $arrayinsta[$instancia->codinst]['precioventa'] += $venta->precioventa;
                    }
                }
            }
        }

        return view('reporteVentas', compact(
            'fechasreport',
            'vsucursal',
            'vendedores',
            'arrayinsta',
            'sucursales',
            'allsucursales',
            'fksucursal',
            'fkestacion',
            'fecha1',
            'fecha2',
            'listado'
        ));
    }

    public function reporteventasucu(Request $request)
    {
        $arraysucursales = auth()->user()->getSucursalesIdsComercialActual();
        $arraysucursales = implode(",",$arraysucursales);

        $comercialid = session('comercialid');
        if(!$comercialid) {
            session(['comercialid' => 1]);
            $comercialid = 1;
        }

        $fechasreport = $request->fechasreport;
        $fksucursal   = $request->fksucursal;
        $contado      = $request->contado;
        $credito      = $request->credito;

        $fechasaux = str_replace(' ','',$fechasreport);
        $fec1 = $fec2 = '';

        if(strpos($fechasaux,"-"))
            list($fec1, $fec2) = explode("-",$fechasaux);
        else {
            list($d1, $m1, $y1) = explode("/", $fechasreport);
            $fec1 = "$d1/$m1/$y1";
            $fec2 = $fec1;
        }

        $fecha1 = $fec1;
        $fecha2 = $fec2;

        list($d1,$m1,$y1) = explode("/",$fec1);
        list($d2,$m2,$y2) = explode("/",$fec2);

        $fec1 = "$y1-$m1-$d1";
        $fec2 = "$y2-$m2-$d2";

        $ventas = Safact::selectRaw("fk_sucursal,nrounico,descrip,numerod, tipofac, codclie,
                            ((dolares-vuelto_dolares)*Signo) as dolares,
                            (pesos*Signo) as pesos,
                            (peso_tranf*Signo) as peso_tranf ,
                            (euros*Signo) as euros,
                            (dolar_transf*Signo) as transf,
                            ((cancele-efectivosumado-vuelto_cancele)*Signo) as cancele,
                             ((vuelto_cancele)*Signo) as vuelto_cancele,

                            (mtotax*Signo) as mtotax,
                            (TGravable*Signo) as montobase,
                            ((cancelt-tarjetasumado)*Signo) as cancelt,
                            (texento*Signo) as texentofact,
                            (igtf_cancele*Signo) as igtf_cancele,
                            (igtf_cancelt*Signo) as igtf_cancelt ,
                            (igtf_dolares*Signo) as igtf_dolares  ,
                            (igtf_pesos*Signo) as igtf_pesos,
                            (igtf_dolar_transf*Signo) as igtf_transf,
                             (igtf_monto*Signo) as igtf_monto ,
                             ((mtototal*Signo)/tasa_dolar) as mtototal,
                             (((contado+credito)*Signo)/tasa_dolar) as totalventa,
                             (((cancelaUSD)*Signo)) as cancelaUSD,
                             ((credito*Signo)/tasa_dolar) as credito,
                             ((contado*Signo)/tasa_dolar) as contado
                            ")
            ->whereRaw(" TipoFac in('A','B','Z','W') and fk_sucursal = $fksucursal")
            ->whereRaw("f.fk_sucursal in ($arraysucursales)")
            ->whereBetween('fechat', [$fec1.' 00:00:00.00', $fec2.' 23:58:22.00']);

        if($credito == 1)
            $ventas = $ventas->whereRaw("credito > 10");

        $ventas = $ventas->get();

        $listado    = [];
        if(isset($ventas))
            foreach ($ventas as $venta) {

                if(!isset($listado[$venta->nrounico]['codclie']))
                    $listado[$venta->nrounico]['codclie'] ='';
                $listado[$venta->nrounico]['codclie'] = $venta->codclie;

                if(!isset($listado[$venta->nrounico]['fksucu']))
                    $listado[$venta->nrounico]['fksucu'] ='';
                $listado[$venta->nrounico]['fksucu'] = $fksucursal;

                if(!isset($listado[$venta->nrounico]['numerod']))
                    $listado[$venta->nrounico]['numerod'] ='';
                $listado[$venta->nrounico]['numerod'] = $venta->numerod;

                if(!isset($listado[$venta->nrounico]['tipofac']))
                    $listado[$venta->nrounico]['tipofac'] ='';
                $listado[$venta->nrounico]['tipofac'] = $venta->tipofac;

                if(!isset($listado[$venta->nrounico]['dolares']))
                    $listado[$venta->nrounico]['dolares'] =0;
                $listado[$venta->nrounico]['dolares'] = $venta->dolares;

                if(!isset($listado[$venta->nrounico]['cliente']))
                    $listado[$venta->nrounico]['cliente'] ='';
                $listado[$venta->nrounico]['cliente'] =$venta->descrip;

                if(!isset($listado[$venta->nrounico]['pesos']))
                    $listado[$venta->nrounico]['pesos'] =0;
                $listado[$venta->nrounico]['pesos']   = $venta->pesos;

                if(!isset($listado[$venta->nrounico]['peso_tranf']))
                    $listado[$venta->nrounico]['peso_tranf'] =0;
                $listado[$venta->nrounico]['peso_tranf'] = $venta->peso_tranf;

                if(!isset($listado[$venta->nrounico]['euros']))
                    $listado[$venta->nrounico]['euros'] =0;
                $listado[$venta->nrounico]['euros']   = $venta->euros;

                if(!isset($listado[$venta->nrounico]['transf']))
                    $listado[$venta->nrounico]['transf'] =0;
                $listado[$venta->nrounico]['transf']  = $venta->transf;

                if(!isset($listado[$venta->nrounico]['cancele']))
                    $listado[$venta->nrounico]['cancele'] =0;
                $listado[$venta->nrounico]['cancele'] = $venta->cancele;

                if(!isset($listado[$venta->nrounico]['vuelto_cancele']))
                    $listado[$venta->nrounico]['vuelto_cancele'] =0;
                $listado[$venta->nrounico]['vuelto_cancele'] = $venta->vuelto_cancele;

                if(!isset($listado[$venta->nrounico]['cancelt']))
                    $listado[$venta->nrounico]['cancelt'] =0;
                $listado[$venta->nrounico]['cancelt'] = $venta->cancelt;

                if(!isset($listado[$venta->nrounico]['credito']))
                    $listado[$venta->nrounico]['credito'] =0;
                $listado[$venta->nrounico]['credito'] = $venta->credito;

                if(!isset($listado[$venta->nrounico]['cancelaUSD']))
                    $listado[$venta->nrounico]['cancelaUSD'] =0;
                $listado[$venta->nrounico]['cancelaUSD'] = $venta->cancelaUSD;

                if(!isset($listado[$venta->nrounico]['totalventa']))
                    $listado[$venta->nrounico]['totalventa'] =0;
                $listado[$venta->nrounico]['totalventa'] = $venta->totalventa;
            }


        $cobranzas = Saacxcw::selectRaw("(cancele - (dolares*tasadolar)) as cancele, codusua, (cancelt - (dolar_tranf*tasadolar)) as cancelt, dolar_tranf as transf, dolares, codclie,
          date_format(FechaT, '%d/%m/%Y') as fecha, codvend, Document, nrounico, euros,cancelausd,
        tasadolar, pesos, peso_tranf, tasapeso, numerod, tipocxc, montodolares, fk_sucursal ")
            ->with([ 'cliente',
                'sucursal.comercial:id',
            ])
            ->whereRaw("fk_sucursal in ($arraysucursales)")
            ->whereRaw(" (tipocxc = 50 or EsUnPago = 1)  and fk_sucursal = $fksucursal")
            ->whereBetween('fechat', [$fec1.' 00:00:00.00', $fec2.' 23:58:22.00'])
            ->whereHas('sucursal.comercial', function ($q) use ($comercialid) {
                $q->where('fk_comercial', $comercialid);
            })
            ->get();
        $listadoc = [];
        if(isset($cobranzas))
            foreach ($cobranzas as $cobranza) {
                if(!isset($listadoc[$cobranza->nrounico]['codclie']))
                    $listadoc[$cobranza->nrounico]['codclie'] ='';
                $listadoc[$cobranza->nrounico]['codclie'] = $cobranza->codclie;

                if(!isset($listadoc[$cobranza->nrounico]['fksucu']))
                    $listadoc[$cobranza->nrounico]['fksucu'] ='';
                $listadoc[$cobranza->nrounico]['fksucu'] = $fksucursal;

                if(!isset($listadoc[$cobranza->nrounico]['numerod']))
                    $listadoc[$cobranza->nrounico]['numerod'] ='';
                $listadoc[$cobranza->nrounico]['numerod'] = $cobranza->numerod;

                if(!isset($listadoc[$cobranza->nrounico]['dolares']))
                    $listadoc[$cobranza->nrounico]['dolares'] =0;
                $listadoc[$cobranza->nrounico]['dolares'] = $cobranza->dolares;

                if(!isset($listadoc[$cobranza->nrounico]['cliente']))
                    $listadoc[$cobranza->nrounico]['cliente'] ='';
                $listadoc[$cobranza->nrounico]['cliente'] =$cobranza->cliente->descrip;

                if(!isset($listadoc[$cobranza->nrounico]['pesos']))
                    $listadoc[$cobranza->nrounico]['pesos'] =0;
                $listadoc[$cobranza->nrounico]['pesos']   = $cobranza->pesos;

                if(!isset($listadoc[$cobranza->nrounico]['peso_tranf']))
                    $listadoc[$cobranza->nrounico]['peso_tranf'] =0;
                $listadoc[$cobranza->nrounico]['peso_tranf'] = $cobranza->peso_tranf;

                if(!isset($listadoc[$cobranza->nrounico]['euros']))
                    $listadoc[$cobranza->nrounico]['euros'] =0;
                $listadoc[$cobranza->nrounico]['euros']   = $cobranza->euros;

                if(!isset($listadoc[$cobranza->nrounico]['transf']))
                    $listadoc[$cobranza->nrounico]['transf'] =0;
                $listadoc[$cobranza->nrounico]['transf']  = $cobranza->transf;

                if(!isset($listadoc[$cobranza->nrounico]['cancele']))
                    $listadoc[$cobranza->nrounico]['cancele'] =0;
                $listadoc[$cobranza->nrounico]['cancele'] = ($cobranza->cancele > 1) ? $cobranza->cancele : 0;

                if(!isset($listadoc[$cobranza->nrounico]['cancelt']))
                    $listadoc[$cobranza->nrounico]['cancelt'] =0;
                $listadoc[$cobranza->nrounico]['cancelt'] = ($cobranza->cancelt > 1) ? $cobranza->cancelt : 0;

                if(!isset($listadoc[$cobranza->nrounico]['cancelausd']))
                    $listadoc[$cobranza->nrounico]['cancelausd'] =0;
                $listadoc[$cobranza->nrounico]['cancelausd'] = $cobranza->cancelausd;

                if(!isset($listadoc[$cobranza->nrounico]['totalcobranza']))
                    $listadoc[$cobranza->nrounico]['totalcobranza'] =0;
                $listadoc[$cobranza->nrounico]['totalcobranza'] = $cobranza->montodolares;
            }

        $topprod = Saitemfac::whereIn('TipoFac', ['A', 'B','Z', 'W'])
            ->selectRaw("CodItem, SUM(Cantidad * Signo) as salidas ")
            ->where('esserv', 0)
            ->whereRaw("fk_sucursal in ($arraysucursales)")
            ->where('nrolineac', 0)
            ->whereBetween('FechaE', ["{$fec1} 00:00:00.00", "{$fec2} 23:59:59.00"])
            ->with([
                'factura.sucursal.comercial:id',
                'producto:codprod,Descrip',
            ])
            ->groupBy(["CodItem"])
            ->where('fk_sucursal',$fksucursal)
            ->orderByDesc('salidas');

        if($credito == 1){
            $topprod = $topprod->whereHas('factura', function ($q) use ($fksucursal) {
                $q->whereRaw("safact.credito > 0 and safact.fk_sucursal = $fksucursal");
            });
        }
        $topprod = $topprod->get();

        //dd($topprod->toSql(), $topprod->getBindings());
        return view('reporteventasucursal', compact('fechasreport','listadoc', 'topprod', 'ventas', 'fecha1', 'fecha2', 'listado'))->render();
    }

    public function index(Request $request)
    {
         //dd(bcrypt('StarsMotors555'));
        $comercialid = session('comercialid');
        if(!$comercialid) {
            session(['comercialid' => 1]);
            $comercialid = 1;
        }

        Session::put('lang', 'sp');
        Session::save();

        if(Auth::user() and auth()->user()->type == 'admin') {
            return view('index');
        }else{
            return view('indexusuario', );
        }

    }

    public function lang($locale) {
        if ($locale) {
            App::setLocale($locale);
            Session::put('lang', $locale);
            Session::save();
            return redirect()->back()->with('locale', $locale);
        } else {
            return redirect()->back();
        }
    }
}
