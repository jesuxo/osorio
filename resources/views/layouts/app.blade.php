<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Mi Tienda') - Osorio Group</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-color: #0072c5;
            --primary-dark: #0059a3;
            --primary-light: #e8f4fd;
            --text-dark: #1a1a2e;
            --text-muted: #6c757d;
            --shadow-sm: 0 2px 8px rgba(0,0,0,0.06);
            --shadow-md: 0 4px 20px rgba(0,0,0,0.1);
            --shadow-lg: 0 10px 40px rgba(0,0,0,0.12);
            --radius: 12px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: #f8fafc;
            color: var(--text-dark);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* ===== HEADER ===== */
        .shop-header {
            background: white;
            border-bottom: 1px solid rgba(0,0,0,0.06);
            position: sticky;
            top: 0;
            z-index: 1000;
            backdrop-filter: blur(12px);
            background: rgba(255,255,255,0.92);
            transition: var(--transition);
        }

        .shop-header.scrolled {
            box-shadow: var(--shadow-sm);
        }

        .shop-header .navbar-brand img {
            height: 45px;
            transition: var(--transition);
        }

        .shop-header .navbar-brand img:hover {
            transform: scale(1.02);
        }

        /* ===== SEARCH BAR ===== */
        .search-wrapper {
            position: relative;
            flex: 1;
            max-width: 580px;
            margin: 0 20px;
        }

        .search-wrapper .form-control {
            border-radius: 50px;
            padding: 12px 20px 12px 48px;
            border: 2px solid #e9ecef;
            font-size: 0.95rem;
            transition: var(--transition);
            background: #f8fafc;
            height: 50px;
        }

        .search-wrapper .form-control:focus {
            border-color: var(--primary-color);
            background: white;
            box-shadow: 0 0 0 4px rgba(0, 114, 197, 0.1);
        }

        .search-wrapper .search-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 1.2rem;
        }

        /* ===== HEADER ACTIONS ===== */
        .header-actions {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .header-actions .btn-icon {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            border: none;
            background: transparent;
            color: var(--text-dark);
            font-size: 1.2rem;
            transition: var(--transition);
            position: relative;
        }

        .header-actions .btn-icon:hover {
            background: var(--primary-light);
            color: var(--primary-color);
        }

        .header-actions .btn-icon .badge-dot {
            position: absolute;
            top: 6px;
            right: 6px;
            width: 10px;
            height: 10px;
            background: #ef476f;
            border-radius: 50%;
            border: 2px solid white;
        }

        /* ===== HERO SECTION ===== */
        .hero-section {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            padding: 60px 0 70px;
            color: white;
            border-radius: 0 0 40px 40px;
            margin-bottom: 40px;
            position: relative;
            overflow: hidden;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 600px;
            height: 600px;
            background: rgba(255,255,255,0.05);
            border-radius: 50%;
        }

        .hero-section::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: -10%;
            width: 400px;
            height: 400px;
            background: rgba(255,255,255,0.03);
            border-radius: 50%;
        }

        .hero-section h1 {
            font-weight: 800;
            font-size: 2.8rem;
            letter-spacing: -0.02em;
            position: relative;
            z-index: 1;
        }

        .hero-section p {
            font-size: 1.15rem;
            opacity: 0.85;
            max-width: 500px;
            position: relative;
            z-index: 1;
        }

        /* ===== CATEGORY PILLS ===== */
        .category-pills {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 30px;
        }

        .category-pills .pill {
            padding: 8px 20px;
            border-radius: 50px;
            background: white;
            border: 2px solid #e9ecef;
            color: var(--text-dark);
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 500;
            transition: var(--transition);
            cursor: pointer;
        }

        .category-pills .pill:hover,
        .category-pills .pill.active {
            background: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
            transform: translateY(-2px);
            box-shadow: var(--shadow-sm);
        }

        /* ===== PRODUCT CARDS ===== */
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
            gap: 24px;
        }

        .product-card {
            background: white;
            border-radius: var(--radius);
            overflow: hidden;
            transition: var(--transition);
            border: 1px solid rgba(0,0,0,0.04);
            position: relative;
        }

        .product-card:hover {
            transform: translateY(-6px);
            box-shadow: var(--shadow-md);
            border-color: transparent;
        }

        .product-card .product-image {
            background: #f8fafc;
            height: 200px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
        }

        .product-card .product-image img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
            transition: var(--transition);
        }

        .product-card:hover .product-image img {
            transform: scale(1.05);
        }

        .product-card .product-badge {
            position: absolute;
            top: 12px;
            left: 12px;
            padding: 4px 12px;
            border-radius: 50px;
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }

        .product-card .product-badge.stock {
            background: #d4edda;
            color: #155724;
        }

        .product-card .product-badge.out-of-stock {
            background: #f8d7da;
            color: #721c24;
        }

        .product-card .product-body {
            padding: 18px 20px 20px;
        }

        .product-card .product-category {
            font-size: 0.75rem;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.04em;
            font-weight: 600;
        }

        .product-card .product-title {
            font-weight: 600;
            font-size: 1rem;
            margin: 4px 0 8px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            min-height: 48px;
        }

        .product-card .product-title a {
            color: var(--text-dark);
            text-decoration: none;
            transition: var(--transition);
        }

        .product-card .product-title a:hover {
            color: var(--primary-color);
        }

        .product-card .product-price {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--primary-color);
        }

        .product-card .product-stock-info {
            font-size: 0.8rem;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .product-card .product-stock-info .in-stock {
            color: #28a745;
        }

        .product-card .product-stock-info .out-of-stock {
            color: #dc3545;
        }

        .product-card .product-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 12px;
            padding-top: 12px;
            border-top: 1px solid #f1f3f5;
        }

        .product-card .product-footer .sucursales {
            font-size: 0.7rem;
            color: var(--text-muted);
        }

        .product-card .product-footer .sucursales .badge-sucursal {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 50px;
            background: var(--primary-light);
            color: var(--primary-color);
            font-size: 0.65rem;
            font-weight: 500;
            margin: 2px 2px 0 0;
        }

        /* ===== PAGINATION ===== */
        .pagination-wrapper {
            margin-top: 40px;
            display: flex;
            justify-content: center;
        }

        .pagination-wrapper .pagination .page-link {
            border-radius: 8px;
            margin: 0 4px;
            padding: 8px 16px;
            border: 1px solid #e9ecef;
            color: var(--text-dark);
            transition: var(--transition);
        }

        .pagination-wrapper .pagination .page-link:hover,
        .pagination-wrapper .pagination .page-item.active .page-link {
            background: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
        }

        /* ===== FOOTER ===== */
        .shop-footer {
            background: #1a1a2e;
            color: rgba(255,255,255,0.7);
            padding: 40px 0 20px;
            margin-top: auto;
        }

        .shop-footer h5 {
            color: white;
            font-weight: 600;
            font-size: 1rem;
            margin-bottom: 16px;
        }

        .shop-footer a {
            color: rgba(255,255,255,0.6);
            text-decoration: none;
            transition: var(--transition);
            display: block;
            padding: 4px 0;
            font-size: 0.9rem;
        }

        .shop-footer a:hover {
            color: white;
            padding-left: 4px;
        }

        .shop-footer .footer-divider {
            border-color: rgba(255,255,255,0.06);
            margin: 24px 0;
        }

        .shop-footer .footer-bottom {
            font-size: 0.85rem;
            text-align: center;
            color: rgba(255,255,255,0.4);
        }

        /* ===== PRODUCT DETAIL ===== */
        .product-detail-image {
            background: white;
            border-radius: var(--radius);
            padding: 40px;
            text-align: center;
            border: 1px solid #e9ecef;
        }

        .product-detail-image img {
            max-height: 400px;
            max-width: 100%;
            object-fit: contain;
        }

        .product-detail-info h1 {
            font-weight: 700;
            font-size: 2rem;
            margin-bottom: 8px;
        }

        .product-detail-info .sku {
            font-size: 0.9rem;
            color: var(--text-muted);
        }

        .product-detail-info .price {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary-color);
            margin: 16px 0;
        }

        .product-detail-info .stock-status {
            display: inline-block;
            padding: 6px 16px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.85rem;
        }

        .product-detail-info .stock-status.in-stock {
            background: #d4edda;
            color: #155724;
        }

        .product-detail-info .stock-status.out-of-stock {
            background: #f8d7da;
            color: #721c24;
        }

        .product-detail-info .sucursales-list {
            margin-top: 16px;
        }

        .product-detail-info .sucursales-list .sucursal-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 12px;
            border-bottom: 1px solid #f1f3f5;
            font-size: 0.9rem;
        }

        .product-detail-info .sucursales-list .sucursal-item:last-child {
            border-bottom: none;
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 768px) {
            .hero-section {
                padding: 40px 0 50px;
                border-radius: 0 0 24px 24px;
            }
            .hero-section h1 {
                font-size: 2rem;
            }
            .hero-section p {
                font-size: 1rem;
            }
            .search-wrapper {
                margin: 10px 0;
                max-width: 100%;
                order: 3;
                flex-basis: 100%;
            }
            .search-wrapper .form-control {
                height: 44px;
                padding: 10px 16px 10px 44px;
                font-size: 0.9rem;
            }
            .product-grid {
                grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
                gap: 16px;
            }
            .product-card .product-image {
                height: 150px;
                padding: 12px;
            }
            .product-card .product-body {
                padding: 12px 14px 14px;
            }
            .product-card .product-title {
                font-size: 0.85rem;
                min-height: 38px;
            }
            .product-card .product-price {
                font-size: 1rem;
            }
            .header-actions .btn-icon {
                width: 38px;
                height: 38px;
                font-size: 1rem;
            }
            .shop-header .navbar-brand img {
                height: 35px;
            }
            .category-pills {
                gap: 6px;
            }
            .category-pills .pill {
                padding: 6px 14px;
                font-size: 0.75rem;
            }
        }

        @media (max-width: 576px) {
            .product-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 12px;
            }
            .product-card .product-image {
                height: 120px;
                padding: 8px;
            }
            .product-card .product-title {
                font-size: 0.8rem;
                min-height: 34px;
                -webkit-line-clamp: 1;
            }
            .product-card .product-price {
                font-size: 0.9rem;
            }
            .product-card .product-footer .sucursales {
                display: none;
            }
        }

        /* ===== UTILITY ===== */
        .text-primary-custom {
            color: var(--primary-color);
        }
        .bg-primary-soft {
            background: var(--primary-light);
        }
        .btn-primary-custom {
            background: var(--primary-color);
            border: none;
            color: white;
            padding: 10px 28px;
            border-radius: 50px;
            font-weight: 600;
            transition: var(--transition);
        }
        .btn-primary-custom:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
            color: white;
        }
        .btn-outline-primary-custom {
            border: 2px solid var(--primary-color);
            color: var(--primary-color);
            background: transparent;
            padding: 8px 24px;
            border-radius: 50px;
            font-weight: 600;
            transition: var(--transition);
        }
        .btn-outline-primary-custom:hover {
            background: var(--primary-color);
            color: white;
            transform: translateY(-2px);
        }
        .section-title {
            font-weight: 700;
            font-size: 1.5rem;
            margin-bottom: 20px;
        }
        .section-title .highlight {
            color: var(--primary-color);
        }
    </style>

    <style>
        .chat-message-welcome {
            flex:0 0 auto;
            margin:0 -15px auto;
            border-bottom:10px solid #efefef;
            padding:20px;
            text-align:center
        }
        .chat-message-welcome .title {
            font-weight:600
        }
        .chat-message-welcome p {
            margin-bottom:5px
        }
        .chat__footer {
            margin:5px -15px 0
        }
        .chat-attachment {
            display:flex;
            font-weight:600;
            align-items:center;
            padding:10px 15px;
            border-top:1px solid #d9d9d9;
            color:#222!important
        }
        .chat-attachment .svgIcon {
            flex:0 0 auto
        }
        .chat-attachment:last-of-type {
            padding:10px 15px 0
        }
        .chat-attachmentIcon {
            flex:0 0 auto;
            margin-right:10px
        }
        .chat-warning {
            position:absolute;
            top:40px;
            width:100%;
            height:100%;
            background-color:#000000bf
        }
        .chat-warning-content {
            position:absolute;
            top:50%;
            left:50%;
            transform:translate(-50%,-50%);
            background-color:#fff;
            padding:25px 14px;
            text-align:center;
            width:285px;
            border-radius:3px;
            box-sizing:border-box
        }
        .chat-warning-close {
            font-size:1.625rem;
            position:absolute;
            top:8px;
            right:15px;
            color:#d9d9d9;
            cursor:pointer
        }
        .chat-warning-close:hover {
            color:#6c6c6c
        }
        .chat-legal {
            font-size:.875rem;
            line-height:1.3125rem
        }
        .chat-legal a {
            display:block;
            color:#8c8c8c;
            text-decoration:underline
        }
        .chat__separator {
            font-size:.875rem;
            line-height:1.3125rem;
            color:#8c8c8c;
            text-align:center;
            margin:15px 0;
            text-transform:uppercase
        }
        .chat__timestamp {
            font-size:.6875rem;
            line-height:1rem;
            color:#8c8c8c;
            text-align:right;
            margin-top:3px
        }
        .chat-message {
            overflow:hidden;
            margin:0 0 15px
        }
        .chat-message .avatar {
            margin-top:5px
        }
        .content+.chat-message {
            margin-top:30px
        }
        .chat-message-avatar {
            width:36px;
            height:36px
        }
        .chat-message-avatar img {
            width:100%;
            height:100%;
            border-radius:50%
        }
        .chat-message-avatar.shape-square {
            height:40px;
            width:40px;
            border-radius:3px;
            position:relative;
            overflow:hidden
        }
        .chat-message-avatar.shape-square img {
            transform:translate(-50%);
            height:40px;
            border-radius:0;
            left:50%;
            display:block;
            position:relative;
            width:auto;
            max-width:inherit
        }
        .chat-message-avatar.avatar-alias {
            min-width:36px
        }
        .chat-message-avatar.avatar-vendor img {
            width:auto;
            max-height:36px;
            height:inherit;
            border-radius:4px
        }
        .chat-message-globe {
            max-width:70%;
            padding:15px;
            background-color:#fff;
            border-radius:6px;
            margin-bottom:5px;
            position:relative;
            display:inline-block;
            word-break:normal;
            word-wrap:break-word
        }
        .chat-message-globe p {
            margin-bottom:0
        }
        .chat-message-globe a {
            color:#0072c5;
            text-decoration:none;
            font-size:inherit!important
        }
        .chat-message-globe a.legal {
            color:#6c6c6c;
            text-decoration:underline
        }
        .chat-message-globe ul,
        .chat-message-globe ol {
            padding-left:15px
        }
        .chat-message-globe ul li {
            list-style-type:disc
        }
        .chat-message-globe ol li {
            list-style-type:decimal
        }
        .chat-message-globe p.chat-message-globe__title {
            margin-bottom:10px;
            font-weight:400;
            text-transform:uppercase;
            letter-spacing:.0625rem
        }
        .chat-message-globe p.chat-message-globe__infoSolic {
            margin-bottom:5px
        }
        .chat-message-globe p.chat-message-globe__infoSolic .svgIcon {
            margin-right:10px
        }
        .chat-message-globe p.chat-message-globe__infoSolic:last-child {
            margin-bottom:10px
        }
        .chat__link {
            color:#0072c5;
            display:block;
            margin:5px 0;
            word-break:break-word
        }
        .chat__figure {
            min-height:150px
        }
        .chat__img {
            width:auto;
            height:150px;
            border-radius:4px
        }
        .chat-message-globe .btn-outline {
            margin:5px 0;
            display:block;
            text-align:center;
            cursor:pointer
        }
        .message-outcome {
            flex:0 0 auto
        }
        .message-outcome .chat-message-avatar,
        .message-outcome .chat-message-globe {
            float:right
        }
        .message-outcome .chat-message-avatar {
            margin-left:13px
        }
        .message-outcome .chat-message-globe {
            border-radius:10px;
            background-color:#fef4f1;
            border:1px solid #0072c5
        }
        .message-outcome .chat-message-globe.note-message {
            background-color:#fff7e1;
            border:1px solid #ffd967
        }
        .note-message .chat-message-avatar {
            display:none
        }
        .note-message .chat-message-globe {
            float:right
        }
        .note-message .chat-message-avatar {
            margin-left:13px
        }
        .note-message .chat-message-globe {
            border-radius:10px 0 10px 10px;
            background-color:#fff7e1
        }
        .message-income {
            flex:0 0 auto
        }
        .message-income .message-income {
            margin-bottom:15px
        }
        .message-income .chat-message-globe,
        .message-income .chat-message-avatar {
            float:left
        }
        .message-income .chat-message-avatar {
            margin-right:13px
        }
        .message-income .chat-message-globe {
            border-radius:10px;
            border:1px solid #d9d9d9
        }
        .message-outcome+.message-income,
        .message-income+.message-outcome {
            flex:0 0 auto
        }
        .message-income.chat-aggregate {
            margin:0 0 15px 49px
        }
        .chat-aggregate .chat-message-globe {
            max-width:82.5%
        }
        .chat-message-send form,
        .chat-btn-new-message {
            padding:1.5rem 1rem
        }
        .chat-message-send form button.btn,
        .chat-btn-new-message button.btn {
            margin:0
        }
        .chat-message-send {
            border-top:1px solid #f8f8f8;
            position:absolute;
            bottom:0;
            width:100%;
            box-sizing:border-box
        }
        .chat-message-send__messageInput {
            vertical-align:middle;
            -webkit-appearance:none;
            appearance:none;
            box-sizing:border-box;
            border:0;
            background-color:#fff;
            resize:none;
            outline:none;
            width:100%;
            margin-right:1rem
        }
        .chat-message-send__messageInput[readonly=readonly]::placeholder {
            opacity:.5
        }
        .chat-message-send__messageInput::-webkit-inner-spin-button,
        .chat-message-send__messageInput::-webkit-outer-spin-button {
            -webkit-appearance:none;
            appearance:none;
            margin:0
        }
        .chat-message-send__messageSubmit {
            background:#0072c5;
            box-sizing:border-box;
            border-radius:3px;
            cursor:pointer;
            color:#fff;
            height:2rem;
            width:2rem
        }
        .chat-message-send__messageSubmit--disabled {
            opacity:.2;
            cursor:not-allowed;
            pointer-events:none
        }
        .chat-message-send input[type=text] {
            width:100%
        }
        .chat-message-send input[type=submit] {
            -webkit-appearance:none;
            appearance:none;
            color:#0072c5;
            font-weight:600;
            background-color:initial;
            border:0;
            padding:0;
            margin:5px 0
        }
        .chat-message-send .alert-error {
            border:none;
            padding:10px 15px;
            font-size:.75rem
        }
        .chat-message-send .composer-textarea-container {
            border:1px solid #d9d9d9;
            border-radius:.5rem;
            padding:12px
        }
        .chat-send-hint {
            font-size:.6875rem;
            line-height:1rem;
            color:#efefef;
            margin-top:5px;
            text-align:right;
            min-height:20px;
            transition:color .4s ease-out
        }
        .chat-send-hint.active {
            color:#8c8c8c
        }
        .chatQuickReply {
            text-align:left;
            padding:0 0 15px
        }
        .chatQuickReply__input {
            display:inline-block;
            margin:5px;
            padding:3px 15px;
            background:#fff;
            border:1px solid #fff;
            border-radius:16px;
            cursor:pointer;
            box-shadow:0 2px 5px #a5a5a580
        }
        .chatQuickReply__input:hover,
        .chatQuickReply__input--selected {
            border:1px solid #0072c5;
            background:#fef4f1
        }
        .loadingMessages {
            background-color:#f8f8f8;
            float:left;
            border-radius:8px 8px 8px 0;
            padding:5px 0;
            width:50px;
            text-align:center;
            margin-bottom:10px
        }
        .loadingMessages__item {
            display:inline-block;
            vertical-align:middle;
            animation:blink 1.4s infinite ease-in-out both;
            width:6px;
            height:6px;
            background-color:#0072c5;
            border-radius:100%;
            margin-right:2px
        }
        .loadingMessages__item:nth-child(2) {
            animation-delay:.2s
        }
        .loadingMessages__item:nth-child(3) {
            animation-delay:.4s
        }
        .pusher-container {
            position:fixed;
            bottom:20px;
            right:20px;
            z-index:1053
        }
        .pusher-container.fadeout {
            pointer-events:none
        }
        .pusher-container.fadeout .chat-launcher-button {
            pointer-events:all
        }
        .pusher-zfix .pusher-container,
        .pusher-zfix .chat-conversation {
            z-index:1030
        }
        .chat-messages {
            position:absolute;
            width:100%;
            padding:0;
            margin-bottom:70px;
            bottom:0;
            top:45px;
            box-sizing:border-box
        }
        .chat-messages__inner {
            overflow-y:auto;
            padding:0 15px;
            display:flex;
            flex-direction:column;
            height:100%
        }
        .chat-messages--chatbot {
            display:flex;
            flex-direction:column;
            justify-content:flex-end;
            padding:0 0 106px;
            margin-bottom:auto;
            bottom:0;
            top:45px
        }
        .chat-header {
            background-color:#fff;
            border-bottom:1px solid #d9d9d9;
            padding:10px 15px;
            text-align:center;
            position:relative
        }
        .chat-name {
            display:block;
            text-overflow:ellipsis;
            overflow:hidden;
            white-space:nowrap;
            width:210px;
            font-weight:600
        }
        .chat-controls {
            position:absolute;
            top:0
        }
        .chat-controls.chat-controls-left {
            left:0
        }
        .chat-controls.chat-controls-right {
            right:0
        }
        .chat-control-btn {
            padding:10px 15px;
            display:inline-block;
            cursor:pointer
        }
        .chat-ui {
            width:16px;
            height:16px;
            background:url(/images/chat-ui.png) no-repeat top left;
            background-size:16px;
            display:inline-block;
            vertical-align:middle
        }
        .chat-ui.chat-max {
            background-position:0 -16px
        }
        .chat-ui.chat-close {
            background-position:0 -32px
        }
        .chat-ui.chat-menu {
            background-position:0 -48px
        }
        .chat-ui.chat-refresh {
            background:url(/images/refresh.svg) no-repeat;
            background-size:14px
        }
        .chat-controls .chat-message-count {
            top:5px;
            right:0;
            height:18px;
            min-width:10px;
            line-height:1.125rem
        }
        .chat-launcher.hidden {
            display:none!important
        }
        .chat-launcher::after {
            content:"";
            display:block;
            clear:both
        }
        .chat-launcher-button {
            position:absolute;
            bottom:0;
            right:0;
            cursor:pointer;
            width:50px;
            height:50px
        }
        .chat-launcher-button img {
            border-radius:50%;
            width:100%;
            height:auto;
            overflow:hidden;
            box-shadow:0 5px 15px #0000004d
        }
        .chat-launcher-button--bottom {
            bottom:60px
        }
        .chat-launcher-button.closed {
            background:#fff url(/images/chat-launcher-button.png) no-repeat center center;
            background-size:cover
        }

        .chat-launcher-preview {
            float:right;
            font-weight:400;
            max-width:240px;
            min-height:22px;
            padding:10px 14px;
            margin-right:70px;
            color:#222;
            border-radius:10px;
            background:#fff;
            box-shadow:0 2px 10px 1px #0000004d;
            cursor:pointer;
            word-break:break-word
        }
        .chat-launcher-preview::after {
            content:"";
            width:10px;
            height:13px;
            background:url(/images/chat-launcher-preview.png) no-repeat center center;
            background-size:10px;
            position:absolute;
            bottom:10px;
            right:60px
        }
        .chat-conversation {
            transform:scale3d(0,0,0);
            transform-origin:bottom right;
            transition:opacity .15s linear .15s;
            opacity:0;
            background-color:#fff;
            border-left:1px solid #d9d9d9;
            z-index:999;
            position:fixed;
            bottom:0;
            right:0;
            width:370px;
            height:100% !important;
            box-shadow:0 0 4px #00000026
        }
        .chat-conversation.active {
            transform:scaleZ(1);
            opacity:1;
            pointer-events:all
        }
        .chat-loader {
            background-color:#efefef;
            z-index:100;
            position:fixed;
            right:0;
            width:100%;
            height:100%;
            overflow:hidden;
            -webkit-overflow-scrolling:touch
        }
        .chat-loader .chat-loader-content {
            position:relative;
            top:50%;
            transform:translateY(-70%)
        }
        .chat-loader .chat-loader-content .animation {
            width:200px;
            height:200px;
            margin:0 auto;
            text-align:center
        }
        .chat-loader .chat-loader-content .animation.default {
            background:url(/images/AR.gif) no-repeat scroll 50% 50% rgba(0,0,0,0);
            background-size:150px
        }
        .chat-loader .chat-loader-content .message {
            font-size:1.125rem;
            line-height:1.6875rem;
            text-align:center
        }
        .chat-history.active {
            background-color:#efefef;
            position:absolute;
            top:42px;
            bottom:0;
            width:100%;
            z-index:10
        }
        .chat-history.active .chat-messages {
            padding:0;
            top:0;
            bottom:67px
        }
        .chat-panel {
            background:#fff;
            border-bottom:1px solid #d9d9d9;
            padding:15px;
            position:relative;
            cursor:pointer
        }
        .chat-panel.chat-message {
            margin:0
        }
        .chat-panel .chat-message-avatar {
            margin-top:5px;
            position:relative
        }
        .chat-message-count {
            font-size:.8125rem;
            line-height:1.1875rem;
            line-height:1.3125rem;
            background:#0072c5;
            border:2px solid #fff;
            border-radius:50px;
            padding:0 4px;
            height:21px;
            min-width:13px;
            color:#fff;
            text-align:center;
            position:absolute
        }
        .chat-launcher .chat-message-count {
            bottom:35px;
            right:-8px
        }
        .chat-panel .chat-message-count {
            top:-10px;
            right:-8px
        }
        .chat-message-name,
        .chat-message-subject {
            display:block;
            color:#6c6c6c
        }
        .chat-message-name {
            font-weight:600;
            text-transform:capitalize
        }
        .chat-message-lastmessage {
            font-size:.875rem;
            line-height:1.3125rem;
            position:absolute;
            top:13px;
            right:20px;
            color:#8c8c8c
        }
        .chat-btn-new-message {
            background-color:#efefef;
            position:absolute;
            bottom:5px;
            left:0;
            width:100%;
            text-align:center;
            box-sizing:border-box
        }
        .transcription-chat {
            background:#fff;
            margin:0;
            padding-top:20px;
            padding-bottom:20px
        }
        .transcription-chat li {
            padding:5px 0 5px 15px
        }
        .transcription-content {
            border-bottom:1px solid #d9d9d9;
            padding:0 20px 5px 0
        }
        .transcription-username {
            font-weight:600;
            margin-bottom:0
        }
        .transcription-timestamp {
            font-size:.6875rem;
            line-height:1rem;
            font-weight:400;
            color:#8c8c8c;
            display:inline-block;
            margin-left:10px
        }
        .app-chat-writing-alert {
            display:none;
            padding:10px;
            text-align:center;
            font-style:italic;
            color:#efefef;
            background:#fff;
            border-bottom:1px solid #efefef
        }
        .modalChat {
            position:absolute;
            inset:0;
            width:100%;
            height:100%;
            z-index:1040;
            overflow:auto;
            outline:0;
            margin:0 auto;
            background:#fff;
            display:none
        }
        .modalChat__header {
            display:flex;
            flex-wrap:wrap;
            justify-content:space-between;
            align-items:center;
            padding:20px 20px 10px
        }
        .modalChat__content {
            position:fixed;
            padding:0 20px 20px;
            height:75%;
            width:100%;
            box-sizing:border-box;
            overflow-y:auto
        }
        .modalChat__close {
            color:#6c6c6c;
            text-decoration:underline;
            cursor:pointer
        }
        .modalChat__title {
            font-size:1.125rem;
            line-height:1.4375rem;
            font-weight:600;
            display:block
        }
        @media (min-width: 768px) {
            .modalChat__title {
                font-size:1.25rem;
                line-height:1.625rem
            }
        }
        .modalChat__input {
            transform:translateZ(0);
            color:#222;
            border:1px solid #8c8c8c;
            border-radius:2px;
            box-sizing:border-box;
            padding:10px;
            width:100%;
            margin-top:15px
        }
        .modalChat__results {
            position:relative
        }
        .modalChat__results ul {
            margin-bottom:0
        }
        .modalChat__results li {
            border-bottom:1px solid #d9d9d9;
            background-color:#fff;
            padding:15px;
            position:relative;
            cursor:pointer
        }
        .modalChat__results li:last-of-type {
            padding-bottom:65px;
            border-bottom:none
        }
        .modalChat__noResults {
            font-size:.8125rem;
            line-height:1.1875rem;
            text-align:center;
            display:block
        }
        .modalChat__resultsTitle {
            display:block;
            text-overflow:ellipsis;
            overflow:hidden;
            white-space:nowrap;
            font-weight:400
        }

        .chat-message-count {
            font-size: .8125rem;
            line-height: 1.1875rem;
            line-height: 1.3125rem;
            background: #0072c5;
            border: 2px solid #fff;
            border-radius: 50px;
            padding: 0 4px;
            height: 21px;
            min-width: 13px;
            color: #fff;
            text-align: center;
            position: absolute;
        }
        .bounce-once {
            -webkit-animation: bounce-once .6s ease-out;
            animation: bounce-once .6s ease-out;
        }

        #app-chat-container *,
        #app-chat-container ::before,
        #app-chat-container ::after {
            box-sizing: content-box;
        }
        .chat-conversation .chat-message-send__messageSubmit {
            display: flex;
            background-image: url(data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIxNiIgaGVpZ2h0PSIxNiIgdmlld0JveD0iMCAwIDE2IDE2IiBmaWxsPSJub25lIj4KPGcgY2xpcC1wYXRoPSJ1cmwoI2NsaXAwXzE5XzMwMykiPgo8cGF0aCBkPSJNMTUuNTA0MyAwLjQ1NDMxN0wxNS41MjQ0IDAuNDcyMzMyQzE1LjUzMTcgMC40Nzk1OTkgMTUuNTM4NyAwLjQ4NzA1NSAxNS41NDUzIDAuNDk0Njg0TDE1LjUyMzIgMC40NzI1NTZDMTUuNzc0OCAwLjcyMjc5MiAxNS44NjUgMS4wOTI4NSAxNS43NTYxIDEuNDMyOTJMMTEuMzk0NSAxNC44MjMxQzExLjE5NTEgMTUuNDIxNiAxMC42MjgyIDE1LjgxOTYgOS45OTc2MiAxNS44MDQxQzkuMzY3IDE1Ljc4ODYgOC44MjA0NyAxNS4zNjMxIDguNjQ2NzIgMTQuNzM5OUw3LjIxMzA5IDguNzgzOTRMMS4yNDYxNSA3LjM1MjM1QzAuNjM5ODk2IDcuMTgxMzIgMC4yMTU5MTcgNi42MzUxIDAuMjAwNjIyIDYuMDA1MzdDMC4xODUzMjggNS4zNzU2MyAwLjU4MjI5IDQuODA5NDcgMS4xODMwMiA0LjYwODA2TDE0LjU2MzcgMC4yNDQwNDNDMTQuODkyOCAwLjEzNjc4NiAxNS4yNTMzIDAuMjE4MDgzIDE1LjUwNDMgMC40NTQzMTdaTTE0LjY0MTEgMS45ODMyNkw4LjA3MTMxIDguNTUzMDNMOS41MDY4OCAxNC41MTY0QzkuNTcxNDcgMTQuNzQ3NiA5Ljc3OTQ5IDE0LjkwOTYgMTAuMDE5NSAxNC45MTU1QzEwLjI1OTUgMTQuOTIxNCAxMC40NzUzIDE0Ljc2OTkgMTAuNTUwMiAxNC41NDQ5TDE0LjY0MTEgMS45ODMyNlpNMTQuMDA3MyAxLjM2MDE1TDEuNDYyMTIgNS40NTE5OUMxLjIzNDYzIDUuNTI4MjcgMS4wODM0MiA1Ljc0MzkyIDEuMDg5MjUgNS45ODM3OEMxLjA5NTA3IDYuMjIzNjUgMS4yNTY1NyA2LjQzMTcxIDEuNDcwNDkgNi40OTI0Mkw3LjQ0Mjg2IDcuOTI0NTlMMTQuMDA3MyAxLjM2MDE1WiIgZmlsbD0id2hpdGUiLz4KPC9nPgo8ZGVmcz4KPGNsaXBQYXRoIGlkPSJjbGlwMF8xOV8zMDMiPgo8cmVjdCB3aWR0aD0iMTYiIGhlaWdodD0iMTYiIGZpbGw9IndoaXRlIi8+CjwvY2xpcFBhdGg+CjwvZGVmcz4KPC9zdmc+);
            background-size: 1rem auto;
            background-repeat: no-repeat;
            background-position: center center;
        }

        .chat-message-send .composer-textarea-container {
            border: 1px solid #d9d9d9;
            border-radius: .5rem;
            padding: 12px;
        }

        .chat-message-send__messageSubmit--disabled {
            opacity: .2;
            cursor: not-allowed;
            pointer-events: none;
        }

        .chat-conversation .flex-justify-space-between {
            justify-content: space-between;
        }

        .chat-conversation .flex-va-center {
            display: flex;
            align-items: center;
        }
        .chat-submit{
            background: none !important;
            border: none !important;
        }
    </style>

    @yield('styles')
