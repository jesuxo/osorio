<style>
    /* Estilos existentes */
    .badge {
        font-size: 11px;
        padding: 4px 6px;
        border-radius: 12px;
        white-space: nowrap;
    }

    .bg-opacity-10 {
        --bs-bg-opacity: 0.1;
    }


    /* Nuevos estilos para la navegación */
    .search-selected {
        background-color: rgba(13, 110, 253, 0.1) !important;
        border-left: 3px solid #0d6efd;
        transition: all 0.2s ease;
    }

    .search-selected td:first-child {
        border-left: none;
    }

    #ajaxbusquedaproductos {
        max-height: 500px;
        overflow-y: auto;
    }

    #ajaxbusquedaproductos tbody tr {
        cursor: pointer;
        transition: background-color 0.2s ease;
    }

    #ajaxbusquedaproductos tbody tr:hover {
        background-color: rgba(13, 110, 253, 0.05);
    }
    .search-mouse-hover {
        background-color: rgba(13, 110, 253, 0.05) !important;
        transition: all 0.2s ease;
    }

    /* La selección activa mantiene su estilo distintivo */
    .search-selected {
        background-color: rgba(13, 110, 253, 0.1) !important;
        border-left: 3px solid #0d6efd;
    }

    /* Si un elemento es hover y también está seleccionado, priorizar el estilo de selección */
    .search-selected.search-mouse-hover {
        background-color: rgba(13, 110, 253, 0.1) !important;
        border-left: 3px solid #0d6efd;
    }
