<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Surat Tugas Reviewer — {{ $assignment->submission->journal->name }}</title>
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700,800&display=swap" rel="stylesheet">
    <style>
        *{box-sizing:border-box;margin:0;padding:0;}
        body{font-family:'Instrument Sans',sans-serif;background:#f1f5f9;color:#1e293b;}
        .page{max-width:800px;margin:2rem auto;background:#fff;border-radius:1rem;box-shadow:0 4px 24px rgba(0,0,0,.08);overflow:hidden;}
        .topbar{background:#064e3b;color:#fff;padding:.875rem 1.5rem;display:flex;align-items:center;justify-content:space-between;}
        .topbar-links{display:flex;gap:1rem;align-items:center;}
        .topbar-links a{color:#6ee7b7;font-size:.8125rem;text-decoration:none;display:flex;align-items:center;gap:.375rem;}
        .doc-wrap{padding:3rem 3.5rem;}
        .header{border-bottom:3px solid #059669;padding-bottom:1.5rem;margin-bottom:2rem;display:flex;align-items:flex-start;justify-content:space-between;gap:1rem;}
        .journal-logo{width:3rem;height:3rem;background:#059669;border-radius:.75rem;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:800;font-size:1rem;flex-shrink:0;}
        .journal-name{font-size:1.125rem;font-weight:800;color:#0f172a;line-height:1.3;}
        .journal-meta{font-size:.8rem;color:#64748b;margin-top:.25rem;}
        .doc-number{font-size:.8rem;color:#94a3b8;text-align:right;line-height:1.8;}
        .title-block{text-align:center;margin-bottom:2.5rem;}
        .title-block h1{font-size:1.375rem;font-weight:800;color:#064e3b;letter-spacing:.03em;text-transform:uppercase;border-bottom:2px solid #059669;padding-bottom:.625rem;display:inline-block;}
        .body-text{font-size:.9375rem;color:#374151;line-height:1.9;margin-bottom:1.25rem;}
        .body-text strong{color:#0f172a;}
        .detail-box{background:#f0fdf4;border:1px solid #bbf7d0;border-radius:.875rem;padding:1.25rem 1.5rem;margin:1.5rem 0;display:flex;flex-direction:column;gap:.75rem;}
        .detail-row{display:grid;grid-template-columns:180px 1fr;gap:.5rem;align-items:baseline;}
        .detail-label{font-size:.8125rem;font-weight:700;color:#065f46;text-transform:uppercase;letter-spacing:.04em;}
        .detail-value{font-size:.9375rem;color:#0f172a;font-weight:500;}
        .badge{display:inline-flex;align-items:center;font-size:.75rem;font-weight:800;padding:.25rem .625rem;border-radius:.375rem;background:#059669;color:#fff;}
        .obligations{margin:1.5rem 0;padding:1.25rem 1.5rem;background:#fffbeb;border:1px solid #fde68a;border-radius:.875rem;}
        .obligations h3{font-size:.875rem;font-weight:800;color:#92400e;text-transform:uppercase;letter-spacing:.05em;margin-bottom:.875rem;}
        .obligations ol{padding-left:1.25rem;display:flex;flex-direction:column;gap:.5rem;}
        .obligations li{font-size:.875rem;color:#78350f;line-height:1.6;}
        .signature{margin-top:3rem;display:grid;grid-template-columns:1fr 1fr;gap:2rem;}
        .sig-block{text-align:center;}
        .sig-label{font-size:.8rem;color:#64748b;margin-bottom:.375rem;}
        .sig-line{border-top:1.5px solid #cbd5e1;padding-top:.625rem;margin-top:3rem;}
        .sig-name{font-size:.9rem;font-weight:700;color:#0f172a;}
        .sig-role{font-size:.8rem;color:#64748b;}
        .footer-doc{border-top:2px solid #e2e8f0;margin-top:2.5rem;padding-top:1rem;text-align:center;font-size:.75rem;color:#94a3b8;}
        .method-badge{font-size:.8rem;font-weight:700;padding:.25rem .75rem;border-radius:.375rem;background:#eff6ff;color:#1e40af;border:1px solid #bfdbfe;}
        @media print{
            body{background:#fff;}
            .page{box-shadow:none;border-radius:0;margin:0;max-width:none;}
            .topbar,.print-btn{display:none!important;}
            .doc-wrap{padding:2rem 2.5rem;}
        }
    </style>
</head>
<body>

{{-- Topbar navigasi --}}
<div class="topbar">
    <div class="topbar-links">
        <a href="{{ route('reviewer.dashboard') }}">
            <svg style="width:.875rem;height:.875rem;" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
            Dashboard
        </a>
        <a href="{{ route('reviewer.review', $assignment) }}">
            <svg style="width:.875rem;height:.875rem;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            Form Review
        </a>
    </div>
    <div class="topbar-links">
        <button onclick="window.print()" class="print-btn"
                style="background:rgba(255,255,255,.15);color:#fff;border:1px solid rgba(255,255,255,.25);padding:.4rem 1rem;border-radius:.5rem;font-size:.8125rem;font-weight:700;cursor:pointer;display:flex;align-items:center;gap:.375rem;">
            <svg style="width:.875rem;height:.875rem;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5zm-3 0h.008v.008H15V10.5z"/></svg>
            Cetak / PDF
        </button>
    </div>
</div>

<div class="page">
<div class="doc-wrap">

    {{-- Kop Surat --}}
    <div class="header">
        <div style="display:flex;align-items:flex-start;gap:1rem;">
            <div class="journal-logo">{{ strtoupper(substr($assignment->submission->journal->name, 0, 2)) }}</div>
            <div>
                <p class="journal-name">{{ $assignment->submission->journal->name }}</p>
                <p class="journal-meta">{{ $assignment->submission->journal->description ? Str::limit(strip_tags($assignment->submission->journal->description), 80) : 'Jurnal Ilmiah Peer-Reviewed' }}</p>
                @if($assignment->submission->journal->issn_online)
                <p class="journal-meta">ISSN (Online): {{ $assignment->submission->journal->issn_online }}</p>
                @endif
            </div>
        </div>
        <div class="doc-number">
            <p style="font-weight:700;color:#0f172a;">No. ST-REV-{{ str_pad($assignment->id, 5, '0', STR_PAD_LEFT) }}</p>
            <p>Tanggal: {{ now()->translatedFormat('d F Y') }}</p>
            <p style="margin-top:.25rem;">
                @php
                    $methodMap = ['double_blind'=>'Double Blind','single_blind'=>'Single Blind','triple_blind'=>'Triple Blind','open'=>'Open Review'];
                @endphp
                <span class="method-badge">{{ $methodMap[$assignment->review_method] ?? 'Peer Review' }}</span>
            </p>
        </div>
    </div>

    {{-- Judul Surat --}}
    <div class="title-block">
        <h1>Surat Tugas Reviewer</h1>
    </div>

    {{-- Pembuka --}}
    <p class="body-text">Yang bertanda tangan di bawah ini, Editor-in-Chief <strong>{{ $assignment->submission->journal->name }}</strong>, dengan ini menugaskan:</p>

    <div class="detail-box">
        <div class="detail-row">
            <span class="detail-label">Nama Reviewer</span>
            <span class="detail-value"><strong>{{ $assignment->reviewer->first_name }} {{ $assignment->reviewer->last_name }}</strong></span>
        </div>
        @if($assignment->reviewer->affiliation)
        <div class="detail-row">
            <span class="detail-label">Institusi</span>
            <span class="detail-value">{{ $assignment->reviewer->affiliation }}</span>
        </div>
        @endif
        @if($assignment->reviewer->orcid)
        <div class="detail-row">
            <span class="detail-label">ORCID</span>
            <span class="detail-value" style="font-family:monospace;font-size:.875rem;">{{ $assignment->reviewer->orcid }}</span>
        </div>
        @endif
        <div class="detail-row">
            <span class="detail-label">Surel</span>
            <span class="detail-value">{{ $assignment->reviewer->email }}</span>
        </div>
    </div>

    <p class="body-text">Untuk melakukan review terhadap naskah berikut:</p>

    <div class="detail-box">
        <div class="detail-row">
            <span class="detail-label">Judul Naskah</span>
            <span class="detail-value" style="font-weight:700;font-size:1rem;line-height:1.5;">
                @if(in_array($assignment->review_method, ['double_blind','triple_blind']))
                <em style="color:#64748b;">[Tersembunyi — {{ strtoupper(str_replace('_',' ',$assignment->review_method)) }}]</em>
                @else
                {{ $assignment->submission->title }}
                @endif
            </span>
        </div>
        @if($assignment->submission->section)
        <div class="detail-row">
            <span class="detail-label">Seksi / Rubrik</span>
            <span class="detail-value">{{ $assignment->submission->section->title }}</span>
        </div>
        @endif
        <div class="detail-row">
            <span class="detail-label">Putaran Review</span>
            <span class="detail-value"><span class="badge">Putaran {{ $assignment->round ?? 1 }}</span></span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Tanggal Ditugaskan</span>
            <span class="detail-value">{{ $assignment->date_assigned?->translatedFormat('d F Y') ?? now()->translatedFormat('d F Y') }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Batas Waktu Review</span>
            <span class="detail-value" style="font-weight:700;color:#059669;">{{ $assignment->date_due?->translatedFormat('d F Y') ?? '—' }}</span>
        </div>
    </div>

    {{-- Kewajiban --}}
    <div class="obligations">
        <h3>Kewajiban Reviewer</h3>
        <ol>
            <li>Mengevaluasi naskah secara objektif berdasarkan kualitas ilmiah dan relevansi topik.</li>
            <li>Menjaga kerahasiaan penuh atas identitas penulis dan isi naskah (sesuai metode review yang berlaku).</li>
            <li>Menyerahkan hasil review beserta rekomendasi sebelum batas waktu yang ditetapkan.</li>
            <li>Melaporkan kepada editor apabila terdapat potensi konflik kepentingan dengan penulis maupun topik naskah.</li>
            <li>Memberikan masukan yang konstruktif dan argumentasi ilmiah yang dapat dipertanggungjawabkan.</li>
        </ol>
    </div>

    <p class="body-text">Demikian surat tugas ini diterbitkan untuk digunakan sebagaimana mestinya. Atas perhatian dan kesediaan Bapak/Ibu/Sdr sebagai reviewer, kami ucapkan terima kasih.</p>

    {{-- Tanda Tangan --}}
    <div class="signature">
        <div class="sig-block">
            <p class="sig-label">Reviewer yang Ditugaskan</p>
            <div class="sig-line">
                <p class="sig-name">{{ $assignment->reviewer->first_name }} {{ $assignment->reviewer->last_name }}</p>
                <p class="sig-role">Reviewer</p>
                @if($assignment->reviewer->affiliation)
                <p class="sig-role">{{ $assignment->reviewer->affiliation }}</p>
                @endif
            </div>
        </div>
        <div class="sig-block">
            <p class="sig-label">Diterbitkan oleh</p>
            <div class="sig-line">
                <p class="sig-name">Editor-in-Chief</p>
                <p class="sig-role">{{ $assignment->submission->journal->name }}</p>
            </div>
        </div>
    </div>

    {{-- Footer --}}
    <div class="footer-doc">
        <p>Dokumen ini diterbitkan secara digital oleh sistem {{ config('app.name') }}.</p>
        <p style="margin-top:.25rem;">No. ST-REV-{{ str_pad($assignment->id, 5, '0', STR_PAD_LEFT) }} · {{ now()->translatedFormat('d F Y') }} · Putaran {{ $assignment->round ?? 1 }}</p>
    </div>

</div>
</div>

</body>
</html>
