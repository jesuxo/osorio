{{-- resources/views/web/partials/filtros-activos.blade.php --}}
{{-- Requiere: $marcaActiva, $marcaCodalte, $estilo,
              $precioDesde, $precioHasta, $busqueda --}}

@php
    $hayFiltros = $marcaCodalte || $estilo || $precioDesde !== null || $precioHasta !== null || $busqueda;
@endphp

@if($hayFiltros)
    <div class="filtros-activos mb-3 d-flex flex-wrap align-items-center gap-2" id="filtros-activos">

        <small class="text-secondary fw-semibold me-1">Filtros aplicados:</small>

        {{-- MARCA --}}
        @if($marcaCodalte)
            <a href="{{ route('web.motos', array_merge(request()->except('marca', 'page'))) }}"
               class="filtro-chip filtro-link"
               data-tipo="marca"
               title="Quitar filtro de marca">
                <span>{{ $marcaActiva->nombre_corto ?? $marcaCodalte }}</span>
                <i class="bi bi-x-lg"></i>
            </a>
        @endif

        {{-- ESTILO --}}
        @if($estilo)
            <a href="{{ route('web.motos', array_merge(request()->except('estilo', 'page'))) }}"
               class="filtro-chip filtro-link"
               data-tipo="estilo"
               title="Quitar filtro de estilo">
                <span>{{ $estilo }}</span>
                <i class="bi bi-x-lg"></i>
            </a>
        @endif

        {{-- RANGO DE PRECIO --}}
        @if($precioDesde !== null || $precioHasta !== null)
            <a href="{{ route('web.motos', array_merge(request()->except('precio_desde', 'precio_hasta', 'page'))) }}"
               class="filtro-chip filtro-link"
               data-tipo="precio"
               title="Quitar filtro de precio">
                <span>
                    @if($precioDesde !== null && $precioHasta !== null)
                        ${{ number_format($precioDesde, 0, ',', '.') }} – ${{ number_format($precioHasta, 0, ',', '.') }}
                    @elseif($precioDesde !== null)
                        Desde ${{ number_format($precioDesde, 0, ',', '.') }}
                    @else
                        Hasta ${{ number_format($precioHasta, 0, ',', '.') }}
                    @endif
                </span>
                <i class="bi bi-x-lg"></i>
            </a>
        @endif

        {{-- BÚSQUEDA --}}
        @if($busqueda)
            <a href="{{ route('web.motos', array_merge(request()->except('q', 'page'))) }}"
               class="filtro-chip filtro-link"
               data-tipo="busqueda"
               title="Quitar búsqueda">
                <span>“{{ $busqueda }}”</span>
                <i class="bi bi-x-lg"></i>
            </a>
        @endif

        {{-- LIMPIAR TODO --}}
        <a href="{{ route('web.motos') }}"
           class="ms-auto small text-decoration-none fw-semibold filtro-link"
           style="color: var(--cf-teal);">
            <i class="bi bi-x-circle me-1"></i> Limpiar todo
        </a>
    </div>
@endif
