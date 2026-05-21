@php
    $nav = [
        ['route' => 'dashboard',         'label' => 'Dashboard',  'icon' => 'home',      'permission' => 'dashboard.view'],
        ['route' => 'pos.index',         'label' => 'POS Ventas', 'icon' => 'cart',      'permission' => 'pos.use'],
        ['route' => 'cajas.index',       'label' => 'Caja',       'icon' => 'clipboard', 'permission' => 'caja.use'],
        ['route' => 'productos.index',   'label' => 'Inventario', 'icon' => 'box',       'permission' => 'inventario.view'],
        ['route' => 'categorias.index',  'label' => 'Categorías', 'icon' => 'grid',      'permission' => 'categorias.manage'],
        ['route' => 'proveedores.index', 'label' => 'Proveedores','icon' => 'users',     'permission' => 'proveedores.manage'],
        ['route' => 'compras.index',     'label' => 'Compras',    'icon' => 'box',       'permission' => 'compras.view'],
        ['route' => 'clientes.index',    'label' => 'Clientes',   'icon' => 'users',     'permission' => 'clientes.view'],
        ['route' => 'cuentas.index',     'label' => 'Créditos',   'icon' => 'clipboard', 'permission' => 'clientes.manage'],
        ['route' => 'recetas.index',     'label' => 'Recetas',    'icon' => 'pencil',    'permission' => 'recetas.view'],
        ['route' => 'ventas.index',      'label' => 'Historial',  'icon' => 'clipboard', 'permission' => 'pos.use|reportes.view'],
        ['route' => 'reportes.index',    'label' => 'Reportes',   'icon' => 'chart',     'permission' => 'reportes.view'],
        ['route' => 'settings.index',    'label' => 'Configuración', 'icon' => 'cog',    'permission' => 'settings.manage'],
    ];

    $user = Auth::user();
    $hasPerm = function ($perm) use ($user) {
        if (! $user) return false;
        foreach (explode('|', $perm) as $p) {
            if ($user->can(trim($p))) return true;
        }
        return false;
    };
@endphp

<aside class="w-64 bg-sidebar text-white shadow-lg flex flex-col h-full">
    <div class="px-6 py-5 border-b border-white/15 flex items-center justify-between">
        <div class="flex items-center gap-3">
            @if(setting('company_logo'))
                <img src="{{ setting('company_logo') }}" class="h-11 w-11 rounded-lg object-contain bg-white p-1" alt="Logo">
            @else
                <div class="h-11 w-11 rounded-full bg-white/30 flex items-center justify-center text-xl font-bold">
                    {{ substr(setting('company_name', 'F+'), 0, 1) }}
                </div>
            @endif
            <div>
                <p class="font-semibold leading-tight truncate w-24" title="{{ setting('company_name', 'ERP Farmacia') }}">
                    {{ setting('company_name', 'ERP Farmacia') }}
                </p>
                <p class="text-xs text-white/70 uppercase tracking-wide">
                    {{ Auth::user()?->getRoleNames()->first() ?? 'Invitado' }}
                </p>
            </div>
        </div>
        <button type="button" class="lg:hidden text-white/70 hover:text-white p-1 rounded hover:bg-white/10 transition-colors" onclick="toggleSidebar()">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <nav class="flex-1 px-3 py-5 space-y-1 overflow-y-auto scrollbar-thin">
        <p class="px-3 pb-2 text-xs uppercase tracking-widest text-white/60 font-bold">Menu</p>
        @foreach ($nav as $item)
            @continue(! $hasPerm($item['permission']))
            @php $active = request()->routeIs(str_replace('.index', '*', $item['route'])) || request()->routeIs($item['route']); @endphp
            <a href="{{ route($item['route']) }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition
                      {{ $active ? 'bg-white text-farmacia-700 shadow-sm' : 'text-white/90 hover:bg-white/15' }}">
                <x-icon name="{{ $item['icon'] }}" class="h-5 w-5" />
                {{ $item['label'] }}
            </a>
        @endforeach
    </nav>

    <div class="p-4 border-t border-white/15">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full text-left text-sm text-white/85 hover:text-white flex items-center gap-2">
                <x-icon name="logout" class="h-5 w-5" />
                Cerrar sesión
            </button>
        </form>
    </div>
</aside>
