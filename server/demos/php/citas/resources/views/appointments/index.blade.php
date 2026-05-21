<x-app-layout>
    <x-slot name="header">Citas Médicas</x-slot>

    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <form method="GET" action="{{ route('appointments.index') }}" class="flex gap-2 flex-wrap">
            <select name="status"
                class="rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                <option value="">Todos los estados</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pendiente</option>
                <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmada</option>
                <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>En Atención</option>
                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completada</option>
                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelada</option>
                <option value="no_show" {{ request('status') == 'no_show' ? 'selected' : '' }}>No Asistió</option>
            </select>
            <select name="doctor_id"
                class="rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                <option value="">Todos los médicos</option>
                @foreach($doctors as $doc)
                    <option value="{{ $doc->id }}" {{ request('doctor_id') == $doc->id ? 'selected' : '' }}>Dr.
                        {{ $doc->user->name }}</option>
                @endforeach
            </select>
            <input type="date" name="date" value="{{ request('date') }}"
                class="rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
            <button
                class="bg-blue-500 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-600 transition">Filtrar</button>
            @if(request()->anyFilled(['status', 'doctor_id', 'date']))
                <a href="{{ route('appointments.index') }}"
                    class="text-gray-500 px-3 py-2 text-sm hover:text-gray-700">Limpiar</a>
            @endif
        </form>
        <a href="{{ route('appointments.create') }}"
            class="inline-flex items-center gap-2 bg-blue-500 text-white px-5 py-2 rounded-lg text-sm font-medium hover:bg-blue-600 transition shadow">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Nueva Cita
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
                    <th class="px-6 py-4 text-left">Paciente</th>
                    <th class="px-6 py-4 text-left">Médico</th>
                    <th class="px-6 py-4 text-left">Especialidad</th>
                    <th class="px-6 py-4 text-left">Fecha y Hora</th>
                    <th class="px-6 py-4 text-center">Estado</th>
                    <th class="px-6 py-4 text-center">Acción</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($appointments as $appt)
                    @php
                        $colors = ['pending' => 'yellow', 'confirmed' => 'blue', 'in_progress' => 'purple', 'completed' => 'green', 'cancelled' => 'red', 'no_show' => 'gray'];
                        $c = $colors[$appt->status] ?? 'gray';
                    @endphp
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 font-medium text-gray-900">{{ $appt->patient->user->name }}</td>
                        <td class="px-6 py-4 text-gray-600">Dr. {{ $appt->doctor->user->name }}</td>
                        <td class="px-6 py-4">
                            <span
                                class="px-2 py-1 bg-indigo-50 text-indigo-600 rounded-full text-xs">{{ $appt->specialty->name }}</span>
                        </td>
                        <td class="px-6 py-4 text-gray-600">
                            <span class="font-medium">{{ \Carbon\Carbon::parse($appt->date)->format('d/m/Y') }}</span>
                            <span
                                class="text-gray-400 text-xs ml-1">{{ \Carbon\Carbon::parse($appt->date)->format('H:i') }}</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span
                                class="px-3 py-1 bg-{{ $c }}-50 text-{{ $c }}-600 rounded-full text-xs font-medium">{{ $appt->status_label }}</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <a href="{{ route('appointments.show', $appt) }}"
                                class="p-2 rounded-lg text-gray-400 hover:text-blue-500 hover:bg-blue-50 transition inline-flex">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-14 text-center text-gray-400">No se encontraron citas.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($appointments->hasPages())
        <div class="mt-4">{{ $appointments->links() }}</div>
    @endif
</x-app-layout>