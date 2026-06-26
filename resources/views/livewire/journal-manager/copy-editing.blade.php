<div style="background:#f6f8fb;min-height:100vh;">

{{-- Page Header --}}
<div class="px-6 py-5 border-b border-slate-200 bg-white">
    <h1 class="text-xl font-bold text-slate-900">Copy Editing</h1>
    <p class="text-sm text-slate-500 mt-0.5">Kelola proses copy editing untuk submission yang telah diterima.</p>
</div>

<div style="max-width:72rem;margin:0 auto;padding:1.5rem;">

@if(session('success'))
<div class="mb-4 px-4 py-3 rounded-xl bg-green-50 border border-green-200 text-green-800 text-sm font-medium">
    {{ session('success') }}
</div>
@endif

@if($journal)

{{-- ── Submissions Siap Copy Edit ─────────────────────────────────────────── --}}
@if($submissions_ready->isNotEmpty())
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm mb-6 overflow-hidden">
    <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
        <div>
            <h2 class="text-sm font-bold text-slate-800">Submission Siap Copy Edit</h2>
            <p class="text-xs text-slate-500 mt-0.5">Submission berstatus "Diterima" yang belum memiliki task copy editing.</p>
        </div>
        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full text-xs font-bold text-white"
              style="background:#0891b2;">{{ $submissions_ready->count() }}</span>
    </div>
    <ul class="divide-y divide-slate-50">
        @foreach($submissions_ready as $sub)
        <li class="flex items-center justify-between px-5 py-3.5 hover:bg-slate-50 transition-colors">
            <div class="min-w-0 flex-1">
                <p class="font-semibold text-slate-900 text-sm truncate max-w-lg">{{ $sub->title ?: '(Tanpa Judul)' }}</p>
                <p class="text-xs text-slate-400 mt-0.5">
                    #{{ $sub->id }}
                    @if($sub->section) · {{ $sub->section->title }} @endif
                </p>
            </div>
            <button wire:click="createTaskForSubmission({{ $sub->id }})"
                    wire:confirm="Buat task copy editing untuk submission ini?"
                    wire:loading.attr="disabled"
                    class="ml-4 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-white transition-opacity hover:opacity-90"
                    style="background:#0891b2;">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Buat Task
            </button>
        </li>
        @endforeach
    </ul>
</div>
@endif

{{-- ── Status Tabs ─────────────────────────────────────────────────────────── --}}
<div class="flex gap-2 mb-5 flex-wrap">
    @foreach([
        ['pending',         'Menunggu'],
        ['assigned',        'Ditugaskan'],
        ['in_progress',     'Dikerjakan'],
        ['awaiting_author', 'Menunggu Penulis'],
        ['completed',       'Selesai'],
    ] as [$val, $lbl])
    <button wire:click="setTab('{{ $val }}')"
            class="px-4 py-1.5 rounded-full text-sm font-semibold border transition-all
                {{ $tab === $val ? 'bg-cyan-600 text-white border-cyan-600' : 'bg-white text-slate-600 border-slate-200 hover:border-cyan-300' }}">
        {{ $lbl }}
    </button>
    @endforeach
</div>

{{-- ── Search ──────────────────────────────────────────────────────────────── --}}
<div class="mb-4">
    <input wire:model.live.debounce.350ms="search"
           type="text"
           placeholder="Cari judul artikel…"
           class="w-full max-w-sm px-3.5 py-2 text-sm border border-slate-200 rounded-xl bg-white focus:outline-none focus:ring-2 focus:ring-cyan-400 focus:border-transparent">
</div>

