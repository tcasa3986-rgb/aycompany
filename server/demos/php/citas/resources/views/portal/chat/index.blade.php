<x-app-layout>
    <x-slot name="header">Mensajes</x-slot>

    <div class="max-w-4xl mx-auto space-y-6 pb-12">
        <div class="flex items-center justify-between bg-white p-6 rounded-3xl shadow-[0_2px_10px_-3px_rgba(6,81,237,0.05)] border border-gray-100">
            <div>
                <h1 class="text-2xl font-extrabold text-gray-900 tracking-tight">Centro de Mensajes</h1>
                <p class="text-[15px] text-gray-500 mt-1">
                    Comunícate directamente con tus médicos tratantes.
                </p>
            </div>
        </div>

        <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden min-h-[400px]">
            @if($availableDoctors->isEmpty())
                <div class="p-16 text-center">
                    <div class="w-24 h-24 bg-gray-50/80 rounded-[2rem] flex items-center justify-center mx-auto mb-6 border border-gray-100/50">
                        <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2 tracking-tight">Sin Conversaciones</h3>
                    <p class="text-[15px] text-gray-500 max-w-sm mx-auto">Aún no has tenido citas con médicos o no hay conversaciones activas.</p>
                </div>
            @else
                <div class="divide-y divide-gray-100">
                    @foreach($availableDoctors as $doctor)
                        @php
                            // Get the last message between patient and doctor to show a preview
                            $lastMessage = \App\Models\Message::between(auth()->id(), $doctor->id)
                                ->orderBy('created_at', 'desc')
                                ->first();
                            
                            $unreadCount = \App\Models\Message::where('sender_id', $doctor->id)
                                ->where('receiver_id', auth()->id())
                                ->whereNull('read_at')
                                ->count();
                        @endphp
                        
                        <a href="{{ route('portal.chat.show', $doctor) }}" class="p-5 sm:p-6 hover:bg-gray-50 transition flex items-center justify-between gap-5 group relative">
                            <div class="flex items-center gap-4 w-full">
                                <div class="relative">
                                    <img src="{{ $doctor->avatar_url }}" alt="Dr. {{ $doctor->name }}" class="w-14 h-14 rounded-2xl object-cover shadow-sm border border-gray-200 group-hover:scale-105 transition-transform">
                                    @if($unreadCount > 0)
                                        <span class="absolute -top-1 -right-1 flex h-5 w-5 items-center justify-center rounded-full bg-red-500 ring-2 ring-white text-[10px] font-bold text-white">
                                            {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                                        </span>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex justify-between items-baseline mb-1">
                                        <h3 class="font-bold text-gray-900 truncate pr-4">Dr. {{ $doctor->name }}</h3>
                                        @if($lastMessage)
                                            <span class="text-xs text-gray-400 font-medium shrink-0">{{ $lastMessage->created_at->diffForHumans(null, true, true) }}</span>
                                        @endif
                                    </div>
                                    <p class="text-sm {{ $unreadCount > 0 ? 'text-gray-900 font-semibold' : 'text-gray-500' }} truncate">
                                        @if($lastMessage)
                                            @if($lastMessage->sender_id === auth()->id())
                                                <span class="text-gray-400 mr-1">Tú:</span>
                                            @endif
                                            {{ Str::limit($lastMessage->content, 40) }}
                                        @else
                                            <span class="text-blue-500 font-medium italic">Iniciar nueva conversación...</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <div class="shrink-0 text-gray-300 group-hover:text-blue-500 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
