<aside
    class="w-20 lg:w-64 bg-[#4A88F6] dark:bg-gray-800 dark:border-r dark:border-gray-700 text-white flex-shrink-0 min-h-screen hidden md:flex flex-col transition-colors">
    <!-- Logo area -->
    <div class="h-20 flex items-center justify-center border-b border-white/20 dark:border-gray-700">
        <div class="flex flex-col items-center">
            @php
                $logoPath = \App\Models\Setting::get('logo_path');
                $clinicName = \App\Models\Setting::get('clinic_name', 'CitasMédicas');
            @endphp
            @if($logoPath)
                <img src="{{ Storage::url($logoPath) }}" alt="Logo"
                    class="w-10 h-10 object-contain mb-1 rounded bg-white p-1">
            @else
                <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center mb-1">
                    <svg class="w-6 h-6 text-[#4A88F6]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z">
                        </path>
                    </svg>
                </div>
            @endif
            <span class="text-xs font-bold tracking-wider hidden lg:block">{{ $clinicName }}</span>
        </div>
    </div>

    <!-- Navigation Links -->
    <nav class="flex-1 py-6 space-y-1">

        @php
            $isPatient = auth()->user()->hasRole('patient') && !auth()->user()->hasRole(['admin', 'doctor', 'receptionist']);

            if ($isPatient) {
                $navItems = [
                    ['route' => 'portal.dashboard', 'label' => 'Mi Portal', 'icon' => 'M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z'],
                    ['route' => 'portal.appointments', 'label' => 'Mis Citas', 'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
                    ['route' => 'portal.medical-history', 'label' => 'Historial Médico', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                    ['route' => 'portal.invoices', 'label' => 'Mis Recibos', 'icon' => 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z'],
                ];
            } else {
                $navItems = [
                    ['route' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z'],
                    ['route' => 'appointments.index', 'label' => 'Citas', 'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
                    ['route' => 'waitlists.index', 'label' => 'Lista de Espera', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
                    ['route' => 'appointments.calendar', 'label' => 'Calendario', 'icon' => 'M3 10h18M3 14h18M7 3v4m10-4v4M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
                    ['route' => 'patients.index', 'label' => 'Pacientes', 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
                    ['route' => 'doctors.index', 'label' => 'Médicos', 'icon' => 'M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0M12 2a10 10 0 100 20 10 10 0 000-20z'],
                    ['route' => 'prescriptions.index', 'label' => 'Recetas Méd.', 'icon' => 'M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9.5a2.5 2.5 0 00-2.5-2.5H14'],
                    ['route' => 'diagnostic-templates.index', 'label' => 'Plantillas (CIE-10)', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                    ['route' => 'chat.index', 'label' => 'Mensajes', 'icon' => 'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z'],
                    ['route' => 'invoices.index', 'label' => 'Facturación', 'icon' => 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z'],
                    ['route' => 'insurances.index', 'label' => 'Aseguradoras', 'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z'],
                    ['route' => 'reports.index', 'label' => 'Reportes', 'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
                    ['route' => 'specialties.index', 'label' => 'Especialidades', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
                ];
            }
        @endphp

        @foreach($navItems as $item)
            @php $active = request()->routeIs($item['route']); @endphp
            <a href="{{ route($item['route']) }}"
                class="flex items-center justify-center lg:justify-start lg:px-6 py-3 relative {{ $active ? 'text-white' : 'text-white/70 hover:text-white dark:text-gray-400 dark:hover:text-gray-100' }} transition">
                @if($active)
                    <div class="absolute left-0 top-0 bottom-0 w-1 bg-white rounded-r-md"></div>
                @endif
                <svg class="w-5 h-5 lg:mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}" />
                </svg>
                <span class="hidden lg:block font-medium text-sm">{{ $item['label'] }}</span>
            </a>
        @endforeach
    </nav>

    <!-- Bottom Links -->
    <div class="pb-6 space-y-1">

        @role('admin')
        <a href="{{ route('users.index') }}"
            class="flex items-center justify-center lg:justify-start lg:px-6 py-3 relative {{ request()->routeIs('users.*') ? 'text-white' : 'text-white/70 hover:text-white dark:text-gray-400 dark:hover:text-gray-100' }} transition">
            @if(request()->routeIs('users.*'))
                <div class="absolute left-0 top-0 bottom-0 w-1 bg-white rounded-r-md"></div>
            @endif
            <svg class="w-5 h-5 lg:mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
            <span class="hidden lg:block font-medium text-sm">Usuarios</span>
        </a>
        @endrole

        <a href="{{ route('settings.backups.index') }}"
            class="flex items-center justify-center lg:justify-start lg:px-6 py-3 relative {{ request()->routeIs('settings.backups.*') ? 'text-white' : 'text-white/70 hover:text-white dark:text-gray-400 dark:hover:text-gray-100' }} transition">
            @if(request()->routeIs('settings.backups.*'))
                <div class="absolute left-0 top-0 bottom-0 w-1 bg-white rounded-r-md"></div>
            @endif
            <svg class="w-5 h-5 lg:mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
            </svg>
            <span class="hidden lg:block font-medium text-sm">Copias de Seguridad</span>
        </a>
        <a href="{{ route('settings.email_templates.index') }}"
            class="flex items-center justify-center lg:justify-start lg:px-6 py-3 relative {{ request()->routeIs('settings.email_templates.*') ? 'text-white' : 'text-white/70 hover:text-white dark:text-gray-400 dark:hover:text-gray-100' }} transition">
            @if(request()->routeIs('settings.email_templates.*'))
                <div class="absolute left-0 top-0 bottom-0 w-1 bg-white rounded-r-md"></div>
            @endif
            <svg class="w-5 h-5 lg:mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
            <span class="hidden lg:block font-medium text-sm">Plantillas de Email</span>
        </a>
        <a href="{{ route('settings.index') }}"
            class="flex items-center justify-center lg:justify-start lg:px-6 py-3 relative {{ request()->routeIs('settings.index') ? 'text-white' : 'text-white/70 hover:text-white dark:text-gray-400 dark:hover:text-gray-100' }} transition">
            @if(request()->routeIs('settings.index'))
                <div class="absolute left-0 top-0 bottom-0 w-1 bg-white rounded-r-md"></div>
            @endif
            <svg class="w-5 h-5 lg:mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
                </path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
            </svg>
            <span class="hidden lg:block font-medium text-sm">Configuración</span>
        </a>
    </div>

</aside>