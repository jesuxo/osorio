@extends('layouts.master')
@section('title')
    Proveedores
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
            color: white !important;
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
        .monto-pendiente {
            color: #f59e0b;
            font-weight: bold;
        }
        .monto-conciliado {
            color: #10b981;
            font-weight: bold;
        }
    </style>
@endsection
@section('content')

    <div class="row">
        <div class="col-xxl-3">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0" id="addCategoryLabel">Buscar Proveedores</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('proveedores.index') }}" method="get" autocomplete="off" class="needs-validation" id="proveedorForm">
                        <input type="hidden" id="codprov" name="codprov" value="">
                        <div class="row">
                            <div class="col-xxl-12">
                                <div class="search-box mb-3">
                                    <input type="text" class="form-control search" id="busqueda" name="busqueda"
                                           value="{{ $busqueda ?? '' }}" required
                                           placeholder="Puede buscar por nombre, RIF, teléfono...">
                                    <i class="ri-search-line search-icon"></i>
                                </div>
                                <div class="invalid-feedback">Ingrese el nombre o código del proveedor</div>
                            </div>

                            <div class="col-xxl-12 col-lg-6">
                                @if($busqueda != '')
                                    <div class="accordion accordion-flush filter-accordion">
                                        <div class="card-body border-bottom p-0">
                                            @if(isset($proveedores) and count($proveedores)>0)
                                                <div>
                                                    <p class="text-muted fs-13 mb-3">Resultados para: {{$busqueda}}</p>
                                                    @foreach($proveedores as $prov)
                                                        <a href="javascript:;" onclick="$('#codprov').val('{{$prov->codprov}}'); $('#proveedorForm').submit()"
                                                           class="card btn btn-soft-light card-animate d-flex p-2
                                                           {{(isset($codprov) and $codprov != '' and $codprov == $prov->codprov) ? 'codprovseleted' : ''}}
                                                           border-bottom border-bottom-dashed cursor-pointer"
                                                           style="text-align: left">
                                                            <div class="flex-grow-1">
                                                                <h5>{{$prov->descrip}}</h5>
                                                                <p class="text-muted mb-0">{{$prov->codprov}}</p>
                                                            </div>
                                                        </a>
                                                    @endforeach
                                                </div>
                                            @else
                                                <div>
                                                    <p class="text-muted fs-13 mb-3">No se encontraron proveedores para la búsqueda: <b>{{$busqueda}}</b></p>
                                                    <a class="card btn btn-soft-light card-animate d-flex p-2 border-bottom border-bottom-dashed cursor-pointer"
                                                       style="text-align: left; display: none">
                                                        <div class="flex-grow-1" href="#modalProveedor" data-bs-toggle="modal">
                                                            <h6>+1 PROVEEDOR NUEVO</h6>
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

                    <form name="form2" id="form2" class="tablelist-form"
                          action="{{(isset($proveedor->codprov) and $proveedor->codprov != '') ? route('proveedoresupdate') : route('proveedores.store')}}"
                          autocomplete="off" method="post">
                        @csrf
                        @method('POST')
                        <div class="modal fade" id="modalProveedor" tabindex="-1" aria-labelledby="modalProveedor" aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                                <div class="modal-content">
                                    <div class="modal-header px-4 pt-4">
                                        <h5 class="modal-title" id="exampleModalLabel">
                                            Información del Proveedor
                                            {{(isset($proveedor->codprov) and $proveedor->codprov != '') ? $proveedor->descrip : ' nuevo' }}
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="close-modal"></button>
                                    </div>

                                    <div class="modal-body p-4">
                                        <div class="row">
                                            <div id="alert-error-msg" class="d-none alert alert-danger py-2"></div>

                                            <div class="col-lg-6">
                                                <div class="mb-2">
                                                    <label for="proveedor-cod-field" class="form-label">Código</label>
                                                    <input type="text" id="proveedor-cod-field" name="codprov"
                                                           value="{{(isset($proveedor->codprov) and $proveedor->codprov !== '') ? $proveedor->codprov : ((isset($busqueda) and $busqueda != '') ? $busqueda : '')}}"
                                                           class="form-control" placeholder="Ej: PROV001" readonly required>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="mb-2">
                                                    <label for="id3-cod-field" class="form-label">RIF / Documento</label>
                                                    <input type="text" id="id3-cod-field" name="id3" class="form-control"
                                                           value="{{(isset($proveedor->codprov) and $proveedor->codprov !== '') ? $proveedor->id3 : ''}}"
                                                           placeholder="Ej: J-12345678-5">
                                                </div>
                                            </div>
                                            <div class="col-lg-12">
                                                <div class="mb-2">
                                                    <label for="descrip-name-field" class="form-label">Nombre / Razón Social</label>
                                                    <input type="text" id="descrip-name-field" value="{{(isset($proveedor->descrip)) ? $proveedor->descrip : ''}}"
                                                           name="descrip" class="form-control" placeholder="Ej: Proveedores C.A." required>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="mb-2">
                                                    <label for="email-field" class="form-label">Email</label>
                                                    <input type="email" name="email" id="email-field" class="form-control"
                                                           value="{{(isset($proveedor->codprov)) ? $proveedor->email : ''}}"
                                                           placeholder="Ej: correo@proveedor.com">
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="mb-2">
                                                    <label for="clase-field" class="form-label">Clase</label>
                                                    <input type="text" name="clase" id="clase-field"
                                                           value="{{(isset($proveedor->codprov)) ? $proveedor->clase : ''}}"
                                                           class="form-control" placeholder="">
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="mb-2">
                                                    <label for="phone-field" class="form-label">Teléfono</label>
                                                    <input type="text" name="telef" id="phone-field" class="form-control"
                                                           value="{{(isset($proveedor->codprov)) ? $proveedor->telef : ''}}"
                                                           placeholder="Ej: 0414-12345678">
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="mb-2">
                                                    <label for="movil-field" class="form-label">Celular</label>
                                                    <input type="text" name="movil" id="movil-field" class="form-control"
                                                           value="{{(isset($proveedor->codprov)) ? $proveedor->movil : ''}}"
                                                           placeholder="Ej: 5841412345678">
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="mb-2">
                                                    <label for="represent-field" class="form-label">Representante</label>
                                                    <input type="text" name="represent" id="represent-field"
                                                           value="{{(isset($proveedor->codprov)) ? $proveedor->represent : ''}}"
                                                           class="form-control" placeholder="">
                                                </div>
                                            </div>

                                            <div class="col-lg-6">
                                                <div class="mb-2">
                                                    <label for="direc1-field" class="form-label">Dirección 1</label>
                                                    <input type="text" name="direc1" id="direc1-field" class="form-control"
                                                           value="{{(isset($proveedor->codprov)) ? $proveedor->direc1 : ''}}"
                                                           placeholder="Dirección principal">
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="mb-2">
                                                    <label for="direc2-field" class="form-label">Dirección 2</label>
                                                    <input type="text" name="direc2" id="direc2-field" class="form-control"
                                                           value="{{(isset($proveedor->codprov)) ? $proveedor->direc2 : ''}}"
                                                           placeholder="">
                                                </div>
                                            </div>

                                            <div class="col-lg-6">
                                                <div class="form-check mt-3">
                                                    <input class="form-check-input" type="checkbox" name="activo" id="activo-field" value="1"
                                                        {{ !isset($proveedor->activo) || $proveedor->activo == 1 ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="activo-field">
                                                        Proveedor Activo
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <div class="hstack gap-2 justify-content-end">
                                            <button type="button" class="btn btn-ghost-danger" data-bs-dismiss="modal">Cerrar</button>
                                            <button type="button" onclick="$('#form2').submit()" class="btn btn-success">
                                                {{(isset($proveedor->codprov) and $proveedor->codprov !== '') ? 'Modificar' : 'Crear'}}
                                            </button>
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
            @if(isset($proveedor) and isset($proveedor->descrip))
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-center">
                            <h5 class="card-title mb-0 flex-grow-1">{{$proveedor->descrip}}</h5>
                            <div class="flex-shrink-0">
                                <p class="mb-0">RIF: <b>{{$proveedor->id3 ?? 'N/A'}}</b></p>
                            </div>
                            <div class="flex-shrink-0" style="margin-left: 20px;">
                                <a class="btn btn-primary" href="#modalProveedor" data-bs-toggle="modal">Modificar</a>
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
                                                    <td width="25%">Dirección 1</td>
                                                    <td width="75%" class="fw-medium">{{$proveedor->direc1}}</td>
                                                </tr>
                                            @endif
                                            @if(isset($proveedor->direc2) and strlen($proveedor->direc2) > 2)
                                                <tr>
                                                    <td>Dirección 2</td>
                                                    <td class="fw-medium">{{$proveedor->direc2}}</td>
                                                </tr>
                                            @endif
                                            @if(isset($proveedor->email) and strlen($proveedor->email) > 2)
                                                <tr>
                                                    <td>Email</td>
                                                    <td class="fw-medium">{{$proveedor->email}}</td>
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
                                            @if(isset($proveedor->telef) and strlen($proveedor->telef) > 2)
                                                <tr bgcolor="#eee">
                                                    <td width="25%">Teléfono</td>
                                                    <td width="75%" class="fw-medium">{{$proveedor->telef}}</td>
                                                </tr>
                                            @endif
                                            @if(isset($proveedor->movil) and strlen($proveedor->movil) > 2)
                                                <tr>
                                                    <td>Celular</td>
                                                    <td class="fw-medium">{{$proveedor->movil}}</td>
                                                </tr>
                                            @endif
                                            @if(isset($proveedor->represent) and strlen($proveedor->represent) > 2)
                                                <tr bgcolor="#eee">
                                                    <td>Representante</td>
                                                    <td class="fw-medium">{{$proveedor->represent}}</td>
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
                                <li class="nav-item">
                                    <a class="nav-link {{ ($tab == 'tab1') ? 'active' : '' }}"
                                       href="{{ route('proveedores.index', ['codprov' => $proveedor->codprov, 'tab' => 'tab1']) }}"
                                       role="tab">
                                        Pagos Pendientes
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ ($tab == 'tab2') ? 'active' : '' }}"
                                       href="{{ route('proveedores.index', ['codprov' => $proveedor->codprov, 'tab' => 'tab2']) }}"
                                       role="tab">
                                        Pagos Realizados
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ ($tab == 'tab3') ? 'active' : '' }}"
                                       href="{{ route('proveedores.index', ['codprov' => $proveedor->codprov, 'tab' => 'tab3']) }}"
                                       role="tab">
                                        Resumen
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <div class="tab-content">
                            {{-- TAB 1: PAGOS PENDIENTES --}}
                            @if($tab == 'tab1')
                                <div class="tab-pane active" role="tabpanel">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="table-responsive table-card mb-1">
                                                <table width="100%" border="0" class="table align-middle table-nowrap">
                                                    <tr bgcolor="#fff">
                                                        <td width="10%" height="30" align="center" class="tdlineff">Fecha Viaje</td>
                                                        <td width="8%" align="center" class="tdlineff">Viaje</td>
                                                        <td width="15%" align="center" class="tdlineff">Cliente</td>
                                                        <td width="12%" align="center" class="tdlineff">Modelo</td>
                                                        <td width="5%" align="center" class="tdlineff">Cant.</td>
                                                        <td width="10%" align="center" class="tdlineff">Transporte</td>
                                                        <td width="10%" align="center" class="tdlineff">Retención</td>
                                                        <td width="10%" align="center" class="tdlineff">Monto a Pagar</td>
                                                        <td width="10%" align="center" class="tdlineff">Acciones</td>
                                                    </tr>

                                                    @php $totalPendiente = 0; @endphp
                                                    @forelse($pagosPendientes as $pago)
                                                        @php $totalPendiente += $pago->monto_esperado_cliente; @endphp
                                                        <tr>
                                                            <td height="30" align="center">
                                                                {{ $pago->viaje->fecha_inicio->format('d/m/Y') }}
                                                            </td>
                                                            <td align="center">
                                                                <a href="#" onclick="verViaje({{ $pago->viaje_id }})">
                                                                    {{ $pago->viaje->folio ?? $pago->viaje_id }}
                                                                </a>
                                                            </td>
                                                            <td align="left">{{ $pago->cliente->descrip ?? 'N/A' }}</td>
                                                            <td align="left">{{ $pago->modelo_moto }}</td>
                                                            <td align="center">{{ $pago->cantidad }}</td>
                                                            <td align="right">${{ number_format($pago->monto_transporte_proveedor, 2) }}</td>
                                                            <td align="right">${{ number_format($pago->retencion_proveedor, 2) }}</td>
                                                            <td align="right" class="monto-pendiente">
                                                                ${{ number_format($pago->monto_esperado_cliente, 2) }}
                                                            </td>
                                                            <td align="center">
                                                                <button class="btn btn-sm btn-success"
                                                                        onclick="marcarPagado({{ $pago->id }}, {{ $pago->monto_esperado_cliente }})">
                                                                    <i class="ri-check-line"></i> Pagar
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="9" align="center" height="50">
                                                                No hay pagos pendientes
                                                            </td>
                                                        </tr>
                                                    @endforelse

                                                    @if($pagosPendientes->count() > 0)
                                                        <tr bgcolor="#eee">
                                                            <td colspan="7" align="right"><strong>TOTAL PENDIENTE:</strong></td>
                                                            <td align="right" class="monto-pendiente">
                                                                <strong>${{ number_format($totalPendiente, 2) }}</strong>
                                                            </td>
                                                            <td></td>
                                                        </tr>
                                                    @endif
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            {{-- TAB 2: PAGOS REALIZADOS --}}
                            @if($tab == 'tab2')
                                <div class="tab-pane active" role="tabpanel">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="table-responsive table-card mb-1">
                                                <table width="100%" border="0" class="table align-middle table-nowrap">
                                                    <tr bgcolor="#fff">
                                                        <td width="10%" height="30" align="center" class="tdlineff">Fecha Pago</td>
                                                        <td width="10%" align="center" class="tdlineff">Fecha Viaje</td>
                                                        <td width="8%" align="center" class="tdlineff">Viaje</td>
                                                        <td width="15%" align="center" class="tdlineff">Cliente</td>
                                                        <td width="12%" align="center" class="tdlineff">Modelo</td>
                                                        <td width="5%" align="center" class="tdlineff">Cant.</td>
                                                        <td width="10%" align="center" class="tdlineff">Esperado</td>
                                                        <td width="10%" align="center" class="tdlineff">Pagado</td>
                                                        <td width="10%" align="center" class="tdlineff">Diferencia</td>
                                                        <td width="10%" align="center" class="tdlineff">Notas</td>
                                                    </tr>

                                                    @php
                                                        $totalEsperado = 0;
                                                        $totalPagado = 0;
                                                    @endphp
                                                    @forelse($pagosRealizados as $pago)
                                                        @php
                                                            $totalEsperado += $pago->monto_esperado_cliente;
                                                            $totalPagado += $pago->monto_real_cliente;
                                                        @endphp
                                                        <tr>
                                                            <td height="30" align="center">
                                                                {{ $pago->fecha_conciliacion ? \Carbon\Carbon::parse($pago->fecha_conciliacion)->format('d/m/Y') : 'N/A' }}
                                                            </td>
                                                            <td align="center">{{ $pago->viaje->fecha_inicio->format('d/m/Y') }}</td>
                                                            <td align="center">
                                                                <a href="#" onclick="verViaje({{ $pago->viaje_id }})">
                                                                    {{ $pago->viaje->folio ?? $pago->viaje_id }}
                                                                </a>
                                                            </td>
                                                            <td align="left">{{ $pago->cliente->descrip ?? 'N/A' }}</td>
                                                            <td align="left">{{ $pago->modelo_moto }}</td>
                                                            <td align="center">{{ $pago->cantidad }}</td>
                                                            <td align="right">${{ number_format($pago->monto_esperado_cliente, 2) }}</td>
                                                            <td align="right" class="monto-conciliado">${{ number_format($pago->monto_real_cliente, 2) }}</td>
                                                            <td align="right" class="{{ ($pago->diferencia ?? 0) >= 0 ? 'text-success' : 'text-danger' }}">
                                                                ${{ number_format($pago->diferencia ?? 0, 2) }}
                                                            </td>
                                                            <td align="left">
                                                                <small>{{ Str::limit($pago->notas_conciliacion, 20) }}</small>
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="10" align="center" height="50">
                                                                No hay pagos realizados
                                                            </td>
                                                        </tr>
                                                    @endforelse

                                                    @if($pagosRealizados->count() > 0)
                                                        <tr bgcolor="#eee">
                                                            <td colspan="6" align="right"><strong>TOTALES:</strong></td>
                                                            <td align="right"><strong>${{ number_format($totalEsperado, 2) }}</strong></td>
                                                            <td align="right" class="monto-conciliado">
                                                                <strong>${{ number_format($totalPagado, 2) }}</strong>
                                                            </td>
                                                            <td align="right">
                                                                <strong class="{{ ($totalPagado - $totalEsperado) >= 0 ? 'text-success' : 'text-danger' }}">
                                                                    ${{ number_format($totalPagado - $totalEsperado, 2) }}
                                                                </strong>
                                                            </td>
                                                            <td></td>
                                                        </tr>
                                                    @endif
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            {{-- TAB 3: RESUMEN --}}
                            @if($tab == 'tab3')
                                <div class="tab-pane active" role="tabpanel">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="card">
                                                <div class="card-header bg-info text-white">
                                                    <h6 class="mb-0">Resumen de Pagos por Mes</h6>
                                                </div>
                                                <div class="card-body">
                                                    <div class="table-responsive">
                                                        <table class="table table-sm">
                                                            <thead>
                                                            <tr>
                                                                <th>Período</th>
                                                                <th class="text-end">Pendiente</th>
                                                                <th class="text-end">Pagado</th>
                                                                <th class="text-end">Total</th>
                                                            </tr>
                                                            </thead>
                                                            <tbody>
                                                            @forelse($resumenPagos as $resumen)
                                                                <tr>
                                                                    <td>{{ \Carbon\Carbon::create()->month($resumen->mes)->format('F') }} {{ $resumen->anio }}</td>
                                                                    <td class="text-end monto-pendiente">${{ number_format($resumen->total_pendiente ?? 0, 2) }}</td>
                                                                    <td class="text-end monto-conciliado">${{ number_format($resumen->total_pagado ?? 0, 2) }}</td>
                                                                    <td class="text-end">${{ number_format(($resumen->total_pendiente ?? 0) + ($resumen->total_pagado ?? 0), 2) }}</td>
                                                                </tr>
                                                            @empty
                                                                <tr>
                                                                    <td colspan="4" class="text-center">No hay datos de pagos</td>
                                                                </tr>
                                                            @endforelse
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card">
                                                <div class="card-header bg-success text-white">
                                                    <h6 class="mb-0">Estadísticas</h6>
                                                </div>
                                                <div class="card-body">
                                                    <div class="mb-3">
                                                        <label>Total Pagos Pendientes</label>
                                                        <h3 class="monto-pendiente">
                                                            ${{ number_format($pagosPendientes->sum('monto_esperado_cliente'), 2) }}
                                                        </h3>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label>Total Pagos Realizados</label>
                                                        <h3 class="monto-conciliado">
                                                            ${{ number_format($pagosRealizados->sum('monto_real_cliente'), 2) }}
                                                        </h3>
                                                    </div>
                                                    <div>
                                                        <label>Cantidad de Viajes</label>
                                                        <h3>{{ $pagosPendientes->count() + $pagosRealizados->count() }}</h3>
                                                    </div>
                                                </div>
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

    {{-- Modal para registrar pago --}}
    <div class="modal fade" id="modalPago" aria-hidden="true" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Registrar Pago a Proveedor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('proveedores.marcar-pagado') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="pago_id" id="pago_id">

                        <div class="mb-3">
                            <label class="form-label">Monto Esperado</label>
                            <input type="text" class="form-control" id="monto_esperado" readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Monto Real <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="0" class="form-control" name="monto_real" id="monto_real" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Fecha de Pago <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="fecha_pago" value="{{ date('Y-m-d') }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Notas</label>
                            <textarea class="form-control" name="notas" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success">Registrar Pago</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ URL::asset('build/js/app.js') }}"></script>
    <script>
        function marcarPagado(id, montoEsperado) {
            $('#pago_id').val(id);
            $('#monto_esperado').val('$' + montoEsperado.toFixed(2));
            $('#monto_real').val(montoEsperado.toFixed(2));
            $('#modalPago').modal('show');
        }

        function verViaje(id) {
            window.open(`/viajes/${id}/ver`, '_blank');
        }

        $(document).ready(function() {
            $('#busqueda').select();

            // Inicializar tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>
@endsection
