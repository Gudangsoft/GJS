@php
$statusMap = [
    'draft'             => ['label' => 'Draft',            'class' => 'bg-slate-100 text-slate-600'],
    'submitted'         => ['label' => 'Dikirim',          'class' => 'bg-blue-50 text-blue-700'],
    'queued'            => ['label' => 'Antrian',          'class' => 'bg-yellow-50 text-yellow-700'],
    'accepted_for_review' => ['label' => 'Diterima',       'class' => 'bg-green-50 text-green-700'],
    'assigned'          => ['label' => 'Ditugaskan',       'class' => 'bg-yellow-50 text-yellow-700'],
    'review'            => ['label' => 'Dalam Review',     'class' => 'bg-purple-50 text-purple-700'],
    'revision_required' => ['label' => 'Revisi',           'class' => 'bg-orange-50 text-orange-700'],
    'resubmit'          => ['label' => 'Resubmit',         'class' => 'bg-orange-50 text-orange-700'],
    'accepted'          => ['label' => 'Disetujui',        'class' => 'bg-green-50 text-green-700'],
    'copyediting'       => ['label' => 'Copy Editing',     'class' => 'bg-teal-50 text-teal-700'],
    'production'        => ['label' => 'Produksi',         'class' => 'bg-teal-50 text-teal-700'],
    'scheduled'         => ['label' => 'Terjadwal',        'class' => 'bg-green-50 text-green-700'],
    'published'         => ['label' => 'Diterbitkan',      'class' => 'bg-green-100 text-green-800'],
    'declined'          => ['label' => 'Ditolak',          'class' => 'bg-red-50 text-red-700'],
    'archived'          => ['label' => 'Diarsipkan',       'class' => 'bg-slate-100 text-slate-500'],
];
$s = $statusMap[$submission->status] ?? ['label' => $submission->status, 'class' => 'bg-slate-100 text-slate-600'];
@endphp
<a href="{{ route('submissions.show', $submission->id) }}"
   class="flex items-center gap-4 bg-white rounded-xl border border-slate-200 hover:border-blue-300 hover:shadow-sm transition-all p-5">
    <div class="flex-1 min-w-0">
        <div class="flex items-center gap-2 mb-1">
            <span class="text-xs font-semibold px-2 py-0.5 rounded-full {{ $s['class'] }}">{{ $s['label'] }}</span>
            @if($submission->journal)
            <span class="text-xs text-slate-400">{{ $submission->journal->name_abbrev }}</span>
            @endif
        </div>
        <p class="font-semibold text-slate-900 truncate">{{ $submission->title }}</p>
        <p class="text-xs text-slate-400 mt-1">
            ID #{{ $submission->id }}
            @if($submission->submitted_at)
            · Dikirim {{ $submission->submitted_at->format('d M Y') }}
            @endif
        </p>
    </div>
    <svg class="w-5 h-5 text-slate-300 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
    </svg>
</a>
