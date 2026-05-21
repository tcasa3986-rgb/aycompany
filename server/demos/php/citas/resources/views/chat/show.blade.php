<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('chat.index') }}" class="p-2 -ml-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-xl transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
            </a>
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-indigo-50 text-indigo-600 rounded-lg flex items-center justify-center font-bold border border-indigo-100">
                    {{ substr($patient->name, 0, 1) }}
                </div>
                Conversación con {{ $patient->name }}
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-gray-100 flex flex-col h-[calc(100vh-200px)]">
                
                {{-- Messages Area --}}
                <div class="flex-1 overflow-y-auto p-4 sm:p-6 space-y-6 bg-gray-50/30" id="chatbox">
                    @if($messages->isEmpty())
                        <div class="h-full flex flex-col items-center justify-center text-center p-8">
                            <div class="w-16 h-16 bg-indigo-50 text-indigo-500 rounded-full flex items-center justify-center mb-4">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" /></svg>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900 mb-1">Sin mensajes previos</h3>
                            <p class="text-sm text-gray-500 max-w-xs">Envía un mensaje para iniciar la conversación con este paciente.</p>
                        </div>
                    @else
                        @php $currentDate = null; @endphp
                        @foreach($messages as $msg)
                            @php 
                                $msgDate = $msg->created_at->format('Y-m-d'); 
                                $isDoctor = $msg->sender_id === auth()->id();
                            @endphp
                            
                            @if($currentDate !== $msgDate)
                                <div class="flex justify-center my-4">
                                    <span class="bg-gray-100 text-gray-500 text-[11px] font-bold px-3 py-1 rounded-full uppercase tracking-wider">
                                        {{ $msg->created_at->isToday() ? 'Hoy' : ($msg->created_at->isYesterday() ? 'Ayer' : $msg->created_at->translatedFormat('d M Y')) }}
                                    </span>
                                </div>
                                @php $currentDate = $msgDate; @endphp
                            @endif

                            <div class="flex {{ $isDoctor ? 'justify-end' : 'justify-start' }} group/msg">
                                @if(!$isDoctor)
                                    <div class="w-8 h-8 bg-indigo-50 text-indigo-600 rounded-lg flex items-center justify-center text-xs font-bold mr-2 self-end mb-1 shrink-0 border border-indigo-100">
                                         {{ substr($patient->name, 0, 1) }}
                                    </div>
                                @endif
                                
                                <div class="max-w-[75%] sm:max-w-[65%] flex flex-col {{ $isDoctor ? 'items-end' : 'items-start' }}">
                                    <div class="px-5 py-3 rounded-2xl shadow-sm {{ $isDoctor ? 'bg-indigo-600 text-white rounded-br-sm' : 'bg-white border border-gray-200 text-gray-800 rounded-bl-sm' }} relative">
                                        <p class="text-[15px] leading-relaxed break-words">{{ $msg->content }}</p>
                                    </div>
                                    <div class="flex items-center gap-1 mt-1.5 px-1 opacity-0 group-hover/msg:opacity-100 transition-opacity">
                                        <span class="text-[11px] font-medium text-gray-400">{{ $msg->created_at->format('H:i') }}</span>
                                        @if($isDoctor)
                                            <svg class="w-3.5 h-3.5 {{ $msg->read_at ? 'text-indigo-500' : 'text-gray-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                @if($msg->read_at)
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 18l4 4L19 12" class="opacity-50" />
                                                @endif
                                            </svg>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>

                {{-- Input Area --}}
                <div class="bg-white border-t border-gray-100 p-4 shrink-0">
                    <form action="{{ route('chat.store', $patient) }}" method="POST" class="flex items-end gap-3">
                        @csrf
                        <div class="flex-1 relative">
                            <textarea name="content" rows="1" required placeholder="Escribir respuesta al paciente..."
                                class="w-full rounded-xl border-gray-200 pl-4 pr-12 py-3 text-[15px] focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all shadow-sm bg-gray-50 hover:bg-white resize-none max-h-32 min-h-[48px]"
                                oninput="this.style.height = ''; this.style.height = Math.min(this.scrollHeight, 128) + 'px'"></textarea>
                        </div>
                        <button type="submit" class="shrink-0 w-12 h-12 bg-indigo-600 text-white rounded-xl flex items-center justify-center hover:bg-indigo-700 transition-colors shadow-sm group">
                            <svg class="w-5 h-5 ml-0.5 transform group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                            </svg>
                        </button>
                    </form>
                    @error('content') <p class="text-red-500 text-xs mt-2 font-medium ml-2">{{ $message }}</p> @enderror
                </div>
                
            </div>
        </div>
    </div>

    <script>
        const chatbox = document.getElementById('chatbox');
        if (chatbox) {
            chatbox.scrollTop = chatbox.scrollHeight;
        }

        document.querySelector('textarea[name="content"]')?.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                if (this.value.trim() !== '') {
                    this.closest('form').submit();
                }
            }
        });
    </script>
</x-app-layout>
