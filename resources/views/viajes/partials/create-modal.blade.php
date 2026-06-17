{{-- resources/views/viajes/partials/create-modal.blade.php --}}
<div class="container-fluid">
    <form id="formCrearViaje" onsubmit="guardarViaje(this)" action="{{ route('viajes.store') }}" method="POST">
        @csrf

        {{-- Pestañas para organizar la información --}}
        <ul class="nav nav-tabs mb-3" id="viajeTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="info-tab" data-bs-toggle="tab" data-bs-target="#info" type="button" role="tab">
                    <i class="bi bi-info-circle me-2"></i>Información Básica
                </button>
            </li>

           <li class="nav-item" role="presentation">
               <button class="nav-link" id="etapas-tab" data-bs-toggle="tab" data-bs-target="#etapas" type="button" role="tab">
                   <i class="fas fa-tasks me-2"></i>Etapas del Viaje
               </button>
           </li>
            <!--
                      <li class="nav-item" role="presentation">
                          <button class="nav-link" id="motos-tab" data-bs-toggle="tab" data-bs-target="#motos" type="button" role="tab">
                              <i class="fas fa-motorcycle me-2"></i>Motos a Transportar
                          </button>
                      </li>-->
        </ul>

        {{-- Contenido de las pestañas --}}
        <div class="tab-content" id="viajeTabsContent">
            {{-- Pestaña 1: Información Básica --}}
            <div class="tab-pane fade show active" id="info" role="tabpanel">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Numero de Viaje</label>
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            El n&uacute;mero del viaje generará automáticamente al crearlo.
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Estado del Viaje</label>
                        <select class="form-select" name="estado" required>
                            <option value="planeado" selected>Planeado</option>
                            <option value="en_curso">En Curso</option>
                            <option value="completado">Completado</option>
                            <option value="cancelado">Cancelado</option>
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Camión <span class="text-danger">*</span></label>
                        <select class="form-select" name="camion_id" required>
                            <option value="">Seleccione un camión</option>
                            @foreach($camiones as $camion)
                                <option value="{{ $camion->id }}"
                                        data-capacidad="{{ $camion->capacidad_motos }}"
                                    {{ $camion->activo ? '' : 'disabled' }}>
                                    {{ $camion->placa }} - {{ $camion->marca }} {{ $camion->modelo }}
                                    (Cap: {{ $camion->capacidad_motos }} motos)
                                    @if(!$camion->activo)
                                        - INACTIVO
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted" id="capacidadInfo"></small>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Chofer <span class="text-danger">*</span></label>
                        <select class="form-select" name="chofer_id" required>
                            <option value="">Seleccione un chofer</option>
                            @foreach($choferes as $chofer)
                                <option value="{{ $chofer->id }}" {{ $chofer->activo ? '' : 'disabled' }}>
                                    {{ $chofer->nombre_completo }}
                                    @if(!$chofer->activo)
                                        - INACTIVO
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Fecha de Inicio <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" name="fecha_inicio" value="{{ date('Y-m-d') }}" required>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Fecha de Fin</label>
                        <input type="date" class="form-control" name="fecha_fin">
                        <small class="text-muted">Dejar vacío si no está definido</small>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Distancia (km)</label>
                        <input type="number" class="form-control" name="distancia_km" step="0.01" min="0">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Origen <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="origen" placeholder="Ciudad de origen" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Destino <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="destino" placeholder="Ciudad de destino" required>
                    </div>

                    <div class="col-12 mb-3">
                        <label class="form-label">Notas</label>
                        <textarea class="form-control" name="notas" rows="3" placeholder="Información adicional del viaje..."></textarea>
                    </div>
                </div>
            </div>

            {{-- Pestaña 2: Etapas del Viaje --}}
            <div class="tab-pane fade" id="etapas" role="tabpanel">
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    Define las etapas del viaje. Se crearán 3 etapas por defecto, pero puedes personalizarlas.
                </div>

                <div id="etapas-container">
                    {{-- Etapas por defecto --}}

                    <div class="row mb-2 etapa-item">
                        <div class="col-md-4">
                            <input type="text" class="form-control" name="etapas[0][nombre]" value="Inicio Viaje" placeholder="De donde parte el chofer?" required>
                        </div>
                        <div class="col-md-4">
                            <input type="text" class="form-control" name="etapas[0][ubicacion]" id="etapa0_origen" placeholder="Ubicación" value="ubicacion" required>
                        </div>
                        <div class="col-md-2">
                            <input type="number" class="form-control" name="etapas[0][kilometraje_estimado]" placeholder="KM" value="1" step="0.01" min="0">
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-outline-secondary btn-sm" disabled>
                                <i class="fas fa-lock"></i> Base
                            </button>
                        </div>
                    </div>


                    <div class="row mb-2 etapa-item">
                        <div class="col-md-4">
                            <input type="text" class="form-control" name="etapas[1][nombre]" value="Carga de motos" placeholder="Nombre de la etapa" required>
                        </div>
                        <div class="col-md-4">
                            <input type="text" class="form-control" name="etapas[1][ubicacion]" id="etapa0_origen" placeholder="Ubicación" value="ubicacion" required>
                        </div>
                        <div class="col-md-2">
                            <input type="number" class="form-control" name="etapas[1][kilometraje_estimado]" placeholder="KM" value="1" step="0.01" min="0">
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-outline-secondary btn-sm" disabled>
                                <i class="fas fa-lock"></i> Base
                            </button>
                        </div>
                    </div>


                    <div class="row mb-2 etapa-item">
                        <div class="col-md-4">
                            <input type="text" class="form-control" name="etapas[2][nombre]" value="Descarga de motos" placeholder="Nombre de la etapa" required>
                        </div>
                        <div class="col-md-4">
                            <input type="text" class="form-control" name="etapas[2][ubicacion]" id="etapa2_destino" value="ubicacion" placeholder="Ubicación" required>
                        </div>
                        <div class="col-md-2">
                            <input type="number" class="form-control" name="etapas[2][kilometraje_estimado]"  value="1" placeholder="KM" step="0.01" min="0">
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-outline-secondary btn-sm" disabled>
                                <i class="fas fa-lock"></i> Base
                            </button>
                        </div>
                    </div>
                </div>

                <div class="text-center mt-3">
                    <button type="button" class="btn btn-outline-primary" onclick="agregarEtapa()">
                        <i class="bi bi-plus-circle me-2"></i>Agregar Etapa Adicional
                    </button>
                </div>

                <small class="text-muted d-block mt-2">
                    <i class="bi bi-info-circle me-1"></i>
                    Las etapas base no pueden eliminarse, pero puedes agregar más según necesites.
                </small>
            </div>

            {{-- Pestaña 3: Motos a Transportar --}}
            <div class="tab-pane fade" id="motos" role="tabpanel">
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Puedes agregar las motos ahora o hacerlo después desde la pantalla principal.
                </div>

                <div id="motos-container">
                    {{-- Aquí se agregarán las motos dinámicamente --}}
                </div>

                <div class="text-center mt-3">
                    <button type="button" class="btn btn-outline-success" onclick="agregarCampoMoto()">
                        <i class="bi-plus-circle me-2"></i>Agregar Moto
                    </button>
                </div>

                <div class="alert alert-light mt-3" id="resumenMotos" style="display: none;">
                    <h6 class="mb-2">Resumen:</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Total Motos:</strong> <span id="totalMotosCount">0</span>
                        </div>
                        <div class="col-md-6">
                            <strong>Ingreso Estimado:</strong> $<span id="totalIngresoEstimado">0.00</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Botones de acción --}}
        <div class="row mt-4">
            <div class="col-12 d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x me-2"></i>Cancelar
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-2"></i>Guardar Viaje
                </button>
            </div>
        </div>
    </form>
