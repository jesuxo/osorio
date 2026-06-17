@extends('layouts.master')
@section('title')
     Detalles del Camión
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
                        <li class="breadcrumb-item"><a href="{{ route('camiones.index') }}">Camiones</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ $camion->placa }}</li>
                    </ol>
                </nav>
                <div class="d-flex justify-content-between align-items-center">
                    <h1 class="h3 mb-0 text-gray-800">
                        <i class="fas fa-truck me-2"></i>Detalles del Camión
                    </h1>
                    <div>
                        <a href="{{ route('camiones.edit', $camion) }}" class="btn btn-warning">
                            <i class="fas fa-edit me-2"></i>Editar
                        </a>
                        <a href="{{ route('camiones.index') }}" class="btn btn-secondary">
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
                        <h6 class="m-0 font-weight-bold text-primary">Información General</h6>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-4">
                            <div class="display-1 text-primary">
                                <i class="fas fa-truck"></i>
                            </div>
                            <h4>{{ $camion->marca }} {{ $camion->modelo }}</h4>
                            <p class="text-muted">{{ $camion->placa }}</p>
                        </div>

                        <table class="table table-borderless">
                            <tr>
                                <th>Tipo:</th>
                                <td>
                                    @if($camion->tipo == 'propio')
                                        <span class="badge bg-success">Propio</span>
                                    @else
                                        <span class="badge bg-warning text-dark">Alquilado</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Capacidad:</th>
                                <td><span class="badge bg-info">{{ $camion->capacidad_motos }} motos</span></td>
                            </tr>
                            <tr>
                                <th>Estado:</th>
                                <td>
                                    @if($camion->activo)
                                        <span class="badge bg-success">Activo</span>
                                    @else
                                        <span class="badge bg-danger">Inactivo</span>
                                    @endif
                                </td>
                            </tr>
                            @if($camion->tipo == 'alquilado')
                                <tr>
                                    <th>Costo Alquiler:</th>
                                    <td><strong>${{ number_format($camion->costo_alquiler, 2) }}</strong></td>
                                </tr>
                            @endif
                            <tr>
                                <th>Registrado:</th>
                                <td>{{ $camion->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                            <tr>
                                <th>Última actualización:</th>
                                <td>{{ $camion->updated_at->format('d/m/Y H:i') }}</td>
                            </tr>
                        </table>

                        @if($camion->notas)
                            <div class="alert alert-info mt-3">
                                <strong><i class="bi bi-info-circle me-2"></i>Notas:</strong>
                                <p class="mb-0">{{ $camion->notas }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Estadísticas --}}
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
                                        <i class="ph-truck"  style="font-size: 20px"></i>
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
                                        <h4 class="fs-22 fw-semibold mb-3">  {{ $estadisticas['total_motos_transportadas'] }}</h4>

                                    </div>
                                    <div class="avatar-xs flex-shrink-0">
                                    <span class="avatar-title bg-warning-subtle text-warning rounded fs-3">
                                        <i class="ri-motorbike-line"  style="font-size: 20px"></i>
                                    </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                {{-- Últimos Viajes --}}
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">Últimos Viajes</h6>
                        <a href="#" class="btn btn-sm btn-primary">Ver todos</a>
                    </div>
                    <div class="card-body">
                        @if($camion->viajes->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                    <tr>
                                        <th>Folio</th>
                                        <th>Chofer</th>
                                        <th>Ruta</th>
                                        <th>Fecha</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($camion->viajes as $viaje)
                                        <tr>
                                            <td>{{ $viaje->folio ?? 'N/A' }}</td>
                                            <td>{{ $viaje->chofer->nombre_completo ?? 'N/A' }}</td>
                                            <td>{{ $viaje->origen }} → {{ $viaje->destino }}</td>
                                            <td>{{ $viaje->fecha_inicio->format('d/m/Y') }}</td>
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
                                Este camión aún no tiene viajes registrados.
                            </p>
                        @endif
                    </div>
                </div>

                {{-- Gráfico de rendimiento (opcional) --}}
                @if($camion->viajes->count() > 0)
                    <div class="card shadow">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Rendimiento Mensual</h6>
                        </div>
                        <div class="card-body">
                            <canvas id="rendimientoChart"></canvas>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
@section('scripts')

    <script src="{{ URL::asset('build/js/app.js') }}"></script>
    @if($camion->viajes->count() > 0)
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Datos de ejemplo para el gráfico
                const ctx = document.getElementById('rendimientoChart').getContext('2d');

                // Aquí puedes procesar los datos reales de los viajes
                // Por ahora, datos de ejemplo
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun'],
                        datasets: [{
                            label: 'Viajes por Mes',
                            data: [12, 19, 3, 5, 2, 3],
                            borderColor: 'rgb(75, 192, 192)',
                            tension: 0.1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false
                    }
                });
            });
        </script>
    @endif
@endsection
