<x-app-layout>
    <x-slot name="header">Nueva Receta Médica</x-slot>

    <div class="max-w-4xl mx-auto">
        <form method="POST" action="{{ route('prescriptions.store') }}" class="space-y-6">
            @csrf

            {{-- General Info --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                <h2 class="text-base font-semibold text-gray-800 mb-5">Información General</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Paciente <span
                                class="text-red-500">*</span></label>
                        <select name="patient_id" required
                            class="w-full rounded-lg border border-gray-200 px-4 py-2 text-sm focus:ring-2 focus:ring-blue-400">
                            <option value="">Seleccionar paciente</option>
                            @foreach($patients as $patient)
                                <option value="{{ $patient->id }}" {{ old('patient_id', $selectedPatient) == $patient->id ? 'selected' : '' }}>
                                    {{ $patient->user->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('patient_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Médico <span
                                class="text-red-500">*</span></label>
                        <select name="doctor_id" required
                            class="w-full rounded-lg border border-gray-200 px-4 py-2 text-sm focus:ring-2 focus:ring-blue-400">
                            <option value="">Seleccionar médico</option>
                            @foreach($doctors as $doctor)
                                <option value="{{ $doctor->id }}" {{ old('doctor_id', $selectedDoctor) == $doctor->id ? 'selected' : '' }}>
                                    Dr. {{ $doctor->user->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('doctor_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Fecha <span
                                class="text-red-500">*</span></label>
                        <input type="date" name="date" value="{{ old('date', now()->format('Y-m-d')) }}" required
                            class="w-full rounded-lg border border-gray-200 px-4 py-2 text-sm focus:ring-2 focus:ring-blue-400">
                        @error('date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="mt-5">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notas Adicionales (Opcional)</label>
                    <textarea name="notes" rows="2" placeholder="Recomendaciones generales, dieta, reposo..."
                        class="w-full rounded-lg border border-gray-200 px-4 py-2 text-sm focus:ring-2 focus:ring-blue-400">{{ old('notes') }}</textarea>
                </div>
            </div>

            {{-- Medicamentos (Items) --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                <div class="flex items-center justify-between mb-5">
                    <div>
                        <h2 class="text-base font-semibold text-gray-800">Medicamentos prescritos</h2>
                        <p class="text-xs text-gray-400 mt-1">Añada los medicamentos, dosis y frecuencias.</p>
                    </div>
                    <button type="button" onclick="addMedicationRow()"
                        class="px-4 py-2 bg-blue-50 text-blue-600 rounded-lg text-sm font-medium hover:bg-blue-100 transition shadow-sm">
                        + Añadir Medicamento
                    </button>
                </div>

                @error('items') <p class="text-red-500 text-sm mb-4">{{ $message }}</p> @enderror

                <div id="medications_container" class="space-y-4">
                    {{-- JS will insert rows here --}}
                </div>
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('prescriptions.index') }}"
                    class="px-6 py-2 text-sm text-gray-500 hover:text-gray-700 font-medium transition">Cancelar</a>
                <button type="submit"
                    class="px-6 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition shadow">
                    Guardar Receta
                </button>
            </div>
        </form>
    </div>

    {{-- Template for a medication row --}}
    <template id="medication_row_template">
        <div class="p-4 border border-gray-100 rounded-xl bg-gray-50/50 relative group">
            <button type="button" onclick="this.closest('.p-4').remove()"
                class="absolute top-4 right-4 text-gray-400 hover:text-red-500 transition opacity-0 group-hover:opacity-100">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
            </button>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 pr-8">
                <div class="md:col-span-2">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Nombre del Medicamento <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="items[__INDEX__][medication_name]" required
                        placeholder="Ej. Paracetamol 500mg"
                        class="w-full rounded-md border border-gray-200 px-3 py-1.5 text-sm focus:ring-1 focus:ring-blue-400">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Dosis</label>
                    <input type="text" name="items[__INDEX__][dosage]" placeholder="Ej. 1 tableta"
                        class="w-full rounded-md border border-gray-200 px-3 py-1.5 text-sm focus:ring-1 focus:ring-blue-400">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Frecuencia</label>
                    <input type="text" name="items[__INDEX__][frequency]" placeholder="Ej. Cada 8 horas"
                        class="w-full rounded-md border border-gray-200 px-3 py-1.5 text-sm focus:ring-1 focus:ring-blue-400">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Duración</label>
                    <input type="text" name="items[__INDEX__][duration]" placeholder="Ej. Por 5 días"
                        class="w-full rounded-md border border-gray-200 px-3 py-1.5 text-sm focus:ring-1 focus:ring-blue-400">
                </div>
                <div class="md:col-span-3">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Instrucciones Adicionales</label>
                    <input type="text" name="items[__INDEX__][instructions]"
                        placeholder="Ej. Tomar después de las comidas"
                        class="w-full rounded-md border border-gray-200 px-3 py-1.5 text-sm focus:ring-1 focus:ring-blue-400">
                </div>
            </div>
        </div>
    </template>

    <script>
        let rowIndex = 0;
        function addMedicationRow() {
            const container = document.getElementById('medications_container');
            const template = document.getElementById('medication_row_template');
            let html = template.innerHTML.replace(/__INDEX__/g, rowIndex);

            const div = document.createElement('div');
            div.innerHTML = html;
            container.appendChild(div.firstElementChild);
            rowIndex++;
        }

        // Initialize with at least one row
        document.addEventListener('DOMContentLoaded', () => {
            addMedicationRow();
        });
    </script>
</x-app-layout>