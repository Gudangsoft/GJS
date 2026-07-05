<div style="background:#f6f8fb;min-height:100vh;">

<div class="px-6 py-5 border-b border-slate-200 bg-white">
    <div>
        <h1 class="text-xl font-bold text-slate-900">Penugasan Review</h1>
        <p class="text-sm text-slate-500 mt-0.5">Pantau seluruh penugasan reviewer untuk jurnal Anda.</p>
    </div>
</div>

<div style="max-width:64rem;margin:0 auto;padding:1.5rem;width:100%;box-sizing:border-box;">

@if($journal)

{{-- Filter tabs --}}
<div class="flex gap-2 mb-5 flex-wrap">
    @foreach([
        ['all',        'Semua'],
        ['pending',    'Menunggu'],
        ['accepted',   'Diterima'],
        ['completed',  'Selesai'],
        ['declined',   'Ditolak'],
        ['cancelled',  'Dibatalkan'],
    ] as [$val, $lbl])
    <button wire:click="setFilter('{{ $val }}')"
            class="px-4 py-1.5 rounded-full text-sm font-semibold border transition-all
                {{ $statusFilter === $val ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-slate-600 border-slate-200 hover:border-blue-300' }}">
        {{ $lbl }}
    </button>
    @endforeach
</div>

<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    @if($assignments->isEmpty())
    <div class="text-center py-16 text-slate-400">
        <svg class="w-10 h-10 mx-auto mb-2 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
        <p class="text-sm font-medium">Belum ada penugasan review.</p>
    </div>
    @else
    @php
    $statusBadge = [
        'pending'   => ['Menunggu',   '#d97706','#fffbeb'],
        'accepted'  => ['Diterima',   '#16a34a','#f0fdf4'],
        'declined'  => ['Ditolak',    '#dc2626','#fef2f2'],
        'completed' => ['Selesai',    '#1d4ed8','#eff6ff'],
        'cancelled' => ['Dibatalkan', '#64748b','#f8fafc'],
    ];
    @endphp
    <table class="w-full text-sm">
        <thead class="bg-slate-50 border-b border-slate-100">
            <tr>
                <th class="text-left px-5 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Submission</th>
                <th class="text-left px-5 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Reviewer</th>
                <th class="text-left px-5 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Batas Waktu</th>
                <th class="text-left px-5 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Status</th>
                <th class="px-5 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-50">
            @foreach($assignments as $a)
            @php [$bLabel,$bColor,$bBg] = $statusBadge[$a->status] ?? [$a->status,'#64748b','#f8fafc']; @endphp
            <tr class="hover:bg-slate-50 transition-colors">
                <td class="px-5 py-3.5">
                    <p class="font-semibold text-slate-900 text-sm truncate max-w-xs">{{ $a->submission?->title ?: '(Tanpa Judul)' }}</p>
                    <p class="text-xs text-slate-400">#{{ $a->submission_id }}
                        @if($a->submission?->section) · {{ $a->submission->section->title }}@endif
                    </p>
                </td>
                <td class="px-5 py-3.5">
                    @if($a->reviewer)
                    <p class="font-medium text-slate-800">{{ $a->reviewer->first_name }} {{ $a->reviewer->last_name }}</p>
                    <p class="text-xs text-slate-400">{{ $a->reviewer->email }}</p>
                    @else
                    <span class="text-slate-400 text-xs">—</span>
                    @endif
                </td>
                <td class="px-5 py-3.5">
                    @if($a->date_due)
                    <span class="{{ $a->date_due->isPast() && $a->status !== 'completed' ? 'text-red-600 font-semibold' : 'text-slate-700' }}">
                        {{ $a->date_due->format('d M Y') }}
                    </span>
                    @else
                    <span class="text-slate-400">—</span>
                    @endif
                </td>
                <td class="px-5 py-3.5">
                    <span class="text-xs font-bold px-2.5 py-1 rounded-full"
                          style="background:{{ $bBg }};color:{{ $bColor }};">{{ $bLabel }}</span>
                </td>
                <td class="px-5 py-3.5 text-right">
                    <a href="{{ route('editor.submissions.review', $a->submission_id) }}"
                       class="inline-flex items-center gap-1 text-xs font-semibold px-3 py-1.5 rounded-lg text-white"
                       style="background:#1d4ed8;">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        Detail
                    </a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
</div>

@else
<div class="text-center py-20 text-slate-400">
    <p class="font-semibold">Anda belum ditugaskan ke jurnal manapun.</p>
</div>
@endif

</div>
</div>
