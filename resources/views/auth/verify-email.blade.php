@extends('layouts.auth')

@section('title', 'Verifikasi Email — ' . config('app.name'))

@section('content')
<div class="text-center mb-6">
    <div class="w-14 h-14 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
        <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
        </svg>
    </div>
    <h2 class="text-xl font-bold text-slate-900 mb-1">Verifikasi Alamat Email</h2>
    <p class="text-sm text-slate-500">Satu langkah lagi untuk mengaktifkan akun Anda</p>
</div>

@if(session('status') === 'verification-link-sent')
<div class="mb-5 p-4 bg-green-50 border border-green-200 rounded-xl flex items-start gap-3">
    <svg class="w-5 h-5 text-green-600 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
    </svg>
    <p class="text-sm text-green-800">
        Tautan verifikasi baru telah dikirim ke alamat email Anda. Silakan cek kotak masuk (dan folder spam).
    </p>
</div>
@endif

<div class="bg-slate-50 border border-slate-200 rounded-xl p-5 mb-6 text-sm text-slate-700 leading-relaxed">
    Terima kasih sudah mendaftar! Sebelum dapat menggunakan semua fitur, harap verifikasi alamat email Anda dengan mengklik tautan yang telah kami kirimkan.
    <br><br>
    Jika Anda tidak menerima email tersebut, klik tombol di bawah untuk mengirim ulang.
</div>

<form method="POST" action="{{ route('verification.send') }}" class="mb-4">
    @csrf
    <button type="submit"
            class="w-full py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition-colors">
        Kirim Ulang Email Verifikasi
    </button>
</form>

<form method="POST" action="{{ route('logout') }}">
    @csrf
    <button type="submit"
            class="w-full py-2.5 border border-slate-300 text-slate-600 text-sm font-medium rounded-lg hover:bg-slate-50 transition-colors">
        Keluar
    </button>
</form>
@endsection
