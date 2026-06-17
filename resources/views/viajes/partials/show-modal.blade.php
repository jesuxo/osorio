{{-- resources/views/viajes/partials/show-modal.blade.php --}}
<div class="container-fluid">
    {{-- Cabecera con estado --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">Viaje #{{ $viaje->folio ?? $viaje->id }}</h4>
                    <p class="text-muted mb-0">
                        <i class="fas fa-calendar me-1"></i> Creado: {{ $viaje->created_at->format('d/m/Y H:i') }}
                    </p>
                </div>
                <div>
                    @switch($viaje->estado)
                        @case('planeado')
                            <span class="badge bg-secondary p-3">Planeado</span>
                            @break
                        @case('en_curso')
                            <span class="badge bg-warning text-dark p-3">En Curso</span>
                            @break
                        @case('completado')
                            <span class="badge bg-success p-3">Completado</span>
                            @break
                        @case('cancelado')
                            <span class="badge bg-danger p-3">Cancelado</span>
                            @break
                    @endswitch
                </div>
            </div>
        </div>
    </div>

    {{-- Progreso del viaje --}}
    @if($viaje->etapas->count() > 0)
        <div class="card mb-4">
            <div class="card-body">
                <h6 class="card-title mb-3">
                    <i class="fas fa-chart-line me-2 text-primary"></i>
                    Progreso del Viaje
                </h6>
                <div class="progress" style="height: 20px;">
                    @php
                        $completadas = $viaje->etapas->where('estado', 'completado')->count();
                        $total = $viaje->etapas->count();
                        $porcentaje = $total > 0 ? round(($completadas / $total) * 100) : 0;
                    @endphp
                    <div class="progress-bar bg-success"
                         role="progressbar"
                         style="width: {{ $porcentaje }}%;"
                         aria-valuenow="{{ $porcentaje }}"
                         aria-valuemin="0"
                         aria-valuemax="100">
                        {{ $porcentaje }}%
                    </div>
                </div>
                <div class="d-flex justify-content-between mt-2">
                    <small class="text-muted">{{ $completadas }} de {{ $total }} etapas completadas</small>
                    @if($viaje->distancia_km)
                        <small class="text-muted">{{ $viaje->distancia_km }} km totales</small>
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- Información Principal --}}
    <div class="row">
        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-truck me-2 text-primary"></i>Información del Camión</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <th>Placa:</th>
                            <td><strong>{{ $viaje->camion->placa ?? 'N/A' }}</strong></td>
                        </tr>
                        <tr>
                            <th>Marca/Modelo:</th>
                            <td>{{ $viaje->camion->marca ?? '' }} {{ $viaje->camion->modelo ?? '' }}</td>
                        </tr>
                        <tr>
                            <th>Capacidad:</th>
                            <td>{{ $viaje->camion->capacidad_motos ?? 0 }} motos</td>
                        </tr>
                        <tr>
                            <th>Tipo:</th>
                            <td>
                                @if($viaje->camion && $viaje->camion->tipo == 'propio')
                                    <span class="badge bg-success">Propio</span>
                                @else
                                    <span class="badge bg-warning text-dark">Alquilado</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-user me-2 text-primary"></i>Información del Chofer</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <th>Nombre:</th>
                            <td><strong>{{ $viaje->chofer->nombre_completo ?? 'N/A' }}</strong></td>
                        </tr>
                        <tr>
                            <th>Licencia:</th>
                            <td>{{ $viaje->chofer->licencia ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Teléfono:</th>
                            <td>{{ $viaje->chofer->telefono ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Email:</th>
                            <td>{{ $viaje->chofer->email ?? 'N/A' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Ruta y Fechas --}}
    <div class="card mb-3">
        <div class="card-header bg-light">
            <h6 class="mb-0"><i class="fas fa-route me-2 text-primary"></i>Ruta y Fechas</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Origen:</strong> {{ $viaje->origen }}</p>
                    <p><strong>Fecha de Inicio:</strong> {{ \Carbon\Carbon::parse($viaje->fecha_inicio)->format('d/m/Y') }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Destino:</strong> {{ $viaje->destino }}</p>
                    <p><strong>Fecha de Fin:</strong> {{ $viaje->fecha_fin ? \Carbon\Carbon::parse($viaje->fecha_fin)->format('d/m/Y') : 'No definida' }}</p>
                </div>
            </div>
            @if($viaje->notas)
                <div class="alert alert-info mt-2">
                    <i class="fas fa-sticky-note me-2"></i>
                    <strong>Notas:</strong> {{ $viaje->notas }}
                </div>
            @endif
        </div>
    </div>

    {{-- Motos Transportadas --}}
    <div class="card mb-3">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h6 class="mb-0"><i class="fas fa-motorcycle me-2 text-primary"></i>Motos Transportadas</h6>
            <span class="badge bg-info">{{ $viaje->motosTransportadas->sum('cantidad') }} unidades</span>
        </div>
        <div class="card-body">
            @if($viaje->motosTransportadas->count() > 0)
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                        <tr>
                            <th>Modelo</th>
                            <th>Cantidad</th>
                            <th class="text-end">Precio Unitario</th>
                            <th class="text-end">Subtotal</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($viaje->motosTransportadas as $moto)
                            <tr>
                                <td>{{ $moto->modelo_moto }}</td>
                                <td>{{ $moto->cantidad }}</td>
                                <td class="text-end">${{ number_format($moto->precio_por_moto, 2) }}</td>
                                <td class="text-end">${{ number_format($moto->cantidad * $moto->precio_por_moto, 2) }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                        <tfoot>
                        <tr>
                            <th colspan="3" class="text-end">Total Ingresos:</th>
                            <th class="text-end text-success">${{ number_format($viaje->ingreso_total ?? 0, 2) }}</th>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            @else
                <p class="text-muted text-center py-3">
                    <i class="bi bi-info-circle me-2"></i>
                    No hay motos registradas en este viaje.
                </p>
            @endif
        </div>
    </div>

    {{-- Gastos --}}
    <div class="card mb-3">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h6 class="mb-0"><i class="fas fa-money-bill me-2 text-primary"></i>Gastos del Viaje</h6>
            <span class="badge bg-danger">${{ number_format($viaje->gasto_total ?? 0, 2) }}</span>
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
                            <th class="text-end">Monto</th>
                            <th>Comprobante</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($viaje->gastos as $gasto)
                            <tr>
                                <td>{{ $gasto->fecha_gasto->format('d/m/Y') }}</td>
                                <td>{{ $gasto->tipoGasto->nombre ?? 'N/A' }}</td>
                                <td>{{ $gasto->concepto }}</td>
                                <td class="text-end text-danger">${{ number_format($gasto->monto, 2) }}</td>
                                <td>
                                    @if($gasto->comprobante)
                                        <a href="{{ asset('storage/'.$gasto->comprobante) }}" target="_blank" class="btn btn-sm btn-info">
                                            <i class="fas fa-file-pdf"></i>
                                        </a>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Gráfico de gastos por tipo --}}
                @if($gastosPorTipo->count() > 0)
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <h6>Distribución por Tipo</h6>
                            <canvas id="graficoGastos" height="200"></canvas>
                        </div>
                        <div class="col-md-6">
                            <h6>Resumen por Tipo</h6>
                            <ul class="list-group">
                                @foreach($gastosPorTipo as $tipo => $datos)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        {{ $tipo }}
                                        <span class="badge bg-danger rounded-pill">
                                            ${{ number_format($datos['total'], 2) }} ({{ $datos['cantidad'] }})
                                        </span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif
            @else
                <p class="text-muted text-center py-3">
                    <i class="bi bi-info-circle me-2"></i>
                    No hay gastos registrados en este viaje.
                </p>
            @endif
        </div>
    </div>

    {{-- Resumen Financiero --}}
    <div class="card mb-3">
        <div class="card-header bg-light">
            <h6 class="mb-0"><i class="fas fa-chart-pie me-2 text-primary"></i>Resumen Financiero</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 text-center">
                    <div class="p-3">
                        <h6 class="text-muted">Ingresos</h6>
                        <h3 class="text-success">${{ number_format($viaje->ingreso_total ?? 0, 2) }}</h3>
                    </div>
                </div>
                <div class="col-md-4 text-center">
                    <div class="p-3">
                        <h6 class="text-muted">Gastos</h6>
                        <h3 class="text-danger">${{ number_format($viaje->gasto_total ?? 0, 2) }}</h3>
                    </div>
                </div>
                <div class="col-md-4 text-center">
                    <div class="p-3">
                        <h6 class="text-muted">Ganancia Neta</h6>
                        <h3 class="{{ ($viaje->ganancia_neta ?? 0) >= 0 ? 'text-success' : 'text-danger' }}">
                            ${{ number_format($viaje->ganancia_neta ?? 0, 2) }}
                        </h3>
                        @if($viaje->ingreso_total > 0)
                            <small>
                                Margen: {{ round(($viaje->ganancia_neta / $viaje->ingreso_total) * 100, 2) }}%
                            </small>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Etapas del Viaje --}}
    @if($viaje->etapas->count() > 0)
        <div class="card mb-3">
            <div class="card-header bg-light">
                <h6 class="mb-0"><i class="fas fa-tasks me-2 text-primary"></i>Etapas del Viaje</h6>
            </div>
            <div class="card-body">
                <div class="timeline">
                    @foreach($viaje->etapas as $etapa)
                        <div class="timeline-item mb-3">
                            <div class="d-flex align-items-start">
                                <div class="timeline-icon me-3">
                                    @if($etapa->estado == 'completado')
                                        <span class="badge bg-success rounded-circle p-2">
                                        <i class="fas fa-check"></i>
                                    </span>
                                    @elseif($etapa->estado == 'en_curso')
                                        <span class="badge bg-warning rounded-circle p-2">
                                        <i class="fas fa-play"></i>
                                    </span>
                                    @else
                                        <span class="badge bg-secondary rounded-circle p-2">
                                        <i class="fas fa-clock"></i>
                                    </span>
                                    @endif
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between">
                                        <h6 class="mb-1">{{ $etapa->nombre }}</h6>
                                        <small class="text-muted">Orden: {{ $etapa->orden }}</small>
                                    </div>
                                    <p class="mb-1"><i class="fas fa-map-marker-alt me-1"></i> {{ $etapa->ubicacion }}</p>
                                    @if($etapa->kilometraje_estimado)
                                        <p class="mb-1"><i class="fas fa-road me-1"></i> {{ $etapa->kilometraje_estimado }} km estimados</p>
                                    @endif
                                    @if($etapa->fecha_real_inicio)
                                        <p class="mb-1">
                                            <i class="fas fa-calendar-check me-1"></i>
                                            Inicio: {{ $etapa->fecha_real_inicio->format('d/m/Y H:i') }}
                                        </p>
                                    @endif
                                    @if($etapa->fecha_real_fin)
                                        <p class="mb-1">
                                            <i class="fas fa-calendar-times me-1"></i>
                                            Fin: {{ $etapa->fecha_real_fin->format('d/m/Y H:i') }}
                                        </p>
                                    @endif
                                    @if($etapa->notas)
                                        <p class="text-muted small"><i class="fas fa-sticky-note me-1"></i> {{ $etapa->notas }}</p>
                                    @endif
                                    @if($etapa->foto_evidencia)
                                        <a href="{{ asset('storage/'.$etapa->foto_evidencia) }}" target="_blank" class="btn btn-sm btn-info">
                                            <i class="fas fa-camera"></i> Ver evidencia
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</div>

@if($viaje->gastos->count() > 0)
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('graficoGastos')?.getContext('2d');
            if (ctx) {
                const gastosPorTipo = @json($gastosPorTipo);
                new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: Object.keys(gastosPorTipo),
                        datasets: [{
                            data: Object.values(gastosPorTipo).map(g => g.total),
                            backgroundColor: [
                                '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40',
                                '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40'
                            ]
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }
                });
            }
        });
    </script>
@endif

<style>
    .timeline-item {
        position: relative;
        padding-left: 20px;
        border-left: 2px solid #e9ecef;
    }
    .timeline-icon {
        position: relative;
        left: -27px;
        top: -3px;
    }
</style>
