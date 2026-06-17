@extends('layouts.masterprint')
@section('title')
   LETRA DE CAMBIO - {{$letra->cliente->descrip}}
@endsection
@section('css')

@endsection
@section('content')
    <table width="100%" border="0"  class="tdline" style="margin:20px 0px 0px 0px !important">

        <tr>
            <td width="1%" valign="top" class="tdline"><table width="100%" border="0" style="transform: rotate(270deg); margin-top: 58px; font-size:11px">
                    <tr>
                        <td width="22%" align="center" class="tdline" colspan="2">ACEPTADA PARA SER PAGADA A SU VENCIMIENTO SIN AVISO Y SIN PROTESTO</td>
                    </tr>
                    <tr>
                        <td width="22%" align="left"> FECHA:</td>
                        <td width="78%" align="center">______________________ </td>
                    </tr>
                    <tr>
                        <td width="22%" align="left"> FIRMA:</td>
                        <td width="78%" align="center">______________________ </td>
                    </tr>
                    <tr>
                        <td width="22%" align="left"> CEDULA:</td>
                        <td width="78%" align="center">______________________ </td>
                    </tr>
                    <tr>
                        <td align="left">REG.MERC.N:</td>
                        <td align="center">______________________ </td>
                    </tr>
                </table></td>
            <td width="98%"><table width="100%" border="0" >
                    <tr>
                        <td width="67%" height="29">
                            <table width="100%" border="0"  >
                                <tr>
                                    <td width="50%" align="left"> Nro 1/1</td>
                                    <td width="29%" align="center"><strong>{{ $letra->ciudadpago }}</strong></td>
                                    <td width="21%" align="center">{{$letra->fechaformat}}</td>
                                </tr>
                            </table></td>
                        <td width="33%" class="tdline"  align="center" style="background:#f2f2f2; font-size:18px">USD <strong>{{ number_format($letra->monto,2,',','.')}}</strong></td>
                    </tr>
                    <tr>
                        <td  colspan="2"  >
                            <span style="text-align:justify"> El d&iacute;a
                                <strong>{{$letra->dialetrapagar}}</strong> de
                                <strong>{{$letra->monpagar}}</strong> del año
                                <strong>{{$letra->yearpagar}},  </strong></span>
                                se servir&aacute;(n), Ud(s) mandar a pagar por esta <strong>UNICA DE CAMBIO</strong> a la orden de   	<strong>RUBEN ALEJANDRO OSORIO ESPINOSA  </strong> la cantidad de:
                            <br>
                            <div class="tdline"   style="background:#f2f2f2; padding:5px; text-align:center; font-size:18px;" ><strong>
                                     {{strtoupper($letra->montoletra)}}
                                    <span style="text-align:justify">DOLARES DE ESTADOS UNIDOS DE NORTEAMERICA</span></strong></div>
                            Lugar de pago, {{$letra->ciudadpago}} valor <strong>CONVENIDO</strong> que cargara(n) en cuenta <strong>SIN AVISO Y SIN PROTESTA</strong><br>
                        </td>
                    </tr>
                    <tr>
                        <td  valign="top">
                            LIBRADO(S) <strong>{{$letra->cliente->descrip}}</strong><br>
                            <strong>{{$letra->cliente->direc1}}</strong><br></td>
                        <td  valign="top"  align="center" >LIBRADOR</td>
                    </tr>
                </table></td>
            <td width="1%"  class="tdline">
                <table width="100%" border="0" style="transform: rotate(270deg);margin-top: 30px; font-size:11px">
                    <tr>
                        <td width="22%" align="center" class="tdline" colspan="2">BUENO POR AVAL PARA GARANTIZAR LAS OBLIGACIONES DEL LIBERADO ACEPTANTE</td>
                    </tr>

                    <tr>
                        <td width="22%" align="left"> FIRMA:</td>
                        <td width="78%" align="center">______________________ </td>
                    </tr>
                    <tr>
                        <td width="22%" align="left"> CEDULA:</td>
                        <td width="78%" align="center">______________________ </td>
                    </tr>
                    <tr>
                        <td align="center">REG.MER.N.:</td>
                        <td align="center">______________________ </td>
                    </tr>
                </table>                </td>
        </tr>
    </table>
    <div class="hstack gap-2 justify-content-end d-print-none mt-5">
        <a href="javascript:window.print()" class="btn btn-success"><i
                class="ri-printer-line align-bottom me-1"></i> Print</a>
    </div>
@endsection
@section('scripts')
    <script src="{{ URL::asset('build/js/app.js') }}"></script>
@endsection
