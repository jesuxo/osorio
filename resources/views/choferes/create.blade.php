@extends('layouts.master')
@section('title')
    Registrar Nuevo Chofer
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
                        <li class="breadcrumb-item active" aria-current="page">Nuevo Chofer</li>
                    </ol>
                </nav>
                <h1 class="h3 mb-0 text-gray-800">
                    <i class="fas fa-user-plus me-2"></i>Registrar Nuevo Chofer
                </h1>
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Información Personal</h6>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('choferes.store') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="row">
                        {{-- Foto --}}
                        <div class="col-12 mb-4 text-center">
                            <div class="foto-preview mb-3">
                                <div class="rounded-circle bg-light d-flex align-items-center justify-content-center mx-auto"
                                     style="width: 120px; height: 120px; border: 2px dashed #ccc; cursor: pointer;"
                                     onclick="document.getElementById('foto').click()">
                                    <i class="fas fa-camera fa-3x text-muted"></i>
                                </div>
                            </div>
                            <input type="file" class="d-none" id="foto" name="foto" accept="image/*" onchange="previewFoto(this)">
                            <small class="text-muted d-block">Haz clic para subir una foto (opcional)</small>
                        </div>

                        {{-- Nombre --}}
                        <div class="col-md-6 mb-3">
                            <label for="nombre" class="form-label">Nombre <span class="text-danger">*</span></label>
                            <input type="text"
                                   class="form-control @error('nombre') is-invalid @enderror"
                                   id="nombre"
                                   name="nombre"
                                   value="{{ old('nombre') }}"
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
                                   value="{{ old('apellido') }}"
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
                                   value="{{ old('licencia') }}"
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
                                   value="{{ old('telefono') }}"
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
                                   value="{{ old('email') }}"
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
                                   value="{{ old('fecha_nacimiento') }}">
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
                                   value="{{ old('fecha_ingreso', date('Y-m-d')) }}">
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
                                <option value="A+" {{ old('tipo_sangre') == 'A+' ? 'selected' : '' }}>A+</option>
                                <option value="A-" {{ old('tipo_sangre') == 'A-' ? 'selected' : '' }}>A-</option>
                                <option value="B+" {{ old('tipo_sangre') == 'B+' ? 'selected' : '' }}>B+</option>
                                <option value="B-" {{ old('tipo_sangre') == 'B-' ? 'selected' : '' }}>B-</option>
                                <option value="AB+" {{ old('tipo_sangre') == 'AB+' ? 'selected' : '' }}>AB+</option>
                                <option value="AB-" {{ old('tipo_sangre') == 'AB-' ? 'selected' : '' }}>AB-</option>
                                <option value="O+" {{ old('tipo_sangre') == 'O+' ? 'selected' : '' }}>O+</option>
                                <option value="O-" {{ old('tipo_sangre') == 'O-' ? 'selected' : '' }}>O-</option>
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
                                      rows="2">{{ old('direccion') }}</textarea>
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
                                   value="{{ old('contacto_emergencia_nombre') }}">
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
                                   value="{{ old('contacto_emergencia_telefono') }}">
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
                                      rows="3">{{ old('observaciones_medicas') }}</textarea>
                            <small class="text-muted">Alergias, condiciones médicas, medicamentos, etc.</small>
                            @error('observaciones_medicas')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Estado Activo --}}
                        <div class="col-12 mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="activo" name="activo" value="1" checked>
                                <label class="form-check-label" for="activo">
                                    Chofer activo (disponible para viajes)
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('choferes.index') }}" class="btn btn-secondary">
                            <i class="bi bi-x me-2"></i>Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-2"></i>Guardar Chofer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('scripts')

    <script src="{{ URL::asset('build/js/app.js') }}"></script>
    <script>
        function previewFoto(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    var preview = document.querySelector('.foto-preview div');
                    preview.innerHTML = '<img src="' + e.target.result + '" class="rounded-circle" style="width: 120px; height: 120px; object-fit: cover;">';
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
@endsection
