{{-- resources/views/reportes/gastos/index.blade.php --}}
@extends('layouts.master')

@section('title', 'Reporte de Gastos')

@section('css')
    <style>
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

        .chart-container {
            background: white;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .filter-section {
            background: white;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .badge-tipo {
            font-size: 0.85rem;
            padding: 0.5rem 0.8rem;
        }

        .table-gastos {
            font-size: 0.9rem;
        }

        .table-gastos td, .table-gastos th {
            padding: 0.5rem;
        }

        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255,255,255,0.8);
            z-index: 9999;
            display: none;
            justify-content: center;
            align-items: center;
        }

        .loading-overlay.active {
            display: flex;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col-12">
                <h1 class="h2 mb-0">
                    <i class="bi bi-pie-chart me-2 text-primary"></i>Reporte de Gastos
                </h1>
                <p class="text-muted">Análisis detallado de todos los gastos del negocio</p>
            </div>
        </div>

        {{-- Filtros --}}
        <div class="filter-section">
            <form id="filtrosForm" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Fecha Inicio</label>
                    <input type="date" class="form-control" name="fecha_inicio" id="fecha_inicio"
                           value="{{ now()->startOfMonth()->format('Y-m-d') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Fecha Fin</label>
                    <input type="date" class="form-control" name="fecha_fin" id="fecha_fin"
                           value="{{ now()->format('Y-m-d') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Tipo de Gasto</label>
                    <select class="form-select" name="tipo_gasto_id" id="tipo_gasto_id">
                        <option value="">Todos los tipos</option>
                        @foreach($tiposGasto as $tipo)
                            <option value="{{ $tipo->id }}">{{ $tipo->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Agrupar por</label>
                    <select class="form-select" name="agrupar_por" id="agrupar_por">
                        <option value="tipo">Tipo de Gasto</option>
                        <option value="dia">Día</option>
                        <option value="mes">Mes</option>
                        <option value="viaje">Viaje</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Camión</label>
                    <select class="form-select" name="camion_id" id="camion_id">
                        <option value="">Todos los camiones</option>
                        @foreach($camiones as $camion)
                            <option value="{{ $camion->id }}">{{ $camion->placa }} - {{ $camion->marca }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Chofer</label>
                    <select class="form-select" name="chofer_id" id="chofer_id">
                        <option value="">Todos los choferes</option>
                        @foreach($choferes as $chofer)
                            <option value="{{ $chofer->id }}">{{ $chofer->nombre_completo }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="button" class="btn btn-primary w-100" onclick="cargarDatos()">
                        <i class="bi bi-search me-2"></i>Generar Reporte
                    </button>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="button" class="btn btn-success w-100" onclick="exportarExcel()">
                        <i class="bi bi-file-excel me-2"></i>Exportar Excel
                    </button>
                </div>
            </form>
        </div>

        {{-- Resumen estadístico --}}
        <div class="row mb-4" id="resumenCards">
            <div class="col-md-3">
                <div class="stat-card-sm">
                    <h6 class="text-muted mb-2">Total Gastos</h6>
                    <h3 class="mb-0" id="totalMonto">$0.00</h3>
                    <small class="text-muted" id="totalCantidad">0 gastos</small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card-sm">
                    <h6 class="text-muted mb-2">Promedio por Gasto</h6>
                    <h3 class="mb-0" id="promedioMonto">$0.00</h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card-sm">
                    <h6 class="text-muted mb-2">Gasto Máximo</h6>
                    <h3 class="mb-0" id="maximoMonto">$0.00</h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card-sm">
                    <h6 class="text-muted mb-2">Gasto Mínimo</h6>
                    <h3 class="mb-0" id="minimoMonto">$0.00</h3>
                </div>
            </div>
        </div>

        {{-- Gráficos --}}
        <div class="row">
            <div class="col-md-6">
                <div class="chart-container">
                    <h5 class="mb-3"><i class="bi bi-pie-chart me-2 text-primary"></i>Distribución por Tipo</h5>
                    <canvas id="chartTipo" height="300"></canvas>
                </div>
            </div>
            <div class="col-md-6">
                <div class="chart-container">
                    <h5 class="mb-3"><i class="bi bi-bar-chart-line me-2 text-primary"></i>Tendencia por Día</h5>
                    <canvas id="chartTendencia" height="300"></canvas>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-6">
                <div class="chart-container">
                    <h5 class="mb-3"><i class="bi bi-bar-chart-steps me-2 text-primary"></i>Top 10 Tipos de Gasto</h5>
                    <canvas id="chartTopTipos" height="300"></canvas>
                </div>
            </div>
            <div class="col-md-6">
                <div class="chart-container">
                    <h5 class="mb-3"><i class="bi bi-truck me-2 text-primary"></i>Gastos por Viaje (Top 10)</h5>
                    <canvas id="chartViajes" height="300"></canvas>
                </div>
            </div>
        </div>

        {{-- Tabla de gastos --}}
        <div class="row mt-4">
            <div class="col-12">
                <div class="chart-container">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0"><i class="bi bi-list me-2 text-primary"></i>Top 10 Gastos más Grandes</h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover table-gastos" id="tablaTopGastos">
                            <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Tipo</th>
                                <th>Concepto</th>
                                <th>Monto</th>
                                <th>Entidad</th>
                                <th>Proveedor</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr><td colspan="6" class="text-center">Cargando datos...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Loading Overlay --}}
    <div class="loading-overlay" id="loadingOverlay">
        <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
            <span class="visually-hidden">Cargando...</span>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        let charts = {};

        document.addEventListener('DOMContentLoaded', function() {
            cargarDatos();
        });

        function cargarDatos() {
            mostrarLoading(true);

            const form = document.getElementById('filtrosForm');
            const formData = new FormData(form);
            const params = new URLSearchParams(formData).toString();

            fetch(`/reportesgastos/datos?${params}`)
                .then(response => response.json())
                .then(data => {
                    console.log('📦 Datos recibidos:', data); // <-- Agrega esto para debug
                    if (data.success) {
                        actualizarResumen(data.resumen);
                        actualizarGraficos(data);
                        actualizarTablaTop(data.top_gastos);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al cargar los datos');
                })
                .finally(() => {
                    mostrarLoading(false);
                });
        }

        function actualizarResumen(resumen) {
            document.getElementById('totalMonto').textContent = formatMoney(resumen.monto_total);
            document.getElementById('totalCantidad').textContent = resumen.total_gastos + ' gastos';
            document.getElementById('promedioMonto').textContent = formatMoney(resumen.monto_promedio);
            document.getElementById('maximoMonto').textContent = formatMoney(resumen.monto_maximo);
            document.getElementById('minimoMonto').textContent = formatMoney(resumen.monto_minimo);
        }

        function actualizarGraficos(data) {
            // Destruir gráficos anteriores
            Object.values(charts).forEach(chart => chart.destroy());
            charts = {};

            // Gráfico de distribución por tipo (Pie)
            const ctxTipo = document.getElementById('chartTipo').getContext('2d');
            const tipos = Object.keys(data.por_tipo);
            const valores = Object.values(data.por_tipo).map(v => v.total);

            charts.tipo = new Chart(ctxTipo, {
                type: 'pie',
                data: {
                    labels: tipos,
                    datasets: [{
                        data: valores,
                        backgroundColor: generarColores(tipos.length)
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { position: 'bottom' },
                        tooltip: {
                            callbacks: {
                                label: (context) => {
                                    const label = context.label || '';
                                    const value = context.raw;
                                    const porcentaje = data.por_tipo[context.label].porcentaje;
                                    return `${label}: ${formatMoney(value)} (${porcentaje}%)`;
                                }
                            }
                        }
                    }
                }
            });

            // Gráfico de tendencia por día (Línea)
            const ctxTendencia = document.getElementById('chartTendencia').getContext('2d');
            const dias = Object.values(data.por_dia).map(d => d.fecha_formateada);
            const valoresDia = Object.values(data.por_dia).map(d => d.total);

            charts.tendencia = new Chart(ctxTendencia, {
                type: 'line',
                data: {
                    labels: dias,
                    datasets: [{
                        label: 'Monto',
                        data: valoresDia,
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        tension: 0.3,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: (context) => `Monto: ${formatMoney(context.raw)}`
                            }
                        }
                    }
                }
            });

            // Gráfico top tipos (Barra horizontal)
            const ctxTop = document.getElementById('chartTopTipos').getContext('2d');
            const topTipos = Object.keys(data.por_tipo).slice(0, 10);
            const topValores = Object.values(data.por_tipo).slice(0, 10).map(v => v.total);

            charts.topTipos = new Chart(ctxTop, {
                type: 'bar',
                data: {
                    labels: topTipos,
                    datasets: [{
                        label: 'Monto',
                        data: topValores,
                        backgroundColor: '#10b981'
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: (context) => `Monto: ${formatMoney(context.raw)}`
                            }
                        }
                    }
                }
            });

            // Gráfico por viajes (Barra)
            if (data.por_viaje && Object.keys(data.por_viaje).length > 0) {
                const ctxViajes = document.getElementById('chartViajes').getContext('2d');
                const viajes = Object.values(data.por_viaje).map(v => v.folio || 'N/A');
                const valoresViaje = Object.values(data.por_viaje).map(v => v.total);

                charts.viajes = new Chart(ctxViajes, {
                    type: 'bar',
                    data: {
                        labels: viajes,
                        datasets: [{
                            label: 'Gastos por Viaje',
                            data: valoresViaje,
                            backgroundColor: '#f59e0b'
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            tooltip: {
                                callbacks: {
                                    label: (context) => {
                                        const viaje = Object.values(data.por_viaje)[context.dataIndex];
                                        return [
                                            `Monto: ${formatMoney(context.raw)}`,
                                            `Cantidad: ${viaje.cantidad} gastos`
                                        ];
                                    }
                                }
                            }
                        }
                    }
                });
            }
        }

        function actualizarTablaTop(topGastos) {
            const tbody = document.querySelector('#tablaTopGastos tbody');
            if (!tbody) return;

            // Limpiar la tabla
            tbody.innerHTML = '';

            // Validar que topGastos existe y es un array
            if (!topGastos) {
                console.warn('topGastos no está definido');
                tbody.innerHTML = '<tr><td colspan="6" class="text-center">No hay datos disponibles</td></tr>';
                return;
            }

            // Si no es un array, mostrar error
            if (!Array.isArray(topGastos)) {
                console.error('topGastos no es un array:', topGastos);
                tbody.innerHTML = '<tr><td colspan="6" class="text-center">Error en el formato de datos</td></tr>';
                return;
            }

            // Si el array está vacío
            if (topGastos.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" class="text-center">No hay gastos para mostrar</td></tr>';
                return;
            }

            // Si hay datos, mostrarlos
            topGastos.forEach(gasto => {
                // Validar que cada gasto tenga las propiedades necesarias
                const fecha = gasto.fecha || 'N/A';
                const tipo = gasto.tipo || 'N/A';
                const concepto = gasto.concepto || 'N/A';
                const monto = gasto.monto || 0;
                const entidad = gasto.entidad || 'N/A';
                const proveedor = gasto.proveedor || 'N/A';

                const row = `
            <tr>
                <td>${fecha}</td>
                <td><span class="badge bg-primary">${tipo}</span></td>
                <td>${concepto}</td>
                <td><strong>${formatMoney(monto)}</strong></td>
                <td>${entidad}</td>
                <td>${proveedor}</td>
            </tr>
        `;
                tbody.insertAdjacentHTML('beforeend', row);
            });
        }

        function formatMoney(value) {
            return '$' + parseFloat(value || 0).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
        }

        function generarColores(cantidad) {
            const colores = [
                '#3b82f6', '#ef4444', '#10b981', '#f59e0b', '#8b5cf6',
                '#ec4899', '#14b8a6', '#f97316', '#6b7280', '#84cc16'
            ];
            return colores.slice(0, cantidad);
        }

        function mostrarLoading(mostrar) {
            const overlay = document.getElementById('loadingOverlay');
            if (mostrar) {
                overlay.classList.add('active');
            } else {
                overlay.classList.remove('active');
            }
        }

        function exportarExcel() {
            const form = document.getElementById('filtrosForm');
            const formData = new FormData(form);
            const params = new URLSearchParams(formData).toString();

            window.location.href = `/reportesgastos/exportar/excel?${params}`;
        }
    </script>
@endsection
