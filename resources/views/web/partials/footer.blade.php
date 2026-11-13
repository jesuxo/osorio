{{-- resources/views/partials/footer.blade.php --}}

<footer class="footer-pro">
    <div class="container">

        {{-- ============================================================ --}}
        {{-- BLOQUE PRINCIPAL: 4 COLUMNAS --}}
        {{-- ============================================================ --}}
        <div class="row g-4 py-5">

            {{-- COLUMNA 1: MARCA + CLAIM + REDES --}}
            <div class="col-lg-4 col-md-6">
                <a href="{{ route('web.home') }}" class="d-inline-block mb-3">
                    <img src="{{ URL::asset('build/images/logolateral.png') }}"
                         alt="Osorio Group"
                         height="42"
                         style="filter: brightness(0) invert(1);">
                </a>

                <p class="footer-desc mb-3">
                    Concesionario, distribuidor e importador de motos y repuestos
                    en Táchira. 11 tiendas en La Fría, San Cristóbal y San Antonio.
                    Envíos a toda Venezuela.
                </p>

                <div class="footer-social d-flex gap-2">
                    <a href="https://instagram.com/osoriogroup"
                       target="_blank" rel="noopener"
                       class="footer-social-link"
                       aria-label="Instagram">
                        <i class="bi bi-instagram"></i>
                    </a>
                    <a href="https://instagram.com/cfmoto_tachira"
                       target="_blank" rel="noopener"
                       class="footer-social-link"
                       aria-label="Instagram CFMOTO">
                        <i class="bi bi-camera"></i>
                    </a>
                    <a href="https://wa.me/584120675809"
                       target="_blank" rel="noopener"
                       class="footer-social-link footer-social-wsp"
                       aria-label="WhatsApp">
                        <i class="bi bi-whatsapp"></i>
                    </a>
                    <a href="https://facebook.com/osoriogroup"
                       target="_blank" rel="noopener"
                       class="footer-social-link"
                       aria-label="Facebook">
                        <i class="bi bi-facebook"></i>
                    </a>
                </div>
            </div>

            {{-- COLUMNA 2: TIENDAS --}}
            <div class="col-lg-2 col-md-6 col-6">
                <h6 class="footer-titulo">Tiendas</h6>
                <ul class="footer-lista">
                    <li><a href="{{ route('web.home') }}#sucursales">La Fría <span class="footer-count">3</span></a></li>
                    <li><a href="{{ route('web.home') }}#sucursales">San Cristóbal <span class="footer-count">4</span></a></li>
                    <li><a href="{{ route('web.home') }}#sucursales">San Antonio <span class="footer-count">4</span></a></li>
                    <li><a href="{{ route('web.home') }}#sucursales" class="footer-ver-todas">
                            Ver todas <i class="bi bi-arrow-right"></i>
                        </a></li>
                </ul>
            </div>

            {{-- COLUMNA 3: CATÁLOGO --}}
            <div class="col-lg-3 col-md-6 col-6">
                <h6 class="footer-titulo">Catálogo</h6>
                <ul class="footer-lista">
                    <li><a href="{{ route('web.motos') }}">Motos</a></li>
                    <li><a href="{{ route('web.catalogo', ['familia' => '45']) }}">Repuestos</a></li>
                    <li><a href="{{ route('web.catalogo', ['familia' => '03.']) }}">Lubricantes</a></li>
                    <li><a href="{{ route('web.catalogo', ['familia' => '02.']) }}">Auto Partes</a></li>
                    <li><a href="{{ route('web.catalogo', ['familia' => '09.']) }}">Boutique</a></li>
                    <li><a href="{{ route('web.catalogo', ['familia' => '05.']) }}">Ciclismo</a></li>
                </ul>
            </div>

            {{-- COLUMNA 4: CONTACTO --}}
            <div class="col-lg-3 col-md-6">
                <h6 class="footer-titulo">Contacto</h6>
                <ul class="footer-lista footer-contacto">
                    <li>
                        <i class="bi bi-whatsapp"></i>
                        <a href="https://wa.me/584120675809" target="_blank" rel="noopener">
                            +58 412-0675809
                        </a>
                    </li>
                    <li>
                        <i class="bi bi-telephone"></i>
                        <a href="tel:+584121717186">
                            +58 412-1717186
                        </a>
                    </li>
                    <li>
                        <i class="bi bi-envelope"></i>
                        <a href="mailto:ventas@osoriogroup.com">
                            ventas@osoriogroup.com
                        </a>
                    </li>
                    <li>
                        <i class="bi bi-geo-alt"></i>
                        <span>San Cristóbal, Táchira</span>
                    </li>
                    <li>
                        <i class="bi bi-clock"></i>
                        <span>Lun–vie 8:30–17:00 · Sáb 9:00–14:00</span>
                    </li>
                </ul>

                {{-- Botón destacado --}}
                <a href="https://wa.me/584120675809?text={{ urlencode('Hola, quiero información sobre las motos de Osorio Group.') }}"
                   target="_blank" rel="noopener"
                   class="btn btn-cf btn-sm w-100 mt-3">
                    <i class="bi bi-whatsapp me-1"></i> Hablar con un asesor
                </a>
            </div>
        </div>

        {{-- ============================================================ --}}
        {{-- BLOQUE DE CONFIANZA --}}
        {{-- ============================================================ --}}
        <div class="footer-trust py-3 border-top">
            <div class="row g-3 text-center">
                <div class="col-md-3 col-6">
                    <div class="footer-trust-item">
                        <i class="bi bi-shop"></i>
                        <div>
                            <strong>11 tiendas</strong>
                            <small>en Táchira</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="footer-trust-item">
                        <i class="bi bi-truck"></i>
                        <div>
                            <strong>Envíos nacionales</strong>
                            <small>a toda Venezuela</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="footer-trust-item">
                        <i class="bi bi-shield-check"></i>
                        <div>
                            <strong>100% originales</strong>
                            <small>repuestos garantizados</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="footer-trust-item">
                        <i class="bi bi-award"></i>
                        <div>
                            <strong>+20 años</strong>
                            <small>de trayectoria</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ============================================================ --}}
        {{-- BARRA INFERIOR --}}
        {{-- ============================================================ --}}
        <div class="footer-bottom py-3 border-top">
            <div class="row align-items-center g-3">
                <div class="col-md-6 text-md-start text-center">
                    <small class="footer-legal">
                        &copy; {{ date('Y') }} Osorio Group · RIF J-XXXXXXXX-X ·
                        Todos los derechos reservados.
                    </small>
                </div>
                <div class="col-md-6 text-md-end text-center">
                    <ul class="footer-legal-links d-inline-flex gap-3 mb-0 list-unstyled">
                        <li><a href="#">Términos y condiciones</a></li>
                        <li><a href="#">Política de privacidad</a></li>
                        <li><a href="#">Preguntas frecuentes</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</footer>
