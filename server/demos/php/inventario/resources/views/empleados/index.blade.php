<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-white">Gestión de Empleados</h2>
    </x-slot>

    <div class="py-8 px-4 sm:px-6 lg:px-8">
        {{-- Header Actions --}}
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-bold text-white md:hidden">Empleados</h2>
            <div class="flex gap-3 ml-auto">
                <a href="{{ route('empleados.export') }}"
                    class="px-4 py-2 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-lg hover:from-green-700 hover:to-emerald-700 transition flex items-center gap-2 shadow-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Exportar Excel
                </a>
                <a href="{{ route('empleados.create') }}"
                    class="px-4 py-2 bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 text-white font-semibold rounded-lg shadow-lg transition-all duration-200">
                    <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Nuevo Empleado
                </a>
            </div>
        </div>

        @if(session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
                class="mb-6 bg-green-500/20 border border-green-500/50 text-green-400 px-4 py-3 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        {{-- Stats Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6 mb-6">
            {{-- Total Empleados --}}
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg shadow p-5">
                <div class="text-white">
                    <div class="text-sm opacity-90">Total Empleados</div>
                    <div class="text-4xl font-bold mt-2">{{ $stats['total'] }}</div>
                </div>
            </div>

            {{-- Activos --}}
            <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg shadow p-5">
                <div class="text-white">
                    <div class="text-sm opacity-90">Activos</div>
                    <div class="text-4xl font-bold mt-2">{{ $stats['activos'] }}</div>
                </div>
            </div>

            {{-- Con Equipos --}}
            <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg shadow p-5">
                <div class="text-white">
                    <div class="text-sm opacity-90">Con Equipos</div>
                    <div class="text-4xl font-bold mt-2">{{ $stats['con_equipos'] }}</div>
                </div>
            </div>

            {{-- Inactivos --}}
            <div class="bg-gradient-to-r from-red-500 to-red-600 rounded-lg shadow p-5">
                <div class="text-white">
                    <div class="text-sm opacity-90">Inactivos</div>
                    <div class="text-4xl font-bold mt-2">{{ $stats['inactivos'] }}</div>
                </div>
            </div>
        </div>

        {{-- Filters --}}
        <div class="bg-gray-800 rounded-xl p-6 mb-6 shadow-lg">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="md:col-span-2">
                    <input type="text" name="search" placeholder="Buscar por DNI o nombre..."
                        value="{{ request('search') }}"
                        class="w-full bg-gray-700 border-gray-600 text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div>
                    <select name="estado"
                        class="w-full bg-gray-700 border-gray-600 text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500">
                        <option value="">Todos los estados</option>
                        <option value="Activo" {{ request('estado') == 'Activo' ? 'selected' : '' }}>Activo</option>
                        <option value="Inactivo" {{ request('estado') == 'Inactivo' ? 'selected' : '' }}>Inactivo</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit"
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                        Filtrar
                    </button>
                    <a href="{{ route('empleados.index') }}"
                        class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition-colors flex items-center justify-center">
                        Limpiar
                    </a>
                </div>
            </form>
        </div>

        {{-- Empleados Table --}}
        <div class="bg-gray-800 rounded-xl shadow-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-750">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                                DNI</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                                Nombre Completo</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                                Cargo</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                                Área</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                                Sucursal</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                                Estado</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                                Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700">
                        @forelse($empleados as $empleado)
                            <tr class="hover:bg-gray-750 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-white">
                                    {{ $empleado->dni }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                    {{ $empleado->nombreCompleto() }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                    {{ $empleado->cargo->nombre ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                    {{ $empleado->area->nombre ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                    {{ $empleado->sucursal->nombre ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $empleado->estado === 'Activo' ? 'bg-green-500/20 text-green-400' : 'bg-red-500/20 text-red-400' }}">
                                        {{ $empleado->estado }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center space-x-3">
                                        {{-- Ver --}}
                                        <a href="{{ route('empleados.show', $empleado) }}"
                                            class="text-blue-400 hover:text-blue-300 transition-colors p-1 hover:bg-blue-500/10 rounded-full"
                                            title="Ver detalles">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>

                                        {{-- Editar --}}
                                        <a href="{{ route('empleados.edit', $empleado) }}"
                                            class="text-yellow-400 hover:text-yellow-300 transition-colors p-1 hover:bg-yellow-500/10 rounded-full"
                                            title="Editar">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>

                                        {{-- Activar/Desactivar --}}
                                        <form action="{{ route('empleados.toggle', $empleado) }}" method="POST"
                                            class="inline-block form-toggle-{{ $empleado->id }}">
                                            @csrf
                                            @method('PATCH')

                                            <button type="button"
                                                onclick="confirmToggle({{ $empleado->id }}, '{{ $empleado->nombreCompleto() }}', '{{ $empleado->estado }}')"
                                                class="{{ $empleado->estado == 'Inactivo' ? 'text-green-400 hover:text-green-300 hover:bg-green-500/10' : 'text-red-400 hover:text-red-300 hover:bg-red-500/10' }} transition-colors p-1 rounded-full"
                                                title="{{ $empleado->estado == 'Inactivo' ? 'Activar' : 'Desactivar' }}">
                                                @if($empleado->estado == 'Inactivo')
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                @else
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                                    </svg>
                                                @endif
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-8 text-center text-gray-400">
                                    No se encontraron empleados registrados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($empleados->hasPages())
                <div class="px-6 py-4 border-t border-gray-700">
                    {{ $empleados->links() }}
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
        <script>
            function confirmToggle(id, name, status) {
                const isInactive = status === 'Inactivo';
                const action = isInactive ? 'activar' : 'desactivar';
                const type = isInactive ? 'info' : 'warning';
                const confirmButtonColor = isInactive ? '#10B981' : '#EF4444'; // green-500 or red-500

                Swal.fire({
                    title: `¿${action.charAt(0).toUpperCase() + action.slice(1)} Empleado?`,
                    text: `¿Estás seguro de que deseas ${action} el empleado "${name}"?`,
                    icon: type,
                    showCancelButton: true,
                    confirmButtonColor: confirmButtonColor,
                    cancelButtonColor: '#6B7280',
                    confirmButtonText: `Sí, ${action}`,
                    cancelButtonText: 'Cancelar',
                    background: '#1F2937', // gray-800
                    color: '#F3F4F6' // gray-100
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.querySelector(`.form-toggle-${id}`).submit();
                    }
                });
            }
        </script>
    @endpush
</x-app-layout>