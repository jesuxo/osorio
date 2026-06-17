@extends('layouts.master')
@section('title')
     Detalles del Camión
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
                        <li class="breadcrumb-item"><a href="{{ route('camiones.index') }}">Camiones</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('camiones.show', $camion->id) }}">{{ $camion->placa }}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Editar</li>
                    </ol>
                </nav>
                <h1 class="h3 mb-0 text-gray-800">
                    <i class="fas fa-edit me-2"></i>Editar Camión: {{ $camion->placa }}
                </h1>
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Información del Camión</h6>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('camiones.update', $camion) }}">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        {{-- Marca --}}
                        <div class="col-md-6 mb-3">
                            <label for="marca" class="form-label">Marca <span class="text-danger">*</span></label>
                            <input type="text"
                                   class="form-control @error('marca') is-invalid @enderror"
                                   id="marca"
                                   name="marca"
                                   value="{{ old('marca', $camion->marca) }}"
                                   required>
                            @error('marca')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Modelo --}}
                        <div class="col-md-6 mb-3">
                            <label for="modelo" class="form-label">Modelo <span class="text-danger">*</span></label>
                            <input type="text"
                                   class="form-control @error('modelo') is-invalid @enderror"
                                   id="modelo"
                                   name="modelo"
                                   value="{{ old('modelo', $camion->modelo) }}"
                                   required>
                            @error('modelo')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Placa --}}
                        <div class="col-md-4 mb-3">
                            <label for="placa" class="form-label">Placa <span class="text-danger">*</span></label>
                            <input type="text"
                                   class="form-control @error('placa') is-invalid @enderror"
                                   id="placa"
                                   name="placa"
                                   value="{{ old('placa', $camion->placa) }}"
                                   placeholder="ABC-123"
                                   style="text-transform: uppercase"
                                   required>
                            @error('placa')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Capacidad de Motos --}}
                        <div class="col-md-4 mb-3">
                            <label for="capacidad_motos" class="form-label">Capacidad de Motos <span class="text-danger">*</span></label>
                            <input type="number"
                                   class="form-control @error('capacidad_motos') is-invalid @enderror"
                                   id="capacidad_motos"
                                   name="capacidad_motos"
                                   value="{{ old('capacidad_motos', $camion->capacidad_motos) }}"
                                   min="1"
                                   required>
                            @error('capacidad_motos')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Tipo de Camión --}}
                        <div class="col-md-4 mb-3">
                            <label for="tipo" class="form-label">Tipo de Camión <span class="text-danger">*</span></label>
                            <select class="form-select @error('tipo') is-invalid @enderror"
                                    id="tipo"
                                    name="tipo"
                                    required>
                                <option value="">Seleccione...</option>
                                <option value="propio" {{ old('tipo', $camion->tipo) == 'propio' ? 'selected' : '' }}>Propio</option>
                                <option value="alquilado" {{ old('tipo', $camion->tipo) == 'alquilado' ? 'selected' : '' }}>Alquilado</option>
                            </select>
                            @error('tipo')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>


                        {{-- Estado Activo --}}
                        <div class="col-md-6 mb-3">
                            <div class="form-check mt-4">
                                <input class="form-check-input" type="checkbox" id="activo" name="activo" value="1"
                                    {{ old('activo', $camion->activo) ? 'checked' : '' }}>
                                <label class="form-check-label" for="activo">
                                    Camión activo (disponible para viajes)
                                </label>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3 {{ $camion->tipo != 'alquilado' ? 'd-none' : '' }}" id="costo-alquiler-container">
                            <label for="costo_alquiler" class="form-label">Costo de Alquiler <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number"
                                       class="form-control @error('costo_alquiler') is-invalid @enderror"
                                       id="costo_alquiler"
                                       name="costo_alquiler"
                                       value="{{ old('costo_alquiler', $camion->costo_alquiler) }}"
                                       step="0.01"
                                       min="0"
                                    {{ $camion->tipo == 'alquilado' ? 'required' : '' }}>
                            </div>
                            @error('costo_alquiler')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Notas --}}
                        <div class="col-12 mb-3">
                            <label for="notas" class="form-label">Notas adicionales</label>
                            <textarea class="form-control @error('notas') is-invalid @enderror"
                                      id="notas"
                                      name="notas"
                                      rows="3">{{ old('notas', $camion->notas) }}</textarea>
                            @error('notas')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('camiones.show', $camion) }}" class="btn btn-secondary">
                            <i class="bi bi-x me-2"></i>Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-2"></i>Actualizar Camión
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
        document.addEventListener('DOMContentLoaded', function() {
            const tipoSelect = document.getElementById('tipo');
            const costoContainer = document.getElementById('costo-alquiler-container');
            const costoInput = document.getElementById('costo_alquiler');

            function toggleCostoAlquiler() {
                if (tipoSelect.value === 'alquilado') {
                    costoContainer.classList.remove('d-none');
                    costoInput.required = true;
                } else {
                    costoContainer.classList.add('d-none');
                    costoInput.required = false;
                    costoInput.value = '';
                }
            }

            tipoSelect.addEventListener('change', toggleCostoAlquiler);

            // Convertir placa a mayúsculas
            document.getElementById('placa').addEventListener('input', function(e) {
                this.value = this.value.toUpperCase();
            });
        });
    </script>
@endsection
