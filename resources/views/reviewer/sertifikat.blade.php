<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sertifikat Reviewer — {{ $assignment->submission->journal->name }}</title>
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700,800&display=swap" rel="stylesheet">
    <style>
        *{box-sizing:border-box;margin:0;padding:0;}
        body{font-family:'Instrument Sans',sans-serif;background:#f1f5f9;color:#1e293b;}
        .topbar{background:#064e3b;color:#fff;padding:.875rem 1.5rem;display:flex;align-items:center;justify-content:space-between;print-color-adjust:exact;}
        .topbar-links{display:flex;gap:1rem;align-items:center;}
        .topbar-links a{color:#6ee7b7;font-size:.8125rem;text-decoration:none;display:flex;align-items:center;gap:.375rem;}
        .cert-outer{max-width:860px;margin:2rem auto;padding:1rem;}
        .cert-frame{background:#fff;border-radius:1rem;box-shadow:0 8px 40px rgba(0,0,0,.12);overflow:hidden;position:relative;}
        .cert-border{padding:2.5rem;background:#fff;}
        .cert-inner{border:3px solid #059669;border-radius:.75rem;padding:2.5rem 3rem;position:relative;}
        .corner{position:absolute;width:2.5rem;height:2.5rem;}
        .corner svg{width:100%;height:100%;}
        .corner-tl{top:.75rem;left:.75rem;}
        .corner-tr{top:.75rem;right:.75rem;transform:scaleX(-1);}
        .corner-bl{bottom:.75rem;left:.75rem;transform:scaleY(-1);}
        .corner-br{bottom:.75rem;right:.75rem;transform:scale(-1);}
        .cert-header{text-align:center;margin-bottom:2rem;}
        .cert-logo{width:4rem;height:4rem;background:linear-gradient(135deg,#059669,#047857);border-radius:1rem;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:800;font-size:1.25rem;margin:0 auto 1rem;}
        .cert-journal{font-size:1rem;font-weight:700;color:#059669;text-transform:uppercase;letter-spacing:.1em;}
        .cert-badge{display:inline-flex;align-items:center;gap:.5rem;background:#f0fdf4;border:1.5px solid #bbf7d0;border-radius:9999px;padding:.5rem 1.25rem;margin:.75rem auto 0;}
        .cert-badge-text{font-size:.75rem;font-weight:800;color:#059669;text-transform:uppercase;letter-spacing:.12em;}
        .divider{display:flex;align-items:center;gap:1rem;margin:1.5rem 0;}
        .divider-line{flex:1;height:1px;background:linear-gradient(to right,transparent,#bbf7d0,transparent);}
        .divider-star{color:#059669;font-size:1.25rem;}
        .cert-title{font-size:2.25rem;font-weight:800;color:#064e3b;text-align:center;letter-spacing:.02em;line-height:1.2;margin-bottom:.75rem;}
        .cert-subtitle{font-size:.9375rem;color:#475569;text-align:center;margin-bottom:2rem;}
        .cert-body{text-align:center;margin:2rem 0;}
        .cert-body p{font-size:1rem;color:#374151;line-height:1.8;margin-bottom:.75rem;}
        .cert-name{font-size:2rem;font-weight:800;color:#064e3b;font-style:italic;border-bottom:2px solid #059669;display:inline-block;padding-bottom:.375rem;margin:.25rem 0 1.25rem;}
        .cert-detail-grid{display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin:2rem 0;}
        .cert-detail-item{background:#f0fdf4;border:1px solid #bbf7d0;border-radius:.75rem;padding:1rem;text-align:center;}
        .cert-detail-label{font-size:.7rem;font-weight:800;text-transform:uppercase;letter-spacing:.1em;color:#059669;margin-bottom:.375rem;}
        .cert-detail-value{font-size:.9375rem;font-weight:700;color:#0f172a;line-height:1.4;}
        .rec-box{display:inline-flex;align-items:center;gap:.625rem;background:linear-gradient(135deg,#059669,#047857);color:#fff;border-radius:.875rem;padding:.875rem 2rem;margin:1rem auto;font-size:1.0625rem;font-weight:800;letter-spacing:.03em;}
        .cert-footer{border-top:2px solid #e2e8f0;margin-top:2.5rem;padding-top:1.75rem;display:grid;grid-template-columns:1fr auto 1fr;gap:2rem;align-items:end;}
        .sig-left{text-align:left;}
        .sig-right{text-align:right;}
        .sig-center{text-align:center;}
        .sig-line{border-top:1.5px solid #cbd5e1;padding-top:.625rem;margin-top:3rem;}
        .sig-name{font-size:.9375rem;font-weight:800;color:#0f172a;}
        .sig-role{font-size:.8rem;color:#64748b;}
        .cert-no{text-align:center;font-size:.75rem;color:#94a3b8;margin-top:1.25rem;}
        .watermark{position:absolute;top:50%;left:50%;transform:translate(-50%,-50%) rotate(-30deg);font-size:5rem;font-weight:900;color:rgba(5,150,105,.04);white-space:nowrap;pointer-events:none;z-index:0;letter-spacing:.1em;text-transform:uppercase;}
        .content{position:relative;z-index:1;}
        @media print{
            body{background:#fff;}
            .topbar,.print-btn{display:none!important;}
            .cert-outer{margin:0;padding:0;max-width:none;}
            .cert-frame{box-shadow:none;border-radius:0;}
            .cert-border{padding:1.5rem;}
        }
    </style>
</head>
<body>

{{-- Topbar --}}
<div class="topbar">
    <div class="topbar-links">
        <a href="{{ route('reviewer.dashboard') }}">
            <svg style="width:.875rem;height:.875rem;" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
            Dashboard
        </a>
        <a href="{{ route('reviewer.review', $assignment) }}">
            <svg style="width:.875rem;height:.875rem;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Lihat Review
        </a>
    </div>
    <button onclick="window.print()" class="print-btn"
            style="background:rgba(255,255,255,.15);color:#fff;border:1px solid rgba(255,255,255,.25);padding:.4rem 1rem;border-radius:.5rem;font-size:.8125rem;font-weight:700;cursor:pointer;display:flex;align-items:center;gap:.375rem;">
        <svg style="width:.875rem;height:.875rem;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5zm-3 0h.008v.008H15V10.5z"/></svg>
        Cetak / Simpan PDF
    </button>
</div>

<div class="cert-outer">
<div class="cert-frame">
<div class="cert-border">
<div class="cert-inner">

    {{-- Watermark --}}
    <div class="watermark">Reviewer</div>

    {{-- Corner decorations --}}
    <div class="corner corner-tl">
        <svg viewBox="0 0 40 40" fill="none"><path d="M2 38 V2 H38" stroke="#059669" stroke-width="2.5" stroke-linecap="round"/><circle cx="2" cy="2" r="3" fill="#059669"/></svg>
    </div>
    <div class="corner corner-tr">
        <svg viewBox="0 0 40 40" fill="none"><path d="M2 38 V2 H38" stroke="#059669" stroke-width="2.5" stroke-linecap="round"/><circle cx="2" cy="2" r="3" fill="#059669"/></svg>
    </div>
    <div class="corner corner-bl">
        <svg viewBox="0 0 40 40" fill="none"><path d="M2 38 V2 H38" stroke="#059669" stroke-width="2.5" stroke-linecap="round"/><circle cx="2" cy="2" r="3" fill="#059669"/></svg>
    </div>
    <div class="corner corner-br">
        <svg viewBox="0 0 40 40" fill="none"><path d="M2 38 V2 H38" stroke="#059669" stroke-width="2.5" stroke-linecap="round"/><circle cx="2" cy="2" r="3" fill="#059669"/></svg>
    </div>

    <div class="content">
        {{-- Header --}}
        <div class="cert-header">
            <div class="cert-logo">{{ strtoupper(substr($assignment->submission->journal->name, 0, 2)) }}</div>
            <p class="cert-journal">{{ $assignment->submission->journal->name }}</p>
            <div class="cert-badge" style="display:flex;">
                <svg style="width:1rem;height:1rem;color:#059669;" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                <span class="cert-badge-text">Sertifikat Resmi Reviewer</span>
            </div>
        </div>

        <div class="divider"><div class="divider-line"></div><span class="divider-star">✦</span><div class="divider-line"></div></div>

        <h1 class="cert-title">SERTIFIKAT<br>REVIEWER</h1>
        <p class="cert-subtitle">Diberikan kepada yang tersebut di bawah ini</p>

        {{-- Nama Reviewer --}}
        <div class="cert-body">
            <p>Dengan bangga kami menyatakan bahwa</p>
            <p class="cert-name">{{ $assignment->reviewer->first_name }} {{ $assignment->reviewer->last_name }}</p>
            @if($assignment->reviewer->affiliation)
            <p style="color:#64748b;font-size:.9375rem;">{{ $assignment->reviewer->affiliation }}</p>
            @endif
            <p style="margin-top:1.25rem;">telah menyelesaikan tugas sebagai <strong>Peer Reviewer</strong> untuk naskah berikut:</p>

            <div style="background:#f8fafc;border:1.5px solid #e2e8f0;border-radius:.875rem;padding:1.25rem 1.5rem;margin:1.25rem auto;max-width:500px;text-align:left;">
                <p style="font-size:.7rem;font-weight:800;text-transform:uppercase;letter-spacing:.08em;color:#94a3b8;margin-bottom:.5rem;">Judul Naskah</p>
                <p style="font-size:1rem;font-weight:700;color:#0f172a;line-height:1.5;">{{ $assignment->submission->title }}</p>
                @if($assignment->submission->section)
                <p style="font-size:.8rem;color:#64748b;margin-top:.375rem;">Seksi: {{ $assignment->submission->section->title }}</p>
                @endif
            </div>
        </div>

        {{-- Detail Grid --}}
        <div class="cert-detail-grid">
            <div class="cert-detail-item">
                <p class="cert-detail-label">Tanggal Penugasan</p>
                <p class="cert-detail-value">{{ $assignment->date_assigned?->translatedFormat('d F Y') ?? '—' }}</p>
            </div>
            <div class="cert-detail-item">
                <p class="cert-detail-label">Tanggal Selesai</p>
                <p class="cert-detail-value">{{ $assignment->date_completed?->translatedFormat('d F Y') ?? now()->translatedFormat('d F Y') }}</p>
            </div>
            <div class="cert-detail-item">
                <p class="cert-detail-label">Metode Review</p>
                @php $mMap=['double_blind'=>'Double Blind','single_blind'=>'Single Blind','triple_blind'=>'Triple Blind','open'=>'Open Review']; @endphp
                <p class="cert-detail-value">{{ $mMap[$assignment->review_method] ?? 'Peer Review' }}</p>
            </div>
            <div class="cert-detail-item">
                <p class="cert-detail-label">Putaran Review</p>
                <p class="cert-detail-value">Putaran {{ $assignment->round ?? 1 }}</p>
            </div>
        </div>

        {{-- Rekomendasi --}}
        @if($assignment->review?->recommendation)
        @php
            $recLabels = ['accept'=>'Terima','pending_revisions'=>'Revisi Minor','resubmit_here'=>'Revisi Mayor','resubmit_elsewhere'=>'Submit Jurnal Lain','decline'=>'Tolak','see_comments'=>'Lihat Komentar'];
        @endphp
        <div style="text-align:center;margin:1rem 0;">
            <p style="font-size:.8rem;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.08em;margin-bottom:.625rem;">Rekomendasi yang Diberikan</p>
            <div class="rec-box" style="display:inline-flex;">
                <svg style="width:1.125rem;height:1.125rem;" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                {{ $recLabels[$assignment->review->recommendation] ?? $assignment->review->recommendation }}
            </div>
        </div>
        @endif

        <div class="divider"><div class="divider-line"></div><span class="divider-star">✦</span><div class="divider-line"></div></div>

        {{-- Tanda Tangan --}}
        <div class="cert-footer">
            <div class="sig-left">
                <p style="font-size:.8rem;color:#64748b;margin-bottom:3rem;">Reviewer</p>
                <div class="sig-line">
                    <p class="sig-name">{{ $assignment->reviewer->first_name }} {{ $assignment->reviewer->last_name }}</p>
                    <p class="sig-role">Reviewer</p>
                </div>
            </div>
            <div class="sig-center">
                <div style="background:linear-gradient(135deg,#059669,#047857);border-radius:50%;width:3rem;height:3rem;display:flex;align-items:center;justify-content:center;margin:0 auto;">
                    <svg style="width:1.5rem;height:1.5rem;color:#fff;" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                </div>
            </div>
            <div class="sig-right">
                <p style="font-size:.8rem;color:#64748b;margin-bottom:3rem;text-align:right;">Diterbitkan oleh</p>
                <div class="sig-line" style="text-align:right;">
                    <p class="sig-name">Editor-in-Chief</p>
                    <p class="sig-role">{{ $assignment->submission->journal->name }}</p>
                </div>
            </div>
        </div>

        <div class="cert-no">
            <p>No. CERT-REV-{{ str_pad($assignment->id, 6, '0', STR_PAD_LEFT) }} · {{ now()->translatedFormat('d F Y') }}</p>
            <p style="margin-top:.25rem;">Dokumen ini diterbitkan secara digital oleh sistem {{ config('app.name') }}</p>
        </div>
    </div>
</div>
</div>
</div>
</div>

</body>
</html>
