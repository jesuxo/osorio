@php
    $ciudades = [
        'lafria' => [
            'label'   => 'La Fría',
            'slug'    => 'lafria',
            'count'   => 3,
            'sucursales' => [
                [
                    'nombre'  => 'Osorio Group',
                    'tipo'    => 'Multimarca',
                    'ciudad'  => 'La Fría',
                    'dir'     => 'Av. Aeropuerto',
                    'horario' => null,
                    'tel'     => null,
                    'ig'      => '@osoriogroup',
                    'logo'    => 'osorio.png',
                ],
                [
                    'nombre'  => 'Bera La Fría',
                    'tipo'    => 'Bera',
                    'ciudad'  => 'La Fría',
                    'dir'     => 'Av. Aeropuerto',
                    'horario' => null,
                    'tel'     => null,
                    'ig'      => '@vera_tachira_lafria',
                    'logo'    => 'bera.png',
                ],
                [
                    'nombre'  => 'Bel Motos',
                    'tipo'    => 'Bel Motos',
                    'ciudad'  => 'La Fría',
                    'dir'     => 'Av. Aeropuerto',
                    'horario' => null,
                    'tel'     => null,
                    'ig'      => '@belmotoosorio',
                    'logo'    => 'bel.png',
                ],
            ],
        ],
        'sancristobal' => [
            'label'   => 'San Cristóbal',
            'slug'    => 'sancristobal',
            'count'   => 4,
            'sucursales' => [
                [
                    'nombre'  => 'EK San Cristóbal',
                    'tipo'    => 'EK · QJMOTOR',
                    'ciudad'  => 'San Cristóbal',
                    'dir'     => 'Av. Carabobo',
                    'horario' => null,
                    'tel'     => null,
                    'ig'      => '@osoriomotos',
                    'logo'    => 'ek.png',
                ],
                [
                    'nombre'  => 'CFMOTO Táchira',
                    'tipo'    => 'CFMOTO',
                    'ciudad'  => 'San Cristóbal',
                    'dir'     => 'Av. Libertador, Edif. Kato Motor, local B-10',
                    'horario' => 'Lun–vie 8:30–17:00 · Sáb 9:00–14:00',
                    'tel'     => '0414-4180083',
                    'ig'      => '@cfmoto_tachira',
                    'logo'    => 'cfmoto.png',
                ],
                [
                    'nombre'  => 'Haojin Osorio',
                    'tipo'    => 'Haojin',
                    'ciudad'  => 'San Cristóbal',
                    'dir'     => null,
                    'horario' => null,
                    'tel'     => null,
                    'ig'      => '@haojinosorio',
                    'logo'    => 'haojin.png',
                ],
                [
                    'nombre'  => 'Bel Motos',
                    'tipo'    => 'Bel Motos',
                    'ciudad'  => 'San Cristóbal',
                    'dir'     => 'Av. Carabobo',
                    'horario' => null,
                    'tel'     => null,
                    'ig'      => '@belmotoosorio',
                    'logo'    => 'bel.png',
                ],
            ],
        ],
        'sanantonio' => [
            'label'   => 'San Antonio',
            'slug'    => 'sanantonio',
            'count'   => 4,
            'sucursales' => [
                [
                    'nombre'  => 'Osorio Group',
                    'tipo'    => 'Multimarca',
                    'ciudad'  => 'San Antonio',
                    'dir'     => 'Av. Venezuela',
                    'horario' => null,
                    'tel'     => null,
                    'ig'      => null,
                    'logo'    => 'osorio.png',
                ],
                [
                    'nombre'  => 'EK San Antonio',
                    'tipo'    => 'EK · QJMOTOR',
                    'ciudad'  => 'San Antonio',
                    'dir'     => 'Av. Venezuela',
                    'horario' => null,
                    'tel'     => null,
                    'ig'      => '@eksanantonio',
                    'logo'    => 'ek.png',
                ],
                [
                    'nombre'  => 'Haojin Osorio',
                    'tipo'    => 'Haojin',
                    'ciudad'  => 'San Antonio',
                    'dir'     => null,
                    'horario' => null,
                    'tel'     => null,
                    'ig'      => '@haojinosorio',
                    'logo'    => 'haojin.png',
                ],
                [
                    'nombre'  => 'Bel Motos',
                    'tipo'    => 'Bel Motos',
                    'ciudad'  => 'San Antonio',
                    'dir'     => 'Av. Venezuela',
                    'horario' => null,
                    'tel'     => null,
                    'ig'      => '@belmotoosorio',
                    'logo'    => 'bel.png',
                ],
            ],
        ],
    ];

    $tabActivo = 'sancristobal';//request('tab', 'todas');
