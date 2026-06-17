@extends('layouts.master')
@section('title')
     Detalles del Chofer
@endsection
@section('css')
    <style>
        .tituloa{
            font-size: 24px;
        }
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
        .cajapequenacolor {
            margin: 5px;
            padding: 1px;
            float: left;
            width: 120px;
            height: 120px;
            -moz-border-radius: 5px;
            border-radius: 5px;
            color: #fff;
            overflow: hidden;
            background-color: #e0f2ff !important;
        }
        .titulocaja {
            border-radius: 5px;
            min-height: 50px;
            font-size: 18px;
            text-align: center;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .underline{
            text-decoration: underline;
        }
    </style>
@endsection
@section('content')
    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('choferes.index') }}">Choferes</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ $chofer->nombre_completo }}</li>
                    </ol>
                </nav>
                <div class="d-flex justify-content-between align-items-center">
                    <h1 class="h3 mb-0 text-gray-800">
                        <i class="fas fa-user me-2"></i>Detalles del Chofer
                    </h1>
                    <div>
                        <a href="{{ route('choferes.edit', $chofer) }}" class="btn btn-warning">
                            <i class="fas fa-edit me-2"></i>Editar
                        </a>
                        <a href="{{ route('choferes.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Volver
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            {{-- Información Principal --}}
            <div class="col-md-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Información Personal</h6>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-4">
                            @if($chofer->foto)
                                <img src="{{ $chofer->foto_url }}" alt="Foto del chofer"
                                     class="rounded-circle img-fluid mb-3"
                                     style="width: 150px; height: 150px; object-fit: cover; border: 3px solid #4e73df;">
                            @else
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3"
                                     style="width: 150px; height: 150px; font-size: 3rem; border: 3px solid #4e73df;">
                                    {{ $chofer->iniciales }}
                                </div>
                            @endif

                            <h4>{{ $chofer->nombre_completo }}</h4>
                            <p class="text-muted">
                                <i class="fas fa-id-card me-1"></i> Licencia: {{ $chofer->licencia }}
                            </p>
                            <div class="mb-2">
                                @if($chofer->trashed())
                                    <span class="badge bg-secondary">Oculto</span>
                                @else
                                    {!! $chofer->estado_badge !!}
                                    @if($estadisticas['disponible'])
                                        <span class="badge bg-success">Disponible</span>
                                    <!--@ else
                                        <span class="badge bg-warning text-dark">En Viaje</span>-->
                                    @endif
                                @endif
                            </div>
                        </div>

                        <table class="table table-borderless">
                            <tr>
                                <th><i class="fas fa-phone me-2"></i>Teléfono:</th>
                                <td>{{ $chofer->telefono ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th><i class="fas fa-envelope me-2"></i>Email:</th>
                                <td>{{ $chofer->email ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th><i class="fas fa-calendar-alt me-2"></i>Edad:</th>
                                <td>{{ $chofer->edad ?? 'N/A' }} años</td>
                            </tr>
                            <tr>
                                <th><i class="fas fa-birthday-cake me-2"></i>Nacimiento:</th>
                                <td>{{ $chofer->fecha_nacimiento?->format('d/m/Y') ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th><i class="fas fa-calendar-check me-2"></i>Ingreso:</th>
                                <td>{{ $chofer->fecha_ingreso?->format('d/m/Y') ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th><i class="fas fa-clock me-2"></i>Antigüedad:</th>
                                <td>{{ $chofer->antiguedad ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th><i class="fas fa-tint me-2"></i>Tipo Sangre:</th>
                                <td><span class="badge bg-danger">{{ $chofer->tipo_sangre ?? 'N/A' }}</span></td>
                            </tr>
                        </table>

                        @if($chofer->direccion)
                            <div class="alert alert-info mt-3">
                                <strong><i class="fas fa-map-marker-alt me-2"></i>Dirección:</strong>
                                <p class="mb-0">{{ $chofer->direccion }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Contacto de Emergencia --}}
                @if($chofer->contacto_emergencia_nombre || $chofer->contacto_emergencia_telefono)
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Contacto de Emergencia</h6>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless">
                                @if($chofer->contacto_emergencia_nombre)
                                    <tr>
                                        <th><i class="fas fa-user me-2"></i>Nombre:</th>
                                        <td>{{ $chofer->contacto_emergencia_nombre }}</td>
                                    </tr>
                                @endif
                                @if($chofer->contacto_emergencia_telefono)
                                    <tr>
                                        <th><i class="fas fa-phone-alt me-2"></i>Teléfono:</th>
                                        <td>{{ $chofer->contacto_emergencia_telefono }}</td>
                                    </tr>
                                @endif
                            </table>
                        </div>
                    </div>
                @endif

                {{-- Observaciones Médicas --}}
                @if($chofer->observaciones_medicas)
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Observaciones Médicas</h6>
                        </div>
                        <div class="card-body">
                            <p class="mb-0">{{ $chofer->observaciones_medicas }}</p>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Estadísticas y Viajes --}}
            <div class="col-md-8">

                <div class="row row-cols-xxl-3 row-cols-1">
                    <div class="col">
                        <div class="card card-animate">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div class="vr rounded bg-secondary opacity-50" style="width: 4px;"></div>
                                    <div class="flex-grow-1 ms-3">
                                        <p class="text-uppercase fw-medium text-muted fs-14 text-truncate">Total Viajes</p>
                                        <h4 class="fs-22 fw-semibold mb-3"> {{ $estadisticas['total_viajes'] }}</h4>

                                    </div>
                                    <div class="avatar-xs flex-shrink-0">
                                    <span class="avatar-title bg-secondary-subtle text-secondary rounded fs-3">
                                        <i class="ph-truck"></i>
                                    </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col">

                        <div class="card card-animate">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div class="vr rounded bg-info opacity-50" style="width: 4px;"></div>
                                    <div class="flex-grow-1 ms-3">
                                        <p class="text-uppercase fw-medium text-muted fs-14 text-truncate">Viajes Completados</p>
                                        <h4 class="fs-22 fw-semibold mb-3">  {{ $estadisticas['viajes_completados'] }} </h4>

                                    </div>
                                    <div class="avatar-xs flex-shrink-0">
                                    <span class="avatar-title bg-info-subtle text-info rounded fs-3">
                                      <i class="bi bi-check-circle" style="font-size: 17px"></i>
                                    </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col">

                        <div class="card card-animate">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div class="vr rounded bg-warning opacity-50" style="width: 4px;"></div>
                                    <div class="flex-grow-1 ms-3">
                                        <p class="text-uppercase fw-medium text-muted fs-14 text-truncate">Motos Transportadas</p>
                                        <h4 class="fs-22 fw-semibold mb-3">  {{ $estadisticas['total_motos'] }}</h4>

                                    </div>
                                    <div class="avatar-xs flex-shrink-0">
                                    <span class="avatar-title bg-warning-subtle text-warning rounded fs-3">
                                        <i class="ri-motorbike-line"></i>
                                    </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                {{-- Resumen Financiero --}}
                @if($financiero)
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Resumen Financiero</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4 text-center">
                                    <h6 class="text-muted">Total Ingresos</h6>
                                    <h4 class="text-success">${{ number_format($financiero['total_ingresos'], 2) }}</h4>
                                </div>
                                <div class="col-md-4 text-center">
                                    <h6 class="text-muted">Total Gastos</h6>
                                    <h4 class="text-danger">${{ number_format($financiero['total_gastos'], 2) }}</h4>
                                </div>
                                <div class="col-md-4 text-center">
                                    <h6 class="text-muted">Ganancia Neta</h6>
                                    <h4 class="{{ $financiero['total_ganancias'] >= 0 ? 'text-success' : 'text-danger' }}">
                                        ${{ number_format($financiero['total_ganancias'], 2) }}
                                    </h4>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Últimos Viajes --}}
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">Últimos Viajes</h6>
                        <a href="/viajes?search={{urlencode($chofer['nombre'].' '.$chofer['apellido'])}}" class="btn btn-sm btn-primary">Ver todos</a>
                    </div>
                    <div class="card-body">
                        @if($chofer->viajes->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                    <tr>
                                        <th>Folio</th>
                                        <th>Camion</th>
                                        <th>Ruta</th>
                                        <th>Fecha</th>
                                        <th>Motos</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($chofer->viajes as $viaje)
                                        <tr>
                                            <td>{{ $viaje->folio ?? 'N/A' }}</td>
                                            <td>{{ $viaje->camion->placa ?? 'N/A' }}</td>
                                            <td>{{ $viaje->origen }} → {{ $viaje->destino }}</td>
                                            <td>{{ $viaje->fecha_inicio->format('d/m/Y') }}</td>
                                            <td class="text-center">
                                                <span class="badge bg-info">{{ $viaje->motosTransportadas->sum('cantidad') }}</span>
                                            </td>
                                            <td>
                                                @switch($viaje->estado)
                                                    @case('completado')
                                                        <span class="badge bg-success">Completado</span>
                                                        @break
                                                    @case('en_curso')
                                                        <span class="badge bg-warning text-dark">En Curso</span>
                                                        @break
                                                    @case('planeado')
                                                        <span class="badge bg-info">Planeado</span>
                                                        @break
                                                    @case('cancelado')
                                                        <span class="badge bg-danger">Cancelado</span>
                                                        @break
                                                @endswitch
                                            </td>
                                            <td>
                                                <a href="{{ route('viajes.show', $viaje) }}" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-muted text-center py-3">
                                <i class="bi bi-info-circle me-2"></i>
                                Este chofer aún no tiene viajes registrados.
                            </p>
                        @endif
                    </div>
                </div>

                {{-- Gráfico de Rendimiento --}}
                @if($chofer->viajes->count() > 0)
                    <div class="card shadow">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Rendimiento Mensual</h6>
                        </div>
                        <div class="card-body">
                            <canvas id="rendimientoChart" height="200"></canvas>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
@section('scripts')

    <script src="{{ URL::asset('build/js/app.js') }}"></script>
    @if($chofer->viajes->count() > 0)
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Procesar datos de viajes por mes
                const viajes = @json($chofer->viajes);
                const meses = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
                const viajesPorMes = new Array(12).fill(0);

                viajes.forEach(viaje => {
                    const fecha = new Date(viaje.fecha_inicio);
                    const mes = fecha.getMonth();
                    viajesPorMes[mes]++;
                });

                const ctx = document.getElementById('rendimientoChart').getContext('2d');
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: meses,
                        datasets: [{
                            label: 'Viajes por Mes',
                            data: viajesPorMes,
                            borderColor: '#4e73df',
                            backgroundColor: 'rgba(78, 115, 223, 0.05)',
                            tension: 0.3,
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1
                                }
                            }
                        }
                    }
                });
            });
        </script>
    @endif
@endsection
