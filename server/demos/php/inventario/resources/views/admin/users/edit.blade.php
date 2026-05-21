<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-bold text-white">Editar Usuario</h2>
            <a href="{{ route('admin.users.index') }}"
                class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white font-semibold rounded-lg shadow">
                Volver
            </a>
        </div>
    </x-slot>

    <div class="py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mx-auto bg-gray-800 rounded-xl shadow-lg p-8">
            <form action="{{ route('admin.users.update', $user) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="space-y-6">
                    {{-- Nombre --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Nombre Completo *</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                            class="w-full bg-gray-700 border-gray-600 text-white rounded-lg focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror">
                        @error('name')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Correo Electrónico *</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                            class="w-full bg-gray-700 border-gray-600 text-white rounded-lg focus:ring-2 focus:ring-blue-500 @error('email') border-red-500 @enderror">
                        @error('email')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Cambiar Contraseña --}}
                    <div class="bg-gray-750 rounded-lg p-4 border border-gray-700">
                        <p class="text-white font-medium mb-4">Cambiar Contraseña (opcional)</p>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Nueva Contraseña</label>
                                <input type="password" name="password"
                                    class="w-full bg-gray-700 border-gray-600 text-white rounded-lg focus:ring-2 focus:ring-blue-500 @error('password') border-red-500 @enderror">
                                @error('password')
                                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Confirmar Nueva
                                    Contraseña</label>
                                <input type="password" name="password_confirmation"
                                    class="w-full bg-gray-700 border-gray-600 text-white rounded-lg focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>

                        <p class="text-gray-400 text-sm mt-2">Dejar en blanco para mantener la contraseña actual</p>
                    </div>

                    {{-- Rol --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Rol *</label>
                        <select name="role" required
                            class="w-full bg-gray-700 border-gray-600 text-white rounded-lg focus:ring-2 focus:ring-blue-500 @error('role') border-red-500 @enderror">
                            <option value="">Seleccione un rol...</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}" {{ old('role', $user->roles->first()?->id) == $role->id ? 'selected' : '' }}>
                                    {{ $role->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('role')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Sucursal --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Sucursal</label>
                        <select name="id_sucursal"
                            class="w-full bg-gray-700 border-gray-600 text-white rounded-lg focus:ring-2 focus:ring-blue-500 @error('id_sucursal') border-red-500 @enderror">
                            <option value="">Todas las sucursales (Administrador)</option>
                            @foreach($sucursales as $sucursal)
                                <option value="{{ $sucursal->id }}" {{ old('id_sucursal', $user->id_sucursal) == $sucursal->id ? 'selected' : '' }}>
                                    {{ $sucursal->nombre }}
                                </option>
                            @endforeach
                        </select>
                        <p class="text-gray-400 text-sm mt-1">Dejar en blanco para usuarios administradores</p>
                        @error('id_sucursal')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-8 flex justify-end space-x-4">
                    <a href="{{ route('admin.users.index') }}"
                        class="px-6 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg">Cancelar</a>
                    <button type="submit"
                        class="px-6 py-2 bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 text-white font-semibold rounded-lg shadow-lg">
                        Actualizar Usuario
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>