</head>
<body>

<!-- ============================================ -->
<!-- HEADER PÚBLICO -->
<!-- ============================================ -->
<header class="shop-header" id="shopHeader">
    <div class="container">
        <nav class="navbar navbar-expand-lg py-2">
            <div class="container-fluid px-0">

                <!-- Logo -->
                <a class="navbar-brand me-4" href="{{ route('shop.index') }}">
                    <img src="{{ URL::asset('build/images/logo-dark.png') }}" alt="Osorio Group">
                </a>

                <!-- Toggle Mobile -->
                <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#shopNavbar">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <!-- Navbar Collapse -->
                <div class="collapse navbar-collapse" id="shopNavbar">
                    <!-- Search Bar -->
                    <div class="search-wrapper">
                        <span class="search-icon"><i class="bi bi-search"></i></span>
                        <form action="{{ route('shop.search') }}" method="GET" class="w-100">
                            <input type="text"
                                   class="form-control"
                                   name="q"
                                   placeholder="Buscar productos, marcas, códigos..."
                                   value="{{ request('q') }}"
                                   autocomplete="off">
                        </form>
                    </div>

                    <!-- Actions -->
                    <div class="header-actions ms-auto">
                        @auth
                            <a href="{{ route('shop.dashboard') }}" class="btn-icon" title="Mi cuenta">
                                <i class="bi bi-person-circle"></i>
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-primary-custom btn-sm px-4">
                                <i class="bi bi-box-arrow-in-right me-1"></i> Ingresar
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </nav>
    </div>
