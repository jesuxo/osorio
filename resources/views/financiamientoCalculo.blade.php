@extends('layouts.master')
@section('title')
    Simulaci&oacute;n {{$valorfinancia}}
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
        input{
            height:40px;
            width: 99%;
            font-size: 24px !important;
            text-align: right;
        }
        table{
            font-size: 20px !important;
        }
    </style>
@endsection
@section('content')
    @php $realizarfinancia = 1; @endphp
    <form id="form1" name="form1" method="post" action="{{route('simulacionFinanciamiento')}}" >
        @method('post')
        @csrf
        <div class="row">
            <div class="col-md-12">
                <div class="card mt-3" onclick="$('#buttonprocesar').hide();">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Simulaci&oacute;n de Financiamiento</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                @php

                                    if($inicialfinancia > 0 and $porcinicialant == 0)
                                        $porcinicial = ($inicialfinancia/$valorfinancia) * 100;

                                    if($inicialfinancia == 0 and $porcinicial > 0)
                                        $inicialfinancia  = $valorfinancia * ($porcinicial/100);

                                    if($saldofinancia == 0  and $inicialfinancia>0){
                                        $saldofinancia = $valorfinancia - $inicialfinancia;
                                    }

                                    if($porccostofi > 0 )
                                        $costofinancia = $saldofinancia * ($porccostofi/100);

                                    if($porccostofi == 0 and $costofinancia > 0)
                                        $porccostofi = ($costofinancia / $saldofinancia) * 100;

                                    $montocalculo = $costofinancia + $saldofinancia;
                                @endphp
                                <div class="table-responsive table-card mt-3">
                                    <table width="100%" border="0" class="table tdline table-borderless table-centered align-middle table-nowrap mb-0 ">

                                    <tr >
                                        <td width="20%" colspan="2"  >Valor</td>
                                        <td width="10%"  align="right">
                                            <input style="width: 99%;"  value="{{(isset($valorfinancia))? $valorfinancia :''}}"
                                                   type="number" size="1" class="" name="valorfinancia"
                                                   onchange="$('#form1').attr('action','{{route('simulacionFinanciamiento')}}'); $('#form1').submit();"/>                                        </td>

                                        <td width="12%"></td>
                                        <td width="12%"></td>
                                        <td width="12%"></td>
                                        <td width="12%"></td>
                                        <td width="12%"></td>
                                        <td width="5%"></td>
                                    </tr>
                                    <tr  bgcolor="#eeeeee">
                                        <td width=""  colspan="2">Inicial
                                            <input type="hidden" name="inicialfinanciaold" value="{{($inicialfinancia)}}" />
                                            <input type="hidden" name="porcinicialold" value="{{($porcinicial)}}" />                                        </td>
                                        <td width="" align="right">
                                            <input style="width: 99%; min-width: 80px;"  value="{{(isset($inicialfinancia))? $inicialfinancia :''}}"
                                                   type="number" size="1" class="" name="inicialfinancia"
                                                   onchange="$('#form1').attr('action','{{route('simulacionFinanciamiento')}}');  $('#form1').submit();"/>                                        </td>
                                        <td width=""  align="left">
                                            <input type="hidden" value="{{$porcinicial}}" name="porcinicialant" />
                                            <input style="width: 80px;"  value="{{(isset($porcinicial) and $porcinicial >0)? $porcinicial :''}}"
                                                   type="number" size="1" class="" name="porcinicial"
                                                   onchange="$('#form1').attr('action','{{route('simulacionFinanciamiento')}}'); $('#form1').submit();"   />%                                        </td>

                                        <td width=""></td>
                                        <td width=""></td>
                                        <td width=""></td>
                                        <td width=""><!--Cant Cuotas Cuotas--></td>
                                        <td width=""><!--<input style="width: 99%;"  value="{{(isset($cantcuotas) and $cantcuotas >0)? $cantcuotas :''}}"
                                                            type="number" size="1" class="" name="cantcuotas" id="cantcuotas" placeholder=" Ej. 44 - 66 "
                                                            onchange="$('#form1').attr('action','{{route('simulacionFinanciamiento')}}'); $('#form1').submit();"   /> -->
                                        </td>
                                    </tr>
                                    <tr >
                                        <td  colspan="2">Saldo a Financiar</td>
                                        <td align="right">
                                            <input style="width: 99%; min-width: 80px;"  value="{{(isset($saldofinancia))? $saldofinancia :''}}"
                                                   type="number" size="1" class="" name="saldofinancia"
                                                   onchange="$('#form1').attr('action','{{route('simulacionFinanciamiento')}}'); $('#form1').submit();" />                                        </td>

                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td> </td>
                                        <td> </td>
                                    </tr>
                                    <tr >
                                        <td  colspan="2">Tasa</td>
                                        <td align="right">  </td>
                                        <td> <input style="width: 80px;"  value="{{(isset($porccostofi))? $porccostofi :''}}"
                                                    type="number" size="1" class="" name="porccostofi"
                                                    onchange="$('#form1').attr('action','{{route('simulacionFinanciamiento')}}'); $('#form1').submit();" />%</td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td> </td>
                                        <td> </td>
                                    </tr>

                                    <tr  bgcolor="#eeeeee">
                                        <td  colspan="2" align="left" >Costo Financiero  </td>
                                        <td align="right" class="tdline" style="font-size: 16px">{{number_format((($saldofinancia*($porccostofi/100))*3),2,',','.')}}</td>
                                        <td align="right" class="tdline" style="font-size: 16px">{{number_format((($saldofinancia*($porccostofi/100))*3.5),2,',','.')}}</td>
                                        <td align="right" class="tdline" style="font-size: 16px">{{number_format((($saldofinancia*($porccostofi/100))*4),2,',','.')}}</td>
                                        <td align="right" class="tdline" style="font-size: 16px">{{number_format((($saldofinancia*($porccostofi/100))*4.5),2,',','.')}}</td>
                                        <td align="right" class="tdline" style="font-size: 16px">{{number_format((($saldofinancia*($porccostofi/100))*5),2,',','.')}}</td>
                                        <td align="right" class="tdline" style="font-size: 16px">{{number_format((($saldofinancia*($porccostofi/100))*10),2,',','.')}}</td>
                                        <td align="right" class="tdline" style="font-size: 16px">{{number_format((($saldofinancia*($porccostofi/100))*15),2,',','.')}}</td>
                                    </tr>
                                    <tr   >
                                        <td  colspan="2" align="left" >  </td>
                                        <td align="right" class=" " style="font-size: 16px">{{number_format((($saldofinancia*($porccostofi/100))*3)+$saldofinancia,2,',','.')}}</td>
                                        <td align="right" class=" " style="font-size: 16px">{{number_format((($saldofinancia*($porccostofi/100))*3.5)+$saldofinancia,2,',','.')}}</td>
                                        <td align="right" class=" " style="font-size: 16px">{{number_format((($saldofinancia*($porccostofi/100))*4)+$saldofinancia,2,',','.')}}</td>
                                        <td align="right" class=" " style="font-size: 16px">{{number_format((($saldofinancia*($porccostofi/100))*4.5)+$saldofinancia,2,',','.')}}</td>
                                        <td align="right" class=" " style="font-size: 16px">{{number_format((($saldofinancia*($porccostofi/100))*5)+$saldofinancia,2,',','.')}}</td>
                                        <td align="right" class=" " style="font-size: 16px">{{number_format((($saldofinancia*($porccostofi/100))*10)+$saldofinancia,2,',','.')}}</td>
                                        <td align="right" class=" " style="font-size: 16px">{{number_format((($saldofinancia*($porccostofi/100))*15)+$saldofinancia,2,',','.')}}</td>
                                    </tr>
                                    <tr  bgcolor="beige">
                                        <td  colspan="2" align="left" >Monto Cuota/Semana</td>
                                        <td align="right" class=" " style="font-size: 16px">{{number_format(((($saldofinancia*($porccostofi/100))*3)+$saldofinancia)/12,2,',','.')}}</td>
                                        <td align="right" class=" " style="font-size: 16px">{{number_format(((($saldofinancia*($porccostofi/100))*3.5)+$saldofinancia)/14,2,',','.')}}</td>
                                        <td align="right" class=" " style="font-size: 16px">{{number_format(((($saldofinancia*($porccostofi/100))*4)+$saldofinancia)/16,2,',','.')}}</td>
                                        <td align="right" class=" " style="font-size: 16px">{{number_format(((($saldofinancia*($porccostofi/100))*4.5)+$saldofinancia)/20,2,',','.')}}</td>
                                        <td align="right" class=" " style="font-size: 16px">{{number_format(((($saldofinancia*($porccostofi/100))*5)+$saldofinancia)/22,2,',','.')}}</td>
                                        <td align="right" class=" " style="font-size: 16px">{{number_format(((($saldofinancia*($porccostofi/100))*10)+$saldofinancia)/44,2,',','.')}}</td>
                                        <td align="right" class=" " style="font-size: 16px">{{number_format(((($saldofinancia*($porccostofi/100))*15)+$saldofinancia)/66,2,',','.')}}</td>
                                    </tr>

                                    <tr >
                                        <td>Nro Meses</td>
                                        <td></td>
                                        <td height="30" align="center" class="tdline" style="font-size: 18px">3</td>
                                        <td align="center" class="tdline"style="font-size: 18px">3.5</td>
                                        <td align="center" class="tdline"style="font-size: 18px">4</td>
                                        <td align="center" class="tdline"style="font-size: 18px">4.5</td>
                                        <td align="center" class="tdline"style="font-size: 18px">5</td>
                                        <td align="center" class="tdline"style="font-size: 18px">10</td>
                                        <td align="center" class="tdline"style="font-size: 18px">15</td>
                                    </tr>
                                    <tr >
                                        <td>Nro Semanas</td>
                                        <td></td>
                                        <td height="30" align="center" class="tdline" style="font-size: 18px">12</td>
                                        <td align="center" class="tdline"style="font-size: 18px">14</td>
                                        <td align="center" class="tdline"style="font-size: 18px">16</td>
                                        <td align="center" class="tdline"style="font-size: 18px">20</td>
                                        <td align="center" class="tdline"style="font-size: 18px">22</td>
                                        <td align="center" class="tdline"style="font-size: 18px">44</td>
                                        <td align="center" class="tdline"style="font-size: 18px">66</td>
                                    </tr>

                                </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection
@section('scripts')
    <script>
        $('#cantcuotas').focus().select();
        $('input').click(function () {
            $(this).select();
        });
    </script>

    <script src="{{ URL::asset('build/js/app.js') }}"></script>

@endsection
