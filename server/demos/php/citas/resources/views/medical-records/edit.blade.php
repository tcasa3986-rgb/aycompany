<x-app-layout>
    <x-slot name="header">Editar Registro Clínico</x-slot>

    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
            <div class="mb-6 pb-4 border-b border-gray-100">
                <p class="text-sm text-gray-500">
                    Paciente: <span class="font-semibold text-gray-900">{{ $medicalRecord->patient->user->name }}</span>
                    · Fecha original: <span class="font-mono text-gray-600">{{ $medicalRecord->record_date->format('d/m/Y') }}</span>
                </p>
            </div>

            <form method="POST" action="{{ route('medical-records.update', $medicalRecord) }}" class="space-y-6" enctype="multipart/form-data">
                @csrf @method('PUT')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Médico <span class="text-red-500">*</span></label>
                        <select name="doctor_id" required
                            class="w-full rounded-lg border border-gray-200 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                            @foreach($doctors as $doc)
                                <option value="{{ $doc->id }}" {{ old('doctor_id', $medicalRecord->doctor_id) == $doc->id ? 'selected' : '' }}>
                                    Dr. {{ $doc->user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Fecha del Registro <span class="text-red-500">*</span></label>
                        <input type="date" name="record_date" value="{{ old('record_date', $medicalRecord->record_date->format('Y-m-d')) }}" required
                            class="w-full rounded-lg border border-gray-200 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cita Vinculada</label>
                    <select name="appointment_id"
                        class="w-full rounded-lg border border-gray-200 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                        <option value="">Sin cita vinculada</option>
                        @foreach($appointments as $appt)
                            <option value="{{ $appt->id }}" {{ old('appointment_id', $medicalRecord->appointment_id) == $appt->id ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::parse($appt->date)->format('d/m/Y H:i') }} — Dr. {{ $appt->doctor->user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Signos Vitales --}}
                <div>
                    <h3 class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
                        <span class="w-1 h-4 bg-blue-400 rounded-full"></span>
                        Signos Vitales
                    </h3>
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
                        @foreach([
                            ['field'=>'blood_pressure',    'label'=>'Presión Art.',   'placeholder'=>'120/80'],
                            ['field'=>'heart_rate',        'label'=>'Frec. Cardíaca', 'placeholder'=>'72 lpm'],
                            ['field'=>'temperature',       'label'=>'Temperatura',    'placeholder'=>'36.5 °C'],
                            ['field'=>'weight',            'label'=>'Peso',           'placeholder'=>'70 kg'],
                            ['field'=>'height',            'label'=>'Talla',          'placeholder'=>'170 cm'],
                            ['field'=>'oxygen_saturation', 'label'=>'SpO2',           'placeholder'=>'98%'],
                        ] as $vital)
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">{{ $vital['label'] }}</label>
                            <input type="text" name="{{ $vital['field'] }}"
                                value="{{ old($vital['field'], $medicalRecord->{$vital['field']}) }}"
                                placeholder="{{ $vital['placeholder'] }}"
                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Clinical Fields --}}
                <div>
                    <h3 class="text-sm font-semibold text-gray-700 mb-3 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-1 h-4 bg-indigo-400 rounded-full"></span>
                            Datos Clínicos
                        </div>
                        @if(isset($diagnosticTemplates) && $diagnosticTemplates->count() > 0)
                        <div class="flex items-center gap-2">
                            <span class="text-xs text-gray-500 font-normal">Cargar Plantilla:</span>
                            <select id="diagnostic_template_select" class="rounded border-gray-200 text-xs py-1 px-2 focus:ring-indigo-400 text-gray-700">
                                <option value="">-- Seleccionar --</option>
                                @foreach($diagnosticTemplates as $tpl)
                                    <option value="{{ $tpl->id }}" 
                                        data-icd="{{ $tpl->icd_code }}"
                                        data-diagnosis="{{ $tpl->diagnosis_text }}"
                                        data-treatment="{{ $tpl->treatment_text }}"
                                        data-prescriptions="{{ $tpl->prescriptions_text }}"
                                        data-notes="{{ $tpl->notes_text }}">
                                        {{ $tpl->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @endif
                    </h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Motivo de Consulta <span class="text-red-500">*</span></label>
                            <textarea name="chief_complaint" rows="2" required
                                class="w-full rounded-lg border border-gray-200 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">{{ old('chief_complaint', $medicalRecord->chief_complaint) }}</textarea>
                        </div>
                        <div class="mb-2 relative">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Buscador ICD-10 (Asistente de Diagnóstico)</label>
                            <input type="text" id="icd10_search" placeholder="Escriba para buscar enfermedad o código..."
                                class="w-full rounded-lg border border-gray-200 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400" autocomplete="off">
                            <div id="icd10_results" class="absolute z-10 w-full bg-white border border-gray-200 rounded-lg mt-1 shadow-lg max-h-48 overflow-y-auto hidden"></div>
                            <p class="text-xs text-gray-400 mt-1">Seleccionar un resultado lo agregará automáticamente al campo de abajo.</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Diagnóstico <span class="text-red-500">*</span></label>
                            <textarea name="diagnosis" rows="3" required
                                class="w-full rounded-lg border border-gray-200 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">{{ old('diagnosis', $medicalRecord->diagnosis) }}</textarea>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tratamiento</label>
                                <textarea name="treatment" rows="3"
                                    class="w-full rounded-lg border border-gray-200 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">{{ old('treatment', $medicalRecord->treatment) }}</textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Notas Clínicas</label>
                                <textarea name="notes" rows="3"
                                    class="w-full rounded-lg border border-gray-200 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">{{ old('notes', $medicalRecord->notes) }}</textarea>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Prescripciones</label>
                                <textarea name="prescriptions" rows="3"
                                    class="w-full rounded-lg border border-gray-200 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">{{ old('prescriptions', $medicalRecord->prescriptions) }}</textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Derivación</label>
                                <textarea name="referred_to" rows="3"
                                    class="w-full rounded-lg border border-gray-200 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">{{ old('referred_to', $medicalRecord->referred_to) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Archivos Adjuntos --}}
                <div>
                    <h3 class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
                        <span class="w-1 h-4 bg-purple-400 rounded-full"></span>
                        Archivos Adjuntos
                    </h3>

                    @if($medicalRecord->attachments && $medicalRecord->attachments->count() > 0)
                        <div class="mb-4 grid grid-cols-2 sm:grid-cols-4 gap-3">
                            @foreach($medicalRecord->attachments as $attachment)
                                <div class="flex flex-col p-2 bg-gray-50 border border-gray-100 rounded-lg group relative">
                                    <div class="flex items-center gap-2 mb-2">
                                        @if(in_array(strtolower(pathinfo($attachment->file_name, PATHINFO_EXTENSION)), ['jpg','jpeg','png']))
                                            <svg class="w-6 h-6 text-blue-500" fill="currentColor" viewBox="0 0 24 24"><path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z"/></svg>
                                        @else
                                            <svg class="w-6 h-6 text-red-500" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 14H9v-2H8v-2h1V9c0-1.1.9-2 2-2h3v2h-3v5h2v2h-2v2zm6-4h-2v2h2v2h-3V8h3v2h-2v2h2v2z"/></svg>
                                        @endif
                                        <div class="truncate text-xs font-medium text-gray-700 flex-1" title="{{ $attachment->file_name }}">
                                            {{ $attachment->file_name }}
                                        </div>
                                    </div>
                                    <a href="{{ route('medical-records.attachments.destroy', $attachment) }}" 
                                       onclick="event.preventDefault(); document.getElementById('delete-file-{{ $attachment->id }}').submit();"
                                       class="text-xs text-red-500 hover:text-red-700 font-medium text-center border-t border-gray-200 pt-1 mt-auto">
                                        Eliminar
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <label
                        class="flex flex-col items-center justify-center w-full h-28 border-2 border-dashed border-gray-200 rounded-xl cursor-pointer hover:border-blue-400 hover:bg-blue-50 transition group" id="dropzone">
                        <svg class="w-8 h-8 text-gray-300 group-hover:text-blue-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                        <p class="text-xs text-gray-400">Subir más archivos (JPG, PNG o PDF — máx. 10 MB)</p>
                        <input type="file" name="attachments[]" id="file_input" multiple accept=".jpg,.jpeg,.png,.pdf" class="hidden" onchange="previewFiles(this)">
                    </label>
                    @error('attachments.*') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    
                    <div id="file_preview" class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-4"></div>
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <input type="hidden" name="is_private" value="0">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_private" value="1" {{ old('is_private', $medicalRecord->is_private) ? 'checked' : '' }}
                            class="w-4 h-4 rounded text-blue-500 border-gray-300 focus:ring-blue-400">
                        <span class="text-sm text-gray-600">Registro privado</span>
                    </label>
                </div>

                <div class="flex justify-end gap-3 pt-2 border-t border-gray-100">
                    <a href="{{ route('medical-records.show', $medicalRecord) }}" class="px-5 py-2 text-sm text-gray-500 hover:text-gray-700">Cancelar</a>
                    <button type="submit" class="px-6 py-2 bg-blue-500 text-white rounded-lg text-sm font-medium hover:bg-blue-600 transition shadow">
                        Actualizar Registro
                    </button>
                </div>
            </form>

            @if($medicalRecord->attachments)
                @foreach($medicalRecord->attachments as $attachment)
                    <form id="delete-file-{{ $attachment->id }}" action="{{ route('medical-records.attachments.destroy', $attachment) }}" method="POST" class="hidden">
                        @csrf
                        @method('DELETE')
                    </form>
                @endforeach
            @endif
        </div>
        </div>
    </div>

    <script>
    function previewFiles(input) {
        const preview = document.getElementById('file_preview');
        preview.innerHTML = '';
        if (input.files) {
            Array.from(input.files).forEach(file => {
                const ext = file.name.split('.').pop().toLowerCase();
                const icon = ext === 'pdf' ? '<svg class="w-6 h-6 text-red-500" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 14H9v-2H8v-2h1V9c0-1.1.9-2 2-2h3v2h-3v5h2v2h-2v2zm6-4h-2v2h2v2h-3V8h3v2h-2v2h2v2z"/></svg>' : '<svg class="w-6 h-6 text-blue-500" fill="currentColor" viewBox="0 0 24 24"><path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z"/></svg>';
                
                preview.innerHTML += `
                    <div class="flex items-center gap-2 p-2 bg-gray-50 border border-gray-100 rounded-lg">
                        ${icon}
                        <div class="truncate text-xs font-medium text-gray-700 flex-1" title="${file.name}">${file.name}</div>
                    </div>
                `;
            });
        }
    }

    // ICD-10 Autocomplete Logic
    document.addEventListener('DOMContentLoaded', () => {
        const icdInput = document.getElementById('icd10_search');
        const icdResults = document.getElementById('icd10_results');
        const diagTextarea = document.querySelector('textarea[name="diagnosis"]');
        let icdTimeout;

        if (icdInput) {
            icdInput.addEventListener('input', function() {
                clearTimeout(icdTimeout);
                const query = this.value.trim();
                if (query.length < 3) {
                    icdResults.classList.add('hidden');
                    return;
                }
                icdTimeout = setTimeout(() => {
                    fetch(`https://clinicaltables.nlm.nih.gov/api/icd10cm/v3/search?sf=code,name&terms=${encodeURIComponent(query)}`)
                        .then(res => res.json())
                        .then(data => {
                            icdResults.innerHTML = '';
                            if (data[0] === 0) {
                                icdResults.innerHTML = '<div class="px-4 py-2 text-sm text-gray-500">No se encontraron resultados</div>';
                                icdResults.classList.remove('hidden');
                                return;
                            }
                            const items = data[3];
                            items.forEach(item => {
                                const div = document.createElement('div');
                                div.className = 'px-4 py-2 text-sm hover:bg-indigo-50 cursor-pointer border-b border-gray-50 last:border-0';
                                div.innerHTML = `<span class="font-medium text-indigo-600">[${item[0]}]</span> ${item[1]}`;
                                div.onclick = () => {
                                    const current = diagTextarea.value.trim();
                                    const appendage = `[${item[0]}] ${item[1]}`;
                                    diagTextarea.value = current ? current + '\n' + appendage : appendage;
                                    icdInput.value = '';
                                    icdResults.classList.add('hidden');
                                };
                                icdResults.appendChild(div);
                            });
                            icdResults.classList.remove('hidden');
                        });
                }, 400);
            });

            document.addEventListener('click', (e) => {
                if (!icdInput.contains(e.target) && !icdResults.contains(e.target)) {
                    icdResults.classList.add('hidden');
                }
            });
        }

        // Diagnostic Template autocompletion
        const templateSelect = document.getElementById('diagnostic_template_select');
        if (templateSelect) {
            templateSelect.addEventListener('change', function() {
                const opt = this.options[this.selectedIndex];
                if (!opt.value) return;

                const icdCode = opt.getAttribute('data-icd');
                const diagnosis = opt.getAttribute('data-diagnosis');
                const treatment = opt.getAttribute('data-treatment');
                const prescriptions = opt.getAttribute('data-prescriptions');
                const notes = opt.getAttribute('data-notes');

                if (diagnosis) document.querySelector('textarea[name="diagnosis"]').value = diagnosis;
                if (treatment) document.querySelector('textarea[name="treatment"]').value = treatment;
                if (prescriptions) document.querySelector('textarea[name="prescriptions"]').value = prescriptions;
                if (notes) document.querySelector('textarea[name="notes"]').value = notes;
                
                if (icdCode) {
                    const current = document.querySelector('textarea[name="diagnosis"]').value;
                    if (!current.includes(`[${icdCode}]`)) {
                        document.querySelector('textarea[name="diagnosis"]').value = `[${icdCode}] \n` + current;
                    }
                }
            });
        }
    });
    </script>
</x-app-layout>
