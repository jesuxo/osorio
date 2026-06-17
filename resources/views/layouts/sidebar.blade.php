<!-- ========== App Menu ========== -->
<div class="app-menu navbar-menu">
    <!-- LOGO -->
    <div class="navbar-brand-box">
        <a href="/index" class="logo logo-dark">
            <span class="logo-sm">
                <img src="{{ URL::asset('build/images/logo-sm.png') }}" alt="" height="50">
            </span>
            <span class="logo-lg">
                <img src="{{ URL::asset('build/images/logo-dark.png') }}" alt="" height="50">
            </span>
        </a>
        <a href=/index" class="logo logo-light">
            <span class="logo-sm">
                <img src="{{ URL::asset('build/images/logo-sm.png') }}" alt="" height="50">
            </span>
            <span class="logo-lg">
                <img src="{{ URL::asset('build/images/logo-light.png') }}" alt="" height="50">
            </span>
        </a>
        <button type="button" class="btn btn-sm p-0 fs-20 header-item float-end btn-vertical-sm-hover" id="vertical-hover">
            <i class="ri-record-circle-line"></i>
        </button>
    </div>

    <div id="scrollbar">
        <div class="container-fluid">

            <div id="two-column-menu">
            </div>
            <ul class="navbar-nav" id="navbar-nav">
                <li class="menu-title"><span data-key="t-menu">{{ __('t-menu') }}</span></li>
                <li class="nav-item">
                    <a href="/index" class="nav-link menu-link">
                        <i class="bi bi-speedometer2"></i>
                        <span data-key="t-dashboard">{{ __('t-dashboard') }}</span>
                    </a>
                </li>

                @if(Auth::user()  and auth()->user()->can('menu_reportes_ventas') )
                    <li class="nav-item">
                        <a class="nav-link menu-link" href="#sidebarreportesdeventas"
                           data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarTransferencias">
                            <i class="bi bi-file-check"></i> <span data-key="t-products">Reportes de Ventas</span>
                        </a>
                        <div class="collapse menu-dropdown" id="sidebarreportesdeventas">
                            <ul class="nav nav-sm flex-column">
                                <li class="nav-item">
                                    <a href="/reporte/venta" class="nav-link" data-key="t-list-view">Reporte Venta</a>
                                </li>
                                <li class="nav-item">
                                    <a href="/resumenVentas" class="nav-link" data-key="t-list-view">Resumen Venta</a>
                                </li>
                                <li class="nav-item">
                                    <a href="/reporte/ventas-vendedor" class="nav-link" data-key="t-list-view">Ventas x Vendedor</a>
                                </li>
                                <li class="nav-item">
                                    <a href="/reporte/instpagobs" class="nav-link" data-key="t-list-view">Inst Pago Bs</a>
                                </li>
                                <li class="nav-item">
                                    <a href="/reporte/instpagodolares" class="nav-link" data-key="t-list-view">Inst Pago USD</a>
                                </li>
                            </ul>
                        </div>
                    </li>
                @endif

                @if(Auth::user()  and auth()->user()->can('menu_transferencias') )
                <li class="nav-item">
                    <a class="nav-link menu-link" href="#sidebarTransferencias"
                       data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarTransferencias">
                        <i class="bi bi-box-seam"></i> <span data-key="t-products">Transferencias</span>
                    </a>
                    <div class="collapse menu-dropdown" id="sidebarTransferencias">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="{{route('reportetransferencias')}}" class="nav-link" data-key="t-list-view">Ver Transferencias</a>
                            </li>
                            <li class="nav-item">
                                <a href="/transferencias/create" class="nav-link" data-key="t-list-view">Agregar Transferencia</a>
                            </li>
                            <li class="nav-item" style="display: none">
                                <a href="/transferencia/informacion" class="nav-link" data-key="t-list-view">Info P/Transferencias</a>
                            </li>
                        </ul>
                    </div>
                </li>
                @endif

                @if(Auth::user()  and auth()->user()->can('menu_transporte') )
                    <li class="nav-item">
                        <a class="nav-link menu-link" href="#sidebarTransporte"
                           data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarTransporte">
                            <i class="bi bi-truck"></i>
                            <span data-key="t-products">Transporte</span>
                            <span class="badge badge-pill bg-danger" data-key="t-custom">Nuevo</span>
                        </a>
                        <div class="collapse menu-dropdown" id="sidebarTransporte">
                            <ul class="nav nav-sm flex-column">
                                <li class="nav-item">
                                    <a href="/viajes" class="nav-link" data-key="t-list-view">Viajes</a>
                                </li>
                                <li class="nav-item">
                                    <a href="/camiones" class="nav-link" data-key="t-list-view">Camiones</a>
                                </li>
                                <li class="nav-item">
                                    <a href="/choferes" class="nav-link" data-key="t-list-view">Choferes</a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('cxctransporte.index') }}" class="nav-link" data-key="t-list-view">Viajes por Cobrar</a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('reportesgastos.index') }}" class="nav-link" data-key="t-list-view">Reporte de Gastos</a>
                                </li>
                            </ul>
                        </div>
                    </li>
                @endif

                @if(Auth::user()  and auth()->user()->can('menu_productos') )
                <li class="nav-item">
                    <a class="nav-link menu-link" href="#sidebarProducts" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarProducts">
                        <i class="bi bi-box-seam"></i> <span data-key="t-products">Inventario</span>
                    </a>
                    <div class="collapse menu-dropdown" id="sidebarProducts">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="/productos" class="nav-link" data-key="t-list-view"> Productos</a>
                            </li>

                            @if(Auth::user()  and auth()->user()->can('menu_productos_existencias') )
                                @php
                                    $comercialid = session('comercialid');
                                    if($comercialid == 1) {

                                @endphp
                                    <li class="nav-item">
                                        <a href="/newexistencias" class="nav-link" data-key="t-list-view"> Existencias</a>
                                    </li>
                                @php
                                    }else{
                                @endphp
                                <li class="nav-item">
                                    <a href="/existencias" class="nav-link" data-key="t-list-view"> Existencias</a>
                                </li>
                                @php
                                    }
                                @endphp
                            @endif
                            <li class="nav-item"  >
                                <a href="productos/create" class="nav-link" data-key="t-create-product">Crear producto</a>
                            </li>
                            @if(Auth::user()  and auth()->user()->can('menu_productos_creardepositos') )
                                <li class="nav-item">
                                    <a href="/depositos" class="nav-link" data-key="t-sub-categories">Depositos</a>
                                </li>
                            @endif
                            @if(Auth::user()  and auth()->user()->can('menu_productos_crearinstancias') )
                                <li class="nav-item">
                                    <a href="/instancias" class="nav-link" data-key="t-sub-categories">Instancias</a>
                                </li>
                            @endif
                        </ul>
                    </div>
                </li>
                @endif

                @if(Auth::user()  and auth()->user()->can('menu_clientes') )
                    <li class="nav-item"  >
                        <a class="nav-link menu-link" href="#sidebarClientes" data-bs-toggle="collapse"
                           role="button" aria-expanded="false" aria-controls="sidebarClientes">
                            <i class="bi bi-person-bounding-box"></i> <span data-key="t-orders">Clientes</span>
                        </a>
                        <div class="collapse menu-dropdown" id="sidebarClientes">
                            <ul class="nav nav-sm flex-column">
                                <li class="nav-item">
                                    <a href="/clientes" class="nav-link" data-key="t-list-view">Buscar Cliente</a>
                                </li>

                                <li class="nav-item">
                                    <a href="/financiamientos" class="nav-link" data-key="t-list-view">Financiamientos</a>
                                </li>

                                @if(Auth::user()  and auth()->user()->can('menu_cxc') )
                                    <li class="nav-item"  >
                                        <a href="/cxc" class="nav-link" data-key="t-list-view">Cuentas x Cobrar</a>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </li>
                @endif

                @if(Auth::user()  and auth()->user()->can('menu_proveedores') )
                    <li class="nav-item"  >
                        <a class="nav-link menu-link" href="#sidebarProveedores" data-bs-toggle="collapse"
                           role="button" aria-expanded="false" aria-controls="sidebarProveedores">
                            <i class="bi bi-person-bounding-box"></i> <span data-key="t-orders">Proveedores</span>
                        </a>
                        <div class="collapse menu-dropdown" id="sidebarProveedores">
                            <ul class="nav nav-sm flex-column">
                                <li class="nav-item">
                                    <a href="/proveedores" class="nav-link" data-key="t-list-view">Buscar Proveedor</a>
                                </li>
                                <li class="nav-item" style="display: none">
                                    <a href="{{ route('reportes.proveedor.index') }}" class="nav-link" data-key="t-list-view">Pagos del Proveedor</a>
                                </li>
                                <li class="nav-item ">
                                    <a href="{{ route('pagos-proveedores.index') }}" class="nav-link" data-key="t-list-view">Motos</a>
                                </li>
                            </ul>
                        </div>
                    </li>
                @endif

                @if(Auth::user()  and auth()->user()->can('menu_vendedores') )
                    <li class="nav-item">
                        <a class="nav-link menu-link" href="/vendedores"   >
                            <i class="bi bi-binoculars"></i> <span data-key="t-sellers">Vendedores</span>
                        </a>
                    </li>
                @endif


                @if(Auth::user()  and auth()->user()->can('menu_caja') )
                    <li class="nav-item"  >
                        <a class="nav-link menu-link" href="/bancos" >
                            <i class="bi bi-currency-dollar"></i> <span data-key="t-shipping">Caja/Bancos</span>
                        </a>
                    </li>
                @endif


                @if(Auth::user()  and auth()->user()->can('menu_token') )
                    <li class="nav-item">
                        <a class="nav-link menu-link" href="/tokens"   >
                            <i class="bi bi-key"></i> <span data-key="t-sellers">Tokens</span>
                        </a>
                    </li>
                @endif




            </ul>
        </div>
        <!-- Sidebar -->
    </div>

    <div class="sidebar-background"></div>
</div>
<!-- Left Sidebar End -->
<!-- Vertical Overlay-->
<div class="vertical-overlay"></div>
