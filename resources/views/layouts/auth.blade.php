<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', $brandName)</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700&display=swap" rel="stylesheet">
    @if(!empty($brandFavicon))
    <link rel="icon" type="image/png" href="{{ $brandFavicon }}">
    @endif
    @livewireStyles
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen antialiased" style="background:radial-gradient(ellipse at 60% 10%,#dbeafe 0%,transparent 55%),radial-gradient(ellipse at 5% 90%,#e0f2fe 0%,transparent 50%),#f8fafc;">
<div class="min-h-screen flex flex-col items-center justify-center px-4 py-12">

    {{-- Logo --}}
    <a href="{{ route('home') }}" class="flex items-center gap-3 mb-8 group">
        @if(!empty($brandLogo))
        <img src="{{ $brandLogo }}" alt="{{ $brandName }}" class="h-10 w-auto object-contain group-hover:scale-105 transition-transform duration-200">
        @else
        <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center shadow-md group-hover:shadow-blue-200 group-hover:scale-105 transition-all duration-200">
            <span class="text-white font-black text-sm tracking-tight">{{ $brandAbbrev }}</span>
        </div>
        @endif
        <div class="leading-tight">
            <div class="font-bold text-slate-900 text-xl group-hover:text-blue-700 transition-colors duration-200">{{ $brandName }}</div>
            @if(!empty($brandTagline))
            <div class="text-xs text-slate-400 font-normal">{{ $brandTagline }}</div>
            @endif
        </div>
    </a>

    {{-- Card --}}
    <div class="w-full @yield('card-width', 'max-w-md') bg-white rounded-2xl shadow-xl shadow-slate-200/60 border border-slate-100 p-8">
        @yield('content')
    </div>

    <p class="mt-6 text-xs text-slate-400">
        @if(!empty($brandCopyright))
        {{ $brandCopyright }}
        @else
        &copy; {{ date('Y') }} {{ $brandName }}. All rights reserved.
        @endif
    </p>
</div>
@livewireScripts
</body>
</html>
