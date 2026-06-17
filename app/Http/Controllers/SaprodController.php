<?php

namespace App\Http\Controllers;

use App\Exports\PlantillaProductosExport;
use App\Exports\SaprodExport;
use App\Imports\ProductosImport;
use App\Imports\SaprodUpdate;
use App\Models\NewSaexis;
use App\Models\Sacomercial;
use App\Models\Sainsta;
use App\Models\Saitemfac;
use App\Models\Saprod;
use App\Models\Saprodsucursal;
use App\Models\Saserv;
use App\Models\Sasucursal;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Maatwebsite\Excel\Facades\Excel;

class SaprodController extends Controller
{
    public function descargarPlantilla()
    {
        try {
            return Excel::download(new PlantillaProductosExport(), 'plantilla_productos_' . date('Y-m-d') . '.xlsx');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al generar la plantilla: ' . $e->getMessage());
        }
    }

    public function importarcrear(Request $request)
    {
        $request->validate([
            'archivo_productos' => 'required|file|mimes:xlsx,xls,csv|max:10240',
        ]);

        try {
            $import = new ProductosImport();
            Excel::import($import, $request->file('archivo_productos'));

            $mensaje = "Importación completada. Productos creados: {$import->getProcesados()}";

            if ($import->getFallidos() > 0) {
                $mensaje .= ". Fallidos: {$import->getFallidos()}";
                session()->flash('errores_importacion', $import->getErrores());
            }

            return redirect()->back()->with('success', $mensaje);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al importar: ' . $e->getMessage());
        }
    }

    public function operacionesSerial(Request $request)
    {
        $comercialid = session('comercialid');
        if(!$comercialid) {
            session(['comercialid' => 1]);
            $comercialid = 1;
        }

        $codprod = (isset($request->codprod))? $request->codprod : 'MOT066';
        $serial  = (isset($request->serial))? $request->serial : 'SC 2752 SM 6925 PLACA AL9T78N';
        $serial  = urldecode($serial);

        $sucursales = Sasucursal::where("fk_comercial", $comercialid)->get();

        $compras = DB::table('saseprcom')
            ->select([
                'id',
                'tipocom as tipo',
                'numerod',
                'created_at',
                DB::raw("date_format(created_at,'%d/%m/%Y') as fecha"),
                'fk_sucursal',
                'coditem',
                DB::raw("'COMPRA' as tipo_movimiento")
            ])
            ->where('nroserial', $serial);

        $ventas = DB::table('saseprfac')
            ->select([
                'id',
                'TipoFac as tipo',
                'numerod',
                'created_at',
                DB::raw("date_format(created_at,'%d/%m/%Y') as fecha"),
                'fk_sucursal',
                'coditem',
                DB::raw("'VENTA' as tipo_movimiento")
            ])
            ->where('nroserial', $serial);

        $operaciones = DB::table('sasepropi')
            ->select([
                'id',
                'tipoopi as tipo',
                'numerod',
                'created_at',
                DB::raw("date_format(created_at,'%d/%m/%Y') as fecha"),
                'fk_sucursal',
                'coditem',
                DB::raw("'OPERACION_INVENTARIO' as tipo_movimiento")
            ])
            ->where('NroSerial', $serial);

        $operacionesrep = $compras->union($ventas)->union($operaciones)
            ->orderBy('created_at', 'asc')
            ->get();

        return view('operaciones-list', compact(  'operacionesrep', 'sucursales', 'codprod', 'serial') );
    }

    public function saprodexport($codalte)
    {
        $file = Excel::download(new SaprodExport($codalte), 'productos.xlsx');

        return $file;
    }

    public function inventarios(Request $request){
        $comercialid = session('comercialid');
        if(!$comercialid) {
            session(['comercialid' => 1]);
            $comercialid = 1;
        }

        $sqlcostoinv = "SELECT sum((a.preciod + a.preciod2)*b.existen) as suma, c.descrip
                                    from   saprod a , saexis b, sasucursal c
                                    where  a.codprod = b.codprod
                                    and b.fk_sucursal = c.id
                                    and a.comercial = $comercialid
                                    and c.fk_comercial = $comercialid
                                group by  c.descrip order by c.descrip
                        union

                           SELECT sum((a.preciod + a.preciod2)*b.existen) as suma, c.descrip
                                    from   saprod a , newsaexis b, sasucursal c
                                    where  a.codprod = b.codprod
                                    and b.fk_sucursal = c.id
                                    and a.comercial = $comercialid
                                    and c.fk_comercial = $comercialid
                                group by  c.descrip order by c.descrip


                                    ";

        $costoinven = DB::select($sqlcostoinv);

        return view('reporteInventarios', compact('costoinven') );
    }

    public function buscarproductoget($codprod, $comercial){

        $producto   = Saprod::where(['codprod'=>$codprod, "comercial" => $comercial])->first();
        session(['comercialid' => $comercial]);
        if(isset($producto) and isset($producto->id)){
            $instancias = Sainsta::selectRaw("concat( repeat('&nbsp;',((nivel-1)*4)), Descrip ) as label, descrip, id, nivel, codinst ")
                ->with(['padre'])
                ->orderBy('codalte','asc')->get();

            $id = $producto->id;
            return view('product-edit', compact('instancias','producto', 'id'));
        }else{
            return response()->redirectTo('index');
        }

    }

    public function updateSaprodData(Request $request)
    {
        $request->validate([
            'import_file' => [
                'required',
                'file'
            ],
        ]);

        Excel::import(new SaprodUpdate(), $request->file('import_file'));

        return redirect()->back()->with('status', 'Archivo Procesado Exitosamente');
    }

