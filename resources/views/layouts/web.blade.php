<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Osorio Group | CFMOTO Táchira')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Bootstrap 5 solo CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <script
        src="https://code.jquery.com/jquery-3.7.0.min.js"
        integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g="
        crossorigin="anonymous"></script>

    <style>
        :root {
            --cf-teal: #2dd1ff;
            --cf-dark: #122658;
            --cf-gray: #F8F9FA;
            --cf-border: #E9ECEF;
        }
        body { font-family: 'Inter', system-ui, sans-serif; color: var(--cf-dark); background: #fff; }
        .btn-cf { background: var(--cf-teal); color: #fff; border: none; border-radius: 6px; font-weight: 600; }
        .btn-cf:hover { background: #122658; color: #fff; }
        .btn-cf-outline { border: 1px solid var(--cf-teal); color: var(--cf-teal); background: #fff; border-radius: 6px; }
        .btn-cf-outline:hover { background: var(--cf-teal); color: #fff; }
        .navbar-cf { background: #fff; border-bottom: 1px solid var(--cf-border); }
        .card-producto { border: 1px solid var(--cf-border); border-radius: 10px; transition: .2s; overflow: hidden; }
        .card-producto:hover { border-color: var(--cf-teal); transform: translateY(-2px); }
        .card-producto img { aspect-ratio: 1/1; object-fit: contain; padding: 1rem; background: var(--cf-gray); }
        .badge-cat { background: var(--cf-gray); color: var(--cf-dark); font-size: .75rem; font-weight: 500; padding: .35rem .75rem; border-radius: 50px; }
        /* ============================================================ */
        /* FOOTER PRO                                                    */
        /* ============================================================ */
        .footer-pro {
            background: var(--cf-dark);
            color: #fff;
            margin-top: 4rem;
            position: relative;
        }

        /* Bloque de marca */
        .footer-desc {
            color: rgba(255, 255, 255, 0.72);
            font-size: .88rem;
            line-height: 1.6;
        }

        .footer-social-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.08);
            color: #fff;
            text-decoration: none;
            transition: all .2s ease;
            font-size: 1rem;
        }
        .footer-social-link:hover {
            background: var(--cf-teal);
            color: var(--cf-dark);
            transform: translateY(-2px);
        }
        .footer-social-wsp:hover {
            background: #25D366;
            color: #fff;
        }

        /* Títulos */
        .footer-titulo {
            color: #fff;
            font-size: .82rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .8px;
            margin-bottom: 1rem;
            position: relative;
            padding-bottom: .5rem;
        }
        .footer-titulo::after {
            content: "";
            position: absolute;
            left: 0;
            bottom: 0;
            width: 28px;
            height: 2px;
            background: var(--cf-teal);
        }

        /* Listas */
        .footer-lista {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .footer-lista li {
            margin-bottom: .55rem;
            font-size: .88rem;
        }
        .footer-lista li a {
            color: rgba(255, 255, 255, 0.72);
            text-decoration: none;
            transition: color .15s ease;
            display: inline-flex;
            align-items: center;
            gap: .35rem;
        }
        .footer-lista li a:hover {
            color: var(--cf-teal);
        }
        .footer-lista li a.footer-ver-todas {
            color: var(--cf-teal);
            font-weight: 600;
            font-size: .82rem;
            margin-top: .5rem;
        }
        .footer-lista li a.footer-ver-todas:hover {
            color: #fff;
        }

        /* Contador de tiendas */
        .footer-count {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 20px;
            height: 20px;
            padding: 0 6px;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.1);
            font-size: .68rem;
            font-weight: 700;
            color: rgba(255, 255, 255, 0.85);
            margin-left: .25rem;
        }

        /* Contacto */
        .footer-contacto li {
            display: flex;
            align-items: flex-start;
            gap: .55rem;
            color: rgba(255, 255, 255, 0.72);
            line-height: 1.5;
        }
        .footer-contacto li i {
            color: var(--cf-teal);
            font-size: .95rem;
            margin-top: 2px;
            flex-shrink: 0;
        }
        .footer-contacto li a {
            color: rgba(255, 255, 255, 0.72);
        }
        .footer-contacto li a:hover {
            color: var(--cf-teal);
        }

        /* Bloque de confianza */
        .footer-trust {
            border-color: rgba(255, 255, 255, 0.08) !important;
        }
        .footer-trust-item {
            display: flex;
            align-items: center;
            gap: .65rem;
            justify-content: center;
            color: rgba(255, 255, 255, 0.85);
            text-align: left;
        }
        .footer-trust-item i {
            font-size: 1.4rem;
            color: var(--cf-teal);
        }
        .footer-trust-item strong {
            display: block;
            font-size: .85rem;
            color: #fff;
        }
        .footer-trust-item small {
            display: block;
            font-size: .72rem;
            color: rgba(255, 255, 255, 0.55);
        }

        /* Barra inferior */
        .footer-bottom {
            border-color: rgba(255, 255, 255, 0.08) !important;
        }
        .footer-legal {
            color: rgba(255, 255, 255, 0.5);
            font-size: .78rem;
        }
        .footer-legal-links li a {
            color: rgba(255, 255, 255, 0.55);
            text-decoration: none;
            font-size: .78rem;
            transition: color .15s ease;
        }
        .footer-legal-links li a:hover {
            color: var(--cf-teal);
        }

        /* Responsive */
        @media (max-width: 767.98px) {
            .footer-pro .row.py-5 {
                padding-top: 2.5rem !important;
                padding-bottom: 2.5rem !important;
            }
            .footer-trust-item {
                justify-content: flex-start;
            }
            .footer-legal-links {
                flex-wrap: wrap;
                justify-content: center;
            }
        }

        /* ============================================================ */
        /* SUCURSALES — FONDO OSCURO                                     */
        /* ============================================================ */

        .sucursales-section-dark {
            background: #122658;
            color: #fff;
        }

        /* Badge de ubicación */
        .badge-cat-dark {
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
        }

        /* Tabs */
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

        /* Card de sucursal */
        .card-sucursal {
            background: #1a326e;
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            transition: all .2s ease;
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
            width: 44px;
            height: 44px;
            object-fit: contain;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.08);
            padding: 4px;
        }

        .suc-logo-placeholder {
            width: 44px;
            height: 44px;
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
        .suc-line:last-child {
            margin-bottom: 0;
        }
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
        .suc-line a:hover {
            color: var(--cf-teal);
        }

        .card-sucursal-foot {
            padding: 0 1rem 1rem;
        }

        /* Responsive */
        @media (max-width: 767.98px) {
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
        .sucursales-section-dark .btn-cf {
            background: var(--cf-teal);
            color: #122658;
            font-weight: 700;
        }
        .sucursales-section-dark .btn-cf:hover {
            background: #fff;
            color: #122658;
        }
    </style>
    @yield('css')
</head>
<body>

{{-- NAVBAR --}}
<nav class="navbar navbar-expand-lg navbar-cf sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold" href="{{ route('web.home') }}">
            <img src="{{ URL::asset('build/images/logolateral.png') }}" alt="Osorio Group" height="40">
        </a>
        {{-- Carrito + Hamburguesa en móvil (siempre visibles) --}}
        <div class="d-flex align-items-center gap-2 order-lg-3">

            {{-- Carrito móvil: solo visible en < lg --}}
            <a href="{{ route('web.cart.view') }}"
               class="btn btn-cf-outline btn-sm position-relative d-lg-none"
               aria-label="Carrito">
                <i class="bi bi-cart3"></i>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                      id="cartCountMobile">0</span>
            </a>

            <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#navWeb"
                    aria-controls="navWeb" aria-expanded="false" aria-label="Abrir menú" style="padding: 3px;
  font-size: 16px !important;">
                <span class="navbar-toggler-icon"></span>
            </button>
        </div>
        <div class="collapse navbar-collapse" id="navWeb">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Motos</a>
                    <ul class="dropdown-menu">
                        @php
                            $marcasMotossession = session('marcasMotos');
                            $marcasMotossession = isset($marcasMotossession)? $marcasMotossession : [] ;
                            if(count($marcasMotossession) ==0 and isset($marcas) and count($marcas)>0){
                                $marcasMotossession = $marcas;
                            }
                        @endphp
                        @foreach($marcasMotossession as $m)
                            <li>
                                <a class="dropdown-item" href="{{ route('web.motos', ['marca' => $m->codalte]) }}">
                                    {{ $m->nombre_corto }}
                                </a>
                            </li>
                        @endforeach
                    </ul><!--{ { route('web.catalogo', ['familia' => 'motos', 'linea' => $linea->descrip]) }}
                    { { route('web.catalogo', ['familia' => 'repuestos']) }}
                    { { route('web.catalogo', ['familia' => 'lubricantes']) }}
                    { { route('web.catalogo', ['familia' => 'boutique']) }}
                    { { route('web.catalogo') }}
                    -->
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('web.sucursales') }}">Sucursales</a>
                </li>
            </ul>
            <form class="d-flex me-3" action="{{ route('web.motos') }}" method="GET">
                <input class="form-control form-control-sm"
                       name="q"
                       placeholder="Buscar moto, repuesto..."
                       value="{{ request('q') }}">
                <button class="btn btn-cf btn-sm ms-2" type="submit">
                    <i class="bi bi-search"></i>
                </button>
            </form>
            <a href="{{ route('web.cart.view') }}"
               class="btn btn-cf-outline btn-sm me-2 position-relative d-none d-lg-inline-flex"
               aria-label="Carrito">
                <i class="bi bi-cart3"></i>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                      id="cartCount">0</span>
            </a>
            @auth
                <a href="{{ route('index') }}" class="btn btn-cf btn-sm">Panel</a>
            @else
                <a href="{{ route('login') }}" class="btn btn-cf btn-sm">Ingresar</a>
            @endauth
        </div>
    </div>
</nav>

{{-- CONTENIDO --}}
<main>
    @yield('content')
</main>

@include('web.partials.footer')


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@yield('scripts')
</body>
</html>
