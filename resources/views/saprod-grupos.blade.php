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
            border: 1px solid transparent;
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
    </style>
@endsection

@section('content')

    <div class="row">
        <div class="col-12">
            <div class="card">

                {{-- Header --}}
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <h5 class="card-title mb-0 text-white">
                                <i class="bi bi-palette me-1"></i>
                                Agrupación de motos por color
                            </h5>
                            <small class="text-white-50">
                                {{ $productos->total() }} motos · Los cambios se guardan automáticamente
                            </small>
                        </div>
                    </div>
                </div>

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
                                <th style="min-width:300px;">Descripción</th>
                                <th style="width: 120px;">Referencia</th>
                                <th style="width: 120px;">Marca</th>
                                <th style="width: 130px;">Grupo</th>
                                <th style="min-width: 160px;">Nombre catálogo</th>
                                <th style="width: 150px;">Color</th>
                                <th style="width: 140px;">Nombre color</th>
                                <th style="width: 140px;">Estilo</th>
                                <th style="width: 90px;">CC</th>
                                <th style="width: 40px;"></th>
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
                                               data-campo="descrip"
                                               value="{{ $p->descrip }}">
                                    </td>

                                    {{-- Referencia --}}
                                    <td>
                                        <input type="text"
                                               class="form-control"
                                               data-campo="refere"
                                               value="{{ $p->refere }}">
                                    </td>

                                    {{-- Marca --}}
                                    <td>
                                        <input type="text"
                                               class="form-control"
                                               data-campo="marca"
                                               value="{{ $p->marca }}"
                                               list="marcas-sugeridas">
                                    </td>

                                    {{-- Grupo --}}
                                    <td>
                                        <input type="text"
                                               class="form-control"
                                               data-campo="grupo"
                                               value="{{ $p->grupo }}"
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
                                               list="clases-sugeridas">
                                    </td>

                                    {{-- Cilindraje --}}
                                    <td>
                                        <input type="number"
                                               class="form-control"
                                               data-campo="cilindraje"
                                               value="{{ $p->cilindraje }}"
                                               min="0" step="1">
                                    </td>

                                    {{-- Estado de guardado --}}
                                    <td class="text-center">
                                        <span class="estado-guardado"></span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="11" class="text-center py-4 text-muted">
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
        })();
    </script>
@endsection