</header>

<!-- ============================================ -->
<!-- CONTENIDO PRINCIPAL -->
<!-- ============================================ -->
<main class="flex-grow-1">
    @yield('content')
</main>

<!-- ============================================ -->
<!-- FOOTER -->
<!-- ============================================ -->
<footer class="shop-footer">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-4">
                <img src="{{ URL::asset('build/images/logo-light.png') }}" alt="Osorio Group" height="40" class="mb-3">
                <p class="mb-2" style="font-size:0.9rem; max-width:300px;">
                    Soluciones integrales para tu negocio. Calidad y confianza en cada producto.
                </p>
                <div class="d-flex gap-3 mt-3">
                    <a href="#" class="text-white-50"><i class="bi bi-instagram fs-5"></i></a>
                    <a href="#" class="text-white-50"><i class="bi bi-facebook fs-5"></i></a>
                    <a href="#" class="text-white-50"><i class="bi bi-youtube fs-5"></i></a>
                    <a href="#" class="text-white-50"><i class="bi bi-whatsapp fs-5"></i></a>
                </div>
            </div>
            <div class="col-md-2">
                <h5>Productos</h5>
                <a href="{{ route('shop.index') }}">Todos</a>
                <a href="{{ route('shop.category', 'motos') }}">Motos</a>
                <a href="{{ route('shop.category', 'repuestos') }}">Repuestos</a>
                <a href="{{ route('shop.category', 'lubricantes') }}">Lubricantes</a>
            </div>
            <div class="col-md-2">
                <h5>Empresa</h5>
                <a href="#">Nosotros</a>
                <a href="#">Sucursales</a>
                <a href="#">Contacto</a>
            </div>
            <div class="col-md-4">
                <h5>Contacto</h5>
                <p class="mb-1"><i class="bi bi-geo-alt me-2"></i> Av. Principal, Ciudad</p>
                <p class="mb-1"><i class="bi bi-telephone me-2"></i> +58 212 555 5555</p>
                <p class="mb-1"><i class="bi bi-envelope me-2"></i> info@osoriogroup.com</p>
            </div>
        </div>

        <hr class="footer-divider">

        <div class="footer-bottom">
            &copy; {{ date('Y') }} Osorio Group. Todos los derechos reservados.
            <span class="mx-2">|</span>
            Desarrollado por <a href="https://CelisWeb.com.ve" target="_blank" class="text-white-50 text-decoration-none">CelisWeb</a>
        </div>
    </div>
