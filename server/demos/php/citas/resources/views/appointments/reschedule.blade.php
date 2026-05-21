<x-app-layout>
    <div class="py-8 max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Reagendar Cita</h1>
                <p class="text-sm text-gray-500 mt-1">Cita #{{ $appointment->id }} —
                    {{ $appointment->patient->user->name }}</p>
            </div>
            <a href="{{ route('appointments.show', $appointment) }}" class="text-gray-500 hover:text-gray-700 text-sm">←
                Volver</a>
        </div>

        @if($errors->any())
            <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
                <ul class="list-disc list-inside">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif

        {{-- Current appointment info --}}
        <div class="bg-amber-50 border border-amber-200 rounded-2xl p-5 mb-6 flex gap-4 items-start">
            <svg class="w-6 h-6 text-amber-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div class="text-sm text-amber-800">
                <p class="font-semibold mb-1">Horario actual</p>
                <p>📅
                    {{ \Carbon\Carbon::parse($appointment->date)->translatedFormat('l, d \d\e F \d\e Y \a \l\a\s H:i') }}
                </p>
                <p>👨‍⚕️ Dr. {{ $appointment->doctor->user->name }} — {{ $appointment->specialty->name }}</p>
            </div>
        </div>

        {{-- Reschedule form --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
            <form method="POST" action="{{ route('appointments.doReschedule', $appointment) }}" class="space-y-5">
                @csrf @method('PATCH')

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nueva Fecha <span
                            class="text-red-500">*</span></label>
                    <input type="date" id="appt_date" min="{{ now()->addDay()->toDateString() }}"
                        value="{{ old('appt_date') }}" onchange="onDateOrDoctorChange()"
                        class="w-full rounded-lg border border-gray-200 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                </div>

                {{-- Slot picker --}}
                <div id="slots_wrapper" class="hidden">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nueva Hora <span
                            class="text-red-500">*</span></label>
                    <div id="slots_container" class="flex flex-wrap gap-2"></div>
                    <p id="slots_message" class="text-sm text-gray-400 mt-1 hidden"></p>
                    <input type="hidden" name="date" id="date_hidden" value="{{ old('date') }}">
                </div>
                @error('date')<p class="text-red-500 text-xs">{{ $message }}</p>@enderror

                <div class="flex gap-3 pt-2">
                    <a href="{{ route('appointments.show', $appointment) }}"
                        class="flex-1 text-center py-2.5 border border-gray-200 text-gray-600 text-sm font-medium rounded-lg hover:bg-gray-50 transition">
                        Cancelar
                    </a>
                    <button type="submit" id="submit_btn" disabled
                        class="flex-1 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition disabled:opacity-50 disabled:cursor-not-allowed">
                        Confirmar Reagendamiento
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const SLOTS_URL = '{{ route("doctors.available-slots") }}';
        const DOCTOR_ID = {{ $appointment->doctor_id }};
        const CURRENT_DT = '{{ \Carbon\Carbon::parse($appointment->date)->format("H:i") }}';

        function onDateOrDoctorChange() {
            const date = document.getElementById('appt_date').value;
            if (date) loadSlots(DOCTOR_ID, date);
            else resetSlots();
        }

        function loadSlots(doctorId, date) {
            const wrapper = document.getElementById('slots_wrapper');
            const container = document.getElementById('slots_container');
            const msgEl = document.getElementById('slots_message');
            container.innerHTML = '<span class="text-sm text-gray-400">Cargando horarios...</span>';
            msgEl.classList.add('hidden');
            wrapper.classList.remove('hidden');
            document.getElementById('date_hidden').value = '';
            document.getElementById('submit_btn').disabled = true;

            fetch(`${SLOTS_URL}?doctor_id=${doctorId}&date=${date}`)
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
                        const isCurrent = (slot.time === CURRENT_DT);
                        const btn = document.createElement('button');
                        btn.type = 'button';
                        btn.textContent = slot.time + (isCurrent ? ' (actual)' : '');
                        btn.dataset.time = slot.time;
                        if (!slot.available && !isCurrent) {
                            btn.disabled = true;
                            btn.className = 'px-3 py-1.5 rounded-lg border border-gray-200 text-sm text-gray-300 cursor-not-allowed line-through bg-gray-50';
                        } else {
                            btn.className = 'px-3 py-1.5 rounded-lg border text-sm font-medium transition ' +
                                (isCurrent ? 'border-amber-300 bg-amber-50 text-amber-700' :
                                    'border-blue-200 text-blue-700 hover:bg-blue-600 hover:text-white hover:border-blue-600');
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
            document.querySelectorAll('#slots_container button').forEach(b => {
                if (!b.disabled) {
                    b.className = 'px-3 py-1.5 rounded-lg border border-blue-200 text-sm text-blue-700 hover:bg-blue-600 hover:text-white hover:border-blue-600 transition font-medium';
                }
            });
            btn.className = 'px-3 py-1.5 rounded-lg border border-blue-600 bg-blue-600 text-white text-sm font-medium';
            document.getElementById('date_hidden').value = date + 'T' + btn.dataset.time;
            document.getElementById('submit_btn').disabled = false;
        }

        function resetSlots() {
            document.getElementById('slots_wrapper').classList.add('hidden');
            document.getElementById('slots_container').innerHTML = '';
            document.getElementById('date_hidden').value = '';
            document.getElementById('submit_btn').disabled = true;
        }
    </script>
</x-app-layout>