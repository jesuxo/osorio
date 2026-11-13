{{-- resources/views/web/partials/sidebar-filtros.blade.php --}}
{{-- Requiere: $marcas, $estilos, $marcaCodalte, $estilo,
              $precioDesde, $precioHasta, $rango --}}
<div class="card border-0 shadow-sm sidebar-filtros-container" style="background: #122658;
  color: white;">
    <div class="card-body p-3">

        {{-- Header con limpiar --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="fw-bold mb-0">
                <i class="bi bi-funnel me-1" style="color: var(--cf-teal);"></i> Filtros
            </h6>
            @if($marcaCodalte || $estilo || $precioDesde !== null || $precioHasta !== null)
                <a href="{{ route('web.motos') }}"
                   class="small text-decoration-none filtro-link"
                   style="color: var(--cf-teal);">
                    Limpiar todo
                </a>
            @endif
        </div>

        {{-- ============================================================ --}}
        {{-- ESTILO --}}
        {{-- ============================================================ --}}
        @if(isset($estilos) && $estilos->count())
            <div class="filtro-bloque">
                <div class="filtro-titulo" style="">Estilo</div>
                <div>
                    <a href="{{ route('web.motos', array_merge(request()->except('estilo', 'page'))) }}"
                       class="chip-estilo-filtro filtro-link {{ !$estilo ? 'activo' : '' }}">
                        Todos
                    </a>
                    @foreach($estilos as $e)
                        <a href="{{ route('web.motos', array_merge(request()->except('estilo', 'page'), ['estilo' => $e])) }}"
                           class="chip-estilo-filtro filtro-link {{ $estilo === $e ? 'activo' : '' }}">
                            {{ $e }}
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- ============================================================ --}}
        {{-- MARCA --}}
        {{-- ============================================================ --}}
        <div class="filtro-bloque">
            <div class="filtro-titulo">Marca</div>

            <a href="{{ route('web.motos', array_merge(request()->except('marca', 'page'))) }}"
               class="filtro-opcion filtro-link {{ !$marcaCodalte ? 'activo' : '' }}">
                <span>Todas</span>
            </a>

            @foreach($marcas as $m)
                <a href="{{ route('web.motos', array_merge(request()->except('marca', 'page'), ['marca' => $m->codalte])) }}"
                   class="filtro-opcion filtro-link {{ $marcaCodalte === $m->codalte ? 'activo' : '' }}">
                    <span>{{ $m->nombre_corto }}</span>
                </a>
            @endforeach
        </div>



        {{-- ============================================================ --}}
        {{-- PRECIO --}}
        {{-- ============================================================ --}}
        <div class="filtro-bloque">
            <div class="filtro-titulo">Precio (USD)</div>

            <div class="precio-grupo mb-2">
                <input type="number" id="precio-desde" class="form-control form-control-sm"
                       placeholder="Desde" value="{{ $precioDesde }}" min="0" step="10">
                <span class="text-secondary small">—</span>
                <input type="number" id="precio-hasta" class="form-control form-control-sm"
                       placeholder="Hasta" value="{{ $precioHasta }}" min="0" step="10">
            </div>

            @if($rango && $rango->min)
                <small class="text-secondary d-block mb-2" style="color:white !important; font-size: .72rem;">
                    Rango disponible:
                    ${{ number_format($rango->min, 0, ',', '.') }}
                    – ${{ number_format($rango->max, 0, ',', '.') }}
                </small>
            @endif

            <button type="button" class="btn btn-cf btn-sm w-100" id="btn-aplicar-precio">
                <i class="bi bi-check2 me-1"></i> Aplicar precio
            </button>
        </div>

    </div>
</div>
