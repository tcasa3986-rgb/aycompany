<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Configuración General</h1>
                <p class="text-sm text-gray-500 mt-1">Personaliza los datos y parámetros del sistema</p>
            </div>
        </div>
    </x-slot>

    <div class="py-6 px-4 sm:px-6 lg:px-8">

        @if(session('success'))
            <div
                class="mb-6 bg-green-50 border border-green-200 text-green-800 rounded-xl px-4 py-3 flex items-center gap-3">
                <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data"
            class="space-y-8 max-w-4xl">
            @csrf

            {{-- ── Section 1: Datos de la Clínica ───────────────────── --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-6 py-4 bg-[#4A88F6]/5 border-b border-gray-100">
                    <h2 class="text-sm font-bold text-[#4A88F6] uppercase tracking-wider flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        Datos de la Clínica
                    </h2>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Nombre del establecimiento <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="clinic_name" value="{{ old('clinic_name', $settings['clinic_name']) }}"
                            class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none @error('clinic_name') border-red-400 @enderror">
                        @error('clinic_name')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Eslogan / Subtítulo</label>
                        <input type="text" name="clinic_tagline"
                            value="{{ old('clinic_tagline', $settings['clinic_tagline']) }}"
                            class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">RUC / NIT</label>
                        <input type="text" name="clinic_ruc" value="{{ old('clinic_ruc', $settings['clinic_ruc']) }}"
                            placeholder="20XXXXXXXXX"
                            class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Teléfono</label>
                        <input type="text" name="clinic_phone"
                            value="{{ old('clinic_phone', $settings['clinic_phone']) }}"
                            class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Correo electrónico</label>
                        <input type="email" name="clinic_email"
                            value="{{ old('clinic_email', $settings['clinic_email']) }}"
                            class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Dirección</label>
                        <input type="text" name="clinic_address"
                            value="{{ old('clinic_address', $settings['clinic_address']) }}"
                            class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                </div>
            </div>

            {{-- ── Section 2: Configuración de Citas ──────────────────── --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-6 py-4 bg-purple-50 border-b border-gray-100">
                    <h2 class="text-sm font-bold text-purple-600 uppercase tracking-wider flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        Configuración de Citas
                    </h2>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                            Duración por defecto (minutos) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="appointment_duration" min="5" max="240" step="5"
                            value="{{ old('appointment_duration', $settings['appointment_duration']) }}"
                            class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none @error('appointment_duration') border-red-400 @enderror">
                        @error('appointment_duration')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                            Días máximos para agendar <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="appointment_max_days" min="1" max="365"
                            value="{{ old('appointment_max_days', $settings['appointment_max_days']) }}"
                            class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none @error('appointment_max_days') border-red-400 @enderror">
                        @error('appointment_max_days')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Inicio de horario de atención
                            <span class="text-red-500">*</span></label>
                        <input type="time" name="working_hours_start"
                            value="{{ old('working_hours_start', $settings['working_hours_start']) }}"
                            class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none @error('working_hours_start') border-red-400 @enderror">
                        @error('working_hours_start')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Fin de horario de atención <span
                                class="text-red-500">*</span></label>
                        <input type="time" name="working_hours_end"
                            value="{{ old('working_hours_end', $settings['working_hours_end']) }}"
                            class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none @error('working_hours_end') border-red-400 @enderror">
                        @error('working_hours_end')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            {{-- ── Section 3: Apariencia ─────────────────────────────── --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-6 py-4 bg-green-50 border-b border-gray-100">
                    <h2 class="text-sm font-bold text-green-600 uppercase tracking-wider flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        Apariencia
                    </h2>
                </div>
                <div class="p-6 flex items-start gap-8">
                    {{-- Current logo preview --}}
                    <div class="flex-shrink-0">
                        @if($settings['logo_path'])
                            <img src="{{ Storage::url($settings['logo_path']) }}" alt="Logo actual"
                                class="w-24 h-24 object-contain rounded-xl border border-gray-100 bg-gray-50 p-2">
                        @else
                            <div
                                class="w-24 h-24 rounded-xl border-2 border-dashed border-gray-200 flex items-center justify-center bg-gray-50 text-gray-400 text-xs text-center px-2">
                                Sin logo
                            </div>
                        @endif
                        <p class="text-xs text-gray-400 mt-2 text-center">Logo actual</p>
                    </div>
                    <div class="flex-1">
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Subir nuevo logo</label>
                        <input type="file" name="logo" accept="image/*"
                            class="block w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        <p class="mt-1.5 text-xs text-gray-400">PNG, JPG, SVG hasta 2MB. Se recomienda fondo
                            transparente.</p>
                        @error('logo')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            {{-- ── Section 4: Sistema ────────────────────────────────── --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-6 py-4 bg-orange-50 border-b border-gray-100">
                    <h2 class="text-sm font-bold text-orange-600 uppercase tracking-wider flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17H3a2 2 0 01-2-2V5a2 2 0 012-2h14a2 2 0 012 2v10a2 2 0 01-2 2h-2" />
                        </svg>
                        Sistema
                    </h2>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Zona Horaria <span
                                class="text-red-500">*</span></label>
                        <select name="timezone"
                            class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                            @php
                                $timezones = [
                                    'America/Lima' => 'Lima, Perú (UTC-5)',
                                    'America/Bogota' => 'Bogotá, Colombia (UTC-5)',
                                    'America/Mexico_City' => 'Ciudad de México (UTC-6)',
                                    'America/Santiago' => 'Santiago, Chile (UTC-4)',
                                    'America/Buenos_Aires' => 'Buenos Aires, Argentina (UTC-3)',
                                    'America/Caracas' => 'Caracas, Venezuela (UTC-4)',
                                    'America/Guayaquil' => 'Guayaquil, Ecuador (UTC-5)',
                                    'America/La_Paz' => 'La Paz, Bolivia (UTC-4)',
                                    'America/Asuncion' => 'Asunción, Paraguay (UTC-4)',
                                    'Europe/Madrid' => 'Madrid, España (UTC+1)',
                                ];
                            @endphp
                            @foreach($timezones as $tz => $label)
                                <option value="{{ $tz }}" {{ old('timezone', $settings['timezone']) === $tz ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('timezone')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Símbolo de moneda</label>
                        <input type="text" name="currency_symbol" maxlength="5"
                            value="{{ old('currency_symbol', $settings['currency_symbol']) }}" placeholder="S/, $, €"
                            class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                </div>
            </div>

            {{-- ── Section 5: Notificaciones ──────────────────────────── --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-6 py-4 bg-red-50 border-b border-gray-100">
                    <h2 class="text-sm font-bold text-red-500 uppercase tracking-wider flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        Notificaciones por Email
                    </h2>
                </div>
                <div class="p-6 space-y-5">
                    <p class="text-xs text-gray-400">
                        Las notificaciones usan el driver de correo configurado en <code class="bg-gray-100 px-1 rounded">.env</code>
                        (<code class="bg-gray-100 px-1 rounded">MAIL_*</code>).
                        Para pruebas locales, usa <code class="bg-gray-100 px-1 rounded">MAIL_MAILER=log</code>.
                    </p>

                    @php
                        $notifSettings = [
                            'notify_on_confirm'   => ['label' => 'Enviar email al confirmar una cita',           'desc' => 'El paciente recibe un email cuando su cita pasa a estado Confirmada.'],
                            'notify_on_cancel'    => ['label' => 'Enviar email al cancelar una cita',             'desc' => 'El paciente recibe un email cuando su cita es cancelada (por admin o por el portal).'],
                            'notify_reminder_24h' => ['label' => 'Enviar recordatorio 24h antes de la cita',      'desc' => 'El scheduler envía un recordatorio diariamente a las 8:00 AM. Requiere: php artisan schedule:run.'],
                        ];
                    @endphp

                    @foreach($notifSettings as $key => $info)
                        <div class="flex items-start gap-4">
                            <label class="relative inline-flex items-center cursor-pointer flex-shrink-0 mt-0.5">
                                <input type="hidden" name="{{ $key }}" value="0">
                                <input type="checkbox" name="{{ $key }}" value="1" class="sr-only peer"
                                       {{ old($key, $settings[$key] ?? '1') === '1' ? 'checked' : '' }}>
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-400 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#4A88F6]"></div>
                            </label>
                            <div>
                                <p class="text-sm font-semibold text-gray-800">{{ $info['label'] }}</p>
                                <p class="text-xs text-gray-400 mt-0.5">{{ $info['desc'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Save button --}}
            <div class="flex items-center gap-4">
                <button type="submit"
                    class="bg-[#4A88F6] hover:bg-blue-600 text-white font-semibold px-8 py-3 rounded-xl shadow transition text-sm">
                    Guardar Configuración
                </button>
                <p class="text-xs text-gray-400">Los cambios se aplican inmediatamente en todo el sistema.</p>
            </div>


        </form>
    </div>
</x-app-layout>