<div class="card-sucursal h-100">
    <div class="card-sucursal-head">

        @if(isset($suc['logo']) && file_exists(public_path('build/images/brands/' . $suc['logo'])))
            <img src="{{ asset('build/images/brands/' . $suc['logo']) }}"
                 alt="{{ $suc['tipo'] }}"
                 class="suc-logo">
        @else
            <div class="suc-logo-placeholder">
                <i class="bi bi-shop"></i>
            </div>
        @endif

        <div>
            <small class="suc-tipo">{{ $suc['tipo'] }}</small>
            <h6 class="suc-nombre">{{ $suc['nombre'] }}</h6>
        </div>
    </div>

    <div class="card-sucursal-body">

        @if($suc['dir'])
            <p class="suc-line">
                <i class="bi bi-geo-alt"></i>
                <span>{{ $suc['dir'] }}, {{ $suc['ciudad'] }}</span>
            </p>
        @else
            <p class="suc-line">
                <i class="bi bi-geo-alt"></i>
                <span>{{ $suc['ciudad'] }}</span>
            </p>
        @endif

        @if($suc['horario'])
            <p class="suc-line">
                <i class="bi bi-clock"></i>
                <span>{{ $suc['horario'] }}</span>
            </p>
        @endif

        @if($suc['tel'])
            <p class="suc-line">
                <i class="bi bi-telephone"></i>
                <a href="tel:+58{{ preg_replace('/[^0-9]/', '', ltrim($suc['tel'], '0')) }}">
                    {{ $suc['tel'] }}
                </a>
            </p>
        @endif

        @if($suc['ig'])
            <p class="suc-line">
                <i class="bi bi-instagram"></i>
                <a href="https://instagram.com/{{ ltrim($suc['ig'], '@') }}"
                   target="_blank" rel="noopener">
                    {{ $suc['ig'] }}
                </a>
            </p>
        @endif

    </div>

    <div class="card-sucursal-foot">
        <a href="https://wa.me/584120675809?text={{ urlencode('Hola, quiero información sobre la tienda ' . $suc['nombre'] . ' en ' . $suc['ciudad'] . '.') }}"
           target="_blank" rel="noopener"
           class="btn btn-cf btn-sm w-100">
            <i class="bi bi-whatsapp me-1"></i> Contactar
        </a>
    </div>
</div>
