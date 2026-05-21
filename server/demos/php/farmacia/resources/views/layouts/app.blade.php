<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') · {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    {{-- Tailwind por CDN (modo sin Node) --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: { extend: { colors: {
                farmacia: {50:'#ecfdf6',100:'#d1fae9',200:'#a7f0d4',300:'#6fe0bb',400:'#3fc99e',500:'#22b388',600:'#199172',700:'#16735c',800:'#155b4b',900:'#114a3e'},
                topbar:'#2a8f88', sidebar:'#46b8a4'
            } } }
        }
    </script>
    <style type="text/tailwindcss">
        @layer components {
            .btn-primary  { @apply inline-flex items-center justify-center rounded-lg bg-farmacia-500 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-farmacia-600 transition; }
            .btn-secondary{ @apply inline-flex items-center justify-center rounded-lg bg-white px-4 py-2 text-sm font-semibold text-farmacia-700 ring-1 ring-farmacia-200 hover:bg-farmacia-50 transition; }
            .btn-danger   { @apply inline-flex items-center justify-center rounded-lg bg-rose-500 px-3 py-1.5 text-xs font-semibold text-white hover:bg-rose-600 transition; }
            .input        { @apply block w-full rounded-lg border border-gray-200 bg-white text-sm shadow-sm focus:border-farmacia-400 focus:ring-farmacia-400 px-3 py-2; }
            .label        { @apply block text-sm font-medium text-gray-700 mb-1; }
            .card         { @apply bg-white rounded-2xl shadow-sm border border-gray-100; }
            .card-pad     { @apply p-5; }
            .table-base   { @apply min-w-full divide-y divide-gray-200 text-sm; }
            .badge        { @apply inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium; }
        }
        .table-base thead th { @apply px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500; }
        .table-base tbody td { @apply px-4 py-3 text-gray-700; }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
</head>
<body class="bg-gray-50 font-sans text-gray-800 antialiased">

    <div class="flex h-screen relative w-full overflow-hidden bg-sidebar">
        {{-- Overlay móvil --}}
        <div id="sidebar-overlay" class="fixed inset-0 bg-gray-900/50 z-20 hidden lg:hidden transition-opacity" onclick="toggleSidebar()"></div>

        {{-- Sidebar --}}
        <div id="sidebar" class="fixed inset-y-0 left-0 z-30 transform -translate-x-full lg:translate-x-0 lg:static lg:h-full transition-transform duration-300 ease-in-out flex-shrink-0">
            @include('layouts.partials.sidebar')
        </div>

        <div class="flex-1 flex flex-col min-w-0 h-full overflow-y-auto bg-gray-50">
            {{-- Top bar --}}
            @include('layouts.partials.topbar')

            <main class="flex-1 p-4 sm:p-6 lg:p-8 bg-gray-50">
                @if (session('success'))
                    <div class="mb-4 rounded-lg bg-emerald-50 border border-emerald-200 px-4 py-3 text-emerald-800">
                        {{ session('success') }}
                    </div>
                @endif
                @if (session('error'))
                    <div class="mb-4 rounded-lg bg-rose-50 border border-rose-200 px-4 py-3 text-rose-800">
                        {{ session('error') }}
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            
            if (sidebar.classList.contains('-translate-x-full')) {
                // Abrir
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.remove('hidden');
            } else {
                // Cerrar
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
            }
        }
    </script>

    @stack('scripts')
</body>
</html>