</style>
<header id="page-topbar">
    <div class="layout-width">
        <div class="navbar-header">
            <div class="d-flex">
                <!-- LOGO -->
                <div class="navbar-brand-box horizontal-logo">
                    <a href="index" class="logo logo-dark">
                        <span class="logo-sm">
                            <img src="{{ URL::asset('build/images/logo-sm.png') }}" alt="" height="50">
                        </span>
                        <span class="logo-lg">
                            <img src="{{ URL::asset('build/images/logo-light.png') }}" alt="" height="50">
                        </span>
                    </a>

                    <a href="index" class="logo logo-light">
                        <span class="logo-sm">
                            <img src="{{ URL::asset('build/images/logo-sm.png') }}" alt="" height="50">
                        </span>
                        <span class="logo-lg">
                            <img src="{{ URL::asset('build/images/logo-light.png') }}" alt="" height="50">
                        </span>
                    </a>
                </div>

                <button type="button" class="btn btn-sm px-3 fs-16 header-item vertical-menu-btn topnav-hamburger" id="topnav-hamburger-icon">
                    <span class="hamburger-icon">
                        <span></span>
                        <span></span>
                        <span></span>
                    </span>
                </button>

                <button onclick="focusbusqueda()" type="button" class="btn btn-sm px-3 fs-15 user-name-text header-item d-none d-md-block" data-bs-toggle="modal" data-bs-target="#searchModal">
                    <span class="bi bi-search me-2"></span> Busqueda...
                </button>
                <script>
                    function selectinput(){
                        $('#search-options').select();
                    }
                    function focusbusqueda(){
                        $('#searchModal').modal('show');
                        setTimeout(selectinput, 600);
                    }
                </script>
            </div>

            <div class="d-flex align-items-center">

                <div class="d-md-none topbar-head-dropdown header-item">
                    <button type="button" class="btn btn-icon btn-topbar btn-ghost-dark rounded-circle" id="page-header-search-dropdown" data-bs-toggle="modal" data-bs-target="#searchModal">
                        <i class="bi bi-search fs-16"></i>
                    </button>
                </div>


                <div class="dropdown topbar-head-dropdown ms-1 header-item dropdown-hover-end">
                    <button type="button" class="btn btn-icon btn-topbar btn-ghost-dark rounded-circle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="bi bi-arrow-left-right align-middle fs-20 "></i>
                    </button>
                    <div class="dropdown-menu p-2 dropdown-menu-end" style="width: 400px">
                        <div class="dropdown-head rounded-top">
                            <div class="p-3 border-bottom border-bottom-dashed">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h6 class="mb-0 fs-16 fw-semibold">Grupo de Empresas
                                            <span class="badge bg-danger-subtle text-danger fs-13 notification-badge">
                                {{ isset($comerciales_acceso) ? count($comerciales_acceso) : 0 }}
                            </span>
                                        </h6>
                                        <p class="fs-14 text-muted mt-1 mb-0">Seleccione el grupo que necesita consultar</p>
                                    </div>
                                    <div class="col-auto">
                                        <a href="javascript:void(0);" class="link-secondary fs-15" id="refreshComerciales">
                                            <i class="bi bi-arrow-repeat"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="dropdown-body" style="max-height: 400px; overflow-y: auto;">
                            @php
                                $user = Auth::user();
                                $comerciales_acceso = $user ? $user->getComercialesAcceso() : collect();
                                $comercialdata = session('comercialdata');
                            @endphp

                            @if($comerciales_acceso->count() > 0)
                                @foreach($comerciales_acceso as $comercial)
                                    <a href="{{ route('comercial.cambiar', $comercial->id) }}"
                                       class="dropdown-item {{ session('comercialid') == $comercial->id ? 'active bg-primary text-white' : '' }}"
                                       data-comercial-id="{{ $comercial->id }}">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-shop me-2 fs-16"></i>
                                            <div class="flex-grow-1">
                                                <span class="fw-medium">{{ $comercial->descrip }}</span>
                                                @if(session('comercialid') == $comercial->id)
                                                    <span class="badge bg-success ms-2">Activo</span>
                                                @endif
                                            </div>
                                            @if(session('comercialid') == $comercial->id)
                                                <i class="bi bi-check-lg text-white fs-16"></i>
                                            @endif
                                        </div>
                                        <small class="text-muted d-block mt-1 ms-4 ps-1">
                                            <i class="bi bi-building"></i>
                                            {{ $comercial->sucursales->count() }} sucursal(es) asignada(s)
                                        </small>
                                    </a>
                                @endforeach
                            @else
                                <div class="text-center py-4">
                                    <i class="bi bi-exclamation-triangle fs-1 text-warning"></i>
                                    <p class="mt-2 text-muted">No tienes comerciales asignados</p>
                                    <small class="text-muted">Contacta al administrador</small>
                                </div>
                            @endif
                        </div>

                        @if($comerciales_acceso->count() > 0)
                            <div class="dropdown-foot p-2 border-top border-top-dashed mt-2">
                                <div class="text-center">
                                    <small class="text-muted">
                                        <i class="bi bi-info-circle"></i>
                                        Cambiar el grupo de empresas actualizará la información mostrada
                                    </small>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>






                <div class="dropdown  header-item topbar-user topbar-head-dropdown dropdown-hover-end"  >
                    <button type="button" class="btn" id="page-header-user-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="d-flex align-items-center">
                            <img class="rounded-circle header-profile-user" src="@if(@Auth::user()->avatar) {{ URL::asset('images/users/')."/". @Auth::user()->avatar}} @else {{ URL::asset('build/images/users/avatar-1.jpg') }} @endif" alt="Header Avatar">
                            <span class="text-start ms-xl-2">
                                <span class="d-none d-xl-inline-block ms-1 fw-medium user-name-text">{{ @Auth::user()->first_name  }}</span>
                                <span class="d-none d-xl-block ms-1 fs-13 user-name-sub-text " style="display: none !important;">...</span>
                            </span>
                        </span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end">
                        <!-- item-->
                        <h6 class="dropdown-header"> {{@Auth::user()->first_name}} {{@Auth::user()->last_name}}</h6>
                         <a class="dropdown-item" href="/productos"><i class="bi bi-box-seam text-muted fs-15 align-middle me-1"></i> <span class="align-middle">Productos</span></a>
                          <div class="dropdown-divider"></div>
                         <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="bi bi-box-arrow-right text-muted fs-15 align-middle me-1"></i> <span class="align-middle" data-key="t-logout">{{ __('t-logout') }}</span></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <form  method="POST" style="display: none;"  action="{{ route('logout') }}" id="logout-form">
        @csrf
    </form>
</header>


<!-- Modal -->
<div class="modal fade" id="searchModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content rounded">
            <div class="modal-header p-3">
                <div class="position-relative w-100">
                    <input type="text" class="form-control form-control-lg border-2 busquedaproductos"
                           placeholder="Busqueda de productos..." autocomplete="off"
                           onchange="performSearch($(this).val())"
                           id="search-options" value="">
                    <span class="bi bi-search search-widget-icon fs-17"></span>
                    <a href="javascript:void(0);" class="search-widget-icon fs-14 link-secondary text-decoration-underline search-widget-icon-close d-none" id="search-close-options">Limpiar</a>
                </div>
            </div>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0 overflow-hidden" id="search-dropdown">

                <div class="dropdown-head rounded-top">
                    <div class="p-3">
                        <div class="row align-items-center">
                            <div class="col">
                                <h6 class="m-0 fs-14 text-muted fw-semibold"> Coincidencias con la busqueda </h6>
                            </div>
                            <div class="col" style="text-align: right">
                                <h6 class="m-0 fs-14 text-muted fw-semibold" id="textbusqueda"> </h6>
                            </div>
                        </div>
                    </div>

                    <div class="dropdown-item bg-transparent text-wrap" id="ajaxbusquedaproductos">

                    </div>


                </div>


                </div>
            </div>
        </div>
    </div>
