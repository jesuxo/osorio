{{-- resources/views/web/producto.blade.php --}}
@extends('layouts.web')

@section('title', ($nombreCatalogo ?? $producto->descrip) . ' · Osorio Group')

{{-- ============================================================ --}}
{{-- OPEN GRAPH (WhatsApp / Facebook / Telegram) --}}
{{-- ============================================================ --}}
@section('og')
    @php
        $ogTitulo = $nombreCatalogo ?? $producto->descrip;
        $ogDesc   = $producto->descrip2
            ?: 'Moto disponible en Osorio Group · Táchira, Venezuela';
        $ogImagen = $producto->imagen_thumb_url;
        $ogUrl    = url()->current();
    @endphp

    <meta property="og:type"         content="product">
    <meta property="og:site_name"    content="Osorio Group">
    <meta property="og:title"        content="{{ $ogTitulo }}">
    <meta property="og:description"  content="{{ $ogDesc }}">
    <meta property="og:image"        content="{{ $ogImagen }}">
    <meta property="og:image:width"  content="600">
    <meta property="og:image:height" content="600">
    <meta property="og:url"          content="{{ $ogUrl }}">

    <meta name="twitter:card"        content="summary_large_image">
    <meta name="twitter:title"       content="{{ $ogTitulo }}">
    <meta name="twitter:description" content="{{ $ogDesc }}">
    <meta name="twitter:image"       content="{{ $ogImagen }}">
@endsection

