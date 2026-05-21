<div x-data="{ open: false }" class="relative" @click.outside="open = false">
    @php
        $unreadCount = auth()->user()->unreadNotifications->count();
        $recent = auth()->user()->notifications()->latest()->take(5)->get();
    @endphp

    {{-- Bell button --}}
    <button @click="open = !open"
        class="relative p-2 rounded-xl text-gray-500 hover:bg-gray-100 transition focus:outline-none">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
        </svg>
        @if($unreadCount > 0)
            <span
                class="absolute top-1 right-1 w-4 h-4 bg-red-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center">
                {{ $unreadCount > 9 ? '9+' : $unreadCount }}
            </span>
        @endif
    </button>

    {{-- Dropdown --}}
    <div x-show="open" x-transition
        class="absolute right-0 mt-2 w-80 bg-white border border-gray-100 rounded-2xl shadow-lg z-50 overflow-hidden"
        style="display:none">

        {{-- Header --}}
        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100">
            <span class="text-sm font-semibold text-gray-800">Notificaciones</span>
            @if($unreadCount > 0)
                <form method="POST" action="{{ route('notifications.mark-read') }}">
                    @csrf
                    <button type="submit" class="text-xs text-blue-500 hover:underline">
                        Marcar todas como leídas
                    </button>
                </form>
            @endif
        </div>

        {{-- Notification list --}}
        <div class="divide-y divide-gray-50 max-h-80 overflow-y-auto">
            @forelse($recent as $notif)
                @php
                    $data = $notif->data;
                    $isNew = !$notif->read_at;
                    $icons = ['appointment_confirmed' => '✅', 'appointment_cancelled' => '❌', 'appointment_reminder' => '⏰'];
                    $icon = $icons[$data['type'] ?? ''] ?? '🔔';
                @endphp
                <div class="px-4 py-3 {{ $isNew ? 'bg-blue-50/50' : '' }} hover:bg-gray-50 transition">
                    <div class="flex items-start gap-2">
                        <span class="text-base flex-shrink-0 mt-0.5">{{ $icon }}</span>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-semibold text-gray-800 leading-tight">
                                {{ $data['title'] ?? 'Notificación' }}</p>
                            <p class="text-xs text-gray-500 mt-0.5 leading-snug">{{ $data['message'] ?? '' }}</p>
                            <p class="text-[10px] text-gray-400 mt-1">{{ $notif->created_at->diffForHumans() }}</p>
                        </div>
                        @if($isNew)
                            <span class="w-2 h-2 rounded-full bg-blue-500 flex-shrink-0 mt-1"></span>
                        @endif
                    </div>
                </div>
            @empty
                <div class="py-8 text-center text-sm text-gray-400">Sin notificaciones</div>
            @endforelse
        </div>
    </div>
</div>