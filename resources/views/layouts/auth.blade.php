<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name'))</title>
    @livewireStyles
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50 antialiased">
<div class="min-h-screen flex flex-col items-center justify-center px-4 py-12">

    {{-- Logo --}}
    <a href="{{ route('home') }}" class="flex items-center gap-3 mb-8">
        <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center shadow-md">
            <span class="text-white font-black text-sm">GJS</span>
        </div>
        <span class="font-bold text-slate-900 text-xl">{{ config('app.name') }}</span>
    </a>

    {{-- Card --}}
    <div class="w-full @yield('card-width', 'max-w-md') bg-white rounded-2xl shadow-sm border border-slate-200 p-8">
        @yield('content')
    </div>

    <p class="mt-6 text-xs text-slate-400">&copy; {{ date('Y') }} {{ config('app.name') }}</p>
</div>
@livewireScripts
</body>
</html>
