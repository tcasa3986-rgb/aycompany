<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Plantillas de Email</h1>
                <p class="text-sm text-gray-500 mt-1">Configura el asunto y contenido de las notificaciones automáticas
                </p>
            </div>
            <a href="{{ route('settings.index') }}" class="text-sm font-medium text-gray-500 hover:text-gray-700">←
                Volver a General</a>
        </div>
    </x-slot>

    <div class="py-6 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
        @if(session('success'))
            <div
                class="mb-6 bg-green-50 border border-green-200 text-green-800 rounded-xl px-4 py-3 flex items-center gap-3">
                <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <!-- Sidebar Navigation -->
            <div class="md:col-span-1 space-y-2">
                @foreach($templates as $index => $template)
                    <button type="button" onclick="openTab('tpl-{{ $template->id }}')" id="btn-tpl-{{ $template->id }}"
                        class="w-full text-left px-4 py-3 rounded-xl text-sm font-medium transition-colors border-l-4 {{ $index === 0 ? 'bg-white shadow-sm border-blue-500 text-blue-700' : 'border-transparent text-gray-600 hover:bg-white/50' }} tab-btn">
                        @php
                            $names = [
                                'appointment_confirmed' => 'Confirmación de Cita',
                                'appointment_cancelled' => 'Cancelación de Cita',
                                'appointment_reminder_24h' => 'Recordatorio 24 horas',
                                'appointment_reminder_1h' => 'Recordatorio 1 hora',
                            ];
                        @endphp
                        {{ $names[$template->name] ?? $template->name }}
                    </button>
                @endforeach
            </div>

            <!-- Content Area -->
            <div class="md:col-span-3">
                @foreach($templates as $index => $template)
                    <div id="tpl-{{ $template->id }}" class="tab-content {{ $index === 0 ? 'block' : 'hidden' }}">
                        <form action="{{ route('settings.email_templates.update', $template) }}" method="POST"
                            class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-6">
                            @csrf
                            @method('PUT')

                            <div class="border-b border-gray-100 pb-4">
                                <h2 class="text-lg font-bold text-gray-800">{{ $names[$template->name] ?? $template->name }}
                                </h2>
                                <p class="text-sm text-gray-500 mt-1">Variables disponibles: <code
                                        class="text-xs bg-gray-100 px-1.5 py-0.5 rounded text-blue-600">{{ $template->variables }}</code>
                                </p>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Asunto del correo <span
                                        class="text-red-500">*</span></label>
                                <input type="text" name="subject" value="{{ old('subject', $template->subject) }}" required
                                    class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                                @error('subject')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Cuerpo del Mensaje <span
                                        class="text-red-500">*</span></label>
                                <textarea name="body" rows="10" required
                                    class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500 outline-none font-mono text-gray-800">{{ old('body', $template->body) }}</textarea>
                                @error('body')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                                <p class="text-xs text-gray-400 mt-2">Puedes utilizar saltos de línea normales. Las
                                    variables serán reemplazadas automáticamente al enviar.</p>
                            </div>

                            <div class="pt-4 flex justify-end">
                                <button type="submit"
                                    class="bg-[#4A88F6] hover:bg-blue-600 text-white font-semibold px-6 py-2.5 rounded-xl shadow transition text-sm">
                                    Guardar Cambios
                                </button>
                            </div>
                        </form>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <script>
        function openTab(tabId) {
            // Hide all contents
            document.querySelectorAll('.tab-content').forEach(el => {
                el.classList.add('hidden');
            });
            // Show targeted content
            document.getElementById(tabId).classList.remove('hidden');

            // Reset all buttons
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('bg-white', 'shadow-sm', 'border-blue-500', 'text-blue-700');
                btn.classList.add('border-transparent', 'text-gray-600', 'hover:bg-white/50');
            });

            // Highlight active button
            const activeBtn = document.getElementById('btn-' + tabId);
            activeBtn.classList.remove('border-transparent', 'text-gray-600', 'hover:bg-white/50');
            activeBtn.classList.add('bg-white', 'shadow-sm', 'border-blue-500', 'text-blue-700');
        }
    </script>
</x-app-layout>