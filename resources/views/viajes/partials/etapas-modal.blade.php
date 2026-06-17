{{-- resources/views/viajes/partials/etapas-modal.blade.php --}}
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12">
            <div class="alert alert-info">
                <i class="bi bi-info-circle me-2"></i>
                Gestiona las etapas del viaje. Puedes marcarlas como completadas a medida que avanza el viaje.
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
                                <div class="progress-bar bg-success" role="progressbar"
                                     style="width: {{ $porcentaje }}%"></div>
                            </div>
                            <small>{{ $completadas }}/{{ $totalEtapas }} etapas</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Lista de etapas --}}
    <div class="row">
        <div class="col-12">
            @forelse($viaje->etapas as $etapa)
                <div class="card mb-3 etapa-card border-{{ $etapa->estado == 'completado' ? 'success' : ($etapa->estado == 'en_curso' ? 'warning' : 'secondary') }}">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-1 text-center">
                                <span class="badge bg-{{ $etapa->estado == 'completado' ? 'success' : ($etapa->estado == 'en_curso' ? 'warning' : 'secondary') }} rounded-circle p-3">
                                    {{ $etapa->orden }}
                                </span>
                            </div>
                            <div class="col-md-7">
                                <h5 class="mb-1">{{ $etapa->nombre }}</h5>
                                <p class="mb-1">
                                    <i class="fas fa-map-marker-alt me-1 text-muted"></i>
                                    {{ $etapa->ubicacion }}
                                </p>
                                @if($etapa->kilometraje_estimado)
                                    <p class="mb-1">
                                        <i class="fas fa-road me-1 text-muted"></i>
                                        KM Estimado: {{ number_format($etapa->kilometraje_estimado, 2) }}
                                    </p>
                                @endif
                                @if($etapa->kilometraje_real)
                                    <p class="mb-1">
                                        <i class="fas fa-road me-1 text-success"></i>
                                        KM Real: {{ number_format($etapa->kilometraje_real, 2) }}
                                    </p>
                                @endif
                                <div class="mt-2">
                                    <small class="text-muted">
                                        <i class="fas fa-calendar me-1"></i>
                                        @if($etapa->fecha_real_inicio)
                                            Inicio: {{ $etapa->fecha_real_inicio->format('d/m/Y H:i') }}
                                        @elseif($etapa->fecha_estimada_inicio)
                                            Est. inicio: {{ $etapa->fecha_estimada_inicio->format('d/m/Y H:i') }}
                                        @endif
                                    </small>
                                    @if($etapa->fecha_real_fin)
                                        <br>
                                        <small class="text-muted">
                                            <i class="fas fa-calendar-check me-1"></i>
                                            Fin: {{ $etapa->fecha_real_fin->format('d/m/Y H:i') }}
                                        </small>
                                    @endif
                                </div>
                                @if($etapa->notas)
                                    <div class="alert alert-light mt-2 mb-0 py-1 px-2">
                                        <small><i class="fas fa-sticky-note me-1"></i> {{ $etapa->notas }}</small>
                                    </div>
                                @endif
                            </div>
                            <div class="col-md-4 text-end">
                                <span class="badge bg-{{ $etapa->estado == 'completado' ? 'success' : ($etapa->estado == 'en_curso' ? 'warning' : 'secondary') }} mb-2">
                                    {{ ucfirst($etapa->estado) }}
                                </span>
                                <br>
                                @if($etapa->estado == 'pendiente')
                                    <button class="btn btn-sm btn-warning mt-2" onclick="cambiarEstadoEtapa({{ $etapa->id }}, 'en_curso')">
                                        <i class="fas fa-play me-1"></i>Iniciar
                                    </button>
                                @elseif($etapa->estado == 'en_curso')
                                    <button class="btn btn-sm btn-success mt-2" onclick="cambiarEstadoEtapa({{ $etapa->id }}, 'completado')">
                                        <i class="fas fa-check me-1"></i>Completar
                                    </button>
                                @endif
                                @if($etapa->foto_evidencia)
                                    <br>
                                    <a href="{{ asset('storage/'.$etapa->foto_evidencia) }}" target="_blank" class="btn btn-sm btn-info mt-2">
                                        <i class="fas fa-camera me-1"></i>Evidencia
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Este viaje no tiene etapas definidas.
                </div>
            @endforelse
        </div>
    </div>

    {{-- Botón para agregar nueva etapa --}}
    <div class="row mt-3">
        <div class="col-12 text-center">
            <button type="button" class="btn btn-outline-primary" onclick="mostrarFormNuevaEtapa()">
                <i class="bi bi-plus-circle me-2"></i>Agregar Nueva Etapa
            </button>
        </div>
    </div>

    {{-- Formulario para nueva etapa (oculto inicialmente) --}}
    <div id="formNuevaEtapa" style="display: none;" class="mt-4">
        <hr>
        <h5 class="mb-3">Nueva Etapa</h5>
        <form id="formAgregarEtapa" onsubmit="guardarNuevaEtapa(event)" action="{{ route('viajes.etapas.store', $viaje->id) }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Nombre de la Etapa <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="nombre" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Orden</label>
                    <input type="number" class="form-control" name="orden" value="{{ $viaje->etapas->count() + 1 }}" min="1" readonly>
                    <small class="text-muted">Se asignará automáticamente</small>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Ubicación <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="ubicacion" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">KM Estimado</label>
                    <input type="number" class="form-control" name="kilometraje_estimado" step="0.01" min="0">
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label">Descripción</label>
                    <textarea class="form-control" name="descripcion" rows="2"></textarea>
                </div>
            </div>
            <div class="text-end">
                <button type="button" class="btn btn-secondary me-2" onclick="cancelarNuevaEtapa()">Cancelar</button>
                <button type="submit" class="btn btn-primary">Guardar Etapa</button>
            </div>
        </form>
    </div>
</div>

<script>
    function mostrarFormNuevaEtapa() {
        document.getElementById('formNuevaEtapa').style.display = 'block';
    }

    function cancelarNuevaEtapa() {
        document.getElementById('formNuevaEtapa').style.display = 'none';
        document.getElementById('formAgregarEtapa').reset();
    }

    function guardarNuevaEtapa(event) {
        event.preventDefault();
        const form = event.target;
        const formData = new FormData(form);

        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    mostrarToast('Etapa agregada correctamente', 'Éxito', 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    alert('Error al agregar la etapa');
                }
            })
            .catch(error => {
                alert('Error de conexión');
            });
    }

    function cambiarEstadoEtapa(etapaId, estado) {
        const mensajes = {
            'en_curso': '¿Iniciar esta etapa?',
            'completado': '¿Marcar esta etapa como completada?'
        };

        if (confirm(mensajes[estado])) {
            fetch(`/etapas/${etapaId}/estado`, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ estado: estado })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        mostrarToast(data.message, 'Éxito', 'success');
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        alert('Error al actualizar la etapa');
                    }
                })
                .catch(error => {
                    alert('Error de conexión');
                });
        }
    }
</script>

<style>
    .etapa-card {
        transition: all 0.3s;
    }
    .etapa-card:hover {
        transform: translateX(5px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
</style>
