<div style="background:#f1f5f9;min-height:100vh;">

{{-- ══ HERO ════════════════════════════════════════════════════════════════════ --}}
<div style="background:linear-gradient(135deg,#0f172a 0%,#1e3a8a 55%,#1d4ed8 100%);position:relative;overflow:hidden;">
    <div style="position:absolute;top:-4rem;right:-4rem;width:20rem;height:20rem;border-radius:50%;background:rgba(255,255,255,.04);pointer-events:none;"></div>
    <div style="position:absolute;bottom:-3rem;left:25%;width:14rem;height:14rem;border-radius:50%;background:rgba(255,255,255,.03);pointer-events:none;"></div>

    <div style="padding:2rem 2rem 1.5rem;display:flex;align-items:center;gap:1.75rem;flex-wrap:wrap;">
        {{-- Avatar hero --}}
        <div style="position:relative;flex-shrink:0;">
            @if($currentAvatar)
                <img src="{{ Storage::url($currentAvatar) }}" alt="Foto"
                     style="width:5.5rem;height:5.5rem;border-radius:50%;object-fit:contain;background:#fff;border:3px solid rgba(255,255,255,.3);">
            @else
                <div style="width:5.5rem;height:5.5rem;border-radius:50%;background:rgba(255,255,255,.15);border:3px solid rgba(255,255,255,.25);display:flex;align-items:center;justify-content:center;font-size:2rem;font-weight:800;color:#fff;">
                    {{ strtoupper(substr($first_name,0,1)) }}{{ strtoupper(substr($last_name,0,1)) }}
                </div>
            @endif
        </div>

        {{-- Name & info --}}
        <div style="flex:1;min-width:200px;">
            <p style="font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:#93c5fd;margin:0 0 .25rem;">Panel Penulis</p>
            <h1 style="font-size:1.5rem;font-weight:800;color:#fff;margin:0 0 .25rem;line-height:1.2;">
                {{ trim(($salutation ? $salutation.' ' : '').$first_name.' '.$last_name) ?: 'Profil Saya' }}
            </h1>
            @if($position || $affiliation)
            <p style="font-size:.875rem;color:rgba(255,255,255,.65);margin:0;">
                {{ $position }}{{ ($position && $affiliation) ? ' — ' : '' }}{{ $affiliation }}
            </p>
            @endif
        </div>

        {{-- Stats --}}
        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:.75rem;">
            @foreach([
                ['n' => $totalSubmitted, 'label' => 'Disubmit',   'color' => '#93c5fd'],
                ['n' => $totalActive,    'label' => 'Aktif',       'color' => '#a5f3fc'],
                ['n' => $totalPublished, 'label' => 'Terbit',      'color' => '#6ee7b7'],
            ] as $s)
            <div style="background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.12);border-radius:.75rem;padding:.75rem .875rem;text-align:center;">
                <div style="font-size:1.375rem;font-weight:800;color:{{ $s['color'] }};line-height:1;">{{ $s['n'] }}</div>
                <div style="font-size:.7rem;font-weight:600;color:rgba(255,255,255,.5);margin-top:.2rem;">{{ $s['label'] }}</div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<div style="padding:1.5rem 2rem;">

    @if($saved)
    <div style="background:#eff6ff;border:1px solid #bfdbfe;color:#1e40af;border-radius:.875rem;padding:.875rem 1.125rem;margin-bottom:1.25rem;display:flex;align-items:center;gap:.625rem;font-size:.875rem;font-weight:600;">
        <svg style="width:1rem;height:1rem;flex-shrink:0;" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
        </svg>
        Profil berhasil disimpan.
    </div>
    @endif

    <form wire:submit="save">
    <div style="display:grid;grid-template-columns:1fr 300px;gap:1.25rem;align-items:start;" class="profil-grid">

        {{-- ═══ LEFT COLUMN ════════════════════════════════════════════════════ --}}
        <div style="display:flex;flex-direction:column;gap:1.125rem;">

            {{-- ── Foto Profil ─────────────────────────────────────────────── --}}
            <div style="background:#fff;border-radius:1rem;border:1px solid #e2e8f0;box-shadow:0 1px 3px rgba(0,0,0,.05);padding:1.375rem;">
                <h2 style="font-size:.875rem;font-weight:700;color:#0f172a;margin:0 0 1.125rem;display:flex;align-items:center;gap:.5rem;">
                    <svg style="width:1rem;height:1rem;color:#1d4ed8;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    Foto Profil
                </h2>
                <div style="display:flex;align-items:center;gap:1.5rem;flex-wrap:wrap;">
                    {{-- Avatar preview --}}
                    <label for="photoInput" style="flex-shrink:0;position:relative;cursor:pointer;">
                        <div style="width:7rem;height:7rem;border-radius:50%;overflow:hidden;border:3px solid #e2e8f0;box-shadow:0 4px 16px rgba(0,0,0,.1);background:#fff;">
                            @if($photo)
                                <img src="{{ $photo->temporaryUrl() }}" style="width:100%;height:100%;object-fit:contain;display:block;">
                            @elseif($currentAvatar)
                                <img src="{{ Storage::url($currentAvatar) }}" style="width:100%;height:100%;object-fit:contain;display:block;">
                            @else
                                <div style="width:100%;height:100%;background:#eff6ff;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:.25rem;">
                                    <svg style="width:2rem;height:2rem;color:#93c5fd;" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
                                    <span style="font-size:.6rem;color:#93c5fd;font-weight:600;">Belum ada foto</span>
                                </div>
                            @endif
                        </div>
                        <div style="position:absolute;bottom:.25rem;right:.25rem;width:1.75rem;height:1.75rem;background:#1d4ed8;border-radius:50%;border:2px solid #fff;display:flex;align-items:center;justify-content:center;box-shadow:0 2px 6px rgba(0,0,0,.2);">
                            <svg style="width:.875rem;height:.875rem;color:#fff;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z"/><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM18.75 10.5h.008v.008h-.008V10.5z"/></svg>
                        </div>
                    </label>
                    <div style="flex:1;min-width:0;">
                        <p style="font-size:.9375rem;font-weight:700;color:#0f172a;margin:0 0 .25rem;">Ubah Foto Profil</p>
                        <p style="font-size:.8rem;color:#64748b;margin:0 0 .875rem;line-height:1.5;">Klik foto atau tombol di bawah, pilih gambar, lalu klik <strong>Simpan</strong>.</p>
                        <label for="photoInput"
                               style="display:inline-flex;align-items:center;gap:.5rem;padding:.5rem 1rem;background:#1d4ed8;color:#fff;border-radius:.5rem;font-size:.8125rem;font-weight:600;cursor:pointer;">
                            <svg style="width:.875rem;height:.875rem;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/></svg>
                            Pilih Foto
                        </label>
                        <input type="file" id="photoInput" wire:model="photo" accept="image/jpeg,image/png,image/webp,image/gif" style="display:none;">

                        {{-- Upload progress --}}
                        <div wire:loading wire:target="photo" style="margin-top:.75rem;display:flex;align-items:center;gap:.5rem;">
                            <svg style="width:.875rem;height:.875rem;color:#1d4ed8;animation:spin .8s linear infinite;" fill="none" viewBox="0 0 24 24">
                                <circle style="opacity:.25;" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path style="opacity:.75;" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                            </svg>
                            <span style="font-size:.8rem;color:#1d4ed8;font-weight:600;">Memuat foto...</span>
                        </div>

                        @if($photo)
                        <div style="margin-top:.625rem;display:flex;align-items:center;gap:.4rem;font-size:.8rem;font-weight:600;color:#1d4ed8;">
                            <svg style="width:.875rem;height:.875rem;" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Foto dipilih — klik Simpan untuk menyimpan
                        </div>
                        @endif

                        @error('photo')
                        <p style="color:#ef4444;font-size:.75rem;margin-top:.375rem;">{{ $message }}</p>
                        @enderror
                        <p style="font-size:.72rem;color:#94a3b8;margin-top:.5rem;">JPG, PNG, WebP · Maks 4MB</p>
                    </div>
                </div>
            </div>

            {{-- ── Informasi Pribadi ──────────────────────────────────────────── --}}
            <div style="background:#fff;border-radius:1rem;border:1px solid #e2e8f0;box-shadow:0 1px 3px rgba(0,0,0,.05);padding:1.375rem;">
                <h2 style="font-size:.875rem;font-weight:700;color:#0f172a;margin:0 0 1rem;display:flex;align-items:center;gap:.5rem;">
                    <svg style="width:1rem;height:1rem;color:#1d4ed8;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    Informasi Pribadi
                </h2>
                @php
                $iStyle = 'width:100%;border:1px solid #d1d5db;border-radius:.5rem;padding:.5rem .75rem;font-size:.875rem;color:#0f172a;outline:none;box-sizing:border-box;';
                $iFocus = 'onfocus="this.style.borderColor=\'#1d4ed8\';this.style.boxShadow=\'0 0 0 3px rgba(29,78,216,.1)\'" onblur="this.style.borderColor=\'#d1d5db\';this.style.boxShadow=\'none\'"';
                @endphp

                <div style="display:grid;grid-template-columns:120px 1fr 1fr;gap:.875rem;margin-bottom:.875rem;">
                    <div>
                        <label style="display:block;font-size:.8125rem;font-weight:600;color:#374151;margin-bottom:.375rem;">Sapaan</label>
                        <select wire:model="salutation" style="{{ $iStyle }}appearance:auto;" {!! $iFocus !!}>
                            <option value="">—</option>
                            <option value="Dr.">Dr.</option>
                            <option value="Prof.">Prof.</option>
                            <option value="Prof. Dr.">Prof. Dr.</option>
                            <option value="Ir.">Ir.</option>
                            <option value="Drs.">Drs.</option>
                            <option value="M.">M.</option>
                        </select>
                    </div>
                    <div>
                        <label style="display:block;font-size:.8125rem;font-weight:600;color:#374151;margin-bottom:.375rem;">Nama Depan <span style="color:#ef4444;">*</span></label>
                        <input wire:model="first_name" type="text" style="{{ $iStyle }}" {!! $iFocus !!} placeholder="Nama depan">
                        @error('first_name')<p style="color:#ef4444;font-size:.72rem;margin:.2rem 0 0;">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label style="display:block;font-size:.8125rem;font-weight:600;color:#374151;margin-bottom:.375rem;">Nama Belakang</label>
                        <input wire:model="last_name" type="text" style="{{ $iStyle }}" {!! $iFocus !!} placeholder="Nama belakang">
                    </div>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:.875rem;">
                    <div>
                        <label style="display:block;font-size:.8125rem;font-weight:600;color:#374151;margin-bottom:.375rem;">Email <span style="font-size:.7rem;font-weight:400;color:#9ca3af;">(tidak dapat diubah)</span></label>
                        <input type="email" value="{{ $email }}" readonly style="{{ $iStyle }}background:#f9fafb;color:#6b7280;cursor:not-allowed;">
                    </div>
                    <div>
                        <label style="display:block;font-size:.8125rem;font-weight:600;color:#374151;margin-bottom:.375rem;">Nomor Telepon</label>
                        <input wire:model="phone" type="tel" style="{{ $iStyle }}" {!! $iFocus !!} placeholder="+62...">
                    </div>
                </div>
            </div>

            {{-- ── Afiliasi Institusional ─────────────────────────────────────── --}}
            <div style="background:#fff;border-radius:1rem;border:1px solid #e2e8f0;box-shadow:0 1px 3px rgba(0,0,0,.05);padding:1.375rem;">
                <h2 style="font-size:.875rem;font-weight:700;color:#0f172a;margin:0 0 1rem;display:flex;align-items:center;gap:.5rem;">
                    <svg style="width:1rem;height:1rem;color:#1d4ed8;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    Afiliasi Institusional
                </h2>

                <div style="margin-bottom:.875rem;">
                    <label style="display:block;font-size:.8125rem;font-weight:600;color:#374151;margin-bottom:.375rem;">Institusi / Universitas</label>
                    <input wire:model="affiliation" type="text" style="{{ $iStyle }}" {!! $iFocus !!} placeholder="Universitas / lembaga penelitian">
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:.875rem;margin-bottom:.875rem;">
                    <div>
                        <label style="display:block;font-size:.8125rem;font-weight:600;color:#374151;margin-bottom:.375rem;">Jabatan / Posisi</label>
                        <input wire:model="position" type="text" style="{{ $iStyle }}" {!! $iFocus !!} placeholder="Dosen, Peneliti, Mahasiswa, dll.">
                    </div>
                    <div>
                        <label style="display:block;font-size:.8125rem;font-weight:600;color:#374151;margin-bottom:.375rem;">Departemen / Prodi</label>
                        <input wire:model="department" type="text" style="{{ $iStyle }}" {!! $iFocus !!} placeholder="Teknik Informatika, dll.">
                    </div>
                </div>
                <div>
                    <label style="display:block;font-size:.8125rem;font-weight:600;color:#374151;margin-bottom:.375rem;">Negara</label>
                    <select wire:model="country" style="{{ $iStyle }}appearance:auto;" {!! $iFocus !!}>
                        <option value="">— Pilih negara —</option>
                        <option value="ID">Indonesia</option>
                        <option value="MY">Malaysia</option>
                        <option value="SG">Singapura</option>
                        <option value="PH">Filipina</option>
                        <option value="TH">Thailand</option>
                        <option value="US">Amerika Serikat</option>
                        <option value="GB">Inggris</option>
                        <option value="AU">Australia</option>
                        <option value="JP">Jepang</option>
                        <option value="CN">China</option>
                    </select>
                </div>
            </div>

            {{-- ── Identitas Akademik ─────────────────────────────────────────── --}}
            <div style="background:#fff;border-radius:1rem;border:1px solid #e2e8f0;box-shadow:0 1px 3px rgba(0,0,0,.05);padding:1.375rem;">
                <h2 style="font-size:.875rem;font-weight:700;color:#0f172a;margin:0 0 .25rem;display:flex;align-items:center;gap:.5rem;">
                    <svg style="width:1rem;height:1rem;color:#1d4ed8;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                    Identitas Akademik
                </h2>
                <p style="font-size:.78rem;color:#94a3b8;margin:0 0 1rem;">ID dan profil di platform akademik internasional</p>

                @php
                $academicFields = [
                    ['key'=>'orcid',           'label'=>'ORCID iD',         'hint'=>'Format: 0000-0000-0000-0000',                     'ph'=>'0000-0000-0000-0000',                           'color'=>'#a3c13d', 'link'=>fn($v)=>'https://orcid.org/'.$v],
                    ['key'=>'google_scholar',  'label'=>'Google Scholar',   'hint'=>'URL lengkap profil Google Scholar Anda',           'ph'=>'https://scholar.google.com/citations?user=XXX',  'color'=>'#4285f4', 'link'=>fn($v)=>$v],
                    ['key'=>'scopus_id',       'label'=>'Scopus Author ID', 'hint'=>'Masukkan angka ID saja',                           'ph'=>'12345678900',                                    'color'=>'#e87722', 'link'=>fn($v)=>'https://www.scopus.com/authid/detail.uri?authorId='.$v],
                    ['key'=>'researchgate',    'label'=>'ResearchGate',     'hint'=>'URL profil ResearchGate Anda',                     'ph'=>'https://www.researchgate.net/profile/Nama',      'color'=>'#00ccbb', 'link'=>fn($v)=>$v],
                    ['key'=>'sinta_id',        'label'=>'SINTA ID',         'hint'=>'Angka ID SINTA (Kemendikbud)',                     'ph'=>'1234567',                                        'color'=>'#dc2626', 'link'=>fn($v)=>'https://sinta.kemdikbud.go.id/authors/detail?id='.$v],
                    ['key'=>'semantic_scholar','label'=>'Semantic Scholar', 'hint'=>'URL profil Semantic Scholar Anda',                 'ph'=>'https://www.semanticscholar.org/author/...',     'color'=>'#8b5cf6', 'link'=>fn($v)=>$v],
                    ['key'=>'url',             'label'=>'Website / Blog',   'hint'=>'URL website atau blog akademik Anda',              'ph'=>'https://yoursite.com',                           'color'=>'#64748b', 'link'=>fn($v)=>$v],
                ];
                @endphp

                <div style="display:flex;flex-direction:column;gap:1rem;">
                    @foreach($academicFields as $f)
                    @php $fkey = $f['key']; $fval = $$fkey ?? ''; @endphp
                    <div>
                        <label style="font-size:.8125rem;font-weight:600;color:#374151;margin-bottom:.375rem;display:flex;align-items:center;justify-content:space-between;">
                            <span style="display:flex;align-items:center;gap:.375rem;">
                                <span style="width:.5rem;height:.5rem;border-radius:50%;background:{{ $f['color'] }};display:inline-block;flex-shrink:0;"></span>
                                {{ $f['label'] }}
                            </span>
                            @if($fval)
                            <a href="{{ $f['link']($fval) }}" target="_blank"
                               style="font-size:.72rem;font-weight:600;color:{{ $f['color'] }};text-decoration:none;display:flex;align-items:center;gap:.25rem;">
                                Buka profil
                                <svg style="width:.625rem;height:.625rem;" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                            </a>
                            @endif
                        </label>
                        <input wire:model="{{ $f['key'] }}" type="text"
                               style="{{ $iStyle }}border-left:3px solid {{ $fval ? $f['color'] : '#e5e7eb' }};"
                               {!! $iFocus !!}
                               placeholder="{{ $f['ph'] }}">
                        <p style="font-size:.7rem;color:#9ca3af;margin:.25rem 0 0;">{{ $f['hint'] }}</p>
                        @error($f['key'])<p style="color:#ef4444;font-size:.72rem;margin:.2rem 0 0;">{{ $message }}</p>@enderror
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- ── Metrik Publikasi ──────────────────────────────────────────── --}}
            <div style="background:#fff;border-radius:1rem;border:1px solid #e2e8f0;box-shadow:0 1px 3px rgba(0,0,0,.05);padding:1.375rem;">
                <h2 style="font-size:.875rem;font-weight:700;color:#0f172a;margin:0 0 .25rem;display:flex;align-items:center;gap:.5rem;">
                    <svg style="width:1rem;height:1rem;color:#1d4ed8;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    Metrik Publikasi <span style="font-size:.7rem;font-weight:500;color:#94a3b8;margin-left:.25rem;">(opsional)</span>
                </h2>
                <p style="font-size:.78rem;color:#94a3b8;margin:0 0 1rem;">Data akademik publik untuk meningkatkan kepercayaan editor</p>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:.875rem;">
                    <div>
                        <label style="display:block;font-size:.8125rem;font-weight:600;color:#374151;margin-bottom:.375rem;">H-Index</label>
                        <input wire:model="h_index" type="number" min="0" max="9999"
                               style="{{ $iStyle }}" {!! $iFocus !!} placeholder="mis. 12">
                        @error('h_index')<p style="color:#ef4444;font-size:.72rem;margin:.2rem 0 0;">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label style="display:block;font-size:.8125rem;font-weight:600;color:#374151;margin-bottom:.375rem;">Total Sitasi</label>
                        <input wire:model="total_citations" type="number" min="0"
                               style="{{ $iStyle }}" {!! $iFocus !!} placeholder="mis. 450">
                        @error('total_citations')<p style="color:#ef4444;font-size:.72rem;margin:.2rem 0 0;">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            {{-- ── Bidang Keahlian ────────────────────────────────────────────── --}}
            <div style="background:#fff;border-radius:1rem;border:1px solid #e2e8f0;box-shadow:0 1px 3px rgba(0,0,0,.05);padding:1.375rem;">
                <h2 style="font-size:.875rem;font-weight:700;color:#0f172a;margin:0 0 .25rem;display:flex;align-items:center;gap:.5rem;">
                    <svg style="width:1rem;height:1rem;color:#1d4ed8;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                    Bidang Keahlian / Topik Penelitian
                </h2>
                <p style="font-size:.78rem;color:#94a3b8;margin:0 0 1rem;">Topik penelitian yang Anda tekuni (maks 15 tag)</p>

                @if(!empty($expertise_areas))
                <div style="display:flex;flex-wrap:wrap;gap:.5rem;margin-bottom:.875rem;">
                    @foreach($expertise_areas as $i => $tag)
                    <span style="display:inline-flex;align-items:center;gap:.375rem;background:#eff6ff;color:#1e40af;border:1px solid #bfdbfe;border-radius:9999px;padding:.25rem .75rem;font-size:.8125rem;font-weight:600;">
                        {{ $tag }}
                        <button type="button" wire:click="removeExpertise({{ $i }})"
                                style="display:flex;align-items:center;justify-content:center;width:1rem;height:1rem;border-radius:50%;background:#bfdbfe44;border:none;cursor:pointer;color:#1e40af;font-size:.75rem;padding:0;">×</button>
                    </span>
                    @endforeach
                </div>
                @endif

                <div style="display:flex;gap:.625rem;">
                    <input wire:model="expertiseInput"
                           wire:keydown.enter.prevent="addExpertise"
                           type="text"
                           style="{{ $iStyle }}flex:1;"
                           {!! $iFocus !!}
                           placeholder="Ketik topik lalu Enter atau klik Tambah"
                           maxlength="60">
                    <button type="button" wire:click="addExpertise"
                            style="background:#1d4ed8;color:#fff;border:none;border-radius:.5rem;padding:.5rem 1rem;font-size:.8125rem;font-weight:700;cursor:pointer;white-space:nowrap;">
                        + Tambah
                    </button>
                </div>
                <p style="font-size:.72rem;color:#94a3b8;margin:.375rem 0 0;">Contoh: Machine Learning, Pertanian Organik, Kebijakan Publik, dsb.</p>
            </div>

            {{-- ── Bio Akademik ──────────────────────────────────────────────── --}}
            <div style="background:#fff;border-radius:1rem;border:1px solid #e2e8f0;box-shadow:0 1px 3px rgba(0,0,0,.05);padding:1.375rem;">
                <h2 style="font-size:.875rem;font-weight:700;color:#0f172a;margin:0 0 .25rem;display:flex;align-items:center;gap:.5rem;">
                    <svg style="width:1rem;height:1rem;color:#1d4ed8;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    Bio Akademik
                </h2>
                <p style="font-size:.78rem;color:#94a3b8;margin:0 0 .875rem;">Deskripsi latar belakang penelitian, pengalaman, dan minat ilmiah Anda</p>

                <textarea wire:model="bio" rows="6"
                          style="{{ $iStyle }}resize:vertical;line-height:1.6;"
                          {!! $iFocus !!}
                          placeholder="Tuliskan latar belakang akademis, fokus penelitian, pengalaman publikasi, dan motivasi Anda..."></textarea>
                <div style="display:flex;justify-content:space-between;margin-top:.25rem;">
                    @error('bio')<p style="color:#ef4444;font-size:.72rem;">{{ $message }}</p>@else<span></span>@enderror
                    <span style="font-size:.72rem;color:#94a3b8;">{{ strlen($bio) }}/3000</span>
                </div>
            </div>

            {{-- ── Save Button ──────────────────────────────────────────────── --}}
            <div style="display:flex;gap:.75rem;align-items:center;">
                <button type="submit"
                        wire:loading.attr="disabled"
                        style="background:linear-gradient(135deg,#1d4ed8,#1e40af);color:#fff;border:none;border-radius:.75rem;padding:.75rem 2rem;font-size:.9375rem;font-weight:700;cursor:pointer;display:inline-flex;align-items:center;gap:.625rem;box-shadow:0 4px 14px rgba(29,78,216,.3);">
                    <span wire:loading.remove>
                        <svg style="width:1rem;height:1rem;" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    </span>
                    <svg wire:loading style="width:1rem;height:1rem;animation:spin 1s linear infinite;" fill="none" viewBox="0 0 24 24">
                        <circle style="opacity:.25;" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path style="opacity:.75;" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                    </svg>
                    <span wire:loading.remove>Simpan Semua Perubahan</span>
                    <span wire:loading>Menyimpan...</span>
                </button>
                @if($saved)
                <span style="font-size:.875rem;color:#1d4ed8;font-weight:600;display:flex;align-items:center;gap:.375rem;">
                    <svg style="width:.875rem;height:.875rem;" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    Tersimpan
                </span>
                @endif
            </div>
        </div>

        {{-- ═══ RIGHT COLUMN — Preview & Kelengkapan ══════════════════════════ --}}
        <div style="position:sticky;top:1.5rem;display:flex;flex-direction:column;gap:1rem;">

            {{-- ── Preview Card Penulis ───────────────────────────────────────── --}}
            @php
            $cardSiteName = \App\Models\Setting::get('brand.site_name', config('app.name'));
            $cardLogoRaw  = \App\Models\Setting::get('brand.logo');
            $cardLogo     = $cardLogoRaw ? asset('storage/' . $cardLogoRaw) : null;
            $displayName  = trim(($salutation ? $salutation.' ' : '') . $first_name . ' ' . $last_name);
            @endphp

            <div style="background:#fff;border-radius:1rem;border:1px solid #e2e8f0;box-shadow:0 2px 8px rgba(0,0,0,.07);overflow:hidden;">
                {{-- Header --}}
                <div style="background:linear-gradient(135deg,#0f172a 0%,#1e3a8a 60%,#1d4ed8 100%);padding:1rem 1.125rem 3.25rem;position:relative;overflow:hidden;">
                    <div style="position:absolute;top:-2rem;right:-2rem;width:7rem;height:7rem;border-radius:50%;background:rgba(255,255,255,.05);pointer-events:none;"></div>
                    <div style="display:flex;align-items:center;gap:.625rem;">
                        @if($cardLogo)
                        <img src="{{ $cardLogo }}" alt="{{ $cardSiteName }}"
                             style="height:1.25rem;max-width:2rem;width:auto;object-fit:contain;object-position:left center;opacity:.85;flex-shrink:0;">
                        @else
                        <div style="width:1.5rem;height:1.5rem;border-radius:.375rem;background:rgba(255,255,255,.2);display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:.6rem;font-weight:800;color:#fff;">
                            {{ mb_strtoupper(mb_substr($cardSiteName,0,1)) }}
                        </div>
                        @endif
                        <div style="min-width:0;">
                            <p style="font-size:.6rem;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:rgba(255,255,255,.5);margin:0 0 .1rem;">Kartu Penulis</p>
                            <p style="font-size:.8rem;font-weight:700;color:rgba(255,255,255,.95);margin:0;line-height:1.2;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $cardSiteName }}</p>
                        </div>
                    </div>
                </div>

                {{-- Avatar overlapping --}}
                <div style="padding:0 1.125rem;margin-top:-2rem;position:relative;z-index:2;">
                    @if($photo)
                        <img src="{{ $photo->temporaryUrl() }}"
                             style="width:4rem;height:4rem;border-radius:50%;object-fit:contain;background:#fff;border:3px solid #fff;box-shadow:0 2px 8px rgba(0,0,0,.15);">
                    @elseif($currentAvatar)
                        <img src="{{ Storage::url($currentAvatar) }}"
                             style="width:4rem;height:4rem;border-radius:50%;object-fit:contain;background:#fff;border:3px solid #fff;box-shadow:0 2px 8px rgba(0,0,0,.15);">
                    @else
                        <div style="width:4rem;height:4rem;border-radius:50%;background:linear-gradient(135deg,#1d4ed8,#3b82f6);display:flex;align-items:center;justify-content:center;font-size:1.375rem;font-weight:800;color:#fff;border:3px solid #fff;box-shadow:0 2px 8px rgba(0,0,0,.15);">
                            {{ strtoupper(substr($first_name,0,1)) }}{{ strtoupper(substr($last_name,0,1)) }}
                        </div>
                    @endif
                </div>

                {{-- Info --}}
                <div style="padding:.75rem 1.125rem 1.125rem;">
                    <p style="font-size:1rem;font-weight:800;color:#0f172a;margin:0;line-height:1.25;">
                        {{ $displayName ?: '(nama belum diisi)' }}
                    </p>
                    @if($position || $department)
                    <p style="font-size:.8rem;color:#1d4ed8;font-weight:600;margin:.2rem 0 0;">
                        {{ implode(', ', array_filter([$position, $department])) }}
                    </p>
                    @endif
                    @if($affiliation)
                    <p style="font-size:.78rem;color:#64748b;margin:.125rem 0 0;">{{ Str::limit($affiliation, 40) }}</p>
                    @endif

                    {{-- Expertise tags --}}
                    @if(!empty($expertise_areas))
                    <div style="display:flex;flex-wrap:wrap;gap:.3rem;margin-top:.75rem;">
                        @foreach(array_slice($expertise_areas, 0, 5) as $tag)
                        <span style="background:#eff6ff;color:#1e40af;border:1px solid #bfdbfe;border-radius:9999px;padding:.15rem .55rem;font-size:.7rem;font-weight:600;">{{ $tag }}</span>
                        @endforeach
                        @if(count($expertise_areas) > 5)
                        <span style="background:#f1f5f9;color:#94a3b8;border:1px solid #e2e8f0;border-radius:9999px;padding:.15rem .55rem;font-size:.7rem;">+{{ count($expertise_areas)-5 }} lagi</span>
                        @endif
                    </div>
                    @endif

                    {{-- Academic IDs --}}
                    @php
                    $cardIds = array_filter([
                        $orcid          ? ['ORCID',   $orcid,         '#a3c13d'] : null,
                        $google_scholar ? ['Scholar', 'Lihat profil', '#4285f4'] : null,
                        $scopus_id      ? ['Scopus',  $scopus_id,     '#e87722'] : null,
                        $sinta_id       ? ['SINTA',   $sinta_id,      '#dc2626'] : null,
                        $researchgate   ? ['RG',      'Lihat profil', '#00ccbb'] : null,
                    ]);
                    @endphp
                    @if(!empty($cardIds))
                    <div style="margin-top:.75rem;padding-top:.75rem;border-top:1px solid #f1f5f9;display:flex;flex-direction:column;gap:.375rem;">
                        @foreach($cardIds as $vid)
                        <div style="display:flex;align-items:center;gap:.5rem;">
                            <span style="font-size:.65rem;font-weight:700;color:{{ $vid[2] }};background:{{ $vid[2] }}15;border:1px solid {{ $vid[2] }}30;border-radius:.3rem;padding:.1rem .4rem;min-width:3rem;text-align:center;flex-shrink:0;">{{ $vid[0] }}</span>
                            <span style="font-size:.75rem;color:#475569;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $vid[1] }}</span>
                        </div>
                        @endforeach
                    </div>
                    @endif

                    {{-- Metrics --}}
                    @if($h_index || $total_citations)
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:.5rem;margin-top:.75rem;padding-top:.75rem;border-top:1px solid #f1f5f9;">
                        @if($h_index !== null)
                        <div style="text-align:center;background:#f8fafc;border:1px solid #e2e8f0;border-radius:.625rem;padding:.625rem .5rem;">
                            <div style="font-size:1.375rem;font-weight:900;color:#0f172a;line-height:1;">{{ $h_index }}</div>
                            <div style="font-size:.65rem;font-weight:600;color:#94a3b8;margin-top:.2rem;text-transform:uppercase;letter-spacing:.05em;">H-Index</div>
                        </div>
                        @endif
                        @if($total_citations)
                        <div style="text-align:center;background:#f8fafc;border:1px solid #e2e8f0;border-radius:.625rem;padding:.625rem .5rem;">
                            <div style="font-size:1.375rem;font-weight:900;color:#0f172a;line-height:1;">{{ number_format($total_citations) }}</div>
                            <div style="font-size:.65rem;font-weight:600;color:#94a3b8;margin-top:.2rem;text-transform:uppercase;letter-spacing:.05em;">Sitasi</div>
                        </div>
                        @endif
                    </div>
                    @endif
                </div>
            </div>

            {{-- ── Kelengkapan Profil ─────────────────────────────────────────── --}}
            @php
            $fields  = ['first_name','affiliation','orcid','google_scholar','sinta_id','bio','expertise_areas','position'];
            $filled  = 0;
            foreach($fields as $f) {
                $val = is_array(${$f}) ? !empty(${$f}) : !empty(trim(${$f} ?? ''));
                if($val) $filled++;
            }
            $pct = round($filled / count($fields) * 100);
            @endphp
            <div style="background:#fff;border-radius:1rem;border:1px solid #e2e8f0;padding:1.125rem;">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:.625rem;">
                    <p style="font-size:.8125rem;font-weight:700;color:#0f172a;margin:0;">Kelengkapan Profil</p>
                    <span style="font-size:.875rem;font-weight:800;color:{{ $pct >= 80 ? '#1d4ed8' : ($pct >= 50 ? '#d97706' : '#dc2626') }};">{{ $pct }}%</span>
                </div>
                <div style="height:.5rem;background:#f1f5f9;border-radius:9999px;overflow:hidden;">
                    <div style="height:100%;width:{{ $pct }}%;background:{{ $pct >= 80 ? 'linear-gradient(90deg,#1d4ed8,#60a5fa)' : ($pct >= 50 ? '#d97706' : '#dc2626') }};border-radius:9999px;transition:width .3s;"></div>
                </div>
                @if($pct < 100)
                <ul style="margin:.75rem 0 0;padding-left:1.125rem;font-size:.78rem;color:#64748b;line-height:1.8;">
                    @if(empty(trim($affiliation)))    <li>Tambahkan afiliasi institusi</li> @endif
                    @if(empty(trim($orcid)))           <li>Isi ORCID iD</li> @endif
                    @if(empty(trim($google_scholar)))  <li>Tambahkan Google Scholar</li> @endif
                    @if(empty(trim($sinta_id)))        <li>Isi SINTA ID</li> @endif
                    @if(empty($expertise_areas))      <li>Tambahkan topik penelitian</li> @endif
                    @if(empty(trim($bio)))             <li>Tulis bio akademik</li> @endif
                    @if(empty(trim($position)))        <li>Isi jabatan / posisi</li> @endif
                </ul>
                @else
                <p style="font-size:.78rem;color:#1d4ed8;font-weight:600;margin:.625rem 0 0;">Profil sudah lengkap!</p>
                @endif
            </div>

            {{-- ── Tips ─────────────────────────────────────────────────────── --}}
            <div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:1rem;padding:1rem;">
                <p style="font-size:.8125rem;font-weight:700;color:#1e40af;margin:0 0 .5rem;display:flex;align-items:center;gap:.375rem;">
                    <svg style="width:.875rem;height:.875rem;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                    Mengapa profil lengkap penting?
                </p>
                <ul style="margin:0;padding-left:1rem;font-size:.78rem;color:#1e40af;line-height:1.7;">
                    <li>ORCID & DOI memperkuat reputasi publikasi</li>
                    <li>SINTA ID diperlukan untuk penilaian BKD/PAK</li>
                    <li>Scopus & Scholar memperlihatkan dampak riset</li>
                    <li>Bio lengkap mempercepat proses review naskah</li>
                </ul>
            </div>
        </div>

    </div>
    </form>
</div>

<style>
@keyframes spin { to { transform:rotate(360deg); } }
@media (max-width:900px) { .profil-grid { grid-template-columns:1fr !important; } }
</style>

</div>
