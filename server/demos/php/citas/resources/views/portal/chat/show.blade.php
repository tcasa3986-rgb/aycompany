<x-app-layout>
    <x-slot name="header">Chat con Dr. {{ $doctor->name }}</x-slot>

    <div class="max-w-4xl mx-auto space-y-6 pb-12 flex flex-col min-h-[calc(100vh-140px)]">
        
        {{-- Chat Header --}}
        <div class="flex items-center justify-between bg-white p-4 sm:p-6 rounded-3xl shadow-[0_2px_10px_-3px_rgba(6,81,237,0.05)] border border-gray-100 sticky top-0 z-10 shrink-0">
            <div class="flex items-center gap-4">
                <a href="{{ route('portal.chat.index') }}" class="p-2 -ml-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-xl transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                </a>
                <div class="relative">
                    <img src="{{ $doctor->avatar_url }}" alt="Dr. {{ $doctor->name }}" class="w-12 h-12 rounded-xl object-cover shadow-sm border border-gray-100">
                    <span class="absolute bottom-0 right-0 block h-3 w-3 rounded-full bg-green-400 ring-2 ring-white"></span>
                </div>
                <div>
                    <h1 class="text-lg font-bold text-gray-900 leading-tight">Dr. {{ $doctor->name }}</h1>
                    <p class="text-[13px] text-gray-500 font-medium">En línea</p>
                </div>
            </div>
        </div>

        {{-- Messages Container --}}
        <div class="flex-1 bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden flex flex-col relative">
            <div class="absolute inset-0 bg-gray-50/50 pointer-events-none"></div>
            
            <div class="flex-1 overflow-y-auto p-4 sm:p-6 space-y-6 relative z-10" id="chatbox">
                @if($messages->isEmpty())
                    <div class="h-full flex flex-col items-center justify-center text-center p-8">
                        <div class="w-16 h-16 bg-blue-50 text-blue-500 rounded-full flex items-center justify-center mb-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" /></svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-1">Inicia la conversación</h3>
                        <p class="text-sm text-gray-500 max-w-xs">Escríbele al Dr. {{ $doctor->name }} para consultar dudas médicas breves.</p>
                    </div>
                @else
                    @php $currentDate = null; @endphp
                    @foreach($messages as $msg)
                        @php 
                            $msgDate = $msg->created_at->format('Y-m-d'); 
                            $isPatient = $msg->sender_id === auth()->id();
                        @endphp
                        
                        @if($currentDate !== $msgDate)
                            <div class="flex justify-center my-4">
                                <span class="bg-gray-100 text-gray-500 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wide">
                                    {{ $msg->created_at->isToday() ? 'Hoy' : ($msg->created_at->isYesterday() ? 'Ayer' : $msg->created_at->translatedFormat('d M Y')) }}
                                </span>
                            </div>
                            @php $currentDate = $msgDate; @endphp
                        @endif

                        <div class="flex {{ $isPatient ? 'justify-end' : 'justify-start' }} group/msg">
                            @if(!$isPatient)
                                <img src="{{ $doctor->avatar_url }}" alt="" class="w-8 h-8 rounded-lg object-cover mr-2 self-end mb-1 opacity-80 shrink-0">
                            @endif
                            
                            <div class="max-w-[75%] sm:max-w-[65%] flex flex-col {{ $isPatient ? 'items-end' : 'items-start' }}">
                                <div class="px-5 py-3 rounded-[1.25rem] shadow-sm {{ $isPatient ? 'bg-blue-600 text-white rounded-br-sm' : 'bg-gray-100/80 text-gray-800 rounded-bl-sm border border-gray-200/50' }} relative">
                                    <p class="text-[15px] leading-relaxed break-words">{{ $msg->content }}</p>
                                </div>
                                <div class="flex items-center gap-1 mt-1.5 px-1 opacity-0 group-hover/msg:opacity-100 transition-opacity">
                                    <span class="text-[11px] font-medium text-gray-400">{{ $msg->created_at->format('H:i') }}</span>
                                    @if($isPatient)
                                        <svg class="w-3.5 h-3.5 {{ $msg->read_at ? 'text-blue-500' : 'text-gray-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
            <div class="bg-white border-t border-gray-100 p-4 sm:p-5 shrink-0 z-10 w-full relative">
                <form action="{{ route('portal.chat.store', $doctor) }}" method="POST" class="flex items-end gap-3 max-w-full">
                    @csrf
                    <div class="flex-1 relative">
                        <textarea name="content" rows="1" required placeholder="Escribe tu mensaje aquí..."
                            class="w-full rounded-2xl border-gray-200 pl-4 pr-12 py-3.5 text-[15px] focus:outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all shadow-sm bg-gray-50/50 hover:bg-gray-50 resize-none max-h-32 min-h-[52px]"
                            oninput="this.style.height = ''; this.style.height = Math.min(this.scrollHeight, 128) + 'px'"></textarea>
                    </div>
                    <button type="submit" class="shrink-0 w-[52px] h-[52px] bg-blue-600 text-white rounded-2xl flex items-center justify-center hover:bg-blue-700 transition-all shadow-[0_4px_10px_rgb(6,81,237,0.2)] hover:shadow-[0_4px_15px_rgb(6,81,237,0.3)] hover:-translate-y-0.5 group">
                        <svg class="w-5 h-5 ml-1 transform group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                        </svg>
                    </button>
                </form>
                @error('content') <p class="text-red-500 text-xs mt-2 font-medium ml-2">{{ $message }}</p> @enderror
            </div>
        </div>
    </div>

    <script>
        // Auto-scroll to bottom of chat
        const chatbox = document.getElementById('chatbox');
        if (chatbox) {
            chatbox.scrollTop = chatbox.scrollHeight;
        }

        // Allow sending with Enter (Shift+Enter for new line)
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
