@extends('layouts.master')
@section('title')
   Clientes
@endsection
@section('css')
    <style>
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
    </style>
@endsection
@section('content')

    <div class="row">
        <div class="col-xxl-3">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0" id="addCategoryLabel">Buscar Clientes </h6>
                </div>
                <div class="card-body">
                    <form action="/clientes" method="post" autocomplete="off" class="needs-validation createCategory-form" id="clientForm" novalidate>
                        @method('post')
                        @csrf
                        <input type="hidden" id="codclie" name="codclie" class="form-control" value="">
                        <div class="row">

                            <div class="col-xxl-12  ">
                                <div class="search-box mb-3">
                                    <input type="text" class="form-control search" id="busqueda" name="busqueda"value="{{(isset($busqueda))? $busqueda : ''}}"  required placeholder="Puede buscar por nombre, cedula ...">
                                    <i class="ri-search-line search-icon"></i>
                                </div>
                                <div class="invalid-feedback">Ingrese la descripci&oacute;n de la instancia</div>
                            </div>

                            <div class="col-xxl-12 col-lg-6">
                                @if($busqueda !='')
                                    <div class="accordion accordion-flush filter-accordion">
                                        <div class="card-body border-bottom p-0">
                                            @if(isset($clientes) and count($clientes)>0)
                                                <div>
                                                    <p class="text-muted fs-13 mb-3">Resultados para: {{$busqueda}}</p>
                                                    @foreach($clientes as $cli)
                                                        <a href="javascript:;" onclick="$('#codclie').val('{{$cli->codclie}}'); $('#clientForm').submit()"
                                                           class="card btn btn-soft-light  card-animate d-flex p-2 {{(isset($codclie) and $codclie !='' and $codclie == $cli->codclie)? 'codclieseleted' : ''}}
                                                       border-bottom border-bottom-dashed  cursor-pointer"
                                                           style="text-align: left" >
                                                            <div class="flex-grow-1">
                                                                <h5>{{$cli->descrip}}</h5>
                                                                <p class="text-muted mb-0">{{$cli->codclie}}</p>
                                                            </div>
                                                        </a>
                                                    @endforeach
                                                </div>
                                            @else
                                                <div>
                                                    <p class="text-muted fs-13 mb-3">No se encontraron clientes para la busqueda: <b>{{$busqueda}} </b> </p>
                                                    <a class="card btn btn-soft-light  card-animate d-flex p-2
                                                       border-bottom border-bottom-dashed  cursor-pointer"
                                                       style="text-align: left; display: none" >
                                                        <div class="flex-grow-1" href="#modalCliente" data-bs-toggle="modal">
                                                            <h6> +1  CLIENTE NUEVO </h6>
                                                        </div>
                                                    </a>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </form>

                    <form name="form2" id="form2" class="tablelist-form"   action="{{(isset($cliente->codclie) and $cliente->codclie !='')?route('updatecliente') : route('cliente.store')}}"
                          autocomplete="off" method="post">
                        @csrf
                        @method('POST')
                        <div class="modal fade" id="modalCliente" tabindex="-1" aria-labelledby="modalCliente" aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-scrollable ">
                                <div class="modal-content">
                                    <div class="modal-header px-4 pt-4">
                                        <h5 class="modal-title" id="exampleModalLabel">Informaci&oacute;n de cliente {{(isset($cliente->codclie) and $cliente->codclie !='')? $cliente->descrip :' nuevo' }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                                                id="close-modal"></button>
                                    </div>

                                    <div class="modal-body p-4">
                                        <div class="row">
                                            <div id="alert-error-msg" class="d-none alert alert-danger py-2"></div>
                                            <input type="hidden" id="id-field">

                                            <div class="col-lg-6">
                                                <div class="mb-2">
                                                    <label for="cliente-cod-field" class="form-label">C&oacute;digo</label>
                                                    <input type="text" id="cliente-cod-field" name="codclie" value="{{(isset($cliente->codclie) and $cliente->codclie !=='')? $cliente->codclie : ((isset($busqueda) and $busqueda!='')? $busqueda:'')}}" class="form-control"
                                                           placeholder="Ej: 0000001" readonly required>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="mb-2">
                                                    <label for="id3-cod-field" class="form-label">C&eacute;dula / RIF</label>
                                                    <input type="text" id="id3-cod-field" name="id3" class="form-control" value="{{(isset($cliente->codclie) and $cliente->codclie !== '') ? $cliente->id3 : ((isset($busqueda) and $busqueda != '') ? 'V-' . $busqueda : '')}}"
                                                           placeholder="Ej: V-12312311" required>
                                                </div>
                                            </div>
                                            <div class="col-lg-12">
                                                <div class="mb-2">
                                                    <label for="descrip-name-field" class="form-label">Nombre</label>
                                                    <input type="text" id="descrip-name-field" value="{{(isset($cliente->descrip))? $cliente->descrip : ''}}" name="descrip" class="form-control"
                                                           placeholder="Ej: Pedro Perez" required>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="mb-2">
                                                    <label for="email-field" class="form-label">  Email</label>
                                                    <input type="email" name="email" id="email-field" class="form-control" value="{{(isset($cliente->codclie))? $cliente->email: ''}}" placeholder="Ej: correo@email.com">
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="mb-2">
                                                    <label for="clase-field" class="form-label">  Clase</label>
                                                    <input type="text" name="clase" id="clase-field" value="{{(isset($cliente->codclie))? $cliente->clase : ''}}" class="form-control" placeholder="" >
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="mb-2">
                                                    <label for="phone-field" class="form-label">Tel&eacute;fono</label>
                                                    <input type="text" name="telef" id="phone-field" class="form-control" value="{{(isset($cliente->codclie))? $cliente->telef: ''}}" placeholder="Ej: 0414-12345678"
                                                           required>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="mb-2">
                                                    <label for="represent-field" class="form-label">  Representante</label>
                                                    <input type="text" name="represent" id="represent-field" value="{{(isset($cliente->codclie))? $cliente->represent : ''}}" class="form-control" placeholder="" >
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="mb-2">
                                                    <label for="movil-field" class="form-label">Celular</label>
                                                    <input type="text" name="movil" id="movil-field" class="form-control" value="{{(isset($cliente->codclie))? $cliente->movil :''}}" placeholder="Ej: 5841412345678">
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="mb-2">
                                                    <label for="fax-field" class="form-label">Fax</label>
                                                    <input type="text" name="fax" id="fax-field" class="form-control" value="{{(isset($cliente->codclie))? $cliente->fax :''}}" placeholder="">
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class=" ">
                                                    <label for="direc1-field" class="form-label">Direcci&oacute;n1</label>
                                                    <input type="text" name="direc1" id="direc1-field" class="form-control" value="{{(isset($cliente->codclie))? $cliente->direc1 :''}}" placeholder="">
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class=" ">
                                                    <label for="direc2-field" class="form-label">Direcci&oacute;n2</label>
                                                    <input type="text" name="direc2" id="direc2-field" class="form-control" value="{{(isset($cliente->codclie))? $cliente->direc2 :''}}" placeholder="" >
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class=" ">
                                                    <label for="direc3-field" class="form-label">Direcci&oacute;n3</label>
                                                    <input type="text" name="direc3" id="direc3-field" class="form-control" value="{{(isset($cliente->codclie))? $cliente->direc3 :''}}" placeholder="" >
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <div class="hstack gap-2 justify-content-end">
                                            <button type="button" class="btn btn-ghost-danger" data-bs-dismiss="modal">Cerrar</button>
                                            <button type="button" onclick="$('#form2').submit()" class="btn btn-success">{{(isset($cliente->codclie) and $cliente->codclie !== '') ? 'Modificar': 'Crear'}}</button>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-xxl-9">
            @if(isset($cliente) and isset($cliente->descrip))
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-center">
                            <h5 class="card-title mb-0 flex-grow-1">{{$cliente->descrip}}</h5>
                            <div class="flex-shrink-0">
                                <p class="mb-0">C&eacute;dula: <b>{{$cliente->id3}}</b></p>
                            </div>
                            <div class="flex-shrink-0 " style="margin-left: 20px;  ">
                                <a  class="btn btn-primary"  href="#modalCliente" data-bs-toggle="modal" >Modificar</a>
                            </div>
                        </div>
                        @if($tab == 'tab1')
                            <div class="row">
                                <div class="col-lg-6 mb-3">
                                    <div class="table-responsive">
                                        <table class="table table-borderless table-sm mb-0">
                                            <tbody>
                                            @if(isset($cliente->direc1) and strlen($cliente->direc1) > 2)
                                                <tr bgcolor="#eee">
                                                    <td width="25%">
                                                        Direcci&oacute;n1
                                                    </td>
                                                    <td width="75%" class="fw-medium">
                                                        {{$cliente->direc1}}
                                                    </td>
                                                </tr>
                                            @endif
                                            @if(isset($cliente->direc2) and strlen($cliente->direc2) > 2)
                                                <tr>
                                                    <td>
                                                        Direcci&oacute;n2
                                                    </td>
                                                    <td class="fw-medium">
                                                        {{$cliente->direc2}}
                                                    </td>
                                                </tr>
                                            @endif
                                            @if(isset($cliente->direc3) and strlen($cliente->direc3) > 2)
                                                <tr>
                                                    <td>
                                                        Direcci&oacute;n3
                                                    </td>
                                                    <td class="fw-medium">
                                                        {{$cliente->direc3}}
                                                    </td>
                                                </tr>
                                            @endif
                                            @if(isset($cliente->email) and strlen($cliente->email) > 2)
                                                <tr bgcolor="#eee">
                                                    <td>
                                                        Email
                                                    </td>
                                                    <td class="fw-medium">
                                                        {{$cliente->email}}
                                                    </td>
                                                </tr>
                                            @endif
                                            @if(isset($cliente->telef) and strlen($cliente->telef) > 2)
                                                <tr>
                                                    <td>
                                                        Tel&eacute;fono
                                                    </td>
                                                    <td class="fw-medium">
                                                        {{$cliente->telef}}
                                                    </td>
                                                </tr>
                                            @endif
                                            @if(isset($cliente->movil) and strlen($cliente->movil) > 2)
                                                <tr bgcolor="#eee">
                                                    <td>
                                                        Celular
                                                    </td>
                                                    <td class="fw-medium">
                                                        {{$cliente->movil}}
                                                    </td>
                                                </tr>
                                            @endif
                                            @if(isset($cliente->codzona) and strlen($cliente->codzona) > 1)
                                                <tr>
                                                    <td>
                                                        Zona
                                                    </td>
                                                    <td class="fw-medium">
                                                        {{$cliente->codzona}}
                                                    </td>
                                                </tr>
                                            @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <div class="table-responsive">
                                        <table class="table table-borderless table-sm mb-0">
                                            <tbody>
                                            @if(isset($cliente->tipopvp) and $cliente->tipopvp > 0)
                                                <tr bgcolor="#eee">
                                                    <td  width="25%" >
                                                        Tipo Precio
                                                    </td>
                                                    <td  width="75%" class="fw-medium">
                                                        PRECIO{{$cliente->tipopvp}}
                                                    </td>
                                                </tr>
                                            @endif
                                            @if(isset($cliente->codvend)  and strlen($cliente->codvend) > 2)
                                                <tr>
                                                    <td>
                                                        Vendedor Asig:
                                                    </td>
                                                    <td class="fw-medium">
                                                        {{$cliente->codvend}}
                                                    </td>
                                                </tr>
                                            @endif
                                            @if(isset($cliente->clase)  and strlen($cliente->clase) > 2)
                                                <tr bgcolor="#eee">
                                                    <td>
                                                        Clase
                                                    </td>
                                                    <td class="fw-medium">
                                                        {{$cliente->clase}}
                                                    </td>
                                                </tr>
                                            @endif
                                            @if(isset($cliente->represent)  and strlen($cliente->represent) > 2)
                                                <tr>
                                                    <td>
                                                        Representante
                                                    </td>
                                                    <td class="fw-medium">
                                                        {{$cliente->represent}}
                                                    </td>
                                                </tr>
                                            @endif
                                            @if(isset($cliente->fax)  and strlen($cliente->fax) > 2)
                                                <tr  bgcolor="#eee">
                                                    <td>
                                                        Fax
                                                    </td>
                                                    <td class="fw-medium">
                                                        {{$cliente->fax}}
                                                    </td>
                                                </tr>
                                            @endif
                                            @if(isset($cliente->Estado)  and $cliente->Estado >0)
                                                <tr>
                                                    <td>
                                                        Estado
                                                    </td>
                                                    <td class="fw-medium">
                                                        {{$cliente->Estado}}
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
                                    <a class="nav-link {{ ($tab == 'tab1')? 'active': '' }}"   href="/clientes/{{$cliente->codclie}}/tab1" role="tab">
                                        Ultimas Facturas
                                    </a>
                                </li>
                                <li class="nav-item " style="display: none">
                                    <a class="nav-link {{ ($tab == 'tab2')? 'active': '' }}"   href="/clientes/{{$cliente->codclie}}/tab2" role="tab">
                                        Estado de cuenta
                                    </a>
                                </li>
                                <li class="nav-item " >
                                    <a class="nav-link {{ ($tab == 'tab3')? 'active': '' }}"   href="/clientes/{{$cliente->codclie}}/tab3" role="tab">
                                        Letra de Cambio
                                    </a>
                                </li>
                                <li class="nav-item " >
                                    <a class="nav-link {{ ($tab == 'tab4')? 'active': '' }}"   href="/clientes/{{$cliente->codclie}}/tab4" role="tab">
                                        Pagar&eacute;
                                    </a>
                                </li>
                                <li class="nav-item " >
                                    <a class="nav-link {{ ($tab == 'tab5')? 'active': '' }}"   href="/clientes/{{$cliente->codclie}}/tab5" role="tab">
                                        Financiamientos
                                    </a>
                                </li>
                            </ul>
                            @if($tab == 'tab3')
                                <div class="flex-shrink-0" >
                                    <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#newLetra"
                                       class="btn btn-success">Crear Letra</a>
                                </div>
                            @endif
                            @if($tab == 'tab4')
                                <div class="flex-shrink-0" >
                                    <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#newPagare"
                                       class="btn btn-success">Crear Pagar&eacute;</a>
                                </div>
                            @endif

                        </div>
                        <!-- Tab panes -->
                        <div class="tab-content">
                            @if($tab == 'tab1')
                                <div class="tab-pane active" id="facturas" role="tabpanel">
                                    <div class="card">

                                        <div class="card-body">
                                            <div class="table-responsive table-card mb-1">
                                                <table width="100%" border="0" class=" table align-middle table-nowrap ">
                                                    <tr bgcolor="#fff">
                                                        <td width="14%" height="30"align="center" class="tdline" >VENTAS  </td>
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

                                                    @php $n=0;
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
                                                            <td height="30"align="left" >
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
                                <script>
                                    @php if($tpesos == 0){ @endphp
                                    $('.ocultarcop').hide();
                                    @php }
                                 if($tpeso_tranf == 0){ @endphp
                                    $('.ocultarcopt').hide();
                                    @php }
                                if($teuros == 0){ @endphp
                                    $('.ocultareur').hide();
                                    @php }  @endphp
                                </script>
                            @endif

                            @if($tab == 'tab2')
                                <div class="tab-pane active" id="edocuenta" role="tabpanel">
                                    <div class="card">

                                        <div class="card-body"   style="height: 400px; overflow: auto" >
                                            <div class="table-responsive table-card mb-1" >
                                                <table class="table align-middle table-nowrap" id="">
                                                    <tr style="position: sticky; top: 0; ">
                                                        <td class="tdlineff" width="5%"  align="center">Fecha</td>
                                                        <td class="tdlineff" width="10%" align="center">Doc</td>
                                                        <td class="tdline"   width="45%" align="center">Observacion</td>
                                                        <td class="tdlineff" width="10%" align="center">Debitos</td>
                                                        <td class="tdlineff" width="10%" align="center">Creditos</td>
                                                        <td class="tdlineff" width="10%" align="center">Anticipos</td>
                                                        <td class="tdlineff" width="10%" align="center">Saldo</td>
                                                    </tr>
                                                    @php  $saldo = 0; @endphp
                                                    @if(isset($listadoc))
                                                        @foreach($listadoc as $doc)
                                                            <tr style="font-size: 11px">
                                                                <td align="center"> {{$doc['fecha']}}</td>
                                                                <td align="center"> {{$doc['numerod']}}</td>
                                                                <td align="left">
                                                                    @if($doc['tipocxc'] == '20')
                                                                        {{$doc['document']}}
                                                                    @else
                                                                        @if($doc['tipocxc'] == '10')
                                                                            FACTURA [Nro {{$doc['document']}}]
                                                                        @else
                                                                            {{$doc['document']}}
                                                                        @endif
                                                                    @endif
                                                                </td>
                                                                <td align="right">
                                                                    @php
                                                                        if ($doc['tipocxc'] == '10' or $doc['tipocxc'] == '25'  or $doc['tipocxc'] == '20') {
                                                                            if($doc['montodolares']>0){
                                                                                    echo number_format($doc['montodolares'],2,',','.');
                                                                                    if($doc['document'] != 'DIF.CAMBIARIO')
                                                                                        $saldo += $doc['montodolares'];
                                                                            }else{
                                                                                if($doc['tasadolar']){
                                                                                    echo number_format($doc['monto']/$doc['tasadolar'], 2, ',', '.');
                                                                                    if($doc['document'] != 'DIF.CAMBIARIO')
                                                                                        $saldo += $doc['monto']/$doc['tasadolar'];
                                                                                }

                                                                            }
                                                                       }
                                                                    @endphp
                                                                </td>
                                                                <td align="right">
                                                                    @php
                                                                        if(($doc['tipocxc']!='10' and $doc['tipocxc']!='50')  and   $doc['tipocxc']!='25' and   $doc['tipocxc']!='20'){

                                                                            if($doc['montodolares']>0){
                                                                                echo number_format($doc['montodolares'],2,',','.');
                                                                                $saldo-=$doc['montodolares'];
                                                                            }else{
                                                                                if($doc['tasadolar']){
                                                                                    echo number_format($doc['monto']/$doc['tasadolar'], 2, ',', '.');
                                                                                    $saldo -= $doc['monto']/$doc['tasadolar'];
                                                                                }
                                                                            }
                                                                        }

                                                                    @endphp
                                                                </td>
                                                                <td align="right"></td>
                                                                <td align="right">
                                                                    {{number_format($saldo,2,',','')}}
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    @endif
                                                </table>
                                                <input type="text" name="textfield" id="dfocus" style="width:1px; height:1px; opacity:0"  />
                                                <script> $("#dfocus").focus(); </script>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if($tab == 'tab3')
                                <div class="tab-pane active" id="newLetras" role="tabpanel">
                                    <div class="card">
                                        <div class="card-body"   style="height: 400px; overflow: auto" >
                                            <div class="table-responsive table-card mb-1" >
                                                <table width="100%" border="0" class=" table align-middle table-nowrap ">
                                                    <tr bgcolor="#fff">
                                                        <td width="9%" height="36" align="center"  class="tdlineff" >Fecha</td>
                                                        <td width="13%" class="tdlineff" align="center" >Fecha Pagar</td>
                                                        <td width="19%" class="tdlineff" align="center" >Monto</td>
                                                        <td width="18%" class="tdlineff" align="center" >Nombre Avalista </td>
                                                        <td width="14%" class="tdlineff" align="center" >Ciudad Pago</td>
                                                        <td width="7%"  class="tdlineff" align="center" >Impresión</td>
                                                        <td width="3%" class="tdlineff"  align="center">&nbsp;</td>

                                                    </tr>
                                                    @foreach($letrasdecambio as $letra)
                                                        <tr  >
                                                            <td align="center">{{$letra->fechaformat}}</td>
                                                            <td align="center"> {{$letra->fechapagarformat}} </td>
                                                            <td height="34" align="right"> {{number_format($letra->monto,2,',','.')}}</td>
                                                            <td align="center">{{$letra->nombreavalista}} </td>
                                                            <td align="center">{{$letra->ciudadpago}} </td>
                                                            <td align="center">
                                                                <a  onclick="urlprintletra('/letra/view/{{$cliente->codclie}}/{{ $letra->id }}')" href="javascript:;"  data-bs-toggle="modal" data-bs-target="#openLetra"    ><i class="bi bi-printer"></i></a>
                                                            </td>
                                                            <td align="center">
                                                                <a  href="/letra/delete/{{$cliente->codclie}}/{{ $letra->id }}"   ><i class="bi bi-trash"></i></a>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if($tab == 'tab4')
                                <div class="tab-pane active" id="newPagares" role="tabpanel">
                                    <div class="card">
                                        <div class="card-body"   style="height: 400px; overflow: auto" >
                                            <div class="table-responsive table-card mb-1" >
                                                <table width="100%" border="0" class=" table align-middle table-nowrap ">
                                                    <tr bgcolor="#fff">
                                                        <td width="9%" height="36" align="center"  class="tdlineff" >Fecha</td>
                                                        <td width="13%" class="tdlineff" align="center" >Fecha Pagar</td>
                                                        <td width="19%" class="tdlineff" align="center" >Monto</td>
                                                        <td width="18%" class="tdlineff" align="center" >Nombre Avalista </td>
                                                        <td width="7%"  class="tdlineff" align="center" >Impresión</td>
                                                        <td width="3%" class="tdlineff"  align="center">&nbsp;</td>

                                                    </tr>
                                                    @foreach($pagares as $pagare)
                                                        <tr  >
                                                            <td align="center">{{$pagare->fechaformat}}</td>
                                                            <td align="center"> {{$pagare->fechapagarformat}} </td>
                                                            <td height="34" align="right"> {{number_format($pagare->monto,2,',','.')}}</td>
                                                            <td align="center">{{$pagare->nombreavalista}} </td>
                                                            <td align="center">
                                                                <a  onclick="urlprintpagare('/pagare/view/{{$cliente->codclie}}/{{ $pagare->id }}')" href="javascript:;"  data-bs-toggle="modal" data-bs-target="#openPagare"    ><i class="bi bi-printer"></i></a>
                                                            </td>
                                                            <td align="center">
                                                                <a  href="/pagare/delete/{{$cliente->codclie}}/{{ $pagare->id }}"   ><i class="bi bi-trash"></i></a>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if($tab == 'tab5')
                                <div class="tab-pane active" id="newPagares" role="tabpanel">
                                    <div class="card">
                                        <div class="card-body"   style="height: 400px; overflow: auto" >
                                            <div class="table-responsive table-card mb-1" >
                                                <table width="100%" border="0" class=" table align-middle table-nowrap ">
                                                    <tr bgcolor="#fff">
                                                        <td width="8%" height="36"      align="center"  class="tdlineff" > </td>
                                                        <td width="8%" height="36"      align="center"  class="tdlineff" >Reserva</td>
                                                        <td width="8%" height="36"      align="center"  class="tdlineff" >Status</td>
                                                        <td width="8%" class="tdlineff" align="center">Fecha</td>
                                                        <td width="8%" class="tdlineff" align="center" >Monto Cuotas</td>
                                                        <td width="8%" class="tdlineff" align="center" >Cant Cuotas</td>
                                                        <td width="8%" class="tdlineff" align="center" >Valor Financ. </td>
                                                        <td width="8%" class="tdlineff" align="center" >Inicial Financ.</td>
                                                        <td width="8%" class="tdlineff" align="center" >Porc. Inicial</td>
                                                        <td width="8%" class="tdlineff" align="center" >Monto Financ.</td>
                                                        <td width="8%" class="tdlineff" align="center" >Costo Financ.</td>
                                                        <td width="8%" class="tdlineff" align="center" >Porc. Costo Financ.</td>

                                                    </tr>
                                                    @foreach($financiamientos as $financiamiento)
                                                        <tr>
                                                            <td align="center">
                                                                <a  onclick="urlprintfinanciamiento('/financiamiento/view/{{ $financiamiento->id }}')" href="javascript:;"
                                                                    data-bs-toggle="modal" data-bs-target="#openFinanciamiento"    ><i class="bi bi-printer"></i>
                                                                </a>
                                                            </td>
                                                            <td align="center">
                                                                <a  onclick="urlprintreserva('/reserva/view/{{ $financiamiento->id }}')" href="javascript:;"
                                                                    data-bs-toggle="modal" data-bs-target="#openReserva"    ><i class="bi bi-printer"></i>
                                                                </a>
                                                            </td>
                                                            <td align="center">
                                                                <a href="{{route('financiamientos',['codclie' => $financiamiento->codclie, 'numerod' => $financiamiento->numerod])}}">
                                                                    {{$financiamiento->status.'/'.$financiamiento->cantcuotas}}
                                                                </a>
                                                            </td>
                                                            <td align="center">{{$financiamiento->fecha}}                                   </td>
                                                            <td align="center"> {{number_format($financiamiento->cuota,2,',','.')}}         </td>
                                                            <td height="34" align="center"> {{$financiamiento->cantcuotas}}                 </td>
                                                            <td align="center">{{number_format($financiamiento->valorfinancia,2,',','.')}}  </td>
                                                            <td align="center">{{number_format($financiamiento->inicialfinancia,2,',','.')}}</td>
                                                            <td align="center">{{number_format($financiamiento->porcinicial,2,',','.')}} %  </td>
                                                            <td align="center">{{number_format($financiamiento->saldofinancia,2,',','.')}}  </td>
                                                            <td align="center">{{number_format($financiamiento->costofinancia,2,',','.')}}  </td>
                                                            <td align="center">{{number_format($financiamiento->porccostofi,2,',','.')}} %  </td>

                                                        </tr>
                                                    @endforeach
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

    <div class="modal fade" id="documentModal" aria-hidden="true" aria-labelledby="..." tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-scrollable" >
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" > </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    </button>
                </div>
                <div class="modal-body  pt-0" id="documentView" >

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">  CERRAR</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="openLetra" aria-hidden="true" aria-labelledby="..." tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-scrollable" >
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" > </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    </button>
                </div>
                <div class="modal-body  pt-0"  >
                    <iframe src="" width="100%" height=100%" id="iframeletra" style="min-height: 500px; width: 100%" frameborder="0" allowtransparency="true"></iframe>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">  CERRAR</button>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="openFinanciamiento" aria-hidden="true" aria-labelledby="..." tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-scrollable" >
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" > </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    </button>
                </div>
                <div class="modal-body  pt-0"    >
                    <iframe src="" width="100%" height=100%" id="iframefinanciamiento" style="min-height: 500px; width: 100%" frameborder="0" allowtransparency="true"></iframe>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">  CERRAR</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="openReserva" aria-hidden="true" aria-labelledby="..." tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-scrollable" >
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" > </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    </button>
                </div>
                <div class="modal-body  pt-0"    >
                    <iframe src="" width="100%" height=100%" id="iframereserva" style="min-height: 500px; width: 100%" frameborder="0" allowtransparency="true"></iframe>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">  CERRAR</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="openPagare" aria-hidden="true" aria-labelledby="..." tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-scrollable" >
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" > </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    </button>
                </div>
                <div class="modal-body  pt-0"    >
                    <iframe src="" width="100%" height=100%" id="iframepagare" style="min-height: 500px; width: 100%" frameborder="0" allowtransparency="true"></iframe>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">  CERRAR</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="newLetra" aria-hidden="true" aria-labelledby="..." tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-scrollable" >
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" > </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    </button>
                </div>
                <div class="modal-body  pt-0"  >
                    <form name="form22" id="form22" class="tablelist-form"
                          action="{{route('cliente.newletra')}}"
                          autocomplete="off" method="post">
                        @csrf
                        @method('POST')
                        <div class="modal-body p-4">
                            <div class="row">
                                <div id="alert-error-msg" class="d-none alert alert-danger py-2"></div>
                                <input type="hidden" id="id-field">

                                <div class="col-lg-6">
                                    <div class="mb-2">
                                        <label for="cliente-cod-field" class="form-label">C&oacute;digo Cliente</label>
                                        <input type="text" id="cliente-cod-field" name="codclie"
                                               value="{{(isset($cliente->codclie) and $cliente->codclie !=='')? $cliente->codclie : '' }}" class="form-control"
                                                readonly required>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-2">
                                        <label  for="id3-cod-field" class="form-label">Cliente</label>
                                        <input type="text" id="id3-cod-field" name="id3" class="form-control" value="{{(isset($cliente->codclie) and $cliente->codclie !== '') ? $cliente->descrip :    '' }}"
                                               readonly required>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="mb-2">
                                        <label for="nombreavalista-name-field" class="form-label">Nombre Avalista(opcional)</label>
                                        <input type="text" id="nombreavalista-name-field" value="" name="nombreavalista" class="form-control"
                                               placeholder="Ej: Pedro Perez" required>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="mb-2">
                                        <label for="email-field" class="form-label">  C&eacute;dula Avalista (opcional)</label>
                                        <input type="text" name="cedulaavalista" id="email-field" class="form-control" value="" placeholder="Ej: V2777222">
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="mb-2">
                                        <label for="domicilioavalista-field" class="form-label">  Domicilio Avalista (opcional)</label>
                                        <input type="text" name="domicilioavalista" id="domicilioavalista-field" value="" class="form-control" placeholder="" >
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="mb-2">
                                        <label for="ciudadpago-field" class="form-label">Lugar de pago</label>
                                        <input type="text" name="ciudadpago" id="ciudadpago-field" class="form-control" value="" placeholder="Ej: La Fria"
                                               required>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-2">
                                        <label for="monto-field" class="form-label">  Monto del pagaré:</label>
                                        <input type="number" step="0.001" min="0.00001" max="5000000.00" name="monto" id="monto-field" value="" class="form-control" placeholder="" >
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class=" ">
                                        <label for="fechapagar-field" class="form-label">Fecha limite que debe pagar</label>
                                        <input name="fechapagar" id="fechapagar-field"   class="  form-control    " value=""
                                                 min="2025-01-01" max="2118-12-31" forma
                                                 type="date" id="fechapagar" name="fechapagar"
                                                 style=" width:89%"    />
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="modal-footer">
                            <div class="hstack gap-2 justify-content-end">
                                <button type="button" class="btn btn-ghost-danger" data-bs-dismiss="modal">Cerrar</button>
                                <button type="button" onclick="$('#form22').submit()" class="btn btn-success">Crear Letra</button>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>

    <div class="modal fade" id="newPagare" aria-hidden="true" aria-labelledby="..." tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-scrollable" >
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" > </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    </button>
                </div>
                <div class="modal-body  pt-0"  >
                    <form name="form222" id="form222" class="tablelist-form"
                          action="{{route('cliente.newpagare')}}"
                          autocomplete="off" method="post">
                        @csrf
                        @method('POST')
                        <div class="modal-body p-4">
                            <div class="row">
                                <div id="alert-error-msg" class="d-none alert alert-danger py-2"></div>
                                <input type="hidden" id="id-field">

                                <div class="col-lg-6">
                                    <div class="mb-2">
                                        <label for="cliente-cod-field" class="form-label">C&oacute;digo Cliente</label>
                                        <input type="text" id="cliente-cod-field" name="codclie"
                                               value="{{(isset($cliente->codclie) and $cliente->codclie !=='')? $cliente->codclie : '' }}" class="form-control"
                                                readonly required>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-2">
                                        <label  for="id3-cod-field" class="form-label">Cliente</label>
                                        <input type="text" id="id3-cod-field" name="id3" class="form-control" value="{{(isset($cliente->codclie) and $cliente->codclie !== '') ? $cliente->descrip :    '' }}"
                                               readonly required>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="mb-2">
                                        <label for="nombreavalista-name-field" class="form-label">Nombre Avalista(opcional)</label>
                                        <input type="text" id="nombreavalista-name-field" value="" name="nombreavalista" class="form-control"
                                               placeholder="Ej: Pedro Perez" required>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="mb-2">
                                        <label for="email-field" class="form-label">  C&eacute;dula Avalista (opcional)</label>
                                        <input type="text" name="cedulaavalista" id="email-field" class="form-control" value="" placeholder="Ej: V2777222">
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="mb-2">
                                        <label for="domicilioavalista-field" class="form-label">  Domicilio Avalista (opcional)</label>
                                        <input type="text" name="domicilioavalista" id="domicilioavalista-field" value="" class="form-control" placeholder="" >
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-2">
                                        <label for="monto-field" class="form-label">  Monto del pagaré:</label>
                                        <input type="number" step="0.001" min="0.00001" max="5000000.00" name="monto" id="monto-field" value="" class="form-control" placeholder="" >
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class=" ">
                                        <label for="fechapagar-field" class="form-label">Fecha limite que debe pagar</label>
                                        <input name="fechapagar" id="fechapagar-field"   class="  form-control    " value=""
                                                 min="2025-01-01" max="2118-12-31" forma
                                                 type="date" id="fechapagar" name="fechapagar"
                                                 style=" width:89%"    />
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="modal-footer">
                            <div class="hstack gap-2 justify-content-end">
                                <button type="button" class="btn btn-ghost-danger" data-bs-dismiss="modal">Cerrar</button>
                                <button type="button" onclick="$('#form222').submit()" class="btn btn-success">Crear Pagar&eacute;</button>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>

@endsection
@section('scripts')

    <!-- App js -->
    <script src="{{ URL::asset('build/js/app.js') }}"></script>

    <script>

        var urlletra          = '';
        var urlpagare         = '';
        var urlreserva        = '';
        var urlfinanciamiento = '';

        function urlprintreserva(url){
            $("#iframereserva").hide().html('');
            urlreserva = url;
        }

        function urlprintfinanciamiento(url){
            $("#iframefinanciamiento").hide().html('');
            urlfinanciamiento = url;
        }

        function urlprintletra(url){
            $("#iframeletra").hide().html('');
            urlletra = url;
        }

        function urlprintpagare(url){
            $("#iframepagare").hide().html('');
            urlpagare = url;
        }

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

            $('#openLetra').on('shown.bs.modal',function(){
                $("#iframeletra").contents().find("body").html('');
                $(this).find('iframe').attr('src',urlletra);
                $("#iframeletra").show()
            })

            $('#openPagare').on('shown.bs.modal',function(){
                $("#iframepagare").contents().find("body").html('');
                $(this).find('iframe').attr('src',urlpagare);
                $("#iframepagare").show()
            })

            $('#openFinanciamiento').on('shown.bs.modal',function(){
                $("#iframefinanciamiento").contents().find("body").html('');
                $(this).find('iframe').attr('src',urlfinanciamiento);
                $("#iframefinanciamiento").show()
            })

            $('#openReserva').on('shown.bs.modal',function(){
                $("#iframereserva").contents().find("body").html('');
                $(this).find('iframe').attr('src',urlreserva);
                $("#iframereserva").show()
            })

        });
    </script>
@endsection
