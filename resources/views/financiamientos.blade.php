@extends('layouts.master')
@section('title')
    Reporte CxC
@endsection
@section('css')

@endsection
@section('content')
    <style>
        .tdline{
            border:1px solid #0072c5 !important;

        }
        .tdlineff{
            border-left:1px solid #fff !important;

            color: white !important;
            background-color: #0072c5 !important;
        }
    </style>
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">{{(isset($ttvienedato) and $ttvienedato > 0)? 'Monto a abonar o pagar $ '.number_format($ttvienedato,2,',','.'):'Financiamientos'}}</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <button style="height: 28px;  line-height: 8px;" class="btn btn-primary" id="botonactarriba"   onclick="$('#form2').submit()">
                                Actualizar
                            </button>
                        </li>
                        <li class="breadcrumb-item active"> {{(isset($ttvienedato) and $ttvienedato > 0) ?'$ '.number_format($ttvienedato,2,',','.'):'Financiamientos'}}</li>
                    </ol>
                </div>

            </div>
        </div>
    </div>
    <form id="form2" name="form2" method="post" action="{{route('financiamientos')}}" >
        @csrf
        @method('post')
    <div class="row">
        @if($ttvienedato == 0)
            <div class="col-xl-4 ">
                <div class="card overflow-hidden" >
                    <div class="accordion accordion-flush filter-accordion">
                        <div class="card-body border-bottom">
                            <div class="table-responsive table-card ">
                                <table width="100%" border="0"
                                       class="table table-borderless table-centered align-middle table-nowrap mb-0 ">
                                    <tr bgcolor="#fff">
                                        <td width="60%" height="30"align="left" class="tdline" >
                                            <input placeholder="Buscar Cliente" type="text" style=" height: 30px; width: 99%; border: none !important" value="{{(isset($buscarcli) and $buscarcli !='')? $buscarcli : ''}}" name="buscarcli" id="buscarcli">
                                        </td>
                                        <td width="20%" align="center" class="tdlineff" > Fecha</td>
                                        <td width="20%" align="center" class="tdlineff" > Status</td>
                                    </tr>
                                    @php $nn = 0; @endphp
                                    @foreach($financiamientos as $index => $financiamiento)

                                        <tr @php if(($nn%2)==0){echo 'bgcolor="#eee"'; }else{echo 'bgcolor="#fff"';} @endphp>
                                            <td  height="30"align="left" class="tdline" >
                                                <a href="/clientes/{{$financiamiento->codclie}}/tab5"  target="_blank" class="mb-0 listname" style=" font-size: 12px;  ">
                                                    {{$financiamiento->cliente->descrip}}
                                                </a>
                                            </td>
                                            <td align="center" class="tdline" style="font-size: 11px">   {{$financiamiento->fecha}}  </td>
                                            <td align="right"  class="tdline">
                                                <a href="{{route('financiamientos',['codclie' => $financiamiento->codclie, 'numerod' => $financiamiento->numerod])}}">
                                                    {{$financiamiento->status.'/'.$financiamiento->cantcuotas}}
                                                </a>
                                            </td>
                                        </tr>


                                    @endforeach
                                    <tr >
                                        <td height="30"align="left"></td>
                                        <td align="center"></td>
                                        <td align="center"></td>
                                    </tr>
                                    <tr >
                                        <td height="30"align="left" class="tdline">  </td>
                                        <td align="center" class="tdline" > </td>
                                        <td align="center" class="tdline" >  </td>
                                    </tr>


                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="col-xl-7">
            @if($codclie != '')


                    <input type="hidden" name="codclie" value="{{(isset($codclie))?$codclie : ''}}">
                    <input type="hidden" name="numerod" value="{{(isset($numerod))?$numerod : ''}}">

                    @method('post')
                    @csrf
                    <div class="card">
                        <div class="card-body"   style="height: 400px; overflow: auto" >
                            <div class="table-responsive table-card mb-1" >
                                <table width="100%" border="0" class=" table align-middle table-nowrap ">
                                    <tr bgcolor="#fff">
                                        <td width="1%" class="tdlineff" align="center"> </td>
                                        <td width="8%" class="tdlineff" align="center">Fecha</td>
                                        <td width="8%" class="tdlineff" align="center" >Descrip</td>
                                        <td width="8%" class="tdlineff" align="center" >Vence</td>
                                        <td width="8%" class="tdlineff" align="center" >Monto </td>
                                        <td width="8%" class="tdlineff" align="center" >Abonado</td>
                                        <td width="8%" class="tdlineff" align="center" >Abonar</td>
                                        <td width="8%" class="tdlineff" align="center" >Saldo deuda</td>
                                    </tr>
                                    @php
                                        $tantos     = 0;
                                        $tvienedato = 0;
                                        $tasamayor  = 0;
                                    @endphp

                                    @foreach($deudascli as $deudas)

                                        @php
                                            $tantos++;
                                            $vienedato  = (isset($nrounicocxc[$deudas->id]))? $nrounicocxc[$deudas->id] : 0;
                                            $tvienedato += $vienedato;

                                            $tasaitem = (isset($deudas->tasadolar))? $deudas->tasadolar: 0;

                                            if($vienedato){
                                               if($tasaitem > $tasamayor ){
                                                   $tasamayor = $tasaitem;
                                               }
                                            }

                                            $saldo = $deudas->montodolares-$deudas->abonado;
                                            $vectorcxc = session('vectorcxc');

                                            $pasa = 1;
                                            if($ttvienedato){
                                                $pasa = 0;
                                                if(isset($vectorcxc[$deudas->id]) and $vectorcxc[$deudas->id] == 1){
                                                   $pasa =1 ;
                                                }
                                            }


                                        @endphp
                                        @if($pasa == 1)
                                            <tr>
                                                <td align="center" >
                                                    <input name="numerodcxc[{{$deudas->id}}]" type="hidden" value="{{$deudas->numerod}}">
                                                    <input name="tipocxccxc[{{$deudas->id}}]" type="hidden" value="{{$deudas->tipocxc}}">
                                                    <input name="tasafaccxc[{{$deudas->id}}]" type="hidden" value="{{$deudas->tasadolar}} ">
                                                    <input name="vectorcxc" type="checkbox" value="{{$deudas->id}}"
                                                           class="checkcxc checkcxc{{$deudas->id}}"  data-nrounico="{{$deudas->id}}"
                                                           @if(isset($vectorcxc) and isset($vectorcxc[$deudas->id]) and$vectorcxc[$deudas->id] == 1) checked @endif >
                                                </td>
                                                <td align="center" style="font-size: 10px">{{$deudas->fecha}}                                 </td>
                                                <td align="left"   style="font-size: 10px" >{{$deudas->Document}}      </td>
                                                <td height="34"    style="font-size: 10px" align="center"> {{$deudas->fechav}}                   </td>
                                                <td align="center">{{number_format($deudas->montodolares,2,',','.')}} </td>
                                                <td align="center">{{number_format($deudas->abonado     ,2,',','.')}} </td>
                                                <td align="center">
                                                    <input type="text"  value="{{$vienedato}}"
                                                           name             = "nrounicocxc[{{$deudas->id}}]"
                                                           class            = "nextfield tantos abonar{{$tantos}}"
                                                           data-name        = "abonar"
                                                           data-nrounico    = "{{$deudas->id}}"
                                                           data-credendolar = "{{number_format($deudas->montodolares,2,'.','')}}"
                                                           data-abonado     = "{{number_format($deudas->abonado,2,'.','')}}"
                                                           data-maximo      = "{{number_format($saldo,2,'.','')}}"
                                                           data-item        = "{{$tantos}}"
                                                           min = "0"
                                                           max = "{{number_format($deudas->montodolares,2,'.','')}}"
                                                           onClick=" $('.checkcxc{{$deudas->id}}').click();
                                                             $(this).val({{number_format($saldo,2,'.','')}});
                                                             $(this).change();
                                                             $('#botonactarriba').hide();
                                                             $(this).select();
                                                             $('.ocultarboton').hide()"
                                                           size        = "1"
                                                           style       = "width: 88%; text-align: right"
                                                           onFocus     = "$(this).select()"
                                                           placeholder = "0.00"  />
                                                </td>
                                                <td align="center">{{number_format($deudas->saldofinancia,2,',','.')}}  </td>

                                            </tr>
                                        @endif
                                    @endforeach
                                    <tr>
                                        <td align="left"  > </td>
                                        <td align="left"  > </td>
                                        <td align="left"  > </td>
                                        <td align="left"  > </td>
                                        <td align="left"  > </td>
                                        <td align="left"  > </td>
                                        <td align="left"  >
                                            <button  class="btn-primary btn" type="button"  onclick="$('#form2').submit()"  >
                                                Actualizar
                                            </button>
                                        <td>
                                        <td align="left"  > </td>

                                    </tr>
                                </table>
                                <input type="hidden" name="ttvienedato" value="{{$tvienedato}}" />
                            </div>
                        </div>
                    </div>

            @endif
        </div>
        @if($ttvienedato > 0)
                <div class="col-xl-5 ">
                    <div class="card overflow-hidden" >
                        <div class="accordion accordion-flush filter-accordion">
                            <div class="card-body border-bottom">
                                <div class="table-responsive table-card ">
                                    @php
                                        $dolar_tranf = $cancelt = $tasamayoralert = $peso_tranf = $totalabono = $nopuedefac = $abonosseparados = $totalabonoaux = 0 ;
                                        $needbanco   = 1;
                                        $error       = '';
                                        if($cancele > 0 or  $dolares >0 or $pesos >0   )
                                            $procesarbanco = 1;

                                        if( $tasamayor > $tasaabono)    $tasamayoralert = 1;



                                    @endphp
                                    <table width="100%" border="0" class="mt-2">

                                        <tr>
                                            <td width="29%" height="30" class="titulo"> TASA BS
                                                @if(!$tasaabono)
                                                    <br>
                                                    <span style="font-size:10px; color:red">(Obligatorio)</span>
                                                @endif
                                            </td>
                                            <td width="35%">

                                                <input name="tasa_abono" type="text" id="tasaanticipo" size="1"
                                                       onClick="$(this).select(); $('#botonprocesar').hide()" onFocus="$(this).select()"
                                                       onChange="$('#form2').submit()"
                                                       style="text-align:right; width:120px; text-align:right"
                                                       value="{{$tasaabono}}"
                                                       onBlur="$('#form2').submit()"
                                                       placeholder="Tasa requerida" />

                                            </td>
                                            <td width="4%" align="right">  </td>
                                            <td colspan="2" class="" align="right" style="font-size:10px ">
                                                Tasa COP
                                                <input type="hidden" name="ttvienedato" value="{{$tvienedato}}" />
                                                <input type="hidden" name="tab" id="valtab" value="{{$tab}}" />
                                                <input name="pesoxdolar" type="number" id="pesoxdolar" size="1"
                                                       style="width:50px; text-align: center"  value="{{$pesoxdolar}}" placeholder="Tasa COP"/></td>
                                        </tr>

                                            <tr>
                                                <td height="33" class="titulo"align="left">BANCO/CAJA</td>
                                                <td align="left" class="titulo" colspan="2">
                                                    <select name="fk_banco">
                                                        <option value=""  {{($fk_banco == 0)? 'selected' : ''}}> ---- </option>

                                                        @foreach($bank as $bankval){
                                                            <option value="{{$bankval->id}}" {{($fk_banco == $bankval->id)? 'selected' : ''}} >
                                                                {{$bankval->descrip}}
                                                            </option>
                                                        @endforeach

                                                    </select>
                                                </td>

                                                <td colspan="2">

                                                    <div style=" display:flex; justify-content: end; align-items: center;">
                                                        <div  style="font-size:10px " >Proc. Banco/Caja</div>
                                                        <div style="margin-left:5px;">
                                                            <input name="procesarbanco" type="checkbox" value="1"  {{($procesarbanco == 1)? 'checked' : '' }}>
                                                        </div>
                                                    </div>

                                                </td>
                                            </tr>
                                            <tr>
                                                <td height="33" class="titulo"align="left">FECHA BANCO</td>
                                                <td align="left" class="titulo" colspan="2">

                                                    <input name="fechabanco" type="date" id="fechabanco" size="1"
                                                           onClick="$(this).select(); $('#botonprocesar').hide()"
                                                           onFocus="$(this).select()"
                                                           onChange="$('#form2').submit()"
                                                           style="text-align:right; width:120px; text-align:left" value="{{$fechabanco}}"
                                                           placeholder="Fecha Operacion" />                          </td>
                                                <td colspan="2">&nbsp;
                                                </td>
                                            </tr>

                                        <tr>
                                            <td colspan="5">
                                                <div class="card">
                                                    <div class="card-body">
                                                        @if($tasaabono > 0)
                                                            <nav>
                                                                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                                                                    <button class="nav-link {{($tab=='tab9')? 'active' : ''}}"  id="idtab9" data-bs-toggle="tab" data-tab="tab9" data-bs-target="#tab9" style="width: 150px; flex-grow: unset"
                                                                            type="button" role="tab" aria-controls="tab9" aria-selected="true">EFECTIVO</button>

                                                                    <button class="nav-link {{($tab=='tab11')? 'active' : ''}}" id="idtab11" data-bs-toggle="tab" data-tab="tab11" data-bs-target="#tab11" style="width: 220px; flex-grow: unset"
                                                                            type="button" role="tab" aria-controls="tab11" aria-selected="false"> INSTRUMENTOS DE PAGO</button>
                                                                </div>
                                                            </nav>
                                                            <div class="tab-content" id="nav-tabContent" style="width:100%; height:200px; overflow:auto">

                                                                <div class="tab-pane fade {{($tab=='tab9')? ' active show': ''}}" id="tab9"
                                                                     role="tabpanel" aria-labelledby="idtab9" tabindex="0">

                                                                    <table border="0" width="100%"  class="mt-2" onClick="  $('#botonprocesar').hide()">
                                                                        <tr>
                                                                            <td width="14%" align="left" class="titulo">Bs.</td>
                                                                            <td align="left" width="38%">
                                                                                <input name="cancele" type="text" id="cancelefectivo" size="1"
                                                                                       onClick="$(this).select()" onFocus="$(this).select()"
                                                                                       class="anticipocalc inputdata"
                                                                                       style="text-align:right; width:90%;
                                                                           text-align:right" value="{{$cancele}}" />

                                                                            </td>
                                                                            <td width="30%" align="right">

                                                                                @php
                                                                                    if($cancele > 0){
                                                                                        echo ' = $'.number_format($cancele/$tasaabono,2,',','.') ;
                                                                                        $totalabono += number_format($cancele/$tasaabono,2,'.','');
                                                                                    }
                                                                                @endphp
                                                                            </td>
                                                                            <td width="12%">&nbsp;</td>
                                                                            <td width="6%">&nbsp;</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td align="left" class="titulo">USD</td>
                                                                            <td align="left"  >
                                                                                <input name="dolares" type="text" id="cancelefecdolar" size="1" onClick="$(this).select()"
                                                                                       onFocus="$(this).select()"
                                                                                       class="anticipocalc inputdata"
                                                                                       style="text-align:right; width:90%; text-align:right" value="{{$dolares}}" />
                                                                            </td>
                                                                            <td align="right">
                                                                                @php
                                                                                    if(isset($dolares) and $dolares > 0){
                                                                                        echo ' = $'.number_format($dolares,2,',','.') ;
                                                                                        $totalabono += number_format($dolares, 2,'.','');
                                                                                    }
                                                                                @endphp
                                                                            </td>
                                                                            <td>&nbsp;</td>
                                                                            <td>&nbsp;</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td class="titulo"> COP</td>
                                                                            <td align="left"  >
                                                                                <input name="pesos" type="text" id="cancelefecpeso" size="1" onClick="$(this).select()"
                                                                                       onFocus="$(this).select()"
                                                                                       class="anticipocalc inputdata"
                                                                                       style="text-align:right; width:90%;
                                                                           text-align:right" value="{{$pesos}}" />
                                                                            </td>
                                                                            <td align="right"> / @php echo number_format($pesoxdolar, 2,',','.');

                                                                                         if($pesos>0 and $pesoxdolar > 0){
                                                                                             echo '	  = $'.number_format($pesos / $pesoxdolar, 2,',','.');
                                                                                             $totalabono += number_format(($pesos / $pesoxdolar),2,'.','');
                                                                                         }
                                                                                @endphp
                                                                            </td>
                                                                            <td>&nbsp;</td>
                                                                            <td>&nbsp;</td>
                                                                        </tr>

                                                                    </table>

                                                                </div>
                                                                @php $ultdol = 0;@endphp
                                                                <div class="tab-pane fade {{($tab=='tab11')? ' active show': ''}}" id="tab11"
                                                                     role="tabpanel" aria-labelledby="idtab11" tabindex="0">

                                                                    <table width="100%"   border="0"  onClick="  $('#botonprocesar').hide()">
                                                                        <tr>
                                                                            <td  align="left" class="tdline" colspan="2">

                                                                                <div style=" width:100%;  padding:10px 0 10px 0;">

                                                                                    <table width="100%" border="0"  >
                                                                                        <tr>
                                                                                            <td height="28" colspan="4"  align="center">
                                                                                                <a href="javascript:;"
                                                                                                   data-bs-toggle="modal" data-bs-target="#openInstPago"
                                                                                                   data-url="{{route('verInstPago',['bs'=> 1, 'ultdol'=> $ultdol ])}}"
                                                                                                   class="verInstPago"
                                                                                                   style=" text-align:center">
                                                                                                    VER INSTRUMENTOS DE PAGO EN BS
                                                                                                </a>
                                                                                            </td>

                                                                                        </tr>
                                                                                            @php

                                                                                            $sqltarj="  SELECT  count(*) tantos
                                                                                                        FROM satarj
                                                                                                        WHERE activo=1 and bs=1 and web = 1
                                                                                                              ";

                                                                                            $listtj = \Illuminate\Support\Facades\DB::select($sqltarj);

                                                                                            $tantos = $listtj[0]->tantos;
                                                                                            $isntbs = session('isntbs');

                                                                                            if(isset($isntbs)){
                                                                                                foreach( $isntbs as $index =>$value){
                                                                                                    $isntbs[$index] = 0;
                                                                                                }
                                                                                                session(['isntbs'=>$isntbs]);
                                                                                            }

                                                                                        for($l=0;$l < $tantos+1; $l++){

                                                                                            if(isset($montotar[$l]) and $montotar[$l] >0 and $tasaabono >0){

                                                                                                $totalabono += number_format($montotar[$l]/$tasaabono , 2,'.','');
                                                                                                $cancelt    += number_format($montotar[$l] , 2,'.','');
                                                                                            }

                                                                                            if(isset($codtar[$l]) and $codtar[$l]!=''){
                                                                                                $isntbs = session('isntbs');
                                                                                                $isntbs[$codtar[$l]] =  $montotar[$l];
                                                                                                session(['isntbs' => $vectorcxc]);
                                                                                            }

                                                                                        if((isset($montotar[$l]) and isset( $codtar[$l]) and  $codtar[$l] and $montotar[$l] >0 )   ){
                                                                                           @endphp

                                                                                        <tr>
                                                                                            <td width="4%"  align="center">

                                                                                                    @php

                                                                                                        $vercod= (isset($codtar[$l]))? $codtar[$l]: '';

                                                                                                        $clasetar = 0;
                                                                                                        if(!isset($montotar[$l])){
                                                                                                            $vercod       = '';
                                                                                                            $codtar[$l]   = '';
                                                                                                            $montotar[$l] = '';
                                                                                                        }

                                                                                                        if(($vercod!='' and strlen($vercod)>0 )){

                                                                                                             $sqltarj="  SELECT CodTarj, descrip
                                                                                                                FROM satarj
                                                                                                                WHERE CodTarj='$vercod' and activo=1 and bs=1 and web = 1
                                                                                                              ";

                                                                                                            $listatar = \Illuminate\Support\Facades\DB::select($sqltarj);

                                                                                                            if(isset($listatar[0]->descrip) and $listatar[0]->descrip!=''){

                                                                                                                if($montotar[$l]>0){
                                                                                                                    echo "<span class='verdealert'>(&radic;)</span>";
                                                                                                                }else{
                                                                                                                    echo "<span class='rojoalert'>(X)</span>"; $nopuedefac=1;  $error.='- REVISAR INSTRUMENTOS DE PAGO';
                                                                                                                }

                                                                                                            }else{
                                                                                                                echo "<span class='rojoalert'>(X)</span>"; $nopuedefac=1;   $error.='- REVISAR INSTRUMENTOS DE PAGO';
                                                                                                            }
                                                                                                        }else{
                                                                                                            if(isset($montotar[$l]) and $montotar[$l] > 0){
                                                                                                                echo "<span class='rojoalert'>(X)</span>"; $nopuedefac=1;  $error.='- REVISAR INSTRUMENTOS DE PAGO';
                                                                                                            }
                                                                                                        }
                                                                                                  @endphp
                                                                                            </td>

                                                                                            <td width="27%"  align="center">
                                                                                                <input name="transftar[]"  type="hidden" value="{{(isset($transftar[$l]))? $transftar[$l] : ''}}">

                                                                                                @php if(isset($transftar[$l]) and $transftar[$l] >0){  echo $codtar[$l]; @endphp
                                                                                                    <input name="codtar[]"  type="hidden" value="{{$codtar[$l]}}">
                                                                                                @php }else{ @endphp
                                                                                                    <input type="text" placeholder="Cod pago BS" class="inputdata" name="codtar[]" size="2"
                                                                                                           id="codtar[]"style="width:90%" value="{{(isset($codtar[$l])? $codtar[$l]  : 0)}}"   />
                                                                                                @php } @endphp

                                                                                            </td>

                                                                                            <td width="47%"  align="center">
                                                                                                @php
                                                                                                    if($clasetar == 1 and (!isset($desctar[$l]) or !$desctar[$l] or $desctar[$l] == '' or strlen($desctar[$l]) < 1)){
                                                                                                        $nopuedefac=1; $error.='- REFERENCIA REQUERIDA EN INST PAGO   BOLIVARES';
                                                                                                    }

                                                                                                     if(isset($transftar[$l]) and $transftar[$l] >0){  echo $desctar[$l];
                                                                                                @endphp
                                                                                                        <input name="desctar[]" type="hidden" value="{{$desctar[$l]}}">
                                                                                                @php }else{  @endphp

                                                                                                     <input  type="text" placeholder="Referencia de pago" name="desctar[]" id="desctar[]" class="inputdata anticipocalc"
                                                                                                        data-index="{{$l}}" size="2"style="text-align:right; width:80%;"
                                                                                                        value="{{(isset($desctar[$l]))? $desctar[$l]: ''}}" />
                                                                                                @php } @endphp

                                                                                            </td>

                                                                                            <td width="22%" align="center">

                                                                                                @php if(isset($transftar[$l]) and $transftar[$l] >0){  echo  number_format($montotar[$l],2,',','.');   @endphp
                                                                                                    <input name="montotar[]" type="hidden" value="{{$montotar[$l]}}">
                                                                                                @php }else{ @endphp

                                                                                                <input  type="text"  name="montotar[]" id="montotar[]" class="inputdata"  size="2"style="text-align:right; width:80%;"
                                                                                                        data-index="{{$l}}"   value="{{(isset($montotar[$l]))? $montotar[$l] : ''}}" />
                                                                                                @php } @endphp

                                                                                            </td>
                                                                                        </tr>

                                                                                        @php } } @endphp

                                                                                        <tr>
                                                                                            <td width="4%"  align="center"> <input name="transftar[]"  type="hidden" value="">
                                                                                            </td>

                                                                                            <td width="27%"  align="center"> <input placeholder="Cod pago BS" type="text"class="inputdata"  name="codtar[]" size="2" id="codtar{{$l}}"style="width:90%" value="" /></td>

                                                                                            <td width="47%"  align="center">

                                                                                                <input type="text" name="desctar[]" id="desctar{{$l}}"  class="inputdata"  placeholder="Referencia de pago" size="2" style="width:80%" value=""  /></td>

                                                                                            <td width="22%" align="center">
                                                                                                <input  type="text"  name="montotar[]" id="montotar{{$l}}" class="inputdata  instpagobs anticipocalc" placeholder="Monto BS"  size="2"style="text-align:right; width:80%;"   value="" /></td>
                                                                                        </tr>
                                                                                    </table>
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td align="left"colspan="4" class=" tdline   ">

                                                                                <div style="  width:100%;   text-align:center; padding:10px 0 10px 0; display:flex; flex-direction:column-reverse">
                                                                                    @php $ultdol = 0;@endphp
                                                                                    <table width="100%" border="0"  >
                                                                                        <tr>
                                                                                            @php
                                                                                                for($l=0;$l<5;$l++){
                                                                                            @endphp

                                                                                        <tr>
                                                                                            <td width="4%"  align="center">

                                                                                                @php
                                                                                                    $clasetar = 0;
                                                                                                    $vercod = (isset($codtardol[$l]))? $codtardol[$l] : '';

                                                                                                    if($vercod != '' and strlen($vercod) > 0){

                                                                                                         $sqltarj="  SELECT CodTarj, descrip
                                                                                                                    FROM satarj
                                                                                                                    WHERE CodTarj='$vercod' and activo=1 and dolares=1 and web = 1
                                                                                                                  ";

                                                                                                        $listatar = \Illuminate\Support\Facades\DB::select($sqltarj);

                                                                                                        if(isset($listatar[0]->descrip) and $listatar[0]->descrip!=''){

                                                                                                            if(isset($montotardol[$l]) and  $montotardol[$l]>0){
                                                                                                                    if($montotardol[$l] >0){
                                                                                                                        $totalabono  += number_format($montotardol[$l] , 2,'.','');
                                                                                                                        $dolar_tranf += number_format($montotardol[$l], 2,'.','');
                                                                                                                    }

                                                                                                                echo "<span class='verdealert'>(&radic;)</span>";
                                                                                                                $ultdol++;
                                                                                                            }else{
                                                                                                                echo "<span class='rojoalert'>(X)</span>"; $nopuedefac=1;  $error.='- REVISAR INSTRUMENTOS DE PAGO';
                                                                                                            }
                                                                                                        }else{
                                                                                                            echo "<span class='rojoalert'>(X)</span>"; $nopuedefac=1;    $error.='- REVISAR INSTRUMENTOS DE PAGO';
                                                                                                        }
                                                                                                    }else{
                                                                                                        if(isset($montotardol[$l]) and $montotardol[$l] > 0){
                                                                                                            echo "<span class='rojoalert'>(X)</span>"; $nopuedefac=1; $error.='- REVISAR INSTRUMENTOS DE PAGO';
                                                                                                        }
                                                                                                    }

                                                                                                @endphp </td>

                                                                                            <td width="27%"  align="center">
                                                                                                <input name="transfdol[]" type="hidden" value="{{(isset($transfdol[$l]) and $transfdol[$l] > 0)? $transfdol[$l]: ''}}">

                                                                                                @if(isset($transfdol[$l]) and $transfdol[$l] >0 and isset($codtardol[$l])) {{$transfdol[$l]}}
                                                                                                    <input name="codtardol[]" type="hidden" value="{{$codtardol[$l]}}"   id="codtardol{{$l}}" >
                                                                                                @else
                                                                                                    <input type="text" class="inputdata" placeholder="Cod pago USD" name="codtardol[]" size="2"  style="width:90%"   id="codtardol" value="{{isset($codtardol[$l])? $codtardol[$l] : ''}}"  />
                                                                                                @endif
                                                                                            </td>

                                                                                            <td width="47%"  align="center">
                                                                                                    @php
                                                                                                    if($clasetar == 1 and (!isset($desctardol[$l]) or !$desctardol[$l] or $desctardol[$l] == '' or strlen($desctardol[$l]) < 1)){
                                                                                                        $nopuedefac=1; $error.='- REFERENCIA REQUERIDA EN INST PAGO   DOLARES';
                                                                                                    }

                                                                                                    if(isset($transfdol[$l]) and $transfdol[$l] >0 and isset($desctardol[$l])){  echo $desctardol[$l];
                                                                                                @endphp
                                                                                                        <input name="desctardol[]" type="hidden" value="{{(isset($desctardol[$l]))? $desctardol[$l] : ''}}"   id="desctardol{{$l}}">
                                                                                                @php }else{ @endphp
                                                                                                       <input  type="text"  name="desctardol[]"  class="inputdata anticipocalc" placeholder="Referencia de pago"
                                                                                                               size="2"style="text-align:right; width:80%;" data-index="{{$l}}"  id="desctardol{{$l}}"
                                                                                                               value="{{(isset($desctardol[$l])? $desctardol[$l] :'')}}" />
                                                                                                @php }  @endphp
                                                                                            </td>

                                                                                            <td width="22%" align="center">
                                                                                                @php if(isset($transfdol[$l]) and $transfdol[$l] >0){ echo number_format($montotardol[$l],2,',','.'); @endphp
                                                                                                        <input name="montotardol[]" type="hidden" value="{{$montotardol[$l]}}"  id="montotardol{{$l}}">
                                                                                                @php }else{ @endphp
                                                                                                        <input  type="text"  name="montotardol[]" id="montotardol{{$l}}" class="inputdata anticipocalc instpagodolar"
                                                                                                                placeholder="Monto USD" size="2"style="text-align:right; width:80%;"  data-index="{{$l}}"  value="{{isset($montotardol[$l])? $montotardol[$l] : ''}}" />
                                                                                                @php } @endphp
                                                                                            </td>
                                                                                        </tr>

                                                                                          @php
                                                                                              }
                                                                                          @endphp
                                                                                    </table>

                                                                                    <a href="javascript"
                                                                                       data-bs-toggle="modal" data-bs-target="#openInstPago"
                                                                                       data-url="{{route('verInstPago',['dolares'=> 1, 'ultdol'=> $ultdol ])}}"
                                                                                       class="verInstPago"
                                                                                       style=" text-align:center">
                                                                                        VER INSTRUMENTOS DE PAGO EN USD
                                                                                    </a>
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td align="left"colspan="4" class=" tdline   ">
                                                                                @php $ultdol = 0; @endphp
                                                                                <div style="  width:100%;   text-align:center; padding:10px 0 10px 0; display:flex; flex-direction:column-reverse">
                                                                                    <table width="100%" border="0"  >
                                                                                            @php
                                                                                                $peso_tranf = 0;
                                                                                                for($l=0;$l<5;$l++){
                                                                                            @endphp

                                                                                        <tr>
                                                                                            <td width="4%"  align="center">

                                                                                                    @php
                                                                                                    $vercod = (isset($codtarpes[$l]))?$codtarpes[$l] : '';
                                                                                                    $clasetar = 0;

                                                                                                    if($vercod!='' and strlen($vercod)>0){


                                                                                                        $sqltarj="  SELECT CodTarj, descrip
                                                                                                            FROM satarj
                                                                                                            WHERE CodTarj = '$vercod' and activo=1 and pesos=1 and web = 1
                                                                                                          ";

                                                                                                        $listatar = \Illuminate\Support\Facades\DB::select($sqltarj);

                                                                                                         if(isset($listatar[0]->descrip) and $listatar[0]->descrip!=''){

                                                                                                            if(isset($montotarpes[$l]) and $montotarpes[$l] > 0){

                                                                                                                if($pesoxdolar>0)
                                                                                                                    $totalabono += number_format($montotarpes[$l]/$pesoxdolar, 2,'.','');

                                                                                                                $peso_tranf += number_format($montotarpes[$l],2,'.','');


                                                                                                                echo "<span class='verdealert'>(&radic;)</span>";
                                                                                                                $ultdol++;
                                                                                                            }else{
                                                                                                                echo "<span class='rojoalert'>(X)</span>"; $nopuedefac=1;  $error.='- REVISAR INSTRUMENTOS DE PAGO';
                                                                                                            }
                                                                                                        }else{
                                                                                                            echo "<span class='rojoalert'>(X)</span>"; $nopuedefac=1;    $error.='- REVISAR INSTRUMENTOS DE PAGO';
                                                                                                        }
                                                                                                    }else{
                                                                                                        if(isset($montotarpes[$l]) and $montotarpes[$l] > 0){
                                                                                                            echo "<span class='rojoalert'>(X)</span>"; $nopuedefac=1; $error.='- REVISAR INSTRUMENTOS DE PAGO';
                                                                                                        }
                                                                                                    }

                                                                                                  @endphp   </td>

                                                                                            <td width="27%"  align="center">
                                                                                                <input name="transfpes[]" type="hidden" value="{{(isset($transfpes[$l]))? $transfpes[$l]  :''}}">

                                                                                                @php if(isset($transfpes[$l]) and $transfpes[$l] >0){  echo $codtarpes[$l]; @endphp
                                                                                                        <input name="codtarpes[]" type="hidden" value="{{$codtarpes[$l]}}" id="codtarpes{{$l}}">
                                                                                                @php }else{ @endphp
                                                                                                            <input type="text" placeholder="Cod pago COP" class="inputdata"   name="codtarpes[]" size="2" id="codtarpes{{$l}}"style="width:90%" value="{{(isset($codtarpes[$l]))? $codtarpes[$l]: ''}}" />
                                                                                                @php } @endphp

                                                                                            </td>

                                                                                            <td width="47%"  align="center">
                                                                                                @php if(isset($transfpes[$l])){  echo $transfpes[$l]; @endphp
                                                                                                <input name="desctarpes[]" type="hidden" value="{{$desctarpes[$l]}}"  id="desctarpes{{$l}}" >
                                                                                                @php }else{ @endphp
                                                                                                <input type="text"class="inputdata anticipocalc"  placeholder="Referencia de pago" data-index="{{$l}}" name="desctarpes[]" id="desctarpes{{$l}}"   size="2" style="width:80%"
                                                                                                       value="{{(isset($desctarpes[$l]))? $desctarpes[$l] : ''}}"  />
                                                                                                @php  } @endphp
                                                                                            </td>

                                                                                            <td width="22%" align="center">

                                                                                                   @php

                                                                                                    if($clasetar == 1 and (!isset($desctarpes[$l]) or !$desctarpes[$l] or $desctarpes[$l] == '' or strlen($desctarpes[$l]) < 1)){
                                                                                                        $nopuedefac=1; $error.='- REFERENCIA REQUERIDA EN INST PAGO   PESOS';
                                                                                                    }

                                                                                                if(isset($transfpes[$l]) and $transfpes[$l] >0 and isset($montotarpes[$l])){
                                                                                                    echo number_format($montotarpes[$l],2,',','.'); @endphp
                                                                                                    <input name="montotarpes[]" type="hidden" value="{{$montotarpes[$l]}}" id="montotarpes{{$l}}">
                                                                                                @php }else{ @endphp
                                                                                                    <input  type="text" data-index="{{$l}}"   name="montotarpes[]" placeholder="Monto COP"  id="montotarpes{{$l}}"
                                                                                                        class="inputdata instpagopeso anticipocalc"  size="2"style="text-align:right; width:80%;"   value="{{(isset($montotarpes[$l]))? $montotarpes[$l] : ''}}" />
                                                                                                @php } @endphp

                                                                                            </td>
                                                                                        </tr>

                                                                                       @php }  @endphp
                                                                                    </table>

                                                                                    <a href="javascript:;"

                                                                                       data-bs-toggle="modal" data-bs-target="#openInstPago"
                                                                                       data-url="{{route('verInstPago',['pesos'=> 1, 'ultdol'=> $ultdol ])}}"
                                                                                       class="verInstPago"
                                                                                        style=" text-align:center" >
                                                                                        VER INSTRUMENTOS DE PAGO EN COP
                                                                                    </a>
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                    </table>

                                                                </div>

                                                            </div>

                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>

                                        @if($tasaabono > 0)
                                            <tr>
                                                <td>&nbsp;</td>
                                                <td align="right"  >Total ($)
                                                    <input type="hidden" name="tab" id="tab"  value="{{ $tab }}" />
                                                    <input type="hidden" name="peso_tranf"  value="{{ $peso_tranf }}" />
                                                    <input type="hidden" name="dolar_tranf" value="{{ $dolar_tranf }}" />


                                                </td>
                                                <td align="right" class="tdlinetop" id="buttonanticipo">{{number_format($totalabono,2,',','.');}}</td>
                                                <td width="19%">&nbsp;</td>
                                                <td width="13%">&nbsp;</td>
                                            </tr>
                                            <tr>
                                                <td class="titulo" valign="top"> DETALLE PAGO
                                                    @if($observacion == '')
                                                        <br><span style="font-size:10px; color:red">(Obligatorio)</span>
                                                    @endif
                                                </td>
                                                <td align="left"   colspan="4">

                                                    <input name="observacion" type="text" id="observacion" size="1"
                                                           onKeyDown="return(tabular(event,this))" class="  inputdata"
                                                           style="text-align:right; width:90%; text-align:left" value="{{$observacion}}" />

                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="titulo" valign="top"> Notas1</td>
                                                <td align="left"   colspan="4">
                                                    <input name="notas1" type="text" id="notas1" size="1"
                                                           onKeyDown="return(tabular(event,this))" class="  inputdata"
                                                           style="text-align:right; width:90%; text-align:left" value="{{$notas1}}" />
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="titulo" valign="top"> Notas2</td>
                                                <td align="left"   colspan="4">
                                                    <input name="notas2" type="text" id="notas2" size="1"
                                                           onKeyDown="return(tabular(event,this))" class="  inputdata"
                                                           style="text-align:right; width:90%; text-align:left" value="{{$notas2}}" />

                                                </td>
                                            </tr>

                                            <tr>
                                                <td align="center"   colspan="5">

                                                    <table width="80%" border="0">
                                                        <tr>
                                                            <td width="26%" align="center">

                                                            </td>
                                                            <td width="74%" align="center">
                                                                @php
                                                                    $bankvalidation = 1;
                                                                    if(  (!$fechabanco and !$fk_banco) and ($cancele > 0 or  $dolares > 0 or  $pesos  > 0 or  $cancelt > 0 or  $dolar_tranf > 0 or $peso_tranf > 0 )){
                                                                        $bankvalidation = 0;
                                                                    }
                                                                    if( isset($needbanco) and  !$fk_banco and $needbanco == 1 ){
                                                                        $bankvalidation = 0;
                                                                    }
                                                                    $totalabonoaux =   $totalabono;

                                                                    $validarafechabanco = 1;
                                                                        if(!$fechabanco)
                                                                            $validarafechabanco = 0;


                                                                        list($y,$m,$d)=explode("-",$fechabanco);

                                                                        if( $m > 0 and $d > 0 and $y>0 ){
                                                                            if(preg_match("/\d{4}-\d{2}-\d{2}/",$fechabanco) and checkdate($m,$d,$y)){

                                                                            }else{
                                                                                $validarafechabanco = 0;
                                                                            }
                                                                        }

                                                                         if(($cancele > 0 or  $dolares >0 or $pesos >0) and ($peso_tranf > 0 or $dolar_tranf >0 or $cancelt > 0 )){
                                                                            $abonosseparados = 1;
                                                                        }

                                                                @endphp
                                                                <br>
                                                                <button type="button" id="botonprocesar"
                                                                        @php
                                                                             if(number_format(($totalabono),2,'.','') < 0.01 or  number_format(($tvienedato - $totalabono),2,'.','') < 0 or  abs($totalabonoaux - $tvienedato)>0.01 or $bankvalidation == 0 or $abonosseparados == 1){   @endphp
                                                                                disabled="disabled"
                                                                        @php } @endphp
                                                                        class="btn btn-primary ocultarboton"
                                                                        onclick="$('#form2').attr('action','{{route('financiamientos',['procesar'=>1])}}'); $('#form2').submit()"
                                                                        style=" @php if(!$tvienedato  or  number_format(($tvienedato - $totalabono),2,'.','') < 0 or !$observacion){   $totalabono = 0; @endphp
                                                                                cursor:not-allowed;
                                                                                @php }@endphp
                                                                        width:320px; margin:auto"
                                                                >
                                                                        @php


                                                                        if( number_format(($tvienedato - $totalabono),2,'.','') == 0
                                                                            and $totalabono > 0
                                                                            and $validarafechabanco == 1
                                                                            and $observacion !=''
                                                                            and $bankvalidation == 1
                                                                            and $tasaabono > ($tasabs * 0.9)
                                                                            and !$tasamayoralert
                                                                            and $tvienedato > 0
                                                                            and $tasaabono
                                                                            and !$abonosseparados
                                                                        ){
                                                                            echo  ' Abonar/Pagar $'.number_format($totalabono,2,',','.');
                                                                        }else{
                                                                            if($nopuedefac){
                                                                                echo "$error";
                                                                            }else{
                                                                                if($abonosseparados){
                                                                                    echo 'Por favor realizar abonos separados';
                                                                                }else{
                                                                                    if(abs($totalabonoaux - $tvienedato)>0.01){
                                                                                        echo 'ERROR:  $'.number_format($totalabonoaux,2,',','.').' <>  $'.number_format($tvienedato,2,',','.');
                                                                                    }else{
                                                                                        if(!$bankvalidation){
                                                                                            echo 'Caja/Banco requerido';
                                                                                        }else{
                                                                                            if( $tasaabono <= ($tasabs * 0.90) ){
                                                                                                echo "$tasaabono <= ($tasabs * 0.9)".'????.';
                                                                                            }else{
                                                                                                if($tasamayoralert > 0){
                                                                                                    if($tasamayoralert == 1111)
                                                                                                        echo "No hay una tasa registrada para el dia de hoy";
                                                                                                    else
                                                                                                        echo "Existe Factura con tasa mayor";
                                                                                                }
                                                                                                else{
                                                                                                    if(!$tasaabono){
                                                                                                        echo "Tasa de cambio requerida";
                                                                                                    }
                                                                                                    else{
                                                                                                        echo '????';
                                                                                                    }
                                                                                                }
                                                                                            }
                                                                                        }
                                                                                    }
                                                                                }
                                                                            }
                                                                        }
                                                                        @endphp
                                                                </button>

                                                            </td>
                                                        </tr>
                                                    </table>
                                                    <input type="hidden" name="insertabono" value="1" />
                                                </td>
                                            </tr>
                                        @endif
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        @endif

    </div>
    </form>

    <div class="modal fade" id="openInstPago" aria-hidden="true" aria-labelledby="..." tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-scrollable" >
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" > </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    </button>
                </div>
                <div class="modal-body  pt-0 instpagohtml"    >

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">  CERRAR</button>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('scripts')
    <!-- App js -->
    <script src="{{ URL::asset('build/js/app.js') }}"></script>
    <script>
        function number_format(amount, decimals) {
            amount += ''; // por si pasan un numero en vez de un string
            amount = parseFloat(amount.replace(/[^0-9\.\-]/g, '')); // elimino cualquier cosa que no sea numero o punto

            decimals = decimals || 0; // por si la variable no fue fue pasada

            // si no es un numero o es igual a cero retorno el mismo cero
            if (isNaN(amount) || amount === 0)
                return parseFloat(0).toFixed(decimals);

            // si es mayor o menor que cero retorno el valor formateado como numero
            amount = '' + amount.toFixed(decimals);

            var amount_parts = amount.split('.'),
                regexp = /(\d+)(\d{3})/;

            while (regexp.test(amount_parts[0]))
                amount_parts[0] = amount_parts[0].replace(regexp, '$1' + '.' + '$2');

            return amount_parts.join(',');
        }

        $('.checkcxc').click(function(){

            var nrounico = $(this).data('nrounico');

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url  : "{{route('itemCxc')}}",
                data : {nrounico:nrounico},
                type : 'post',
            }).done(function(resp) {
                $('#searchcontentcuenta').html(resp);
                $('#botonactarriba').show();
            });

        });

        $('.verInstPago').click(function(){

            var url = $(this).data('url');
            $('.instpagohtml').html('<button class="btn btn-outline-primary btn-load"><span class="d-flex align-items-center"><span class="spinner-border flex-shrink-0" role="status"> <span class="visually-hidden"> Cargando...</span> </span> <span class="flex-grow-1 ms-2">Cargando... </span> </span> </button>');
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url  : url,
                type : 'post',
            }).done(function(resp) {
                $('.instpagohtml').html(resp);
            });

        });

        $(".nextfield").unbind('change').bind('change',function (e) {

            var name        = $(this).data('name');
            var val         = $(this).val();
            var maximo      = $(this).data('maximo');
            var abonado     = $(this).data('abonado');
            var nrounico    = $(this).data('nrounico');
            var credendolar = $(this).data('credendolar');

            $('.ocultarboton').hide();

            var resta = Number(credendolar) - Number(abonado) - Number(val);

            resta = resta.toFixed(10);
            if(resta >= 0)
                $('.saldocxc'+nrounico).html('$ ' + number_format(resta, 2));
            else
                $('.saldocxc'+nrounico).html('$ ' + number_format(maximo, 2));

            if(Number(val) > Number(maximo))
                $(this).val(0);

            var items = $(this).data('item')+Number(1);

            var sumatantos=0;
            var sumarestas=0;
            var num = 0;
            var sald= 0;
            var arrtantos = $('.tantos');

            arrtantos.each(function(index, item) {
                num  = $(this).val();
                sald = $(this).data('maximo');
                sald = parseFloat(sald);
                num  = parseFloat(num);
                if(parseFloat(sald) - parseFloat(num) >= 0 ){
                    sumatantos = sumatantos+num;
                    sumarestas = parseFloat(sumarestas) + sald - num;
                }
            });


            sumatantos = sumatantos.toFixed(2);
            sumarestas = sumarestas.toFixed(2);

            $('.breadcrumb-item.active').html( '$ ' + number_format(sumarestas, 2));
            $('.breadcrumb-item.active').html( '$ ' + number_format(sumatantos, 2));

            if ( $('.'+name+items).length    )
                $('.'+name+items).select();

        });

        $('.nav-link').click(function(){

            var tab = $(this).data('tab');
            $('#tab').val(tab);

        });


    </script>
@endsection
