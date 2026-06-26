@php
    $bLogo   = \App\Models\Setting::get('brand.logo');
    $bName   = \App\Models\Setting::get('brand.site_name', config('app.name'));
    $bAbbrev = \App\Models\Setting::get('brand.abbrev') ?: strtoupper(substr(preg_replace('/[^A-Za-z]/','',$bName),0,3)) ?: 'APP';
    $bTagline = \App\Models\Setting::get('brand.tagline', 'Go Journal System');
@endphp
<div class="flex items-center gap-2.5">
    @if($bLogo)
    <img src="{{ asset('storage/' . $bLogo) }}" alt="{{ $bName }}" class="h-8 w-auto object-contain">
    @else
    <div class="w-8 h-8 rounded-lg bg-blue-600 flex items-center justify-center shrink-0">
        <svg viewBox="0 0 24 24" fill="none" class="w-5 h-5 text-white" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0118 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/>
        </svg>
    </div>
    @endif
    <div class="leading-tight">
        <span class="text-sm font-black tracking-tight text-slate-900 dark:text-white">{{ $bAbbrev }}</span>
        <span class="block text-[10px] font-medium text-slate-400 dark:text-slate-500 -mt-0.5 tracking-wider uppercase">{{ $bTagline }}</span>
    </div>
</div>
