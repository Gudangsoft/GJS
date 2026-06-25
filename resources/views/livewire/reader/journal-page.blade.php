<div>
@php
$navItems = [
    'about'               => ['label' => 'Tentang Jurnal',           'icon' => 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
    'editorial-team'      => ['label' => 'Tim Editorial',             'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
    'guidelines'          => ['label' => 'Panduan Penulis',           'icon' => 'M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25'],
    'reviewer-guidelines' => ['label' => 'Panduan Reviewer',          'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4'],
    'ethics'              => ['label' => 'Etika Publikasi',           'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z'],
    'submissions'         => ['label' => 'Pengiriman Naskah',         'icon' => 'M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12'],
    'privacy'             => ['label' => 'Kebijakan Privasi',         'icon' => 'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z'],
    'contact'             => ['label' => 'Kontak',                    'icon' => 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'],
];
$isCustomPage = !empty($customPageData);
@endphp

{{-- Journal header strip --}}
<div class="bg-white border-b border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5">
        <div class="flex items-center gap-4">
            @if($journal->logo)
            <img src="{{ Storage::disk('public')->url($journal->logo) }}" alt="{{ $journal->name }}" class="w-12 h-12 object-contain rounded-lg">
            @endif
            <div>
                <a href="{{ route('journals.home', $journal->slug) }}" class="text-lg font-black text-slate-900 hover:text-blue-700 transition-colors">
                    {{ $journal->name }}
                </a>
                <div class="flex gap-3 text-xs text-slate-500">
                    @if($journal->issn_print) <span>p-ISSN: {{ $journal->issn_print }}</span> @endif
                    @if($journal->issn_online) <span>e-ISSN: {{ $journal->issn_online }}</span> @endif
                </div>
            </div>
        </div>
    </div>
    {{-- Sub-nav --}}
    <div class="border-t border-slate-200" style="background:#f8fafc;">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav class="flex gap-0 overflow-x-auto text-sm">
                <a href="{{ route('journals.home', $journal->slug) }}"
                   class="shrink-0 px-4 py-3 text-slate-600 hover:text-blue-700 border-b-2 border-transparent hover:border-blue-300 transition-colors font-medium">
                    Beranda
                </a>
                <a href="{{ route('journals.issues', $journal->slug) }}"
                   class="shrink-0 px-4 py-3 text-slate-600 hover:text-blue-700 border-b-2 border-transparent hover:border-blue-300 transition-colors font-medium">
                    Arsip
                </a>
                <a href="{{ route('journals.page', [$journal->slug, 'about']) }}"
                   class="shrink-0 px-4 py-3 border-b-2 transition-colors font-medium {{ $page === 'about' ? 'border-blue-600 text-blue-700' : 'text-slate-600 hover:text-blue-700 border-transparent hover:border-blue-300' }}">
                    Tentang
                </a>
            </nav>
        </div>
    </div>
</div>

{{-- Body: sidebar + content --}}
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">

        {{-- Left sidebar nav --}}
        <div class="lg:col-span-1">
            <nav class="bg-white border border-slate-200 rounded-xl overflow-hidden sticky top-4">
                <div class="px-4 py-3 border-b border-slate-100" style="background:#eff6ff;">
                    <p class="text-xs font-bold text-blue-800 uppercase tracking-wider">Menu</p>
                </div>
                <ul class="p-2 space-y-0.5">
                    @foreach($navItems as $slug => $item)
                    @php
                        $hasContent = match($slug) {
                            'about'               => !empty($journal->about_journal) || !empty($journal->focus_scope),
                            'editorial-team'      => !empty($journal->settings['editorial_team'] ?? null),
                            'guidelines'          => !empty($journal->author_guidelines),
                            'reviewer-guidelines' => !empty($journal->reviewer_guidelines),
                            'ethics'              => !empty($journal->ethics_statement),
                            'submissions'         => !empty($journal->author_guidelines) || !empty($journal->submission_checklist),
                            'privacy'             => !empty($journal->privacy_statement),
                            'contact'             => !empty($journal->email) || !empty($journal->contact_name),
                            default               => true,
                        };
                    @endphp
                    <li>
                        <a href="{{ route('journals.page', [$journal->slug, $slug]) }}"
                           class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-sm transition-colors {{ $page === $slug ? 'bg-blue-600 text-white font-semibold' : 'text-slate-700 hover:bg-blue-50 hover:text-blue-700' }}">
                            <svg class="w-4 h-4 shrink-0 {{ $page === $slug ? 'text-blue-200' : 'text-slate-400' }}" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}"/>
                            </svg>
                            {{ $item['label'] }}
                            @if(!$hasContent)
                            <span class="ml-auto text-xs opacity-50">—</span>
                            @endif
                        </a>
                    </li>
                    @endforeach
                    {{-- Halaman kustom --}}
                    @foreach($customPages as $cp)
                    <li>
                        <a href="{{ route('journals.page', [$journal->slug, $cp['slug']]) }}"
                           class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-sm transition-colors {{ $page === $cp['slug'] ? 'bg-blue-600 text-white font-semibold' : 'text-slate-700 hover:bg-blue-50 hover:text-blue-700' }}">
                            <svg class="w-4 h-4 shrink-0 {{ $page === $cp['slug'] ? 'text-blue-200' : 'text-slate-400' }}" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            {{ $cp['title'] }}
                        </a>
                    </li>
                    @endforeach
                </ul>
            </nav>
        </div>

        {{-- Main content --}}
        <div class="lg:col-span-3">
            <div class="bg-white border border-slate-200 rounded-xl overflow-hidden">

                {{-- Page header --}}
                <div class="px-6 py-5 border-b border-slate-100" style="background:linear-gradient(135deg,#eff6ff,#f8fafc);">
                    <h1 class="text-xl font-black text-slate-900">
                        {{ $isCustomPage ? $customPageData['title'] : ($presetConfig[$page]['title'] ?? 'Halaman Jurnal') }}
                    </h1>
                    <p class="text-sm text-blue-600 mt-0.5">{{ $journal->name }}</p>
                </div>

                <div class="p-6">

                    @if($page === 'about')
                        @if($journal->about_journal)
                        <div class="prose prose-sm max-w-none text-slate-700 leading-relaxed">
                            {!! $journal->about_journal !!}
                        </div>
                        @endif
                        @if($journal->focus_scope)
                        <div class="mt-6 pt-6 {{ $journal->about_journal ? 'border-t border-slate-100' : '' }}">
                            <h2 class="text-base font-bold text-slate-800 mb-3">Fokus dan Ruang Lingkup</h2>
                            <div class="prose prose-sm max-w-none text-slate-700 leading-relaxed">
                                {!! $journal->focus_scope !!}
                            </div>
                        </div>
                        @endif
                        @if(!$journal->about_journal && !$journal->focus_scope)
                        <p class="text-slate-400 text-sm italic">Informasi tentang jurnal belum diisi.</p>
                        @endif

                    @elseif($page === 'editorial-team')
                        @php $team = $journal->settings['editorial_team'] ?? null; @endphp
                        @if($team)
                        <div class="prose prose-sm max-w-none text-slate-700">{!! $team !!}</div>
                        @else
                        <div class="text-center py-10">
                            <svg class="w-12 h-12 mx-auto mb-3 text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <p class="text-slate-400 text-sm">Data tim editorial belum diisi di pengaturan jurnal.</p>
                        </div>
                        @endif

                    @elseif($page === 'guidelines')
                        @if($journal->author_guidelines)
                        <div class="prose prose-sm max-w-none text-slate-700 leading-relaxed">
                            {!! $journal->author_guidelines !!}
                        </div>
                        @else
                        <p class="text-slate-400 text-sm italic">Panduan penulis belum diisi.</p>
                        @endif

                    @elseif($page === 'reviewer-guidelines')
                        @if($journal->reviewer_guidelines)
                        <div class="prose prose-sm max-w-none text-slate-700 leading-relaxed">
                            {!! $journal->reviewer_guidelines !!}
                        </div>
                        @else
                        <p class="text-slate-400 text-sm italic">Panduan reviewer belum diisi.</p>
                        @endif

                    @elseif($page === 'ethics')
                        @if($journal->ethics_statement)
                        <div class="prose prose-sm max-w-none text-slate-700 leading-relaxed">
                            {!! $journal->ethics_statement !!}
                        </div>
                        @else
                        <p class="text-slate-400 text-sm italic">Pernyataan etika belum diisi.</p>
                        @endif

                    @elseif($page === 'submissions')
                        @if($journal->author_guidelines)
                        <div class="prose prose-sm max-w-none text-slate-700 leading-relaxed mb-6">
                            {!! $journal->author_guidelines !!}
                        </div>
                        @endif
                        @if($journal->submission_checklist)
                        <div class="mt-4 {{ $journal->author_guidelines ? 'pt-6 border-t border-slate-100' : '' }}">
                            <h2 class="text-base font-bold text-slate-800 mb-3">Daftar Periksa Pengiriman</h2>
                            <ul class="space-y-2">
                                @foreach((array)$journal->submission_checklist as $item)
                                <li class="flex items-start gap-3 p-3 rounded-lg bg-blue-50 border border-blue-100">
                                    <svg class="w-4 h-4 text-blue-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <span class="text-sm text-slate-700">{{ $item }}</span>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                        @endif
                        @if(!$journal->author_guidelines && !$journal->submission_checklist)
                        <p class="text-slate-400 text-sm italic">Panduan pengiriman belum diisi.</p>
                        @endif
                        {{-- Submit CTA --}}
                        <div class="mt-6 p-5 rounded-xl text-center" style="background:#eff6ff;border:1px dashed #bfdbfe;">
                            <p class="font-semibold text-slate-800 mb-3">Siap mengirimkan naskah Anda?</p>
                            @auth
                            <a href="{{ route('submit') }}"
                               class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-bold text-white rounded-lg"
                               style="background:#1e40af;">
                                Kirim Naskah Sekarang →
                            </a>
                            @else
                            <a href="{{ route('login') }}"
                               class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-bold text-white rounded-lg"
                               style="background:#1e40af;">
                                Login untuk Mengirim →
                            </a>
                            @endauth
                        </div>

                    @elseif($page === 'privacy')
                        @if($journal->privacy_statement)
                        <div class="prose prose-sm max-w-none text-slate-700 leading-relaxed">
                            {!! $journal->privacy_statement !!}
                        </div>
                        @else
                        <p class="text-slate-400 text-sm italic">Kebijakan privasi belum diisi.</p>
                        @endif

                    @elseif($page === 'contact')
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        @if($journal->contact_name || $journal->email)
                        <div class="p-5 rounded-xl border border-slate-100" style="background:#f8fafc;">
                            <p class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-3">Kontak Editorial</p>
                            @if($journal->contact_name)
                            <p class="font-semibold text-slate-800">{{ $journal->contact_name }}</p>
                            @endif
                            @if($journal->email)
                            <a href="mailto:{{ $journal->email }}" class="text-sm text-blue-600 hover:underline block mt-1">{{ $journal->email }}</a>
                            @endif
                            @if($journal->contact_phone)
                            <p class="text-sm text-slate-600 mt-1">{{ $journal->contact_phone }}</p>
                            @endif
                            @if($journal->wa_contact)
                            <a href="https://wa.me/{{ preg_replace('/\D/','',$journal->wa_contact) }}" target="_blank"
                               class="inline-flex items-center gap-1.5 mt-3 text-xs font-semibold px-3 py-1.5 rounded-lg text-white" style="background:#25d366;">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M11.998 2C6.477 2 2 6.484 2 12.017c0 1.99.521 3.848 1.427 5.449L2.036 22l4.66-1.366A9.987 9.987 0 0011.998 22c5.521 0 9.998-4.484 9.998-10.017C21.996 6.484 17.519 2 11.998 2z"/></svg>
                                Chat WhatsApp
                            </a>
                            @endif
                        </div>
                        @endif
                        @if($journal->tech_support_name || $journal->tech_support_email)
                        <div class="p-5 rounded-xl border border-slate-100" style="background:#f8fafc;">
                            <p class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-3">Dukungan Teknis</p>
                            @if($journal->tech_support_name)
                            <p class="font-semibold text-slate-800">{{ $journal->tech_support_name }}</p>
                            @endif
                            @if($journal->tech_support_email)
                            <a href="mailto:{{ $journal->tech_support_email }}" class="text-sm text-blue-600 hover:underline block mt-1">{{ $journal->tech_support_email }}</a>
                            @endif
                        </div>
                        @endif
                        @if($journal->mailing_address)
                        <div class="p-5 rounded-xl border border-slate-100 sm:col-span-2" style="background:#f8fafc;">
                            <p class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-3">Alamat Korespondensi</p>
                            <p class="text-sm text-slate-700 whitespace-pre-line">{{ $journal->mailing_address }}</p>
                        </div>
                        @endif
                    </div>
                    @if(!$journal->contact_name && !$journal->email && !$journal->mailing_address)
                    <p class="text-slate-400 text-sm italic">Informasi kontak belum diisi.</p>
                    @endif

                    @elseif($isCustomPage)
                    {{-- Halaman kustom --}}
                    <div class="prose prose-sm max-w-none text-slate-700 leading-relaxed">
                        {!! $customPageData['content'] !!}
                    </div>

                    @endif

                </div>
            </div>
        </div>

    </div>
</div>
</div>
