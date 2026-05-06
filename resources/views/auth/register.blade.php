@extends('layouts.auth')

@section('title', 'Daftar Akun — ' . config('app.name'))
@section('card-width', 'max-w-2xl')

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold text-slate-900 mb-1">Buat Akun Baru</h2>
    <p class="text-sm text-slate-500">Bergabung dengan komunitas peneliti. Gratis selamanya.</p>
</div>

<form method="POST" action="{{ route('register') }}" id="registerForm" novalidate>
    @csrf

    {{-- ── Honeypot (bot trap) ────────────────────────────────────────────── --}}
    <div aria-hidden="true" style="position:absolute;left:-9999px;width:1px;height:1px;overflow:hidden">
        <label for="hp_website">Website</label>
        <input type="text" id="hp_website" name="hp_website" tabindex="-1" autocomplete="off" value="">
    </div>

    {{-- ── SECTION 1: Data Pribadi ──────────────────────────────────────── --}}
    <div class="mb-6">
        <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-4 flex items-center gap-2">
            <span class="w-5 h-5 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-xs font-bold shrink-0">1</span>
            Data Pribadi
        </h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
                <label for="first_name" class="block text-sm font-medium text-slate-700 mb-1">Nama Depan <span class="text-red-500">*</span></label>
                <input id="first_name" name="first_name" type="text" required autocomplete="given-name"
                       value="{{ old('first_name') }}"
                       class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('first_name') border-red-400 @enderror">
                @error('first_name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="last_name" class="block text-sm font-medium text-slate-700 mb-1">Nama Belakang <span class="text-red-500">*</span></label>
                <input id="last_name" name="last_name" type="text" required autocomplete="family-name"
                       value="{{ old('last_name') }}"
                       class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('last_name') border-red-400 @enderror">
                @error('last_name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>
    </div>

    {{-- ── SECTION 2: Kontak & Institusi ──────────────────────────────────── --}}
    <div class="mb-6 pt-5 border-t border-slate-100">
        <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-4 flex items-center gap-2">
            <span class="w-5 h-5 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-xs font-bold shrink-0">2</span>
            Kontak & Institusi
        </h3>
        <div class="space-y-3">
            <div>
                <label for="email" class="block text-sm font-medium text-slate-700 mb-1">Alamat Email <span class="text-red-500">*</span></label>
                <input id="email" name="email" type="email" required autocomplete="email"
                       value="{{ old('email') }}"
                       placeholder="nama@institusi.ac.id"
                       class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('email') border-red-400 @enderror">
                @error('email')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label for="affiliation" class="block text-sm font-medium text-slate-700 mb-1">
                        Afiliasi / Institusi
                        <span class="text-slate-400 font-normal">(opsional)</span>
                    </label>
                    <input id="affiliation" name="affiliation" type="text" autocomplete="organization"
                           value="{{ old('affiliation') }}"
                           placeholder="Universitas / Lembaga Penelitian..."
                           class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label for="country" class="block text-sm font-medium text-slate-700 mb-1">
                        Negara
                        <span class="text-slate-400 font-normal">(opsional)</span>
                    </label>
                    <select id="country" name="country"
                            class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">— Pilih Negara —</option>
                        <option value="ID" {{ old('country', 'ID') === 'ID' ? 'selected' : '' }}>Indonesia</option>
                        <option value="MY" {{ old('country') === 'MY' ? 'selected' : '' }}>Malaysia</option>
                        <option value="SG" {{ old('country') === 'SG' ? 'selected' : '' }}>Singapura</option>
                        <option value="PH" {{ old('country') === 'PH' ? 'selected' : '' }}>Filipina</option>
                        <option value="TH" {{ old('country') === 'TH' ? 'selected' : '' }}>Thailand</option>
                        <option value="VN" {{ old('country') === 'VN' ? 'selected' : '' }}>Vietnam</option>
                        <option value="AU" {{ old('country') === 'AU' ? 'selected' : '' }}>Australia</option>
                        <option value="NZ" {{ old('country') === 'NZ' ? 'selected' : '' }}>Selandia Baru</option>
                        <option value="US" {{ old('country') === 'US' ? 'selected' : '' }}>Amerika Serikat</option>
                        <option value="GB" {{ old('country') === 'GB' ? 'selected' : '' }}>Inggris</option>
                        <option value="DE" {{ old('country') === 'DE' ? 'selected' : '' }}>Jerman</option>
                        <option value="NL" {{ old('country') === 'NL' ? 'selected' : '' }}>Belanda</option>
                        <option value="JP" {{ old('country') === 'JP' ? 'selected' : '' }}>Jepang</option>
                        <option value="KR" {{ old('country') === 'KR' ? 'selected' : '' }}>Korea Selatan</option>
                        <option value="CN" {{ old('country') === 'CN' ? 'selected' : '' }}>Tiongkok</option>
                        <option value="IN" {{ old('country') === 'IN' ? 'selected' : '' }}>India</option>
                        <option value="SA" {{ old('country') === 'SA' ? 'selected' : '' }}>Arab Saudi</option>
                        <option value="EG" {{ old('country') === 'EG' ? 'selected' : '' }}>Mesir</option>
                        <option value="ZA" {{ old('country') === 'ZA' ? 'selected' : '' }}>Afrika Selatan</option>
                        <option value="BR" {{ old('country') === 'BR' ? 'selected' : '' }}>Brasil</option>
                        <option value="CA" {{ old('country') === 'CA' ? 'selected' : '' }}>Kanada</option>
                        <option value="FR" {{ old('country') === 'FR' ? 'selected' : '' }}>Prancis</option>
                        <option value="IT" {{ old('country') === 'IT' ? 'selected' : '' }}>Italia</option>
                        <option value="PK" {{ old('country') === 'PK' ? 'selected' : '' }}>Pakistan</option>
                        <option value="BD" {{ old('country') === 'BD' ? 'selected' : '' }}>Bangladesh</option>
                        <option value="NG" {{ old('country') === 'NG' ? 'selected' : '' }}>Nigeria</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    {{-- ── SECTION 3: Profil Peneliti ──────────────────────────────────────── --}}
    <div class="mb-6 pt-5 border-t border-slate-100">
        <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1 flex items-center gap-2">
            <span class="w-5 h-5 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-xs font-bold shrink-0">3</span>
            Profil Peneliti
            <span class="text-slate-400 font-normal normal-case text-xs">(opsional)</span>
        </h3>
        <p class="text-xs text-slate-400 mb-4 ml-7">ORCID iD membantu menghubungkan karya ilmiah Anda secara global.</p>
        <div>
            <label for="orcid" class="block text-sm font-medium text-slate-700 mb-1">ORCID iD</label>
            <div class="flex gap-2">
                <div class="flex items-center px-3 py-2 bg-slate-50 border border-r-0 border-slate-300 rounded-l-lg text-xs text-slate-500 shrink-0">
                    orcid.org/
                </div>
                <input id="orcid" name="orcid" type="text"
                       value="{{ old('orcid') }}"
                       placeholder="0000-0000-0000-0000"
                       pattern="\d{4}-\d{4}-\d{4}-\d{3}[\dX]"
                       class="flex-1 px-3 py-2 border border-slate-300 rounded-r-lg text-sm font-mono focus:outline-none focus:ring-2 focus:ring-blue-500 @error('orcid') border-red-400 @enderror">
            </div>
            @error('orcid')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>
    </div>

    {{-- ── SECTION 4: Keamanan Akun ─────────────────────────────────────────── --}}
    <div class="mb-6 pt-5 border-t border-slate-100"
         x-data="{
            password: '',
            showPass: false,
            showConfirm: false,
            get score() {
                const p = this.password;
                if (p.length === 0) return 0;
                let s = 0;
                if (p.length >= 8)  s++;
                if (p.length >= 12) s++;
                if (/[A-Z]/.test(p)) s++;
                if (/[0-9]/.test(p)) s++;
                if (/[^A-Za-z0-9]/.test(p)) s++;
                return s;
            },
            get label() {
                return ['', 'Sangat Lemah', 'Lemah', 'Sedang', 'Kuat', 'Sangat Kuat'][this.score] || '';
            },
            get color() {
                return ['', 'text-red-600', 'text-orange-500', 'text-yellow-600', 'text-blue-600', 'text-green-600'][this.score] || '';
            },
            get barColor() {
                return ['', 'bg-red-500', 'bg-orange-500', 'bg-yellow-500', 'bg-blue-500', 'bg-green-500'][this.score] || '';
            }
         }">
        <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-4 flex items-center gap-2">
            <span class="w-5 h-5 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-xs font-bold shrink-0">4</span>
            Keamanan Akun
        </h3>
        <div class="space-y-3">
            <div>
                <label for="password" class="block text-sm font-medium text-slate-700 mb-1">Kata Sandi <span class="text-red-500">*</span></label>
                <div class="relative">
                    <input id="password" name="password" :type="showPass ? 'text' : 'password'"
                           x-model="password"
                           required autocomplete="new-password"
                           class="w-full px-3 py-2 pr-10 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('password') border-red-400 @enderror">
                    <button type="button" @click="showPass = !showPass"
                            class="absolute inset-y-0 right-0 flex items-center px-3 text-slate-400 hover:text-slate-600">
                        <svg x-show="!showPass" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <svg x-show="showPass" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                        </svg>
                    </button>
                </div>
                {{-- Strength bar --}}
                <div x-show="password.length > 0" class="mt-2" style="display:none">
                    <div class="flex gap-1 mb-1">
                        <template x-for="i in 5">
                            <div class="h-1 flex-1 rounded-full transition-all duration-300"
                                 :class="i <= score ? barColor : 'bg-slate-200'"></div>
                        </template>
                    </div>
                    <p class="text-xs" :class="color">
                        Kekuatan kata sandi: <span class="font-medium" x-text="label"></span>
                    </p>
                </div>
                <p class="mt-1 text-xs text-slate-400">Min. 8 karakter. Gunakan huruf besar, angka, dan simbol.</p>
                @error('password')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-slate-700 mb-1">Konfirmasi Kata Sandi <span class="text-red-500">*</span></label>
                <div class="relative">
                    <input id="password_confirmation" name="password_confirmation" :type="showConfirm ? 'text' : 'password'"
                           required autocomplete="new-password"
                           class="w-full px-3 py-2 pr-10 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <button type="button" @click="showConfirm = !showConfirm"
                            class="absolute inset-y-0 right-0 flex items-center px-3 text-slate-400 hover:text-slate-600">
                        <svg x-show="!showConfirm" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <svg x-show="showConfirm" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ── SECTION 5: Peran ─────────────────────────────────────────────────── --}}
    <div class="mb-6 pt-5 border-t border-slate-100">
        <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3 flex items-center gap-2">
            <span class="w-5 h-5 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-xs font-bold shrink-0">5</span>
            Daftar sebagai
        </h3>
        <div class="space-y-2">
            <label class="flex items-start gap-3 p-3 border border-slate-200 rounded-lg bg-slate-50 cursor-not-allowed opacity-75">
                <input type="checkbox" checked disabled class="mt-0.5 rounded text-blue-600">
                <div>
                    <p class="text-sm font-medium text-slate-700">Penulis</p>
                    <p class="text-xs text-slate-500">Kirim naskah dan pantau proses review</p>
                </div>
                <span class="ml-auto text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full font-medium shrink-0">Default</span>
            </label>
            <label class="flex items-start gap-3 p-3 border border-slate-200 rounded-lg hover:bg-slate-50 cursor-pointer transition-colors
                          {{ old('register_as_reviewer') ? 'border-blue-300 bg-blue-50' : '' }}">
                <input type="checkbox" name="register_as_reviewer" value="1"
                       {{ old('register_as_reviewer') ? 'checked' : '' }}
                       class="mt-0.5 rounded text-blue-600 focus:ring-blue-500">
                <div>
                    <p class="text-sm font-medium text-slate-700">Reviewer</p>
                    <p class="text-xs text-slate-500">Berpartisipasi sebagai mitra bestari untuk mengevaluasi naskah</p>
                </div>
            </label>
        </div>
    </div>

    {{-- ── SECTION 6: Persetujuan Privasi ──────────────────────────────────── --}}
    <div class="mb-6 pt-5 border-t border-slate-100">
        <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 mb-4 text-xs text-slate-600 leading-relaxed max-h-28 overflow-y-auto">
            <strong class="text-slate-800">Pernyataan Privasi</strong><br>
            Nama dan alamat email yang Anda daftarkan di platform ini akan digunakan sepenuhnya untuk keperluan yang dinyatakan dan tidak akan disediakan untuk tujuan lain maupun pihak ketiga mana pun. Informasi yang Anda berikan akan digunakan sesuai dengan kebijakan privasi kami dan regulasi perlindungan data yang berlaku.
        </div>
        <label class="flex items-start gap-3 cursor-pointer">
            <input type="checkbox" name="privacy_consent" value="1"
                   {{ old('privacy_consent') ? 'checked' : '' }}
                   required
                   class="mt-0.5 rounded text-blue-600 focus:ring-blue-500 @error('privacy_consent') ring-2 ring-red-400 @enderror">
            <span class="text-sm text-slate-700">
                Ya, saya menyetujui pengumpulan dan penyimpanan data saya sesuai pernyataan privasi di atas. <span class="text-red-500">*</span>
            </span>
        </label>
        @error('privacy_consent')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>

    {{-- ── Submit ──────────────────────────────────────────────────────────── --}}
    @if($errors->any())
    <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-xl">
        <p class="text-sm font-semibold text-red-800 mb-1">Mohon perbaiki kesalahan berikut:</p>
        <ul class="space-y-0.5">
            @foreach($errors->all() as $error)
            <li class="text-sm text-red-700 flex items-start gap-1.5">
                <svg class="w-3.5 h-3.5 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                {{ $error }}
            </li>
            @endforeach
        </ul>
    </div>
    @endif

    <button type="submit"
            class="w-full py-3 px-4 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white text-sm font-semibold rounded-xl transition-colors shadow-sm">
        Buat Akun
    </button>

    @if(config('services.orcid.client_id'))
    <div class="relative my-4">
        <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-slate-200"></div></div>
        <div class="relative flex justify-center"><span class="px-3 bg-white text-xs text-slate-400">atau daftar dengan</span></div>
    </div>
    <a href="{{ route('orcid.redirect') }}"
       class="flex items-center justify-center gap-2 w-full py-2.5 px-4 border border-slate-300 rounded-xl text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">
        <img src="https://orcid.org/sites/default/files/images/orcid_16x16.png" alt="ORCID" class="w-4 h-4">
        Daftar / Masuk dengan ORCID
    </a>
    @endif
</form>

<div class="mt-6 pt-5 border-t border-slate-100 text-center">
    <p class="text-sm text-slate-500">
        Sudah punya akun?
        <a href="{{ route('login') }}" class="text-blue-600 font-medium hover:underline">Masuk</a>
    </p>
</div>
@endsection
