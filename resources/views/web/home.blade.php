@extends('layouts.web')

@section('title', 'Osorio Group · Concesionario, distribuidor e importador de repuestos')

@section('content')
    <style>

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        .dropdown-item.active, .dropdown-item:active{
            background-color: #2dd1ff;
        }
        /* Contenedor del Banner Principal */
        .banner-container {
            position: relative;
            width: 100%;
            height: 450px;
            overflow: hidden;
            background-color: #111;
            font-family: 'Arial', sans-serif;
        }

        /* Contenedor de las diapositivas */
        .banner-slider {
            display: flex;
            width: 100%;
            height: 100%;
        }

        /* Diapositiva individual */
        .slide {
            min-width: 100%;
            height: 100%;
            position: relative;
            background-size: cover;
            background-position: center;
            display: none; /* jQuery controlará la visibilidad */
        }

        .slide.active {
            display: block;
        }

        /* Capa oscura superpuesta para mejorar legibilidad del texto */
        .slide::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, rgba(0,0,0,0.7) 0%, rgba(0,0,0,0.3) 100%);
        }

        /* Información o texto del banner */
        .slide-content {
            position: absolute;
            top: 50%;
            left: 10%;
            transform: translateY(-50%);
            color: #fff;
            z-index: 2;
            max-width: 500px;
        }

        .slide-content h2 {
            font-size: 3rem;
            text-transform: uppercase;
            margin-bottom: 10px;
            color: #2dd1ff; /* Naranja deportivo */
            font-weight: 800;
        }

        .slide-content p {
            font-size: 1.2rem;
            margin-bottom: 20px;
            color: #ddd;
        }

        .btn-banner {
            display: inline-block;
            padding: 12px 25px;
            background-color: #2dd1ff;
            color: #fff;
            text-decoration: none;
            font-weight: bold;
            text-transform: uppercase;
            border-radius: 4px;
            transition: background 0.3s ease;
        }

        .btn-banner:hover {
            background-color: #2dd1ff;
        }

        /* Flechas de Navegación */
        .arrow {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background-color: rgba(0, 0, 0, 0.5);
            color: white;
            padding: 15px;
            cursor: pointer;
            font-size: 1.5rem;
            border: none;
            z-index: 10;
            transition: background 0.3s;
        }

        .arrow:hover {
            background-color: #2dd1ff;
        }

        .arrow-prev { left: 15px; }
        .arrow-next { right: 15px; }

        /* Puntos de navegación (Dots) */
        .banner-dots {
            position: absolute;
            bottom: 20px;
            width: 100%;
            display: flex;
            justify-content: center;
            gap: 10px;
            z-index: 10;
        }

        .dot {
            width: 12px;
            height: 12px;
            background-color: rgba(255,255,255,0.5);
            border-radius: 50%;
            cursor: pointer;
            transition: background 0.3s;
        }

        .dot.active {
            background-color: #2dd1ff;
            transform: scale(1.2);
        }

        /* Ajuste responsivo para móviles */
        @media (max-width: 768px) {
            .banner-container { height: 300px; }
            .slide-content h2 { font-size: 2rem; }
            .slide-content p { font-size: 1rem; }
            .arrow { padding: 10px; font-size: 1rem; }
        }
        .depto-item {
            color: var(--cf-dark);
            border-radius: 8px;
            transition: all .2s ease;
        }
        .depto-item:hover {
            background: #f8f9fa;
            color: var(--cf-teal);
        }
        .depto-icono {
            font-size: 1.35rem;
            color: var(--cf-teal);
            margin-bottom: .25rem;
            transition: transform .2s ease;
        }
        .depto-item:hover .depto-icono {
            transform: scale(1.15);
        }
        .depto-label {
            font-size: .78rem;
            text-align: center;
            line-height: 1.2;
        }

        /* En móvil, 4 por fila con menos ruido */
        @media (max-width: 767.98px) {
            .depto-icono { font-size: 1.2rem; }
            .depto-label { font-size: .72rem; }
        }
    </style>


    {{-- ============================================================ --}}
    {{-- HERO --}}
    {{-- ============================================================ --}}
    <div class="banner-container">
        <!-- Flechas -->
        <button class="arrow arrow-prev">&#10094;</button>
        <button class="arrow arrow-next">&#10095;</button>

        <!-- Diapositivas (Reemplaza los URLs de Unsplash por tus imágenes de motos) -->
        <div class="banner-slider">
            <div class="slide active" style="background-image: url('{{ URL::asset('build/images/cflite.jpg') }}');">
                <div class="slide-content">
                    <h2 style="color: white!important;">CFMOTO ya tiene  </h2>
                    <h2>casa en Táchira. </h2>

                    <p>Visita nuestra tienda en la Av. Libertador y conoce la línea CFMOTO con un asesor.</p>
                    <a href="https://wa.me/584120675809?text={{ urlencode('Hola, vengo de la web de Osorio Group y quisiera información sobre la línea CFMOTO.') }}"
                       target="_blank"
                       rel="noopener noreferrer"
                       class="btn-banner"
                       style="display: inline-flex; align-items: center; gap: 8px;  ">
                        <i class="bi bi-whatsapp" style="font-size: 1.15rem;"></i>
                        Contactar por WhatsApp
                    </a>
                </div>
            </div>

            <div class="slide" style="background-image: url({{ URL::asset('build/images/teamcf.jpg') }});">
                <div class="slide-content">
                    <h2 style="color: white!important;">CFMOTO Team  </h2>
                    <h2> Amigos Moteros</h2>

                    <p>Deseas ser parte? Con gusto todo gusto te incluimos</p>
                    <a href="#" class="btn-banner">Conócenos </a>
                </div>
            </div>
            <!--
                        <div class="slide" style="background-image: url('https://unsplash.com');">
                            <div class="slide-content">
                                <h2>Accesorios & Cascos</h2>
                                <p>Equípate con lo mejor en seguridad y estilo. Descuentos de temporada.</p>
                                <a href="#" class="btn-banner">Ir a Tienda</a>
                            </div>
                        </div>

                        -->
        </div>

        <!-- Puntos Inferiores -->
        <div class="banner-dots">
            <div class="dot active"></div>
          <div class="dot"></div>
            <!--<div class="dot"></div>-->
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- FRANJA DE DEPARTAMENTOS (derivada de sainsta) --}}
    {{-- ============================================================ --}}
    @if(isset($departamentos) && $departamentos->count())
        <section class="py-3 border-top border-bottom">
            <div class="container">
                <div class="row text-center g-2  ">

                    @php
                        $departamentos = [
                            ['label' => 'Motos',       'href' => route('web.motos'),               'icon' => 'bi-bicycle'],
                            ['label' => 'Repuestos',   'href' => route('web.catalogo', ['familia' => '45']), 'icon' => 'bi-tools'],
                            ['label' => 'Lubricantes', 'href' => route('web.catalogo', ['familia' => '03.']), 'icon' => 'bi-droplet'],
                            ['label' => 'Auto Partes', 'href' => route('web.catalogo', ['familia' => '02.']), 'icon' => 'bi-car-front'],
                            ['label' => 'Ciclismo',    'href' => route('web.catalogo', ['familia' => '05.']), 'icon' => 'bi-bicycle'],
                            ['label' => 'Agrícola',    'href' => route('web.catalogo', ['familia' => '06.']), 'icon' => 'bi-tree'],
                            ['label' => 'Boutique',    'href' => route('web.catalogo', ['familia' => '09.']), 'icon' => 'bi-bag'],

                        ];
                    @endphp

                    @foreach($departamentos as $dep)
                        <div class="col">
                            <a href="#"
                               class="d-flex flex-column align-items-center text-decoration-none py-2 depto-item">
                                <i class="bi {{ $dep['icon'] }} depto-icono"></i>
                                <span class="fw-semibold depto-label">{{ $dep['label'] }}</span>
                            </a>
                        </div>
                    @endforeach

                </div>
            </div>
        </section>
    @endif

    <section class="py-5" style="background: #f8f9fa;">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-6 text-center">
                    <img src="{{ URL::asset('build/images/osoriowall.jpg') }}"
                         alt="CFMOTO"
                         class="img-fluid"
                         style="max-height: 440px; border-radius: 5px; object-fit: contain;">
                </div>
                <div class="col-lg-6">
                <span class="badge-cat mb-3 d-inline-block">
                    <i class="bi bi-geo-alt me-1"></i>
                    La Fría · San Cristóbal · San Antonio · Envíos a toda Venezuela
                </span>
                    <h1 class="display-5 fw-bold mb-3" style="line-height: 1.1;">
                        Concesionario, distribuidor <br>
                        e importador de <span style="color: var(--cf-teal);">repuestos</span>
                    </h1>
                    <p class="lead text-secondary mb-4" style="max-width: 520px;">
                        Adquiere tu moto en linea desde cualquier parte del pais.
                        Envios a toda venezuela.

                    </p>
                    <div class="d-flex gap-2 flex-wrap">
                        <a href="{{ route('web.motos') }}" class="btn btn-cf btn-lg px-4">
                            <i class="bi bi-bicycle me-1"></i> Ver motos
                        </a> <!-- { { route('web.catalogo', ['familia' => '45']) }}-->
                        <a href="#" class="btn btn-cf-outline btn-lg px-4">
                            <i class="bi bi-tools me-1"></i> Repuestos
                        </a>
                        <a href="#" class="btn btn-cf-outline btn-lg px-4">
                            <i class="bi bi-shop me-1"></i> Sucursales
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </section>


    {{-- ============================================================ --}}
    {{-- EXPLORA POR MARCA (instancias nivel 2 bajo 01.) --}}
    {{-- ============================================================ --}}
    @if(isset($marcasMotos) && $marcasMotos->count())
        <section class="py-5">
            <div class="container">
                <div class="d-flex justify-content-between align-items-end mb-4">
                    <div>
                        <h2 class="fw-bold mb-1">Encuentra tu moto por marca</h2>
                        <p class="text-secondary mb-0">
                            {{ $marcasMotos->sum('total_motos') }} motos disponibles en nuestras marcas distribuidoras
                        </p>
                    </div>
                    <a href="{{ route('web.motos') }}"
                       class="text-decoration-none fw-semibold"
                       style="color: var(--cf-teal);">
                        Ver todas <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
                @php $imoto=0; @endphp
                <div class="row g-3">
                    @foreach($marcasMotos as $m)
                        @php $imoto++; @endphp
                        <div class="col-lg-2 col-md-3 col-4">
                            <a href="{{ route('web.motos', ['marca' => $m->codalte]) }}"
                               class="d-block border rounded-3 p-3 text-center text-decoration-none card-marca h-100">
                                <div class="fw-bold mb-1" style="color: var(--cf-dark); font-size: .95rem;">
                                    <img src="{{ asset('build/images/brands/'. $m->imagen) }}" style="max-width: 100px">
                                </div>
                            </a>
                        </div>
                        @php if($imoto>12)break; @endphp
                    @endforeach
                </div>
            </div>
        </section>
    @endif


    {{-- ============================================================ --}}
    {{-- SUCURSALES CON TABS --}}
    {{-- ============================================================ --}}
    @include('web.partials.sucursales')


    {{-- ============================================================ --}}
    {{-- MOTOS DESTACADAS --}}
    {{-- ============================================================ --}}
    @if(isset($motosDestacadas) && $motosDestacadas->count())
        <section class="py-5" style="background: #f8f9fa;">
            <div class="container">
                <div class="d-flex justify-content-between align-items-end mb-4">
                    <div>
                        <h2 class="fw-bold mb-1">Motos destacadas</h2>
                        <p class="text-secondary mb-0">Los modelos más recientes de nuestro showroom</p>
                    </div>
                    <a href="{{ route('web.motos') }}"
                       class="text-decoration-none fw-semibold"
                       style="color: var(--cf-teal);">
                        Ver todas <i class="bi bi-arrow-right"></i>
                    </a>
                </div>

                <div class="row g-4">
                    @foreach($motosDestacadas as $moto)
                        @php $precio = (float) ($moto->costod3 ?? 0); @endphp
                        <div class="col-lg-3 col-md-4 col-6">
                            <div class="card-producto h-100 d-flex flex-column"> <!--{ { route('web.producto', $moto->codprod) }}-->
                                <a href="#"
                                   class="text-decoration-none d-block"
                                   aria-label="Ver {{ $moto->descrip }}">
                                    <img src="{{ asset('build/images/noimagen.jpg') }}"
                                         alt="{{ $moto->descrip }}"
                                         loading="lazy">
                                </a>
                                <div class="p-3 d-flex flex-column flex-grow-1">
                                    <small class="text-secondary text-uppercase"
                                           style="font-size: .7rem; letter-spacing: .5px;">
                                        {{ $moto->marca ?? 'Moto' }}
                                    </small>
                                    <h6 class="fw-bold mt-1 mb-2" style="font-size: .95rem; line-height: 1.3;"><!-- { { route('web.producto', $moto->codprod) }}-->
                                        <a href="#"
                                           class="text-decoration-none"
                                           style="color: var(--cf-dark);">
                                            {{ $moto->descrip }}
                                        </a>
                                    </h6>
                                    <div class="mt-auto d-flex justify-content-between align-items-center gap-2">
                                        @if($precio > 0)
                                            <span class="fw-bold" style="color: var(--cf-teal); font-size: .95rem;">
                                        REF ${{ number_format($precio, 0, ',', '.') }}
                                    </span>
                                        @else
                                            <span class="text-secondary" style="font-size: .78rem;">
                                        Precio por confirmar
                                    </span>
                                        @endif <!--{ { route('web.producto', $moto->codprod) }}-->
                                        <a href="#"
                                           class="btn btn-cf btn-sm px-3">
                                            Ver
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- ============================================================ --}}
    {{-- REPUESTOS + ASISTENTE --}}
    {{-- ============================================================ --}}
    <section id="repuestos" class="py-5">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-6">
                    <span class="badge-cat mb-3 d-inline-block">Repuestos originales</span>
                    <h2 class="fw-bold mb-3">
                        Distribuidor e importador de repuestos al mayor y detal
                    </h2>
                    <p class="text-secondary mb-4">
                        Importamos y distribuimos repuestos para motos. Atendemos a talleres, tiendas
                        y particulares en toda Venezuela: escríbele a un asesor, llámanos o visita una
                        tienda con la marca, el modelo y la pieza que necesitas y te confirmamos
                        disponibilidad y precio.
                    </p>
                    <ul class="list-unstyled text-secondary mb-4" style="font-size: .92rem;">
                        <li class="mb-2">
                            <i class="bi bi-check2-circle me-2" style="color: var(--cf-teal);"></i>
                            <strong>Dinos tu moto:</strong> marca, modelo y año.
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check2-circle me-2" style="color: var(--cf-teal);"></i>
                            <strong>Indica la pieza:</strong> una foto o el código del repuesto ayuda.
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check2-circle me-2" style="color: var(--cf-teal);"></i>
                            <strong>Te respondemos:</strong> disponibilidad, precio y forma de entrega.
                        </li>
                    </ul>
                    <div class="d-flex gap-2 flex-wrap">
                        <!-- { { route('web.catalogo', ['familia' => '45']) }}-->
                        <a href="#" class="btn btn-cf px-4">
                            <i class="bi bi-search me-1"></i> Explorar repuestos
                        </a>
                        <a href="tel:+584121717186" class="btn btn-cf-outline px-4">
                            <i class="bi bi-telephone me-1"></i> +58 412 1717186
                        </a>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="border rounded-3 p-4 bg-white">
                        <div class="d-flex align-items-center mb-3">
                            <div class="rounded-circle me-3 d-flex align-items-center justify-content-center flex-shrink-0"
                                 style="width: 48px; height: 48px; background: var(--cf-teal); color: #fff;">
                                <i class="bi bi-robot fs-5"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold">Asistente Oso</h6>
                                <small class="text-secondary">En línea · responde en segundos</small>
                            </div>
                        </div>
                        <p class="text-secondary small mb-3">
                            "Hola, soy Oso. Dime qué repuesto buscas (ej. filtro de aceite 250DUAL)
                            y te muestro disponibilidad, precio y alternativas."
                        </p>
                        <button type="button"
                                class="btn btn-cf w-100"
                                onclick="abrirChatWeb()">
                            <i class="bi bi-chat-dots me-1"></i> Preguntar al asistente
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>



