{{-- resources/views/web/producto.blade.php --}}
@extends('layouts.web')

@section('title', ($nombreCatalogo ?? $producto->descrip) . ' · Osorio Group')

@section('content')
    <section class="py-5">
        <div class="container">
            <div class="row g-5">

                {{-- ============================================================ --}}
                {{-- GALERÍA --}}
                {{-- ============================================================ --}}
                <div class="col-lg-6">
                    <div class="producto-galeria position-relative">

                        <img id="img-producto"
                             src="{{ asset('build/images/noimagen.jpg') }}"
                             alt="{{ $nombreCatalogo ?? $producto->descrip }}"
                             class="img-fluid rounded-3 border w-100">

                        {{-- Indicador de color activo (puntito) --}}
                        @if(!empty($producto->color_hex))
                            <span id="color-activo-indicador"
                                  class="position-absolute"
                                  style="bottom: 16px; right: 16px;
                                     width: 28px; height: 28px; border-radius: 50%;
                                     background: {{ $producto->color_hex }};
                                     border: 3px solid #fff;
                                     box-shadow: 0 2px 8px rgba(0,0,0,.2);">
                        </span>
                        @endif
                    </div>
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
                    <div class="d-flex gap-2 flex-wrap mb-4">
                        <button class="btn btn-cf btn-lg flex-grow-1 btn-add-cart"
                                data-codprod="{{ $producto->codprod }}">
                            <i class="bi bi-cart-plus me-1"></i> Añadir al carrito
                        </button>
                        <a id="btn-whatsapp"
                           href="https://wa.me/584120675809?text={{ urlencode('Hola, me interesa la ' . ($nombreCatalogo ?? $producto->descrip)) }}"
                           target="_blank"
                           rel="noopener"
                           class="btn btn-success btn-lg"
                           title="Consultar por WhatsApp">
                            <i class="bi bi-whatsapp"></i>
                        </a>
                    </div>

                    {{-- ============================================================ --}}
                    {{-- COMPRA EN 4 PASOS --}}
                    {{-- ============================================================ --}}
                    <div class="border rounded-3 p-3">
                        <h6 class="fw-bold mb-3">Compra en 4 pasos</h6>
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
    </style>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            const btnCarrito    = document.querySelector('.btn-add-cart');
            const btnWhatsapp   = document.getElementById('btn-whatsapp');
            const precioEl      = document.getElementById('precio-dinamico');
            const colorEl       = document.getElementById('color-nombre');
            const indicador     = document.getElementById('color-activo-indicador');

            // ------------------------------------------------------------
            // SELECTOR DE COLOR
            // ------------------------------------------------------------
            document.querySelectorAll('.btn-color-selector').forEach(btn => {
                btn.addEventListener('click', function () {
                    // Marcar activo
                    document.querySelectorAll('.btn-color-selector').forEach(b => b.classList.remove('active'));
                    this.classList.add('active');

                    const codprod = this.dataset.codprod;
                    const precio  = parseFloat(this.dataset.precio || 0);
                    const color   = this.dataset.color || '';
                    const hex     = this.dataset.hex || '';

                    // Actualizar botón de carrito
                    if (btnCarrito) btnCarrito.dataset.codprod = codprod;

                    // Actualizar precio
                    if (precioEl) {
                        precioEl.innerHTML = precio > 0
                            ? '$' + precio.toLocaleString('es-VE', { minimumFractionDigits: 0 })
                            : '<span style="font-size: 1.1rem; color: #6C757D;">Precio por confirmar</span>';
                    }

                    // Actualizar nombre del color
                    if (colorEl) colorEl.textContent = color;

                    // Actualizar indicador de color sobre la imagen
                    if (indicador && hex) indicador.style.background = hex;

                    // Actualizar WhatsApp con la variante
                    if (btnWhatsapp) {
                        const texto = encodeURIComponent(
                            'Hola, me interesa la ' + (color ? (this.closest('h1')?.textContent?.trim() || '') + ' ' + color : (this.closest('h1')?.textContent?.trim() || ''))
                        );
                        btnWhatsapp.href = 'https://wa.me/584120675809?text=' + texto;
                    }

                    // Actualizar URL sin recargar
                    const url = new URL(window.location.href);
                    url.pathname = url.pathname.replace(/\/producto\/[^/]+$/, '/producto/' + codprod);
                    history.replaceState({}, '', url);
                });
            });

            // ------------------------------------------------------------
            // AÑADIR AL CARRITO
            // ------------------------------------------------------------
            if (btnCarrito) {
                btnCarrito.addEventListener('click', function () {
                    const codprod = this.dataset.codprod;
                    const original = this.innerHTML;

                    this.disabled = true;
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
                                // Actualizar contador global
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
                                this.disabled = false;
                            }
                        })
                        .catch(err => {
                            console.error(err);
                            this.innerHTML = original;
                            this.disabled = false;
                        });
                });
            }
        });
    </script>
@endsection
