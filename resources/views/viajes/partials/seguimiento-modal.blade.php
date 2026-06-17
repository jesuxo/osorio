{{-- resources/views/viajes/partials/seguimiento-modal.blade.php --}}
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12">
            <div class="alert alert-info">
                <i class="bi bi-info-circle me-2"></i>
                Seguimiento en tiempo real del viaje. Se muestran todos los puntos registrados.
            </div>
        </div>
    </div>

    {{-- Leyenda de colores --}}
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex flex-wrap gap-3 justify-content-center">
                <div class="d-flex align-items-center">
                    <div style="width: 20px; height: 20px; background: #10b981; border-radius: 50%; margin-right: 5px;"></div>
                    <span>Inicio de Etapa</span>
                </div>
                <div class="d-flex align-items-center">
                    <div style="width: 20px; height: 20px; background: #ef4444; border-radius: 50%; margin-right: 5px;"></div>
                    <span>Fin de Etapa</span>
                </div>
                <div class="d-flex align-items-center">
                    <div style="width: 20px; height: 20px; background: #3b82f6; border-radius: 50%; margin-right: 5px;"></div>
                    <span>Reporte Manual</span>
                </div>
                <div class="d-flex align-items-center">
                    <div style="width: 20px; height: 20px; background: #f59e0b; border-radius: 50%; margin-right: 5px;"></div>
                    <span>Ubicación Actual</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Información actual --}}
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0"><i class="fas fa-map-marker-alt me-2"></i>Última Ubicación</h6>
                </div>
                <div class="card-body">
                    @if(isset($ultimoSeguimiento) && $ultimoSeguimiento)
                        <p class="mb-1">
                            <strong>Fecha/Hora:</strong>
                            {{ $ultimoSeguimiento->fecha_hora->format('d/m/Y H:i:s') }}
                        </p>
                        <p class="mb-1">
                            <strong>Coordenadas:</strong>
                            {{ $ultimoSeguimiento->latitud }}, {{ $ultimoSeguimiento->longitud }}
                        </p>
                        @if($ultimoSeguimiento->ubicacion_texto)
                            <p class="mb-1">
                                <strong>Dirección:</strong> {{ $ultimoSeguimiento->ubicacion_texto }}
                            </p>
                        @endif
                        <p class="mb-1">
                            <strong>Velocidad:</strong> {{ $ultimoSeguimiento->velocidad ?? 0 }} km/h
                        </p>
                    @else
                        <p class="text-muted">No hay información de ubicación disponible</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0"><i class="fas fa-chart-line me-2"></i>Información del Vehículo</h6>
                </div>
                <div class="card-body">
                    @if(isset($ultimoSeguimiento) && $ultimoSeguimiento)
                        <p class="mb-1">
                            <strong>Combustible:</strong>
                        </p>
                        <div class="progress mb-2">
                            <div class="progress-bar bg-{{ $ultimoSeguimiento->combustible_color ?? 'secondary' }}"
                                 role="progressbar"
                                 style="width: {{ $ultimoSeguimiento->nivel_combustible ?? 0 }}%">
                                {{ $ultimoSeguimiento->nivel_combustible ?? 0 }}%
                            </div>
                        </div>
                        <p class="mb-1">
                            <strong>Temperatura Motor:</strong>
                            {{ $ultimoSeguimiento->temperatura_motor ?? 'N/A' }} °C
                        </p>
                        <p class="mb-1">
                            <strong>Estado Motor:</strong>
                            @if(isset($ultimoSeguimiento->estado_motor) && $ultimoSeguimiento->estado_motor == 'encendido')
                                <span class="badge bg-success">Encendido</span>
                            @else
                                <span class="badge bg-danger">Apagado</span>
                            @endif
                        </p>
                        <p class="mb-1">
                            <strong>Kilometraje Total:</strong>
                            {{ number_format($ultimoSeguimiento->kilometraje_total ?? 0, 2) }} km
                        </p>
                    @else
                        <p class="text-muted">No hay información del vehículo disponible</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Mapa con todos los puntos --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <h6 class="mb-0"><i class="fas fa-map-marked-alt me-2"></i>Recorrido Completo</h6>
                </div>
                <div class="card-body p-0">
                    <div id="mapa-seguimiento-completo" style="height: 450px;"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Etapa Actual --}}
    @if(isset($viaje) && $viaje && $viaje->etapa_actual)
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-warning">
                    <div class="card-header bg-warning text-dark">
                        <h6 class="mb-0"><i class="fas fa-play-circle me-2"></i>Etapa Actual</h6>
                    </div>
                    <div class="card-body">
                        <h5>{{ $viaje->etapa_actual->nombre }}</h5>
                        <p class="mb-1"><i class="fas fa-map-marker-alt me-2"></i> {{ $viaje->etapa_actual->ubicacion }}</p>
                        @if($viaje->etapa_actual->fecha_real_inicio)
                            <p class="mb-0"><small>Iniciada: {{ $viaje->etapa_actual->fecha_real_inicio->format('d/m/Y H:i') }}</small></p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Historial de ubicaciones --}}
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-secondary text-white">
                    <h6 class="mb-0"><i class="fas fa-history me-2"></i>Todos los Puntos Registrados</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive" style="max-height: 300px;">
                        <table class="table table-sm table-hover">
                            <thead>
                            <tr>
                                <th>Fecha/Hora</th>
                                <th>Tipo</th>
                                <th>Ubicación</th>
                                <th>KM</th>
                                <th>Acción</th>
                            </tr>
                            </thead>
                            <tbody id="historial-seguimiento-completo">
                            {{-- Se llenará vía JavaScript --}}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<input type="hidden" id="viaje-id-seguimiento-completo" value="{{ $viaje->id ?? '' }}">