</footer>

<!-- ============================================ -->
<!-- SCRIPTS -->
<!-- ============================================ -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script>
    // Scroll effect para el header
    document.addEventListener('DOMContentLoaded', function() {
        const header = document.getElementById('shopHeader');
        window.addEventListener('scroll', function() {
            if (window.scrollY > 20) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
        });
    });

    // Cerrar navbar mobile al hacer click fuera
    document.addEventListener('click', function(e) {
        const nav = document.getElementById('shopNavbar');
        const toggle = document.querySelector('.navbar-toggler');
        if (nav.classList.contains('show') && !nav.contains(e.target) && !toggle.contains(e.target)) {
            const bsCollapse = bootstrap.Collapse.getInstance(nav);
            if (bsCollapse) bsCollapse.hide();
        }
    });
</script>

@yield('scripts')

<!-- ============================================================
CHAT WIDGET - ASISTENTE IA "OSO"
============================================================ -->
<div id="app-chat-container" class="pusher-container pusher-mobile">
    <div id="app-bot-bot" data-fromtype="bot" data-fromid="bot" data-idconversation="{{ $conversation->id ?? 'null' }}" data-id-question="1" data-id-flow="null" data-id-categ="null" data-id-sector="null" class="app-chat-container-top">
        <div class="chat-launcher app-chat-launcher" onclick="activechat()" style="display: block;">
            <div class="chat-launcher-button">
                <img class="app-chat-avatar" src="/images/avatar.png" width="50" height="50" alt="Oso - Asistente IA">
                <span class="app-chat-num-messages chat-message-count dnone bounce-once" style="display: none"></span>
            </div>
            <div class="chat-launcher-preview" style="display: none">
                <div class="app-conversation-summary firstmessage">
                    ¡Hola! Soy <strong>Oso</strong>, tu asistente virtual. ¿En qué puedo ayudarte?
                </div>
            </div>
        </div>

        <div class="chat-conversation app-chat-conversation" data-initconversation="0">
            <!-- Header -->
            <div class="chat-header">
                <div class="app-controls-menu chat-controls chat-controls-left">
                    <div class=""></div>
                </div>
                <span class="app-chat-name chat-name">
                    <span class="badge bg-success bg-opacity-10 text-success me-1" style="font-size: 0.5rem; vertical-align: middle;">
                        <i class="bi bi-dot"></i> En línea
                    </span>
                    Oso - Asistente IA
                </span>
                <div class="chat-controls chat-controls-left">
                    <span class="chat-control-btn app-chat-refresh">
                        <i class="icon icon-refresh-chat"></i>
                    </span>
                </div>
                <div class="chat-controls chat-controls-right" onclick="$('.app-chat-conversation').removeClass('active')">
                    <span class="chat-control-btn app-chat-min">
                        <span class="chat-ui chat-min"></span>
                    </span>
                </div>
            </div>

            <!-- Mensajes -->
            <div class="app-chat-history chat-history">
                <div class="app-chat-conversations chat-messages"></div>
            </div>

            <div class="app-mobile-nel-scrollfix chat-messages chat-messages--chatbot app-scroll-calculate">
                <div class="app-conversation-parts chat-messages__inner">
                    <div class="chat-message-welcome">
                        <p>
                            <strong>🤖 Oso - Asistente IA</strong><br>
                            <span class="text-muted" style="font-size: 0.9rem;">
                                Pregúntame sobre productos, precios, disponibilidad o sucursales.
                            </span>
                        </p>
                        <div class="chat-legal mt-2">
                            <div class="d-flex flex-wrap gap-2 justify-content-center">
                                <button class="btn btn-sm btn-outline-primary rounded-pill" onclick="sendQuickMessage('¿Qué productos tienen en stock?')">
                                    📦 Ver productos
                                </button>
                                <button class="btn btn-sm btn-outline-success rounded-pill" onclick="sendQuickMessage('¿Dónde están sus sucursales?')">
                                    📍 Sucursales
                                </button>
                                <button class="btn btn-sm btn-outline-warning rounded-pill" onclick="sendQuickMessage('¿Tienen motos disponibles?')">
                                    🏍️ Motos
                                </button>
                            </div>
                            <div class="mt-2">
                                <a href="https://instagram.com/osoriogroup" target="_blank" class="btn btn-primary btn-sm" style="color: white !important;">
                                    <i class="bi bi-instagram me-1"></i> Síguenos
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="chat__separator">
                        <span>{{ \Carbon\Carbon::now()->format('d/m/Y') }}</span>
                    </div>
                    <div class="app-income-message message-income" id="chatMessages"></div>
                </div>
            </div>

            <!-- Input -->
            <div class="composer-container chat-message-send app-chat-message-send">
                <div class="app-chat-writing-alert"></div>
                <div class="composer-textarea-container flex-va-center flex-justify-space-between">
                    <input class="app-no-tiny app-chat-textarea chat-message-send__messageInput"
                           name="comment" id="message" placeholder="Escribe tu mensaje..." autocomplete="off"
                           onkeypress="if(event.key === 'Enter') sendMessage()">
                    <button class="chat-submit" id="sendButton" onclick="sendMessage()">
                        <span class="app-chat-form-submit chat-message-send__messageSubmit"></span>
                    </button>
                </div>
                <div class="text-center mt-1">
                    <small class="text-muted" style="font-size: 0.6rem;">
                        <i class="bi bi-shield-check me-1"></i> Este chat es asistido por IA
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const chatMessages = document.getElementById('chatMessages');
    const messageInput = document.getElementById('message');
    const sendButton = document.getElementById('sendButton');
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    let isLoading = false;
    var counter = 1;
    var firstmessage = 0;
    let conversationId = null;
    let isChatInitialized = false;

    // Inicializar el chat al cargar la página
    $(document).ready(function() {
        initializeChat();
        $('.chat-launcher-preview').show();

        // Enter para enviar mensaje
        $('#message').on('keypress', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendMessage();
            }
        });
    });

    function activechat() {
        $('.app-chat-conversation').addClass('active');
        $('.chat-launcher-preview').hide();
        setTimeout(() => {
            $('#message').focus();
            scrollToBottom();
        }, 300);
    }

    async function initializeChat() {
        if (isChatInitialized) return;

        try {
            const response = await fetch('{{ route("chat.initialize") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({}) // Cuerpo vacío pero necesario
            });

            if (!response.ok) {
                throw new Error('Error al inicializar el chat');
            }

            const data = await response.json();

            if (data.success) {
                conversationId = data.chat_conversation_id;
                isChatInitialized = true;

                // Si hay historial, cargarlo
                if (data.history && data.history.length > 0) {
                    // Limpiar mensajes de bienvenida existentes
                    const welcomeDiv = document.querySelector('.chat-message-welcome');
                    if (welcomeDiv) {
                        welcomeDiv.style.display = 'none';
                    }

                    // Cargar historial
                    data.history.forEach(msg => {
                        if (msg.sender === 'assistant') {
                            if (firstmessage === 0) {
                                // Actualizar mensaje de preview
                                $('.firstmessage').html(msg.message);
                                firstmessage = 1;
                                $('.chat-launcher-preview').show();
                            }
                            addMessage(msg.message, 'bot', msg.time);
                        } else if (msg.sender === 'user') {
                            addMessage(msg.message, 'user', msg.time);
                        }
                    });

                    scrollToBottom();
                }

                // Si es nueva conversación, mostrar mensaje de bienvenida
                if (data.is_new && data.welcome_message) {
                    // El mensaje de bienvenida ya está en el historial
                    // pero podemos actualizar el preview
                    $('.firstmessage').html(data.welcome_message.message || data.welcome_message);
                    $('.chat-launcher-preview').show();
                }
            }
        } catch (error) {
            console.error('Error inicializando chat:', error);
            // Mostrar un mensaje de error en el chat
            addMessage('Error al conectar con el servidor. Por favor, recarga la página.', 'bot error-message');
        }
    }

    async function sendMessage() {
        const message = messageInput.value.trim();

        if (!message || isLoading) return;

        // Asegurar que el chat está inicializado
        if (!isChatInitialized) {
            await initializeChat();
        }

        // Mostrar mensaje del usuario
        addMessage(message, 'user');
        messageInput.value = '';

        // Mostrar indicador de escritura
        const typingId = showTypingIndicator();

        // Deshabilitar input mientras se procesa
        setLoading(true);

        try {
            const response = await fetch('{{ route("chatbot.message") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    message: message,
                    chat_conversation_id: conversationId
                })
            });

            removeTypingIndicator(typingId);

            if (!response.ok) {
                throw new Error('Error en la respuesta del servidor');
            }

            const data = await response.json();

            // Actualizar conversationId si es nuevo
            if (data.chat_conversation_id && !conversationId) {
                conversationId = data.chat_conversation_id;
            }

            // Si la respuesta incluye productos, mostrarlos de forma especial
            if (data.products && data.products.length > 0) {
                showProductsResponse(data);
            } else if (data.success) {
                addMessage(data.reply, 'bot');
            } else {
                addMessage('Error: ' + (data.reply || 'No se pudo procesar tu mensaje'), 'bot error-message');
            }

        } catch (error) {
            console.error('Error:', error);
            removeTypingIndicator(typingId);
            addMessage('Error de conexión. Por favor verifica tu internet e intenta de nuevo.', 'bot error-message');

        } finally {
            setLoading(false);
        }
    }

    // Nueva función para mostrar productos de forma estructurada
    function showProductsResponse(data) {
        const messageDiv = document.createElement('div');
        messageDiv.className = 'chat-message message-income product-message';

        let productsHTML = `
            <div class="chat-message-avatar">
                <img src="/images/avatar.png" width="50" height="50" alt="Avatar">
            </div>
            <div class="chat-message-globe product-list">
                <div class="product-header">
                    <strong>${data.mensaje || 'Productos disponibles:'}</strong>
                </div>
                <div class="product-grid">
        `;

        data.products.forEach(product => {
            productsHTML += `
                <div class="product-item">
                    <div class="product-info">
                        <div class="product-name"><strong>${product.descrip}</strong></div>
                        ${product.marca ? `<div class="product-brand">Marca: ${product.marca}</div>` : ''}
                        ${product.codprod ? `<div class="product-code">Código: ${product.codprod}</div>` : ''}
                        ${product.precio > 0 ? `<div class="product-price">Precio: $${Number(product.precio).toFixed(2)}</div>` : ''}
                        <div class="product-stock ${product.hayenexistencia > 0 ? 'in-stock' : 'out-of-stock'}">
                            ${product.hayenexistencia > 0 ? `✅ Disponible: ${product.hayenexistencia} unidades` : '❌ No disponible'}
                        </div>
                    </div>
                </div>
            `;
        });

        const now = new Date();
        const timestamp = `${now.getHours().toString().padStart(2, '0')}:${now.getMinutes().toString().padStart(2, '0')}`;

        productsHTML += `
                </div>
                ${data.total_products > 0 ? `<div class="product-footer">${data.total_products} productos encontrados</div>` : ''}
                <div class="chat__timestamp">${timestamp}</div>
            </div>
        `;

        messageDiv.innerHTML = productsHTML;
        chatMessages.appendChild(messageDiv);
        scrollToBottom();
        updateMessageCounter();
    }

    function addMessage(text, type, timestamp = null) {
        const messageDiv = document.createElement('div');

        // Convertir URLs en links
        var linkedText = text.replace(/(https?:\/\/[^\s]+)/g, '<a href="$1" target="_blank" class="text-blue-600 underline">$1</a>');

        // Si no se proporciona timestamp, usar hora actual
        if (!timestamp) {
            const now = new Date();
            const hours = now.getHours().toString().padStart(2, '0');
            const minutes = now.getMinutes().toString().padStart(2, '0');
            timestamp = `${hours}:${minutes}`;
        }

        // Ocultar mensaje de bienvenida si existe
        const welcomeDiv = document.querySelector('.chat-message-welcome');
        if (welcomeDiv && type !== 'welcome') {
            welcomeDiv.style.display = 'none';
        }

        if (type === 'user') {
            messageDiv.className = 'chat-message message-income';
            messageDiv.innerHTML = `
                <div class="chat-message-globe" style="text-align: right; float: right; background-color: #e3f2fd;">
                    ${linkedText}
                    <div class="chat__timestamp">${timestamp}</div>
                </div>
            `;
        } else {
            const isError = type.includes('error');
            messageDiv.className = 'chat-message message-income';
            messageDiv.innerHTML = `
                <div class="chat-message-avatar">
                    <img src="/images/avatar.png" width="50" height="50" alt="Avatar">
                </div>
                <div class="chat-message-globe" style="${isError ? 'border-color: #dc3545; background-color: #f8d7da;' : ''}">
                    ${linkedText}
                    <div class="chat__timestamp">${timestamp}</div>
                </div>
            `;
        }

        chatMessages.appendChild(messageDiv);
        scrollToBottom();
        updateMessageCounter();
    }

    function showTypingIndicator() {
        const typingDiv = document.createElement('div');
        typingDiv.className = 'chat-message message-income typing-indicator';
        typingDiv.id = 'typing-' + Date.now();
        typingDiv.innerHTML = `
            <div class="chat-message-avatar">
                <img src="/images/avatar.png" width="50" height="50" alt="Avatar">
            </div>
            <div class="chat-message-globe">
                <span class="dot-animation">Escribiendo</span>
            </div>
        `;
        chatMessages.appendChild(typingDiv);
        scrollToBottom();
        return typingDiv.id;
    }

    function removeTypingIndicator(id) {
        const typingDiv = document.getElementById(id);
        if (typingDiv) {
            typingDiv.remove();
        }
    }

    function scrollToBottom() {
        const containers = document.querySelectorAll('.app-conversation-parts.chat-messages__inner, .app-chat-conversations.chat-messages');
        containers.forEach(container => {
            container.scrollTop = container.scrollHeight;
        });
    }

    function setLoading(loading) {
        isLoading = loading;
        messageInput.disabled = loading;
        sendButton.disabled = loading;

        if (loading) {
            $('#sendButton').html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');
        } else {
            $('#sendButton').html('<span class="app-chat-form-submit chat-message-send__messageSubmit"></span>');
            setTimeout(() => {
                $('#message').focus().select();
            }, 100);
        }
    }

    function updateMessageCounter() {
        var counterEl = $('.bounce-once');
        var currentCount = parseInt(counterEl.html() || 0);
        counterEl.html(currentCount + 1);
        counterEl.show();
    }

    // Agregar estilos para productos
    const productStyles = document.createElement('style');
    productStyles.textContent = `
        .product-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 10px;
            margin: 10px 0;
        }
        .product-item {
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 12px;
            background: #f9f9f9;
            transition: all 0.3s ease;
        }
        .product-item:hover {
            background: #f0f0f0;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .product-name {
            font-size: 14px;
            margin-bottom: 4px;
        }
        .product-brand {
            font-size: 12px;
            color: #666;
        }
        .product-code {
            font-size: 11px;
            color: #888;
        }
        .product-price {
            font-size: 16px;
            font-weight: bold;
            color: #0072c5;
            margin: 4px 0;
        }
        .product-stock {
            font-size: 12px;
            font-weight: 600;
        }
        .in-stock {
            color: #28a745;
        }
        .out-of-stock {
            color: #dc3545;
        }
        .product-header {
            margin-bottom: 10px;
            font-size: 15px;
        }
        .product-footer {
            margin-top: 10px;
            font-size: 12px;
            color: #666;
            text-align: center;
            border-top: 1px solid #eee;
            padding-top: 8px;
        }
        .dot-animation::after {
            content: '...';
            animation: dots 1.5s steps(4, end) infinite;
            display: inline-block;
            width: 0;
            overflow: hidden;
            vertical-align: bottom;
        }
        @keyframes dots {
            0%, 20% { width: 0; }
            40% { width: 0.5em; }
            60% { width: 1em; }
            80%, 100% { width: 1.5em; }
        }
        .error-message .chat-message-globe {
            border-color: #dc3545 !important;
            background-color: #f8d7da !important;
        }
        .chat-message-welcome {
            display: block !important;
        }
    `;
    document.head.appendChild(productStyles);
</script>

</body>
</html>