    public function index(Request $request)
    {
        $comercialid = session('comercialid');
        if(!$comercialid) {
            session(['comercialid' => 1]);
            $comercialid = 1;
        }

        $sucursales = Sasucursal::where("fk_comercial", $comercialid)->get();

        $fechasaux      = '';
        $operacionesrep = '';

        $fechasreport   = (isset($request->fechasreport))? $request->fechasreport : '';
        $codprod        = (isset($request->codprod))? $request->codprod : '';
        $fechashoy      =  Carbon::now()->format('d/m/Y');
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

        $instancias = Sainsta::selectRaw("  Descrip as label, descrip, id, nivel, codinst , codalte")
            ->with(['padre','hijos',  'productos'])
            ->where('comercial',$comercialid)
            ->orderBy('codalte','asc')
            ->get();

        // OBTENER ÚLTIMOS PRODUCTOS CREADOS
        $ultimosProductos = Saprod::where('comercial', $comercialid)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Calcular total de productos activos
        $totalProductos = Saprod::where('comercial', $comercialid)
            ->where('activo', 1)
            ->count();

        if(isset($codprod) and $codprod !=''  and isset($aaaa) and $aaaa ==1){

            $compras = DB::table('saitemcom')
                ->select([
                    'id',
                    'tipocom as tipo',
                    'numerod',
                    'FechaE',
                    DB::raw("date_format(fechae,'%d/%m/%Y') as fecha"),
                    DB::raw('(cantidad*signo) as cantidad'),
                    'fk_sucursal',
                    'preciod as costo',
                    'costod as precio',
                    'codubic as dep1',
                    DB::raw("'' as dep2"),
                    'descrip1 as descripcion',
                    DB::raw("'COMPRA' as tipo_movimiento")
                ])
                ->where('coditem', $codprod)
                ->whereBetween('fechae', ["$fec1 00:00:00", "$fec2  23:55:00"]);

            $ventas = DB::table('saitemfac')
                ->select([
                    'id',
                    'TipoFac as tipo',
                    'numerod',
                    'FechaE',
                    DB::raw("date_format(fechae,'%d/%m/%Y') as fecha"),
                    DB::raw('(cantidad*signo) as cantidad'),
                    'fk_sucursal',
                    'preciod as costo',
                    'costod as precio',
                    'codubic as dep1',
                    DB::raw("'' as dep2"),
                    'Descrip1 as descripcion',
                    DB::raw("'VENTA' as tipo_movimiento")
                ])
                ->where('CodItem', $codprod)
                ->whereBetween('FechaE', ["$fec1 00:00:00", "$fec2  23:55:00"]);

            $operaciones = DB::table('saitemopi')
                ->select([
                    'id',
                    'tipoopi as tipo',
                    'numerod',
                    'FechaE',
                    DB::raw("date_format(fechae,'%d/%m/%Y') as fecha"),
                    DB::raw('(cantidad*signo) as cantidad'),
                    'fk_sucursal',
                    'preciod as costo',
                    DB::raw('0 as precio'),
                    'codubic as dep1',
                    'codubic2 as dep2',
                    'Descrip1 as descripcion',
                    DB::raw("'OPERACION_INTERNA' as tipo_movimiento")
                ])
                ->where('CodItem', $codprod)
                ->whereBetween('FechaE', ["$fec1 00:00:00", "$fec2  23:55:00"]);

            $operacionesrep = $compras->union($ventas)->union($operaciones)
                ->orderBy('FechaE', 'asc')
                ->get();
        }

        return view('product-list', compact(
            'instancias',
            'sucursales',
            'codprod',
            'operacionesrep',
            'fechasreport',
            'ultimosProductos',
            'totalProductos',
        ));
    }

