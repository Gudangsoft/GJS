<div class="relative" x-data="{ bellOpen: @entangle('open') }">

    {{-- Bell button --}}
    <button wire:click="toggle"
            class="relative flex items-center justify-center w-9 h-9 rounded-lg text-blue-200 hover:text-white hover:bg-blue-800 transition-colors focus:outline-none"
            aria-label="Notifikasi">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
        </svg>
        @if($unreadCount > 0)
        <span class="absolute -top-0.5 -right-0.5 flex items-center justify-center min-w-[1.1rem] h-[1.1rem] px-0.5 rounded-full bg-red-500 text-white text-[10px] font-bold leading-none">
            {{ $unreadCount > 99 ? '99+' : $unreadCount }}
        </span>
        @endif
    </button>

    {{-- Dropdown --}}
    @if($open)
    <div class="absolute right-0 mt-2 w-80 sm:w-96 bg-white rounded-xl shadow-lg border border-slate-200 z-50"
         style="top: calc(100% + 4px);">

        {{-- Header --}}
        <div class="flex items-center justify-between px-4 py-3 border-b border-slate-100">
            <h3 class="text-sm font-semibold text-slate-900">Notifikasi</h3>
            <div class="flex items-center gap-2">
                @if($unreadCount > 0)
                <button wire:click="markAllRead"
                        class="text-xs text-blue-600 hover:text-blue-700 font-medium transition-colors">
                    Tandai semua dibaca
                </button>
                @endif
                <button wire:click="toggle" class="text-slate-400 hover:text-slate-600 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- List --}}
        <div class="max-h-96 overflow-y-auto divide-y divide-slate-50">
            @forelse($notifications as $notif)
            @php
                $data = $notif['data'];
                $isUnread = ! $notif['read'];
                $icon = $data['icon'] ?? 'clock';
                $url  = $data['url'] ?? '#';
            @endphp
            <div class="flex items-start gap-3 px-4 py-3 {{ $isUnread ? 'bg-blue-50/60' : 'hover:bg-slate-50' }} transition-colors">

                {{-- Icon --}}
                <div class="shrink-0 mt-0.5 w-8 h-8 rounded-full flex items-center justify-center
                    {{ match($icon) {
                        'check'    => 'bg-green-100 text-green-600',
                        'x-circle' => 'bg-red-100 text-red-600',
                        'pencil'   => 'bg-amber-100 text-amber-600',
                        default    => 'bg-blue-100 text-blue-600',
                    } }}">
                    @if($icon === 'check')
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    @elseif($icon === 'x-circle')
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    @elseif($icon === 'pencil')
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                    </svg>
                    @else
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    @endif
                </div>

                {{-- Content --}}
                <div class="flex-1 min-w-0">
                    <a href="{{ $url }}"
                       wire:click="markRead('{{ $notif['id'] }}')"
                       class="block">
                        <p class="text-sm {{ $isUnread ? 'font-semibold text-slate-900' : 'font-medium text-slate-700' }} leading-snug truncate">
                            {{ $data['submission_title'] ?? $data['message'] ?? 'Notifikasi baru' }}
                        </p>
                        @if(!empty($data['message']))
                        <p class="text-xs text-slate-500 mt-0.5 line-clamp-2">{{ $data['message'] }}</p>
                        @elseif(!empty($data['status']))
                        <p class="text-xs text-slate-500 mt-0.5">Status: {{ $data['status'] }}</p>
                        @endif
                        <p class="text-xs text-slate-400 mt-1">{{ $notif['time'] }}</p>
                    </a>
                </div>

                {{-- Unread dot --}}
                @if($isUnread)
                <div class="shrink-0 mt-2 w-2 h-2 rounded-full bg-blue-500"></div>
                @endif
            </div>
            @empty
            <div class="px-4 py-10 text-center">
                <svg class="w-10 h-10 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                <p class="text-sm text-slate-400">Belum ada notifikasi</p>
            </div>
            @endforelse
        </div>

        {{-- Footer --}}
        <div class="border-t border-slate-100 px-4 py-2.5">
            <a href="{{ route('notifications.index') }}"
               class="block text-center text-sm text-blue-600 hover:text-blue-700 font-medium transition-colors">
                Lihat semua notifikasi
            </a>
        </div>
    </div>
    @endif

</div>
