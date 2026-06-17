{{-- resources/views/viajes/partials/gastos-admin-modal.blade.php --}}
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12">
            <div class="alert alert-info">
                <i class="bi bi-info-circle me-2"></i>
                Administra todos los gastos del viaje. Puedes agregar, editar o eliminar gastos.
                <br><small>Los gastos con <span class="text-warning">fondo amarillo</span> son rendiciones de viáticos.</small>
            </div>
        </div>
    </div>

    {{-- Información del viaje y resumen --}}
    <div class="row mb-3">
        <div class="col-md-12">
            <div class="card bg-light">
                <div class="card-body py-2">
                    <div class="row">
                        <div class="col-md-3">
                            <small class="text-muted">Viaje:</small>
                            <strong>#{{ $viaje->folio ?? $viaje->id }}</strong>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted">Ruta:</small>
                            <strong>{{ $viaje->origen }} → {{ $viaje->destino }}</strong>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted">Camión:</small>
                            <strong>{{ $viaje->camion->placa ?? 'N/A' }} - {{ $viaje->camion->marca ?? '' }} {{ $viaje->camion->modelo ?? '' }}</strong>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted">Chofer:</small>
                            <strong>{{ $viaje->chofer->nombre_completo ?? 'N/A' }}</strong>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-3">
                            <small class="text-muted">Estado:</small>
                            <span class="badge bg-{{ $viaje->estado == 'completado' ? 'success' : ($viaje->estado == 'en_curso' ? 'warning' : 'secondary') }}">
                                {{ ucfirst($viaje->estado) }}
                            </span>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted">Fecha inicio:</small>
                            <strong>{{ $viaje->fecha_inicio ? \Carbon\Carbon::parse($viaje->fecha_inicio)->format('d/m/Y') : 'N/A' }}</strong>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted">Fecha fin:</small>
                            <strong>{{ $viaje->fecha_fin ? \Carbon\Carbon::parse($viaje->fecha_fin)->format('d/m/Y') : 'En curso' }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Sección de anticipo/viáticos --}}
    <div class="card mb-3 border-primary">
        <div class="card-header bg-primary text-white">
            <h6 class="mb-0" style="color: white !important;"><i class="mdi mdi-hand-coin me-2"></i>Viáticos y Rendiciones</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <label class="form-label">Anticipo entregado al chofer (USD)</label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" class="form-control" id="anticipo_chofer" value="{{ $viaje->anticipo_chofer ?? 0 }}" step="0.01" min="0">
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Total gastado (USD)</label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="text" class="form-control" id="total_gastado_usd" value="{{ number_format($viaje->gastos->where('moneda_original', 'USD')->sum('monto'), 2) }}" readonly>
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Total gastado (VES)</label>
                    <div class="input-group">
                        <span class="input-group-text">Bs.</span>
                        <input type="text" class="form-control" id="total_gastado_ves" value="{{ number_format($viaje->gastos->where('moneda_original', 'VES')->sum('monto_original'), 2) }}" readonly>
                    </div>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-md-4">
                    <label class="form-label">Diferencia (a rendir/devolver)</label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="text" class="form-control" id="diferencia_viaticos" readonly>
                    </div>
                    <small class="text-muted" id="mensaje_diferencia"></small>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-12">
                    <button class="btn btn-sm btn-primary" onclick="calcularDiferencia()">
                        <i class="bi bi-calculator me-1"></i>Calcular diferencia
                    </button>
                    <button class="btn btn-sm btn-success" onclick="guardarAnticipo({{ $viaje->id }})">
                        <i class="bi bi-save me-1"></i>Guardar anticipo
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Formulario para agregar nuevo gasto --}}
    <div class="card mb-3 border-success">
        <div class="card-header bg-success text-white">
            <h6 class="mb-0" style="color: white !important;"><i class="bi bi-plus-circle me-2"></i>Agregar Nuevo Gasto</h6>
        </div>
        <div class="card-body">
            <form id="formAgregarGasto" onsubmit="return agregarGasto(event)">
                @csrf
                <input type="hidden" id="viaje_id" value="{{ $viaje->id }}">

                {{-- Primera fila: Tipo, Concepto y Fecha --}}
                <div class="row">
                    <div class="col-md-4 mb-2">
                        <select class="form-select" id="nuevo_tipo_gasto" required>
                            <option value="">Tipo de gasto</option>
                            @foreach($tiposGasto as $tipo)
                                <option value="{{ $tipo->id }}">{{ $tipo->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-5 mb-2">
                        <input type="text" class="form-control" id="nuevo_concepto" placeholder="Concepto" required>
                    </div>
                    <div class="col-md-3 mb-2">
                        <input type="date" class="form-control" id="nueva_fecha" value="{{ date('Y-m-d') }}" required>
                    </div>
                </div>

                {{-- Segunda fila: Moneda y Montos --}}
                <div class="row">
                    <div class="col-md-2 mb-2">
                        <select class="form-select" id="nuevo_moneda" onchange="cambiarMoneda()">
                            <option value="USD">Dólares (USD)</option>
                            <option value="VES">Bolívares (VES)</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-2">
                        <input type="number" class="form-control" id="nuevo_monto_original" placeholder="Monto" step="0.01" min="0" required oninput="calcularEquivalentes()">
                    </div>
                    <div class="col-md-3 mb-2" id="tasa_container" style="display: none;">
                        <div class="input-group">
                            <span class="input-group-text">Tasa (Bs./USD)</span>
                            <input type="number" class="form-control" id="nuevo_tasa" value="" step="0.01" min="0" placeholder="Ej: 36.50" oninput="calcularEquivalentes()">
                        </div>
                    </div>
                    <div class="col-md-4 mb-2" id="equivalente_container" style="display: none;">
                        <div class="input-group">
                            <span class="input-group-text bg-info text-white">Equivalente USD</span>
                            <input type="text" class="form-control" id="equivalente_usd" readonly>
                        </div>
                    </div>
                </div>

                {{-- Tercera fila: Proveedor, Método pago, Referencia, Viático --}}
                <div class="row">
                    <div class="col-md-3 mb-2">
                        <input type="text" class="form-control" id="nuevo_proveedor" placeholder="Proveedor">
                    </div>
                    <div class="col-md-3 mb-2">
                        <select class="form-select" id="nuevo_metodo_pago">
                            <option value="">Método de pago</option>
                            <option value="efectivo">Efectivo</option>
                            <option value="transferencia">Transferencia</option>
                            <option value="tarjeta_credito">Tarjeta Crédito</option>
                            <option value="tarjeta_debito">Tarjeta Débito</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-2">
                        <input type="text" class="form-control" id="nueva_referencia" placeholder="Referencia (opcional)">
                    </div>
                    <div class="col-md-3 mb-2">
                        <div class="form-check form-switch mt-2">
                            <input class="form-check-input" type="checkbox" id="es_viatico">
                            <label class="form-check-label" for="es_viatico">Es viático</label>
                        </div>
                    </div>
                </div>

                {{-- Botón submit --}}
                <div class="row mt-2">
                    <div class="col-12 text-end">
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-save me-1"></i>Agregar Gasto
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Lista de gastos existentes --}}
    <div class="card" id="divgastos">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h6 class="mb-0" style="color: white !important;">
                <i class="bi bi-list me-2"></i>Gastos Registrados
                <small class="ms-2 text-light">
                    (Camión: {{ $viaje->camion->modelo ?? '' }} {{ $viaje->camion->placa ?? 'N/A' }} {{ $viaje->camion->marca ?? '' }} | Chofer: {{ $viaje->chofer->nombre_completo ?? 'N/A' }})
                </small>
            </h6>
            <div>
                <span class="badge bg-light text-dark">{{ $viaje->gastos->count() }} gastos</span>
                <button type="button" class="btn btn-sm btn-outline-primary me-2" style="color: white !important;" onclick=" $(this).hide(); capture('#divgastos')">
                    <i class="bi bi-camera"></i> Capturar
                </button>
            </div>
        </div>
        <div class="card-body">
            @if($viaje->gastos->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover" id="tablaGastos">
                        <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Tipo</th>
                            <th>Concepto</th>
                            <th>Monto USD</th>
                            <th>Monto VES</th>
                            <th>Tasa</th>
                            <th>Proveedor</th>
                            <th>Método</th>
                            <th>Acciones</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($viaje->gastos->sortByDesc('fecha_gasto') as $gasto)
                            <tr id="gasto-{{ $gasto->id }}" data-id="{{ $gasto->id }}" data-moneda-original="{{ $gasto->moneda_original }}" data-monto-original="{{ $gasto->monto_original }}" data-tasa="{{ $gasto->tasa_cambio }}"   }}">
                                <td>
                                    <span class="fecha-text">{{ $gasto->fecha_gasto->format('d/m/Y') }}</span>
                                    <input type="date" class="form-control fecha-input" value="{{ $gasto->fecha_gasto->format('Y-m-d') }}" style="display: none; width: 130px;">
                                </td>
                                <td>
                                    <span class="tipo-text">{{ $gasto->tipoGasto->nombre ?? 'N/A' }}</span>
                                    <select class="form-select tipo-input" style="display: none; width: 150px;">
                                        @foreach($tiposGasto as $tipo)
                                            <option value="{{ $tipo->id }}" {{ $gasto->tipo_gasto_id == $tipo->id ? 'selected' : '' }}>{{ $tipo->nombre }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <span class="concepto-text">{{ $gasto->concepto }}</span>
                                    <input type="text" class="form-control concepto-input" value="{{ $gasto->concepto }}" style="display: none;">
                                </td>
                                {{-- COLUMNA MONTO USD (siempre el valor en dólares - sea cual sea la moneda original) --}}
                                <td>
                                    <span class="monto-usd-text">${{ number_format($gasto->monto, 2) }}</span>
                                    <input type="number" class="form-control monto-usd-input" value="{{ $gasto->monto }}" step="0.01" min="0" style="display: none; width: 100px;">
                                </td>
                                {{-- COLUMNA MONTO VES (solo si fue en bolívares) --}}
                                <td>
                                    @if($gasto->moneda_original == 'VES')
                                        <span class="monto-ves-text">Bs. {{ number_format($gasto->monto_original, 2) }}</span>
                                        <input type="number" class="form-control monto-ves-input" value="{{ $gasto->monto_original }}" step="0.01" min="0" style="display: none; width: 130px;">
                                    @else
                                        <span class="monto-ves-text text-muted">—</span>
                                        <input type="number" class="form-control monto-ves-input" value="" step="0.01" min="0" style="display: none; width: 130px;">
                                    @endif
                                </td>
                                {{-- COLUMNA TASA --}}
                                <td>
                                    @if($gasto->moneda_original == 'VES' && $gasto->tasa_cambio)
                                        <span class="tasa-text">{{ number_format($gasto->tasa_cambio, 2) }}</span>
                                        <input type="number" class="form-control tasa-input" value="{{ $gasto->tasa_cambio }}" step="0.01" min="0" style="display: none; width: 100px;">
                                    @else
                                        <span class="tasa-text text-muted">—</span>
                                        <input type="number" class="form-control tasa-input" value="" step="0.01" min="0" style="display: none; width: 100px;">
                                    @endif
                                </td>
                                <td>
                                    <span class="proveedor-text">{{ $gasto->proveedor ?? 'N/A' }}</span>
                                    <input type="text" class="form-control proveedor-input" value="{{ $gasto->proveedor }}" style="display: none;">
                                </td>
                                <td>
                                    <span class="metodo-text">{{ $gasto->metodo_pago ?? 'N/A' }}</span>
                                    <select class="form-select metodo-input" style="display: none; width: 130px;">
                                        <option value="">N/A</option>
                                        <option value="efectivo" {{ $gasto->metodo_pago == 'efectivo' ? 'selected' : '' }}>Efectivo</option>
                                        <option value="transferencia" {{ $gasto->metodo_pago == 'transferencia' ? 'selected' : '' }}>Transferencia</option>
                                        <option value="tarjeta_credito" {{ $gasto->metodo_pago == 'tarjeta_credito' ? 'selected' : '' }}>T. Crédito</option>
                                        <option value="tarjeta_debito" {{ $gasto->metodo_pago == 'tarjeta_debito' ? 'selected' : '' }}>T. Débito</option>
                                    </select>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-warning btn-editar" onclick="editarGasto({{ $gasto->id }})" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="btn btn-sm btn-success btn-guardar" onclick="guardarGasto({{ $gasto->id }})" style="display: none;" title="Guardar">
                                        <i class="bi bi-save"></i>
                                    </button>
                                    <button class="btn btn-sm btn-secondary btn-cancelar" onclick="cancelarEdicionGasto({{ $gasto->id }})" style="display: none;" title="Cancelar">
                                        <i class="bi bi-x"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger" onclick="eliminarGasto({{ $gasto->id }})" title="Eliminar">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                        <tfoot class="table-secondary">
                        <tr>
                            @php
                                $totalUSDOriginal = $viaje->gastos->where('moneda_original', 'USD')->sum('monto');
                                $totalVES = $viaje->gastos->where('moneda_original', 'VES')->sum('monto_original');
                            @endphp
                            <th colspan="3" class="text-end">Totales:</th>
                            <th id="totalUSD">${{ number_format($totalUSDOriginal, 2) }}</th>
                            <th id="totalVES">
                                @if($totalVES > 0)
                                    Bs. {{ number_format($totalVES, 2) }}
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </th>
                            <th colspan="4"></th>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            @else
                <p class="text-muted text-center py-3">
                    <i class="bi bi-info-circle me-2"></i>
                    No hay gastos registrados en este viaje. Agrega uno usando el formulario de arriba.
                </p>
            @endif
        </div>
    </div>
</div>

<style>
    #tablaGastos {
        font-size: 0.9rem;
    }

    #tablaGastos thead {
        background-color: #f8f9fa;
        border-bottom: 2px solid #dee2e6;
    }

    #tablaGastos tbody tr:hover {
        background-color: #f1f5f9 !important;
    }

    #tablaGastos tbody tr.table-warning {
        background-color: #fff3cd !important;
    }

    #tablaGastos .btn-sm {
        padding: 4px 8px;
        margin: 0 2px;
    }

    #tablaGastos .form-control,
    #tablaGastos .form-select {
        font-size: 0.85rem;
        padding: 4px 8px;
        height: auto;
    }