    public function existencias(Request $request)
    {
        $arraysucursales = auth()->user()->getSucursalesIdsComercialActual();
        $arraysucursales = implode(",",$arraysucursales);

        $comercial  = session('comercialid') ;
        if(!$comercial) {
            session(['comercialid' => 1]);
            $comercial = 1;
        }

        $instancias = Sainsta::selectRaw("  Descrip as label, descrip, id, nivel, codinst , codalte")
            ->whereRaw("nivel=1 AND insPadre=0 AND tipoins=0")
            ->orderBy('descrip','asc')
            ->get();

        $sucursales  = Sasucursal::where("fk_comercial", $comercial)->whereRaw("id in ($arraysucursales)")->get();
        $sucursalarr = $sucursales->pluck('id');
        $sucursalIds = implode(",", $sucursalarr->toArray());

        $consulta = " SELECT a.descrip as producto, a.codprod, b.codinst, b.codalte, b.nivel, b.insPadre, b.descrip as instancia, d.descrip as sucursal, d.id as sucursalid,
                              (c.existen * a.preciod) as costo,  (c.existen) as cantidad
                      FROM   saprod a, sainsta b, saexis c, sasucursal d
                      WHERE
                              a.comercial    = $comercial
                          AND a.codprod      = c.codprod
                          AND a.codinst      = b.codinst
                          AND c.fk_sucursal  = d.id
                          AND d.id in ($arraysucursales)
                          AND c.fk_sucursal IN ($sucursalIds)
                          and c.existen     <> 0

                       ";

        $query = DB::select($consulta);

        $arraysucursal = array();
        $arrayinstanci = array();
        $arrayproducto = array();

        foreach ($query as $item) {
            if(!isset($arrayproducto[$item->codprod])){
                $arrayproducto[$item->codprod] = $item->producto;
            }

            if(!isset($arraysucursal[$item->sucursalid])){
                $arraysucursal[$item->sucursalid] = [];
            }

            $arraysucursal[$item->sucursalid]['descrip'] = $item->sucursal;
            if(!isset($arraysucursal[$item->sucursalid]['costo']   )) $arraysucursal[$item->sucursalid]['costo']    = 0;
            if(!isset($arraysucursal[$item->sucursalid]['cantidad'])) $arraysucursal[$item->sucursalid]['cantidad'] = 0;
            $arraysucursal[$item->sucursalid]['costo']    += $item->costo;
            $arraysucursal[$item->sucursalid]['cantidad'] += $item->cantidad;

            foreach ($instancias as  $instancia) {
                $len = strlen($instancia->codalte);
                if(substr($item->codalte,0, $len) == $instancia->codalte){

                    if(!isset($arrayinstanci[$instancia->codinst]))             $arrayinstanci[$instancia->codinst] = [];

                    $arrayinstanci[$instancia->codinst]['descrip'] = $instancia->descrip;
                    $arrayinstanci[$instancia->codinst]['codalte'] = $instancia->codalte;

                    if(!isset($arrayinstanci[$instancia->codinst]['costo']   )) $arrayinstanci[$instancia->codinst]['costo']    = 0;
                    if(!isset($arrayinstanci[$instancia->codinst]['cantidad'])) $arrayinstanci[$instancia->codinst]['cantidad'] = 0;
                    $arrayinstanci[$instancia->codinst]['costo']    += $item->costo;
                    $arrayinstanci[$instancia->codinst]['cantidad'] += $item->cantidad;
                }
            }


        }

        return view('existenciasInstancias',
            compact( 'arraysucursal', 'arrayinstanci', 'arrayproducto') );
    }

    public function existenciasphp(Request $request)
    {
        $arraysucursales = auth()->user()->getSucursalesIdsComercialActual();
        $arraysucursales = implode(",",$arraysucursales);

        $codinst    = $request->codinst;
        $fksucursal = $request->fksucursal;


        $comercial  = session('comercialid') ;
        if(!$comercial) {
            session(['comercialid' => 1]);
            $comercial = 1;
        }

        $instancias = Sainsta::selectRaw("  Descrip as label, descrip, id, nivel, codinst , codalte, insPadre")

            ->orderBy('descrip','asc')
            ->get();
        $insPadre = 0;

        $sucursales = Sasucursal::where("fk_comercial", $comercial)->whereRaw("id in ($arraysucursales)")->get();

        $instanciaselected = '';
        foreach ($instancias as $instancia){
            if($instancia->codinst == $codinst){
                $instanciaselected = $instancia;
                $insPadre = $instancia->insPadre;
                break;
            }
        }
        return view('existenciasInstanciasphp', compact('fksucursal', 'insPadre', 'codinst', 'sucursales', 'instancias', 'instanciaselected', 'comercial') )->render();
    }

    public function viewprodinstsanciascodalte(Request $request)
    {
        $comercial  = session('comercialid') ;
        if(!$comercial) {
            session(['comercialid' => 1]);
            $comercial = 1;
        }

        $codalte     = $request->codalte;
        $len         = strlen($codalte);

        $sqlcostoinv = "SELECT a.preciod, a.descrip, a.codprod, e.codubic, b.existen, e.descrip as deposito
								from saprod a , saexis b, sasucursal c, sainsta d, sadepo e
								where a.codprod    = b.codprod
                                and b.fk_sucursal  = c.id
								and b.codubic      = e.codubic
                                and c.fk_comercial = $comercial
								and a.comercial    = $comercial
								and d.comercial    = $comercial
								and e.comercial    = $comercial
								and d.codinst      = a.codinst
                                and left(d.codalte,$len) = '$codalte'
								and b.existen <> 0
                                order by a.descrip
								";

        $listado = DB::select($sqlcostoinv);

        $productos    = [];
        $deposito     = [];
        $existencias  = [];

        foreach($listado as $producto){

            if(!isset($productos[$producto->codprod]))
                $productos[$producto->codprod] = [];

            $productos[$producto->codprod]['descrip']    = $producto->descrip;
            $productos[$producto->codprod]['preciod']    = $producto->preciod;

            if(!isset($deposito[$producto->codubic]))
                $deposito[$producto->codubic] = $producto->deposito;

            if(!isset($existencias[$producto->codprod][$producto->codubic]))
                $existencias[$producto->codprod][$producto->codubic] = 0;

            $existencias[$producto->codprod][$producto->codubic] = $producto->existen;
        }

        return view('productosallinstsancias', compact('productos', 'deposito', 'existencias') )->render();


    }

