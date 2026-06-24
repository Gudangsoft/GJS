<div style="background:#f6f8fb;min-height:100vh;">

<div class="px-6 py-5 border-b border-slate-200 bg-white flex items-center justify-between">
    <div>
        <h1 class="text-xl font-bold text-slate-900">WA Blast</h1>
        <p class="text-sm text-slate-500 mt-0.5">Kirim pesan WhatsApp massal ke pengguna jurnal {{ $journal?->name_abbrev }}</p>
    </div>
</div>

<div class="max-w-4xl mx-auto px-6 py-6 space-y-6">

@if(session('success'))
<div class="px-4 py-3 bg-green-50 border border-green-200 text-green-800 rounded-xl text-sm flex items-center gap-2">
    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
    {{ session('success') }}
</div>
@endif
@if(session('error'))
<div class="px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm flex items-center gap-2">
    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    {{ session('error') }}
</div>
@endif

@if(!$journal?->wa_api_token)
<div class="px-4 py-3 bg-amber-50 border border-amber-200 rounded-xl text-sm text-amber-800 flex items-start gap-2">
    <svg class="w-4 h-4 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    <span>Token API WhatsApp (Fonnte) belum diatur. Konfigurasikan di
        <a href="{{ route('manager.settings') }}" class="font-semibold underline">Pengaturan Jurnal</a>.</span>
</div>
@endif

{{-- COMPOSE FORM --}}
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="px-6 py-3 border-b border-slate-100 flex items-center gap-2" style="background:#f0fff4;">
        <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M11.998 2C6.477 2 2 6.484 2 12.017c0 1.99.521 3.848 1.427 5.449L2.036 22l4.66-1.366A9.987 9.987 0 0011.998 22c5.521 0 9.998-4.484 9.998-10.017C21.996 6.484 17.519 2 11.998 2z"/></svg>
        <h2 class="text-xs font-bold text-green-800 uppercase tracking-wider">Compose Pesan WhatsApp</h2>
    </div>
    <div class="p-6 space-y-4">

        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1">Penerima</label>
            <select wire:model.live="recipients_type"
                    class="w-full px-3 py-2 text-sm rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-green-500">
                <option value="all_journal_users">Semua Pengguna Jurnal (ada no. HP)</option>
                <option value="authors">Penulis (Author)</option>
                <option value="reviewers">Reviewer</option>
                <option value="editors">Editor</option>
                <option value="custom">Nomor Kustom (manual)</option>
            </select>
            <p class="text-xs text-slate-400 mt-1">Hanya pengguna yang memiliki nomor telepon yang akan dikirimi pesan.</p>
        </div>

        @if($recipients_type === 'custom')
        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1">Daftar Nomor WA (satu per baris)</label>
            <textarea wire:model="custom_numbers" rows="4" placeholder="628123456789&#10;628987654321"
                      class="w-full px-3 py-2 text-sm rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-green-500 font-mono resize-none"></textarea>
            <p class="text-xs text-slate-400 mt-1">Format internasional tanpa + (misal: 628123456789)</p>
        </div>
        @endif

        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1">Pesan <span class="text-red-500">*</span></label>
            <textarea wire:model="message" rows="8" placeholder="Tulis pesan WhatsApp di sini...&#10;&#10;Anda bisa menggunakan format WhatsApp:&#10;*teks tebal*&#10;_teks miring_&#10;~teks coret~"
                      class="w-full px-3 py-2 text-sm rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-green-500 resize-none"></textarea>
            @error('message')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            <p class="text-xs text-slate-400 mt-1">{{ strlen($message) }} karakter</p>
        </div>

        @if($message)
        <div class="bg-slate-800 rounded-2xl p-4 max-w-xs">
            <p class="text-xs text-slate-400 mb-2">Pratinjau WA:</p>
            <div class="bg-[#dcf8c6] rounded-xl px-3 py-2 text-sm text-slate-900 whitespace-pre-line">{{ $message }}</div>
        </div>
        @endif

        <div class="flex justify-end pt-1">
            <button wire:click="send" wire:confirm="Kirim WA blast sekarang?"
                    class="inline-flex items-center gap-2 px-6 py-2.5 text-white text-sm font-semibold rounded-lg transition-colors"
                    style="background:#25d366;" onmouseover="this.style.background='#1ebe5d'" onmouseout="this.style.background='#25d366'">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M11.998 2C6.477 2 2 6.484 2 12.017c0 1.99.521 3.848 1.427 5.449L2.036 22l4.66-1.366A9.987 9.987 0 0011.998 22c5.521 0 9.998-4.484 9.998-10.017C21.996 6.484 17.519 2 11.998 2z"/></svg>
                Kirim WA Blast
            </button>
        </div>

    </div>
</div>

{{-- HISTORY --}}
@if($history->isNotEmpty())
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="px-6 py-3 border-b border-slate-100" style="background:#f8fafc;">
        <h2 class="text-xs font-bold text-slate-500 uppercase tracking-wider">Riwayat WA Blast</h2>
    </div>
    <div class="divide-y divide-slate-50">
        @foreach($history as $h)
        @php
        $stColor = match($h->status) {
            'sent'   => 'bg-green-100 text-green-700',
            'failed' => 'bg-red-100 text-red-700',
            default  => 'bg-amber-100 text-amber-700',
        };
        @endphp
        <div class="px-6 py-4">
            <div class="flex items-start justify-between gap-4">
                <div class="flex-1 min-w-0">
                    <p class="text-xs text-slate-400">
                        {{ $h->sentBy?->first_name }} &bull;
                        {{ $h->sent_at?->format('d M Y H:i') }} &bull;
                        Penerima: <strong>{{ ucfirst(str_replace('_',' ',$h->recipients_type)) }}</strong>
                    </p>
                    <p class="text-sm text-slate-700 mt-1 line-clamp-3 whitespace-pre-line">{{ $h->message }}</p>
                </div>
                <div class="shrink-0 text-right">
                    <span class="inline-block text-xs font-bold px-2 py-0.5 rounded-full {{ $stColor }}">
                        {{ ucfirst($h->status) }}
                    </span>
                    <p class="text-xs text-slate-400 mt-1">{{ $h->sent_count }} terkirim</p>
                    @if($h->failed_count > 0)
                    <p class="text-xs text-red-400">{{ $h->failed_count }} gagal</p>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif

</div>
</div>