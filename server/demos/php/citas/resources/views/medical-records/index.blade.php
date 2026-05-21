<x-app-layout>
    <x-slot name="header">Historia Clínica</x-slot>

    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <form method="GET" action="{{ route('medical-records.index') }}" class="flex gap-2 flex-wrap">
            <select name="patient_id"
                class="rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                <option value="">Todos los pacientes</option>
                @foreach($patients as $p)
                    <option value="{{ $p->id }}" {{ request('patient_id') == $p->id ? 'selected' : '' }}>{{ $p->user->name }}
                    </option>
                @endforeach
            </select>
            <select name="doctor_id"
                class="rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                <option value="">Todos los médicos</option>
                @foreach($doctors as $doc)
                    <option value="{{ $doc->id }}" {{ request('doctor_id') == $doc->id ? 'selected' : '' }}>Dr.
                        {{ $doc->user->name }}</option>
                @endforeach
            </select>
            <button
                class="bg-blue-500 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-600 transition">Filtrar</button>
            @if(request()->anyFilled(['patient_id', 'doctor_id']))
                <a href="{{ route('medical-records.index') }}"
                    class="text-gray-500 px-3 py-2 text-sm hover:text-gray-700">Limpiar</a>
            @endif
        </form>
        <a href="{{ route('medical-records.create') }}"
            class="inline-flex items-center gap-2 bg-blue-500 text-white px-5 py-2 rounded-lg text-sm font-medium hover:bg-blue-600 transition shadow">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Nuevo Registro
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
                    <th class="px-6 py-4 text-left">Fecha</th>
                    <th class="px-6 py-4 text-left">Paciente</th>
                    <th class="px-6 py-4 text-left">Médico</th>
                    <th class="px-6 py-4 text-left">Diagnóstico</th>
                    <th class="px-6 py-4 text-center">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($records as $rec)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 text-gray-500 font-mono text-xs">
                            {{ $rec->record_date->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4 font-medium text-gray-900">
                            <a href="{{ route('medical-records.patient-history', $rec->patient) }}"
                                class="hover:text-blue-600">
                                {{ $rec->patient->user->name }}
                            </a>
                        </td>
                        <td class="px-6 py-4 text-gray-600">Dr. {{ $rec->doctor->user->name }}</td>
                        <td class="px-6 py-4 text-gray-700 max-w-xs truncate">{{ $rec->diagnosis }}</td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center gap-1">
                                <a href="{{ route('medical-records.show', $rec) }}"
                                    class="p-2 rounded-lg text-gray-400 hover:text-blue-500 hover:bg-blue-50 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </a>
                                <a href="{{ route('medical-records.edit', $rec) }}"
                                    class="p-2 rounded-lg text-gray-400 hover:text-emerald-500 hover:bg-emerald-50 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </a>
                                <form method="POST" action="{{ route('medical-records.destroy', $rec) }}"
                                    onsubmit="return confirm('¿Eliminar este registro clínico?')">
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
                        <td colspan="5" class="px-6 py-14 text-center text-gray-400">No hay registros clínicos.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($records->hasPages())
        <div class="mt-4">{{ $records->links() }}</div>
    @endif
</x-app-layout>