</div>

<!-- removeNotificationModal -->
<div id="removeNotificationModal" class="modal fade zoomIn" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="NotificationModalbtn-close"></button>
            </div>
            <div class="modal-body p-md-5">
                <div class="text-center">
                    <div class="text-danger">
                        <i class="bi bi-trash display-4"></i>
                    </div>
                    <div class="mt-4 fs-15">
                        <h4 class="mb-1">Are you sure ?</h4>
                        <p class="text-muted mx-4 mb-0">Are you sure you want to remove this Notification ?</p>
                    </div>
                </div>
                <div class="d-flex gap-2 justify-content-center mt-4 mb-2">
                    <button type="button" class="btn w-sm btn-light" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn w-sm btn-danger" id="delete-notification">Yes, Delete It!</button>
                </div>
            </div>

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script>
    document.addEventListener('keydown', function(e) {
        /*if ((e.ctrlKey || e.metaKey) && (e.key === 'f' || e.key === 'F')) {
            e.preventDefault(); // Prevenir el comportamiento predeterminado del navegador
            focusbusqueda(); // Ejecutar tu función
        }*/
    });
</script>


<script>
    $(document).ready(function() {
        let currentSelectedIndex = -1;
        let searchResults = [];
        let isScrolling = false;
        let mouseOverIndex = -1;
        let isNavigatingWithKeyboard = false;




        // Variable para saber si ya se realizó una búsqueda
        let hasSearchResults = false;

        // Evento para el input de búsqueda


        // Evento para el botón "Limpiar"
        $('#search-close-options').off('click').on('click', function() {
            $('#search-options').val('');
            $('#textbusqueda').html('');
            $('#ajaxbusquedaproductos').html('');
            searchResults = [];
            currentSelectedIndex = -1;
            mouseOverIndex = -1;
            hasSearchResults = false;
            $('#search-options').focus();
        });

        // Cuando se abre el modal
        $('#searchModal').off('shown.bs.modal').on('shown.bs.modal', function() {
            const $input = $('#search-options');
            $input.focus();
            currentSelectedIndex = -1;
            searchResults = [];
            mouseOverIndex = -1;
            hasSearchResults = false;

            // Limpiar búsqueda anterior
            $input.val('');
            $('#textbusqueda').html('');
            $('#ajaxbusquedaproductos').html('');
        });

        // Cuando se cierra el modal
        $('#searchModal').off('hidden.bs.modal').on('hidden.bs.modal', function() {
            currentSelectedIndex = -1;
            searchResults = [];
            mouseOverIndex = -1;
            hasSearchResults = false;
        });

        // Navegación con mouse - SOLO efecto visual
        $(document).off('mouseenter.searchModal', '#ajaxbusquedaproductos tbody tr').on('mouseenter.searchModal', '#ajaxbusquedaproductos tbody tr', function() {
            if ($('#searchModal').hasClass('show') && !isNavigatingWithKeyboard) {
                const index = searchResults.indexOf(this);
                if (index !== -1 && index !== currentSelectedIndex) {
                    mouseOverIndex = index;
                    $('.search-mouse-hover').removeClass('search-mouse-hover');
                    $(this).addClass('search-mouse-hover');
                }
            }
        });

        $(document).off('mouseleave.searchModal', '#ajaxbusquedaproductos tbody tr').on('mouseleave.searchModal', '#ajaxbusquedaproductos tbody tr', function() {
            if ($('#searchModal').hasClass('show')) {

                $('.search-mouse-hover').removeClass('search-mouse-hover');
            }
        });


    });
    function performSearch(busqueda) {
        if (busqueda.trim() !== '') {
            $('#textbusqueda').html("Búsqueda: " + busqueda);
        } else {
            $('#textbusqueda').html('');
            $('#ajaxbusquedaproductos').html('');
            searchResults = [];
            currentSelectedIndex = -1;
            mouseOverIndex = -1;
            return;
        }

        $('#ajaxbusquedaproductos').html('<div class="text-center p-4"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Cargando...</span></div><p class="mt-2">Buscando productos...</p></div>');

        $.ajax({
            type: 'POST',
            url: '/saprod/home/busqueda',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: { busqueda: busqueda },
            success: function(response) {
                updateSearchResults(response);
            },
            error: function(xhr, status, error) {
                console.error('Error en búsqueda:', error);
                $('#ajaxbusquedaproductos').html('<div class="text-center p-4 text-danger">Error al realizar la búsqueda</div>');
            }
        });
    }

    function updateSearchResults(response) {
        $('#ajaxbusquedaproductos').html(response);

    }

</script>
