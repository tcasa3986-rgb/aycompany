<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Sistema Integral de Gestión para Laboratorios Clínicos - Resultados, Órdenes y Pacientes.">
    <meta name="author" content="LabSalud Tech">
    <meta name="theme-color" content="#0b132e">
    <title>{{ config('app.name', 'Laboratorio Clínico') }} - @yield('title', 'Dashboard')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    @stack('styles')
</head>
<body>
    <div class="app-container">
        <!-- Sidebar Backdrop for mobile -->
        <div class="sidebar-backdrop" id="sidebarBackdrop"></div>
        
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <a href="{{ route('dashboard') }}" class="brand">
                    <div class="brand-icon"><i class="fa-solid fa-microscope text-white"></i></div>
                    <span class="text-gradient" style="font-family: 'Outfit'; font-size: 1.5rem;">LabSalud</span>
                </a>
            </div>
            
            <nav class="sidebar-nav">
                <div class="nav-label">Principal</div>
                
                <div class="nav-item">
                    <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="fa-solid fa-chart-pie nav-icon"></i>
                        <span>Dashboard</span>
                    </a>
                </div>

                <div class="nav-label">Recepción</div>
                
                <div class="nav-item">
                    <a href="{{ route('pacientes.index') }}" class="nav-link {{ request()->routeIs('pacientes.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-users nav-icon"></i>
                        <span>Pacientes</span>
                    </a>
                </div>
                
                <div class="nav-item">
                    <a href="{{ route('ordenes.index') }}" class="nav-link {{ request()->routeIs('ordenes.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-file-medical nav-icon"></i>
                        <span>Órdenes Médicas</span>
                    </a>
                </div>

                <div class="nav-item">
                    <a href="{{ route('citas.index') }}" class="nav-link {{ request()->routeIs('citas.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-calendar-check nav-icon"></i>
                        <span>Agenda / Citas</span>
                    </a>
                </div>

                <div class="nav-label">Laboratorio</div>
                
                <div class="nav-item">
                    <a href="{{ route('muestras.index') }}" class="nav-link {{ request()->routeIs('muestras.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-vial nav-icon"></i>
                        <span>Toma de Muestras</span>
                    </a>
                </div>
                
                <div class="nav-item">
                    <a href="{{ route('resultados.index') }}" class="nav-link {{ request()->routeIs('resultados.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-flask-vial nav-icon"></i>
                        <span>Resultados</span>
                    </a>
                </div>

                <div class="nav-item">
                    <a href="{{ route('reactivos.index') }}" class="nav-link {{ request()->routeIs('reactivos.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-boxes-stacked nav-icon"></i>
                        <span>Inventario Reactivos</span>
                    </a>
                </div>
                
                <div class="nav-label">Administración</div>
                
                <div class="nav-item">
                    <a href="{{ route('facturas.index') }}" class="nav-link {{ request()->routeIs('facturas.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-file-invoice-dollar nav-icon"></i>
                        <span>Facturación</span>
                    </a>
                </div>

                <div class="nav-item">
                    <a href="{{ route('pruebas.index') }}" class="nav-link {{ request()->routeIs('pruebas.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-list-check nav-icon"></i>
                        <span>Catálogo de Pruebas</span>
                    </a>
                </div>

                <div class="nav-item">
                    <a href="{{ route('medicos.index') }}" class="nav-link {{ request()->routeIs('medicos.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-user-doctor nav-icon"></i>
                        <span>Médicos Referidores</span>
                    </a>
                </div>

                <div class="nav-item">
                    <a href="{{ route('convenios.index') }}" class="nav-link {{ request()->routeIs('convenios.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-handshake nav-icon"></i>
                        <span>Convenios</span>
                    </a>
                </div>

                <div class="nav-item">
                    <a href="{{ route('areas.index') }}" class="nav-link {{ request()->routeIs('areas.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-sitemap nav-icon"></i>
                        <span>Áreas Lab.</span>
                    </a>
                </div>

                <div class="nav-label">Sistema</div>

                <div class="nav-item">
                    <a href="{{ route('usuarios.index') }}" class="nav-link {{ request()->routeIs('usuarios.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-users-gear nav-icon"></i>
                        <span>Usuarios</span>
                    </a>
                </div>

                <div class="nav-item">
                    <a href="{{ route('configuracion.index') }}" class="nav-link {{ request()->routeIs('configuracion.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-gear nav-icon"></i>
                        <span>Configuración</span>
                    </a>
                </div>
                
                <div class="nav-item">
                    <a href="{{ route('sistema.mantenimiento') }}" class="nav-link {{ request()->routeIs('sistema.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-database nav-icon"></i>
                        <span>Mantenimiento</span>
                    </a>
                </div>
                
                <div class="nav-item" style="margin-top: 30px;">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="nav-link" style="width: 100%; border: none; background: transparent; text-align: left;">
                            <i class="fa-solid fa-sign-out-alt nav-icon text-danger"></i>
                            <span class="text-danger">Cerrar Sesión</span>
                        </button>
                    </form>
                </div>
            </nav>

        </aside>

        <!-- Main Wrapper -->
        <main class="main-wrapper">
            <!-- Topbar -->
            <header class="topbar">
                <button class="mobile-toggle" id="mobileToggle">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <div class="topbar-search">
                    <i class="fa-solid fa-search text-muted"></i>
                    <input type="text" placeholder="Buscar paciente, orden (Ctrl+K)...">
                </div>
                
                <div class="topbar-actions">
                    <button class="action-btn">
                        <i class="fa-solid fa-bell"></i>
                        <span class="badge">3</span>
                    </button>
                    
                    <button class="action-btn">
                        <i class="fa-solid fa-envelope"></i>
                    </button>
                    
                    <div class="user-profile">
                        <div class="user-info" style="text-align: right;">
                            <span class="user-name">{{ Auth::user()->name ?? 'Usuario' }}</span>
                            <span class="user-role">{{ Auth::user() ? Auth::user()->roles->first()->name ?? 'Admin' : 'Admin' }}</span>
                        </div>
                        <div class="avatar">
                            {{ substr(Auth::user()->name ?? 'U', 0, 1) }}
                        </div>
                        <i class="fa-solid fa-chevron-down text-muted" style="font-size: 0.8rem;"></i>
                    </div>
                </div>
            </header>

            <!-- Content Area -->
            <div class="content-area">
                @yield('content')
            </div>
        </main>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Lógica de Sidebar Móvil
        const mobileToggle = document.getElementById('mobileToggle');
        const sidebarBackdrop = document.getElementById('sidebarBackdrop');
        const sidebar = document.querySelector('.sidebar');

        if (mobileToggle && sidebarBackdrop && sidebar) {
            mobileToggle.addEventListener('click', () => {
                sidebar.classList.add('mobile-open');
                sidebarBackdrop.classList.add('active');
            });

            sidebarBackdrop.addEventListener('click', () => {
                sidebar.classList.remove('mobile-open');
                sidebarBackdrop.classList.remove('active');
            });
        }
    </script>
    @stack('scripts')
</body>
</html>
