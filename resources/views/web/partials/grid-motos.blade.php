{{-- resources/views/web/partials/grid-motos.blade.php --}}

<div id="grid-motos">

    @if($motos->count())

        <div class="row g-4">
            @foreach($motos as $moto)
                @php
                    $precio = (float) ($moto->costod3 ?? 0);
                @endphp
                <div class="col-6 col-md-4">



                    <div class="card-producto h-100 d-flex flex-column">

                        <a href="{{ route('web.producto', $moto->codprod) }}"
                           class="text-decoration-none d-block mt-2"
                           aria-label="Ver {{ $moto->descrip }}">
                            <img src="{{ asset('build/images/noimagen.jpg') }}"
                                 alt="{{ $moto->descrip }}"
                                 loading="lazy">
                        </a>

                        <div class="p-3 d-flex flex-column flex-grow-1">

                            @php
                                $variantes = $moto->variantes ?? collect([$moto]);
                                $tieneMultiples = $variantes->count() > 1;
                                $nombreCatalogo = $moto->grupo_nombre ?: $moto->descrip;
                            @endphp

                            @if($moto->cilindraje)
                                <span class="badge bg-dark-subtle text-dark align-self-start mb-2"
                                      style="font-size: .68rem; letter-spacing: .5px;">
            {{ $moto->cilindraje }} CC
        </span>
                            @endif

                            <small class="text-secondary text-uppercase"
                                   style="font-size: .7rem; letter-spacing: .5px; font-weight: 600;">
                                {{ $moto->marca ?? 'CFMOTO' }}
                                @if($moto->clase) · {{ $moto->clase }} @endif
                            </small>

                            <h6 class="fw-bold mt-1 mb-2" style="font-size: 1rem; line-height: 1.25;">
                                {{ $nombreCatalogo }}
                            </h6>

                            <small class="text-secondary d-block mb-2"
                                   style="font-size: .78rem; line-height: 1.4; min-height: 2.4em;">
                                {{ \Illuminate\Support\Str::limit($moto->descrip2 ?? '', 70) }}
                            </small>

                            {{-- 🎨 Colores disponibles --}}
                            @if($tieneMultiples)
                                <div class="d-flex align-items-center gap-2 mb-3">
                                    @foreach($variantes->take(4) as $v)
                                        <span style="display: inline-block; width: 16px; height: 16px; border-radius: 50%;
                             background: {{ $v->color_hex ?? '#ccc' }};
                             border: 2px solid #fff;
                             box-shadow: 0 0 0 1px #ddd;"
                                              title="{{ $v->color_nombre ?? '' }}"></span>
                                    @endforeach
                                    @if($variantes->count() > 4)
                                        <small class="text-secondary">+{{ $variantes->count() - 4 }}</small>
                                    @endif
                                    <small class="text-secondary ms-auto">
                                        {{ $variantes->count() }} colores
                                    </small>
                                </div>
                            @elseif($moto->color_hex)
                                <div class="d-flex align-items-center gap-2 mb-3">
            <span style="display: inline-block; width: 16px; height: 16px; border-radius: 50%;
                         background: {{ $moto->color_hex }};
                         border: 2px solid #fff;
                         box-shadow: 0 0 0 1px #ddd;"></span>
                                    <small class="text-secondary">{{ $moto->color_nombre ?? '' }}</small>
                                </div>
                            @endif

                            <div class="mt-auto d-flex justify-content-between align-items-end gap-2">
                                <div>
                                    @if($precio > 0)
                                        <small class="text-secondary d-block" style="font-size: .65rem;">Precio ref.</small>
                                        <span class="fw-bold" style="color: var(--cf-teal); font-size: 1.1rem;">
                    ${{ number_format($precio, 0, ',', '.') }}
                </span>
                                    @else
                                        <span class="text-secondary fst-italic" style="font-size: .78rem;">
                    Precio por confirmar
                </span>
                                    @endif
                                </div>
                                <a href="{{ route('web.producto', $moto->codprod) }}"
                                   class="btn btn-cf btn-sm px-3">
                                    Ver ficha <i class="bi bi-arrow-right ms-1"></i>
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
