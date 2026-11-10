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
        <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#navWeb">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navWeb">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Motos</a>
                    <ul class="dropdown-menu">
                        @foreach($lineasMotos ?? [] as $linea)
                            <li>
                                <a class="dropdown-item" href="#">
                                    {{ $linea->descrip }}
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
                <li class="nav-item"><a class="nav-link" href="#">Repuestos</a></li>
                <li class="nav-item"><a class="nav-link" href="#">Lubricantes</a></li>
                <li class="nav-item"><a class="nav-link" href="#">Boutique</a></li>
            </ul>
            <form class="d-flex me-3" action="#" method="GET">
                <input class="form-control form-control-sm" name="q" placeholder="Buscar producto..." value="{{ request('q') }}">
                <button class="btn btn-cf btn-sm ms-2" type="submit"><i class="bi bi-search"></i></button>
            </form>
            <a href="#" class="btn btn-cf-outline btn-sm me-2 position-relative">
                <i class="bi bi-cart3"></i>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="cartCount">0</span>
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

{{-- FOOTER --}}
<footer style="background: var(--cf-dark); color: #fff;" class="py-4 mt-5">
    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <h6 class="fw-bold"><span style="color: var(--cf-teal);">CFMOTO</span> Táchira</h6>
                <p class="small mb-0">San Cristóbal, Táchira · RIF J-XXXXXXXX-X</p>
            </div>
            <div class="col-md-4">
                <h6 class="fw-bold">Contacto</h6>
                <p class="small mb-0"><i class="bi bi-whatsapp"></i> +58 412-XXX-XXXX</p>
                <p class="small mb-0"><i class="bi bi-instagram"></i> @cfmoto_tachira</p>
            </div>
            <div class="col-md-4 text-md-end">
                <p class="small mb-0">&copy; {{ date('Y') }} Osorio Group</p>
            </div>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@yield('scripts')
</body>
</html>
