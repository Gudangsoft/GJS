<?php
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
$qrUrl  = route('loa.verify', $loa->verification_code);
$qrOpts = new QROptions([
    'outputType'  => QRCode::OUTPUT_MARKUP_SVG,
    'eccLevel'    => QRCode::ECC_H,
    'imageBase64' => false,
    'svgDefs'     => '',
    'markupDark'  => '#1e3a5f',
    'markupLight' => '#ffffff',
    'scale'       => 4,
]);
$qrSvg = (new QRCode($qrOpts))->render($qrUrl);
$wmText = strtoupper($loa->journal->name_abbrev ?: Str::limit($loa->journal->name, 20, ''));
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>LOA — {{ $loa->loa_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Times New Roman', serif; font-size: 12pt; color: #111; background: #f0f2f5; }

        .page {
            width: 210mm;
            min-height: 297mm;
            margin: 20px auto;
            background: #fff;
            padding: 22mm 20mm 20mm;
            box-shadow: 0 4px 32px rgba(0,0,0,.14);
            position: relative;
            overflow: hidden;
        }

        /* ── WATERMARK ── */
        .watermark {
            position: absolute;
            top: 50%; left: 50%;
            transform: translate(-50%, -50%) rotate(-42deg);
            font-size: 56pt;
            font-weight: 900;
            color: rgba(30, 58, 95, 0.048);
            letter-spacing: 6px;
            text-transform: uppercase;
            pointer-events: none;
            user-select: none;
            white-space: nowrap;
            font-family: Arial, sans-serif;
            z-index: 0;
        }
        .watermark-2 {
            position: absolute;
            top: 25%; left: 50%;
            transform: translate(-50%, -50%) rotate(-42deg);
            font-size: 38pt;
            font-weight: 900;
            color: rgba(30, 58, 95, 0.030);
            letter-spacing: 5px;
            text-transform: uppercase;
            pointer-events: none;
            user-select: none;
            white-space: nowrap;
            font-family: Arial, sans-serif;
            z-index: 0;
        }
        .watermark-3 {
            position: absolute;
            top: 75%; left: 50%;
            transform: translate(-50%, -50%) rotate(-42deg);
            font-size: 38pt;
            font-weight: 900;
            color: rgba(30, 58, 95, 0.030);
            letter-spacing: 5px;
            text-transform: uppercase;
            pointer-events: none;
            user-select: none;
            white-space: nowrap;
            font-family: Arial, sans-serif;
            z-index: 0;
        }
        .watermark-pattern {
            position: absolute; inset: 0; z-index: 0; pointer-events: none;
            background-image: repeating-linear-gradient(
                -45deg, transparent, transparent 70px,
                rgba(30,58,95,.012) 70px, rgba(30,58,95,.012) 71px
            );
        }
        .page > *:not(.watermark):not(.watermark-2):not(.watermark-3):not(.watermark-pattern) {
            position: relative; z-index: 1;
        }

        /* ── KOP SURAT ── */
        .header {
            display: flex; align-items: center; gap: 16px;
            border-bottom: 3px solid #1e3a5f;
            padding-bottom: 12px; margin-bottom: 22px;
        }
        .header-logo { width: 60px; height: 60px; object-fit: contain; flex-shrink: 0; }
        .header-logo-placeholder {
            width: 60px; height: 60px; background: #1e3a5f; border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-size: 16pt; font-weight: bold; flex-shrink: 0;
        }
        .header-text h1 { font-size: 13.5pt; font-weight: bold; color: #1e3a5f; line-height: 1.3; }
        .header-text p  { font-size: 9pt; color: #555; line-height: 1.6; }

        /* ── JUDUL LOA ── */
        .loa-meta { text-align: center; margin: 18px 0 14px; }
        .loa-meta .number { font-size: 10pt; color: #666; letter-spacing: .5px; }
        .loa-meta h2 { font-size: 15pt; font-weight: bold; color: #1e3a5f; letter-spacing: 2px; margin: 4px 0 0; text-transform: uppercase; }
        .divider { height: 2px; background: linear-gradient(to right, #1e3a5f, #60a5fa, #1e3a5f); margin: 12px 0 18px; }

        /* ── BODY ── */
        .body-text { font-size: 11.5pt; line-height: 1.9; text-align: justify; margin-bottom: 12px; }

        .article-box {
            border: 1.5px solid #1e3a5f; border-radius: 6px;
            padding: 13px 16px; margin: 16px 0; background: #f0f5ff;
        }
        .article-box table { width: 100%; border-collapse: collapse; }
        .article-box td { padding: 3.5px 0; font-size: 11pt; vertical-align: top; }
        .article-box td:first-child { width: 155px; font-weight: bold; color: #1e3a5f; }
        .article-box td:nth-child(2) { width: 14px; }
        .status-badge {
            display: inline-block; padding: 2px 12px; border-radius: 20px;
            font-size: 9pt; font-weight: bold; text-transform: uppercase;
            background: #dcfce7; color: #166534; border: 1px solid #86efac;
        }

        /* ── SIGNATURE ── */
        .signature-section {
            margin-top: 32px;
            display: flex; justify-content: space-between; align-items: flex-end;
        }
        .sig-left { font-size: 10pt; color: #555; line-height: 1.8; max-width: 50%; }
        .sig-right { text-align: center; min-width: 42%; }
        .sig-right .city-date { font-size: 10.5pt; color: #333; margin-bottom: 10px; }
        .sig-right .qr-seal { display: inline-block; margin-bottom: 6px; }
        .sig-right .qr-seal svg { width: 72px; height: 72px; }
        .sig-right .signer-name {
            font-weight: bold; font-size: 11pt;
            border-top: 1.5px solid #333; padding-top: 5px;
            display: inline-block; min-width: 180px; margin-top: 4px;
        }
        .sig-right .signer-role { font-size: 10pt; color: #555; margin-top: 2px; }

        @media print {
            body { background: #fff; }
            .page { margin: 0; box-shadow: none; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>

<div class="no-print" style="text-align:center;padding:14px 20px;background:#1e3a5f;display:flex;align-items:center;justify-content:center;gap:16px;">
    <button onclick="window.print()" style="background:#2563eb;color:#fff;border:none;padding:9px 24px;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;display:flex;align-items:center;gap:6px;">
        <span style="font-size:15px;">🖨️</span> Cetak / Simpan PDF
    </button>
    <a href="{{ $loa->verifyUrl() }}" target="_blank" style="color:#93c5fd;font-size:13px;font-weight:500;display:flex;align-items:center;gap:4px;">
        <span>🔍</span> Verifikasi Keaslian
    </a>
    <a href="{{ url()->previous() }}" style="color:#93c5fd;font-size:13px;">← Kembali</a>
</div>

<div class="page">

    <div class="watermark">{{ $wmText }}</div>
    <div class="watermark-2">{{ $wmText }}</div>
    <div class="watermark-3">{{ $wmText }}</div>
    <div class="watermark-pattern"></div>

    {{-- KOP SURAT --}}
    <div class="header">
        @if($loa->journal->logo)
        <img src="{{ Storage::disk('public')->url($loa->journal->logo) }}" class="header-logo" alt="">
        @else
        <div class="header-logo-placeholder">{{ strtoupper(substr($loa->journal->name_abbrev ?: $loa->journal->name, 0, 2)) }}</div>
        @endif
        <div class="header-text">
            <h1>{{ $loa->journal->name }}</h1>
            <p>
                @if($loa->journal->issn_print) ISSN (Cetak): {{ $loa->journal->issn_print }}&nbsp;&nbsp;@endif
                @if($loa->journal->issn_online) e-ISSN: {{ $loa->journal->issn_online }}&nbsp;&nbsp;@endif
                @if($loa->journal->publisher) Penerbit: {{ $loa->journal->publisher }} @endif
            </p>
            @if($loa->journal->url)<p style="color:#2563eb;">{{ $loa->journal->url }}</p>@endif
        </div>
    </div>

    {{-- NOMOR & JUDUL LOA --}}
    <div class="loa-meta">
        <div class="number">Nomor: {{ $loa->loa_number }}</div>
        <h2>Letter of Acceptance</h2>
    </div>
    <div class="divider"></div>

    <p class="body-text">
        Yang bertanda tangan di bawah ini, Editor-in-Chief / Pengelola Jurnal
        <strong>{{ $loa->journal->name }} ({{ $loa->journal->name_abbrev }})</strong>,
        dengan ini menyatakan bahwa naskah ilmiah berikut telah diterima untuk
        diterbitkan pada jurnal tersebut:
    </p>

    <div class="article-box">
        <table>
            <tr>
                <td>Judul Artikel</td><td>:</td>
                <td><strong>{{ $loa->article_title }}</strong></td>
            </tr>
            <tr>
                <td style="vertical-align:top;">Penulis</td><td style="vertical-align:top;">:</td>
                <td>
                    @php
                        $authorsList  = is_array($loa->authors) ? $loa->authors : [];
                        $hasAffiliated = collect($authorsList)->contains(
                            fn($a) => is_array($a) && !empty(trim($a['affiliation'] ?? ''))
                        );
                    @endphp
                    @if($hasAffiliated)
                        <table style="width:100%;border-collapse:collapse;margin:0;">
                            @foreach($authorsList as $idx => $author)
                            @php
                                $aName  = is_array($author) ? ($author['name'] ?? '') : (string)$author;
                                $aAffil = is_array($author) ? ($author['affiliation'] ?? '') : '';
                            @endphp
                            <tr>
                                <td style="padding:2px 0;width:20px;color:#888;font-size:10pt;vertical-align:top;">{{ $idx+1 }}.</td>
                                <td style="padding:2px 4px;vertical-align:top;">
                                    <span style="font-weight:600;">{{ $aName }}</span>
                                    @if($aAffil)
                                    <br><span style="font-size:10pt;color:#555;font-style:italic;">{{ $aAffil }}</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </table>
                    @else
                        {{ collect($authorsList)->map(fn($a) => is_array($a) ? ($a['name'] ?? '') : (string)$a)->filter()->implode('; ') }}
                    @endif
                </td>
            </tr>
            @if($loa->volume || $loa->number || $loa->year)
            <tr>
                <td>Rencana Terbitan</td><td>:</td>
                <td>
                    @if($loa->volume) Vol. {{ $loa->volume }} @endif
                    @if($loa->number) No. {{ $loa->number }} @endif
                    @if($loa->year) ({{ $loa->year }}) @endif
                </td>
            </tr>
            @endif
            @if($loa->expected_publication_date)
            <tr>
                <td>Estimasi Terbit</td><td>:</td>
                <td>{{ $loa->expected_publication_date->translatedFormat('d F Y') }}</td>
            </tr>
            @endif
            <tr>
                <td>Status Naskah</td><td>:</td>
                <td><span class="status-badge">&#10003; Diterima (Accepted)</span></td>
            </tr>
        </table>
    </div>

    <p class="body-text">
        Surat keterangan penerimaan naskah (Letter of Acceptance) ini diterbitkan
        atas permintaan penulis dan dapat digunakan sebagai bukti bahwa naskah
        tersebut telah melalui proses penelaahan sejawat (peer review) dan dinyatakan
        layak untuk dipublikasikan.
    </p>

    @if($loa->notes)
    <p class="body-text" style="font-style:italic;color:#444;font-size:10.5pt;">
        <strong>Catatan:</strong> {{ $loa->notes }}
    </p>
    @endif

    {{-- TANDA TANGAN --}}
    <div class="signature-section">
        <div class="sig-left">
            Surat ini diterbitkan pada:<br>
            <strong>{{ $loa->acceptance_date->translatedFormat('d F Y') }}</strong>
        </div>
        <div class="sig-right">
            <p class="city-date">
                {{ $loa->journal->country ?? 'Indonesia' }},
                {{ $loa->acceptance_date->translatedFormat('d F Y') }}
            </p>
            {{-- QR sebagai stempel digital di atas nama --}}
            <div class="qr-seal">{!! $qrSvg !!}</div>
            <br>
            <span class="signer-name">
                {{ $loa->journal->loa_signer_name ?: trim(($loa->issuedBy->first_name ?? '').' '.($loa->issuedBy->last_name ?? '')) }}
            </span>
            <p class="signer-role">{{ $loa->journal->loa_signer_title ?: 'Editor-in-Chief / Pengelola Jurnal' }}</p>
        </div>
    </div>


</div>
</body>
</html>