    public function existenciasMotos()
    {
        $arraysucursales = auth()->user()->getSucursalesIdsComercialActual();
        $arraysucursales = implode(",",$arraysucursales);

        $comercial  = session('comercialid') ;
        if(!$comercial) {
            session(['comercialid' => 1]);
            $comercial = 1;
        }

        $instancias = Sainsta::selectRaw("  Descrip as label, descrip, id, nivel, codinst , codalte")
            ->whereRaw("nivel=2 AND   tipoins=0 and codalte like '01.%'")
            ->orderBy('descrip','asc')->get();

        $sucursales  = Sasucursal::where("fk_comercial", $comercial)->whereRaw("id in ($arraysucursales)")->get();
        $sucursalarr = $sucursales->pluck('id');
        $sucursalIds = implode(",", $sucursalarr->toArray());

        $sucursales  = Sasucursal::where("fk_comercial", $comercial)->get();

        /*$consulta = " SELECT  b.inspadre,  c.fk_sucursal, sum(c.existen) as cantidad
                      FROM   saprod a, sainsta b, newsaexis c, sasucursal d
                      WHERE   a.comercial    = $comercial
                          AND a.codprod      = c.codprod
                          AND a.codinst      = b.codinst
                          AND c.fk_sucursal  = d.id
                          AND b.inspadre     <>104
                          AND c.fk_sucursal IN ($sucursalIds)
                          AND c.existen      <> 0

                     group by c.fk_sucursal, b.inspadre

                       ";*/
        $query = DB::table('saprod as productos')
            ->join('sainsta as i', 'productos.codinst', '=', 'i.codinst')
            ->join('newsaexis as e', 'productos.codprod', '=', 'e.codprod')
            ->join('sasucursal as s', 'e.fk_sucursal', '=', 's.id')
            ->select(
                'i.inspadre',
                'e.fk_sucursal',
                DB::raw('SUM(e.existen) as total_cantidad')
            )
            ->where('productos.comercial', $comercial)
            ->where('i.inspadre', '<>', 104)
            ->whereRaw("e.fk_sucursal in ($sucursalIds)")
            ->where('e.existen', '>', 0)
            ->groupBy('e.fk_sucursal', 'i.inspadre')
            ->having('total_cantidad', '>', 0)
            ->orderBy('i.inspadre')
            ->orderBy('e.fk_sucursal')
            ->get();

        //$query = DB::select($consulta);

        $vectorsucursales = [];
        foreach ($sucursales as $sucursal){
            if(!isset($vectorsucursales[$sucursal->id])){
                $vectorsucursales[$sucursal->id] = $sucursal->descrip;
            }
        }

        $vectorinstancias = [];
        foreach ($instancias as $instancia){
            if(!isset($vectorinstancias[$instancia->codinst])){
                $vectorinstancias[$instancia->codinst] = $instancia->descrip;
            }
        }

        $arraysucursal = array();
        $arrayinstanci = array();
        $arraycantidad = array();


        foreach ($query as $item) {
            if(!isset($arraysucursal[$item->fk_sucursal]))
                $arraysucursal[$item->fk_sucursal] = $vectorsucursales[$item->fk_sucursal];

            if(!isset($arrayinstanci[$item->inspadre]) and isset($vectorinstancias[$item->inspadre]))
                $arrayinstanci[$item->inspadre] = $vectorinstancias[$item->inspadre];

            if(!isset($arraycantidad[$item->inspadre][$item->fk_sucursal]))
                $arraycantidad[$item->inspadre][$item->fk_sucursal] = 0;

            $arraycantidad[$item->inspadre][$item->fk_sucursal] += $item->total_cantidad;
        }

        asort($arrayinstanci);

        return view('existenciasMotos', compact( 'arraysucursal', 'arrayinstanci', 'arraycantidad') );
    }

    public function existenciasMotosModelos(Request $request)
    {
        $arraysucursales = auth()->user()->getSucursalesIdsComercialActual();
        $arraysucursales = implode(",",$arraysucursales);

        $comercial  = session('comercialid') ;
        if(!$comercial) {
            session(['comercialid' => 1]);
            $comercial = 1;
        }

        $inspadre   = $request->inspadre;

        $instancias = Sainsta::selectRaw("  Descrip as label, descrip, id, nivel, codinst , codalte")
            ->whereRaw("nivel=3 AND  inspadre = $inspadre and  tipoins = 0 and codalte like '01.%'")
            ->orderBy('descrip','asc')->get();


        $sucursales  = Sasucursal::where("fk_comercial", $comercial)->whereRaw("id in ($arraysucursales)")->get();
        $sucursalarr = $sucursales->pluck('id');
        $sucursalIds = implode(",", $sucursalarr->toArray());

        $sucursales  = Sasucursal::where("fk_comercial", $comercial)->whereRaw("id in ($arraysucursales)")->get();

        $query = DB::table('saprod as productos')
            ->join('sainsta as i', 'productos.codinst', '=', 'i.codinst')
            ->join('newsaexis as e', 'productos.codprod', '=', 'e.codprod')
            ->join('sasucursal as s', 'e.fk_sucursal', '=', 's.id')
            ->select(
                'i.codinst',
                        'e.fk_sucursal',
                DB::raw('SUM(e.existen) as total_cantidad')
            )
            ->where('productos.comercial', $comercial)
            ->whereRaw("e.fk_sucursal in ($sucursalIds)")
            ->where('e.existen', '>', 0)
            ->groupBy('e.fk_sucursal','i.codinst')
            ->having('total_cantidad', '>', 0)
            ->orderBy('e.fk_sucursal')
            ->get();

        //$query = DB::select($consulta);

        $vectorsucursales = [];
        foreach ($sucursales as $sucursal){
            if(!isset($vectorsucursales[$sucursal->id])){
                $vectorsucursales[$sucursal->id] = $sucursal->descrip;
            }
        }

        $vectorinstancias = [];
        foreach ($instancias as $instancia){
            if(!isset($vectorinstancias[$instancia->codinst])){
                $vectorinstancias[$instancia->codinst] = $instancia->descrip;
            }
        }

        $arraysucursal = array();
        $arrayinstanci = array();
        $arraycantidad = array();


        foreach ($query as $item) {
            if(!isset($arraysucursal[$item->fk_sucursal]))
                $arraysucursal[$item->fk_sucursal] = $vectorsucursales[$item->fk_sucursal];

            if(!isset($arrayinstanci[$item->codinst]) and isset($vectorinstancias[$item->codinst]))
                $arrayinstanci[$item->codinst] = $vectorinstancias[$item->codinst];

            if(!isset($arraycantidad[$item->codinst][$item->fk_sucursal]))
                $arraycantidad[$item->codinst][$item->fk_sucursal] = 0;

            $arraycantidad[$item->codinst][$item->fk_sucursal] += $item->total_cantidad;
        }

        asort($arrayinstanci);
        $ajax = ($request->ajax())? 1 : 0;
        $html = view('existenciasMotosModelos', compact('ajax', 'inspadre', 'arraysucursal',  'arrayinstanci', 'arraycantidad') )->render();

        if ($ajax){
            return $html;
        }else{
            return  view('existenciasMotosModelosPrint',compact('html'));
        }
    }

