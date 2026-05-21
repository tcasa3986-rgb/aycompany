<x-app-layout>
    <x-slot name="header">Nueva Plantilla de Diagnóstico</x-slot>

    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
            <form method="POST" action="{{ route('diagnostic-templates.store') }}" class="space-y-6">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre de la Plantilla <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                        placeholder="Ej: Resfriado Común, Faringitis Aguda..."
                        class="w-full rounded-lg border border-gray-200 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 @error('name') border-red-400 @enderror">
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="mb-2 relative">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Buscador ICD-10 (CIE-10)</label>
                    <input type="text" id="icd10_search" placeholder="Escriba para buscar enfermedad o código..."
                        class="w-full rounded-lg border border-gray-200 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400"
                        autocomplete="off">
                    <div id="icd10_results"
                        class="absolute z-10 w-full bg-white border border-gray-200 rounded-lg mt-1 shadow-lg max-h-48 overflow-y-auto hidden">
                    </div>
                    <p class="text-xs text-gray-400 mt-1">Seleccionar un resultado rellenará el código de abajo y el
                        campo de diagnóstico.</p>
                    <input type="hidden" name="icd_code" id="icd_code_input" value="{{ old('icd_code') }}">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Diagnóstico (Texto) <span
                            class="text-red-500">*</span></label>
                    <textarea name="diagnosis_text" rows="3" required
                        placeholder="Texto descriptivo o predeterminado para el diagnóstico..."
                        class="w-full rounded-lg border border-gray-200 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 @error('diagnosis_text') border-red-400 @enderror">{{ old('diagnosis_text') }}</textarea>
                    @error('diagnosis_text') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tratamiento Sugerido</label>
                        <textarea name="treatment_text" rows="3" placeholder="Tratamiento estándar..."
                            class="w-full rounded-lg border border-gray-200 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">{{ old('treatment_text') }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Prescripciones</label>
                        <textarea name="prescriptions_text" rows="3" placeholder="Medicamentos, dosis, frecuencia..."
                            class="w-full rounded-lg border border-gray-200 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">{{ old('prescriptions_text') }}</textarea>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notas Clínicas o Tips</label>
                    <textarea name="notes_text" rows="3" placeholder="Observaciones adicionales, consejos de cuidado..."
                        class="w-full rounded-lg border border-gray-200 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">{{ old('notes_text') }}</textarea>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                    <a href="{{ route('diagnostic-templates.index') }}"
                        class="px-5 py-2 text-sm text-gray-500 hover:text-gray-700">Cancelar</a>
                    <button type="submit"
                        class="px-6 py-2 bg-blue-500 text-white rounded-lg text-sm font-medium hover:bg-blue-600 transition shadow">
                        Guardar Plantilla
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const icdInput = document.getElementById('icd10_search');
            const icdResults = document.getElementById('icd10_results');
            const diagTextarea = document.querySelector('textarea[name="diagnosis_text"]');
            const codeInput = document.getElementById('icd_code_input');
            let icdTimeout;

            if (icdInput) {
                icdInput.addEventListener('input', function () {
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
                                        codeInput.value = item[0];
                                        const current = diagTextarea.value.trim();
                                        const appendage = `[${item[0]}] ${item[1]}`;
                                        diagTextarea.value = current ? current + '\n' + appendage : appendage;
                                        icdInput.value = item[1];
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
        });
    </script>
</x-app-layout>