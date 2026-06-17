{{-- resources/views/viajes/partials/motos-admin-modal.blade.php --}}
<style>
    .table-success {
        /* Mantén todas las variables existentes */
        --tb-table-hover-bg: #e3f2fd !important; /* Cambia black por el color que prefieras */
        --tb-table-hover-color: #000 !important; /* Ajusta el color del texto si es necesario */
    }
</style>
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12">
            <div class="alert alert-info">
                <i class="bi bi-info-circle me-2"></i>
                Administra las motos transportadas en este viaje. Puedes agregar, editar o eliminar motos.
                <br><small>Asigna cada moto a un cliente para control de facturación.</small>
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
                            <small class="text-muted">Total Motos:</small>
                            <strong id="totalMotosDisplay">{{ $viaje->motosTransportadas->sum('cantidad') }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Formulario para agregar nueva moto --}}
    <div class="card mb-3 border-success">
        <div class="card-header bg-success text-white">
            <h6 class="mb-0 text-white"><i class="bi bi-plus-circle me-2"></i>Agregar Nueva Moto</h6>
        </div>
        <div class="card-body">
            <form id="formAgregarMoto" onsubmit="agregarMoto(event)">
                @csrf
                <input type="hidden" id="viaje_id" value="{{ $viaje->id }}">

                {{-- Datos básicos de la moto --}}
                <div class="row">
                    <div class="col-md-3 mb-2">
                        <label class="form-label">Cliente <span class="text-danger">*</span></label>
                        <select class="form-select" id="nuevo_cliente" required>
                            <option value="">Seleccione cliente</option>
                            @foreach($clientes as $cliente)
                                <option value="{{ $cliente->codclie }}">{{ $cliente->descrip }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-2 position-relative">
                        <label class="form-label">Modelo de Moto <span class="text-danger">*</span></label>
                        <div class="position-relative">
                            <input type="text"
                                   class="form-control"
                                   id="nuevo_modelo"
                                   placeholder="Escribe para buscar modelo..."
                                   autocomplete="off"
                                   oninput="window.buscarModelosMoto(this.value)"
                                   onfocus="this.select()">
                            <input type="hidden" id="modelo_id" name="modelo_id">
                            <div id="resultados-modelos" class="list-group position-absolute w-100" style="z-index: 1000; max-height: 300px; overflow-y: auto; display: none;"></div>
                        </div>
                        <small class="text-muted">Escribe al menos 2 caracteres para buscar</small>
                    </div>
                    <div class="col-md-2 mb-2">
                        <label class="form-label">Cantidad <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="nueva_cantidad" placeholder="Cantidad" min="1" required>
                    </div>
                    <div class="col-md-2 mb-2">
                        <label class="form-label">Precio por moto <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="nuevo_precio" placeholder="Precio por moto" step="0.01" min="0" required>
                    </div>
                    <div class="col-md-2 mb-2">
                        <label class="form-label"> &nbsp; </label>
                        <button type="submit" class="btn btn-success w-100">
                            <i class="bi bi-save me-1"></i>Agregar
                        </button>
                    </div>
                </div>

                {{-- SECCIÓN DE PROVEEDOR - AHORA VISIBLE SIEMPRE PERO COLAPSABLE --}}
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="card border-info">
                            <div class="card-header bg-info text-white py-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="proveedor_paga_nuevo">
                                    <label class="form-check-label text-white" for="proveedor_paga_nuevo">
                                        <i class="bi bi-truck me-2"></i>
                                        Este transporte es pagado por un proveedor
                                    </label>
                                </div>
                            </div>
                            <div class="card-body" id="camposProveedorNuevo" style="display: none;">
                                <div class="row">
                                    <div class="col-md-6 mb-2">
                                        <label class="form-label">Seleccione Proveedor</label>
                                        <select class="form-select" id="nuevo_proveedor_codprov">
                                            <option value="">Seleccione proveedor</option>
                                            @foreach($proveedores as $prov)
                                                <option value="{{ $prov->codprov }}">{{ $prov->descrip }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <label class="form-label">Monto del transporte</label>
                                        <input type="number" class="form-control" id="nuevo_monto_transporte" step="0.01" min="0" placeholder="0.00">
                                    </div>
                                </div>

                                <div class="alert alert-info mt-2 mb-0">
                                    <small>
                                        <i class="bi bi-info-circle me-1"></i>
                                        <strong>Cálculo automático:</strong><br>
                                        Transporte: <span id="preview_transporte">0.00</span><br>
                                        Retención (30%): <span id="preview_retencion">0.00</span><br>
                                        <strong>El proveedor pagará: <span id="preview_total">0.00</span></strong>
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Sección de Proveedor (visible solo al editar) --}}
    <div class="card mt-3 border-info" id="proveedorSection" style="display: none;">
        <div class="card-header bg-info text-white">
            <h6 class="mb-0"><i class="bi bi-truck me-2"></i>Pago por Proveedor</h6>
        </div>
        <div class="card-body">
            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" id="proveedor_paga">
                <label class="form-check-label" for="proveedor_paga">
                    Este transporte es pagado por proveedor
                </label>
            </div>

            <div id="camposProveedor" style="display: none;">
                <div class="row">
                    <div class="col-md-6 mb-2">
                        <label class="form-label">Proveedor</label>
                        <select class="form-select" id="proveedor_codprov">
                            <option value="">Seleccione proveedor</option>
                            @foreach($proveedores as $prov)
                                <option value="{{ $prov->codprov }}">{{ $prov->descrip }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-2">
                        <label class="form-label">Monto transporte</label>
                        <input type="number" class="form-control" id="monto_transporte_proveedor" step="0.01" min="0">
                    </div>
                </div>

                <div class="alert alert-info mt-2">
                    <small>
                        <i class="bi bi-info-circle me-1"></i>
                        Retención del 30%: Se calculará automáticamente al guardar
                    </small>
                </div>
            </div>
        </div>
    </div>

    {{-- Lista de motos existentes --}}
    <div class="card">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h6 class="mb-0 text-white" ><i class="bi bi-list me-2"></i>Motos Registradas</h6>
            <span class="badge bg-light text-dark">{{ $viaje->motosTransportadas->count() }} registros</span>
        </div>
        <div class="card-body">
            @if($viaje->motosTransportadas->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover" id="tablaMotos">
                        <thead>
                        <tr>
                            <th>Cliente</th>
                            <th>Proveedor</th>
                            <th>Modelo</th>
                            <th>Cantidad</th>
                            <th>Precio Unit.</th>
                            <th>Subtotal</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                        </thead>
                        <tbody id="tablaMotosBody">
                        @foreach($viaje->motosTransportadas as $moto)
                            <tr id="moto-{{ $moto->id }}" data-id="{{ $moto->id }}" class="{{ $moto->facturado ? 'table-success' : '' }}">
                                <td>
                                    <span class="cliente-text" data-codclie="{{ $moto->cliente_codclie }}">{{ $moto->cliente->descrip ?? 'Sin asignar' }}</span>
                                    <select class="form-select cliente-input" style="display: none;">
                                        @foreach($clientes as $cliente)
                                            <option value="{{ $cliente->codclie }}" {{ $moto->cliente_codclie == $cliente->codclie ? 'selected' : '' }}>
                                                {{ $cliente->descrip }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    @if($moto->proveedor_paga)
                                        <span class="badge bg-info" title="Pagado por proveedor">
                                            <i class="bi bi-truck"></i>
                                            {{ $moto->proveedor->descrip ?? 'Proveedor' }}
                                        </span>
                                        <br>
                                        <small class="text-muted">${{ number_format($moto->monto_transporte_proveedor ?? 0, 2) }}</small>
                                        <br>
                                        <small class="text-warning">Ret: ${{ number_format($moto->retencion_proveedor ?? 0, 2) }}</small>

                                        {{-- Campos ocultos para edición --}}
                                        <input type="hidden" class="proveedor-paga" value="1">
                                        <input type="hidden" class="proveedor-codprov" value="{{ $moto->proveedor_codprov }}">
                                        <input type="hidden" class="monto-transporte" value="{{ $moto->monto_transporte_proveedor }}">
                                    @else
                                        <span class="text-muted">-</span>
                                        <input type="hidden" class="proveedor-paga" value="0">
                                    @endif
                                </td>
                                <td>
                                    <span class="modelo-text">{{ $moto->modelo_moto }}</span>
                                    <input type="text" class="form-control modelo-input" value="{{ $moto->modelo_moto }}" style="display: none;">
                                </td>
                                <td>
                                    <span class="cantidad-text">{{ $moto->cantidad }}</span>
                                    <input type="number" class="form-control cantidad-input" value="{{ $moto->cantidad }}" min="1" style="display: none; width: 80px;">
                                </td>
                                <td>
                                    <span class="precio-text">${{ number_format($moto->precio_por_moto, 2) }}</span>
                                    <input type="number" class="form-control precio-input" value="{{ $moto->precio_por_moto }}" step="0.01" min="0" style="display: none; width: 100px;">
                                </td>
                                <td class="subtotal">${{ number_format($moto->cantidad * $moto->precio_por_moto, 2) }}</td>
                                <td>
                                    @if($moto->facturado)
                                        <span class="badge bg-success">Facturado</span>
                                        <br><small>{{ $moto->fecha_facturacion?->format('d/m/Y') }}</small>
                                    @else
                                        <span class="badge bg-warning">Pendiente</span>
                                        @if($moto->cliente_codclie !== 'V15184480')
                                            <button class="btn btn-sm btn-primary ms-1" onclick="marcarFacturado({{ $moto->id }})" title="Facturar">
                                                <i class="bi bi-file-check"></i>
                                            </button>
                                        @endif
                                    @endif

                                    {{-- Estado de conciliación --}}
                                    @if($moto->proveedor_paga && $moto->estado_conciliacion)
                                        <br>
                                        @if($moto->estado_conciliacion == 'conciliado')
                                            <span class="badge bg-success">Conciliado</span>
                                        @elseif($moto->estado_conciliacion == 'discrepancia')
                                            <span class="badge bg-danger">Discrepancia</span>
                                        @else
                                            <span class="badge bg-warning">Pendiente conciliar</span>
                                        @endif
                                    @endif
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-warning btn-editar" onclick="editarMoto({{ $moto->id }})" title="Editar">
                                        <i class="bi bi-pen-fill"></i>
                                    </button>
                                    <button class="btn btn-sm btn-success btn-guardar" onclick="guardarMoto({{ $moto->id }})" style="display: none;" title="Guardar">
                                        <i class="bi bi-save-fill"></i>
                                    </button>
                                    <button class="btn btn-sm btn-secondary btn-cancelar" onclick="cancelarEdicion({{ $moto->id }})" style="display: none;" title="Cancelar">
                                        <i class="bi bi-x"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger" onclick="eliminarMoto({{ $moto->id }})" title="Eliminar">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                        <tfoot>
                        @php
                            $totalProveedor = $viaje->motosTransportadas->where('proveedor_paga', true)->sum('monto_transporte_proveedor');
                            $totalRetenciones = $viaje->motosTransportadas->where('proveedor_paga', true)->sum('retencion_proveedor');
                        @endphp

                        @if($totalProveedor > 2222222222220)
                            <tr class="table-info">
                                <th colspan="2" class="text-end">Total  Proveedores:</th>
                                <th colspan="2">${{ number_format($totalProveedor, 2) }}</th>
                                <th>Retenciones: ${{ number_format($totalRetenciones, 2) }}</th>
                                <th colspan="2"></th>
                            </tr>
                        @endif

                        <tr class="table-primary">
                            <th colspan="5" class="text-end">Totales Generales:</th>
                            <th id="totalMotos">{{ $viaje->motosTransportadas->sum('cantidad') }} motos</th>
                            <th id="totalIngreso">${{ number_format($viaje->motosTransportadas->sum(function($m) { return $m->cantidad * $m->precio_por_moto; }), 2) }}</th>
                            <th></th>
                        </tr>
                        </tfoot>
                    </table>

                    {{-- Resumen por Cliente --}}
                    @if($viaje->motosTransportadas->count() > 0)
                        <hr>
                        <h6 class="mt-3">Resumen por Cliente</h6>
                        <table class="table table-sm">
                            @php
                                $otrosClientes = [];
                                $totalGeneral = 0;

                                foreach($viaje->resumen_clientes as $codclie => $data) {
                                    // Excluir al cliente V15184480
                                    if($codclie !== 'V15184480') {
                                        $otrosClientes[] = $data;
                                        $totalGeneral += $data['total_pagar'];
                                    }
                                }
                            @endphp

                            @forelse($otrosClientes as $data)
                                <tr class="{{ $data['facturado'] ? 'table-success' : 'table-warning' }}">
                                    <td><strong>{{ $data['cliente'] }}</strong></td>
                                    <td>{{ $data['total_motos'] }} motos</td>
                                    <td><strong>${{ number_format($data['total_pagar'], 2) }}</strong></td>
                                    <td>
                                        @if($data['facturado'])
                                            <span class="badge bg-success">Pagado</span>
                                        @else
                                            <span class="badge bg-warning">Pendiente</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if(!$data['facturado'])
                                            <button class="btn btn-sm btn-primary" onclick="facturarCliente('{{ $codclie }}')">
                                                <i class="bi bi-file-check"></i> Facturar
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">
                                        No hay otros clientes para mostrar
                                    </td>
                                </tr>
                            @endforelse

                            @if(count($otrosClientes) > 0)
                                <tr class="table-info">
                                    <td colspan="2"><strong>Total General (Otros clientes)</strong></td>
                                    <td><strong>${{ number_format($totalGeneral, 2) }}</strong></td>
                                    <td colspan="2"></td>
                                </tr>
                            @endif

                            {{-- Resumen de proveedores --}}
                            @php
                                $motosProveedor = $viaje->motosTransportadas->where('proveedor_paga', true);
                            @endphp

                            @if($motosProveedor->count() > 0)
                                <tr class="table-secondary">
                                    <td colspan="5" class="text-center">
                                        <strong>Transportes pagados por proveedor</strong>
                                    </td>
                                </tr>
                                @foreach($motosProveedor->groupBy('proveedor_codprov') as $codprov => $grupo)
                                    @php
                                        $proveedor = $grupo->first()->proveedor;
                                    @endphp
                                    <tr>
                                        <td><small>Proveedor: {{ $proveedor->descrip ?? 'N/A' }}</small></td>
                                        <td><small>{{ $grupo->sum('cantidad') }} motos</small></td>
                                        <td><small>${{ number_format($grupo->sum('monto_transporte_proveedor'), 2) }}</small></td>
                                        <td colspan="2">
                                            <small>Ret: ${{ number_format($grupo->sum('retencion_proveedor'), 2) }}</small>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </table>
                    @endif
                </div>
            @else
                <p class="text-muted text-center py-3">
                    <i class="bi bi-info-circle me-2"></i>
                    No hay motos registradas en este viaje. Agrega una usando el formulario de arriba.
                </p>
            @endif
        </div>
    </div>
</div>

<input type="hidden" id="viajetalmotos" value="{{ $viaje->id }}">
