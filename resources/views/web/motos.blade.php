@extends('layouts.web')

@section('title', 'Motos · Osorio Group')

@section('content')

    {{-- ============================================================ --}}
    {{-- HEADER DINÁMICO --}}
    {{-- ============================================================ --}}
    @include('web.partials.header-motos', [
        'marcaActiva' => $marcaActiva,
        'estilo'      => $estilo,
        'motos'       => $motos,
    ])

    {{-- ============================================================ --}}
    {{-- LAYOUT 2 COLUMNAS --}}
    {{-- ============================================================ --}}
    <section class="py-4">
        <div class="container">

            {{-- Botón filtros en móvil --}}
            <div class="d-lg-none mb-3">
                <button class="btn btn-cf-outline w-100" type="button"
                        data-bs-toggle="offcanvas" data-bs-target="#filtrosOffcanvas">
                    <i class="bi bi-funnel me-1"></i> Filtros
                    @if($marcaCodalte || $estilo || $precioDesde !== null || $precioHasta !== null)
                        <span class="badge bg-danger ms-1">●</span>
                    @endif
                </button>
            </div>

            <div class="row g-4">

                {{-- ============================================================ --}}
                {{-- SIDEBAR ESCRITORIO --}}
                {{-- ============================================================ --}}
                <aside class="col-lg-3 d-none d-lg-block">
                    <div class="sidebar-filtros-sticky">
                        @include('web.partials.sidebar-filtros', [
                            'marcas'       => $marcas,
                            'estilos'      => $estilos,
                            'marcaCodalte' => $marcaCodalte,
                            'estilo'       => $estilo,
                            'precioDesde'  => $precioDesde,
                            'precioHasta'  => $precioHasta,
                            'rango'        => $rango,
                        ])
                    </div>
                </aside>

                {{-- ============================================================ --}}
                {{-- RESULTADOS --}}
                {{-- ============================================================ --}}
                <div class="col-lg-9">
                    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                        <small class="text-secondary" id="resultado-info">
                            @if($motos->total() > 0)
                                Mostrando <strong>{{ $motos->firstItem() }}–{{ $motos->lastItem() }}</strong>
                                de <strong>{{ $motos->total() }}</strong>
                            @else
                                Sin resultados
                            @endif
                        </small>

                        <div class="d-flex align-items-center gap-2">
                            <small class="text-secondary">Ordenar:</small>
                            <select id="ordenar-por" class="form-select form-select-sm" style="width: auto;">
                                <option value="reciente"    {{ $ordenarPor === 'reciente'    ? 'selected' : '' }}>Más recientes</option>
                                <option value="precio_asc"  {{ $ordenarPor === 'precio_asc'  ? 'selected' : '' }}>Menor precio</option>
                                <option value="precio_desc" {{ $ordenarPor === 'precio_desc' ? 'selected' : '' }}>Mayor precio</option>
                                <option value="nombre"      {{ $ordenarPor === 'nombre'      ? 'selected' : '' }}>Nombre A-Z</option>
                            </select>
                        </div>
                    </div>

                    <div id="grid-wrapper">
                        <div id="grid-loader" class="loader-overlay">
                            <div class="text-center">
                                <div class="loader-spinner mx-auto mb-2"></div>
                                <small class="text-secondary">Cargando motos…</small>
                            </div>
                        </div>

                        @include('web.partials.grid-motos', [
                            'motos'       => $motos,
                            'marcaActiva' => $marcaActiva,
                            'busqueda'    => $busqueda,
                        ])
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================================ --}}
    {{-- OFFCANVAS FILTROS (MÓVIL) --}}
    {{-- ============================================================ --}}
    <div class="offcanvas offcanvas-start" tabindex="-1" id="filtrosOffcanvas" aria-labelledby="filtrosOffcanvasLabel">
        <div class="offcanvas-header border-bottom">
            <h5 class="offcanvas-title fw-bold" id="filtrosOffcanvasLabel">
                <i class="bi bi-funnel me-1"></i> Filtros
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Cerrar"></button>
        </div>
        <div class="offcanvas-body">
            @include('web.partials.sidebar-filtros', [
                'marcas'       => $marcas,
                'estilos'      => $estilos,
                'marcaCodalte' => $marcaCodalte,
                'estilo'       => $estilo,
                'precioDesde'  => $precioDesde,
                'precioHasta'  => $precioHasta,
                'rango'        => $rango,
            ])
        </div>
    </div>

@endsection

