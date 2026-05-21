<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Facturación</h1>
                <p class="text-sm text-gray-500 mt-1">Gestión de pagos y facturas por citas médicas</p>
            </div>
            <a href="{{ route('invoices.create') }}"
                class="inline-flex items-center gap-2 bg-[#4A88F6] hover:bg-blue-600 text-white font-semibold px-5 py-2.5 rounded-xl shadow transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Nueva Factura
            </a>
        </div>
    </x-slot>

    <div class="py-6 px-4 sm:px-6 lg:px-8 space-y-6">

        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-800 rounded-xl px-4 py-3 flex items-center gap-3">
                <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                {{ session('success') }}
            </div>
        @endif

        {{-- Stats Cards --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                <p class="text-xs text-gray-500 uppercase font-semibold tracking-wide">Total Facturas</p>
                <p class="text-3xl font-bold text-gray-800 mt-1">{{ $stats['total'] }}</p>
            </div>
            <div class="bg-green-50 rounded-2xl border border-green-100 shadow-sm p-5">
                <p class="text-xs text-green-600 uppercase font-semibold tracking-wide">Pagadas</p>
                <p class="text-3xl font-bold text-green-700 mt-1">{{ $stats['paid'] }}</p>
            </div>
            <div class="bg-yellow-50 rounded-2xl border border-yellow-100 shadow-sm p-5">
                <p class="text-xs text-yellow-600 uppercase font-semibold tracking-wide">Pendientes</p>
                <p class="text-3xl font-bold text-yellow-700 mt-1">{{ $stats['pending'] }}</p>
            </div>
            <div class="bg-blue-50 rounded-2xl border border-blue-100 shadow-sm p-5">
                <p class="text-xs text-blue-600 uppercase font-semibold tracking-wide">Ingresos Pagados</p>
                <p class="text-2xl font-bold text-blue-700 mt-1">{{ \App\Models\Setting::get('currency_symbol', 'S/') }}
                    {{ number_format($stats['revenue'], 2) }}</p>
            </div>
        </div>

        {{-- Invoices Table --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            @if($invoices->isEmpty())
                <div class="py-20 text-center text-gray-400">
                    <svg class="w-14 h-14 mx-auto mb-4 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <p class="text-lg font-semibold">Sin facturas registradas</p>
                    <p class="text-sm mt-1">Crea la primera factura para una cita completada.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-gray-500 uppercase text-xs font-semibold tracking-wider">
                            <tr>
                                <th class="px-6 py-4 text-left">#</th>
                                <th class="px-6 py-4 text-left">Paciente</th>
                                <th class="px-6 py-4 text-left">Médico</th>
                                <th class="px-6 py-4 text-left">Fecha Cita</th>
                                <th class="px-6 py-4 text-right">Monto</th>
                                <th class="px-6 py-4 text-center">Método</th>
                                <th class="px-6 py-4 text-center">Estado</th>
                                <th class="px-6 py-4 text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($invoices as $invoice)
                                <tr class="hover:bg-gray-50/50 transition">
                                    <td class="px-6 py-4 text-gray-400 font-mono">{{ $invoice->id }}</td>
                                    <td class="px-6 py-4 font-medium text-gray-800">
                                        {{ $invoice->appointment->patient->name ?? '—' }}
                                    </td>
                                    <td class="px-6 py-4 text-gray-600">
                                        Dr. {{ $invoice->appointment->doctor->name ?? '—' }}
                                    </td>
                                    <td class="px-6 py-4 text-gray-600">
                                        {{ $invoice->appointment->date?->format('d/m/Y H:i') ?? '—' }}
                                    </td>
                                    <td class="px-6 py-4 text-right font-semibold text-gray-800">
                                        {{ \App\Models\Setting::get('currency_symbol', 'S/') }}
                                        {{ number_format($invoice->amount, 2) }}
                                    </td>
                                    <td class="px-6 py-4 text-center text-gray-600">
                                        {{ $invoice->payment_method_label }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @php $color = $invoice->status_color; @endphp
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold
                                                                    bg-{{ $color }}-100 text-{{ $color }}-700">
                                            {{ $invoice->status_label }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center justify-center gap-2">
                                            <a href="{{ route('invoices.show', $invoice) }}" title="Ver"
                                                class="p-1.5 rounded-lg text-gray-500 hover:bg-blue-50 hover:text-blue-600 transition">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M2.458 12C3.732 7.943 7.522 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.478 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </a>
                                            <a href="{{ route('invoices.edit', $invoice) }}" title="Editar"
                                                class="p-1.5 rounded-lg text-gray-500 hover:bg-yellow-50 hover:text-yellow-600 transition">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </a>
                                            <a href="{{ route('invoices.pdf', $invoice) }}" title="Descargar PDF"
                                                class="p-1.5 rounded-lg text-gray-500 hover:bg-green-50 hover:text-green-600 transition">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                            </a>
                                            <form method="POST" action="{{ route('invoices.destroy', $invoice) }}"
                                                onsubmit="return confirm('¿Eliminar esta factura?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" title="Eliminar"
                                                    class="p-1.5 rounded-lg text-gray-500 hover:bg-red-50 hover:text-red-600 transition">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-4 border-t border-gray-100">
                    {{ $invoices->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>