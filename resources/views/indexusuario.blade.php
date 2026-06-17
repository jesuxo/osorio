@extends('layouts.master')
@section('title')
    Inicio
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
    <div class="row">
        <div class=" col-lg-3  ">
            <div class="row  ">


                <div class="col-12">
                    <!-- card -->
                    <a class="card card-animate" href="/productos"   >
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div class="vr rounded bg-info opacity-50" style="width: 4px;"></div>
                                <div class="flex-grow-1 ms-3">
                                    <p class="text-uppercase fw-medium text-muted fs-14 text-truncate">INVENTARIO</p>
                                    <h6 class="  mb-3"> <span>PRODUCTOS</span> </h6>

                                </div>
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title bg-info-subtle text-info rounded fs-3">
                                        <i class="ph-storefront"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-12" style="display: none">
                    <!-- card -->
                    <div class="card card-animate">
                        <div class="card-body">
                            <a href="/existencias" class="d-flex justify-content-between">
                                <div class="vr rounded bg-primary opacity-50" style="width: 4px;"></div>
                                <div class="flex-grow-1 ms-3">
                                    <p class="text-uppercase fw-medium text-muted fs-14 text-truncate">Costo Inventario </p>
                                    <h4 class="fs-22 fw-semibold mb-3">   </h4>

                                </div>
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title bg-primary-subtle text-primary rounded fs-3">
                                        <i class="ph-sketch-logo"></i>
                                    </span>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-12" style="display: none">
                    <!-- card -->
                    <div class="card card-animate">
                        <div class="card-body">
                            <a href="/cxc" class="d-flex justify-content-between">
                                <div class="vr rounded bg-primary opacity-50" style="width: 4px;"></div>
                                <div class="flex-grow-1 ms-3">
                                    <p class="text-uppercase fw-medium text-muted fs-14 text-truncate">Cuentas x cobrar </p>
                                    <h4 class="fs-22 fw-semibold mb-3"><span >   </span> </h4>

                                </div>
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title bg-primary-subtle text-primary rounded fs-3">
                                        <i class="ph-currency-dollar-bold"></i>
                                    </span>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div class="col-lg-3">
            <div class="card card-height-100">
                <div class="card-header align-items-center d-flex">
                    <h4 class="card-title mb-0 flex-grow-1">Top Productos</h4>
                </div>

                <div class="card-body" data-simplebar style="max-height: 445px;">
                    @php
                        $porc      = 0;
                        $tantosprd = 0;
                        if(isset($topprod)){
                            foreach ($topprod as $top){
                                $tantosprd += $top->salidas;
                            }
                        }
                    @endphp
                    @if(isset($topprod))
                        @foreach($topprod as $top)
                            @php
                                if($tantosprd>0)
                                 $porc = ($top->salidas / $tantosprd) *100;
                            @endphp

                            <div class="mb-4">
                                <span class="badge badge-soft-dark float-end">{{ ($top->salidas+0) }}</span>
                                <h6 class="mb-2"> {{(isset($top->producto) and isset($top->producto->descrip))? $top->producto->descrip: $top}}</h6>
                                <div class="progress progress-sm" role="progressbar" aria-label="Success example"
                                     aria-valuenow="{{$porc}}" aria-valuemin="0" aria-valuemax="100">
                                    <div class="progress-bar bg-success bg-opacity-50 progress-bar-striped progress-bar-animated"
                                         style="width: {{$porc}}%"></div>
                                </div>
                            </div>
                        @endforeach
                    @endif

                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header align-items-center d-flex">
                    <h4 class="card-title mb-0 flex-grow-1">Productos recien actualizados</h4>
                    <div class="flex-shrink-0">
                        <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#searchModal"  onclick="focusbusqueda(); $('#search-options').change(); $('#search-dropdown').fadeIn() " class="btn btn-soft-info btn-sm">
                            <i class="ri-file-list-3-line align-middle"></i> Ver todos
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="table-responsive table-card">
                        <table class="table table-borderless table-centered align-middle table-nowrap mb-0">
                            <thead class="text-muted table-light">
                            <tr>
                                <th width="5%" scope="col">Codigo</th>
                                <th  width="70%"scope="col">Producto</th>

                                <th  width="10%"scope="col">Costo</th>
                                <th  width="10%"scope="col">Precio</th>

                                <th  width="5%"scope="col">Fecha Actualizado</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(isset($prdupdated) and count($prdupdated) > 0)
                                @foreach($prdupdated as $producto)
                                    <tr>
                                        <td>
                                            <!--<a href="product-overview" class="fw-medium link-primary">#00541</a>-->
                                            <a href="{{route('productos.edit',$producto->id)}}" class="fw-medium link-primary">{{$producto->codprod}}</a>
                                        </td>
                                        <td>
                                            <a href="{{route('productos.edit',$producto->id)}}" class="fw-medium link-primary">{{$producto->descrip}}</a>
                                        </td>
                                        <td align="right"> {{number_format($producto->preciod+$producto->preciod2,2,',','.')}}  </td>
                                        <td align="right"> {{number_format($producto->costod3,2,',','.')}}</td>
                                        <td>
                                            {{date("d/m/Y h:i a",strtotime($producto->updated_at))}}
                                        </td>
                                    </tr><!-- end tr -->
                                @endforeach
                            @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>



@endsection
@section('scripts')

    <!-- App js -->
    <script src="{{ URL::asset('build/js/app.js') }}"></script>

@endsection
