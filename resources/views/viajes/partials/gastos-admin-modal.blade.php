{{-- resources/views/viajes/partials/gastos-admin-modal.blade.php --}}
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12">
            <div class="alert alert-info">
                <i class="bi bi-info-circle me-2"></i>
                Administra todos los gastos del viaje. Puedes agregar, editar o eliminar gastos.
                <br><small>Los gastos con <span class="text-warning">fondo amarillo</span> son rendiciones de viáticos.</small>
                <br><small>La columna <strong>"Gastado Real"</strong> te permite registrar el monto que realmente se gastó (edición directa).</small>
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

    {{-- Formulario para agregar nuevo gasto --}}
    <div class="card mb-3 border-success">
        <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
            <h6 class="mb-0" style="color: white !important;"><i class="bi bi-plus-circle me-2"></i>Agregar Nuevo Gasto</h6>
            <span class="badge bg-light text-dark">Completa y presiona Enter o el botón</span>
        </div>
        <div class="card-body">
            <form id="formAgregarGasto" onsubmit="return agregarGasto(event)">
                @csrf
                <input type="hidden" id="viaje_id" value="{{ $viaje->id }}">

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
                        <input type="text" class="form-control" id="nuevo_concepto" placeholder="Concepto">
                    </div>
                    <div class="col-md-3 mb-2">
                        <input type="date" class="form-control" id="nueva_fecha" value="{{ date('Y-m-d') }}" required>
                    </div>
                </div>

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

                <div class="row mt-2">
                    <div class="col-12 text-end">
                        <button type="submit" class="btn btn-success" id="btnAgregarGasto">
                            <i class="bi bi-save me-1"></i>Agregar Gasto
                        </button>
                        <button type="button" class="btn btn-secondary" onclick="limpiarFormularioGasto()">
                            <i class="bi bi-arrow-counterclockwise me-1"></i>Limpiar
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
                <span class="badge bg-light text-dark" id="contadorGastos">{{ $viaje->gastos->count() }} gastos</span>
                <button type="button" class="btn btn-sm btn-outline-primary me-2" style="color: white !important;" onclick=" $(this).hide(); capture('#divgastos')">
                    <i class="bi bi-camera"></i> Capturar
                </button>
                <button type="button" class="btn btn-sm btn-warning" onclick="recargarGastos()" style="color: white !important;">
                    <i class="bi bi-arrow-clockwise"></i> Recargar
                </button>
            </div>
        </div>
        <div class="card-body" id="tablaGastosContainer">
            @if($viaje->gastos->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover" id="tablaGastos">
                        <thead>
                        <tr>
                            <th style="min-width: 90px;">Fecha</th>
                            <th style="min-width: 120px;">Tipo</th>
                            <th>Concepto</th>
                            <th style="min-width: 100px;">Monto USD</th>
                            <th style="min-width: 100px;">Monto VES</th>
                            <th style="min-width: 120px; text-align: center;">Gastado Real</th>
                            <th style="min-width: 100px; text-align: center;">Diferencia</th>
                            <th style="min-width: 80px;">Tasa</th>
                            <th style="min-width: 100px;">Proveedor</th>
                            <th style="min-width: 100px;">Método</th>
                            <th style="min-width: 130px;">Acciones</th>
                        </tr>
                        </thead>
                        <tbody id="tbodyGastos">
                        @foreach($viaje->gastos->sortByDesc('fecha_gasto') as $gasto)
                            @php
                                $gastoReal = $gasto->gasto_real ?? $gasto->monto;
                                $diferencia = $gastoReal - $gasto->monto;
                                $claseDiferencia = $diferencia > 0 ? 'text-success' : ($diferencia < 0 ? 'text-danger' : 'text-muted');
                                $iconoDiferencia = $diferencia > 0 ? '▲' : ($diferencia < 0 ? '▼' : '•');
                            @endphp
                            <tr id="gasto-{{ $gasto->id }}"
                                data-id="{{ $gasto->id }}"
                                data-moneda-original="{{ $gasto->moneda_original }}"
                                data-monto-original="{{ $gasto->monto_original }}"
                                data-tasa="{{ $gasto->tasa_cambio }}">
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
                                <td>
                                    <span class="monto-usd-text">${{ number_format($gasto->monto, 2) }}</span>
                                    <input type="number" class="form-control monto-usd-input" value="{{ $gasto->monto }}" step="0.01" min="0" style="display: none; width: 100px;">
                                </td>
                                <td>
                                    @if($gasto->moneda_original == 'VES')
                                        <span class="monto-ves-text">Bs. {{ number_format($gasto->monto_original, 2) }}</span>
                                        <input type="number" class="form-control monto-ves-input" value="{{ $gasto->monto_original }}" step="0.01" min="0" style="display: none; width: 130px;">
                                    @else
                                        <span class="monto-ves-text text-muted">—</span>
                                        <input type="number" class="form-control monto-ves-input" value="" step="0.01" min="0" style="display: none; width: 130px;">
                                    @endif
                                </td>
                                <td class="text-center">
                                    <input type="number"
                                           class="form-control form-control-sm gasto-real-input"
                                           data-id="{{ $gasto->id }}"
                                           value="{{ number_format($gastoReal, 2) }}"
                                           step="0.01"
                                           min="0"
                                           style="width: 100px; display: inline-block;"
                                           onchange="actualizarGastoReal({{ $gasto->id }}, this.value)"
                                           onfocus="this.select()"
                                           title="Monto realmente gastado">
                                </td>
                                <td class="text-center diferencia-cell" data-id="{{ $gasto->id }}">
                                        <span class="diferencia-text {{ $claseDiferencia }}">
                                            {{ $iconoDiferencia }} ${{ number_format(abs($diferencia), 2) }}
                                        </span>
                                </td>
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
                        @php
                            $totalUSDOriginal = $viaje->gastos->where('moneda_original', 'USD')->sum('monto');
                            $totalVES = $viaje->gastos->where('moneda_original', 'VES')->sum('monto_original');

                            // Calcular equivalente en USD de los gastos en VES
                            $totalVESEnUSD = 0;
                            foreach ($viaje->gastos->where('moneda_original', 'VES') as $gasto) {
                                if ($gasto->tasa_cambio > 0) {
                                    $totalVESEnUSD += $gasto->monto_original / $gasto->tasa_cambio;
                                }
                            }
                            $totalUSDEquivalente = $totalUSDOriginal + $totalVESEnUSD;

                            $totalGastoReal = $viaje->gastos->sum(function($g) { return $g->gasto_real ?? $g->monto; });
                            $totalDiferencia = $totalGastoReal - $totalUSDEquivalente;
                            $claseTotalDif = $totalDiferencia > 0 ? 'text-success' : ($totalDiferencia < 0 ? 'text-danger' : 'text-muted');
                            $iconoTotalDif = $totalDiferencia > 0 ? '▲' : ($totalDiferencia < 0 ? '▼' : '•');
                        @endphp
                        <tr>
                            <th colspan="3" class="text-end">Totales:</th>
                            <th id="totalUSD">${{ number_format($totalUSDOriginal, 2) }}</th>
                            <th id="totalVES">
                                @if($totalVES > 0)
                                    Bs. {{ number_format($totalVES, 2) }}
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </th>
                            <th class="text-center" id="totalGastoReal">
                                <div>
                                    <strong>${{ number_format($totalUSDEquivalente, 2) }}</strong>
                                    @if($totalVES > 0)
                                        <br><small class="text-muted">(USD ${{ number_format($totalUSDOriginal, 2) }} + VES convertido)</small>
                                    @endif
                                </div>
                            </th>
                            <th class="text-center" id="totalDiferencia">
            <span class="{{ $claseTotalDif }}">
                {{ $iconoTotalDif }} ${{ number_format(abs($totalDiferencia), 2) }}
            </span>
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

    {{-- Viáticos y Rendiciones --}}
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
</div>

<style>
    #tablaGastos {
        font-size: 0.9rem;
    }

    #tablaGastos thead {
        background-color: #f8f9fa;
        border-bottom: 2px solid #dee2e6;
        position: sticky;
        top: 0;
        z-index: 10;
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

    .gasto-nuevo {
        animation: highlightRow 1s ease;
    }

    @keyframes highlightRow {
        0% { background-color: #d4edda; }
        100% { background-color: transparent; }
    }

    .gasto-real-input {
        transition: all 0.2s;
    }

    .gasto-real-input:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.25);
        background-color: #fff;
    }

    .gasto-real-input:hover {
        background-color: #f8f9fa;
    }

    .diferencia-text {
        font-weight: 500;
        font-size: 0.9rem;
    }

    .diferencia-text.text-success {
        color: #28a745 !important;
    }

    .diferencia-text.text-danger {
        color: #dc3545 !important;
    }

    .diferencia-text.text-muted {
        color: #6c757d !important;
    }

    .table-responsive {
        max-height: 500px;
        overflow-y: auto;
    }
