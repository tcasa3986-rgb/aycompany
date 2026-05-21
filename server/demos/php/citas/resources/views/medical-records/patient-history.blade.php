<x-app-layout>
    <x-slot name="header">Historial Clínico — {{ $patient->user->name }}</x-slot>

    <div class="max-w-4xl mx-auto">
        {{-- Patient header --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div
                        class="w-14 h-14 rounded-2xl bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-xl">
                        {{ strtoupper(substr($patient->user->name, 0, 1)) }}
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-gray-900">{{ $patient->user->name }}</h2>
                        <p class="text-sm text-gray-400">{{ $patient->user->email }}
                            @if($patient->dob) · {{ \Carbon\Carbon::parse($patient->dob)->age }} años @endif
                        </p>
                    </div>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('medical-records.create', ['patient_id' => $patient->id]) }}"
                        class="inline-flex items-center gap-2 bg-blue-500 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-600 transition shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Nuevo Registro
                    </a>
                    <a href="{{ route('patients.show', $patient) }}"
                        class="px-4 py-2 bg-gray-50 text-gray-600 rounded-lg text-sm hover:bg-gray-100 transition">
                        Ver Paciente
                    </a>
                </div>
            </div>
        </div>

        {{-- Timeline --}}
        @if($records->isEmpty())
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-14 text-center text-gray-300">
                <svg class="w-12 h-12 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <p class="text-sm">No hay registros clínicos para este paciente.</p>
            </div>
        @else
            <div class="relative">
                {{-- Timeline line --}}
                <div class="absolute left-6 top-0 bottom-0 w-0.5 bg-gray-100"></div>

                <div class="space-y-5">
                    @foreach($records as $rec)
                        <div class="pl-16 relative">
                            {{-- Timeline dot --}}
                            <div class="absolute left-4 top-5 w-4 h-4 rounded-full bg-blue-400 border-4 border-white shadow-sm">
                            </div>

                            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition">
                                <div class="flex items-start justify-between mb-3">
                                    <div>
                                        <span
                                            class="text-xs text-gray-400 font-mono">{{ $rec->record_date->format('d/m/Y') }}</span>
                                        <p class="text-xs text-gray-400 mt-0.5">Dr. {{ $rec->doctor->user->name }}</p>
                                    </div>
                                    <a href="{{ route('medical-records.show', $rec) }}"
                                        class="text-xs text-blue-500 hover:underline">Ver detalle →</a>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                                    <div>
                                        <p class="text-xs text-gray-400 mb-0.5">Motivo</p>
                                        <p class="text-gray-700 line-clamp-2">{{ $rec->chief_complaint }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-400 mb-0.5">Diagnóstico</p>
                                        <p class="text-gray-900 font-medium line-clamp-2">{{ $rec->diagnosis }}</p>
                                    </div>
                                </div>

                                @if($rec->prescriptions)
                                    <div class="mt-3 pt-3 border-t border-gray-50 flex items-start gap-2">
                                        <svg class="w-4 h-4 text-amber-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <p class="text-xs text-gray-500 line-clamp-1">{{ $rec->prescriptions }}</p>
                                    </div>
                                @endif

                                @if($rec->attachments && $rec->attachments->count() > 0)
                                    <div class="mt-3 pt-3 border-t border-gray-50">
                                        <p class="text-xs text-gray-400 mb-2">Archivos Adjuntos</p>
                                        <div class="flex flex-wrap gap-2">
                                            @foreach($rec->attachments as $attachment)
                                                <a href="{{ route('medical-records.attachments.download', $attachment) }}" 
                                                   class="inline-flex items-center gap-1.5 px-2.5 py-1.5 bg-gray-50 border border-gray-200 hover:border-gray-300 hover:bg-gray-100 rounded-md text-xs font-medium text-gray-600 transition group"
                                                   title="Descargar {{ $attachment->file_name }}">
                                                    @if(in_array(strtolower(pathinfo($attachment->file_name, PATHINFO_EXTENSION)), ['jpg','jpeg','png']))
                                                        <svg class="w-4 h-4 text-blue-500" fill="currentColor" viewBox="0 0 24 24"><path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z"/></svg>
                                                    @else
                                                        <svg class="w-4 h-4 text-red-500" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 14H9v-2H8v-2h1V9c0-1.1.9-2 2-2h3v2h-3v5h2v2h-2v2zm6-4h-2v2h2v2h-3V8h3v2h-2v2h2v2z"/></svg>
                                                    @endif
                                                    <span class="max-w-[120px] truncate">{{ $attachment->file_name }}</span>
                                                    <svg class="w-3 h-3 text-gray-400 opacity-0 group-hover:opacity-100 transition-opacity ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                @if($rec->is_private)
                                    <span class="mt-3 inline-flex items-center gap-1 text-xs text-amber-600">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                        </svg>
                                        Privado
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</x-app-layout>