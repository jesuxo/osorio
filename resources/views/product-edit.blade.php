@extends('layouts.master')
@section('title')
    Actualizaci&oacute;n de producto
@endsection
@section('css')
    <style>
        .nav-tabs-custom {
            border-bottom: 2px solid #E9ECEF;
            gap: .25rem;
        }
        .nav-tabs-custom .nav-link {
            border: none;
            border-bottom: 2px solid transparent;
            color: #6C757D;
            font-weight: 600;
            font-size: .9rem;
            padding: .65rem 1rem;
            margin-bottom: -2px;
        }
        .nav-tabs-custom .nav-link:hover {
            color: #122658;
            background: transparent;
        }
        .nav-tabs-custom .nav-link.active {
            color: #122658;
            border-bottom-color: var(--cf-teal, #2dd1ff);
            background: transparent;
        }
        .nav-tabs-custom .nav-link i {
            margin-right: .35rem;
        }
        .tab-pane {
            padding-top: 1.25rem;
        }
        .form-section-title {
            font-size: .8rem;
            text-transform: uppercase;
            letter-spacing: .5px;
            font-weight: 700;
            color: #495057;
            margin: 1.25rem 0 .75rem;
            padding-bottom: .35rem;
            border-bottom: 1px solid #E9ECEF;
        }
        .form-section-title:first-child {
            margin-top: 0;
        }
        .badge-permiso {
            font-size: .7rem;
            font-weight: 600;
        }
    </style>
@endsection
@section('content')

    <form id="editproduct-form" autocomplete="off" class="needs-validation" method="post"
          novalidate action="{{route('productos.update',$id)}}">
        @method('PUT')
        @csrf

        <div class="row">
            {{-- ============================================================ --}}
            {{-- COLUMNA IZQUIERDA: TABS                                     --}}
            {{-- ============================================================ --}}
            <div class="col-xl-9 col-lg-8">
                <div class="card">
                    <div class="card-body p-0">

                        {{-- HEADER PRODUCTO --}}
                        <div class="d-flex align-items-center gap-3 p-3 border-bottom flex-wrap">

                            <div class="flex-shrink-0">
                                <div class="avatar-sm">
                                    <div class="avatar-title rounded-circle bg-light text-primary fs-20">
                                        <i class="bi bi-box-seam"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="flex-grow-1">
                                <h5 class="mb-0 fw-bold">{{ $producto->descrip ?: 'Producto sin nombre' }}</h5>
                                <small class="text-muted">
                                    Código: <code>{{ $producto->codprod }}</code>
                                    @if($producto->grupo)
                                        · Grupo: <code>{{ $producto->grupo }}</code>
                                    @endif
                                </small>
                            </div>

                            <div class="d-flex gap-2 flex-shrink-0">
                                <a href="{{ route('web.producto', $producto->codprod) }}"
                                   target="_blank"
                                   rel="noopener"
                                   class="btn btn-sm btn-outline-primary"
                                   title="Abrir la ficha pública del producto en una pestaña nueva">
                                    <i class="bi bi-box-arrow-up-right me-1"></i> Ver en web
                                </a>

                                <a href="{{ route('saprod.grupos', ['q' => $producto->grupo]) }}"
                                   class="btn btn-sm btn-outline-secondary"
                                   title="Volver al listado de agrupación">
                                    <i class="bi bi-arrow-left me-1"></i> Grupos
                                </a>
                            </div>
                        </div>

                        {{-- TABS --}}
                        <ul class="nav nav-tabs nav-tabs-custom px-3 pt-3" id="productTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="tab-info-tab" data-bs-toggle="tab"
                                        data-bs-target="#tab-info" type="button" role="tab">
                                    <i class="bi bi-info-circle"></i> Información
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="tab-catalogo-tab" data-bs-toggle="tab"
                                        data-bs-target="#tab-catalogo" type="button" role="tab">
                                    <i class="bi bi-palette"></i> Catálogo
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="tab-imagenes-tab" data-bs-toggle="tab"
                                        data-bs-target="#tab-imagenes" type="button" role="tab">
                                    <i class="bi bi-images"></i> Imágenes
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="tab-config-tab" data-bs-toggle="tab"
                                        data-bs-target="#tab-config" type="button" role="tab">
                                    <i class="bi bi-sliders"></i> Configuración
                                </button>
                            </li>
                        </ul>

                        {{-- CONTENIDO DE TABS --}}
                        <div class="tab-content p-3">

                            {{-- ============================================================ --}}
                            {{-- TAB 1: INFORMACIÓN                                          --}}
                            {{-- ============================================================ --}}
                            <div class="tab-pane fade show active" id="tab-info" role="tabpanel">

                                <div class="form-section-title">Clasificación de inventario</div>
                                <div class="d-flex align-items-start gap-2 mb-3">
                                    <div class="flex-grow-1">
                                        <select onchange="$('.error-msg').hide();"
                                                class="form-select"
                                                data-choices
                                                required
                                                id="choices-category-input"
                                                name="codinst">
                                            @foreach($instancias as $instancia)
                                                <option {{($instancia->codinst == $producto->codinst)?'selected':''}}
                                                        style="margin-left: {{($instancia->nivel-1) * 14}}px !important;"
                                                        value="{{$instancia->codinst}}">
                                                    {!! $instancia->label !!}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <a href="/instancias" class="btn btn-sm btn-outline-secondary flex-shrink-0">
                                        <i class="bi bi-plus-lg"></i> Instancia
                                    </a>
                                </div>
                                <div class="error-msg mt-1 small text-muted">
                                    Seleccione la instancia del inventario para clasificar este producto.
                                </div>

                                <div class="form-section-title">Identificación</div>
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label class="form-label" id="invalidcodprod" for="codprod">C&oacute;digo</label>
                                            <input type="text" class="form-control" id="codprod" name="codprod"
                                                   disabled value="{{$producto->codprod}}"
                                                   onclick="$('#invalidcodprod').html('C&oacute;digo'); $('#invalidcodprod').removeClass('text-danger');">
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label class="form-label" for="refere">Referencia</label>
                                            <input type="text" class="form-control" id="refere" name="refere"
                                                   value="{{$producto->refere}}"
                                                   placeholder="Ej: C&oacute;digo de barra">
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label" for="descrip">Nombre del producto</label>
                                    <input type="hidden" id="formAction" name="formAction" value="edit">
                                    <input type="text" class="form-control" id="descrip"
                                           value="{{$producto->descrip}}"
                                           placeholder="Descripción principal"
                                           name="descrip" required>
                                    <div class="invalid-feedback">Por favor, ingrese el nombre/descripci&oacute;n del producto</div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label class="form-label" for="descrip2">Descripción 2</label>
                                            <input type="text" class="form-control" id="descrip2" name="descrip2"
                                                   value="{{$producto->descrip2}}" placeholder="Opcional">
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label class="form-label" for="descrip3">Descripción 3</label>
                                            <input type="text" class="form-control" id="descrip3" name="descrip3"
                                                   value="{{$producto->descrip3}}" placeholder="Opcional">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-section-title">Marca y unidad</div>
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label class="form-label" for="marca">Marca</label>
                                            <input type="text" class="form-control" id="marca"
                                                   value="{{$producto->marca}}" name="marca"
                                                   placeholder="Ej: CFMOTO">
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label class="form-label" for="unidad">Unidad de medida</label>
                                            <input type="text" class="form-control" id="unidad" name="unidad"
                                                   value="{{$producto->unidad}}" placeholder="Ej: Kg">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- ============================================================ --}}
                            {{-- TAB 2: CATÁLOGO                                             --}}
                            {{-- ============================================================ --}}
                            <div class="tab-pane fade" id="tab-catalogo" role="tabpanel">

                                <div class="form-section-title">
                                    <i class="bi bi-palette me-1"></i> Agrupación por colores
                                </div>
                                <p class="text-muted small mb-3">
                                    Si esta moto tiene versiones por color, asígnales el mismo
                                    <strong>grupo</strong> para que se muestren como un solo producto en el catálogo.
                                </p>

                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label class="form-label" for="grupo">Grupo / Código de agrupación</label>
                                            <input type="text" class="form-control" id="grupo" name="grupo"
                                                   list="grupos-sugeridos"
                                                   value="{{ old('grupo', $producto->grupo ?? '') }}"
                                                   placeholder="Ej: cf450mt, xpress-200s"
                                                   autocomplete="off">
                                            <datalist id="grupos-sugeridos">
                                                @foreach($gruposExistentes ?? [] as $g)
                                                    <option value="{{ $g }}">{{ $g }}</option>
                                                @endforeach
                                            </datalist>
                                            <small class="text-muted">
                                                Todos los productos con el mismo grupo se muestran como uno solo.
                                            </small>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label class="form-label" for="grupo_nombre">Nombre a mostrar en el catálogo</label>
                                            <input type="text" class="form-control" id="grupo_nombre" name="grupo_nombre"
                                                   value="{{ old('grupo_nombre', $producto->grupo_nombre ?? '') }}"
                                                   placeholder="Ej: CFMOTO 450MT">
                                            <small class="text-muted">
                                                Si lo dejas vacío, se usa el nombre del producto más antiguo del grupo.
                                            </small>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label class="form-label" for="color_hex">Color (hex)</label>
                                            <div class="input-group">
                                                <input type="color"
                                                       class="form-control form-control-color"
                                                       id="color_hex_picker"
                                                       value="{{ $producto->color_hex ?? '#122658' }}"
                                                       onchange="document.getElementById('color_hex').value = this.value">
                                                <input type="text"
                                                       class="form-control"
                                                       id="color_hex"
                                                       name="color_hex"
                                                       value="{{ $producto->color_hex ?? '' }}"
                                                       placeholder="#122658"
                                                       pattern="^#[0-9A-Fa-f]{6}$">
                                            </div>
                                            <small class="text-muted">Se muestra como círculo en la card del catálogo.</small>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label class="form-label" for="color_nombre">Nombre del color</label>
                                            <input type="text" class="form-control" id="color_nombre" name="color_nombre"
                                                   value="{{ $producto->color_nombre ?? '' }}"
                                                   placeholder="Ej: Azul metálico, Negro mate">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-check form-switch form-switch-lg mb-3" dir="ltr">
                                    <input type="checkbox" class="form-check-input" id="grupo_destacado"
                                           name="grupo_destacado"
                                           {{ ($producto->grupo_destacado ?? false) ? 'checked' : '' }}
                                           value="1">
                                    <label class="form-check-label" for="grupo_destacado">
                                        Destacar este grupo en el home
                                    </label>
                                </div>

                                {{-- Variantes del grupo --}}
                                @if(!empty($producto->grupo))
                                    @php
                                        $variantesGrupo = \App\Models\Saprod::where('comercial', $producto->comercial)
                                            ->where('grupo', $producto->grupo)
                                            ->orderBy('id')
                                            ->get();
                                    @endphp
                                    @if($variantesGrupo->count() > 0)
                                        <div class="form-section-title">
                                            <i class="bi bi-layers me-1"></i>
                                            Productos en el grupo <code>{{ $producto->grupo }}</code>
                                            ({{ $variantesGrupo->count() }})
                                        </div>
                                        <div class="d-flex flex-wrap gap-2">
                                            @foreach($variantesGrupo as $v)
                                                <a href="{{ route('productos.edit', $v->id) }}"
                                                   class="border rounded px-2 py-1 text-decoration-none d-inline-flex align-items-center gap-2 small
                                                          {{ $v->id === $producto->id ? 'border-primary bg-primary-subtle' : '' }}">
                                                    <span style="display: inline-block; width: 14px; height: 14px; border-radius: 50%;
                                                                 background: {{ $v->color_hex ?? '#ccc' }};
                                                                 border: 1px solid #ddd;"></span>
                                                    {{ $v->descrip }}
                                                    @if($v->color_nombre)
                                                        <span class="text-muted">({{ $v->color_nombre }})</span>
                                                    @endif
                                                </a>
                                            @endforeach
                                        </div>
                                    @endif
                                @endif

                                {{-- Solo motos --}}
                                @if($esmoto==1)
                                    <div class="form-section-title" style="margin-top: 1.5rem;">
                                        <i class="bi bi-bicycle me-1"></i> Especificaciones de moto
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="clase">Clasificación / Estilo</label>
                                                <input type="text" class="form-control" id="clase" name="clase"
                                                       list="clases-sugeridas"
                                                       value="{{ old('clase', $producto->clase ?? '') }}"
                                                       placeholder="Ej: Deportiva, Clásica, Scooter…"
                                                       autocomplete="off">
                                                <datalist id="clases-sugeridas">
                                                    @foreach($clases as $c)
                                                        <option value="{{ $c }}">{{ $c }}</option>
                                                    @endforeach
                                                </datalist>
                                                <small class="text-muted">
                                                    Puedes elegir una sugerencia o escribir un estilo nuevo.
                                                </small>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="cilindraje">Cilindraje (CC)</label>
                                                <input type="number" class="form-control" id="cilindraje" name="cilindraje"
                                                       value="{{ old('cilindraje', $producto->cilindraje ?? '') }}"
                                                       placeholder="Ej: 250" min="0" step="1">
                                                <small class="text-muted">Se muestra como etiqueta en la tienda.</small>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            {{-- ============================================================ --}}
                            {{-- TAB 3: IMÁGENES                                             --}}
                            {{-- ============================================================ --}}
                            <div class="tab-pane fade" id="tab-imagenes" role="tabpanel">

                                <div class="form-section-title">
                                    <i class="bi bi-image me-1"></i> Imagen principal
                                </div>
                                <div class="d-flex align-items-start gap-3 mb-4">
                                    <div id="imagen-preview-wrap"
                                         style="width: 200px; height: 150px; border-radius: 8px; overflow: hidden;
                                                border: 1px dashed #ccc; background: #f8f9fa;
                                                display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                        @if($producto->tieneImagen())
                                            <img id="imagen-preview"
                                                 src="{{ $producto->imagen_url }}"
                                                 alt="{{ $producto->descrip }}"
                                                 style="width: 100%; height: 100%; object-fit: cover;">
                                        @else
                                            <img id="imagen-preview"
                                                 src="{{ asset('build/images/noimagen.jpg') }}"
                                                 alt="Sin imagen"
                                                 style="width: 100%; height: 100%; object-fit: cover; opacity: .5;">
                                        @endif
                                    </div>

                                    <div class="flex-grow-1">
                                        <input type="file"
                                               id="imagen-input"
                                               accept="image/jpeg,image/png,image/webp"
                                               class="form-control mb-2">
                                        <small class="text-muted d-block mb-2">
                                            JPG/PNG/WebP · máx. 5 MB. Se generará automáticamente una versión
                                            optimizada para WhatsApp y el carrito.
                                        </small>

                                        <div class="d-flex gap-2">
                                            <button type="button" class="btn btn-sm btn-primary" id="btn-subir-imagen" disabled>
                                                <i class="bi bi-upload me-1"></i> Subir
                                            </button>
                                            @if($producto->tieneImagen())
                                                <button type="button" class="btn btn-sm btn-outline-danger" id="btn-eliminar-imagen">
                                                    <i class="bi bi-trash me-1"></i> Eliminar
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="form-section-title d-flex justify-content-between align-items-center">
                                    <span><i class="bi bi-images me-1"></i> Galería adicional</span>
                                    <button type="button" class="btn btn-sm btn-outline-primary" id="btn-add-galeria">
                                        <i class="bi bi-plus-lg me-1"></i> Añadir fotos
                                    </button>
                                </div>

                                <input type="file"
                                       id="galeria-input"
                                       accept="image/jpeg,image/png,image/webp"
                                       multiple
                                       class="d-none">

                                <div id="galeria-wrap"
                                     class="d-flex flex-wrap gap-2"
                                     style="min-height: 90px; padding: 8px; border: 1px dashed #ccc; border-radius: 8px;">
                                    @foreach($producto->imagenes as $img)
                                        <div class="galeria-item position-relative"
                                             data-id="{{ $img->id }}"
                                             style="width: 110px; height: 90px; border-radius: 6px; overflow: hidden;
                                                    border: 1px solid #dee2e6; background: #f8f9fa;">
                                            <img src="{{ $img->imagen_thumb_url }}"
                                                 alt=""
                                                 style="width: 100%; height: 100%; object-fit: cover;">

                                            @if($img->principal)
                                                <span class="badge bg-success position-absolute"
                                                      style="top: 4px; left: 4px; font-size: .6rem;">Portada</span>
                                            @endif

                                            <div class="d-flex position-absolute gap-1"
                                                 style="bottom: 4px; right: 4px;">
                                                @unless($img->principal)
                                                    <button type="button"
                                                            class="btn btn-sm btn-light btn-marcar-principal p-0"
                                                            style="width: 22px; height: 22px; line-height: 1;"
                                                            title="Marcar como portada"
                                                            data-id="{{ $img->id }}">
                                                        <i class="bi bi-star-fill" style="font-size:.7rem;"></i>
                                                    </button>
                                                @endunless
                                                <button type="button"
                                                        class="btn btn-sm btn-danger btn-eliminar-galeria p-0"
                                                        style="width: 22px; height: 22px; line-height: 1;"
                                                        title="Eliminar"
                                                        data-id="{{ $img->id }}">
                                                    <i class="bi bi-x-lg" style="font-size:.7rem;"></i>
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <small class="text-muted d-block mt-1">
                                    La primera imagen (o la marcada con ★) se usa como portada, en el carrito y en el link de WhatsApp.
                                </small>
                            </div>

                            {{-- ============================================================ --}}
                            {{-- TAB 4: CONFIGURACIÓN                                        --}}
                            {{-- ============================================================ --}}
                            <div class="tab-pane fade" id="tab-config" role="tabpanel">

                                <div class="form-section-title">Impuestos y precios</div>
                                <div class="row">
                                    <div class="col-lg-4">
                                        <div class="form-check form-switch form-switch-lg mb-3" dir="ltr">
                                            <input type="checkbox" class="form-check-input" id="esexento" name="esexento"
                                                   {{($producto->esexento)?'checked':''}} value="1">
                                            <label class="form-check-label" for="esexento">Producto exento</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-check form-switch form-switch-lg mb-3" dir="ltr">
                                            <input type="checkbox" class="form-check-input" id="exdecimal" name="exdecimal"
                                                   {{($producto->exdecimal)?'checked':''}} value="1">
                                            <label class="form-check-label" for="exdecimal">Uso de decimales</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-check form-switch form-switch-lg mb-3" dir="ltr">
                                            <input type="checkbox" class="form-check-input" id="preciodolarfijo"
                                                   name="preciodolarfijo"
                                                   {{($producto->preciodolarfijo)?'checked':''}} value="1">
                                            <label class="form-check-label" for="preciodolarfijo">Producto en dólares</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-section-title">Empaque y dimensiones</div>
                                <div class="row">
                                    <div class="col-lg-4">
                                        <div class="mb-3">
                                            <label class="form-label" for="peso">Peso</label>
                                            <input type="text" class="form-control" id="peso" name="peso"
                                                   value="{{$producto->peso}}" placeholder="Ej: 2">
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="mb-3">
                                            <label class="form-label" for="volumen">Volumen</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control" id="volumen" name="volumen"
                                                       value="{{$producto->volumen}}" placeholder="Ej: 100">
                                                <span class="input-group-text">Mt3</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="mb-3">
                                            <label class="form-label" for="cantxempaq">Cant x Caja</label>
                                            <input type="text" class="form-control" id="cantxempaq" name="cantxempaq"
                                                   value="{{$producto->cantxempaq}}" placeholder="">
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="text-end mb-3">
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check2 me-1"></i> Guardar cambios
                    </button>
                </div>
            </div>

            {{-- ============================================================ --}}
            {{-- COLUMNA DERECHA: CONDICIÓN Y PRECIOS                       --}}
            {{-- ============================================================ --}}
            <div class="col-xl-3 col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Condici&oacute;n</h5>
                    </div>
                    <div class="card-body">
                        <select class="form-select" id="choices-publish-visibility-input" data-choices
                                data-choices-search-false name="activo">
                            <option value="1" {{( $producto->activo)?'selected':''}}>Activo</option>
                            <option value="0" {{(!$producto->activo)?'selected':''}}>Inactivo</option>
                        </select>
                    </div>
                </div>

                @if(Auth::user() and auth()->user()->can('menu_productos_modificar_precios'))
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-currency-dollar me-1"></i> Precios
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-2">
                                <label class="form-label small" for="preciod">Costo</label>
                                <input type="text" class="form-control form-control-sm" id="preciod"
                                       value="{{$producto->preciod}}" name="preciod" placeholder="Ej: 15" required>
                            </div>
                            <div class="mb-2">
                                <label class="form-label small" for="costod">Precio 1</label>
                                <input type="text" class="form-control form-control-sm" id="costod"
                                       value="{{$producto->costod}}" name="costod" placeholder="Ej: 18">
                            </div>
                            <div class="mb-2">
                                <label class="form-label small" for="costod2">Precio 2</label>
                                <input type="text" class="form-control form-control-sm" id="costod2"
                                       value="{{$producto->costod2}}" name="costod2" placeholder="Ej: 20">
                            </div>
                            <div class="mb-0">
                                <label class="form-label small" for="costod3">Precio 3 (público)</label>
                                <input type="text" class="form-control form-control-sm" id="costod3"
                                       value="{{$producto->costod3}}" name="costod3" placeholder="Ej: 22.5" required>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </form>
