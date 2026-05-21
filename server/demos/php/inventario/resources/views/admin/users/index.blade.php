<x-app-layout>
    <div class="py-8 px-4 sm:px-6 lg:px-8">
        {{-- Header Content --}}
        <div class="flex justify-between items-center bg-gray-800 rounded-xl shadow-lg p-6 mb-6">
            <h2 class="text-2xl font-bold text-white">Gestión de Usuarios</h2>
            @can('users.create')
                <a href="{{ route('admin.users.create') }}"
                    class="px-6 py-2 bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 text-white font-semibold rounded-lg shadow-lg flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Nuevo Usuario
                </a>
            @endcan
        </div>

        {{-- Filters --}}
        <div class="bg-gray-800 rounded-xl p-6 mb-6 shadow-lg">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="md:col-span-2">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Buscar por nombre o email..."
                        class="w-full bg-gray-700 border-gray-600 text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500">
                </div>
                <!-- Changed id_rol to role -->
                <select name="role"
                    class="bg-gray-700 border-gray-600 text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500">
                    <option value="">Todos los roles</option>
                    @foreach($roles as $role)
                        <!-- Use role ID for value, name for display -->
                        <option value="{{ $role->id }}" {{ request('role') == $role->id ? 'selected' : '' }}>
                            {{ $role->name }}
                        </option>
                    @endforeach
                </select>
                <div class="flex gap-2">
                    <button type="submit"
                        class="px-6 py-2 bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 text-white font-semibold rounded-lg shadow-lg flex-1">
                        Buscar
                    </button>
                    <a href="{{ route('admin.users.index') }}"
                        class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg">
                        Limpiar
                    </a>
                </div>
            </form>
        </div>

        {{-- Messages --}}
        @if(session('success'))
            <div class="bg-green-500/20 border border-green-500 text-green-300 px-6 py-4 rounded-lg mb-6">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-500/20 border border-red-500 text-red-300 px-6 py-4 rounded-lg mb-6">
                {{ session('error') }}
            </div>
        @endif

        {{-- Table --}}
        <div class="bg-gray-800 rounded-xl shadow-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-700">
                    <thead class="bg-gray-750">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                Usuario</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                Email</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                Rol</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                Sucursal</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                Creado</th>
                            <th class="px-6 py-4 text-right text-xs font-medium text-gray-300 uppercase tracking-wider">
                                Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700">
                        @forelse($users as $user)
                            <tr class="hover:bg-gray-750 transition">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div
                                            class="h-10 w-10 flex-shrink-0 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-full flex items-center justify-center">
                                            <span
                                                class="text-white font-bold text-lg">{{ substr($user->name, 0, 1) }}</span>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-white font-medium">{{ $user->name }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-gray-300">{{ $user->email }}</td>
                                <td class="px-6 py-4">
                                    <!-- Check role existence safely -->
                                    @php
                                        $roleName = $user->getRoleNames()->first();
                                    @endphp
                                    @if($roleName === 'Administrador')
                                        <span
                                            class="px-3 py-1 bg-purple-500/20 text-purple-400 rounded-full text-xs font-semibold">Administrador</span>
                                    @else
                                        <span
                                            class="px-3 py-1 bg-blue-500/20 text-blue-400 rounded-full text-xs font-semibold">{{ $roleName ?? 'Sin rol' }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-gray-300">{{ $user->sucursal?->nombre ?? 'Todas' }}</td>
                                <td class="px-6 py-4 text-gray-300">{{ $user->created_at->format('d/m/Y') }}</td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end space-x-3">
                                        {{-- Ver --}}
                                        @can('users.view')
                                            <a href="{{ route('admin.users.show', $user) }}"
                                                class="text-blue-400 hover:text-blue-300 transition-colors p-1 hover:bg-blue-500/10 rounded-full"
                                                title="Ver detalles">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </a>
                                        @endcan

                                        {{-- Editar --}}
                                        @can('users.edit')
                                            <a href="{{ route('admin.users.edit', $user) }}"
                                                class="text-yellow-400 hover:text-yellow-300 transition-colors p-1 hover:bg-yellow-500/10 rounded-full"
                                                title="Editar">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </a>
                                        @endcan

                                        {{-- Activar/Desactivar --}}
                                        @if($user->id !== auth()->id())
                                            @can('users.toggle')
                                                <form action="{{ route('admin.users.toggle', $user) }}" method="POST"
                                                    class="inline-block"
                                                    onsubmit="return confirm('¿Estás seguro de que deseas {{ $user->activo ? 'desactivar' : 'activar' }} este usuario?');">
                                                    @csrf
                                                    @method('PATCH')

                                                    @if(!$user->activo)
                                                        <button type="submit"
                                                            class="text-green-400 hover:text-green-300 transition-colors p-1 hover:bg-green-500/10 rounded-full"
                                                            title="Activar Usuario">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                            </svg>
                                                        </button>
                                                    @else
                                                        <button type="submit"
                                                            class="text-red-400 hover:text-red-300 transition-colors p-1 hover:bg-red-500/10 rounded-full"
                                                            title="Desactivar Usuario">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                    d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                                            </svg>
                                                        </button>
                                                    @endif
                                                </form>
                                            @endcan
                                        @endif
                                    </div>
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                                    No hay usuarios registrados
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($users->hasPages())
                <div class="px-6 py-4 bg-gray-750">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>