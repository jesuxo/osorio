{{-- resources/views/publico/seguimiento.blade.php --}}
    <!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Seguimiento de Viaje - Chofer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet-easybutton@2/src/easy-button.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding-bottom: 70px;
        }

        .popup-etapa .leaflet-popup-content {
            margin: 10px;
            line-height: 1.5;
        }

        .popup-etapa h6 {
            font-size: 14px;
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
            margin-bottom: 8px;
        }

        .popup-etapa p {
            margin: 3px 0;
            font-size: 12px;
        }

        .marcador-etapa {
            transition: transform 0.2s;
        }

        .marcador-etapa:hover {
            transform: scale(1.1);
            z-index: 1000 !important;
        }

        .leaflet-tooltip {
            background: rgba(0,0,0,0.8);
            color: white;
            border: none;
            border-radius: 4px;
            padding: 4px 8px;
            font-size: 12px;
        }

        .leaflet-tooltip-top:before {
            border-top-color: rgba(0,0,0,0.8);
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 0 0 20px 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .card {
            border-radius: 15px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            border: none;
        }

        .card-header {
            background-color: #fff;
            border-bottom: 2px solid #f0f0f0;
            font-weight: 600;
            border-radius: 15px 15px 0 0 !important;
        }

        .etapa-item {
            transition: all 0.3s ease;
            border-left: 4px solid #dee2e6;
            padding: 15px;
            margin-bottom: 15px;
            background-color: #f8f9fa;
            border-radius: 8px;
        }

        .etapa-item.pendiente {
            border-left-color: #6c757d;
            background-color: #f8f9fa;
        }

        .etapa-item.en_curso {
            border-left-color: #ffc107;
            background-color: #fff3cd;
        }

        .etapa-item.completado {
            border-left-color: #28a745;
            background-color: #d4edda;
        }

        .etapa-item .flex-grow-1 {
            padding-right: 15px;
        }

        .etapa-item .badge {
            font-size: 0.85rem;
            padding: 6px 10px;
        }

        .etapa-item small {
            font-size: 0.8rem;
        }

        .etapa-item .text-success {
            color: #28a745 !important;
        }

        .etapa-item .text-info {
            color: #17a2b8 !important;
        }

        .btn-etapa {
            transition: all 0.2s;
            font-weight: 500;
            border-radius: 20px;
        }

        .btn-etapa:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        #mapa {
            height: 250px;
            border-radius: 10px;
            margin-bottom: 15px;
        }

        .ubicacion-actual {
            background-color: #e3f2fd;
            padding: 10px;
            border-radius: 10px;
            margin-bottom: 15px;
        }

        .float-btn {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: #28a745;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
            cursor: pointer;
            transition: all 0.3s;
            z-index: 1000;
        }

        .float-btn:hover {
            transform: scale(1.1);
        }

        .float-btn:active {
            transform: scale(0.95);
        }

        .badge-estado {
            font-size: 0.9rem;
            padding: 8px 12px;
        }

        .progress {
            height: 10px;
            border-radius: 5px;
        }

        .combustible-bar {
            height: 8px;
            border-radius: 4px;
        }

        @media (max-width: 768px) {
            .container {
                padding-left: 10px;
                padding-right: 10px;
            }
        }
    </style>
</head>
<body>
<div class="header text-center">
    <div class="d-flex justify-content-between align-items-center px-3">
        <h4 class="mb-0"><i class="fas fa-truck me-2"></i>Seguimiento de Viaje</h4>
        <span class="badge {{
            $viaje->estado == 'planeado' ? 'bg-secondary' :
            ($viaje->estado == 'en_curso' ? 'bg-warning text-dark' :
            ($viaje->estado == 'completado' ? 'bg-success' : 'bg-danger'))
        }} badge-estado" id="estado-viaje">
            {{ ucfirst($viaje->estado) }}
        </span>
    </div>
    <p class="mb-0 mt-2">Viaje #{{ $viaje->folio ?? $viaje->id }}</p>
