<x-app-layout>
    <x-slot name="header">Mi Historial Médico</x-slot>

    <div class="max-w-4xl mx-auto space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Historial Médico</h1>
                <p class="text-sm text-gray-500 mt-1">Revisa el registro de tus consultas, diagnósticos y recetas médicas.</p>
            </div>
        </div>

        @if($records->isEmpty())
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-14 text-center">
                <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-gray-100">
                    <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-1">Aún no hay registros médicos</h3>
                <p class="text-sm text-gray-500">Tu historial clínico aparecerá aquí después de tus consultas médicas.</p>
            </div>
        @else
            <div class="relative">
                {{-- Línea de tiempo --}}
                <div class="absolute left-6 top-0 bottom-0 w-0.5 bg-gray-100 hidden sm:block"></div>

                <div class="space-y-6">
                    @foreach($records as $rec)
                        <div class="sm:pl-16 relative">
                            {{-- Punto de tiempo (oculto en móvil) --}}
                            <div class="hidden sm:block absolute left-4 top-5 w-4 h-4 rounded-full bg-blue-500 border-4 border-white shadow-sm"></div>

                            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 sm:p-6 hover:shadow-md hover:-translate-y-0.5 transition duration-200">
                                <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4 mb-4">
                                    <div class="flex items-start gap-4">
                                        <div class="w-12 h-12 shrink-0 rounded-xl bg-blue-50 text-blue-600 flex flex-col items-center justify-center font-bold sm:hidden">
                                            <span class="text-lg leading-none">{{ $rec->record_date->format('d') }}</span>
                                            <span class="text-[10px] uppercase font-semibold">{{ $rec->record_date->translatedFormat('M') }}</span>
                                        </div>
                                        <div>
                                            <p class="text-sm font-semibold text-gray-900 flex items-center gap-2">
                                                Dr. {{ $rec->doctor->user->name }}
                                                <span class="hidden sm:inline-block w-1 h-1 rounded-full bg-gray-300"></span>
                                                <span class="hidden sm:inline text-gray-500 font-normal">{{ $rec->doctor->specialties->first()->name ?? 'Medicina General' }}</span>
                                            </p>
                                            <p class="text-xs text-gray-400 mt-1 sm:hidden">{{ $rec->doctor->specialties->first()->name ?? 'Medicina General' }}</p>
                                            <p class="text-xs font-mono text-gray-400 mt-1.5 sm:hidden">{{ $rec->record_date->format('d/m/Y') }}</p>
                                        </div>
                                    </div>
                                    <div class="hidden sm:block text-right">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-gray-50 text-gray-600 border border-gray-200 shadow-sm">
                                            <svg class="w-3.5 h-3.5 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                            {{ $rec->record_date->format('d / m / Y') }}
                                        </span>
                                    </div>
                                </div>

                                <div class="box-border border border-gray-100 bg-gray-50/50 rounded-xl p-4 sm:p-5 mb-4 grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                                    <div>
                                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Motivo de Consulta</p>
                                        <p class="text-gray-700 leading-relaxed">{{ $rec->chief_complaint }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Diagnóstico</p>
                                        <p class="text-gray-900 font-medium leading-relaxed">{{ $rec->diagnosis }}</p>
                                    </div>
                                </div>

                                <div class="space-y-3">
                                    @if($rec->treatment)
                                        <div>
                                            <h4 class="text-sm font-semibold text-gray-800 mb-1 flex items-center gap-1.5">
                                                <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                                Indicaciones y Tratamiento
                                            </h4>
                                            <p class="text-sm text-gray-600 leading-relaxed pl-5.5">{{ $rec->treatment }}</p>
                                        </div>
                                    @endif

                                    @if($rec->prescriptions)
                                        <div class="pt-2 border-t border-gray-100">
                                            <h4 class="text-sm font-semibold text-gray-800 mb-2 flex items-center gap-1.5">
                                                <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" /></svg>
                                                Receta Médica
                                            </h4>
                                            <div class="bg-amber-50 rounded-lg p-3 text-sm text-amber-800 border border-amber-100/50">
                                                {{ $rec->prescriptions }}
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                
                                @if($rec->attachments && $rec->attachments->count() > 0)
                                    <div class="mt-4 pt-4 border-t border-gray-100">
                                        <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Archivos Adjuntos</h4>
                                        <div class="flex flex-wrap gap-2">
                                            @foreach($rec->attachments as $attachment)
                                                <a href="{{ route('medical-records.attachments.download', $attachment) }}" 
                                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-50 border border-gray-200 hover:border-gray-300 hover:bg-gray-100 shadow-sm rounded-lg text-xs font-medium text-gray-700 transition group"
                                                   title="Descargar {{ $attachment->file_name }}">
                                                    @if(in_array(strtolower(pathinfo($attachment->file_name, PATHINFO_EXTENSION)), ['jpg','jpeg','png']))
                                                        <svg class="w-4 h-4 text-blue-500" fill="currentColor" viewBox="0 0 24 24"><path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z"/></svg>
                                                    @else
                                                        <svg class="w-4 h-4 text-red-500" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 14H9v-2H8v-2h1V9c0-1.1.9-2 2-2h3v2h-3v5h2v2h-2v2zm6-4h-2v2h2v2h-3V8h3v2h-2v2h2v2z"/></svg>
                                                    @endif
                                                    <span class="max-w-[150px] truncate">{{ $attachment->file_name }}</span>
                                                    <svg class="w-3.5 h-3.5 text-gray-400 opacity-0 group-hover:opacity-100 transition-opacity ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                                
                                {{-- Indicadores Vitales Simplificados --}}
                                @if($rec->blood_pressure || $rec->weight || $rec->height)
                                    <div class="mt-4 pt-3 border-t border-gray-100 flex flex-wrap gap-x-4 gap-y-2 text-xs text-gray-500">
                                        @if($rec->blood_pressure)<span><strong>PA:</strong> {{ $rec->blood_pressure }}</span>@endif
                                        @if($rec->heart_rate)<span><strong>FC:</strong> {{ $rec->heart_rate }}</span>@endif
                                        @if($rec->weight)<span><strong>Peso:</strong> {{ $rec->weight }}</span>@endif
                                        @if($rec->height)<span><strong>Talla:</strong> {{ $rec->height }}</span>@endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
                
                @if($records->hasPages())
                    <div class="mt-6">
                        {{ $records->links() }}
                    </div>
                @endif
            </div>
        @endif
    </div>
</x-app-layout>
