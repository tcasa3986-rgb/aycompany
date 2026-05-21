<x-app-layout>
    <x-slot name="header">Cambiar Rol — {{ $user->name }}</x-slot>

    <div class="max-w-lg mx-auto">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">

            {{-- User info --}}
            <div class="flex items-center gap-4 mb-8 pb-6 border-b border-gray-100">
                <img src="{{ $user->avatar_url }}"
                     class="w-16 h-16 rounded-full object-cover border-2 border-blue-100"
                     alt="{{ $user->name }}">
                <div>
                    <p class="font-semibold text-gray-900 text-lg">{{ $user->name }}</p>
                    <p class="text-gray-400 text-sm">{{ $user->email }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">Rol actual:
                        <span class="font-medium text-gray-600">{{ $user->role_label }}</span>
                    </p>
                </div>
            </div>

            @if($errors->any())
                <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
                    <ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
            @endif

            <form method="POST" action="{{ route('users.update', $user) }}" class="space-y-6">
                @csrf @method('PUT')

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">Asignar Rol</label>
                    <div class="space-y-2">
                        @foreach($roles as $role)
                        @php
                            $desc = [
                                'admin'        => 'Acceso total al sistema',
                                'doctor'       => 'Gestión clínica, horarios y citas propias',
                                'receptionist' => 'Pacientes, citas y facturación',
                                'patient'      => 'Solo vista de sus propias citas',
                            ][$role->name] ?? '';
                        @endphp
                        <label class="flex items-center gap-3 p-3 rounded-xl border cursor-pointer transition
                            {{ $currentRole === $role->name ? 'border-blue-400 bg-blue-50' : 'border-gray-100 hover:bg-gray-50' }}">
                            <input type="radio" name="role" value="{{ $role->name }}"
                                   {{ $currentRole === $role->name ? 'checked' : '' }}
                                   class="text-blue-500 focus:ring-blue-400">
                            <div>
                                <p class="font-medium text-gray-800 text-sm capitalize">{{ $role->name }}</p>
                                @if($desc) <p class="text-xs text-gray-400">{{ $desc }}</p> @endif
                            </div>
                        </label>
                        @endforeach
                    </div>
                    @error('role') <p class="text-red-500 text-xs mt-2">{{ $message }}</p> @enderror
                </div>

                <div class="flex gap-3 pt-2">
                    <a href="{{ route('users.index') }}"
                       class="flex-1 text-center py-2.5 border border-gray-200 text-gray-600 text-sm font-medium rounded-lg hover:bg-gray-50 transition">
                        Cancelar
                    </a>
                    <button type="submit"
                            class="flex-1 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition">
                        Guardar Cambio
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
