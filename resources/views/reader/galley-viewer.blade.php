<!DOCTYPE html>
<html lang="id" style="height:100%;margin:0;padding:0;">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ Str::limit($article->submission->title ?? 'Artikel', 60) }} — {{ $journal->name_abbrev ?? $journal->name }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700&display=swap" rel="stylesheet">
</head>

{{-- No Tailwind/Vite — pure inline styles to prevent any CSS conflicts --}}
<body style="margin:0;padding:0;height:100%;font-family:'Instrument Sans',system-ui,sans-serif;background:#0f172a;display:flex;flex-direction:column;overflow:hidden;">

    {{-- ── TOP BAR ─────────────────────────────────────────────────────── --}}
    <div style="
        flex-shrink:0;
        height:52px;
        background:linear-gradient(135deg,#0c1a3a 0%,#1a3272 100%);
        border-bottom:1px solid rgba(255,255,255,.12);
        box-shadow:0 2px 16px rgba(0,0,0,.5);
        display:flex;
        align-items:center;
        gap:10px;
        padding:0 14px;
    ">
        {{-- Back to article --}}
        <a href="{{ route('journals.articles.show', [$journal->slug, $article->id]) }}"
           title="Kembali ke halaman artikel"
           style="
               display:inline-flex;align-items:center;gap:5px;
               color:#93c5fd;text-decoration:none;
               font-size:.8125rem;font-weight:700;white-space:nowrap;flex-shrink:0;
               padding:.3rem .6rem;border-radius:.375rem;
               transition:background .15s;
           "
           onmouseover="this.style.background='rgba(147,197,253,.12)'"
           onmouseout="this.style.background='transparent'">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
            </svg>
            Kembali
        </a>

        {{-- Divider --}}
        <div style="width:1px;height:24px;background:rgba(255,255,255,.15);flex-shrink:0;"></div>

        {{-- Meta --}}
        <div style="flex:1;min-width:0;overflow:hidden;">
            <div style="font-size:.6rem;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:#60a5fa;line-height:1;margin-bottom:2px;">
                {{ $journal->name_abbrev ?? $journal->name }}
            </div>
            <div style="font-size:.8125rem;font-weight:600;color:#f1f5f9;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                {{ $article->submission->title ?? '' }}
            </div>
        </div>

        {{-- Issue link (md+) --}}
        @if($article->issue_id)
        <a href="{{ route('journals.issues.show', [$journal->slug, $article->issue_id]) }}"
           id="btn-issue"
           style="
               display:none;
               align-items:center;gap:5px;
               background:rgba(255,255,255,.08);color:#cbd5e1;
               font-size:.8125rem;font-weight:600;
               padding:.35rem .7rem;border-radius:.5rem;
               text-decoration:none;white-space:nowrap;flex-shrink:0;
               transition:background .15s;
           "
           onmouseover="this.style.background='rgba(255,255,255,.14)'"
           onmouseout="this.style.background='rgba(255,255,255,.08)'">
            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/>
            </svg>
            Terbitan
        </a>
        @endif

        {{-- Download button --}}
        <a href="{{ route('journals.articles.galley', [$journal->slug, $article->id, $galley->id]) }}?dl=1"
           style="
               display:inline-flex;align-items:center;gap:6px;
               background:#ef4444;color:#fff;
               font-size:.8125rem;font-weight:700;
               padding:.375rem .875rem;border-radius:.5rem;
               text-decoration:none;white-space:nowrap;flex-shrink:0;
               transition:background .15s;
           "
           onmouseover="this.style.background='#dc2626'"
           onmouseout="this.style.background='#ef4444'">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/>
            </svg>
            Unduh
        </a>
    </div>

    {{-- ── PDF FRAME ────────────────────────────────────────────────────── --}}
    {{-- $pdfUrl = direct storage URL (avoids single-threaded PHP server deadlock) --}}

    @if($pdfUrl)
    <iframe
        id="pdfFrame"
        src="{{ $pdfUrl }}"
        style="
            flex:1;
            width:100%;
            border:none;
            display:block;
            background:#525659;
        "
        allowfullscreen
        loading="eager"
        title="{{ $article->submission->title ?? 'Artikel PDF' }}"
    ></iframe>

    {{-- Fallback: shown by JS if iframe fails --}}
    <div id="fallback" style="
        display:none;
        flex-direction:column;
        align-items:center;
        justify-content:center;
        flex:1;
        color:#94a3b8;
        text-align:center;
        padding:2rem;
        gap:1rem;
    ">
        <svg width="52" height="52" fill="none" stroke="#475569" stroke-width="1.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
        </svg>
        <p style="font-size:.9375rem;">PDF tidak dapat ditampilkan langsung di browser ini.</p>
        <div style="display:flex;gap:.75rem;flex-wrap:wrap;justify-content:center;">
            <a href="{{ $pdfUrl }}" target="_blank"
               style="display:inline-flex;align-items:center;gap:6px;background:#3b82f6;color:#fff;font-weight:700;font-size:.875rem;padding:.6rem 1.25rem;border-radius:.5rem;text-decoration:none;">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/></svg>
                Buka di Tab Baru
            </a>
            <a href="{{ route('journals.articles.galley', [$journal->slug, $article->id, $galley->id]) }}?dl=1"
               style="display:inline-flex;align-items:center;gap:6px;background:#ef4444;color:#fff;font-weight:700;font-size:.875rem;padding:.6rem 1.25rem;border-radius:.5rem;text-decoration:none;">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
                Unduh PDF
            </a>
        </div>
    </div>

    <script>
        // Show "Terbitan" button on desktop
        (function() {
            var btn = document.getElementById('btn-issue');
            function applyWidth() {
                if (!btn) return;
                btn.style.display = window.innerWidth >= 768 ? 'inline-flex' : 'none';
            }
            applyWidth();
            window.addEventListener('resize', applyWidth);
        })();

        // Only show fallback on actual network/load error.
        // NOTE: do NOT check contentDocument.body — when the browser's built-in
        // PDF viewer renders inside an iframe, body.children is empty even on
        // success, which would incorrectly trigger the fallback.
        (function() {
            var frame    = document.getElementById('pdfFrame');
            var fallback = document.getElementById('fallback');
            if (!frame || !fallback) return;

            frame.addEventListener('error', function() {
                frame.style.display = 'none';
                fallback.style.display = 'flex';
            });
        })();
    </script>

    @else
    {{-- File not found --}}
    <div style="display:flex;flex-direction:column;align-items:center;justify-content:center;flex:1;color:#94a3b8;text-align:center;padding:2rem;gap:1rem;">
        <svg width="52" height="52" fill="none" stroke="#475569" stroke-width="1.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
        </svg>
        <p style="font-size:.9375rem;">File galley belum tersedia.</p>
        <a href="{{ route('journals.articles.show', [$journal->slug, $article->id]) }}"
           style="display:inline-flex;align-items:center;gap:6px;background:#3b82f6;color:#fff;font-weight:700;font-size:.875rem;padding:.6rem 1.25rem;border-radius:.5rem;text-decoration:none;">
            Kembali ke Artikel
        </a>
    </div>
    @endif

</body>
</html>
