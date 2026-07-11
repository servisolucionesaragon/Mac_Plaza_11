<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <script>
        (function () {
            var tema = localStorage.getItem('tema_crm') || 'light';
            document.documentElement.setAttribute('data-theme', tema);
        })();
    </script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'CRM') — {{ (isset($config) ? $config->nombre_tienda : null) ?? 'Tienda Celulares' }}</title>
    @if(isset($config) && $config->logo)
        <link rel="icon" type="{{ \Illuminate\Support\Facades\Storage::disk('public')->mimeType($config->logo) ?? 'image/png' }}" href="{{ asset('storage/' . $config->logo) }}?v={{ $config->updated_at->timestamp }}">
    @endif

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --sidebar-bg:    {{ $config->color_sidebar ?? '#1a0a3e' }};
            --sidebar-width: 260px;
            --accent1:       {{ $config->color_primario ?? '#a855f7' }};
            --accent2:       {{ $config->color_secundario ?? '#ec4899' }};
            --accent3:       {{ $config->color_acento ?? '#06b6d4' }};
            --gradient:      linear-gradient(135deg, var(--accent1), var(--accent2));
            --card-bg:       #ffffff;
            --page-bg:       #f4f0fb;
            --text-dark:     #1e1b4b;
            --text-muted:    #6b7280;
            --text-muted-2:  #9ca3af;
            --sidebar-text:  {{ $config->color_menu_texto ?? '#c9c3d9' }};
            --sidebar-active:#ffffff;
            --menu-active-bg:{{ $config->color_menu_activo ?? '#a855f7' }};
            --nav-hover-bg:  rgba(168,85,247,0.2);
            --btn-bg:        {{ $config->color_boton_fondo ?? '#a855f7' }};
            --btn-text:      {{ $config->color_boton_texto ?? '#ffffff' }};
            --topbar-bg:     #ffffff;
            --border-color:  #e5e7eb;
            --input-bg:      #f9fafb;
            --table-head-bg: #f8f5ff;
            --table-head-border: #e9d5ff;
            --table-hover-bg: #fdf4ff;
            --dropdown-bg:   #ffffff;
            --scrollbar-thumb: #d1d5db;
            --shadow-color:  rgba(0,0,0,.06);
        }

        html[data-theme="dark"] {
            --sidebar-bg:    #0f0c1a;
            --card-bg:       #1e1b2e;
            --page-bg:       #121018;
            --text-dark:     #e8e6f5;
            --text-muted:    #9691ac;
            --text-muted-2:  #7d7693;
            --topbar-bg:     #1a1726;
            --border-color:  #322c47;
            --input-bg:      #24202f;
            --table-head-bg: #201c30;
            --table-head-border: #3a3255;
            --table-hover-bg: #241f36;
            --dropdown-bg:   #1e1b2e;
            --scrollbar-thumb: #423a5c;
            --shadow-color:  rgba(0,0,0,.35);
        }

        * { box-sizing: border-box; }

        body {
            font-family: 'Poppins', sans-serif;
            background: var(--page-bg);
            color: var(--text-dark);
            margin: 0;
            overflow-x: hidden;
            transition: background .2s ease, color .2s ease;
        }

        /* ── SIDEBAR ───────────────────────────────────────────────── */
        .sidebar {
            position: fixed;
            top: 0; left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: var(--sidebar-bg);
            display: flex;
            flex-direction: column;
            z-index: 1000;
            transition: width .3s ease;
            overflow-y: auto;
            overflow-x: hidden;
        }

        .sidebar-brand {
            padding: 24px 20px 16px;
            border-bottom: 1px solid rgba(255,255,255,.08);
        }

        .sidebar-brand .brand-logo {
            width: 42px; height: 42px;
            background: var(--gradient);
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 20px; color: #fff;
            flex-shrink: 0;
        }

        .sidebar-brand .brand-name {
            color: #fff;
            font-weight: 700;
            font-size: 14px;
            line-height: 1.2;
        }

        .sidebar-brand .brand-sub {
            color: var(--accent1);
            font-size: 11px;
            font-weight: 400;
        }

        .user-profile {
            padding: 16px 20px;
            border-bottom: 1px solid rgba(255,255,255,.08);
        }

        .user-avatar {
            width: 40px; height: 40px;
            background: var(--gradient);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-weight: 600; font-size: 16px;
            flex-shrink: 0;
        }

        .user-name { color: #fff; font-size: 13px; font-weight: 600; }
        .user-role { color: var(--accent1); font-size: 11px; }

        .sidebar-nav { padding: 12px 0; flex: 1; }

        .nav-section-title {
            color: rgba(255,255,255,.35);
            font-size: 10px;
            font-weight: 600;
            letter-spacing: 1px;
            text-transform: uppercase;
            padding: 12px 20px 6px;
        }

        .sidebar-nav .nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 20px;
            color: var(--sidebar-text);
            text-decoration: none;
            font-size: 13.5px;
            font-weight: 400;
            border-radius: 0;
            transition: all .2s;
            position: relative;
        }

        .sidebar-nav .nav-link:hover {
            background: var(--nav-hover-bg);
            color: #fff;
        }

        .sidebar-nav .nav-link.active {
            background: var(--menu-active-bg);
            color: var(--sidebar-active);
            font-weight: 600;
        }

        .sidebar-nav .nav-link .nav-icon {
            width: 20px;
            text-align: center;
            font-size: 15px;
            flex-shrink: 0;
        }

        .sidebar-nav .nav-link .badge-count {
            margin-left: auto;
            background: var(--accent2);
            color: #fff;
            font-size: 10px;
            padding: 2px 6px;
            border-radius: 10px;
        }

        .sidebar-footer {
            padding: 16px 20px;
            border-top: 1px solid rgba(255,255,255,.08);
        }

        /* ── MAIN CONTENT ────────────────────────────────────────── */
        .main-wrapper {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* ── TOPBAR ──────────────────────────────────────────────── */
        .topbar {
            background: var(--topbar-bg);
            padding: 14px 28px;
            display: flex;
            align-items: center;
            gap: 16px;
            box-shadow: 0 1px 3px var(--shadow-color);
            position: sticky;
            top: 0;
            z-index: 100;
            transition: background .2s ease;
        }

        .topbar .search-box {
            flex: 1;
            max-width: 420px;
            position: relative;
        }

        .topbar .search-box input {
            width: 100%;
            padding: 8px 16px 8px 40px;
            border: 1.5px solid var(--border-color);
            border-radius: 24px;
            font-size: 13px;
            font-family: inherit;
            background: var(--input-bg);
            color: var(--text-dark);
            transition: border-color .2s, background .2s ease;
            outline: none;
        }

        .topbar .search-box input:focus {
            border-color: var(--accent1);
            background: var(--card-bg);
        }

        .topbar .search-box .search-icon {
            position: absolute;
            left: 14px; top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 13px;
        }

        .topbar-actions { margin-left: auto; display: flex; align-items: center; gap: 10px; }

        .topbar-btn {
            width: 38px; height: 38px;
            border-radius: 50%;
            border: none;
            background: var(--input-bg);
            color: var(--text-muted);
            display: flex; align-items: center; justify-content: center;
            cursor: pointer;
            transition: all .2s;
            position: relative;
            text-decoration: none;
        }

        .topbar-btn:hover { background: var(--accent1); color: #fff; }

        .topbar-btn .notif-dot {
            position: absolute;
            top: 7px; right: 8px;
            width: 7px; height: 7px;
            background: var(--accent2);
            border-radius: 50%;
            border: 1.5px solid #fff;
        }

        .topbar .page-title {
            font-size: 16px;
            font-weight: 600;
            color: var(--text-dark);
            margin: 0;
        }

        /* ── PAGE CONTENT ────────────────────────────────────────── */
        .page-content { padding: 24px 28px; flex: 1; }

        /* ── CARDS ───────────────────────────────────────────────── */
        .card {
            background: var(--card-bg);
            border: none;
            border-radius: 16px;
            box-shadow: 0 2px 10px var(--shadow-color);
        }

        .dropdown-menu {
            background: var(--dropdown-bg);
            color: var(--text-dark);
        }

        .dropdown-item {
            color: var(--text-dark);
        }

        .dropdown-item:hover, .dropdown-item:focus {
            background: var(--table-hover-bg);
            color: var(--text-dark);
        }

        .modal-content {
            background: var(--card-bg);
            color: var(--text-dark);
        }

        .list-group-item {
            background: var(--card-bg);
            color: var(--text-dark);
            border-color: var(--border-color);
        }

        .kpi-card {
            border-radius: 16px;
            padding: 20px;
            color: #fff;
            position: relative;
            overflow: hidden;
        }

        .kpi-card::after {
            content: '';
            position: absolute;
            right: -20px; top: -20px;
            width: 100px; height: 100px;
            background: rgba(255,255,255,.1);
            border-radius: 50%;
        }

        .kpi-card .kpi-icon {
            width: 44px; height: 44px;
            background: rgba(255,255,255,.2);
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 20px;
            margin-bottom: 12px;
        }

        .kpi-card .kpi-value {
            font-size: 26px;
            font-weight: 700;
            line-height: 1;
            margin-bottom: 4px;
        }

        .kpi-card .kpi-label {
            font-size: 12px;
            opacity: .85;
            margin-bottom: 8px;
        }

        .kpi-card .kpi-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            background: rgba(255,255,255,.2);
            border-radius: 20px;
            padding: 2px 8px;
            font-size: 11px;
        }

        .bg-grad-purple { background: linear-gradient(135deg, #a855f7, #7c3aed); }
        .bg-grad-pink   { background: linear-gradient(135deg, #ec4899, #db2777); }
        .bg-grad-cyan   { background: linear-gradient(135deg, #06b6d4, #0284c7); }
        .bg-grad-green  { background: linear-gradient(135deg, #10b981, #059669); }
        .bg-grad-orange { background: linear-gradient(135deg, #f59e0b, #d97706); }

        /* ── TABLE STYLES ────────────────────────────────────────── */
        .table { font-size: 13.5px; color: var(--text-dark); }
        .table thead th {
            background: var(--table-head-bg);
            font-weight: 600;
            color: var(--text-dark);
            border-bottom: 2px solid var(--table-head-border);
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: .5px;
        }

        .table tbody tr:hover { background: var(--table-hover-bg); }
        .table > :not(caption) > * > * { background: transparent; border-color: var(--border-color); }

        /* ── BADGES ──────────────────────────────────────────────── */
        .badge-estado {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 500;
        }

        /* ── BUTTONS ─────────────────────────────────────────────── */
        .btn-primary {
            background: var(--btn-bg);
            color: var(--btn-text);
            border: none;
            border-radius: 8px;
        }
        .btn-primary:hover { opacity: .9; filter: brightness(1.05); color: var(--btn-text); }

        .btn-outline-primary {
            border-color: var(--accent1);
            color: var(--accent1);
            border-radius: 8px;
        }
        .btn-outline-primary:hover {
            background: var(--accent1);
            color: #fff;
        }

        /* ── FORMS ───────────────────────────────────────────────── */
        .form-control, .form-select {
            background: var(--input-bg);
            color: var(--text-dark);
            border-radius: 8px;
            border-color: var(--border-color);
            font-size: 13.5px;
        }
        .form-control::placeholder { color: var(--text-muted); }
        .form-control:focus, .form-select:focus {
            background: var(--card-bg);
            color: var(--text-dark);
            border-color: var(--accent1);
            box-shadow: 0 0 0 3px rgba(168,85,247,.15);
        }

        .form-label { font-size: 13px; font-weight: 500; color: var(--text-dark); }

        /* ── DARK MODE: utilidades Bootstrap ────────────────────── */
        .text-muted { color: var(--text-muted) !important; }
        .text-dark { color: var(--text-dark) !important; }
        .breadcrumb-item.active { color: var(--text-muted); }

        /* ── ALERTS ──────────────────────────────────────────────── */
        .alert { border-radius: 10px; font-size: 13.5px; }

        /* ── PAGINATION ──────────────────────────────────────────── */
        .pagination .page-link {
            border-radius: 8px !important;
            margin: 0 2px;
            color: var(--accent1);
        }
        .pagination .page-item.active .page-link {
            background: var(--gradient);
            border-color: transparent;
        }

        /* ── RESPONSIVE ──────────────────────────────────────────── */
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
            .main-wrapper { margin-left: 0; }
        }

        /* ── SCROLLBAR ───────────────────────────────────────────── */
        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: var(--scrollbar-thumb); border-radius: 3px; }

        /* ── IMPRESIÓN: siempre en claro, sin importar el tema ──── */
        @media print {
            :root {
                --card-bg:       #ffffff !important;
                --page-bg:       #ffffff !important;
                --text-dark:     #1e1b4b !important;
                --text-muted:    #6b7280 !important;
                --text-muted-2:  #9ca3af !important;
                --topbar-bg:     #ffffff !important;
                --border-color:  #e5e7eb !important;
                --input-bg:      #ffffff !important;
                --table-head-bg: #f8f5ff !important;
                --table-head-border: #e9d5ff !important;
                --table-hover-bg: transparent !important;
                --dropdown-bg:   #ffffff !important;
            }
            body { background:#fff !important; color:#1e1b4b !important; }
            .card, .modal-content, .list-group-item, .dropdown-menu {
                background:#fff !important;
                color:#1e1b4b !important;
            }
            .table thead th { background:#f8f5ff !important; border-bottom-color:#e9d5ff !important; }
            .table tbody tr:hover { background:transparent !important; }
        }
    </style>

    @stack('styles')
</head>
<body>

<!-- ══════════ SIDEBAR ══════════ -->
<aside class="sidebar" id="sidebar">
    <!-- Brand -->
    <div class="sidebar-brand d-flex align-items-center gap-3">
        <div class="brand-logo">
            @if(isset($config) && $config->logo)
                <img src="{{ asset('storage/' . $config->logo) }}?v={{ $config->updated_at->timestamp }}" alt="Logo" style="width:100%; height:100%; object-fit:cover; border-radius:12px;">
            @else
                <i class="fas fa-mobile-alt"></i>
            @endif
        </div>
        <div>
            <div class="brand-name">{{ $config->nombre_tienda ?? 'CRM Celulares' }}</div>
            <div class="brand-sub">Panel de gestión</div>
        </div>
    </div>

    <!-- User -->
    <div class="user-profile d-flex align-items-center gap-3">
        <div class="user-avatar">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div>
        <div>
            <div class="user-name">{{ Auth::user()->name }}</div>
            <div class="user-role">{{ ucfirst(Auth::user()->rol) }}</div>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="sidebar-nav">
        @if(Auth::user()->puedeAcceder('dashboard'))
        <div class="nav-section-title">Principal</div>

        <a href="{{ route('dashboard') }}"
           class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fas fa-th-large"></i></span>
            Dashboard
        </a>
        @endif

        @if(Auth::user()->puedeAcceder('clientes') || Auth::user()->puedeAcceder('productos') || Auth::user()->esAdmin() || Auth::user()->puedeAcceder('ventas') || Auth::user()->puedeAcceder('reparaciones'))
        <div class="nav-section-title">Gestión</div>
        @endif

        @if(Auth::user()->puedeAcceder('ventas'))
        <a href="{{ route('ventas.index') }}"
           class="nav-link {{ request()->routeIs('ventas.*') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fas fa-shopping-cart"></i></span>
            Ventas
        </a>
        @endif

        @if(Auth::user()->puedeAcceder('clientes'))
        <a href="{{ route('clientes.index') }}"
           class="nav-link {{ request()->routeIs('clientes.*') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fas fa-users"></i></span>
            Clientes
        </a>
        @endif

        @if(Auth::user()->puedeAcceder('productos'))
        <a href="{{ route('productos.index') }}"
           class="nav-link {{ request()->routeIs('productos.*') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fas fa-box"></i></span>
            Inventario
        </a>
        @endif

        @if(Auth::user()->esAdmin())
        <a href="{{ route('catalogos.index') }}"
           class="nav-link {{ request()->routeIs('catalogos.*') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fas fa-tags"></i></span>
            Catálogos
        </a>
        @endif

        @if(Auth::user()->puedeAcceder('reparaciones'))
        <a href="{{ route('reparaciones.index') }}"
           class="nav-link {{ request()->routeIs('reparaciones.*') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fas fa-tools"></i></span>
            Reparaciones
            @php $pendRep = \App\Models\Reparacion::where('estado','listo')->count(); @endphp
            @if($pendRep > 0)
                <span class="badge-count">{{ $pendRep }}</span>
            @endif
        </a>
        @endif

        @if(Auth::user()->puedeAcceder('reportes'))
        <div class="nav-section-title">Reportes</div>

        <a href="{{ route('reportes.index') }}"
           class="nav-link {{ request()->routeIs('reportes.*') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fas fa-chart-bar"></i></span>
            Reportes
        </a>
        @endif

        @if(Auth::user()->esAdmin())
        <div class="nav-section-title">Sistema</div>

        <a href="{{ route('configuracion.index') }}"
           class="nav-link {{ request()->routeIs('configuracion.*') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fas fa-cog"></i></span>
            Configuración
        </a>

        <a href="{{ route('backup.index') }}"
           class="nav-link {{ request()->routeIs('backup.*') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fas fa-database"></i></span>
            Backup & Restauración
        </a>
        @endif
    </nav>

    <!-- Logout -->
    <div class="sidebar-footer">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="nav-link w-100 border-0 bg-transparent"
                    style="color: var(--sidebar-text); text-align:left;">
                <span class="nav-icon"><i class="fas fa-sign-out-alt"></i></span>
                Cerrar sesión
            </button>
        </form>
    </div>
</aside>

<!-- ══════════ MAIN WRAPPER ══════════ -->
<div class="main-wrapper">

    <!-- Topbar -->
    <header class="topbar">
        <button class="topbar-btn d-md-none" onclick="document.getElementById('sidebar').classList.toggle('open')">
            <i class="fas fa-bars"></i>
        </button>

        <div class="search-box">
            <i class="fas fa-search search-icon"></i>
            <input type="text" placeholder="Buscar clientes, productos, ventas...">
        </div>

        <div class="topbar-actions">
            <button class="topbar-btn" id="themeToggleBtn" onclick="toggleTheme()" title="Cambiar tema" type="button">
                <i class="fas fa-moon" id="themeIcon"></i>
            </button>

            @if(Auth::user()->puedeAcceder('ventas'))
            <a href="{{ route('ventas.create') }}" class="btn btn-sm btn-primary px-3" style="border-radius:20px;">
                <i class="fas fa-plus me-1"></i> Nueva Venta
            </a>
            @endif

            @php
                $stockBajoCount = \App\Models\Producto::where('activo', true)->whereColumn('stock','<=','stock_minimo')->count();
                $creditosVencidosCount = 0;
                $creditosPorVencerCount = 0;
                if (Auth::user()->puedeAcceder('ventas')) {
                    $creditosVencidosCount = \App\Models\Venta::where('es_credito', true)->where('saldo_pendiente', '>', 0)
                        ->whereDate('fecha_vencimiento', '<', now())->count();
                    $creditosPorVencerCount = \App\Models\Venta::where('es_credito', true)->where('saldo_pendiente', '>', 0)
                        ->whereDate('fecha_vencimiento', '>=', now())->whereDate('fecha_vencimiento', '<=', now()->addDays(3))->count();
                }
                $cumpleanioMesCount = 0;
                if (Auth::user()->puedeAcceder('clientes')) {
                    $cumpleanioMesCount = \App\Models\Cliente::where('activo', true)->conCumpleanioEsteMes()->count();
                }
                $totalAlertas = $stockBajoCount + $creditosVencidosCount + $creditosPorVencerCount + $cumpleanioMesCount;
            @endphp
            <div class="dropdown">
                <button class="topbar-btn" data-bs-toggle="dropdown">
                    <i class="fas fa-bell"></i>
                    @if($totalAlertas > 0)<span class="notif-dot"></span>@endif
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0" style="border-radius:12px; font-size:13px; min-width:260px;">
                    @if($totalAlertas === 0)
                        <li><span class="dropdown-item-text text-muted">Sin alertas pendientes</span></li>
                    @else
                        @if($stockBajoCount > 0)
                        <li><a class="dropdown-item" href="{{ route('productos.index', ['stock_bajo' => 1]) }}">
                            <i class="fas fa-box text-warning me-2"></i>{{ $stockBajoCount }} producto(s) con stock bajo
                        </a></li>
                        @endif
                        @if($creditosVencidosCount > 0)
                        <li><a class="dropdown-item" href="{{ route('reportes.index') }}">
                            <i class="fas fa-exclamation-circle text-danger me-2"></i>{{ $creditosVencidosCount }} crédito(s) vencido(s)
                        </a></li>
                        @endif
                        @if($creditosPorVencerCount > 0)
                        <li><a class="dropdown-item" href="{{ route('reportes.index') }}">
                            <i class="fas fa-clock text-warning me-2"></i>{{ $creditosPorVencerCount }} crédito(s) por vencer (≤3 días)
                        </a></li>
                        @endif
                        @if($cumpleanioMesCount > 0)
                        <li><a class="dropdown-item" href="{{ route('clientes.index', ['cumpleanio' => 1]) }}">
                            <i class="fas fa-birthday-cake text-danger me-2"></i>{{ $cumpleanioMesCount }} cliente(s) de cumpleaños este mes
                        </a></li>
                        @endif
                    @endif
                </ul>
            </div>

            <div class="dropdown">
                <button class="topbar-btn" data-bs-toggle="dropdown">
                    <i class="fas fa-user-circle"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0" style="border-radius:12px; font-size:13px;">
                    <li><a class="dropdown-item {{ request()->routeIs('perfil.*') ? 'active' : '' }}" href="{{ route('perfil.edit') }}"><i class="fas fa-user me-2 text-muted"></i>Mi Perfil</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger">
                                <i class="fas fa-sign-out-alt me-2"></i>Salir
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </header>

    <!-- Page Content -->
    <main class="page-content">
        {{-- Breadcrumb --}}
        @hasSection('breadcrumb')
        <nav class="mb-3" style="font-size:13px;">
            <ol class="breadcrumb mb-0" style="background:transparent; padding:0;">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" style="color:var(--accent1);">Inicio</a></li>
                @yield('breadcrumb')
            </ol>
        </nav>
        @endif

        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2" role="alert">
                <i class="fas fa-check-circle"></i>
                {{ session('success') }}
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2" role="alert">
                <i class="fas fa-exclamation-circle"></i>
                {{ session('error') }}
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </main>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
function toggleTheme() {
    var html = document.documentElement;
    var next = html.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
    html.setAttribute('data-theme', next);
    localStorage.setItem('tema_crm', next);
    actualizarIconoTema(next);
    document.dispatchEvent(new CustomEvent('themechange', { detail: { theme: next } }));
}
function actualizarIconoTema(tema) {
    var icon = document.getElementById('themeIcon');
    if (!icon) return;
    icon.classList.remove('fa-sun', 'fa-moon');
    icon.classList.add(tema === 'dark' ? 'fa-sun' : 'fa-moon');
}
document.addEventListener('DOMContentLoaded', function () {
    actualizarIconoTema(document.documentElement.getAttribute('data-theme') || 'light');
});
</script>

@stack('scripts')
</body>
</html>
