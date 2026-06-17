
@extends('layouts.master')
@section('title')
    Gestión de Viajes
@endsection
@section('css')
    <style>
        .table-success {
            /* Mantén todas las variables existentes */
            --tb-table-hover-bg: #e3f2fd !important; /* Cambia black por el color que prefieras */
            --tb-table-hover-color: #000 !important; /* Ajusta el color del texto si es necesario */
        }
        #resultados-modelos {
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            border-radius: 0 0 4px 4px;
            background: white;
        }

        #resultados-modelos .list-group-item {
            cursor: pointer;
            border-left: none;
            border-right: none;
            transition: background-color 0.2s;
        }

        #resultados-modelos .list-group-item:hover {
            background-color: #f8f9fa;
        }

        #resultados-modelos .list-group-item:first-child {
            border-top: none;
        }

        #resultados-modelos .list-group-item:last-child {
            border-bottom: none;
        }

        .search-box {
            position: relative;
        }

        .search-indicator {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
        }

        .search-box input {
            padding-right: 40px; /* Espacio para el indicador */
        }

        /* Estilo para cuando no hay resultados */
        .no-results {
            text-align: center;
            padding: 3rem;
            color: #6c757d;
        }

        .no-results i {
            font-size: 3rem;
            margin-bottom: 1rem;
        }

        .viaje-card {
            transition: all 0.3s ease;
            border-left: 5px solid transparent;
            margin-bottom: 1rem;
            border-radius: 8px;
            overflow: hidden;
        }
        .viaje-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
        }
        .estado-planeado { border-left-color: #6c757d; }
        .estado-en_curso { border-left-color: #ffc107; }
        .estado-completado { border-left-color: #28a745; }
        .estado-cancelado { border-left-color: #dc3545; }

        .progress {
            height: 8px;
            border-radius: 4px;
        }

        .stat-card {
            background: linear-gradient(45deg, #667eea, #764ba2);
            border-radius: 10px;
            padding: 1.5rem;
            color: white;
        }

        .search-box {
            background: white;
            border-radius: 40px;
            padding: 0.5rem 1rem;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .search-box input {
            border: none;
            background: transparent;
            width: 100%;
            padding: 0.5rem;
        }

        .search-box input:focus {
            outline: none;
        }

        .filter-badge {
            background: #e9ecef;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            margin-right: 0.5rem;
            margin-bottom: 0.5rem;
            display: inline-flex;
            align-items: center;
            cursor: pointer;
            transition: all 0.2s;
        }

        .filter-badge.active {
            background: #007bff;
            color: white;
        }

        .filter-badge:hover {
            background: #dee2e6;
        }

        .action-btn {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin: 0 2px;
            transition: all 0.2s;
        }

        .action-btn:hover {
            transform: scale(1.1);
        }

        .statitemactive  {
            background: #007bff !important;
            color: white !important;
            border-radius: 3px;
        }

        .quick-stats {
            background: white;
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 1rem;
        }

        .stat-item {
            text-align: center;
            padding: 0.5rem;
        }

        .stat-value {
            font-size: 1.5rem;
            font-weight: bold;
        }

        .stat-label {
            font-size: 0.85rem;
            color: #6c757d;
        }

        .ganancia-positiva {
            color: #28a745;
            font-weight: bold;
        }

        .ganancia-negativa {
            color: #dc3545;
            font-weight: bold;
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




        .overfiltros:hover {
            color: rgb(24, 136, 216) !important;
            background-color:  white !important;
        }
    .search-box-viajes{
        display: flex;
        justify-content: center;
        align-items: center;
        background: white;
        border-radius: 40px;
        padding: 0.5rem 1rem;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }

        .search-box-viajes input {
            border: none;
            background: transparent;
            width: 100%;
            padding: 0.5rem;
        }
    </style>
@endsection
@section('content')
    <div class="container-fluid py-4">


        {{-- Estadísticas rápidas --}}
        <div class="quick-stats shadow-sm mb-4">
            <div class="row">
                <div data-estado="todos" onclick="abrirModalCrear()"
                     class="cursor-pointer col-md-1 col-12 stat-item  " style="background-color: #007bff !important; border-radius: 3px">
                    <div class="stat-value text-white">NUEVO</div>
                    <div class="stat-label text-white">Viaje</div>
                </div>
                <div data-estado="todos"    class="cursor-pointer col-md-1 col-6 stat-item  ">  </div>
                <div data-estado="todos"    class="cursor-pointer col-md-1 col-6 stat-item  ">  </div>
                <div data-estado="todos" onclick="filtrarPorEstado('todos')" class="cursor-pointer col-md-1 col-6 stat-item {{ $estado == 'todos' ? 'statitemactive' : '' }}">
                    <div class="stat-value {{ $estado == 'todos' ? 'text-white' : ' text-primary' }} ">{{ $estadisticas['total'] }}</div>
                    <div class="stat-label {{ $estado == 'todos' ? 'text-white' : '' }} ">Total Viajes</div>
                </div>
                <div data-estado="planeado" onclick="filtrarPorEstado('planeado')"  class="cursor-pointer col-md-1 col-6 stat-item {{ $estado == 'planeado' ? 'statitemactive' : '' }}">
                    <div class="stat-value {{ $estado == 'planeado' ? 'text-white' : ' text-warning' }}  ">{{ $estadisticas['planeados'] }}</div>
                    <div class="stat-label {{ $estado == 'planeado' ? 'text-white' : '  ' }}">Planeados</div>
                </div>
                <div data-estado="en_curso" onclick="filtrarPorEstado('en_curso')" class="cursor-pointer col-md-1 col-6  stat-item {{ $estado == 'en_curso' ? 'statitemactive' : '' }}">
                    <div class="stat-value  {{ $estado == 'en_curso' ? 'text-white' : ' text-info' }} ">{{ $estadisticas['en_curso'] }}</div>
                    <div class="stat-label  {{ $estado == 'en_curso' ? 'text-white' : ' ' }} ">En Curso</div>
                </div>
                <div data-estado="completado" onclick="filtrarPorEstado('completado')"  class="cursor-pointer col-md-1 col-6 stat-item {{ $estado == 'completado' ? 'statitemactive' : '' }}">
                    <div class="stat-value {{ $estado == 'completado' ? 'text-white' : ' text-success' }}">{{ $estadisticas['completados'] }}</div>
                    <div class="stat-label {{ $estado == 'completado' ? 'text-white' : '  ' }}">Completados</div>
                </div>
                <div ata-estado="cancelado" onclick="filtrarPorEstado('cancelado')" class="cursor-pointer col-md-1 col-6 stat-item {{ $estado == 'cancelado' ? 'statitemactive' : '' }}">
                    <div class="stat-value {{ $estado == 'cancelado' ? 'text-white' : ' text-danger' }}">{{ $estadisticas['cancelados'] }}</div>
                    <div class="stat-label {{ $estado == 'cancelado' ? 'text-white' : '  ' }}">Cancelados</div>
                </div>
                <div class="col-md-2 col-6 stat-item">
                    <div class="stat-value text-secondary " id="totalMotos">0</div>
                    <div class="stat-label ">Motos Transportadas</div>
                </div>
                <div class="col-md-2 col-6 stat-item btn btn-outline-secondary overfiltros" style="float:right;"  data-bs-toggle="collapse" data-bs-target="#filtrosAvanzados">
                    <div class="stat-value text-secondary " id="totalMotos">Filtros </div>
                    <div class="stat-label ">  Avanzados</div>
                </div>
            </div>
        </div>

        {{-- Barra de búsqueda y filtros --}}
        <div class="    mb-4">
            <div class=" ">
                {{-- Búsqueda rápida --}}
                <div class="row mb-3">
                    <div class="col-md-12">
                        <div class="search-box-viajes">
                            <i class="bi bi-search text-muted me-2"></i>
                            <input type="text"
                                   id="searchInput"
                                   placeholder="Buscar por folio, origen, destino, placa del camión o nombre del chofer..."
                                   value="{{ $search ?? '' }}"
                                   autocomplete="off">
                            @if($search)
                                <button class="btn btn-sm btn-link text-danger" onclick="limpiarBusqueda()">
                                    <i class="bi bi-x"></i>
                                </button>
                            @endif
                        </div>
                        <div class="search-indicator" style="display: none; position: absolute; right: 20px; top: 50%; transform: translateY(-50%);">
                            <div class="spinner-border spinner-border-sm text-primary" role="status">
                                <span class="visually-hidden">Buscando...</span>
                            </div>
                        </div>
                    </div>

                </div>



                {{-- Filtros avanzados (colapsables) --}}
                <div class="collapse {{ $fecha_desde || $fecha_hasta || $camion_id || $chofer_id ? 'show' : '' }}" id="filtrosAvanzados">
                    <div class="card card-body bg-light mt-3">
                        <form id="filtrosForm" method="GET" action="{{ route('viajes.index') }}">
                            <div class="row">
                                <div class="col-md-3 mb-2">
                                    <label class="form-label">Fecha Desde</label>
                                    <input type="date" class="form-control" onchange="$('#filtrosForm').submit()" name="fecha_desde" value="{{ $fecha_desde ?? '' }}">
                                </div>
                                <div class="col-md-3 mb-2">
                                    <label class="form-label">Fecha Hasta</label>
                                    <input type="date" class="form-control" onchange="$('#filtrosForm').submit()" name="fecha_hasta" value="{{ $fecha_hasta ?? '' }}">
                                </div>
                                <div class="col-md-3 mb-2">
                                    <label class="form-label">Camión</label>
                                    <select class="form-select" name="camion_id" onchange="$('#filtrosForm').submit()" >
                                        <option value="">Todos</option>
                                        @foreach($camiones as $camion)
                                            <option value="{{ $camion->id }}" {{ ($camion_id ?? '') == $camion->id ? 'selected' : '' }}>
                                                {{ $camion->placa }} - {{ $camion->marca }} {{ $camion->modelo }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3 mb-2">
                                    <label class="form-label">Chofer</label>
                                    <select class="form-select" name="chofer_id" onchange="$('#filtrosForm').submit()" >
                                        <option value="">Todos</option>
                                        @foreach($choferes as $chofer)
                                            <option value="{{ $chofer->id }}" {{ ($chofer_id ?? '') == $chofer->id ? 'selected' : '' }}>
                                                {{ $chofer->nombre_completo }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-filter me-2"></i>Aplicar Filtros
                                    </button>
                                    <button type="button" class="btn btn-secondary" onclick="limpiarFiltros()">
                                        <i class="mdi mdi-undo me-2"></i>Limpiar
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- Resultados de búsqueda --}}
        <div id="resultadosViajes">
            @include('viajes.partials.lista-viajes', ['viajes' => $viajes])
        </div>
    </div>

    {{-- Modal principal para gestión de viajes --}}
    <div class="modal fade" id="modalViaje" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title text-white" id="modalTitle">
                        <i class="bi bi-truck me-2"></i>
                        <span id="modalTitleText" class="text-white">Gestión de Viaje</span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white text-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="modalBody">
                    {{-- Contenido cargado vía AJAX --}}
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                        <p class="mt-2">Cargando información...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal para agregar motos --}}
    <div class="modal fade" id="modalMotos" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">
                        <i class="ri-motorbike-fill me-2"></i>
                        Agregar Motos al Viaje
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="modalMotosBody">
                    {{-- Contenido cargado vía AJAX --}}
                </div>
            </div>
        </div>
    </div>

    {{-- Modal para agregar gastos --}}
    <div class="modal fade" id="modalGasto" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title">
                        <i class="ri-bill-line  me-2"></i>
                        Registrar Gasto
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="modalGastoBody">
                    {{-- Contenido cargado vía AJAX --}}
                </div>
            </div>
        </div>
    </div>

    {{-- Modal de confirmación --}}
    <div class="modal fade" id="modalConfirmacion" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        Confirmar Acción
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="confirmacionMensaje">¿Estás seguro de realizar esta acción?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="confirmarAccionBtn">Confirmar</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Loading spinner --}}
    <div class="loading-spinner" id="loadingSpinner">
        <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
            <span class="visually-hidden">Cargando...</span>
        </div>
    </div>

    {{-- Template para notificaciones toast --}}
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
        <div id="liveToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <i class="bi bi-info-circle me-2"></i>
                <strong class="me-auto" id="toastTitle">Notificación</strong>
                <small>ahora</small>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body" id="toastMessage">
                Mensaje de notificación
            </div>
        </div>
    </div>
@endsection
@section('scripts')

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet-easybutton@2/src/easy-button.css">
    <script src="https://cdn.jsdelivr.net/npm/leaflet-easybutton@2/src/easy-button.js"></script>

    <script src="{{ URL::asset('build/js/app.js') }}"></script>
    <script>
        // Variables globales
        let viajeActualId = null;
        let accionConfirmar = null;
        const modalViaje = new bootstrap.Modal(document.getElementById('modalViaje'));
        const modalMotos = new bootstrap.Modal(document.getElementById('modalMotos'));
        const modalGasto = new bootstrap.Modal(document.getElementById('modalGasto'));
        const modalConfirmacion = new bootstrap.Modal(document.getElementById('modalConfirmacion'));
        const toast = new bootstrap.Toast(document.getElementById('liveToast'));

        // Búsqueda en tiempo real
        let timeoutId;
        let abortController = null;

        document.getElementById('searchInput').addEventListener('keyup', function(e) {
            const term = e.target.value;
            clearTimeout(timeoutId);

            timeoutId = setTimeout(() => {

                window.location.href = '{{ route("viajes.index") }}?search=' + encodeURIComponent(term);
            }, 1000);
        });

        document.addEventListener('DOMContentLoaded', function() {
            actualizarEstadisticas();
            inicializarTooltips();

        });

        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }

        function mostrarBuscando(mostrar) {
            let indicator = document.querySelector('.search-indicator');
            if (!indicator) {
                // Crear indicador si no existe
                const searchBox = document.querySelector('.search-box');
                if (searchBox) {
                    indicator = document.createElement('div');
                    indicator.className = 'search-indicator';
                    indicator.style.cssText = 'display: none; position: absolute; right: 20px; top: 50%; transform: translateY(-50%);';
                    indicator.innerHTML = '<div class="spinner-border spinner-border-sm text-primary" role="status"><span class="visually-hidden">Buscando...</span></div>';
                    searchBox.style.position = 'relative';
                    searchBox.appendChild(indicator);
                }
            }

            if (indicator) {
                indicator.style.display = mostrar ? 'block' : 'none';
            }
        }

        function buscarViajes(term) {
            // Cancelar petición anterior si existe
            if (abortController) {
                abortController.abort();
            }

            abortController = new AbortController();

            mostrarBuscando(true);
            mostrarLoading(true);

            const url = new URL(window.location.href);
            const params = new URLSearchParams(url.search);

            if (term && term.trim() !== '') {
                params.set('search', term.trim());
            } else {
                params.delete('search');
            }

            // Preservar otros filtros
            const estadoActual = document.querySelector('.filter-badge.active')?.getAttribute('data-estado');
            if (estadoActual && estadoActual !== 'todos') {
                params.set('estado', estadoActual);
            }

            const searchUrl = `${url.pathname}?${params.toString()}`;

            fetch(searchUrl, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'text/html'
                },
                signal: abortController.signal
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`Error ${response.status}: ${response.statusText}`);
                    }
                    return response.text();
                })
                .then(html => {
                    const resultadosDiv = document.getElementById('resultadosViajes');
                    if (resultadosDiv) {
                        resultadosDiv.innerHTML = html;
                        actualizarEstadisticas();
                        inicializarTooltips();
                    }
                })
                .catch(error => {
                    if (error.name === 'AbortError') {
                        console.log('Petición cancelada');
                        return;
                    }
                    console.error('Error en búsqueda:', error);
                    mostrarToast('Error al buscar viajes', 'Error', 'danger');
                })
                .finally(() => {
                    mostrarBuscando(false);
                    mostrarLoading(false);
                    abortController = null;
                });
        }


        function inicializarTooltips() {
            // Inicializar tooltips de Bootstrap
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function(tooltipTriggerEl) {
                // Destruir tooltip existente si lo hay
                const tooltip = bootstrap.Tooltip.getInstance(tooltipTriggerEl);
                if (tooltip) {
                    tooltip.dispose();
                }
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        }

        function filtrarPorEstado(estado) {
            const url = new URL(window.location.href);
            const params = new URLSearchParams(url.search);

            if (estado === 'todos') {
                params.delete('estado');
            } else {
                params.set('estado', estado);
            }

            // Mantener el término de búsqueda si existe
            const searchTerm = document.getElementById('searchInput')?.value;
            if (searchTerm && searchTerm.trim() !== '') {
                params.set('search', searchTerm.trim());
            }

            window.location.href = `${url.pathname}?${params.toString()}`;
        }

        function limpiarFiltros() {
            window.location.href = '{{ route("viajes.index") }}';
        }

        function limpiarBusqueda() {
            const searchInput = document.getElementById('searchInput');
            if (searchInput) {
                searchInput.value = '';
                window.location.href='/viajes';
            }
        }

        function abrirModalCrear() {
            document.getElementById('modalTitleText').innerText = 'Nuevo Viaje';
            document.getElementById('modalBody').innerHTML = `
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
                <p class="mt-2">Cargando formulario...</p>
            </div>
        `;
            modalViaje.show();

            fetch('{{ route("viajes.create") }}')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('modalBody').innerHTML = data.html;
                })
                .catch(error => {
                    document.getElementById('modalBody').innerHTML = `
                    <div class="alert alert-danger">
                        Error al cargar el formulario. Por favor intenta de nuevo.
                    </div>
                `;
                });
        }

        function verViaje(id) {
            document.getElementById('modalTitleText').innerText = 'Detalles del Viaje';
            document.getElementById('modalBody').innerHTML = `
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
                <p class="mt-2">Cargando información del viaje...</p>
            </div>
        `;
            modalViaje.show();

            fetch(`/viajes/${id}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('modalBody').innerHTML = data.html;
                })
                .catch(error => {
                    document.getElementById('modalBody').innerHTML = `
                    <div class="alert alert-danger">
                        Error al cargar la información del viaje.
                    </div>
                `;
                });
        }

        function editarViaje(id) {
            document.getElementById('modalTitleText').innerText = 'Editar Viaje';
            document.getElementById('modalBody').innerHTML = `
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
                <p class="mt-2">Cargando formulario de edición...</p>
            </div>
        `;
            modalViaje.show();

            fetch(`/viajes/${id}/edit`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('modalBody').innerHTML = data.html;
                })
                .catch(error => {
                    document.getElementById('modalBody').innerHTML = `
                    <div class="alert alert-danger">
                        Error al cargar el formulario de edición.
                    </div>
                `;
                });
        }

        function agregarGasto(id) {
            viajeActualId = id;
            document.getElementById('modalGastoBody').innerHTML = `
            <div class="text-center py-4">
                <div class="spinner-border text-warning" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
                <p class="mt-2">Cargando...</p>
            </div>
        `;
            modalGasto.show();

            fetch(`/viajes/${id}/gastos/form`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('modalGastoBody').innerHTML = data.html;
                })
                .catch(error => {
                    document.getElementById('modalGastoBody').innerHTML = `
                    <div class="alert alert-danger">
                        Error al cargar el formulario.
                    </div>
                `;
                });
        }

        function gestionarEtapas(id) {
            viajeActualId = id;
            document.getElementById('modalTitleText').innerText = 'Gestión de Etapas';
            document.getElementById('modalBody').innerHTML = `
        <div class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
            <p class="mt-2">Cargando etapas del viaje...</p>
        </div>
    `;
            modalViaje.show();

            // Usar la ruta que devuelve HTML
            const url = `/viajes/${id}/etapas`;

            fetch(url, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => {
                            throw new Error(err.error || 'Error en la respuesta del servidor');
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success && data.html) {
                        // Insertar el HTML en el modal
                        document.getElementById('modalBody').innerHTML = data.html;

                        // Inicializar eventos después de cargar el contenido
                        inicializarEventosEtapas();
                    } else if (data.html) {
                        // Por si la respuesta no tiene success pero sí html
                        document.getElementById('modalBody').innerHTML = data.html;
                        inicializarEventosEtapas();
                    } else {
                        // Aquí puedes construir el HTML manualmente si es necesario
                        construirHtmlEtapas(data);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('modalBody').innerHTML = `
            <div class="alert alert-danger m-3">
                <h5><i class="bi bi-exclamation-triangle me-2"></i>Error al cargar las etapas</h5>
                <p>${error.message}</p>
                <button class="btn btn-sm btn-primary mt-2" onclick="gestionarEtapas(${id})">
                    <i class="mdi mdi-sync-alert me-2"></i>Reintentar
                </button>
            </div>
        `;
                });
        }

        function construirHtmlEtapas(etapas) {
            let html = `
        <div class="container-fluid">
            <div class="row mb-3">
                <div class="col-12">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        Etapas del viaje
                    </div>
                </div>
            </div>
    `;

            etapas.forEach(etapa => {
                const colorClass = etapa.estado === 'completado' ? 'success' :
                    (etapa.estado === 'en_curso' ? 'warning' : 'secondary');

                html += `
            <div class="card mb-3 etapa-card border-${colorClass}">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-1 text-center">
                            <span class="badge bg-${colorClass} rounded-circle p-3">${etapa.id}</span>
                        </div>
                        <div class="col-md-7">
                            <h5 class="mb-1">${etapa.nombre}</h5>
                            <p class="mb-1"><i class="mdi mdi-map-marker-alert   me-1"></i> ${etapa.ubicacion}</p>
                            <p class="mb-1"><i class="ri-road-map-fill me-1"></i> ${etapa.kilometraje}</p>
                            ${etapa.fecha_inicio ? `<p class="mb-1"><small>Inicio: ${etapa.fecha_inicio}</small></p>` : ''}
                            ${etapa.fecha_fin ? `<p class="mb-1"><small>Fin: ${etapa.fecha_fin}</small></p>` : ''}
                        </div>
                        <div class="col-md-4 text-end">
                            <span class="badge bg-${colorClass} mb-2">${etapa.estado}</span>
                        </div>
                    </div>
                </div>
            </div>
        `;
            });

            html += '</div>';
            document.getElementById('modalBody').innerHTML = html;
        }

        function inicializarEventosEtapas() {
            // Inicializar tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

        }

        function seguimientoViaje(id) {
            viajeActualId = id;
            document.getElementById('modalTitleText').innerText = 'Seguimiento del Viaje';
            document.getElementById('modalBody').innerHTML = `
        <div class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
            <p class="mt-2">Cargando información de seguimiento...</p>
        </div>
    `;
            modalViaje.show();

            fetch(`/viajes/${id}/seguimiento`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Error en la respuesta del servidor');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.html) {
                        // Insertar el HTML
                        document.getElementById('modalBody').innerHTML = data.html;

                        // IMPORTANTE: Ejecutar los scripts del modal
                        ejecutarScriptsModal();
                    } else {
                        throw new Error('Respuesta inválida del servidor');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('modalBody').innerHTML = `
            <div class="alert alert-danger m-3">
                <h5><i class="bi bi-exclamation-triangle me-2"></i>Error al cargar el seguimiento</h5>
                <p>${error.message}</p>
                <button class="btn btn-sm btn-primary mt-2" onclick="seguimientoViaje(${id})">
                    <i class="mdi mdi-sync-alert me-2"></i>Reintentar
                </button>
            </div>
        `;
                });
        }

        function ejecutarScriptsModal() {


            // Buscar todos los scripts en el modal
            const scripts = document.getElementById('modalBody').querySelectorAll('script');

            scripts.forEach(oldScript => {
                // Crear un nuevo script
                const newScript = document.createElement('script');

                // Copiar atributos
                Array.from(oldScript.attributes).forEach(attr => {
                    newScript.setAttribute(attr.name, attr.value);
                });

                // Copiar contenido
                newScript.textContent = oldScript.textContent;

                // Reemplazar el script viejo por el nuevo
                oldScript.parentNode.replaceChild(newScript, oldScript);
            });

        }


        function eliminarViaje(id) {
            viajeActualId = id;
            document.getElementById('confirmacionMensaje').innerText = '¿Estás seguro de eliminar este viaje? Esta acción no se puede deshacer.';
            accionConfirmar = () => {
                fetch(`/viajes/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            modalConfirmacion.hide();
                            mostrarToast(data.message, 'Éxito', 'success');
                            setTimeout(() => location.reload(), 1500);
                        } else {
                            mostrarToast(data.error || 'Error al eliminar', 'Error', 'danger');
                        }
                    })
                    .catch(error => {
                        mostrarToast('Error al eliminar el viaje', 'Error', 'danger');
                    });
            };
            modalConfirmacion.show();
        }

        function completarViaje(id) {
            viajeActualId = id;
            document.getElementById('confirmacionMensaje').innerText = '¿Estás seguro de marcar este viaje como completado?';
            accionConfirmar = () => {
                fetch(`/viajes/${id}/completar`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            modalConfirmacion.hide();
                            mostrarToast(data.message, 'Éxito', 'success');
                            setTimeout(() => location.reload(), 1500);
                        } else {
                            mostrarToast(data.error || 'Error al completar', 'Error', 'danger');
                        }
                    })
                    .catch(error => {
                        mostrarToast('Error al completar el viaje', 'Error', 'danger');
                    });
            };
            modalConfirmacion.show();
        }

        function cancelarViaje(id) {
            viajeActualId = id;
            const motivo = prompt('Por favor indica el motivo de la cancelación:');
            if (motivo) {
                fetch(`/viajes/${id}/cancelar`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ motivo_cancelacion: motivo })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            mostrarToast(data.message, 'Éxito', 'success');
                            setTimeout(() => location.reload(), 1500);
                        } else {
                            mostrarToast(data.error || 'Error al cancelar', 'Error', 'danger');
                        }
                    })
                    .catch(error => {
                        mostrarToast('Error al cancelar el viaje', 'Error', 'danger');
                    });
            }
        }

        function guardarViaje(form) {
            event.preventDefault();
            mostrarLoading(true);

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
                        modalViaje.hide();
                        mostrarToast(data.message, 'Éxito', 'success');
                        setTimeout(() => location.reload(), 1500);
                    } else if (data.errors) {
                        mostrarErroresValidacion(data.errors);
                    } else {
                        mostrarToast('Error al guardar el viaje', 'Error', 'danger');
                    }
                })
                .catch(error => {
                    mostrarToast('Error de conexión', 'Error', 'danger');
                })
                .finally(() => {
                    mostrarLoading(false);
                });
        }

        function guardarMotos(form) {
            event.preventDefault();
            mostrarLoading(true);

            const formData = new FormData(form);

            fetch(`/viajes/${viajeActualId}/motos`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        modalMotos.hide();
                        mostrarToast(data.message, 'Éxito', 'success');
                        setTimeout(() => location.reload(), 1500);
                    } else if (data.errors) {
                        mostrarErroresValidacion(data.errors);
                    } else {
                        mostrarToast('Error al guardar las motos', 'Error', 'danger');
                    }
                })
                .catch(error => {
                    mostrarToast('Error de conexión', 'Error', 'danger');
                })
                .finally(() => {
                    mostrarLoading(false);
                });
        }

        function guardarGasto(form) {
            event.preventDefault();
            mostrarLoading(true);

            const formData = new FormData(form);

            fetch(`/viajes/${viajeActualId}/gastos`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        modalGasto.hide();
                        mostrarToast(data.message, 'Éxito', 'success');
                        setTimeout(() => location.reload(), 1500);
                    } else if (data.errors) {
                        mostrarErroresValidacion(data.errors);
                    } else {
                        mostrarToast('Error al guardar el gasto', 'Error', 'danger');
                    }
                })
                .catch(error => {
                    mostrarToast('Error de conexión', 'Error', 'danger');
                })
                .finally(() => {
                    mostrarLoading(false);
                });
        }

        function cambiarEstadoEtapa(etapaId, estado) {
            const mensajes = {
                'en_curso': '¿Iniciar esta etapa?',
                'completado': '¿Marcar esta etapa como completada?'
            };

            if (!confirm(mensajes[estado])) return;

            // Mostrar loading
            mostrarLoading(true);

            fetch(`/etapas/${etapaId}/estado`, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ estado: estado })
            })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => {
                            throw new Error(err.error || 'Error al actualizar');
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        mostrarToast(data.message, 'Éxito', 'success');
                        // Recargar las etapas después de 1 segundo
                        setTimeout(() => {
                            gestionarEtapas(viajeActualId);
                        }, 1000);
                    } else {
                        throw new Error(data.error || 'Error desconocido');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    mostrarToast(error.message, 'Error', 'danger');
                })
                .finally(() => {
                    mostrarLoading(false);
                });
        }

        function guardarNuevaEtapa(event) {
            event.preventDefault();
            const form = event.target;
            const formData = new FormData(form);

            mostrarLoading(true);

            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => {
                            throw new Error(err.error || 'Error al guardar');
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        mostrarToast(data.message, 'Éxito', 'success');
                        cancelarNuevaEtapa();
                        // Recargar las etapas
                        setTimeout(() => {
                            gestionarEtapas(viajeActualId);
                        }, 1000);
                    } else {
                        throw new Error(data.error || 'Error desconocido');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    mostrarToast(error.message, 'Error', 'danger');
                })
                .finally(() => {
                    mostrarLoading(false);
                });
        }

        function agregarCampoMoto() {
            const container = document.getElementById('motos-container');
            if (!container) return;

            const index = container.children.length;
            const html = `
            <div class="row mb-2 moto-item align-items-center">
                 <div class="col-md-5">
                    <input type="text" class="form-control" name="motos[${index}][modelo_moto]" placeholder="Modelo de moto" required>
                </div>
                <div class="col-md-5">
                    <input type="text" class="form-control" name="motos[${index}][modelo_moto]" placeholder="Modelo de moto" required>
                </div>
                <div class="col-md-2">
                    <input type="number" class="form-control" name="motos[${index}][cantidad]" placeholder="Cantidad" min="1" required>
                </div>
                <div class="col-md-3">
                    <input type="number" class="form-control" name="motos[${index}][precio_por_moto]" placeholder="Precio por moto" step="0.01" min="0" required>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-danger btn-sm" onclick="eliminarCampoMoto(this)">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
        `;
            container.insertAdjacentHTML('beforeend', html);
        }

        function eliminarCampoMoto(btn) {
            btn.closest('.moto-item').remove();
        }

        function mostrarErroresValidacion(errors) {
            let mensaje = 'Errores de validación:\n';
            for (let campo in errors) {
                mensaje += `- ${errors[campo].join(', ')}\n`;
            }
            alert(mensaje);
        }

        function actualizarEstadisticas() {
            // Calcular total de motos de todos los viajes visibles
            let totalMotos = 0;
            document.querySelectorAll('[data-motos-count]').forEach(el => {
                totalMotos += parseInt(el.dataset.motosCount) || 0;
            });
            document.getElementById('totalMotos').innerText = totalMotos;
        }

        // Inicializar tooltips y otros componentes
        document.addEventListener('DOMContentLoaded', function() {
            actualizarEstadisticas();

            // Inicializar tooltips de Bootstrap
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });

        // Confirmar acción desde modal
        document.getElementById('confirmarAccionBtn').addEventListener('click', function() {
            if (accionConfirmar) {
                accionConfirmar();
            }
        });



        function adminMotos(id) {
            viajeActualId = id;
            document.getElementById('modalTitleText').innerText = 'Administrar Motos del Viaje';
            document.getElementById('modalBody').innerHTML = `
        <div class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
            <p class="mt-2">Cargando información de motos...</p>
        </div>
    `;
            modalViaje.show();

            fetch(`/viajes/${id}/motos/admin`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.html) {
                        document.getElementById('modalBody').innerHTML = data.html;

                        // 🔴 OCULTAR LA SECCIÓN DE EDICIÓN AL INICIO
                        const proveedorSection = document.getElementById('proveedorSection');
                        if (proveedorSection) {
                            proveedorSection.style.display = 'none';
                        }

                        // Manejar visibilidad de campos de proveedor en el formulario nuevo
                        document.getElementById('proveedor_paga_nuevo').addEventListener('change', function() {
                            const container = document.getElementById('camposProveedorNuevo');
                            container.style.display = this.checked ? 'block' : 'none';

                            if (!this.checked) {
                                document.getElementById('nuevo_proveedor_codprov').value = '';
                                document.getElementById('nuevo_monto_transporte').value = '';
                                actualizarPreviewProveedor();
                            }
                        });

                        // Calcular preview cuando cambia el monto
                        document.getElementById('nuevo_monto_transporte').addEventListener('input', actualizarPreviewProveedor);

                    } else {
                        throw new Error('Error al cargar la información');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('modalBody').innerHTML = `
            <div class="alert alert-danger m-3">
                <h5><i class="bi bi-exclamation-triangle me-2"></i>Error al cargar</h5>
                <p>${error.message}</p>
                <button class="btn btn-sm btn-primary mt-2" onclick="adminMotos(${id})">
                    <i class="mdi mdi-sync-alert   me-2"></i>Reintentar
                </button>
            </div>
        `;
                });
        }

        function actualizarPreviewProveedor() {
            const monto = parseFloat(document.getElementById('nuevo_monto_transporte').value) || 0;
            const retencion = monto * 0.3;
            const total = monto - retencion;

            document.getElementById('preview_transporte').textContent = monto.toFixed(2);
            document.getElementById('preview_retencion').textContent = retencion.toFixed(2);
            document.getElementById('preview_total').textContent = total.toFixed(2);
        }

        function adminEtapas(id) {
            viajeActualId = id;
            document.getElementById('modalTitleText').innerText = 'Administrar Etapas del Viaje';
            document.getElementById('modalBody').innerHTML = `
        <div class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
            <p class="mt-2">Cargando información de etapas...</p>
        </div>
    `;
            modalViaje.show();

            fetch(`/viajes/${id}/etapas/admin`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.html) {
                        document.getElementById('modalBody').innerHTML = data.html;
                    } else {
                        throw new Error('Error al cargar la información');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('modalBody').innerHTML = `
            <div class="alert alert-danger m-3">
                <h5><i class="bi bi-exclamation-triangle me-2"></i>Error al cargar</h5>
                <p>${error.message}</p>
                <button class="btn btn-sm btn-primary mt-2" onclick="adminEtapas(${id})">
                    <i class="mdi mdi-sync-alert me-2"></i>Reintentar
                </button>
            </div>
        `;
                });
        }


        //////////////////////
        function agregarEtapa(event) {

            var viajeId =  document.getElementById(`viajetaletapas`).value;
            event.preventDefault();

            const nombre = document.getElementById('nuevo_nombre').value;
            const ubicacion = document.getElementById('nueva_ubicacion').value;
            const kilometraje = document.getElementById('nuevo_kilometraje').value;
           // const fechaInicio = document.getElementById('nueva_fecha_inicio').value;
          //  const fechaFin = document.getElementById('nueva_fecha_fin').value;

            if (!nombre || !ubicacion) {
                alert('Nombre y ubicación son requeridos');
                return;
            }

            mostrarLoading(true);

            fetch(`/viajes/${viajeId}/etapas`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    nombre: nombre,
                    ubicacion: ubicacion,
                    kilometraje_estimado: kilometraje || null,
                   // fecha_estimada_inicio: fechaInicio || null,
                   // fecha_estimada_fin: fechaFin || null,
                    orden: obtenerProximoOrden()
                })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        mostrarToast('Etapa agregada correctamente', 'Éxito', 'success');

                        // Limpiar formulario
                        document.getElementById('nuevo_nombre').value = '';
                        document.getElementById('nueva_ubicacion').value = '';
                        document.getElementById('nuevo_kilometraje').value = '';
                        //document.getElementById('nueva_fecha_inicio').value = '';
                        //document.getElementById('nueva_fecha_fin').value = '';

                        // Agregar la nueva etapa a la tabla sin recargar
                        agregarEtapaATabla(data.etapa);

                        // Actualizar contador de etapas
                        actualizarContadorEtapas();

                    } else {
                        mostrarToast('Error al agregar la etapa', 'Error', 'danger');
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

        function obtenerProximoOrden() {
            const filas = document.querySelectorAll('#tablaEtapas tbody tr');
            return filas.length + 1;
        }

        function agregarEtapaATabla(etapa) {
            const tbody = document.querySelector('#tablaEtapas tbody');
            if (!tbody) {
                // Si no hay tabla, probablemente no había etapas, recargamos
                location.reload();
                return;
            }

            // Formatear fechas
           /* let fechasHtml = 'No definidas';
            if (etapa.fecha_estimada_inicio || etapa.fecha_estimada_fin) {
                fechasHtml = '';
                if (etapa.fecha_estimada_inicio) {
                    const fechaInicio = new Date(etapa.fecha_estimada_inicio);
                    fechasHtml += `<small>Inicio: ${fechaInicio.toLocaleDateString()} ${fechaInicio.toLocaleTimeString()}</small><br>`;
                }
                if (etapa.fecha_estimada_fin) {
                    const fechaFin = new Date(etapa.fecha_estimada_fin);
                    fechasHtml += `<small>Fin: ${fechaFin.toLocaleDateString()} ${fechaFin.toLocaleTimeString()}</small>`;
                }
            }*/

            const nuevaFila = document.createElement('tr');
            nuevaFila.id = `etapa-${etapa.id}`;
            nuevaFila.setAttribute('data-id', etapa.id);
            nuevaFila.setAttribute('data-orden', etapa.orden);
            nuevaFila.innerHTML = `
        <td>
            <span class="orden-text">${etapa.orden}</span>
            <input type="number" class="form-control orden-input" value="${etapa.orden}" min="1" style="display: none; width: 70px;">
        </td>
        <td>
            <span class="nombre-text">${etapa.nombre}</span>
            <input type="text" class="form-control nombre-input" value="${etapa.nombre}" style="display: none;">
        </td>
        <td>
            <span class="ubicacion-text">${etapa.ubicacion}</span>
            <input type="text" class="form-control ubicacion-input" value="${etapa.ubicacion}" style="display: none;">
        </td>
        <td>
            <span class="km-text">${etapa.kilometraje_estimado || 'N/A'}</span>
            <input type="number" class="form-control km-input" value="${etapa.kilometraje_estimado || ''}" step="0.01" min="0" style="display: none; width: 100px;">
        </td>

        <td>
            <span class="badge bg-secondary">Pendiente</span>
        </td>
        <td>
            <button class="btn btn-sm btn-warning btn-editar" onclick="editarEtapa(${etapa.id})" title="Editar">
                <i class="bi bi-pencil"></i>
            </button>
            <button class="btn btn-sm btn-success btn-guardar" onclick="guardarEtapa(${etapa.id})" style="display: none;" title="Guardar">
                <i class="bi bi-save-fill"></i>
            </button>
            <button class="btn btn-sm btn-secondary btn-cancelar" onclick="cancelarEdicionEtapa(${etapa.id})" style="display: none;" title="Cancelar">
                <i class="bi bi-x"></i>
            </button>
            <button class="btn btn-sm btn-danger" onclick="eliminarEtapa(${etapa.id})" title="Eliminar">
                <i class="bi bi-trash"></i>
            </button>
        </td>
    `;

            tbody.appendChild(nuevaFila);

            // Si no había etapas antes, ocultar el mensaje de "no hay etapas"
            const mensajeVacio = document.querySelector('.text-muted.text-center.py-3');
            if (mensajeVacio) {
                mensajeVacio.closest('.card-body').innerHTML = '';
                mensajeVacio.closest('.card-body').appendChild(tbody.closest('.table-responsive'));
            }

            // Actualizar el contador en el header
            actualizarContadorEtapas();
        }

        function actualizarContadorEtapas() {
            const contador = document.querySelector('.card-header .badge');
            if (contador) {
                const totalEtapas = document.querySelectorAll('#tablaEtapas tbody tr').length;
                contador.textContent = totalEtapas + ' etapas';
            }

            // Actualizar también el progreso en la info del viaje
            actualizarProgresoViaje();
        }

        function actualizarProgresoViaje() {
            const totalEtapas = document.querySelectorAll('#tablaEtapas tbody tr').length;
            const completadas = document.querySelectorAll('#tablaEtapas tbody tr td .badge.bg-success').length;
            const porcentaje = totalEtapas > 0 ? Math.round((completadas / totalEtapas) * 100) : 0;

            const progressBar = document.querySelector('.progress .progress-bar');
            const progressText = document.querySelector('.progress + small');

            if (progressBar) {
                progressBar.style.width = porcentaje + '%';
            }
            if (progressText) {
                progressText.textContent = `${completadas}/${totalEtapas} etapas`;
            }
        }


        function editarEtapa(id) {
            const row = document.getElementById(`etapa-${id}`);

            // Ocultar textos
            row.querySelector('.orden-text').style.display = 'none';
            row.querySelector('.nombre-text').style.display = 'none';
            row.querySelector('.ubicacion-text').style.display = 'none';
            row.querySelector('.km-text').style.display = 'none';
           // row.querySelector('.fechas-text').style.display = 'none';

            // Mostrar inputs
            row.querySelector('.orden-input').style.display = 'block';
            row.querySelector('.nombre-input').style.display = 'block';
            row.querySelector('.ubicacion-input').style.display = 'block';
            row.querySelector('.km-input').style.display = 'block';
          //  row.querySelector('.fechas-input').style.display = 'block';

            // Ocultar/mostrar botones
            row.querySelector('.btn-editar').style.display = 'none';
            row.querySelector('.btn-guardar').style.display = 'inline-block';
            row.querySelector('.btn-cancelar').style.display = 'inline-block';
        }

        function cancelarEdicionEtapa(id) {
            const row = document.getElementById(`etapa-${id}`);

            // Restaurar valores originales
            row.querySelector('.orden-input').value = row.querySelector('.orden-text').textContent;
            row.querySelector('.nombre-input').value = row.querySelector('.nombre-text').textContent;
            row.querySelector('.ubicacion-input').value = row.querySelector('.ubicacion-text').textContent;
            row.querySelector('.km-input').value = row.querySelector('.km-text').textContent.replace(' km', '');

            // Mostrar textos
            row.querySelector('.orden-text').style.display = 'inline';
            row.querySelector('.nombre-text').style.display = 'inline';
            row.querySelector('.ubicacion-text').style.display = 'inline';
            row.querySelector('.km-text').style.display = 'inline';
            //row.querySelector('.fechas-text').style.display = 'block';

            // Ocultar inputs
            row.querySelector('.orden-input').style.display = 'none';
            row.querySelector('.nombre-input').style.display = 'none';
            row.querySelector('.ubicacion-input').style.display = 'none';
            row.querySelector('.km-input').style.display = 'none';
            //row.querySelector('.fechas-input').style.display = 'none';

            // Restaurar botones
            row.querySelector('.btn-editar').style.display = 'inline-block';
            row.querySelector('.btn-guardar').style.display = 'none';
            row.querySelector('.btn-cancelar').style.display = 'none';
        }

        function guardarEtapa(id) {
            const row = document.getElementById(`etapa-${id}`);


            const orden = row.querySelector('.orden-input').value;
            const nombre = row.querySelector('.nombre-input').value;
            const ubicacion = row.querySelector('.ubicacion-input').value;
            const kilometraje = row.querySelector('.km-input').value;
            const fechaInicio = row.querySelector('.fecha_inicio-input')?.value;
            const fechaFin = row.querySelector('.fecha_fin-input')?.value;

            if (!nombre || !ubicacion) {
                alert('Nombre y ubicación son requeridos');
                return;
            }

            mostrarLoading(true);

            fetch(`/etapas/${id}`, {
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    orden: parseInt(orden),
                    nombre: nombre,
                    ubicacion: ubicacion,
                    kilometraje_estimado: kilometraje || null,
                    fecha_estimada_inicio: fechaInicio || null,
                    fecha_estimada_fin: fechaFin || null
                })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        mostrarToast('Etapa actualizada correctamente', 'Éxito', 'success');

                        // Actualizar textos
                        row.querySelector('.orden-text').textContent = orden;
                        row.querySelector('.nombre-text').textContent = nombre;
                        row.querySelector('.ubicacion-text').textContent = ubicacion;
                        row.querySelector('.km-text').textContent = kilometraje || 'N/A';

                        // Actualizar fechas
                       /* let fechasHtml = '';
                        if (fechaInicio) {
                            const fecha = new Date(fechaInicio);
                            fechasHtml += `<small>Inicio: ${fecha.toLocaleDateString()} ${fecha.toLocaleTimeString()}</small><br>`;
                        }
                        if (fechaFin) {
                            const fecha = new Date(fechaFin);
                            fechasHtml += `<small>Fin: ${fecha.toLocaleDateString()} ${fecha.toLocaleTimeString()}</small>`;
                        }
                        row.querySelector('.fechas-text').innerHTML = fechasHtml || 'No definidas';*/

                        cancelarEdicionEtapa(id);

                        // Si cambió el orden, reordenar visualmente
                        if (parseInt(orden) !== parseInt(row.getAttribute('data-orden'))) {
                            location.reload(); // Por simplicidad, recargamos si cambia el orden
                        }
                    } else {
                        mostrarToast('Error al actualizar la etapa', 'Error', 'danger');
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

        function eliminarEtapa(id) {
            if (!confirm('¿Estás seguro de eliminar esta etapa?')) return;

            mostrarLoading(true);

            fetch(`/etapas/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        mostrarToast('Etapa eliminada correctamente', 'Éxito', 'success');

                        // Eliminar la fila
                        const fila = document.getElementById(`etapa-${id}`);
                        if (fila) {
                            fila.remove();

                            // Reordenar las etapas restantes
                            reordenarEtapasDespuesDeEliminar();
                        }

                        // Actualizar contadores
                        actualizarContadorEtapas();

                        // Si no quedan etapas, mostrar mensaje
                        if (document.querySelectorAll('#tablaEtapas tbody tr').length === 0) {
                            mostrarMensajeSinEtapas();
                        }
                    } else {
                        mostrarToast('Error al eliminar la etapa', 'Error', 'danger');
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

        function recargarEtapas() {
            mostrarLoading(true);

            var viajeId =  document.getElementById(`viajetaletapas`).value;

            fetch(`/viajes/${viajeId}/etapas/admin`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.html) {
                        // Reemplazar todo el contenido del modal
                        const modalBody = document.getElementById('modalBody');
                        modalBody.innerHTML = data.html;

                        mostrarToast('Etapas actualizadas', 'Éxito', 'success');
                    } else {
                        throw new Error('Error al recargar');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    mostrarToast('Error al recargar etapas', 'Error', 'danger');
                })
                .finally(() => {
                    mostrarLoading(false);
                });
        }

        function reordenarEtapasDespuesDeEliminar() {
            const filas = document.querySelectorAll('#tablaEtapas tbody tr');
            filas.forEach((fila, index) => {
                const nuevoOrden = index + 1;
                const ordenText = fila.querySelector('.orden-text');
                const ordenInput = fila.querySelector('.orden-input');

                if (ordenText) ordenText.textContent = nuevoOrden;
                if (ordenInput) ordenInput.value = nuevoOrden;

                // Actualizar el atributo data-orden
                fila.setAttribute('data-orden', nuevoOrden);
            });
        }

        // Función para mostrar mensaje cuando no hay etapas
        function mostrarMensajeSinEtapas() {
            const cardBody = document.querySelector('#tablaEtapas')?.closest('.card-body');
            if (cardBody) {
                cardBody.innerHTML = `
            <p class="text-muted text-center py-3">
                <i class="bi bi-info-circle me-2"></i>
                No hay etapas registradas en este viaje. Agrega una usando el formulario de arriba.
            </p>
        `;
            }
        }



        function enviarLinkWhatsApp(viajeId, telefono) {
            if (!telefono) {
                mostrarToast('El chofer no tiene teléfono registrado', 'Atención', 'warning');
                return;
            }

            // Generar token (encriptar el ID)
            fetch(`/generar-token-seguimiento/${viajeId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const url = window.location.origin + '/publico/seguimiento/' + data.token;
                        const mensaje = encodeURIComponent(`Hola! Para dar seguimiento a tu viaje #${viajeId}, usa este enlace:\n${url}\n\nPodrás actualizar tu ubicación y marcar las etapas del viaje.`);

                        // Abrir WhatsApp
                        window.open(`https://wa.me/${telefono}?text=${mensaje}`, '_blank');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    mostrarToast('Error al generar el enlace', 'Error', 'danger');
                });
        }

        function adminGastos(id) {
            viajeActualId = id;
            document.getElementById('modalTitleText').innerText = 'Administrar Gastos del Viaje';
            document.getElementById('modalBody').innerHTML = `
        <div class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
            <p class="mt-2">Cargando información de gastos...</p>
        </div>
    `;
            modalViaje.show();


            fetch(`/viajes/${id}/gastos/admin`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                }
            })
                .then(response => {

                    if (!response.ok) {
                        return response.text().then(text => {
                            console.error('❌ Respuesta error:', text);
                            throw new Error(`Error ${response.status}: ${response.statusText}`);
                        });
                    }
                    return response.json();
                })
                .then(data => {

                    if (data.success && data.html) {
                        document.getElementById('modalBody').innerHTML = data.html;

                        // Ejecutar scripts si es necesario
                        if (typeof ejecutarScriptsModal === 'function') {
                            ejecutarScriptsModal();
                        }
                    } else {
                        throw new Error(data.error || 'Respuesta inválida del servidor');
                    }
                })
                .catch(error => {
                    console.error('❌ Error:', error);
                    document.getElementById('modalBody').innerHTML = `
            <div class="alert alert-danger m-3">
                <h5><i class="bi bi-exclamation-triangle me-2"></i>Error al cargar</h5>
                <p>${error.message}</p>
                <p class="small text-muted">URL: /viajes/${id}/gastos/admin</p>
                <button class="btn btn-sm btn-primary mt-2" onclick="adminGastos(${id})">
                    <i class="mdi mdi-sync-alert me-2"></i>Reintentar
                </button>
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
            document.getElementById('toastTitle').innerText = titulo;
            document.getElementById('toastMessage').innerText = mensaje;

            const toastElement = document.getElementById('liveToast');
            toastElement.classList.remove('bg-success', 'bg-danger', 'bg-warning', 'bg-info');
            toastElement.classList.add(`bg-${tipo}`, 'text-white');

            toast.show();

            setTimeout(() => {
                toast.hide();
            }, 5000);
        }

        // ========== FUNCIONES PARA EL MODAL DE MOTOS ==========
        // Variables globales para el modal de motos
        window.timeoutIdModelo = null;
        window.timeoutIdModeloEdit = {};
        window.modeloSeleccionado = null;

        // Función de búsqueda de modelos
        window.buscarModelosMoto = function(term) {

            const resultadosDiv = document.getElementById('resultados-modelos');
            if (!resultadosDiv) {
                console.error('❌ No se encontró el div resultados-modelos');
                return;
            }

            if (term.length < 2) {
                resultadosDiv.style.display = 'none';
                return;
            }

            resultadosDiv.innerHTML = '<div class="list-group-item text-center"><div class="spinner-border spinner-border-sm text-primary" role="status"></div> Buscando...</div>';
            resultadosDiv.style.display = 'block';

            if (window.timeoutIdModelo) {
                clearTimeout(window.timeoutIdModelo);
            }

            window.timeoutIdModelo = setTimeout(() => {
                fetch(`/buscar-modelos-moto?q=${encodeURIComponent(term)}`)
                    .then(response => {
                        if (!response.ok) throw new Error('Error en la respuesta');
                        return response.json();
                    })
                    .then(data => {


                        if (data && data.length > 0) {
                            resultadosDiv.innerHTML = '';
                            data.forEach(modelo => {
                                const item = document.createElement('a');
                                item.href = '#';
                                item.className = 'list-group-item list-group-item-action';
                                item.innerHTML = `
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>${modelo.modelo || modelo.descrip || 'N/A'}</strong>
                                </div>
                                <i class="bi bi-chevron-right text-muted"></i>
                            </div>
                        `;
                                item.onclick = (e) => {
                                    e.preventDefault();
                                    window.seleccionarModelo(modelo);
                                };
                                resultadosDiv.appendChild(item);
                            });
                            resultadosDiv.style.display = 'block';
                        } else {
                            resultadosDiv.innerHTML = '<div class="list-group-item text-muted">No se encontraron modelos</div>';
                            resultadosDiv.style.display = 'block';
                        }
                    })
                    .catch(error => {
                        console.error('❌ Error en búsqueda:', error);
                        resultadosDiv.innerHTML = '<div class="list-group-item text-danger">Error al buscar</div>';
                        resultadosDiv.style.display = 'block';
                    });
            }, 300);
        };

        // Función seleccionar modelo
        window.seleccionarModelo = function(modelo) {


            const nuevoModelo = document.getElementById('nuevo_modelo');
            const modeloId = document.getElementById('modelo_id');

            if (nuevoModelo) {
                nuevoModelo.value = modelo.modelo || modelo.descrip || '';
            }
            if (modeloId) {
                modeloId.value = modelo.id || '';
            }

            const resultadosDiv = document.getElementById('resultados-modelos');
            if (resultadosDiv) {
                resultadosDiv.style.display = 'none';
            }

            window.modeloSeleccionado = modelo;
        };

        // Función marcar facturado
        window.marcarFacturado = function(motoId) {
            const row = document.getElementById(`moto-${motoId}`);
            if (!row) return;

            const clienteCod = row.querySelector('.cliente-text')?.getAttribute('data-codclie');

            if (clienteCod === 'V15184480') {
                window.mostrarToast('Este cliente no requiere facturación', 'Información', 'info');
                return;
            }

            if (!confirm('¿Marcar esta moto como facturada?')) return;

            const viajeId = document.getElementById('viajetalmotos')?.value;
            if (!viajeId) return;

            window.mostrarLoading(true);

            fetch(`/viajes/${viajeId}/motos/${motoId}/facturar`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.mostrarToast('Moto marcada como facturada', 'Éxito', 'success');

                        // Actualizar la fila visualmente
                        row.classList.add('table-success');

                        // Actualizar la celda de estado
                        const estadoCell = row.querySelector('td:nth-child(7)');
                        if (estadoCell) {
                            const hoy = new Date();
                            const fechaStr = hoy.toLocaleDateString();

                            // Verificar si la moto tiene proveedor
                            const tieneProveedor = row.querySelector('.proveedor-paga')?.value == 1;
                            let proveedorHTML = '';

                            if (tieneProveedor) {
                                const proveedorBadge = row.querySelector('.badge.bg-info');
                                if (proveedorBadge) {
                                    proveedorHTML = `<br>${proveedorBadge.outerHTML}`;
                                }
                            }

                            estadoCell.innerHTML = `
                        <span class="badge bg-success">Facturado</span>
                        <br><small>${fechaStr}</small>
                        ${proveedorHTML}
                    `;
                        }

                        // Actualizar el resumen
                        window.actualizarResumenClientes();

                        // Actualizar totales generales
                        window.actualizarTotales();
                    } else {
                        window.mostrarToast('Error al facturar', 'Error', 'danger');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    window.mostrarToast('Error de conexión', 'Error', 'danger');
                })
                .finally(() => {
                    window.mostrarLoading(false);
                });
        };

        // Función facturar cliente
        // Función facturar cliente (CORREGIDA)
        window.facturarCliente = function(codclie) {
            if (codclie === 'V15184480') {
                window.mostrarToast('Este cliente no requiere facturación', 'Información', 'info');
                return;
            }

            if (!confirm('¿Facturar todas las motos pendientes de este cliente?')) return;

            const viajeId = document.getElementById('viajetalmotos')?.value;
            if (!viajeId) return;

            window.mostrarLoading(true);

            fetch(`/viajes/${viajeId}/cliente/${codclie}/facturar`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => {
                            throw new Error(err.error || 'Error en la respuesta');
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        window.mostrarToast('Cliente facturado correctamente', 'Éxito', 'success');

                        // 🔴 ACTUALIZAR CADA MOTO DEL CLIENTE INDIVIDUALMENTE
                        document.querySelectorAll(`#tablaMotos tbody tr`).forEach(row => {
                            const rowCliente = row.querySelector('.cliente-text')?.getAttribute('data-codclie');

                            if (rowCliente === codclie) {
                                // Marcar la fila como facturada
                                row.classList.add('table-success');

                                // Actualizar la celda de estado (columna 7)
                                const estadoCell = row.querySelector('td:nth-child(7)');
                                if (estadoCell) {
                                    // Obtener la fecha actual para mostrarla
                                    const hoy = new Date();
                                    const fechaStr = hoy.toLocaleDateString();

                                    // Verificar si la moto tiene proveedor para mantener esa información
                                    const tieneProveedor = row.querySelector('.proveedor-paga')?.value == 1;
                                    let proveedorHTML = '';

                                    if (tieneProveedor) {
                                        const proveedorBadge = row.querySelector('.badge.bg-info');
                                        if (proveedorBadge) {
                                            proveedorHTML = `<br>${proveedorBadge.outerHTML}`;
                                        }
                                    }

                                    estadoCell.innerHTML = `
                                <span class="badge bg-success">Facturado</span>
                                <br><small>${fechaStr}</small>
                                ${proveedorHTML}
                            `;
                                }

                                // Si había un botón de facturar individual, eliminarlo o reemplazarlo
                                const botonFacturarIndividual = row.querySelector('td:nth-child(7) .btn-primary');
                                if (botonFacturarIndividual) {
                                    botonFacturarIndividual.remove();
                                }
                            }
                        });

                        // 🔴 ACTUALIZAR EL RESUMEN DE CLIENTES
                        window.actualizarResumenClientes();

                        // 🔴 ACTUALIZAR TOTALES GENERALES
                        window.actualizarTotales();

                        // 🔴 ACTUALIZAR EL FOOTER DE PROVEEDORES (por si acaso)
                        window.actualizarFooterProveedores();

                    } else {
                        window.mostrarToast(data.error || 'Error al facturar', 'Error', 'danger');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    window.mostrarToast(error.message || 'Error de conexión', 'Error', 'danger');
                })
                .finally(() => {
                    window.mostrarLoading(false);
                });
        };

        // Función agregar moto
        window.agregarMoto = function(event) {
            event.preventDefault();
            event.stopPropagation();

            console.log('agregarMoto');

            const viajeId  = document.getElementById('viajetalmotos')?.value;
            const cliente  = document.getElementById('nuevo_cliente')?.value;
            const modelo   = document.getElementById('nuevo_modelo')?.value;
            const modeloId = document.getElementById('modelo_id')?.value;
            const cantidad = document.getElementById('nueva_cantidad')?.value;
            const precio   = document.getElementById('nuevo_precio')?.value;

            // 🔴 DATOS DEL PROVEEDOR - CORREGIDO
            const proveedorPaga = document.getElementById('proveedor_paga_nuevo')?.checked || false;
            const proveedorCodprov = proveedorPaga ? document.getElementById('nuevo_proveedor_codprov')?.value : null;
            const montoTransporte = proveedorPaga ? document.getElementById('nuevo_monto_transporte')?.value : null;

            console.log('📦 Datos de proveedor:', {
                proveedorPaga,
                proveedorCodprov,
                montoTransporte
            });

            if (!cliente || !modelo || !cantidad || !precio) {
                alert('Todos los campos son requeridos');
                return false;
            }

            if (proveedorPaga && (!proveedorCodprov || !montoTransporte)) {
                alert('Debes seleccionar proveedor y monto de transporte');
                return false;
            }

            window.mostrarLoading(true);

            const data = {
                motos: [{
                    cliente_codclie: cliente,
                    modelo_moto: modelo,
                    modelo_id: modeloId,
                    cantidad: parseInt(cantidad),
                    precio_por_moto: parseFloat(precio),
                    proveedor_paga: proveedorPaga ? 1 : 0,
                    proveedor_codprov: proveedorCodprov,
                    monto_transporte_proveedor: montoTransporte ? parseFloat(montoTransporte) : null
                }]
            };

            console.log('📤 Enviando datos al servidor:', data);

            fetch(`/viajes/${viajeId}/motos`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => {
                            throw new Error(err.error || 'Error en la respuesta');
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('📥 Respuesta del servidor:', data);

                    if (data.success) {
                        window.mostrarToast('Moto agregada correctamente', 'Éxito', 'success');

                        // Verificar si la tabla existe antes de intentar agregar
                        const tablaExistente = document.querySelector('#tablaMotos tbody');

                        if (tablaExistente) {
                            if (data.motos && data.motos.length > 0) {
                                data.motos.forEach(moto => window.agregarMotoATabla(moto));
                            } else if (data.moto) {
                                window.agregarMotoATabla(data.moto);
                            }
                        } else {
                            console.log('⚠️ Tabla no encontrada, recargando modal...');
                            if (viajeId) {
                                adminMotos(viajeId);
                            }
                        }

                        // Limpiar formulario
                        document.getElementById('nuevo_cliente').value = '';
                        document.getElementById('nuevo_modelo').value = '';
                        document.getElementById('modelo_id').value = '';
                        document.getElementById('nueva_cantidad').value = '';
                        document.getElementById('nuevo_precio').value = '';

                        // Limpiar campos de proveedor
                        if (document.getElementById('proveedor_paga_nuevo')) {
                            document.getElementById('proveedor_paga_nuevo').checked = false;
                            document.getElementById('camposProveedorNuevo').style.display = 'none';
                            document.getElementById('nuevo_proveedor_codprov').value = '';
                            document.getElementById('nuevo_monto_transporte').value = '';
                        }

                        const resultadosDiv = document.getElementById('resultados-modelos');
                        if (resultadosDiv) resultadosDiv.style.display = 'none';

                    } else {
                        window.mostrarToast(data.error || 'Error al agregar la moto', 'Error', 'danger');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    window.mostrarToast(error.message || 'Error de conexión', 'Error', 'danger');
                })
                .finally(() => {
                    window.mostrarLoading(false);
                });

            return false;
        };

        // 🔴 NUEVA FUNCIÓN: Actualizar preview de proveedor durante edición
        window.actualizarPreviewEdicion = function() {
            const monto = parseFloat(document.getElementById('monto_transporte_proveedor').value) || 0;

            // Crear o actualizar el elemento de preview si no existe
            let previewDiv = document.getElementById('previewEdicionProveedor');

            if (!previewDiv) {
                // Crear el div de preview si no existe
                const camposProveedor = document.getElementById('camposProveedor');
                previewDiv = document.createElement('div');
                previewDiv.id = 'previewEdicionProveedor';
                previewDiv.className = 'alert alert-info mt-2';
                previewDiv.innerHTML = `
            <small>
                <i class="bi bi-info-circle me-1"></i>
                <strong>Cálculo automático:</strong><br>
                Transporte: $<span id="preview_transporte_edit">0.00</span><br>
                Retención (30%): $<span id="preview_retencion_edit">0.00</span><br>
                <strong>El proveedor pagará: $<span id="preview_total_edit">0.00</span></strong>
            </small>
        `;
                camposProveedor.appendChild(previewDiv);
            }

            // Actualizar los valores
            const retencion = monto * 0.3;
            const total = monto - retencion;

            document.getElementById('preview_transporte_edit').textContent = monto.toFixed(2);
            document.getElementById('preview_retencion_edit').textContent = retencion.toFixed(2);
            document.getElementById('preview_total_edit').textContent = total.toFixed(2);
        };

        // Función editar moto (CORREGIDA)
        window.editarMoto = function(id) {
            console.log('editarMoto', id);

            const row = document.getElementById(`moto-${id}`);
            if (!row) return;

            // 🔴 OCULTAR EL FORMULARIO DE AGREGAR NUEVA MOTO
            const formularioAgregar = document.querySelector('.card.border-success');
            if (formularioAgregar) {
                formularioAgregar.style.display = 'none';
            }

            // Guardar el cliente actual para referencia
            const clienteActual = row.querySelector('.cliente-text').getAttribute('data-codclie');

            // Ocultar textos
            row.querySelector('.cliente-text').style.display = 'none';
            row.querySelector('.modelo-text').style.display = 'none';
            row.querySelector('.cantidad-text').style.display = 'none';
            row.querySelector('.precio-text').style.display = 'none';

            // Mostrar inputs
            row.querySelector('.cliente-input').style.display = 'block';
            row.querySelector('.modelo-input').style.display = 'block';
            row.querySelector('.cantidad-input').style.display = 'block';
            row.querySelector('.precio-input').style.display = 'block';

            // Cargar datos de proveedor si existen
            const proveedorPaga = row.querySelector('.proveedor-paga')?.value;
            const proveedorCodprov = row.querySelector('.proveedor-codprov')?.value;
            const montoTransporte = row.querySelector('.monto-transporte')?.value;

            // 🔴 SOLO MOSTRAR LA SECCIÓN DE PROVEEDOR SI LA MOTO TIENE PROVEEDOR
            const proveedorSection = document.getElementById('proveedorSection');
            if (proveedorSection) {
                // Limpiar los campos primero
                document.getElementById('proveedor_paga').checked = false;
                document.getElementById('camposProveedor').style.display = 'none';
                document.getElementById('proveedor_codprov').value = '';
                document.getElementById('monto_transporte_proveedor').value = '';

                // Si la moto tiene proveedor (proveedorPaga == 1), mostrar la sección
                if (proveedorPaga == 1) {
                    proveedorSection.style.display = 'block';
                    document.getElementById('proveedor_paga').checked = true;
                    document.getElementById('camposProveedor').style.display = 'block';
                    document.getElementById('proveedor_codprov').value = proveedorCodprov || '';
                    document.getElementById('monto_transporte_proveedor').value = montoTransporte || '';

                    // 🔴 AGREGAR EVENT LISTENER PARA ACTUALIZAR PREVIEW AL EDITAR
                    const montoInput = document.getElementById('monto_transporte_proveedor');
                    montoInput.removeEventListener('input', actualizarPreviewEdicion);
                    montoInput.addEventListener('input', actualizarPreviewEdicion);

                    // También ejecutar una vez para mostrar la preview inicial
                    actualizarPreviewEdicion();
                } else {
                    // 🔴 SI NO TIENE PROVEEDOR, OCULTAR LA SECCIÓN
                    proveedorSection.style.display = 'none';
                }
            }

            // Configurar el select de cliente para que al cambiar, actualice el botón
            const clienteSelect = row.querySelector('.cliente-input');
            clienteSelect.setAttribute('onchange', `window.actualizarBotonFacturar(${id}, this.value)`);

            // Agregar funcionalidad de búsqueda al input de modelo
            const modeloInput = row.querySelector('.modelo-input');
            modeloInput.setAttribute('autocomplete', 'off');
            modeloInput.setAttribute('oninput', `window.buscarModelosMotoEdit(this.value, ${id})`);

            // Crear contenedor para resultados si no existe
            let resultadosDiv = document.getElementById(`resultados-modelos-${id}`);
            if (!resultadosDiv) {
                resultadosDiv = document.createElement('div');
                resultadosDiv.id = `resultados-modelos-${id}`;
                resultadosDiv.className = 'list-group position-absolute w-100';
                resultadosDiv.style.cssText = 'z-index: 1000; max-height: 200px; overflow-y: auto; display: none;';
                modeloInput.parentNode.style.position = 'relative';
                modeloInput.parentNode.appendChild(resultadosDiv);
            }

            // Ocultar/mostrar botones
            row.querySelector('.btn-editar').style.display = 'none';
            row.querySelector('.btn-guardar').style.display = 'inline-block';
            row.querySelector('.btn-cancelar').style.display = 'inline-block';
        };

        // Función para actualizar el botón de facturar cuando se cambia el cliente en edición
        window.actualizarBotonFacturar = function(motoId, nuevoCliente) {
            const row = document.getElementById(`moto-${motoId}`);
            if (!row) return;

            const botonFacturar = row.querySelector('td:nth-child(6) .btn-primary');
            const badgeCell = row.querySelector('td:nth-child(6)');

            if (nuevoCliente === 'V15184480') {
                // Si el nuevo cliente es V15184480, ocultar el botón de facturar
                if (botonFacturar) botonFacturar.style.display = 'none';
            } else {
                // Si no es V15184480, mostrar el botón (si no está facturado)
                const estaFacturado = row.classList.contains('tablesuccess');
                if (!estaFacturado) {
                    if (!botonFacturar) {
                        // Si no existe el botón, crearlo
                        const nuevoBoton = document.createElement('button');
                        nuevoBoton.className = 'btn btn-sm btn-primary ms-1';
                        nuevoBoton.setAttribute('onclick', `window.marcarFacturado(${motoId})`);
                        nuevoBoton.setAttribute('title', 'Facturar');
                        nuevoBoton.innerHTML = '<i class="bi bi-file-check"></i>';
                        badgeCell.appendChild(nuevoBoton);
                    } else {
                        botonFacturar.style.display = 'inline-block';
                    }
                }
            }
        };

        // Función buscar modelos en edición
        window.buscarModelosMotoEdit = function(term, id) {
            const resultadosDiv = document.getElementById(`resultados-modelos-${id}`);
            if (!resultadosDiv) return;

            if (term.length < 2) {
                resultadosDiv.style.display = 'none';
                return;
            }

            if (window.timeoutIdModeloEdit[id]) {
                clearTimeout(window.timeoutIdModeloEdit[id]);
            }

            window.timeoutIdModeloEdit[id] = setTimeout(() => {
                fetch(`/buscar-modelos-moto?q=${encodeURIComponent(term)}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.length > 0) {
                            resultadosDiv.innerHTML = '';
                            data.forEach(modelo => {
                                const item = document.createElement('a');
                                item.href = '#';
                                item.className = 'list-group-item list-group-item-action';
                                item.innerHTML = `
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>${modelo.modelo}</strong>
                                    ${modelo.clave ? `<br><small class="text-muted">Clave: ${modelo.clave}</small>` : ''}
                                </div>
                            </div>
                        `;
                                item.onclick = (e) => {
                                    e.preventDefault();
                                    const row = document.getElementById(`moto-${id}`);
                                    row.querySelector('.modelo-input').value = modelo.modelo;
                                    resultadosDiv.style.display = 'none';
                                };
                                resultadosDiv.appendChild(item);
                            });
                            resultadosDiv.style.display = 'block';
                        } else {
                            resultadosDiv.innerHTML = '<div class="list-group-item text-muted">No se encontraron modelos</div>';
                            resultadosDiv.style.display = 'block';
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        resultadosDiv.style.display = 'none';
                    });
            }, 300);
        };

        // Función guardar moto
        // Función guardar moto (CORREGIDA)
        window.guardarMoto = function(id) {
            console.log('guardarMoto', id);

            const viajeId = document.getElementById('viajetalmotos')?.value;
            const row = document.getElementById(`moto-${id}`);
            if (!row) {
                console.error('Fila no encontrada:', id);
                return;
            }

            const cliente = row.querySelector('.cliente-input').value;
            const modelo = row.querySelector('.modelo-input').value;
            const cantidad = row.querySelector('.cantidad-input').value;
            const precio = row.querySelector('.precio-input').value;

            // Datos de proveedor
            const proveedorPaga = document.getElementById('proveedor_paga')?.checked ? 1 : 0;
            const proveedorCodprov = document.getElementById('proveedor_codprov')?.value;
            const montoTransporte = document.getElementById('monto_transporte_proveedor')?.value;

            if (!cliente || !modelo || !cantidad || !precio) {
                alert('Todos los campos son requeridos');
                return;
            }

            window.mostrarLoading(true);

            const data = {
                cliente_codclie: cliente,
                modelo_moto: modelo,
                cantidad: parseInt(cantidad),
                precio_por_moto: parseFloat(precio),
                proveedor_paga: proveedorPaga,
                proveedor_codprov: proveedorCodprov,
                monto_transporte_proveedor: montoTransporte ? parseFloat(montoTransporte) : null
            };

            fetch(`/viajes/${viajeId}/motos/${id}`, {
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => { throw new Error(err.error || 'Error en la respuesta'); });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        window.mostrarToast('Moto actualizada correctamente', 'Éxito', 'success');

                        // Usar los datos devueltos por el servidor
                        const motoActualizada = data.moto;

                        // 🔴 ACTUALIZAR LA CELDA DE PROVEEDOR COMPLETAMENTE
                        const proveedorCell = row.querySelector('td:nth-child(2)');
                        if (motoActualizada.proveedor_paga) {
                            // Buscar el nombre del proveedor (puede venir en la respuesta)
                            let proveedorNombre = 'Proveedor';
                            if (motoActualizada.proveedor && motoActualizada.proveedor.descrip) {
                                proveedorNombre = motoActualizada.proveedor.descrip;
                            }

                            const transporte = parseFloat(motoActualizada.monto_transporte_proveedor || 0).toFixed(2);
                            const retencion = parseFloat(motoActualizada.retencion_proveedor || 0).toFixed(2);

                            proveedorCell.innerHTML = `
                        <span class="badge bg-info" title="Pagado por proveedor">
                            <i class="bi bi-truck"></i> ${proveedorNombre}
                        </span>
                        <br>
                        <small class="text-muted">$${transporte}</small>
                        <br>
                        <small class="text-warning">Ret: $${retencion}</small>
                        <input type="hidden" class="proveedor-paga" value="1">
                        <input type="hidden" class="proveedor-codprov" value="${motoActualizada.proveedor_codprov || ''}">
                        <input type="hidden" class="monto-transporte" value="${motoActualizada.monto_transporte_proveedor || ''}">
                    `;
                        } else {
                            proveedorCell.innerHTML = `
                        <span class="text-muted">-</span>
                        <input type="hidden" class="proveedor-paga" value="0">
                    `;
                        }

                        // Actualizar textos con los datos del servidor
                        const clienteText = row.querySelector('.cliente-input option:checked')?.text || 'Desconocido';
                        row.querySelector('.cliente-text').textContent = clienteText;
                        row.querySelector('.cliente-text').setAttribute('data-codclie', motoActualizada.cliente_codclie);

                        row.querySelector('.modelo-text').textContent = motoActualizada.modelo_moto;
                        row.querySelector('.cantidad-text').textContent = motoActualizada.cantidad;
                        row.querySelector('.precio-text').textContent = `$${parseFloat(motoActualizada.precio_por_moto).toFixed(2)}`;

                        // Actualizar subtotal
                        const subtotal = motoActualizada.cantidad * motoActualizada.precio_por_moto;
                        row.querySelector('.subtotal').textContent = `$${subtotal.toFixed(2)}`;

                        // 🔴 ACTUALIZAR LA CELDA DE ESTADO (si es necesario)
                        const estadoCell = row.querySelector('td:nth-child(7)');
                        if (estadoCell) {
                            if (motoActualizada.facturado) {
                                estadoCell.innerHTML = `
                            <span class="badge bg-success">Facturado</span>
                            ${motoActualizada.fecha_facturacion ? `<br><small>${new Date(motoActualizada.fecha_facturacion).toLocaleDateString()}</small>` : ''}
                        `;
                            } else {
                                let html = `<span class="badge bg-warning">Pendiente</span>`;
                                if (motoActualizada.cliente_codclie !== 'V15184480') {
                                    html += `<button class="btn btn-sm btn-primary ms-1" onclick="marcarFacturado(${motoActualizada.id})" title="Facturar">
                                <i class="bi bi-file-check"></i>
                            </button>`;
                                }

                                // Estado de conciliación
                                if (motoActualizada.proveedor_paga && motoActualizada.estado_conciliacion) {
                                    html += `<br>`;
                                    if (motoActualizada.estado_conciliacion == 'conciliado') {
                                        html += `<span class="badge bg-success mt-1">Conciliado</span>`;
                                    } else if (motoActualizada.estado_conciliacion == 'discrepancia') {
                                        html += `<span class="badge bg-danger mt-1">Discrepancia</span>`;
                                    } else {
                                        html += `<span class="badge bg-warning mt-1">Pendiente conciliar</span>`;
                                    }
                                }

                                estadoCell.innerHTML = html;
                            }
                        }

                        // Salir del modo edición
                        window.cancelarEdicion(id);

                        // 🔴 ACTUALIZAR TOTALES Y RESUMEN
                        window.actualizarTotales();
                        window.actualizarResumenClientes();

                        // 🔴 ACTUALIZAR EL FOOTER DE LA TABLA (totales de proveedor)
                        window.actualizarFooterProveedores();

                    } else {
                        window.mostrarToast(data.error || 'Error al actualizar la moto', 'Error', 'danger');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    window.mostrarToast(error.message || 'Error de conexión', 'Error', 'danger');
                })
                .finally(() => {
                    window.mostrarLoading(false);
                });
        };

        // 🔴 NUEVA FUNCIÓN: Actualizar el footer de la tabla con totales de proveedores
        window.actualizarFooterProveedores = function() {
            const tfoot = document.querySelector('#tablaMotos tfoot');
            if (!tfoot) return;

            // Calcular totales de proveedores
            let totalProveedor = 0;
            let totalRetenciones = 0;

            document.querySelectorAll('#tablaMotos tbody tr').forEach(row => {
                const proveedorPaga = row.querySelector('.proveedor-paga')?.value;
                if (proveedorPaga == 1) {
                    const monto = parseFloat(row.querySelector('.monto-transporte')?.value) || 0;
                    totalProveedor += monto;

                    // Calcular retención (30% del monto)
                    const retencion = monto * 0.3;
                    totalRetenciones += retencion;
                }
            });

            // Buscar o crear la fila de proveedores
            let proveedorRow = tfoot.querySelector('.table-info');
            if (!proveedorRow) {
                // Si no existe, crear la fila
                proveedorRow = document.createElement('tr');
                proveedorRow.className = 'table-info';
                tfoot.insertBefore(proveedorRow, tfoot.querySelector('.table-primary'));
            }

            if (totalProveedor > 0) {
                proveedorRow.innerHTML = `
            <th colspan="3" class="text-end">Total Proveedores:</th>
            <th colspan="2">$${totalProveedor.toFixed(2)}</th>
            <th>Retenciones: $${totalRetenciones.toFixed(2)}</th>
            <th colspan="2"></th>
        `;
                proveedorRow.style.display = '';
            } else {
                // Ocultar la fila si no hay proveedores
                proveedorRow.style.display = 'none';
            }

            // Actualizar también el resumen de proveedores en la tabla inferior
            window.actualizarResumenProveedores();
        };

        // 🔴 NUEVA FUNCIÓN: Actualizar el resumen de proveedores en la tabla inferior
        window.actualizarResumenProveedores = function() {
            // Buscar la tabla de resumen (la que está después del hr)
            const resumenTables = document.querySelectorAll('table');
            let resumenTable = null;

            for (let table of resumenTables) {
                if (table !== document.getElementById('tablaMotos') && table.querySelector('.table-secondary')) {
                    resumenTable = table;
                    break;
                }
            }

            if (!resumenTable) return;

            // Agrupar motos por proveedor
            const proveedores = {};

            document.querySelectorAll('#tablaMotos tbody tr').forEach(row => {
                const proveedorPaga = row.querySelector('.proveedor-paga')?.value;
                if (proveedorPaga == 1) {
                    const proveedorCodprov = row.querySelector('.proveedor-codprov')?.value;
                    const proveedorNombre = row.querySelector('.badge.bg-info')?.textContent.trim() || 'Proveedor';
                    const cantidad = parseInt(row.querySelector('.cantidad-text')?.textContent) || 0;
                    const monto = parseFloat(row.querySelector('.monto-transporte')?.value) || 0;

                    if (!proveedores[proveedorCodprov]) {
                        proveedores[proveedorCodprov] = {
                            nombre: proveedorNombre,
                            cantidad: 0,
                            monto: 0
                        };
                    }

                    proveedores[proveedorCodprov].cantidad += cantidad;
                    proveedores[proveedorCodprov].monto += monto;
                }
            });

            // Buscar o crear la sección de proveedores en la tabla de resumen
            let proveedorSection = Array.from(resumenTable.querySelectorAll('tr')).find(tr =>
                tr.querySelector('td[colspan="5"]')?.textContent.includes('Transportes pagados por proveedor')
            );

            if (Object.keys(proveedores).length > 0) {
                // Si hay proveedores pero no existe la sección, crearla
                if (!proveedorSection) {
                    const tbody = resumenTable.querySelector('tbody');
                    if (tbody) {
                        // Agregar línea separadora
                        const separatorRow = document.createElement('tr');
                        separatorRow.innerHTML = '<td colspan="5" class="text-center"><strong>Transportes pagados por proveedor</strong></td>';
                        separatorRow.className = 'table-secondary';
                        tbody.appendChild(separatorRow);

                        // Agregar filas de proveedores
                        for (let [codprov, data] of Object.entries(proveedores)) {
                            const row = document.createElement('tr');
                            row.setAttribute('data-proveedor', codprov);
                            row.innerHTML = `
                        <td><small>Proveedor: ${data.nombre}</small></td>
                        <td><small>${data.cantidad} motos</small></td>
                        <td><small>$${data.monto.toFixed(2)}</small></td>
                        <td colspan="2">
                            <small>Ret: $${(data.monto * 0.3).toFixed(2)}</small>
                        </td>
                    `;
                            tbody.appendChild(row);
                        }
                    }
                } else {
                    // Actualizar filas existentes
                    for (let [codprov, data] of Object.entries(proveedores)) {
                        let proveedorRow = resumenTable.querySelector(`tr[data-proveedor="${codprov}"]`);
                        if (proveedorRow) {
                            proveedorRow.innerHTML = `
                        <td><small>Proveedor: ${data.nombre}</small></td>
                        <td><small>${data.cantidad} motos</small></td>
                        <td><small>$${data.monto.toFixed(2)}</small></td>
                        <td colspan="2">
                            <small>Ret: $${(data.monto * 0.3).toFixed(2)}</small>
                        </td>
                    `;
                        }
                    }
                }
            } else {
                // Si no hay proveedores, eliminar la sección si existe
                if (proveedorSection) {
                    const tbody = resumenTable.querySelector('tbody');
                    if (tbody) {
                        // Eliminar la línea separadora y todas las filas de proveedores
                        const rows = tbody.querySelectorAll('tr');
                        for (let i = rows.length - 1; i >= 0; i--) {
                            if (rows[i].classList.contains('table-secondary') || rows[i].hasAttribute('data-proveedor')) {
                                rows[i].remove();
                            }
                        }
                    }
                }
            }
        };


        // Manejar visibilidad de campos de proveedor
        // Al inicio del documento, después de que se cargue el DOM
        document.addEventListener('DOMContentLoaded', function() {
            // Event listener para el checkbox de proveedor en edición
            const proveedorCheck = document.getElementById('proveedor_paga');
            if (proveedorCheck) {
                proveedorCheck.addEventListener('change', function() {
                    const camposProveedor = document.getElementById('camposProveedor');
                    if (this.checked) {
                        camposProveedor.style.display = 'block';
                        // Si se marca, asegurarse de que el preview esté actualizado
                        actualizarPreviewEdicion();
                    } else {
                        camposProveedor.style.display = 'none';
                        // Eliminar preview si existe
                        const previewEdit = document.getElementById('previewEdicionProveedor');
                        if (previewEdit) {
                            previewEdit.remove();
                        }
                    }
                });
            }
        });

         // Función para actualizar el resumen por cliente (MODIFICADA)
        window.actualizarResumenClientes = function() {
            console.log('actualizarResumenClientes');

            // Buscar la tabla de resumen (la que está después del hr)
            const resumenTables = document.querySelectorAll('table');
            let resumenTable = null;

            for (let table of resumenTables) {
                if (table !== document.getElementById('tablaMotos') && table.querySelector('h6.mt-3') === null) {
                    // Esta es probablemente la tabla de resumen
                    resumenTable = table;
                    break;
                }
            }

            if (!resumenTable) {
                console.log('⚠️ No se encontró la tabla de resumen');
                return;
            }

            const filas = document.querySelectorAll('#tablaMotos tbody tr');
            console.log('📋 Total filas en tabla:', filas.length);

            const clientes = {};

            filas.forEach((row, index) => {
                const clienteCod = row.querySelector('.cliente-text')?.getAttribute('data-codclie');
                const clienteNombre = row.querySelector('.cliente-text')?.textContent || 'Sin asignar';
                const cantidad = parseInt(row.querySelector('.cantidad-text')?.textContent) || 0;
                const precioText = row.querySelector('.precio-text')?.textContent.replace('$', '').replace(',', '') || '0';
                const precio = parseFloat(precioText);
                const subtotal = cantidad * precio;
                const facturado = row.classList.contains('table-success');

                console.log(`  Fila ${index}: Cliente=${clienteCod}, Cant=${cantidad}, Precio=${precio}, Fact=${facturado}`);

                if (!clienteCod || clienteCod === 'V15184480') return;

                if (!clientes[clienteCod]) {
                    clientes[clienteCod] = {
                        codclie: clienteCod,
                        nombre: clienteNombre,
                        total_motos: 0,
                        total_pagar: 0,
                        total_facturado: 0,
                        tienePendiente: false
                    };
                }

                clientes[clienteCod].total_motos += cantidad;

                if (facturado) {
                    clientes[clienteCod].total_facturado += subtotal;
                } else {
                    clientes[clienteCod].total_pagar += subtotal;
                    clientes[clienteCod].tienePendiente = true;
                }
            });

            console.log('📊 Clientes procesados:', clientes);

            let htmlResumen = '';
            let totalGeneralPendiente = 0;
            let totalGeneralFacturado = 0;

            const clientesOrdenados = Object.values(clientes).sort((a, b) => a.nombre.localeCompare(b.nombre));

            for (let cli of clientesOrdenados) {
                totalGeneralPendiente += cli.total_pagar;
                totalGeneralFacturado += cli.total_facturado;

                const claseFila = cli.tienePendiente ? 'table-warning' : 'table-success';
                const badgeEstado = cli.tienePendiente ?
                    '<span class="badge bg-warning">Pendiente</span>' :
                    '<span class="badge bg-success">Pagado</span>';

                const botonFacturar = cli.tienePendiente ?
                    `<button class="btn btn-sm btn-primary" onclick="window.facturarCliente('${cli.codclie}')">
                <i class="bi bi-file-check"></i> Facturar ($${cli.total_pagar.toFixed(2)})
            </button>` : '';

                const montoMostrar = cli.tienePendiente ? cli.total_pagar : 0;

                htmlResumen += `
            <tr class="${claseFila}">
                <td><strong>${cli.nombre}</strong></td>
                <td>${cli.total_motos} motos</td>
                <td><strong>$${montoMostrar.toFixed(2)}</strong></td>
                <td>${badgeEstado}</td>
                <td>${botonFacturar}</td>
            </tr>
        `;
            }

            if (clientesOrdenados.length === 0) {
                htmlResumen = `
            <tr>
                <td colspan="5" class="text-center text-muted">
                    No hay otros clientes para mostrar
                </td>
            </tr>
        `;
            } else {
                htmlResumen += `
            <tr class="table-info">
                <td colspan="2"><strong>Total Pendiente (Otros clientes)</strong></td>
                <td><strong>$${totalGeneralPendiente.toFixed(2)}</strong></td>
                <td colspan="2"></td>
            </tr>
            <tr class="table-secondary">
                <td colspan="2"><small>Total Facturado</small></td>
                <td><small>$${totalGeneralFacturado.toFixed(2)}</small></td>
                <td colspan="2"></td>
            </tr>
        `;
            }

            // Actualizar la tabla de resumen
            let tbody = resumenTable.querySelector('tbody');
            if (!tbody) {
                tbody = document.createElement('tbody');
                resumenTable.appendChild(tbody);
            }
            tbody.innerHTML = htmlResumen;
            console.log('✅ Resumen actualizado');
        };

        window.agregarMotoATabla = function(moto) {
            console.log('Agregando moto a tabla:', moto);

            const tbody = document.querySelector('#tablaMotos tbody');
            if (!tbody) {
                console.error('❌ No se encontró el tbody de la tabla');
                return;
            }

            // Buscar el nombre del cliente
            const clienteSelect = document.getElementById('nuevo_cliente');
            let clienteNombre = 'Desconocido';
            if (clienteSelect) {
                const option = Array.from(clienteSelect.options).find(opt => opt.value === moto.cliente_codclie);
                clienteNombre = option ? option.text : 'Desconocido';
            }

            // 🔴 PROCESAR DATOS DEL PROVEEDOR
            let proveedorHTML = '<span class="text-muted">-</span>';
            let proveedorPaga = 0;
            let proveedorCodprov = '';
            let montoTransporte = '';

            if (moto.proveedor_paga) {
                proveedorPaga = 1;
                proveedorCodprov = moto.proveedor_codprov || '';
                montoTransporte = moto.monto_transporte_proveedor || '';

                // Buscar nombre del proveedor (si viene en la respuesta)
                let proveedorNombre = 'Proveedor';
                if (moto.proveedor && moto.proveedor.descrip) {
                    proveedorNombre = moto.proveedor.descrip;
                }

                const transporte = parseFloat(moto.monto_transporte_proveedor || 0).toFixed(2);
                const retencion = parseFloat(moto.retencion_proveedor || 0).toFixed(2);

                proveedorHTML = `
            <span class="badge bg-info" title="Pagado por proveedor">
                <i class="bi bi-truck"></i> ${proveedorNombre}
            </span>
            <br>
            <small class="text-muted">$${transporte}</small>
            <br>
            <small class="text-warning">Ret: $${retencion}</small>
        `;
            }

            const mostrarBotonFacturar = moto.cliente_codclie !== 'V15184480';
            const botonFacturar = mostrarBotonFacturar ?
                `<button class="btn btn-sm btn-primary ms-1" onclick="window.marcarFacturado(${moto.id})" title="Facturar">
            <i class="bi bi-file-check"></i>
        </button>` : '';

            const subtotal = moto.cantidad * moto.precio_por_moto;

            const nuevaFila = document.createElement('tr');
            nuevaFila.id = `moto-${moto.id}`;
            nuevaFila.setAttribute('data-id', moto.id);
            nuevaFila.className = '';

            nuevaFila.innerHTML = `
        <td>
            <span class="cliente-text" data-codclie="${moto.cliente_codclie}">${clienteNombre}</span>
            <select class="form-select cliente-input" style="display: none;">
                ${Array.from(clienteSelect.options).map(opt =>
                `<option value="${opt.value}" ${opt.value === moto.cliente_codclie ? 'selected' : ''}>${opt.text}</option>`
            ).join('')}
            </select>
        </td>
        <td>
            ${proveedorHTML}
            <input type="hidden" class="proveedor-paga" value="${proveedorPaga}">
            <input type="hidden" class="proveedor-codprov" value="${proveedorCodprov}">
            <input type="hidden" class="monto-transporte" value="${montoTransporte}">
        </td>
        <td>
            <span class="modelo-text">${moto.modelo_moto}</span>
            <input type="text" class="form-control modelo-input" value="${moto.modelo_moto}" style="display: none;">
        </td>
        <td>
            <span class="cantidad-text">${moto.cantidad}</span>
            <input type="number" class="form-control cantidad-input" value="${moto.cantidad}" min="1" style="display: none; width: 80px;">
        </td>
        <td>
            <span class="precio-text">$${parseFloat(moto.precio_por_moto).toFixed(2)}</span>
            <input type="number" class="form-control precio-input" value="${moto.precio_por_moto}" step="0.01" min="0" style="display: none; width: 100px;">
        </td>
        <td class="subtotal">$${subtotal.toFixed(2)}</td>
        <td>
            <span class="badge bg-warning">Pendiente</span>
            ${botonFacturar}

            ${moto.proveedor_paga ? `
                <br>
                <span class="badge bg-info mt-1">Conciliación pendiente</span>
            ` : ''}
        </td>
        <td>
            <button class="btn btn-sm btn-warning btn-editar" onclick="window.editarMoto(${moto.id})" title="Editar">
                <i class="bi bi-pen-fill"></i>
            </button>
            <button class="btn btn-sm btn-success btn-guardar" onclick="window.guardarMoto(${moto.id})" style="display: none;" title="Guardar">
                <i class="bi bi-save-fill"></i>
            </button>
            <button class="btn btn-sm btn-secondary btn-cancelar" onclick="window.cancelarEdicion(${moto.id})" style="display: none;" title="Cancelar">
                <i class="bi bi-x"></i>
            </button>
            <button class="btn btn-sm btn-danger" onclick="window.eliminarMoto(${moto.id})" title="Eliminar">
                <i class="bi bi-trash"></i>
            </button>
        </td>
    `;

            tbody.appendChild(nuevaFila);

            // Actualizar contador
            const contadorBadge = document.querySelector('.card-header .badge');
            if (contadorBadge) {
                const totalFilas = document.querySelectorAll('#tablaMotos tbody tr').length;
                contadorBadge.textContent = totalFilas + ' registros';
            }

            // Actualizar totales
            if (typeof window.actualizarTotales === 'function') {
                window.actualizarTotales();
            }

            // Actualizar resumen
            if (typeof window.actualizarResumenClientes === 'function') {
                window.actualizarResumenClientes();
            }
        };

        // Función cancelar edición (CORREGIDA)
        window.cancelarEdicion = function(id) {
            console.log('cancelarEdicion', id);

            const row = document.getElementById(`moto-${id}`);
            if (!row) return;

            // 🔴 VOLVER A MOSTRAR EL FORMULARIO DE AGREGAR MOTO
            const formularioAgregar = document.querySelector('.card.border-success');
            if (formularioAgregar) {
                formularioAgregar.style.display = 'block';
            }

            // Ocultar sección de edición de proveedor
            const proveedorSection = document.getElementById('proveedorSection');
            if (proveedorSection) {
                proveedorSection.style.display = 'none';
                // Limpiar los campos de proveedor
                document.getElementById('proveedor_paga').checked = false;
                document.getElementById('camposProveedor').style.display = 'none';
                document.getElementById('proveedor_codprov').value = '';
                document.getElementById('monto_transporte_proveedor').value = '';

                // 🔴 ELIMINAR EL PREVIEW DE EDICIÓN
                const previewEdit = document.getElementById('previewEdicionProveedor');
                if (previewEdit) {
                    previewEdit.remove();
                }
            }

            // Obtener los valores actuales de los textos
            const clienteActual = row.querySelector('.cliente-text').getAttribute('data-codclie') || '';
            const modeloActual = row.querySelector('.modelo-text').textContent;
            const cantidadActual = row.querySelector('.cantidad-text').textContent;
            const precioActual = row.querySelector('.precio-text').textContent.replace('$', '').replace(',', '');

            // Restaurar valores en los inputs
            const clienteInput = row.querySelector('.cliente-input');
            const modeloInput = row.querySelector('.modelo-input');
            const cantidadInput = row.querySelector('.cantidad-input');
            const precioInput = row.querySelector('.precio-input');

            if (clienteInput) clienteInput.value = clienteActual;
            if (modeloInput) modeloInput.value = modeloActual;
            if (cantidadInput) cantidadInput.value = cantidadActual;
            if (precioInput) precioInput.value = precioActual;

            // Mostrar textos
            row.querySelector('.cliente-text').style.display = 'inline';
            row.querySelector('.modelo-text').style.display = 'inline';
            row.querySelector('.cantidad-text').style.display = 'inline';
            row.querySelector('.precio-text').style.display = 'inline';

            // Ocultar inputs
            if (clienteInput) clienteInput.style.display = 'none';
            if (modeloInput) modeloInput.style.display = 'none';
            if (cantidadInput) cantidadInput.style.display = 'none';
            if (precioInput) precioInput.style.display = 'none';

            // Eliminar contenedor de resultados si existe
            const resultadosDiv = document.getElementById(`resultados-modelos-${id}`);
            if (resultadosDiv) resultadosDiv.remove();

            // Restaurar botones
            row.querySelector('.btn-editar').style.display = 'inline-block';
            row.querySelector('.btn-guardar').style.display = 'none';
            row.querySelector('.btn-cancelar').style.display = 'none';
        };

        // Función eliminar moto
        window.eliminarMoto = function(id) {
            if (!confirm('¿Estás seguro de eliminar esta moto?')) return;

            const viajeId = document.getElementById('viajetalmotos')?.value;

            window.mostrarLoading(true);

            fetch(`/viajes/${viajeId}/motos/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.mostrarToast('Moto eliminada correctamente', 'Éxito', 'success');
                        document.getElementById(`moto-${id}`).remove();
                        window.actualizarTotales();
                        window.actualizarResumenClientes(); // Actualizar resumen

                        if (document.querySelectorAll('#tablaMotos tbody tr').length === 0) {
                            // Si no quedan motos, mostrar mensaje
                            location.reload();
                        }
                    } else {
                        window.mostrarToast('Error al eliminar la moto', 'Error', 'danger');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    window.mostrarToast('Error de conexión', 'Error', 'danger');
                })
                .finally(() => {
                    window.mostrarLoading(false);
                });
        };

        // Función actualizar totales (MODIFICADA)
        window.actualizarTotales = function() {
            console.log('actualizarTotales');

            let totalMotos = 0;
            let totalIngreso = 0;

            document.querySelectorAll('#tablaMotos tbody tr').forEach(row => {
                const cantidad = parseInt(row.querySelector('.cantidad-text')?.textContent) || 0;
                const precioText = row.querySelector('.precio-text')?.textContent.replace('$', '').replace(',', '') || '0';
                const precio = parseFloat(precioText) || 0;

                totalMotos += cantidad;
                totalIngreso += cantidad * precio;
            });

            console.log('📊 Totales calculados - Motos:', totalMotos, 'Ingreso:', totalIngreso);

            // Actualizar el display en el encabezado (totalMotosDisplay)
            const totalMotosDisplay = document.getElementById('totalMotosDisplay');
            if (totalMotosDisplay) {
                totalMotosDisplay.textContent = totalMotos;
            }

            // Actualizar el total en el footer de la tabla (totalMotos y totalIngreso)
            const totalMotosEl = document.getElementById('totalMotos');
            const totalIngresoEl = document.getElementById('totalIngreso');

            if (totalMotosEl) {
                totalMotosEl.textContent = totalMotos + ' motos';
            }
            if (totalIngresoEl) {
                totalIngresoEl.textContent = '$' + totalIngreso.toFixed(2);
            }

            // ✅ ACTUALIZAR EL RESUMEN POR CLIENTE
            window.actualizarResumenClientes();

            // ✅ ACTUALIZAR EL FOOTER DE PROVEEDORES
            window.actualizarFooterProveedores();
        };


    </script>
@endsection