@section('css')
    <style>
        /* Sticky del sidebar */
        .sidebar-filtros-sticky {
            position: sticky;
            top: 90px;
        }

        /* Bloques del sidebar */
        .filtro-bloque {
            border-bottom: 1px solid #E9ECEF;
            padding-bottom: 1rem;
            margin-bottom: 1rem;
        }
        .filtro-bloque:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }
        .filtro-titulo {
            font-size: .8rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .5px;
            color: #495057;
            margin-bottom: .75rem;
        }

        /* Opciones tipo lista (marca) */
        .filtro-opcion {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: .35rem .5rem;
            border-radius: 6px;
            font-size: .85rem;
            color: #495057;
            text-decoration: none;
            transition: all .15s ease;
            margin-bottom: 2px;
        }
        .filtro-opcion:hover {
            background: #f8f9fa;
            color: var(--cf-teal);
        }
        .filtro-opcion.activo {
            background: var(--cf-teal);
            color: #fff;
            font-weight: 600;
        }

        /* Chips de estilo */
        .chip-estilo-filtro {
            display: inline-block;
            padding: .3rem .75rem;
            border-radius: 50px;
            font-size: .78rem;
            border: 1px solid #DEE2E6;
            color: #495057;
            background: #fff;
            text-decoration: none;
            margin: 0 .25rem .35rem 0;
            transition: all .15s ease;
        }
        .chip-estilo-filtro:hover {
            border-color: var(--cf-teal);
            color: var(--cf-teal);
        }
        .chip-estilo-filtro.activo {
            background: var(--cf-teal);
            border-color: var(--cf-teal);
            color: #fff;
            font-weight: 600;
        }

        /* Inputs de precio */
        .precio-grupo {
            display: flex;
            align-items: center;
            gap: .35rem;
        }
        .precio-grupo input {
            flex: 1;
            min-width: 0;
        }

        /* ============================================================ */
        /* LOADER                                                       */
        /* ============================================================ */
        .loader-overlay {
            position: absolute;
            inset: 0;
            background: rgba(255, 255, 255, 0.75);
            backdrop-filter: blur(2px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 20;
            border-radius: 8px;
            opacity: 0;
            pointer-events: none;
            transition: opacity .2s ease;
        }
        .loader-overlay.activo {
            opacity: 1;
            pointer-events: all;
        }
        .loader-spinner {
            width: 42px;
            height: 42px;
            border: 3px solid #e9ecef;
            border-top-color: var(--cf-teal);
            border-radius: 50%;
            animation: loader-spin .8s linear infinite;
        }
        @keyframes loader-spin {
            to { transform: rotate(360deg); }
        }

        #grid-wrapper {
            position: relative;
            min-height: 320px;
        }

        /* Chip cargando */
        .filtro-opcion.cargando,
        .chip-estilo-filtro.cargando {
            opacity: .6;
            pointer-events: none;
            position: relative;
        }
        .filtro-opcion.cargando::after,
        .chip-estilo-filtro.cargando::after {
            content: "";
            width: 12px;
            height: 12px;
            border: 2px solid rgba(255,255,255,.4);
            border-top-color: #fff;
            border-radius: 50%;
            animation: loader-spin .7s linear infinite;
            display: inline-block;
            margin-left: 6px;
            vertical-align: middle;
        }

        /* Botón precio cargando */
        #btn-aplicar-precio.cargando {
            pointer-events: none;
            opacity: .7;
        }
        #btn-aplicar-precio.cargando .spinner-border-sm {
            width: 12px;
            height: 12px;
            border-width: 2px;
        }

        @media (max-width: 767.98px) {
            .filtro-opcion { font-size: .8rem; }
            .chip-estilo-filtro { font-size: .72rem; padding: .25rem .6rem; }
        }
    </style>
@endsection

