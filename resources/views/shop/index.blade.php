@extends('layouts.app')

@section('title', 'Osorio Group - Líder en Motos y Repuestos')

@section('styles')
    <style>
        /* ============================================================
           ESTILOS PREMIUM - OSORIO GROUP
           ============================================================ */

        /* Variables de marca */
        :root {
            --osorio-black: #0a0a0f;
            --osorio-dark: #12121a;
            --osorio-gray: #1a1a2e;
            --osorio-gold: #d4a843;
            --osorio-gold-light: #f0d078;
            --osorio-gold-gradient: linear-gradient(135deg, #d4a843, #f0d078, #d4a843);
            --osorio-blue: #0a3d6b;
            --osorio-blue-light: #1a5a8a;
        }

        /* ============================================================
           HERO IMPONENTE
           ============================================================ */
        .hero-premium {
            background: linear-gradient(135deg, var(--osorio-black) 0%, var(--osorio-dark) 50%, var(--osorio-blue) 100%);
            padding: 100px 0 80px;
            position: relative;
            overflow: hidden;
            min-height: 600px;
            display: flex;
            align-items: center;
        }

        .hero-premium::before {
            content: '';
            position: absolute;
            top: -40%;
            right: -15%;
            width: 700px;
            height: 700px;
            background: radial-gradient(circle, rgba(212, 168, 67, 0.12) 0%, transparent 70%);
            border-radius: 50%;
            animation: heroGlow 8s ease-in-out infinite alternate;
        }

        .hero-premium::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: -10%;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(10, 61, 107, 0.3) 0%, transparent 70%);
            border-radius: 50%;
        }

        @keyframes heroGlow {
            0% { transform: scale(1) translateX(0); opacity: 0.5; }
            100% { transform: scale(1.3) translateX(-50px); opacity: 1; }
        }

        .hero-premium .hero-content {
            position: relative;
            z-index: 2;
        }

        .hero-premium .badge-premium {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(212, 168, 67, 0.15);
            border: 1px solid rgba(212, 168, 67, 0.3);
            padding: 6px 16px;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--osorio-gold);
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-bottom: 20px;
            backdrop-filter: blur(10px);
        }

        .hero-premium .badge-premium .pulse-dot {
            width: 8px;
            height: 8px;
            background: #22c55e;
            border-radius: 50%;
            display: inline-block;
            animation: pulse-dot 2s ease-in-out infinite;
        }

        @keyframes pulse-dot {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.5; transform: scale(0.8); }
        }

        .hero-premium h1 {
            font-weight: 900;
            font-size: 4rem;
            line-height: 1.05;
            color: white;
            letter-spacing: -0.02em;
        }

        .hero-premium h1 .highlight-gold {
            background: var(--osorio-gold-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hero-premium .subtitle-premium {
            color: rgba(255,255,255,0.7);
            font-size: 1.2rem;
            max-width: 520px;
            margin-top: 16px;
            line-height: 1.8;
        }

        .hero-premium .subtitle-premium strong {
            color: white;
        }

        .hero-premium .hero-stats {
            display: flex;
            gap: 50px;
            margin-top: 35px;
            padding: 20px 0;
            border-top: 1px solid rgba(255,255,255,0.06);
        }

        .hero-premium .hero-stats .stat-item {
            color: white;
        }

        .hero-premium .hero-stats .stat-number {
            font-size: 2.2rem;
            font-weight: 800;
            background: var(--osorio-gold-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 1;
        }

        .hero-premium .hero-stats .stat-label {
            font-size: 0.85rem;
            color: rgba(255,255,255,0.5);
            font-weight: 500;
            margin-top: 2px;
        }

        .hero-premium .hero-image {
            position: relative;
            z-index: 2;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .hero-premium .hero-image img {
            max-height: 400px;
            filter: drop-shadow(0 30px 60px rgba(0,0,0,0.5));
            animation: floatMoto 6s ease-in-out infinite;
        }

        .hero-premium .hero-image .floating-badge {
            position: absolute;
            background: rgba(255,255,255,0.05);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.08);
            padding: 12px 20px;
            border-radius: 16px;
            color: white;
            font-size: 0.8rem;
            display: flex;
            align-items: center;
            gap: 12px;
            animation: floatBadge 8s ease-in-out infinite;
        }

        .hero-premium .hero-image .floating-badge:nth-child(2) {
            top: 10%;
            right: -10%;
            animation-delay: 2s;
        }

        .hero-premium .hero-image .floating-badge:nth-child(3) {
            bottom: 15%;
            left: -15%;
            animation-delay: 4s;
        }

        .hero-premium .hero-image .floating-badge .icon {
            font-size: 1.5rem;
        }

        @keyframes floatMoto {
            0%, 100% { transform: translateY(0px) rotate(-2deg); }
            50% { transform: translateY(-20px) rotate(2deg); }
        }

        @keyframes floatBadge {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }

        @media (max-width: 768px) {
            .hero-premium {
                padding: 60px 0 50px;
                min-height: 450px;
            }
            .hero-premium h1 {
                font-size: 2.5rem;
            }
            .hero-premium .subtitle-premium {
                font-size: 1rem;
            }
            .hero-premium .hero-stats {
                gap: 25px;
                flex-wrap: wrap;
            }
            .hero-premium .hero-stats .stat-number {
                font-size: 1.6rem;
            }
            .hero-premium .hero-image img {
                max-height: 200px;
            }
            .hero-premium .hero-image .floating-badge {
                display: none;
            }
        }

        /* ============================================================
           BOTONES PREMIUM
           ============================================================ */
        .btn-gold {
            background: var(--osorio-gold-gradient);
            border: none;
            color: var(--osorio-black);
            padding: 14px 36px;
            border-radius: 50px;
            font-weight: 700;
            font-size: 1rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 20px rgba(212, 168, 67, 0.3);
            position: relative;
            overflow: hidden;
        }

        .btn-gold:hover {
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 8px 40px rgba(212, 168, 67, 0.5);
            color: var(--osorio-black);
        }

        .btn-gold::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, transparent, rgba(255,255,255,0.1), transparent);
            transform: rotate(45deg) translateX(-100%);
            transition: all 0.6s ease;
        }

        .btn-gold:hover::after {
            transform: rotate(45deg) translateX(100%);
        }

        .btn-outline-gold {
            background: transparent;
            border: 2px solid var(--osorio-gold);
            color: var(--osorio-gold);
            padding: 12px 32px;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-outline-gold:hover {
            background: var(--osorio-gold);
            color: var(--osorio-black);
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(212, 168, 67, 0.2);
        }

        /* ============================================================
           SECCIÓN "POR QUÉ SOMOS LÍDERES"
           ============================================================ */
        .section-leader {
            padding: 70px 0 60px;
            background: white;
        }

        .section-leader .section-badge {
            display: inline-block;
            background: rgba(212, 168, 67, 0.1);
            color: var(--osorio-gold);
            padding: 4px 16px;
            border-radius: 50px;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-bottom: 10px;
        }

        .section-leader h2 {
            font-weight: 800;
            font-size: 2.5rem;
            margin-bottom: 10px;
        }

        .section-leader h2 .highlight {
            color: var(--osorio-gold);
        }

        .section-leader .subtitle {
            color: var(--text-muted);
            font-size: 1.1rem;
            max-width: 600px;
        }

        .leader-card {
            background: white;
            border-radius: 16px;
            padding: 30px 24px;
            border: 1px solid rgba(0,0,0,0.04);
            transition: all 0.4s ease;
            position: relative;
            overflow: hidden;
            height: 100%;
        }

        .leader-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 0;
            background: var(--osorio-gold-gradient);
            transition: height 0.4s ease;
        }

        .leader-card:hover::before {
            height: 100%;
        }

        .leader-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 60px rgba(0,0,0,0.06);
        }

        .leader-card .icon-wrapper {
            width: 60px;
            height: 60px;
            border-radius: 14px;
            background: rgba(212, 168, 67, 0.08);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            margin-bottom: 16px;
            color: var(--osorio-gold);
            transition: all 0.3s ease;
        }

        .leader-card:hover .icon-wrapper {
            background: var(--osorio-gold);
            color: white;
            transform: scale(1.05);
        }

        .leader-card h5 {
            font-weight: 700;
            font-size: 1.1rem;
            margin-bottom: 8px;
        }

        .leader-card p {
            color: var(--text-muted);
            font-size: 0.95rem;
            line-height: 1.7;
            margin-bottom: 0;
        }

        /* ============================================================
           SECCIÓN DE CATEGORÍAS DESTACADAS
           ============================================================ */
        .category-premium-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 16px;
            margin: 30px 0;
        }

        .category-premium-card {
            background: white;
            border-radius: 16px;
            padding: 28px 16px;
            text-align: center;
            border: 1px solid rgba(0,0,0,0.04);
            transition: all 0.4s ease;
            cursor: pointer;
            text-decoration: none;
            color: var(--text-dark);
            position: relative;
            overflow: hidden;
        }

        .category-premium-card::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 3px;
            background: var(--osorio-gold-gradient);
            transform: scaleX(0);
            transition: transform 0.4s ease;
        }

        .category-premium-card:hover::after {
            transform: scaleX(1);
        }

        .category-premium-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 12px 40px rgba(0,0,0,0.06);
        }

        .category-premium-card .category-icon {
            font-size: 2.8rem;
            display: block;
            margin-bottom: 8px;
        }

        .category-premium-card .category-name {
            font-weight: 700;
            font-size: 0.95rem;
        }

        .category-premium-card .category-count {
            font-size: 0.75rem;
            color: var(--text-muted);
        }

        .category-premium-card.active {
            border-color: var(--osorio-gold);
            background: rgba(212, 168, 67, 0.04);
        }

        /* ============================================================
           PRODUCTOS DESTACADOS - ESTILO PREMIUM
           ============================================================ */
        .product-card-premium {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            transition: all 0.4s ease;
            border: 1px solid rgba(0,0,0,0.04);
            height: 100%;
            position: relative;
        }

        .product-card-premium:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 60px rgba(0,0,0,0.08);
            border-color: rgba(212, 168, 67, 0.2);
        }

        .product-card-premium .product-image {
            background: #f8fafc;
            height: 180px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
        }

        .product-card-premium .product-image img {
            max-height: 100%;
            max-width: 100%;
            object-fit: contain;
            transition: all 0.5s ease;
        }

        .product-card-premium:hover .product-image img {
            transform: scale(1.05);
        }

        .product-card-premium .product-badge {
            position: absolute;
            top: 12px;
            left: 12px;
            padding: 4px 14px;
            border-radius: 50px;
            font-size: 0.65rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .product-card-premium .product-badge.stock {
            background: #22c55e;
            color: white;
        }

        .product-card-premium .product-badge.out-of-stock {
            background: #ef4444;
            color: white;
        }

        .product-card-premium .product-badge.hot {
            background: var(--osorio-gold);
            color: var(--osorio-black);
            right: 12px;
            left: auto;
        }

        .product-card-premium .product-body {
            padding: 16px 18px 18px;
        }

        .product-card-premium .product-category {
            font-size: 0.7rem;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-weight: 600;
        }

        .product-card-premium .product-title {
            font-weight: 600;
            font-size: 0.95rem;
            margin: 4px 0 6px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            min-height: 44px;
        }

        .product-card-premium .product-title a {
            color: var(--text-dark);
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .product-card-premium .product-title a:hover {
            color: var(--osorio-gold);
        }

        .product-card-premium .product-price {
            font-size: 1.2rem;
            font-weight: 800;
            color: var(--osorio-gold);
        }

        .product-card-premium .product-stock-info {
            font-size: 0.75rem;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .product-card-premium .product-stock-info .in-stock {
            color: #22c55e;
        }

        .product-card-premium .product-stock-info .out-of-stock {
            color: #ef4444;
        }

        .product-card-premium .product-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px solid #f1f3f5;
        }

        .product-card-premium .product-footer .sucursales {
            font-size: 0.65rem;
            color: var(--text-muted);
        }

        .product-card-premium .product-footer .sucursales .badge-sucursal {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 50px;
            background: rgba(212, 168, 67, 0.08);
            color: var(--osorio-gold);
            font-size: 0.6rem;
            font-weight: 600;
            margin: 2px 2px 0 0;
        }

        /* ============================================================
           SECCIÓN IA 24/7
           ============================================================ */
        .section-ia {
            background: linear-gradient(135deg, var(--osorio-black), var(--osorio-dark));
            padding: 70px 0;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .section-ia::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(212, 168, 67, 0.05) 0%, transparent 70%);
            border-radius: 50%;
        }

        .section-ia .ia-content {
            position: relative;
            z-index: 1;
        }

        .section-ia .ia-badge {
            display: inline-block;
            background: rgba(212, 168, 67, 0.12);
            border: 1px solid rgba(212, 168, 67, 0.2);
            padding: 4px 16px;
            border-radius: 50px;
            font-size: 0.7rem;
            font-weight: 600;
            color: var(--osorio-gold);
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-bottom: 12px;
        }

        .section-ia h2 {
            font-weight: 800;
            font-size: 2.5rem;
            margin-bottom: 12px;
        }

        .section-ia h2 .highlight {
            color: var(--osorio-gold);
        }

        .section-ia .subtitle-ia {
            color: rgba(255,255,255,0.6);
            font-size: 1.05rem;
            max-width: 500px;
            line-height: 1.8;
        }

        .section-ia .ia-features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }

        .section-ia .ia-feature {
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.06);
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            transition: all 0.3s ease;
        }

        .section-ia .ia-feature:hover {
            background: rgba(255,255,255,0.06);
            transform: translateY(-4px);
        }

        .section-ia .ia-feature .feature-icon {
            font-size: 2rem;
            display: block;
            margin-bottom: 8px;
        }

        .section-ia .ia-feature .feature-title {
            font-weight: 600;
            font-size: 0.9rem;
        }

        .section-ia .ia-feature .feature-desc {
            font-size: 0.8rem;
            color: rgba(255,255,255,0.4);
            margin-top: 2px;
        }

        .section-ia .ia-avatar {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-top: 30px;
            padding: 20px 24px;
            background: rgba(255,255,255,0.03);
            border-radius: 16px;
            border: 1px solid rgba(255,255,255,0.06);
        }

        .section-ia .ia-avatar .avatar-img {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            background: var(--osorio-gold-gradient);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--osorio-black);
            flex-shrink: 0;
        }

        .section-ia .ia-avatar .avatar-info {
            flex: 1;
        }

        .section-ia .ia-avatar .avatar-info .name {
            font-weight: 600;
            font-size: 1rem;
        }

        .section-ia .ia-avatar .avatar-info .status {
            font-size: 0.85rem;
            color: rgba(255,255,255,0.5);
        }

        .section-ia .ia-avatar .avatar-info .status .online-dot {
            display: inline-block;
            width: 8px;
            height: 8px;
            background: #22c55e;
            border-radius: 50%;
            margin-right: 6px;
            animation: pulse-dot 2s ease-in-out infinite;
        }

        /* ============================================================
           SUCURSALES PREMIUM
           ============================================================ */
        .sucursal-premium-card {
            background: white;
            border-radius: 16px;
            padding: 18px 20px;
            border: 1px solid rgba(0,0,0,0.04);
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 16px;
            height: 100%;
        }

        .sucursal-premium-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 40px rgba(0,0,0,0.06);
            border-color: rgba(212, 168, 67, 0.2);
        }

        .sucursal-premium-card .suc-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            background: rgba(212, 168, 67, 0.08);
            color: var(--osorio-gold);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            flex-shrink: 0;
        }

        .sucursal-premium-card .suc-info h6 {
            font-weight: 600;
            font-size: 0.95rem;
            margin-bottom: 2px;
        }

        .sucursal-premium-card .suc-info small {
            color: var(--text-muted);
            font-size: 0.8rem;
        }

        /* ============================================================
           RESPONSIVE
           ============================================================ */
        @media (max-width: 768px) {
            .section-leader h2 {
                font-size: 1.8rem;
            }
            .section-ia h2 {
                font-size: 1.8rem;
            }
            .category-premium-grid {
                grid-template-columns: repeat(3, 1fr);
                gap: 10px;
            }
            .category-premium-card {
                padding: 16px 10px;
            }
            .category-premium-card .category-icon {
                font-size: 2rem;
            }
            .category-premium-card .category-name {
                font-size: 0.75rem;
            }
            .product-card-premium .product-image {
                height: 140px;
                padding: 12px;
            }
            .section-ia .ia-features {
                grid-template-columns: repeat(2, 1fr);
            }
            .section-ia .ia-avatar {
                flex-direction: column;
                text-align: center;
            }
        }

        @media (max-width: 576px) {
            .category-premium-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            .product-card-premium .product-title {
                font-size: 0.8rem;
                min-height: 36px;
            }
            .product-card-premium .product-price {
                font-size: 1rem;
            }
            .hero-premium h1 {
                font-size: 2rem;
            }
            .hero-premium .hero-stats .stat-number {
                font-size: 1.3rem;
            }
            .section-leader h2 {
                font-size: 1.5rem;
            }
            .section-ia h2 {
                font-size: 1.5rem;
            }
        }
    </style>

    @yield('styles-extra')