</div>

<div class="container">
    {{-- Información del viaje --}}
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-6">
                    <small class="text-muted">Origen</small>
                    <p class="fw-bold mb-0">{{ $viaje->origen }}</p>
                </div>
                <div class="col-6 text-end">
                    <small class="text-muted">Destino</small>
                    <p class="fw-bold mb-0">{{ $viaje->destino }}</p>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-6">
                    <small class="text-muted">Camión</small>
                    <p class="fw-bold mb-0">{{ $viaje->camion->placa ?? 'N/A' }}</p>
                </div>
                <div class="col-6 text-end">
                    <small class="text-muted">Chofer</small>
                    <p class="fw-bold mb-0">{{ $viaje->chofer->nombre_completo ?? 'N/A' }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Progreso --}}
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between mb-2">
                <span>Progreso del viaje</span>
                <span class="badge bg-primary" id="progreso-text">{{ $viaje->progreso }}%</span>
            </div>
            <div class="progress">
                <div class="progress-bar bg-success" id="progreso-bar" role="progressbar" style="width: {{ $viaje->progreso }}%"></div>
            </div>
        </div>
    </div>

    {{-- Mapa y ubicación actual --}}
    <div class="card">
        <div class="card-header">
            <i class="fas fa-map-marker-alt me-2 text-danger"></i>Mi Ubicación Actual
        </div>
        <div class="card-body">
            <div id="mapa"></div>
        </div>
    </div>

    {{-- Etapas del viaje --}}
    <div class="card">
        <div class="card-header">
            <i class="fas fa-tasks me-2 text-primary"></i>Etapas del Viaje
        </div>
        <div class="card-body" id="etapas-container">
            @foreach($viaje->etapas as $etapa)
                <div class="etapa-item {{ $etapa->estado }}" id="etapa-{{ $etapa->id }}" data-id="{{ $etapa->id }}">
                    <div class="d-flex justify-content-between align-items-start">
                        {{-- Información de la etapa --}}
                        <div class="flex-grow-1">
                            <h6 class="mb-1 fw-bold">{{ $etapa->nombre }}</h6>
                            <p class="mb-1 small">
                                <i class="fas fa-map-marker-alt me-1 text-muted"></i>
                                {{ $etapa->ubicacion }}
                            </p>

                            @if($etapa->kilometraje_estimado)
                                <p class="mb-0 small text-muted">
                                    <i class="fas fa-road me-1"></i>
                                    KM estimado: {{ number_format($etapa->kilometraje_estimado, 2) }}
                                </p>
                            @endif

                            @if($etapa->kilometraje_inicio)
                                <p class="mb-0 small text-primary km-inicio">
                                    <i class="fas fa-flag-checkered me-1"></i>
                                    KM inicio: {{ number_format($etapa->kilometraje_inicio, 2) }}
                                </p>
                            @endif

                            @if($etapa->kilometraje_real)
                                <p class="mb-0 small text-success km-fin">
                                    <i class="fas fa-flag-checkered me-1"></i>
                                    KM final: {{ number_format($etapa->kilometraje_real, 2) }}
                                </p>
                                @php
                                    $recorrido = $etapa->kilometraje_real - $etapa->kilometraje_inicio;
                                @endphp
                                <p class="mb-0 small text-info km-recorrido fw-bold">
                                    <i class="fas fa-road me-1"></i>
                                    KM recorridos: {{ number_format($recorrido, 2) }}
                                </p>
                            @endif

                            @if($etapa->fecha_real_inicio)
                                <p class="mb-0 small text-success">
                                    <i class="fas fa-play-circle me-1"></i>
                                    Inicio: {{ $etapa->fecha_real_inicio->format('d/m/Y H:i') }}
                                </p>
                            @endif
                            @if($etapa->fecha_real_fin)
                                <p class="mb-0 small text-success">
                                    <i class="fas fa-stop-circle me-1"></i>
                                    Fin: {{ $etapa->fecha_real_fin->format('d/m/Y H:i') }}
                                </p>
                            @endif
                        </div>

                        {{-- Botones y estado --}}
                        <div class="ms-3 text-end" style="min-width: 100px;" id="etapa-botones-{{ $etapa->id }}">
                            @if($etapa->estado == 'pendiente')
                                <button class="btn btn-sm btn-warning btn-etapa w-100" onclick="iniciarEtapa({{ $etapa->id }})">
                                    <i class="fas fa-play me-1"></i>Iniciar
                                </button>
                            @elseif($etapa->estado == 'en_curso')
                                <span class="badge bg-warning text-dark d-block mb-2">En curso</span>
                                <button class="btn btn-sm btn-success btn-etapa w-100" onclick="completarEtapa({{ $etapa->id }})">
                                    <i class="fas fa-check me-1"></i>Completar
                                </button>
                            @elseif($etapa->estado == 'completado')
                                <span class="badge bg-success d-block mb-1">Completado</span>
                                @if($etapa->fecha_real_fin)
                                    <small class="text-muted d-block">
                                        {{ $etapa->fecha_real_fin->format('H:i d/m') }}
                                    </small>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Botón flotante para actualizar ubicación --}}
    <div class="float-btn" onclick="obtenerUbicacion()">
        <i class="fas fa-location-arrow"></i>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/leaflet-easybutton@2/src/easy-button.js"></script>
