<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verifikasi LOA</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #f1f5f9; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; }
        .card { background: #fff; border-radius: 20px; box-shadow: 0 10px 40px rgba(0,0,0,.12); max-width: 560px; width: 100%; overflow: hidden; }
        .card-header { padding: 32px; text-align: center; }
        .card-body { padding: 0 32px 32px; }
        .badge { display: inline-flex; align-items: center; gap: 8px; padding: 8px 20px; border-radius: 100px; font-size: 14px; font-weight: 700; letter-spacing: .3px; }
        .badge-valid   { background: #dcfce7; color: #166534; }
        .badge-invalid { background: #fee2e2; color: #991b1b; }
        .icon-valid   { width: 80px; height: 80px; background: #dcfce7; border-radius: 50%; margin: 0 auto 16px; display: flex; align-items: center; justify-content: center; }
        .icon-invalid { width: 80px; height: 80px; background: #fee2e2; border-radius: 50%; margin: 0 auto 16px; display: flex; align-items: center; justify-content: center; }
        .info-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .info-table td { padding: 10px 12px; font-size: 14px; border-bottom: 1px solid #f1f5f9; }
        .info-table td:first-child { font-weight: 600; color: #64748b; width: 140px; }
        .info-table td:last-child { color: #0f172a; }
        .code-box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 12px 16px; text-align: center; font-family: monospace; font-size: 13px; color: #475569; letter-spacing: 1px; margin-top: 20px; }
        .warning { background: #fefce8; border: 1px solid #fef08a; border-radius: 12px; padding: 14px 16px; font-size: 13px; color: #713f12; margin-top: 20px; }
        .btn { display: inline-block; padding: 10px 24px; border-radius: 10px; background: #1e3a5f; color: #fff; text-decoration: none; font-size: 14px; font-weight: 600; margin-top: 24px; }
    </style>
</head>
<body>

<div class="card">
    @if($loa)
    {{-- VALID --}}
    <div class="card-header" style="background: linear-gradient(135deg, #0c1a3a, #1a3272);">
        <div class="icon-valid">
            <svg width="36" height="36" fill="none" stroke="#16a34a" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
        </div>
        <span class="badge badge-valid">✓ LOA Terverifikasi & Asli</span>
        <p style="color:#94a3b8;font-size:13px;margin-top:10px;">Dokumen ini terdaftar dan valid dalam sistem kami</p>
    </div>
    <div class="card-body">
        <table class="info-table">
            <tr>
                <td>Nomor LOA</td>
                <td><strong>{{ $loa->loa_number }}</strong></td>
            </tr>
            <tr>
                <td>Judul Artikel</td>
                <td>{{ $loa->article_title }}</td>
            </tr>
            <tr>
                <td>Penulis</td>
                <td>{{ collect($loa->authors ?? [])->map(fn($a) => is_array($a) ? ($a['name'] ?? '') : (string)$a)->filter()->implode(', ') }}</td>
            </tr>
            <tr>
                <td>Jurnal</td>
                <td>{{ $loa->journal->name }} ({{ $loa->journal->name_abbrev }})</td>
            </tr>
            <tr>
                <td>Tanggal Diterbitkan</td>
                <td>{{ $loa->acceptance_date?->format('d F Y') }}</td>
            </tr>
            <tr>
                <td>Status</td>
                <td><span style="background:#dcfce7;color:#166534;padding:2px 10px;border-radius:20px;font-size:12px;font-weight:700;">Diterima (Accepted)</span></td>
            </tr>
        </table>
        <div class="code-box">Kode Verifikasi: {{ $loa->verification_code }}</div>
        <div class="warning">
            <strong>⚠ Catatan:</strong> Verifikasi ini hanya membuktikan bahwa dokumen LOA terdaftar dalam sistem. Keaslian fisik dokumen tetap perlu diverifikasi melalui jurnal terkait.
        </div>
        <div style="text-align:center;">
            <a href="{{ route('loa.preview', $loa) }}" class="btn">Lihat Dokumen LOA →</a>
        </div>
    </div>

    @else
    {{-- INVALID --}}
    <div class="card-header" style="background: #fef2f2;">
        <div class="icon-invalid">
            <svg width="36" height="36" fill="none" stroke="#dc2626" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
        </div>
        <span class="badge badge-invalid">✗ LOA Tidak Ditemukan</span>
        <p style="color:#6b7280;font-size:13px;margin-top:10px;">Kode verifikasi tidak terdaftar dalam sistem kami</p>
    </div>
    <div class="card-body">
        <div class="code-box">Kode: {{ $code }}</div>
        <div class="warning" style="background:#fee2e2;border-color:#fca5a5;color:#7f1d1d;">
            <strong>⚠ Peringatan:</strong> Dokumen ini <strong>tidak dapat diverifikasi</strong>. Kemungkinan dokumen palsu atau kode verifikasi tidak valid. Hubungi pengelola jurnal untuk konfirmasi.
        </div>
    </div>
    @endif
</div>

</body>
</html>