@extends('layouts.master')
@section('title')
    Crear nuevo producto
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
        .tituloinsta {
            font-size: 24px !important;
        }
    </style>
    <script>
        function verUltimoProd(codinst) {
            $('#invalidcodprod').html('C&oacute;digo ');

            $.ajax({
                type: 'POST',
                url: '/sainsta/check/lastprod/' + codinst,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {},
                success: function (data) {
                    lastprod = data.last;
                    if (lastprod)
                        $('#invalidcodprod').html('C&oacute;digo  [ ' + lastprod + ' &Uacute;ltimo producto creado ] ');
                }
            });
        }
    </script>
@endsection

@section('content')
    <x-breadcrumb title="Crear nuevo producto" pagetitle="Productos" />

    <form id="createproduct-form" autocomplete="off" class="needs-validation" method="post"
          novalidate action="{{ route('productos.store') }}">
        @method('POST')
        @csrf

        <div class="row">
            {{-- ============================================================ --}}
            {{-- COLUMNA IZQUIERDA: TABS                                     --}}
            {{-- ============================================================ --}}
            <div class="col-xl-9 col-lg-8">
                <div class="card">
                    <div class="card-body p-0">

                        {{-- HEADER --}}
                        <div class="d-flex align-items-center gap-3 p-3 border-bottom">
                            <div class="flex-shrink-0">
                                <div class="avatar-sm">
                                    <div class="avatar-title rounded-circle bg-light text-primary fs-20">
                                        <i class="bi bi-box-seam"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <h5 class="mb-0 fw-bold">Nuevo producto</h5>
                                <small class="text-muted">
                                    Completa los datos básicos. Podrás agregar imágenes, precios y agrupación después de crearlo.
                                </small>
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
                                        <select onchange="$('.error-msg').hide(); $('.datosprod').fadeIn(); verUltimoProd(this.value)"
                                                class="form-select"
                                                data-choices
                                                required
                                                id="choices-category-input"
                                                name="codinst">
                                            <option value="">Seleccionar instancia</option>
                                            @foreach($instancias as $instancia)
                                                @if($instancia->insPadre > 0)
                                                    <script>
                                                        document.querySelector('#padre{{$instancia->insPadre}}').disabled = true;
                                                        $('#padre{{$instancia->insPadre}}').addClass('tituloinsta');
                                                    </script>
                                                @endif
                                                <option id="padre{{$instancia->codinst}}"
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

                                {{-- BLOQUE QUE SE MUESTRA AL ELEGIR INSTANCIA --}}
                                <div class="datosprod" style="display: none;">

                                    <div class="form-section-title">Identificación</div>
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label class="form-label" id="invalidcodprod" for="codprod">C&oacute;digo</label>
                                                <input type="text"
                                                       class="form-control"
                                                       id="codprod"
                                                       maxlength="15"
                                                       name="codprod"
                                                       pattern="[a-zA-Z0-9]+"
                                                       required
                                                       value="{{ ($last) ? $last : '' }}"
                                                       onclick="$('#invalidcodprod').html('C&oacute;digo'); $('#invalidcodprod').removeClass('text-danger');">
                                                <small class="text-muted">Máx. 15 caracteres, sin espacios.</small>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="refere">Referencia</label>
                                                <input type="text" class="form-control" id="refere" name="refere"
                                                       placeholder="Ej: C&oacute;digo de barra">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label" for="descrip">Nombre del producto</label>
                                        <input type="hidden" id="formAction" name="formAction" value="add">
                                        <input type="text" class="form-control" id="descrip" value=""
                                               placeholder="Descripción principal"
                                               name="descrip" required>
                                        <div class="invalid-feedback">Por favor, ingrese el nombre/descripci&oacute;n del producto</div>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="descrip2">Descripción 2</label>
                                                <input type="text" class="form-control" id="descrip2" name="descrip2"
                                                       value="" placeholder="Opcional">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="descrip3">Descripción 3</label>
                                                <input type="text" class="form-control" id="descrip3" name="descrip3"
                                                       value="" placeholder="Opcional">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="marca">Marca</label>
                                                <input type="text" class="form-control" id="marca" name="marca"
                                                       placeholder="Ej: CFMOTO">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- ============================================================ --}}
                            {{-- TAB 2: CONFIGURACIÓN                                        --}}
                            {{-- ============================================================ --}}
                            <div class="tab-pane fade" id="tab-config" role="tabpanel">

                                <div class="form-section-title">Impuestos y precios</div>
                                <div class="row">
                                    <div class="col-lg-4">
                                        <div class="form-check form-switch form-switch-lg mb-3" dir="ltr">
                                            <input type="checkbox" class="form-check-input" id="esexento" name="esexento" value="1">
                                            <label class="form-check-label" for="esexento">Producto exento</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-check form-switch form-switch-lg mb-3" dir="ltr">
                                            <input type="checkbox" class="form-check-input" id="exdecimal" name="exdecimal" value="1">
                                            <label class="form-check-label" for="exdecimal">Uso de decimales</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-check form-switch form-switch-lg mb-3" dir="ltr">
                                            <input type="checkbox" class="form-check-input" id="preciodolarfijo"
                                                   name="preciodolarfijo" value="1">
                                            <label class="form-check-label" for="preciodolarfijo">Producto en dólares</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="alert alert-info d-flex align-items-start gap-2 mb-0" role="alert">
                                    <i class="bi bi-info-circle fs-5 mt-1"></i>
                                    <div class="small">
                                        Los <strong>precios</strong> y las <strong>imágenes</strong> se configuran después
                                        de crear el producto, desde la pantalla de edición.
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
                                    Si esta moto tendrá versiones por color, asígnales el mismo <strong>grupo</strong>
                                    para que se muestren como un solo producto en el catálogo. Puedes crear la primera
                                    variante aquí y luego crear las demás desde el botón <strong>Duplicar</strong> o
                                    desde <a href="{{ route('saprod.grupos') }}" class="text-decoration-underline">Grupos</a>.
                                </p>

                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label class="form-label" for="grupo">Grupo / Código de agrupación</label>
                                            <input type="text" class="form-control" id="grupo" name="grupo"
                                                   list="grupos-sugeridos"
                                                   value="{{ old('grupo') }}"
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
                                                   value="{{ old('grupo_nombre') }}"
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
                                                       value="{{ old('color_hex', '#122658') }}"
                                                       onchange="document.getElementById('color_hex').value = this.value">
                                                <input type="text"
                                                       class="form-control"
                                                       id="color_hex"
                                                       name="color_hex"
                                                       value="{{ old('color_hex') }}"
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
                                                   value="{{ old('color_nombre') }}"
                                                   placeholder="Ej: Azul metálico, Negro mate">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-check form-switch form-switch-lg mb-3" dir="ltr">
                                    <input type="checkbox" class="form-check-input" id="grupo_destacado"
                                           name="grupo_destacado"
                                           {{ old('grupo_destacado') ? 'checked' : '' }}
                                           value="1">
                                    <label class="form-check-label" for="grupo_destacado">
                                        Destacar este grupo en el home
                                    </label>
                                </div>

                            </div>

                        </div>
                    </div>
                </div>

                <div class="text-end mb-3">
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check2 me-1"></i> Crear producto
                    </button>
                </div>
            </div>

            {{-- ============================================================ --}}
            {{-- COLUMNA DERECHA: CONDICIÓN                                 --}}
            {{-- ============================================================ --}}
            <div class="col-xl-3 col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Condici&oacute;n</h5>
                    </div>
                    <div class="card-body">
                        <select class="form-select" id="choices-publish-visibility-input" data-choices
                                data-choices-search-false name="activo">
                            <option value="1" selected>Activo</option>
                            <option value="0">Inactivo</option>
                        </select>
                        <small class="text-muted d-block mt-2">
                            Los productos inactivos no aparecen en la tienda web.
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@section('scripts')
    <script src="{{ URL::asset('build/libs/@ckeditor/ckeditor5-build-classic/ckeditor.js') }}"></script>
    <script src="{{ URL::asset('build/libs/dropzone/dropzone-min.js') }}"></script>
    <script src="{{ URL::asset('build/js/backend/create-product.init.js') }}?version={{rand(0,500)}}"></script>
    <script src="{{ URL::asset('build/js/app.js') }}"></script>

    <script>
        /* ============================================================ */
        /* PESTAÑA ACTIVA SEGÚN EL HASH DE LA URL                       */
        /* ============================================================ */
        (function () {
            const hash = window.location.hash.replace('#', '');
            const map = {
                info:     'tab-info-tab',
                catalogo: 'tab-catalogo-tab',
                config:   'tab-config-tab',
            };
            if (map[hash]) {
                const tab = document.getElementById(map[hash]);
                if (tab && window.bootstrap) {
                    new bootstrap.Tab(tab).show();
                }
            }

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