    public function newexistencias(Request $request)
    {
        $comercialid  = session('comercialid') ;
        if(!$comercialid) {
            session(['comercialid' => 1]);
            $comercialid = 1;
        }

        $fksucursal    = (isset($request->fksucursal ))? $request->fksucursal : '';

        $arraysucursales = auth()->user()->getSucursalesIdsComercialActual();
        $arraysucursales = implode(",",$arraysucursales);
        $sucursales = Sasucursal::whereRaw("id  in ($arraysucursales)")->orderBy('descrip', 'asc')->get();
        $allsucursales = $sucursales;

        $instancias = Sainsta::selectRaw("  Descrip as label, descrip, id, nivel, codinst , codalte")
                        ->whereRaw("nivel=1 AND insPadre=0 AND tipoins=0")
                        ->where('comercial',$comercialid)
                        ->orderBy('descrip','asc')
                        ->get();

        $sucursalarr = $sucursales->pluck('id');
        $sucursalIds = implode(",", $sucursalarr->toArray());

        $consulta = " SELECT a.descrip as producto, a.codprod, b.codinst, b.codalte,
                        b.nivel, b.insPadre, b.descrip as instancia, d.descrip as sucursal, d.id as sucursalid,
                              (c.existen * a.preciod) as costo,  (c.existen) as cantidad
                      FROM   saprod a, sainsta b, newsaexis c, sasucursal d
                      WHERE
                              a.comercial    = $comercialid
                          AND a.codprod      = c.codprod
                          AND a.codinst      = b.codinst
                          AND c.fk_sucursal  = d.id
                          AND c.fk_sucursal IN ($sucursalIds)
                          and c.existen     <> 0

                       ";

        $query = DB::select($consulta);

        $arraysucursal = array();
        $arrayinstanci = array();
        $arrayproducto = array();

        foreach ($query as $item) {
            if(!isset($arrayproducto[$item->codprod])){
                $arrayproducto[$item->codprod] = $item->producto;
            }

            if(!isset($arraysucursal[$item->sucursalid])){
                $arraysucursal[$item->sucursalid] = [];
            }

            $arraysucursal[$item->sucursalid]['descrip'] = $item->sucursal;
            if(!isset($arraysucursal[$item->sucursalid]['costo']   )) $arraysucursal[$item->sucursalid]['costo']    = 0;
            if(!isset($arraysucursal[$item->sucursalid]['cantidad'])) $arraysucursal[$item->sucursalid]['cantidad'] = 0;
            $arraysucursal[$item->sucursalid]['costo']    += $item->costo;
            $arraysucursal[$item->sucursalid]['cantidad'] += $item->cantidad;

            foreach ($instancias as  $instancia) {
                $len = strlen($instancia->codalte);
                if(substr($item->codalte,0, $len) == $instancia->codalte){

                    if(!isset($arrayinstanci[$instancia->codinst]))             $arrayinstanci[$instancia->codinst] = [];

                    $arrayinstanci[$instancia->codinst]['descrip'] = $instancia->descrip;
                    $arrayinstanci[$instancia->codinst]['codalte'] = $instancia->codalte;

                    if(!isset($arrayinstanci[$instancia->codinst]['costo']   )) $arrayinstanci[$instancia->codinst]['costo']    = 0;
                    if(!isset($arrayinstanci[$instancia->codinst]['cantidad'])) $arrayinstanci[$instancia->codinst]['cantidad'] = 0;
                    $arrayinstanci[$instancia->codinst]['costo']    += $item->costo;
                    $arrayinstanci[$instancia->codinst]['cantidad'] += $item->cantidad;
                }
            }


        }

        return view('newexistenciasInstancias',
            compact( 'arraysucursal',
                'sucursales', 'fksucursal','comercialid', 'instancias', 'allsucursales', 'arrayinstanci', 'arrayproducto') );
    }

    public function newexistenciasphp(Request $request)
    {
        $arraysucursales = auth()->user()->getSucursalesIdsComercialActual();
        $arraysucursales = implode(",",$arraysucursales);

        $codinst    = $request->codinst;
        $fksucursal = $request->fksucursal;


        $comercial  = session('comercialid') ;
        if(!$comercial) {
            session(['comercialid' => 1]);
            $comercial = 1;
        }

        $instancias = Sainsta::with(['newproductosexistencias'])
            ->orderBy('descrip','asc')
            ->get();

        $insPadre = 0;

        $sucursales = Sasucursal::where("fk_comercial", $comercial)->whereRaw("id in ($arraysucursales)")->get();

        $instanciaselected = '';
        foreach ($instancias as $instancia){
            if($instancia->codinst == $codinst){
                $instanciaselected = $instancia;
                $insPadre = $instancia->insPadre;
                break;
            }
        }

        return view('newexistenciasInstanciasphp', compact('fksucursal', 'insPadre', 'codinst', 'sucursales', 'instancias', 'instanciaselected', 'comercial') )->render();
    }

