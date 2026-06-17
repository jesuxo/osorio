{{-- resources/views/viajes/partials/gastos-form.blade.php --}}
 <div class="container-fluid">
    <form id="formAgregarGasto" onsubmit="guardarGasto(this)" action="{{ route('viajes.gastos.store', $viaje) }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="row">
            <div class="col-md-12 mb-3">
                <label class="form-label">Tipo de Gasto <span class="text-danger">*</span></label>
                <select class="form-select" name="tipo_gasto_id" required>
                    <option value="">Seleccione tipo de gasto</option>
                    @ foreach($tiposGasto as $tipo)
                        <option value="{{ $tipo->id }}">{{ $tipo->nombre }}</option>
                    @ endforeach
                </select>
            </div>

            <div class="col-md-12 mb-3">
                <label class="form-label">Concepto <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="concepto" placeholder="Ej: Combustible, Peaje, Comida..." required>
            </div>

            <div class="col-md-12 mb-3">
                <label class="form-label">Descripción</label>
                <textarea class="form-control" name="descripcion" rows="2" placeholder="Detalles adicionales del gasto..."></textarea>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Monto <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text">$</span>
                    <input type="number" class="form-control" name="monto" step="0.01" min="0" required>
                </div>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Fecha del Gasto <span class="text-danger">*</span></label>
                <input type="date" class="form-control" name="fecha_gasto" value="{{ date('Y-m-d') }}" required>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Proveedor</label>
                <input type="text" class="form-control" name="proveedor" placeholder="Nombre del proveedor">
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Método de Pago</label>
                <select class="form-select" name="metodo_pago">
                    <option value="">Seleccione método</option>
                    <option value="efectivo">Efectivo</option>
                    <option value="transferencia">Transferencia</option>
                    <option value="tarjeta_credito">Tarjeta de Crédito</option>
                    <option value="tarjeta_debito">Tarjeta de Débito</option>
                    <option value="cheque">Cheque</option>
                </select>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Referencia de Pago</label>
                <input type="text" class="form-control" name="referencia_pago" placeholder="N° de transferencia, cheque, etc.">
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Comprobante</label>
                <input type="file" class="form-control" name="comprobante" accept=".pdf,.jpg,.jpeg,.png">
                <small class="text-muted">PDF, JPG o PNG (Max. 5MB)</small>
            </div>

            <div class="col-md-12 mb-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="deducible_impuestos" value="1" checked>
                    <label class="form-check-label">
                        Deducible de impuestos
                    </label>
                </div>
            </div>

            <div class="col-md-12 mb-3">
                <label class="form-label">Notas Internas</label>
                <textarea class="form-control" name="notas_internas" rows="2" placeholder="Notas solo para uso interno..."></textarea>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-12 d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x me-2"></i>Cancelar
                </button>
                <button type="submit" class="btn btn-warning">
                    <i class="bi bi-save me-2"></i>Registrar Gasto
                </button>
            </div>
        </div>
    </form>
</div>


