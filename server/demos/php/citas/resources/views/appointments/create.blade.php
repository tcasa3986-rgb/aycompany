<x-app-layout>
    <x-slot name="header">Nueva Cita</x-slot>

    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
            <form method="POST" action="{{ route('appointments.store') }}" class="space-y-5">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Paciente <span
                            class="text-red-500">*</span></label>
                    <select name="patient_id" required
                        class="w-full rounded-lg border border-gray-200 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 @error('patient_id') border-red-400 @enderror">
                        <option value="">Seleccionar paciente</option>
                        @foreach($patients as $p)
                            <option value="{{ $p->id }}" {{ (old('patient_id', request('patient_id')) == $p->id) ? 'selected' : '' }}>
                                {{ $p->user->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('patient_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Especialidad <span
                            class="text-red-500">*</span></label>
                    <select name="specialty_id" id="specialty_select" required
                        class="w-full rounded-lg border border-gray-200 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 @error('specialty_id') border-red-400 @enderror"
                        onchange="loadDoctors(this.value)">
                        <option value="">Seleccionar especialidad</option>
                        @foreach($specialties as $sp)
                            <option value="{{ $sp->id }}" {{ old('specialty_id') == $sp->id ? 'selected' : '' }}>
                                {{ $sp->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('specialty_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Médico <span
                            class="text-red-500">*</span></label>
                    <select name="doctor_id" id="doctor_select" required
                        class="w-full rounded-lg border border-gray-200 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 @error('doctor_id') border-red-400 @enderror"
                        onchange="onDoctorChange()">
                        <option value="">Primero seleccione una especialidad</option>
                    </select>
                    @error('doctor_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Consultorio <span
                            class="text-red-500">*</span></label>
                    <select name="office_id" id="office_select" required
                        class="w-full rounded-lg border border-gray-200 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 @error('office_id') border-red-400 @enderror">
                        <option value="">Primero seleccione un médico</option>
                    </select>
                    @error('office_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Servicio <span class="text-red-500">*</span></label>
                    <select name="appointment_type_id" id="appointment_type_select" required onchange="onDoctorOrDateChange()"
                        class="w-full rounded-lg border border-gray-200 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 @error('appointment_type_id') border-red-400 @enderror">
                        <option value="">Primero seleccione un médico</option>
                    </select>
                    @error('appointment_type_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Date picker --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fecha <span
                            class="text-red-500">*</span></label>
                    <input type="date" id="appt_date" min="{{ now()->toDateString() }}" value="{{ old('appt_date') }}"
                        onchange="onDoctorOrDateChange()"
                        class="w-full rounded-lg border border-gray-200 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                </div>

                {{-- Time slot picker (AJAX) --}}
                <div id="slots_wrapper" class="hidden">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Hora disponible <span
                            class="text-red-500">*</span></label>
                    <div id="slots_container" class="flex flex-wrap gap-2"></div>
                    <p id="slots_message" class="text-sm text-gray-400 mt-1 hidden"></p>
                    {{-- Hidden inputs that get submitted --}}
                    <input type="hidden" name="date" id="date_hidden" value="{{ old('date') }}">
                </div>
                @error('date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Motivo de consulta</label>
                    <textarea name="reason" rows="3" placeholder="Describa brevemente el motivo de la consulta..."
                        class="w-full rounded-lg border border-gray-200 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">{{ old('reason') }}</textarea>
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <a href="{{ route('appointments.index') }}"
                        class="px-5 py-2 text-sm text-gray-500 hover:text-gray-700">Cancelar</a>
                    <button type="submit"
                        class="px-6 py-2 bg-blue-500 text-white rounded-lg text-sm font-medium hover:bg-blue-600 transition shadow">
                        Agendar Cita
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const AVAILABLE_SLOTS_URL = '{{ route("doctors.available-slots") }}';
        const DOCTORS_BY_SPEC_URL = '/specialties/{id}/doctors';

        let cachedDoctors = [];

        function loadDoctors(specialtyId) {
            const select = document.getElementById('doctor_select');
            const officeSelect = document.getElementById('office_select');
            const typeSelect = document.getElementById('appointment_type_select');
            select.innerHTML = '<option value="">Cargando...</option>';
            officeSelect.innerHTML = '<option value="">Primero seleccione un médico</option>';
            typeSelect.innerHTML = '<option value="">Primero seleccione un médico</option>';
            cachedDoctors = [];

            if (!specialtyId) {
                select.innerHTML = '<option value="">Primero seleccione una especialidad</option>';
                resetSlots();
                return;
            }
            fetch(DOCTORS_BY_SPEC_URL.replace('{id}', specialtyId))
                .then(r => r.json())
                .then(doctors => {
                    cachedDoctors = doctors;
                    select.innerHTML = '<option value="">Seleccionar médico</option>';
                    doctors.forEach(d => {
                        select.innerHTML += `<option value="${d.id}">Dr. ${d.name}</option>`;
                    });
                    if (!doctors.length) {
                        select.innerHTML = '<option value="">No hay médicos para esta especialidad</option>';
                    }
                    resetSlots();
                });
        }

        function onDoctorChange() {
            const doctorId = document.getElementById('doctor_select').value;
            const officeSelect = document.getElementById('office_select');
            const typeSelect = document.getElementById('appointment_type_select');

            officeSelect.innerHTML = '<option value="">Seleccionar consultorio</option>';
            typeSelect.innerHTML = '<option value="">Seleccionar servicio</option>';

            if (doctorId) {
                const doctor = cachedDoctors.find(d => d.id == doctorId);
                if (doctor && doctor.offices && doctor.offices.length > 0) {
                    doctor.offices.forEach(o => {
                        officeSelect.innerHTML += `<option value="${o.id}">${o.name}</option>`;
                    });
                } else {
                    officeSelect.innerHTML = '<option value="">El médico no tiene consultorios registrados</option>';
                }

                if (doctor && doctor.appointmentTypes && doctor.appointmentTypes.length > 0) {
                    doctor.appointmentTypes.forEach(t => {
                        typeSelect.innerHTML += `<option value="${t.id}">${t.name} (${t.duration_minutes} min)</option>`;
                    });
                } else {
                    typeSelect.innerHTML = '<option value="">El médico no ha configurado servicios</option>';
                }
            } else {
                officeSelect.innerHTML = '<option value="">Primero seleccione un médico</option>';
                typeSelect.innerHTML = '<option value="">Primero seleccione un médico</option>';
            }

            onDoctorOrDateChange();
        }

        function onDoctorOrDateChange() {
            const doctorId = document.getElementById('doctor_select').value;
            const date = document.getElementById('appt_date').value;
            const typeId = document.getElementById('appointment_type_select').value;

            if (doctorId && date && typeId) {
                loadSlots(doctorId, date, typeId);
            }
            else {
                resetSlots();
            }
        }

        function loadSlots(doctorId, date, typeId) {
            const wrapper = document.getElementById('slots_wrapper');
            const container = document.getElementById('slots_container');
            const msgEl = document.getElementById('slots_message');
            container.innerHTML = '<span class="text-sm text-gray-400">Cargando horarios...</span>';
            msgEl.classList.add('hidden');
            wrapper.classList.remove('hidden');
            document.getElementById('date_hidden').value = '';

            fetch(`${AVAILABLE_SLOTS_URL}?doctor_id=${doctorId}&date=${date}&appointment_type_id=${typeId}`)
                .then(r => r.json())
                .then(data => {
                    container.innerHTML = '';
                    if (!data.available) {
                        msgEl.textContent = data.message;
                        msgEl.classList.remove('hidden');
                        return;
                    }
                    if (!data.slots.length) {
                        msgEl.textContent = 'No hay horarios disponibles para esta fecha.';
                        msgEl.classList.remove('hidden');
                        return;
                    }
                    data.slots.forEach(slot => {
                        const btn = document.createElement('button');
                        btn.type = 'button';
                        btn.textContent = slot.time;
                        btn.dataset.time = slot.time;
                        if (!slot.available) {
                            btn.disabled = true;
                            btn.className = 'px-3 py-1.5 rounded-lg border border-gray-200 text-sm text-gray-300 cursor-not-allowed line-through bg-gray-50';
                        } else {
                            btn.className = 'px-3 py-1.5 rounded-lg border border-blue-200 text-sm text-blue-700 hover:bg-blue-600 hover:text-white hover:border-blue-600 transition font-medium';
                            btn.onclick = () => selectSlot(btn, date);
                        }
                        container.appendChild(btn);
                    });
                })
                .catch(() => {
                    container.innerHTML = '';
                    msgEl.textContent = 'Error al cargar los horarios. Intente de nuevo.';
                    msgEl.classList.remove('hidden');
                });
        }

        function selectSlot(btn, date) {
            // Unselect all
            document.querySelectorAll('#slots_container button').forEach(b => {
                if (!b.disabled) {
                    b.className = 'px-3 py-1.5 rounded-lg border border-blue-200 text-sm text-blue-700 hover:bg-blue-600 hover:text-white hover:border-blue-600 transition font-medium';
                }
            });
            btn.className = 'px-3 py-1.5 rounded-lg border border-blue-600 bg-blue-600 text-white text-sm font-medium';
            document.getElementById('date_hidden').value = date + 'T' + btn.dataset.time;
        }

        function resetSlots() {
            document.getElementById('slots_wrapper').classList.add('hidden');
            document.getElementById('slots_container').innerHTML = '';
            document.getElementById('date_hidden').value = '';
        }

        // Pre-load if old value
        @if(old('specialty_id'))
            loadDoctors('{{ old('specialty_id') }}');
        @endif
    </script>
</x-app-layout>