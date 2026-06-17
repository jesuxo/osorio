@extends('layouts.master')
@section('title')
    Gestión de Camiones
@endsection
@section('css')
    <style>
        .tituloa{
            font-size: 24px;
        }
        .btn-soft-light:hover, .codclieseleted{
            background-color: #e0f2ff !important;
        }
        .nav-pills .nav-link {
            background: #eee !important;
            border-bottom-right-radius: 0 !important;
            border-bottom-left-radius: 0 !important;
        }
        .nav-pills .nav-link.active  {
            background: #0072c5 !important;
            border-bottom-right-radius: 0 !important;
            border-bottom-left-radius: 0 !important;
        }
        .nav-pills{
            border-bottom: 1px solid #0072c5;
        }
        .tdline{
            border:1px solid #0072c5 !important;
            font-size: 12px;
        }
        .tdlineff{
            border-left:1px solid #fff !important;
            font-size: 12px;
            color: white !important;
            background-color: #0072c5 !important;
        }
        .cajapequenacolor {
            margin: 5px;
            padding: 1px;
            float: left;
            width: 120px;
            height: 120px;
            -moz-border-radius: 5px;
            border-radius: 5px;
            color: #fff;
            overflow: hidden;
            background-color: #e0f2ff !important;
        }
        .titulocaja {
            border-radius: 5px;
            min-height: 50px;
            font-size: 18px;
            text-align: center;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .underline{
            text-decoration: underline;
        }
    </style>
@endsection
@section('content')
    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0 text-gray-800">
                    <i class="fa fa-truck me-2"></i>Gestión de Camiones
                </h1>
                <a href="{{ route('camiones.create') }}" class="btn btn-primary">
                    <i class="fa fa-plus-circle me-2"></i>Nuevo Camión
                </a>
            </div>
        </div>

        {{-- Filtros y búsqueda --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Filtros de Búsqueda</h6>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('camiones.index') }}" id="form1" class="row g-3">
                    <div class="col-md-3">
                        <label for="search" class="form-label">Buscar</label>
                        <input type="text" class="form-control" id="search" name="search" onchange="$('#form1').submit()"
                               value="{{ $search ?? '' }}" placeholder="Marca, modelo o placa...">
                    </div>
                    <div class="col-md-3">
                        <label for="tipo" class="form-label">Tipo</label>
                        <select class="form-select" id="tipo" name="tipo" onchange="$('#form1').submit()">
                            <option value="todos" {{ ($tipo ?? '') == 'todos' ? 'selected' : '' }}>Todos</option>
                            <option value="propio" {{ ($tipo ?? '') == 'propio' ? 'selected' : '' }}>Propios</option>
                            <option value="alquilado" {{ ($tipo ?? '') == 'alquilado' ? 'selected' : '' }}>Alquilados</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="estado" class="form-label">Estado</label>
                        <select class="form-select" id="estado" name="estado"  onchange="$('#form1').submit()">
                            <option value="todos"    {{ ($estado ?? '') == 'todos'    ? 'selected' : '' }}>Todos    </option>
                            <option value="activo"   {{ ($estado ?? '') == 'activo'   ? 'selected' : '' }}>Activos  </option>
                            <option value="inactivo" {{ ($estado ?? '') == 'inactivo' ? 'selected' : '' }}>Inactivos</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="trashed" class="form-label">Mostrar</label>
                        <select class="form-select" id="trashed" name="trashed" onchange="$('#form1').submit()">
                            <option value="without" {{ ($trashed ?? 'without') == 'without' ? 'selected' : '' }}>Activos</option>
                             <option value="only" {{ ($trashed ?? '') == 'only' ? 'selected' : '' }}>  Ocultos</option>
                        </select>
                    </div>
                    <div class="col-md-1 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fa fa-search me-2"></i>Filtrar
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Tabla de camiones --}}
        <div class="card shadow mb-4">
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fa fa-check-circle me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fa fa-exclamation-circle me-2"></i>{{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Placa</th>
                            <th>Marca / Modelo</th>
                            <th>Capacidad</th>
                            <th>Tipo</th>
                            <th>Costo Alquiler</th>
                            <th>Estado</th>
                            <th>Viajes</th>
                            <th>Acciones</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($camiones as $camion)
                            <tr>
                                <td>{{ $camion->id }}</td>
                                <td>
                                    <strong>{{ $camion->placa }}</strong>
                                </td>
                                <td>
                                    {{ $camion->marca }} {{ $camion->modelo }}
                                    @if($camion->notas)
                                        <i class="fa fa-info-circle text-info ms-1"
                                           data-bs-toggle="tooltip"
                                           title="{{ $camion->notas }}"></i>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-info">
                                        {{ $camion->capacidad_motos }} motos
                                    </span>
                                </td>
                                <td>
                                    @if($camion->tipo == 'propio')
                                        <span class="badge bg-success">Propio</span>
                                    @else
                                        <span class="badge bg-warning text-dark">Alquilado</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    @if($camion->tipo == 'alquilado')
                                        ${{ number_format($camion->costo_alquiler, 2) }}
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td class="text-center">

                                    @if($camion->trashed())
                                        <span class="badge bg-secondary">Oculto</span>
                                    @else
                                        @if($camion->activo)
                                            <span class="badge bg-success">Activo</span>
                                        @else
                                            <span class="badge bg-danger">Inactivo</span>
                                        @endif
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-secondary">{{ $camion->viajes_count ?? 0 }}</span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        @if($camion->trashed())
                                            {{-- Botones para registros ocultos --}}
                                            <form action="{{ route('camiones.restore', $camion->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-sm btn-success" data-bs-toggle="tooltip" title="Restaurar">
                                                    <i class="mdi mdi-backup-restore"></i>
                                                </button>
                                            </form>

                                            <button type="button"
                                                    class="btn btn-sm btn-danger"
                                                    onclick="confirmForceDelete({{ $camion->id }})"
                                                    data-bs-toggle="tooltip"
                                                    title="Eliminar Permanentemente">
                                                <i class="bi-trash-fill"></i>
                                            </button>
                                        @else

                                            <a href="{{ route('camiones.show', $camion) }}"
                                               class="btn btn-sm btn-info"
                                               data-bs-toggle="tooltip"
                                               title="Ver detalles">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('camiones.edit', $camion) }}"
                                               class="btn btn-sm btn-warning"
                                               data-bs-toggle="tooltip"
                                               title="Editar">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <button type="button"
                                                    class="btn btn-sm {{ $camion->activo ? 'btn-secondary' : 'btn-success' }}"
                                                    onclick="toggleActivo({{ $camion->id }})"
                                                    data-bs-toggle="tooltip"
                                                    title="{{ $camion->activo ? 'Desactivar' : 'Activar' }}">
                                                <i class="bi  {{ $camion->activo ? 'bi-x' : 'bi-check-circle' }}"></i>
                                            </button>
                                            @if($camion->viajes_count == 0)
                                                <button type="button"
                                                        class="btn btn-sm btn-danger"
                                                        onclick="confirmSoftDelete({{ $camion->id }})"
                                                        data-bs-toggle="tooltip"
                                                        title="Ocultar">
                                                    <i class="bi bi-eye-slash"></i>
                                                </button>
                                            @endif

                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4">
                                    <i class="fa fa-truck fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">No hay resultados para los filtros aplicados  </p>
                                    <a href="{{ route('camiones.create') }}" class="btn btn-primary btn-sm">
                                        <i class="fa fa-plus-circle me-2"></i>Agregar nuevo camión
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Paginación --}}
                <div class="d-flex justify-content-end mt-3">
                    {{ $camiones->links() }}
                </div>
            </div>
        </div>
    </div>

    {{-- Formularios ocultos para acciones --}}
    <form id="toggle-activo-form" method="POST" style="display: none;">
        @csrf
        @method('PATCH')
    </form>

    <form id="soft-delete-form" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>

    <form id="force-delete-form" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>
@endsection
@section('scripts')

    <script src="{{ URL::asset('build/js/app.js') }}"></script>
    <script>
        function toggleActivo(camionId) {
            if (confirm('¿Estás seguro de cambiar el estado de este camión?')) {
                const form = document.getElementById('toggle-activo-form');
                form.action = `/camiones/${camionId}/toggle-activo`;
                form.submit();
            }
        }

        function confirmSoftDelete(camionId) {
            if (confirm('¿Estás seguro de ocultar este camión? Podrás restaurarlo después.')) {
                const form = document.getElementById('soft-delete-form');
                form.action = `/camiones/${camionId}`;
                form.submit();
            }
        }

        function confirmForceDelete(camionId) {
            if (confirm('¿Estás seguro de ELIMINAR PERMANENTEMENTE este camión? Esta acción NO se puede deshacer.')) {
                const form = document.getElementById('force-delete-form');
                form.action = `/camiones/${camionId}/force-delete`;
                form.submit();
            }
        }

        // Inicializar tooltips
        document.addEventListener('DOMContentLoaded', function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>
@endsection
