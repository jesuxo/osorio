@extends('layouts.master')
@section('title')
     Editar Chofer
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
            <div class="col-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('choferes.index') }}">Choferes</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('choferes.show', $chofer) }}">{{ $chofer->nombre_completo }}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Editar</li>
                    </ol>
                </nav>
                <h1 class="h3 mb-0 text-gray-800">
                    <i class="fas fa-user-edit me-2"></i>Editar Chofer: {{ $chofer->nombre_completo }}
                </h1>
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Información Personal</h6>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('choferes.update', $chofer) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        {{-- Foto --}}
                        <div class="col-12 mb-4 text-center">
                            <div class="foto-preview mb-3">
                                <div class="rounded-circle bg-light d-flex align-items-center justify-content-center mx-auto"
                                     style="width: 120px; height: 120px; border: 2px dashed #ccc; cursor: pointer; position: relative; overflow: hidden;"
                                     onclick="document.getElementById('foto').click()">

                                    @if($chofer->foto)
                                        <img src="{{ $chofer->foto_url }}" alt="Foto actual"
                                             style="width: 100%; height: 100%; object-fit: cover; position: absolute; top: 0; left: 0;">
                                        <div class="overlay" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; opacity: 0; transition: opacity 0.3s;">
                                            <i class="fas fa-camera text-white fa-2x"></i>
                                        </div>
                                    @else
                                        <i class="fas fa-camera fa-3x text-muted"></i>
                                    @endif
                                </div>
                            </div>
                            <input type="file" class="d-none" id="foto" name="foto" accept="image/*" onchange="previewFoto(this)">

                            @if($chofer->foto)
                                <small class="text-muted d-block">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Haz clic para cambiar la foto.
                                    <button type="button" class="btn btn-link btn-sm text-danger p-0 ms-2" onclick="confirmarEliminarFoto()">
                                        <i class="bi bi-trash"></i> Eliminar foto actual
                                    </button>
                                </small>
                                <input type="hidden" name="eliminar_foto" id="eliminar_foto" value="0">
                            @else
                                <small class="text-muted d-block">Haz clic para subir una foto (opcional)</small>
                            @endif

                            @error('foto')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Nombre --}}
                        <div class="col-md-6 mb-3">
                            <label for="nombre" class="form-label">Nombre <span class="text-danger">*</span></label>
                            <input type="text"
                                   class="form-control @error('nombre') is-invalid @enderror"
                                   id="nombre"
                                   name="nombre"
                                   value="{{ old('nombre', $chofer->nombre) }}"
                                   required>
                            @error('nombre')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Apellido --}}
                        <div class="col-md-6 mb-3">
                            <label for="apellido" class="form-label">Apellido <span class="text-danger">*</span></label>
                            <input type="text"
                                   class="form-control @error('apellido') is-invalid @enderror"
                                   id="apellido"
                                   name="apellido"
                                   value="{{ old('apellido', $chofer->apellido) }}"
                                   required>
                            @error('apellido')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Licencia --}}
                        <div class="col-md-4 mb-3">
                            <label for="licencia" class="form-label">Licencia <span class="text-danger">*</span></label>
                            <input type="text"
                                   class="form-control @error('licencia') is-invalid @enderror"
                                   id="licencia"
                                   name="licencia"
                                   value="{{ old('licencia', $chofer->licencia) }}"
                                   placeholder="Ej: LIC-123456"
                                   required>
                            @error('licencia')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Teléfono --}}
                        <div class="col-md-4 mb-3">
                            <label for="telefono" class="form-label">Teléfono</label>
                            <input type="text"
                                   class="form-control @error('telefono') is-invalid @enderror"
                                   id="telefono"
                                   name="telefono"
                                   value="{{ old('telefono', $chofer->telefono) }}"
                                   placeholder="Ej: 1234-5678">
                            @error('telefono')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Email --}}
                        <div class="col-md-4 mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   id="email"
                                   name="email"
                                   value="{{ old('email', $chofer->email) }}"
                                   placeholder="correo@ejemplo.com">
                            @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Fecha de Nacimiento --}}
                        <div class="col-md-4 mb-3">
                            <label for="fecha_nacimiento" class="form-label">Fecha de Nacimiento</label>
                            <input type="date"
                                   class="form-control @error('fecha_nacimiento') is-invalid @enderror"
                                   id="fecha_nacimiento"
                                   name="fecha_nacimiento"
                                   value="{{ old('fecha_nacimiento', $chofer->fecha_nacimiento?->format('Y-m-d')) }}">
                            @error('fecha_nacimiento')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Fecha de Ingreso --}}
                        <div class="col-md-4 mb-3">
                            <label for="fecha_ingreso" class="form-label">Fecha de Ingreso</label>
                            <input type="date"
                                   class="form-control @error('fecha_ingreso') is-invalid @enderror"
                                   id="fecha_ingreso"
                                   name="fecha_ingreso"
                                   value="{{ old('fecha_ingreso', $chofer->fecha_ingreso?->format('Y-m-d')) }}">
                            @error('fecha_ingreso')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Tipo de Sangre --}}
                        <div class="col-md-4 mb-3">
                            <label for="tipo_sangre" class="form-label">Tipo de Sangre</label>
                            <select class="form-select @error('tipo_sangre') is-invalid @enderror"
                                    id="tipo_sangre"
                                    name="tipo_sangre">
                                <option value="">Seleccione...</option>
                                <option value="A+" {{ old('tipo_sangre', $chofer->tipo_sangre) == 'A+' ? 'selected' : '' }}>A+</option>
                                <option value="A-" {{ old('tipo_sangre', $chofer->tipo_sangre) == 'A-' ? 'selected' : '' }}>A-</option>
                                <option value="B+" {{ old('tipo_sangre', $chofer->tipo_sangre) == 'B+' ? 'selected' : '' }}>B+</option>
                                <option value="B-" {{ old('tipo_sangre', $chofer->tipo_sangre) == 'B-' ? 'selected' : '' }}>B-</option>
                                <option value="AB+" {{ old('tipo_sangre', $chofer->tipo_sangre) == 'AB+' ? 'selected' : '' }}>AB+</option>
                                <option value="AB-" {{ old('tipo_sangre', $chofer->tipo_sangre) == 'AB-' ? 'selected' : '' }}>AB-</option>
                                <option value="O+" {{ old('tipo_sangre', $chofer->tipo_sangre) == 'O+' ? 'selected' : '' }}>O+</option>
                                <option value="O-" {{ old('tipo_sangre', $chofer->tipo_sangre) == 'O-' ? 'selected' : '' }}>O-</option>
                            </select>
                            @error('tipo_sangre')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Dirección --}}
                        <div class="col-12 mb-3">
                            <label for="direccion" class="form-label">Dirección</label>
                            <textarea class="form-control @error('direccion') is-invalid @enderror"
                                      id="direccion"
                                      name="direccion"
                                      rows="2">{{ old('direccion', $chofer->direccion) }}</textarea>
                            @error('direccion')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <hr>
                            <h5 class="mb-3">Contacto de Emergencia</h5>
                        </div>

                        {{-- Contacto Emergencia Nombre --}}
                        <div class="col-md-6 mb-3">
                            <label for="contacto_emergencia_nombre" class="form-label">Nombre del Contacto</label>
                            <input type="text"
                                   class="form-control @error('contacto_emergencia_nombre') is-invalid @enderror"
                                   id="contacto_emergencia_nombre"
                                   name="contacto_emergencia_nombre"
                                   value="{{ old('contacto_emergencia_nombre', $chofer->contacto_emergencia_nombre) }}">
                            @error('contacto_emergencia_nombre')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Contacto Emergencia Teléfono --}}
                        <div class="col-md-6 mb-3">
                            <label for="contacto_emergencia_telefono" class="form-label">Teléfono de Emergencia</label>
                            <input type="text"
                                   class="form-control @error('contacto_emergencia_telefono') is-invalid @enderror"
                                   id="contacto_emergencia_telefono"
                                   name="contacto_emergencia_telefono"
                                   value="{{ old('contacto_emergencia_telefono', $chofer->contacto_emergencia_telefono) }}">
                            @error('contacto_emergencia_telefono')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Observaciones Médicas --}}
                        <div class="col-12 mb-3">
                            <label for="observaciones_medicas" class="form-label">Observaciones Médicas</label>
                            <textarea class="form-control @error('observaciones_medicas') is-invalid @enderror"
                                      id="observaciones_medicas"
                                      name="observaciones_medicas"
                                      rows="3">{{ old('observaciones_medicas', $chofer->observaciones_medicas) }}</textarea>
                            <small class="text-muted">Alergias, condiciones médicas, medicamentos, etc.</small>
                            @error('observaciones_medicas')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Estado Activo --}}
                        <div class="col-12 mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="activo" name="activo" value="1"
                                    {{ old('activo', $chofer->activo) ? 'checked' : '' }}>
                                <label class="form-check-label" for="activo">
                                    Chofer activo (disponible para viajes)
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('choferes.show', $chofer) }}" class="btn btn-secondary">
                            <i class="bi bi-x me-2"></i>Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-2"></i>Actualizar Chofer
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Sección de Información Adicional (Viajes Recientes) --}}
        @if($chofer->viajes()->count() > 0)
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Viajes Recientes</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                            <tr>
                                <th>Folio</th>
                                <th>Fecha</th>
                                <th>Ruta</th>
                                <th>Camion</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($chofer->viajes()->latest()->limit(5)->get() as $viaje)
                                <tr>
                                    <td>{{ $viaje->folio ?? 'N/A' }}</td>
                                    <td>{{ $viaje->fecha_inicio->format('d/m/Y') }}</td>
                                    <td>{{ $viaje->origen }} → {{ $viaje->destino }}</td>
                                    <td>{{ $viaje->camion->placa ?? 'N/A' }}</td>
                                    <td>
                                        @switch($viaje->estado)
                                            @case('completado')
                                                <span class="badge bg-success">Completado</span>
                                                @break
                                            @case('en_curso')
                                                <span class="badge bg-warning text-dark">En Curso</span>
                                                @break
                                            @case('planeado')
                                                <span class="badge bg-info">Planeado</span>
                                                @break
                                            @case('cancelado')
                                                <span class="badge bg-danger">Cancelado</span>
                                                @break
                                        @endswitch
                                    </td>
                                    <td>
                                        <a href="{{ route('viajes.show', $viaje) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