</style>

<script>
    (function() {
        'use strict';

        let viajeId = {{ $viaje->id }};
        let gastoRealTimeout = null;

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

        // ========== FUNCIONES DEL FORMULARIO ==========

        window.limpiarFormularioGasto = function() {
            document.getElementById('nuevo_tipo_gasto').value = '';
            document.getElementById('nuevo_concepto').value = '';
            document.getElementById('nuevo_moneda').value = 'USD';
            document.getElementById('nuevo_monto_original').value = '';
            document.getElementById('nuevo_tasa').value = '';
            document.getElementById('equivalente_usd').value = '';
            document.getElementById('nuevo_proveedor').value = '';
            document.getElementById('nuevo_metodo_pago').value = '';
            document.getElementById('nueva_referencia').value = '';
            document.getElementById('es_viatico').checked = false;
            // La fecha se mantiene con el valor actual
            cambiarMoneda();
            document.getElementById('nuevo_tipo_gasto').focus();
        };

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

        // ========== FUNCIONES DE VIÁTICOS ==========

        window.calcularDiferencia = function() {
            const anticipo = parseFloat(document.getElementById('anticipo_chofer')?.value) || 0;

            // 🔴 OBTENER EL TOTAL EQUIVALENTE EN USD (incluye VES convertido)
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

        // ========== RECARGAR GASTOS ==========

        window.recargarGastos = function() {
            mostrarLoading(true);

            fetch(`/viajes/${viajeId}/gastos/admin`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.html) {
                        const container = document.getElementById('tablaGastosContainer');
                        if (container) {
                            const tempDiv = document.createElement('div');
                            tempDiv.innerHTML = data.html;
                            const nuevaTabla = tempDiv.querySelector('#tablaGastosContainer')?.innerHTML;

                            if (nuevaTabla) {
                                container.innerHTML = nuevaTabla;
                                const contador = document.getElementById('contadorGastos');
                                const totalFilas = document.querySelectorAll('#tablaGastos tbody tr').length;
                                if (contador) contador.textContent = totalFilas + ' gastos';

                                actualizarTotalesGastos();
                                calcularDiferencia();

                                mostrarToast('Gastos recargados correctamente', 'Éxito', 'success');
                            } else {
                                location.reload();
                            }
                        }
                    } else {
                        throw new Error(data.error || 'Error al recargar');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    mostrarToast('Error al recargar gastos', 'Error', 'danger');
                })
                .finally(() => {
                    mostrarLoading(false);
                });
        };

        // ========== AGREGAR GASTO ==========

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
                concepto: concepto?.trim() || 'Sin concepto',
                monto: montoUSD,
                moneda_original: moneda,
                monto_original: montoOriginal,
                tasa_cambio: tasaCambio,
                fecha_gasto: fecha,
                proveedor: proveedor || null,
                metodo_pago: metodoPago || null,
                referencia_pago: referencia || null,
                es_viatico: esViatico,
                gasto_real: montoUSD // Por defecto, el gasto real es igual al monto
            };

            const btnSubmit = document.getElementById('btnAgregarGasto');
            if (btnSubmit) {
                btnSubmit.disabled = true;
                btnSubmit.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Guardando...';
            }

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

                        limpiarFormularioGasto();
                        actualizarTotalesGastos();
                        calcularDiferencia();

                        const contador = document.getElementById('contadorGastos');
                        const totalFilas = document.querySelectorAll('#tablaGastos tbody tr').length;
                        if (contador) contador.textContent = totalFilas + ' gastos';

                        const tablaContainer = document.querySelector('.table-responsive');
                        if (tablaContainer) {
                            tablaContainer.scrollTop = tablaContainer.scrollHeight;
                        }
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
                    if (btnSubmit) {
                        btnSubmit.disabled = false;
                        btnSubmit.innerHTML = '<i class="bi bi-save me-1"></i>Agregar Gasto';
                    }
                });

            return false;
        };

        // ========== GASTO REAL (EDICIÓN EN LÍNEA) ==========

        window.actualizarGastoReal = function(gastoId, valor) {
            const row = document.getElementById(`gasto-${gastoId}`);
            if (!row) return;

            const montoReal = parseFloat(valor) || 0;
            const montoOriginalText = row.querySelector('.monto-usd-text')?.textContent || '$0';
            const montoOriginal = parseFloat(montoOriginalText.replace('$', '').replace(',', '')) || 0;

            // Actualizar la diferencia en la fila
            const diferencia = montoReal - montoOriginal;
            const diferenciaCell = row.querySelector('.diferencia-cell');
            const diferenciaText = diferenciaCell?.querySelector('.diferencia-text');

            if (diferenciaText) {
                const clase = diferencia > 0 ? 'text-success' : (diferencia < 0 ? 'text-danger' : 'text-muted');
                const icono = diferencia > 0 ? '▲' : (diferencia < 0 ? '▼' : '•');
                diferenciaText.className = `diferencia-text ${clase}`;
                diferenciaText.textContent = `${icono} $${Math.abs(diferencia).toFixed(2)}`;
            }

            // Actualizar totales generales
            actualizarTotalesGastos();

            // Guardar automáticamente con debounce
            if (gastoRealTimeout) {
                clearTimeout(gastoRealTimeout);
            }

            gastoRealTimeout = setTimeout(() => {
                guardarGastoReal(gastoId, montoReal);
            }, 500);
        };

        function guardarGastoReal(gastoId, montoReal) {
            fetch(`/viajes/${viajeId}/gastos/${gastoId}/gasto-real`, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    gasto_real: montoReal
                })
            })
                .then(response => response.json())
                .then(data => {
                    if (!data.success) {
                        mostrarToast('Error al guardar el gasto real', 'Error', 'danger');
                        recargarGastos();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }

        // ========== AGREGAR GASTO A LA TABLA ==========

        function agregarGastoATabla(gasto) {
            const tbody = document.querySelector('#tablaGastos tbody');
            if (!tbody) {
                recargarGastos();
                return;
            }

            if (document.getElementById(`gasto-${gasto.id}`)) {
                return;
            }

            const fecha = new Date(gasto.fecha_gasto);
            const fechaFormateada = fecha.toLocaleDateString('es-ES');
            const tipoNombre = gasto.tipo_gasto?.nombre || 'N/A';
            const claseViatico = gasto.es_viatico ? 'table-warning' : '';

            const gastoReal = gasto.gasto_real ?? gasto.monto;
            const diferencia = gastoReal - gasto.monto;
            const claseDiferencia = diferencia > 0 ? 'text-success' : (diferencia < 0 ? 'text-danger' : 'text-muted');
            const iconoDiferencia = diferencia > 0 ? '▲' : (diferencia < 0 ? '▼' : '•');

            const montoVES = gasto.moneda_original === 'VES' ? `Bs. ${parseFloat(gasto.monto_original).toFixed(2)}` : '—';
            const tasa = gasto.moneda_original === 'VES' && gasto.tasa_cambio ? gasto.tasa_cambio.toFixed(2) : '—';

            // 🔴 Calcular equivalente en USD para mostrar en el tooltip
            let equivalenteUSD = '';
            if (gasto.moneda_original === 'VES' && gasto.tasa_cambio > 0) {
                equivalenteUSD = `($${(gasto.monto_original / gasto.tasa_cambio).toFixed(2)} USD)`;
            }

            const tiposOptions = document.querySelector('#nuevo_tipo_gasto')?.innerHTML || '';

            const nuevaFila = document.createElement('tr');
            nuevaFila.id = `gasto-${gasto.id}`;
            nuevaFila.setAttribute('data-id', gasto.id);
            nuevaFila.setAttribute('data-moneda-original', gasto.moneda_original);
            nuevaFila.setAttribute('data-monto-original', gasto.monto_original);
            nuevaFila.setAttribute('data-tasa', gasto.tasa_cambio || '');
            nuevaFila.className = claseViatico + ' gasto-nuevo';

            nuevaFila.innerHTML = `
        <td>
            <span class="fecha-text">${fechaFormateada}</span>
            <input type="date" class="form-control fecha-input" value="${gasto.fecha_gasto}" style="display: none; width: 130px;">
        </td>
        <td>
            <span class="tipo-text">${tipoNombre}</span>
            <select class="form-select tipo-input" style="display: none; width: 150px;">
                ${tiposOptions}
            </select>
        </td>
        <td>
            <span class="concepto-text">${gasto.concepto}</span>
            <input type="text" class="form-control concepto-input" value="${gasto.concepto}" style="display: none;">
        </td>
        <td>
            <span class="monto-usd-text">$${parseFloat(gasto.monto).toFixed(2)}</span>
            <input type="number" class="form-control monto-usd-input" value="${gasto.monto}" step="0.01" min="0" style="display: none; width: 100px;">
        </td>
        <td>
            <span class="monto-ves-text">${montoVES}</span>
            <input type="number" class="form-control monto-ves-input" value="${gasto.moneda_original === 'VES' ? gasto.monto_original : ''}" step="0.01" min="0" style="display: none; width: 130px;">
        </td>
        <td class="text-center">
            <input type="number"
                   class="form-control form-control-sm gasto-real-input"
                   data-id="${gasto.id}"
                   value="${gastoReal.toFixed(2)}"
                   step="0.01"
                   min="0"
                   style="width: 100px; display: inline-block;"
                   onchange="actualizarGastoReal(${gasto.id}, this.value)"
                   onfocus="this.select()"
                   title="Monto realmente gastado">
            ${equivalenteUSD ? `<br><small class="text-muted">${equivalenteUSD}</small>` : ''}
        </td>
        <td class="text-center diferencia-cell" data-id="${gasto.id}">
            <span class="diferencia-text ${claseDiferencia}">
                ${iconoDiferencia} $${Math.abs(diferencia).toFixed(2)}
            </span>
        </td>
        <td>
            <span class="tasa-text">${tasa}</span>
            <input type="number" class="form-control tasa-input" value="${gasto.tasa_cambio || ''}" step="0.01" min="0" style="display: none; width: 100px;">
        </td>
        <td>
            <span class="proveedor-text">${gasto.proveedor || 'N/A'}</span>
            <input type="text" class="form-control proveedor-input" value="${gasto.proveedor || ''}" style="display: none;">
        </td>
        <td>
            <span class="metodo-text">${gasto.metodo_pago || 'N/A'}</span>
            <select class="form-select metodo-input" style="display: none; width: 130px;">
                <option value="">N/A</option>
                <option value="efectivo" ${gasto.metodo_pago === 'efectivo' ? 'selected' : ''}>Efectivo</option>
                <option value="transferencia" ${gasto.metodo_pago === 'transferencia' ? 'selected' : ''}>Transferencia</option>
                <option value="tarjeta_credito" ${gasto.metodo_pago === 'tarjeta_credito' ? 'selected' : ''}>T. Crédito</option>
                <option value="tarjeta_debito" ${gasto.metodo_pago === 'tarjeta_debito' ? 'selected' : ''}>T. Débito</option>
            </select>
        </td>
        <td>
            <button class="btn btn-sm btn-warning btn-editar" onclick="editarGasto(${gasto.id})" title="Editar">
                <i class="bi bi-pencil"></i>
            </button>
            <button class="btn btn-sm btn-success btn-guardar" onclick="guardarGasto(${gasto.id})" style="display: none;" title="Guardar">
                <i class="bi bi-save"></i>
            </button>
            <button class="btn btn-sm btn-secondary btn-cancelar" onclick="cancelarEdicionGasto(${gasto.id})" style="display: none;" title="Cancelar">
                <i class="bi bi-x"></i>
            </button>
            <button class="btn btn-sm btn-danger" onclick="eliminarGasto(${gasto.id})" title="Eliminar">
                <i class="bi bi-trash"></i>
            </button>
        </td>
    `;

            tbody.appendChild(nuevaFila);

            setTimeout(() => {
                nuevaFila.classList.remove('gasto-nuevo');
            }, 2000);
        }

        // ========== EDICIÓN DE GASTOS ==========

        window.editarGasto = function(id) {
            const row = document.getElementById(`gasto-${id}`);
            if (!row) return;

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
                        row.querySelector('.tipo-text').textContent = data.gasto.tipo_gasto?.nombre || 'N/A';
                        row.querySelector('.concepto-text').textContent = concepto;
                        row.querySelector('.monto-usd-text').textContent = `$${parseFloat(montoUSD).toFixed(2)}`;
                        row.querySelector('.proveedor-text').textContent = proveedor || 'N/A';
                        row.querySelector('.metodo-text').textContent = metodoPago || 'N/A';

                        cancelarEdicionGasto(id);
                        actualizarTotalesGastos();
                        calcularDiferencia();
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

                        const contador = document.getElementById('contadorGastos');
                        const totalFilas = document.querySelectorAll('#tablaGastos tbody tr').length;
                        if (contador) contador.textContent = totalFilas + ' gastos';

                        actualizarTotalesGastos();
                        calcularDiferencia();

                        if (totalFilas === 0) {
                            mostrarMensajeSinGastos();
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

        function mostrarMensajeSinGastos() {
            const tbody = document.querySelector('#tablaGastos tbody');
            if (tbody) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="11" class="text-center text-muted py-3">
                            <i class="bi bi-info-circle me-2"></i>
                            No hay gastos registrados
                        </td>
                    </tr>
                `;
            }
        }

        // ========== ACTUALIZAR TOTALES ==========

        function actualizarTotalesGastos() {
            let totalUSD = 0;
            let totalVES = 0;
            let totalUSDEquivalente = 0; // 🔴 NUEVO: Total en USD equivalente (USD + VES convertido)
            let totalGastoReal = 0;

            document.querySelectorAll('#tablaGastos tbody tr').forEach(row => {
                const monedaOriginal = row.getAttribute('data-moneda-original');
                const tasa = parseFloat(row.getAttribute('data-tasa')) || 0;

                // Obtener monto en USD original (siempre existe)
                const usdText = row.querySelector('.monto-usd-text')?.textContent || '$0';
                const usdMatch = usdText.match(/[\d,]+\.\d+/);
                const montoUSD = usdMatch ? parseFloat(usdMatch[0].replace(',', '')) || 0 : 0;

                // Obtener monto en VES original (si aplica)
                const vesText = row.querySelector('.monto-ves-text')?.textContent || '';
                const vesMatch = vesText.match(/[\d,]+\.\d+/);
                const montoVES = vesMatch ? parseFloat(vesMatch[0].replace(',', '')) || 0 : 0;

                // Sumar USD directos
                if (monedaOriginal === 'USD') {
                    totalUSD += montoUSD;
                    totalUSDEquivalente += montoUSD; // Los USD ya están en USD
                }

                // Sumar VES y convertir a USD usando la tasa guardada
                if (monedaOriginal === 'VES' && montoVES > 0) {
                    totalVES += montoVES;
                    // Convertir VES a USD usando la tasa de cambio del gasto
                    if (tasa > 0) {
                        const equivalenteUSD = montoVES / tasa;
                        totalUSDEquivalente += equivalenteUSD;
                    } else {
                        // Si no hay tasa, intentar obtener del campo de tasa
                        const tasaText = row.querySelector('.tasa-text')?.textContent || '';
                        const tasaMatch = tasaText.match(/[\d,]+\.\d+/);
                        const tasaValor = tasaMatch ? parseFloat(tasaMatch[0].replace(',', '')) || 0 : 0;
                        if (tasaValor > 0) {
                            const equivalenteUSD = montoVES / tasaValor;
                            totalUSDEquivalente += equivalenteUSD;
                        }
                    }
                }

                // Sumar Gasto Real (solo para gastos en USD, porque los VES ya están convertidos)
                const gastoRealInput = row.querySelector('.gasto-real-input');
                if (gastoRealInput) {
                    const gastoReal = parseFloat(gastoRealInput.value) || 0;
                    totalGastoReal += gastoReal;
                }
            });

            // Actualizar totales en el footer
            const totalUSDElement = document.getElementById('totalUSD');
            if (totalUSDElement) totalUSDElement.textContent = `$${totalUSD.toFixed(2)}`;

            const totalVESElement = document.getElementById('totalVES');
            if (totalVESElement) {
                if (totalVES > 0) {
                    totalVESElement.innerHTML = `Bs. ${totalVES.toFixed(2)}`;
                } else {
                    totalVESElement.innerHTML = '<span class="text-muted">—</span>';
                }
            }

            // 🔴 NUEVO: Mostrar el total equivalente en USD (USD + VES convertido)
            const totalGastoRealElement = document.getElementById('totalGastoReal');
            if (totalGastoRealElement) {
                // Si hay gastos en VES, mostrar el total convertido
                if (totalVES > 0) {
                    totalGastoRealElement.innerHTML = `
                <div>
                    <strong>$${totalUSDEquivalente.toFixed(2)}</strong>
                    <br><small class="text-muted">(USD $${totalUSD.toFixed(2)} + VES convertido)</small>
                </div>
            `;
                } else {
                    totalGastoRealElement.textContent = `$${totalUSDEquivalente.toFixed(2)}`;
                }
            }

            // Calcular diferencia total (Gasto Real vs Total en USD)
            const totalDiferencia = totalGastoReal - totalUSDEquivalente;
            const totalDiferenciaElement = document.getElementById('totalDiferencia');
            if (totalDiferenciaElement) {
                const clase = totalDiferencia > 0 ? 'text-success' : (totalDiferencia < 0 ? 'text-danger' : 'text-muted');
                const icono = totalDiferencia > 0 ? '▲' : (totalDiferencia < 0 ? '▼' : '•');
                totalDiferenciaElement.innerHTML = `
            <span class="${clase}">
                ${icono} $${Math.abs(totalDiferencia).toFixed(2)}
            </span>
        `;
            }

            // 🔴 ACTUALIZAR EL CAMPO DE TOTAL GASTADO EN LA SECCIÓN DE VIÁTICOS
            const totalGastadoUSDElement = document.getElementById('total_gastado_usd');
            if (totalGastadoUSDElement) {
                // Mostrar el total equivalente en USD (USD + VES convertido)
                totalGastadoUSDElement.value = totalUSDEquivalente.toFixed(2);
            }

            // Actualizar total VES (sin cambios)
            const totalGastadoVESElement = document.getElementById('total_gastado_ves');
            if (totalGastadoVESElement) {
                totalGastadoVESElement.value = totalVES.toFixed(2);
            }

            // Recalcular la diferencia de viáticos
            calcularDiferencia();
        }

        // ========== KEYBOARD SHORTCUT ==========

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                const form = document.getElementById('formAgregarGasto');
                const activeElement = document.activeElement;

                if (form && activeElement && form.contains(activeElement)) {
                    e.preventDefault();
                    form.dispatchEvent(new Event('submit'));
                }
            }
        });

        // ========== INICIALIZACIÓN ==========

        document.addEventListener('DOMContentLoaded', function() {
            cambiarMoneda();
            calcularDiferencia();

            document.getElementById('nuevo_monto_original')?.addEventListener('input', calcularEquivalentes);
            document.getElementById('nuevo_tasa')?.addEventListener('input', calcularEquivalentes);
        });

        // Exponer funciones al scope global
        window.editarGasto = editarGasto;
        window.cancelarEdicionGasto = cancelarEdicionGasto;
        window.guardarGasto = guardarGasto;
        window.eliminarGasto = eliminarGasto;
        window.agregarGasto = agregarGasto;
        window.actualizarTotalesGastos = actualizarTotalesGastos;

    })();
</script>
