<x-app-layout>
    <x-slot name="header">Editar Entrada en la Lista de Espera</x-slot>

    <div class="max-w-2xl mx-auto">
        <form action="{{ route('waitlists.update', $waitlist) }}" method="POST"
            class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 space-y-6">
            @csrf @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Solo lectura de entidades principales -->
                <div class="md:col-span-2 bg-gray-50 p-4 rounded-xl border border-gray-100">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-wide">Paciente</p>
                            <p class="font-medium text-gray-900">{{ $waitlist->patient->user->name }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-wide">Médico</p>
                            <p class="font-medium text-gray-900">Dr. {{ $waitlist->doctor->user->name }}</p>
                        </div>
                    </div>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Estado de la Solicitud <span
                            class="text-red-500">*</span></label>
                    <select name="status" required class="w-full rounded-lg border border-gray-200 px-4 py-2 text-sm font-medium focus:ring-2 focus:ring-blue-400
                        {{ $waitlist->status === 'waiting' ? 'bg-yellow-50 text-yellow-800' : '' }}
                        {{ $waitlist->status === 'notified' ? 'bg-blue-50 text-blue-800' : '' }}
                        {{ $waitlist->status === 'resolved' ? 'bg-green-50 text-green-800' : '' }}
                        {{ $waitlist->status === 'cancelled' ? 'bg-gray-50 text-gray-800' : '' }}
                    ">
                        <option value="waiting" {{ old('status', $waitlist->status) == 'waiting' ? 'selected' : '' }}>En
                            Espera</option>
                        <option value="notified" {{ old('status', $waitlist->status) == 'notified' ? 'selected' : '' }}>
                            Notificado (Avisado por la secretaria)</option>
                        <option value="resolved" {{ old('status', $waitlist->status) == 'resolved' ? 'selected' : '' }}>
                            Resuelto (Cita Agendada ya)</option>
                        <option value="cancelled" {{ old('status', $waitlist->status) == 'cancelled' ? 'selected' : '' }}>
                            Cancelado / Ya no desea</option>
                    </select>
                    @error('status') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Servicio / Tipo de Cita</label>
                    <select name="appointment_type_id"
                        class="w-full rounded-lg border border-gray-200 px-4 py-2 text-sm focus:ring-2 focus:ring-blue-400">
                        <option value="">Cualquier Servicio / Indiferente</option>
                        @foreach($appointmentTypes as $type)
                            <option value="{{ $type->id }}" {{ old('appointment_type_id', $waitlist->appointment_type_id) == $type->id ? 'selected' : '' }}>
                                {{ $type->name }} ({{ $type->duration_minutes }} min)
                            </option>
                        @endforeach
                    </select>
                    @error('appointment_type_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Buscar a partir del</label>
                    <input type="date" name="requested_date_from"
                        value="{{ old('requested_date_from', $waitlist->requested_date_from?->format('Y-m-d')) }}"
                        class="w-full rounded-lg border border-gray-200 px-4 py-2 text-sm focus:ring-2 focus:ring-blue-400">
                    @error('requested_date_from') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Límite hasta el</label>
                    <input type="date" name="requested_date_to"
                        value="{{ old('requested_date_to', $waitlist->requested_date_to?->format('Y-m-d')) }}"
                        class="w-full rounded-lg border border-gray-200 px-4 py-2 text-sm focus:ring-2 focus:ring-blue-400">
                    @error('requested_date_to') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notas u Observaciones (Horarios
                        preferidos, urgencia, etc.)</label>
                    <textarea name="notes" rows="3"
                        class="w-full rounded-lg border border-gray-200 px-4 py-2 text-sm focus:ring-2 focus:ring-blue-400">{{ old('notes', $waitlist->notes) }}</textarea>
                    @error('notes') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="flex justify-between items-center pt-4 border-t border-gray-100 mt-6">
                <!-- Delete on the left for edit forms -->
                <div>
                    <button type="button"
                        onclick="if(confirm('¿Eliminar por completo?')) document.getElementById('delete-form').submit();"
                        class="text-sm text-red-600 hover:text-red-800 font-medium">
                        Eliminar
                    </button>
                </div>

                <div class="flex gap-3">
                    <a href="{{ route('waitlists.index') }}"
                        class="px-5 py-2 text-sm text-gray-500 hover:text-gray-700 font-medium">Cancelar</a>
                    <button type="submit"
                        class="px-5 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition">
                        Guardar Cambios
                    </button>
                </div>
            </div>
        </form>

        <form id="delete-form" action="{{ route('waitlists.destroy', $waitlist) }}" method="POST" class="hidden">
            @csrf @method('DELETE')
        </form>
    </div>
</x-app-layout>