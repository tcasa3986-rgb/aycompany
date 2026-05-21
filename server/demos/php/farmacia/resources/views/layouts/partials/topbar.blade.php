<header class="bg-topbar text-white px-4 sm:px-6 lg:px-8 py-3 sm:py-4 flex items-center justify-between shadow z-10 relative">
    <div class="flex items-center gap-2 sm:gap-4">
        {{-- Botón menú móvil --}}
        <button type="button" class="lg:hidden p-1.5 sm:p-2 -ml-1 rounded-lg hover:bg-white/15 focus:outline-none transition-colors" onclick="toggleSidebar()">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>

        <div class="hidden sm:flex h-10 w-10 rounded-full bg-white/30 items-center justify-center font-bold flex-shrink-0">
            {{ strtoupper(substr(Auth::user()->name ?? 'F', 0, 1)) }}
        </div>
        <div class="flex flex-col justify-center">
            <h1 class="text-base sm:text-lg font-semibold leading-tight truncate max-w-[150px] sm:max-w-xs">{{ setting('company_name', config('app.name')) }}</h1>
            <div class="flex items-center gap-2">
                <p class="text-[10px] sm:text-xs text-white/75 uppercase tracking-wider hidden sm:block truncate">@yield('section', 'panel general')</p>
                <form action="" method="POST" id="switchBranchForm" class="hidden sm:block">
                    @csrf
                    <select onchange="this.form.action='{{ url('sucursales/switch') }}/' + this.value; this.form.submit();" class="bg-white/10 text-[10px] sm:text-xs text-white border-0 rounded py-0 px-2 h-5 focus:ring-0 cursor-pointer">
                        @foreach(Auth::user()->sucursales as $s)
                            <option value="{{ $s->id }}" @selected($s->id == Auth::user()->current_sucursal_id) class="text-gray-800">{{ $s->nombre }}</option>
                        @endforeach
                    </select>
                </form>
            </div>
        </div>
    </div>

    <div class="flex items-center gap-1 sm:gap-3">
        <button type="button" class="p-2 rounded-lg hover:bg-white/15 hidden xs:block">
            <x-icon name="grid" class="h-5 w-5" />
        </button>
        <button type="button" class="p-2 rounded-lg hover:bg-white/15">
            <x-icon name="cog" class="h-5 w-5" />
        </button>
        <div class="hidden md:flex items-center bg-white rounded-full pl-4 pr-2 py-1.5 text-gray-600 w-48 lg:w-72">
            <input type="text" placeholder="Buscar..." class="flex-1 bg-transparent outline-none text-sm placeholder:text-gray-400 border-0 focus:ring-0">
            <button class="h-8 w-8 rounded-full bg-farmacia-500 text-white flex items-center justify-center flex-shrink-0">
                <x-icon name="search" class="h-4 w-4" />
            </button>
        </div>
    </div>
</header>