@section('scripts')
    <script>
        (function () {
            const baseUrl = '{{ route('web.motos') }}';

            const loader    = document.getElementById('grid-loader');
            const gridWrap  = document.getElementById('grid-wrapper');
            const selectOrd = document.getElementById('ordenar-por');

            let requestEnCurso = null;

            /* ============================================================ */
            /* LOADER HELPERS                                                */
            /* ============================================================ */
            function mostrarLoader() {
                loader?.classList.add('activo');
                gridWrap?.classList.add('cargando');
            }
            function ocultarLoader() {
                loader?.classList.remove('activo');
                gridWrap?.classList.remove('cargando');
            }
            function resetearChipsCargando() {
                document.querySelectorAll('.filtro-opcion.cargando, .chip-estilo-filtro.cargando')
                    .forEach(el => el.classList.remove('cargando'));
            }
            function resetearBotonPrecio() {
                const btn = document.getElementById('btn-aplicar-precio');
                if (!btn) return;
                btn.classList.remove('cargando');
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-check2 me-1"></i> Aplicar precio';
            }

            /* ============================================================ */
            /* CARGAR GRID                                                   */
            /* ============================================================ */
            function cargarMotos(url, options = {}) {
                if (requestEnCurso) requestEnCurso.abort();

                const controller = new AbortController();
                requestEnCurso = controller;

                mostrarLoader();

                if (options.chip) options.chip.classList.add('cargando');

                if (options.desdePrecio) {
                    const btn = document.getElementById('btn-aplicar-precio');
                    if (btn) {
                        btn.disabled = true;
                        btn.classList.add('cargando');
                        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status"></span> Aplicando…';
                    }
                }

                if (selectOrd) selectOrd.disabled = true;

                fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    signal: controller.signal,
                })
                    .then(r => {
                        if (!r.ok) throw new Error('HTTP ' + r.status);
                        return r.json();
                    })
                    .then(data => {
                        // Header
                        const headerActual = document.getElementById('header-motos');
                        if (headerActual && data.header) headerActual.outerHTML = data.header;

                        // Sidebar (desktop + offcanvas móvil)
                        if (data.sidebar) {
                            document.querySelectorAll('.sidebar-filtros-container').forEach(el => {
                                const wrapper = document.createElement('div');
                                wrapper.innerHTML = data.sidebar.trim();
                                el.replaceWith(wrapper.firstElementChild);
                            });
                        }

                        // Grid
                        const grid = document.getElementById('grid-motos');
                        if (grid && data.html) grid.outerHTML = data.html;

                        // Barra "Mostrando X–Y"
                        const info = document.getElementById('resultado-info');
                        if (info) {
                            info.innerHTML = data.total > 0
                                ? `Mostrando <strong>${data.firstItem}–${data.lastItem}</strong> de <strong>${data.total}</strong>`
                                : 'Sin resultados';
                        }

                        // Cerrar offcanvas en móvil
                        if (window.bootstrap) {
                            const oc = document.getElementById('filtrosOffcanvas');
                            if (oc) {
                                const inst = bootstrap.Offcanvas.getInstance(oc);
                                if (inst) inst.hide();
                            }
                        }

                        history.pushState({}, '', url);
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    })
                    .catch(err => {
                        if (err.name === 'AbortError') return;
                        console.error(err);
                        window.location.href = url;
                    })
                    .finally(() => {
                        ocultarLoader();
                        resetearChipsCargando();
                        resetearBotonPrecio();
                        if (selectOrd) selectOrd.disabled = false;
                        requestEnCurso = null;
                    });
            }

            /* ============================================================ */
            /* CONSTRUIR URL                                                 */
            /* ============================================================ */
            function construirUrl(overrides = {}) {
                const u = new URL(baseUrl);
                const actuales = new URL(window.location.href).searchParams;
                for (const [k, v] of actuales.entries()) {
                    if (k !== 'page') u.searchParams.set(k, v);
                }
                Object.entries(overrides).forEach(([k, v]) => {
                    if (v === null || v === undefined || v === '') u.searchParams.delete(k);
                    else u.searchParams.set(k, v);
                });
                u.searchParams.delete('page');
                return u.href;
            }

            /* ============================================================ */
            /* DELEGACIÓN DE EVENTOS                                         */
            /* ============================================================ */

            // Clicks en chips y paginación (delegado)
            document.addEventListener('click', function (e) {
                const link = e.target.closest('a.filtro-link, #grid-motos .pagination a');
                if (!link) return;
                if (e.metaKey || e.ctrlKey || e.shiftKey || e.button !== 0) return;

                e.preventDefault();
                const esFiltro = link.classList.contains('filtro-link');
                cargarMotos(link.href, { chip: esFiltro ? link : null });
            });

            // Click en "Aplicar precio" (delegado — sobrevive al reemplazo del sidebar)
            document.addEventListener('click', function (e) {
                if (!e.target.closest('#btn-aplicar-precio')) return;

                let desde = document.getElementById('precio-desde')?.value || '';
                let hasta = document.getElementById('precio-hasta')?.value || '';

                if (desde && hasta && Number(desde) > Number(hasta)) {
                    [desde, hasta] = [hasta, desde];
                }

                const url = construirUrl({
                    precio_desde: desde || null,
                    precio_hasta: hasta || null,
                });
                cargarMotos(url, { desdePrecio: true });
            });

            // Enter en los inputs de precio (delegado)
            document.addEventListener('keypress', function (e) {
                if (e.key !== 'Enter') return;
                if (e.target.id !== 'precio-desde' && e.target.id !== 'precio-hasta') return;
                e.preventDefault();
                document.getElementById('btn-aplicar-precio')?.click();
            });

            // Cambio de orden
            document.addEventListener('change', function (e) {
                if (e.target.id !== 'ordenar-por') return;
                const url = construirUrl({ orden: e.target.value });
                cargarMotos(url);
            });

            // Botón atrás/adelante
            window.addEventListener('popstate', function () {
                cargarMotos(window.location.href);
            });
        })();
    </script>
@endsection