@endsection

@section('content')
    <!-- ============================================================
HERO PREMIUM
============================================================ -->
    <section class="hero-premium" id="tour-hero">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 hero-content">
                    <div class="badge-premium">
                        <span class="pulse-dot"></span>
                        Líder en el mercado desde 1998
                    </div>
                    <h1>
                        Motos y Repuestos<br>
                        <span class="highlight-gold">De Calidad Premium</span>
                    </h1>
                    <p class="subtitle-premium">
                        <strong>Osorio Group</strong> es la empresa líder en venta de motos,
                        repuestos y lubricantes. Con <strong>años de experiencia</strong>
                        y el respaldo de las mejores marcas.
                    </p>

                    <div class="hero-stats">
                        <div class="stat-item">
                            <div class="stat-number">+{{ number_format($stats['total_productos'] ?? 0) }}</div>
                            <div class="stat-label">Productos Disponibles</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number">{{ $stats['total_sucursales'] ?? 0 }}</div>
                            <div class="stat-label">Sucursales</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number">24/7</div>
                            <div class="stat-label">Asistencia IA</div>
                        </div>
                    </div>

                    <div class="d-flex flex-wrap gap-3 mt-3">
                        <a href="#productos" class="btn-gold">
                            <i class="bi bi-grid me-2"></i>Ver Catálogo
                        </a>
                        <button class="btn-outline-gold" onclick="startTour()">
                            <i class="bi bi-compass me-2"></i>Tour Guiado
                        </button>
                    </div>
                </div>

                <div class="col-lg-6 hero-image d-none d-lg-block">
                    <img src="{{ URL::asset('build/images/hero-moto.png') }}" alt="Osorio Group Moto" class="img-fluid">
                    <div class="floating-badge" style="top: 5%; right: -5%;">
                        <span class="icon">🏍️</span>
                        <div>
                            <strong>+50 Modelos</strong>
                            <div style="font-size:0.7rem; opacity:0.7;">De las mejores marcas</div>
                        </div>
                    </div>
                    <div class="floating-badge" style="bottom: 10%; left: -10%;">
                        <span class="icon">⭐</span>
                        <div>
                            <strong>4.9/5</strong>
                            <div style="font-size:0.7rem; opacity:0.7;">Calificación promedio</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================================================
    POR QUÉ SOMOS LÍDERES
    ============================================================ -->
    <section class="section-leader" id="tour-leader">
        <div class="container">
            <div class="text-center mb-5">
                <span class="section-badge">🌟 Excelencia</span>
                <h2>¿Por qué somos <span class="highlight">líderes</span>?</h2>
                <p class="subtitle mx-auto">Calidad, experiencia y compromiso con nuestros clientes nos distinguen en el mercado.</p>
            </div>

            <div class="row g-4">
                <div class="col-md-3">
                    <div class="leader-card">
                        <div class="icon-wrapper">🏆</div>
                        <h5>Años de Experiencia</h5>
                        <p>Más de 25 años en el mercado automotriz, construyendo confianza y calidad.</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="leader-card">
                        <div class="icon-wrapper">🛡️</div>
                        <h5>Calidad Garantizada</h5>
                        <p>Trabajamos con las mejores marcas y productos certificados.</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="leader-card">
                        <div class="icon-wrapper">📍</div>
                        <h5>Cobertura Nacional</h5>
                        <p>{{ $stats['total_sucursales'] ?? 0 }} sucursales estratégicamente ubicadas para servirte mejor.</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="leader-card">
                        <div class="icon-wrapper">🤖</div>
                        <h5>IA 24/7</h5>
                        <p>Asistencia inteligente disponible las 24 horas para resolver tus dudas.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================================================
    CATEGORÍAS DESTACADAS
    ============================================================ -->
    <div class="container" id="tour-categorias">
        <div class="d-flex justify-content-between align-items-center mt-4">
            <div>
                <span class="section-badge" style="display:inline-block; background:rgba(212,168,67,0.1); color:var(--osorio-gold); padding:4px 16px; border-radius:50px; font-size:0.7rem; font-weight:700; text-transform:uppercase; letter-spacing:0.08em;">Categorías</span>
                <h4 class="fw-bold mt-1">Explora nuestro <span style="color:var(--osorio-gold);">catálogo</span></h4>
            </div>
        </div>
        <div class="category-premium-grid">
            <a href="{{ route('shop.index') }}" class="category-premium-card {{ !request('categoria') ? 'active' : '' }}">
                <span class="category-icon">🏍️</span>
                <div class="category-name">Todos</div>
                <div class="category-count">{{ $stats['total_productos'] ?? 0 }} productos</div>
            </a>
            @foreach($categorias->take(7) as $cat)
                <a href="{{ route('shop.category', $cat->codinst) }}"
                   class="category-premium-card {{ request('categoria') == $cat->codinst ? 'active' : '' }}">
                <span class="category-icon">
                    @if(str_contains(strtolower($cat->descrip), 'moto'))
                        🏍️
                    @elseif(str_contains(strtolower($cat->descrip), 'repuesto'))
                        🔧
                    @elseif(str_contains(strtolower($cat->descrip), 'lubricante'))
                        🛢️
                    @elseif(str_contains(strtolower($cat->descrip), 'accesorio'))
                        🎒
                    @else
                        📦
                    @endif
                </span>
                    <div class="category-name">{{ \Illuminate\Support\Str::limit($cat->descrip, 18) }}</div>
                    <div class="category-count">{{ $cat->productos->count() }} productos</div>
                </a>
            @endforeach
        </div>
    </div>

    <!-- ============================================================
    PRODUCTOS DESTACADOS
    ============================================================ -->
    @if($productosDestacados->count() > 0)
        <section class="container" id="tour-destacados">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <span class="section-badge" style="display:inline-block; background:rgba(212,168,67,0.1); color:var(--osorio-gold); padding:4px 16px; border-radius:50px; font-size:0.7rem; font-weight:700; text-transform:uppercase; letter-spacing:0.08em;">🔥 Destacados</span>
                    <h4 class="fw-bold mt-1">Productos <span style="color:var(--osorio-gold);">más populares</span></h4>
                </div>
                <a href="#productos" class="text-decoration-none" style="color:var(--osorio-gold); font-weight:600; font-size:0.9rem;">
                    Ver todos <i class="bi bi-arrow-right"></i>
                </a>
            </div>
            <div class="row g-3">
                @foreach($productosDestacados as $producto)
                    @php
                        $totalStock = $producto->existencias_por_sucursal->sum('existen');
                    @endphp
                    @if($totalStock > 0)
                        <div class="col-6 col-md-3 col-lg-2">
                            <div class="product-card-premium">
                                <div class="product-image">
                                    <img src="{{ asset('build/images/noimagen.jpg') }}" alt="{{ $producto->descrip }}">
                                    <span class="product-badge stock"><i class="bi bi-check-circle-fill me-1"></i>Stock</span>
                                    @if($loop->index < 3)
                                        <span class="product-badge hot">🔥 Popular</span>
                                    @endif
                                </div>
                                <div class="product-body">
                                    <div class="product-category">{{ $producto->instancia->descrip ?? 'General' }}</div>
                                    <div class="product-title">
                                        <a href="{{ route('shop.product', $producto->codprod) }}">{{ $producto->descrip }}</a>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="product-price">${{ number_format($producto->costod3 ?? $producto->preciod ?? 0, 2) }}</span>
                                        <span class="product-stock-info">
                                <span class="in-stock"><i class="bi bi-box me-1"></i>{{ $totalStock }}</span>
                            </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </section>
    @endif

    <!-- ============================================================
