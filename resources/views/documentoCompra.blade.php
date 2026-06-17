@extends('layouts.master')
@section('title')
    Compra NRO: {{$numerod}}
@endsection
@section('css')

@endsection
@section('content')
    <div class="row justify-content-center">
        <div class="col-xxl-12">
            <div class="card " id="demo">
                @include('layouts.documentoSacomp')
            </div>
        </div>
    </div>
    <div class="modal fade" id="cambiarStatusModal" tabindex="-1" aria-labelledby="cambiarStatusModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cambiarStatusModalLabel">Cambiar Estatus de Compra</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="/compra/cambiar-status" method="POST">
                    @csrf
                    <input type="hidden" name="id" value="{{ $documento->id }}">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="status" class="form-label">Seleccionar nuevo estatus:</label>
                            <select class="form-select" name="status" id="statuscompra" required>
                                <option value="">-- Seleccione --</option>
                                <option value="0" {{ $documento->status == 0 ? 'selected' : '' }}>Cerrada</option>
                                <option value="1" {{ $documento->status == 1 ? 'selected' : '' }}>Abierta</option>
                                <option value="2" {{ $documento->status == 2 ? 'selected' : '' }}>Pendiente</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="motivo" class="form-label">Motivo del cambio (opcional):</label>
                            <textarea class="form-control" name="motivo" id="motivo" rows="3" placeholder="Ingrese el motivo del cambio..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script src="{{ URL::asset('build/js/app.js') }}"></script>
@endsection
