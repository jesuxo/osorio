<?php

namespace App\Http\Controllers;

use App\Models\Cwbancos;
use App\Models\Cwcuentas;
use App\Models\Cwebancos;
use App\Models\Cwfinanciamiento;
use App\Models\Cwtransaccion;
use App\Models\Cwtransferencia;
use App\Models\Cwtrcuenta;
use App\Models\Letracambio;
use App\Models\Pagare;
use App\Models\Saacxc;
use App\Models\Saacxcw;
use App\Models\Saclie;
use App\Models\Sacliesucursal;
use App\Models\Sacomercial;
use App\Models\Safact;
use App\Models\Sapagcxc;
use App\Models\Saprod;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaclieController extends Controller
{
    public function letradelete(Request $request)
    {
        $letra = Letracambio::find($request->id);
        $letra->delete();

        $busqueda = $request->codclie;
        $tab = 'tab3';

        return redirect()->route('buscarclientes', [$busqueda, $tab]);
    }

    public function pagaredelete(Request $request)
    {
        $letra = Pagare::find($request->id);
        $letra->delete();

        $busqueda = $request->codclie;
        $tab = 'tab4';

        return redirect()->route('buscarclientes', [$busqueda, $tab]);
    }

    public function letraview(Request $request)
    {
        $letra = Letracambio::with(['cliente'])->find($request->id);
        return view('printLetra', compact('letra'));
    }

    public function pagareview(Request $request)
    {
        $pagare = Pagare::with(['cliente'])->find($request->id);
        return view('printPagare', compact('pagare'));
    }

    public function financiamientoview(Request $request)
    {
        $financiamiento = Cwfinanciamiento::with(['cliente'])->find($request->id);
        return view('printFinanciamiento', compact('financiamiento'));
    }

    public function reservaview(Request $request)
    {
        $financiamiento = Cwfinanciamiento::with(['cliente'])->find($request->id);
        return view('printReserva', compact('financiamiento'));
    }

    public function newletra(Request $request)
    {
        $letra = new Letracambio();
        $letra->fill($request->all());
        $letra->fecha      = Carbon::now();
        $letra->fk_usuario = auth()->id();
        $letra->save();

        $busqueda = $request->codclie;
        $tab = 'tab3';

        return redirect()->route('buscarclientes', [$busqueda, $tab]);
    }

    public function newpagare(Request $request)
    {
        $pagare = new Pagare();
        $pagare->fill($request->all());
        $pagare->fecha      = Carbon::now();
        $pagare->fk_usuario = auth()->id();
        $pagare->save();

        $busqueda = $request->codclie;
        $tab = 'tab4';

        return redirect()->route('buscarclientes', [$busqueda, $tab]);
    }

    public function buscarclienteajax(Request $request)
    {
        $cdcd          = (isset($request->cdcd)         )? $request->cdcd          : '';
        $fkbanco       = (isset($request->fkbanco)      )? $request->fkbanco       : '';
        $buscarcliente = (isset($request->buscarcliente))? $request->buscarcliente : '';

        $buscarcliente = str_replace("*", " ", $buscarcliente);
        $buscarcliente = str_replace("\"", "", $buscarcliente);
        $buscarcliente = str_replace("'", "",  $buscarcliente);

        $cadena   = '';
        $numerito = 0;
        $clientes = [];
        if($buscarcliente != '') {
            $vector = explode(" ", $buscarcliente);

            if ($vector) {
                foreach ($vector as $value) {
                    if ($numerito > 0) {
                        $cadena .= ' AND ';
                    }
                    $cadena .= "(codclie like '%$value%' or descrip like '%$value%' or id3 like '%$value%'  )";

                    $numerito++;
                }
            }
            if ($cadena) $cadena = " and ($cadena)";

            $clientes = Saclie::whereRaw("activo = 1 $cadena")->limit(50)->get();
        }
        return view('layouts.buscarclientes', compact('clientes', 'fkbanco', 'cdcd', 'buscarcliente'))->render();
    }

    public function updatecliente(Request $request)
    {
        $cliente = Saclie::where('codclie',$request->codclie)->first();
        $cliente->descrip    = (isset($request->descrip)  )? $request->descrip : '';
        $cliente->email      = (isset($request->email)    )? $request->email   : '';
        $cliente->id3        = (isset($request->id3)      )? $request->id3     : '';
        $cliente->clase      = (isset($request->clase)    )? $request->clase   : '';
        $cliente->telef      = (isset($request->telef)    )? $request->telef   : '';
        $cliente->represent  = (isset($request->represent))? $request->represent : '';
        $cliente->movil      = (isset($request->movil)    )? $request->movil   : '';
        $cliente->fax        = (isset($request->fax)      )? $request->fax     : '';
        $cliente->direc1     = (isset($request->direc1)   )? $request->direc1  : '';
        $cliente->direc2     = (isset($request->direc2)   )? $request->direc2  : '';
        $cliente->direc3     = (isset($request->direc3)   )? $request->direc3  : '';
        $cliente->DescripExt = (isset($request->descrip)  )? $request->descrip : '';
        $cliente->save();

        $busqueda = $request->codclie;

        $sacliesucursal = Sacliesucursal::where('codclie', $request->codclie)->get();
        if(isset($sacliesucursal) and count($sacliesucursal) > 0)
            foreach ($sacliesucursal as $item){
                $item->delete();
            }

        return redirect()->route('buscarclientes', [$busqueda]);
    }

    public function index(Request $request)
    {
        $datetime = Carbon::now()->isoFormat('DD-MM-YYYY');
        $tab      = (isset($request->tab)     )? $request->tab      : 'tab1';
        $busqueda = (isset($request->busqueda))? $request->busqueda : '';
        $codclie  = (isset($request->codclie) )? $request->codclie  : '';
        $clientes = [];
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
                    $cadena .= "(codclie like '%$value%' or descrip like '%$value%' or id3 like '%$value%')";
                    $numerito++;
                }
            }

            $clientes = Saclie::whereRaw($cadena)->orderBy('descrip', 'asc')->limit(60)->get();
        }
        if($codclie != '' and $busqueda == '') {
            $clientes = Saclie::where('codclie',$codclie)->get();
        }

        $cliente         = '' ;
        $listado         = [];
        $pagares         = [];
        $listadoc        = [];
        $letrasdecambio  = [];
        $financiamientos = [];

        if(isset($clientes) and count($clientes) == 1 and !$codclie) {
            $cliente = $clientes[0];
            $codclie = $clientes[0]->codclie;
        }

        if($codclie != ''){
            $cliente  = Saclie::where('codclie',$codclie)->first();

            if($tab == 'tab1'){
                $ventas = Safact::selectRaw("fk_sucursal as fk_sucu, nrounico,descrip,numerod, tipofac, codclie,
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
                             date_format(FechaT,'%d/%m/%Y') as fecha,

                             ((mtototal*Signo)/tasa_dolar) as mtototal,
                             (((contado+credito)*Signo)/tasa_dolar) as totalventa,
                             (((cancelaUSD)*Signo)) as cancelaUSD,
                             ((credito*Signo)/tasa_dolar) as credito,
                             ((contado*Signo)/tasa_dolar) as contado
                            ")
                    ->whereRaw(" TipoFac in('A','B','Z','W') and codclie ='$codclie'  ")
                    ->orderBy('FechaT','desc')
                    ->limit(200)
                    ->get();


                if(isset($ventas))
                    foreach ($ventas as $venta) {

                        if(!isset($listado[$venta->nrounico]['fk_sucu']))
                            $listado[$venta->nrounico]['fk_sucu'] ='';
                        $listado[$venta->nrounico]['fk_sucu'] = $venta->fk_sucu;

                        if(!isset($listado[$venta->nrounico]['codclie']))
                            $listado[$venta->nrounico]['codclie'] ='';
                        $listado[$venta->nrounico]['codclie'] = $venta->codclie;

                        if(!isset($listado[$venta->nrounico]['fecha']))
                            $listado[$venta->nrounico]['fecha'] ='';
                        $listado[$venta->nrounico]['fecha'] = $venta->fecha;

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

            }

            if($tab == 'tab2'){

                $cobranzas = Saacxcw::selectRaw(" codusua, dolar_tranf as transf, dolares, codclie,
                 (cancele - (dolares*tasadolar)) as cancele, document, nrounico, euros,cancelausd, monto,
                 (cancelt - (dolar_tranf*tasadolar)) as cancelt,  tasadolar, pesos, peso_tranf, tasapeso,
                 date_format(FechaT, '%d/%m/%Y') as fecha, codvend,  numerod, tipocxc, montodolares, fk_sucursal")
                    ->with([ 'cliente', 'sucursalcli'])
                    ->whereRaw("codclie = '$codclie' and montodolares >0")
                    ->orderBy('fechat','asc')
                    ->get();


                if(isset($cobranzas))
                    foreach ($cobranzas as $cobranza) {

                        if(!isset($listadoc[$cobranza->nrounico]['tipocxc']))
                            $listadoc[$cobranza->nrounico]['tipocxc'] ='';
                        $listadoc[$cobranza->nrounico]['tipocxc'] =$cobranza->tipocxc;

                        if(!isset($listadoc[$cobranza->nrounico]['document']))
                            $listadoc[$cobranza->nrounico]['document'] ='';
                        $listadoc[$cobranza->nrounico]['document'] = $cobranza->document;

                        if(!isset($listadoc[$cobranza->nrounico]['fk_sucu']))
                            $listadoc[$cobranza->nrounico]['fk_sucu'] ='';
                        $listadoc[$cobranza->nrounico]['fk_sucu'] = $cobranza->fk_sucursal;

                        if(!isset($listadoc[$cobranza->nrounico]['sucu']))
                            $listadoc[$cobranza->nrounico]['sucu'] ='';
                        $listadoc[$cobranza->nrounico]['sucu'] = $cobranza->sucursal->descrip;

                        if(!isset($listadoc[$cobranza->nrounico]['codclie']))
                            $listadoc[$cobranza->nrounico]['codclie'] ='';
                        $listadoc[$cobranza->nrounico]['codclie'] = $cobranza->codclie;

                        if(!isset($listadoc[$cobranza->nrounico]['numerod']))
                            $listadoc[$cobranza->nrounico]['numerod'] ='';
                        $listadoc[$cobranza->nrounico]['numerod'] = $cobranza->numerod;

                        if(!isset($listadoc[$cobranza->nrounico]['fecha']))
                            $listadoc[$cobranza->nrounico]['fecha'] =0;
                        $listadoc[$cobranza->nrounico]['fecha']   = $cobranza->fecha;

                        if(!isset($listadoc[$cobranza->nrounico]['cancelausd']))
                            $listadoc[$cobranza->nrounico]['cancelausd'] =0;
                        $listadoc[$cobranza->nrounico]['cancelausd'] = $cobranza->cancelausd;

                        if(!isset($listadoc[$cobranza->nrounico]['tasadolar']))
                            $listadoc[$cobranza->nrounico]['tasadolar'] =0;
                        $listadoc[$cobranza->nrounico]['tasadolar'] = $cobranza->tasadolar;

                        if(!isset($listadoc[$cobranza->nrounico]['monto']))
                            $listadoc[$cobranza->nrounico]['monto'] =0;
                        $listadoc[$cobranza->nrounico]['monto'] = $cobranza->monto;

                        if(!isset($listadoc[$cobranza->nrounico]['montodolares']))
                            $listadoc[$cobranza->nrounico]['montodolares'] =0;
                        $listadoc[$cobranza->nrounico]['montodolares'] = $cobranza->montodolares;

                    }


            }

            if($tab == 'tab3'){
                $letrasdecambio = Letracambio::where('codclie',$codclie)->get();

            }

            if($tab == 'tab4'){
                $pagares = Pagare::where('codclie',$codclie)->get();
            }

            if($tab == 'tab5'){
                $financiamientos = Cwfinanciamiento::selectRaw("id, DATE_FORMAT(fecha,'%d/%m/%Y') as fecha, codclie, costobien, valorfinancia, inicialfinancia, porcinicial, saldofinancia,
        costofinancia, porccostofi, cantcuotas, cuota, codclieavalista")
                    ->whereRaw("codclie = '$codclie'")
                    ->with(["avalista"])
                    ->get();
            }

        }

        return view('clientes', compact('financiamientos', 'letrasdecambio','pagares', 'codclie', 'tab', 'busqueda', 'datetime', 'clientes', 'cliente', 'listado', 'listadoc'));
    }

    public function json()
    {
        $user     = User::where('id',auth()->user()->id)->with(['sucursales.sucursal.sacliesucursales.cliente'])->first();
        $clientes = $sucursales = $aux = $all = [];
        foreach ($user->sucursales as $rel){
            array_push($sucursales, $rel->sucursal);
            if(isset($rel->sucursal->sacliesucursales)){
                foreach ($rel->sucursal->sacliesucursales as $relcliente){
                    array_push($clientes,$relcliente->cliente);
                }
            }
        }

        foreach ($clientes as $item){
            list($fecha,$hora) = explode(" ",$item->created_at);
            list($y,$m,$d) = explode("-",$fecha);
            $aux = [
                "id"        => "$item->id",
                "codclie"   => "$item->codclie",
                "descrip"   => "$item->descrip",
                "telef"     => "$item->telef"." "."$item->movil",
                "date"      => "$y-$m-$d",
                "datelabel" => "$d/$m/$y"
            ];

            array_push($all, $aux);
        }
        return response()->json($all);

    }

    public function sacliesucursal(Request $request)
    {
        $sucursalid = str_replace("300", "", $request->sucursal);
        $clientes = $request->clientes;
        $clientes = json_decode($clientes);

        if (isset($clientes))
            foreach ($clientes as $cliente){
                $aux = Sacliesucursal::where(['codclie' => $cliente->codclie, 'fk_sucursal'=>$sucursalid])->first();
                if(!$aux){
                    $rel              = new Sacliesucursal();
                    $rel->codclie     = $cliente->codclie;
                    $rel->fk_sucursal = $sucursalid;
                    $rel->save();
                }
            }

        return response()->json(['success'=>'success']);
    }

    public function itemCxc(Request $request)
    {
        $nrounico = $request->nrounico;
        $vectorcxc = session('vectorcxc');
        $vectorcxc[$nrounico] = 1;
        session(['vectorcxc' => $vectorcxc]);

        return response()->json(['success'=>'success']);
    }
    public function list(Request $request)
    {
        $sucursalid = str_replace("300","",$request->sucursal);
        $clientes   = $request->clientes;
        $clientes   = json_decode($clientes);

        if(isset($clientes))
            foreach ($clientes as $cliente){
                $aux = Saclie::where(['codclie' => $cliente->codclie])->first();
                if(!$aux){
                    $new = new Saclie();
                    $new->id3           = (isset($cliente->id3))       ?$cliente->id3        : '';
                    $new->fax           = (isset($cliente->fax))       ?$cliente->fax        : '';
                    $new->clase         = (isset($cliente->clase))     ?$cliente->clase      : '';
                    $new->telef         = (isset($cliente->telef))     ?$cliente->telef      : '';
                    $new->movil         = (isset($cliente->movil))     ?$cliente->movil      : '';
                    $new->email         = (isset($cliente->email))     ?$cliente->email      : '';
                    $new->direc1        = (isset($cliente->direc1))    ?$cliente->direc1     : '';
                    $new->direc2        = (isset($cliente->direc2))    ?$cliente->direc2     : '';
                    $new->direc3        = (isset($cliente->direc3))    ?$cliente->direc3     : '';
                    $new->activo        = (isset($cliente->activo))   ?$cliente->activo     : 0;
                    $new->codclie       = $cliente->codclie;
                    $new->tipocli       = (isset($cliente->tipocli))   ?$cliente->tipocli    : 0;
                    $new->TipoID3       = (isset($cliente->TipoID3))   ?$cliente->TipoID3    : 0;
                    $new->descrip       = (isset($cliente->descrip))   ?$cliente->descrip    : '';
                    $new->represent     = (isset($cliente->represent)) ?$cliente->represent  : '';
                    $new->escredito     = (isset($cliente->escredito)) ?$cliente->escredito  : 0;
                    $new->DescripExt    = (isset($cliente->DescripExt))?$cliente->DescripExt : '';
                    $new->LimiteCred    = (isset($cliente->LimiteCred))?$cliente->LimiteCred : 0;
                    $new->Observaciones = (isset($cliente->Observaciones)) ?$cliente->Observaciones     : '';

                    $new->save();

                    $rel              = new Sacliesucursal();
                    $rel->codclie     = $cliente->codclie;
                    $rel->fk_sucursal = $sucursalid;
                    $rel->save();
                }else{
                    $aux = Sacliesucursal::where(['codclie' => $cliente->codclie, 'fk_sucursal'=>$sucursalid])->first();
                    if(!$aux){
                        $rel              = new Sacliesucursal();
                        $rel->codclie     = $cliente->codclie;
                        $rel->fk_sucursal = $sucursalid;
                        $rel->save();
                    }
                }
            }

        $clientes = Saclie::whereRaw("codclie not in (select codclie from sacliesucursal where fk_sucursal=$sucursalid )")->limit('30')->get();

        $transferencias = Cwtransferencia::where(["fksucursal" => $sucursalid, "status"=>1, "tipo"=>"venta", "descargada" => 0])->limit('30')->get();

        $prodfalt = Saprod::whereRaw("codprod not in (select codprod from saprodsucursal where fk_sucursal=$sucursalid )")->limit('30')->get();

        $prodflag = 0;
        if(isset($prodfalt) and count($prodfalt)>0){
            $prodflag = 1;
        }

        return response()->json(['success'=>'success', 'newclientes' => $clientes, 'prodflag' => $prodflag, 'newtransfer' => $transferencias]);
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $newCliente = new Saclie();
        $newCliente->fill($request->all());
        $newCliente->DescripExt = $request->descrip;
        $newCliente->save();

        $busqueda = $request->codclie;

        return redirect()->route('buscarclientes', [$busqueda]);
    }

    public function verInstPago(Request $request){
        $bs        = (isset($request->bs)       )? $request->bs        : 0;
        $anticipos = (isset($request->anticipos))? $request->anticipos : 0;
        $ultdol    = (isset($request->ultdol)   )? $request->ultdol    : 0;
        $anticipocli = (isset($request->anticipocli))? $request->anticipocli : 0;

        $pesos   = (isset($request->pesos)   )? $request->pesos   : 0;
        $dolares =  (isset($request->dolares))? $request->dolares : 0;
        $saldotodafac  = (isset($request->saldotodafac))? $request->saldotodafac : 0;

        return view('partials.verInstPago',compact('pesos','dolares','saldotodafac','anticipocli','ultdol','anticipos','bs'))->render();

    }

    public function financiamientos(Request $request)
    {
        $comercial  = session('comercialid') ;
        if(!$comercial) {
            session(['comercialid' => 1]);
            $comercial = 1;
        }


        $objcomercial     = Sacomercial::find($comercial);
        $tasabs           = (isset($objcomercial->tasabs)   and $objcomercial->tasabs   > 0) ? $objcomercial->tasabs   : 0;
        $pesoxdolarsuc    = (isset($objcomercial->tasapeso) and $objcomercial->tasapeso > 0) ? $objcomercial->tasapeso : 0;
        $fk_cuentacxc     = (isset($objcomercial->fk_cuentacxc) and $objcomercial->fk_cuentacxc > 0) ? $objcomercial->fk_cuentacxc : 0;
        $prx_recibo       = (isset($objcomercial->prx_recibo)   and $objcomercial->prx_recibo   > 0) ? $objcomercial->prx_recibo : 0;
        $bank        = [];

        $procesar    = (isset( $request->procesar)   )? $request->procesar    : 0;
        $cancele     = (isset( $request->cancele)    )? $request->cancele     : 0;
        $cancelt     = (isset( $request->cancelt)    )? $request->cancelt     : 0;
        $dolares     = (isset( $request->dolares)    )? $request->dolares     : 0;
        $pesoxdolar  = (isset( $request->pesoxdolar) and $request->pesoxdolar >0 )? $request->pesoxdolar  : $pesoxdolarsuc;
        $pesos       = (isset( $request->pesos)      )? $request->pesos       : 0;
        $dolar_tranf = (isset( $request->dolar_tranf))? $request->dolar_tranf : 0;
        $peso_tranf  = (isset( $request->peso_tranf) )? $request->peso_tranf  : 0;
        $notas1      = (isset( $request->notas1)     )? $request->notas1      : '';
        $notas2      = (isset( $request->notas2)     )? $request->notas2      : '';
        $observacion = (isset( $request->observacion))? $request->observacion : '';
        $tab         = (isset( $request->tab)        )? $request->tab         : 'tab9';
        $fechabanco  = (isset( $request->fechabanco) )? $request->fechabanco  : Carbon::now()->format('Y-m-d');
        $fk_banco    = (isset( $request->fk_banco)   )? $request->fk_banco    : 0;
        $codclie     = (isset( $request->codclie)    )? $request->codclie     : '';
        $numerod     = (isset( $request->numerod)    )? $request->numerod     : '';
        $fechahoy    = (isset( $request->fechahoy)   )? $request->fechahoy    : Carbon::now()->format('Y-m-d');
        $tasaabono   = (isset( $request->tasaabono)  )? $request->tasaabono   : $tasabs;
        $ttvienedato = (isset( $request->ttvienedato))? $request->ttvienedato : 0;

        $transftar   = (isset( $request->transftar)  )? $request->transftar   : '';
        $buscarcli   = (isset( $request->buscarcli)  )? $request->buscarcli   : '';

        $desctar     = (isset( $request->desctar)    )? $request->desctar     : '';
        $desctardol  = (isset( $request->desctardol) )? $request->desctardol  : '';
        $desctarpes  = (isset( $request->desctarpes) )? $request->desctarpes  : '';

        $codtar      = (isset( $request->codtar)     )? $request->codtar      : [];
        $codtardol   = (isset( $request->codtardol)  )? $request->codtardol   : [];
        $codtarpes   = (isset( $request->codtarpes)  )? $request->codtarpes   : [];

        $montotar    = (isset( $request->montotar)   )? $request->montotar    : [];
        $montotardol = (isset( $request->montotardol))? $request->montotardol : [];
        $montotarpes = (isset( $request->montotarpes))? $request->montotarpes : [];

        $nrounicocxc = (isset( $request->nrounicocxc))? $request->nrounicocxc : [];
        $tipocxccxc  = (isset( $request->tipocxccxc) )? $request->tipocxccxc  : [];
        $numerodcxc  = (isset( $request->numerodcxc) )? $request->numerodcxc  : [];
        $tasafaccxc  = (isset( $request->tasafaccxc) )? $request->tasafaccxc  : [];

        $procesarbanco = 1; //(isset( $request->procesarbanco))? $request->procesarbanco : 0;
        $totalcancela  = (isset( $request->totalcancela) )? $request->totalcancela  : 0;
        $cancela       = (isset( $request->cancela)      )? $request->cancela       : 0;

        if($cancele > 0 or  $dolares >0 or $pesos >0   )
            $procesarbanco = 1;

        if(isset($nrounicocxc) and count($nrounicocxc) > 0 and $ttvienedato == 0){
            foreach ($nrounicocxc as $cxc) {
                $ttvienedato += $cxc;
            }
        }


        $vector = explode(" ", $buscarcli);
        $cadena = ''; $numerito = 0;
        if ($vector) {
            foreach ($vector as $value) {
                if ($numerito > 0) {
                    $cadena .= ' AND ';
                }
                $cadena .= "(saclie.codclie like '%$value%' or saclie.descrip like '%$value%' or saclie.id3 like '%$value%'  )";

                $numerito++;
            }
        }
        if ($cadena) $cadena = "   ($cadena)";

        $datanumerod = '';

        if($numerod !='')
            $datanumerod = " and numerod = '$numerod' ";

        $cliente = Saclie::select('descrip')->where('codclie',$codclie)->first();

        $deudascli = [];
        $financiamientos = Cwfinanciamiento::selectRaw("id, DATE_FORMAT(Fecha,'%d/%m/%Y') as fecha, codclie, costobien, valorfinancia, inicialfinancia, porcinicial, saldofinancia,
        costofinancia, porccostofi, cantcuotas, cuota")
            ->whereRaw("id in (select fkfinanciamiento from saacxc where saldo>0 group by fkfinanciamiento)")
            ->with('cliente');

        if($cadena != '')
            $financiamientos = $financiamientos->whereHas('cliente', function ($query) use ($cadena) {
                                    return $query->WhereRaw("  $cadena " );
                                });

        $financiamientos = $financiamientos->get();

        if($codclie != ''){
            $deudascli = Saacxc::selectRaw("id, DATE_FORMAT(FechaV,'%d/%m/%Y') as fechav, tipocxc,
                    numerod, DATE_FORMAT(FechaT,'%d/%m/%Y') as fecha, codclie,
                    Document, montodolares, (montodolares- (saldo/tasadolar)) as abonado, tasadolar,
                     fkfinanciamiento ")
                ->whereRaw("  montodolares >0 and saldo > 0 and codclie ='$codclie'  $datanumerod")
                ->with('cliente')
                ->get();

            $sql  = "  SELECT a.id, a.descrip, a.fk_cuenta, a.sbs, a.sdolares, a.seuros, a.spesos
                       FROM  cwbancos a,   cwcuentas b
                       WHERE a.activo   = 1 and a.web = 1
                       and a.fk_cuenta  = b.id
                       order by b.numpadre, b.id
				 ";

            $bank = DB::select($sql);


        }else{
            $vector = [];
            session(['vectorcxc' => $vector]);
            session(['isntbs'    => $vector]);
        }

        if($procesar == 1){
                $tabonobs  = 0;

                if(!$totalcancela)$totalcancela = 0;
                if(!$cancela)     $cancela      = 0;
                if(!$cancele)     $cancele      = 0;
                if(!$cancelt)     $cancelt      = 0;
                if(!$pesos)       $pesos        = 0;
                if(!$dolares)     $dolares      = 0;
                if(!$dolar_tranf) $dolar_tranf  = 0;
                if(!$peso_tranf)  $peso_tranf   = 0;

                $canceleaux = $cancele;
                $canceltaux = $cancelt;

                    $monto = number_format(($dolares +  ($pesos / $pesoxdolar)
                                    + $dolar_tranf + ($peso_tranf/ $pesoxdolar)+  ($cancele/$tasaabono)
                                + ($cancelt/$tasaabono) + $cancela),2,'.','');

                $cancela = $cancela * $tasaabono;

                if(isset($nrounicocxc)){
                    foreach($nrounicocxc as $indexcxc => $amount){
                        $tabonobs += $amount * $tasafaccxc[$indexcxc];
                    }
                }

                $cancelepago = $canceltpago = $nddifbs = 0;

                if($cancele > 0 or $cancelt > 0){
                    $bstasa_abono = ($cancele) + ($cancelt);
                    $diftasa_abon = $bstasa_abono - $tabonobs;
                    if($diftasa_abon > 0.02){
                        $nddifbs = 1;
                        $montond = $diftasa_abon / $tasaabono;
                        $cancelepago = $diftasa_abon * ($cancele/$bstasa_abono);
                        $canceltpago = $diftasa_abon * ($cancelt/$bstasa_abono);
                    }

                }

                $sqlcheck = "select id from saacxc
                             where date_format(fechat,'%Y-%m-%y') = '$fechabanco'
                             and montodolares = $monto
                             and tipocxc      = 33
                             and esunpago     = 1
                             and codclie      = '$codclie' ";

                $resquery = DB::select($sqlcheck);

                if(isset($resquery) and count($resquery) == 0){

                    $descripcxc    = '';
                    if($fk_cuentacxc){
                        $sqlcheck = "SELECT b.descrip
								     FROM   cwcuentas b
									 WHERE  b.id  = $fk_cuentacxc";

                        $resquery = DB::select($sqlcheck);

                        $descripcxc  = $resquery[0]->descrip;
                    }

                    $codestacion = 'web';
                    $monto   = number_format($monto,2,'.','');
                    $sqldeb  = '';

                    $sqlcheck = "select lpad('$prx_recibo',8,'0') as cadena ";
                    $resquery = DB::select($sqlcheck);
                    $numprx   = $resquery[0]->cadena;

                    $usernamelogin = "OSORIO";
                    $abonobs       = $tabonobs;
                    $tasadolar     = $tasaabono;

                    $montodolares  = $monto ; //- $montond;
                    $tasacalculos  = $tabonobs / $montodolares;
                    $cancele      += $tasacalculos  * ($dolares   + ($pesos / $pesoxdolar)) - $cancelepago ;
                    $cancelt      += ($tasacalculos * $dolar_tranf) + (($peso_tranf/$pesoxdolar) * $tasacalculos) - $canceltpago;
                    $codvend       = '';
                    $texento       = $abonobs;
                    $base          = 0;
                    $iva           = 0;
                    if(!isset($cancele))$cancele  =0;
                    if(!isset($cancelt))$cancelt  =0;
                    $objcomercial->prx_recibo = $objcomercial->prx_recibo +1;
                    $objcomercial->save();

                    if( $fk_banco > 0 and (($dolares + $dolar_tranf) > 0 or ($canceleaux + $canceltaux) > 0 or ($pesos + $peso_tranf) > 0)){

                        $pesosaux      = $pesos  +$peso_tranf;
                        $bankdolar     = $dolares + $dolar_tranf;
                        $bankbs        = $canceleaux + $canceltaux;
                        $bankbsdolar   = $bankbs / $tasaabono;


                        $banckpesdolar = ($pesos / $pesoxdolar) + ($peso_tranf / $pesoxdolar);

                        list($y,$m,$d)=explode("-",$fechabanco);

                        $fechasys = "$y-$m-$d";
                        $periodo  = "$y$m";

                        $sqlcheck = "
                                      SELECT a.id, a.descrip, a.fk_cuenta, a.sbs, a.sdolares, a.seuros, a.spesos
                                      FROM cwbancos a
                                      INNER JOIN cwcuentas b ON a.fk_cuenta = b.id
                                      WHERE a.activo = 1
                                      AND a.id = $fk_banco
                                 ";

                        $bank        = DB::select($sqlcheck);
                        $fk_cuenta   = $bank[0]->fk_cuenta;
                        $descripbank = $bank[0]->descrip;

                        list($yn,$mn,$dn) = explode("-", date('Y-m-d',strtotime("$y-$m-01 +1 month")));

                        $selectp = "SELECT id
                                    FROM cwebancos
                                    WHERE fk_banco = $fk_banco and periodo='$yn$mn'";

                        $ebank   = DB::select($selectp);

                        if(!isset($ebank[0])){
                            $newebank = new Cwebancos();
                            $newebank->periodo  = $yn.$mn;
                            $newebank->fk_banco = $fk_banco;
                            $newebank->save();
                            $idebank = $newebank->id;
                        }else{
                            $idebank = $ebank[0]->id;
                        }

                        $newtrans              = new Cwtransaccion();
                        $newtrans->codbene     = $codclie;
                        $newtrans->descripbene = $cliente->descrip;
                        $newtrans->numero      = $numprx;
                        $newtrans->fk_banco    = $fk_banco;
                        $newtrans->monto       = $montodolares;
                        $newtrans->monto_bs    = $bankbs;
                        $newtrans->monto_dolar = $bankdolar;
                        $newtrans->monto_peso  = $pesosaux;
                        $newtrans->fecha       = $fechabanco;
                        $newtrans->cdcd        = 2;
                        $newtrans->periodo     = $periodo;
                        $newtrans->tipobene    = 0;
                        $newtrans->fbs         = $bankbsdolar;
                        $newtrans->fpesos      = $banckpesdolar;
                        $newtrans->descripcion = $observacion;
                        $newtrans->notas1      = $notas1;
                        $newtrans->notas2      = $notas2;
                        $newtrans->save();

                        $fk_transaccion = $newtrans->id;

                        $newcwcuenta = new Cwtrcuenta();
                        $newcwcuenta->descrip   = $descripbank ;
                        $newcwcuenta->monto     = $montodolares ;
                        $newcwcuenta->fk_cuenta = $fk_cuenta ;
                        $newcwcuenta->signo     = 0 ;
                        $newcwcuenta->periodo   = $periodo ;
                        $newcwcuenta->fk_transaccion   = $fk_transaccion ;

                        $newcwcuenta->save();

                        $newcwcuenta = new Cwtrcuenta();
                        $newcwcuenta->descrip   = $descripcxc ;
                        $newcwcuenta->monto     = $montodolares ;
                        $newcwcuenta->fk_cuenta = $fk_cuentacxc ;
                        $newcwcuenta->signo     = 1 ;
                        $newcwcuenta->periodo   = $periodo ;
                        $newcwcuenta->fk_transaccion   = $fk_transaccion ;

                        $newcwcuenta->save();

                        $cwebancos = Cwebancos::where('id',$idebank)->first();
                        $cwebancos->saldo_bs      = $cwebancos->saldo_bs      + $bankbs;
                        $cwebancos->saldo_dolares = $cwebancos->saldo_dolares + $bankdolar;
                        $cwebancos->saldo_pesos   = $cwebancos->saldo_pesos   + $pesosaux;
                        $cwebancos->save();

                        $cwbancos = Cwbancos::where('id',$fk_banco)->first();
                        $cwbancos->sbs      = $cwbancos->sbs      + $bankbs;
                        $cwbancos->sdolares = $cwbancos->sdolares + $bankdolar;
                        $cwbancos->spesos   = $cwbancos->spesos   + $pesosaux;
                        $cwbancos->save();


                    }

                    $saacxc = new Saacxc();
                    $saacxc->CodClie  = $codclie;
                    $saacxc->NroUnico = 0;
                    $saacxc->FechaE   = $fechahoy;
                    $saacxc->FechaV   = $fechahoy;
                    $saacxc->FechaT   = $fechahoy;
                    $saacxc->CodEsta  = 'web';
                    $saacxc->CodUsua  = 'web';
                    $saacxc->CodOper  = '9999';
                    $saacxc->CodVend  = '01';
                    $saacxc->NumeroD  = $prx_recibo;
                    $saacxc->NumeroN  = '';
                    $saacxc->TipoCxc  = 43;
                    $saacxc->Document = $observacion;
                    $saacxc->Notas1   = $notas1;
                    $saacxc->Notas2   = $notas2;
                    $saacxc->Notas3   = '';
                    $saacxc->Monto    = $tabonobs;
                    $saacxc->MontoNeto= $tabonobs;
                    $saacxc->MtoTax   = 0;
                    $saacxc->Saldo    = 0;
                    $saacxc->SaldoOrg = 0;
                    $saacxc->BaseImpo = 0;
                    $saacxc->TExento  = $tabonobs;
                    $saacxc->CancelA  = 0;
                    $saacxc->CancelE  = $cancele;
                    $saacxc->CancelT  = $cancelt;
                    $saacxc->CancelC  = 0;
                    $saacxc->EsUnPago = 1;
                    $saacxc->dolares  = $dolares;
                    $saacxc->pesos    = $pesos;
                    $saacxc->dolar_tranf  = $dolar_tranf;
                    $saacxc->peso_tranf   = $peso_tranf;
                    $saacxc->montodolares = $montodolares;
                    $saacxc->tasadolar= $tasadolar;
                    $saacxc->xdev     = 0;
                    $saacxc->tasapeso = $pesoxdolar;
                    $saacxc->fk_transaccion = 1;
                    $saacxc->tasaeuro = 1;
                    $saacxc->cancelaUSD = 0;
                    $saacxc->save();

                    $NroUnicoDOC = $saacxc->id;

                    if(isset($montotar)){
                        $datosArray = [];
                        foreach($montotar as $index => $amount){
                            if($amount >0){
                                $CodPago = $codtar[$index];
                                $Descrip = $desctar[$index];

                                $arrayaux = ['fk_sucursal' => 0, 'dolares' => 0, 'pesos' => 0,
                                    'NroPpal' => $NroUnicoDOC, 'CodPago' => $CodPago ,
                                    'Descrip' => $Descrip, 'Monto' => $amount, 'codclie' => $codclie];

                                array_push($datosArray, $arrayaux);

                            }
                        }
                        if(isset($datosArray) and isset($datosArray[0]))
                            DB::table('saipacxc')->insertOrIgnore($datosArray);
                    }

                    $cancela = 0;
                    if(isset($montotardol)){
                        $datosArray = [];
                        foreach($montotardol as $index => $amount){
                            if($amount >0){
                                $CodPago = $codtardol[$index];
                                $Descrip = $desctardol[$index];

                                $arrayaux = ['fk_sucursal' => 0, 'dolares' => $amount, 'pesos' => 0,
                                    'NroPpal' => $NroUnicoDOC, 'CodPago' => $CodPago ,
                                    'Descrip' => $Descrip, 'Monto' => 0, 'codclie' => $codclie];

                                array_push($datosArray, $arrayaux);

                            }
                        }
                        if(isset($datosArray) and isset($datosArray[0]))
                            DB::table('saipacxc')->insertOrIgnore($datosArray);
                    }

                    if(isset($montotarpes)){
                        $datosArray = [];
                        foreach($montotarpes as $index => $amount){
                            if($amount >0){
                                $CodPago = $codtarpes[$index];
                                $Descrip = $desctarpes[$index];

                                $arrayaux = ['fk_sucursal' => 0, 'dolares' => 0, 'pesos' => $amount,
                                    'NroPpal' => $NroUnicoDOC, 'CodPago' => $CodPago ,
                                    'Descrip' => $Descrip, 'Monto' => 0, 'codclie' => $codclie];

                                array_push($datosArray, $arrayaux);

                            }
                        }
                        if(isset($datosArray) and isset($datosArray[0]))
                            DB::table('saipacxc')->insertOrIgnore($datosArray);
                    }

         if(isset($nrounicocxc)){
             foreach($nrounicocxc as $indexcxc => $amount){

                 $NroUnico    = $indexcxc;
                 if(isset($numerodcxc[$indexcxc])  and $amount > 0){

                     $numerodabono = $numerodcxc[$indexcxc];
                     $tipocxcabono = $tipocxccxc[$indexcxc];
                     $abonobsitem  = $amount * $tasafaccxc[$indexcxc];
                     $descripabono = "ABONO A DOC NRO. $numerodabono";

                     $saldocxc = Saacxc::where('id',$NroUnico)->first();
                     $restasal = $saldocxc->Saldo - $abonobsitem;
                     $saldocxc->saldo = $restasal;
                     $saldocxc->save();

                     $saacxc->NumeroN = $saldocxc->NumeroD;
                     $saacxc->save();

                     $sapagcxc = new Sapagcxc();
                     $sapagcxc->fk_sucursal = 0;
                     $sapagcxc->codclie     = $codclie;
                     $sapagcxc->NroPpal     = $NroUnicoDOC;
                     $sapagcxc->NroUnico    = 0;
                     $sapagcxc->NumeroD     = $numerodabono;
                     $sapagcxc->Descrip     = $descripabono;
                     $sapagcxc->Monto       = $abonobsitem;
                     $sapagcxc->FechaE      = $fechahoy;
                     $sapagcxc->TipoCxc     = $tipocxcabono;
                     $sapagcxc->montodolar  = $amount;
                     $sapagcxc->save();



                 }
             }
         }

         return response()->redirectTo('financiamientos');


/*
                                        if(isset($montosanticipo) ){
                                            foreach($montosanticipo as $ind => $amount){
                                                if($amount > 0){
                                                    $saldoant = $tasaant[$ind] * $amount;


                                                    if(!$side){
                                                        $sqlcxcms .= "
                                                        UPDATE tablesql WITH (ROWLOCK) SET
                                                               [Saldo]=[Saldo]+-$saldoant, [cancelaUSD]=[cancelaUSD]-$amount
                                                            WHERE (NroUnico=$ind) and codclie = '$codclie';

                                                      ";
                                                    }else{
                                                        $sqlcxcms .= "
                                                            UPDATE tablesql WITH (ROWLOCK) SET
                                                                    [cancelaUSD]=[cancelaUSD] - $amount
                                                                WHERE (NroUnico=$ind) and codclie = '$codclie';

                                                         ";

                                                    }
                                                }

                                            }
                                        }
*/


                }else{

                    dd('Posible Duplicacion de informacion');

                }

        }

        return view('financiamientos', compact('cancele', 'cancelt', 'dolares', 'pesos', 'bank', 'fk_banco',
            'fechabanco', 'tab', 'peso_tranf', 'dolar_tranf', 'observacion', 'notas1', 'notas2', 'tasabs', 'pesoxdolar',
            'montotardol', 'buscarcli', 'codtardol', 'montotar', 'codtar', 'desctardol', 'desctar', 'codtarpes', 'montotarpes', 'desctarpes',
            'financiamientos', 'procesarbanco',  'tasaabono', 'fechahoy', 'ttvienedato', 'nrounicocxc', 'codclie', 'deudascli'));
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
         //
    }

    public function destroy($id)
    {
        //
    }
}
