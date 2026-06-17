
@extends('layouts.master')
@section('title')
    INSTRUMENTOS DE PAGO DOLARES
@endsection
@section('css')
    <link href="{{ URL::asset('build/libs/jsvectormap/jsvectormap.min.css') }}" rel="stylesheet" type="text/css">

    <!--Swiper slider css-->
    <link href="{{ URL::asset('build/libs/swiper/swiper-bundle.min.css') }}" rel="stylesheet" type="text/css">
    <style>
    .botoncal{
        background: transparent;
        border: none;
        color: white;
    }
    .botoncal:hover{
         font-size: 13px;
    }
    </style>
@endsection
@section('content')
    <style>
        .tdline{
            border:1px solid #f95a02 !important;

        }
        .tdlineff{
            border-left:1px solid #fff !important;

            color: white !important;
            background-color: #f95a02 !important;
        }
    </style>
    <div class="row">
        <form  method="post" name="form1" id="form1" action="/reporte/instpagodolares">
            <div class="col-md-3 order-last">

                        <div class="input-group">
                            <input type="text" class="form-control" data-provider="flatpickr"
                                   data-range-date="true" data-date-format="d/m/Y"
                                   data-deafult-date="" name="fechasreport" id="fechasreport"
                                   readonly="readonly" value="{{$fechasreport}}"
                            >
                            <div class="input-group-text bg-primary border-primary text-white">
                                <button type="submit" class="botoncal" >Consultar</button>
                            </div>
                        </div>

            </div>
            @csrf
            @method('POST')
        </form>
        <div class="col-md-12 ">
            <div class="card-header mt-3 align-items-center justify-content-center text-center">
                REPORTE DE INSTRUMENTOS DE PAGO - DOLARES
                <br />
                DESDE  {{$fecha1}} HASTA {{$fecha2}}
            </div>
            @php $totales = [];@endphp
                <div class="table-responsive table-card mt-3">
                    <table width="100%" border="0"    class="table table-borderless table-centered align-middle table-nowrap mb-0 mt-3">
                        <tr bgcolor="#fff">
                            <td width="30%" height="30"align="center" class="tdline" >SUCURSAL</td>
                            @foreach($clases as $index => $data)
                                <td width="" align="center" class="tdlineff" > {{$index}} </td>
                            @endforeach
                        </tr>

                        @if(isset($sucursales))
                            @foreach($sucursales as $indexsuc => $puntos)
                                @foreach($puntos as $indexpunto => $punto)
                                    @php
                                        $n       = 0;
                                        $tmontos = 0;
                                    @endphp

                                    <tr @php if(($n%2)==0){echo 'bgcolor="#eee"'; }else{echo 'bgcolor="#fff"';} @endphp>
                                        <td  height="30"align="left" class="tdline" >{{str_replace("APOCHI",'',$indexsuc)}}-{{$indexpunto}}</td>
                                        @foreach($clases as $index => $data)
                                            @php
                                                if(!isset($totales[$index])) $totales[$index] = 0;
                                                $totales[$index] += (isset($listado[$indexsuc][$indexpunto][$index]))? $listado[$indexsuc][$indexpunto][$index] : 0;
                                            @endphp
                                            <td width="" align="right" class=" tdline" >
                                                <a href="javascript:;" data-fksucu="{{$indexsuc}}" data-fksucu="{{$indexpunto}}" data-index="{{$index}}">
                                                  {{(isset($listado[$indexsuc][$indexpunto][$index]))?number_format($listado[$indexsuc][$indexpunto][$index],2,',','.'): ''}}
                                                </a>
                                            </td>
                                        @endforeach
                                    </tr>
                                    @php $n++; @endphp
                                @endforeach
                            @endforeach
                        @endif
                        <tr >
                            <td height="30"align="left" class=" " > </td>
                            @foreach($clases as $index => $data)
                                <td width="" align="center" class=" " >  </td>
                            @endforeach
                        </tr>
                        <tr bgcolor="#eee">
                            <td height="30"align="left" class="tdline" >TOTALES</td>
                            @foreach($clases as $index => $data)
                                <td width="" align="right" class="tdline " > {{ number_format($totales[$index],2,',','.') }} </td>
                            @endforeach
                        </tr>
                    </table>
                </div>
                <br>
                <br>
                <br>
            </div>
        </div>

        <div class="modal fade" id="detallemodal" aria-hidden="true" aria-labelledby="..." tabindex="-1">
            <div class="modal-dialog modal-xl modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="titulolistado">Listado pagos en dolares</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <div class="modal-body" id="contentdetreport">

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">  CERRAR</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('scripts')
    <script>
        $('.detallemodal').unbind('click').bind('click',function () {
            var codpago   = $(this).attr('data-codpago');
            var fk_sucu   = $(this).attr('data-fksucu');
            var fechasr   = $("#fechasreport").val()
            $('#contentdetreport').html('<button class="btn btn-outline-primary btn-load"><span class="d-flex align-items-center"><span class="spinner-border flex-shrink-0" role="status"> <span class="visually-hidden"> Cargando...</span> </span> <span class="flex-grow-1 ms-2">Cargando... </span> </span> </button>');
            $.ajax({
                type:'post',
                data:{codclie: (codclie)? codclie : '' },
                url:'/reporte/detinstpagodolares',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success:function(response) {
                    $('#contentdetreport').html(response);
                }
            });
        });
    </script>
    <script src="{{ URL::asset('build/js/app.js') }}"></script>

@endsection