@section('scripts')

    <script src="{{ URL::asset('build/js/app.js') }}"></script>
    <script>
        function previewFoto(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    var previewDiv = document.querySelector('.foto-preview div');
                    // Limpiar el contenido actual
                    previewDiv.innerHTML = '';
                    previewDiv.style.backgroundImage = 'none';

                    // Crear la nueva imagen
                    var img = document.createElement('img');
                    img.src = e.target.result;
                    img.style.width = '100%';
                    img.style.height = '100%';
                    img.style.objectFit = 'cover';
                    img.style.position = 'absolute';
                    img.style.top = '0';
                    img.style.left = '0';

                    // Crear el overlay
                    var overlay = document.createElement('div');
                    overlay.className = 'overlay';
                    overlay.style.position = 'absolute';
                    overlay.style.top = '0';
                    overlay.style.left = '0';
                    overlay.style.width = '100%';
                    overlay.style.height = '100%';
                    overlay.style.background = 'rgba(0,0,0,0.5)';
                    overlay.style.display = 'flex';
                    overlay.style.alignItems = 'center';
                    overlay.style.justifyContent = 'center';
                    overlay.style.opacity = '0';
                    overlay.style.transition = 'opacity 0.3s';
                    overlay.innerHTML = '<i class="fas fa-camera text-white fa-2x"></i>';

                    // Agregar hover effect
                    previewDiv.onmouseenter = function() {
                        overlay.style.opacity = '1';
                    };
                    previewDiv.onmouseleave = function() {
                        overlay.style.opacity = '0';
                    };

                    previewDiv.appendChild(img);
                    previewDiv.appendChild(overlay);

                    // Resetear el flag de eliminar foto
                    document.getElementById('eliminar_foto').value = '0';
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        function confirmarEliminarFoto() {
            if (confirm('¿Estás seguro de eliminar la foto actual?')) {
                document.getElementById('eliminar_foto').value = '1';

                // Actualizar la vista previa
                var previewDiv = document.querySelector('.foto-preview div');
                previewDiv.innerHTML = '<i class="fas fa-camera fa-3x text-muted"></i>';

                // Resetear el input file
                document.getElementById('foto').value = '';
            }
        }

        // Hover effect para la foto actual
        document.addEventListener('DOMContentLoaded', function() {
            var previewDiv = document.querySelector('.foto-preview div');
            if (previewDiv) {
                var overlay = previewDiv.querySelector('.overlay');
                if (overlay) {
                    previewDiv.onmouseenter = function() {
                        overlay.style.opacity = '1';
                    };
                    previewDiv.onmouseleave = function() {
                        overlay.style.opacity = '0';
                    };
                }
            }
        });
    </script>
@endsection