LISTADO DE PRODUCTOS (con búsqueda)
============================================================ -->
    <section id="productos" class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4" id="tour-productos">
            <div>
                <h2 class="fw-bold" style="font-size:1.8rem;">
                    <span style="color:var(--osorio-gold);">📦</span> Catálogo Completo
                </h2>
                @php
                    $totalProductos = isset($productos) ? ($productos instanceof \Illuminate\Pagination\LengthAwarePaginator ? $productos->total() : $productos->count()) : 0;
                @endphp
                <span class="text-muted" style="font-size:0.9rem;">{{ $totalProductos }} productos disponibles</span>
            </div>
        </div>

        <!-- Barra de búsqueda premium -->
        <div class="search-wrapper mb-4" style="max-width: 100%; margin: 0 0 20px 0;">
            <form action="{{ route('shop.index') }}" method="GET" class="w-100">
                <div class="input-group">
                <span class="input-group-text bg-white border-end-0" style="border-radius: 50px 0 0 50px; padding-left: 18px;">
                    <i class="bi bi-search" style="color: var(--osorio-gold);"></i>
                </span>
                    <input type="text"
                           class="form-control border-start-0"
                           name="q"
                           placeholder="Buscar por nombre, código, marca..."
                           value="{{ request('q') }}"
                           style="border-radius: 0; padding: 14px 20px; border-color: #e9ecef;">
                    <button class="btn-gold" type="submit" style="border-radius: 0 50px 50px 0; padding: 12px 28px;">
                        Buscar
                    </button>
                </div>
            </form>
        </div>

        <!-- Resultados -->
        @if(request()->has('q') && request()->q)
            <div class="d-flex justify-content-between align-items-center mb-3">
            <span class="text-muted">
                <strong>{{ $totalProductos }}</strong>
                {{ $totalProductos == 1 ? 'resultado' : 'resultados' }}
                para "<strong>{{ request('q') }}</strong>"
            </span>
                <a href="{{ route('shop.index') }}" class="text-decoration-none" style="color:var(--osorio-gold);">
                    <i class="bi bi-x-circle me-1"></i> Limpiar
                </a>
            </div>
        @endif

        @if(isset($productos) && $productos->count() > 0)
            <div class="row g-3">
                @foreach($productos as $producto)
                    <div class="col-6 col-md-4 col-lg-3">
                        @include('partials.product-card-premium', ['producto' => $producto])
                    </div>
                @endforeach
            </div>

            @if($productos instanceof \Illuminate\Pagination\LengthAwarePaginator)
                <div class="pagination-wrapper">
                    {{ $productos->links() }}
                </div>
            @endif
        @elseif(request()->has('q') && request()->q)
            <div class="text-center py-5">
                <i class="bi bi-search fs-1 text-muted d-block mb-3"></i>
                <h5>No encontramos productos</h5>
                <p class="text-muted">Intenta con otros términos de búsqueda</p>
                <a href="{{ route('shop.index') }}" class="btn-gold" style="display:inline-block; padding:10px 30px; font-size:0.9rem;">
                    Ver todos los productos
                </a>
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-box-seam fs-1 text-muted d-block mb-3"></i>
                <h5>No hay productos disponibles</h5>
                <p class="text-muted">Pronto tendremos nuevos productos para ti.</p>
            </div>
        @endif
    </section>

    <!-- ============================================================
    IA 24/7 - ASISTENCIA INTELIGENTE
    ============================================================ -->
    <section class="section-ia" id="tour-ia">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-7 ia-content">
                    <div class="ia-badge">
                        <i class="bi bi-robot me-2"></i>Asistencia 24/7
                    </div>
                    <h2>
                        Tu asistente <span class="highlight">inteligente</span><br>
                        siempre disponible
                    </h2>
                    <p class="subtitle-ia">
                        <strong>Oso</strong>, nuestro asistente con inteligencia artificial,
                        está disponible <strong>24 horas al día, 7 días a la semana</strong>
                        para ayudarte a encontrar productos, verificar disponibilidad y resolver tus dudas.
                    </p>

                    <div class="ia-features">
                        <div class="ia-feature">
                            <span class="feature-icon">⚡</span>
                            <div class="feature-title">Respuesta Instantánea</div>
                            <div class="feature-desc">Sin esperas</div>
                        </div>
                        <div class="ia-feature">
                            <span class="feature-icon">🎯</span>
                            <div class="feature-title">Precisión</div>
                            <div class="feature-desc">Información actualizada</div>
                        </div>
                        <div class="ia-feature">
                            <span class="feature-icon">💬</span>
                            <div class="feature-title">Conversación Natural</div>
                            <div class="feature-desc">Como hablar con un experto</div>
                        </div>
                        <div class="ia-feature">
                            <span class="feature-icon">🔄</span>
                            <div class="feature-title">Aprendizaje Continuo</div>
                            <div class="feature-desc">Mejora constante</div>
                        </div>
                    </div>

                    <div class="ia-avatar">
                        <div class="avatar-img">🐻</div>
                        <div class="avatar-info">
                            <div class="name">Oso - Asistente IA</div>
                            <div class="status">
                                <span class="online-dot"></span>
                                Disponible 24/7 · <span style="color:var(--osorio-gold);">Pregúntame</span>
                            </div>
                        </div>
                        <button class="btn-gold" onclick="openChat()" style="padding:10px 24px; font-size:0.85rem;">
                            <i class="bi bi-chat me-2"></i>Hablar ahora
                        </button>
                    </div>
                </div>
                <div class="col-lg-5 d-none d-lg-block text-center">
                    <div style="background: rgba(255,255,255,0.03); border-radius: 24px; padding: 30px; border: 1px solid rgba(255,255,255,0.06);">
                        <div style="font-size: 5rem; margin-bottom: 16px;">🐻</div>
                        <h5 style="color:white;">¡Hola! Soy Oso</h5>
                        <p style="color: rgba(255,255,255,0.5); font-size: 0.95rem;">
                            ¿Buscas una moto? ¿Necesitas un repuesto?<br>
                            <strong style="color:var(--osorio-gold);">¡Estoy aquí para ayudarte!</strong>
                        </p>
                        <div style="display: flex; gap: 8px; justify-content: center; flex-wrap: wrap; margin-top: 12px;">
                            <button class="btn btn-sm btn-outline-light rounded-pill" onclick="sendQuickMessage('¿Qué motos tienen disponibles?')" style="font-size:0.8rem;">
                                🏍️ Motos
                            </button>
                            <button class="btn btn-sm btn-outline-light rounded-pill" onclick="sendQuickMessage('¿Tienen repuestos para Honda?')" style="font-size:0.8rem;">
                                🔧 Repuestos
                            </button>
                            <button class="btn btn-sm btn-outline-light rounded-pill" onclick="sendQuickMessage('¿Dónde están sus sucursales?')" style="font-size:0.8rem;">
                                📍 Sucursales
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================================================
    SUCURSALES
    ============================================================ -->
    <section class="container py-4" id="tour-sucursales">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <span class="section-badge" style="display:inline-block; background:rgba(212,168,67,0.1); color:var(--osorio-gold); padding:4px 16px; border-radius:50px; font-size:0.7rem; font-weight:700; text-transform:uppercase; letter-spacing:0.08em;">📍 Ubicación</span>
                <h4 class="fw-bold mt-1">Nuestras <span style="color:var(--osorio-gold);">Sucursales</span></h4>
            </div>
        </div>
        <div class="row g-3">
            @foreach($sucursales as $sucursal)
                <div class="col-md-6 col-lg-3">
                    <div class="sucursal-premium-card">
                        <div class="suc-icon">
                            <i class="bi bi-shop"></i>
                        </div>
                        <div class="suc-info">
                            <h6>{{ $sucursal->descrip }}</h6>
                            @if($sucursal->direccion)
                                <small><i class="bi bi-geo-alt me-1"></i>{{ \Illuminate\Support\Str::limit($sucursal->direccion, 40) }}</small>
                            @endif
                            @if($sucursal->telefono)
                                <br><small><i class="bi bi-telephone me-1"></i>{{ $sucursal->telefono }}</small>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <!-- ============================================================
    BOTÓN DE TOUR
    ============================================================ -->
    <button class="btn-tour" id="btnTour" onclick="startTour()" style="position:fixed; bottom:30px; left:30px; z-index:100; background:white; border:none; border-radius:50px; padding:12px 24px; box-shadow:0 8px 30px rgba(0,0,0,0.12); font-weight:600; display:flex; align-items:center; gap:10px; transition:all 0.3s ease; cursor:pointer; color:var(--text-dark);">
    <span style="width:36px; height:36px; border-radius:50%; background:var(--osorio-gold-gradient); color:white; display:flex; align-items:center; justify-content:center; font-size:1.1rem;">
        <i class="bi bi-compass"></i>
    </span>
        <span>Recorrido</span>
        <span class="badge" style="background:var(--osorio-gold); color:var(--osorio-black); font-size:0.6rem;">Nuevo</span>
    </button>
