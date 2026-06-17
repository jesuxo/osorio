{{-- resources/views/viajes/partials/etapas-admin-modal.blade.php --}}
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12">
            <div class="alert alert-info">
                <i class="bi bi-info-circle me-2"></i>
                Administra las etapas del viaje. Puedes agregar, editar o eliminar etapas.
            </div>
        </div>
    </div>

    {{-- Información del viaje --}}
    <div class="row mb-3">
        <div class="col-12">
            <div class="card bg-light">
                <div class="card-body py-2">
                    <div class="row">
                        <div class="col-md-4">
                            <small class="text-muted">Viaje:</small>
                            <strong>#{{ $viaje->folio ?? $viaje->id }}</strong>
                        </div>
                        <div class="col-md-4">
                            <small class="text-muted">Ruta:</small>
                            <strong>{{ $viaje->origen }} → {{ $viaje->destino }}</strong>
                        </div>
                        <div class="col-md-4">
                            <small class="text-muted">Progreso:</small>
                            <div class="progress" style="height: 5px;">
                                @php
                                    $totalEtapas = $viaje->etapas->count();
                                    $completadas = $viaje->etapas->where('estado', 'completado')->count();
                                    $porcentaje = $totalEtapas > 0 ? round(($completadas / $totalEtapas) * 100) : 0;
                                @endphp
                                <div class="progress-bar bg-success" style="width: {{ $porcentaje }}%"></div>
                            </div>
                            <small>{{ $completadas }}/{{ $totalEtapas }} etapas</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Formulario para agregar nueva etapa --}}
    <div class="card mb-3 border-success">
        <div class="card-header bg-success text-white">
            <h6 class="mb-0"><i class="bi bi-plus-circle me-2"></i>Agregar Nueva Etapa</h6>
        </div>
        <div class="card-body">
            <form id="formAgregarEtapa" onsubmit="agregarEtapa(event)">
                @csrf
                <input type="hidden" id="viaje_id" value="{{ $viaje->id }}">
                <div class="row">
                    <div class="col-md-4 mb-2">
                        <input type="text" class="form-control" id="nuevo_nombre" placeholder="Nombre de la etapa" required>
                    </div>
                    <div class="col-md-4 mb-2">
                        <input type="text" class="form-control" id="nueva_ubicacion" placeholder="Ubicación" required>
                    </div>
                    <div class="col-md-2 mb-2">
                        <input type="number" class="form-control" id="nuevo_kilometraje" placeholder="KM estimado" step="0.01" min="0">
                    </div>
                    <div class="col-md-2 mb-2">
                        <button type="submit" class="btn btn-success w-100">
                            <i class="bi bi-save me-1"></i>Agregar
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </div>

    {{-- Lista de etapas existentes --}}
    <div class="card">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h6 class="mb-0"><i class="bi bi-list me-2"></i>Etapas Registradas</h6>
            <span class="badge bg-light text-dark">{{ $viaje->etapas->count() }} etapas</span>
        </div>
        <div class="card-body">
            @if($viaje->etapas->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover" id="tablaEtapas">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Nombre</th>
                            <th>Ubicación</th>
                            <th>KM</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($viaje->etapas->sortBy('orden') as $etapa)
                            <tr id="etapa-{{ $etapa->id }}" data-id="{{ $etapa->id }}" data-orden="{{ $etapa->orden }}">
                                <td>
                                    <span class="orden-text">{{ $etapa->orden }}</span>
                                    <input type="number" class="form-control orden-input" value="{{ $etapa->orden }}" min="1" style="display: none; width: 70px;">
                                </td>
                                <td>
                                    <span class="nombre-text">{{ $etapa->nombre }}</span>
                                    <input type="text" class="form-control nombre-input" value="{{ $etapa->nombre }}" style="display: none;">
                                </td>
                                <td>
                                    <span class="ubicacion-text">{{ $etapa->ubicacion }}</span>
                                    <input type="text" class="form-control ubicacion-input" value="{{ $etapa->ubicacion }}" style="display: none;">
                                </td>
                                <td>
                                    <span class="km-text">{{ $etapa->kilometraje_estimado ?? 'N/A' }}</span>
                                    <input type="number" class="form-control km-input" value="{{ $etapa->kilometraje_estimado }}" step="0.01" min="0" style="display: none; width: 100px;">
                                </td>

                                <td>
                                    @if($etapa->estado == 'completado')
                                        <span class="badge bg-success">Completado</span>
                                    @elseif($etapa->estado == 'en_curso')
                                        <span class="badge bg-warning text-dark">En Curso</span>
                                    @else
                                        <span class="badge bg-secondary">Pendiente</span>
                                    @endif
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-warning btn-editar" onclick="editarEtapa({{ $etapa->id }})" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="btn btn-sm btn-success btn-guardar" onclick="guardarEtapa({{ $etapa->id }})" style="display: none;" title="Guardar">
                                        <i class="bi bi-save-fill"></i>
                                    </button>
                                    <button class="btn btn-sm btn-secondary btn-cancelar" onclick="cancelarEdicionEtapa({{ $etapa->id }})" style="display: none;" title="Cancelar">
                                        <i class="bi bi-x"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger" onclick="eliminarEtapa({{ $etapa->id }})" title="Eliminar">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-muted text-center py-3">
                    <i class="bi bi-info-circle me-2"></i>
                    No hay etapas registradas en este viaje. Agrega una usando el formulario de arriba.
                </p>
            @endif
        </div>
    </div>
</div>


<input type="hidden" id="viajetaletapas" value="{{ $viaje->id }}">