    public function newviewprodinstsanciascodalte(Request $request)
    {
        $arraysucursales = auth()->user()->getSucursalesIdsComercialActual();
        $arraysucursales = implode(",",$arraysucursales);

        $comercial  = session('comercialid') ;
        if(!$comercial) {
            session(['comercialid' => 1]);
            $comercial = 1;
        }

        $codalte     = $request->codalte;
        $len         = strlen($codalte);

        $sqlcostoinv = "SELECT a.preciod, a.descrip, a.codprod, e.codubic, b.existen, e.descrip as deposito
								from saprod a , newsaexis b, sasucursal c, sainsta d, sadepo e
								where a.codprod    = b.codprod
                                and b.fk_sucursal  = c.id
								and b.codubic      = e.codubic
                                and c.fk_comercial = $comercial
								and a.comercial    = $comercial
								and d.comercial    = $comercial
								and e.comercial    = $comercial
								and c.id in ($arraysucursales)
								and d.codinst      = a.codinst
                                and left(d.codalte,$len) = '$codalte'
								and b.existen <> 0
                                order by a.descrip
								";

        $listado = DB::select($sqlcostoinv);

        $productos    = [];
        $deposito     = [];
        $existencias  = [];

        foreach($listado as $producto){

            if(!isset($productos[$producto->codprod]))
                $productos[$producto->codprod] = [];

            $productos[$producto->codprod]['descrip']    = $producto->descrip;
            $productos[$producto->codprod]['preciod']    = $producto->preciod;

            if(!isset($deposito[$producto->codubic]))
                $deposito[$producto->codubic] = $producto->deposito;

            if(!isset($existencias[$producto->codprod][$producto->codubic]))
                $existencias[$producto->codprod][$producto->codubic] = 0;

            $existencias[$producto->codprod][$producto->codubic] = $producto->existen;
        }

        return view('newproductosallinstsancias', compact('productos', 'deposito', 'existencias') )->render();


    }

    public function json()
    {
        $comercial  = session('comercialid') ;
        $all = Saprod::where('comercial',$comercial)->with(['instancia'])->orderBy('descrip','asc')->get();
        $aux = [];
        $productos = [];
        $noimage = URL::asset('build/images/noimagen.jpg');
        foreach ($all as $item){
            $aux = [
                "id"            => "$item->id",
                "price"         => "$item->costod3",
                "exdecimal"     => "$item->exdecimal",
                "image"         => (isset($item->productImg))? '': $noimage,
                "productTitle"  => "$item->descrip",
                "category"      => $item->instancia->descrip
            ];

            array_push($productos,$aux);
        }
        return response()->json($productos );
    }

    public function productossucursales(Request $request)
    {
        $comercialid  = session('comercialid') ;
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

        $listado = Saitemfac::whereRaw("TipoFac in ('A','B','Z','W')")
            ->selectRaw("fk_sucursal, coditem, SUM(Cantidad*Signo) as salidas")
            ->with(['sucursal','producto.instancia' => function($q) { $q->orderBy('codalte', 'asc'); }])
            ->where('esserv',0)
            ->whereHas('sucursal.comercial', function($q) use ($comercialid) {
                $q->where('fk_comercial',$comercialid);
            })
            ->whereBetween('FechaE', [$fec1.' 00:00:00.00', $fec2.' 23:58:22.00'])
            ->groupBy(['fk_sucursal','coditem'])->orderBy('fk_sucursal')->get();

        $sucursales = [];
        $cantidadprod = [];
        $itemventas = [];

        if(isset($listado))
            foreach($listado as $prodsuc){

                if(!isset($sucursales[$prodsuc->sucursal->id])){
                    $sucursales[$prodsuc->sucursal->id] = $prodsuc->sucursal->descrip;
                }

                if(!isset($cantidadprod[$prodsuc->coditem.$prodsuc->sucursal->id])){
                    $cantidadprod[$prodsuc->coditem.$prodsuc->sucursal->id]=0;
                }

                $cantidadprod[$prodsuc->coditem.$prodsuc->sucursal->id] += $prodsuc->salidas;
                $itemventas[$prodsuc->producto->instancia->descrip][$prodsuc->coditem]['descrip']   = $prodsuc->producto->descrip;
                $itemventas[$prodsuc->producto->instancia->descrip][$prodsuc->coditem]['exdecimal'] = $prodsuc->producto->exdecimal;

            }

        asort($sucursales);

        return view('productosSucursales', compact('fechasreport', 'sucursales',   'itemventas', 'cantidadprod'));
    }