@endsection

@section('scripts')
    {{-- Scripts existentes (no tocar) --}}
    <script src="{{ URL::asset('build/js/backend/edit-product.init.js') }}"></script>
    <script src="{{ URL::asset('build/js/app.js') }}"></script>

    <script>
        (function () {
            const productoId   = {{ $producto->id }};
            const input        = document.getElementById('imagen-input');
            const preview      = document.getElementById('imagen-preview');
            const btnSubir     = document.getElementById('btn-subir-imagen');
            const btnEliminar  = document.getElementById('btn-eliminar-imagen');
            const csrf         = document.querySelector('meta[name="csrf-token"]').content;

            input?.addEventListener('change', function () {
                if (this.files && this.files[0]) {
                    preview.src = URL.createObjectURL(this.files[0]);
                    preview.style.opacity = 1;
                    btnSubir.disabled = false;
                } else {
                    btnSubir.disabled = true;
                }
            });

            btnSubir?.addEventListener('click', function () {
                if (!input.files[0]) return;

                const fd = new FormData();
                fd.append('imagen', input.files[0]);
                fd.append('_token', csrf);

                btnSubir.disabled = true;
                btnSubir.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Subiendo…';

                fetch(`/saprod/${productoId}/imagen`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
                    body: fd,
                })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) {
                            preview.src = data.imagen;
                            btnSubir.innerHTML = '<i class="bi bi-check me-1"></i> Subida';
                            setTimeout(() => location.reload(), 800);
                        } else {
                            alert(data.error || 'Error al subir');
                            btnSubir.disabled = false;
                            btnSubir.innerHTML = '<i class="bi bi-upload me-1"></i> Subir';
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        alert('Error al subir la imagen');
                        btnSubir.disabled = false;
                        btnSubir.innerHTML = '<i class="bi bi-upload me-1"></i> Subir';
                    });
            });

            btnEliminar?.addEventListener('click', function () {
                if (!confirm('¿Eliminar la imagen del producto?')) return;

                fetch(`/saprod/${productoId}/imagen`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
                })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) {
                            preview.src = '{{ asset("build/images/noimagen.jpg") }}';
                            preview.style.opacity = .5;
                            btnEliminar?.remove();
                        }
                    });
            });
        })();

        (function () {
            const productoId = {{ $producto->id }};
            const csrf       = document.querySelector('meta[name="csrf-token"]').content;
            const input      = document.getElementById('galeria-input');
            const wrap       = document.getElementById('galeria-wrap');
            const btnAdd     = document.getElementById('btn-add-galeria');

            btnAdd?.addEventListener('click', () => input.click());

            input?.addEventListener('change', function () {
                if (!this.files.length) return;

                const fd = new FormData();
                fd.append('_token', csrf);
                Array.from(this.files).forEach(f => fd.append('imagenes[]', f));

                fetch(`/saprod/${productoId}/imagenes`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
                    body: fd,
                })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) {
                            data.imagenes.forEach(img => {
                                const div = document.createElement('div');
                                div.className = 'galeria-item position-relative';
                                div.dataset.id = img.id;
                                div.style.cssText = 'width:110px;height:90px;border-radius:6px;overflow:hidden;border:1px solid #dee2e6;background:#f8f9fa;';
                                div.innerHTML = `
                                    <img src="${img.imagen_thumb_url}" style="width:100%;height:100%;object-fit:cover;">
                                    <div class="d-flex position-absolute gap-1" style="bottom:4px;right:4px;">
                                        <button type="button" class="btn btn-sm btn-light btn-marcar-principal p-0"
                                                style="width:22px;height:22px;line-height:1;" data-id="${img.id}">
                                            <i class="bi bi-star-fill" style="font-size:.7rem;"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger btn-eliminar-galeria p-0"
                                                style="width:22px;height:22px;line-height:1;" data-id="${img.id}">
                                            <i class="bi bi-x-lg" style="font-size:.7rem;"></i>
                                        </button>
                                    </div>`;
                                wrap.appendChild(div);
                            });
                            input.value = '';
                        } else {
                            alert('Error al subir las imágenes');
                        }
                    })
                    .catch(err => { console.error(err); alert('Error al subir'); });
            });

            document.addEventListener('click', function (e) {
                const btn = e.target.closest('.btn-eliminar-galeria');
                if (!btn) return;
                if (!confirm('¿Eliminar esta imagen?')) return;

                fetch(`/saprod/imagenes/${btn.dataset.id}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
                })
                    .then(r => r.json())
                    .then(d => { if (d.success) btn.closest('.galeria-item').remove(); });
            });

            document.addEventListener('click', function (e) {
                const btn = e.target.closest('.btn-marcar-principal');
                if (!btn) return;

                fetch(`/saprod/imagenes/${btn.dataset.id}/principal`, {
                    method: 'PUT',
                    headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
                })
                    .then(r => r.json())
                    .then(d => { if (d.success) location.reload(); });
            });
        })();

        /* ============================================================ */
        /* PESTAÑA ACTIVA SEGÚN EL HASH DE LA URL                       */
        /* Ej: /productos/123/edit#imagenes → abre la pestaña Imágenes  */
        /* ============================================================ */
        (function () {
            const hash = window.location.hash.replace('#', '');
            const map = {
                info:     'tab-info-tab',
                catalogo: 'tab-catalogo-tab',
                imagenes: 'tab-imagenes-tab',
                config:   'tab-config-tab',
            };
            if (map[hash]) {
                const tab = document.getElementById(map[hash]);
                if (tab && window.bootstrap) {
                    new bootstrap.Tab(tab).show();
                }
            }

            // Actualizar el hash al cambiar de pestaña (para que el link sea compartible)
            document.querySelectorAll('#productTabs button[data-bs-toggle="tab"]').forEach(btn => {
                btn.addEventListener('shown.bs.tab', function (e) {
                    const target = e.target.getAttribute('data-bs-target') || '';
                    const id = target.replace('#tab-', '');
                    if (id) history.replaceState(null, '', '#' + id);
                });
            });
        })();
    </script>
@endsection
