{{-- resources/views/web/partials/header-motos.blade.php --}}
<section class="py-4" style="background: #f8f9fa;" id="header-motos">
    <div class="container">
        <nav aria-label="breadcrumb" class="mb-2">
            <ol class="breadcrumb mb-0 small">
                <li class="breadcrumb-item">
                    <a href="{{ route('web.home') }}" class="text-decoration-none">Inicio</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('web.motos') }}" class="text-decoration-none">Motos</a>
                </li>
                @if(!empty($marcaActiva))
                    <li class="breadcrumb-item active" aria-current="page">
                        {{ $marcaActiva->nombre_corto }}
                    </li>
                @endif
                @if(!empty($estilo))
                    <li class="breadcrumb-item active" aria-current="page">
                        {{ $estilo }}
                    </li>
                @endif
            </ol>
        </nav>

        <h1 class="fw-bold mb-1" style="font-size: 1.75rem;">
            @if(!empty($marcaActiva) && !empty($estilo))
                {{ $estilo }} {{ $marcaActiva->nombre_corto }}
            @elseif(!empty($marcaActiva))
                Motos {{ $marcaActiva->nombre_corto }}
            @elseif(!empty($estilo))
                Motos {{ $estilo }}
            @else
                Encuentra tu moto
            @endif
        </h1>
        <p class="text-secondary mb-0 small">
            <span id="total-motos">{{ $motos->total() }}</span>
            {{ $motos->total() == 1 ? 'moto encontrada' : 'motos encontradas' }}
        </p>
    </div>
</section>
