{{-- resources/views/web/partials/grid-motos.blade.php --}}

<div id="grid-motos">

    @if($motos->count())

        <div class="row g-4">
            @foreach($motos as $moto)
                @php
                    $precio = (float) ($moto->preciod ?? 0);
                @endphp
                <div class="col-6 col-md-4">
                    <div class="card-producto h-100 d-flex flex-column">

                        @if($moto->cilindraje)
                            <div class="px-3 pt-3">
                                <span class="badge bg-dark-subtle text-dark"
                                      style="font-size: .68rem; letter-spacing: .5px; text-transform: uppercase;">
                                    {{ $moto->cilindraje }} CC
                                </span>
                            </div>
                        @endif

                        <a href="{{ route('web.producto', $moto->codprod) }}"
                           class="text-decoration-none d-block mt-2"
                           aria-label="Ver {{ $moto->descrip }}">
                            <img src="{{ asset('build/images/noimagen.jpg') }}"
                                 alt="{{ $moto->descrip }}"
                                 loading="lazy">
                        </a>

                        <div class="p-3 d-flex flex-column flex-grow-1">

                            <small class="text-secondary" style="font-size: .72rem; letter-spacing: .3px;">
                                {{ $moto->marca ?? ($marcaActiva->nombre_corto ?? 'Moto') }}
                                @if($moto->clase)
                                    · {{ $moto->clase }}
                                @endif
                            </small>

                            <h6 class="fw-bold mt-1 mb-2" style="font-size: .95rem; line-height: 1.3;">
                                <a href="{{ route('web.producto', $moto->codprod) }}"
                                   class="text-decoration-none"
                                   style="color: var(--cf-dark);">
                                    {{ $moto->descrip }}
                                </a>
                            </h6>

                            <small class="text-secondary d-block mb-3"
                                   style="font-size: .72rem; min-height: 1.2em;">
                                {{ \Illuminate\Support\Str::limit($moto->descrip2 ?? '', 60) }}
                            </small>

                            <div class="mt-auto d-flex justify-content-between align-items-center gap-2">
                                @if($precio > 0)
                                    <div>
                                        <small class="text-secondary d-block" style="font-size: .65rem;">REF</small>
                                        <span class="fw-bold" style="color: var(--cf-teal); font-size: 1rem;">
                                            ${{ number_format($precio, 0, ',', '.') }}
                                        </span>
                                    </div>
                                @else
                                    <span class="text-secondary" style="font-size: .78rem;">
                                        Precio por confirmar
                                    </span>
                                @endif

                                <a href="{{ route('web.producto', $moto->codprod) }}"
                                   class="btn btn-cf btn-sm px-3">
                                    Ver
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        @if($motos->hasPages())
            <div class="mt-4 d-flex justify-content-center">
                {{ $motos->links('pagination::bootstrap-5') }}
            </div>
        @endif

    @else
        <div class="text-center py-5">
            <i class="bi bi-inbox fs-1 text-secondary d-block mb-3"></i>
            <h5 class="fw-bold mb-2">No hay motos con esos filtros</h5>
            <p class="text-secondary mb-4">
                Prueba ajustando el rango de precio o cambiando de marca/estilo.
            </p>
            <a href="{{ route('web.motos') }}" class="btn btn-cf-outline">
                <i class="bi bi-arrow-left me-1"></i> Ver todas las motos
            </a>
        </div>
    @endif

</div>