</style>

<script>
    (function() {
        'use strict';

        let viajeId = {{ $viaje->id }};

        function mostrarToast(mensaje, titulo = 'Notificación', tipo = 'info') {
            if (typeof window.mostrarToast === 'function') {
                window.mostrarToast(mensaje, titulo, tipo);
            } else {
                alert(mensaje);
            }
        }

        function mostrarLoading(mostrar) {
            if (typeof window.mostrarLoading === 'function') {
                window.mostrarLoading(mostrar);
            }
        }

        window.cambiarMoneda = function() {
            const moneda = document.getElementById('nuevo_moneda').value;
            const tasaContainer = document.getElementById('tasa_container');
            const equivalenteContainer = document.getElementById('equivalente_container');
            const montoInput = document.getElementById('nuevo_monto_original');

            if (moneda === 'VES') {
                tasaContainer.style.display = 'block';
                equivalenteContainer.style.display = 'block';
                montoInput.placeholder = 'Monto en Bolívares (Bs.)';
            } else {
                tasaContainer.style.display = 'none';
                equivalenteContainer.style.display = 'none';
                montoInput.placeholder = 'Monto en Dólares (USD)';
                document.getElementById('nuevo_tasa').value = '';
                document.getElementById('equivalente_usd').value = '';
            }
        };

        window.calcularEquivalentes = function() {
            const moneda = document.getElementById('nuevo_moneda').value;
            const montoOriginal = parseFloat(document.getElementById('nuevo_monto_original').value) || 0;
            const tasa = parseFloat(document.getElementById('nuevo_tasa').value) || 0;

            if (moneda === 'VES' && tasa > 0 && montoOriginal > 0) {
                const equivalenteUSD = montoOriginal / tasa;
                document.getElementById('equivalente_usd').value = equivalenteUSD.toFixed(2);
            } else if (moneda === 'VES') {
                document.getElementById('equivalente_usd').value = '';
            }
        };

        window.calcularDiferencia = function() {
            const anticipo = parseFloat(document.getElementById('anticipo_chofer')?.value) || 0;
            const totalGastadoUSDElement = document.getElementById('total_gastado_usd');
            const totalGastado = totalGastadoUSDElement ? parseFloat(totalGastadoUSDElement.value.replace(/,/g, '')) || 0 : 0;
            const diferencia = anticipo - totalGastado;

            const diferenciaInput = document.getElementById('diferencia_viaticos');
            if (diferenciaInput) diferenciaInput.value = diferencia.toFixed(2);

            const mensaje = document.getElementById('mensaje_diferencia');
            if (mensaje) {
                if (diferencia > 0) {
                    mensaje.innerHTML = `<span class="text-success">El chofer debe devolver $${diferencia.toFixed(2)}</span>`;
                } else if (diferencia < 0) {
                    mensaje.innerHTML = `<span class="text-danger">Faltan $${Math.abs(diferencia).toFixed(2)} por rendir</span>`;
                } else {
                    mensaje.innerHTML = `<span class="text-success">Cuadre perfecto</span>`;
                }
            }
        };

        window.guardarAnticipo = function(id) {
            const anticipo = document.getElementById('anticipo_chofer')?.value;

            if (!confirm('¿Guardar el anticipo del chofer?')) return;

            mostrarLoading(true);

            fetch(`/viajes/${id}/anticipo`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ anticipo: anticipo })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        mostrarToast('Anticipo guardado correctamente', 'Éxito', 'success');
                        calcularDiferencia();
                    } else {
                        throw new Error(data.error || 'Error al guardar');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    mostrarToast(error.message, 'Error', 'danger');
                })
                .finally(() => {
                    mostrarLoading(false);
                });
        };

        window.agregarGasto = function(event) {
            event.preventDefault();
            event.stopPropagation();

            const tipoGastoId = document.getElementById('nuevo_tipo_gasto')?.value;
            const concepto = document.getElementById('nuevo_concepto')?.value;
            const moneda = document.getElementById('nuevo_moneda')?.value;
            const montoOriginal = parseFloat(document.getElementById('nuevo_monto_original')?.value) || 0;
            const fecha = document.getElementById('nueva_fecha')?.value;
            const proveedor = document.getElementById('nuevo_proveedor')?.value;
            const metodoPago = document.getElementById('nuevo_metodo_pago')?.value;
            const referencia = document.getElementById('nueva_referencia')?.value;
            const esViatico = document.getElementById('es_viatico')?.checked ? 1 : 0;

            if (!tipoGastoId) {
                alert('Debes seleccionar un tipo de gasto');
                return false;
            }
            if (!concepto || concepto.trim() === '') {
                alert('Debes ingresar un concepto');
                return false;
            }
            if (!montoOriginal || montoOriginal <= 0) {
                alert('Debes ingresar un monto válido');
                return false;
            }
            if (!fecha) {
                alert('Debes seleccionar una fecha');
                return false;
            }

            let montoUSD = montoOriginal;
            let tasaCambio = null;

            if (moneda === 'VES') {
                const tasa = parseFloat(document.getElementById('nuevo_tasa').value) || 0;
                if (tasa <= 0) {
                    alert('Debes ingresar una tasa de cambio válida');
                    return false;
                }
                montoUSD = montoOriginal / tasa;
                tasaCambio = tasa;
            }

            mostrarLoading(true);

            const data = {
                tipo_gasto_id: tipoGastoId,
                concepto: concepto.trim(),
                monto: montoUSD,
                moneda_original: moneda,
                monto_original: montoOriginal,
                tasa_cambio: tasaCambio,
                fecha_gasto: fecha,
                proveedor: proveedor || null,
                metodo_pago: metodoPago || null,
                referencia_pago: referencia || null,
                es_viatico: esViatico
            };

            fetch(`/viajes/${viajeId}/gastos`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        mostrarToast('Gasto agregado correctamente', 'Éxito', 'success');
                        agregarGastoATabla(data.gasto);
                        limpiarFormulario();
                        actualizarTotalesGastos();
                    } else {
                        throw new Error(data.error || 'Error al agregar el gasto');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    mostrarToast(error.message, 'Error', 'danger');
                })
                .finally(() => {
                    mostrarLoading(false);
                });

            return false;
        };

        function limpiarFormulario() {
            document.getElementById('nuevo_tipo_gasto').value = '';
            document.getElementById('nuevo_concepto').value = '';
            document.getElementById('nuevo_moneda').value = 'USD';
            document.getElementById('nuevo_monto_original').value = '';
            document.getElementById('nuevo_tasa').value = '';
            document.getElementById('equivalente_usd').value = '';
            document.getElementById('nueva_fecha').value = '{{ date('Y-m-d') }}';
            document.getElementById('nuevo_proveedor').value = '';
            document.getElementById('nuevo_metodo_pago').value = '';
            document.getElementById('nueva_referencia').value = '';
            document.getElementById('es_viatico').checked = false;
            cambiarMoneda();
        }

        function agregarGastoATabla(gasto) {
            const tbody = document.querySelector('#tablaGastos tbody');
            if (!tbody) return;

            const fecha = new Date(gasto.fecha_gasto);
            const fechaFormateada = fecha.toLocaleDateString('es-ES');
            const tipoNombre = gasto.tipo_gasto?.nombre || 'N/A';
            const claseViatico = gasto.es_viatico ? 'table-warning' : '';

            const montoVES = gasto.moneda_original === 'VES' ? `Bs. ${parseFloat(gasto.monto_original).toFixed(2)}` : '—';
            const tasa = gasto.moneda_original === 'VES' && gasto.tasa_cambio ? gasto.tasa_cambio.toFixed(2) : '—';

            const nuevaFila = document.createElement('tr');
            nuevaFila.id = `gasto-${gasto.id}`;
            nuevaFila.setAttribute('data-id', gasto.id);
            nuevaFila.setAttribute('data-moneda-original', gasto.moneda_original);
            nuevaFila.setAttribute('data-monto-original', gasto.monto_original);
            nuevaFila.setAttribute('data-tasa', gasto.tasa_cambio || '');
            nuevaFila.className = claseViatico;

            nuevaFila.innerHTML = `
                <td>${fechaFormateada}</td>
                <td>${tipoNombre}</td>
                <td>${gasto.concepto}</td>
                <td>$${parseFloat(gasto.monto).toFixed(2)}</td>
                <td>${montoVES}</td>
                <td>${tasa}</td>
                <td>${gasto.proveedor || 'N/A'}</td>
                <td>${gasto.metodo_pago || 'N/A'}</td>
                <td>
                    <button class="btn btn-sm btn-warning btn-editar" onclick="editarGasto(${gasto.id})" title="Editar">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <button class="btn btn-sm btn-danger" onclick="eliminarGasto(${gasto.id})" title="Eliminar">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            `;

            tbody.appendChild(nuevaFila);

            const total = document.querySelectorAll('#tablaGastos tbody tr').length;
            const contador = document.querySelector('.card-header .badge');
            if (contador) contador.textContent = total + ' gastos';
        }

        window.editarGasto = function(id) {
            const row = document.getElementById(`gasto-${id}`);
            if (!row) return;

            // Ocultar textos
            row.querySelector('.fecha-text').style.display = 'none';
            row.querySelector('.tipo-text').style.display = 'none';
            row.querySelector('.concepto-text').style.display = 'none';
            row.querySelector('.monto-usd-text').style.display = 'none';

            const montoVesSpan = row.querySelector('.monto-ves-text');
            if (montoVesSpan) montoVesSpan.style.display = 'none';

            const tasaSpan = row.querySelector('.tasa-text');
            if (tasaSpan) tasaSpan.style.display = 'none';

            row.querySelector('.proveedor-text').style.display = 'none';
            row.querySelector('.metodo-text').style.display = 'none';

            // Mostrar inputs
            row.querySelector('.fecha-input').style.display = 'block';
            row.querySelector('.tipo-input').style.display = 'block';
            row.querySelector('.concepto-input').style.display = 'block';
            row.querySelector('.monto-usd-input').style.display = 'block';

            const montoVesInput = row.querySelector('.monto-ves-input');
            if (montoVesInput) montoVesInput.style.display = 'block';

            const tasaInput = row.querySelector('.tasa-input');
            if (tasaInput) tasaInput.style.display = 'block';

            row.querySelector('.proveedor-input').style.display = 'block';
            row.querySelector('.metodo-input').style.display = 'block';

            // Ocultar/mostrar botones
            const btnEditar = row.querySelector('.btn-editar');
            const btnGuardar = row.querySelector('.btn-guardar');
            const btnCancelar = row.querySelector('.btn-cancelar');

            if (btnEditar) btnEditar.style.display = 'none';
            if (btnGuardar) btnGuardar.style.display = 'inline-block';
            if (btnCancelar) btnCancelar.style.display = 'inline-block';
        };

        window.cancelarEdicionGasto = function(id) {
            const row = document.getElementById(`gasto-${id}`);
            if (!row) return;

            // Restaurar textos
            row.querySelector('.fecha-text').style.display = 'inline';
            row.querySelector('.tipo-text').style.display = 'inline';
            row.querySelector('.concepto-text').style.display = 'inline';
            row.querySelector('.monto-usd-text').style.display = 'inline';

            const montoVesSpan = row.querySelector('.monto-ves-text');
            if (montoVesSpan) montoVesSpan.style.display = 'inline';

            const tasaSpan = row.querySelector('.tasa-text');
            if (tasaSpan) tasaSpan.style.display = 'inline';

            row.querySelector('.proveedor-text').style.display = 'inline';
            row.querySelector('.metodo-text').style.display = 'inline';

            // Ocultar inputs
            row.querySelector('.fecha-input').style.display = 'none';
            row.querySelector('.tipo-input').style.display = 'none';
            row.querySelector('.concepto-input').style.display = 'none';
            row.querySelector('.monto-usd-input').style.display = 'none';

            const montoVesInput = row.querySelector('.monto-ves-input');
            if (montoVesInput) montoVesInput.style.display = 'none';

            const tasaInput = row.querySelector('.tasa-input');
            if (tasaInput) tasaInput.style.display = 'none';

            row.querySelector('.proveedor-input').style.display = 'none';
            row.querySelector('.metodo-input').style.display = 'none';

            // Restaurar botones
            const btnEditar = row.querySelector('.btn-editar');
            const btnGuardar = row.querySelector('.btn-guardar');
            const btnCancelar = row.querySelector('.btn-cancelar');

            if (btnEditar) btnEditar.style.display = 'inline-block';
            if (btnGuardar) btnGuardar.style.display = 'none';
            if (btnCancelar) btnCancelar.style.display = 'none';
        };

        window.guardarGasto = function(id) {
            const row = document.getElementById(`gasto-${id}`);
            if (!row) return;

            const tipoGastoId = row.querySelector('.tipo-input')?.value;
            const concepto = row.querySelector('.concepto-input')?.value;
            const montoUSD = row.querySelector('.monto-usd-input')?.value;
            const fecha = row.querySelector('.fecha-input')?.value;
            const proveedor = row.querySelector('.proveedor-input')?.value;
            const metodoPago = row.querySelector('.metodo-input')?.value;

            if (!tipoGastoId || !concepto || !montoUSD || !fecha) {
                alert('Todos los campos requeridos deben ser llenados');
                return;
            }

            mostrarLoading(true);

            fetch(`/viajes/${viajeId}/gastos/${id}`, {
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    tipo_gasto_id: tipoGastoId,
                    concepto: concepto,
                    monto: parseFloat(montoUSD),
                    fecha_gasto: fecha,
                    proveedor: proveedor,
                    metodo_pago: metodoPago
                })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        mostrarToast('Gasto actualizado correctamente', 'Éxito', 'success');

                        row.querySelector('.fecha-text').textContent = fecha.split('-').reverse().join('/');
                        row.querySelector('.tipo-text').textContent = data.gasto.tipo_gasto.nombre;
                        row.querySelector('.concepto-text').textContent = concepto;
                        row.querySelector('.monto-usd-text').textContent = `$${parseFloat(montoUSD).toFixed(2)}`;
                        row.querySelector('.proveedor-text').textContent = proveedor || 'N/A';
                        row.querySelector('.metodo-text').textContent = metodoPago || 'N/A';

                        cancelarEdicionGasto(id);
                        actualizarTotalesGastos();
                    } else {
                        throw new Error('Error al actualizar');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    mostrarToast(error.message, 'Error', 'danger');
                })
                .finally(() => {
                    mostrarLoading(false);
                });
        };

        window.eliminarGasto = function(id) {
            if (!confirm('¿Estás seguro de eliminar este gasto?')) return;

            mostrarLoading(true);

            fetch(`/viajes/${viajeId}/gastos/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        mostrarToast('Gasto eliminado correctamente', 'Éxito', 'success');
                        document.getElementById(`gasto-${id}`)?.remove();
                        actualizarTotalesGastos();

                        if (document.querySelectorAll('#tablaGastos tbody tr').length === 0) {
                            location.reload();
                        }
                    } else {
                        throw new Error('Error al eliminar');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    mostrarToast(error.message, 'Error', 'danger');
                })
                .finally(() => {
                    mostrarLoading(false);
                });
        };

        window.actualizarTotalesGastos = function() {
            let totalUSD = 0;
            let totalVES = 0;

            document.querySelectorAll('#tablaGastos tbody tr').forEach(row => {
                const monedaOriginal = row.getAttribute('data-moneda-original');

                // Sumar USD solo si el gasto fue originalmente en USD
                if (monedaOriginal === 'USD') {
                    const usdText = row.querySelector('.monto-usd-text')?.textContent || '';
                    const usdMatch = usdText.match(/[\d,]+\.\d+/);
                    if (usdMatch) {
                        totalUSD += parseFloat(usdMatch[0].replace(',', '')) || 0;
                    }
                }

                // Sumar VES solo si el gasto fue originalmente en VES
                if (monedaOriginal === 'VES') {
                    const vesText = row.querySelector('.monto-ves-text')?.textContent || '';
                    const vesMatch = vesText.match(/[\d,]+\.\d+/);
                    if (vesMatch) {
                        totalVES += parseFloat(vesMatch[0].replace(',', '')) || 0;
                    }
                }
            });

            // Actualizar total USD en footer
            const totalUSDElement = document.getElementById('totalUSD');
            if (totalUSDElement) totalUSDElement.textContent = `$${totalUSD.toFixed(2)}`;

            // Actualizar total VES en footer
            const totalVESElement = document.getElementById('totalVES');
            if (totalVESElement) {
                if (totalVES > 0) {
                    totalVESElement.innerHTML = `Bs. ${totalVES.toFixed(2)}`;
                } else {
                    totalVESElement.innerHTML = '<span class="text-muted">—</span>';
                }
            }

            // Actualizar campos de resumen
            const totalGastadoUSDElement = document.getElementById('total_gastado_usd');
            if (totalGastadoUSDElement) totalGastadoUSDElement.value = totalUSD.toFixed(2);

            const totalGastadoVESElement = document.getElementById('total_gastado_ves');
            if (totalGastadoVESElement) totalGastadoVESElement.value = totalVES.toFixed(2);

            calcularDiferencia();
        };

        document.addEventListener('DOMContentLoaded', function() {
            cambiarMoneda();
            calcularDiferencia();

            document.getElementById('nuevo_monto_original')?.addEventListener('input', calcularEquivalentes);
            document.getElementById('nuevo_tasa')?.addEventListener('input', calcularEquivalentes);
        });
    })();
</script>