<input type="hidden" id="puntos-data-seguimiento" value="{{ json_encode($puntos ?? []) }}">

<style>
    .punto-tooltip {
        background: rgba(0,0,0,0.8);
        color: white;
        border: none;
        border-radius: 4px;
        padding: 4px 8px;
        font-size: 11px;
        font-weight: normal;
    }

    .marcador-punto {
        transition: transform 0.2s;
    }

    .marcador-punto:hover {
        transform: scale(1.2);
        z-index: 1000 !important;
    }
</style>

<script>
    (function() {
        'use strict';

        console.log('🚀 Script del modal ejecutándose');

        // Variables específicas del modal
        let mapaCompleto = null;
        let marcadorActual = null;
        let polylineRuta = null;
        let marcadoresPuntos = [];
        let intervaloActualizacion = null;

        const viajeId = document.getElementById('viaje-id-seguimiento-completo')?.value;
        console.log('🆔 viajeId:', viajeId);

        // Datos de puntos
        let puntosData = [];
        try {
            const puntosElement = document.getElementById('puntos-data-seguimiento');
            if (puntosElement && puntosElement.value) {
                puntosData = JSON.parse(puntosElement.value);
                console.log('✅ Puntos cargados:', puntosData.length);
            }
        } catch (e) {
            console.error('❌ Error parsing puntos:', e);
        }

        // Inicializar inmediatamente
        setTimeout(() => {
            console.log('⏰ Inicializando mapa...');

            const mapaContainer = document.getElementById('mapa-seguimiento-completo');
            console.log('🗺️ Contenedor del mapa:', mapaContainer ? 'Encontrado' : 'NO ENCONTRADO');

            @if(isset($ultimoSeguimiento) && $ultimoSeguimiento)
            const lat = {{ $ultimoSeguimiento->latitud ?? 19.4326 }};
            const lng = {{ $ultimoSeguimiento->longitud ?? -99.1332 }};
            const ubicacionTexto = '{{ addslashes($ultimoSeguimiento->ubicacion_texto ?? '') }}';
            const fechaHora = '{{ isset($ultimoSeguimiento->fecha_hora) ? $ultimoSeguimiento->fecha_hora->format('d/m/Y H:i:s') : '' }}';
            @else
            const lat = 19.4326;
            const lng = -99.1332;
            const ubicacionTexto = '';
            const fechaHora = '';
            @endif

            inicializarMapa(lat, lng, ubicacionTexto, fechaHora);

            if (puntosData && puntosData.length > 0) {
                mostrarPuntosEnMapa(puntosData);
                actualizarTablaHistorial(puntosData);
            }

            iniciarSeguimientoTiempoReal();
        }, 100);

        function inicializarMapa(lat, lng, ubicacionTexto, fechaHora) {
            console.log('🗺️ Creando mapa...');

            if (typeof L === 'undefined') {
                console.error('❌ Leaflet no está disponible');
                return;
            }

            try {
                mapaCompleto = L.map('mapa-seguimiento-completo').setView([lat, lng], 13);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap contributors'
                }).addTo(mapaCompleto);

                const iconActual = L.icon({
                    iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-blue.png',
                    shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                    iconSize: [25, 41],
                    iconAnchor: [12, 41],
                    popupAnchor: [1, -34]
                });

                marcadorActual = L.marker([lat, lng], { icon: iconActual }).addTo(mapaCompleto);

                if (ubicacionTexto) {
                    marcadorActual.bindPopup(`
                    <b>Ubicación Actual</b><br>
                    ${ubicacionTexto}<br>
                    <small>${fechaHora}</small>
                `).openPopup();
                }

                polylineRuta = L.polyline([], { color: '#3b82f6', weight: 4, opacity: 0.7 }).addTo(mapaCompleto);

                // Agregar controles
                setTimeout(() => {
                    if (mapaCompleto) {
                        L.easyButton('fa-crosshairs', function() {
                            if (marcadorActual) mapaCompleto.setView(marcadorActual.getLatLng(), 15);
                        }, 'Centrar').addTo(mapaCompleto);

                        L.easyButton('fa-route', function() {
                            if (polylineRuta && marcadoresPuntos.length > 1) {
                                const grupo = L.featureGroup(marcadoresPuntos);
                                mapaCompleto.fitBounds(grupo.getBounds(), { padding: [50, 50] });
                            }
                        }, 'Ver todo').addTo(mapaCompleto);
                    }
                }, 1000);

                console.log('✅ Mapa creado exitosamente');

            } catch (error) {
                console.error('❌ Error creando mapa:', error);
            }
        }

        function mostrarPuntosEnMapa(puntos) {
            if (!mapaCompleto) return;

            marcadoresPuntos.forEach(m => mapaCompleto.removeLayer(m));
            marcadoresPuntos = [];

            const bounds = [];
            const puntosRuta = [];

            puntos.sort((a, b) => new Date(a.fecha_raw) - new Date(b.fecha_raw));

            puntos.forEach(punto => {
                const color = punto.color || getColorPorTipo(punto.tipo_punto);
                const icono = punto.icono || getIconoPorTipo(punto.tipo_punto);

                const icon = L.divIcon({
                    html: `<div style="background:${color}; width:24px; height:24px; border-radius:50%; display:flex; align-items:center; justify-content:center; color:white; font-size:12px; font-weight:bold; border:2px solid white; box-shadow:0 2px 5px rgba(0,0,0,0.3);">${icono}</div>`,
                    className: 'marcador-punto',
                    iconSize: [24, 24],
                    iconAnchor: [12, 12],
                    popupAnchor: [0, -12]
                });

                const marcador = L.marker([parseFloat(punto.latitud), parseFloat(punto.longitud)], { icon }).addTo(mapaCompleto);

                // Tooltip al pasar el mouse
                marcador.bindTooltip(`${punto.tipo_texto} - ${punto.kilometraje ? parseFloat(punto.kilometraje).toFixed(2) + ' km' : ''}`, {
                    permanent: false,
                    direction: 'top',
                    className: 'punto-tooltip'
                });

                // Popup al hacer clic
                marcador.bindPopup(`
                <div style="min-width: 200px;">
                    <b style="color: ${color};">${punto.tipo_texto}</b><br>
                    <small><b>Fecha:</b> ${punto.fecha_hora}</small><br>
                    <small><b>KM:</b> ${punto.kilometraje ? parseFloat(punto.kilometraje).toFixed(2) + ' km' : 'N/A'}</small><br>
                    <small><b>Lugar:</b> ${punto.ubicacion_texto?.substring(0, 100) || 'Sin dirección'}</small>
                </div>
            `);

                marcadoresPuntos.push(marcador);
                bounds.push([parseFloat(punto.latitud), parseFloat(punto.longitud)]);
                puntosRuta.push([parseFloat(punto.latitud), parseFloat(punto.longitud)]);
            });

            // Separar puntos superpuestos
            separarPuntosSuperpuestos(marcadoresPuntos);

            if (puntosRuta.length > 1) {
                polylineRuta.setLatLngs(puntosRuta);
            }

            if (bounds.length > 0) {
                mapaCompleto.fitBounds(bounds, { padding: [50, 50] });
            }
        }

        function separarPuntosSuperpuestos(marcadores) {
            const ubicaciones = {};

            marcadores.forEach((marcador, index) => {
                const pos = marcador.getLatLng();
                const key = pos.lat.toFixed(6) + ',' + pos.lng.toFixed(6);

                if (!ubicaciones[key]) {
                    ubicaciones[key] = [];
                }
                ubicaciones[key].push({ marcador, index });
            });

            Object.values(ubicaciones).forEach(grupo => {
                if (grupo.length > 1) {
                    const radio = 0.00015; // ~15 metros
                    const angulo = (2 * Math.PI) / grupo.length;

                    grupo.forEach((item, i) => {
                        const nuevaLat = item.marcador.getLatLng().lat + radio * Math.cos(angulo * i);
                        const nuevaLng = item.marcador.getLatLng().lng + radio * Math.sin(angulo * i);
                        item.marcador.setLatLng([nuevaLat, nuevaLng]);
                    });
                }
            });
        }

        function actualizarTablaHistorial(puntos) {
            const tbody = document.getElementById('historial-seguimiento-completo');
            if (!tbody) return;

            tbody.innerHTML = '';

            if (!puntos || puntos.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" class="text-center">No hay puntos registrados</td></tr>';
                return;
            }

            puntos.sort((a, b) => new Date(b.fecha_raw) - new Date(a.fecha_raw));

            puntos.forEach(punto => {
                const row = `
                <tr>
                    <td>${punto.fecha_hora}</td>
                    <td><span class="badge" style="background:${punto.color}">${punto.tipo_texto}</span></td>
                    <td>${punto.ubicacion_texto?.substring(0, 30) || 'Sin dirección'}...</td>
                    <td>${punto.kilometraje ? parseFloat(punto.kilometraje).toFixed(2) + ' km' : 'N/A'}</td>
                    <td><button class="btn btn-sm btn-info" onclick="window.centrarEnPunto(${punto.latitud}, ${punto.longitud})">📍</button></td>
                </tr>
            `;
                tbody.insertAdjacentHTML('beforeend', row);
            });
        }

        function getColorPorTipo(tipo) {
            const colores = {
                'inicio_etapa': '#10b981',
                'fin_etapa': '#ef4444',
                'manual': '#3b82f6',
                'automatico': '#f59e0b'
            };
            return colores[tipo] || '#6b7280';
        }

        function getIconoPorTipo(tipo) {
            const iconos = {
                'inicio_etapa': '▶',
                'fin_etapa': '◼',
                'manual': '📍',
                'automatico': '⚡'
            };
            return iconos[tipo] || '•';
        }

        function iniciarSeguimientoTiempoReal() {
            intervaloActualizacion = setInterval(() => {
                if (!viajeId) return;
                fetch(`/seguimiento/${viajeId}/ultima-ubicacion`)
                    .then(r => r.json())
                    .then(data => {
                        if (data.success && data.lat && data.lng && marcadorActual) {
                            marcadorActual.setLatLng([data.lat, data.lng]);
                        }
                    })
                    .catch(console.error);
            }, 15000);
        }

        window.centrarEnPunto = function(lat, lng) {
            if (mapaCompleto) mapaCompleto.setView([parseFloat(lat), parseFloat(lng)], 16);
        };

    })();
</script>
