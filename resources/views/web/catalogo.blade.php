@extends('layouts.web')

@section('title', 'Catálogo · CFMOTO Táchira')

@section('content')
    <div class="container py-5">
        <div class="row">

            {{-- SIDEBAR FILTROS --}}
            <div class="col-lg-3 mb-4">
                <div class="border rounded-3 p-3">
                    <h6 class="fw-bold mb-3">Categorías</h6>
                    @foreach(['motos' => 'Motos', 'repuestos' => 'Repuestos', 'lubricantes' => 'Lubricantes', 'boutique' => 'Boutique'] as $key => $label)
                        <a href="{{ route('web.catalogo', ['categoria' => $key, 'q' => $busqueda]) }}"
                           class="d-block py-2 text-decoration-none {{ $categoria === $key ? 'fw-bold' : 'text-secondary' }}"
                           style="{{ $categoria === $key ? 'color: var(--cf-teal) !important;' : '' }}">
                            {{ $label }}
                        </a>
                    @endforeach
                </div>

                @if(session('historial_busquedas'))
                    <div class="border rounded-3 p-3 mt-3">
                        <h6 class="fw-bold mb-2">Búsquedas recientes</h6>
                        @foreach(array_reverse(session('historial_busquedas')) as $h)
                            <a href="{{ route('web.catalogo', ['q' => $h]) }}" class="badge-cat d-inline-block mb-1 text-decoration-none">{{ $h }}</a>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- GRID PRODUCTOS --}}
            <div class="col-lg-9">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="fw-bold mb-0">{{ ucfirst($categoria) }}</h4>
                    <small class="text-secondary">{{ $productos->total() }} resultados</small>
                </div>

                @if($busqueda)
                    <p class="text-secondary">Resultados para: <strong>{{ $busqueda }}</strong>
                        <a href="{{ route('web.catalogo', ['categoria' => $categoria]) }}" class="ms-2 text-decoration-none">Limpiar</a>
                    </p>
                @endif

                <div class="row g-3">
                    @forelse($productos as $p)
                        <div class="col-lg-4 col-md-6">
                            <div class="card-producto h-100">
                                <img src="{{ asset('build/images/noimagen.jpg') }}" alt="{{ $p->descrip }}">
                                <div class="p-3">
                                    <small class="text-secondary">{{ $p->marca }}</small>
                                    <h6 class="fw-bold mb-2" style="font-size: .9rem;">{{ $p->descrip }}</h6>
                                    <small class="text-secondary d-block mb-2">Código: {{ $p->codprod }}</small>
                                    @if($p->preciod > 0)
                                        <p class="fw-bold mb-2" style="color: var(--cf-teal);">${{ number_format($p->preciod, 2) }}</p>
                                    @endif
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('web.producto', $p->codprod) }}" class="btn btn-cf-outline btn-sm flex-grow-1">Ver</a>
                                        <button class="btn btn-cf btn-sm flex-grow-1 btn-add-cart" data-codprod="{{ $p->codprod }}">
                                            <i class="bi bi-cart-plus"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12 text-center text-secondary py-5">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            No se encontraron productos con esos criterios.
                        </div>
                    @endforelse
                </div>

                <div class="mt-4">{{ $productos->links() }}</div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.querySelectorAll('.btn-add-cart').forEach(btn => {
            btn.addEventListener('click', function () {
                const codprod = this.dataset.codprod;
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
                            document.getElementById('cartCount').textContent = data.cart_count;
                            this.innerHTML = '<i class="bi bi-check"></i>';
                            setTimeout(() => this.innerHTML = '<i class="bi bi-cart-plus"></i>', 1200);
                        }
                    });
            });
        });
    </script>
@endsection
