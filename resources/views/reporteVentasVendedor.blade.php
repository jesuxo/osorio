@extends('layouts.master')
@section('title')
    Reporte de Ventas por Vendedor
@endsection
@section('css')
    <style>
        .tdline{
            border:1px solid #0072c5 !important;
        }
        .tdlineff{
            border-left:1px solid #fff !important;
            color: white !important;
            background-color: #0072c5 !important;
        }
        .vendedor-table {
            margin-bottom: 20px;
        }
        .vendedor-table th {
            background-color: #f8f9fa;
        }
        .botoncal{
            background: transparent;
            border: none;
            color: white;
        }
        .botoncal:hover{
            font-size: 13px;
        }
        .categoria-monto {
            font-weight: bold;
        }
        .total-vendedor {
            background-color: #e9ecef;
        }
    </style>
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="card-title mb-0">Reporte de Ventas por Vendedor</h4>
                    <p class="text-white-50 mb-0 small">Desde {{$fecha1}} Hasta {{$fecha2}}</p>
                </div>
                <div class="card-body">
                    <form method="post" action="{{ route('reporte.ventas.vendedor') }}" id="formFechas">
                        @csrf
                        <div class="row">
                            <div class="col-md-3">
                                <div class="input-group">
                                    <input type="text" class="form-control" data-provider="flatpickr"
                                           data-range-date="true" data-date-format="d/m/Y"
                                           name="fechasreport" readonly="readonly" value="{{$fechasreport}}">
                                    <div class="input-group-text bg-primary border-primary text-white">
                                        <button type="submit" class="botoncal">Consultar</button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <select class="form-select" name="fksucursal" onchange="$('#formFechas').submit()">
                                    <option value="">Todas las Sucursales</option>
                                    @foreach($sucursalesFiltro as $suc)
                                        <option value="{{ $suc->id }}" {{ $fksucursal == $suc->id ? 'selected' : '' }}>
                                            {{ $suc->descrip }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @if(count($dataCompleta) > 0)
        <div class="row">
            @foreach($dataCompleta as $sucId => $sucData)
                <div class="col-md-6 col-sm-12 mt-4">
                    <div class="card">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0 text-white">{{ $sucData['nombre'] }}</h5>
                        </div>
                        <div class="card-body">
                            @foreach($sucData['vendedores'] as $vendId => $vendedor)
                                <div class="vendedor-table">
                                    <h6 class="mt-3">{{ $vendedor['nombre'] }}</h6>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered">
                                            <thead class="table-light">
                                            <tr>
                                                <th> </th>
                                                <th class="text-end">Unidades</th>
                                                <th class="text-end">Monto (USD)</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @php
                                                $totalUnidades = 0;
                                                $totalMonto = 0;
                                            @endphp
                                            @foreach($vendedor['categorias'] as $catNombre => $catData)
                                                @php
                                                    $totalUnidades += $catData['unidades'];
                                                    $totalMonto += $catData['monto'];
                                                    if($catData['monto'] >0){
                                                @endphp
                                                <tr>
                                                    <td>
                                                        <strong>{{ $catNombre }}</strong>
                                                        @if($catData['unidades'] == 0 && $catData['monto'] == 0)
                                                            <span class="badge bg-secondary ms-2">Sin ventas</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-end {{ $catData['unidades'] > 0 ? 'fw-bold text-success' : 'text-muted' }}">
                                                        {{ number_format($catData['unidades'], 0) }}
                                                    </td>
                                                    <td class="text-end {{ $catData['monto'] > 0 ? 'fw-bold text-primary' : 'text-muted' }}">
                                                        ${{ number_format($catData['monto'], 2) }}
                                                    </td>
                                                </tr>
                                                @php } @endphp
                                            @endforeach
                                            </tbody>
                                            <tfoot class="table-secondary">
                                            <tr class="fw-bold">
                                                <td class="text-end">TOTAL VENDEDOR</td>
                                                <td class="text-end">{{ number_format($totalUnidades, 0) }}</td>
                                                <td class="text-end">${{ number_format($totalMonto, 2) }}</td>
                                            </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card mt-3">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0">Resumen General</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-6 col-sm-12">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                    <tr>
                                        <th>Categoría</th>
                                        <th class="text-end">Unidades</th>
                                        <th class="text-end">Monto (USD)</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @php
                                        $totalesCategorias = [];
                                        foreach($dataCompleta as $sucData) {
                                            foreach($sucData['vendedores'] as $vendedor) {
                                                foreach($vendedor['categorias'] as $catNombre => $catData) {
                                                    if(!isset($totalesCategorias[$catNombre])) {
                                                        $totalesCategorias[$catNombre] = ['unidades' => 0, 'monto' => 0];
                                                    }
                                                    $totalesCategorias[$catNombre]['unidades'] += $catData['unidades'];
                                                    $totalesCategorias[$catNombre]['monto'] += $catData['monto'];
                                                }
                                            }
                                        }
                                    @endphp
                                    @foreach($totalesCategorias as $catNombre => $catTotal)
                                        @if($catTotal['monto']>0)
                                        <tr>
                                            <td><strong>{{ $catNombre }}</strong></td>
                                            <td class="text-end">{{ number_format($catTotal['unidades'], 0) }}</td>
                                            <td class="text-end">${{ number_format($catTotal['monto'], 2) }}</td>
                                        </tr>
                                        @endif
                                    @endforeach
                                    </tbody>
                                    <tfoot class="table-secondary">
                                    <tr class="fw-bold">
                                        <td class="text-end">TOTAL GENERAL</td>
                                        <td class="text-end">{{ number_format($totalGeneral['unidades'], 0) }}</td>
                                        <td class="text-end">${{ number_format($totalGeneral['monto'], 2) }}</td>
                                    </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="row">
            <div class="col-12">
                <div class="alert alert-info text-center">
                    <i class="ri-information-line fs-4"></i>
                    <p class="mb-0">No hay ventas en el período seleccionado para {{ $fksucursal ? 'la sucursal seleccionada' : 'ninguna sucursal' }}.</p>
                </div>
            </div>
        </div>
    @endif
@endsection

@section('scripts')
    <script src="{{ URL::asset('build/js/app.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Inicializar flatpickr si está presente
            if (typeof flatpickr !== 'undefined') {
                flatpickr("[data-provider='flatpickr']", {
                    mode: "range",
                    dateFormat: "d/m/Y",
                    locale: "es",
                    defaultDate: "{{ $fechasreport }}"
                });
            }
        });
    </script>
@endsection