    public function busquedaHomeProd(Request $request)
    {
        $busqueda = $request->busqueda;
        $busqueda = str_replace("\"", "", $busqueda);
        $busqueda = str_replace("'", "", $busqueda);
        $busqueda = str_replace("*", " ", $busqueda);
        $vector = explode(" ", $busqueda);

        if ($vector) {
            $numerito = 0;
            $cadena   = '';
            foreach ($vector as $value) {
                if ($numerito > 0) {
                    $cadena  .= ' AND ';
                }
                $cadena  .= "(codprod like '%$value%' or descrip like '%$value%' or refere like '%$value%' or marca like '%$value%' or descrip2 like '%$value%')";
                $numerito++;
            }
        }

        $comercial = session('comercialid');

        // Obtener los productos
        $productos = Saprod::where('comercial', $comercial)
            ->whereRaw($cadena)
            ->orderBy('updated_at', 'desc')
            ->limit(60)
            ->get();

        $sucursales = Sasucursal::where('fk_comercial', $comercial)
            ->orderBy('descrip')
            ->get();

        // Para cada producto, obtener las existencias por sucursal
        foreach ($productos as $producto) {
            if(!isset($producto->existencias_por_sucursal))
                $producto->existencias_por_sucursal = [];

            $existencias = NewSaexis::where('codprod', $producto->codprod)
                ->whereIn('fk_sucursal', $sucursales->pluck('id'))
                ->where('existen','<>',0)
                ->with('deposito')
                ->get();

            $producto->existencias_por_sucursal = $existencias;
        }


        return view('layouts.ajaxbusqueda', compact('productos', 'sucursales'))->render();
    }

    public function saprodsucursal(Request $request)
    {
        $sucursalid = str_replace("300", "", $request->sucursal);
        $productos = $request->productos;
        $productos = json_decode($productos);

        if (isset($productos))
            foreach ($productos as $producto){
                $aux = Saprodsucursal::where(['codprod' => $producto->codprod, 'fk_sucursal'=>$sucursalid])->first();
                if(!$aux){
                    $rel              = new Saprodsucursal();
                    $rel->codprod     = $producto->codprod;
                    $rel->fk_sucursal = $sucursalid;
                    $rel->save();
                }
            }

        return response()->json(['success'=>'success']);
    }

    public function list(Request $request)
    {
        $sucursalid = str_replace("300","",$request->sucursal);
        $sucursal   = Sasucursal::find($sucursalid);
        $comercial  = $sucursal->fk_comercial;

        $productos = Saprod::where('comercial',$comercial)
            ->whereRaw("codprod not in (select codprod from saprodsucursal where fk_sucursal=$sucursalid )")->limit(1000)->get();

        $servicios = Saserv::whereRaw("codserv not in (select codserv from saservsucursal where fk_sucursal=$sucursalid )")->get()->take(10);

        return response()->json(['success'=>'success', 'newproductos' => $productos, 'newservicios' => $servicios]);
    }

    public function create()
    {
        $instancias = Sainsta::selectRaw("concat( repeat('&nbsp;',((nivel-1)*4)), Descrip ) as label, descrip, id, nivel, codinst, insPadre ")
            ->with(['padre'])
            ->where('tipoins','0')
            ->orderBy('codalte','asc')->get();

        $last   = 0;
        $product = Saprod::orderBy('id','desc')->first();
        if(isset($product) and $product->codprod != '')
            $last = $product->codprod;

        return view('product-create', compact('instancias','last') );
    }

    public function checkcodprod($codprod)
    {
        $check   = 1;
        $comercial = session('comercialid') ;

        if($codprod != '')
            $product = Saprod::where(['codprod' => $codprod, 'comercial' => $comercial])->first();

        if(isset($product) and $product->codprod != '')
            $check = 0;

        return response()->json(['check' => $check ]);
    }

    public function store(Request $request)
    {
        $comerciales = Sacomercial::get();
        foreach ($comerciales as $comercial){

            $product = Saprod::where(['codprod' => substr($request->codprod,0,15), 'comercial' => $comercial->id])->first();

            if(isset($product) and isset($product->codprod) and $product->codprod != ''){

            }else{
                $codigo  = substr($request->codprod,0,15);
                $codigo  = trim($codigo);
                $codigo  = strtoupper($codigo);
                $codigo  = str_replace(" ",'',$codigo);
                $newprod = new Saprod();
                $newprod->fill($request->all());
                $newprod->codprod   = $codigo;
                $newprod->comercial = $comercial->id;
                $newprod->save();
            }
        }
        return redirect()->route('productos.index');
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
            if(Auth::user()  and auth()->user()->can('menu_productos_modificar_productos') ){
                $producto   = Saprod::find($id);

                $instancias = Sainsta::selectRaw("concat( repeat('&nbsp;',((nivel-1)*4)), Descrip ) as label, descrip, id, nivel, codinst ")
                    ->with(['padre'])
                    ->orderBy('codalte','asc')->get();

                return view('product-edit', compact('instancias','producto', 'id'));
            }else{
                return response()->redirectTo('index');
            }
    }

    public function update(Request $request, $id)
    {
        $producto = Saprod::find($id);

        if(isset($producto) and isset($producto->codprod) and $producto->codprod != ''){

            $codprod  = $producto->codprod;
            if($codprod!=''){
                $producto->fill($request->all());

                if(!$request->exdecimal)
                    $producto->exdecimal = 0;
                if(!$request->activo)
                    $producto->activo = 0;
                if(!$request->esexento)
                    $producto->esexento = 0;

                if($producto->comercial == 1)
                    $producto->esexento = 1;

                $producto->save();

                $otrosprod = Saprod::where('codprod',$codprod)->get();
                foreach ($otrosprod as $otro){
                    $otro->descrip  = $request->descrip;
                    $otro->descrip2 = $request->descrip2;
                    $otro->descrip3 = $request->descrip3;
                    $otro->descrip4 = $request->descrip4;
                    $otro->marca    = $request->marca;
                    $otro->codinst  = $request->codinst;
                    $otro->refere   = $request->refere;
                    $otro->save();
                }

                $prodsucursal = Saprodsucursal::with('producto')->where('codprod', $producto->codprod)->get();
                if($prodsucursal)
                    foreach ($prodsucursal as $item){
                        $item->delete();
                    }

                $comerciales = Sacomercial::get();

                foreach ($comerciales as $comercial){

                    $product = Saprod::where(['codprod' => $codprod, 'comercial' => $comercial->id])
                        ->first();

                    if(isset($product) and isset($product->codprod) and $product->codprod != ''){

                    }else{
                        $newprod = new Saprod();
                        $newprod->fill($request->all());
                        $newprod->codprod   = $codprod;
                        $newprod->preciod   = 0;
                        $newprod->costod    =  0;
                        if($comercial->id == 1){  $newprod->esexento = 1; }else{$newprod->esexento = 0;}
                        $newprod->costod2   =  0;
                        $newprod->costod3   =  0;
                        $newprod->comercial = $comercial->id;
                        $newprod->save();
                    }
                }
            }

        }

        return redirect()->route('productos.edit',$id);
    }

