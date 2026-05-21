<x-app-layout>
    <x-slot name="header">Añadir Paciente a Lista de Espera</x-slot>

    <div class="max-w-2xl mx-auto">
        <form action="{{ route('waitlists.store') }}" method="POST"
            class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Paciente <span
                            class="text-red-500">*</span></label>
                    <select name="patient_id" required
                        class="w-full rounded-lg border border-gray-200 px-4 py-2 text-sm focus:ring-2 focus:ring-blue-400">
                        <option value="">Seleccionar Paciente</option>
                        @foreach($patients as $patient)
                            <option value="{{ $patient->id }}" {{ old('patient_id') == $patient->id ? 'selected' : '' }}>
                                {{ $patient->user->name }} ({{ $patient->user->email }})
                            </option>
                        @endforeach
                    </select>
                    @error('patient_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Médico <span
                            class="text-red-500">*</span></label>
                    <select name="doctor_id" id="doctor_select" required
                        class="w-full rounded-lg border border-gray-200 px-4 py-2 text-sm focus:ring-2 focus:ring-blue-400">
                        <option value="">Seleccionar Médico</option>
                        @foreach($doctors as $doctor)
                            <option value="{{ $doctor->id }}" {{ old('doctor_id') == $doctor->id ? 'selected' : '' }}>
                                Dr. {{ $doctor->user->name }} - {{ $doctor->specialty->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('doctor_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Servicio / Tipo de Cita
                        (Opcional)</label>
                    <select name="appointment_type_id" id="appointment_type_select"
                        class="w-full rounded-lg border border-gray-200 px-4 py-2 text-sm focus:ring-2 focus:ring-blue-400">
                        <option value="">Cualquier Servicio / Seleccione Médico Primero</option>
                    </select>
                    @error('appointment_type_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Buscar a partir del</label>
                    <input type="date" name="requested_date_from" value="{{ old('requested_date_from') }}"
                        class="w-full rounded-lg border border-gray-200 px-4 py-2 text-sm focus:ring-2 focus:ring-blue-400">
                    @error('requested_date_from') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Límite hasta el</label>
                    <input type="date" name="requested_date_to" value="{{ old('requested_date_to') }}"
                        class="w-full rounded-lg border border-gray-200 px-4 py-2 text-sm focus:ring-2 focus:ring-blue-400">
                    @error('requested_date_to') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notas u Observaciones (Horarios
                        preferidos, urgencia, etc.)</label>
                    <textarea name="notes" rows="3"
                        class="w-full rounded-lg border border-gray-200 px-4 py-2 text-sm focus:ring-2 focus:ring-blue-400">{{ old('notes') }}</textarea>
                    @error('notes') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                <a href="{{ route('waitlists.index') }}"
                    class="px-5 py-2 text-sm text-gray-500 hover:text-gray-700 font-medium">Cancelar</a>
                <button type="submit"
                    class="px-5 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition">
                    Añadir a Lista de Espera
                </button>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const doctors = @json($doctors->map(function ($d) {
                return ['id' => $d->id, 'appointmentTypes' => $d->appointmentTypes->where('is_active', true)->map(function ($t) {
                    return ['id' => $t->id, 'name' => $t->name]; })];
            }));

            const doctorSelect = document.getElementById('doctor_select');
            const typeSelect = document.getElementById('appointment_type_select');

            doctorSelect.addEventListener('change', function () {
                const doctorId = this.value;
                typeSelect.innerHTML = '<option value="">Cualquier Servicio / Indiferente</option>';

                if (doctorId) {
                    const doctor = doctors.find(d => d.id == doctorId);
                    if (doctor && doctor.appointmentTypes && doctor.appointmentTypes.length > 0) {
                        doctor.appointmentTypes.forEach(t => {
                            typeSelect.innerHTML += `<option value="${t.id}">${t.name}</option>`;
                        });
                    }
                }
            });

            // Trigger on load for old input
            if (doctorSelect.value) {
                const event = new Event('change');
                doctorSelect.dispatchEvent(event);

                @if(old('appointment_type_id'))
                    setTimeout(() => {
                        typeSelect.value = "{{ old('appointment_type_id') }}";
                    }, 50);
                @endif
            }
        });
    </script>
</x-app-layout>