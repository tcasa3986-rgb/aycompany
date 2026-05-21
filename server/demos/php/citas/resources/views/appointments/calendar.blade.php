<x-app-layout>
    <x-slot name="header">Calendario de Citas</x-slot>

    {{-- FullCalendar CDN --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css">

    <div class="flex flex-col lg:flex-row gap-6">

        {{-- ── Left Panel: Calendar ─────────────────────────────────── --}}
        <div class="flex-1 bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h2 class="text-base font-semibold text-gray-800">Calendario</h2>
                    <p class="text-xs text-gray-400">Haz clic en un día para agendar una cita</p>
                </div>
                <a href="{{ route('appointments.create') }}"
                    class="inline-flex items-center gap-2 bg-blue-500 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-600 transition shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Nueva Cita
                </a>
            </div>
            <div id="calendar"></div>
        </div>

        {{-- ── Right Panel: Appointment Detail ─────────────────────── --}}
        <div class="w-full lg:w-80 space-y-4">

            {{-- Filter by doctor --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <h3 class="text-sm font-semibold text-gray-700 mb-3">Filtrar por Médico</h3>
                <select id="filter-doctor"
                    class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                    <option value="">Todos los médicos</option>
                    @foreach($doctors as $doc)
                        <option value="{{ $doc->id }}">Dr. {{ $doc->user->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Legend --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <h3 class="text-sm font-semibold text-gray-700 mb-3">Estados</h3>
                <div class="space-y-2">
                    @foreach([
                        ['color'=>'#f59e0b', 'label'=>'Pendiente'],
                        ['color'=>'#4A88F6', 'label'=>'Confirmada'],
                        ['color'=>'#8b5cf6', 'label'=>'En Atención'],
                        ['color'=>'#10b981', 'label'=>'Completada'],
                        ['color'=>'#ef4444', 'label'=>'Cancelada'],
                        ['color'=>'#6b7280', 'label'=>'No Asistió'],
                    ] as $item)
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full flex-shrink-0" style="background:{{ $item['color'] }}"></span>
                        <span class="text-xs text-gray-600">{{ $item['label'] }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Selected appointment detail --}}
            <div id="detail-panel" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 hidden">
                <h3 class="text-sm font-semibold text-gray-700 mb-3">Detalle de Cita</h3>
                <div class="space-y-3 text-sm">
                    <div>
                        <p class="text-xs text-gray-400">Paciente</p>
                        <p id="detail-patient" class="font-medium text-gray-900"></p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Médico</p>
                        <p id="detail-doctor" class="font-medium text-gray-900"></p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Especialidad</p>
                        <p id="detail-specialty" class="text-gray-600"></p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Hora</p>
                        <p id="detail-time" class="font-mono text-gray-900"></p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Estado</p>
                        <span id="detail-status" class="px-2 py-0.5 rounded-full text-xs font-medium"></span>
                    </div>
                    <div class="pt-2">
                        <a id="detail-link" href="#"
                            class="block w-full text-center px-4 py-2 bg-blue-500 text-white rounded-lg text-sm font-medium hover:bg-blue-600 transition">
                            Ver Cita Completa →
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
    <script>
    const STATUS_COLORS = {
        pending:     '#f59e0b',
        confirmed:   '#4A88F6',
        in_progress: '#8b5cf6',
        completed:   '#10b981',
        cancelled:   '#ef4444',
        no_show:     '#6b7280',
    };
    const STATUS_LABELS = {
        pending:     'Pendiente',
        confirmed:   'Confirmada',
        in_progress: 'En Atención',
        completed:   'Completada',
        cancelled:   'Cancelada',
        no_show:     'No Asistió',
    };

    let calendar;

    function buildEventSource(doctorId) {
        const url = new URL('/appointments/calendar-events', window.location.origin);
        if (doctorId) url.searchParams.set('doctor_id', doctorId);
        return { url: url.toString(), method: 'GET' };
    }

    document.addEventListener('DOMContentLoaded', function () {
        const calEl = document.getElementById('calendar');

        calendar = new FullCalendar.Calendar(calEl, {
            initialView: 'dayGridMonth',
            locale: 'es',
            height: 640,
            headerToolbar: {
                left:   'prev,next today',
                center: 'title',
                right:  'dayGridMonth,timeGridWeek,timeGridDay',
            },
            buttonText: { today: 'Hoy', month: 'Mes', week: 'Semana', day: 'Día' },
            events: buildEventSource(''),
            eventTimeFormat: { hour: '2-digit', minute: '2-digit', hour12: false },
            eventClick: function(info) {
                const p = info.event.extendedProps;
                document.getElementById('detail-patient').textContent  = p.patient;
                document.getElementById('detail-doctor').textContent   = 'Dr. ' + p.doctor;
                document.getElementById('detail-specialty').textContent = p.specialty;
                document.getElementById('detail-time').textContent     = info.event.start.toLocaleTimeString('es', {hour:'2-digit', minute:'2-digit'});

                const badge = document.getElementById('detail-status');
                badge.textContent = STATUS_LABELS[p.status] ?? p.status;
                badge.style.background = STATUS_COLORS[p.status] + '22';
                badge.style.color      = STATUS_COLORS[p.status];

                document.getElementById('detail-link').href = '/appointments/' + info.event.id;
                document.getElementById('detail-panel').classList.remove('hidden');
            },
            dateClick: function(info) {
                window.location.href = '{{ route("appointments.create") }}?date=' + info.dateStr + 'T08:00';
            },
            eventDidMount: function(info) {
                info.el.title = info.event.title;
            },
        });

        calendar.render();

        // Filter by doctor
        document.getElementById('filter-doctor').addEventListener('change', function() {
            calendar.removeAllEventSources();
            calendar.addEventSource(buildEventSource(this.value));
        });
    });
    </script>

    <style>
    /* FullCalendar custom styles */
    .fc-toolbar-title { font-size: 1rem !important; font-weight: 600 !important; color: #1f2937 !important; }
    .fc-button { background: #4A88F6 !important; border-color: #4A88F6 !important; font-size: 0.75rem !important; border-radius: 0.5rem !important; padding: 0.25rem 0.75rem !important; }
    .fc-button:hover { background: #3b73e0 !important; }
    .fc-button-active { background: #2563eb !important; }
    .fc-day-today { background: #eff6ff !important; }
    .fc-event { border-radius: 5px !important; font-size: 11px !important; border: none !important; padding: 1px 4px !important; cursor: pointer !important; }
    .fc-daygrid-event-dot { display: none !important; }
    </style>
</x-app-layout>
