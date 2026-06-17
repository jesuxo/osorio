@extends('layouts.master')
@section('title')
    Existencias de productos
@endsection
@section('css')
    <style>
        #clearall{
            text-decoration: none !important;
        }
    </style>
@endsection
@section('content')

    <x-breadcrumb title="INVENTARIO" pagetitle="Productos" />

    <div class="row">
        <div class="col-xl-5 ">
            <div class="card overflow-hidden">
                <div class="accordion accordion-flush filter-accordion">
                    <div class="card-body border-bottom">
                        <div>

                            <ul class="list-unstyled mb-0 filter-list">
                                <li  style="color: black; font-weight: bold" class="mb-2">
                                    <div class="d-flex  align-items-center">
                                        <div class="flex-grow-1" style="width: 73px">
                                            <div class="mb-0 listname" >SUCURSAL</div>
                                        </div>
                                        <div class="flex-grow-1" style="width: 73px;text-align: right;">
                                            <div class="mb-0 listname" style="width: 239px; text-align: center;  ">  UNDS</div>
                                        </div>
                                        <div class="flex-grow-1" style="width: 73px;text-align: right;">
                                            <div class="mb-0 listname" style="width: 121px; text-align: right; "> COSTO</div>
                                        </div>
                                    </div>
                                </li>
                                @php
                                    $nn        = 1;
                                    $tunidades = 0;
                                    $tcosto= 0;
                                    $titulosuc = '';
                                @endphp

                                @foreach($arraysucursal as $sucursalid => $sucursal)

                                    @if(  isset($sucursal['cantidad']) and $sucursal['cantidad'] > 0)
                                        @php  $nn++;@endphp
                                        <li>
                                            <div class="d-flex  align-items-center"
                                                 style="@if(($nn%2)==0) background: #eee; @endif padding:5px " >

                                                @php
                                                        $tunidades += $sucursal['cantidad'];
                                                        $tcosto+= $sucursal['costo'];
                                                @endphp

                                                <div class="flex-grow-1" style="width: 173px">
                                                        {{$sucursal['descrip']}}
                                                </div>

                                                <div class="flex-grow-1" class="" style="width: 73px;text-align: right;">
                                                    <div class="mb-0 listname" style="width: 73px; text-align: right; font-size: 12px;  ">
                                                        {{($sucursal['cantidad'] != 0  )?  $sucursal['cantidad'].'  ' : ''}}

                                                    </div>
                                                </div>
                                                <div class="flex-grow-1" style="width: 73px;text-align: right;">
                                                    <div class="mb-0 listname" style="width: 90px; text-align: right; font-size: 12px;  ">
                                                        {{($sucursal['costo'] != 0 )? '$'.number_format($sucursal['costo'],2,',','.'):''}}
                                                    </div>
                                                </div>

                                            </div>
                                        </li>
                                    @endif
                                @endforeach

                                <li  style="color: black; font-weight: bold" class="mb-5">
                                    <div class="d-flex  align-items-center">
                                        <div class="flex-grow-1" style="width: 173px">
                                            <div class="mb-0 listname" >  &nbsp;</div>
                                        </div>
                                        <div class="flex-grow-1" style="width: 73px;text-align: right;">
                                            <div class="mb-0 listname" style="width: 110px; text-align: center;  ">
                                                {{($tunidades != 0)?  $tunidades.'  ' : ''}}
                                            </div>
                                        </div>

                                        <div class="flex-grow-1" style="width: 73px;text-align: right;">
                                            <div class="mb-0 listname" style="width: 90px; text-align: right; ">
                                                {{($tcosto!= 0)? '$'.number_format($tcosto,2,',','.'):''}}
                                            </div>
                                        </div>
                                    </div>
                                </li>

                                @php
                                    $nn        = 1;
                                    $tunidades = 0;
                                    $tcosto= 0;
                                    //asdasd aqui las instancias
                                @endphp
                                <li  style="color: black; font-weight: bold" class="mb-2">
                                    <div class="d-flex  align-items-center">
                                        <div class="flex-grow-1" style="width: 173px">
                                            <div class="mb-0 listname" > &nbsp;INVENTARIO X INSTANCIA</div>
                                        </div>
                                        <div class="flex-grow-1" style="width: 73px;text-align: right;">
                                            <div class="mb-0 listname" style="width: 110px; text-align: center;  ">

                                            </div>
                                        </div>

                                        <div class="flex-grow-1" style="width: 73px;text-align: right;">
                                            <div class="mb-0 listname" style="width: 90px; text-align: right; ">
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                @foreach($arrayinstanci as $index => $inst)

                                        @php  $nn++;@endphp
                                    <li>
                                            <div class="d-flex  align-items-center"
                                                 style="@if(($nn%2)==0) background: #eee; @endif padding:5px " >

                                                @php
                                                        $tunidades += $inst['cantidad'];
                                                        $tcosto    += $inst['costo'];
                                                @endphp

                                                <div class="flex-grow-1" style="width: 173px">
                                                    <a href="javascript:;" data-codinst="{{$index}}"
                                                       class="mb-0 listname getexistencias" style=" font-size: 12px; ">
                                                        {{$inst['descrip']}}
                                                    </a>
                                                </div>

                                                <div class="flex-grow-1" class="" style="width: 73px;text-align: right;">
                                                    <div class="mb-0 listname" style="width: 73px; text-align: right; font-size: 12px;  ">
                                                        {{ $inst['cantidad'] }}
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1" style="width: 73px;text-align: right; display: flex; justify-content: space-evenly;">
                                                    <div class="mb-0 listname" style="width: 90px; text-align: right; font-size: 12px;  ">
                                                        {{'$'.number_format($inst['costo'],2,',','.')}}
                                                    </div>
                                                    <a href="javascriptL:;" data-codalte="{{$inst['codalte']}}"
                                                       data-bs-toggle="modal" data-bs-target="#viewprodcodaltemodal"
                                                       class="text-primary viewprodcodalte"><span class="bi bi-search me-2"></span></a>
                                                </div>

                                            </div>
                                        </li>

                                @endforeach


                                    <li  style="color: black; font-weight: bold" class="mb-2">
                                    <div class="d-flex  align-items-center">
                                        <div class="flex-grow-1" style="width: 173px">
                                            <div class="mb-0 listname" > &nbsp;</div>
                                        </div>
                                        <div class="flex-grow-1" style="width: 73px;text-align: right;">
                                            <div class="mb-0 listname" style="width: 110px; text-align: center;  ">
                                                {{($tunidades != 0)?  $tunidades.'  ' : ''}}
                                            </div>
                                        </div>

                                        <div class="flex-grow-1" style="width: 73px;text-align: right;">
                                            <div class="mb-0 listname" style="width: 90px; text-align: right; ">
                                                {{($tcosto!= 0)? '$'.number_format($tcosto,2,',','.'):''}}
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-7" id="existencontent">

        </div>
    </div>

    <div class="modal fade" id="viewprodcodaltemodal" aria-hidden="true" aria-labelledby="..." tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="titulorepventasucu">EXISTENCIAS POR DEPOSITO</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">  </button>
                </div>
                <div class="modal-body" id="contentviewprodcodalte">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">  CERRAR</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script src="{{ URL::asset('build/js/app.js') }}"></script>

    <script>
        $('.getexistencias').unbind('click').bind('click',function () {
            var codinst = $(this).attr('data-codinst');

            $('#existencontent').html('<button class="btn btn-outline-primary btn-load"><span class="d-flex align-items-center"><span class="spinner-border flex-shrink-0" role="status"> <span class="visually-hidden"> Cargando...</span> </span> <span class="flex-grow-1 ms-2">Cargando... </span> </span> </button>');
            $.ajax({
                type:'post',
                data:{codinst: (codinst)? codinst : '' },
                url:'/reporte/existen/php',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success:function(response) {
                    $('#existencontent').html(response);
                }
            });
        });


        $('.viewprodcodalte').unbind('click').bind('click',function () {

           var codalte   = $(this).attr('data-codalte');

            $('#contentviewprodcodalte').html('<button class="btn btn-outline-primary btn-load"><span class="d-flex align-items-center"><span class="spinner-border flex-shrink-0" role="status"> <span class="visually-hidden"> Cargando...</span> </span> <span class="flex-grow-1 ms-2">Cargando... </span> </span> </button>');

            $.ajax({
                type:'post',
                data:{codalte: (codalte)? codalte : ''},
                url:'/saprod/viewprodinstsanciascodalte',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success:function(response) {
                    $('#contentviewprodcodalte').html(response);
                }
            });
        });
    </script>
@endsection