{{-- ── Tasks Table ─────────────────────────────────────────────────────────── --}}
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    @php
    $statusBadge = [
        'pending'         => ['Menunggu',          '#d97706', '#fffbeb'],
        'assigned'        => ['Ditugaskan',         '#2563eb', '#eff6ff'],
        'in_progress'     => ['Sedang Dikerjakan',  '#7c3aed', '#faf5ff'],
        'awaiting_author' => ['Menunggu Penulis',   '#ea580c', '#fff7ed'],
        'completed'       => ['Selesai',            '#16a34a', '#f0fdf4'],
    ];
    @endphp

    @if($tasks->isEmpty())
    <div class="text-center py-16 text-slate-400">
        <svg class="w-10 h-10 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                  d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        <p class="text-sm font-medium">Tidak ada task dengan status ini.</p>
    </div>
    @else
    <table class="w-full text-sm">
        <thead class="bg-slate-50 border-b border-slate-100">
            <tr>
                <th class="text-left px-5 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Judul Artikel</th>
                <th class="text-left px-5 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Copyeditor</th>
                <th class="text-left px-5 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Ronde</th>
                <th class="text-left px-5 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Status</th>
                <th class="text-left px-5 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Deadline</th>
                <th class="px-5 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-50">
            @foreach($tasks as $task)
            @php [$bLabel, $bColor, $bBg] = $statusBadge[$task->status] ?? [$task->status, '#64748b', '#f8fafc']; @endphp
            <tr class="hover:bg-slate-50 transition-colors" x-data="{ showAssign: false, showStatus: false }">
                <td class="px-5 py-3.5">
                    <p class="font-semibold text-slate-900 truncate max-w-xs">
                        {{ $task->submission?->title ?: '(Tanpa Judul)' }}
                    </p>
                    <p class="text-xs text-slate-400 mt-0.5">#{{ $task->submission_id }}</p>
                </td>
                <td class="px-5 py-3.5">
                    @if($task->assignee)
                        <p class="font-medium text-slate-800">
                            {{ $task->assignee->first_name }} {{ $task->assignee->last_name }}
                        </p>
                        <p class="text-xs text-slate-400">{{ $task->assignee->email }}</p>
                    @else
                        <span class="text-slate-400 text-xs italic">Belum ditugaskan</span>
                    @endif
                </td>
                <td class="px-5 py-3.5">
                    <span class="text-slate-700">Ronde {{ $task->round }}</span>
                </td>
                <td class="px-5 py-3.5">
                    <span class="text-xs font-bold px-2.5 py-1 rounded-full"
                          style="background:{{ $bBg }};color:{{ $bColor }};">{{ $bLabel }}</span>
                </td>
                <td class="px-5 py-3.5">
                    @if($task->deadline)
                        <span class="{{ $task->deadline->isPast() && $task->status !== 'completed' ? 'text-red-600 font-semibold' : 'text-slate-700' }}">
                            {{ $task->deadline->format('d M Y') }}
                        </span>
                    @else
                        <span class="text-slate-400">—</span>
                    @endif
                </td>
                <td class="px-5 py-3.5">
                    <div class="flex items-center gap-2 justify-end">

                        {{-- Assign Copyeditor --}}
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open"
                                    class="inline-flex items-center gap-1 text-xs font-semibold px-3 py-1.5 rounded-lg border border-slate-200 bg-white text-slate-600 hover:border-cyan-400 hover:text-cyan-700 transition-colors">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                Assign
                            </button>
                            <div x-show="open" @click.outside="open = false" x-cloak
                                 class="absolute right-0 mt-1 z-20 bg-white border border-slate-200 rounded-xl shadow-lg min-w-48 py-1">
                                @forelse($editors as $editor)
                                <button wire:click="assignTask({{ $task->id }}, {{ $editor->id }})"
                                        @click="open = false"
                                        class="w-full text-left px-4 py-2 text-xs text-slate-700 hover:bg-slate-50 transition-colors">
                                    {{ $editor->first_name }} {{ $editor->last_name }}
                                    <span class="text-slate-400 block">{{ $editor->email }}</span>
                                </button>
                                @empty
                                <p class="px-4 py-2 text-xs text-slate-400">Tidak ada editor tersedia.</p>
                                @endforelse
                            </div>
                        </div>

                        {{-- Update Status --}}
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open"
                                    class="inline-flex items-center gap-1 text-xs font-semibold px-3 py-1.5 rounded-lg border border-slate-200 bg-white text-slate-600 hover:border-violet-400 hover:text-violet-700 transition-colors">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                                Status
                            </button>
                            <div x-show="open" @click.outside="open = false" x-cloak
                                 class="absolute right-0 mt-1 z-20 bg-white border border-slate-200 rounded-xl shadow-lg min-w-44 py-1">
                                @foreach([
                                    ['pending',         'Menunggu'],
                                    ['assigned',        'Ditugaskan'],
                                    ['in_progress',     'Sedang Dikerjakan'],
                                    ['awaiting_author', 'Menunggu Penulis'],
                                    ['completed',       'Selesai'],
                                ] as [$sVal, $sLbl])
                                <button wire:click="updateStatus({{ $task->id }}, '{{ $sVal }}')"
                                        @click="open = false"
                                        class="w-full text-left px-4 py-2 text-xs transition-colors
                                            {{ $task->status === $sVal ? 'font-bold text-cyan-700 bg-cyan-50' : 'text-slate-700 hover:bg-slate-50' }}">
                                    {{ $sLbl }}
                                </button>
                                @endforeach
                            </div>
                        </div>

                    </div>
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