</div>

<script>
    // Script específico para este modal
    document.addEventListener('DOMContentLoaded', function() {
        // Sincronizar origen/destino con etapas
        const origenInput = document.querySelector('input[name="origen"]');
        const destinoInput = document.querySelector('input[name="destino"]');
        const etapaOrigen = document.getElementById('etapa0_origen');
        const etapaDestino = document.getElementById('etapa2_destino');

        if (origenInput && etapaOrigen) {
            origenInput.addEventListener('input', function() {
                etapaOrigen.value = this.value;
            });
        }

        if (destinoInput && etapaDestino) {
            destinoInput.addEventListener('input', function() {
                etapaDestino.value = this.value;
            });
        }

        // Mostrar capacidad del camión seleccionado
        const camionSelect = document.querySelector('select[name="camion_id"]');
        const capacidadInfo = document.getElementById('capacidadInfo');

        if (camionSelect) {
            camionSelect.addEventListener('change', function() {
                const selected = this.options[this.selectedIndex];
                if (selected && selected.dataset.capacidad) {
                    capacidadInfo.textContent = `Capacidad máxima: ${selected.dataset.capacidad} motos`;
                } else {
                    capacidadInfo.textContent = '';
                }
            });
        }

        // Actualizar resumen de motos cuando se agreguen o modifiquen
        document.addEventListener('input', function(e) {
            if (e.target.matches('[name*="[cantidad]"]') || e.target.matches('[name*="[precio_por_moto]"]')) {
                actualizarResumenMotos();
            }
        });
    });



    function eliminarEtapa(btn) {
        if (confirm('¿Estás seguro de eliminar esta etapa?')) {
            btn.closest('.etapa-item').remove();
        }
    }

    function agregarCampoMoto() {
        const container = document.getElementById('motos-container');
        const index = container.children.length;
        const html = `
            <div class="row mb-2 moto-item">
                <div class="col-md-5">
                    <input type="text" class="form-control" name="motos[${index}][modelo_moto]" placeholder="Modelo de moto" required>
                </div>
                <div class="col-md-2">
                    <input type="number" class="form-control cantidad-moto" name="motos[${index}][cantidad]" placeholder="Cantidad" min="1" required>
                </div>
                <div class="col-md-3">
                    <input type="number" class="form-control precio-moto" name="motos[${index}][precio_por_moto]" placeholder="Precio por moto" step="0.01" min="0" required>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-danger btn-sm" onclick="eliminarCampoMoto(this)">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', html);

        // Mostrar el resumen
        document.getElementById('resumenMotos').style.display = 'block';
    }

    function eliminarCampoMoto(btn) {
        btn.closest('.moto-item').remove();
        actualizarResumenMotos();

        // Ocultar resumen si no hay más motos
        if (document.querySelectorAll('.moto-item').length === 0) {
            document.getElementById('resumenMotos').style.display = 'none';
        }
    }

    function actualizarResumenMotos() {
        let totalMotos = 0;
        let totalIngreso = 0;

        document.querySelectorAll('.moto-item').forEach(item => {
            const cantidad = parseInt(item.querySelector('.cantidad-moto')?.value) || 0;
            const precio = parseFloat(item.querySelector('.precio-moto')?.value) || 0;

            totalMotos += cantidad;
            totalIngreso += cantidad * precio;
        });

        document.getElementById('totalMotosCount').textContent = totalMotos;
        document.getElementById('totalIngresoEstimado').textContent = totalIngreso.toFixed(2);
    }
</script>
