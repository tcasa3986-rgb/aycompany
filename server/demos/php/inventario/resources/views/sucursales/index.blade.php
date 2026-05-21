<x-app-layout>
    <x-slot name="header">
        {{-- The header content is moved to the search form container --}}
    </x-slot>

    <div class="py-8 px-4 sm:px-6 lg:px-8">
        @if(session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
                class="mb-6 bg-green-500/20 border border-green-500/50 text-green-400 px-4 py-3 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        {{-- Filtros y Buscador --}}
        <div class="mb-6 bg-gray-800 rounded-xl shadow-lg p-4">
            <div class="flex flex-col md:flex-row justify-between items-center gap-4 mb-4">
                <h2 class="text-xl font-bold text-white">Gestión de Sucursales</h2>
                <a href="{{ route('sucursales.create') }}"
                    class="px-4 py-2 bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 text-white font-semibold rounded-lg shadow-lg transition-all duration-200 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Nueva Sucursal
                </a>
            </div>

            <form action="{{ route('sucursales.index') }}" method="GET"
                class="flex flex-col md:flex-row gap-4 items-center justify-between">
                <div class="flex-1 w-full md:w-auto">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}"
                            class="block w-full pl-10 pr-3 py-2 border border-gray-600 rounded-lg leading-5 bg-gray-700 text-gray-300 placeholder-gray-400 focus:outline-none focus:bg-gray-600 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition duration-150 ease-in-out"
                            placeholder="Buscar por nombre, dirección o teléfono...">
                    </div>
                </div>

                <div class="flex flex-col md:flex-row gap-4 w-full md:w-auto">
                    <select name="estado"
                        class="block w-full pl-3 pr-10 py-2 text-base border-gray-600 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-lg bg-gray-700 text-gray-300">
                        <option value="">Todos los estados</option>
                        <option value="Activo" {{ request('estado') === 'Activo' ? 'selected' : '' }}>Activo</option>
                        <option value="Inactivo" {{ request('estado') === 'Inactivo' ? 'selected' : '' }}>Inactivo
                        </option>
                    </select>

                    <button type="submit"
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow transition-colors duration-200">
                        Filtrar
                    </button>

                    @if(request()->hasAny(['search', 'estado']))
                        <a href="{{ route('sucursales.index') }}"
                            class="px-4 py-2 bg-gray-600 hover:bg-gray-500 text-white font-semibold rounded-lg shadow transition-colors duration-200 text-center">
                            Limpiar
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <div class="bg-gray-800 rounded-xl shadow-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-750">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                                Nombre</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                                Dirección</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                                Teléfono</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                                Equipos</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                                Empleados</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                                Estado</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                                Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700">
                        @forelse($sucursales as $sucursal)
                            <tr class="hover:bg-gray-750 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm font-semibold text-white">{{ $sucursal->nombre }}</span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-300">
                                    {{ $sucursal->direccion ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                    {{ $sucursal->telefono ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                    <span class="px-2 py-1 bg-blue-500/20 text-blue-400 rounded-full text-xs font-semibold">
                                        {{ $sucursal->equipos_count ?? 0 }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                    <span
                                        class="px-2 py-1 bg-purple-500/20 text-purple-400 rounded-full text-xs font-semibold">
                                        {{ $sucursal->empleados_count ?? 0 }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($sucursal->estado === 'Activo')
                                        <span
                                            class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-500/20 text-green-400">
                                            Activo
                                        </span>
                                    @else
                                        <span
                                            class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-500/20 text-red-400">
                                            Inactivo
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center space-x-3">
                                        {{-- Ver --}}
                                        <a href="{{ route('sucursales.show', $sucursal) }}"
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
                                        <a href="{{ route('sucursales.edit', $sucursal) }}"
                                            class="text-yellow-400 hover:text-yellow-300 transition-colors p-1 hover:bg-yellow-500/10 rounded-full"
                                            title="Editar">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>

                                        {{-- Activar/Desactivar --}}
                                        <form action="{{ route('sucursales.toggle', $sucursal) }}" method="POST"
                                            class="inline-block form-toggle-{{ $sucursal->id }}">
                                            @csrf
                                            @method('PATCH')

                                            <button type="button"
                                                onclick="confirmToggle({{ $sucursal->id }}, '{{ $sucursal->nombre }}', '{{ $sucursal->estado }}')"
                                                class="{{ $sucursal->estado === 'Activo' ? 'text-red-400 hover:text-red-300 hover:bg-red-500/10' : 'text-green-400 hover:text-green-300 hover:bg-green-500/10' }} transition-colors p-1 rounded-full"
                                                title="{{ $sucursal->estado === 'Activo' ? 'Desactivar' : 'Activar' }}">
                                                @if($sucursal->estado === 'Activo')
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                                    </svg>
                                                @else
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
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
                                    No se encontraron sucursales
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-6">
            {{ $sucursales->links() }}
        </div>
    </div>

    @push('scripts')
        <script>
            function confirmToggle(id, name, status) {
                const isActive = status === 'Activo';
                const action = isActive ? 'desactivar' : 'activar';
                const type = isActive ? 'warning' : 'info';
                const confirmButtonColor = isActive ? '#EF4444' : '#10B981'; // red-500 or green-500

                Swal.fire({
                    title: `¿${action.charAt(0).toUpperCase() + action.slice(1)} Sucursal?`,
                    text: `¿Estás seguro de que deseas ${action} la sucursal "${name}"?`,
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