
@extends('layouts.master')
@section('title')
    INSTRUMENTOS DE PAGO BOLIVARES
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
        <form  method="post" name="form1" id="form1" action="/reporte/instpagobs">
            <div class="col-md-3 order-last">

                        <div class="input-group">
                            <input type="text" class="form-control" data-provider="flatpickr"
                                   data-range-date="true" data-date-format="d/m/Y"
                                   data-deafult-date="" name="fechasreport" readonly="readonly" value="{{$fechasreport}}"
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
                REPORTE DE INSTRUMENTOS DE PAGO - BOLIVARES
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
                                            <td width="" align="right" class=" tdline" > {{(isset($listado[$indexsuc][$indexpunto][$index]))?number_format($listado[$indexsuc][$indexpunto][$index],2,',','.'): ''}}</td>
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
    </div>

@endsection
@section('scripts')
    <!-- apexcharts -->
    <script src="{{ URL::asset('build/libs/apexcharts/apexcharts.min.js') }}"></script>

    <!-- Vector map-->
    <script src="{{ URL::asset('build/libs/jsvectormap/jsvectormap.min.js') }}"></script>
    <script src="{{ URL::asset('build/libs/jsvectormap/world-merc.js') }}"></script>

    <script src="{{ URL::asset('build/libs/list.js/list.min.js') }}"></script>

    <!--Swiper slider js-->
    <script src="{{ URL::asset('build/libs/swiper/swiper-bundle.min.js') }}"></script>

    <!-- Dashboard init -->
    <script src="{{ URL::asset('build/js/pages/dashboard-ecommerce.init.js') }}"></script>

    <!-- App js -->
    <script src="{{ URL::asset('build/js/app.js') }}"></script>

@endsection
