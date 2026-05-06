<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Dashboard Penulis</h1>
            <p class="text-sm text-slate-500 mt-0.5">Selamat datang, {{ auth()->user()->first_name }}</p>
        </div>
        <a href="{{ route('submit') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Kirim Naskah
        </a>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        @php
            $statCards = [
                ['label' => 'Dalam Proses', 'count' => $active->count(), 'color' => 'blue'],
                ['label' => 'Diterbitkan',  'count' => $published->count(), 'color' => 'green'],
                ['label' => 'Draft',         'count' => $drafts->count(), 'color' => 'slate'],
                ['label' => 'Ditolak',       'count' => $declined->count(), 'color' => 'red'],
            ];
        @endphp
        @foreach($statCards as $card)
        <div class="bg-white rounded-xl border border-slate-200 p-4 text-center">
            <p class="text-2xl font-bold text-{{ $card['color'] }}-600">{{ $card['count'] }}</p>
            <p class="text-xs text-slate-500 mt-0.5">{{ $card['label'] }}</p>
        </div>
        @endforeach
    </div>

    {{-- Tabs --}}
    <div x-data="{ tab: 'active' }" class="space-y-4">
        <div class="flex gap-1 bg-slate-100 p-1 rounded-xl w-fit">
            @foreach([['active','Dalam Proses'],['published','Diterbitkan'],['drafts','Draft'],['declined','Ditolak']] as [$key, $label])
            <button @click="tab = '{{ $key }}'"
                    :class="tab === '{{ $key }}' ? 'bg-white shadow text-slate-900' : 'text-slate-500 hover:text-slate-700'"
                    class="px-4 py-2 rounded-lg text-sm font-medium transition-all">
                {{ $label }}
            </button>
            @endforeach
        </div>

        {{-- Active Submissions --}}
        <div x-show="tab === 'active'" x-cloak>
            @forelse($active as $sub)
            @include('livewire.author._submission-row', ['submission' => $sub])
            @empty
            <div class="text-center py-12 text-slate-400 bg-white rounded-xl border border-slate-200">
                <p class="font-medium">Tidak ada submission yang sedang diproses.</p>
                <a href="{{ route('submit') }}" class="text-blue-600 text-sm hover:underline mt-1 inline-block">Kirim naskah pertama Anda</a>
            </div>
            @endforelse
        </div>

        {{-- Published --}}
        <div x-show="tab === 'published'" x-cloak>
            @forelse($published as $sub)
            @include('livewire.author._submission-row', ['submission' => $sub])
            @empty
            <div class="text-center py-12 text-slate-400 bg-white rounded-xl border border-slate-200">
                <p class="font-medium">Belum ada artikel yang diterbitkan.</p>
            </div>
            @endforelse
        </div>

        {{-- Drafts --}}
        <div x-show="tab === 'drafts'" x-cloak>
            @forelse($drafts as $sub)
            @include('livewire.author._submission-row', ['submission' => $sub])
            @empty
            <div class="text-center py-12 text-slate-400 bg-white rounded-xl border border-slate-200">
                <p class="font-medium">Tidak ada draft tersimpan.</p>
            </div>
            @endforelse
        </div>

        {{-- Declined --}}
        <div x-show="tab === 'declined'" x-cloak>
            @forelse($declined as $sub)
            @include('livewire.author._submission-row', ['submission' => $sub])
            @empty
            <div class="text-center py-12 text-slate-400 bg-white rounded-xl border border-slate-200">
                <p class="font-medium">Tidak ada submission yang ditolak.</p>
            </div>
            @endforelse
        </div>
    </div>
</div>
