<div class="max-w-3xl mx-auto px-4 sm:px-6 py-10">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Notifikasi</h1>
            <p class="text-sm text-slate-500 mt-1">Semua pemberitahuan untuk akun Anda</p>
        </div>
        @if($notifications->total() > 0)
        <button wire:click="markAllRead"
                class="px-4 py-2 text-sm font-medium text-blue-600 border border-blue-200 rounded-lg hover:bg-blue-50 transition-colors">
            Tandai semua dibaca
        </button>
        @endif
    </div>

    {{-- Notification list --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        @forelse($notifications as $notif)
        @php
            $data    = $notif->data;
            $isUnread = is_null($notif->read_at);
            $icon    = $data['icon'] ?? 'clock';
            $url     = $data['url'] ?? '#';
        @endphp
        <div class="flex items-start gap-4 px-5 py-4 {{ $isUnread ? 'bg-blue-50/50' : '' }} {{ !$loop->last ? 'border-b border-slate-100' : '' }} transition-colors hover:bg-slate-50">

            {{-- Icon --}}
            <div class="shrink-0 mt-0.5 w-9 h-9 rounded-full flex items-center justify-center
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
                   wire:click="markRead('{{ $notif->id }}')"
                   class="block group">
                    <p class="text-sm {{ $isUnread ? 'font-semibold text-slate-900' : 'font-medium text-slate-700' }} group-hover:text-blue-600 transition-colors">
                        {{ $data['submission_title'] ?? $data['message'] ?? 'Notifikasi' }}
                    </p>
                    @if(!empty($data['message']))
                    <p class="text-sm text-slate-500 mt-0.5">{{ $data['message'] }}</p>
                    @endif
                    @if(!empty($data['status']))
                    <p class="text-xs text-slate-400 mt-1">
                        Status: <span class="font-medium text-slate-600">{{ $data['status'] }}</span>
                    </p>
                    @endif
                    @if(!empty($data['deadline']))
                    <p class="text-xs text-slate-400 mt-1">
                        Tenggat: <span class="font-medium text-slate-600">{{ $data['deadline'] }}</span>
                    </p>
                    @endif
                </a>
            </div>

            {{-- Time + unread badge --}}
            <div class="shrink-0 flex flex-col items-end gap-1.5">
                <span class="text-xs text-slate-400 whitespace-nowrap">{{ $notif->created_at->diffForHumans() }}</span>
                @if($isUnread)
                <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                @endif
            </div>
        </div>
        @empty
        <div class="px-5 py-16 text-center">
            <svg class="w-12 h-12 mx-auto text-slate-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
            </svg>
            <p class="text-base font-medium text-slate-600">Belum ada notifikasi</p>
            <p class="text-sm text-slate-400 mt-1">Notifikasi akan muncul di sini saat ada aktivitas.</p>
        </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($notifications->hasPages())
    <div class="mt-6">
        {{ $notifications->links() }}
    </div>
    @endif

</div>
