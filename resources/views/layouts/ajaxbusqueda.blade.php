<div class="table-responsive table-card" style="padding-bottom:20px; margin-top: 0px;">
    <table class="table table-bordered table-centered align-middle table-nowrap mb-0" style="font-size: 13px;">
        <thead class="text-muted table-light">
        <tr>
            <th width="60" class="text-center">Imagen</th>
            <th width="90"  class="text-center">Código</th>
            <th width="30%" class="text-center">Producto</th>
            <th width="90"  class="text-end">Costo</th>
            <th width="90"  class="text-end">Precio 3</th>
            <th width="30%" class="text-center">Existencias por sucursal</th>
            <th width="60"  class="text-center">Total</th>
        </tr>
        </thead>
        <tbody>
        @php $pros = 0; @endphp

        @foreach($productos as $producto)
            @php
                $pros++;
                $totalExistencias = 0;
                $existenciasHtml = [];
            @endphp

            @if(isset($producto->existencias_por_sucursal) && count($producto->existencias_por_sucursal) > 0)
                @foreach($producto->existencias_por_sucursal as $array)
                    @php
                        $existencia = $array->existen ?? 0;
                        $totalExistencias += $existencia;

                        if ($existencia > 0) {
                            $color = 'primary';
                            $existenciasHtml[] = "<span class='badge bg-{$color} bg-opacity-10 text-{$color} stock-badge' title='{$array->deposito->descrip}'>" .
                                str_replace('SARA', '', $array->deposito->descrip) . ": " . number_format($existencia, 0) .
                                "</span>";
                        }
                    @endphp
                @endforeach
            @endif

            <tr class="{{ !$producto->activo ? 'fila-inactiva' : '' }}">

                {{-- THUMB --}}
                <td class="align-middle text-center">
                    <a href="{{ route('web.producto', $producto->codprod) }}"
                       target="_blank" rel="noopener"
                       title="Ver en la web">
                        <img src="{{ $producto->imagen_thumb_grupo_url }}"
                             alt="{{ $producto->descrip }}"
                             loading="lazy"
                             style="width: 48px; height: 40px; object-fit: cover;
                                    border-radius: 6px; border: 1px solid #dee2e6; background: #f8f9fa;">
                    </a>
                </td>

                {{-- CÓDIGO --}}
                <td class="align-middle text-center">
                    <a href="{{ route('productos.edit', $producto->id) }}"
                       class="fw-medium link-primary"
                       title="Editar producto">
                        <code>{{ $producto->codprod }}</code>
                    </a>
                </td>

                {{-- PRODUCTO + ACCIONES --}}
                <td class="align-middle">
                    <div class="d-flex align-items-center justify-content-between gap-2">
                        <div class="text-truncate" style="min-width: 0;">
                            <a href="{{ route('productos.edit', $producto->id) }}"
                               class="fw-medium link-primary d-block text-truncate">
                                {{ $producto->descrip }}
                            </a>
                            <small class="text-muted d-block text-truncate">
                                {{ $producto->marca }}
                                @if($producto->grupo)
                                    · <span class="badge-grupo">{{ $producto->grupo }}</span>
                                @endif
                                @if(!$producto->activo)
                                    · <span class="badge bg-warning text-dark" style="font-size: .65rem;">Inactivo</span>
                                @endif
                            </small>
                        </div>

                        <div class="d-flex gap-1 flex-shrink-0">
                            <a href="{{ route('productos.edit', $producto->id) }}"
                               class="btn btn-sm btn-icon btn-ghost-primary"
                               title="Editar producto">
                                <i class="bi bi-pencil-square"></i>
                            </a>
                            <a href="{{ route('web.producto', $producto->codprod) }}"
                               target="_blank" rel="noopener"
                               class="btn btn-sm btn-icon btn-ghost-success"
                               title="Ver en la web">
                                <i class="bi bi-box-arrow-up-right"></i>
                            </a>
                        </div>
                    </div>
                </td>

                {{-- COSTO --}}
                <td class="text-end align-middle">
                    <span class="text-muted">$</span>{{ number_format($producto->preciod, 2, ',', '.') }}
                </td>

                {{-- PRECIO 3 --}}
                <td class="text-end align-middle">
                    <span class="fw-semibold" style="color: var(--cf-teal, #2dd1ff);">
                        ${{ number_format($producto->costod3, 2, ',', '.') }}
                    </span>
                </td>

                {{-- EXISTENCIAS --}}
                <td class="align-middle">
                    <div style="width: 100%; max-height: 60px; overflow: auto;">
                        @if(!empty($existenciasHtml))
                            <div style="display: flex; flex-wrap: wrap; gap: 3px;">
                                @foreach($existenciasHtml as $html)
                                    {!! $html !!}
                                @endforeach
                            </div>
                        @else
                            <span class="text-muted small">Sin stock</span>
                        @endif
                    </div>
                </td>

                {{-- TOTAL --}}
                <td class="text-center align-middle">
                    <span class="fw-bold {{ $totalExistencias == 0 ? 'text-secondary' : ($totalExistencias < 10 ? 'text-danger' : ($totalExistencias < 30 ? 'text-warning' : 'text-success')) }}">
                        {{ number_format($totalExistencias, 0) }}
                    </span>
                </td>

            </tr>
        @endforeach

        @if($pros == 0)
            <tr>
                <td colspan="7" class="text-center py-5">
                    <div class="text-muted">
                        <i class="bi bi-search fs-1 d-block mb-2"></i>
                        <h6 class="mb-1">No se encontraron productos</h6>
                        <p class="mb-3 small">Intenta con otros términos de búsqueda</p>
                        <a href="{{ route('productos.create') }}" class="btn btn-primary btn-sm">
                            <i class="bi bi-plus-circle me-1"></i> Crear nuevo producto
                        </a>
                    </div>
                </td>
            </tr>
        @endif
        </tbody>
    </table>
</div>

<style>
    .stock-badge {
        font-size: 11px;
        padding: 3px 7px;
        border-radius: 12px;
        white-space: nowrap;
        font-weight: 500;
    }
    .bg-opacity-10 {
        --bs-bg-opacity: 0.1;
    }

    .badge-grupo {
        background: #e3f2fd;
        color: #122658;
        font-size: .68rem;
        padding: .1rem .4rem;
        border-radius: 4px;
        font-family: monospace;
    }

    .fila-inactiva {
        background: #fafafa;
        opacity: .75;
    }

    /* Botones compactos de acción */
    .btn-icon {
        width: 30px;
        height: 30px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1px solid transparent;
        background: transparent;
        border-radius: 6px;
        transition: all .15s ease;
        font-size: .9rem;
    }
    .btn-ghost-primary {
        color: #0d6efd;
    }
    .btn-ghost-primary:hover {
        background: rgba(13, 110, 253, .1);
        border-color: rgba(13, 110, 253, .2);
    }
    .btn-ghost-success {
        color: #198754;
    }
    .btn-ghost-success:hover {
        background: rgba(25, 135, 84, .1);
        border-color: rgba(25, 135, 84, .2);
    }

    /* Thumb con hover sutil */
    td a img {
        transition: transform .15s ease;
    }
    td a:hover img {
        transform: scale(1.05);
    }
</style>
