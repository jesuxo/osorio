@extends('layouts.masterprint')
@section('title')
  {{$banco->descrip}}   {{$fecha}} - {{$fecha}}
@endsection
@section('css')
    <style>
        .tdline{
            border:1px solid #0072c5 !important;
            font-size: 12px;
        }
        .iniciobox{
            box-shadow: 0 25px 25px -19px rgba(100,100,100,0.3) !important;
            margin: 20px 5px;
        }
    </style>
@endsection
@section('content')

    @php
        $faltancuentas = 0;
        $entramoneda   = 0;
        $fechas        = [];
        $cadenatr      = '';
        $tmonto_bs     = 0;
        $tmonto_dolar  = 0;
        $tmonto_peso   = 0;
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
    <div style="width:600px; margin:auto; display:flex; flex-direction: column-reverse;" >
        <table border="0" width="100%" >
            <tr>
                <td colspan="3">

                    <table border="0" width="98%" style="font-size: 11px">
                        <tr>
                            <td width="9%"  height="30" align="center" class="titulo tdline">Fecha  </td>
                            <td width="3%"  align="left"class="titulo tdline  ">  </td>
                            <td width="46%"  align="left"class="titulo tdline  ">
                                Beneficiario, Descripci&oacute;n
                            </td>
                            <td width="11%"  align="center"  class="titulo tdline" >
                                Debe
                            </td>
                            <td width="11%"  align="center" class="titulo tdline"  >
                                Haber
                            </td>
                        </tr>
                        @php $numtr = 0;
                                    if(isset($transaccions) and count($transaccions) > 0){
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

                            </td>
                            <td align="left">
                                {{$descripbene}} - {{$descripcion}}
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

                                    $fechas[$fecha]['bss'] = $tmonto_bs;
                                    $fechas[$fecha]['usd'] = $tmonto_dolar;
                                    $fechas[$fecha]['cop'] = $tmonto_peso;
                                @endphp
                            </td>
                        </tr>

                        @php  }
                                    }@endphp
                        <tr >
                            <td height="25" align="center">&nbsp;</td>
                            <td align="center">&nbsp;</td>
                            <td align="left"><a href="#" name="lastline">&nbsp;</a></td>
                            <td align="right">&nbsp;</td>
                            <td  align="right">&nbsp;</td>
                        </tr>
                    </table>
                </td>
            </tr>

        </table>
        <div class="iniciobox cajapequenacolor titulo"
             align="center"
             style="cursor: pointer; background: #FF8F32; color: white; border-radius: 5px; padding: 10px;
              width:100% !important; max-width:581px; height:188px; "  >

            <table border="0" width="100%">
                <tr>
                    <td width="77%" align="left">Bolivares (Bs)</td>
                    <td width="23%" align="right">{{number_format($saldobs,2,',','.')}} </td>
                </tr>
                <tr>
                    <td align="left">Dolares (s)</td>
                    <td align="right"> {{number_format($saldousd,2,',','.')}}  </td>
                </tr>

                <tr>
                    <td align="left">Pesos (Cop)</td>
                    <td align="right"> {{number_format($saldocop,2,',','.')}}   </td>
                </tr>
            </table>

            Sumatoria por Tipo de Moneda
            <table border="0" width="100%" style="padding: 5px; border-radius: 5px; background: rgba(0,0,0,0.2);">
                <tr>
                    <td width="77%" align="left">Bolivares (Bs)</td>
                    <td width="23%" align="right"> {{number_format($tmonto_bs,2,',','.')}} </td>
                </tr>
                <tr>
                    <td align="left">Dolares (s)</td>
                    <td align="right">{{number_format($tmonto_dolar,2,',','.')}}   </td>
                </tr>
                <tr>
                    <td align="left">Pesos (Cop)</td>
                    <td align="right"> {{number_format($tmonto_peso,2,',','.')}}   </td>
                </tr>
            </table>

        </div>
        <table border="0" width="100%" >
            <tr >
                <td width="20%"  align="center" >
                    <img src="{{ URL::asset('build/images/logo-dark.png') }}"  class="card-logo card-logo-dark" alt="logo dark" width="70px"></td>
                <td width="50%"  align="center">
                    Saldo  {{$banco->descrip}}
                </td>
                <td width="30%"  align="center"> {{$fecha}}</td>
            </tr>
        </table>
    </div>

    <div class="hstack gap-2 justify-content-end d-print-none mt-5">
        <a href="javascript:window.print()" class="btn btn-success"><i
                class="ri-printer-line align-bottom me-1"></i> Print</a>
    </div>
@endsection
@section('scripts')
    <script src="{{ URL::asset('build/js/app.js') }}"></script>
@endsection
