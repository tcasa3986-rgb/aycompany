<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — CRM Colegio</title>

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>

    <style>
        :root {
            --sidebar-w: 260px;
            --primary:   #2563eb;
            --primary-d: #1e40af;
            --secondary: #64748b;
            --success:   #10b981;
            --warning:   #f59e0b;
            --danger:    #f43f5e;
            --info:      #0ea5e9;
            --bg:        #f8fafc;
            --card:      #ffffff;
            --text:      #0f172a;
            --muted:     #64748b;
            --border:    #f1f5f9;
            --radius:    16px;
            --shadow:    0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', system-ui, sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
        }

        /* ── SIDEBAR ── */
        .sidebar {
            position: fixed;
            top: 0; left: 0;
            width: var(--sidebar-w);
            height: 100vh;
            background: linear-gradient(180deg, #0f2460 0%, #1e3a8a 60%, #1d4ed8 100%);
            color: white;
            display: flex;
            flex-direction: column;
            z-index: 100;
            overflow-y: auto;
            transition: transform .3s;
        }
        .sidebar-header {
            padding: 24px 20px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        .profile-avatar {
            width: 52px; height: 52px;
            border-radius: 14px;
            background: rgba(255,255,255,0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            margin-bottom: 12px;
        }
        .profile-name {
            font-size: 14px;
            font-weight: 700;
            color: white;
        }
        .profile-role {
            font-size: 11px;
            color: rgba(255,255,255,0.6);
            margin-top: 2px;
        }
        .profile-stats {
            display: flex;
            gap: 16px;
            margin-top: 12px;
        }
        .profile-stat {
            text-align: center;
        }
        .profile-stat span {
            display: block;
            font-size: 15px;
            font-weight: 700;
        }
        .profile-stat small {
            font-size: 10px;
            color: rgba(255,255,255,0.5);
            text-transform: uppercase;
        }

        .sidebar-menu {
            padding: 16px 0;
            flex: 1;
        }
        .menu-section {
            padding: 12px 20px 4px;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: rgba(255,255,255,0.35);
            font-weight: 600;
        }
        .menu-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 11px 20px;
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            font-size: 13.5px;
            font-weight: 500;
            transition: all .2s;
            border-left: 3px solid transparent;
            position: relative;
        }
        .menu-item:hover {
            background: rgba(255,255,255,0.08);
            color: white;
            border-left-color: rgba(255,255,255,0.4);
        }
        .menu-item.active {
            background: rgba(255,255,255,0.15);
            color: white;
            border-left-color: #60a5fa;
        }
        .menu-item i { width: 18px; text-align: center; font-size: 15px; }
        .badge-count {
            margin-left: auto;
            background: var(--danger);
            color: white;
            font-size: 10px;
            padding: 2px 7px;
            border-radius: 10px;
            font-weight: 700;
        }

        .sidebar-footer {
            padding: 16px 20px;
            border-top: 1px solid rgba(255,255,255,0.1);
        }
        .dark-mode-toggle {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 12px;
            color: rgba(255,255,255,0.6);
            padding: 8px 0;
        }

        /* ── MAIN ── */
        .main-content {
            margin-left: var(--sidebar-w);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* ── TOPBAR ── */
        .topbar {
            background: var(--card);
            border-bottom: 1px solid var(--border);
            padding: 0 28px;
            height: 64px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 50;
            box-shadow: 0 1px 8px rgba(0,0,0,0.06);
        }
        .topbar-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .sidebar-toggle {
            display: none;
            width: 40px; height: 40px;
            border-radius: 10px;
            background: var(--bg);
            border: none;
            cursor: pointer;
            color: var(--text);
            font-size: 18px;
            align-items: center;
            justify-content: center;
        }
        .page-title {
            font-size: 18px;
            font-weight: 700;
            color: var(--text);
        }
        .breadcrumb {
            font-size: 12px;
            color: var(--muted);
        }
        .breadcrumb a { color: var(--primary-l); text-decoration: none; }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 16px;
        }
        .topbar-search {
            position: relative;
        }
        .topbar-search input {
            padding: 8px 12px 8px 36px;
            border: 1.5px solid var(--border);
            border-radius: 10px;
            font-size: 13px;
            width: 220px;
            outline: none;
            color: var(--text);
            background: var(--bg);
            transition: border-color .2s;
        }
        .topbar-search input:focus { border-color: var(--primary-l); }
        .topbar-search i {
            position: absolute;
            left: 11px; top: 50%;
            transform: translateY(-50%);
            color: var(--muted);
            font-size: 13px;
        }
        .topbar-icon {
            width: 38px; height: 38px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--bg);
            color: var(--muted);
            cursor: pointer;
            transition: all .2s;
            text-decoration: none;
            position: relative;
        }
        .topbar-icon:hover { background: var(--primary-l); color: white; }
        .notif-dot {
            position: absolute;
            top: 6px; right: 6px;
            width: 8px; height: 8px;
            background: var(--danger);
            border-radius: 50%;
            border: 2px solid white;
        }
        .topbar-user {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            padding: 6px 10px;
            border-radius: 10px;
            transition: background .2s;
        }
        .topbar-user:hover { background: var(--bg); }
        .user-avatar-sm {
            width: 34px; height: 34px;
            border-radius: 10px;
            background: linear-gradient(135deg, var(--primary), var(--primary-l));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 14px;
        }
        .user-name-sm { font-size: 13px; font-weight: 600; }
        .user-role-sm { font-size: 11px; color: var(--muted); }

        /* ── PAGE BODY ── */
        .page-body {
            padding: 28px;
            flex: 1;
        }

        /* ── CARDS ── */
        .card {
            background: var(--card);
            border-radius: 16px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06);
            overflow: hidden;
        }
        .card-header {
            padding: 18px 22px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .card-title {
            font-size: 15px;
            font-weight: 700;
            color: var(--text);
        }
        .card-body { padding: 22px; }

        /* ── STAT CARDS ── */
        .stat-card {
            background: var(--card);
            border-radius: 16px;
            padding: 20px 22px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06);
            display: flex;
            align-items: center;
            gap: 16px;
        }
        .stat-icon {
            width: 52px; height: 52px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            color: white;
            flex-shrink: 0;
        }
        .stat-icon.blue   { background: linear-gradient(135deg, #1e3a8a, #3b82f6); }
        .stat-icon.green  { background: linear-gradient(135deg, #065f46, #10b981); }
        .stat-icon.orange { background: linear-gradient(135deg, #92400e, #f59e0b); }
        .stat-icon.red    { background: linear-gradient(135deg, #7f1d1d, #ef4444); }
        .stat-icon.purple { background: linear-gradient(135deg, #4c1d95, #8b5cf6); }
        .stat-icon.cyan   { background: linear-gradient(135deg, #164e63, #06b6d4); }

        .stat-info .stat-value {
            font-size: 26px;
            font-weight: 800;
            color: var(--text);
            line-height: 1;
        }
        .stat-info .stat-label {
            font-size: 12px;
            color: var(--muted);
            margin-top: 4px;
        }
        .stat-change {
            font-size: 11px;
            margin-top: 6px;
            font-weight: 600;
        }
        .stat-change.up { color: var(--success); }
        .stat-change.down { color: var(--danger); }

        /* ── TABLA ── */
        .table-wrapper { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        thead th {
            padding: 12px 16px;
            text-align: left;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--muted);
            border-bottom: 2px solid var(--border);
            font-weight: 700;
            white-space: nowrap;
        }
        tbody td {
            padding: 13px 16px;
            font-size: 13.5px;
            border-bottom: 1px solid var(--border);
            vertical-align: middle;
        }
        tbody tr:hover { background: #f8fafc; }
        tbody tr:last-child td { border-bottom: none; }

        /* ── BADGES ── */
        .badge {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            display: inline-block;
        }
        .badge-success  { background: #d1fae5; color: #065f46; }
        .badge-warning  { background: #fef3c7; color: #92400e; }
        .badge-danger   { background: #fee2e2; color: #7f1d1d; }
        .badge-info     { background: #e0e7ff; color: #3730a3; }
        .badge-secondary{ background: #f1f5f9; color: #475569; }
        .badge-primary  { background: #dbeafe; color: #1e40af; }

        /* ── BOTONES ── */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 9px 18px;
            border-radius: 10px;
            font-size: 13.5px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            text-decoration: none;
            transition: all .2s;
        }
        .btn-primary {
            background: linear-gradient(135deg, #1e3a8a, #3b82f6);
            color: white;
        }
        .btn-primary:hover { transform: translateY(-1px); box-shadow: 0 6px 16px rgba(59,130,246,.35); }
        .btn-secondary { background: var(--bg); color: var(--text); border: 1.5px solid var(--border); }
        .btn-danger    { background: #fee2e2; color: var(--danger); }
        .btn-success   { background: #d1fae5; color: #065f46; }
        .btn-sm { padding: 6px 12px; font-size: 12px; border-radius: 8px; }
        .btn-icon { padding: 8px; width: 34px; height: 34px; justify-content: center; }

        /* ── FORMULARIOS ── */
        .form-group { margin-bottom: 20px; }
        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #475569;
            margin-bottom: 7px;
        }
        .form-control {
            width: 100%;
            padding: 10px 14px;
            border: 1.5px solid var(--border);
            border-radius: 10px;
            font-size: 14px;
            color: var(--text);
            outline: none;
            transition: border-color .2s, box-shadow .2s;
            background: white;
        }
        .form-control:focus {
            border-color: var(--primary-l);
            box-shadow: 0 0 0 3px rgba(59,130,246,0.1);
        }
        .form-control.is-invalid { border-color: var(--danger); }
        .invalid-feedback { color: var(--danger); font-size: 12px; margin-top: 4px; }

        /* ── GRID SYSTEM RESPONSIVO ── */
        .grid { display: grid; gap: 20px; }
        .grid-2 { grid-template-columns: repeat(2, 1fr); }
        .grid-3 { grid-template-columns: repeat(3, 1fr); }
        .grid-4 { grid-template-columns: repeat(4, 1fr); }
        .grid-6 { grid-template-columns: repeat(6, 1fr); }

        /* ── RESPONSIVIDAD (MEDIA QUERIES) ── */
        @media (max-width: 1024px) {
            .grid-4 { grid-template-columns: repeat(2, 1fr); }
            .grid-3 { grid-template-columns: repeat(2, 1fr); }
            .grid-6 { grid-template-columns: repeat(3, 1fr); }
        }

        @media (max-width: 768px) {
            :root { --sidebar-w: 0px; }
            .sidebar {
                transform: translateX(-100%);
                width: 260px;
                position: fixed;
                z-index: 1000;
            }
            .sidebar.show {
                transform: translateX(0);
            }
            .sidebar-overlay {
                display: none;
                position: fixed;
                inset: 0;
                background: rgba(0,0,0,0.5);
                z-index: 900;
                backdrop-filter: blur(4px);
            }
            .sidebar-overlay.show { display: block; }
            
            .main-content { margin-left: 0; }
            .sidebar-toggle { display: flex; }
            
            .grid-2, .grid-3, .grid-4, .grid-6 { grid-template-columns: 1fr; }
            
            .topbar-search { display: none; }
            .topbar { padding: 0 16px; }
            .page-body { padding: 16px; }
            
            .welcome-banner { padding: 24px; text-align: center; }
            .dashboard-stats-grid { grid-template-columns: 1fr; }
        }

        @media (max-width: 480px) {
            .topbar-user .user-info-sm { display: none; }
            .page-title { font-size: 16px; }
            .btn span { display: none; }
            .btn i { margin: 0; }
        }


        /* ── ALERTS ── */
        .alert {
            padding: 13px 16px;
            border-radius: 10px;
            font-size: 13.5px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .alert-success { background: #d1fae5; color: #065f46; border-left: 4px solid var(--success); }
        .alert-danger  { background: #fee2e2; color: #7f1d1d; border-left: 4px solid var(--danger); }
        .alert-warning { background: #fef3c7; color: #92400e; border-left: 4px solid var(--warning); }
        .alert-info    { background: #e0e7ff; color: #3730a3; border-left: 4px solid var(--info); }

        /* ── PAGINACIÓN ── */
        .pagination { display: flex; gap: 6px; align-items: center; }
        .page-link {
            padding: 7px 12px;
            border-radius: 8px;
            border: 1.5px solid var(--border);
            font-size: 13px;
            color: var(--text);
            text-decoration: none;
            transition: all .2s;
        }
        .page-link:hover, .page-link.active {
            background: var(--primary-l);
            color: white;
            border-color: var(--primary-l);
        }

    </style>
    @stack('styles')
</head>
<body>

<!-- ── SIDEBAR ── -->
<nav class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="profile-avatar">
            <i class="fas fa-user-circle"></i>
        </div>
        <div class="profile-name">{{ auth()->user()->name }}</div>
        <div class="profile-role">{{ ucfirst(auth()->user()->role) }}</div>
        <div class="profile-stats">
            <div class="profile-stat">
                <span>{{ \App\Models\Alumno::where('estado','activo')->count() }}</span>
                <small>Alumnos</small>
            </div>
            <div class="profile-stat">
                <span>{{ \App\Models\Personal::where('estado','activo')->count() }}</span>
                <small>Personal</small>
            </div>
            <div class="profile-stat">
                <span>{{ \App\Models\Mensaje::where('destinatario_id',auth()->id())->where('leido',false)->count() }}</span>
                <small>Mensajes</small>
            </div>
        </div>
    </div>

    <div class="sidebar-menu">
        <div class="menu-section">Principal</div>

        <a href="{{ route('dashboard') }}" class="menu-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="fas fa-th-large"></i> Dashboard
        </a>

        <div class="menu-section">Académico</div>

        <a href="{{ route('grados.index') }}" class="menu-item {{ request()->routeIs('grados.*') || request()->routeIs('secciones.*') ? 'active' : '' }}">
            <i class="fas fa-layer-group"></i> Grados y Sec.
        </a>
        <a href="{{ route('materias.index') }}" class="menu-item {{ request()->routeIs('materias.*') || request()->routeIs('asignaciones.*') ? 'active' : '' }}">
            <i class="fas fa-book-open"></i> Materias
        </a>
        <a href="{{ route('alumnos.index') }}" class="menu-item {{ request()->routeIs('alumnos.*') ? 'active' : '' }}">
            <i class="fas fa-user-graduate"></i> Alumnos
        </a>
        <a href="{{ route('matriculas.index') }}" class="menu-item {{ request()->routeIs('matriculas.*') ? 'active' : '' }}">
            <i class="fas fa-file-signature"></i> Matrículas
        </a>
        <a href="{{ route('notas.index') }}" class="menu-item {{ request()->routeIs('notas.*') ? 'active' : '' }}">
            <i class="fas fa-clipboard-list"></i> Calificaciones
        </a>

        <div class="menu-section">Administración</div>

        <a href="{{ route('pagos.index') }}" class="menu-item {{ request()->routeIs('pagos.*') ? 'active' : '' }}">
            <i class="fas fa-credit-card"></i> Pagos
        </a>
        <a href="{{ route('personal.index') }}" class="menu-item {{ request()->routeIs('personal.*') ? 'active' : '' }}">
            <i class="fas fa-users"></i> Personal
        </a>

        <div class="menu-section">Comunicación</div>

        <a href="{{ route('mensajes.index') }}" class="menu-item {{ request()->routeIs('mensajes.*') ? 'active' : '' }}">
            <i class="fas fa-envelope"></i> Mensajes
            @php $noLeidos = \App\Models\Mensaje::where('destinatario_id',auth()->id())->where('leido',false)->count(); @endphp
            @if($noLeidos > 0)
                <span class="badge-count">{{ $noLeidos }}</span>
            @endif
        </a>

        <div class="menu-section">Configuración</div>

        <a href="{{ route('configuracion.index') }}" class="menu-item {{ request()->routeIs('configuracion.*') ? 'active' : '' }}">
            <i class="fas fa-sliders-h"></i> Ajustes del Sistema
        </a>
        <a href="{{ route('sistema.index') }}" class="menu-item {{ request()->routeIs('sistema.*') ? 'active' : '' }}">
            <i class="fas fa-database"></i> Mantenimiento
        </a>
        <a href="{{ route('conceptos.index') }}" class="menu-item {{ request()->routeIs('conceptos.*') ? 'active' : '' }}">
            <i class="fas fa-tags"></i> Conceptos de Pago
        </a>
        <a href="{{ route('reportes.index') }}" class="menu-item {{ request()->routeIs('reportes.*') ? 'active' : '' }}">
            <i class="fas fa-chart-bar"></i> Reportes
        </a>
    </div>

    <div class="sidebar-footer">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <a href="{{ route('logout') }}"
               onclick="event.preventDefault(); this.closest('form').submit();"
               class="menu-item" style="border-left:none; color:rgba(255,255,255,0.5);">
                <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
            </a>
        </form>
    </div>
</nav>
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- ── MAIN ── -->
<div class="main-content">
    <!-- TOPBAR -->
    <header class="topbar">
        <div class="topbar-left">
            <button class="sidebar-toggle" id="btnToggle">
                <i class="fas fa-bars"></i>
            </button>
            <div class="page-title">@yield('page-title', 'Dashboard')</div>
        </div>
        <div class="topbar-right">
            <div class="topbar-search">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Buscar...">
            </div>
            <a href="{{ route('mensajes.index') }}" class="topbar-icon" title="Mensajes">
                <i class="fas fa-bell"></i>
                @if(isset($noLeidos) && $noLeidos > 0)
                    <span class="notif-dot"></span>
                @endif
            </a>
            <div class="topbar-user">
                <div class="user-avatar-sm">
                    <i class="fas fa-user"></i>
                </div>
                <div>
                    <div class="user-name-sm">{{ auth()->user()->name }}</div>
                    <div class="user-role-sm">{{ ucfirst(auth()->user()->role) }}</div>
                </div>
            </div>
        </div>
    </header>

    <!-- ALERTS -->
    <div style="padding: 0 28px; margin-top: 20px;">
        @if(session('success'))
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">
                <i class="fas fa-times-circle"></i>
                {{ session('error') }}
            </div>
        @endif
    </div>

    <!-- PAGE CONTENT -->
    <div class="page-body">
        @yield('content')
    </div>
</div>

<script>
    const btnToggle = document.getElementById('btnToggle');
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');

    if(btnToggle) {
        btnToggle.addEventListener('click', () => {
            sidebar.classList.toggle('show');
            overlay.classList.toggle('show');
        });
    }

    if(overlay) {
        overlay.addEventListener('click', () => {
            sidebar.classList.remove('show');
            overlay.classList.remove('show');
        });
    }
</script>
@stack('scripts')
</body>
</html>
