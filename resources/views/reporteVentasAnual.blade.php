@extends('layouts.master')
@section('title') Reporte Venta Anual @endsection

@section('css')
<style>
    .kpi-card { border-left: 4px solid #0072c5; }
    .table-anual td, .table-anual th { white-space: nowrap; }
</style>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="card-title mb-0">Reporte de Ventas Anual</h4>
                    <p class="text-white-50 mb-0 small">Año {{ $anio }} — Todas las instancias</p>
                </div>
                <form method="get" action="{{ route('reporteventa.anual') }}" class="d-flex gap-2">
                    <select name="anio" class="form-select form-select-sm" onchange="this.form.submit()">
                        @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                        <option value="{{ $y }}" {{ $y == $anio ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </form>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-anual align-middle mb-0">
                        <thead class="table-light">
                        <tr class="text-uppercase small fw-semibold text-muted">
                            <th class="ps-3">Instancia de Inventario</th>
                            <th class="text-center">Cantidad Vendida</th>
                            <th class="text-end">Total Venta</th>
                            <th class="text-end">Total Base Venta</th>
                            <th class="text-end">Total Costo</th>
                            <th class="text-end">Total Utilidad</th>
                            <th class="text-end pe-3">% Utilidad</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php
                        $tCant = $tVenta = $tBase = $tCosto = $tUtil = 0;
                        @endphp
                        @forelse($arrayinsta as $key => $row)
                        @php
                        $tCant  += $row['cant'];
                        $tVenta += $row['precioventa'];
                        $tBase  += $row['basesuma'];
                        $tCosto += $row['preciod'];
                        $tUtil  += $row['resta'];
                        $utilPct = $row['preciod'] > 0 ? ($row['resta'] / $row['preciod']) * 100 : 0;
                        @endphp
                        @if($row['cant'] != 0)
                        <tr>
                            <td class="ps-3 fw-medium">{{ $row['descrip'] }}</td>
                            <td class="text-center">
                                        <span class="badge bg-primary-subtle text-primary fw-semibold">
                                            {{ number_format($row['cant'], 0, ',', '.') }}
                                        </span>
                            </td>
                            <td class="text-end">$ {{ number_format($row['precioventa'], 2, ',', '.') }}</td>
                            <td class="text-end">$ {{ number_format($row['basesuma'], 2, ',', '.') }}</td>
                            <td class="text-end">$ {{ number_format($row['preciod'], 2, ',', '.') }}</td>
                            <td class="text-end fw-semibold text-success">
                                $ {{ number_format($row['resta'], 2, ',', '.') }}
                            </td>
                            <td class="text-end pe-3">
                                        <span class="badge {{ $utilPct >= 0 ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }}">
                                            {{ number_format($utilPct, 2, ',', '.') }} %
                                        </span>
                            </td>
                        </tr>
                        @endif
                        @empty
                        <tr><td colspan="7" class="text-center text-muted py-4">Sin ventas en el año {{ $anio }}</td></tr>
                        @endforelse
                        </tbody>
                        <tfoot class="table-light fw-semibold">
                        <tr>
                            <td class="ps-3">TOTALES</td>
                            <td class="text-center">{{ number_format($tCant, 0, ',', '.') }}</td>
                            <td class="text-end">$ {{ number_format($tVenta, 2, ',', '.') }}</td>
                            <td class="text-end">$ {{ number_format($tBase, 2, ',', '.') }}</td>
                            <td class="text-end">$ {{ number_format($tCosto, 2, ',', '.') }}</td>
                            <td class="text-end text-success">$ {{ number_format($tUtil, 2, ',', '.') }}</td>
                            <td class="text-end pe-3">
                                {{ $tCosto > 0 ? number_format(($tUtil / $tCosto) * 100, 2, ',', '.') : '0,00' }} %
                            </td>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
