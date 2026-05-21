<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body class="font-sans antialiased bg-gray-900">
    <div x-data="{ sidebarOpen: false }" class="flex min-h-screen relative"
        @toggle-sidebar.window="sidebarOpen = !sidebarOpen">
        {{-- Sidebar --}}
        <aside x-cloak :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
            class="fixed inset-y-0 left-0 z-50 w-64 bg-gray-800 border-r border-gray-700 transform transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-0 flex flex-col h-full shadow-xl lg:shadow-none">

            <!-- Logo -->
            <div class="flex items-center justify-center h-20 bg-gray-900 border-b border-gray-700 shrink-0 px-4">
                @if(isset($setting) && $setting->empresa_logo)
                    <div class="flex flex-col items-center gap-2">
                        <img src="{{ asset('storage/' . $setting->empresa_logo) }}" alt="Logo"
                            class="h-10 w-auto object-contain">
                        <h1
                            class="text-sm font-bold bg-gradient-to-r from-blue-400 to-cyan-400 bg-clip-text text-transparent text-center leading-tight">
                            {{ $setting->empresa_nombre }}
                        </h1>
                    </div>
                @else
                    <h1
                        class="text-xl font-bold bg-gradient-to-r from-blue-400 to-cyan-400 bg-clip-text text-transparent text-center">
                        {{ $setting->empresa_nombre ?? 'Sistema Inventario TI' }}
                    </h1>
                @endif
            </div>

            <!-- Navigation -->
            <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto custom-scrollbar">
                <a href="{{ route('dashboard') }}"
                    class="flex items-center px-4 py-3 text-gray-300 rounded-lg hover:bg-gray-700 hover:text-white transition-colors {{ request()->routeIs('dashboard') ? 'bg-gradient-to-r from-blue-600 to-cyan-600 text-white' : '' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    Dashboard
                </a>

                <a href="{{ route('equipos.index') }}"
                    class="flex items-center px-4 py-3 text-gray-300 rounded-lg hover:bg-gray-700 hover:text-white transition-colors {{ request()->routeIs('equipos.*') ? 'bg-gradient-to-r from-blue-600 to-cyan-600 text-white' : '' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                    Equipos
                </a>

                <a href="{{ route('empleados.index') }}"
                    class="flex items-center px-4 py-3 text-gray-300 rounded-lg hover:bg-gray-700 hover:text-white transition-colors {{ request()->routeIs('empleados.*') ? 'bg-gradient-to-r from-blue-600 to-cyan-600 text-white' : '' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    Empleados
                </a>

                {{-- Asignaciones --}}
                <a href="{{ route('asignaciones.index') }}"
                    class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-white rounded-lg transition {{ request()->routeIs('asignaciones.*') ? 'bg-gradient-to-r from-blue-600 to-cyan-600 text-white' : '' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                    <span>Asignaciones</span>
                </a>

                {{-- Reparaciones --}}
                <a href="{{ route('reparaciones.index') }}"
                    class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-white rounded-lg transition {{ request()->routeIs('reparaciones.*') ? 'bg-gradient-to-r from-blue-600 to-cyan-600 text-white' : '' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span>Reparaciones</span>
                </a>

                {{-- Bajas --}}
                <a href="{{ route('bajas.index') }}"
                    class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-white rounded-lg transition {{ request()->routeIs('bajas.*') ? 'bg-gradient-to-r from-blue-600 to-cyan-600 text-white' : '' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    <span>Bajas</span>
                </a>

                {{-- Reportes --}}
                <a href="{{ route('reportes.index') }}"
                    class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-white rounded-lg transition {{ request()->routeIs('reportes.*') ? 'bg-gradient-to-r from-blue-600 to-cyan-600 text-white' : '' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span>Reportes PDF</span>
                </a>

                @if(auth()->user()->isAdmin())
                    <!-- Administración Dropdown -->
                    <div x-data="{ open: {{ request()->routeIs('admin.*') ? 'true' : 'false' }} }">
                        <button @click="open = !open"
                            class="flex items-center justify-between w-full px-4 py-3 text-gray-300 rounded-lg hover:bg-gray-700 hover:text-white transition-colors {{ request()->routeIs('admin.*') ? 'bg-gradient-to-r from-purple-600 to-pink-600 text-white' : '' }}">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                                Administración
                            </div>
                            <svg class="w-4 h-4 transition-transform" :class="open ? 'rotate-180' : ''" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="open" x-cloak class="ml-4 mt-2 space-y-2">
                            <a href="{{ route('admin.users.index') }}"
                                class="flex items-center px-4 py-2 text-sm text-gray-400 rounded-lg hover:bg-gray-700 hover:text-white transition-colors {{ request()->routeIs('admin.users.*') ? 'bg-gray-700 text-white' : '' }}">
                                Usuarios
                            </a>
                            <a href="{{ route('admin.settings.index') }}"
                                class="flex items-center px-4 py-2 text-sm text-gray-400 rounded-lg hover:bg-gray-700 hover:text-white transition-colors {{ request()->routeIs('admin.settings.*') ? 'bg-gray-700 text-white' : '' }}">
                                Configuración
                            </a>
                        </div>
                    </div>
                @endif

                <!-- Catálogos Dropdown -->
                <div
                    x-data="{ open: {{ request()->routeIs('sucursales.*', 'marcas.*', 'tipos-equipo.*', 'modelos.*', 'areas.*', 'cargos.*') ? 'true' : 'false' }} }">
                    <button @click="open = !open"
                        class="flex items-center justify-between w-full px-4 py-3 text-gray-300 rounded-lg hover:bg-gray-700 hover:text-white transition-colors">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                            Catálogos
                        </div>
                        <svg class="w-4 h-4 transition-transform" :class="open ? 'rotate-180' : ''" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="open" x-cloak class="ml-4 mt-2 space-y-2">
                        <a href="{{ route('sucursales.index') }}"
                            class="flex items-center px-4 py-2 text-sm text-gray-400 rounded-lg hover:bg-gray-700 hover:text-white transition-colors {{ request()->routeIs('sucursales.*') ? 'bg-gray-700 text-white' : '' }}">Sucursales</a>
                        <a href="{{ route('areas.index') }}"
                            class="flex items-center px-4 py-2 text-sm text-gray-400 rounded-lg hover:bg-gray-700 hover:text-white transition-colors {{ request()->routeIs('areas.*') ? 'bg-gray-700 text-white' : '' }}">Áreas</a>
                        <a href="{{ route('cargos.index') }}"
                            class="flex items-center px-4 py-2 text-sm text-gray-400 rounded-lg hover:bg-gray-700 hover:text-white transition-colors {{ request()->routeIs('cargos.*') ? 'bg-gray-700 text-white' : '' }}">Cargos</a>
                        <a href="{{ route('marcas.index') }}"
                            class="flex items-center px-4 py-2 text-sm text-gray-400 rounded-lg hover:bg-gray-700 hover:text-white transition-colors {{ request()->routeIs('marcas.*') ? 'bg-gray-700 text-white' : '' }}">Marcas</a>
                        <a href="{{ route('tipos-equipo.index') }}"
                            class="flex items-center px-4 py-2 text-sm text-gray-400 rounded-lg hover:bg-gray-700 hover:text-white transition-colors {{ request()->routeIs('tipos-equipo.*') ? 'bg-gray-700 text-white' : '' }}">Tipos
                            de Equipo</a>
                        <a href="{{ route('modelos.index') }}"
                            class="flex items-center px-4 py-2 text-sm text-gray-400 rounded-lg hover:bg-gray-700 hover:text-white transition-colors {{ request()->routeIs('modelos.*') ? 'bg-gray-700 text-white' : '' }}">Modelos</a>
                    </div>
                </div>
            </nav>

            <!-- User Menu -->
            <div class="p-4 border-t border-gray-700 shrink-0">
                <div x-data="{ userMenuOpen: false }" class="relative">
                    <button @click="userMenuOpen = !userMenuOpen"
                        class="flex items-center w-full px-4 py-3 text-gray-300 rounded-lg hover:bg-gray-700 hover:text-white transition-colors">
                        <div class="flex items-center flex-1 min-w-0">
                            <div
                                class="w-8 h-8 bg-gradient-to-r from-blue-600 to-cyan-600 rounded-full flex items-center justify-center shrink-0">
                                <span
                                    class="text-sm font-semibold text-white">{{ substr(auth()->user()->name, 0, 1) }}</span>
                            </div>
                            <div class="ml-3 text-left truncate">
                                <p class="text-sm font-medium truncate">{{ auth()->user()->name }}</p>
                                <p class="text-xs text-gray-400 truncate">{{ auth()->user()->email }}</p>
                            </div>
                        </div>
                        <svg class="w-4 h-4 shrink-0 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div x-show="userMenuOpen" @click.away="userMenuOpen = false" x-cloak style="display: none;"
                        class="absolute bottom-full left-0 right-0 mb-2 bg-gray-700 rounded-lg shadow-lg overflow-hidden z-50">
                        <a href="{{ route('profile.edit') }}"
                            class="block px-4 py-2 text-sm text-gray-300 hover:bg-gray-600">Perfil</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="block w-full text-left px-4 py-2 text-sm text-gray-300 hover:bg-gray-600">Cerrar
                                Sesión</button>
                        </form>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="lg:pl-0 flex flex-col flex-1 w-full relative z-0">
            <!-- Mobile Header (Solo visible en movil, contiene el botón hamburguesa del Sidebar) -->
            {{-- EL HEADER SE MANEJA EN NAVIGATION.BLADE.PHP --}}

            <!-- Page Header -->
            @if (isset($header))
                <header class="bg-gray-800 shadow border-b border-gray-700">
                    <div class="px-4 py-4 sm:px-6 lg:px-8 flex justify-between items-center">
                        <div class="flex items-center gap-4">
                            <!-- Mobile Hamburger -->
                            <button @click="sidebarOpen = true" class="text-gray-400 focus:outline-none lg:hidden">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16"></path>
                                </svg>
                            </button>
                            {{ $header }}
                        </div>

                        <!-- System Title on Right -->
                        @if(isset($setting) && $setting->empresa_nombre)
                            <span
                                class="text-gray-400 text-sm font-medium hidden sm:block">{{ $setting->empresa_nombre }}</span>
                        @endif
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main class="flex-1 w-full overflow-y-auto">
                {{ $slot }}
            </main>
        </div>

        <!-- Mobile Overlay -->
        <div x-show="sidebarOpen" @click="sidebarOpen = false" x-cloak style="display: none;"
            x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-300"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-40 bg-gray-900 bg-opacity-50 lg:hidden">
        </div>
    </div>

    {{-- Chart.js Library --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

    {{-- SweetAlert2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @stack('scripts')
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <script>
        // SweetAlert2 Toast Mixin
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });

        // Handle Session Flashes
        @if (session('success'))
            Toast.fire({
                icon: 'success',
                title: '{{ session('success') }}'
            });
        @endif

        @if (session('error'))
            Toast.fire({
                icon: 'error',
                title: '{{ session('error') }}'
            });
        @endif
    </script>

    <script>
        // Asegurar que el sidebar siempre esté abierto en pantallas grandes
        document.addEventListener('alpine:init', () => {
            // Al cargar la página, verificar el tamaño de pantalla
            if (window.innerWidth >= 1024) {
                Alpine.store('sidebarOpen', true);
            }
        });

        // Manejar cambios de tamaño de ventana
        window.addEventListener('resize', () => {
            if (window.innerWidth >= 1024) {
                // En pantallas grandes, forzar sidebar abierto
                const sidebar = document.querySelector('[x-data]').__x.$data;
                if (sidebar.sidebarOpen !== undefined) {
                    sidebar.sidebarOpen = true;
                }
            }
        });
    </script>
</body>

</html>