<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('invoices.index') }}" class="p-2 rounded-lg text-gray-400 hover:bg-gray-100 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Factura #{{ $invoice->id }}</h1>
                <p class="text-sm text-gray-500 mt-1">Detalle del comprobante de pago</p>
            </div>
        </div>
    </x-slot>

    <div class="py-6 px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl mx-auto space-y-6">

            @if(session('success'))
                <div
                    class="bg-green-50 border border-green-200 text-green-800 rounded-xl px-4 py-3 flex items-center gap-3">
                    <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    {{ session('success') }}
                </div>
            @endif

            {{-- Status Badge --}}
            <div class="flex items-center justify-between">
                @php $color = $invoice->status_color; @endphp
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold
                    bg-{{ $color }}-100 text-{{ $color }}-700">
                    {{ $invoice->status_label }}
                </span>
                <div class="flex items-center gap-2">
                    <a href="{{ route('invoices.edit', $invoice) }}"
                        class="inline-flex items-center gap-1.5 text-sm border border-gray-200 text-gray-600 hover:bg-gray-50 font-medium px-4 py-2 rounded-xl transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Editar
                    </a>
                    <a href="{{ route('invoices.pdf', $invoice) }}"
                        class="inline-flex items-center gap-1.5 text-sm bg-[#4A88F6] hover:bg-blue-600 text-white font-medium px-4 py-2 rounded-xl transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Descargar PDF
                    </a>
                </div>
            </div>

            {{-- Details Card --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm divide-y divide-gray-50">
                <div class="px-6 py-4">
                    <h2 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4">Datos de la Cita</h2>
                    <dl class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <dt class="text-gray-500">Paciente</dt>
                            <dd class="font-semibold text-gray-800 mt-0.5">
                                {{ $invoice->appointment->patient->name ?? '—' }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">Médico</dt>
                            <dd class="font-semibold text-gray-800 mt-0.5">
                                Dr. {{ $invoice->appointment->doctor->name ?? '—' }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">Especialidad</dt>
                            <dd class="font-semibold text-gray-800 mt-0.5">
                                {{ $invoice->appointment->specialty->name ?? '—' }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">Fecha de Cita</dt>
                            <dd class="font-semibold text-gray-800 mt-0.5">
                                {{ $invoice->appointment->date?->format('d/m/Y H:i') ?? '—' }}
                            </dd>
                        </div>
                    </dl>
                </div>
                <div class="px-6 py-4">
                    <h2 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4">Datos de Pago</h2>
                    <dl class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <dt class="text-gray-500">Monto Subtotal</dt>
                            <dd class="text-2xl font-bold text-gray-800 mt-0.5">
                                {{ \App\Models\Setting::get('currency_symbol', 'S/') }}
                                {{ number_format($invoice->amount, 2) }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">Aportación Seguro ARS</dt>
                            <dd class="font-bold text-indigo-600 mt-0.5">
                                -{{ \App\Models\Setting::get('currency_symbol', 'S/') }}
                                {{ number_format($invoice->insurance_coverage_amount, 2) }}
                                @if($invoice->insurance) (<span class="text-xs">{{ $invoice->insurance->name }}</span>)
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="text-gray-500 font-semibold">Total Copago Paciente</dt>
                            <dd class="text-xl font-bold text-red-600 mt-0.5">
                                {{ \App\Models\Setting::get('currency_symbol', 'S/') }}
                                {{ number_format($invoice->patient_copay_amount ?: $invoice->amount, 2) }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-gray-500 mt-1">Método de Pago</dt>
                            <dd class="font-semibold text-gray-800 mt-0.5">{{ $invoice->payment_method_label }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">Registrado el</dt>
                            <dd class="font-semibold text-gray-800 mt-0.5">
                                {{ $invoice->created_at->format('d/m/Y H:i') }}
                            </dd>
                        </div>
                    </dl>
                </div>
                @if($invoice->notes)
                    <div class="px-6 py-4">
                        <h2 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Observaciones</h2>
                        <p class="text-sm text-gray-700">{{ $invoice->notes }}</p>
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>