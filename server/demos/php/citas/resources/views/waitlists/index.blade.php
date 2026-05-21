<x-app-layout>
    <x-slot name="header">Lista de Espera</x-slot>

    <div class="mb-6 flex flex-col md:flex-row gap-4 items-start md:items-center justify-between">
        <form method="GET" action="{{ route('waitlists.index') }}" class="flex flex-wrap items-center gap-3 w-full md:w-auto">
            <select name="status" class="rounded-lg border-gray-200 text-sm focus:ring-blue-500 min-w-[150px]">
                <option value="">Todos los Estados</option>
                <option value="waiting" {{ request('status') == 'waiting' ? 'selected' : '' }}>En Espera</option>
                <option value="notified" {{ request('status') == 'notified' ? 'selected' : '' }}>Notificado</option>
                <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Resuelto (Agendado)</option>
                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelado</option>
            </select>

            <select name="doctor_id" class="rounded-lg border-gray-200 text-sm focus:ring-blue-500 min-w-[200px]">
                <option value="">Todos los Médicos</option>
                @foreach($doctors as $doctor)
                    <option value="{{ $doctor->id }}" {{ request('doctor_id') == $doctor->id ? 'selected' : '' }}>
                        Dr. {{ $doctor->user->name }}
                    </option>
                @endforeach
            </select>

            <button type="submit" class="px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg text-sm hover:bg-gray-50 transition">
                Filtrar
            </button>
            @if(request()->hasAny(['status', 'doctor_id']))
                <a href="{{ route('waitlists.index') }}" class="text-sm text-gray-500 hover:text-gray-700">Limpiar</a>
            @endif
        </form>

        <a href="{{ route('waitlists.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition shrink-0">
            + Añadir a Lista
        </a>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-green-50 text-green-700 p-4 rounded-xl text-sm border border-green-200">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 border-b border-gray-100 hidden md:table-row">
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Paciente</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Médico & Servicio</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Fechas Solicitadas</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Estado</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($waitlists as $waitlist)
                        <tr class="hover:bg-gray-50/50 transition duration-150 flex flex-col md:table-row">
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-900 border-b border-gray-100 mb-2 pb-2 md:border-0 md:mb-0 md:pb-0">
                                    <span class="md:hidden text-xs text-gray-500 uppercase block mb-1">Paciente:</span>
                                    <a href="{{ route('patients.show', $waitlist->patient_id) }}" class="text-blue-600 hover:underline">
                                        {{ $waitlist->patient->user->name }}
                                    </a>
                                    <div class="text-xs text-gray-500 flex items-center gap-1 mt-0.5">
                                        {{ $waitlist->patient->user->email }}
                                    </div>
                                    <div class="text-xs text-gray-400 mt-1">
                                        Añadido el {{ $waitlist->created_at->format('d/m/Y') }}
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col gap-1 border-b border-gray-100 mb-2 pb-2 md:border-0 md:mb-0 md:pb-0">
                                    <span class="md:hidden text-xs text-gray-500 uppercase block mb-1">Médico:</span>
                                    <span class="text-sm text-gray-900 font-medium">Dr. {{ $waitlist->doctor->user->name }}</span>
                                    @if($waitlist->appointmentType)
                                        <span class="text-xs text-purple-600 bg-purple-50 px-2 py-0.5 rounded-full inline-flex w-max">
                                            {{ $waitlist->appointmentType->name }}
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="md:hidden text-xs text-gray-500 uppercase block mb-1">Fechas:</span>
                                <div class="text-sm text-gray-700">
                                    @if($waitlist->requested_date_from && $waitlist->requested_date_to)
                                        {{ $waitlist->requested_date_from->format('d/m/Y') }} al {{ $waitlist->requested_date_to->format('d/m/Y') }}
                                    @elseif($waitlist->requested_date_from)
                                        A partir del {{ $waitlist->requested_date_from->format('d/m/Y') }}
                                    @elseif($waitlist->requested_date_to)
                                        Hasta el {{ $waitlist->requested_date_to->format('d/m/Y') }}
                                    @else
                                        <span class="text-gray-400 italic">Cualquier fecha</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="md:hidden text-xs text-gray-500 uppercase block mb-1">Estado:</span>
                                <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium bg-{{ $waitlist->status_color }}-50 text-{{ $waitlist->status_color }}-700 border border-{{ $waitlist->status_color }}-200">
                                    {{ $waitlist->status_label }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <!-- Botón rápido de contacto si hay teléfono -->
                                    @if($waitlist->patient->phone)
                                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $waitlist->patient->phone) }}" target="_blank" class="p-1.5 text-emerald-600 hover:bg-emerald-50 rounded-lg transition" title="Contactar por WhatsApp">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.347-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.064 2.876 1.213 3.074.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                                        </a>
                                    @endif
                                    
                                    <a href="{{ route('appointments.create', ['patient_id' => $waitlist->patient_id, 'doctor_id' => $waitlist->doctor_id, 'specialty_id' => $waitlist->doctor->specialty_id]) }}" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Agendar Cita Rápidamente">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    </a>

                                    <a href="{{ route('waitlists.edit', $waitlist) }}" class="p-1.5 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Editar / Cambiar Estado">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </a>

                                    <form action="{{ route('waitlists.destroy', $waitlist) }}" method="POST" onsubmit="return confirm('¿Eliminar de la lista de espera?');" class="inline-block">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-1.5 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition" title="Eliminar">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <span class="text-sm">No hay pacientes en lista de espera.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($waitlists->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
                {{ $waitlists->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
