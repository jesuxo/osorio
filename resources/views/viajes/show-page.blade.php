@extends('layouts.master')

@section('title', 'Detalles del Viaje')

@section('content')
    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <h1 class="h2 mb-0">
                        <i class="bi bi-truck me-2 text-primary"></i>
                        Detalles del Viaje #{{ $viaje->folio ?? $viaje->id }}
                    </h1>
                    <div>
                        <a href="{{ url()->previous() }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-2"></i>Volver
                        </a>
                        <a href="{{ route('viajes.index') }}" class="btn btn-info">
                            <i class="bi bi-list me-2"></i>Lista de Viajes
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="card mb-3">
                    <div class="card-header bg-primary text-white">
                        <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i>Información General</h6>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tr>
                                <th>Estado:</th>
                                <td>
                                    @if($viaje->estado == 'planeado')
                                        <span class="badge bg-secondary">Planeado</span>
                                    @elseif($viaje->estado == 'en_curso')
                                        <span class="badge bg-warning">En Curso</span>
                                    @elseif($viaje->estado == 'completado')
                                        <span class="badge bg-success">Completado</span>
                                    @else
                                        <span class="badge bg-danger">Cancelado</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Folio:</th>
                                <td>{{ $viaje->folio ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Fecha Inicio:</th>
                                <td>{{ $viaje->fecha_inicio->format('d/m/Y') }}</td>
                            </tr>
                            <tr>
                                <th>Fecha Fin:</th>
                                <td>{{ $viaje->fecha_fin?->format('d/m/Y') ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Ruta:</th>
                                <td>{{ $viaje->origen }} → {{ $viaje->destino }}</td>
                            </tr>
                            <tr>
                                <th>Distancia:</th>
                                <td>{{ $viaje->distancia_km ?? 'N/A' }} km</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card mb-3">
                    <div class="card-header bg-success text-white">
                        <h6 class="mb-0"><i class="bi bi-truck-front me-2"></i>Camiones y Choferes</h6>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tr>
                                <th>Camión:</th>
                                <td>
                                    <strong>{{ $viaje->camion->placa ?? 'N/A' }}</strong><br>
                                    <small>{{ $viaje->camion->marca ?? '' }} {{ $viaje->camion->modelo ?? '' }}</small>
                                </td>
                            </tr>
                            <tr>
                                <th>Chofer:</th>
                                <td>
                                    <strong>{{ $viaje->chofer->nombre_completo ?? 'N/A' }}</strong><br>
                                    <small>Licencia: {{ $viaje->chofer->licencia ?? 'N/A' }}</small>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-header bg-warning text-dark">
                        <h6 class="mb-0"><i class="bi bi-cash-stack me-2"></i>Resumen Financiero</h6>
                    </div>
                    <div class="card-body">
                        @php
                            $ingresoTotal = $viaje->motosTransportadas->sum(function($m) {
                                return $m->cantidad * $m->precio_por_moto;
                            });
                            $gastoTotal = $viaje->gastos->sum('monto');
                            $ganancia = $ingresoTotal - $gastoTotal;
                        @endphp
                        <table class="table table-borderless">
                            <tr>
                                <th>Ingresos:</th>
                                <td class="text-success"><strong>${{ number_format($ingresoTotal, 2) }}</strong></td>
                            </tr>
                            <tr>
                                <th>Gastos:</th>
                                <td class="text-danger"><strong>${{ number_format($gastoTotal, 2) }}</strong></td>
                            </tr>
                            <tr>
                                <th>Ganancia:</th>
                                <td class="{{ $ganancia >= 0 ? 'text-success' : 'text-danger' }}">
                                    <strong>${{ number_format($ganancia, 2) }}</strong>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Motos Transportadas --}}
        <div class="card mb-3">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0"><i class="bi bi-motorcycle me-2"></i>Motos Transportadas</h6>
            </div>
            <div class="card-body">
                @if($viaje->motosTransportadas->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                            <tr>
                                <th>Cliente</th>
                                <th>Modelo</th>
                                <th>Cantidad</th>
                                <th>Precio Unit.</th>
                                <th>Subtotal</th>
                                <th>Estado</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($viaje->motosTransportadas as $moto)
                                <tr>
                                    <td>{{ $moto->cliente->descrip ?? 'N/A' }}</td>
                                    <td>{{ $moto->modelo_moto }}</td>
                                    <td class="text-center">{{ $moto->cantidad }}</td>
                                    <td>${{ number_format($moto->precio_por_moto, 2) }}</td>
                                    <td>${{ number_format($moto->cantidad * $moto->precio_por_moto, 2) }}</td>
                                    <td>
                                        @if($moto->facturado)
                                            <span class="badge bg-success">Facturado</span>
                                        @else
                                            <span class="badge bg-warning">Pendiente</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                            <tfoot>
                            <tr class="table-info">
                                <th colspan="4" class="text-end">Total Motos:</th>
                                <th colspan="2">{{ $viaje->motosTransportadas->sum('cantidad') }}</th>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                @else
                    <p class="text-muted">No hay motos registradas en este viaje</p>
                @endif
            </div>
        </div>

        {{-- Gastos --}}
        <div class="card mb-3">
            <div class="card-header bg-danger text-white">
                <h6 class="mb-0"><i class="bi bi-receipt me-2"></i>Gastos del Viaje</h6>
            </div>
            <div class="card-body">
                @if($viaje->gastos->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Tipo</th>
                                <th>Concepto</th>
                                <th>Monto</th>
                                <th>Moneda</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($viaje->gastos as $gasto)
                                <tr>
                                    <td>{{ $gasto->fecha_gasto->format('d/m/Y') }}</td>
                                    <td>{{ $gasto->tipoGasto->nombre ?? 'N/A' }}</td>
                                    <td>{{ $gasto->concepto }}</td>
                                    <td class="text-danger">${{ number_format($gasto->monto, 2) }}</td>
                                    <td>
                                        @if($gasto->moneda_original == 'VES')
                                            <span class="badge bg-info">VES</span>
                                        @else
                                            <span class="badge bg-success">USD</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                            <tfoot>
                            <tr class="table-danger">
                                <th colspan="3" class="text-end">Total Gastos:</th>
                                <th colspan="2">${{ number_format($viaje->gastos->sum('monto'), 2) }}</th>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                @else
                    <p class="text-muted">No hay gastos registrados en este viaje</p>
                @endif
            </div>
        </div>
    </div>
@endsection