@endsection

@section('css')
    <style>
        .card-marca {
            transition: all .2s ease;
            border-color: #E9ECEF !important;
            background: #fff;
        }
        .card-marca:hover {
            border-color: var(--cf-teal) !important;
            transform: translateY(-2px);
        }
        .card-marca:hover .fw-bold {
            color: var(--cf-teal) !important;
        }
    </style>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Estado inicial del carrito desde sesión
            const cartCount = {{ count(session('cart', [])) }};

            document.querySelectorAll('#cartCount, #cartCountMobile').forEach(el => {
                el.textContent = cartCount;
                el.style.display = cartCount > 0 ? '' : 'none';
            });
        });

        // Helper global para actualizar el contador cuando se añade al carrito
        window.actualizarContadorCarrito = function (total) {
            document.querySelectorAll('#cartCount, #cartCountMobile').forEach(el => {
                el.textContent = total;
                el.style.display = total > 0 ? '' : 'none';
            });
        };

        $(document).ready(function() {
            const slides = $('.slide');
            const dots = $('.dot');
            let currentIndex = 0;
            const slideInterval = 5000; // Tiempo de auto-rotación (5 segundos)
            let autoSlide;

            function showSlide(index) {
                // Controlar límites del índice
                if (index >= slides.length) currentIndex = 0;
                else if (index < 0) currentIndex = slides.length - 1;
                else currentIndex = index;

                // Transición suave ocultando/mostrando elementos
                slides.removeClass('active').fadeOut(400);
                dots.removeClass('active');

                slides.eq(currentIndex).fadeIn(400).addClass('active');
                dots.eq(currentIndex).addClass('active');
            }

            function startAutoSlide() {
                autoSlide = setInterval(function() {
                    showSlide(currentIndex + 1);
                }, slideInterval);
            }

            function stopAutoSlide() {
                clearInterval(autoSlide);
            }

            // Eventos de los botones Siguiente y Anterior
            $('.arrow-next').click(function() {
                stopAutoSlide();
                showSlide(currentIndex + 1);
                startAutoSlide();
            });

            $('.arrow-prev').click(function() {
                stopAutoSlide();
                showSlide(currentIndex - 1);
                startAutoSlide();
            });

            // Eventos al hacer clic en los puntos (dots)
            dots.click(function() {
                stopAutoSlide();
                const dotIndex = $(this).index();
                showSlide(dotIndex);
                startAutoSlide();
            });

            // Inicializar la rotación automática al cargar la página
            startAutoSlide();
        });

        function abrirChatWeb() {
            // Ajusta al selector de tu widget de chat flotante
            const launcher = document.querySelector('.chat-launcher-button');
            if (launcher) {
                launcher.click();
            } else {
                // Fallback: si aún no está montado, muestra aviso
                alert('El asistente estará disponible en breve.');
            }
        }
    </script>
@endsection
