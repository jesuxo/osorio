// Inicializar Choices para sucursal
document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('fksucursal')) {
        var sucursalSelect = new Choices('#fksucursal', {
            searchEnabled: false,
            shouldSort: false,
        });
    }

    // Validación en tiempo real para el monto (solo números y punto decimal)
    const montoInput = document.getElementById('monto');
    if (montoInput) {
        montoInput.addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9.]/g, '');
        });
    }

    // Limpiar mensajes de error cuando el usuario empieza a escribir
    const numeroInput = document.getElementById('numero');
    if (numeroInput) {
        numeroInput.addEventListener('input', function() {
            document.querySelector('.alertatransferencia').innerHTML = '';
            this.classList.remove('is-invalid');
        });
    }

    const titularInput = document.getElementById('titular');
    if (titularInput) {
        titularInput.addEventListener('input', function() {
            document.querySelector('.alertatransferencia').innerHTML = '';
        });
    }
});

// Variable para el select de bancos (se inicializará después de cargar)
var bancoChoices = null;

// Función para inicializar Choices en el select de bancos después de cargarlo
function inicializarBancoChoices() {
    if (document.getElementById('bancosucursal')) {
        // Si ya existe una instancia, destrúyela
        if (bancoChoices && typeof bancoChoices.destroy === 'function') {
            bancoChoices.destroy();
        }

        bancoChoices = new Choices('#bancosucursal', {
            searchEnabled: true,
            shouldSort: false,
            placeholder: true,
            placeholderValue: 'Seleccione un banco',
            itemSelectText: ''
        });
    }
}

// Función para cargar bancos por sucursal
window.bancoSucursal = function(fksucursal) {
    const bancosucursaldiv = document.getElementById('bancosucursaldiv');
    if (!bancosucursaldiv) return;

    bancosucursaldiv.innerHTML = '<div class="text-center"><span class="spinner-border spinner-border-sm"></span> Cargando bancos...</div>';

    fetch('/sascursal/bancos', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ fksucursal: fksucursal })
    })
        .then(response => response.text())
        .then(html => {
            bancosucursaldiv.innerHTML = html;
            inicializarBancoChoices();
        })
        .catch(error => {
            console.error('Error:', error);
            bancosucursaldiv.innerHTML = '<div class="alert alert-danger">Error al cargar bancos</div>';
        });
};

// Validación del formulario
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('createtransf-form');
    if (!form) return;

    form.addEventListener('submit', function(event) {
        event.preventDefault();

        // Validar campos requeridos
        if (!form.checkValidity()) {
            event.stopPropagation();
            form.classList.add('was-validated');
            return false;
        }

        // Obtener valores
        var fksucursal = document.getElementById('fksucursal') ? document.getElementById('fksucursal').value : '';
        var fkbanco = document.getElementById('bancosucursal') ? document.getElementById('bancosucursal').value : '';
        var monto = document.getElementById('monto') ? document.getElementById('monto').value : '';
        var fecha = document.getElementById('fecha') ? document.getElementById('fecha').value : '';
        var numero = document.getElementById('numero') ? document.getElementById('numero').value : '';
        var titular = document.getElementById('titular') ? document.getElementById('titular').value : '';
        var imagen = document.getElementById('imagen') ? document.getElementById('imagen').files[0] : null;

        var alertaElement = document.querySelector('.alertatransferencia');

        // Validaciones adicionales
        if (!fksucursal) {
            alertaElement.innerHTML = 'Debe seleccionar una sucursal';
            return false;
        }

        if (!fkbanco) {
            alertaElement.innerHTML = 'Debe seleccionar un banco';
            return false;
        }

        if (!numero) {
            alertaElement.innerHTML = 'Debe ingresar el número de transferencia';
            return false;
        }

        if (!monto || parseFloat(monto) <= 0) {
            alertaElement.innerHTML = 'Debe ingresar un monto válido';
            return false;
        }

        if (!fecha) {
            alertaElement.innerHTML = 'Debe ingresar la fecha';
            return false;
        }

        if (!titular) {
            alertaElement.innerHTML = 'Debe ingresar el titular';
            return false;
        }

        // Validar tamaño de imagen si se seleccionó una
        if (imagen && imagen.size > 5 * 1024 * 1024) {
            alertaElement.innerHTML = 'La imagen no puede superar los 5MB';
            return false;
        }

        // Mostrar mensaje de carga
        alertaElement.innerHTML = '<span class="text-info"><i class="ri-loader-4-line ri-spin"></i> Verificando transferencia...</span>';

        // Verificar si la transferencia ya existe
        fetch('/transferencias/verificar', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                fkbanco: fkbanco,
                monto: monto,
                fecha: fecha,
                numero: numero
            })
        })
            .then(response => response.json())
            .then(data => {
                if (data.valid) {
                    // Si es válida, enviar el formulario
                    form.submit();
                } else {
                    alertaElement.innerHTML = '<span class="text-danger"><i class="ri-error-warning-line"></i> ERROR: La transferencia N° ' + numero + ' ya existe</span>';
                    document.getElementById('numero').focus();
                    document.getElementById('numero').classList.add('is-invalid');
                }
            })
            .catch(error => {
                console.error('Error en verificación:', error);
                alertaElement.innerHTML = '<span class="text-danger"><i class="ri-error-warning-line"></i> Error al verificar la transferencia</span>';
            });

        return false;
    });
});
