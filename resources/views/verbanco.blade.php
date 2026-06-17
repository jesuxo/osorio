@extends('layouts.master')
@section('title')
   Banco {{ $bank->descrip}}
@endsection
@section('css')
    <style>
        .tituloa{
            font-size: 24px;
        }
        .btn-soft-light:hover, .codclieseleted{
            background-color: #e0f2ff !important;
        }
        .nav-pills .nav-link {
            background: #eee !important;
            border-bottom-right-radius: 0 !important;
            border-bottom-left-radius: 0 !important;
        }
        .nav-pills .nav-link.active  {
            background: #0072c5 !important;
            border-bottom-right-radius: 0 !important;
            border-bottom-left-radius: 0 !important;
        }
        .nav-pills{
            border-bottom: 1px solid #0072c5;
        }
        .tdline{
            border:1px solid #0072c5 !important;
            font-size: 12px;
        }
        .tdlineff{
            border-left:1px solid #fff !important;
            font-size: 12px;
            color: white !important;
            background-color: #0072c5 !important;
        }
        .cajapequenacolor {
            margin: 5px;
            padding: 1px;
            float: left;
            width: 120px;
            height: 120px;
            -moz-border-radius: 5px;
            border-radius: 5px;
            color: #fff;
            overflow: hidden;
            background-color: #e0f2ff !important;
        }
        .titulocaja {
            border-radius: 5px;
            min-height: 50px;
            font-size: 18px;
            text-align: center;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .underline{
            text-decoration: underline;
        }
    </style>
@endsection
@section('content')
    @php
        $faltancuentas = 0;
        $entramoneda   = 0;
        $tmonto_bs     = $ebank->saldo_bs;
        $tmonto_dolar  = $ebank->saldo_dolares;
        $tmonto_euro   = $ebank->saldo_euros;
        $tmonto_peso   = $ebank->saldo_pesos;
        $fechas        = [];
        $cadenatr      = '';
        $tmonto_bstr   = 0;
        $tmonto_dolartr= 0;
        $tmonto_eurotr = 0;
        $tmonto_pesotr = 0;
        $sumadebe      = 0;
        $sumahaber     = 0;
        $tusd          = 0;
        $tcop          = 0;
        $tbss          = 0;
        $procesartal   = 0;
    @endphp

    <form id="form1" name="form1" method="post" action="{{route('verbanco',['cdcd'=>0, 'fkbanco'=>$fkbanco, 'clearVect'=>0 ])}}" >
        @method('post')
        @csrf


        <div class="row">
            <div class="col-xxl-8" id="content8">


                @if($cdcd == 0)
                    <table border="0" width="98%" style="font-size: 11px">
                        <tr>
                            <td width="9%"  height="30" align="center" class="titulo tdline">Fecha
                                <input type="hidden" name="trdeleteid" id="trdeleteid"  value=""/>
                            </td>
                            <td width="3%"  align="left"class="titulo tdline  "><a href="javascript:;" class="gotodown" style="float:right"> Ir al final </a></td>

                            <td width="46%"  align="left"class="titulo tdline  ">
                                <input name="Beneficiariobusqueda" type="text" id="Beneficiariobusqueda" onchange="$('#form1').submit()"
                                       class="Beneficiariobusqueda" placeholder="Beneficiario, Descripci&oacute;n "
                                       value="{{(isset($Beneficiariobusqueda) and $Beneficiariobusqueda)? $Beneficiariobusqueda : ''}}"
                                       size="5" style="width:98%; height:40px; text-align:center; border: none; font-size:30px;color:#1E314F;"/>

                                <script>$('.Beneficiariobusqueda').select()</script>
                            </td>

                            <td width="11%"  align="center"  class="titulo tdline" >
                                <input name="Debe" type="text" id="Debe"
                                       onchange="$('#form1').attr('action','{{route('verbanco',['fkbanco'=>$fkbanco])}}'); $('#form1').submit()"
                                       placeholder="Debe" value="{{(isset($Debe) and $Debe !='')? $Debe : ''}}" size="5"
                                       style="width:98%; height:40px; text-align:center; font-size:30px;color:#1E314F; "/>
                            </td>
                            <td width="11%"  align="center" class="titulo tdline"  >
                                <input name="Haber" type="text" id="Haber" onchange="$('#form1').submit()"
                                       placeholder="Haber" value="{{(isset($Haber) and $Haber != '')? $Haber : ''}}" size="5"
                                       style="width:98%; height:40px; text-align:center; font-size:30px;color:#1E314F;"/>
                            </td>
                            <td width="4%"   align="center" class="titulo  "  >&nbsp; </td>
                        </tr>
                            @php

                            $numerito         = 0;
                            $tr               = [];
                            $vector           = [];

                            if(isset($Beneficiariobusqueda)){
                                $Beneficiariobusqueda = str_replace("*"," ",$Beneficiariobusqueda);
                                $vector=explode(" ",$Beneficiariobusqueda);

                                if($vector){
                                    foreach($vector as $value){
                                        if($numerito>0){$cadenatr.=' AND ';}
                                        $cadenatr.="(a.descripbene  like '%$value%' or
                                                         a.descripcion  like '%$value%' or
                                                         date_format(fecha, '%d/%m/%Y') like '%$value%' or
                                                         notas1 like '%$value%' or
                                                         notas2 like '%$value%')";
                                        $numerito++;
                                    }
                                }
                            }

                            $datadebe = $datahaber = '';
                            if(isset($Debe) and $Debe !='')
                                $datadebe = "and a.monto like '%$Debe%'";

                            if(isset($Haber) and $Haber !='')
                                $datahaber = "and a.monto like '%$Haber%'";

                             session(['trlines' => []]);

                            $datadate = '';
                            if(isset($fecha1tr) and $fecha1tr !='' and isset($fecha2tr) and $fecha2tr !='')
                                $datadate = "and a.fecha >= '$fecha1tr 00:00:00' and a.fecha <= '$fecha2tr 23:59:00'";


                            if(isset($cadenatr) and isset($vector) and $cadenatr != '' and strlen($cadenatr) > 2)
                                $cadenatr = " and ($cadenatr)";


                             $sqltr = "SELECT  date_format(fecha,'%d/%m/%Y') as fecha, a.monto, a.id, a.cdcd, a.descripcion, notas1, notas2,a.chequeado,
                                              a.descripbene, a.tipobene, a.codbene, a.monto_bs, a.monto_dolar,   a.monto_peso, a.fk_transaccion, a.numero, a.codoper
                                      FROM  cwtransaccion  a
                                      WHERE a.fk_banco = '$fkbanco'
                                                $cadenatr
                                                and a.periodo = '$periodo'
                                                $datadate
                                                $datahaber $datadebe
                                      ORDER BY a.fecha, a.id
                                         ";
                            $numtr = 0;
                            $transaccions = \Illuminate\Support\Facades\DB::select($sqltr);
                            foreach ($transaccions as $listtran){

                                    $fktr        = $listtran->fk_transaccion;
                                    $fecha       = $listtran->fecha;
                                    $notas1      = strtoupper($listtran->notas1);
                                    $notas2      = strtoupper($listtran->notas2);
                                    $numero      = $listtran->numero;
                                    $codbene     = $listtran->codbene;
                                    $codoper     = $listtran->codoper;
                                    $chequeado   = $listtran->chequeado;
                                    $descripcion = $listtran->descripcion;
                                    $descripbene = $listtran->descripbene;
                                    $tipobene    = $listtran->tipobene;
                                    $idtr        = (isset($fktr) and $fktr > 0)? $fktr : $listtran->id;
                                    $idtrreal    = $listtran->id;
                                    $monto       = $listtran->monto;
                                    $cdcdtr      = $listtran->cdcd;

                                    $numtr++;
                                    $monto_bs    = $listtran->monto_bs;
                                    $monto_dolar = $listtran->monto_dolar;
                                    $monto_peso  = $listtran->monto_peso;

                                    if(  !isset($tr[$idtrreal]))
                                        $tr[$idtrreal] = 1;

                                     if(!isset($fechas[$fecha])){
                                         $fechas[$fecha]['debe']  = 0;
                                         $fechas[$fecha]['haber'] = 0;
                                         $fechas[$fecha]['cop'] = 0;
                                         $fechas[$fecha]['eur'] = 0;
                                         $fechas[$fecha]['usd'] = 0;
                                         $fechas[$fecha]['bss'] = 0;
                                     }

                           @endphp
                            <tr {{(($numtr%2)==0)? 'bgcolor=#eeeeee' : '' }}>
                            <td height="25" align="center" valign="top">{{$fecha}}</td>
                            <td align="center" valign="top">
                                @if($tipobene == 1)
                                    <a target="_blank" style=" border: none; background: transparent !important;"
                                       href="/clientes/{{$codbene}}/tab1" >
                                        <i class="bi bi-person"></i>
                                    </a>
                                @endif
                            </td>
                            <td align="left">
                                <a href="imprimirReciboBanco/{{$idtr}}"  >
                                    {{($numero)? $numero : ''}} {{$descripcion}} {{(strlen($descripcion)>2)? '' :$descripbene}}
                                </a>
                                <table width="100%" border="0" class=" ">
                                    <tr>
                                        <td width="87" align="right" class=" ">{{($monto_bs >0.1)?  number_format($monto_bs,2,',','.'):''}}</td>
                                        <td width="20" align="center">{{ ($monto_bs >0.1)?'Bs.': ''    }}</td>

                                        <td width="93" align="right" class=" ">{{($monto_dolar >0.1)?  number_format($monto_dolar,2,',','.'):''}}</td>
                                        <td width="10" align="center">{{($monto_dolar >0.1)? '$' : ''  }}</td>

                                        <td width="119" align="right" class=" ">{{($monto_peso >0.1)?  number_format($monto_peso,2,',','.'):''}}</td>
                                        <td width="31" align="center">{{($monto_peso >0.1)?'Cop' : '' }}</td>
                                    </tr>
                                </table>
                                <div style=" width:100%; font-size:12px; color:#666">
                                         {{(strlen($notas1)>4)? "[$notas1]" : ''}}
                                         {{(strlen($notas2)>4)? "[$notas2]" : ''}}
                                </div>

                            </td>

                            <td align="right">
                                    @php
                                        if(($cdcdtr == 3 or $cdcdtr==2)){

                                            $tmonto_bs    += $monto_bs;
                                            $tmonto_dolar += $monto_dolar;
                                            $tmonto_peso  += $monto_peso;
                                            $sumadebe     += $monto ;
                                            echo number_format($monto, 2, ',', '.');

                                            $fechas[$fecha]['debe'] +=$monto;

                                        }
                                    @endphp
                            </td>
                            <td  align="right">@php
                                                   if(($cdcdtr == 1 or $cdcdtr==4) ){

                                                       $sumahaber +=  $monto ;
                                                       echo number_format($monto, 2, ',', '.');
                                                       $fechas[$fecha]['haber'] +=$monto;

                                                       $tmonto_bs    -= $monto_bs;
                                                       $tmonto_dolar -= $monto_dolar;
                                                       $tmonto_peso  -= $monto_peso;


                                                   }


                                                   $fechas[$fecha]['eur'] = $tmonto_euro;
                                                   $fechas[$fecha]['bss'] = $tmonto_bs;
                                                   $fechas[$fecha]['usd'] = $tmonto_dolar;
                                                   $fechas[$fecha]['cop'] = $tmonto_peso;
                                               @endphp
                            </td>
                            <td  align="center" bgcolor="#FFFFFF" style=" background: none;">
                                @if(Auth::user()  and auth()->user()->can('menu_caja_borrar_tr') )
                                    <button style="border: none; background: transparent !important;" type="button"  id="delete{{$idtrreal}}"
                                            onclick="$('#hrefeliminartrlink').attr('href','/eliminarTr/{{$idtr}}/{{$fkbanco}}');"
                                            data-bs-toggle="modal" data-bs-target="#eliminarTr"
                                         >
                                        <i class="bi bi-trash"></i>
                                    </button>
                                @endif
                            </td>
                        </tr>

                           @php  } @endphp
                        <tr >
                            <td height="25" align="center">&nbsp;</td>
                            <td align="center">&nbsp;</td>
                            <td align="left"><a href="#" name="lastline">&nbsp;</a></td>

                            <td align="right">&nbsp;</td>
                            <td  align="right">&nbsp;</td>
                            <td  align="center" bgcolor="#FFFFFF">&nbsp;</td>
                        </tr>
                    </table>
                @else


                    <table border="0" width="98%">
                        <tr>
                            <td width="12%" align="center" class="titulo tdline">

                                    @if($cdcd == 1)  Beneficiario @endif
                                    @if($cdcd == 2)  Descripci&oacute;n @endif

                            </td>
                            <td colspan="3" align="left"  class="titulo tdline">
                                <input type="hidden" name="iii" id="iii" value="" />
                                <input type="hidden" name="montotrupdate" id="montotrupdate" value="" />
                                <input type="hidden" name="cdcd"        id="cdcd"        value="{{(isset($cdcd))? $cdcd : 0}}"/>
                                <input type="hidden" name="fkbanco"     id="fkbanco"     value="{{(isset($fkbanco))? $fkbanco : 0}}"/>
                                <input type="hidden" name="codbene"     id="codbene"     value="{{$codbene}}"     />
                                <input type="hidden" name="tipobene"    id="tipobene"    value="{{$tipobene}}"    />

                                @if( $cdcd == 1)
                                   <input type="hidden" name="descripbene" id="descripbene" value="{{$descripbene}}" />
                                   {{(isset($descripbene))? $descripbene : ''}}
                                @endif

                                @if( $cdcd == 2)
                                    @php
                                        if($descripbene != '' and $descripciontr == '')
                                            $descripciontr = $descripbene;
                                    @endphp
                                    <input value="{{$descripciontr}}" required placeholder="Concepto de la transaccion"
                                           size="1" class="enviardatatr"   style="width: 98%"   name="descripciontr"   />
                                @endif

                           </td>
                            <td width="14%"align="center"     class="  tdline" >
                                <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#cambiarclie"
                                   class="   @if($cdcd == 1 or $cdcd == 4 or $cdcd == 2) tituloa @endif" > Clientes </a>
                            </td>
                            <td width="13%" align="center"  class="  tdline">
                                @if($cdcd == 1 or $cdcd == 4)
                                <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#cambiarprov" class="   @if($cdcd == 1 or $cdcd == 4) tituloa @endif"> Proveedores </a>
                                @endif
                            </td>
                        </tr>

                        @if(isset($descripbene) or isset($descripciontr))
                        <tr>
                            <td width="12%" align="center" class="titulo tdline">Fecha</td>
                            <td width="32%" class="tdline">
                                <input  type="date" value="{{(isset($fechatr))? $fechatr : ''}}" required="required"
                                                    size="1" class="  enviardatatr" name="fechatr"  style="width: 98%;  border: none"/>
                            </td>
                            <td width="21%" align="center" class="titulo tdline">Num Transacci&oacute;n (Opcional)</td>
                            <td colspan="3" class="titulo tdline"><input value="{{ (isset($numerotr))? $numerotr :'' }}"
                                                   placeholder="Nro documento/Nro Idenfiticador" type="text"
                                                   size="1" class="  enviardatatr"  style="width: 97%; border: none"
                                                   name="numerotr"  />
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td colspan="3">&nbsp;</td>
                        </tr>
                        @endif

                        @if(isset($fechatr) and (isset($codbene) or isset($descripciontr)))
                        <tr>
                            <td height="30"align="center" class="tdline titulo">Cuenta  </td>
                            <td colspan="3"class="tdline titulo" align="center">Descripci&oacute;n</td>
                            <td class="tdline titulo" align="center">
                                @if(isset($ctasblocked) and $ctasblocked == 1)
                                        Debe
                                @else
                                    <button type="button"
                                        onclick="$('#form1').attr('action','{{route('verbanco',['newdebehaber'=>'debe'])}}'); $('#form1').submit()"
                                         style="background:transparent; border:none;" class="nombrecampo">Debe</button>
                                @endif
                            </td>
                            <td class="tdline titulo" align="center">
                                @if(isset($ctasblocked) and $ctasblocked == 1)
                                    Haber
                                @else
                                    <button type="button"
                                        onclick="$('#form1').attr('action','{{route('verbanco',['newdebehaber'=>'haber'])}}'); $('#form1').submit()"
                                        style="background:transparent; border:none;" class="nombrecampo">Haber</button>
                                @endif
                            </td>
                        </tr>

                       @php
                           $arraycwTran = session('cwTran');
                       @endphp

                        @for($ii=1; $ii <= session('cwtr'); $ii++)

                            @if( isset($arraycwTran[$ii]['signo']))
                                <tr>
                                    <td>
                                        @if( $arraycwTran[$ii]['ppal'] == 0)
                                            <a href="javascript:;"
                                               data-bs-toggle="modal" data-bs-target="#cwcuentas"
                                                 onclick="$('#iii').val({{$ii}});">

                                                  @if($arraycwTran[$ii]['numerocta'] != '')
                                                        @php
                                                            echo $arraycwTran[$ii]['numerocta'];
                                                            if($ctaorigin == $arraycwTran[$ii]['numerocta'] and ($cdcd == 1 )){
                                                               $entramoneda = 1;
                                                            }
                                                        @endphp
                                                  @else
                                                      @php  echo '----'; $faltancuentas = 1; @endphp
                                                  @endif

                                            </a>
                                        @else

                                            @if($arraycwTran[$ii]['numerocta'])
                                                @php  $ctaorigin = $arraycwTran[$ii]['numerocta'];
                                                echo $arraycwTran[$ii]['numerocta']; @endphp
                                            @else
                                                @php  echo '----'; $faltancuentas = 1; @endphp
                                            @endif

                                        @endif

                                    </td>
                                    <td colspan="2">
                                        @if( $arraycwTran[$ii]['ppal'] ==0)
                                            <a href="" data-title="Agregar"
                                               data-bs-toggle="modal" data-bs-target="#cwcuentas"

                                               onclick="$('#iii').val({{$ii}});">

                                                    @if($arraycwTran[$ii]['descripcuenta'])
                                                         {{$arraycwTran[$ii]['descripcuenta']}}
                                                    @else
                                                        @php  echo '----'; $faltancuentas = 1; @endphp
                                                    @endif
                                            </a>
                                        @else
                                                @if($arraycwTran[$ii]['descripcuenta'])
                                                      {{$arraycwTran[$ii]['descripcuenta']}}
                                                @else
                                                    @php  echo '----'; $faltancuentas = 1; @endphp
                                                @endif
                                        @endif
                                    </td>
                                    <td width="8%" align="right">

                                        @if( $arraycwTran[$ii]['ppal'] ==0)
                                            <a href="javascript:;" style="background:transparent; border:none; color: red;"
                                             onclick=" $('#form1').attr('action','{{route('verbanco', ['sacardelalista'=> $ii])}}'); $('#form1').submit()"> X</a>
                                        @endif

                                    </td>
                                    <td align="right">

                                           @if( $arraycwTran[$ii]['signo']==0)
                                                @if( $arraycwTran[$ii]['ppal']==1)
                                                     {{ number_format($arraycwTran[$ii]['monto'],2,',','.')}}
                                                @else
                                                     <input  onchange=" $('#montotrupdate').val($(this).val()); $('#form1').attr('action','{{route('verbanco',[ 'imonto' => $ii, 'cambiarmontotr' => 1])}}'); $('#form1').submit() "
                                                        size="1"  value="{{$arraycwTran[$ii]['monto']}}"
                                                        name="montotr" class="enviardatatr " type="text"
                                                             style=" width: 80px; text-align:right" onclick="$(this).select()" />
                                                @endif
                                                @php $sumadebe += $arraycwTran[$ii]['monto']; @endphp
                                           @endif

                                    </td>
                                    <td align="right">

                                             @if( $arraycwTran[$ii]['signo']==1)

                                                    @if( $arraycwTran[$ii]['ppal']==1)
                                                        {{ number_format($arraycwTran[$ii]['monto'],2,',','.')}}
                                                    @else
                                                    <input value="{{$arraycwTran[$ii]['monto']}}"
                                                           onchange="$('#montotrupdate').val($(this).val()); $('#form1').attr('action','{{route('verbanco',['imonto' => $ii, 'cambiarmontotr' => 1])}}'); $('#form1').submit()"
                                                                    size="1"
                                                                    name="montotr" class="enviardatatr " type="text"
                                                                    style=" width: 80px; text-align:right"
                                                                onclick="$('#montotrupdate').val($(this).val()); $(this).select()" />
                                                    @endif
                                                @php  $sumahaber += $arraycwTran[$ii]['monto']; @endphp
                                            @endif

                                    </td>
                                </tr>
                            @endif
                        @endfor
                            <tr>
                                <td height="30">&nbsp; </td>
                                <td colspan="3" ></td>
                                <td class="tdline titulo" align="right">{{ number_format($sumadebe,2,',','.') }}</td>
                                <td class="tdline titulo" align="right">{{number_format($sumahaber,2,',','.')}}</td>
                            </tr>
                            <tr>
                                <td>&nbsp;</td>
                                <td colspan="2"class="rojoalert titulo" align="left">&nbsp;</td>
                                <td width="8%" >&nbsp;</td>
                                <td style="padding-left:15px"></td>
                                <td>
                                </td>
                            </tr>
                            <tr>
                                <td>Notas1</td>
                                <td colspan="2"class=" titulo" align="left">
                                    <table width="100%" border="0">
                                        <tr>
                                            <td width="70%">
                                                @php
                                                    if(!isset($notas1)){
                                                        $notas2 = '';
                                                    }
                                                @endphp
                                                <input style="width: 237px;"  value="{{(isset($notas1))? $notas1 :''}}"
                                                       required="required"  type="text" size="1" class="enviardatatr" name="notas1"   />
                                            </td>
                                            <td width="30%"  align="right"> </td>
                                        </tr>
                                    </table></td>

                                <td width="8%" ></td>
                                <td  style="padding-left:15px; font-size: 10px"> x Dolares (s)</td>
                                <td>
                                    <input
                                        size="1" value="{{(isset($dolarestr))? $dolarestr : ''}}"
                                        @if($entramoneda)  placeholder="Salen" @endif
                                        name="dolarestr" class="enviardatatr " type="text"
                                        style="text-align:right;  @if($entramoneda)  width:35% @else width:100% @endif" onClick="$(this).select()" />
                                    @if($entramoneda)
                                        <input
                                            size="1"  value="{{(isset($dolarestrentra))? $dolarestrentra : ''}}" placeholder="Entran"
                                            name="dolarestrentra" class="enviardatatr " type="text"
                                            style="text-align:right;   width:35% " onClick="$(this).select()" />
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td>Notas2</td>
                                <td colspan="2"class="  titulo" align="left"><table width="100%" border="0">
                                        <tr>
                                            <td width="70%"><input style="width: 237px;"  value="{{(isset($notas2))? $notas2 :''}}"   type="text" size="1" class="enviardatatr" name="notas2"   /></td>
                                            <td width="30%"  align="right" >{{(isset($tasabs))? $tasabs : ''}} Bs. x $&nbsp;</td>
                                        </tr>
                                    </table>
                                </td>
                                <td> <input
                                        size="1"  value="{{(isset($fbs))? $fbs : ''}}"type="text"
                                        name="fbs" id="fbs" class="enviardatatr " placeholder="$$" onchange="calcbs(this)"

                                        style="text-align:right; width: 80px;"onclick="$(this).select()" /></td>
                                <td style="padding-left:15px; font-size: 10px;">x Bolivares (Bs) </td>
                                <td>  <input
                                        size="1"  value="{{(isset($bstr))? $bstr : ''}}"
                                        name="bstr" id="bstr" class="enviardatatr" type="text"
                                        @if($entramoneda) placeholder="Salen"  @endif
                                        onchange="calcbsrev(this)"
                                        style="text-align:right; @if($entramoneda) width:35% @else width:100% @endif" onClick="$(this).select()" />
                                    @if($entramoneda)
                                        <input
                                            size="1"  value="{{(isset($bstrentra))? $bstrentra : ''}}" placeholder="Entran"
                                            name="bstrentra" class="enviardatatr " type="text"

                                            style="text-align:right;   width:35%  " onClick="$(this).select()" />

                                    @endif
                                    @php

                                        $montocal    = $montoentra = 0;
                                        $montocal   += $dolarestr;
                                        if($entramoneda)
                                            $montoentra += $dolarestrentra;


                                        if(isset($fbs) and $fbs > 0){
                                            $fbs1 = $bstr / $fbs;
                                            if($fbs1 >0)
                                                $montocal +=  $bstr / $fbs1;
                                            if($entramoneda and $bstrentra)
                                                $montoentra +=  $fbs;
                                        }

                                    @endphp</td>
                            </tr>
                            <tr>
                                <td height="37">Notas3</td>
                                <td colspan="2"class="  titulo" align="left">
                                    <table width="100%" border="0">
                                        <tr>
                                            <td width="70%"><input style="width: 237px;" value="{{(isset($notas3))? $notas3 :''}}"  type="text" size="1" class="enviardatatr" name="notas3"  /></td>
                                            <td width="30%" align="right">{{(isset($tasapeso))? number_format($tasapeso,2,',','.') : ''}} Cop x $&nbsp;</td>
                                        </tr>
                                    </table>
                                </td>
                                <td><input
                                        size="1"  value="{{(isset($fpesos))? $fpesos :''}}"type="text"
                                        name="fpesos" id="fpesos" class="enviardatatr " placeholder="$$" onchange="calcpeso(this)"

                                        style="text-align:right; width: 80px;"onclick="$(this).select()" />
                                </td>
                                <td style="padding-left:15px; font-size: 10px;"> x Pesos (Cop) </td>
                                <td>
                                    <input
                                        size="1"  value="{{(isset($pesostr))? $pesostr : ''}}"
                                        name="pesostr" id ="pesostr" class="enviardatatr "type="text" @if($entramoneda) placeholder="Salen" @endif

                                        onchange="calcpesosrev()"
                                        style="text-align:right; @if($entramoneda)  width:35% @else width:100% @endif" onClick="$(this).select()" />
                                        @if($entramoneda)
                                            <input
                                                size="1"  value="{{(isset($pesostrentra))? $pesostrentra : ''}}" placeholder="Entran"
                                                name="pesostrentra" class="enviardatatr " type="text"

                                                style="text-align:right;  width:35% " onClick="$(this).select()" />
                                        @endif
                                      @php
                                        if($fpesos > 0){

                                            $fpesos1 = $pesostr / $fpesos;
                                            if($fpesos1 >0)
                                                $montocal +=  $pesostr / $fpesos1;
                                            if($entramoneda and $pesostrentra)
                                                $montoentra +=    $fpesos;
                                        }
                                       @endphp

                                </td>
                            </tr>

                                @php
                                    $entracheck = 1;
                                @endphp
                            <tr>
                                <td height="37">

                                    <button  type="button" onClick="$('#form1').submit()" class="btn btn-primary"
                                             style="border-radius:5px; width:100px" >Actualizar</button>
                                </td>
                                <td>
                                    <span colspan="2" class="rojoalert titulo" align="center">
                                        @php
                                            if($entramoneda and number_format($montocal - $montoentra,2,'.','') != 0){
                                                $entracheck = 0;
                                                echo number_format($montocal - $montoentra,2,',','.');
                                            }

                                            $cwTran = session('cwTran');

                                            $procesoextra = 1;

                                            if(($realizarletra == 1 or  $realizarpagare == 1) and $tipobene == 1){
                                                if(!$fechapagar or !$montoproceso)
                                                    $procesoextra = 0;
                                            }

                                            $procesofinancia = 1;

                                            if(($realizarfinancia == 1) and $tipobene == 1){
                                                if(!$mtocuota or !$cantcuotas or !$fechainiciofinan or $fechainiciofinan == '' or !$fksucu or $fksucu == 0)
                                                    $procesofinancia = 0;
                                            }

                                            $procesobien = 1;
                                            if($vehiculomoto == 1 and (!$marca or!$sm or !$sc or !$placa or !$modelo or !$color or !$year)){
                                                $procesobien = 0;
                                            }

                                            $procesocasaterreno = 1;
                                            if($casaterreno == 1 and (!$superficie or !$caracteristicas or !$direccion)){
                                                $procesocasaterreno = 0;
                                            }

                                        @endphp

                                        @if(isset($fechatr) and $fechatr
                                            and abs($montocal - $sumahaber)<0.01
                                            and $cwTran[1]['monto'] > 0
                                            and !$faltancuentas
                                            and ($descripbene or $descripciontr)
                                            and $sumahaber == $sumadebe
                                            and ($sumadebe > 0 or $sumahaber >0)
                                            and $entracheck
                                            and $procesoextra    == 1
                                            and $procesofinancia == 1
                                            and $procesobien     == 1
                                            and $procesocasaterreno == 1
                                            and $nopuedeingreso  == 0
                                            and ($pesostr > 0 or $dolarestr >0 or $bstr >0))

                                            @php
                                                $procesartal = 1;
                                            @endphp

                                            <button  type="button"  id="buttonprocesar"
                                                     onClick=" $('#form1').attr('action','{{ route('verbanco', ['ingresarTr' => 1, 'procesarbanco' => 1, 'checkentramoneda' => $entramoneda])  }}'); $('#form1').submit()"
                                                     class="btn btn-primary" style="border-radius:5px; width: 100px" >Procesar</button>
                                        @endif
                                    <span style="color:red">
                                        @php
                                            if(isset($nocuadranm)     and $nocuadranm     !='' ) echo " No coincide el monto a pagar con lo asignado a cada cxp ($nocuadranm)<br>";
                                            if(isset($denuevoprov)    and $denuevoprov    > 0  ) echo " Debe volver al proveedor para poder continuar<br>";
                                            if(isset($nopuedeingreso) and $nopuedeingreso == 1 ) echo " No puede seleccionar un banco al momento de realizar un ingreso.<br>";
                                            if(!$codbene and $descripbene) echo " Por favor utilice el buscador de proveedores o clientes.<br>";
                                            if($procesoextra    == 0 and ($realizarletra    == 1 or  $realizarpagare == 1)) echo " Fecha o monto requerido para pagare o letra de cambio<br>";
                                            if($procesofinancia == 0 and ($realizarfinancia == 1 )) echo "Sucursal, Monto a Financiar y Cantidad de cuotas requeridas<br>";
                                            if($procesobien     == 0 and ($vehiculomoto     == 1)) echo "Datos del bien a financiar requeridos";
                                            if($procesocasaterreno == 0 and ($casaterreno   == 1)) echo "Datos del Inmueble requeridos";
                                        @endphp
                                    </span>
                                </td>
                                <td align="left">

                                </td>
                                <td style="padding-left:15px">
                                    Total =
                                </td>
                                <td align="right">{{number_format($montocal ,2,',','.')}}</td>
                            </tr>


                        @endif
                    </table>
                    @if($tipobene==1 and $cdcd == 1)
                        <div class="card mt-3" onclick="$('#buttonprocesar').hide();">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Procesos Adicionales</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-check form-switch form-switch-lg mb-3" dir="ltr">
                                            <input type="checkbox" class="form-check-input checkprocesos" id="realizarpagare" name="realizarpagare"
                                                   {{(isset($realizarpagare) and $realizarpagare == 1)?'checked' : ''}} value="1">
                                            <label class="form-check-label" for="realizarpagare">Realizar pagar&eacute;?</label>
                                        </div>
                                        <div class="form-check form-switch form-switch-lg mb-3" dir="ltr">
                                            <input type="checkbox" class="form-check-input checkprocesos" id="realizarletra" name="realizarletra"
                                                   {{(isset($realizarletra) and $realizarletra == 1)?'checked' : ''}} value="1">
                                            <label class="form-check-label" for="realizarletra">Realizar letra de cambio?</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <table width="100%" border="0" class="tdline" id="checkprocesos"
                                               @if($realizarletra==0 and $realizarpagare ==0) style="display: none" @endif>
                                            <tr>
                                                <td height="30" colspan="2" align="left"  class="titulo tdline">
                                                    <strong>Datos requeridos:</strong>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td valign="top">Nombre Avalista </td>
                                                <td>
                                                    <input style="width: 237px;"  value="{{(isset($nombreavalista))? $nombreavalista :''}}"
                                                           type="text" size="1" class="" name="nombreavalista"  placeholder=""  />
                                                </td>
                                            </tr>
                                            <tr>
                                                <td valign="top">C&eacute;dula Avalista  </td>
                                                <td>
                                                    <input style="width: 237px;"  value="{{(isset($cedulaavalista))? $cedulaavalista :''}}"
                                                           type="text" size="1" class="" name="cedulaavalista"    placeholder=""/>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td valign="top"> Tel&eacute;fono1 Avalista  </td>
                                                <td>
                                                    <input style="width: 237px;"  value="{{(isset($telef1avalista))? $telef1avalista :''}}"
                                                           type="text" size="1" class="" name="telef1avalista"    placeholder=""/>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td valign="top">Tel&eacute;fono2 Avalista  </td>
                                                <td>
                                                    <input style="width: 237px;"  value="{{(isset($telef2avalista))? $telef2avalista :''}}"
                                                           type="text" size="1" class="" name="telef2avalista"    placeholder=""/>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td valign="top">Email Avalista  </td>
                                                <td>
                                                    <input style="width: 237px;"  value="{{(isset($emailavalista))? $emailavalista :''}}"
                                                           type="text" size="1" class="" name="emailavalista"    placeholder=""/>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td valign="top">Domicilio  Avalista  </td>
                                                <td>
                                                    <input style="width: 237px;"  value="{{(isset($domicilioavalista))? $domicilioavalista : ''}}"
                                                           type="text" size="1" class="" name="domicilioavalista"  placeholder=""   />
                                                </td>
                                            </tr>
                                            <tr>
                                                <td valign="top">Lugar de pago </td>
                                                <td>
                                                    <input style="width: 237px;"  value="{{(isset($ciudadpago) and $ciudadpago != '')? $ciudadpago :' EN LA CIUDAD DE ...'}}" placeholder="(Opcional)"
                                                           type="text" size="1" class="" name="ciudadpago"   />  </td>
                                            </tr>
                                            <tr>
                                                <td valign="top">Monto a pagar</td>
                                                <td>
                                                    <input style="width: 100px;"  value="{{(isset($montoproceso))? $montoproceso :''}}"
                                                           type="number" size="1" class="" name="montoproceso"   />
                                                    DOLARES($)
                                                </td>
                                            </tr>
                                            <tr>
                                                <td valign="top">Fecha limite que debe pagar</td>
                                                <td>
                                                    <input  type="date" value="{{(isset($fechapagar))? $fechapagar : ''}}"
                                                            size="1" class="" name="fechapagar"  style="width: 98%;  border: none"/>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card mt-3" onclick="$('#buttonprocesar').hide();">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Financiamiento</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-check form-switch form-switch-lg mb-3" dir="ltr">
                                                <input type="checkbox" class="form-check-input checkfinancia" id="realizarfinancia"
                                                       name="realizarfinancia"
                                                       {{(isset($realizarfinancia) and $realizarfinancia == 1)?'checked' : ''}} value="1">
                                                <label class="form-check-label" for="realizarpagare">Generar financiamiento?</label>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            @php


                                                    if($saldofinancia==0 and $montocal > 0 and $inicialfinancia > 0)
                                                        $saldofinancia = $montocal-$inicialfinancia;

                                                    if($inicialfinancia > 0 and $porcinicialant == 0)
                                                        $porcinicial = ($inicialfinancia/$valorfinancia) * 100;

                                                    if($inicialfinancia == 0 and $porcinicial > 0)
                                                        $inicialfinancia  = $valorfinancia * ($porcinicial/100);

                                                    if($saldofinancia == 0  and $inicialfinancia>0){
                                                        $saldofinancia = $valorfinancia - $inicialfinancia;
                                                    }

                                                    $montocalculo = $costofinancia+$saldofinancia;
                                            @endphp
                                            <table  width="100%" class="tdline" id="checkfinancia"
                                                    @if($realizarfinancia==0 ) style="display: none" @endif>

                                                <tr >
                                                    <td> &nbsp;</td>
                                                    <td></td>
                                                    <td width="10%" colspan="5"  align="left"> &nbsp;{{(isset($notas1) and $notas1 != '')? $notas1 : ''}}</td>
                                                </tr>
                                                <tr >
                                                    <td width="20%"  >Valor</td>
                                                    <td width="10%"  align="right">
                                                        <input style="width: 99%;"  value="{{(isset($valorfinancia))? $valorfinancia :''}}"
                                                               type="number" size="1" class="" name="valorfinancia"
                                                               onchange="$('#form1').attr('action','{{route('verbanco',['financiar'=>1])}}'); $('#form1').submit();"/>
                                                    </td>

                                                    <td align="center" > </td>
                                                    <td align="center" > </td>
                                                    <td align="center" > </td>
                                                    <td align="left" > Costo Bien</td>
                                                    <td align="rigth"  width="15%">
                                                        <input style="width: 99%;"  value="{{(isset($costobien))? $costobien :''}}"
                                                               type="number" size="1" class="" name="costobien" required
                                                               onchange="$('#form1').attr('action','{{route('verbanco',['financiar'=>1])}}'); $('#form1').submit();"/> </td>
                                                </tr>
                                                <tr >
                                                    <td width="10%" >Inicial</td>
                                                    <td width="10%" align="right">
                                                        <input style="width: 99%;"  value="{{(isset($inicialfinancia))? $inicialfinancia :''}}"
                                                               type="number" size="1" class="" name="inicialfinancia"
                                                               onchange="$('#form1').attr('action','{{route('verbanco',['financiar'=>1])}}');  $('#form1').submit();"/>
                                                    </td>
                                                    <td width="10%"  align="left">
                                                        <input type="hidden" value="{{$porcinicial}}" name="porcinicialant" />
                                                        <input style="width: 50px;"  value="{{(isset($porcinicial) and $porcinicial >0)? $porcinicial :''}}"
                                                               type="number" size="1" class="" name="porcinicial"
                                                               onchange="$('#form1').attr('action','{{route('verbanco',['financiar'=>1])}}'); $('#form1').submit();"   />%
                                                    </td>
                                                    <td width="15%"></td>
                                                    <td width="15%"></td>
                                                    <td width="30%" colspan="2">

                                                        <select required class="form-control"   data-choices
                                                                onchange="$('.error-msg').hide();
                                                                $('.datosocultos').fadeIn(); bancoSucursal(this.value)"
                                                                name="fksucu"  id="fksucu">

                                                            <option value=""  {{(isset($fksucu) and $fksucu == 0)? 'selected': ''}}>Seleccione</option>
                                                            @foreach($sucursales as $sucursal)
                                                                <option value="{{$sucursal->id}}" {{(isset($fksucu) and $fksucu == $sucursal->id)? 'selected': ''}}>{{$sucursal->descrip}}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                </tr>
                                                <tr >
                                                    <td >Saldo a Financiar</td>
                                                    <td align="right">
                                                        <input style="width: 99%;"  value="{{(isset($saldofinancia))? $saldofinancia :''}}"
                                                               type="number" size="1" class="" name="saldofinancia"
                                                               onchange="$('#form1').attr('action','{{route('verbanco',['financiar'=>1])}}'); $('#form1').submit();" />
                                                    </td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td>Fecha 1era Cuota</td>
                                                    <td><input type="date" name="fechainiciofinan"
                                                               onchange="$('#form1').attr('action','{{route('verbanco',['financiar'=>1])}}'); $('#form1').submit();"
                                                               size="1" style="width:99%" value="{{$fechainiciofinan}}" /></td>
                                                </tr>

                                                <tr >
                                                    <td >Costo Financiero</td>
                                                    <td align="right">
                                                        <input style="width: 99%;"  value="{{(isset($costofinancia))? $costofinancia :''}}"
                                                               type="number" size="1" class="" name="costofinancia"
                                                               onchange="$('#form1').attr('action','{{route('verbanco',['financiar'=>1])}}'); $('#form1').submit();" />
                                                    </td>
                                                    <td align="left">
                                                        <input style="width: 50px;"  value="{{(isset($porccostofi) and $porccostofi >0)? $porccostofi :''}}"
                                                               type="number" size="1" class="" name="porccostofi"
                                                               onchange="$('#form1').attr('action','{{route('verbanco',['financiar'=>1])}}'); $('#form1').submit();"   />%
                                                    </td>
                                                    <td></td>
                                                    <td></td>
                                                    <td>Cantidad Cuotas</td>
                                                    <td>
                                                        <input style="width: 99%;"  value="{{(isset($cantcuotas) and $cantcuotas >0)? $cantcuotas :''}}"
                                                               type="number" size="1" class="" name="cantcuotas" id="cantcuotas" placeholder=" Ej. 44 - 66 "
                                                               onchange="$('#form1').attr('action','{{route('verbanco',['financiar'=>1])}}'); $('#form1').submit();"   />
                                                    </td>
                                                </tr>
                                                <tr >
                                                    <td > &nbsp;</td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td>Monto Cuota</td>
                                                    <td>
                                                        <input style="width: 99%;"  value="{{(isset($mtocuota) and $mtocuota >0)? $mtocuota :''}}"
                                                               type="number" size="1" class="" name="mtocuota" placeholder=""
                                                               onchange="$('#form1').attr('action','{{route('verbanco',['financiar'=>1])}}'); $('#form1').submit();"   />
                                                    </td>
                                                </tr>

                                                <tr >
                                                    <td> &nbsp;</td>
                                                    <td></td>
                                                    <td align="center" > </td>
                                                    <td align="center" > </td>
                                                    <td align="center" > </td>
                                                    <td align="center" > </td>
                                                    <td align="center" > </td>
                                                </tr>

                                            </table>
                                        </div>
                                    </div>
                                </div>
                        </div>

                        <div class="card mt-3" onclick="$('#buttonprocesar').hide();">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Datos del Vehiculo Financiado</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-check form-switch form-switch-lg mb-3" dir="ltr">
                                            <input type="checkbox" class="form-check-input checkvehiculomoto" id="vehiculomoto" name="vehiculomoto"
                                                   {{(isset($vehiculomoto) and $vehiculomoto == 1)?'checked' : ''}} value="1">
                                            <label class="form-check-label" for="vehiculomoto">Financiamiento de Veh&iacute;culo/Moto?</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <table width="100%" border="0" class="tdline" id="checkvehiculomoto"
                                               @if($vehiculomoto==0) style="display: none" @endif>
                                            <tr>
                                                <td height="30" colspan="2" align="left"  class="titulo tdline">
                                                    <strong>Datos requeridos:</strong>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td valign="top">Marca </td>
                                                <td>
                                                    <input style="width: 237px;"  value="{{(isset($marca))? $marca :''}}"
                                                           type="text" size="1" class="" name="marca"  placeholder=""  />
                                                </td>
                                            </tr>
                                            <tr>
                                                <td valign="top">Modelo </td>
                                                <td>
                                                    <input style="width: 237px;"  value="{{(isset($modelo))? $modelo :''}}"
                                                           type="text" size="1" class="" name="modelo"    placeholder=""/>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td valign="top"> A&ntilde;o  </td>
                                                <td>
                                                    <input style="width: 237px;"  value="{{(isset($year))? $year :''}}"
                                                           type="text" size="1" class="" name="year"    placeholder=""/>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td valign="top">Color </td>
                                                <td>
                                                    <input style="width: 237px;"  value="{{(isset($color))? $color :''}}"
                                                           type="text" size="1" class="" name="color"    placeholder=""/>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td valign="top">Serial Motor</td>
                                                <td>
                                                    <input style="width: 237px;"  value="{{(isset($sm))? $sm :''}}"
                                                           type="text" size="1" class="" name="sm"    placeholder=""/>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td valign="top">Serial Carroceria </td>
                                                <td>
                                                    <input style="width: 237px;"  value="{{(isset($sc))? $sc : ''}}"
                                                           type="text" size="1" class="" name="sc"  placeholder=""   />
                                                </td>
                                            </tr>
                                            <tr>
                                                <td valign="top">Placa </td>
                                                <td>
                                                    <input style="width: 237px;"  value="{{(isset($placa) and $placa != '')? $placa :''}}" placeholder=""
                                                           type="text" size="1" class="" name="placa"   />  </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card mt-3" onclick="$('#buttonprocesar').hide();">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Datos del bien Inmueble Financiado</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-check form-switch form-switch-lg mb-3" dir="ltr">
                                            <input type="checkbox" class="form-check-input checkcasaterreno" id="casaterreno" name="casaterreno"
                                                   {{(isset($casaterreno) and $casaterreno == 1)?'checked' : ''}} value="1">
                                            <label class="form-check-label" for="casaterreno">Financiamiento de Casa/Terreno?</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <table width="100%" border="0" class="tdline" id="checkcasaterreno"
                                               @if($casaterreno==0) style="display: none" @endif>
                                            <tr>
                                                <td height="30" colspan="2" align="left"  class="titulo tdline">
                                                    <strong>Datos requeridos:</strong>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td valign="top">Direcci&oacute;n </td>
                                                <td>
                                                    <input style="width: 237px;"  value="{{(isset($direccion))? $direccion :''}}"
                                                           type="text" size="1" class="" name="direccion"  placeholder=""  />
                                                </td>
                                            </tr>
                                            <tr>
                                                <td valign="top">Caracter&iacute;sticas </td>
                                                <td>
                                                    <input style="width: 237px;"  value="{{(isset($caracteristicas))? $caracteristicas :''}}"
                                                           type="text" size="1" class="" name="caracteristicas"    placeholder=""/>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td valign="top"> Superficie  </td>
                                                <td>
                                                    <input style="width: 237px;"  value="{{(isset($superficie))? $superficie :''}}"
                                                           type="text" size="1" class="" name="superficie"    placeholder=""/>
                                                </td>
                                            </tr>

                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                @endif
                </div>
                <div class="col-xxl-4">
                    <div class="card" style="position: sticky; top: 80px;">
                        <div class="card-header" style="display: flex; justify-content: space-between;  align-items: center;">
                            <a class="card-title mb-0 h6" href="{{route('verbanco',['fkbanco'=>$fkbanco,'cdcd'=>0, 'clearVect'=>0])}}" onclick="contentloading('content8')"> {{$bank->descrip}}  </a>
                            <a href="/bancos/padrebancos/{{$bank->numpadre}}"  class="text-end"> << volver </a>
                        </div>
                        <div class="card-body">
                            @if($cdcd == 0)
                                <table border="0" width="100%" class="mb-2 " style="   border: var(--tb-card-border-width) solid var(--tb-card-border-color); border-radius: 5px !important;">
                                    <tr>
                                        <td width="77%" class="px-2" align="left">Bolivares (Bs)</td>
                                        <td width="23%" class="px-2" align="right"> {{number_format($tmonto_bs,2,',','.')}}</td>
                                    </tr>
                                    <tr>
                                        <td class="px-2" align="left">Dolares (s)</td>
                                        <td class="px-2" align="right">{{number_format($tmonto_dolar,2,',','.')}} </td>
                                    </tr>
                                    <tr>
                                        <td class="px-2" align="left">Pesos (Cop)</td>
                                        <td class="px-2" align="right"> {{number_format($tmonto_peso,2,',','.')}}</td>
                                    </tr>
                                </table>
                            @endif
                            <table width="100%" border="0"  style="  border: var(--tb-card-border-width) solid var(--tb-card-border-color);  border-radius:5px !important; ">

                                <tr>
                                    <td colspan="2" height="30" align="center" >
                                        <a href="{{route('verbanco',['cambiarclie'=>'V15184480', 'cdcd'=>1, 'fkbanco'=>$fkbanco, 'clearVect' => 1])}}" class=" {{($cdcd == 1)? 'tituloa underline' : ''}}"
                                        onclick=" contentloading('content8')"
                                        >Egreso</a>
                                    </td>
                                    <td colspan="2" align="center" >
                                        <a href="{{route('verbanco',['cambiarclie'=>'V15184480', 'cdcd'=>2, 'fkbanco'=>$fkbanco, 'clearVect' => 1])}}" class=" {{($cdcd == 2)? 'tituloa underline' : ''}}"
                                        onclick=" contentloading('content8')"
                                        >Ingreso</a>
                                    </td>
                                </tr>

                            </table>
                            @if($cdcd == 0)
                                <table width="100%" border="0" class="  ">
                                    <tr>
                                        <td width="5%" height="40" align="center" >Desde</td>
                                        <td width="45%" align="center"> <input type="date" name="fecha1tr"  size="1"style="width:90px"value="{{$fecha1tr}}" /></td>
                                        <td width="5%" align="right">Hasta</td>
                                        <td width="45%" align="right"> <input type="date" name="fecha2tr"  size="1" style="width:90px"  value="{{$fecha2tr}}"  /></td>
                                    </tr>
                                    <tr>
                                        <td height="43" colspan="4" align="center">
                                            <button type="button" class="btn btn-primary" onClick="$('#form1').submit(); contentloading('content8')" style="border-radius:5px; width:100%;">
                                                Actualizar
                                            </button>
                                        </td>
                                    </tr>

                                </table>
                            @endif

                            <table border="0" width="100%"  class="mt-3">
                                <tr>
                                    <td width="43%" align="left">Tasa Bs</td>
                                    <td width="57%" align="right">
                                        <input name="tasabscambiar"  id="tasabscambiar"
                                               type="number" required value="{{$tasabs}}"
                                           size="1" style="width:100%; text-align:right" placeholder="Tasa bs"/>
                                    </td>
                                </tr>

                                <tr>
                                    <td align="left">Tasa Pesos </td>
                                    <td align="right">
                                        <input name="tasapesocambiar" required id ="tasapesocambiar"
                                        value="{{$tasapeso}}" size="1"  type="number"
                                               style="width:100%; text-align:right" placeholder="Tasa pesos"/>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="left">&nbsp;</td>
                                    <td align="right">
                                        <button type="button" class="btn btn-primary"
                                                onClick="$('#form1').submit(); contentloading('content8')" style="border-radius:5px; width:100%;">
                                            Actualizar tasas
                                        </button>
                                    </td>
                                </tr>

                            </table>

                            @if(isset($fechas) and $cdcd == 0)
                                <table border="0" width="100%">
                                    <tr>
                                        <td width="13%" height="33" align="center" class="tdline titulo">Fecha</td>
                                        <td width="15%" align="center" class="tdline titulo">Bs</td>
                                        <td width="13%" align="center" class="tdline titulo">$</td>
                                        <td width="18%" align="center" class="tdline titulo">COP</td>
                                        <td width="5%" align="center" class="  titulo">&nbsp; </td>
                                        <td width="12%" align="center" class="tdline titulo">&nbsp;</td>
                                    </tr>
                                    @php
                                        $fnum    = 0;
                                        $ttdebe  = 0;
                                        $tthaber = 0;
                                @endphp
                            @foreach($fechas as $indx => $item)
                                @php
                                    $fnum += 1;
                                    $tbss  += $fechas[$indx]['bss'];
                                    $tusd  += $fechas[$indx]['usd'];
                                    $tcop  += $fechas[$indx]['cop'];

                                    $ttdebe  += $fechas[$indx]['debe'];
                                    $tthaber += $fechas[$indx]['haber'];
                                    list($di,$mi,$yi) = explode('/',$indx);
                                @endphp

                                <tr {{ (($fnum%2)==0)? 'bgcolor=#eeeeee' : ''}}>
                                    <td height="27" align="center" >
                                        <a href="javascript:;"  onClick="$('#fecha1tr').val('{{$indx}}'); $('#fecha2tr').val('{{$indx}}'); $('#form1').submit()">
                                            {{$indx}}
                                        </a>
                                    </td>
                                    <td align="right">{{number_format($fechas[$indx]['bss'],2,',','.')}}</td>
                                    <td align="right">{{number_format($fechas[$indx]['usd'],2,',','.')}}</td>
                                    <td align="right">{{number_format($fechas[$indx]['cop'],2,',','.')}}</td>
                                    <td align="right" style="background:#f8f8f8;">&nbsp; </td>
                                    <td align="center">
                                        <a href="{{route('bancos.printBancoFecha',['fkbanco'=>$fkbanco, 'fecha'=>"$yi-$mi-$di", "saldobs"=>$fechas[$indx]['bss'] , "saldousd"=>$fechas[$indx]['usd'], "saldocop"=>$fechas[$indx]['cop']])}}"  target="_blank">
                                            <i class="bi bi-printer"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach

                            </table>
                            @endif
                    </div>
                </div>
            </div>
        </div>
    </form>
    @php

        $tmonto_bs    = ($tmonto_bs)?    $tmonto_bs    : 0;
        $tmonto_dolar = ($tmonto_dolar)? $tmonto_dolar : 0;
        $tmonto_euro  = ($tmonto_euro)?  $tmonto_euro  : 0;
        $tmonto_peso  = ($tmonto_peso)?  $tmonto_peso  : 0;


         if(  !$Beneficiariobusqueda and !$Debe and !$Haber and $cdcd == 0){

             $thisbank = \App\Models\Cwbancos::find($fkbanco);
             $thisbank->sbs      = $tmonto_bs;
             $thisbank->sdolares = $tmonto_dolar;
             $thisbank->spesos   = $tmonto_peso;
             $thisbank->save();

             $ebank  = \App\Models\Cwebancos::where(['fk_banco'=>$fkbanco, 'periodo' => (isset($periodon))? $periodon : ''])->first();

             if(!$ebank){
                 $ebank = new \App\Models\Cwebancos();
                 $ebank->periodo       = "$periodon";
                 $ebank->fk_banco      = $fkbanco;
                 $ebank->saldo_bs      = 0;
                 $ebank->saldo_dolares = 0;
                 $ebank->saldo_pesos   = 0;
                 $ebank->save();
            }

             $ebank->saldo_bs      = $tmonto_bs;
             $ebank->saldo_dolares = $tmonto_dolar;
             $ebank->saldo_pesos   = $tmonto_peso;
             $ebank->save();

	 }

    @endphp


    <div id="eliminarTr"  class="modal fade" tabindex="-1" aria-labelledby="eliminarTr"  aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myModalLabel"> Desea eliminar este registro?</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
                </div>
                <div class="modal-body">
                    <h5 class="fs-15">
                    </h5>
                </div>
                <div class="modal-footer">
                    <a type="button" class="btn btn-danger " id="hrefeliminartrlink" href="" >Si, Eliminar</a>
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <div id="cwcuentas"   class="modal fade" tabindex="-1" aria-labelledby="cwcuentas"   aria-hidden="true" >
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myModalLabel"> Busqueda de cuenta</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
                </div>
                <div class="modal-body">

                    <table width="100%"  class="tdline" cellpadding="0" cellspacing="0" border="0" align="center"  >
                        <tr>
                            <td width="85%" align="center">
                                <input name="buscarcuenta" type="text" id="buscarcuenta" value="" size="5" style="width:98%; height:40px; text-align:center; border: none; font-size:30px;color:#1E314F;"/>
                            </td>
                            <td width="15%" align="center">
                                <button class="btn btn-primary"> Buscar</button>
                            </td>
                        </tr>
                    </table>

                    <div id="searchcontentcuenta">

                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="cambiarclie" class="modal fade" tabindex="-1" aria-labelledby="cambiarclie" aria-hidden="true" >
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myModalLabel"> Busqueda de cliente</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
                </div>
                <div class="modal-body">

                    <table width="100%"  class="tdline" cellpadding="0" cellspacing="0" border="0" align="center"  >
                        <tr>
                            <td width="85%" align="center">
                                <input name="buscarcliente" type="text" id="buscarcliente" value="" size="5" style="width:98%; height:40px; text-align:center; border: none; font-size:30px;color:#1E314F;"/>
                            </td>
                            <td width="15%" align="center">
                                <button class="btn btn-primary"> Buscar</button>
                            </td>
                        </tr>
                    </table>

                    <div id="searchcontentclie">

                    </div>

                </div>
            </div>
        </div>
    </div>

    <div id="cambiarprov" class="modal fade" tabindex="-1" aria-labelledby="cambiarprov" aria-hidden="true" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myModalLabel"> Busqueda de proveedor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
                </div>
                <div class="modal-body">
                    asd
                </div>
            </div>
        </div>
    </div>


