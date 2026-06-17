{{-- resources/views/viajes/partials/motos-form.blade.php --}}
<div class="container-fluid">
    <form id="formAgregarMotos" onsubmit="guardarMotos(this)" action="{{ route('viajes.motos.store', $viaje) }}" method="POST">
        @csrf

        <div class="alert alert-info">
            <i class=" bi bi-info-circle me-2"></i>
            Agrega las motos transportadas en este viaje. Puedes agregar varias motos del mismo modelo.
        </div>

        <div id="motos-container">
            {{-- Un campo inicial vacío --}}
            <div class="row mb-2 moto-item">
                <div class="col-md-5">
                    <input type="text" class="form-control" name="motos[0][modelo_moto]" placeholder="Modelo de moto" required>
                </div>
                <div class="col-md-2">
                    <input type="number" class="form-control cantidad-moto" name="motos[0][cantidad]" placeholder="Cantidad" min="1" required>
                </div>
                <div class="col-md-3">
                    <input type="number" class="form-control precio-moto" name="motos[0][precio_por_moto]" placeholder="Precio por moto" step="0.01" min="0" required>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-danger btn-sm" onclick="eliminarCampoMoto(this)" style="display: none;">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
        </div>

        <div class="text-center mt-3">
            <button type="button" class="btn btn-outline-success" onclick="agregarCampoMoto()">
                <i class="bi bi-plus-circle me-2"></i>Agregar Otra Moto
            </button>
        </div>

        <div class="alert alert-light mt-3" id="resumenMotos">
            <h6 class="mb-2">Resumen:</h6>
            <div class="row">
                <div class="col-md-6">
                    <strong>Total Motos:</strong> <span id="totalMotosCount">0</span>
                </div>
                <div class="col-md-6">
                    <strong>Ingreso Total:</strong> $<span id="totalIngreso">0.00</span>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-12 d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x me-2"></i>Cancelar
                </button>
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-save me-2"></i>Guardar Motos
                </button>
            </div>
        </div>
    </form>
</div>

<script>
    function agregarCampoMoto() {
        const container = document.getElementById('motos-container');
        const index = container.children.length;
        const html = `
            <div class="row mb-2 moto-item">
                <div class="col-md-5">
                    <input type="text" class="form-control" name="motos[${index}][modelo_moto]" placeholder="Modelo de moto" required>
                </div>
                <div class="col-md-2">
                    <input type="number" class="form-control cantidad-moto" name="motos[${index}][cantidad]" placeholder="Cantidad" min="1" required>
                </div>
                <div class="col-md-3">
                    <input type="number" class="form-control precio-moto" name="motos[${index}][precio_por_moto]" placeholder="Precio por moto" step="0.01" min="0" required>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-danger btn-sm" onclick="eliminarCampoMoto(this)">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', html);

        // Mostrar botón eliminar en el primer campo si hay más de uno
        if (container.children.length > 1) {
            const firstItem = container.children[0];
            const deleteBtn = firstItem.querySelector('button');
            if (deleteBtn) {
                deleteBtn.style.display = 'block';
            }
        }

        actualizarResumenMotos();
    }

    function eliminarCampoMoto(btn) {
        if (confirm('¿Eliminar esta moto?')) {
            const container = document.getElementById('motos-container');
            btn.closest('.moto-item').remove();

            // Ocultar botón eliminar del primer campo si solo queda uno
            if (container.children.length === 1) {
                const firstItem = container.children[0];
                const deleteBtn = firstItem.querySelector('button');
                if (deleteBtn) {
                    deleteBtn.style.display = 'none';
                }
            }

            actualizarResumenMotos();
        }
    }

    function actualizarResumenMotos() {
        let totalMotos = 0;
        let totalIngreso = 0;

        document.querySelectorAll('.moto-item').forEach(item => {
            const cantidad = parseInt(item.querySelector('.cantidad-moto')?.value) || 0;
            const precio = parseFloat(item.querySelector('.precio-moto')?.value) || 0;

            totalMotos += cantidad;
            totalIngreso += cantidad * precio;
        });

        document.getElementById('totalMotosCount').textContent = totalMotos;
        document.getElementById('totalIngreso').textContent = totalIngreso.toFixed(2);
    }

    // Actualizar resumen en tiempo real
    document.addEventListener('input', function(e) {
        if (e.target.matches('.cantidad-moto') || e.target.matches('.precio-moto')) {
            actualizarResumenMotos();
        }
    });
</script>
