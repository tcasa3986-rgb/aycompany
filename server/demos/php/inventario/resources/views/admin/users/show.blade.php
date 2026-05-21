<x-app-layout>
    <div class="py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-5xl mx-auto space-y-6">
            {{-- Header Content --}}
            <div class="flex justify-between items-center bg-gray-800 rounded-xl shadow-lg p-6">
                <h2 class="text-2xl font-bold text-white">Detalle de Usuario</h2>
                <div class="flex gap-3">
                    @can('users.edit')
                        <a href="{{ route('admin.users.edit', $user) }}"
                            class="px-4 py-2 bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 text-white font-semibold rounded-lg shadow-lg flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Editar
                        </a>
                    @endcan
                    <a href="{{ route('admin.users.index') }}"
                        class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white font-semibold rounded-lg shadow flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Volver
                    </a>
                </div>
            </div>

            {{-- Información Principal --}}
            <div class="bg-gray-800 rounded-xl shadow-lg p-8">
                <div class="flex items-center mb-6">
                    <div
                        class="h-20 w-20 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-full flex items-center justify-center">
                        <span class="text-white font-bold text-3xl">{{ substr($user->name, 0, 1) }}</span>
                    </div>
                    <div class="ml-6">
                        <h3 class="text-2xl font-bold text-white">{{ $user->name }}</h3>
                        <p class="text-gray-400">{{ $user->email }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div>
                        <p class="text-gray-400 text-sm mb-1">Rol</p>
                        @php
                            $roleName = $user->getRoleNames()->first();
                        @endphp
                        @if($roleName === 'Administrador')
                            <span
                                class="inline-block px-3 py-1 bg-purple-500/20 text-purple-400 rounded-full text-sm font-semibold">Administrador</span>
                        @else
                            <span
                                class="inline-block px-3 py-1 bg-blue-500/20 text-blue-400 rounded-full text-sm font-semibold">{{ $roleName ?? 'Sin rol' }}</span>
                        @endif
                    </div>

                    <div>
                        <p class="text-gray-400 text-sm mb-1">Sucursal Asignada</p>
                        <p class="text-white font-medium">{{ $user->sucursal?->nombre ?? 'Todas las sucursales' }}</p>
                    </div>

                    <div>
                        <p class="text-gray-400 text-sm mb-1">Fecha de Registro</p>
                        <p class="text-white font-medium">{{ $user->created_at->format('d/m/Y H:i') }}</p>
                    </div>

                    <div>
                        <p class="text-gray-400 text-sm mb-1">Última Actualización</p>
                        <p class="text-white font-medium">{{ $user->updated_at->format('d/m/Y H:i') }}</p>
                    </div>

                    <div>
                        <p class="text-gray-400 text-sm mb-1">Estado de la Cuenta</p>
                        <span
                            class="inline-block px-3 py-1 bg-green-500/20 text-green-400 rounded-full text-sm font-semibold">Activo</span>
                    </div>
                </div>
            </div>

            {{-- Información Adicional --}}
            <div class="bg-gray-800 rounded-xl shadow-lg p-8">
                <h3 class="text-xl font-bold text-white mb-6">Información del Sistema</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-gray-750 rounded-lg p-4 border border-gray-700">
                        <p class="text-gray-400 text-sm mb-2">ID de Usuario</p>
                        <p class="text-white font-mono">{{ $user->id }}</p>
                    </div>

                    <div class="bg-gray-750 rounded-lg p-4 border border-gray-700">
                        <p class="text-gray-400 text-sm mb-2">Tiempo como Usuario</p>
                        <p class="text-white">{{ $user->created_at->diffForHumans() }}</p>
                    </div>
                </div>
            </div>

            {{-- Permisos y Acceso --}}
            <div class="bg-gray-800 rounded-xl shadow-lg p-8">
                <h3 class="text-xl font-bold text-white mb-6">Permisos y Acceso</h3>
                <div class="space-y-4">
                    @if($user->isAdmin())
                        <div class="flex items-start">
                            <svg class="w-6 h-6 text-green-400 mt-0.5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <div class="ml-3">
                                <p class="text-white font-medium">Acceso Total al Sistema</p>
                                <p class="text-gray-400 text-sm">Puede ver y gestionar todas las sucursales</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <svg class="w-6 h-6 text-green-400 mt-0.5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <div class="ml-3">
                                <p class="text-white font-medium">Panel de Administración</p>
                                <p class="text-gray-400 text-sm">Puede gestionar usuarios y configuración del sistema</p>
                            </div>
                        </div>
                    @else
                        <div class="flex items-start">
                            <svg class="w-6 h-6 text-blue-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                            <div class="ml-3">
                                <p class="text-white font-medium">Acceso Restringido</p>
                                <p class="text-gray-400 text-sm">Solo puede ver información de:
                                    {{ $user->sucursal?->nombre ?? 'ninguna sucursal asignada' }}
                                </p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>