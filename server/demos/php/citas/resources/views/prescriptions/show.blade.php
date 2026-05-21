<x-app-layout>
    <x-slot name="header">Detalle de Receta Médica</x-slot>

    <div class="max-w-4xl mx-auto space-y-6">
        {{-- Superior Actions --}}
        <div class="flex items-center justify-between">
            <a href="{{ route('prescriptions.index') }}" class="text-sm text-gray-500 hover:text-gray-700">← Volver al
                listado</a>
            <div class="flex gap-2">
                <a href="{{ route('prescriptions.edit', $prescription) }}"
                    class="px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition shadow-sm">
                    Editar Receta
                </a>
                <a href="{{ route('prescriptions.export.pdf', $prescription) }}"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition shadow-sm inline-flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Descargar PDF
                </a>
            </div>
        </div>

        {{-- Preview --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
            {{-- Receipt Header --}}
            <div class="flex justify-between items-start border-b border-gray-100 pb-6 mb-6">
                <div>
                    <h2 class="text-xl font-bold text-gray-800">Receta Médica</h2>
                    <p class="text-sm text-gray-500 mt-1">N° {{ str_pad($prescription->id, 5, '0', STR_PAD_LEFT) }}</p>
                </div>
                <div class="text-right">
                    <p class="text-sm font-medium text-gray-900">Fecha de Emisión</p>
                    <p class="text-sm text-gray-500">
                        {{ \Carbon\Carbon::parse($prescription->date)->format('d de F, Y') }}</p>
                </div>
            </div>

            {{-- People --}}
            <div class="grid grid-cols-2 gap-8 mb-8">
                <div>
                    <p class="text-xs uppercase tracking-wider text-gray-400 font-bold mb-2">Datos del Paciente</p>
                    <p class="font-medium text-gray-900">{{ $prescription->patient->user->name }}</p>
                    <p class="text-sm text-gray-500">{{ $prescription->patient->user->email }}</p>
                    @if($prescription->patient->phone)
                        <p class="text-sm text-gray-500">Tel: {{ $prescription->patient->phone }}</p>
                    @endif
                </div>
                <div class="bg-blue-50/50 p-4 rounded-xl border border-blue-100">
                    <p class="text-xs uppercase tracking-wider text-blue-400 font-bold mb-2">Médico Tratante</p>
                    <p class="font-bold text-blue-900">Dr. {{ $prescription->doctor->user->name }}</p>
                    <p class="text-sm text-blue-700">{{ $prescription->doctor->specialty->name }}</p>
                    <p class="text-xs text-blue-500 mt-1">Colegiatura: {{ $prescription->doctor->collegiate_number }}
                    </p>
                </div>
            </div>

            {{-- Medications --}}
            <div>
                <p class="text-xs uppercase tracking-wider text-gray-400 font-bold mb-3">Prescripción</p>
                <div class="overflow-x-auto border border-gray-100 rounded-xl">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 text-gray-600 border-b border-gray-100">
                            <tr>
                                <th class="py-3 px-4 text-left font-medium">Medicamento</th>
                                <th class="py-3 px-4 text-left font-medium">Dosis / Frecuencia / Duración</th>
                                <th class="py-3 px-4 text-left font-medium">Indicaciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($prescription->items as $item)
                                <tr>
                                    <td class="py-3 px-4 font-medium text-gray-800">{{ $item->medication_name }}</td>
                                    <td class="py-3 px-4 text-gray-600">
                                        {{ $item->dosage ? $item->dosage . ',' : '' }}
                                        {{ $item->frequency }}
                                        {{ $item->duration ? ' (' . $item->duration . ')' : '' }}
                                    </td>
                                    <td class="py-3 px-4 text-gray-500">{{ $item->instructions ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            @if($prescription->notes)
                <div class="mt-8">
                    <p class="text-xs uppercase tracking-wider text-gray-400 font-bold mb-2">Notas y Recomendaciones</p>
                    <div class="bg-gray-50 rounded-xl p-4 text-sm text-gray-700">
                        {{ $prescription->notes }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>