@endphp
<section class="py-5 sucursales-section sucursales-section-dark" id="sucursales">
    <div class="container">

        {{-- HEADER --}}
        <div class="text-center mb-4">
            <span class="badge-cat badge-cat-dark mb-3 d-inline-block">
                <i class="bi bi-geo-alt me-1"></i> Táchira, Venezuela
            </span>
            <h2 class="fw-bold mb-2 text-white">Nuestras sucursales</h2>
            <p class="text-light-emphasis mb-0" style="color: rgba(255, 255, 255, 0.7) !important;">
                11 tiendas en La Fría, San Cristóbal y San Antonio.
                Retira tu moto en tienda o recíbela en tu estado.
            </p>
        </div>

        {{-- TABS --}}
        <div class="suc-tabs-wrapper mb-4">
            <ul class="nav nav-pills suc-tabs justify-content-center" id="sucursalesTabs" role="tablist">

                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $tabActivo === 'todas' ? 'active' : '' }}"
                            data-bs-toggle="pill"
                            data-bs-target="#tab-todas"
                            type="button"
                            role="tab">
                        Todas
                        <span class="suc-tab-count">11</span>
                    </button>
                </li>

                @foreach($ciudades as $key => $ciudad)
                    <li class="nav-item" role="presentation">
                        <button class="nav-link {{ $tabActivo === $key ? 'active' : '' }}"
                                data-bs-toggle="pill"
                                data-bs-target="#tab-{{ $key }}"
                                type="button"
                                role="tab">
                            {{ $ciudad['label'] }}
                            <span class="suc-tab-count">{{ $ciudad['count'] }}</span>
                        </button>
                    </li>
                @endforeach

            </ul>
        </div>

        {{-- CONTENIDO DE TABS --}}
        <div class="tab-content" id="sucursalesTabContent">

            {{-- TAB "TODAS" --}}
            <div class="tab-pane fade {{ $tabActivo === 'todas' ? 'show active' : '' }}"
                 id="tab-todas" role="tabpanel">

                @foreach($ciudades as $key => $ciudad)
                    <div class="mb-5">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <h5 class="fw-bold mb-0 text-white">{{ $ciudad['label'] }}</h5>
                            <span class="badge bg-white text-dark">
                                {{ $ciudad['count'] }} {{ $ciudad['count'] == 1 ? 'tienda' : 'tiendas' }}
                            </span>
                        </div>

                        <div class="row g-3">
                            @foreach($ciudad['sucursales'] as $suc)
                                <div class="col-lg-3 col-md-6">
                                    @include('web.partials.card-sucursal', ['suc' => $suc])
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach

            </div>

            {{-- TABS POR CIUDAD --}}
            @foreach($ciudades as $key => $ciudad)
                <div class="tab-pane fade {{ $tabActivo === $key ? 'show active' : '' }}"
                     id="tab-{{ $key }}" role="tabpanel">

                    <div class="row g-3">
                        @foreach($ciudad['sucursales'] as $suc)
                            <div class="col-lg-3 col-md-6">
                                @include('web.partials.card-sucursal', ['suc' => $suc])
                            </div>
                        @endforeach
                    </div>

                </div>
            @endforeach

        </div>
    </div>
</section>
