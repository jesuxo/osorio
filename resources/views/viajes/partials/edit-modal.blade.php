{{-- resources/views/viajes/partials/edit-modal.blade.php --}}
<div class="container-fluid">
    <form id="formEditarViaje" onsubmit="guardarViaje(this)" action="{{ route('viajes.update', $viaje) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Folio</label>
                <input type="text" class="form-control" name="folio" value="{{ $viaje->folio }}" placeholder="Ej: VIAJE-001">
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Estado del Viaje</label>
                <select class="form-select" name="estado" required>
                    <option value="planeado" {{ $viaje->estado == 'planeado' ? 'selected' : '' }}>Planeado</option>
                    <option value="en_curso" {{ $viaje->estado == 'en_curso' ? 'selected' : '' }}>En Curso</option>
                    <option value="completado" {{ $viaje->estado == 'completado' ? 'selected' : '' }}>Completado</option>
                    <option value="cancelado" {{ $viaje->estado == 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                </select>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Camión <span class="text-danger">*</span></label>
                <select class="form-select" name="camion_id" required>
                    <option value="">Seleccione un camión</option>
                    @foreach($camiones as $camion)
                        <option value="{{ $camion->id }}"
                            {{ $viaje->camion_id == $camion->id ? 'selected' : '' }}
                            {{ $camion->activo ? '' : 'disabled' }}>
                            {{ $camion->placa }} - {{ $camion->marca }} {{ $camion->modelo }}
                            (Cap: {{ $camion->capacidad_motos }} motos)
                            @if(!$camion->activo)
                                - INACTIVO
                            @endif
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Chofer <span class="text-danger">*</span></label>
                <select class="form-select" name="chofer_id" required>
                    <option value="">Seleccione un chofer</option>
                    @foreach($choferes as $chofer)
                        <option value="{{ $chofer->id }}"
                            {{ $viaje->chofer_id == $chofer->id ? 'selected' : '' }}
                            {{ $chofer->activo ? '' : 'disabled' }}>
                            {{ $chofer->nombre_completo }} - {{ $chofer->licencia }}
                            @if(!$chofer->activo)
                                - INACTIVO
                            @endif
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4 mb-3">
                <label class="form-label">Fecha de Inicio <span class="text-danger">*</span></label>
                <input type="date" class="form-control" name="fecha_inicio" value="{{ $viaje->fecha_inicio->format('Y-m-d') }}" required>
            </div>

            <div class="col-md-4 mb-3">
                <label class="form-label">Fecha de Fin</label>
                <input type="date" class="form-control" name="fecha_fin" value="{{ $viaje->fecha_fin ? $viaje->fecha_fin->format('Y-m-d') : '' }}">
            </div>

            <div class="col-md-4 mb-3">
                <label class="form-label">Distancia (km)</label>
                <input type="number" class="form-control" name="distancia_km" step="0.01" min="0" value="{{ $viaje->distancia_km }}">
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Origen <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="origen" value="{{ $viaje->origen }}" required>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Destino <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="destino" value="{{ $viaje->destino }}" required>
            </div>

            <div class="col-12 mb-3">
                <label class="form-label">Notas</label>
                <textarea class="form-control" name="notas" rows="3">{{ $viaje->notas }}</textarea>
            </div>
        </div>

        {{-- Etapas existentes --}}
        @if($viaje->etapas->count() > 0)
            <div class="card mt-3 mb-3">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-tasks me-2"></i>Etapas del Viaje</h6>
                </div>
                <div class="card-body">
                    <p class="text-muted small">
                        <i class="bi bi-info-circle me-1"></i>
                        Las etapas se gestionan desde la sección de "Etapas" en el menú de acciones.
                    </p>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Etapa</th>
                                <th>Ubicación</th>
                                <th>Estado</th>
                                <th>KM Estimado</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($viaje->etapas as $etapa)
                                <tr>
                                    <td>{{ $etapa->orden }}</td>
                                    <td>{{ $etapa->nombre }}</td>
                                    <td>{{ $etapa->ubicacion }}</td>
                                    <td>
                                        @if($etapa->estado == 'completado')
                                            <span class="badge bg-success">Completado</span>
                                        @elseif($etapa->estado == 'en_curso')
                                            <span class="badge bg-warning text-dark">En Curso</span>
                                        @else
                                            <span class="badge bg-secondary">Pendiente</span>
                                        @endif
                                    </td>
                                    <td>{{ $etapa->kilometraje_estimado ?? 'N/A' }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif

        <div class="row mt-4">
            <div class="col-12 d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x me-2"></i>Cancelar
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-2"></i>Actualizar Viaje
                </button>
            </div>
        </div>
    </form>
</div>
