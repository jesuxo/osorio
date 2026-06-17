@extends('layouts.master-auth')
@section('title') Iniciar Sesión - Grupo Osorio @endsection

@section('content')
    <div class="container-fluid p-0">
        <div class="row g-0 min-vh-100">
            <!-- Columna izquierda - Formulario -->
            <div class="col-lg-4 col-md-6 d-flex align-items-center justify-content-center bg-white">
                <div class="w-100" style="max-width: 380px; padding: 2rem;">
                    <!-- Logo -->
                    <div class="text-center mb-5">
                        <img src="{{ URL::asset('build/images/logo-dark.png') }}" alt="Grupo Osorio" height="60" class="mb-3">
                        <h2 class="fw-bold mb-1" style="color: #0c192c;">¡Bienvenido!</h2>
                        <p class="text-muted">Inicia sesión para continuar</p>
                    </div>

                    <!-- Mensajes de error -->
                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert" style="background: linear-gradient(135deg, #ef476f, #d13b5f); color: white;">
                            <div class="d-flex align-items-center">
                                <i class="ri-error-warning-line fs-4 me-2"></i>
                                <div>
                                    <strong>Error de autenticación</strong><br>
                                    {{ $errors->first() }}
                                </div>
                            </div>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <!-- Formulario -->
                    <form method="POST" action="{{ route('login') }}" class="needs-validation" novalidate>
                        @csrf

                        <!-- Campo Email -->
                        <div class="mb-4">
                            <label for="email" class="form-label fw-semibold text-secondary">Correo Electrónico</label>
                            <div class="input-group">
                            <span class="input-group-text bg-light border-end-0" style="border-radius: 10px 0 0 10px;">
                                <i class="ri-mail-line text-primary"></i>
                            </span>
                                <input type="email"
                                       class="form-control border-start-0 ps-0 @error('email') is-invalid @enderror"
                                       id="email"
                                       name="email"
                                       value="{{ old('email') }}"
                                       placeholder="correo@ejemplo.com"
                                       required
                                       autofocus
                                       style="border-radius: 0 10px 10px 0; padding-left: 10px !important">
                            </div>
                            @error('email')
                            <div class="invalid-feedback d-block">
                                <i class="ri-information-line me-1"></i>{{ $message }}
                            </div>
                            @enderror
                        </div>

                        <!-- Campo Contraseña -->
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label for="password" class="form-label fw-semibold text-secondary">Contraseña</label>

                            </div>
                            <div class="input-group">
                            <span class="input-group-text bg-light border-end-0" style="border-radius: 10px 0 0 10px;">
                                <i class="ri-lock-line text-primary"></i>
                            </span>
                                <input type="password"
                                       class="form-control border-start-0 ps-0 password-input @error('password') is-invalid @enderror"
                                       id="password"
                                       name="password"
                                       placeholder="••••••••"
                                       required
                                       style="border-radius: 0 10px 10px 0; padding-left:  10px !important">
                                <button class="btn btn-link position-absolute end-0 top-50 translate-middle-y text-muted password-addon"
                                        type="button"
                                        style="z-index: 10; text-decoration: none;"
                                        onclick="togglePassword()">
                                    <i class="ri-eye-line" id="togglePasswordIcon"></i>
                                </button>
                            </div>
                            @error('password')
                            <div class="invalid-feedback d-block">
                                <i class="ri-information-line me-1"></i>{{ $message }}
                            </div>
                            @enderror
                        </div>

                        <!-- Recordar sesión -->
                        <div class="mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }} style="border-color: #0072c5;">
                                <label class="form-check-label text-secondary" for="remember">
                                    Recordar mi sesión
                                </label>
                            </div>
                        </div>

                        <!-- Botón de inicio -->
                        <button type="submit" class="btn btn-primary w-100 py-3 mb-4 fw-semibold"
                                style="border-radius: 10px; background: linear-gradient(135deg, #0072c5, #0059a3); border: none; box-shadow: 0 10px 20px rgba(0, 114, 197, 0.2);">
                        <span class="d-flex align-items-center justify-content-center">
                            <i class="ri-login-circle-line me-2 fs-5"></i>
                            Iniciar Sesión
                        </span>
                        </button>

                        <!-- Separador -->
                        <div class="position-relative text-center mb-4">
                            <hr class="text-muted opacity-25">
                            <span class="position-absolute top-50 start-50 translate-middle bg-white px-3 text-muted small">o</span>
                        </div>

                    </form>

                    <!-- Footer -->
                    <div class="text-center mt-5">
                        <p class="small text-muted mb-0">
                            <i class="ri-copyright-line align-middle me-1"></i>
                            {{ date('Y') }} Grupo Osorio. Todos los derechos reservados.
                        </p>
                        <p class="small text-muted">
                            Desarrollado por <a href="https://CelisWeb.com.ve" target="_blank" class="text-primary text-decoration-none">CelisWeb</a>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Columna derecha - Hero/Branding -->
            <div class="col-lg-8 col-md-6 d-none d-md-block" style="background: linear-gradient(135deg, #2f4b9a 0%, #458cca 100%);">
                <div class="h-100 d-flex align-items-center justify-content-center p-5">
                    <div class="text-center text-white" style="max-width: 600px;">

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Script para funcionalidades -->
    <script>
        // Toggle password visibility
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const icon = document.getElementById('togglePasswordIcon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('ri-eye-line');
                icon.classList.add('ri-eye-off-line');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('ri-eye-off-line');
                icon.classList.add('ri-eye-line');
            }
        }

        // Fill demo credentials
        function fillDemoCredentials() {
            document.getElementById('email').value = 'demo@osoriogroup.com.ve';
            document.getElementById('password').value = 'Demo123';

            // Animación simple
            const btn = event.target;
            btn.innerHTML = '<i class="ri-check-line me-2"></i>Credenciales cargadas';
            btn.disabled = true;

            setTimeout(() => {
                btn.innerHTML = '<i class="ri-user-star-line me-2"></i>Usuario de demostración';
                btn.disabled = false;
            }, 2000);
        }

        // Validación del formulario
        (function() {
            'use strict';

            const forms = document.querySelectorAll('.needs-validation');

            Array.from(forms).forEach(form => {
                form.addEventListener('submit', event => {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }

                    form.classList.add('was-validated');
                }, false);
            });
        })();

        // Efecto de hover en botones
        document.querySelectorAll('.btn-outline-secondary').forEach(btn => {
            btn.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-2px)';
                this.style.transition = 'all 0.3s ease';
            });

            btn.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });
    </script>

    <!-- Estilos adicionales -->
    <style>
        /* Animaciones */
        .btn-primary {
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 30px rgba(0, 114, 197, 0.3) !important;
        }

        .form-control {
            transition: all 0.3s ease;
        }

        .form-control:focus {
            box-shadow: 0 0 0 3px rgba(0, 114, 197, 0.1);
            border-color: #0072c5;
        }

        .input-group-text {
            transition: all 0.3s ease;
        }

        .form-control:focus + .input-group-text {
            border-color: #0072c5;
        }

        /* Loading animation */
        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .btn-primary:active {
            transform: scale(0.98);
        }

        /* Responsive */
        @media (max-width: 767.98px) {
            .col-lg-4 {
                padding: 2rem 1rem;
            }
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        ::-webkit-scrollbar-thumb {
            background: #0072c5;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #0059a3;
        }

        /* Glassmorphism effects */
        .rounded-3 {
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
        }

        .rounded-3:hover {
            transform: translateY(-5px);
            background: rgba(255, 255, 255, 0.1) !important;
        }

        /* Checkbox personalizado */
        .form-check-input:checked {
            background-color: #0072c5;
            border-color: #0072c5;
        }

        .form-check-input:focus {
            box-shadow: 0 0 0 3px rgba(0, 114, 197, 0.25);
        }

        /* Alert personalizado */
        .alert-danger {
            border: none;
            position: relative;
            overflow: hidden;
        }

        .alert-danger::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: rgba(255, 255, 255, 0.3);
        }

        /* Fade-in animation */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .bg-white, [class*="col-"] {
            animation: fadeIn 0.6s ease-out;
        }
    </style>
@endsection

@section('scripts')
    <script src="{{ URL::asset('build/js/pages/password-addon.init.js') }}"></script>
@endsection
