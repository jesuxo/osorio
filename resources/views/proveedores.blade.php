@extends('layouts.master')
@section('title')
   Clientes
@endsection
@section('css')
    <style>
        .btn-soft-light:hover, .codprovseleted{
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
    </style>
@endsection
@section('content')

    <div class="row">
        <div class="col-xxl-3">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0" id="addCategoryLabel">Buscar Proveedor </h6>
                </div>
            </div>
        </div>
        <div class="col-xxl-9">
            @if(isset($proveedor) and isset($proveedor->descrip))
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-center">
                            <h5 class="card-title mb-0 flex-grow-1">{{$proveedor->descrip}}</h5>
                            <div class="flex-shrink-0">
                                <p class="mb-0">C&eacute;dula: <b>{{$proveedor->id3}}</b></p>
                            </div>
                        </div>
                        @if($tab == 'tab1')
                            <div class="row">
                                <div class="col-lg-6 mb-3">
                                    <div class="table-responsive">
                                        <table class="table table-borderless table-sm mb-0">
                                            <tbody>
                                            @if(isset($proveedor->direc1) and strlen($proveedor->direc1) > 2)
                                                <tr bgcolor="#eee">
                                                    <td width="25%">
                                                        Direcci&oacute;n1
                                                    </td>
                                                    <td width="75%" class="fw-medium">
                                                        {{$proveedor->direc1}}
                                                    </td>
                                                </tr>
                                            @endif
                                            @if(isset($proveedor->direc2) and strlen($proveedor->direc2) > 2)
                                                <tr>
                                                    <td>
                                                        Direcci&oacute;n2
                                                    </td>
                                                    <td class="fw-medium">
                                                        {{$proveedor->direc2}}
                                                    </td>
                                                </tr>
                                            @endif
                                            @if(isset($proveedor->direc3) and strlen($proveedor->direc3) > 2)
                                                <tr>
                                                    <td>
                                                        Direcci&oacute;n3
                                                    </td>
                                                    <td class="fw-medium">
                                                        {{$proveedor->direc3}}
                                                    </td>
                                                </tr>
                                            @endif
                                            @if(isset($proveedor->email) and strlen($proveedor->email) > 2)
                                                <tr bgcolor="#eee">
                                                    <td>
                                                        Email
                                                    </td>
                                                    <td class="fw-medium">
                                                        {{$proveedor->email}}
                                                    </td>
                                                </tr>
                                            @endif
                                            @if(isset($proveedor->telef) and strlen($proveedor->telef) > 2)
                                                <tr>
                                                    <td>
                                                        Tel&eacute;fono
                                                    </td>
                                                    <td class="fw-medium">
                                                        {{$proveedor->telef}}
                                                    </td>
                                                </tr>
                                            @endif
                                            @if(isset($proveedor->movil) and strlen($proveedor->movil) > 2)
                                                <tr bgcolor="#eee">
                                                    <td>
                                                        Celular
                                                    </td>
                                                    <td class="fw-medium">
                                                        {{$proveedor->movil}}
                                                    </td>
                                                </tr>
                                            @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                            </div>
                        @endif
                    </div>
                    <div class="card-body">

                        <div class="d-flex align-items-center flex-wrap gap-3 mb-4">
                            <ul class="nav nav-pills flex-grow-1 mb-0" role="tablist">
                                <li class="nav-item ">
                                    <a class="nav-link {{ ($tab == 'tab1')? 'active': '' }}"   href="/proveedores/{{$proveedor->codprov}}/tab1" role="tab">
                                        Ultimas Compras
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <div class="tab-content">
                            @if($tab == 'tab1')
                                <div class="tab-pane active" id="facturas" role="tabpanel">
                                    <div class="card">

                                        <div class="card-body">
                                            <div class="table-responsive table-card mb-1">
                                                <table width="100%" border="0" class=" table align-middle table-nowrap ">
                                                    <tr bgcolor="#fff">
                                                        <td width="14%" height="30"align="center" class="tdline" >COMPRAS  </td>
                                                        <td width="3%" align="center" class="tdlineff" > FECHA</td>
                                                        <td width="3%" align="center" class="tdlineff" > DOC</td>
                                                        <td width="6%" align="center" class="tdlineff" > BS</td>
                                                        <td width="5%" align="center" class="tdlineff" > BS.T</td>
                                                        <td width="6%" align="center" class="tdlineff" > USD</td>
                                                        <td width="5%" align="center" class="tdlineff" > USD.T</td>
                                                        <td width="6%" align="center" class="tdlineff ocultarcop" > COP</td>
                                                        <td width="6%" align="center" class="tdlineff ocultarcopt" > COP.T</td>
                                                        <td width="5%" align="center" class="tdlineff ocultareur" > EUR</td>
                                                        <td width="5%" align="center" class="tdlineff" > ANTICIPOS </td>
                                                        <td width="5%" align="center" class="tdlineff" > CREDITO </td>
                                                        <td width="6%" align="center" class="tdlineff" > TOTAL USD</td>
                                                    </tr>

                                                    @php
                                                        $n           = 0;
                                                        $tcancele    =0;
                                                        $tcancelt    =0;
                                                        $tdolares    =0;
                                                        $ttransf     =0;
                                                        $tpesos      =0;
                                                        $tpeso_tranf =0;
                                                        $teuros      =0;
                                                        $tcredito    =0;
                                                        $tcancelaUSD =0;
                                                        $ttotalventa =0;
                                                    @endphp
                                                    @if(isset($listado))
                                                        @foreach($listado as $nrounico => $sucursal)
                                                            @php
                                                                $tcancele    += $listado[$nrounico]['cancele'];
                                                                $tcancelt    += $listado[$nrounico]['cancelt'];
                                                                $tdolares    += $listado[$nrounico]['dolares'];
                                                                $ttransf     += $listado[$nrounico]['transf'];
                                                                $tpesos      += $listado[$nrounico]['pesos'];
                                                                $tpeso_tranf += $listado[$nrounico]['peso_tranf'];
                                                                $teuros      += $listado[$nrounico]['euros'];
                                                                $tcredito    += $listado[$nrounico]['credito'];
                                                                $tcancelaUSD += $listado[$nrounico]['cancelaUSD'];
                                                                $ttotalventa += $listado[$nrounico]['totalventa'];
                                                            @endphp
                                                        <tr  >
                                                            <td height="30" align="left" >
                                                                <div style=" max-width: 99%;  width: 100%; height: 20px; overflow: hidden; font-size: 12px">   {{$listado[$nrounico]['cliente']}}</div>
                                                            </td>
                                                            <td align="right" ><a href="javascript:;"  data-bs-toggle="modal" data-bs-target="#documentModal" class="openDocumento" data-fksucu="{{ $listado[$nrounico]['fk_sucu'] }}" data-numerod="{{ $listado[$nrounico]['numerod'] }}" data-tipofac="{{ $listado[$nrounico]['tipofac'] }}"  > {{($listado[$nrounico]['fecha'] !='')? $listado[$nrounico]['fecha']    : ''}} </a> </td>
                                                            <td align="right" ><a href="javascript:;"  data-bs-toggle="modal" data-bs-target="#documentModal" class="openDocumento" data-fksucu="{{ $listado[$nrounico]['fk_sucu'] }}" data-numerod="{{ $listado[$nrounico]['numerod'] }}" data-tipofac="{{ $listado[$nrounico]['tipofac'] }}"   > {{($listado[$nrounico]['numerod'] !='')? $listado[$nrounico]['numerod']        : ''}} </a> </td>
                                                            <td align="right" >  {{($listado[$nrounico]['cancele']!=0)?number_format($listado[$nrounico]['cancele'],2,',','.') : ''}}</td>
                                                            <td align="right" >  {{($listado[$nrounico]['cancelt']!=0)?number_format($listado[$nrounico]['cancelt'],2,',','.') : ''}}</td>
                                                            <td align="right" >  {{($listado[$nrounico]['dolares']!=0)?number_format($listado[$nrounico]['dolares'],2,',','.') : ''}}</td>
                                                            <td align="right" >  {{($listado[$nrounico]['transf'] !=0)?number_format($listado[$nrounico]['transf'] ,2,',','.') : ''}}</td>
                                                            <td align="right" class=" ocultarcop" >  {{($listado[$nrounico]['pesos']  !=0)?number_format($listado[$nrounico]['pesos']  ,2,',','.') : ''}}</td>
                                                            <td align="right" class=" ocultarcopt" >  {{($listado[$nrounico]['peso_tranf']!=0)?number_format($listado[$nrounico]['peso_tranf'],2,',','.') : ''}}</td>
                                                            <td align="right" class=" ocultareur" >  {{($listado[$nrounico]['euros']!=0)?number_format($listado[$nrounico]['euros'],2,',','.') : ''}}</td>
                                                            <td align="right" class=" " >  {{($listado[$nrounico]['cancelaUSD']!=0)?number_format($listado[$nrounico]['cancelaUSD'],2,',','.') : ''}}</td>
                                                            <td align="right" >  {{($listado[$nrounico]['credito']!=0)?number_format($listado[$nrounico]['credito'],2,',','.') : ''}}</td>
                                                            <td align="right" >  {{($listado[$nrounico]['totalventa']!=0)?number_format($listado[$nrounico]['totalventa'],2,',','.') : ''}}</td>
                                                        </tr>
                                                        @php $n++; @endphp
                                                    @endforeach
                                                    @endif

                                                    <tr bgcolor="#eee" style=" display: none">
                                                        <td height="30"align="left" > TOTALES</td>
                                                        <td align="left" ></td>
                                                        <td align="left" ></td>
                                                        <td align="right" >{{ ($tcancele!=0)? number_format($tcancele,2,',','.') : ''}}     </td>
                                                        <td align="right" >{{ ($tcancelt!=0)?number_format($tcancelt,2,',','.'): ''}}        </td>
                                                        <td align="right" >{{ ($tdolares!=0)?number_format($tdolares,2,',','.'): ''}}       </td>
                                                        <td align="right" >{{ ($ttransf!=0)?number_format($ttransf,2,',','.'): ''}}         </td>
                                                        <td align="right" class=" ocultarcop" >{{ ($tpesos!=0)?number_format($tpesos,2,',','.'): ''}}           </td>
                                                        <td align="right" class=" ocultarcopt" >{{ ($tpeso_tranf!=0)?number_format($tpeso_tranf,2,',','.'): ''}} </td>
                                                        <td align="right" class=" ocultareur" >{{ ($teuros!=0)?number_format($teuros,2,',','.'): ''}}           </td>
                                                        <td align="right" >{{ ($tcancelaUSD!=0)?number_format($tcancelaUSD,2,',','.'): ''}}       </td>
                                                        <td align="right" >{{ ($tcredito!=0)?number_format($tcredito,2,',','.'): ''}}       </td>
                                                        <td align="right" >{{ ($ttotalventa!=0)?number_format($ttotalventa,2,',','.'): ''}} </td>
                                                    </tr>

                                                </table>
                                            </div>

                                        </div>
                                    </div>
                                </div>

                            @endif

                        </div>
                    </div>
                </div>
            @endif
        </div>

    </div>






@endsection
@section('scripts')

    <!-- App js -->
    <script src="{{ URL::asset('build/js/app.js') }}"></script>

    <script>

        $(document).ready(function() {

            $('#busqueda').select();

            $('.openDocumento').unbind('click').bind('click',function () {

                var fksucu   = $(this).attr('data-fksucu');
                var numerod  = $(this).attr('data-numerod');
                var tipofac  = $(this).attr('data-tipofac');

                $('#documentView').html('<button class="btn btn-outline-primary btn-load"><span class="d-flex align-items-center"><span class="spinner-border flex-shrink-0" role="status"> <span class="visually-hidden"> Cargando...</span> </span> <span class="flex-grow-1 ms-2">Cargando... </span> </span> </button>');
                $.ajax({
                    type:'post',
                    data:{tipofac: (tipofac)? tipofac : '', numerod: (numerod)? numerod : '', fksucu: (fksucu)? fksucu : '' },
                    url:'/openDoc',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success:function(response) {
                        $('#documentView').html(response);
                    }
                });

            });



        });
    </script>
@endsection
