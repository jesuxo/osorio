@extends('layouts.app')

@section('title', $producto->descrip . ' - Osorio Group')

@section('content')
    <div class="container py-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('shop.index') }}">Inicio</a></li>
                @if($producto->instancia)
                    <li class="breadcrumb-item">
                        <a href="{{ route('shop.category', $producto->instancia->codinst) }}">
                            {{ $producto->instancia->descrip }}
                        </a>
                    </li>
                @endif
                <li class="breadcrumb-item active">{{ $producto->descrip }}</li>
            </ol>
        </nav>

        <div class="row g-4">
            <!-- Imagen -->
            <div class="col-lg-5">
                <div class="product-detail-image">
                    <img src="{{ asset('build/images/noimagen.jpg') }}" alt="{{ $producto->descrip }}">
                </div>
            </div>

            <!-- Info -->
            <div class="col-lg-7">
                <div class="product-detail-info">
                    <h1>{{ $producto->descrip }}</h1>
                    <div class="sku">Código: <strong>{{ $producto->codprod }}</strong></div>

                    @if($producto->marca)
                        <div class="mt-1 text-muted">Marca: {{ $producto->marca }}</div>
                    @endif

                    <div class="price">${{ number_format($producto->costod3 ?? $producto->preciod ?? 0, 2) }}</div>

                    @php
                        $totalStock = 0;
                        $sucursalesStock = [];
                        if(isset($producto->existencias_por_sucursal)) {
                            foreach($producto->existencias_por_sucursal as $ex) {
                                if($ex->existen > 0) {
                                    $totalStock += $ex->existen;
                                    $sucursalesStock[] = [
                                        'sucursal' => $ex->sucursal->descrip ?? 'Sucursal',
                                        'cantidad' => $ex->existen,
                                        'deposito' => $ex->deposito->descrip ?? ''
                                    ];
                                }
                            }
                        }
                        $hasStock = $totalStock > 0;
                    @endphp

                    <div class="mt-3">
                    <span class="stock-status {{ $hasStock ? 'in-stock' : 'out-of-stock' }}">
                        <i class="bi bi-{{ $hasStock ? 'check-circle-fill' : 'x-circle-fill' }} me-1"></i>
                        {{ $hasStock ? 'Disponible ('.$totalStock.' unidades)' : 'Sin stock disponible' }}
                    </span>
                    </div>

                    @if($producto->descrip2)
                        <div class="mt-3">
                            <h6 class="fw-semibold">Descripción:</h6>
                            <p class="text-muted">{{ $producto->descrip2 }}</p>
                        </div>
                    @endif

                    <!-- Sucursales con stock -->
                    @if(count($sucursalesStock) > 0)
                        <div class="sucursales-list mt-3">
                            <h6 class="fw-semibold"><i class="bi bi-shop me-1"></i> Disponible en:</h6>
                            @foreach($sucursalesStock as $suc)
                                <div class="sucursal-item">
                                <span>
                                    <i class="bi bi-geo-alt me-1 text-muted"></i>
                                    {{ $suc['sucursal'] }}
                                    @if($suc['deposito'])
                                        <span class="text-muted">({{ $suc['deposito'] }})</span>
                                    @endif
                                </span>
                                    <span class="fw-semibold text-success">{{ $suc['cantidad'] }} unidades</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-warning mt-3">
                            <i class="bi bi-exclamation-triangle me-1"></i>
                            Este producto no tiene stock disponible en este momento.
                        </div>
                    @endif

                    <div class="mt-4 d-flex gap-3">
                        <a href="{{ route('shop.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                            <i class="bi bi-arrow-left me-1"></i> Seguir comprando
                        </a>
                        @auth
                            <button class="btn btn-primary-custom rounded-pill px-5" onclick="alert('Funcionalidad de cotización próximamente')">
                                <i class="bi bi-cart-plus me-1"></i> Solicitar cotización
                            </button>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-primary-custom rounded-pill px-5">
                                <i class="bi bi-box-arrow-in-right me-1"></i> Iniciar sesión para cotizar
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
