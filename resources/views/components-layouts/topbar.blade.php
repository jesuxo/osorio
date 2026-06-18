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
                            <img src="{{ URL::asset('build/images/logo-dark.png') }}" alt="" height="50">
                        </span>
                    </a>

                    <a href="index" class="logo logo-light">
                        <span class="logo-sm">
                            <img src="{{ URL::asset('build/images/logo-sm.png') }}" alt="" height="22">
                        </span>
                        <span class="logo-lg">
                            <img src="{{ URL::asset('build/images/logo-light.png') }}" alt="" height="25">
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

                <button type="button" class="btn btn-sm px-3 fs-15 user-name-text header-item d-none d-md-block" data-bs-toggle="modal" data-bs-target="#searchModal">
                    <span class="bi bi-search me-2"></span> Search for Toner...
                </button>
            </div>

            <div class="d-flex align-items-center">

                <div class="d-md-none topbar-head-dropdown header-item">
                    <button type="button" class="btn btn-icon btn-topbar btn-ghost-dark rounded-circle" id="page-header-search-dropdown" data-bs-toggle="modal" data-bs-target="#searchModal">
                        <i class="bi bi-search fs-16"></i>
                    </button>
                </div>


                <div class="dropdown ms-sm-3 header-item topbar-user topbar-head-dropdown dropdown-hover-end">
                    <button type="button" class="btn" id="page-header-user-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="d-flex align-items-center">
                            <img class="rounded-circle header-profile-user" src="@if(@Auth::user()->avatar) {{ URL::asset('images/users/')."/". @Auth::user()->avatar}} @else {{ URL::asset('build/images/users/avatar-1.jpg') }} @endif" alt="Header Avatar">
                            <span class="text-start ms-xl-2">
                                <span class="d-none d-xl-inline-block ms-1 fw-medium user-name-text">{{ @Auth::user()->first_name." ".@Auth::user()->last_name }}</span>
                                <span class="d-none d-xl-block ms-1 fs-13 user-name-sub-text" style="display: none">...</span>
                            </span>
                        </span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end">

                        <h6 class="dropdown-header">  {{@Auth::user()->last_name}}!</h6>
                        <a class="dropdown-item" href="product-list"><i class="bi bi-box-seam text-muted fs-15 align-middle me-1"></i> <span class="align-middle">Products</span></a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="{{ url('logout') }}"><i class="bi bi-box-arrow-right text-muted fs-15 align-middle me-1"></i> <span class="align-middle" data-key="t-logout">{{ __('t-logout') }}</span></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>


<!-- Modal -->
<div class="modal fade" id="searchModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content rounded">
            <div class="modal-header p-3">
                <div class="position-relative w-100">
                    <input type="text" class="form-control form-control-lg border-2" placeholder="Search for Toner..." autocomplete="off" id="search-options" value="">
                    <span class="bi bi-search search-widget-icon fs-17"></span>
                    <a href="javascript:void(0);" class="search-widget-icon fs-14 link-secondary text-decoration-underline search-widget-icon-close d-none" id="search-close-options">Clear</a>
                </div>
            </div>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0 overflow-hidden" id="search-dropdown">




            </div>
        </div>
    </div>
</div>
