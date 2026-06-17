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
use App\Models\Saclie;
use App\Models\Sacomercial;
use App\Models\Saprov;
use App\Models\Sasucursal;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CwbancosController extends Controller
{
    public function index(Request $request)
    {
        $padrebancos = (isset($request->padrebancos))? $request->padrebancos : '';

        session(['cwTran' => []]);

        $sql = "   SELECT descrip,  nivel, detalle, numero, numpadre, id
                   FROM cwcuentas a
				   where nivel=5
				   and left(numpadre,1) = '1'
				   and numero in (
							SELECT  numpadre
						   FROM  cwcuentas b
						   where  left(numpadre,1) = '1'
						   and (banco=1 )
				   )
				   and (detalle=0 )
                   ORDER BY a.numero
				 ";

        $bancos= DB::select($sql);

        return view('bancos',compact('bancos', 'padrebancos'));
    }

    public function imprimirReciboBanco(Request $request)
    {
        $id = ( isset($request->id) )?$request->id : '';
        dd($id);
    }

    public function eliminarTr(Request $request)
    {

        $trdeleteid = $request->id;
        $fkbanco    = $request->fkbanco;

        if($trdeleteid > 0 ){

            $transaccions = Cwtransaccion::where('fk_transaccion',$trdeleteid)->get();
            if(isset($transaccions) and count($transaccions) >0 ){
                foreach ($transaccions as $transaccion) {
                    $fkbadupda   = $transaccion->fk_banco;
                    $monto_dolar = $transaccion->monto_dolar;
                    $monto_bs    = $transaccion->monto_bs;
                    $monto_peso  = $transaccion->monto_peso;

                    if($fkbadupda > 0) {
                        $banco = Cwbancos::find($fkbadupda);
                        $banco->sbs      = $banco->sbs      - $monto_bs;
                        $banco->spesos   = $banco->spesos   - $monto_peso;
                        $banco->sdolares = $banco->sdolares - $monto_dolar;
                        $banco->save();
                    }
                    $transaccion->delete();
                }
            }

            $transaccions = Cwtransaccion::where('id', $trdeleteid)->get();
            if(isset($transaccions) and count($transaccions) >0 ){
                foreach ($transaccions as $transaccion) {
                    $fkbadupda   = $transaccion->fk_banco;
                    $monto_dolar = $transaccion->monto_dolar;
                    $monto_bs    = $transaccion->monto_bs;
                    $monto_peso  = $transaccion->monto_peso;

                    if($fkbadupda > 0) {
                        $banco = Cwbancos::find($fkbadupda);
                        $banco->sbs      = $banco->sbs      + $monto_bs;
                        $banco->spesos   = $banco->spesos   + $monto_peso;
                        $banco->sdolares = $banco->sdolares + $monto_dolar;
                        $banco->save();
                    }
                    $transaccion->delete();
                }
            }

            $trcuentas = Cwtrcuenta::where('fk_transaccion', $trdeleteid)->get();
            if(isset($trcuentas))
                foreach ($trcuentas as $trcuenta) {
                    $trcuenta->delete();
                }

        }

        return redirect()->route('verbanco',['fkbanco' => $fkbanco, 'cdcd' => 0]);

    }

    public function simulacionFinanciamiento(Request $request)
    {
        $costofinancia    = (isset($request->costofinancia)   )?$request->costofinancia   : 0;
        $porccostofi      = (isset($request->porccostofi)     )?$request->porccostofi     : 0;
        $valorfinancia    = (isset($request->valorfinancia)   )?$request->valorfinancia   : 0;
        $costobien        = (isset($request->costobien)       )?$request->costobien       : 0;
        $saldofinancia    = (isset($request->saldofinancia)   )?$request->saldofinancia   : 0;
        $inicialfinancia  = (isset($request->inicialfinancia) )?$request->inicialfinancia : 0;
        $porcinicialant   = (isset($request->porcinicialant)  )?$request->porcinicialant  : 0;
        $porcinicial      = (isset($request->porcinicial)     )?$request->porcinicial     : 0;
        $cantcuotas       = (isset($request->cantcuotas)      )?$request->cantcuotas      : 0;
        $mtocuota         = (isset($request->mtocuota)        )?$request->mtocuota        : 0;

        $porcinicialold     = (isset($request->porcinicialold)    )?$request->porcinicialold     : 0;
        $costofinanciaodl   = (isset($request->costofinanciaodl)  )?$request->costofinanciaodl   : 0;
        $inicialfinanciaold = (isset($request->inicialfinanciaold))?$request->inicialfinanciaold : 0;

        if($porcinicialold   != $porcinicial ) {
            $inicialfinancia = 0;
            $saldofinancia   = 0;
        }

        if($costofinanciaodl   != $costofinancia )
            $porccostofi = 0;

        if($inicialfinanciaold != $inicialfinancia ) {
            $porcinicialant = 0;
            $saldofinancia  = 0;
        }

        return view('financiamientoCalculo', compact('porccostofi',
            'costofinancia', 'mtocuota', 'saldofinancia', 'inicialfinancia', 'porcinicial', 'valorfinancia',
            'porcinicialant', 'costobien', 'cantcuotas'));
    }

    public function verbanco( Request $request)
    {
        $comercial  = session('comercialid') ;
        if(!$comercial) {
            session(['comercialid' => 1]);
            $comercial = 1;
        }

        $fechasistema  = session('fechasistema') ;
        if(!$fechasistema) {
            $fechasistema = Carbon::now()->format('Y-m-d');
            session(['fechasistema' => $fechasistema]);
        }

        $descripbene = '';

        list($year, $month, $day)    = explode('-', $fechasistema);
        list($yearn, $monthn, $dayn) = explode('-', date('Y-m-d',strtotime("$year-$month-01 +1 month")));
        $periodo  = "$year$month";
        $periodon = "$yearn$monthn";

        $objcomercial     = Sacomercial::find($comercial);
        $sucursales       = Sasucursal::where('fk_comercial',1)->orderBy('descrip','asc')->get();

        $fbs              = (isset($request->fbs)             )?$request->fbs             : 0;
        $bstr             = (isset($request->bstr)            )?$request->bstr            : 0;
        $iii              = (isset($request->iii)             )?$request->iii             : '';
        $numerotr         = (isset($request->numerotr)        )?$request->numerotr        : '';
        $notas1           = (isset($request->notas1)          )?$request->notas1          : '';
        $notas2           = (isset($request->notas2)          )?$request->notas2          : '';
        $notas3           = (isset($request->notas3)          )?$request->notas3          : '';
        $cdcd             = (isset($request->cdcd)            )?$request->cdcd            : '';
        $Debe             = (isset($request->Debe)            )?$request->Debe            : '';
        $Haber            = (isset($request->Haber)           )?$request->Haber           : '';
        $imonto           = (isset($request->imonto)          )?$request->imonto          : '';
        $fecha1tr         = (isset($request->fecha1tr)        )?$request->fecha1tr        : '';
        $fecha2tr         = (isset($request->fecha2tr)        )?$request->fecha2tr        : '';
        $clearVect        = (isset($request->clearVect)       )?$request->clearVect       : '';
        $bstrentra        = (isset($request->bstrentra)       )?$request->bstrentra       : 0;
        $pesostrentra     = (isset($request->pesostrentra)    )?$request->pesostrentra    : 0;
        $dolarestrentra   = (isset($request->dolarestrentra)  )?$request->dolarestrentra  : 0;
        $pesostr          = (isset($request->pesostr)         )?$request->pesostr         : 0;
        $codbene          = (isset($request->codbene)         )?$request->codbene         : '';
        $fkbanco          = (isset($request->fkbanco)         )?$request->fkbanco         : 0;
        $fksucu           = (isset($request->fksucu)          )?$request->fksucu          : '';
        $fechatr          = (isset($request->fechatr)         )?$request->fechatr         : $fechasistema;
        $tipobene         = (isset($request->tipobene)        )?$request->tipobene        : '';
        $cambiarclie      = (isset($request->cambiarclie)     )?$request->cambiarclie     : '';
        $cambiarprov      = (isset($request->cambiarprov)     )?$request->cambiarprov     : '';
        $newdebehaber     = (isset($request->newdebehaber)    )?$request->newdebehaber    : '';
        $fkcuentacambiar  = (isset($request->fkcuentacambiar) )?$request->fkcuentacambiar : 0;
        $montotrupdate    = (isset($request->montotrupdate)   )?$request->montotrupdate   : 0;
        $cambiarmontotr   = (isset($request->cambiarmontotr)  )?$request->cambiarmontotr  : 0;
        $dolarestr        = (isset($request->dolarestr)       )?$request->dolarestr       : 0;
        $fpesos           = (isset($request->fpesos)          )?$request->fpesos          : 0;
        $checkentramoneda = (isset($request->checkentramoneda))?$request->checkentramoneda: 0;
        $procesarbanco    = (isset($request->procesarbanco)   )?$request->procesarbanco   : 0;
        $descripciontr    = (isset($request->descripciontr)   )?$request->descripciontr   : '';
        $realizarletra    = (isset($request->realizarletra)   )?$request->realizarletra   : 0;
        $porccostofi      = (isset($request->porccostofi)     )?$request->porccostofi     : 0;
        $costofinancia    = (isset($request->costofinancia)   )?$request->costofinancia   : 0;
        $costobien        = (isset($request->costobien)       )?$request->costobien       : 0;
        $valorfinancia    = (isset($request->valorfinancia)   )?$request->valorfinancia   : 0;
        $realizarpagare   = (isset($request->realizarpagare)  )?$request->realizarpagare  : 0;
        $realizarfinancia = (isset($request->realizarfinancia))?$request->realizarfinancia: 0;
        $fechainiciofinan = (isset($request->fechainiciofinan))?$request->fechainiciofinan: 0;
        $cantcuotas       = (isset($request->cantcuotas)      )?$request->cantcuotas      : 0;
        $mtocuota         = (isset($request->mtocuota)        )?$request->mtocuota        : 0;
        $financiar        = (isset($request->financiar)       )?$request->financiar       : 0;
        $generarcuotas    = 0;
        $sacardelalista   = (isset($request->sacardelalista)  )?$request->sacardelalista  : 0;
        $ciudadpago       = (isset($request->ciudadpago)      )?$request->ciudadpago      : '';
        $cedulaavalista   = (isset($request->cedulaavalista)  )?$request->cedulaavalista  : '';
        $nombreavalista   = (isset($request->nombreavalista)  )?$request->nombreavalista  : '';
        $domicilioavalista= (isset($request->domicilioavalista))?$request->domicilioavalista: '';
        $telef1avalista   = (isset($request->telef1avalista)  )?$request->telef1avalista  : '';
        $telef2avalista   = (isset($request->telef2avalista)  )?$request->telef2avalista  : '';
        $emailavalista    = (isset($request->emailavalista)   )?$request->emailavalista   : '';
        $montoproceso     = (isset($request->montoproceso)    )?$request->montoproceso    : 0;
        $porcinicial      = (isset($request->porcinicial)     )?$request->porcinicial     : 0;
        $porcinicialant   = (isset($request->porcinicialant)  )?$request->porcinicialant  : 0;
        $saldofinancia    = (isset($request->saldofinancia)   )?$request->saldofinancia   : 0;
        $inicialfinancia  = (isset($request->inicialfinancia) )?$request->inicialfinancia : 0;
        $fechapagar       = (isset($request->fechapagar)      )?$request->fechapagar      : '';

        ///------------------------------------------------------------------------------------
        $placa            = (isset($request->placa)           )?$request->placa           : '';
        $sc               = (isset($request->sc)              )?$request->sc              : '';
        $sm               = (isset($request->sm)              )?$request->sm              : '';
        $color            = (isset($request->color)           )?$request->color           : '';
        $year             = (isset($request->year)            )?$request->year            : '';
        $modelo           = (isset($request->modelo)          )?$request->modelo          : '';
        $marca            = (isset($request->marca)           )?$request->marca           : '';
        $vehiculomoto     = (isset($request->vehiculomoto)    )?$request->vehiculomoto    : 0;

        ///------------------------------------------------------------------------------------
        $casaterreno      = (isset($request->casaterreno)     )?$request->casaterreno     : 0;
        $direccion        = (isset($request->direccion)       )?$request->direccion       : '';
        $caracteristicas  = (isset($request->caracteristicas) )?$request->caracteristicas : '';
        $superficie       = (isset($request->superficie)      )?$request->superficie      : '';

        $codclieavalista  = preg_replace('/[^0-9]/', '', $cedulaavalista);

        if($vehiculomoto == 1 and $casaterreno == 1){
            $casaterreno  = 0;
            $vehiculomoto = 0;
        }

        if($codclieavalista != ''){
            $avalista = Saclie::whereRaw("codclie like '%$codclieavalista%'")->first();
            if(isset($avalista) and isset($avalista->descrip)){

                if(isset($nombreavalista) and $nombreavalista !='')
                    $avalista->descrip = $nombreavalista;
                if(isset($telef1avalista) and $telef1avalista !='')
                    $avalista->telef   = $telef1avalista;
                if(isset($telef2avalista) and $telef2avalista !='')
                    $avalista->movil   = $telef2avalista;
                if(isset($emailavalista) and $emailavalista !='')
                    $avalista->email   = $emailavalista;

                $avalista->save();
            }else{
                if($codclieavalista != '' and $cedulaavalista != '' and $nombreavalista){
                    $avalista = new Saclie();
                    $avalista->codclie = $codclieavalista;
                    $avalista->id3     = $codclieavalista;
                    $avalista->descrip = $nombreavalista;
                    $avalista->telef   = $telef1avalista;
                    $avalista->movil   = $telef2avalista;
                    $avalista->email   = $emailavalista;
                    $avalista->save();
                }
            }
        }

        if($fkbanco == 0)
            return response()->redirectTo('bancos');

        if($cantcuotas and $mtocuota){
            $generarcuotas = 1;
        }

        if($sacardelalista){
            $auxtrc = session('cwTran');
            $auxtrc[$sacardelalista] = [];
            session(['cwTran' => $auxtrc]);
        }

        if($imonto > 0 and $cambiarmontotr > 0 and $montotrupdate > 0){
            $auxtrc = session('cwTran');
            $auxtrc[$imonto]['monto'] = $montotrupdate;
            session(['cwTran' => $auxtrc]);
        }

        $auxtrc = session('cwTran');
        if(isset($auxtrc[1]['ppal'])){
            $cwtr   = session('cwtr');
            $montoppal = 0;
            for($ii=1; $ii < $cwtr; $ii++){
                if( isset($auxtrc[$ii]['signo']) and $auxtrc[$ii]['ppal'] != 1){

                    if($cdcd == 1){
                        if($auxtrc[$ii]['signo'] == 1)
                            $montoppal -= $auxtrc[$ii]['monto'];
                        else
                            $montoppal += $auxtrc[$ii]['monto'];
                    }else{
                        if($auxtrc[$ii]['signo'] == 1)
                            $montoppal += $auxtrc[$ii]['monto'];
                        else
                            $montoppal -= $auxtrc[$ii]['monto'];
                    }
                }
            }

            $auxtrc[1]['monto'] = $montoppal;
            session(['cwTran' => $auxtrc]);
        }

        if($iii > 0 and $fkcuentacambiar > 0){

            $cuenta = Cwcuentas::selectRaw('descrip as cuenta, numero')->find($fkcuentacambiar);
            $auxtrc = session('cwTran');
            $auxtrc[$iii]['fk_cuenta']     = $fkcuentacambiar;
            $auxtrc[$iii]['numerocta']     = $cuenta->numero;
            $auxtrc[$iii]['descripcuenta'] = $cuenta->cuenta;
            session(['cwTran' => $auxtrc]);

        }

        list($y,$m,$d) = explode("-", $fechatr);

        if(preg_match("/\d{2}\/\d{2}\/\d{4}/",$fechatr) and checkdate($m,$d,$y)){
            if($periodon != $y.$m)
                $fechatr = $fechasistema;
        }else{
            $fechatr = $fechasistema;
        }

        if($tipobene == 1) $cambiarclie = $codbene;
        if($tipobene == 2) $cambiarprov = $codbene;

        if($cambiarprov != ''){
            $codbene     = $cambiarprov;
            $proveedor   = Saprov::selectRaw('descrip')->where('codprov', $cambiarprov)->first();
            $descripbene = $proveedor->descrip;
            $tipobene    = 2;
        }

        if($cambiarclie != ''){
            $codbene  = $cambiarclie;
            $cliente  = Saclie::selectRaw('descrip')->where('codclie', $cambiarclie)->first();
            $descripbene = $cliente->descrip;
            $tipobene    = 1;
        }

        $tasabscambiar    = (isset($request->tasabscambiar)  )?$request->tasabscambiar   : 0;
        $tasapesocambiar  = (isset($request->tasapesocambiar))?$request->tasapesocambiar : 0;
        $Beneficiariobusqueda  = (isset($request->Beneficiariobusqueda))? $request->Beneficiariobusqueda : '';
        $tasabs           = (isset($objcomercial->tasabs)   and $objcomercial->tasabs   > 0) ? $objcomercial->tasabs   : 0;
        $tasapeso         = (isset($objcomercial->tasapeso) and $objcomercial->tasapeso > 0) ? $objcomercial->tasapeso : 0;

        if(isset($tasabscambiar) and  $tasabscambiar > 0 and $tasabscambiar != $tasabs){
            $objcomercial->tasabs = $tasabscambiar;
            $objcomercial->save();
        }

        if(isset($tasapesocambiar) and  $tasapesocambiar >0 and $tasapesocambiar != $tasapeso){
            $objcomercial->tasapeso = $tasapesocambiar;
            $objcomercial->save();
        }

        $tasabs   = (isset($objcomercial->tasabs)   and $objcomercial->tasabs   > 0) ? $objcomercial->tasabs   : 0;
        $tasapeso = (isset($objcomercial->tasapeso) and $objcomercial->tasapeso > 0) ? $objcomercial->tasapeso : 0;

        $sql  = "  SELECT   a.id,   a.fk_cuenta,  a.prxegreso,  a.descrip,  a.sbs, a.sdolares, a.spesos, a.seuros, b.descrip AS cuenta, b.numero, a.cierrecaja, b.numpadre, b.descrip AS descripcuenta
                   FROM cwbancos a
                   INNER JOIN cwcuentas b ON a.fk_cuenta = b.id
                   WHERE a.id = $fkbanco
				 ";

        $bank = DB::select($sql);
        $bank = $bank[0];
        $fk_c = $bank->fk_cuenta;
        $nume = $bank->numero;
        $desc = $bank->descripcuenta;

        $ebank  = Cwebancos::where(['fk_banco'=>$fkbanco, 'periodo'=>$periodo])->first();

        if(!$ebank){
            $ebank = new Cwebancos();
            $ebank->periodo       = "$periodo";
            $ebank->fk_banco      = $fkbanco;
            $ebank->saldo_bs      = 0;
            $ebank->saldo_dolares = 0;
            $ebank->saldo_euros   = 0;
            $ebank->saldo_pesos   = 0;
            $ebank->save();
        }

        if($clearVect >0 and $cdcd > 0){
            session(['cwtr'   => 1]);
            session(['cwTran' => []]);

            $cwTran[session('cwtr')]['ppal']  = 1;
            $cwTran[session('cwtr')]['monto'] = 0;
            $cwTran[session('cwtr')]['fk_cuenta']     = $fk_c;
            $cwTran[session('cwtr')]['numerocta']     = $nume;
            $cwTran[session('cwtr')]['descripcuenta'] = $desc;

            if($clearVect and ($cdcd == 2 or $cdcd == 3))
                $cwTran[session('cwtr')]['signo'] = 0;

            if($clearVect and ($cdcd == 1 or $cdcd == 4))
                $cwTran[session('cwtr')]['signo'] = 1;

            session(['cwtr'   => 2]);

            $cwTran[session('cwtr')]['ppal']  = 0;
            $cwTran[session('cwtr')]['monto'] = 0;
            $cwTran[session('cwtr')]['fk_cuenta']     = '';
            $cwTran[session('cwtr')]['numerocta']     = '';
            $cwTran[session('cwtr')]['descripcuenta'] = '';

            if($clearVect and ($cdcd == 2 or $cdcd == 3))
                $cwTran[session('cwtr')]['signo'] = 1;

            if($clearVect and ($cdcd == 1 or $cdcd == 4))
                $cwTran[session('cwtr')]['signo'] = 0;

            session(['cwTran' => $cwTran]);

            session(['cwtr'   => 3]);
        }

        if($newdebehaber != ''){
            $auxtrc = session('cwTran');
            $auxtrc[session('cwtr')]['ppal']  = 0;
            $auxtrc[session('cwtr')]['monto'] = 0;
            $auxtrc[session('cwtr')]['fk_cuenta']     = '';
            $auxtrc[session('cwtr')]['numerocta']     = '';
            $auxtrc[session('cwtr')]['descripcuenta'] = '';

            if($newdebehaber == 'debe')
                $auxtrc[session('cwtr')]['signo'] = 0;

            if($newdebehaber == 'haber')
                $auxtrc[session('cwtr')]['signo'] = 1;

            session(['cwTran' => $auxtrc]);

            session(['cwtr'=> session('cwtr') +1]);

        }

        $ctasblocked    = 0;
        $nopuedeingreso = 0;

        if($cdcd == 2){

            $cwtr   = session('cwtr');
            $auxtrc = session('cwTran');
            if($cdcd > 0 and isset($cwtr) and $cwtr > 0  and isset($auxtrc) and count($auxtrc) > 1)
                for($ii=1; $ii < $cwtr; $ii++){
                    if(isset($auxtrc[$ii]['signo']) and $ii > 1 and $auxtrc[$ii]['signo'] == 1){
                        $fkcuentacheck = $auxtrc[$ii]['fk_cuenta'];
                        if($fkcuentacheck > 0){
                            $sqlinsert = Cwcuentas::whereRaw("id = $fkcuentacheck and banco = 1")->first();
                            if(isset($sqlinsert) and $sqlinsert->banco){
                                $ctasblocked = 1;
                                $nopuedeingreso = 1;
                            }
                        }
                    }
                }
        }

        if($procesarbanco and $cdcd){

            if(!$pesostrentra)  $pesostrentra   = 0;
            if(!$bstrentra)     $bstrentra      = 0;
            if(!$dolarestrentra)$dolarestrentra = 0;

            $numero = "";
            if($numerotr){
                if($cdcd==1) $numero = "E";
                if($cdcd==2) $numero = "I";

                $numero  .="-$numerotr";
            }

            $arraycwTran = session('cwTran');
            $monto       = $arraycwTran[1]['monto'];

            $fbs         = ($fbs       !=0)? $fbs       : 0;
            $fpesos      = ($fpesos    !=0)? $fpesos    : 0;
            $monto_bs    = ($bstr      !=0)? $bstr      : 0;
            $monto_dolar = ($dolarestr !=0)? $dolarestr : 0;
            $monto_peso  = ($pesostr   !=0)? $pesostr   : 0;

            $tipobene    = ($tipobene)? $tipobene : 0;

            $cwtransaccion = new Cwtransaccion();
            $cwtransaccion->fill($request->all());
            $cwtransaccion->numero      = $numero;
            $cwtransaccion->fk_banco    = $fkbanco;
            $cwtransaccion->fecha       = $fechatr;
            $cwtransaccion->periodo     = $periodo;
            $cwtransaccion->monto_bs    = $bstr;
            $cwtransaccion->monto_dolar = $dolarestr;
            $cwtransaccion->monto_peso  = $pesostr;
            $cwtransaccion->monto       = $monto;
            $cwtransaccion->descripbene = $descripbene;
            $cwtransaccion->descripcion = $descripciontr;
            $cwtransaccion->save();

            if(isset($realizarletra) and $realizarletra==1) {
                $letra = new Letracambio();
                $letra->codclie        = $codbene;
                $letra->fechapagar     = $fechapagar;
                $letra->nombreavalista = $nombreavalista;
                $letra->cedulaavalista = $cedulaavalista;
                $letra->ciudadpago     = $ciudadpago;
                $letra->domicilioavalista = $domicilioavalista;
                $letra->monto          = $montoproceso;
                $letra->fk_usuario     = auth()->id();
                $letra->fecha          = Carbon::now();
                $letra->save();
            }

            if($realizarfinancia and $generarcuotas){

                $Cwfina                  = new Cwfinanciamiento();
                $Cwfina->costobien       = $costobien;
                $Cwfina->valorfinancia   = $valorfinancia;
                $Cwfina->inicialfinancia = $inicialfinancia;
                $Cwfina->porcinicial     = $porcinicial;
                $Cwfina->saldofinancia   = $saldofinancia;
                $Cwfina->costofinancia   = $costofinancia;
                $Cwfina->porccostofi     = $porccostofi;
                $Cwfina->codclie         = $codbene;
                $Cwfina->fecha           = Carbon::now();
                $Cwfina->cantcuotas      = $cantcuotas;
                $Cwfina->cuota           = $mtocuota;

                ///--------------------------------------
                $Cwfina->placa           = $placa;
                $Cwfina->sc              = $sc;
                $Cwfina->sm              = $sm;
                $Cwfina->color           = $color;
                $Cwfina->year            = $year;
                $Cwfina->modelo          = $modelo;

                ///--------------------------------------
                $Cwfina->direccion       = $direccion;
                $Cwfina->caracteristicas = $caracteristicas;
                $Cwfina->superficie      = $superficie;


                $Cwfina->marca           = $marca;

                if((isset($codclieavalista) and $codclieavalista !=''))
                    $Cwfina->codclieavalista = $codclieavalista;

                $Cwfina->save();

                $fkfinanciamiento = $Cwfina->id;

                $prxsaacxc = $objcomercial->prxsaacxc;
                for($i=0 ; $i < $cantcuotas; $i++){
                    $ncuota = $i +1;
                    list($yc, $mc, $dc)=explode('-', $fechainiciofinan);

                    $saacxc = new Saacxc();
                    $saacxc->TipoCxc   = 33;
                    $saacxc->CodClie   = "$codbene";
                    $saacxc->NumeroD   = "$prxsaacxc";
                    $saacxc->FechaT    = Carbon::now();
                    $saacxc->FechaE    = "$yc-$mc-$dc";
                    $saacxc->FechaV    = "$yc-$mc-$dc";
                    $saacxc->CodUsua   = "osorio";
                    $saacxc->codvend   = "01";
                    $saacxc->CodEsta   = "osorioweb";
                    $saacxc->codoper   = "9999";
                    $saacxc->Document  = "Cuota #$ncuota - $notas1";
                    $saacxc->Monto     = $mtocuota * $tasabs;
                    $saacxc->MontoNeto = $mtocuota * $tasabs;
                    $saacxc->TExento   = $mtocuota * $tasabs;
                    $saacxc->Saldo     = $mtocuota * $tasabs;
                    $saacxc->SaldoOrg  = $mtocuota * $tasabs;
                    $saacxc->tasadolar = $tasabs;

                    $saacxc->montodolares    = $mtocuota;
                    $saacxc->fkfinanciamiento= $fkfinanciamiento;
                    $saacxc->save();

                    $objcomercial = Sacomercial::find($comercial);
                    $objcomercial->prxsaacxc = $prxsaacxc + 1;
                    $objcomercial->save();

                    $fechainiciofinan = date("Y-m-d",strtotime("$fechainiciofinan +7 days"));

                }

                $objsucu = Sasucursal::select('fk_financiadora')->where('id', $fksucu)->first();

                $newt = new Cwtransferencia();

                $newt->fecha       = Carbon::now();
                $newt->numero      = "";
                $newt->observacion = $notas1;
                $newt->titular     = "FINANCIADORA";
                $newt->monto       = $costobien;
                $newt->fkbanco     = $objsucu->fk_financiadora;
                $newt->fksucursal  = $fksucu;
                $newt->dolares     = 1;
                $newt->status      = 1;

                $newt->save();

            }

            if(isset($realizarpagare) and $realizarpagare==1) {
                $pagare = new Pagare();
                $pagare->codclie        = $codbene;
                $pagare->fechapagar     = $fechapagar;
                $pagare->nombreavalista = $nombreavalista;
                $pagare->cedulaavalista = $cedulaavalista;
                $pagare->domicilioavalista = $domicilioavalista;
                $pagare->monto          = $montoproceso;
                $pagare->fk_usuario     = auth()->id();
                $pagare->fecha          = Carbon::now();
                $pagare->save();
            }

            $ndmonto  = $monto;
            $fk_transaccion = $cwtransaccion->id;


            for($ii=1; $ii < session('cwtr'); $ii++){
                if( isset($arraycwTran[$ii]['signo']) and isset( $arraycwTran[$ii]['descripcuenta'])){

                    $montocwtr   = $arraycwTran[$ii]['monto'];
                    $fk_cuentatr = $arraycwTran[$ii]['fk_cuenta'];
                    $signo       = $arraycwTran[$ii]['signo'];
                    $descrip     = $arraycwTran[$ii]['descripcuenta'];

                    $newcwtrcuenta = new Cwtrcuenta();
                    $newcwtrcuenta->descrip        = $descrip;
                    $newcwtrcuenta->monto          = $montocwtr;
                    $newcwtrcuenta->signo          = $signo;
                    $newcwtrcuenta->fk_cuenta      = $fk_cuentatr;
                    $newcwtrcuenta->periodo        = $periodo;
                    $newcwtrcuenta->fk_transaccion = $fk_transaccion;
                    $newcwtrcuenta->save();

                    if($fk_cuentatr > 0 and $ii > 1){

                        $cuentacheck  = Cwcuentas::whereRaw("id = $fk_cuentatr and banco = 1")->first();

                        if(isset($cuentacheck) and $cuentacheck->banco == 1){
                            $cdcdtr = 0;
                            if($cdcd == 1) $cdcdtr = 2;
                            if($cdcd == 2) $cdcdtr = 1;

                            $fkcuentabanco = $cuentacheck->id;
                            $bancorel = Cwbancos::whereRaw("fk_cuenta = $fkcuentabanco")->first();


                            $cwtransaccion2 = new Cwtransaccion();
                            $cwtransaccion2->fill($request->all());
                            $cwtransaccion2->fecha       = $fechatr;
                            $cwtransaccion2->fk_transaccion = $fk_transaccion;
                            $cwtransaccion2->descripbene = $descripbene;
                            $cwtransaccion2->descripcion = $descripciontr;
                            $cwtransaccion2->fk_banco    = $bancorel->id;
                            $cwtransaccion2->cdcd        = $cdcdtr;
                            $cwtransaccion2->monto_bs    = $monto_bs;
                            $cwtransaccion2->monto       = $monto;
                            $cwtransaccion2->monto_dolar = $monto_dolar;
                            $cwtransaccion2->monto_peso  = $monto_peso;
                            $cwtransaccion2->periodo     = $periodo;
                            $cwtransaccion2->numero      = $numero;
                            $cwtransaccion2->save();

                            if($checkentramoneda){

                                $cwtransaccion3 = new Cwtransaccion();
                                $cwtransaccion3->fill($request->all());
                                $cwtransaccion3->fecha       = $fechatr;
                                $cwtransaccion3->fk_transaccion = $fk_transaccion;
                                $cwtransaccion3->descripbene = $descripbene;
                                $cwtransaccion3->descripcion = $descripciontr;
                                $cwtransaccion3->fk_banco    = $fkcuentabanco;
                                $cwtransaccion3->cdcd        = $cdcdtr;
                                $cwtransaccion3->periodo     = $periodo;
                                $cwtransaccion3->monto_bs    = $bstrentra;
                                $cwtransaccion3->monto       = $monto;
                                $cwtransaccion3->monto_dolar = $dolarestrentra;
                                $cwtransaccion3->monto_peso  = $pesostrentra;
                                $cwtransaccion3->numero      = $numero;
                                $cwtransaccion3->save();
                            }

                            if($cdcdtr == 2){
                                $subbanco = Cwbancos::whereRaw("id = ".$bancorel->id)->first();
                                $subbanco->sbs      = $subbanco->sbs      + $monto_bs;
                                $subbanco->sdolares = $subbanco->sdolares + $monto_dolar;
                                $subbanco->spesos   = $subbanco->spesos   + $monto_peso;
                                $subbanco->save();
                            }

                            if($cdcdtr == 1){

                                $subbanco = Cwbancos::whereRaw("id = ".$bancorel->id)->first();
                                $subbanco->sbs      = $subbanco->sbs      - $monto_bs;
                                $subbanco->sdolares = $subbanco->sdolares - $monto_dolar;
                                $subbanco->spesos   = $subbanco->spesos   - $monto_peso;
                                $subbanco->save();

                            }

                        }
                    }
                }
            }

            $cdcd = 0;

        }

        return view('verbanco',
            compact('realizarletra', 'realizarfinancia', 'porccostofi', 'costofinancia', 'mtocuota',
                'realizarpagare', 'saldofinancia', 'inicialfinancia', 'porcinicial','costobien', 'valorfinancia', 'financiar', 'porcinicialant',
                'Haber', 'fpesos', 'dolarestr', 'fechatr', 'codbene', 'numerotr', 'descripciontr', 'cantcuotas','fechainiciofinan',
                'fechapagar', 'domicilioavalista', 'montoproceso', 'cedulaavalista', 'ciudadpago', 'nombreavalista',
                'tipobene', 'descripbene', 'Debe', 'Beneficiariobusqueda', 'cdcd', 'periodo', 'ctasblocked',
                'notas1', 'notas2', 'notas3', 'fbs', 'bstr', 'bstrentra', 'pesostr', 'nopuedeingreso', 'fksucu', 'sucursales',
                'periodon', 'ebank', 'tasapeso', 'tasabs', 'bank', 'fkbanco', 'fecha2tr', 'fecha1tr',
                'caracteristicas', 'superficie', 'direccion',
                'vehiculomoto', 'casaterreno', 'marca', 'modelo', 'year', 'color', 'sm', 'sc', 'placa', 'telef2avalista', 'telef1avalista', 'emailavalista'
            ));

    }

    public function printBancoFecha(Request $request)
    {
        $fkbanco  = $request->fkbanco;
        $fecha    = $request->fecha;
        $saldobs  = $request->saldobs;
        $saldousd = $request->saldousd;
        $saldocop = $request->saldocop;

        $banco = Cwbancos::where('id', $fkbanco)->first();

        list($y,$m,$d) = explode('-',$fecha);
        $periodo = "$y$m";
        $datadate = "and a.fecha >= '$fecha 00:00:00' and a.fecha <= '$fecha 23:59:00'";

        $sqltr = "SELECT  date_format(fecha,'%d/%m/%Y') as fecha, a.monto, a.id, a.cdcd, a.descripcion, notas1, notas2,a.chequeado,
                          a.descripbene, a.tipobene, a.codbene, a.monto_bs, a.monto_dolar,   a.monto_peso, a.fk_transaccion, a.numero, a.codoper
                  FROM  cwtransaccion  a
                  WHERE a.fk_banco = '$fkbanco'
                    and a.periodo = '$periodo'
                    $datadate
                  ORDER BY a.fecha, a.id
                   ";

        $transaccions = DB::select($sqltr);
        $fecha = "$d/$m/$y";

        return view('printBancoFecha',   compact('banco', 'saldocop', 'saldobs', 'saldousd', 'fecha', 'transaccions'));
    }

    public function json()
    {

    }

    public function list(Request $request)
    {

    }


    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
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
    }
}
