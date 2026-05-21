<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('invoices.index') }}" class="p-2 rounded-lg text-gray-400 hover:bg-gray-100 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Nueva Factura</h1>
                <p class="text-sm text-gray-500 mt-1">Registra el pago de una cita completada</p>
            </div>
        </div>
    </x-slot>

    <div class="py-6 px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl mx-auto">
            <form action="{{ route('invoices.store') }}" method="POST"
                class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8 space-y-6">
                @csrf

                {{-- Appointment --}}
                <div>
                    <label for="appointment_id" class="block text-sm font-semibold text-gray-700 mb-2">
                        Cita Médica <span class="text-red-500">*</span>
                    </label>
                    <select id="appointment_id" name="appointment_id"
                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none @error('appointment_id') border-red-400 @enderror">
                        <option value="">— Selecciona una cita —</option>
                        @foreach($appointments as $appt)
                            <option value="{{ $appt->id }}" {{ (old('appointment_id', $selectedAppointment?->id) == $appt->id) ? 'selected' : '' }}>
                                #{{ $appt->id }} — {{ $appt->patient->name }} · Dr. {{ $appt->doctor->name }} ·
                                {{ $appt->date?->format('d/m/Y H:i') }}
                            </option>
                        @endforeach
                    </select>
                    @error('appointment_id')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                    @if($appointments->isEmpty())
                        <p class="mt-2 text-xs text-yellow-600 bg-yellow-50 rounded-lg px-3 py-2">
                            No hay citas completadas sin factura disponibles.
                        </p>
                    @endif
                </div>

                {{-- Insurance Selector (Hidden by default or read from appointment) --}}
                <div id="insurance-container"
                    class="hidden bg-indigo-50 border border-indigo-100 p-4 rounded-xl space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-indigo-800 mb-2">Seguro Médico a Aplicar</label>
                        <select id="insurance_id" name="insurance_id"
                            class="w-full border border-indigo-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none">
                            <option value="">Sin Seguro / Particular</option>
                            @foreach($insurances as $ins)
                                <option value="{{ $ins->id }}" data-coverage="{{ $ins->coverage_percentage }}">
                                    {{ $ins->name }} ({{ number_format($ins->coverage_percentage, 0) }}%)
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Amount --}}
                <div>
                    <label for="amount" class="block text-sm font-semibold text-gray-700 mb-2">
                        Monto Total ({{ \App\Models\Setting::get('currency_symbol', 'S/') }}) <span
                            class="text-red-500">*</span>
                    </label>
                    <input type="number" id="amount" name="amount" step="0.01" min="0" value="{{ old('amount') }}"
                        placeholder="0.00"
                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-bold focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none @error('amount') border-red-400 @enderror">
                    @error('amount')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Insurance Breakdown (Read-only calculated) --}}
                <div id="breakdown-container" class="hidden grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Cubierto por Seguro</label>
                        <input type="number" id="insurance_coverage_amount" name="insurance_coverage_amount" step="0.01"
                            value="0" readonly
                            class="w-full border border-gray-100 bg-gray-50 text-indigo-600 font-bold rounded-xl px-4 py-2.5 text-sm outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Copago a Pagar</label>
                        <input type="number" id="patient_copay_amount" name="patient_copay_amount" step="0.01" value="0"
                            readonly
                            class="w-full border border-gray-100 bg-gray-50 text-red-600 font-bold rounded-xl px-4 py-2.5 text-sm outline-none">
                    </div>
                </div>

                {{-- Payment Method --}}
                <div>
                    <label for="payment_method" class="block text-sm font-semibold text-gray-700 mb-2">
                        Método de Pago <span class="text-red-500">*</span>
                    </label>
                    <select id="payment_method" name="payment_method"
                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none @error('payment_method') border-red-400 @enderror">
                        <option value="">— Selecciona —</option>
                        <option value="cash" {{ old('payment_method') === 'cash' ? 'selected' : '' }}>Efectivo</option>
                        <option value="card" {{ old('payment_method') === 'card' ? 'selected' : '' }}>Tarjeta</option>
                        <option value="transfer" {{ old('payment_method') === 'transfer' ? 'selected' : '' }}>
                            Transferencia</option>
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
                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none @error('status') border-red-400 @enderror">
                        <option value="pending" {{ old('status', 'pending') === 'pending' ? 'selected' : '' }}>Pendiente
                        </option>
                        <option value="paid" {{ old('status') === 'paid' ? 'selected' : '' }}>Pagado</option>
                        <option value="cancelled" {{ old('status') === 'cancelled' ? 'selected' : '' }}>Cancelado</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Notes --}}
                <div>
                    <label for="notes" class="block text-sm font-semibold text-gray-700 mb-2">Observaciones</label>
                    <textarea id="notes" name="notes" rows="3" placeholder="Notas adicionales sobre el pago..."
                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none resize-none @error('notes') border-red-400 @enderror">{{ old('notes') }}</textarea>
                    @error('notes')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Actions --}}
                <div class="flex items-center gap-3 pt-2">
                    <button type="submit"
                        class="flex-1 bg-[#4A88F6] hover:bg-blue-600 text-white font-semibold py-2.5 rounded-xl transition text-sm">
                        Registrar Factura
                    </button>
                    <a href="{{ route('invoices.index') }}"
                        class="flex-1 text-center border border-gray-200 text-gray-600 font-semibold py-2.5 rounded-xl hover:bg-gray-50 transition text-sm">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const appointmentSelect = document.getElementById('appointment_id');
            const insuranceContainer = document.getElementById('insurance-container');
            const insuranceSelect = document.getElementById('insurance_id');
            const amountInput = document.getElementById('amount');
            const breakdownContainer = document.getElementById('breakdown-container');
            const coverageInput = document.getElementById('insurance_coverage_amount');
            const copayInput = document.getElementById('patient_copay_amount');

            // We embed Patient Insurances Mapping for fast client side switching
            const patientInsurances = {
                @foreach($appointments as $appt)
                    "{{ $appt->id }}": "{{ $appt->patient->insurance_id ?? '' }}",
                @endforeach
        };

        function updateCalculations() {
            const total = parseFloat(amountInput.value) || 0;
            const $opt = insuranceSelect.options[insuranceSelect.selectedIndex];

            if (insuranceSelect.value && $opt && total > 0) {
                const perc = parseFloat($opt.getAttribute('data-coverage')) || 0;
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

        appointmentSelect.addEventListener('change', function () {
            const val = this.value;
            if (val && patientInsurances[val]) {
                const insId = patientInsurances[val];
                insuranceSelect.value = insId;
                insuranceContainer.classList.remove('hidden');
            } else {
                insuranceSelect.value = "";
                insuranceContainer.classList.add('hidden');
            }
            updateCalculations();
        });

        insuranceSelect.addEventListener('change', updateCalculations);
        amountInput.addEventListener('input', updateCalculations);

        // Run once on load
        if (appointmentSelect.value) {
            appointmentSelect.dispatchEvent(new Event('change'));
        }
    });
    </script>
</x-app-layout>