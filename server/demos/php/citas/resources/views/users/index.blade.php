<x-app-layout>
    <x-slot name="header">Usuarios del Sistema</x-slot>

    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <form method="GET" action="{{ route('users.index') }}" class="flex gap-2 flex-wrap">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar por nombre o email..."
                class="rounded-lg border border-gray-200 px-4 py-2 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-400 w-64">
            <select name="role"
                class="rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                <option value="">Todos los roles</option>
                @foreach($roles as $role)
                    <option value="{{ $role->name }}" {{ request('role') === $role->name ? 'selected' : '' }}>
                        {{ ucfirst($role->name) }}
                    </option>
                @endforeach
            </select>
            <button
                class="bg-blue-500 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-600 transition">Filtrar</button>
            @if(request('search') || request('role'))
                <a href="{{ route('users.index') }}" class="text-gray-500 px-3 py-2 text-sm hover:text-gray-700">Limpiar</a>
            @endif
        </form>
        <span class="text-sm text-gray-400">Total: {{ $users->total() }} usuario(s)</span>
    </div>

    @if(session('success'))
        <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm overflow-hidden border border-gray-100">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                <tr>
                    <th class="px-6 py-4 text-left">Usuario</th>
                    <th class="px-6 py-4 text-left">Email</th>
                    <th class="px-6 py-4 text-left">Rol</th>
                    <th class="px-6 py-4 text-left">Registrado</th>
                    <th class="px-6 py-4 text-center">Acción</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($users as $user)
                    @php
                        $roleName = $user->roles->first()?->name ?? null;
                        $roleColors = [
                            'admin' => 'bg-purple-50 text-purple-600',
                            'doctor' => 'bg-blue-50 text-blue-600',
                            'receptionist' => 'bg-teal-50 text-teal-600',
                            'patient' => 'bg-emerald-50 text-emerald-600',
                        ];
                        $roleClass = $roleColors[$roleName] ?? 'bg-gray-50 text-gray-500';
                    @endphp
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <img src="{{ $user->avatar_url }}"
                                    class="w-9 h-9 rounded-full object-cover border border-gray-100"
                                    alt="{{ $user->name }}">
                                <p class="font-medium text-gray-900">{{ $user->name }}</p>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-gray-500">{{ $user->email }}</td>
                        <td class="px-6 py-4">
                            @if($roleName)
                                <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $roleClass }}">
                                    {{ $user->role_label }}
                                </span>
                            @else
                                <span class="text-gray-400 text-xs">Sin rol</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-gray-500 text-xs">{{ $user->created_at->format('d/m/Y') }}</td>
                        <td class="px-6 py-4 text-center">
                            <a href="{{ route('users.edit', $user) }}"
                                class="inline-flex items-center gap-1 px-3 py-1.5 bg-indigo-50 text-indigo-600 rounded-lg text-xs font-medium hover:bg-indigo-100 transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                </svg>
                                Cambiar Rol
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-14 text-center text-gray-400">No se encontraron usuarios</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($users->hasPages())
        <div class="mt-4">{{ $users->links() }}</div>
    @endif
</x-app-layout>