@endsection
@section('scripts')

    <script src="{{ URL::asset('build/js/app.js') }}"></script>
    <script>
        @if($financiar)
            $('#cantcuotas').focus().select();
        @endif
        function calcbs(obj) {
            $('#buttonprocesar').hide();
            var fbs  = $('#fbs').val();
            var tasabscambiar = $('#tasabscambiar').val();
            var fbs  = parseFloat(fbs);
            var tasabscambiar = parseFloat(tasabscambiar);

            var bstr = fbs * tasabscambiar;
            $('#bstr').val(bstr);
            $("#form1").submit();
        }

        function calcbsrev(obj) {
            $('#buttonprocesar').hide();
            var bstr  = $('#bstr').val();
            var tasabscambiar = $('#tasabscambiar').val();
            var bstr  = parseFloat(bstr);
            var tasabscambiar = parseFloat(tasabscambiar);

            var fbs = bstr / tasabscambiar;
            $('#fbs').val(fbs);
            $("#form1").submit();
        }



        function calcpeso(obj) {
            $('#buttonprocesar').hide();
            var fpesos  = $('#fpesos').val();
            var tasapesocambiar = $('#tasapesocambiar').val();
            var fpesos  = parseFloat(fpesos);
            var tasapesocambiar = parseFloat(tasapesocambiar);

            var pesostr = fpesos * tasapesocambiar;
            $('#pesostr').val(pesostr);
            $("#form1").submit();
        }

        function calcpesosrev(obj) {
            $('#buttonprocesar').hide();
            var pesostr  = $('#pesostr').val();
            var tasapesocambiar = $('#tasapesocambiar').val();
            var pesostr  = parseFloat(pesostr);
            var tasapesocambiar = parseFloat(tasapesocambiar);

            var fpesos = pesostr / tasapesocambiar;
            $('#fpesos').val(fpesos);
            $("#form1").submit();
        }

        function contentloading(content) {
            $('#'+content).html('<button class="btn btn-outline-primary btn-load"><span class="d-flex align-items-center"><span class="spinner-border flex-shrink-0" role="status"> <span class="visually-hidden"> Cargando...</span> </span> <span class="flex-grow-1 ms-2">Cargando... </span> </span> </button>')
        }


        $( document ).ready(function() {
            $('.gotodown').bind("click", function () {
                $('html, body').animate({ scrollTop:   document.body.clientHeight  },"fast");
                return false;
            });
        });

        $('#buscarcuenta').change(function () {
            var v   = $(this).val();
            var iii = $('#iii').val();
            contentloading('searchcontentcuenta');

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url  : "{{route('buscarcuentaajax')}}",
                data : {buscarcuenta: v, iii: iii},
                type : 'post',
            }).done(function(resp) {
                $('#searchcontentcuenta').html(resp);
            });
        });

        $('#buscarcliente').change(function () {
            contentloading('searchcontentclie');
            var v = $(this).val();
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url  : "{{route('buscarclienteajax')}}",
                data : {buscarcliente: v, fkbanco: {{$fkbanco}}, cdcd : {{$cdcd}} },
                type : 'post',
            }).done(function(resp) {
                $('#searchcontentclie').html(resp);
            });
        });

        $('.checkfinancia').click(function () {
            $('#buttonprocesar').hide();
            const realizarfinancia = document.getElementById("realizarfinancia");
            var show = 0;

            if (realizarfinancia.checked ) {
                var show = 1;
            }
            if(show)
                $('#checkfinancia').show();
            else
                $('#checkfinancia').hide();

        });

        $('.checkprocesos').click(function () {
            $('#buttonprocesar').hide();
            const realizarpagare = document.getElementById("realizarpagare");
            const realizarletra  = document.getElementById("realizarletra");
            var show = 0;

            if (realizarletra.checked || realizarpagare.checked) {
                var show = 1;
            }
            if(show)
                $('#checkprocesos').show();
            else
                $('#checkprocesos').hide();

        });


        $('.checkvehiculomoto').click(function () {
            $('#buttonprocesar').hide();
            const vehiculomoto = document.getElementById("vehiculomoto");
            var show = 0;

            if (vehiculomoto.checked || vehiculomoto.checked) {
                var show = 1;
            }
            if(show)
                $('#checkvehiculomoto').show();
            else
                $('#checkvehiculomoto').hide();

        });

        $('.checkcasaterreno').click(function () {
            $('#buttonprocesar').hide();
            const casaterreno = document.getElementById("casaterreno");
            var show = 0;

            if (casaterreno.checked || casaterreno.checked) {
                var show = 1;
            }
            if(show)
                $('#checkcasaterreno').show();
            else
                $('#checkcasaterreno').hide();

        });

    </script>
@endsection
