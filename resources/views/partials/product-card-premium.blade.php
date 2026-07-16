@php
    $totalStock = $producto->existencias_por_sucursal->sum('existen');
    $hasStock = $totalStock > 0;
    $sucursalesStock = [];
    if(isset($producto->existencias_por_sucursal) && $producto->existencias_por_sucursal->count() > 0) {
        foreach($producto->existencias_por_sucursal as $ex) {
            if($ex->existen > 0) {
                $sucursalesStock[] = [
                    'sucursal' => $ex->sucursal->descrip ?? 'Sucursal',
                    'cantidad' => $ex->existen
                ];
            }
        }
    }
@endphp

<div class="product-card-premium">
    <div class="product-image">
        <img src="{{ asset('build/images/noimagen.jpg') }}" alt="{{ $producto->descrip }}" loading="lazy">
        <span class="product-badge {{ $hasStock ? 'stock' : 'out-of-stock' }}">
            <i class="bi bi-{{ $hasStock ? 'check-circle-fill' : 'x-circle-fill' }} me-1"></i>
            {{ $hasStock ? 'Stock' : 'Agotado' }}
        </span>
    </div>
    <div class="product-body">
        <div class="product-category">{{ $producto->instancia->descrip ?? 'General' }}</div>
        <div class="product-title">
            <a href="{{ route('shop.product', $producto->codprod) }}">{{ $producto->descrip }}</a>
        </div>
        <div class="d-flex justify-content-between align-items-center">
            <span class="product-price">${{ number_format($producto->costod3 ?? $producto->preciod ?? 0, 2) }}</span>
            <span class="product-stock-info">
                <span class="{{ $hasStock ? 'in-stock' : 'out-of-stock' }}">
                    <i class="bi bi-{{ $hasStock ? 'box' : 'x-circle' }} me-1"></i>{{ $hasStock ? $totalStock : '0' }}
                </span>
            </span>
        </div>
        <div class="product-footer">
            <div class="sucursales">
                @if(count($sucursalesStock) > 0)
                    @foreach(array_slice($sucursalesStock, 0, 2) as $suc)
                        <span class="badge-sucursal">{{ \Illuminate\Support\Str::limit($suc['sucursal'], 10) }}: {{ $suc['cantidad'] }}</span>
                    @endforeach
                    @if(count($sucursalesStock) > 2)
                        <span class="badge-sucursal">+{{ count($sucursalesStock) - 2 }}</span>
                    @endif
                @else
                    <span class="text-muted">Sin stock</span>
                @endif
            </div>
            <a href="{{ route('shop.product', $producto->codprod) }}" class="btn btn-sm btn-link text-decoration-none" style="color:var(--osorio-gold); font-size:0.7rem;">
                Ver <i class="bi bi-arrow-right"></i>
            </a>
        </div>
    </div>
</div>
