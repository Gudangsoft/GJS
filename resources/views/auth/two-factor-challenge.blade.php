@extends('layouts.auth')

@section('title', 'Verifikasi Dua Faktor — ' . config('app.name'))

@section('content')
    <div class="text-center mb-6">
        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-3">
            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
        </div>
        <h2 class="text-xl font-bold text-slate-900">Verifikasi Dua Faktor</h2>
        <p class="text-sm text-slate-500 mt-1">Masukkan kode dari aplikasi autentikator Anda</p>
    </div>

    <div x-data="{ recovery: false }">
        <div x-show="!recovery">
            <form method="POST" action="{{ route('two-factor.login') }}" class="space-y-4">
                @csrf
                <div>
                    <label for="code" class="block text-sm font-medium text-slate-700 mb-1">Kode Autentikasi</label>
                    <input id="code" name="code" type="text" inputmode="numeric" autofocus autocomplete="one-time-code"
                           class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm text-center tracking-widest focus:outline-none focus:ring-2 focus:ring-blue-500 @error('code') border-red-400 @enderror">
                    @error('code')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <button type="submit"
                        class="w-full py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition-colors">
                    Verifikasi
                </button>
            </form>
            <button @click="recovery = true" class="mt-4 w-full text-sm text-slate-500 hover:text-blue-600 text-center">
                Gunakan kode pemulihan
            </button>
        </div>

        <div x-show="recovery" style="display:none">
            <form method="POST" action="{{ route('two-factor.login') }}" class="space-y-4">
                @csrf
                <div>
                    <label for="recovery_code" class="block text-sm font-medium text-slate-700 mb-1">Kode Pemulihan</label>
                    <input id="recovery_code" name="recovery_code" type="text" autocomplete="one-time-code"
                           class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <button type="submit"
                        class="w-full py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition-colors">
                    Verifikasi
                </button>
            </form>
            <button @click="recovery = false" class="mt-4 w-full text-sm text-slate-500 hover:text-blue-600 text-center">
                Gunakan kode autentikasi
            </button>
        </div>
    </div>
@endsection
