@extends('layouts.master')

@section('title')
    Extraer Seriales desde PDF
@endsection

@section('css')
    <style>
        .serial-box {
            font-family: monospace;
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 1rem;
            word-break: break-all;
            font-size: .9rem;
        }
        .preview-row td {
            font-family: monospace;
            font-size: .78rem;
        }
        /* Overlay de carga */
        #loadingOverlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(18, 38, 88, .55);
            z-index: 99999;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            color: #fff;
            backdrop-filter: blur(2px);
        }
        #loadingOverlay.active {
            display: flex;
        }
        .loading-spinner {
            width: 64px;
            height: 64px;
            border: 6px solid rgba(255,255,255,.25);
            border-top-color: #2dd1ff;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        .loading-text {
            margin-top: 1rem;
            font-size: 1rem;
            font-weight: 600;
            letter-spacing: .5px;
        }
        .loading-sub {
            font-size: .8rem;
            opacity: .8;
            margin-top: .25rem;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-file-earmark-pdf me-1"></i> Extraer seriales de factura PDF
                    </h5>
                </div>
                <div class="card-body">

                    {{-- Alertas --}}
                    <div id="alertBox"></div>

                    <form id="formSeriales" enctype="multipart/form-data">
                        @csrf
                        <div class="row g-3 align-items-end">
                            <div class="col-md-6">
                                <label class="form-label">Archivo PDF</label>
                                <input type="file" name="pdf" id="pdfInput" class="form-control"
                                       accept="application/pdf" required>
                                <small class="text-muted">Tamaño máximo: 10 MB</small>
                            </div>
                            <div class="col-md-6">
                                <button type="submit" class="btn btn-primary w-100" id="btnExtraer">
                                    <i class="bi bi-search me-1"></i> Extraer seriales
                                </button>
                            </div>
                        </div>
                    </form>

                    {{-- Resultado (oculto hasta que llegue) --}}
                    <div id="resultadoWrap" style="display:none;">
                        <hr class="my-4">

                        <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
                            <h6 class="mb-0">
                                <i class="bi bi-check2-circle text-success me-1"></i>
                                <span id="resultadoCount">0</span> moto(s) detectada(s)
                            </h6>
                            <button type="button" class="btn btn-sm btn-outline-primary"
                                    onclick="copiarSeriales()">
                                <i class="bi bi-clipboard me-1"></i> Copiar todo
                            </button>
                        </div>

                        <label class="form-label small text-muted">Cadena formateada</label>
                        <div class="serial-box mb-3" id="serialChain"></div>

                        <div class="table-responsive">
                            <table class="table table-sm table-bordered table-hover preview-row">
                                <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Chasis (completo)</th>
                                    <th>SC</th>
                                    <th>Motor (completo)</th>
                                    <th>SM</th>
                                    <th>Placa</th>
                                </tr>
                                </thead>
                                <tbody id="tablaFilas"></tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- Overlay de carga --}}
    <div id="loadingOverlay">
        <div class="loading-spinner"></div>
        <div class="loading-text">
            <i class="bi bi-file-earmark-pdf me-1"></i> Procesando PDF…
        </div>
        <div class="loading-sub">Extrayendo texto y detectando seriales</div>
    </div>
@endsection

@section('scripts')
    <script>
        const form       = document.getElementById('formSeriales');
        const overlay    = document.getElementById('loadingOverlay');
        const btn        = document.getElementById('btnExtraer');
        const alertBox   = document.getElementById('alertBox');
        const wrap       = document.getElementById('resultadoWrap');
        const tabla      = document.getElementById('tablaFilas');
        const cadena     = document.getElementById('serialChain');
        const countEl    = document.getElementById('resultadoCount');

        form.addEventListener('submit', function (e) {
            e.preventDefault();

            const fileInput = document.getElementById('pdfInput');
            if (!fileInput.files.length) {
                mostrarAlerta('Selecciona un archivo PDF primero.', 'warning');
                return;
            }

            // Mostrar overlay + deshabilitar botón
            overlay.classList.add('active');
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Procesando…';
            alertBox.innerHTML = '';
            wrap.style.display = 'none';

            const fd = new FormData(this);

            fetch('{{ route("seriales.extraer") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
                body: fd,
            })
                .then(async r => {
                    const data = await r.json().catch(() => ({}));
                    if (!r.ok) throw data;
                    return data;
                })
                .then(data => {
                    renderResultado(data);
                    mostrarAlerta('✅ Seriales extraídos correctamente.', 'success');
                })
                .catch(err => {
                    console.error(err);
                    const msg = err.error || err.message || 'Error procesando el PDF';
                    mostrarAlerta('❌ ' + msg, 'danger');
                })
                .finally(() => {
                    overlay.classList.remove('active');
                    btn.disabled = false;
                    btn.innerHTML = '<i class="bi bi-search me-1"></i> Extraer seriales';
                });
        });

        function renderResultado(data) {
            countEl.textContent = data.filas.length;
            cadena.textContent  = data.cadena;

            tabla.innerHTML = '';
            data.filas.forEach((f, i) => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${i + 1}</td>
                    <td>${f.chasis}</td>
                    <td class="fw-bold text-primary">${f.sc}</td>
                    <td>${f.motor}</td>
                    <td class="fw-bold text-primary">${f.sm}</td>
                    <td>${f.placa}</td>
                `;
                tabla.appendChild(tr);
            });

            wrap.style.display = 'block';
        }

        function mostrarAlerta(msg, tipo) {
            alertBox.innerHTML = `
                <div class="alert alert-${tipo} alert-dismissible fade show" role="alert">
                    ${msg}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>`;
        }

        function copiarSeriales() {
            const texto = document.getElementById('serialChain').innerText;
            navigator.clipboard.writeText(texto).then(() => {
                mostrarAlerta('✅ Seriales copiados al portapapeles', 'success');
            }).catch(() => mostrarAlerta('No se pudo copiar', 'warning'));
        }
    </script>
@endsection
