@extends('layouts.master')

@section('title')
    Agrupación de Motos
@endsection

@section('css')
    <style>
        .tabla-grupos {
            font-size: .85rem;
        }
        .tabla-grupos thead th {
            position: sticky;
            top: 0;
            background: #122658;
            color: #fff;
            font-weight: 600;
            font-size: .75rem;
            text-transform: uppercase;
            letter-spacing: .5px;
            white-space: nowrap;
            z-index: 10;
        }
        .tabla-grupos td {
            vertical-align: middle;
            padding: .35rem .5rem;
        }
        .tabla-grupos .form-control,
        .tabla-grupos .form-select {
            font-size: .82rem;
            padding: .25rem .5rem;
            border: 1px solid #d1d3d6;
            background: transparent;
            transition: all .15s ease;
        }
        .tabla-grupos .form-control:hover,
        .tabla-grupos .form-select:hover {
            border-color: #DEE2E6;
            background: #fff;
        }
        .tabla-grupos .form-control:focus,
        .tabla-grupos .form-select:focus {
            border-color: var(--cf-teal, #2dd1ff);
            background: #fff;
            box-shadow: 0 0 0 2px rgba(45,209,255,.15);
        }
        .tabla-grupos tr.fila-guardando {
            background: rgba(45,209,255,.05);
        }
        .tabla-grupos tr.fila-guardada {
            background: rgba(40,167,69,.08);
            transition: background 1s ease;
        }
        .tabla-grupos tr.fila-error {
            background: rgba(220,53,69,.08);
        }

        /* Color picker compacto */
        .color-picker-wrap {
            display: flex;
            align-items: center;
            gap: .35rem;
        }
        .color-picker-wrap input[type="color"] {
            width: 32px;
            height: 28px;
            padding: 2px;
            border-radius: 6px;
            cursor: pointer;
            border: 1px solid #DEE2E6;
        }
        .color-picker-wrap input[type="text"] {
            width: 90px;
            font-family: monospace;
            font-size: .78rem;
        }

        /* Estado de guardado */
        .estado-guardado {
            display: inline-block;
            width: 18px;
            text-align: center;
            font-size: .75rem;
        }

        .tabla-grupos-wrapper {
            max-height: 75vh;
            overflow: auto;
            border: 1px solid #E9ECEF;
            border-radius: 8px;
        }

        .badge-grupo {
            background: #e3f2fd;
            color: #122658;
            font-size: .68rem;
            padding: .15rem .4rem;
            border-radius: 4px;
            font-family: monospace;
        }
        .grupo-imagen-wrap:hover .grupo-imagen-btn {
            opacity: 1;
        }
        .grupo-imagen-wrap:hover .grupo-imagen-preview {
            filter: brightness(0.7);
        }
        .tabla-grupos .btn-link {
            text-decoration: none;
            line-height: 1;
            font-size: 1rem;
            transition: transform .15s ease;
        }
        .tabla-grupos .btn-link:hover {
            transform: scale(1.15);
        }
        .tabla-grupos .btn-link.text-primary:hover {
            color: #0d6efd !important;
        }
        .tabla-grupos .btn-link.text-success:hover {
            color: #198754 !important;
        }
    </style>
@endsection

@section('content')

    <div class="row">
        <div class="col-12">
            <div class="card">



                {{-- Filtros --}}
                <div class="card-body border-bottom bg-light">
                    <form method="GET" action="{{ route('saprod.grupos') }}" class="row g-2 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label small mb-1">Buscar</label>
                            <input type="text" name="q" class="form-control form-control-sm"
                                   placeholder="Código, descripción, marca, grupo..."
                                   value="{{ $busqueda }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small mb-1">Marca</label>
                            <select name="marca" class="form-select form-select-sm">
                                <option value="">Todas las marcas</option>
                                @foreach($marcas as $m)
                                    <option value="{{ $m }}" {{ $marcaFiltro === $m ? 'selected' : '' }}>
                                        {{ $m }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small mb-1">Grupo</label>
                            <select name="con_grupo" class="form-select form-select-sm">
                                <option value=""  {{ $soloConGrupo === ''  ? 'selected' : '' }}>Todos</option>
                                <option value="1" {{ $soloConGrupo === '1' ? 'selected' : '' }}>Solo con grupo</option>
                                <option value="0" {{ $soloConGrupo === '0' ? 'selected' : '' }}>Solo sin grupo</option>
                            </select>
                        </div>
                        <div class="col-md-2 d-flex gap-2">
                            <button type="submit" class="btn btn-sm btn-primary w-100">
                                <i class="bi bi-funnel"></i> Filtrar
                            </button>
                            <a href="{{ route('saprod.grupos') }}" class="btn btn-sm btn-outline-secondary w-100">
                                <i class="bi bi-x"></i>
                            </a>
                        </div>
                    </form>
                </div>

                {{-- Tabla --}}
                <div class="card-body p-0">
                    <div class="tabla-grupos-wrapper">
                        <table class="table table-hover mb-0 tabla-grupos" id="tablaGrupos">
                            <thead>
                            <tr>
                                <th style="min-width:220px;">Descripción</th>
                                <th style="width: 120px;">Marca</th>
                                <th style="width: 130px;">Grupo</th>
                                <th style="min-width: 160px;">Nombre catálogo</th>
                                <th style="width: 150px;">Color</th>
                                <th style="width: 140px;">Nombre color</th>
                                <th style="width: 140px;">Estilo</th>
                                <th style="width: 90px;">CC</th>
                                <th style="width: 80px;">Imagen</th>
                                <th style="width: 110px;">Precio (USD)</th>
                                <th style="width: 90px;">Acciones</th>
                            </tr>
                            </thead>
                            <tbody>
                            <datalist id="grupos-sugeridos">
                                @foreach($gruposExistentes as $g)
                                    <option value="{{ $g }}"></option>
                                @endforeach
                            </datalist>
                            <datalist id="clases-sugeridas">
                                @foreach($clases as $c)
                                    <option value="{{ $c }}"></option>
                                @endforeach
                            </datalist>

                            @forelse($productos as $p)
                                <tr data-codprod="{{ $p->codprod }}"
                                    data-id="{{ $p->id }}"
                                    data-comercial="{{ $p->comercial }}">


                                    {{-- Descripción --}}
                                    <td>
                                        <input type="text"
                                               class="form-control"
                                               data-campo="descrip" style="min-width: 220px"
                                               value="{{ $p->descrip }}">
                                    </td>

                                    {{-- Marca --}}
                                    <td>
                                        <input type="text"
                                               class="form-control"
                                               data-campo="marca" style="min-width: 80px"
                                               value="{{ $p->marca }}"
                                               list="marcas-sugeridas">
                                    </td>

                                    {{-- Grupo --}}
                                    <td>
                                        <input type="text"
                                               class="form-control"
                                               data-campo="grupo"
                                               value="{{ $p->grupo }}" style="min-width: 70px"
                                               list="grupos-sugeridos"
                                               placeholder="ej: cf450mt">
                                    </td>

                                    {{-- Nombre catálogo --}}
                                    <td>
                                        <input type="text"
                                               class="form-control"
                                               data-campo="grupo_nombre"
                                               value="{{ $p->grupo_nombre }}"
                                               placeholder="ej: CFMOTO 450MT">
                                    </td>

                                    {{-- Color hex + nombre --}}
                                    <td>
                                        <div class="color-picker-wrap">
                                            <input type="color"
                                                   data-color-picker
                                                   value="{{ $p->color_hex ?: '#122658' }}"
                                                   data-campo="color_hex"
                                                   data-original="{{ $p->color_hex }}">
                                            <input type="text"
                                                   class="form-control"
                                                   data-campo="color_hex"
                                                   data-color-text
                                                   value="{{ $p->color_hex }}"
                                                   placeholder="#122658"
                                                   pattern="^#[0-9A-Fa-f]{6}$">
                                        </div>
                                    </td>

                                    {{-- Nombre color --}}
                                    <td>
                                        <input type="text"
                                               class="form-control"
                                               data-campo="color_nombre"
                                               value="{{ $p->color_nombre }}"
                                               placeholder="ej: Azul metálico">
                                    </td>

                                    {{-- Clase --}}
                                    <td>
                                        <input type="text"
                                               class="form-control"
                                               data-campo="clase"
                                               value="{{ $p->clase }}"
                                               list="clases-sugeridas" style="min-width: 122px; ">
                                    </td>

                                    {{-- Cilindraje --}}
                                    <td>
                                        <input type="number"
                                               class="form-control"  style="min-width: 45px;"
                                               data-campo="cilindraje"
                                               value="{{ $p->cilindraje }}"
                                               min="0" step="1">
                                    </td>
                                    {{-- Imagen --}}
                                    <td class="text-center">
                                        <div class="grupo-imagen-wrap position-relative"
                                             style="width: 56px; height: 56px; border-radius: 6px; overflow: hidden;
                border: 1px solid #dee2e6; background: #f8f9fa;
                display: flex; align-items: center; justify-content: center; margin: 0 auto;">
                                            <img src="{{ $p->imagen_thumb_url }}"
                                                 alt="{{ $p->descrip }}"
                                                 style="width: 100%; height: 100%; object-fit: cover;"
                                                 class="grupo-imagen-preview"
                                                 data-id="{{ $p->id }}">
                                            <input type="file"
                                                   class="grupo-imagen-input d-none"
                                                   accept="image/jpeg,image/png,image/webp"
                                                   data-id="{{ $p->id }}">
                                            <button type="button"
                                                    class="grupo-imagen-btn position-absolute"
                                                    style="inset: 0; background: rgba(0,0,0,.35); color: #fff; border: 0;
                       opacity: 0; transition: opacity .15s;"
                                                    title="Cambiar imagen">
                                                <i class="bi bi-camera"></i>
                                            </button>
                                        </div>
                                    </td>

                                    {{-- Precio --}}
                                    <td>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text">$</span>
                                            <input type="number"
                                                   class="form-control"
                                                   data-campo="costod3"
                                                   value="{{ $p->costod3 }}"
                                                   min="0" step="0.01">
                                        </div>
                                    </td>

                                    {{-- Estado de guardado --}}
                                    {{-- Estado de guardado + Acciones --}}
                                    <td class="text-center">
                                        <div class="d-flex align-items-center justify-content-center gap-1">
                                            <span class="estado-guardado"></span>

                                            <a href="{{ route('productos.edit', $p->id) }}"
                                               class="btn btn-sm btn-link p-0 text-primary"
                                               title="Editar producto"
                                               target="_blank" rel="noopener">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>

                                            <a href="{{ route('web.producto', $p->codprod) }}"
                                               class="btn btn-sm btn-link p-0 text-success"
                                               title="Ver en la web"
                                               target="_blank" rel="noopener">
                                                <i class="bi bi-box-arrow-up-right"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="13" class="text-center py-4 text-muted">
                                        No se encontraron motos con esos filtros.
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Paginación --}}
                @if($productos->hasPages())
                    <div class="card-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                Mostrando {{ $productos->firstItem() }}–{{ $productos->lastItem() }}
                                de {{ $productos->total() }}
                            </small>
                            {{ $productos->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        (function () {
            const endpoint = '{{ route('saprod.grupos.update') }}';
            const csrf     = document.querySelector('meta[name="csrf-token"]').content;

            /**
             * Guarda un campo vía AJAX.
             */
            function guardarCampo(tr, input) {
                const codprod = tr.dataset.codprod;
                const campo   = input.dataset.campo;
                const valor   = input.value;

                // Feedback: fila en estado guardando
                tr.classList.add('fila-guardando');
                tr.classList.remove('fila-guardada', 'fila-error');
                const estado = tr.querySelector('.estado-guardado');
                estado.innerHTML = '<span class="spinner-border spinner-border-sm text-primary" role="status"></span>';

                fetch(endpoint, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ codprod, campo, valor }),
                })
                    .then(async r => {
                        const data = await r.json();
                        if (!r.ok) throw data;
                        return data;
                    })
                    .then(data => {
                        tr.classList.remove('fila-guardando');
                        tr.classList.add('fila-guardada');
                        estado.innerHTML = '<i class="bi bi-check-circle-fill text-success"></i>';

                        // Si el campo era color_hex, sincronizar el picker y el texto
                        if (campo === 'color_hex') {
                            const picker = tr.querySelector('[data-color-picker]');
                            const texto  = tr.querySelector('[data-color-text]');
                            if (picker && data.valor) picker.value = data.valor;
                            if (texto)  texto.value = data.valor || '';
                        }

                        setTimeout(() => tr.classList.remove('fila-guardada'), 1500);
                    })
                    .catch(err => {
                        tr.classList.remove('fila-guardando');
                        tr.classList.add('fila-error');
                        estado.innerHTML = '<i class="bi bi-x-circle-fill text-danger" title="' + (err.error || 'Error') + '"></i>';
                        setTimeout(() => tr.classList.remove('fila-error'), 2000);
                        console.error('Error guardando:', err);
                    });
            }

            /* ============================================================ */
            /* Eventos                                                     */
            /* ============================================================ */

            // Guardar al salir del campo (blur)
            document.querySelectorAll('#tablaGrupos input[data-campo]').forEach(input => {
                const original = input.value;

                input.addEventListener('blur', function () {
                    if (this.value !== original) {
                        guardarCampo(this.closest('tr'), this);
                        // Actualizar el valor "original" tras guardar
                        this.dataset.original = this.value;
                    }
                });

                // Guardar con Enter
                input.addEventListener('keydown', function (e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        this.blur();
                    }
                });
            });

            // Sincronizar color picker con input de texto
            document.querySelectorAll('[data-color-picker]').forEach(picker => {
                picker.addEventListener('input', function () {
                    const tr   = this.closest('tr');
                    const texto = tr.querySelector('[data-color-text]');
                    if (texto) texto.value = this.value.toUpperCase();
                });

                picker.addEventListener('change', function () {
                    const tr = this.closest('tr');
                    guardarCampo(tr, { dataset: { campo: 'color_hex' }, value: this.value.toUpperCase() });
                });
            });

            // Sincronizar input de texto con color picker
            document.querySelectorAll('[data-color-text]').forEach(texto => {
                texto.addEventListener('input', function () {
                    const tr = this.closest('tr');
                    const picker = tr.querySelector('[data-color-picker]');
                    if (picker && /^#[0-9A-Fa-f]{6}$/.test(this.value)) {
                        picker.value = this.value;
                    }
                });
            });


