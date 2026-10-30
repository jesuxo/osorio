@extends('layouts.master')
@section('title')
    Venta de productos por sucursal
@endsection
@section('css')

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
            border:1px solid #0072c5 !important;

        }
        .tdlineff{
            border-left:1px solid #fff !important;

            color: white !important;
            background-color: #0072c5 !important;
        }
    </style>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <br />

                <div class="card-header bg-primary text-white">
                    <h4 class="card-title mb-0">Reporte de Ventas </h4>
                    <p class="text-white-50 mb-0 small">  Desde  {{$fecha1}} Hasta {{$fecha2}}</p>
                </div>
                <div class="card-body   ">

                    <form  method="post" name="form1" id="form1" action="/reporte/venta">

                        <div class="row">
                            <div class="col-md-3  ">

                                <select class="form-select" data-choices  onchange="$('#form1').submit()"
                                        id="idsucu" name="fksucursal">
                                    <option  {{($fksucursal == '' or $fksucursal == 0 )?'selected':''}} value="0">Todas las Sucursales</option>
                                    @foreach($allsucursales as $sucu)
                                        <option  {{($sucu->id == $fksucursal)?'selected':''}} value="{{$sucu->id}}">{!! $sucu->descrip !!}</option>
                                    @endforeach
                                </select>

                            </div>
                            <div class="col-md-3  ">

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
                            <div class="col-md-3  ">
                                <div class="input-group">
                                    <div class="input-group-text bg-primary border-primary text-white">
                                        <button onclick="loadingreport('/resumenVentas')" type="button" class="botoncal" >VER Resumen</button>
                                    </div>
                                </div>
                            </div>
                        @csrf
                        @method('POST')
                    </form>


                </div>
            </div>
        </div>
    </div>


    <div id="contentReport"></div>
        <div class="row">
            <div class="col-md-12 mt-3 ">
                <table width="100%" border="0" class="" >

                    <tr>
                        <td width="30%" valign="top">
                            <table width="100%" border="0"  class="table table-borderless table-centered align-middle table-nowrap mb-0 " style="  color:#333; " >
                                <tr bgcolor="#fff">
                                    <td width="39%" height="30"align="center" class="tdline" >SUCURSAL</td>
                                    <td width="6%" align="center" class="tdlineff" > BS</td>
                                    <td width="5%" align="center" class="tdlineff" > BS.T</td>
                                    <td width="6%" align="center" class="tdlineff" > USD</td>
                                    <td width="5%" align="center" class="tdlineff" > USD.T</td>
                                    <td width="5%" align="center" class="tdlineff" > CREDITO </td>
                                    <td width="6%" align="center" class="tdlineff" > TOTAL USD</td>
                                </tr>
                                @if(isset($sucursales))
                                    @php $n=0;
                                        $tcancele    =0;
                                        $tcancelt    =0;
                                        $tdolares    =0;
                                        $ttransf     =0;
                                        $tpesos      =0;
                                        $tpeso_tranf =0;
                                        $teuros      =0;
                                        $tcredito    =0;
                                        $ttotalventa =0;
                                    @endphp
                                    @foreach($sucursales as $indexsuc => $sucursal)
                                        @php
                                            $tcancele    += $listado[$indexsuc]['cancele'];
                                            $tcancelt    += $listado[$indexsuc]['cancelt'];
                                            $tdolares    += $listado[$indexsuc]['dolares'];
                                            $ttransf     += $listado[$indexsuc]['transf'];
                                            $tpesos      += $listado[$indexsuc]['pesos'];
                                            $tpeso_tranf += $listado[$indexsuc]['peso_tranf'];
                                            $teuros      += $listado[$indexsuc]['euros'];
                                            $tcredito    += $listado[$indexsuc]['credito'];
                                            $ttotalventa += $listado[$indexsuc]['totalventa'];
                                        @endphp
                                        <tr @php if(($n%2)==0){echo 'bgcolor="#ddd"'; }else{echo 'bgcolor="#fff"';} @endphp>
                                            <td width="39%" height="30"align="left" class="tdline" >{{$sucursal}}</td>
                                            <td width="6%" align="right" class="tdline" >  {{($listado[$indexsuc]['cancele']!=0)?number_format($listado[$indexsuc]['cancele'],2,',','.') : ''}}</td>
                                            <td width="5%" align="right" class="tdline" >  {{($listado[$indexsuc]['cancelt']!=0)?number_format($listado[$indexsuc]['cancelt'],2,',','.') : ''}}</td>
                                            <td width="6%" align="right" class="tdline" >  {{($listado[$indexsuc]['dolares']!=0)?number_format($listado[$indexsuc]['dolares'],2,',','.') : ''}}</td>
                                            <td width="5%" align="right" class="tdline" >  {{($listado[$indexsuc]['transf'] !=0)?number_format($listado[$indexsuc]['transf'] ,2,',','.') : ''}}</td>
                                            <td width="5%" align="right" class="tdline" >  {{($listado[$indexsuc]['credito']!=0)?number_format($listado[$indexsuc]['credito'],2,',','.') : ''}}</td>
                                            <td width="6%" align="right" class="tdline" >  {{($listado[$indexsuc]['totalventa']!=0)?number_format($listado[$indexsuc]['totalventa'],2,',','.') : ''}}</td>
                                        </tr>
                                        @php $n++; @endphp
                                    @endforeach
                                @endif

                                <tr bgcolor="#ddd">
                                    <td width="39%" height="30"align="left" class="tdline" >TOTALES</td>
                                    <td width="6%" align="right" class="tdline" >{{ ($tcancele!=0)? number_format($tcancele,2,',','.') : ''}}     </td>
                                    <td width="5%" align="right" class="tdline" >{{ ($tcancelt!=0)?number_format($tcancelt,2,',','.'): ''}}        </td>
                                    <td width="6%" align="right" class="tdline" >{{ ($tdolares!=0)?number_format($tdolares,2,',','.'): ''}}       </td>
                                    <td width="5%" align="right" class="tdline" >{{ ($ttransf!=0)?number_format($ttransf,2,',','.'): ''}}         </td>
                                    <td width="5%" align="right" class="tdline" >{{ ($tcredito!=0)?number_format($tcredito,2,',','.'): ''}}       </td>
                                    <td width="6%" align="right" class="tdline" >{{ ($ttotalventa!=0)?number_format($ttotalventa,2,',','.'): ''}} </td>
                                </tr>

                            </table>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="col-md-12 mt-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0 fw-semibold" style="color: #fff !important;">Venta por Instancias</h5>
                            <small class="text-white-50">Desde {{ $fecha1 }} hasta {{ $fecha2 }}</small>
                        </div>
                        <span class="badge bg-light text-primary fw-semibold">
                {{ count($arrayinsta ?? []) }} instancias
            </span>
                    </div>

                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                <tr class="text-uppercase small fw-semibold text-muted">
                                    <th class="ps-3">Instancia de Inventario</th>
                                    <th class="text-center">Cantidad Vendida</th>
                                    <th class="text-end">Total Venta</th>
                                    <th class="text-end">Total Base Venta</th>
                                    <th class="text-end">Total Costo</th>
                                    <th class="text-end">Total Utilidad</th>
                                    <th class="text-end">% Utilidad</th>
                                    <th class="text-center pe-3" style="width:70px;">Detalle</th>
                                </tr>
                                </thead>
                                <tbody>
                                @php
                                    $tbasesuma = $costoxcantidad = $restaxcantidad = $tprecioventa = 0;
                                    $totalcant = 0;
                                @endphp
                                @forelse($arrayinsta ?? [] as $key => $listavend)
                                    @php
                                        $totalcant      += $listavend['cant'];
                                        $tprecioventa   += $listavend['precioventa'];
                                        $tbasesuma      += $listavend['basesuma'];
                                        $costoxcantidad += $listavend['preciod'];
                                        $restaxcantidad += $listavend['resta'];
                                        $utilidad = $listavend['preciod'] > 0
                                            ? ($listavend['resta'] / $listavend['preciod']) * 100
                                            : 0;
                                    @endphp
                                    @if($listavend['cant'] != 0)
                                        <tr>
                                            <td class="ps-3 fw-medium">{{ $listavend['descrip'] }}</td>
                                            <td class="text-center">
                                        <span class="badge bg-primary-subtle text-primary fw-semibold">
                                            {{ number_format($listavend['cant'], 0, ',', '.') }}
                                        </span>
                                            </td>
                                            <td class="text-end">$ {{ number_format($listavend['precioventa'], 2, ',', '.') }}</td>
                                            <td class="text-end">$ {{ number_format($listavend['basesuma'], 2, ',', '.') }}</td>
                                            <td class="text-end">$ {{ number_format($listavend['preciod'], 2, ',', '.') }}</td>
                                            <td class="text-end fw-semibold text-success">
                                                $ {{ number_format($listavend['resta'], 2, ',', '.') }}
                                            </td>
                                            <td class="text-end">
                                        <span class="badge {{ $utilidad >= 0 ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }}">
                                            {{ number_format($utilidad, 2, ',', '.') }} %
                                        </span>
                                            </td>
                                            <td class="text-center pe-3">
                                                <button type="button"
                                                        class="btn btn-sm btn-outline-primary btn-ver-detalle"
                                                        data-codinst="{{ $key }}"
                                                        data-descrip="{{ $listavend['descrip'] }}"
                                                        title="Ver distribución por sucursal">
                                                    <i class="bi bi-diagram-3"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endif
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-4">
                                            <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                                            Sin ventas en el período
                                        </td>
                                    </tr>
                                @endforelse
                                </tbody>
                                @if(count($arrayinsta ?? []) > 0)
                                    <tfoot class="table-light fw-semibold">
                                    <tr>
                                        <td class="ps-3">TOTALES</td>
                                        <td class="text-center">{{ number_format($totalcant, 0, ',', '.') }}</td>
                                        <td class="text-end">$ {{ number_format($tprecioventa, 2, ',', '.') }}</td>
                                        <td class="text-end">$ {{ number_format($tbasesuma, 2, ',', '.') }}</td>
                                        <td class="text-end">$ {{ number_format($costoxcantidad, 2, ',', '.') }}</td>
                                        <td class="text-end text-success">$ {{ number_format($restaxcantidad, 2, ',', '.') }}</td>
                                        <td class="text-end">
                                            {{ $costoxcantidad > 0 ? number_format(($restaxcantidad / $costoxcantidad) * 100, 2, ',', '.') : '0,00' }} %
                                        </td>
                                        <td></td>
                                    </tr>
                                    </tfoot>
                                @endif
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mt-3">
                <table width="100%" border="0" class=" table table-borderless table-centered align-middle table-nowrap mb-0 " style="  color:#333; ">
                    <tr>
                        <td width="30%" height="53" align="center" class="titulo">
                            VENTA AGRUPADAS DE VENDEDORES <br />
                            DESDE  {{$fecha1}} HASTA {{$fecha2}}
                        </td>
                    </tr>
                    <tr>
                        <td width="30%" valign="top">
                            <table width="100%" border="0"   class="table table-borderless table-centered align-middle table-nowrap mb-0 " style="  color:#333; ">
                                <tr bgcolor="#ddd">
                                    <td width="50%" align="center" class="tdlineff" style="font-size:12px"> VENDEDOR </td>
                                    <td width="25%" align="center" class="tdlineff" style="font-size:12px"> CANTIDAD </td>
                                    <td width="25%" align="center" class="tdlineff"style="font-size:12px"> TOTAL    </td>
                                </tr>
                                @php
                                    $tantos = $tcant  = $tventa   =0;
                                @endphp
                                @if(isset($vendedores) )
                                    @foreach($vendedores as $index => $vendedor)
                                        @php
                                            $tantos ++;
                                            $tcant  +=  $vendedor['cant'] ;
                                            $tventa += $vendedor['venta'] ;
                                        @endphp
                                        <tr @php if(($tantos%2)==0){echo 'bgcolor="#ddd"'; }else{echo 'bgcolor="#fff"';} @endphp>

                                            <td align="left"  class="tdline">{{ $vendedor['descrip'] }}</td>
                                            <td align="center"class="tdline">{{ $vendedor['cant'] }} </td>
                                            <td align="right" class="tdline">{{ number_format($vendedor['venta'],2,',','.') }} </td>
                                        </tr>
                                    @endforeach
                                @endif
                                <tr  >
                                    <td align="right" class="titulo tdline"  >&nbsp; </td>
                                    <td align="center"class="titulo tdline"  > {{$tcant}}</td>
                                    <td align="right" class="titulo tdline"  > {{ number_format($tventa,2,',','.')}}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6 mt-3">
                <table width="100%" border="0" class=" table table-borderless table-centered align-middle table-nowrap mb-0 " style="  color:#333; ">
                    <tr>
                        <td width="30%" height="53" align="center" class="titulo">
                            VENTA AGRUPADAS CADA SUCURSAL <br />
                            DESDE  {{$fecha1}} HASTA {{$fecha2}}
                        </td>
                    </tr>
                    <tr>
                        <td width="30%" valign="top">
                            <table width="100%" border="0"   class="table table-borderless table-centered align-middle table-nowrap mb-0 " style="  color:#333; ">
                                <tr bgcolor="#ddd">
                                    <td width="50%" align="center" class="tdlineff" style="font-size:12px">SUCURSAL</td>
                                    <td width="25%" align="center" class="tdlineff" style="font-size:12px"> CANTIDAD </td>
                                    <td width="25%" align="center" class="tdlineff"style="font-size:12px"> TOTAL    </td>
                                </tr>
                                @php
                                    $tantos = $tcant  = $tventa   =0;
                                @endphp
                                @if(isset($vsucursal) )
                                    @foreach($vsucursal as $index => $vendedor)
                                        @php
                                            $tantos ++;
                                            $tcant  +=  $vendedor['cant'] ;
                                            $tventa += $vendedor['venta'] ;
                                        @endphp
                                        <tr @php if(($tantos%2)==0){echo 'bgcolor="#ddd"'; }else{echo 'bgcolor="#fff"';} @endphp>

                                            <td align="left"  class="tdline">{{ $vendedor['descrip'] }}</td>
                                            <td align="center"class="tdline">{{ $vendedor['cant'] }} </td>
                                            <td align="right" class="tdline">{{ number_format($vendedor['venta'],2,',','.') }} </td>
                                        </tr>
                                    @endforeach
                                @endif
                                <tr  >
                                    <td align="right" class="titulo tdline"  >&nbsp; </td>
                                    <td align="center"class="titulo tdline"  > {{$tcant}}</td>
                                    <td align="right" class="titulo tdline"  > {{ number_format($tventa,2,',','.')}}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

    <!-- Modal: distribución por sucursal -->
    <div class="modal fade" id="modalDistribucion" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-primary text-white">
                    <div>
                        <h5 class="modal-title mb-0" id="modalDistribucionTitle">Distribución por Sucursal</h5>
                        <small class="text-white-50" id="modalDistribucionSubtitle"></small>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle mb-0">
                            <thead class="table-light small text-uppercase text-muted">
                            <tr>
                                <th class="ps-3">Sucursal</th>
                                <th class="text-center">Cantidad</th>
                                <th class="text-end">Total Venta</th>
                                <th class="text-end">Total Costo</th>
                                <th class="text-end">Utilidad</th>
                                <th class="text-end pe-3">% Utilidad</th>
                            </tr>
                            </thead>
                            <tbody id="modalDistribucionBody">
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <div class="spinner-border text-primary" role="status"></div>
                                    <div class="small text-muted mt-2">Cargando...</div>
                                </td>
                            </tr>
                            </tbody>
                            <tfoot class="table-light fw-semibold" id="modalDistribucionFoot"></tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection
@section('scripts')
    <script src="{{ URL::asset('build/js/app.js') }}"></script>

    <script>
        function loadingreport(action){
            $('#form1').attr('action',action);
            $('#contentReport').html('<button class="btn btn-outline-primary btn-load"><span class="d-flex align-items-center"><span class="spinner-border flex-shrink-0" role="status"> <span class="visually-hidden"> Cargando...</span> </span> <span class="flex-grow-1 ms-2">Cargando... </span> </span> </button>');
            $('#form1').submit()
        }

        $(document).on('click', '.btn-ver-detalle', function () {
            const codinst = $(this).data('codinst');
            const descrip = $(this).data('descrip');

            $('#modalDistribucionTitle').text('Distribución: ' + descrip);
            $('#modalDistribucionSubtitle').text('{{ $fecha1 }} — {{ $fecha2 }}');
            $('#modalDistribucionBody').html(
                '<tr><td colspan="6" class="text-center py-4">' +
                '<div class="spinner-border text-primary" role="status"></div>' +
                '<div class="small text-muted mt-2">Cargando...</div></td></tr>'
            );
            $('#modalDistribucionFoot').empty();

            const modal = new bootstrap.Modal(document.getElementById('modalDistribucion'));
            modal.show();

            $.ajax({
                url: '{{ route("reporteventa.instancia-detalle") }}',
                method: 'GET',
                data: {
                    codinst: codinst,
                    fecha1: '{{ $fecha1 }}',
                    fecha2: '{{ $fecha2 }}',
                },
                success: function (res) {
                    if (!res.detalle || res.detalle.length === 0) {
                        $('#modalDistribucionBody').html(
                            '<tr><td colspan="6" class="text-center text-muted py-4">Sin ventas registradas</td></tr>'
                        );
                        return;
                    }

                    let html = '';
                    res.detalle.forEach(function (r) {
                        const pctClass = r.utilidad_pct >= 0 ? 'text-success' : 'text-danger';
                        html += '<tr>' +
                            '<td class="ps-3 fw-medium">' + r.sucursal + '</td>' +
                            '<td class="text-center"><span class="badge bg-primary-subtle text-primary">' +
                            Number(r.cant).toLocaleString('es-VE') + '</span></td>' +
                            '<td class="text-end">$ ' + Number(r.precioventa).toLocaleString('es-VE', {minimumFractionDigits: 2}) + '</td>' +
                            '<td class="text-end">$ ' + Number(r.preciod).toLocaleString('es-VE', {minimumFractionDigits: 2}) + '</td>' +
                            '<td class="text-end fw-semibold ' + pctClass + '">$ ' +
                            Number(r.resta).toLocaleString('es-VE', {minimumFractionDigits: 2}) + '</td>' +
                            '<td class="text-end pe-3 fw-semibold ' + pctClass + '">' +
                            Number(r.utilidad_pct).toFixed(2) + ' %</td>' +
                            '</tr>';
                    });
                    $('#modalDistribucionBody').html(html);

                    const t = res.totales;
                    $('#modalDistribucionFoot').html(
                        '<tr>' +
                        '<td class="ps-3">TOTALES</td>' +
                        '<td class="text-center">' + Number(t.cant).toLocaleString('es-VE') + '</td>' +
                        '<td class="text-end">$ ' + Number(t.venta).toLocaleString('es-VE', {minimumFractionDigits: 2}) + '</td>' +
                        '<td class="text-end">$ ' + Number(t.costo).toLocaleString('es-VE', {minimumFractionDigits: 2}) + '</td>' +
                        '<td class="text-end text-success">$ ' + Number(t.utilidad).toLocaleString('es-VE', {minimumFractionDigits: 2}) + '</td>' +
                        '<td class="text-end pe-3">' + Number(t.pct).toFixed(2) + ' %</td>' +
                        '</tr>'
                    );
                },
                error: function () {
                    $('#modalDistribucionBody').html(
                        '<tr><td colspan="6" class="text-center text-danger py-4">Error al cargar datos</td></tr>'
                    );
                }
            });
        });
    </script>



@endsection
