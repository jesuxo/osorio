{{-- resources/views/cxctransporte/index.blade.php --}}
@extends('layouts.master')

@section('title', 'Cuentas por Cobrar - Transporte')

@section('css')
    <style>

        .table-success {
            /* Mantén todas las variables existentes */
            --tb-table-hover-bg: #e3f2fd !important; /* Cambia black por el color que prefieras */
            --tb-table-hover-color: #000 !important; /* Ajusta el color del texto si es necesario */
        }
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 10px;
            padding: 1.5rem;
            color: white;
            margin-bottom: 1rem;
        }

        .stat-card-sm {
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

        .cliente-resumen {
            background: #f8f9fa;
            border-left: 4px solid #3b82f6;
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .cliente-resumen:hover {
            transform: translateX(5px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .cliente-resumen.pendiente {
            border-left-color: #f59e0b;
        }

        .table-cxc {
            font-size: 0.9rem;
        }

        .badge-facturado {
            background-color: #10b981;
            color: white;
        }

        .badge-pendiente {
            background-color: #f59e0b;
            color: white;
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
        {{-- Header --}}
        <div class="row mb-4">
            <div class="col-12">
                <h1 class="h2 mb-0">
                    <i class="bi bi-cash-stack me-2 text-primary"></i>Cuentas por Cobrar - Transporte
                </h1>
                <p class="text-muted">Gestión de facturación de motos transportadas por cliente</p>
            </div>
        </div>

        {{-- Estadísticas rápidas --}}
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="stat-card-sm">
                    <h6 class="text-muted mb-2">Total Pendiente</h6>
                    <h3 class="mb-0 text-warning">${{ number_format($totales['pendiente'], 2) }}</h3>
                    <small class="text-muted">{{ $totales['total_motos_pendientes'] }} motos</small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card-sm">
                    <h6 class="text-muted mb-2">Total Facturado</h6>
                    <h3 class="mb-0 text-success">${{ number_format($totales['facturado'], 2) }}</h3>
                    <small class="text-muted">{{ $totales['total_motos_facturadas'] }} motos</small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card-sm">
                    <h6 class="text-muted mb-2">Clientes con Deuda</h6>
                    <h3 class="mb-0 text-primary">{{ $resumenClientes->count() }}</h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card-sm">
                    <h6 class="text-muted mb-2">Total Registros</h6>
                    <h3 class="mb-0">{{ $motosPendientes->total() }}</h3>
                </div>
            </div>
        </div>

        {{-- Filtros --}}
        <div class="filter-section">
            <form method="GET" action="{{ route('cxctransporte.index') }}" id="filtrosForm">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Cliente</label>
                        <select class="form-select" name="cliente">
                            <option value="todos" {{ $clienteId == 'todos' ? 'selected' : '' }}>Todos los clientes</option>
                            @foreach($clientes as $cliente)
                                <option value="{{ $cliente->codclie }}" {{ $clienteId == $cliente->codclie ? 'selected' : '' }}>
                                    {{ $cliente->descrip }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Estado</label>
                        <select class="form-select" name="estado">
                            <option value="pendientes" {{ $estado == 'pendientes' ? 'selected' : '' }}>Pendientes</option>
                            <option value="facturadas" {{ $estado == 'facturadas' ? 'selected' : '' }}>Facturadas</option>
                            <option value="todas" {{ $estado == 'todas' ? 'selected' : '' }}>Todas</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Fecha Desde</label>
                        <input type="date" class="form-control" name="fecha_inicio" value="{{ $fechaInicio }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Fecha Hasta</label>
                        <input type="date" class="form-control" name="fecha_fin" value="{{ $fechaFin }}">
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-search me-2"></i>Aplicar Filtros
                        </button>
                    </div>
                </div>
            </form>
        </div>

        {{-- Resumen por cliente -- solo los que no son V15184480 --}}
        @if($resumenClientes->count() > 0 && $estado == 'pendientes')
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h6 class="mb-0 text-white"><i class="bi bi-people me-2"></i>Resumen por Cliente - Deudas Pendientes</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @foreach($resumenClientes as $resumen)
                                    @if($resumen['codclie'] !== 'V15184480')
                                        <div class="col-md-4 mb-3">
                                            <div class="cliente-resumen pendiente" data-cliente="{{ $resumen['codclie'] }}" onclick="filtrarCliente('{{ $resumen['codclie'] }}')">
                                                <div class="d-flex justify-content-between">
                                                    <h6 class="mb-1">{{ $resumen['cliente'] }}</h6>
                                                    <span class="badge bg-warning">{{ $resumen['total_motos'] }} motos</span>
                                                </div>
                                                <div class="d-flex justify-content-between align-items-center mt-2">
                                                    <span class="text-muted">Total a pagar:</span>
                                                    <h5 class="text-warning mb-0">${{ number_format($resumen['total_pagar'], 2) }}</h5>
                                                </div>
                                                <div class="mt-2 d-flex flex-wrap gap-1">
                                                    <button class="btn btn-sm btn-primary" onclick="event.stopPropagation(); facturarCliente('{{ $resumen['codclie'] }}')">
                                                        <i class="bi bi-file-check me-1"></i>Facturar todo
                                                    </button>
                                                    <button class="btn btn-sm btn-info" onclick="event.stopPropagation(); verDetalleCliente('{{ $resumen['codclie'] }}')">
                                                        <i class="bi bi-eye me-1"></i>Ver detalle
                                                    </button>
                                                    {{-- BOTÓN DE HISTORIAL --}}
                                                    <button class="btn btn-sm btn-secondary" onclick="event.stopPropagation(); verHistorialCliente('{{ $resumen['codclie'] }}')">
                                                        <i class="bi bi-clock-history me-1"></i>Historial
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Tabla de motos pendientes --}}
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h6 class="mb-0  text-white"><i class="bi bi-list me-2"></i>Detalle de Motos</h6>
                        <span class="badge bg-light text-dark">{{ $motosPendientes->total() }} registros</span>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-cxc" id="tablaMotos">
                                <thead>
                                <tr>
                                    <th>Cliente</th>
                                    <th>Viaje #</th>
                                    <th>Ruta</th>
                                    <th>Fecha</th>
                                    <th>Modelo</th>
                                    <th>Cantidad</th>
                                    <th>Precio Unit.</th>
                                    <th>Total</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($motosPendientes as $moto)
                                    @if($moto->cliente_codclie === 'V15184480')
                                        @continue
                                    @endif
                                    <tr data-moto-id="{{ $moto->id }}" class="{{ $moto->facturado ? 'table-success' : '' }}">
                                        <td>
                                            <strong>{{ $moto->cliente->descrip ?? 'Sin cliente' }}</strong>
                                            <br><small class="text-muted">{{ $moto->cliente_codclie }}</small>
                                        </td>
                                        <td>
                                            <a href="#" onclick="verViaje({{ $moto->viaje_id }})">
                                                {{ $moto->viaje->folio ?? $moto->viaje_id }}
                                            </a>
                                        </td>
                                        <td>{{ $moto->viaje->origen }} → {{ $moto->viaje->destino }}</td>
                                        <td>{{ $moto->viaje->fecha_inicio->format('d/m/Y') }}</td>
                                        <td>{{ $moto->modelo_moto }}</td>
                                        <td class="text-center">{{ $moto->cantidad }}</td>
                                        <td>${{ number_format($moto->precio_por_moto, 2) }}</td>
                                        <td><strong>${{ number_format($moto->cantidad * $moto->precio_por_moto, 2) }}</strong></td>
                                        <td>
                                            @if($moto->facturado)
                                                <span class="badge badge-facturado">Facturado</span>
                                                <br><small>{{ $moto->fecha_facturacion?->format('d/m/Y') }}</small>
                                            @else
                                                <span class="badge badge-pendiente">Pendiente</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if(!$moto->facturado)
                                                <button class="btn btn-sm btn-success" onclick="facturarMoto({{ $moto->id }})" title="Facturar">
                                                    <i class="bi bi-file-check"></i>
                                                </button>
                                            @else
                                                <button class="btn btn-sm btn-warning" onclick="revertirFactura({{ $moto->id }})" title="Revertir">
                                                    <i class="bi bi-arrow-counterclockwise"></i>
                                                </button>
                                            @endif
                                            <button class="btn btn-sm btn-info" onclick="verViaje({{ $moto->viaje_id }})" title="Ver viaje">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center py-4">
                                            <i class="bi bi-inbox fs-1 d-block mb-3 text-muted"></i>
                                            <p class="text-muted">No hay registros para mostrar</p>
                                        </td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- Paginación --}}
                        <div class="d-flex justify-content-end mt-3">
                            {{ $motosPendientes->withQueryString()->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal de detalle de cliente --}}
    <div class="modal fade" id="modalDetalleCliente" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title">
                        <i class="bi bi-person me-2"></i>
                        <span id="modalClienteNombre">Detalle del Cliente</span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="modalDetalleBody">
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                    </div>
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
        const modalDetalleCliente = new bootstrap.Modal(document.getElementById('modalDetalleCliente'));

        function filtrarCliente(codclie) {
            const url = new URL(window.location.href);
            url.searchParams.set('cliente', codclie);
            url.searchParams.set('estado', 'pendientes');
            window.location.href = url.toString();
        }

        function facturarMoto(id) {
            if (!confirm('¿Marcar esta moto como facturada?')) return;

            mostrarLoading(true);

            fetch(`/cxctransporte/facturar-moto/${id}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        mostrarToast('Moto facturada correctamente', 'Éxito', 'success');

                        // Actualizar la fila visualmente
                        const row = document.querySelector(`tr[data-moto-id="${id}"]`);
                        if (row) {
                            row.classList.add('table-success');

                            const estadoCell = row.querySelector('td:nth-child(9)');
                            if (estadoCell) {
                                estadoCell.innerHTML = '<span class="badge badge-facturado">Facturado</span>';
                            }

                            const accionesCell = row.querySelector('td:nth-child(10)');
                            if (accionesCell) {
                                const facturarBtn = accionesCell.querySelector('button[onclick*="facturarMoto"]');
                                if (facturarBtn) {
                                    facturarBtn.outerHTML = `
                            <button class="btn btn-sm btn-warning" onclick="revertirFactura(${id})" title="Revertir">
                                <i class="bi bi-arrow-counterclockwise"></i>
                            </button>
                        `;
                                }
                            }
                        }

                        // Actualizar totales y resumen
                        actualizarTotales();
                        actualizarResumenClientes();

                    } else {
                        mostrarToast(data.message, 'Error', 'danger');
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

        function actualizarTotales() {
            // Recalcular total pendiente
            let totalPendiente = 0;
            let totalMotosPendientes = 0;
            let totalFacturado = 0;
            let totalMotosFacturadas = 0;

            document.querySelectorAll('#tablaMotos tbody tr').forEach(row => {
                const cantidad = parseInt(row.querySelector('td:nth-child(6)')?.textContent) || 0;
                const precioText = row.querySelector('td:nth-child(7)')?.textContent.replace('$', '').replace(',', '') || '0';
                const precio = parseFloat(precioText);
                const total = cantidad * precio;
                const estaFacturado = row.classList.contains('table-success');

                if (estaFacturado) {
                    totalFacturado += total;
                    totalMotosFacturadas += cantidad;
                } else {
                    totalPendiente += total;
                    totalMotosPendientes += cantidad;
                }
            });

            // Actualizar las cards de estadísticas
            const pendienteCard = document.querySelector('.stat-card-sm h3.text-warning');
            if (pendienteCard) {
                pendienteCard.textContent = '$' + totalPendiente.toFixed(2);
            }

            const pendienteSmall = document.querySelector('.stat-card-sm small.text-muted');
            if (pendienteSmall) {
                pendienteSmall.textContent = totalMotosPendientes + ' motos';
            }

            const facturadoCard = document.querySelector('.stat-card-sm h3.text-success');
            if (facturadoCard) {
                facturadoCard.textContent = '$' + totalFacturado.toFixed(2);
            }

            const facturadoSmall = document.querySelectorAll('.stat-card-sm small.text-muted')[1];
            if (facturadoSmall) {
                facturadoSmall.textContent = totalMotosFacturadas + ' motos';
            }
        }

        // Función para actualizar el resumen por cliente
        function actualizarResumenClientes() {
            const resumenContainer = document.querySelector('.row.mb-4 .row'); // El contenedor de las tarjetas de clientes
            if (!resumenContainer) return;

            // Recopilar datos actualizados de la tabla
            const clientesMap = new Map();

            document.querySelectorAll('#tablaMotos tbody tr').forEach(row => {
                const clienteCodigo = row.querySelector('td:first-child small')?.textContent;
                const clienteNombre = row.querySelector('td:first-child strong')?.textContent;
                const cantidad = parseInt(row.querySelector('td:nth-child(6)')?.textContent) || 0;
                const precioText = row.querySelector('td:nth-child(7)')?.textContent.replace('$', '').replace(',', '') || '0';
                const precio = parseFloat(precioText);
                const total = cantidad * precio;
                const estaFacturado = row.classList.contains('table-success');

                if (!clienteCodigo || estaFacturado) return; // No incluir facturados

                if (!clientesMap.has(clienteCodigo)) {
                    clientesMap.set(clienteCodigo, {
                        codclie: clienteCodigo,
                        nombre: clienteNombre,
                        total_motos: 0,
                        total_pagar: 0
                    });
                }

                const cliente = clientesMap.get(clienteCodigo);
                cliente.total_motos += cantidad;
                cliente.total_pagar += total;
            });

            // Convertir Map a array y ordenar
            const clientes = Array.from(clientesMap.values())
                .filter(c => c.codclie !== 'V15184480')
                .sort((a, b) => b.total_pagar - a.total_pagar);

            // Generar nuevo HTML para el resumen
            let htmlResumen = '';

            if (clientes.length === 0) {
                htmlResumen = `
            <div class="col-12">
                <div class="alert alert-success text-center">
                    <i class="bi bi-check-circle me-2"></i>
                    No hay deudas pendientes
                </div>
            </div>
        `;
            } else {
                clientes.forEach(cliente => {
                    htmlResumen += `
                <div class="col-md-4 mb-3">
                    <div class="cliente-resumen pendiente" data-cliente="${cliente.codclie}" onclick="filtrarCliente('${cliente.codclie}')">
                        <div class="d-flex justify-content-between">
                            <h6 class="mb-1">${cliente.nombre}</h6>
                            <span class="badge bg-warning">${cliente.total_motos} motos</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <span class="text-muted">Total a pagar:</span>
                            <h5 class="text-warning mb-0">$${cliente.total_pagar.toFixed(2)}</h5>
                        </div>
                        <div class="mt-2">
                            <button class="btn btn-sm btn-primary" onclick="event.stopPropagation(); facturarCliente('${cliente.codclie}')">
                                <i class="bi bi-file-check me-1"></i>Facturar todo
                            </button>
                            <button class="btn btn-sm btn-info" onclick="event.stopPropagation(); verDetalleCliente('${cliente.codclie}')">
                                <i class="bi bi-eye me-1"></i>Ver detalle
                            </button>
                        </div>
                    </div>
                </div>
            `;
                });
            }

            // Reemplazar el contenido
            resumenContainer.innerHTML = htmlResumen;
        }

        function facturarCliente(codclie) {
            if (!confirm('¿Facturar todas las motos pendientes de este cliente?')) return;

            mostrarLoading(true);

            fetch(`/cxctransporte/facturar-cliente/${codclie}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        mostrarToast(data.message, 'Éxito', 'success');

                        // Actualizar todas las filas de este cliente
                        document.querySelectorAll('#tablaMotos tbody tr').forEach(row => {
                            const clienteCodigo = row.querySelector('td:first-child small')?.textContent;
                            if (clienteCodigo && clienteCodigo.includes(codclie)) {
                                row.classList.add('table-success');

                                const estadoCell = row.querySelector('td:nth-child(9)');
                                if (estadoCell) {
                                    estadoCell.innerHTML = '<span class="badge badge-facturado">Facturado</span>';
                                }

                                const accionesCell = row.querySelector('td:nth-child(10)');
                                if (accionesCell) {
                                    const facturarBtn = accionesCell.querySelector('button[onclick*="facturarMoto"]');
                                    if (facturarBtn) {
                                        const motoId = row.getAttribute('data-moto-id');
                                        facturarBtn.outerHTML = `
                                <button class="btn btn-sm btn-warning" onclick="revertirFactura(${motoId})" title="Revertir">
                                    <i class="bi bi-arrow-counterclockwise"></i>
                                </button>
                            `;
                                    }
                                }
                            }
                        });

                        // Actualizar totales y resumen
                        actualizarTotales();
                        actualizarResumenClientes();

                    } else {
                        mostrarToast(data.message, 'Error', 'danger');
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

        function revertirFactura(id) {
            if (!confirm('¿Revertir la facturación de esta moto?')) return;

            mostrarLoading(true);

            fetch(`/cxctransporte/revertir-factura/${id}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        mostrarToast('Factura revertida correctamente', 'Éxito', 'success');

                        const row = document.querySelector(`tr[data-moto-id="${id}"]`);
                        if (row) {
                            row.classList.remove('table-success');

                            const estadoCell = row.querySelector('td:nth-child(9)');
                            if (estadoCell) {
                                estadoCell.innerHTML = '<span class="badge badge-pendiente">Pendiente</span>';
                            }

                            const accionesCell = row.querySelector('td:nth-child(10)');
                            if (accionesCell) {
                                const revertirBtn = accionesCell.querySelector('button[onclick*="revertirFactura"]');
                                if (revertirBtn) {
                                    revertirBtn.outerHTML = `
                            <button class="btn btn-sm btn-success" onclick="facturarMoto(${id})" title="Facturar">
                                <i class="bi bi-file-check"></i>
                            </button>
                        `;
                                }
                            }
                        }

                        // Actualizar totales y resumen
                        actualizarTotales();
                        actualizarResumenClientes();

                    } else {
                        mostrarToast(data.message, 'Error', 'danger');
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

        function verDetalleCliente(codclie) {
            modalDetalleCliente.show();

            // Agregar botón de historial en el header del modal
            const modalHeader = document.querySelector('#modalDetalleCliente .modal-header');
            if (modalHeader) {
                const historialBtn = document.createElement('a');
                historialBtn.href = `/cxctransporte/cliente/${codclie}/historial`;
                historialBtn.className = 'btn btn-sm btn-light ms-2';
                historialBtn.innerHTML = '<i class="bi bi-clock-history me-1"></i>Ver historial completo';
                modalHeader.querySelector('.modal-title').after(historialBtn);
            }

            fetch(`/cxctransporte/cliente/${codclie}/resumen`)
                .then(response => response.json())
                .then(data => {
                    let html = `
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="card bg-warning text-white">
                            <div class="card-body">
                                <h6>Pendiente</h6>
                                <h3>$${data.pendientes.total.toFixed(2)}</h3>
                                <small>${data.pendientes.cantidad} motos</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <h6>Facturado</h6>
                                <h3>$${data.facturadas.total.toFixed(2)}</h3>
                                <small>${data.facturadas.cantidad} motos</small>
                            </div>
                        </div>
                    </div>
                </div>
                <h6 class="mb-3">Detalle de Viajes</h6>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Viaje</th>
                                <th>Fecha</th>
                                <th>Modelo</th>
                                <th>Cant.</th>
                                <th>Total</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
            `;

                    data.detalle.forEach(item => {
                        html += `
                    <tr class="${item.facturado ? 'table-success' : ''}">
                        <td>${item.viaje_folio}</td>
                        <td>${item.fecha}</td>
                        <td>${item.modelo}</td>
                        <td>${item.cantidad}</td>
                        <td>$${item.total.toFixed(2)}</td>
                        <td>
                            ${item.facturado ?
                            '<span class="badge bg-success">Facturado</span>' :
                            '<span class="badge bg-warning">Pendiente</span>'
                        }
                        </td>
                    </tr>
                `;
                    });

                    html += '</tbody></table></div>';

                    document.getElementById('modalDetalleBody').innerHTML = html;
                    document.getElementById('modalClienteNombre').textContent = 'Detalle del Cliente';
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('modalDetalleBody').innerHTML = `
                <div class="alert alert-danger">
                    Error al cargar el detalle del cliente
                </div>
            `;
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

        function mostrarToast(mensaje, titulo = 'Notificación', tipo = 'info') {
            // Verificar que los elementos existan
            const toastTitle = document.getElementById('toastTitle');
            const toastMessage = document.getElementById('toastMessage');
            const toastElement = document.getElementById('liveToast');

            if (!toastTitle || !toastMessage || !toastElement) {
                console.log('Toast no disponible, mostrando alert:', mensaje);
                alert(mensaje);
                return;
            }

            // Actualizar contenido
            toastTitle.innerText = titulo;
            toastMessage.innerText = mensaje;

            // Remover clases anteriores
            toastElement.classList.remove('bg-success', 'bg-danger', 'bg-warning', 'bg-info');

            // Agregar clase según tipo
            toastElement.classList.add(`bg-${tipo}`, 'text-white');

            // Crear o reutilizar instancia del toast
            let toastInstance = bootstrap.Toast.getInstance(toastElement);
            if (!toastInstance) {
                toastInstance = new bootstrap.Toast(toastElement);
            }

            // Mostrar toast
            toastInstance.show();

            // Auto-cerrar después de 5 segundos
            setTimeout(() => {
                toastInstance.hide();
            }, 5000);
        }

        document.addEventListener('DOMContentLoaded', function() {
            actualizarResumenClientes(); // Para asegurar que el resumen inicial está correcto
        });

        function verHistorialCliente(codclie) {
            window.location.href = `/cxctransporte/cliente/${codclie}/historial`;
        }


        function verViaje(id) {
            window.open(`/viajes/${id}/ver`, '_blank');
        }
    </script>
@endsection