// Hover para mostrar botón
            document.querySelectorAll('.grupo-imagen-wrap').forEach(wrap => {
                wrap.addEventListener('mouseenter', () => wrap.querySelector('.grupo-imagen-btn').style.opacity = 1);
                wrap.addEventListener('mouseleave', () => wrap.querySelector('.grupo-imagen-btn').style.opacity = 0);
            });

// Click en botón de imagen → abre input file
            document.querySelectorAll('.grupo-imagen-btn').forEach(btn => {
                btn.addEventListener('click', function () {
                    this.closest('.grupo-imagen-wrap').querySelector('.grupo-imagen-input').click();
                });
            });

// Cambio de archivo → sube vía AJAX
            document.querySelectorAll('.grupo-imagen-input').forEach(input => {
                input.addEventListener('change', function () {
                    if (!this.files[0]) return;

                    const id      = this.dataset.id;
                    const preview = this.closest('.grupo-imagen-wrap').querySelector('.grupo-imagen-preview');
                    const fd      = new FormData();
                    fd.append('imagen', this.files[0]);
                    fd.append('_token', document.querySelector('meta[name="csrf-token"]').content);

                    preview.style.opacity = .4;

                    fetch(`/saprod/${id}/imagen`, {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
                        body: fd,
                    })
                        .then(r => r.json())
                        .then(data => {
                            if (data.success) {
                                // Cache-busting para ver el cambio al instante
                                preview.src = data.thumb + '&t=' + Date.now();
                                preview.style.opacity = 1;
                            } else {
                                alert(data.error || 'Error al subir imagen');
                                preview.style.opacity = 1;
                            }
                        })
                        .catch(err => {
                            console.error(err);
                            alert('Error al subir imagen');
                            preview.style.opacity = 1;
                        });
                });
            });
        })();
    </script>
@endsection
