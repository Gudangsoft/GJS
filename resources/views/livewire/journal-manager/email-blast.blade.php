<div style="background:#f6f8fb;min-height:100vh;">

<div class="px-6 py-5 border-b border-slate-200 bg-white flex items-center justify-between">
    <div>
        <h1 class="text-xl font-bold text-slate-900">Email Blast</h1>
        <p class="text-sm text-slate-500 mt-0.5">Kirim email massal ke pengguna jurnal {{ $journal?->name_abbrev }}</p>
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

{{-- COMPOSE FORM --}}
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="px-6 py-3 border-b border-slate-100 flex items-center gap-2" style="background:#f0f5ff;">
        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
        <h2 class="text-xs font-bold text-blue-800 uppercase tracking-wider">Compose Email</h2>
    </div>
    <div class="p-6 space-y-4">

        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1">Penerima</label>
            <select wire:model.live="recipients_type"
                    class="w-full px-3 py-2 text-sm rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="all_journal_users">Semua Pengguna Jurnal</option>
                <option value="authors">Penulis (Author)</option>
                <option value="reviewers">Reviewer</option>
                <option value="editors">Editor</option>
                <option value="custom">Email Kustom (manual)</option>
            </select>
        </div>

        @if($recipients_type === 'custom')
        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1">Daftar Email (satu per baris)</label>
            <textarea wire:model="custom_emails" rows="4" placeholder="user@example.com&#10;user2@example.com"
                      class="w-full px-3 py-2 text-sm rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono resize-none"></textarea>
        </div>
        @endif

        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1">Subjek Email <span class="text-red-500">*</span></label>
            <input wire:model="subject" type="text" placeholder="Judul email..."
                   class="w-full px-3 py-2 text-sm rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
            @error('subject')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1">Isi Pesan <span class="text-red-500">*</span></label>
            <textarea wire:model="message" rows="8" placeholder="Tulis isi email di sini..."
                      class="w-full px-3 py-2 text-sm rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"></textarea>
            @error('message')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="flex justify-end pt-1">
            <button wire:click="send" wire:confirm="Kirim email blast sekarang?"
                    class="inline-flex items-center gap-2 px-6 py-2.5 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                Kirim Email Blast
            </button>
        </div>

    </div>
</div>

{{-- HISTORY --}}
@if($history->isNotEmpty())
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="px-6 py-3 border-b border-slate-100" style="background:#f8fafc;">
        <h2 class="text-xs font-bold text-slate-500 uppercase tracking-wider">Riwayat Email Blast</h2>
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
                    <p class="font-semibold text-slate-800 text-sm truncate">{{ $h->subject }}</p>
                    <p class="text-xs text-slate-400 mt-0.5">
                        {{ $h->sentBy?->first_name }} &bull;
                        {{ $h->sent_at?->format('d M Y H:i') }} &bull;
                        Penerima: <strong>{{ ucfirst(str_replace('_',' ',$h->recipients_type)) }}</strong>
                    </p>
                    <p class="text-xs text-slate-500 mt-1 line-clamp-2">{{ $h->message }}</p>
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