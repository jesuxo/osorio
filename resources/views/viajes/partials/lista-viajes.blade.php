{{-- resources/views/viajes/partials/lista-viajes.blade.php --}}
@foreach($viajes as $viaje)
    <div class="card viaje-card estado-{{ $viaje->estado }} shadow-sm" data-viaje-id="{{ $viaje->id }}">
        <div class="card-body">
            {{-- Cabecera del viaje --}}
            <div class="row">
                <div class="col-md-8">
                    <div class="d-flex align-items-center mb-2 flex-wrap">
                        <h5 class="mb-0 me-3">
                            <strong>Viaje #{{ $viaje->folio ?? $viaje->id }}</strong>
                        </h5>
                        @switch($viaje->estado)
                            @case('planeado')
                                <span class="badge bg-secondary me-2">Planeado</span>
                                @break
                            @case('en_curso')
                                <span class="badge bg-warning text-dark me-2">En Curso</span>
                                @break
                            @case('completado')
                                <span class="badge bg-success me-2">Completado</span>
                                @break
                            @case('cancelado')
                                <span class="badge bg-danger me-2">Cancelado</span>
                                @break
                        @endswitch

                        @if($viaje->fecha_fin && $viaje->estado == 'completado')
                            <small class="text-muted">
                                <i class="bi bi-check-circle me-1"></i>
                                Finalizado: {{ \Carbon\Carbon::parse($viaje->fecha_fin)->format('d/m/Y') }}
                            </small>
                        @endif
                    </div>

                    {{-- Información principal --}}
                    <div class="row">
                        <div class="col-md-6">
                            <p class="mb-1">
                                <i class="bi bi-truck text-primary me-2" style="width: 20px;"></i>
                                <strong>Camion:</strong>
                                <span class="text-primary">{{ $viaje->camion->placa ?? 'N/A' }}</span>
                                <small class="text-muted">({{ $viaje->camion->marca ?? '' }} {{ $viaje->camion->modelo ?? '' }})</small>
                            </p>
                            <p class="mb-1">
                                <a class="text-success me-2" style="width: 20px;" href="javascript:;" onclick="enviarLinkWhatsApp({{ $viaje->folio }}, '{{ $viaje->chofer->telefono ?? '' }}')"
                                   data-bs-toggle="tooltip" title="Enviar link al chofer por WhatsApp">
                                    <i class="bi bi-whatsapp"></i>
                                </a>
                                <strong>Chofer:</strong>
                                <span class="text-info">{{ $viaje->chofer->nombre_completo ?? 'N/A' }}</span>

                            </p>
                            <p class="mb-1">
                                <i class="ri-route-line text-success me-2" style="width: 20px;"></i>
                                <strong>Ruta:</strong>
                                <span class="text-success">{{ $viaje->origen }}</span>
                                <i class="bi bi-arrow-right  mx-1"></i>
                                <span class="text-danger">{{ $viaje->destino }}</span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1">
                                <i class="bi bi-calendar text-warning me-2" style="width: 20px;"></i>
                                <strong>Inicio:</strong>
                                {{ \Carbon\Carbon::parse($viaje->fecha_inicio)->format('d/m/Y') }}
                                @if($viaje->fecha_fin && $viaje->estado != 'completado')
                                    - {{ \Carbon\Carbon::parse($viaje->fecha_fin)->format('d/m/Y') }}
                                @endif
                            </p>
                            <p class="mb-1">
                                <i class="ri-motorbike-fill text-danger me-2" style="width: 20px;"></i>
                                <strong>Motos:</strong>
                                <span class="badge bg-info" data-motos-count="{{ $viaje->total_motos ?? 0 }}">
                                    {{ $viaje->total_motos ?? 0 }} unidades
                                </span>
                            </p>
                            <p class="mb-1">
                                <i class="bi bi-clock text-secondary me-2" style="width: 20px;"></i>
                                <strong>Duración:</strong>
                                {{ $viaje->tiempo_transcurrido ?? 'N/A' }}
                                @if($viaje->distancia_km)
                                    <small class="text-muted ms-2">({{ $viaje->distancia_km }} km)</small>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Información financiera --}}
                <div class="col-md-4">
                    <div class="row text-end">
                        <div class="col-6">
                            <div class="mb-2">
                                <small class="text-muted d-block">Ingresos</small>
                                <h5 class="text-success mb-0">
                                    ${{ number_format($viaje->ingreso_total ?? 0, 2) }}
                                </h5>
                            </div>
                            <div class="mb-2">
                                <small class="text-muted d-block">Gastos</small>
                                <h5 class="text-danger mb-0">
                                    ${{ number_format($viaje->total_gastos ?? 0, 2) }}
                                </h5>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="mb-2">
                                <small class="text-muted d-block">Ganancia</small>
                                <h5 class="{{ ($viaje->ganancia_neta ?? 0) >= 0 ? 'ganancia-positiva' : 'ganancia-negativa' }} mb-0">
                                    ${{ number_format($viaje->ganancia_neta ?? 0, 2) }}
                                </h5>
                            </div>
                            @if($viaje->ingreso_total > 0)
                                <div class="mb-2">
                                    <small class="text-muted d-block">Margen</small>
                                    <h6 class="mb-0">
                                        {{ round(($viaje->ganancia_neta / $viaje->ingreso_total) * 100, 1) }}%
                                    </h6>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Barra de progreso --}}
            @if($viaje->etapas->count() > 0)
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <small class="text-muted">
                                <i class="bi bi-list-task me-1"></i>
                                Progreso del viaje
                            </small>
                            <div>
                                <small class="text-muted me-2">
                                    {{ $viaje->etapas->where('estado', 'completado')->count() }}/{{ $viaje->etapas->count() }} etapas
                                </small>
                                <small class="fw-bold">{{ $viaje->progreso }}%</small>
                            </div>
                        </div>
                        <div class="progress">
                            @php
                                $completadas = $viaje->etapas->where('estado', 'completado')->count();
                                $en_curso = $viaje->etapas->where('estado', 'en_curso')->count();
                                $total = $viaje->etapas->count();
                                $porcentajeCompletado = $total > 0 ? round(($completadas / $total) * 100) : 0;
                                $porcentajeEnCurso = $total > 0 && $en_curso > 0 ? round(($en_curso / $total) * 100) : 0;
                            @endphp
                            <div class="progress-bar bg-success" role="progressbar"
                                 style="width: {{ $porcentajeCompletado }}%"></div>
                            @if($porcentajeEnCurso > 0)
                                <div class="progress-bar bg-warning" role="progressbar"
                                     style="width: {{ $porcentajeEnCurso }}%"></div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            {{-- Etiquetas rápidas --}}
            <div class="row mt-3">
                <div class="col-12">
                    <div class="d-flex flex-wrap gap-2">
                        @if($viaje->etapas->where('estado', 'en_curso')->first())
                            @php $etapaActual = $viaje->etapas->where('estado', 'en_curso')->first(); @endphp
                            <span class="badge bg-warning text-dark">
                                <i class="bi bi-play-circle me-1"></i>
                                Etapa actual: {{ $etapaActual->nombre }}
                            </span>
                        @endif

                        @if($viaje->notas)
                            <span class="badge bg-info" data-bs-toggle="tooltip" title="{{ $viaje->notas }}">
                                <i class="ri-sticky-note-fill  me-1"></i>
                                Notas
                            </span>
                        @endif

                        @if($viaje->gastos->count() > 0)
                            <span class="badge bg-danger">
                                <i class="ri-bill-line me-1"></i>
                                {{ $viaje->gastos->count() }} gastos
                            </span>
                        @endif

                        @if($viaje->ultimoSeguimiento)
                            <span class="badge bg-secondary">
                                <i class="mdi mdi-map-marker-alert me-1"></i>
                                Última ubicación: hace {{ $viaje->ultimoSeguimiento->fecha_hora->diffForHumans() }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Acciones --}}
            <div class="row mt-3">
                <div class="col-12">
                    <div class="btn-group" role="group">
                        <button class="btn btn-sm btn-info action-btn" style="padding: 0"onclick="verViaje({{ $viaje->id }})" data-bs-toggle="tooltip" title="Ver detalles">
                            <i class="bi bi-eye"style="font-size: 20px"></i>
                        </button>
                        <button class="btn btn-sm btn-warning action-btn" style="padding: 0" onclick="editarViaje({{ $viaje->id }})" data-bs-toggle="tooltip" title="Editar viaje">
                            <i class="bi bi-pencil"style="font-size: 20px"></i>
                        </button>

                        <button class="btn btn-sm btn-info action-btn" style="padding: 0" onclick="adminMotos({{ $viaje->id }})" data-bs-toggle="tooltip" title="Administrar motos">
                            <i class="ri-motorbike-fill" style="font-size: 20px"></i>
                        </button>

                        <button class="btn btn-sm btn-danger action-btn" style="padding: 0" onclick="adminGastos({{ $viaje->id }})" data-bs-toggle="tooltip" title="Administrar gastos">
                            <i class="ri-bill-line"style="font-size: 20px"></i>
                        </button>

                        @if(  $viaje->estado != 'cancelado')
                            <button class="btn btn-sm btn-primary action-btn" style="padding: 0"  onclick="adminEtapas({{ $viaje->id }})" data-bs-toggle="tooltip" title="Gestionar etapas">
                                <i class="bi-list-task"style="font-size: 20px"></i>
                            </button>
                            <button class="btn btn-sm btn-secondary action-btn" style="padding: 0" onclick="seguimientoViaje({{ $viaje->id }})" data-bs-toggle="tooltip" title="Seguimiento">
                                <i class="mdi mdi-map-marker-distance"style="font-size: 20px"></i>
                            </button>
                        @endif
                        @if($viaje->estado == 'en_curso')
                            <button class="btn btn-sm btn-success action-btn"  style="padding: 0" onclick="completarViaje({{ $viaje->id }})" data-bs-toggle="tooltip" title="Completar viaje">
                                <i class="mdi mdi-map-check" style="font-size: 20px"></i>
                            </button>
                        @endif
                        @if($viaje->estado != 'completado' && $viaje->estado != 'cancelado')
                            <button class="btn btn-sm btn-danger action-btn" style="padding: 0" onclick="cancelarViaje({{ $viaje->id }})" data-bs-toggle="tooltip" title="Cancelar viaje">
                                <i class="bi-x-circle"style="font-size: 20px"></i>
                            </button>
                        @endif
                        <button class="btn btn-sm btn-danger action-btn" style="padding: 0" onclick="eliminarViaje({{ $viaje->id }})" data-bs-toggle="tooltip" title="Eliminar viaje">
                            <i class="bi bi-trash"style="font-size: 20px"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endforeach

@if($viajes->isEmpty())
    <div class="alert alert-info text-center py-5">
        <i class="bi bi-info-circle fa-3x mb-3"></i>
        <h5>No se encontraron viajes</h5>
        <p class="mb-3">No hay viajes que coincidan con los criterios de búsqueda.</p>
        <button class="btn btn-primary" onclick="abrirModalCrear()">
            <i class="bi bi-plus-circle me-2"></i>Crear  viaje nuevo
        </button>
    </div>
@endif

{{-- Paginación --}}
@if(method_exists($viajes, 'links'))
    <div class="d-flex justify-content-end mt-4">
        {{ $viajes->withQueryString()->links() }}
    </div>
@endif
