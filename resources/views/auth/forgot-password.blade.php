@extends('layouts.auth')

@section('title', 'Lupa Kata Sandi — ' . config('app.name'))

@section('content')
    <h2 class="text-2xl font-bold text-slate-900 mb-1">Lupa kata sandi?</h2>
    <p class="text-sm text-slate-500 mb-6">Masukkan email Anda dan kami akan mengirimkan tautan reset.</p>

    @if(session('status'))
    <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-lg text-sm text-green-700">
        {{ session('status') }}
    </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
        @csrf
        <div>
            <label for="email" class="block text-sm font-medium text-slate-700 mb-1">Email</label>
            <input id="email" name="email" type="email" required value="{{ old('email') }}"
                   class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('email') border-red-400 @enderror">
            @error('email')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>
        <button type="submit"
                class="w-full py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition-colors">
            Kirim Tautan Reset
        </button>
    </form>

    <p class="mt-6 text-center text-sm text-slate-500">
        <a href="{{ route('login') }}" class="text-blue-600 hover:underline">← Kembali ke halaman masuk</a>
    </p>
@endsection
