<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('reports.index') }}" class="p-2 rounded-lg text-gray-400 hover:bg-gray-100 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Reporte de Ingresos</h1>
                    <p class="text-sm text-gray-500 mt-1">Filtra y exporta el historial de facturación</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('reports.revenue.pdf', request()->query()) }}"
                   class="inline-flex items-center gap-2 border border-red-300 text-red-600 hover:bg-red-50 font-semibold px-4 py-2 rounded-xl text-sm transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                    PDF
                </a>
                <a href="{{ route('reports.revenue.excel', request()->query()) }}"
                   class="inline-flex items-center gap-2 border border-green-300 text-green-600 hover:bg-green-50 font-semibold px-4 py-2 rounded-xl text-sm transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Excel
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6 px-4 sm:px-6 lg:px-8 space-y-6">

        {{-- Totals --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                <p class="text-xs text-gray-400 uppercase font-semibold tracking-wide">Total Facturas</p>
                <p class="text-3xl font-bold text-gray-800 mt-1">{{ $totals['count'] }}</p>
            </div>
            <div class="bg-green-50 rounded-2xl border border-green-100 shadow-sm p-5">
                <p class="text-xs text-green-600 uppercase font-semibold tracking-wide">Total Pagado</p>
                <p class="text-2xl font-bold text-green-700 mt-1">S/ {{ number_format($totals['paid'], 2) }}</p>
            </div>
            <div class="bg-yellow-50 rounded-2xl border border-yellow-100 shadow-sm p-5">
                <p class="text-xs text-yellow-600 uppercase font-semibold tracking-wide">Pendiente</p>
                <p class="text-2xl font-bold text-yellow-700 mt-1">S/ {{ number_format($totals['pending'], 2) }}</p>
            </div>
            <div class="bg-red-50 rounded-2xl border border-red-100 shadow-sm p-5">
                <p class="text-xs text-red-600 uppercase font-semibold tracking-wide">Cancelado</p>
                <p class="text-2xl font-bold text-red-700 mt-1">S/ {{ number_format($totals['cancelled'], 2) }}</p>
            </div>
        </div>

        {{-- Filters --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <form method="GET" action="{{ route('reports.revenue') }}" class="flex flex-wrap gap-3 items-end">
                <div class="flex flex-col gap-1">
                    <label class="text-xs font-semibold text-gray-500">Desde</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}"
                           class="border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                <div class="flex flex-col gap-1">
                    <label class="text-xs font-semibold text-gray-500">Hasta</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}"
                           class="border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                <div class="flex flex-col gap-1">
                    <label class="text-xs font-semibold text-gray-500">Método de Pago</label>
                    <select name="payment_method" class="border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                        <option value="">Todos</option>
                        <option value="cash"     {{ request('payment_method') === 'cash'     ? 'selected' : '' }}>Efectivo</option>
                        <option value="card"     {{ request('payment_method') === 'card'     ? 'selected' : '' }}>Tarjeta</option>
                        <option value="transfer" {{ request('payment_method') === 'transfer' ? 'selected' : '' }}>Transferencia</option>
                    </select>
                </div>
                <div class="flex flex-col gap-1">
                    <label class="text-xs font-semibold text-gray-500">Estado</label>
                    <select name="status" class="border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                        <option value="">Todos</option>
                        <option value="pending"   {{ request('status') === 'pending'   ? 'selected' : '' }}>Pendiente</option>
                        <option value="paid"      {{ request('status') === 'paid'      ? 'selected' : '' }}>Pagado</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelado</option>
                    </select>
                </div>
                <button type="submit"
                        class="bg-[#4A88F6] hover:bg-blue-600 text-white font-semibold px-5 py-2 rounded-xl text-sm transition">
                    Filtrar
                </button>
                <a href="{{ route('reports.revenue') }}"
                   class="border border-gray-200 text-gray-500 hover:bg-gray-50 font-semibold px-4 py-2 rounded-xl text-sm transition">
                    Limpiar
                </a>
            </form>
        </div>

        {{-- Table --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            @if($invoices->isEmpty())
                <div class="py-16 text-center text-sm text-gray-400">Sin resultados para los filtros aplicados.</div>
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
                                <th class="px-6 py-4 text-left">Registrado</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($invoices as $inv)
                                @php $color = $inv->status_color; @endphp
                                <tr class="hover:bg-gray-50/50 transition">
                                    <td class="px-6 py-3 text-gray-400 font-mono">{{ $inv->id }}</td>
                                    <td class="px-6 py-3 font-medium text-gray-800">{{ $inv->appointment->patient->name ?? '—' }}</td>
                                    <td class="px-6 py-3 text-gray-600">Dr. {{ $inv->appointment->doctor->name ?? '—' }}</td>
                                    <td class="px-6 py-3 text-gray-600">{{ $inv->appointment?->date?->format('d/m/Y') ?? '—' }}</td>
                                    <td class="px-6 py-3 text-right font-semibold text-gray-800">S/ {{ number_format($inv->amount, 2) }}</td>
                                    <td class="px-6 py-3 text-center text-gray-600">{{ $inv->payment_method_label }}</td>
                                    <td class="px-6 py-3 text-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-{{ $color }}-100 text-{{ $color }}-700">
                                            {{ $inv->status_label }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-3 text-gray-500">{{ $inv->created_at->format('d/m/Y') }}</td>
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
