<div style="background:#f6f8fb;min-height:100vh;">

{{-- Page Header --}}
<div class="px-6 py-5 border-b border-slate-200 bg-white">
    <h1 class="text-xl font-bold text-slate-900">Copy Editing</h1>
    <p class="text-sm text-slate-500 mt-0.5">Pantau proses copy editing artikel Anda dan berikan catatan kepada copyeditor.</p>
</div>

<div style="max-width:56rem;margin:0 auto;padding:1.5rem;">

@if(session('success'))
<div class="mb-4 px-4 py-3 rounded-xl bg-green-50 border border-green-200 text-green-800 text-sm font-medium">
    {{ session('success') }}
</div>
@endif

@if($tasks->isEmpty())
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm text-center py-16 text-slate-400">
    <svg class="w-10 h-10 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
              d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
    </svg>
    <p class="text-sm font-medium">Belum ada proses copy editing untuk artikel Anda.</p>
</div>
@else

@php
$statusBadge = [
    'pending'         => ['Menunggu',          '#d97706', '#fffbeb'],
    'assigned'        => ['Ditugaskan',         '#2563eb', '#eff6ff'],
    'in_progress'     => ['Sedang Dikerjakan',  '#7c3aed', '#faf5ff'],
    'awaiting_author' => ['Menunggu Tanggapan', '#ea580c', '#fff7ed'],
    'completed'       => ['Selesai',            '#16a34a', '#f0fdf4'],
];
@endphp

<div class="space-y-5">
    @foreach($tasks as $task)
    @php [$bLabel, $bColor, $bBg] = $statusBadge[$task->status] ?? [$task->status, '#64748b', '#f8fafc']; @endphp

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden"
         x-data="{ authorNotes: @js($task->author_notes ?? ''), sent: false }">

        {{-- Card Header --}}
        <div class="px-5 py-4 border-b border-slate-100 flex items-start justify-between gap-3">
            <div class="min-w-0 flex-1">
                <h2 class="font-bold text-slate-900 text-sm leading-snug truncate max-w-lg">
                    {{ $task->submission?->title ?: '(Tanpa Judul)' }}
                </h2>
                <p class="text-xs text-slate-400 mt-0.5">
                    {{ $task->submission?->journal?->name }}
                    · Ronde {{ $task->round }}
                    @if($task->deadline)
                        · Deadline {{ $task->deadline->format('d M Y') }}
                    @endif
                </p>
            </div>
            <span class="shrink-0 text-xs font-bold px-2.5 py-1 rounded-full"
                  style="background:{{ $bBg }};color:{{ $bColor }};">{{ $bLabel }}</span>
        </div>

        <div class="px-5 py-4 space-y-4">

            {{-- Copyeditor Info --}}
            @if($task->assignee)
            <div class="flex items-center gap-2 text-xs text-slate-500">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <span>Copyeditor:
                    <span class="font-semibold text-slate-700">
                        {{ $task->assignee->first_name }} {{ $task->assignee->last_name }}
                    </span>
                </span>
            </div>
            @endif

            {{-- Catatan Copyeditor --}}
            @if($task->copyeditor_notes)
            <div class="rounded-xl border border-cyan-100 bg-cyan-50 px-4 py-3">
                <p class="text-xs font-bold text-cyan-700 mb-1.5">Catatan dari Copyeditor</p>
                <p class="text-sm text-slate-700 whitespace-pre-line">{{ $task->copyeditor_notes }}</p>
            </div>
            @endif

            {{-- Catatan Editor --}}
            @if($task->editor_notes)
            <div class="rounded-xl border border-slate-100 bg-slate-50 px-4 py-3">
                <p class="text-xs font-bold text-slate-600 mb-1.5">Catatan dari Editor</p>
                <p class="text-sm text-slate-700 whitespace-pre-line">{{ $task->editor_notes }}</p>
            </div>
            @endif

            {{-- Tanggapan Penulis --}}
            @if($task->status === 'awaiting_author' || $task->status === 'in_progress')
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">
                    Catatan / Tanggapan Anda
                </label>
                <textarea x-model="authorNotes"
                          rows="4"
                          placeholder="Tulis tanggapan atau catatan Anda untuk copyeditor di sini…"
                          class="w-full px-3.5 py-2.5 text-sm border border-slate-200 rounded-xl bg-white focus:outline-none focus:ring-2 focus:ring-cyan-400 focus:border-transparent resize-none"></textarea>
                <div class="mt-2 flex items-center gap-3">
                    <button
                        x-on:click="
                            $wire.submitAuthorNotes({{ $task->id }}, authorNotes);
                            sent = true;
                        "
                        :disabled="authorNotes.trim() === '' || sent"
                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-xs font-semibold text-white transition-opacity disabled:opacity-50"
                        style="background:#0891b2;">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                        Kirim Catatan
                    </button>
                    <span x-show="sent" class="text-xs text-green-600 font-medium">Catatan dikirim!</span>
                </div>
            </div>
            @elseif($task->author_notes)
            <div class="rounded-xl border border-green-100 bg-green-50 px-4 py-3">
                <p class="text-xs font-bold text-green-700 mb-1.5">Catatan Anda (Terkirim)</p>
                <p class="text-sm text-slate-700 whitespace-pre-line">{{ $task->author_notes }}</p>
            </div>
            @endif

        </div>
    </div>
    @endforeach
</div>

@endif
</div>
</div>
