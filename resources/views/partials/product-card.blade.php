@php
    $totalStock = 0;
    $sucursalesStock = [];
    if(isset($producto->existencias_por_sucursal) && $producto->existencias_por_sucursal->count() > 0) {
        foreach($producto->existencias_por_sucursal as $ex) {
            if($ex->existen > 0) {
                $totalStock += $ex->existen;
                $sucursalesStock[] = [
                    'sucursal' => $ex->sucursal->descrip ?? 'Sucursal',
                    'cantidad' => $ex->existen
                ];
            }
        }
    }
    $hasStock = $totalStock > 0;
    $imageUrl = isset($producto->productImg) && $producto->productImg ? asset('images/products/'.$producto->productImg) : asset('build/images/noimagen.jpg');
@endphp

<div class="product-card">
    <!-- Imagen -->
    <div class="product-image">
        <img src="{{ $imageUrl }}" alt="{{ $producto->descrip }}" loading="lazy">
        <span class="product-badge {{ $hasStock ? 'stock' : 'out-of-stock' }}">
            {{ $hasStock ? 'Disponible' : 'Sin stock' }}
        </span>
    </div>

    <!-- Cuerpo -->
    <div class="product-body">
        <div class="product-category">
            {{ $producto->instancia->descrip ?? 'General' }}
        </div>
        <h3 class="product-title">
            <a href="{{ route('shop.product', $producto->codprod) }}">
                {{ $producto->descrip }}
            </a>
        </h3>
        <div class="d-flex justify-content-between align-items-center">
            <span class="product-price">${{ number_format($producto->costod3 ?? $producto->preciod ?? 0, 2) }}</span>
            <span class="product-stock-info">
                <i class="bi bi-{{ $hasStock ? 'check-circle-fill text-success' : 'x-circle-fill text-danger' }}"></i>
                <span class="{{ $hasStock ? 'in-stock' : 'out-of-stock' }}">
                    {{ $hasStock ? 'Stock: '.$totalStock : 'Agotado' }}
                </span>
            </span>
        </div>

        <!-- Footer con sucursales -->
        <div class="product-footer">
            <div class="sucursales">
                @if(count($sucursalesStock) > 0)
                    @foreach(array_slice($sucursalesStock, 0, 2) as $suc)
                        <span class="badge-sucursal">{{ $suc['sucursal'] }}: {{ $suc['cantidad'] }}</span>
                    @endforeach
                    @if(count($sucursalesStock) > 2)
                        <span class="badge-sucursal">+{{ count($sucursalesStock) - 2 }}</span>
                    @endif
                @else
                    <span class="text-muted">Sin stock</span>
                @endif
            </div>
            <a href="{{ route('shop.product', $producto->codprod) }}" class="btn btn-sm btn-outline-primary-custom">
                Ver <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>
    </div>
</div>
