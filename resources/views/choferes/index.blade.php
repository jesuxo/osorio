@extends('layouts.master')
@section('title')
    Gestión de Choferes
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
                    <i class="fas fa-users me-2"></i>Gestión de Choferes
                </h1>
                <a href="{{ route('choferes.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-2"></i>Nuevo Chofer
                </a>
            </div>
        </div>

        <div class="row row-cols-xxl-4 row-cols-1">
            <div class="col">

                <div class="card card-animate">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div class="vr rounded bg-secondary opacity-50" style="width: 4px;"></div>
                            <div class="flex-grow-1 ms-3">
                                <p class="text-uppercase fw-medium text-muted fs-14 text-truncate">Total Choferes</p>
                                <h4 class="fs-22 fw-semibold mb-3"> {{ $estadisticas['total'] }}</h4>

                            </div>
                            <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title bg-secondary-subtle text-secondary rounded fs-3">
                                        <i class="ph-user-circle"></i>
                                    </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col">

                <div class="card card-animate">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div class="vr rounded bg-info opacity-50" style="width: 4px;"></div>
                            <div class="flex-grow-1 ms-3">
                                <p class="text-uppercase fw-medium text-muted fs-14 text-truncate">Activos</p>
                                <h4 class="fs-22 fw-semibold mb-3">  {{ $estadisticas['activos'] }} </h4>

                            </div>
                            <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title bg-info-subtle text-info rounded fs-3">
                                        <i class="ph-user-circle"></i>
                                    </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col">

                <div class="card card-animate">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div class="vr rounded bg-warning opacity-50" style="width: 4px;"></div>
                            <div class="flex-grow-1 ms-3">
                                <p class="text-uppercase fw-medium text-muted fs-14 text-truncate">Inactivos</p>
                                <h4 class="fs-22 fw-semibold mb-3">  {{ $estadisticas['inactivos'] }}</h4>

                            </div>
                            <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title bg-warning-subtle text-warning rounded fs-3">
                                        <i class="ph-user-circle"></i>
                                    </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col">

                <div class="card card-animate">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div class="vr rounded bg-primary opacity-50" style="width: 4px;"></div>
                            <div class="flex-grow-1 ms-3">
                                <p class="text-uppercase fw-medium text-muted fs-14 text-truncate">Ocultos</p>
                                <h4 class="fs-22 fw-semibold mb-3">{{ $estadisticas['ocultos'] }} </h4>

                            </div>
                            <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title bg-primary-subtle text-primary rounded fs-3">
                                        <i class="ph-user-circle"></i>
                                    </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Filtros de Búsqueda</h6>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('choferes.index') }}" id="form1" class="row g-3">
                    <input type="hidden" name="trashed" value="{{ $trashed }}">

                    <div class="col-md-6">
                        <label for="search" class="form-label">Buscar</label>
                        <input type="text" class="form-control" id="search" name="search"  onchange="$('#form1').submit()"
                               value="{{ $search ?? '' }}" placeholder="Nombre, licencia, teléfono o email...">
                    </div>
                    <div class="col-md-2">
                        <label for="estado" class="form-label">Estado</label>
                        <select class="form-select" id="estado" name="estado"  onchange="$('#form1').submit()">
                            <option value="todos"    {{ ($estado ?? '') == 'todos' ? 'selected'    : '' }}>Todos    </option>
                            <option value="activo"   {{ ($estado ?? '') == 'activo' ? 'selected'   : '' }}>Activos  </option>
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
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search me-2"></i>Filtrar
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Tabla de choferes --}}
        <div class="card shadow mb-4">
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Foto</th>
                            <th>Nombre Completo</th>
                            <th>Licencia</th>
                            <th>Teléfono</th>
                            <th>Email</th>
                            <th>Estado</th>
                            <th>Viajes</th>
                            <th>Acciones</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($choferes as $chofer)
                            <tr class="{{ $chofer->trashed() ? 'table-secondary' : '' }}">
                                <td>{{ $chofer->id }}</td>
                                <td class="text-center">
                                    @if($chofer->foto)
                                        <img src="{{ $chofer->foto_url }}" alt="Foto" class="rounded-circle" width="40" height="40" style="object-fit: cover;">
                                    @else
                                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                            {{ $chofer->iniciales }}
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ $chofer->nombre_completo }}</strong>
                                    @if($chofer->edad)
                                        <br><small class="text-muted">{{ $chofer->edad }} años</small>
                                    @endif
                                </td>
                                <td>{{ $chofer->licencia }}</td>
                                <td>{{ $chofer->telefono ?? 'N/A' }}</td>
                                <td>{{ $chofer->email ?? 'N/A' }}</td>
                                <td>
                                    @if($chofer->trashed())
                                        <span class="badge bg-secondary">Oculto</span>
                                    @else
                                        {!! $chofer->estado_badge !!}
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-info">{{ $chofer->viajes()->count() }}</span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        @if($chofer->trashed())
                                            {{-- Botones para registros ocultos --}}
                                            <form action="{{ route('choferes.restore', $chofer->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-sm btn-success" data-bs-toggle="tooltip" title="Restaurar">
                                                    <i class="mdi mdi-backup-restore"></i>
                                                </button>
                                            </form>

                                            <button type="button"
                                                    class="btn btn-sm btn-danger"
                                                    onclick="confirmForceDelete({{ $chofer->id }})"
                                                    data-bs-toggle="tooltip"
                                                    title="Eliminar Permanentemente">
                                                <i class="bi-trash-fill"></i>
                                            </button>
                                        @else
                                            {{-- Botones para registros activos --}}
                                            <a href="{{ route('choferes.show', $chofer) }}"
                                               class="btn btn-sm btn-info"
                                               data-bs-toggle="tooltip"
                                               title="Ver detalles">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('choferes.edit', $chofer) }}"
                                               class="btn btn-sm btn-warning"
                                               data-bs-toggle="tooltip"
                                               title="Editar">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <button type="button"
                                                    class="btn btn-sm {{ $chofer->activo ? 'btn-secondary' : 'btn-success' }}"
                                                    onclick="toggleActivo({{ $chofer->id }})"
                                                    data-bs-toggle="tooltip"
                                                    title="{{ $chofer->activo ? 'Desactivar' : 'Activar' }}">
                                                <i class="bi  {{ $chofer->activo ? 'bi-x' : 'bi-check-circle' }}"></i>
                                            </button>
                                            <button type="button"
                                                    class="btn btn-sm btn-danger"
                                                    onclick="confirmSoftDelete({{ $chofer->id }})"
                                                    data-bs-toggle="tooltip"
                                                    title="Ocultar">
                                                <i class="bi bi-eye-slash"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4">
                                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">No hay choferes registrados</p>
                                    <a href="{{ route('choferes.create') }}" class="btn btn-primary btn-sm">
                                        <i class="bi bi-plus-circle me-2"></i>Registrar primer chofer
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Paginación --}}
                <div class="d-flex justify-content-end mt-3">
                    {{ $choferes->links() }}
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
        function toggleActivo(choferId) {
            if (confirm('¿Estás seguro de cambiar el estado de este chofer?')) {
                const form = document.getElementById('toggle-activo-form');
                form.action = `/choferes/${choferId}/toggle-activo`;
                form.submit();
            }
        }

        function confirmSoftDelete(choferId) {
            if (confirm('¿Estás seguro de ocultar este chofer? Podrás restaurarlo después.')) {
                const form = document.getElementById('soft-delete-form');
                form.action = `/choferes/${choferId}`;
                form.submit();
            }
        }

        function confirmForceDelete(choferId) {
            if (confirm('¿Estás seguro de ELIMINAR PERMANENTEMENTE este chofer? Esta acción NO se puede deshacer.')) {
                const form = document.getElementById('force-delete-form');
                form.action = `/choferes/${choferId}/force-delete`;
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
