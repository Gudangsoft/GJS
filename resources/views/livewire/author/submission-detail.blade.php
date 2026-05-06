<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 text-sm text-slate-500 mb-6">
        <a href="{{ route('dashboard') }}" class="hover:text-blue-600">Dashboard</a>
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <span class="text-slate-700 font-medium">Submission #{{ $submission->id }}</span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Main --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- Title card --}}
            <div class="bg-white rounded-2xl border border-slate-200 p-6">
                @php
                $statusMap = [
                    'draft'             => ['label'=>'Draft','class'=>'bg-slate-100 text-slate-600'],
                    'submitted'         => ['label'=>'Dikirim','class'=>'bg-blue-50 text-blue-700'],
                    'queued'            => ['label'=>'Antrian','class'=>'bg-yellow-50 text-yellow-700'],
                    'assigned'          => ['label'=>'Ditugaskan','class'=>'bg-yellow-50 text-yellow-700'],
                    'review'            => ['label'=>'Dalam Review','class'=>'bg-purple-50 text-purple-700'],
                    'revision_required' => ['label'=>'Perlu Revisi','class'=>'bg-orange-50 text-orange-700'],
                    'accepted'          => ['label'=>'Diterima','class'=>'bg-green-50 text-green-700'],
                    'copyediting'       => ['label'=>'Copy Editing','class'=>'bg-teal-50 text-teal-700'],
                    'production'        => ['label'=>'Produksi','class'=>'bg-teal-50 text-teal-700'],
                    'scheduled'         => ['label'=>'Terjadwal','class'=>'bg-green-50 text-green-700'],
                    'published'         => ['label'=>'Diterbitkan','class'=>'bg-green-100 text-green-800'],
                    'declined'          => ['label'=>'Ditolak','class'=>'bg-red-50 text-red-700'],
                ];
                $s = $statusMap[$submission->status] ?? ['label'=>$submission->status,'class'=>'bg-slate-100 text-slate-600'];
                @endphp
                <div class="flex items-start justify-between gap-3 mb-3">
                    <span class="inline-block text-sm font-semibold px-3 py-1 rounded-full {{ $s['class'] }}">
                        {{ $s['label'] }}
                    </span>
                    @if($submission->journal)
                    <span class="text-xs text-slate-400 bg-slate-50 border border-slate-200 px-2 py-1 rounded">
                        {{ $submission->journal->name_abbrev }}
                    </span>
                    @endif
                </div>
                <h1 class="text-xl font-bold text-slate-900 mb-1">{{ $submission->title }}</h1>
                @if($submission->subtitle)
                <p class="text-base text-slate-600 mb-3">{{ $submission->subtitle }}</p>
                @endif
                @if($submission->abstract)
                <div class="mt-4 pt-4 border-t border-slate-100">
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-2">Abstrak</p>
                    <p class="text-sm text-slate-700 leading-relaxed">{{ $submission->abstract }}</p>
                </div>
                @endif
                @if($submission->keywords)
                <div class="mt-3 flex flex-wrap gap-2">
                    @foreach($submission->keywords as $kw)
                    <span class="text-xs bg-blue-50 text-blue-700 border border-blue-200 px-2.5 py-1 rounded-full">{{ $kw }}</span>
                    @endforeach
                </div>
                @endif
            </div>

            {{-- Review Rounds --}}
            @if($submission->reviewRounds->isNotEmpty())
            <div class="bg-white rounded-2xl border border-slate-200 p-6">
                <h2 class="text-base font-bold text-slate-900 mb-4">Riwayat Review</h2>
                @foreach($submission->reviewRounds as $round)
                <div class="mb-6 last:mb-0">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="text-sm font-semibold text-slate-700">Putaran {{ $round->round }}</span>
                        <span class="text-xs px-2 py-0.5 rounded-full bg-slate-100 text-slate-500">{{ $round->status }}</span>
                    </div>
                    @foreach($round->assignments as $assignment)
                    <div class="ml-4 border-l-2 border-slate-200 pl-4 mb-3 last:mb-0">
                        <div class="flex items-center justify-between mb-1">
                            <p class="text-sm font-medium text-slate-700">
                                Reviewer #{{ $assignment->reviewer_id }}
                            </p>
                            <span class="text-xs {{ match($assignment->status){
                                'completed' => 'text-green-600',
                                'accepted'  => 'text-blue-600',
                                'declined'  => 'text-red-500',
                                default     => 'text-slate-400'
                            } }}">{{ $assignment->status }}</span>
                        </div>
                        @if($assignment->review)
                        <div class="mt-2 p-3 bg-slate-50 rounded-lg">
                            <p class="text-xs font-semibold text-slate-500 mb-1">
                                Rekomendasi:
                                <span class="text-slate-800">{{ str_replace('_', ' ', $assignment->review->recommendation) }}</span>
                            </p>
                            @if($assignment->review->comments_for_author)
                            <p class="text-sm text-slate-700 mt-1">{{ $assignment->review->comments_for_author }}</p>
                            @endif
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
                @endforeach
            </div>
            @endif

            {{-- Published article --}}
            @if($submission->article && $submission->status === 'published')
            <div class="bg-green-50 rounded-2xl border border-green-200 p-5">
                <h2 class="text-sm font-bold text-green-900 mb-2">Artikel Diterbitkan</h2>
                @if($submission->article->issue)
                <p class="text-sm text-green-800">
                    Diterbitkan di: <strong>{{ $submission->article->issue->getLabel() }}</strong>
                </p>
                @endif
                @if($submission->article->doi)
                <a href="https://doi.org/{{ $submission->article->doi }}" target="_blank" rel="noopener"
                   class="text-sm text-green-700 hover:underline mt-1 block">
                    DOI: {{ $submission->article->doi }}
                </a>
                @endif
                @if($submission->article->galleys->isNotEmpty())
                <div class="flex gap-2 mt-3">
                    @foreach($submission->article->galleys as $galley)
                    <span class="text-xs bg-green-100 text-green-800 border border-green-300 px-2.5 py-1 rounded font-medium">
                        {{ $galley->label }}
                    </span>
                    @endforeach
                </div>
                @endif
            </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="space-y-5">

            {{-- Contributors --}}
            @if($submission->contributors->isNotEmpty())
            <div class="bg-white rounded-xl border border-slate-200 p-5">
                <h3 class="text-sm font-semibold text-slate-700 mb-3">Penulis</h3>
                <div class="space-y-2">
                    @foreach($submission->contributors as $c)
                    <div class="text-sm">
                        <p class="font-medium text-slate-800">{{ $c->full_name }}</p>
                        @if($c->affiliation)
                        <p class="text-xs text-slate-500">{{ $c->affiliation }}</p>
                        @endif
                        @if($c->primary_contact)
                        <span class="text-xs text-blue-600">Korespondensi</span>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Details --}}
            <div class="bg-white rounded-xl border border-slate-200 p-5">
                <h3 class="text-sm font-semibold text-slate-700 mb-3">Detail Submission</h3>
                <dl class="space-y-2 text-sm">
                    <div>
                        <dt class="text-xs text-slate-400">ID</dt>
                        <dd class="font-medium text-slate-700">#{{ $submission->id }}</dd>
                    </div>
                    @if($submission->submitted_at)
                    <div>
                        <dt class="text-xs text-slate-400">Tanggal Kirim</dt>
                        <dd class="font-medium text-slate-700">{{ $submission->submitted_at->format('d M Y') }}</dd>
                    </div>
                    @endif
                    @if($submission->section)
                    <div>
                        <dt class="text-xs text-slate-400">Seksi</dt>
                        <dd class="font-medium text-slate-700">{{ $submission->section->title }}</dd>
                    </div>
                    @endif
                    <div>
                        <dt class="text-xs text-slate-400">Bahasa</dt>
                        <dd class="font-medium text-slate-700">{{ strtoupper($submission->locale) }}</dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
</div>