    public function destroy($id)
    {
        //
    }

    public function productosinstsancias(Request $request)
    {
        $codinst     = $request->codinst;

        $sqlcostoinv = "SELECT   a.preciod, a.descrip, a.codprod, e.codubic, b.existen, e.descrip as deposito
								from   saprod a , saexis b, sasucursal c, sainsta d, sadepo e
								where  a.codprod = b.codprod
                                and b.fk_sucursal = c.id
								and b.codubic = e.codubic
								and d.codinst = a.codinst
                                and a.codinst = $codinst
								and b.existen > 0
                        union

                        SELECT  a.preciod, a.descrip, a.codprod, e.codubic, b.existen, e.descrip as deposito
								from   saprod a , newsaexis b, sasucursal c, sainsta d, sadepo e
								where  a.codprod = b.codprod
                                and b.fk_sucursal = c.id
								and b.codubic = e.codubic
								and d.codinst = a.codinst
                                and a.codinst = $codinst
								and b.existen > 0


								";


        $listado = DB::select($sqlcostoinv);

        return response()->json(['success'=>'success', 'listado' => $listado,'sql'=>$sqlcostoinv]);

    }

    public function productosinstsanciasnew(Request $request)
    {
        $sucursalid = str_replace("300","",$request->sucursal);
        $sucursal   = Sasucursal::find($sucursalid);
        $comercial  = $sucursal->fk_comercial;

        $allsucursa = Sasucursal::where('fk_comercial',$sucursal->fk_comercial)->get();
        $auxsucu    = [];

        foreach ($allsucursa as $sucu){
            array_push( $auxsucu, $sucu->id);
        }

        $auxsucu = implode(',' , $auxsucu);


        $codinst     = $request->codinst;

        $sqlcostoinv = "SELECT a.marca, a.preciod, a.descrip, a.codprod, e.codubic, b.existen, e.descrip as deposito
								from   saprod a , newsaexis b, sasucursal c, sainsta d, sadepo e
								where  a.codprod = b.codprod
                                and b.fk_sucursal = c.id
								and b.codubic = e.codubic
								and a.comercial =$comercial
                                and b.fk_sucursal in ($auxsucu)
								and d.codinst = a.codinst
                                and a.codinst = $codinst
								and b.existen > 0
								";

        $listado = DB::select($sqlcostoinv);

        return response()->json(['success'=>'success', 'listado' => $listado, 'sql' => $sqlcostoinv]);

    }

    public function productosallinstsanciasnew(Request $request)
    {
        $codalte  = $request->codalte;
        $len = strlen($codalte);
        $sqlcostoinv = "SELECT a.marca, a.preciod, a.descrip, a.codprod, c.codubic, c.existen, f.descrip as deposito
                        from saprod a, sainsta d, newsaexis c, sasucursal e, sadepo f
                        where d.codinst = a.codinst and f.codubic = c.codubic and e.id = c.fk_sucursal
                        and c.codprod = a.codprod and c.existen >0 and left(d.codalte,$len) = '$codalte'
                        order by a.codprod;";

        $listado = DB::select($sqlcostoinv);

        return response()->json(['success'=>'success', 'listado' => $listado,  'sqlcostoinv' => $sqlcostoinv, 'codalte' => $codalte]);

    }

    public function newlistprodubic(Request $request)
    {
        $sucursalid = str_replace("300","",$request->sucursal);
        $sucursal   = Sasucursal::find($sucursalid);
        $codprod    = $request->codprod;

        $allsucursa = Sasucursal::where('fk_comercial',$sucursal->fk_comercial)->get();
        $auxsucu    = [];

        foreach ($allsucursa as $sucu){
            array_push( $auxsucu, $sucu->id);
        }

        $auxsucu = implode(',' , $auxsucu);

        $existencias = NewSaexis::whereRaw("fk_sucursal in ($auxsucu) and codprod='$codprod' and existen <> 0")->orderBy('codubic')->get();

        return response()->json(['success'=>'success', 'existencias' => $existencias]);
    }

    public function listprodubic(Request $request)
    {
        $sucursalid = str_replace("300","",$request->sucursal);
        $sucursal   = Sasucursal::find($sucursalid);
        $codprod    = $request->codprod;

        $allsucursa = Sasucursal::where('fk_comercial',$sucursal->fk_comercial)->get();
        $auxsucu    = [];

        foreach ($allsucursa as $sucu){
            array_push( $auxsucu, $sucu->id);
        }

        $auxsucu = implode(',' , $auxsucu);

        $existencias = NewSaexis::whereRaw("fk_sucursal in ($auxsucu) and codprod='$codprod' and existen <> 0")->orderBy('codubic')->get();

        return response()->json(['success'=>'success', 'existencias' => $existencias]);
    }
}
