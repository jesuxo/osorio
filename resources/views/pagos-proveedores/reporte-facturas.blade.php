{{-- resources/views/pagos-proveedores/reporte-facturas.blade.php --}}
@extends('layouts.master')
@section('title')
    Reporte de Facturas
@endsection
@section('css')
    <style>
        .stat-card {
            background: white;
            border-radius: 10px;
            padding: 1rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            transition: all 0.3s;
        }
        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .stat-number {
            font-size: 24px;
            font-weight: bold;
        }
        .stat-label {
            font-size: 12px;
            color: #6c757d;
        }
        .table-facturas {
            font-size: 14px;
        }
        .table-facturas th {
            background: #f8f9fa;
            position: sticky;
            top: 0;
            z-index: 10;
        }
        .badge-proveedor {
            font-size: 12px;
        }
        .resumen-proveedor {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 0.5rem 1rem;
            margin-bottom: 0.5rem;
        }
        .resumen-proveedor .badge {
            font-size: 12px;
        }
        .filtros-card {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 1.5rem;
        }
        .btn-exportar {
            border-radius: 20px;
            padding: 0.5rem 1.5rem;
        }
        .detalle-producto {
            font-size: 12px;
            color: #6c757d;
        }
    </style>
@endsection
@section('content')
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4><i class="bi bi-receipt me-2"></i>Reporte de Facturas</h4>
            <div>
                <button class="btn btn-success btn-exportar me-2" onclick="exportarExcel()">
                    <i class="bi bi-file-excel me-1"></i> Exportar Excel
                </button>
                <button class="btn btn-info btn-exportar" onclick="window.print()">
                    <i class="bi bi-printer me-1"></i> Imprimir
                </button>
            </div>
        </div>

        {{-- Filtros --}}
        <div class="filtros-card">
            <form id="filtrosForm" method="GET" action="{{ route('reporte-facturas') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label fw-bold">Fecha Desde</label>
                    <input type="date" class="form-control" name="fecha_desde" value="{{ $fecha_desde }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Fecha Hasta</label>
                    <input type="date" class="form-control" name="fecha_hasta" value="{{ $fecha_hasta }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Proveedor</label>
                    <select class="form-select" name="proveedor">
                        <option value="">Todos</option>
                        @foreach($proveedores as $prov)
                            <option value="{{ $prov->codprov }}" {{ ($proveedor ?? '') == $prov->codprov ? 'selected' : '' }}>
                                {{ $prov->descrip }} ({{ $prov->codprov }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold">N° Factura</label>
                    <input type="text" class="form-control" name="numero_factura" value="{{ $numero_factura ?? '' }}" placeholder="Buscar...">
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </form>
        </div>

        {{-- Estadísticas --}}
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-number text-primary">{{ number_format($estadisticas['total_facturas'], 0) }}</div>
                            <div class="stat-label">Total Facturas</div>
                        </div>
                        <i class="bi bi-receipt text-primary" style="font-size: 32px;"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-number text-success">{{ number_format($estadisticas['total_motos'], 0) }}</div>
                            <div class="stat-label">Total Motos Facturadas</div>
                        </div>
                        <i class="bi bi-motorbike text-success" style="font-size: 32px;"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-number text-info">${{ number_format($estadisticas['total_monto'], 2) }}</div>
                            <div class="stat-label">Total Facturado</div>
                        </div>
                        <i class="bi bi-currency-dollar text-info" style="font-size: 32px;"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-number text-warning">{{ $estadisticas['total_proveedores'] }}</div>
                            <div class="stat-label">Proveedores</div>
                        </div>
                        <i class="bi bi-building text-warning" style="font-size: 32px;"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Resumen por Proveedor --}}
        @if($porProveedor->count() > 0)
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="bi bi-building me-2"></i>Resumen por Proveedor</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($porProveedor as $nombre => $facturasGrupo)
                            <div class="col-md-3 col-sm-6 mb-2">
                                <div class="resumen-proveedor">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="fw-bold">{{ $nombre }}</span>
                                        <span class="badge bg-primary">{{ $facturasGrupo->count() }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between mt-1">
                                        <small class="text-muted">Motos: {{ $facturasGrupo->sum('cantidad_facturada') }}</small>
                                        <small class="text-success">${{ number_format($facturasGrupo->sum('monto_facturado'), 2) }}</small>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        {{-- Tabla de Facturas --}}
        <div class="card">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="bi bi-list me-2"></i>Detalle de Facturas</h6>
                <span class="badge bg-secondary">{{ $facturas->count() }} registros</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive" style="max-height: 600px; overflow-y: auto;">
                    <table class="table table-bordered table-hover table-facturas mb-0">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>N° Factura</th>
                            <th>Fecha</th>
                            <th>Proveedor</th>
                            <th>Producto</th>
                            <th class="text-center">Cantidad</th>
                            <th class="text-end">Monto Unitario</th>
                            <th class="text-end">Monto Total</th>
                            <th>Pedido</th>
                            <th>Archivo</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($facturas as $factura)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <strong>{{ $factura->numero_factura }}</strong>
                                    @if($factura->notas)
                                        <br><small class="text-muted">{{ Str::limit($factura->notas, 30) }}</small>
                                    @endif
                                </td>
                                <td>{{ $factura->fecha_factura->format('d/m/Y') }}</td>
                                <td>
                                        <span class="badge badge-proveedor bg-light text-dark">
                                            {{ $factura->pago->proveedor->descrip ?? $factura->pago->codprov ?? 'N/A' }}
                                        </span>
                                </td>
                                <td>
                                    <div>{{ $factura->pagoDetalle->producto_descrip ?? 'N/A' }}</div>
                                    <div class="detalle-producto">
                                        Código: {{ $factura->pagoDetalle->producto_codprod ?? 'N/A' }}
                                    </div>
                                </td>
                                <td class="text-center">{{ $factura->cantidad_facturada }}</td>
                                <td class="text-end">
                                    ${{ number_format($factura->monto_facturado / $factura->cantidad_facturada, 2) }}
                                </td>
                                <td class="text-end fw-bold text-success">
                                    ${{ number_format($factura->monto_facturado, 2) }}
                                </td>
                                <td>
                                    <small>
                                        {{ $factura->pago->folio ?? 'N/A' }}
                                        @if($factura->pago->numero_aprobacion)
                                            <br><span class="badge bg-info">Aprob: {{ $factura->pago->numero_aprobacion }}</span>
                                        @endif
                                    </small>
                                </td>
                                <td>
                                    @if($factura->archivo_path)
                                        <a href="/{{ $factura->archivo_path }}" target="_blank" class="btn btn-sm btn-outline-info">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-4">
                                    <i class="bi bi-inbox" style="font-size: 48px; color: #dee2e6;"></i>
                                    <p class="text-muted mt-2">No hay facturas en el rango de fechas seleccionado</p>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                        @if($facturas->count() > 0)
                            <tfoot class="table-secondary">
                            <tr>
                                <th colspan="5" class="text-end">TOTALES:</th>
                                <th class="text-center fw-bold">{{ $facturas->sum('cantidad_facturada') }}</th>
                                <th></th>
                                <th class="text-end fw-bold text-success">
                                    ${{ number_format($facturas->sum('monto_facturado'), 2) }}
                                </th>
                                <th colspan="2"></th>
                            </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        function exportarExcel() {
            const form = document.getElementById('filtrosForm');
            const action = form.action.replace('reporte-facturas', 'exportar-facturas');
            form.action = action;
            form.submit();
            // Restaurar acción original
            setTimeout(() => {
                form.action = '{{ route("reporte-facturas") }}';
            }, 100);
        }

        // Búsqueda en tiempo real (opcional)
        $('#filtrosForm input, #filtrosForm select').on('change', function() {
            if ($(this).attr('name') !== 'numero_factura') {
                $('#filtrosForm').submit();
            }
        });

        let timeoutId;
        $('input[name="numero_factura"]').on('keyup', function() {
            clearTimeout(timeoutId);
            timeoutId = setTimeout(() => {
                $('#filtrosForm').submit();
            }, 500);
        });
    </script>
@endsection
