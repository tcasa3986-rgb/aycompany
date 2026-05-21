<x-app-layout>
    <x-slot name="header">Mis Recibos</x-slot>

    <div class="max-w-5xl mx-auto space-y-8 pb-10">
        <div
            class="flex flex-col md:flex-row md:items-end justify-between gap-6 bg-white p-8 rounded-3xl shadow-[0_2px_10px_-3px_rgba(6,81,237,0.05)] border border-gray-100">
            <div>
                <h1 class="text-2xl font-extrabold text-gray-900 tracking-tight">Historial de Facturación</h1>
                <p class="text-[15px] text-gray-500 mt-1 max-w-xl">
                    Revisa y descarga tus recibos por consultas médicas y servicios en cualquier momento.
                </p>
            </div>
        </div>

        <div>
            @if($invoices->isEmpty())
                <div
                    class="bg-white rounded-3xl p-16 text-center shadow-[0_2px_10px_-3px_rgba(6,81,237,0.05)] border border-gray-100">
                    <div
                        class="w-24 h-24 bg-gray-50/80 rounded-[2rem] flex items-center justify-center mx-auto mb-6 border border-gray-100/50">
                        <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2 tracking-tight">Registro Vacío</h3>
                    <p class="text-[15px] text-gray-500 max-w-sm mx-auto">Aún no se ha generado ningún recibo o factura a tu
                        nombre.</p>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($invoices as $invoice)
                        <div
                            class="bg-white rounded-3xl p-6 sm:p-8 hover:shadow-[0_8px_30px_rgb(0,0,0,0.04)] hover:border-blue-100 transition-all duration-300 border border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-6 group relative overflow-hidden">

                            {{-- Decorative Background element for status --}}
                            <div
                                class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-bl from-gray-50 to-transparent opacity-50 rounded-bl-[100px] pointer-events-none">
                            </div>

                            <div class="flex items-start gap-5 relative z-10 w-full lg:w-3/5">
                                <div
                                    class="w-16 h-16 shrink-0 rounded-[1.25rem] bg-gray-50 text-gray-400 border border-gray-100/80 shadow-inner flex items-center justify-center group-hover:scale-105 transition-transform duration-300">
                                    <svg class="w-8 h-8 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                </div>
                                <div class="pt-1">
                                    <div class="flex flex-wrap items-center gap-3 mb-1.5">
                                        <h3 class="font-bold text-gray-900 text-lg tracking-tight">Consulta Dr.
                                            {{ $invoice->appointment->doctor->user->name ?? 'N/A' }}
                                        </h3>
                                        @php
                                            $c = $invoice->status === 'paid' ? 'green' : ($invoice->status === 'pending' ? 'yellow' : 'red');
                                            $label = $invoice->status === 'paid' ? 'Pagada' : ($invoice->status === 'pending' ? 'Pendiente' : 'Cancelada');
                                        @endphp
                                        <span
                                            class="px-2.5 py-1 rounded-lg text-[11px] font-bold bg-{{ $c }}-50 text-{{ $c }}-700 ring-1 ring-inset ring-{{ $c }}-200/50">
                                            {{ $label }}
                                        </span>
                                    </div>
                                    <div
                                        class="flex flex-wrap items-center gap-x-4 gap-y-2 text-[15px] font-medium text-gray-500">
                                        <span>Cita del
                                            {{ \Carbon\Carbon::parse($invoice->appointment->date)->format('d/m/Y') }}</span>
                                    </div>
                                    <div
                                        class="text-sm font-medium text-gray-400 mt-2.5 flex items-center gap-1.5 bg-gray-50 w-fit px-2.5 py-1 rounded-lg font-mono">
                                        Factura No: {{ str_pad($invoice->id, 5, '0', STR_PAD_LEFT) }} | Emitida:
                                        {{ \Carbon\Carbon::parse($invoice->created_at)->format('d/m/Y') }}
                                    </div>
                                </div>
                            </div>

                            <div
                                class="flex flex-col sm:flex-row sm:items-center justify-end gap-4 sm:gap-6 relative z-10 w-full lg:w-2/5 mt-4 sm:mt-0 pt-4 sm:pt-0 border-t sm:border-t-0 border-gray-50">
                                <div class="text-left sm:text-right">
                                    <p class="text-sm text-gray-400 font-medium mb-0.5">Total a pagar</p>
                                    <span
                                        class="text-2xl font-extrabold tracking-tight {{ $invoice->status === 'paid' ? 'text-gray-900' : 'text-gray-500' }}">
                                        {{ \App\Models\Setting::get('currency_symbol', '$') }}{{ number_format($invoice->amount, 2) }}
                                    </span>
                                </div>

                                <div class="flex items-center gap-3">
                                    @if($invoice->status === 'pending')
                                        <form action="{{ route('portal.invoices.checkout', $invoice) }}" method="POST">
                                            @csrf
                                            <button type="submit"
                                                class="px-5 py-2.5 bg-blue-600 text-white rounded-xl text-[14px] font-bold hover:bg-blue-700 transition-all shadow-[0_4px_10px_rgb(6,81,237,0.2)] hover:shadow-[0_4px_15px_rgb(6,81,237,0.3)] hover:-translate-y-0.5 flex items-center justify-center gap-2">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z">
                                                    </path>
                                                </svg>
                                                Pagar
                                            </button>
                                        </form>
                                    @endif

                                    <a href="{{ route('invoices.pdf', $invoice) }}" target="_blank"
                                        class="px-4 py-2.5 bg-white text-gray-600 rounded-xl text-[14px] font-bold border border-gray-200 hover:border-gray-300 hover:bg-gray-50 hover:text-gray-900 transition-all shadow-sm hover:shadow flex items-center justify-center gap-2"
                                        title="Descargar PDF">
                                        <svg class="w-4 h-4 text-red-500" fill="currentColor" viewBox="0 0 24 24">
                                            <path
                                                d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 14H9v-2H8v-2h1V9c0-1.1.9-2 2-2h3v2h-3v5h2v2h-2v2zm6-4h-2v2h2v2h-3V8h3v2h-2v2h2v2z" />
                                        </svg>
                                        PDF
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if($invoices->hasPages())
                    <div class="pt-8">
                        {{ $invoices->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
</x-app-layout>