@endsection

@section('scripts-extra')
    <script>
        // ============================================================
        // TOUR INTERACTIVO
        // ============================================================
        let tourActive = false;
        let currentStep = 0;
        let tourSteps = [];

        function startTour() {
            if (tourActive) { endTour(); return; }

            tourSteps = [
                { target: '#tour-hero', title: '🏍️ Osorio Group', description: 'Líderes en venta de motos y repuestos. Calidad y experiencia desde 1998.', position: 'bottom', action: 'Siguiente' },
                { target: '#tour-leader', title: '🌟 ¿Por qué somos líderes?', description: 'Años de experiencia, calidad garantizada y cobertura nacional.', position: 'bottom', action: 'Siguiente' },
                { target: '#tour-categorias', title: '📂 Categorías', description: 'Explora nuestro catálogo organizado por categorías.', position: 'bottom', action: 'Siguiente' },
                { target: '#tour-destacados', title: '🔥 Productos Destacados', description: 'Los productos más populares con stock disponible.', position: 'top', action: 'Siguiente' },
                { target: '#tour-productos', title: '📦 Catálogo Completo', description: 'Busca cualquier producto por nombre, código o marca.', position: 'top', action: 'Siguiente' },
                { target: '#tour-ia', title: '🤖 Asistente IA 24/7', description: 'Oso, nuestro asistente inteligente, está disponible siempre para ayudarte.', position: 'top', action: 'Finalizar' }
            ];

            tourActive = true;
            currentStep = 0;
            document.getElementById('btnTour').innerHTML = `
        <span style="width:36px; height:36px; border-radius:50%; background:#ef4444; color:white; display:flex; align-items:center; justify-content:center; font-size:1.1rem;">
            <i class="bi bi-x-lg"></i>
        </span>
        <span>Salir del tour</span>
    `;
            showStep(currentStep);
        }

        function showStep(index) {
            document.querySelectorAll('.tour-step').forEach(el => el.remove());
            document.querySelectorAll('.tour-overlay').forEach(el => el.remove());

            if (index >= tourSteps.length || !tourActive) { endTour(); return; }

            const step = tourSteps[index];
            const target = document.querySelector(step.target);
            if (!target) { currentStep++; showStep(currentStep); return; }

            target.classList.add('tour-highlight');

            const overlay = document.createElement('div');
            overlay.className = 'tour-overlay';
            overlay.style.cssText = 'position:fixed; inset:0; z-index:1040; background:rgba(0,0,0,0.5); backdrop-filter:blur(4px);';
            document.body.appendChild(overlay);

            const rect = target.getBoundingClientRect();
            const tooltip = document.createElement('div');
            tooltip.className = 'tour-step';
            tooltip.style.cssText = `
        position:fixed; z-index:1050; background:white; border-radius:16px; padding:24px 28px; max-width:380px;
        box-shadow:0 20px 60px rgba(0,0,0,0.2); border:1px solid rgba(0,0,0,0.06);
        ${step.position === 'bottom' ? `top:${rect.bottom + 20}px; left:${Math.max(16, rect.left + rect.width/2 - 170)}px;` : ''}
        ${step.position === 'top' ? `bottom:${window.innerHeight - rect.top + 20}px; left:${Math.max(16, rect.left + rect.width/2 - 170)}px;` : ''}
    `;

            const isLast = index === tourSteps.length - 1;
            tooltip.innerHTML = `
        <div class="d-flex align-items-center justify-content-between mb-2">
            <span style="display:inline-block; background:var(--osorio-gold); color:var(--osorio-black); width:28px; height:28px; border-radius:50%; text-align:center; line-height:28px; font-weight:700; font-size:0.85rem;">${index + 1}</span>
            <button class="btn btn-sm btn-link text-muted" onclick="endTour()" style="font-size:1.2rem;"><i class="bi bi-x-lg"></i></button>
        </div>
        <div style="font-weight:700; font-size:1.15rem; margin-bottom:6px;">${step.title}</div>
        <div style="color:var(--text-muted); font-size:0.95rem; line-height:1.6; margin-bottom:16px;">${step.description}</div>
        <div style="display:flex; gap:10px; justify-content:flex-end;">
            ${index > 0 ? `<button class="btn btn-sm btn-outline-secondary" onclick="prevStep()">Anterior</button>` : ''}
            <button class="btn btn-sm" style="background:var(--osorio-gold); color:var(--osorio-black); border:none; padding:6px 20px; border-radius:50px; font-weight:600;" onclick="${isLast ? 'endTour()' : 'nextStep()'}">
                ${isLast ? '✨ Finalizar' : step.action}
            </button>
        </div>
    `;

            document.body.appendChild(tooltip);

            if (window.innerWidth < 768) {
                tooltip.style.left = '16px';
                tooltip.style.right = '16px';
                tooltip.style.top = step.position === 'bottom' ? rect.bottom + 20 + 'px' : 'auto';
                tooltip.style.bottom = step.position === 'top' ? window.innerHeight - rect.top + 20 + 'px' : 'auto';
                tooltip.style.maxWidth = 'calc(100vw - 32px)';
            }
        }

        function nextStep() { currentStep++; showStep(currentStep); }
        function prevStep() { if (currentStep > 0) { currentStep--; showStep(currentStep); } }

        function endTour() {
            tourActive = false;
            document.querySelectorAll('.tour-step').forEach(el => el.remove());
            document.querySelectorAll('.tour-overlay').forEach(el => el.remove());
            document.querySelectorAll('.tour-highlight').forEach(el => el.classList.remove('tour-highlight'));

            document.getElementById('btnTour').innerHTML = `
        <span style="width:36px; height:36px; border-radius:50%; background:var(--osorio-gold-gradient); color:white; display:flex; align-items:center; justify-content:center; font-size:1.1rem;">
            <i class="bi bi-compass"></i>
        </span>
        <span>Recorrido</span>
        <span class="badge" style="background:var(--osorio-gold); color:var(--osorio-black); font-size:0.6rem;">Nuevo</span>
    `;

            const toast = document.createElement('div');
            toast.style.cssText = `
        position:fixed; bottom:100px; left:50%; transform:translateX(-50%); background:white;
        padding:20px 32px; border-radius:16px; box-shadow:0 20px 60px rgba(0,0,0,0.15); z-index:1060;
        text-align:center; max-width:400px;
    `;
            toast.innerHTML = `
        <div style="font-size:2.5rem; margin-bottom:8px;">🎉</div>
        <h6 class="fw-bold">¡Tour completado!</h6>
        <p class="text-muted mb-2" style="font-size:0.9rem;">
            Conoce nuestro catálogo. <br>
            <strong>¿Necesitas ayuda? Pregúntale a Oso</strong>
        </p>
        <button class="btn btn-sm" style="background:var(--osorio-gold); color:var(--osorio-black); border:none; padding:6px 24px; border-radius:50px; font-weight:600;" onclick="this.parentElement.remove(); openChat();">
            💬 Hablar con Oso
        </button>
    `;
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 6000);
        }

        // Inicializar tour automático
        document.addEventListener('DOMContentLoaded', function() {
            const hasSeenTour = localStorage.getItem('osorio_tour_seen_v3');
            if (!hasSeenTour) {
                setTimeout(() => {
                    startTour();
                    localStorage.setItem('osorio_tour_seen_v3', 'true');
                }, 2000);
            }
        });

        // Función para abrir el chat
        window.openChat = function() {
            const container = document.querySelector('#app-chat-container');
            if (container) {
                const launcher = container.querySelector('.app-chat-launcher');
                if (launcher && typeof activechat === 'function') {
                    activechat();
                } else if (launcher) {
                    launcher.click();
                }
            }
        };

        // Función para mensajes rápidos en el chat
        window.sendQuickMessage = function(text) {
            const container = document.querySelector('#app-chat-container');
            if (container) {
                const launcher = container.querySelector('.app-chat-launcher');
                if (launcher) {
                    if (typeof activechat === 'function') {
                        activechat();
                        setTimeout(() => {
                            const input = document.getElementById('message');
                            if (input) {
                                input.value = text;
                                if (typeof sendMessage === 'function') {
                                    sendMessage();
                                }
                            }
                        }, 500);
                    } else {
                        launcher.click();
                    }
                }
            }
        };
    </script>
@endsection
