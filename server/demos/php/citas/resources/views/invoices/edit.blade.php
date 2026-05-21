<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('invoices.show', $invoice) }}"
                class="p-2 rounded-lg text-gray-400 hover:bg-gray-100 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Editar Factura #{{ $invoice->id }}</h1>
                <p class="text-sm text-gray-500 mt-1">Modifica el estado o los datos del cobro</p>
            </div>
        </div>
    </x-slot>

    <div class="py-6 px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl mx-auto">

            {{-- Appointment info (read-only) --}}
            <div class="bg-blue-50 border border-blue-100 rounded-2xl px-6 py-4 mb-6">
                <p class="text-xs font-semibold text-blue-400 uppercase tracking-wider mb-2">Cita vinculada</p>
                <p class="text-sm font-semibold text-blue-800">
                    #{{ $invoice->appointment->id }} — {{ $invoice->appointment->patient->name }}
                    · Dr. {{ $invoice->appointment->doctor->name }}
                    · {{ $invoice->appointment->date?->format('d/m/Y H:i') }}
                </p>
                @if($invoice->appointment->patient->insurance)
                    <p class="text-xs text-indigo-600 mt-2 flex items-center gap-1 font-medium">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z">
                            </path>
                        </svg>
                        Paciente asegurado con: {{ $invoice->appointment->patient->insurance->name }} (Póliza:
                        {{ $invoice->appointment->patient->policy_number ?? 'N/A' }})
                    </p>
                @endif
            </div>

            <form action="{{ route('invoices.update', $invoice) }}" method="POST"
                class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8 space-y-6">
                @csrf @method('PUT')

                {{-- Amount --}}
                <div>
                    <label for="amount" class="block text-sm font-semibold text-gray-700 mb-2">
                        Monto Total ({{ \App\Models\Setting::get('currency_symbol', 'S/') }}) <span
                            class="text-red-500">*</span>
                    </label>
                    <input type="number" id="amount" name="amount" step="0.01" min="0"
                        value="{{ old('amount', $invoice->amount) }}"
                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-bold focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none @error('amount') border-red-400 @enderror">
                    @error('amount')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Insurance Selector --}}
                <div id="insurance-container" class="bg-indigo-50 border border-indigo-100 p-4 rounded-xl space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-indigo-800 mb-2">Seguro Médico a Aplicar</label>
                        <select id="insurance_id" name="insurance_id"
                            class="w-full border border-indigo-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none">
                            <option value="">Sin Seguro / Particular</option>
                            @foreach($insurances as $ins)
                                <option value="{{ $ins->id }}" data-coverage="{{ $ins->coverage_percentage }}" {{ old('insurance_id', $invoice->insurance_id) == $ins->id ? 'selected' : '' }}>
                                    {{ $ins->name }} ({{ number_format($ins->coverage_percentage, 0) }}%)
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Insurance Breakdown (Read-only calculated) --}}
                <div id="breakdown-container" class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Cubierto por Seguro</label>
                        <input type="number" id="insurance_coverage_amount" name="insurance_coverage_amount" step="0.01"
                            value="{{ old('insurance_coverage_amount', $invoice->insurance_coverage_amount) }}" readonly
                            class="w-full border border-gray-100 bg-gray-50 text-indigo-600 font-bold rounded-xl px-4 py-2.5 text-sm outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Copago a Pagar</label>
                        <input type="number" id="patient_copay_amount" name="patient_copay_amount" step="0.01"
                            value="{{ old('patient_copay_amount', $invoice->patient_copay_amount) }}" readonly
                            class="w-full border border-gray-100 bg-gray-50 text-red-600 font-bold rounded-xl px-4 py-2.5 text-sm outline-none">
                    </div>
                </div>

                {{-- Payment Method --}}
                <div>
                    <label for="payment_method" class="block text-sm font-semibold text-gray-700 mb-2">
                        Método de Pago <span class="text-red-500">*</span>
                    </label>
                    <select id="payment_method" name="payment_method"
                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                        <option value="cash" {{ old('payment_method', $invoice->payment_method) === 'cash' ? 'selected' : '' }}>Efectivo</option>
                        <option value="card" {{ old('payment_method', $invoice->payment_method) === 'card' ? 'selected' : '' }}>Tarjeta</option>
                        <option value="transfer" {{ old('payment_method', $invoice->payment_method) === 'transfer' ? 'selected' : '' }}>Transferencia</option>
                    </select>
                    @error('payment_method')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Status --}}
                <div>
                    <label for="status" class="block text-sm font-semibold text-gray-700 mb-2">
                        Estado <span class="text-red-500">*</span>
                    </label>
                    <select id="status" name="status"
                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                        <option value="pending" {{ old('status', $invoice->status) === 'pending' ? 'selected' : '' }}>
                            Pendiente</option>
                        <option value="paid" {{ old('status', $invoice->status) === 'paid' ? 'selected' : '' }}>Pagado
                        </option>
                        <option value="cancelled" {{ old('status', $invoice->status) === 'cancelled' ? 'selected' : '' }}>
                            Cancelado</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Notes --}}
                <div>
                    <label for="notes" class="block text-sm font-semibold text-gray-700 mb-2">Observaciones</label>
                    <textarea id="notes" name="notes" rows="3"
                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none resize-none">{{ old('notes', $invoice->notes) }}</textarea>
                    @error('notes')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Actions --}}
                <div class="flex items-center gap-3 pt-2">
                    <button type="submit"
                        class="flex-1 bg-[#4A88F6] hover:bg-blue-600 text-white font-semibold py-2.5 rounded-xl transition text-sm">
                        Guardar Cambios
                    </button>
                    <a href="{{ route('invoices.show', $invoice) }}"
                        class="flex-1 text-center border border-gray-200 text-gray-600 font-semibold py-2.5 rounded-xl hover:bg-gray-50 transition text-sm">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const insuranceContainer = document.getElementById('insurance-container');
            const insuranceSelect = document.getElementById('insurance_id');
            const amountInput = document.getElementById('amount');
            const breakdownContainer = document.getElementById('breakdown-container');
            const coverageInput = document.getElementById('insurance_coverage_amount');
            const copayInput = document.getElementById('patient_copay_amount');

            function updateCalculations() {
                const total = parseFloat(amountInput.value) || 0;
                const $opt = insuranceSelect.options[insuranceSelect.selectedIndex];

                if (insuranceSelect.value && $opt && total > 0) {
                    const perc = parseFloat($opt.getAttribute('data-coverage')) || 0;

                    // Keep existing calculated values IF they were modified manually on backend, 
                    // else auto recalc. Here we just strictly recalc to keep it simple and consistent.
                    const covered = (total * perc) / 100;
                    const copay = total - covered;

                    coverageInput.value = covered.toFixed(2);
                    copayInput.value = copay.toFixed(2);
                    breakdownContainer.classList.remove('hidden');
                    insuranceContainer.classList.remove('hidden');
                } else {
                    coverageInput.value = "0.00";
                    copayInput.value = total.toFixed(2);
                    breakdownContainer.classList.add('hidden');
                }
            }

            insuranceSelect.addEventListener('change', updateCalculations);
            amountInput.addEventListener('input', updateCalculations);

            updateCalculations();
        });
    </script>
</x-app-layout>