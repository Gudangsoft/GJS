<div style="background:#f6f8fb;min-height:100vh;">

<div class="px-6 py-5 border-b border-slate-200 bg-white">
    <div>
        <h1 class="text-xl font-bold text-slate-900">Daftar Pengguna</h1>
        <p class="text-sm text-slate-500 mt-0.5">Editor, reviewer, dan manajer jurnal Anda.</p>
    </div>
</div>

<div style="max-width:72rem;margin:0 auto;padding:1.5rem;">

@if($journal)

{{-- Search --}}
<div class="mb-5">
    <div class="relative max-w-sm">
        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        <input wire:model.live.debounce.400ms="search" type="text" placeholder="Cari nama atau email..."
               class="w-full pl-9 pr-4 py-2 text-sm rounded-lg border border-slate-200 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
    </div>
</div>

<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    @if($users->isEmpty())
    <div class="text-center py-16 text-slate-400">
        <svg class="w-10 h-10 mx-auto mb-2 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
        <p class="text-sm font-medium">Tidak ada pengguna ditemukan.</p>
    </div>
    @else
    @php
    $roleColor = [
        'journal_manager' => ['#1d4ed8','#eff6ff'],
        'editor'          => ['#7c3aed','#faf5ff'],
        'reviewer'        => ['#0891b2','#ecfeff'],
    ];
    @endphp
    <table class="w-full text-sm">
        <thead class="bg-slate-50 border-b border-slate-100">
            <tr>
                <th class="text-left px-5 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Nama</th>
                <th class="text-left px-5 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Email</th>
                <th class="text-left px-5 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Peran</th>
                <th class="text-left px-5 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Terdaftar</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-50">
            @foreach($users as $u)
            <tr class="hover:bg-slate-50 transition-colors">
                <td class="px-5 py-3.5">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-blue-500 flex items-center justify-center text-white text-xs font-bold shrink-0">
                            {{ strtoupper(substr($u->first_name, 0, 1)) }}{{ strtoupper(substr($u->last_name ?? '', 0, 1)) }}
                        </div>
                        <div>
                            <p class="font-semibold text-slate-900">{{ $u->first_name }} {{ $u->last_name }}</p>
                        </div>
                    </div>
                </td>
                <td class="px-5 py-3.5 text-slate-600">{{ $u->email }}</td>
                <td class="px-5 py-3.5">
                    <div class="flex flex-wrap gap-1">
                        @foreach($u->roles as $role)
                        @php [$c,$bg] = $roleColor[$role->name] ?? ['#64748b','#f8fafc']; @endphp
                        <span class="text-xs font-bold px-2 py-0.5 rounded-full"
                              style="background:{{ $bg }};color:{{ $c }};">{{ $role->name }}</span>
                        @endforeach
                    </div>
                </td>
                <td class="px-5 py-3.5 text-slate-500 text-xs">{{ $u->created_at->format('d M Y') }}</td>
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