@section('content')
    <section class="py-5">
        <div class="container">
            <div class="row g-5">

                {{-- ============================================================ --}}
                {{-- GALERÍA --}}
                {{-- ============================================================ --}}
                <div class="col-lg-6">
                    @php
                        // Galería: usa el accessor del modelo. Si no existe (sin galería),
                        // cae de vuelta a la imagen principal como única entrada.
                        $galeria = $producto->galeria ?? collect();
                        if ($galeria->isEmpty()) {
                            $galeria = collect([(object) [
                                'imagen_url'       => $producto->imagen_url,
                                'imagen_thumb_url' => $producto->imagen_thumb_url,
                            ]]);
                        }
                        $portada = $galeria->first();
                    @endphp

                    <div class="producto-galeria position-relative">
                        <img id="img-producto"
                             src="{{ $portada?->imagen_url ?? asset('build/images/noimagen.jpg') }}"
                             alt="{{ $nombreCatalogo ?? $producto->descrip }}"
                             class="img-fluid rounded-3 border w-100"
                             width="{{ $producto->imagen_ancho ?: 800 }}"
                             height="{{ $producto->imagen_alto ?: 600 }}"
                             style="aspect-ratio: 4/3; object-fit: cover;">

                        <span id="color-activo-indicador"
                              class="position-absolute"
                              style="bottom: 16px; right: 16px;
                                     width: 28px; height: 28px; border-radius: 50%;
                                     background: {{ $producto->color_hex ?: 'transparent' }};
                                     border: 3px solid #fff;
                                     box-shadow: 0 2px 8px rgba(0,0,0,.2);
                                     {{ $producto->color_hex ? '' : 'display: none;' }}">
                        </span>
                    </div>

                    @if($galeria->count() > 1)
                        <div class="d-flex gap-2 mt-2 flex-wrap" id="thumbs-galeria"
                             data-codprod="{{ $producto->codprod }}">
                            @foreach($galeria as $i => $img)
                                <button type="button"
                                        class="btn p-0 btn-thumb {{ $i === 0 ? 'active' : '' }}"
                                        data-src="{{ $img?->imagen_url }}"
                                        style="width: 70px; height: 55px; border-radius: 6px; overflow: hidden;
                           border: 2px solid {{ $i === 0 ? 'var(--cf-teal)' : '#dee2e6' }};">
                                    <img src="{{ $img?->imagen_thumb_url }}"
                                         alt=""
                                         loading="lazy"
                                         style="width: 100%; height: 100%; object-fit: cover;">
                                </button>
                            @endforeach
                        </div>
                    @else
                        <div class="d-flex gap-2 mt-2 flex-wrap" id="thumbs-galeria"
                             data-codprod="{{ $producto->codprod }}"></div>
                    @endif
                </div>

                {{-- ============================================================ --}}
                {{-- INFO --}}
                {{-- ============================================================ --}}
                <div class="col-lg-6">

                    {{-- Breadcrumb --}}
                    <nav class="breadcrumb mb-2 small">
                        <a href="{{ route('web.home') }}" class="text-decoration-none">Inicio</a>
                        <span class="mx-2">/</span>
                        <a href="{{ route('web.motos') }}" class="text-decoration-none">Motos</a>
                        <span class="mx-2">/</span>
                        <span>{{ $nombreCatalogo ?? $producto->descrip }}</span>
                    </nav>

                    {{-- Marca · clase · código --}}
                    <small class="text-secondary text-uppercase">
                        {{ $producto->marca }}
                        @if($producto->clase) · {{ $producto->clase }} @endif
                        <span class="text-lowercase">· Cód. {{ $producto->codprod }}</span>
                    </small>

                    {{-- Título --}}
                    <h1 class="fw-bold mb-3" style="font-size: 2rem;">
                        {{ $nombreCatalogo ?? $producto->descrip }}
                    </h1>

                    {{-- Descripción corta --}}
                    @if($producto->descrip2)
                        <p class="text-secondary mb-4">{{ $producto->descrip2 }}</p>
                    @endif

                    {{-- Especificaciones --}}
                    <div class="row g-2 mb-4">
                        <div class="col-6">
                            <div class="border rounded p-2 small">
                                <small class="text-secondary d-block">Cilindraje</small>
                                <strong>{{ $producto->cilindraje ?? '—' }} cc</strong>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border rounded p-2 small">
                                <small class="text-secondary d-block">Categoría</small>
                                <strong>{{ $producto->clase ?? '—' }}</strong>
                            </div>
                        </div>
                    </div>

                    {{-- ============================================================ --}}
                    {{-- SELECTOR DE COLOR (si hay variantes) --}}
                    {{-- ============================================================ --}}
                    @if(isset($variantes) && $variantes->count() > 1)
                        <div class="mb-4">
                            <label class="fw-bold mb-2 d-block">
                                Color:
                                <span id="color-nombre" class="fw-normal text-secondary">
                                    {{ $producto->color_nombre ?? '' }}
                                </span>
                            </label>
                            <div class="d-flex flex-wrap gap-2" id="selector-color">
                                @foreach($variantes as $v)
                                    <button type="button"
                                            class="btn btn-color-selector {{ $v->codprod === $producto->codprod ? 'active' : '' }}"
                                            data-codprod="{{ $v->codprod }}"
                                            data-precio="{{ $v->costod3 }}"
                                            data-color="{{ $v->color_nombre }}"
                                            data-imagen="{{ $v->imagen_url }}"
                                            data-hex="{{ $v->color_hex }}"
                                            title="{{ $v->color_nombre ?? $v->descrip }}">
                                        <span style="display: inline-block; width: 20px; height: 20px; border-radius: 50%;
                                                     background: {{ $v->color_hex ?? '#ccc' }};
                                                     border: 2px solid #fff;
                                                     box-shadow: 0 0 0 1px #ddd;"></span>
                                        <span class="ms-1 small">{{ $v->color_nombre ?? 'Color' }}</span>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @elseif(!empty($producto->color_hex))
                        <div class="mb-4">
                            <label class="fw-bold mb-2 d-block">Color</label>
                            <div class="d-flex align-items-center gap-2">
                                <span style="display: inline-block; width: 20px; height: 20px; border-radius: 50%;
                                             background: {{ $producto->color_hex }};
                                             border: 2px solid #fff;
                                             box-shadow: 0 0 0 1px #ddd;"></span>
                                <span class="text-secondary">{{ $producto->color_nombre ?? '' }}</span>
                            </div>
                        </div>
                    @endif

                    {{-- ============================================================ --}}
                    {{-- PRECIO DINÁMICO --}}
                    {{-- ============================================================ --}}
                    <div class="mb-4 p-3 rounded" style="background: #f8f9fa;">
                        <span class="text-secondary small d-block">Precio referencial</span>
                        <span class="fw-bold" id="precio-dinamico"
                              style="font-size: 2rem; color: var(--cf-teal); line-height: 1.1;">
                            @if($producto->costod3 > 0)
                                ${{ number_format($producto->costod3, 0, ',', '.') }}
                            @else
                                <span style="font-size: 1.1rem; color: #6C757D;">Precio por confirmar</span>
                            @endif
                        </span>
                    </div>

                    {{-- ============================================================ --}}
                    {{-- CTA --}}
                    {{-- ============================================================ --}}
                    @php
                        $textoWhatsapp = 'Hola, me interesa la '
                            . ($nombreCatalogo ?? $producto->descrip)
                            . ($producto->color_nombre ? ' ' . $producto->color_nombre : '')
                            . ".\n" . url()->current();
                    @endphp

                    <div class="d-flex gap-2 flex-wrap mb-4">
                        <button class="btn btn-cf btn-lg flex-grow-1 btn-add-cart"
                                data-codprod="{{ $producto->codprod }}">
                            <i class="bi bi-cart-plus me-1"></i> Añadir al carrito
                        </button>
                        <a id="btn-whatsapp"
                           href="https://wa.me/584120675809?text={{ urlencode($textoWhatsapp) }}"
                           target="_blank"
                           rel="noopener"
                           class="btn btn-success btn-lg"
                           title="Consultar por WhatsApp">
                            <i class="bi bi-whatsapp"></i>
                        </a>
                    </div>

                    {{-- ============================================================ --}}
                    {{-- COMPRA EN 3 PASOS --}}
                    {{-- ============================================================ --}}
                    <div class="border rounded-3 p-3">
                        <h6 class="fw-bold mb-3">Compra en 3 pasos</h6>
                        <ol class="list-unstyled mb-0 small">
                            <li class="d-flex mb-2">
                                <span class="badge bg-dark rounded-circle me-2 flex-shrink-0"
                                      style="width:22px;height:22px;line-height:14px;">1</span>
                                <div><strong>Identifícate</strong> · Cédula y licencia para el documento de venta.</div>
                            </li>
                            <li class="d-flex mb-2">
                                <span class="badge bg-dark rounded-circle me-2 flex-shrink-0"
                                      style="width:22px;height:22px;line-height:14px;">2</span>
                                <div><strong>Confirma</strong> · Un asesor valida tus datos y tu pedido.</div>
                            </li>
                            <li class="d-flex">
                                <span class="badge bg-dark rounded-circle me-2 flex-shrink-0"
                                      style="width:22px;height:22px;line-height:14px;">3</span>
                                <div><strong>Retira o recibe</strong> · En sucursal o en tu dirección.</div>
                            </li>
                        </ol>
                    </div>

                </div>
            </div>

            {{-- ============================================================ --}}
            {{-- RELACIONADOS --}}
            {{-- ============================================================ --}}
            @if(isset($relacionados) && $relacionados->count())
                <hr class="my-5">
                <h3 class="fw-bold mb-4">También te puede interesar</h3>
                <div class="row g-4">
                    @foreach($relacionados as $rel)
                        <div class="col-lg-3 col-md-4 col-6">
                            @include('web.partials.card-moto', ['moto' => $rel])
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>
@endsection

