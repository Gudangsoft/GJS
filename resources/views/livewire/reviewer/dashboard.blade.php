<div>
{{-- Header --}}
<div style="background:linear-gradient(135deg,#065f46 0%,#059669 100%);padding:2rem 1.5rem;">
    <div class="max-w-5xl mx-auto">
        <p class="text-sm font-semibold mb-1" style="color:#6ee7b7;">Panel Reviewer</p>
        <h1 class="text-2xl font-black text-white">Dashboard Reviewer</h1>
    </div>
</div>

<div class="max-w-5xl mx-auto px-6 py-8">

    {{-- Flash --}}
    @if(session('success'))
    <div class="mb-6 flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium" style="background:#f0fdf4;border:1px solid #bbf7d0;color:#166534;">
        <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
        {{ session('success') }}
    </div>
    @endif

    {{-- Stats --}}
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:1rem;margin-bottom:2rem;">
        @foreach([
            ['tab'=>'pending',  'label'=>'Undangan Baru',   'color'=>'#d97706','bg'=>'#fffbeb','n'=>$counts['pending']],
            ['tab'=>'active',   'label'=>'Sedang Review',   'color'=>'#2563eb','bg'=>'#eff6ff','n'=>$counts['active']],
            ['tab'=>'completed','label'=>'Review Selesai',  'color'=>'#059669','bg'=>'#f0fdf4','n'=>$counts['completed']],
        ] as $s)
        <button wire:click="setTab('{{ $s['tab'] }}')"
                class="text-left rounded-2xl p-4 transition-all"
                style="background:{{ $tab === $s['tab'] ? $s['bg'] : '#fff' }};border:2px solid {{ $tab === $s['tab'] ? $s['color'] : '#e2e8f0' }};">
            <div class="font-black text-2xl leading-none mb-1" style="color:{{ $s['color'] }};">{{ $s['n'] }}</div>
            <div class="text-xs font-semibold" style="color:#64748b;">{{ $s['label'] }}</div>
        </button>
        @endforeach
    </div>

    {{-- Tab nav --}}
    <div class="flex gap-1 mb-6 p-1 rounded-xl" style="background:#f1f5f9;">
        @foreach([
            ['tab'=>'pending',  'label'=>'Undangan Baru'],
            ['tab'=>'active',   'label'=>'Sedang Review'],
            ['tab'=>'completed','label'=>'Selesai'],
            ['tab'=>'declined', 'label'=>'Ditolak'],
        ] as $t)
        <button wire:click="setTab('{{ $t['tab'] }}')"
                class="flex-1 py-2 px-3 rounded-lg text-sm font-semibold transition-all"
                style="{{ $tab === $t['tab'] ? 'background:#fff;color:#059669;box-shadow:0 1px 3px rgba(0,0,0,.1)' : 'color:#64748b;background:transparent' }}">
            {{ $t['label'] }}
        </button>
        @endforeach
    </div>

    {{-- Assignment list --}}
    @if($assignments->isEmpty())
    <div class="text-center py-16 rounded-2xl" style="border:2px dashed #e2e8f0;">
        <div style="font-size:2.5rem;margin-bottom:.75rem;">📭</div>
        <p class="font-semibold" style="color:#94a3b8;">Tidak ada assignment di kategori ini</p>
    </div>
    @else
    <div class="space-y-3">
        @foreach($assignments as $a)
        <div class="rounded-2xl p-5" style="background:#fff;border:1px solid #e2e8f0;box-shadow:0 1px 3px rgba(0,0,0,.05);">
            <div class="flex items-start justify-between gap-4 flex-wrap">
                <div class="flex-1 min-w-0">
                    <div class="flex flex-wrap items-center gap-2 mb-1.5">
                        @if($a->submission->journal)
                        <span class="text-xs font-bold" style="color:#059669;">{{ $a->submission->journal->name_abbrev ?? Str::limit($a->submission->journal->name,20) }}</span>
                        @endif
                        @if($a->submission->section)
                        <span class="text-xs px-2 py-0.5 rounded" style="background:#f1f5f9;color:#475569;">{{ $a->submission->section->title }}</span>
                        @endif
                        @if($a->date_due)
                        <span class="text-xs font-semibold {{ $a->date_due->isPast() ? 'text-red-600' : '' }}" style="{{ $a->date_due->isPast() ? 'color:#dc2626' : 'color:#94a3b8' }}">
                            Deadline: {{ $a->date_due->format('d M Y') }}
                            @if($a->date_due->isPast()) ⚠️ @endif
                        </span>
                        @endif
                    </div>
                    <h3 class="font-bold leading-snug mb-1" style="color:#0f172a;font-size:.9375rem;">
                        {{ $a->submission->title }}
                    </h3>
                    <p class="text-sm" style="color:#64748b;">
                        Ditugaskan: {{ $a->date_assigned?->format('d M Y') ?? '—' }}
                        @if($a->review_method === 'double_blind')
                        · Double Blind Review
                        @elseif($a->review_method === 'single_blind')
                        · Single Blind Review
                        @else
                        · Open Review
                        @endif
                    </p>
                </div>

                <div class="flex items-center gap-2 shrink-0 flex-wrap">
                    @if($a->status === 'awaiting_response')
                    <button wire:click="acceptInvitation({{ $a->id }})"
                            wire:confirm="Terima undangan review ini?"
                            class="text-sm font-bold px-4 py-2 rounded-xl text-white"
                            style="background:#059669;">
                        Terima
                    </button>
                    <button wire:click="declineInvitation({{ $a->id }})"
                            wire:confirm="Tolak undangan review ini?"
                            class="text-sm font-semibold px-4 py-2 rounded-xl"
                            style="background:#fff1f2;color:#dc2626;border:1px solid #fecaca;">
                        Tolak
                    </button>
                    @elseif($a->status === 'accepted')
                    <a href="{{ route('reviewer.review', $a) }}"
                       class="text-sm font-bold px-4 py-2 rounded-xl text-white"
                       style="background:#2563eb;text-decoration:none;">
                        {{ $a->review ? 'Edit Review' : 'Mulai Review' }}
                    </a>
                    @elseif($a->status === 'completed')
                    <span class="text-sm font-semibold px-3 py-1.5 rounded-xl" style="background:#f0fdf4;color:#059669;">
                        ✓ Review Terkirim
                    </span>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif

</div>
</div>
