@extends('layouts.masterprint')
@section('title')
   Financiamiento - {{$financiamiento->cliente->descrip}}
@endsection
@section('css')
<style>
    .tdline{
        border:1px solid #0072c5 !important;
        font-size: 12px;
    }
</style>
@endsection
@section('content')

                <table border="0" width="100%" >

                    <tr >
                        <td width="20%" >
                            <img src="{{ URL::asset('build/images/logo-dark.png') }}"  class="card-logo card-logo-dark" alt="logo dark" width="70px"></td>
                        <td width="50%" >Nro de Prestamo
                            <strong>
                                @php
                                    $num      = $financiamiento->id;
                                    $sqlcheck = "select lpad('$num',4,'0') as cadena ";
                                    $resquery = \Illuminate\Support\Facades\DB::select($sqlcheck);
                                    $num      = $resquery[0]->cadena;
                                    echo $num;
                                @endphp
                            </strong>
                        </td>
                        <td width="30%" >Fecha: <strong>{{$financiamiento->fechaformat}}</strong></td>
                    </tr>
                    <tr >
                        <td height="35"  colspan="3" class="tdline" align="center"><strong>DATOS PRESTATARIO</strong></td>
                    </tr>
                    <tr >
                        <td >Nombres y Apellidos</td>
                        <td colspan="2">{{$financiamiento->cliente->descrip}}</td>
                    </tr>
                    <tr >
                        <td >Nro C&eacute;dula</td>
                        <td colspan="2">{{$financiamiento->cliente->id3}}</td>
                    </tr>
                    <tr >
                        <td >Direcci&oacute;n Habitaci&oacute;n</td>
                        <td colspan="2">{{$financiamiento->cliente->direc1}}   {{$financiamiento->cliente->direc2}}  </td>
                    </tr>
                    <tr >
                        <td >Tel&eacute;fono Cel</td>
                        <td colspan="2">{{$financiamiento->cliente->telef}}</td>
                    </tr>
                    <tr >
                        <td >Tel&eacute;fono Cel Alter.</td>
                        <td colspan="2">{{$financiamiento->cliente->movil}}</td>
                    </tr>
                    <tr >
                        <td >Correo Electr&oacute;nico</td>
                        <td colspan="2"><a href="mailto:{{$financiamiento->cliente->email}}">{{$financiamiento->cliente->email}}</a></td>
                    </tr>
                    <tr >
                        <td >Direci&oacute;n Trabajo</td>
                        <td colspan="2">{{$financiamiento->cliente->direc3}}</td>
                    </tr>
                    @if(isset($financiamiento->avalista->descrip))
                        <tr >
                            <td height="35"  colspan="3" class="tdline" align="center"><strong>DATOS AVALISTA</strong></td>
                        </tr>
                        <tr >
                            <td >Nombres y Apellidos</td>
                            <td colspan="2">{{(isset($financiamiento->avalista->descrip))? $financiamiento->avalista->descrip : ''}}</td>
                        </tr>
                        <tr >
                            <td >Nro C&eacute;dula Avalista</td>
                            <td colspan="2"> {{(isset($financiamiento->avalista->id3))? $financiamiento->avalista->id3 : ''}} </td>
                        </tr>
                        <tr >
                            <td >Direcci&oacute;n Habitaci&oacute;n</td>
                            <td colspan="2">
                                {{(isset($financiamiento->avalista->direc1))? $financiamiento->avalista->direc1 : ''}}
                                {{(isset($financiamiento->avalista->direc2))? $financiamiento->avalista->direc2 : ''}}
                                {{(isset($financiamiento->avalista->direc3))? $financiamiento->avalista->direc3 : ''}}
                            </td>
                        </tr>
                        <tr >
                            <td >Tel&eacute;fono Cel Avalista</td>
                            <td colspan="2">{{(isset($financiamiento->avalista->telef))? $financiamiento->avalista->telef : ''}}</td>
                        </tr>
                        <tr >
                            <td >Tel&eacute;fono Cel Alternativo</td>
                            <td>{{(isset($financiamiento->avalista->movil))? $financiamiento->avalista->movil : ''}}</td>
                        </tr>
                        <tr >
                            <td >Correo Electr&oacute;nico</td>
                            <td colspan="2">{{(isset($financiamiento->avalista->email))? $financiamiento->avalista->email : ''}}</td>
                        </tr>
                    @endif
                    <tr >
                        <td height="35"  colspan="3" class="tdline" align="center"><strong>CARACTERISTICAS DEL PRESTAMO </strong></td>
                    </tr>
                    @if(isset($financiamiento->marca) and $financiamiento->marca !='')
                        <tr >
                            <td >Marca</td>
                            <td colspan="2">{{isset($financiamiento->marca)?$financiamiento->marca:''}}</td>
                        </tr>
                        <tr >
                            <td >Modelo</td>
                            <td colspan="2">{{isset($financiamiento->modelo)?$financiamiento->modelo:''}}</td>
                        </tr>
                        <tr >
                            <td >A&ntilde;o</td>
                            <td colspan="2">{{isset($financiamiento->year)?$financiamiento->year:''}}</td>
                        </tr>
                        <tr >
                            <td >Color</td>
                            <td colspan="2">{{isset($financiamiento->color)?$financiamiento->color:''}}</td>
                        </tr>
                        <tr >
                            <td >Serial Motor</td>
                            <td colspan="2">{{isset($financiamiento->sm)?$financiamiento->sm:''}}</td>
                        </tr>
                        <tr >
                            <td >Serial Carroceria</td>
                            <td colspan="2">{{isset($financiamiento->sc)?$financiamiento->sc:''}}</td>
                        </tr>
                        <tr >
                            <td >Placa</td>
                            <td colspan="2">{{isset($financiamiento->placa)?$financiamiento->placa:''}}</td>
                        </tr>
                    @endif

                    @if(isset($financiamiento->superficie) and $financiamiento->superficie !='')
                        <tr >
                            <td >Direcci&oacute;n</td>
                            <td colspan="2"  align="left">{{isset($financiamiento->direccion)?$financiamiento->direccion:''}}</td>
                        </tr>
                        <tr >
                            <td >Caracter&iacute;ticas;</td>
                            <td colspan="2"  align="left">{{isset($financiamiento->caracteristicas)?$financiamiento->caracteristicas:''}}</td>
                        </tr>
                        <tr >
                            <td >Superficie</td>
                            <td colspan="2" align="left">{{isset($financiamiento->superficie)?$financiamiento->superficie:''}}</td>
                        </tr>

                    @endif
                    <tr >
                        <td >Valor del Financiamiento</td>
                        <td align="right">{{number_format($financiamiento->saldofinancia,2,',','.')}}</td>
                        <td></td>
                    </tr>
                    <tr >
                        <td>Inicial</td>
                        <td  align="right">{{number_format($financiamiento->inicialfinancia,2,',','.')}}</td>
                        <td></td>
                    </tr>
                    <tr >
                        <td >Nro de Cuotas</td>
                        <td  align="right">{{number_format($financiamiento->cantcuotas,0,',','.')}}</td>
                        <td></td>
                    </tr>
                    <tr >
                        <td >Valor Cuota Semanal</td>
                        <td  align="right">{{number_format($financiamiento->cuota,2,',','.')}}</td>
                        <td></td>
                    </tr>
                    <tr >
                        <td ></td>
                        <td></td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr >
                        <td >Cr&eacute;dito Autorizado Por: </td>
                        <td> <b>RUBEN OSORIO</b>__________________________ </td>
                        <td>Fecha: <strong>{{\Carbon\Carbon::now()->format('d/m/Y')}}</strong></td>
                    </tr>
                    <tr >
                        <td >Recibe Conforme</td>
                        <td>{{$financiamiento->cliente->descrip}} ____________________</td>
                        <td>Fecha: <strong>{{\Carbon\Carbon::now()->format('d/m/Y')}}</strong></td>
                    </tr>
                    <tr >
                        <td ></td>
                        <td>&nbsp;</td>
                        <td></td>
                    </tr>
                    <tr >
                        <td  colspan="3">Nota: Se entregara copia de la documentaci&oacute;n Original al momento de otorgar el prestamo</td>
                    </tr>
                    <tr >
                        <td  colspan="3">Al momento del pago total de la unidad, se entregar&aacute; la documentaci&oacute;n original a nombre del comprador</td>
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
