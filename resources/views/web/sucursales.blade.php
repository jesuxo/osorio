{{-- resources/views/web/sucursales.blade.php --}}
@extends('layouts.web')

@section('title', 'Sucursales · Osorio Group Táchira')

@section('content')

    {{-- ============================================================ --}}
    {{-- HERO OSCURO                                                  --}}
    {{-- ============================================================ --}}
    <section class="suc-hero">
        <div class="container">
            <nav aria-label="breadcrumb" class="mb-3">
                <ol class="breadcrumb breadcrumb-dark mb-0 small">
                    <li class="breadcrumb-item">
                        <a href="{{ route('web.home') }}" class="text-decoration-none">Inicio</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Sucursales</li>
                </ol>
            </nav>

            <span class="badge-cat badge-cat-dark mb-3 d-inline-block">
            <i class="bi bi-geo-alt me-1"></i> Táchira, Venezuela
        </span>

            <h1 class="display-5 fw-bold text-white mb-3" style="line-height: 1.1;">
                Nuestras sucursales
            </h1>
            <p class="lead text-light-emphasis mb-4" style="max-width: 620px; color: rgba(255, 255, 255, 0.65) !important;">
                {{ $totalTiendas }} tiendas en La Fría, San Cristóbal y San Antonio.
                Adquiere tu moto en linea desde cualquier parte del pais.
                Envios a toda venezuela.
            </p>

            {{-- Stats rápidas --}}
            <div class="d-flex flex-wrap gap-4 mt-4">
                <div class="suc-hero-stat">
                    <div class="suc-hero-stat-num">{{ $totalTiendas }}</div>
                    <div class="suc-hero-stat-label">Tiendas en Táchira</div>
                </div>
                <div class="suc-hero-stat">
                    <div class="suc-hero-stat-num">3</div>
                    <div class="suc-hero-stat-label">Ciudades</div>
                </div>
                <div class="suc-hero-stat">
                    <div class="suc-hero-stat-num">100%</div>
                    <div class="suc-hero-stat-label">Repuestos originales</div>
                </div>
                <div class="suc-hero-stat">
                    <div class="suc-hero-stat-num">+20</div>
                    <div class="suc-hero-stat-label">Años de trayectoria</div>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================================ --}}
    {{-- SECCIÓN PRINCIPAL: TABS + CARDS + MAPA                        --}}
    {{-- ============================================================ --}}
    <section class="sucursales-section sucursales-section-dark py-5" id="sucursales">
        <div class="container">

            {{-- TABS --}}
            <div class="suc-tabs-wrapper mb-4">
                <ul class="nav nav-pills suc-tabs justify-content-center" id="sucursalesTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link {{ $tabActivo === 'todas' ? 'active' : '' }}"
                                data-bs-toggle="pill"
                                data-bs-target="#tab-todas"
                                type="button" role="tab">
                            Todas
                            <span class="suc-tab-count">{{ $totalTiendas }}</span>
                        </button>
                    </li>
                    @foreach($ciudades as $key => $ciudad)
                        <li class="nav-item" role="presentation">
                            <button class="nav-link {{ $tabActivo === $key ? 'active' : '' }}"
                                    data-bs-toggle="pill"
                                    data-bs-target="#tab-{{ $key }}"
                                    type="button" role="tab">
                                {{ $ciudad['label'] }}
                                <span class="suc-tab-count">{{ $ciudad['count'] }}</span>
                            </button>
                        </li>
                    @endforeach
                </ul>
            </div>

            {{-- CONTENIDO --}}
            <div class="tab-content" id="sucursalesTabContent">

                {{-- TAB TODAS --}}
                <div class="tab-pane fade {{ $tabActivo === 'todas' ? 'show active' : '' }}"
                     id="tab-todas" role="tabpanel">
                    @foreach($ciudades as $key => $ciudad)
                        <div class="suc-ciudad-bloque mb-5">
                            <div class="suc-ciudad-head">
                                <div>
                                    <h3 class="fw-bold text-white mb-1">{{ $ciudad['label'] }}</h3>
                                    <p class="  small mb-0" style="color: rgba(255, 255, 255, 0.65) !important;">
                                        {{ $ciudad['descripcion'] }}
                                    </p>
                                </div>
                                <span class="badge bg-white text-dark">
                                {{ $ciudad['count'] }} {{ $ciudad['count'] == 1 ? 'tienda' : 'tiendas' }}
                            </span>
                            </div>

                            <div class="row g-3">
                                @foreach($ciudad['sucursales'] as $suc)
                                    <div class="col-lg-3 col-md-6">
                                        @include('web.partials.card-sucursal', ['suc' => array_merge($suc, ['ciudad' => $ciudad['label']])])
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- TABS POR CIUDAD --}}
                @foreach($ciudades as $key => $ciudad)
                    <div class="tab-pane fade {{ $tabActivo === $key ? 'show active' : '' }}"
                         id="tab-{{ $key }}" role="tabpanel">

                        <div class="suc-ciudad-bloque">
                            <div class="suc-ciudad-head">
                                <div>
                                    <h3 class="fw-bold text-white mb-1">{{ $ciudad['label'] }}</h3>
                                    <p class="text-light-emphasis small mb-0" style="color: rgba(255, 255, 255, 0.7) !important;">
                                        {{ $ciudad['descripcion'] }}
                                    </p>
                                </div>
                                <span class="badge bg-white text-dark">
                                {{ $ciudad['count'] }} {{ $ciudad['count'] == 1 ? 'tienda' : 'tiendas' }}
                            </span>
                            </div>

                            <div class="row g-3">
                                @foreach($ciudad['sucursales'] as $suc)
                                    <div class="col-lg-3 col-md-6">
                                        @include('web.partials.card-sucursal', ['suc' => array_merge($suc, ['ciudad' => $ciudad['label']])])
                                    </div>
                                @endforeach
                            </div>
                        </div>

                    </div>
                @endforeach

            </div>
        </div>
    </section>

    {{-- ============================================================ --}}
    {{-- CTA FINAL                                                    --}}
    {{-- ============================================================ --}}
    <section class="py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center">
                    <h2 class="fw-bold mb-3">¿No sabes cuál te queda más cerca?</h2>
                    <p class="text-secondary mb-4">
                        Escríbenos por WhatsApp y te ayudamos a encontrar la sucursal más conveniente
                        o coordinamos el envío a tu estado.
                    </p>
                    <div class="d-flex gap-2 justify-content-center flex-wrap">
                        <a href="https://wa.me/584120675809?text={{ urlencode('Hola, quiero saber cuál sucursal de Osorio Group me queda más cerca.') }}"
                           target="_blank" rel="noopener"
                           class="btn btn-cf btn-lg px-4">
                            <i class="bi bi-whatsapp me-1"></i> WhatsApp
                        </a>
                        <a href="tel:+584121717186" class="btn btn-cf-outline btn-lg px-4">
                            <i class="bi bi-telephone me-1"></i> +58 412-1717186
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection

