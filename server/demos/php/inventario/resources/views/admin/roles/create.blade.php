<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-bold text-white">Crear Nuevo Rol</h2>
            <a href="{{ route('admin.roles.index') }}"
                class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white font-semibold rounded-lg shadow">
                Volver
            </a>
        </div>
    </x-slot>

    <div class="py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-6xl mx-auto bg-gray-800 rounded-xl shadow-lg p-8">
            <form action="{{ route('admin.roles.store') }}" method="POST">
                @csrf

                <div class="mb-8">
                    <label class="block text-sm font-medium text-gray-300 mb-2">Nombre del Rol *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                        class="w-full bg-gray-700 border-gray-600 text-white rounded-lg focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror"
                        placeholder="Ej: Editor, Supervisor">
                    @error('name')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <h3 class="text-xl font-bold text-white mb-6">Permisos del Sistema</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($groupedPermissions as $module => $permissions)
                        <div class="bg-gray-750 border border-gray-700 rounded-xl p-5 hover:border-blue-500/50 transition">
                            <h4 class="text-lg font-semibold text-blue-400 mb-4 capitalize border-b border-gray-700 pb-2">
                                {{ $module }}
                            </h4>
                            <div class="space-y-3">
                                @foreach($permissions as $permission)
                                    <label class="flex items-center space-x-3 cursor-pointer group">
                                        <input type="checkbox" name="permissions[]" value="{{ $permission->name }}"
                                            class="w-5 h-5 rounded border-gray-600 bg-gray-700 text-blue-600 focus:ring-blue-500 group-hover:border-blue-500 transition">
                                        <span class="text-gray-300 group-hover:text-white transition-colors text-sm">
                                            {{ explode('.', $permission->name)[1] ?? $permission->name }}
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-8 flex justify-end space-x-4 border-t border-gray-700 pt-6">
                    <a href="{{ route('admin.roles.index') }}"
                        class="px-6 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg">Cancelar</a>
                    <button type="submit"
                        class="px-6 py-2 bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 text-white font-semibold rounded-lg shadow-lg">
                        Crear Rol
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>