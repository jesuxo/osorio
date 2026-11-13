{{-- web/producto.blade.php --}}
@extends('layouts.web')

@section('title', $producto->descrip . ' · Osorio Group')

@section('content')
    <section class="py-5">
        <div class="container">
            <div class="row g-5">

                {{-- Galería (izquierda) --}}
                <div class="col-lg-6">
                    <div class="producto-galeria">
                        <img src="{{ asset('build/images/noimagen.jpg') }}"
                             alt="{{ $producto->descrip }}"
                             class="img-fluid rounded-3 border w-100">
                    </div>
                </div>

                {{-- Info (derecha) --}}
                <div class="col-lg-6">
                    <nav class="breadcrumb mb-2 small">
                        <a href="{{ route('web.motos') }}" class="text-decoration-none">Motos</a>
                        <span class="mx-2">/</span>
                        <span>{{ $producto->descrip }}</span>
                    </nav>

                    <small class="text-secondary text-uppercase">
                        {{ $producto->marca }} · {{ $producto->clase }}
                    </small>

                    <h1 class="fw-bold mb-3">{{ $producto->descrip }}</h1>

                    <p class="text-secondary mb-4">{{ $producto->descrip2 }}</p>

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
                                <small class="text-secondary d-block">Código</small>
                                <strong>{{ $producto->codprod }}</strong>
                            </div>
                        </div>
                    </div>

                    {{-- Precio --}}
                    <div class="mb-4 p-3 rounded" style="background: #f8f9fa;">
                        @if($producto->costod3 > 0)
                            <span class="text-secondary small d-block">Precio referencial</span>
                            <span class="fw-bold" style="font-size: 2rem; color: var(--cf-teal);">
                            ${{ number_format($producto->costod3, 0, ',', '.') }}
                        </span>
                        @else
                            <span class="text-secondary">Precio por confirmar con un asesor</span>
                        @endif
                    </div>

                    {{-- CTA --}}
                    <div class="d-flex gap-2 flex-wrap mb-4">
                        <button class="btn btn-cf btn-lg flex-grow-1 btn-add-cart"
                                data-codprod="{{ $producto->codprod }}">
                            <i class="bi bi-cart-plus me-1"></i> Añadir al carrito
                        </button>
                        <a href="https://wa.me/584120675809?text={{ urlencode('Hola, me interesa la ' . $producto->descrip) }}"
                           target="_blank"
                           class="btn btn-success btn-lg">
                            <i class="bi bi-whatsapp"></i>
                        </a>
                    </div>

                    {{-- Los 4 pasos --}}
                    <div class="border rounded-3 p-3">
                        <h6 class="fw-bold mb-3">Compra en 4 pasos</h6>
                        <ol class="list-unstyled mb-0 small">
                            <li class="d-flex mb-2">
                                <span class="badge bg-dark rounded-circle me-2" style="width:22px;height:22px;">1</span>
                                <div><strong>Identifícate</strong> · Cédula y licencia para el documento de venta.</div>
                            </li>
                            <li class="d-flex mb-2">
                                <span class="badge bg-dark rounded-circle me-2" style="width:22px;height:22px;">2</span>
                                <div><strong>Confirma</strong> · Un asesor valida tus datos y tu pedido.</div>
                            </li>
                            <li class="d-flex">
                                <span class="badge bg-dark rounded-circle me-2" style="width:22px;height:22px;">3</span>
                                <div><strong>Retira o recibe</strong> · En sucursal o en tu dirección.</div>
                            </li>
                        </ol>
                    </div>
                </div>
            </div>

            {{-- Relacionados --}}
            @if($relacionados->count())
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