@section('css')
    <style>
        /* ============================================================ */
        /* HERO OSCURO                                                   */
        /* ============================================================ */
        .suc-hero {
            background: #122658;
            color: #fff;
            padding: 3.5rem 0 3rem;
        }
        .suc-hero .breadcrumb-dark .breadcrumb-item a,
        .suc-hero .breadcrumb-dark .breadcrumb-item.active {
            color: rgba(255, 255, 255, 0.65);
        }
        .suc-hero .breadcrumb-dark .breadcrumb-item + .breadcrumb-item::before {
            color: rgba(255, 255, 255, 0.35);
        }
        .suc-hero-stat {
            border-left: 2px solid var(--cf-teal);
            padding-left: .85rem;
        }
        .suc-hero-stat-num {
            font-size: 1.6rem;
            font-weight: 800;
            color: #fff;
            line-height: 1;
        }
        .suc-hero-stat-label {
            font-size: .78rem;
            color: rgba(255, 255, 255, 0.65);
            margin-top: .25rem;
            text-transform: uppercase;
            letter-spacing: .5px;
        }

        /* ============================================================ */
        /* SECCIÓN SUCURSALES (heredada del home)                        */
        /* ============================================================ */
        .sucursales-section-dark {
            background: #122658;
            color: #fff;
        }
        .badge-cat-dark {
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
        }

        .suc-tabs-wrapper {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            padding-bottom: .25rem;
        }
        .suc-tabs {
            flex-wrap: nowrap;
            gap: .5rem;
            border: none;
        }
        .suc-tabs .nav-link {
            background: rgba(255, 255, 255, 0.08);
            color: rgba(255, 255, 255, 0.85);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 50px;
            padding: .5rem 1rem;
            font-size: .85rem;
            font-weight: 600;
            white-space: nowrap;
            transition: all .2s ease;
            display: inline-flex;
            align-items: center;
            gap: .4rem;
        }
        .suc-tabs .nav-link:hover {
            background: rgba(255, 255, 255, 0.15);
            border-color: rgba(255, 255, 255, 0.3);
            color: #fff;
        }
        .suc-tabs .nav-link.active {
            background: var(--cf-teal);
            border-color: var(--cf-teal);
            color: #122658;
            font-weight: 700;
        }
        .suc-tabs .nav-link.active .suc-tab-count {
            background: rgba(18, 38, 88, 0.25);
            color: #fff;
        }
        .suc-tab-count {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 22px;
            height: 22px;
            padding: 0 .4rem;
            border-radius: 11px;
            background: rgba(255, 255, 255, 0.15);
            color: #fff;
            font-size: .7rem;
            font-weight: 700;
        }

        /* Bloques de ciudad */
        .suc-ciudad-bloque + .suc-ciudad-bloque {
            margin-top: 3rem;
            padding-top: 3rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }
        .suc-ciudad-head {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            gap: 1rem;
            margin-bottom: 1.25rem;
            flex-wrap: wrap;
        }

        /* Card de sucursal (versión oscura) */
        .card-sucursal {
            background: #1a326e;
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            transition: all .2s ease;
            height: 100%;
        }
        .card-sucursal:hover {
            border-color: var(--cf-teal);
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.25);
        }
        .card-sucursal-head {
            padding: 1rem 1rem .75rem;
            display: flex;
            align-items: center;
            gap: .75rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }
        .suc-logo {
            width: 44px; height: 44px;
            object-fit: contain;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.08);
            padding: 4px;
        }
        .suc-logo-placeholder {
            width: 44px; height: 44px;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.08);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--cf-teal);
            font-size: 1.2rem;
            flex-shrink: 0;
        }
        .suc-tipo {
            display: block;
            color: rgba(255, 255, 255, 0.55);
            font-size: .7rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .5px;
        }
        .suc-nombre {
            margin: 0;
            font-size: .95rem;
            font-weight: 700;
            color: #fff;
            line-height: 1.2;
        }
        .card-sucursal-body {
            padding: .75rem 1rem 1rem;
            flex-grow: 1;
        }
        .suc-line {
            display: flex;
            align-items: flex-start;
            gap: .5rem;
            font-size: .8rem;
            color: rgba(255, 255, 255, 0.7);
            margin-bottom: .5rem;
            line-height: 1.4;
        }
        .suc-line:last-child { margin-bottom: 0; }
        .suc-line i {
            color: var(--cf-teal);
            font-size: .9rem;
            margin-top: 1px;
            flex-shrink: 0;
        }
        .suc-line a {
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            transition: color .15s ease;
        }
        .suc-line a:hover { color: var(--cf-teal); }
        .card-sucursal-foot {
            padding: 0 1rem 1rem;
        }

        @media (max-width: 767.98px) {
            .suc-hero { padding: 2.5rem 0 2rem; }
            .suc-hero-stat-num { font-size: 1.3rem; }
            .suc-tabs .nav-link {
                font-size: .78rem;
                padding: .45rem .85rem;
            }
            .suc-tab-count {
                min-width: 18px;
                height: 18px;
                font-size: .65rem;
            }
        }
    </style>
@endsection
