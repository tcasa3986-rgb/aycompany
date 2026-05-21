<!DOCTYPE html>
<html lang="es" class="h-full bg-gray-50">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Paciente — {{ \App\Models\Setting::get('clinic_name', 'CitasMédicas') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="h-full font-[Inter]">

    {{-- Top Nav --}}
    <nav class="bg-white border-b border-gray-100 shadow-sm">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                {{-- Brand --}}
                <a href="{{ route('portal.dashboard') }}" class="flex items-center gap-3">
                    @php
                        $logoPath = \App\Models\Setting::get('logo_path');
                        $clinicName = \App\Models\Setting::get('clinic_name', 'CitasMédicas');
                    @endphp
                    @if($logoPath)
                        <img src="{{ Storage::url($logoPath) }}" alt="Logo"
                            class="w-9 h-9 object-contain rounded-xl bg-[#4A88F6] p-1">
                    @else
                        <div class="w-9 h-9 bg-[#4A88F6] rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                    @endif
                    <div>
                        <p class="text-sm font-bold text-gray-800 leading-none">
                            {{ $clinicName }}
                        </p>
                        <p class="text-xs text-gray-400 leading-none mt-0.5">Portal del Paciente</p>
                    </div>
                </a>

                {{-- Nav links --}}
                <div class="hidden md:flex items-center gap-1">
                    @php
                        $navLinks = [
                            ['route' => 'portal.dashboard', 'label' => 'Inicio'],
                            ['route' => 'portal.appointments', 'label' => 'Mis Citas'],
                            ['route' => 'portal.medical-history', 'label' => 'Historia Clínica'],
                            ['route' => 'portal.invoices', 'label' => 'Mis Facturas'],
                            ['route' => 'portal.chat.index', 'label' => 'Mensajes'],
                        ];
                    @endphp
                    @foreach($navLinks as $link)
                        <a href="{{ route($link['route']) }}"
                            class="px-4 py-2 rounded-xl text-sm font-medium transition
                                                  {{ request()->routeIs($link['route']) ? 'bg-blue-50 text-[#4A88F6]' : 'text-gray-600 hover:bg-gray-50' }}">
                            {{ $link['label'] }}
                        </a>
                    @endforeach
                </div>

                {{-- User + logout --}}
                <div class="flex items-center gap-3">
                    <span class="text-sm text-gray-600 hidden sm:block">{{ auth()->user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="text-xs text-gray-500 hover:text-red-500 border border-gray-200 hover:border-red-200 px-3 py-1.5 rounded-lg transition">
                            Salir
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    {{-- Flash messages --}}
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pt-5">
        @if(session('success'))
            <div
                class="mb-4 bg-green-50 border border-green-200 text-green-800 rounded-xl px-4 py-3 flex items-center gap-2 text-sm">
                <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-4 bg-red-50 border border-red-200 text-red-800 rounded-xl px-4 py-3 text-sm">
                {{ session('error') }}
            </div>
        @endif
    </div>

    {{-- Main content --}}
    <main class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pb-12">
        {{ $slot }}
    </main>

    @stack('scripts')
</body>

</html>