@section('css')
    <style>
        .btn-color-selector {
            background: #fff;
            border: 2px solid #E9ECEF;
            border-radius: 50px;
            padding: .35rem .85rem;
            display: inline-flex;
            align-items: center;
            transition: all .15s ease;
        }
        .btn-color-selector:hover {
            border-color: var(--cf-teal);
        }
        .btn-color-selector.active {
            border-color: var(--cf-teal);
            background: rgba(45, 209, 255, 0.08);
            font-weight: 600;
        }

        .btn-thumb {
            padding: 0 !important;
            background: none;
            transition: transform .15s ease;
        }
        .btn-thumb:hover {
            transform: scale(1.05);
        }
    </style>
@endsection

@section('scripts')
    <script>
        window.VARIANTES_DATA = @json($variantesData ?? []);


        document.addEventListener('DOMContentLoaded', function () {

            const btnCarrito  = document.querySelector('.btn-add-cart');
            const btnWhatsapp = document.getElementById('btn-whatsapp');
            const precioEl    = document.getElementById('precio-dinamico');
            const colorEl     = document.getElementById('color-nombre');
            const indicador   = document.getElementById('color-activo-indicador');
            const imgProducto = document.getElementById('img-producto');
            const thumbsWrap  = document.getElementById('thumbs-galeria');

            /* ============================================================ */
            /* SELECTOR DE COLOR + GALERÍA POR COLOR                        */
            /* ============================================================ */

// Reconstruye los thumbs para un codprod dado.
// Si no hay galería, oculta el contenedor.
            function renderizarGaleria(codprod, imagenPrincipal) {
                const wrap = document.getElementById('thumbs-galeria');
                if (!wrap) return;

                const data = (window.VARIANTES_DATA || {})[codprod];
                const galeria = (data && Array.isArray(data.galeria)) ? data.galeria : [];

                // Si la variante no tiene galería propia, cae al thumb de la imagen principal
                const items = galeria.length
                    ? galeria
                    : [{ grande: imagenPrincipal, thumb: imagenPrincipal }];

                if (items.length <= 1) {
                    // Sin galería adicional → ocultar
                    wrap.innerHTML = '';
                    wrap.style.display = 'none';
                    return;
                }

                wrap.style.display = '';
                wrap.dataset.codprod = codprod;
                wrap.innerHTML = '';

                items.forEach((img, i) => {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'btn p-0 btn-thumb' + (i === 0 ? ' active' : '');
                    btn.dataset.src = img.grande;
                    btn.style.cssText = `
            width: 70px; height: 55px; border-radius: 6px; overflow: hidden;
            border: 2px solid ${i === 0 ? 'var(--cf-teal)' : '#dee2e6'};
        `;

                    const imgEl = document.createElement('img');
                    imgEl.src = img.thumb;
                    imgEl.alt = '';
                    imgEl.loading = 'lazy';
                    imgEl.style.cssText = 'width: 100%; height: 100%; object-fit: cover;';

                    btn.appendChild(imgEl);
                    wrap.appendChild(btn);
                });
            }

            document.addEventListener('click', function (e) {
                const btn = e.target.closest('.btn-color-selector');
                if (!btn) return;

                e.preventDefault();

                document.querySelectorAll('.btn-color-selector').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');

                const codprod = btn.dataset.codprod;
                const precio  = parseFloat(btn.dataset.precio || 0);
                const color   = btn.dataset.color || '';
                const hex     = btn.dataset.hex || '';
                const imagen  = btn.dataset.imagen;

                // Imagen grande
                if (imgProducto && imagen) {
                    imgProducto.src = imagen;
                }

                // 🔥 Reconstruir galería de la nueva variante
                renderizarGaleria(codprod, imagen);

                // Botón carrito
                if (btnCarrito) btnCarrito.dataset.codprod = codprod;

                // Precio
                if (precioEl) {
                    precioEl.innerHTML = precio > 0
                        ? '$' + precio.toLocaleString('es-VE', { minimumFractionDigits: 0 })
                        : '<span style="font-size: 1.1rem; color: #6C757D;">Precio por confirmar</span>';
                }

                // Nombre del color
                if (colorEl) colorEl.textContent = color;

                // Indicador de color
                if (indicador) {
                    if (hex) {
                        indicador.style.background = hex;
                        indicador.style.display = '';
                    } else {
                        indicador.style.display = 'none';
                    }
                }

                // WhatsApp con variante + URL
                if (btnWhatsapp) {
                    const nombre = document.querySelector('h1')?.textContent?.trim() || '';
                    const texto = encodeURIComponent(
                        `Hola, me interesa la ${nombre}${color ? ' ' + color : ''}.\n${window.location.href}`
                    );
                    btnWhatsapp.href = 'https://wa.me/584120675809?text=' + texto;
                }

                // URL sin recargar
                const url = new URL(window.location.href);
                url.pathname = url.pathname.replace(/\/producto\/[^/]+$/, '/producto/' + codprod);
                history.replaceState({}, '', url);
            });

            /* ============================================================ */
            /* MINIATURAS DE GALERÍA                                        */
            /* ============================================================ */
            document.addEventListener('click', function (e) {
                const btn = e.target.closest('.btn-thumb');
                if (!btn || !imgProducto) return;

                imgProducto.src = btn.dataset.src;

                document.querySelectorAll('.btn-thumb').forEach(b => {
                    b.classList.remove('active');
                    b.style.borderColor = '#dee2e6';
                });
                btn.classList.add('active');
                btn.style.borderColor = 'var(--cf-teal)';
            });

            /* ============================================================ */
            /* AÑADIR AL CARRITO                                            */
            /* ============================================================ */
            if (btnCarrito) {
                btnCarrito.addEventListener('click', function () {
                    const codprod  = this.dataset.codprod;
                    const original = this.innerHTML;

                    this.disabled  = true;
                    this.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Añadiendo…';

                    fetch('{{ route("web.cart.add") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({ codprod, cantidad: 1 })
                    })
                        .then(r => r.json())
                        .then(data => {
                            if (data.success) {
                                if (typeof window.actualizarContadorCarrito === 'function') {
                                    window.actualizarContadorCarrito(data.cart_count);
                                }

                                this.innerHTML = '<i class="bi bi-check me-1"></i> Añadido al carrito';
                                this.classList.add('btn-success');
                                this.classList.remove('btn-cf');

                                setTimeout(() => {
                                    this.innerHTML = original;
                                    this.classList.remove('btn-success');
                                    this.classList.add('btn-cf');
                                    this.disabled = false;
                                }, 1800);
                            } else {
                                this.innerHTML = original;
                                this.disabled  = false;
                            }
                        })
                        .catch(err => {
                            console.error(err);
                            this.innerHTML = original;
                            this.disabled  = false;
                        });
                });
            }
        });
    </script>
@endsection
