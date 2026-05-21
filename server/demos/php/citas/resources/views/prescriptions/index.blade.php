<x-app-layout>
    <x-slot name="header">Recetas Médicas</x-slot>

    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <form method="GET" action="{{ route('prescriptions.index') }}" class="flex gap-2 flex-wrap">
            <select name="patient_id"
                class="rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                <option value="">Todos los pacientes</option>
                @foreach($patients as $patient)
                    <option value="{{ $patient->id }}" {{ request('patient_id') == $patient->id ? 'selected' : '' }}>
                        {{ $patient->user->name }}
                    </option>
                @endforeach
            </select>
            <select name="doctor_id"
                class="rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                <option value="">Todos los médicos</option>
                @foreach($doctors as $doctor)
                    <option value="{{ $doctor->id }}" {{ request('doctor_id') == $doctor->id ? 'selected' : '' }}>
                        Dr. {{ $doctor->user->name }}
                    </option>
                @endforeach
            </select>
            <input type="date" name="date" value="{{ request('date') }}"
                class="rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
            <button type="submit"
                class="bg-blue-500 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-600 transition">Filtrar</button>
            @if(request()->anyFilled(['patient_id', 'doctor_id', 'date']))
                <a href="{{ route('prescriptions.index') }}"
                    class="text-gray-500 px-3 py-2 text-sm hover:text-gray-700">Limpiar</a>
            @endif
        </form>
        <a href="{{ route('prescriptions.create') }}"
            class="inline-flex items-center gap-2 bg-blue-500 text-white px-5 py-2 rounded-lg text-sm font-medium hover:bg-blue-600 transition shadow">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Nueva Receta
        </a>
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
                    <th class="px-6 py-4 text-left">ID</th>
                    <th class="px-6 py-4 text-left">Paciente</th>
                    <th class="px-6 py-4 text-left">Médico</th>
                    <th class="px-6 py-4 text-left">Fecha</th>
                    <th class="px-6 py-4 text-center">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($prescriptions as $recipe)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 text-gray-400">#{{ str_pad($recipe->id, 5, '0', STR_PAD_LEFT) }}</td>
                        <td class="px-6 py-4 font-medium text-gray-900">{{ $recipe->patient->user->name }}</td>
                        <td class="px-6 py-4 text-gray-600">Dr. {{ $recipe->doctor->user->name }}</td>
                        <td class="px-6 py-4 text-gray-600">{{ \Carbon\Carbon::parse($recipe->date)->format('d/m/Y') }}</td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('prescriptions.export.pdf', $recipe) }}"
                                    class="p-2 rounded-lg text-red-400 hover:text-red-500 hover:bg-red-50 transition"
                                    title="Generar PDF">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </a>
                                <a href="{{ route('prescriptions.show', $recipe) }}"
                                    class="p-2 rounded-lg text-gray-400 hover:text-blue-500 hover:bg-blue-50 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </a>
                                <a href="{{ route('prescriptions.edit', $recipe) }}"
                                    class="p-2 rounded-lg text-gray-400 hover:text-yellow-600 hover:bg-yellow-50 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </a>
                                <form action="{{ route('prescriptions.destroy', $recipe) }}" method="POST" class="inline"
                                    onsubmit="return confirm('¿Confirma la eliminación de esta receta?');">
                                    @csrf @method('DELETE')
                                    <button
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
                        <td colspan="5" class="px-6 py-14 text-center text-gray-400">
                            No se han emitido recetas médicas con estos filtros.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($prescriptions->hasPages())
        <div class="mt-4">
            {{ $prescriptions->links() }}
        </div>
    @endif
</x-app-layout>