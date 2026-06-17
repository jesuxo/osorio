<div class="container-fluid">

    {{-- Tabla de resumen por pago --}}
    <div class="card">
        <div class="card-header bg-light d-flex justify-content-between align-items-center d-none">
            <h6 class="mb-0">Detalle de Pedido</h6>
            <div>
                <input type="text" id="buscarResumen" class="form-control form-control-sm" placeholder="Buscar por folio o proveedor..." style="width: 250px;">
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive" style="max-height: 60vh; overflow-y: auto;">
                <table class="table table-bordered table-hover mb-0" id="tablaResumen">
                    <thead class="table-light sticky-top">
                    <tr>
                        <th class="text-center">Notas</th>
                        <th class="text-center">Fecha</th>
                        <th class="text-center">Motos</th>
                        <th class="text-center">Motos x Recibir</th>
                        <th class="text-center">Monto</th>
                        <th class="text-center">Comprobantes</th>
                        <th class="text-center">Diferencia</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($resumenPagos as $pago)
                        <tr>
                            <td class="text-start">{{ $pago['notas'] }} <br><small class="text-muted small">{{ $pago['numero_aprobacion'] }}</small></td>
                            <td class="text-center">{{ \Carbon\Carbon::parse($pago['fecha_pago'])->format('d/m/Y') }}</td>
                            <td class="text-center">
                                @if(!empty($pago['motos_por_instancia']))
                                    @foreach($pago['motos_por_instancia'] as $instancia => $cantidad)
                                        <div class="mb-1">
                                            <span class="badge bg-secondary">{{ $instancia }}</span>
                                            <span class="badge bg-info">{{ $cantidad }} motos</span>
                                        </div>
                                    @endforeach
                                @else
                                    <span class="text-muted">Sin categoría</span>
                                @endif
                            </td>
                            <td class="text-center text-warning fw-bold">
                                {{ number_format($pago['motos_pendientes_recibir'], 0) }}
                            </td>
                            <td class="text-end">${{ number_format($pago['monto_total'], 2) }}</td>
                            <td class="text-end">
                                ${{ number_format($pago['total_comprobantes'], 2) }}
                                <br><small class="text-muted">{{ $pago['cantidad_comprobantes'] }} comprobantes</small>
                            </td>
                            <td class="text-end {{ $pago['diferencia'] > 0 ? 'text-danger fw-bold' : 'text-success' }}">
                                ${{ number_format($pago['diferencia'], 2) }}
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-sm btn-warning" onclick="editarPago({{ $pago['id'] }})" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="btn btn-sm btn-secondary" onclick="agregarComprobante({{ $pago['id'] }})" title="Agregar comprobante">
                                        <i class="bi bi-camera"></i>
                                    </button>
                                    <button class="btn btn-sm btn-info" onclick="verComprobantes({{ $pago['id'] }})" title="Ver comprobantes">
                                        <i class="bi bi-receipt"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot class="table-secondary">
                    <tr>
                        <th colspan="2" class="text-end">TOTALES:</th>
                        <th class="text-center">{{ number_format($estadisticas['total_motos'], 0) }}</th>
                        <th class="text-center">{{ number_format($estadisticas['total_unidades_pendientes'] ?? 0, 0) }}</th>
                        <th class="text-end">${{ number_format($estadisticas['total_monto_pagos'], 2) }}</th>
                        <th class="text-end">${{ number_format($estadisticas['total_comprobantes'], 2) }}</th>
                        <th class="text-end fw-bold">${{ number_format($estadisticas['total_diferencia'], 2) }}</th>
                        <th class="text-end fw-bold"></th>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    {{-- NUEVO: Resumen de modelos pendientes por recibir --}}
    @if(isset($modelosPendientes) && count($modelosPendientes) > 0)
        <div class="card mt-4 border-warning">
            <div class="card-header bg-warning text-dark d-flex justify-content-between">
                <h6 class="mb-0">
                    <i class="bi bi-truck me-2"></i>
                    <strong>RESUMEN DE MODELOS PENDIENTES POR RECIBIR</strong>
                    <span class="badge bg-dark ms-2">
                    {{ count($modelosPendientes) }} modelos |
                    {{ number_format($estadisticas['total_unidades_pendientes'] ?? 0, 0, ',', '.') }} unidades
                </span>
                </h6>
                <div>
                    <button type="button" class="btn btn-sm btn-light me-2"   onclick="   capture('#motospagasporrecibir')">
                        <i class="bi bi-camera"></i> Capturar
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive" style="max-height: unset !important;">
                    <table class="table table-sm table-bordered mb-0" id="motospagasporrecibir" >
                        <thead class="table-light">
                        <tr class="text-center">
                            <th style="width: 15%">Código</th>
                            <th style="width: 40%">Modelo</th>
                            <th style="width: 20%">Cantidad Pendiente</th>
                            <th style="width: 25%">% del Total</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php
                            $totalUnidades = $estadisticas['total_unidades_pendientes'] ?? 0;
                        @endphp
                        @foreach($modelosPendientes as $modelo)
                            <tr>
                                <td class="text-center text-muted">
                                    <code>{{ $modelo['codprod'] ?? 'N/A' }}</code>
                                </td>
                                <td>
                                    <strong>{{ $modelo['modelo'] }}</strong>
                                </td>
                                <td class="text-center">
                                <span class="badge bg-danger fs-6 p-2">
                                    <i class="bi bi-truck me-1"></i>
                                    {{ number_format($modelo['pendientes'], 0, ',', '.') }} unidades
                                </span>
                                </td>
                                <td class="text-center" style="width: 200px;">
                                    <div class="d-flex justify-content-between align-items-center">
                                    <span class="small fw-bold">
                                        {{ $totalUnidades > 0 ? number_format(($modelo['pendientes'] / $totalUnidades) * 100, 1) : 0 }}%
                                    </span>
                                        <div class="progress flex-grow-1 ms-2" style="height: 8px;">
                                            <div class="progress-bar bg-danger"
                                                 role="progressbar"
                                                 style="width: {{ $totalUnidades > 0 ? ($modelo['pendientes'] / $totalUnidades) * 100 : 0 }}%">
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                        <tfoot class="table-warning fw-bold">
                        <tr>
                            <td colspan="2" class="text-end">TOTAL PENDIENTE:</td>
                            <td class="text-center">
                                <span class="badge bg-danger fs-6 p-2">
                                    {{ number_format($totalUnidades, 0, ',', '.') }} unidades
                                </span>
                            </td>
                            <td class="text-center">100%</td>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    @endif

    {{-- Mensaje cuando no hay modelos pendientes --}}
    @if(isset($modelosPendientes) && count($modelosPendientes) == 0 && ($estadisticas['total_unidades_pendientes'] ?? 0) == 0)
        <div class="alert alert-success mt-4 text-center">
            <i class="bi bi-check-circle-fill me-2"></i>
            <strong>¡Excelente!</strong> No hay modelos pendientes por recibir. Todos los pedidos están completos.
        </div>
    @endif

    <hr class="my-3 d-none">

    <div class="d-flex justify-content-end gap-2 d-none">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            <i class="bi bi-x-circle me-1"></i>Cerrar
        </button>
        <a class="btn btn-primary" href="/pagos-proveedores/exportar-resumen">
            <i class="ri-download-line me-1"></i>Exportar a Excel
        </a>
    </div>
</div>

<script>
    // Búsqueda en la tabla de resumen
    $('#buscarResumen').on('keyup', function() {
        const term = $(this).val().toLowerCase();
        $('#tablaResumen tbody tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(term) > -1);
        });
    });
</script>

<style>
    .sticky-top {
        position: sticky;
        top: 0;
        z-index: 10;
    }

    /* Animación suave para la tabla de modelos */
    .table-sm tbody tr {
        transition: background-color 0.2s ease;
    }

    .table-sm tbody tr:hover {
        background-color: #fff3cd !important;
    }
</style>
