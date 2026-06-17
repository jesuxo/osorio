{{-- resources/views/reportes/proveedor/index.blade.php --}}
@extends('layouts.master')

@section('title', 'Pagos del Proveedor')

@section('css')
    <style>
        .table-success {
            /* Mantén todas las variables existentes */
            --tb-table-hover-bg: #e3f2fd !important; /* Cambia black por el color que prefieras */
            --tb-table-hover-color: #000 !important; /* Ajusta el color del texto si es necesario */
        }
        .stat-card {
            background: white;
            border-radius: 8px;
            padding: 1rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            height: 100%;
        }

        .filter-section {
            background: white;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .badge-pendiente { background-color: #f59e0b; color: white; }
        .badge-conciliado { background-color: #10b981; color: white; }
        .badge-discrepancia { background-color: #ef4444; color: white; }

        .table-proveedor {
            font-size: 0.9rem;
        }

        .loading-spinner {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 9999;
        }

        .loading-spinner.active {
            display: block;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col-12">
                <h1 class="h2 mb-0">
                    <i class="bi bi-truck me-2 text-primary"></i>
                    Pagos del Proveedor
                </h1>
                <p class="text-muted">
                    Control de pagos de: <strong>{{ $proveedor->descrip ?? 'Proveedor' }}</strong>
                </p>
            </div>
        </div>

        {{-- Filtros --}}
        <div class="filter-section">
            <form method="GET" action="{{ route('reportes.proveedor.index') }}" id="filtrosForm">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Fecha Desde</label>
                        <input type="date" class="form-control" name="fecha_inicio" value="{{ $fechaInicio }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Fecha Hasta</label>
                        <input type="date" class="form-control" name="fecha_fin" value="{{ $fechaFin }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Estado</label>
                        <select class="form-select" name="estado">
                            <option value="pendiente" {{ $estado == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                            <option value="conciliado" {{ $estado == 'conciliado' ? 'selected' : '' }}>Pagado</option>
                            <option value="discrepancia" {{ $estado == 'discrepancia' ? 'selected' : '' }}>Discrepancia</option>
                            <option value="todos" {{ $estado == 'todos' ? 'selected' : '' }}>Todos</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-search me-2"></i>Filtrar
                        </button>
                    </div>
                </div>
            </form>
        </div>

        {{-- Cards de resumen --}}
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="stat-card">
                    <h6 class="text-muted mb-2">Total Transporte</h6>
                    <h3 class="mb-0 text-primary">${{ number_format($totales['transporte'], 2) }}</h3>
                    <small>Pagado por proveedor</small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <h6 class="text-muted mb-2">Debe Pagar (Esperado)</h6>
                    <h3 class="mb-0 text-warning">${{ number_format($totales['esperado'], 2) }}</h3>
                    <small>Transporte - 30%</small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <h6 class="text-muted mb-2">Pagado Real</h6>
                    <h3 class="mb-0 text-success">${{ number_format($totales['real'], 2) }}</h3>
                    <small>Lo que ha pagado</small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <h6 class="text-muted mb-2">Diferencia</h6>
                    <h3 class="mb-0 {{ $totales['diferencia'] >= 0 ? 'text-success' : 'text-danger' }}">
                        ${{ number_format($totales['diferencia'], 2) }}
                    </h3>
                    <small>Real - Esperado</small>
                </div>
            </div>
        </div>

        {{-- Estado de pagos --}}
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="stat-card bg-warning text-dark">
                    <h6>Pendientes</h6>
                    <h3>{{ $totales['pendiente'] }} registros</h3>
                    <h4>${{ number_format($totales['esperado'] - $totales['conciliado'] - $totales['discrepancia'], 2) }}</h4>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card bg-success text-white">
                    <h6>Pagados (Conciliados)</h6>
                    <h3>{{ $totales['conciliado'] }} registros</h3>
                    <h4>${{ number_format($totales['conciliado'] ?: 0, 2) }}</h4>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card bg-danger text-white">
                    <h6>Con Discrepancia</h6>
                    <h3>{{ $totales['discrepancia'] }} registros</h3>
                    <h4>${{ number_format($totales['discrepancia'] ?: 0, 2) }}</h4>
                </div>
            </div>
        </div>

        {{-- Tabla de registros --}}
        <div class="card">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="bi bi-list me-2"></i>Detalle de Pagos del Proveedor</h6>
                <a href="{{ route('reportes.proveedor.exportar', request()->all()) }}" class="btn btn-sm btn-light">
                    <i class="bi bi-file-excel me-2"></i>Exportar Excel
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-proveedor">
                        <thead>
                        <tr>
                            <th>Fecha Viaje</th>
                            <th>Viaje</th>
                            <th>Cliente</th>
                            <th>Modelo</th>
                            <th>Cant.</th>
                            <th>Transporte</th>
                            <th>Ret. 30%</th>
                            <th>Debe Pagar</th>
                            <th>Pagó</th>
                            <th>Diferencia</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($registros as $reg)
                            <tr class="{{ $reg->estado_conciliacion == 'discrepancia' ? 'table-danger' : ($reg->estado_conciliacion == 'conciliado' ? 'table-success' : '') }}">
                                <td>{{ $reg->viaje->fecha_inicio->format('d/m/Y') }}</td>
                                <td>
                                    <a href="#" onclick="window.open('/viajes/{{ $reg->viaje_id }}/ver', '_blank')">
                                        {{ $reg->viaje->folio ?? $reg->viaje_id }}
                                    </a>
                                </td>
                                <td>{{ $reg->cliente->descrip ?? 'N/A' }}</td>
                                <td>{{ $reg->modelo_moto }}</td>
                                <td class="text-center">{{ $reg->cantidad }}</td>
                                <td class="text-end">${{ number_format($reg->monto_transporte_proveedor, 2) }}</td>
                                <td class="text-end">${{ number_format($reg->retencion_proveedor, 2) }}</td>
                                <td class="text-end text-primary">${{ number_format($reg->monto_esperado_cliente ?? 0, 2) }}</td>
                                <td class="text-end text-success">${{ number_format($reg->monto_real_cliente ?? 0, 2) }}</td>
                                <td class="text-end {{ ($reg->diferencia ?? 0) >= 0 ? 'text-success' : 'text-danger' }}">
                                    ${{ number_format($reg->diferencia ?? 0, 2) }}
                                </td>
                                <td>
                                    @if($reg->estado_conciliacion == 'conciliado')
                                        <span class="badge badge-conciliado">Pagado</span>
                                        <br><small>{{ $reg->fecha_conciliacion ? \Carbon\Carbon::parse($reg->fecha_conciliacion)->format('d/m/Y') : '' }}</small>
                                    @elseif($reg->estado_conciliacion == 'discrepancia')
                                        <span class="badge badge-discrepancia">Discrepancia</span>
                                    @else
                                        <span class="badge badge-pendiente">Pendiente</span>
                                    @endif
                                </td>
                                <td>
                                    @if($reg->estado_conciliacion != 'conciliado')
                                        <button class="btn btn-sm btn-primary" onclick="registrarPago({{ $reg->id }}, {{ $reg->monto_esperado_cliente }})">
                                            <i class="bi bi-cash"></i> Registrar Pago
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="12" class="text-center py-4">
                                    <i class="bi bi-inbox fs-1 d-block mb-3 text-muted"></i>
                                    <p class="text-muted">No hay registros para mostrar</p>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                {{ $registros->withQueryString()->links() }}
            </div>
        </div>
    </div>

    {{-- Modal para registrar pago --}}
    <div class="modal fade" id="modalPago" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">
                        <i class="bi bi-cash me-2"></i>
                        Registrar Pago del Proveedor
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formPago">
                        @csrf
                        <input type="hidden" id="registro_id" name="registro_id">

                        <div class="mb-3">
                            <label class="form-label">Monto Esperado</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="text" class="form-control" id="monto_esperado_display" readonly>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Monto Pagado <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control" id="monto_real" name="monto_real" step="0.01" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Fecha de Pago <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="fecha_pago" name="fecha_pago" value="{{ date('Y-m-d') }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Notas</label>
                            <textarea class="form-control" id="notas_pago" rows="3"></textarea>
                        </div>

                        <div class="alert alert-info" id="previewDiferencia" style="display: none;">
                            <strong>Diferencia:</strong> $<span id="diferencia_valor">0.00</span>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-success" onclick="guardarPago()">
                        <i class="bi bi-save me-2"></i>Registrar Pago
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Loading Spinner --}}
    <div class="loading-spinner" id="loadingSpinner">
        <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;">
            <span class="visually-hidden">Cargando...</span>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        const modalPago = new bootstrap.Modal(document.getElementById('modalPago'));

        function registrarPago(id, montoEsperado) {
            document.getElementById('registro_id').value = id;
            document.getElementById('monto_esperado_display').value = montoEsperado.toFixed(2);
            document.getElementById('monto_real').value = montoEsperado.toFixed(2);
            document.getElementById('fecha_pago').valueAsDate = new Date();
            document.getElementById('notas_pago').value = '';
            document.getElementById('previewDiferencia').style.display = 'none';
            modalPago.show();
        }

        document.getElementById('monto_real').addEventListener('input', calcularDiferencia);

        function calcularDiferencia() {
            const esperado = parseFloat(document.getElementById('monto_esperado_display').value) || 0;
            const real = parseFloat(document.getElementById('monto_real').value) || 0;
            const diferencia = real - esperado;

            document.getElementById('diferencia_valor').textContent = diferencia.toFixed(2);
            document.getElementById('previewDiferencia').style.display = 'block';

            const preview = document.getElementById('previewDiferencia');
            if (Math.abs(diferencia) < 0.01) {
                preview.className = 'alert alert-success';
            } else {
                preview.className = 'alert alert-warning';
            }
        }

        function guardarPago() {
            const id = document.getElementById('registro_id').value;
            const montoReal = document.getElementById('monto_real').value;
            const fechaPago = document.getElementById('fecha_pago').value;
            const notas = document.getElementById('notas_pago').value;

            if (!montoReal || !fechaPago) {
                alert('Debes completar todos los campos');
                return;
            }

            mostrarLoading(true);

            fetch(`/reportes/proveedor/marcar-pagado/${id}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    monto_real: montoReal,
                    fecha_pago: fechaPago,
                    notas: notas
                })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        mostrarToast('Pago registrado correctamente', 'Éxito', 'success');
                        modalPago.hide();
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        mostrarToast(data.message || 'Error al registrar', 'Error', 'danger');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    mostrarToast('Error de conexión', 'Error', 'danger');
                })
                .finally(() => {
                    mostrarLoading(false);
                });
        }

        function mostrarLoading(mostrar) {
            const spinner = document.getElementById('loadingSpinner');
            if (mostrar) {
                spinner.classList.add('active');
            } else {
                spinner.classList.remove('active');
            }
        }

        function mostrarToast(mensaje, titulo, tipo) {
            if (typeof window.mostrarToast === 'function') {
                window.mostrarToast(mensaje, titulo, tipo);
            } else {
                alert(mensaje);
            }
        }
    </script>
@endsection
