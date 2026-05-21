<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - CRM Delivery</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --sidebar-width: 260px;
            --sidebar-bg: #1a2035;
            --sidebar-hover: #2a3150;
            --sidebar-active: #0d6efd;
            --topbar-height: 60px;
        }
        body { background-color: #f0f2f5; font-family: 'Segoe UI', sans-serif; }

        /* SIDEBAR */
        #sidebar {
            width: var(--sidebar-width);
            min-height: 100vh;
            background: var(--sidebar-bg);
            position: fixed;
            top: 0; left: 0;
            z-index: 1040;
            transition: all 0.3s;
            overflow-y: auto;
            overflow-x: hidden;
        }
        #sidebar.collapsed { width: 70px; }
        .sidebar-brand {
            padding: 1.2rem 1.2rem;
            color: #fff;
            font-size: 1.2rem;
            font-weight: 700;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            display: flex; align-items: center; gap: 10px;
        }
        .sidebar-brand .brand-icon { font-size: 1.8rem; color: #0d6efd; }
        .sidebar-brand .brand-text { white-space: nowrap; }
        #sidebar.collapsed .brand-text { display: none; }
        .sidebar-nav .nav-link {
            color: rgba(255,255,255,0.75);
            padding: 0.65rem 1.2rem;
            border-radius: 0;
            transition: all 0.2s;
            display: flex; align-items: center; gap: 10px;
            font-size: 0.9rem;
            white-space: nowrap;
        }
        .sidebar-nav .nav-link:hover {
            background: var(--sidebar-hover);
            color: #fff;
        }
        .sidebar-nav .nav-link.active {
            background: var(--sidebar-active);
            color: #fff;
            font-weight: 600;
        }
        .sidebar-nav .nav-link i { font-size: 1.1rem; min-width: 22px; }
        #sidebar.collapsed .nav-link span { display: none; }
        .sidebar-section {
            padding: 0.6rem 1.2rem 0.3rem;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: rgba(255,255,255,0.35);
        }
        #sidebar.collapsed .sidebar-section { opacity: 0; }

        /* TOPBAR */
        #topbar {
            height: var(--topbar-height);
            background: #fff;
            position: fixed;
            top: 0;
            left: var(--sidebar-width);
            right: 0;
            z-index: 1030;
            box-shadow: 0 1px 4px rgba(0,0,0,0.1);
            display: flex; align-items: center; padding: 0 1.5rem;
            transition: left 0.3s;
        }
        #topbar.collapsed { left: 70px; }
        .topbar-toggle { background: none; border: none; font-size: 1.4rem; color: #555; cursor: pointer; margin-right: 1rem; }

        /* MAIN CONTENT */
        #main-content {
            margin-left: var(--sidebar-width);
            padding-top: calc(var(--topbar-height) + 1.5rem);
            padding-bottom: 2rem;
            padding-left: 1.5rem;
            padding-right: 1.5rem;
            transition: margin-left 0.3s;
            min-height: 100vh;
        }
        #main-content.collapsed { margin-left: 70px; }

        /* CARDS */
        .card { border: none; box-shadow: 0 2px 8px rgba(0,0,0,0.07); border-radius: 12px; }
        .card-header { background: transparent; border-bottom: 1px solid rgba(0,0,0,0.08); font-weight: 600; }

        /* KPI CARDS */
        .kpi-card { border-radius: 12px; padding: 1.2rem 1.4rem; color: #fff; position: relative; overflow: hidden; }
        .kpi-card .kpi-icon { font-size: 2.5rem; opacity: 0.3; position: absolute; right: 1rem; top: 50%; transform: translateY(-50%); }
        .kpi-card .kpi-value { font-size: 2rem; font-weight: 700; line-height: 1; }
        .kpi-card .kpi-label { font-size: 0.85rem; opacity: 0.85; margin-top: 4px; }

        /* TABLE */
        .table th { font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px; color: #888; border-bottom: 2px solid #dee2e6; }
        .table td { vertical-align: middle; font-size: 0.9rem; }
        .table-hover tbody tr:hover { background-color: rgba(13,110,253,0.04); }

        /* BADGES */
        .badge { font-size: 0.75rem; font-weight: 500; padding: 0.35em 0.7em; border-radius: 6px; }

        /* PAGINATION */
        .pagination { gap: 3px; }

        /* ALERTS */
        .alert { border-radius: 10px; border: none; }

        /* FORMS */
        .form-control, .form-select { border-radius: 8px; border-color: #dee2e6; }
        .form-control:focus, .form-select:focus { box-shadow: 0 0 0 3px rgba(13,110,253,0.15); }
        .btn { border-radius: 8px; }
        .btn-sm { border-radius: 6px; }

        /* PAGE TITLE */
        .page-title { font-size: 1.4rem; font-weight: 700; color: #1a2035; margin-bottom: 0; }
        .breadcrumb { font-size: 0.83rem; margin-bottom: 0; }

        /* AVATAR */
        .avatar-sm { width: 32px; height: 32px; border-radius: 50%; object-fit: cover; }
        .avatar-md { width: 48px; height: 48px; border-radius: 50%; object-fit: cover; }

        @media (max-width: 768px) {
            #sidebar { transform: translateX(-100%); }
            #sidebar.mobile-open { transform: translateX(0); }
            #topbar { left: 0 !important; }
            #main-content { margin-left: 0 !important; }
        }
    </style>
    @stack('styles')
</head>
<body>

<!-- SIDEBAR -->
<nav id="sidebar">
    <div class="sidebar-brand">
        <i class="bi bi-truck brand-icon"></i>
        <span class="brand-text">CRM Delivery</span>
    </div>
    <ul class="nav flex-column sidebar-nav py-2">

        <li><a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i><span>Dashboard</span>
        </a></li>

        @can('ver pedidos')
        <div class="sidebar-section">Operaciones</div>
        <li><a href="{{ route('pedidos.index') }}" class="nav-link {{ request()->routeIs('pedidos.*') ? 'active' : '' }}">
            <i class="bi bi-bag-check"></i><span>Pedidos</span>
            @php $pendientes = \App\Models\Pedido::where('estado','pendiente')->count(); @endphp
            @if($pendientes > 0) <span class="badge bg-danger ms-auto">{{ $pendientes }}</span> @endif
        </a></li>
        @endcan

        @can('ver entregas')
        <li><a href="{{ route('entregas.index') }}" class="nav-link {{ request()->routeIs('entregas.*') ? 'active' : '' }}">
            <i class="bi bi-geo-alt"></i><span>Entregas</span>
        </a></li>
        @endcan

        @can('ver pagos')
        <li><a href="{{ route('pagos.index') }}" class="nav-link {{ request()->routeIs('pagos.*') ? 'active' : '' }}">
            <i class="bi bi-cash-coin"></i><span>Pagos</span>
        </a></li>
        @endcan

        @can('ver clientes')
        <div class="sidebar-section">Gestión</div>
        <li><a href="{{ route('clientes.index') }}" class="nav-link {{ request()->routeIs('clientes.*') ? 'active' : '' }}">
            <i class="bi bi-people"></i><span>Clientes</span>
        </a></li>
        @endcan

        @can('ver repartidores')
        <li><a href="{{ route('repartidores.index') }}" class="nav-link {{ request()->routeIs('repartidores.*') ? 'active' : '' }}">
            <i class="bi bi-bicycle"></i><span>Repartidores</span>
        </a></li>
        @endcan

        @can('ver productos')
        <li><a href="{{ route('productos.index') }}" class="nav-link {{ request()->routeIs('productos.*') ? 'active' : '' }}">
            <i class="bi bi-grid"></i><span>Productos</span>
        </a></li>
        <li><a href="{{ route('categorias.index') }}" class="nav-link {{ request()->routeIs('categorias.*') ? 'active' : '' }}">
            <i class="bi bi-tags"></i><span>Categorías</span>
        </a></li>
        @endcan

        @can('ver reportes')
        <div class="sidebar-section">Análisis</div>
        <li><a href="{{ route('reportes.index') }}" class="nav-link {{ request()->routeIs('reportes.*') ? 'active' : '' }}">
            <i class="bi bi-bar-chart-line"></i><span>Reportes</span>
        </a></li>
        @endcan

        @can('ver usuarios')
        <div class="sidebar-section">Administración</div>
        <li><a href="{{ route('usuarios.index') }}" class="nav-link {{ request()->routeIs('usuarios.*') ? 'active' : '' }}">
            <i class="bi bi-person-badge"></i><span>Usuarios</span>
        </a></li>
        @endcan

        @can('ver configuracion')
        <li><a href="{{ route('configuracion.index') }}" class="nav-link {{ request()->routeIs('configuracion.*') ? 'active' : '' }}">
            <i class="bi bi-gear"></i><span>Configuración</span>
        </a></li>
        @endcan

    </ul>
</nav>

<!-- TOPBAR -->
<header id="topbar">
    <button class="topbar-toggle" id="toggleSidebar">
        <i class="bi bi-list"></i>
    </button>
    <div class="me-auto">
        @yield('breadcrumb')
    </div>
    <div class="d-flex align-items-center gap-3 ms-auto">
        <!-- Notificación pedidos pendientes -->
        @php $pendientes = \App\Models\Pedido::where('estado','pendiente')->count(); @endphp
        @if($pendientes > 0)
        <a href="{{ route('pedidos.index', ['estado' => 'pendiente']) }}" class="btn btn-sm btn-outline-warning position-relative">
            <i class="bi bi-bell"></i>
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size:0.65rem;">{{ $pendientes }}</span>
        </a>
        @endif
        <!-- Usuario -->
        <div class="dropdown">
            <a class="dropdown-toggle d-flex align-items-center gap-2 text-decoration-none text-dark" href="#" data-bs-toggle="dropdown">
                <img src="{{ auth()->user()->avatar_url }}" class="avatar-sm" alt="">
                <span class="d-none d-md-block fw-semibold" style="font-size:0.9rem;">{{ auth()->user()->name }}</span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                <li><h6 class="dropdown-header">{{ auth()->user()->nombre_rol }}</h6></li>
                <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="bi bi-person me-2"></i>Mi Perfil</a></li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item text-danger"><i class="bi bi-box-arrow-right me-2"></i>Cerrar Sesión</button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</header>

<!-- MAIN -->
<main id="main-content">
    <!-- Flash messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-3">
            <i class="bi bi-exclamation-circle me-2"></i>
            <strong>Por favor corrige los siguientes errores:</strong>
            <ul class="mb-0 mt-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @yield('content')
</main>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const sidebar      = document.getElementById('sidebar');
    const topbar       = document.getElementById('topbar');
    const mainContent  = document.getElementById('main-content');
    const toggleBtn    = document.getElementById('toggleSidebar');

    // Restaurar estado del sidebar
    if (localStorage.getItem('sidebarCollapsed') === 'true') {
        sidebar.classList.add('collapsed');
        topbar.classList.add('collapsed');
        mainContent.classList.add('collapsed');
    }

    toggleBtn.addEventListener('click', () => {
        sidebar.classList.toggle('collapsed');
        topbar.classList.toggle('collapsed');
        mainContent.classList.toggle('collapsed');
        localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
    });

    // Auto-dismiss alerts after 5s
    setTimeout(() => {
        document.querySelectorAll('.alert').forEach(el => {
            const bsAlert = bootstrap.Alert.getOrCreateInstance(el);
            bsAlert.close();
        });
    }, 5000);
</script>
@stack('scripts')
</body>
</html>
