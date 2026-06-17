{{-- resources/views/cxctransporte/historial-cliente.blade.php --}}
@extends('layouts.master')

@section('title', 'Historial de Cobros')

@section('content')
    <style>
        .table-success {
            /* Mantén todas las variables existentes */
            --tb-table-hover-bg: #e3f2fd !important; /* Cambia black por el color que prefieras */
            --tb-table-hover-color: #000 !important; /* Ajusta el color del texto si es necesario */
        }
    </style>
    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <h1 class="h3 mb-0">
                        <i class="bi bi-clock-history me-2 text-primary"></i>
                        Historial de Cobros - {{ $cliente->descrip }}
                    </h1>
                    <a href="{{ route('cxctransporte.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Volver
                    </a>
                </div>
                <p class="text-muted">
                    <strong>Código:</strong> {{ $cliente->codclie }} |
                    <strong>Saldo pendiente:</strong> <span class="text-warning">${{ number_format($saldoPendiente, 2) }}</span> |
                    <strong>Total facturado:</strong> <span class="text-success">${{ number_format($totalFacturado, 2) }}</span>
                </p>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-primary text-white">
                <h6 class="mb-0 text-white"><i class="bi bi-list me-2"></i>Movimientos</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th>Fecha/Hora</th>
                            <th>Tipo</th>
                            <th>Viaje</th>
                            <th>Modelo</th>
                            <th>Cantidad</th>
                            <th>Monto</th>
                            <th>Usuario</th>
                            <th>Observaciones</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($historial as $mov)
                            <tr class="{{ $mov->tipo == 'cobro' ? 'table-success' : 'table-warning' }}">
                                <td>{{ $mov->fecha_hora->format('d/m/Y H:i:s') }}</td>
                                <td>
                                    @if($mov->tipo == 'cobro')
                                        <span class="badge bg-success">COBRO</span>
                                    @else
                                        <span class="badge bg-warning">REVERSIÓN</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="#" onclick="verViaje({{ $mov->viaje_id }})">
                                        {{ $mov->viaje->folio ?? $mov->viaje_id }}
                                    </a>
                                </td>
                                <td>{{ $mov->modelo_moto }}</td>
                                <td class="text-center">{{ $mov->cantidad }}</td>
                                <td><strong>${{ number_format($mov->monto, 2) }}</strong></td>
                                <td>{{ $mov->usuario->name ?? 'Sistema' }}</td>
                                <td><small>{{ $mov->observaciones }}</small></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <i class="bi bi-inbox fs-1 d-block mb-3 text-muted"></i>
                                    <p class="text-muted">No hay movimientos registrados</p>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
                {{ $historial->links() }}
            </div>
        </div>
    </div>
@endsection
