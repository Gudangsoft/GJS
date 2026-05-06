@extends('layouts.auth')

@section('title', 'Masuk — ' . config('app.name'))

@section('content')
    <h2 class="text-2xl font-bold text-slate-900 mb-1">Selamat datang kembali</h2>
    <p class="text-sm text-slate-500 mb-6">Masuk untuk mengakses dashboard jurnal Anda</p>

    @if(session('status'))
    <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-lg text-sm text-green-700">
        {{ session('status') }}
    </div>
    @endif
    @if(session('error'))
    <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700">
        {{ session('error') }}
    </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf
        <div>
            <label for="email" class="block text-sm font-medium text-slate-700 mb-1">Email</label>
            <input id="email" name="email" type="email" autocomplete="email" required value="{{ old('email') }}"
                   class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('email') border-red-400 @enderror">
            @error('email')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <div class="flex items-center justify-between mb-1">
                <label for="password" class="block text-sm font-medium text-slate-700">Kata Sandi</label>
                @if(Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="text-xs text-blue-600 hover:underline">Lupa kata sandi?</a>
                @endif
            </div>
            <input id="password" name="password" type="password" autocomplete="current-password" required
                   class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('password') border-red-400 @enderror">
            @error('password')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        <div class="flex items-center gap-2">
            <input id="remember" name="remember" type="checkbox"
                   class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
            <label for="remember" class="text-sm text-slate-600">Ingat saya</label>
        </div>

        <button type="submit"
                class="w-full py-2.5 px-4 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition-colors">
            Masuk
        </button>

        @if(config('services.orcid.client_id'))
        <div class="relative my-4">
            <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-slate-200"></div></div>
            <div class="relative flex justify-center"><span class="px-3 bg-white text-xs text-slate-400">atau</span></div>
        </div>
        <a href="{{ route('orcid.redirect') }}"
           class="flex items-center justify-center gap-2 w-full py-2.5 px-4 border border-slate-300 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">
            <img src="https://orcid.org/sites/default/files/images/orcid_16x16.png" alt="ORCID" class="w-4 h-4">
            Masuk dengan ORCID
        </a>
        @endif
    </form>

    <p class="mt-6 text-center text-sm text-slate-500">
        Belum punya akun?
        <a href="{{ route('register') }}" class="text-blue-600 font-medium hover:underline">Daftar sekarang</a>
    </p>
@endsection
