<x-app-layout>
    <x-slot name="header">Especialidades</x-slot>

    <div class="mb-6 flex justify-end">
        <a href="{{ route('specialties.create') }}"
            class="inline-flex items-center gap-2 bg-blue-500 text-white px-5 py-2 rounded-lg text-sm font-medium hover:bg-blue-600 transition shadow">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Nueva Especialidad
        </a>
    </div>

    @if(session('success'))
        <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm">
            {{ session('success') }}</div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm overflow-hidden border border-gray-100">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                <tr>
                    <th class="px-6 py-4 text-left">Especialidad</th>
                    <th class="px-6 py-4 text-left">Descripción</th>
                    <th class="px-6 py-4 text-center">Médicos</th>
                    <th class="px-6 py-4 text-center">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($specialties as $specialty)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 font-medium text-gray-900">{{ $specialty->name }}</td>
                        <td class="px-6 py-4 text-gray-500">{{ $specialty->description ?? '—' }}</td>
                        <td class="px-6 py-4 text-center">
                            <span
                                class="px-3 py-1 bg-blue-50 text-blue-600 rounded-full text-xs font-semibold">{{ $specialty->doctors_count }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('specialties.edit', $specialty) }}"
                                    class="p-2 rounded-lg text-gray-400 hover:text-yellow-500 hover:bg-yellow-50 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </a>
                                <form method="POST" action="{{ route('specialties.destroy', $specialty) }}"
                                    onsubmit="return confirm('¿Eliminar esta especialidad?')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                        class="p-2 rounded-lg text-gray-400 hover:text-red-500 hover:bg-red-50 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-14 text-center text-gray-400">No hay especialidades registradas.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($specialties->hasPages())
        <div class="mt-4">{{ $specialties->links() }}</div>
    @endif
</x-app-layout>