<script>
    // Variables globales
    const token = '{{ $token }}';
    const viajeId = {{ $viaje->id }};
    let mapa;
    let marcadorActual;
    let marcadoresInicio = [];
    let marcadoresFin = [];
    let capaRuta = null;
    let intervalId;

    // Inicialización cuando el DOM está listo
    document.addEventListener('DOMContentLoaded', function() {
        inicializarMapa();
        cargarHistorial();
        cargarPuntosEtapas();
        iniciarSeguimientoTiempoReal();
    });

    function inicializarMapa() {
        @if($ultimoSeguimiento)
        const lat = {{ $ultimoSeguimiento->latitud ?? 8.222989501368694 }};
        const lng = {{ $ultimoSeguimiento->longitud ?? -72.25706047004749 }};
        @else
        const lat = 8.222989501368694;
        const lng = -72.25706047004749;
        @endif

            mapa = L.map('mapa').setView([lat, lng], 15);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap'
        }).addTo(mapa);

        const icon = L.icon({
            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-blue.png',
            shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34]
        });

        marcadorActual = L.marker([lat, lng], {icon: icon}).addTo(mapa);

        // Agregar controles al mapa
        agregarControlesMapa();
    }

    function cargarPuntosEtapas() {
        fetch(`/publico/seguimiento/${token}/info`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    limpiarMarcadores();
                    const puntosRuta = [];
                    const etapasOrdenadas = data.etapas.sort((a, b) => a.orden - b.orden);

                    etapasOrdenadas.forEach(etapa => {
                        if (etapa.latitud_inicio && etapa.longitud_inicio) {
                            agregarMarcadorEtapa(
                                etapa.id,
                                'inicio',
                                parseFloat(etapa.latitud_inicio),
                                parseFloat(etapa.longitud_inicio),
                                etapa.ubicacion_texto_inicio,
                                etapa.kilometraje_inicio,
                                etapa.nombre,
                                etapa.fecha_real_inicio
                            );
                            puntosRuta.push({
                                lat: parseFloat(etapa.latitud_inicio),
                                lng: parseFloat(etapa.longitud_inicio)
                            });
                        }

                        if (etapa.latitud_fin && etapa.longitud_fin) {
                            agregarMarcadorEtapa(
                                etapa.id,
                                'fin',
                                parseFloat(etapa.latitud_fin),
                                parseFloat(etapa.longitud_fin),
                                etapa.ubicacion_texto_fin,
                                etapa.kilometraje_real,
                                etapa.nombre,
                                etapa.fecha_real_fin
                            );
                            puntosRuta.push({
                                lat: parseFloat(etapa.latitud_fin),
                                lng: parseFloat(etapa.longitud_fin)
                            });
                        }
                    });

                    if (puntosRuta.length > 1) {
                        dibujarRuta(puntosRuta);
                    }

                    ajustarMapaAPuntos();
                    mostrarEstadisticasPuntos();
                }
            })
            .catch(error => console.error('Error cargando puntos:', error));
    }

    function agregarMarcadorEtapa(etapaId, tipo, lat, lng, direccion, kilometraje, nombreEtapa, fecha) {
        const color = tipo === 'inicio' ? '#10b981' : '#ef4444';
        const simbolo = tipo === 'inicio' ? '▶' : '◼';

        const icon = L.divIcon({
            html: `
                <div style="
                    background-color: ${color};
                    width: 36px;
                    height: 36px;
                    border-radius: 50%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    color: white;
                    font-size: 18px;
                    font-weight: bold;
                    border: 3px solid white;
                    box-shadow: 0 4px 8px rgba(0,0,0,0.3);
                    cursor: pointer;
                ">
                    ${simbolo}
                </div>
            `,
            className: 'marcador-etapa',
            iconSize: [36, 36],
            iconAnchor: [18, 18],
            popupAnchor: [0, -18]
        });

        const marcador = L.marker([lat, lng], { icon: icon, riseOnHover: true }).addTo(mapa);

        const popupContent = `
            <div style="min-width: 200px;">
                <h6 style="margin: 0 0 5px 0; color: ${color}; font-weight: bold;">
                    ${tipo === 'inicio' ? '🚩 INICIO' : '🏁 FIN'} - ${nombreEtapa}
                </h6>
                <p style="margin: 5px 0;">
                    <strong>Dirección:</strong><br>
                    ${direccion || 'No disponible'}
                </p>
                <p style="margin: 5px 0;">
                    <strong>Kilometraje:</strong> ${kilometraje ? kilometraje : 'N/A'} km
                </p>
                ${fecha ? `<p style="margin: 5px 0;"><strong>Fecha:</strong> ${fecha}</p>` : ''}
            </div>
        `;

        marcador.bindPopup(popupContent, { maxWidth: 300, className: 'popup-etapa' });

        if (tipo === 'inicio') {
            marcadoresInicio.push(marcador);
        } else {
            marcadoresFin.push(marcador);
        }

        return marcador;
    }

    function dibujarRuta(puntos) {
        if (capaRuta) mapa.removeLayer(capaRuta);
        const puntosLatLng = puntos.map(p => [p.lat, p.lng]);
        capaRuta = L.polyline(puntosLatLng, {
            color: '#3b82f6',
            weight: 4,
            opacity: 0.8,
            lineCap: 'round',
            lineJoin: 'round'
        }).addTo(mapa);
    }

    function ajustarMapaAPuntos() {
        const todosMarcadores = [...marcadoresInicio, ...marcadoresFin];
        if (todosMarcadores.length > 0) {
            const grupo = L.featureGroup(todosMarcadores);
            mapa.fitBounds(grupo.getBounds(), { padding: [50, 50], maxZoom: 15 });
        }
    }

    function limpiarMarcadores() {
        marcadoresInicio.forEach(m => mapa.removeLayer(m));
        marcadoresFin.forEach(m => mapa.removeLayer(m));
        marcadoresInicio = [];
        marcadoresFin = [];
        if (capaRuta) {
            mapa.removeLayer(capaRuta);
            capaRuta = null;
        }
    }

    function mostrarEstadisticasPuntos() {
        const totalInicios = marcadoresInicio.length;
        const totalFines = marcadoresFin.length;

        if (totalInicios > 0 || totalFines > 0) {
            const statsHtml = `
                <div id="stats-puntos" style="
                    position: absolute;
                    top: 20px;
                    right: 20px;
                    background: white;
                    padding: 15px;
                    border-radius: 10px;
                    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                    z-index: 1000;
                    border-left: 4px solid #3b82f6;
                ">
                    <h6 style="margin: 0 0 10px 0; font-weight: bold;">
                        <i class="fas fa-map-marked-alt me-2" style="color: #3b82f6;"></i>
                        Puntos de Control
                    </h6>
                    <div style="display: flex; gap: 15px;">
                        <div>
                            <div style="display: flex; align-items: center; gap: 5px;">
                                <div style="width: 12px; height: 12px; border-radius: 50%; background: #10b981;"></div>
                                <span>Inicios:</span>
                                <strong>${totalInicios}</strong>
                            </div>
                        </div>
                        <div>
                            <div style="display: flex; align-items: center; gap: 5px;">
                                <div style="width: 12px; height: 12px; border-radius: 50%; background: #ef4444;"></div>
                                <span>Fines:</span>
                                <strong>${totalFines}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            const statsAnterior = document.getElementById('stats-puntos');
            if (statsAnterior) statsAnterior.remove();

            document.getElementById('mapa').insertAdjacentHTML('beforeend', statsHtml);
        }
    }

    function agregarControlesMapa() {
        L.easyButton('fa-route', function() {
            ajustarMapaAPuntos();
        }, 'Ver todos los puntos').addTo(mapa);

        L.easyButton('fa-play-circle', function() {
            const visible = marcadoresInicio.some(m => mapa.hasLayer(m));
            marcadoresInicio.forEach(m => {
                if (visible) {
                    mapa.removeLayer(m);
                } else {
                    m.addTo(mapa);
                }
            });
        }, 'Alternar inicios').addTo(mapa);

        L.easyButton('fa-stop-circle', function() {
            const visible = marcadoresFin.some(m => mapa.hasLayer(m));
            marcadoresFin.forEach(m => {
                if (visible) {
                    mapa.removeLayer(m);
                } else {
                    m.addTo(mapa);
                }
            });
        }, 'Alternar fines').addTo(mapa);
    }

    function iniciarSeguimientoTiempoReal() {
        setInterval(actualizarInfoViaje, 30000);
    }

    function obtenerUbicacion() {
        if (!navigator.geolocation) {
            Swal.fire('Error', 'Tu navegador no soporta geolocalización', 'error');
            return;
        }

        document.querySelector('.float-btn').innerHTML = '<div class="spinner-border spinner-border-sm"></div>';

        navigator.geolocation.getCurrentPosition(
            function(position) {
                enviarUbicacion(position.coords.latitude, position.coords.longitude);
            },
            function(error) {
                Swal.fire('Error', 'Error obteniendo ubicación: ' + error.message, 'error');
                document.querySelector('.float-btn').innerHTML = '<i class="fas fa-location-arrow"></i>';
            },
            { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
        );
    }

    function enviarUbicacion(lat, lng) {
        fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`)
            .then(response => response.json())
            .then(data => {
                return fetch(`/publico/seguimiento/${token}/ubicacion`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        latitud: lat,
                        longitud: lng,
                        ubicacion_texto: data.display_name || '',
                        velocidad: 0
                    })
                });
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    actualizarMarcador(lat, lng);
                    actualizarInfoViaje();

                    if (data.viaje_actualizado) {
                        Swal.fire({
                            icon: 'success',
                            title: '¡Viaje iniciado!',
                            text: 'El viaje ha sido marcado como "En Curso" automáticamente',
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000
                        });
                        actualizarEstadoViaje(data.viaje_estado);
                    }

                    document.querySelector('.float-btn').innerHTML = '<i class="fas fa-check"></i>';
                    setTimeout(() => {
                        document.querySelector('.float-btn').innerHTML = '<i class="fas fa-location-arrow"></i>';
                    }, 2000);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Error', 'Error al enviar ubicación', 'error');
                document.querySelector('.float-btn').innerHTML = '<i class="fas fa-location-arrow"></i>';
            });
    }

    function actualizarMarcador(lat, lng) {
        marcadorActual.setLatLng([lat, lng]);
        mapa.setView([lat, lng], mapa.getZoom());
    }

    function iniciarEtapa(etapaId) {
        if (!navigator.geolocation) {
            Swal.fire('Error', 'Tu navegador no soporta geolocalización', 'error');
            return;
        }

        Swal.fire({
            title: 'Iniciar Etapa',
            text: 'Ingresa el kilometraje actual del camión:',
            input: 'number',
            inputAttributes: { step: 0.1, min: 0 },
            showCancelButton: true,
            confirmButtonText: 'Iniciar',
            cancelButtonText: 'Cancelar',
            inputValidator: (value) => {
                if (!value || value <= 0) return 'Debes ingresar un kilometraje válido';
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const kmInicio = parseFloat(result.value);
                Swal.fire({
                    title: 'Obteniendo ubicación...',
                    html: 'Por favor espera mientras obtenemos tu ubicación',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        obtenerDireccion(position.coords.latitude, position.coords.longitude)
                            .then(ubicacionTexto => {
                                enviarInicioEtapa(etapaId, kmInicio, position.coords.latitude, position.coords.longitude, ubicacionTexto);
                            });
                    },
                    function(error) {
                        Swal.fire('Error', 'No se pudo obtener tu ubicación: ' + error.message, 'error');
                    },
                    { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
                );
            }
        });
    }

    function completarEtapa(etapaId) {
        if (!navigator.geolocation) {
            Swal.fire('Error', 'Tu navegador no soporta geolocalización', 'error');
            return;
        }

        const etapaDiv = document.getElementById(`etapa-${etapaId}`);
        const kmInicioEl = etapaDiv.querySelector('.km-inicio');
        const kmInicio = kmInicioEl ? parseFloat(kmInicioEl.textContent.match(/[\d.]+/)[0]) : 0;

        Swal.fire({
            title: 'Completar Etapa',
            html: `
                <p>Kilometraje de inicio: <strong>${kmInicio} km</strong></p>
                <p>Ingresa el kilometraje actual del camión:</p>
                <input type="number" id="km-fin" class="swal2-input" step="0.1" min="${kmInicio}" required>
            `,
            showCancelButton: true,
            confirmButtonText: 'Completar',
            cancelButtonText: 'Cancelar',
            preConfirm: () => {
                const kmFin = document.getElementById('km-fin').value;
                if (!kmFin || kmFin <= 0) {
                    Swal.showValidationMessage('Debes ingresar un kilometraje válido');
                    return false;
                }
                if (parseFloat(kmFin) < kmInicio) {
                    Swal.showValidationMessage('El kilometraje final no puede ser menor al de inicio');
                    return false;
                }
                return parseFloat(kmFin);
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const kmFin = result.value;
                Swal.fire({
                    title: 'Obteniendo ubicación...',
                    html: 'Por favor espera mientras obtenemos tu ubicación',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        obtenerDireccion(position.coords.latitude, position.coords.longitude)
                            .then(ubicacionTexto => {
                                enviarFinEtapa(etapaId, kmFin, position.coords.latitude, position.coords.longitude, ubicacionTexto, kmInicio);
                            });
                    },
                    function(error) {
                        Swal.fire('Error', 'No se pudo obtener tu ubicación: ' + error.message, 'error');
                    }
                );
            }
        });
    }

    function obtenerDireccion(lat, lng) {
        return fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&accept-language=es`)
            .then(response => response.json())
            .then(data => data.display_name || 'Ubicación no disponible')
            .catch(() => 'Ubicación no disponible');
    }

    function enviarInicioEtapa(etapaId, kmInicio, lat, lng, ubicacionTexto) {
        fetch(`/publico/seguimiento/${token}/etapa/${etapaId}/estado`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                estado: 'en_curso',
                kilometraje_inicio: kmInicio,
                latitud: lat,
                longitud: lng,
                ubicacion_texto: ubicacionTexto
            })
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Etapa iniciada!',
                        text: 'Se ha guardado tu ubicación de inicio',
                        timer: 2000,
                        showConfirmButton: false
                    });
                    actualizarInfoViaje();
                    actualizarEstadoViaje(data.viaje_estado);
                    cargarPuntosEtapas(); // Recargar puntos para mostrar el nuevo marcador
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Error', 'No se pudo iniciar la etapa', 'error');
            });
    }

    function enviarFinEtapa(etapaId, kmFin, lat, lng, ubicacionTexto, kmInicio) {
        fetch(`/publico/seguimiento/${token}/etapa/${etapaId}/estado`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                estado: 'completado',
                kilometraje_real: kmFin,
                latitud: lat,
                longitud: lng,
                ubicacion_texto: ubicacionTexto
            })
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const kmRecorridos = kmFin - kmInicio;
                    Swal.fire({
                        icon: 'success',
                        title: '¡Etapa completada!',
                        text: `Recorriste ${kmRecorridos} km`,
                        timer: 2000,
                        showConfirmButton: false
                    });
                    actualizarInfoViaje();
                    actualizarEstadoViaje(data.viaje_estado);
                    cargarPuntosEtapas(); // Recargar puntos para mostrar el nuevo marcador
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Error', 'No se pudo completar la etapa', 'error');
            });
    }

    function actualizarInfoViaje() {
        fetch(`/publico/seguimiento/${token}/info`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const progresoText = document.getElementById('progreso-text');
                    const progresoBar = document.getElementById('progreso-bar');
                    if (progresoText) progresoText.textContent = data.viaje.progreso + '%';
                    if (progresoBar) progresoBar.style.width = data.viaje.progreso + '%';

                    data.etapas.forEach(etapa => {
                        const elemento = document.getElementById(`etapa-${etapa.id}`);
                        if (!elemento) return;

                        const botonesDiv = document.getElementById(`etapa-botones-${etapa.id}`);
                        if (botonesDiv) {
                            if (etapa.estado === 'pendiente') {
                                botonesDiv.innerHTML = `
                                    <button class="btn btn-sm btn-warning btn-etapa w-100" onclick="iniciarEtapa(${etapa.id})">
                                        <i class="fas fa-play me-1"></i>Iniciar
                                    </button>
                                `;
                            } else if (etapa.estado === 'en_curso') {
                                botonesDiv.innerHTML = `
                                    <span class="badge bg-warning text-dark d-block mb-2">En curso</span>
                                    <button class="btn btn-sm btn-success btn-etapa w-100" onclick="completarEtapa(${etapa.id})">
                                        <i class="fas fa-check me-1"></i>Completar
                                    </button>
                                `;
                            } else if (etapa.estado === 'completado') {
                                botonesDiv.innerHTML = `
                                    <span class="badge bg-success d-block mb-1">Completado</span>
                                    ${etapa.fecha_real_fin ? `<small class="text-muted d-block">${etapa.fecha_real_fin}</small>` : ''}
                                `;
                            }
                        }

                        elemento.className = `etapa-item ${etapa.estado}`;
                    });
                }
            })
            .catch(error => console.error('Error:', error));
    }

    function actualizarEstadoViaje(estado) {
        const estadoViajeEl = document.getElementById('estado-viaje');
        if (estadoViajeEl) {
            const clases = {
                'planeado': 'bg-secondary',
                'en_curso': 'bg-warning text-dark',
                'completado': 'bg-success',
                'cancelado': 'bg-danger'
            };
            estadoViajeEl.className = `badge ${clases[estado] || 'bg-secondary'} badge-estado`;
            estadoViajeEl.textContent = estado.charAt(0).toUpperCase() + estado.slice(1).replace('_', ' ');
        }
    }

    function cargarHistorial() {
        // Implementar si es necesario
        console.log('Historial no implementado');
    }
</script>
</body>
</html>
