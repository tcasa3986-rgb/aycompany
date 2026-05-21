<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="p-2 bg-blue-50 text-blue-600 rounded-xl">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" /></svg>
            </div>
            Mensajes
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-gray-100">
                <div class="border-b border-gray-100 bg-gray-50/50 p-6">
                    <h2 class="text-xl font-bold text-gray-900">Bandeja de Entrada</h2>
                    <p class="text-sm text-gray-500 mt-1">Gestione las consultas y mensajes directos de sus pacientes.</p>
                </div>

                @if($availablePatients->isEmpty())
                    <div class="p-16 text-center">
                        <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-6 border border-gray-100">
                            <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-1">Sin Conversaciones</h3>
                        <p class="text-gray-500 max-w-sm mx-auto">No hay mensajes recientes de pacientes. Los pacientes con los que tenga citas podrán enviarle mensajes aquí.</p>
                    </div>
                @else
                    <div class="divide-y divide-gray-100">
                        @foreach($availablePatients as $patient)
                            @php
                                $lastMessage = \App\Models\Message::between(auth()->id(), $patient->id)
                                    ->orderBy('created_at', 'desc')
                                    ->first();
                                
                                $unreadCount = \App\Models\Message::where('sender_id', $patient->id)
                                    ->where('receiver_id', auth()->id())
                                    ->whereNull('read_at')
                                    ->count();
                            @endphp
                            
                            <a href="{{ route('chat.show', $patient) }}" class="p-6 hover:bg-gray-50 transition flex items-center justify-between gap-6 group">
                                <div class="flex items-center gap-4 w-full">
                                    <div class="relative">
                                        <div class="w-14 h-14 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center text-xl font-bold shadow-sm border border-indigo-100 group-hover:scale-105 transition-transform">
                                            {{ substr($patient->name, 0, 1) }}
                                        </div>
                                        @if($unreadCount > 0)
                                            <span class="absolute -top-1.5 -right-1.5 flex h-5 w-5 items-center justify-center rounded-full bg-red-500 ring-2 ring-white text-[10px] font-bold text-white">
                                                {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                                            </span>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex justify-between items-baseline mb-1">
                                            <h3 class="font-bold text-gray-900 truncate pr-4">{{ $patient->name }}</h3>
                                            @if($lastMessage)
                                                <span class="text-xs text-gray-400 font-medium shrink-0">{{ $lastMessage->created_at->diffForHumans() }}</span>
                                            @endif
                                        </div>
                                        <p class="text-sm {{ $unreadCount > 0 ? 'text-gray-900 font-bold' : 'text-gray-500' }} truncate pr-8">
                                            @if($lastMessage)
                                                @if($lastMessage->sender_id === auth()->id())
                                                    <span class="text-gray-400 mr-1">Tú:</span>
                                                @endif
                                                {{ Str::limit($lastMessage->content, 80) }}
                                            @else
                                                <span class="text-indigo-500 font-medium italic">Sin mensajes previos...</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                <div class="shrink-0 text-gray-300 group-hover:text-indigo-600 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
