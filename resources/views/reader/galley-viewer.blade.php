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

        @if(!($htmlContent ?? null))
        {{-- Download button — only for non-HTML galleys --}}
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
        @else
        {{-- Font size toggle for HTML reader --}}
        <div style="display:inline-flex;align-items:center;gap:4px;background:rgba(255,255,255,.1);border-radius:.5rem;padding:3px;">
            <button onclick="changeFontSize(-1)" title="Perkecil teks"
                    style="display:inline-flex;align-items:center;justify-content:center;width:28px;height:28px;border-radius:.375rem;border:none;background:transparent;color:#cbd5e1;cursor:pointer;font-size:1rem;font-weight:700;line-height:1;"
                    onmouseover="this.style.background='rgba(255,255,255,.15)'"
                    onmouseout="this.style.background='transparent'">A−</button>
            <button onclick="changeFontSize(1)" title="Perbesar teks"
                    style="display:inline-flex;align-items:center;justify-content:center;width:28px;height:28px;border-radius:.375rem;border:none;background:transparent;color:#cbd5e1;cursor:pointer;font-size:1.125rem;font-weight:700;line-height:1;"
                    onmouseover="this.style.background='rgba(255,255,255,.15)'"
                    onmouseout="this.style.background='transparent'">A+</button>
        </div>
        @endif
    </div>

    {{-- ── HTML ARTICLE READER ─────────────────────────────────────────── --}}
    @if($htmlContent ?? null)
    <div id="htmlReader" style="
        flex:1;
        overflow-y:auto;
        background:#f8fafc;
        padding:0;
    ">
        <div id="htmlBody" style="
            max-width:52rem;
            margin:0 auto;
            padding:2.5rem 1.5rem 4rem;
            font-family:'Georgia','Times New Roman',serif;
            font-size:1.0625rem;
            line-height:1.85;
            color:#1e293b;
        ">
            {{-- Article meta header --}}
            <div style="text-align:center;margin-bottom:2.5rem;padding-bottom:2rem;border-bottom:2px solid #e2e8f0;">
                @if($article->submission->section)
                <div style="font-size:.7rem;font-weight:800;text-transform:uppercase;letter-spacing:.12em;color:#2563eb;margin-bottom:.75rem;">
                    {{ $article->submission->section->title ?? '' }}
                </div>
                @endif
                <h1 style="font-size:1.5rem;font-weight:700;line-height:1.3;color:#0f172a;margin:0 0 1rem;font-family:'Instrument Sans',system-ui,sans-serif;">
                    {{ $article->submission->title }}
                </h1>
                @if($article->submission->subtitle)
                <p style="font-size:1rem;color:#475569;margin:0 0 1rem;font-style:italic;font-family:'Instrument Sans',system-ui,sans-serif;">
                    {{ $article->submission->subtitle }}
                </p>
                @endif
                <div style="font-size:.875rem;color:#64748b;font-family:'Instrument Sans',system-ui,sans-serif;">
                    @foreach($article->submission->contributors as $i => $c)
                    <span>{{ $c->full_name }}@if($c->affiliation) <span style="color:#94a3b8;font-size:.8rem;">({{ $c->affiliation }})</span>@endif</span>@if(!$loop->last), @endif
                    @endforeach
                </div>
                @if($article->doi)
                <div style="margin-top:.75rem;font-size:.8125rem;font-family:'Instrument Sans',system-ui,sans-serif;">
                    <a href="https://doi.org/{{ $article->doi }}" style="color:#2563eb;">https://doi.org/{{ $article->doi }}</a>
                </div>
                @endif
            </div>

            {{-- Abstract --}}
            @if($article->submission->abstract)
            <div style="background:#eff6ff;border-left:4px solid #2563eb;padding:1.25rem 1.5rem;margin-bottom:2rem;border-radius:0 .5rem .5rem 0;">
                <p style="font-size:.75rem;font-weight:800;text-transform:uppercase;letter-spacing:.1em;color:#1d4ed8;margin:0 0 .5rem;font-family:'Instrument Sans',system-ui,sans-serif;">Abstrak</p>
                <p style="margin:0;font-size:.9375rem;color:#1e3a8a;line-height:1.7;">{!! $article->submission->abstract !!}</p>
            </div>
            @endif

            {{-- HTML content --}}
            <div class="article-html-content">
                {!! $htmlContent !!}
            </div>
        </div>
    </div>

    <style>
        .article-html-content h1,.article-html-content h2,.article-html-content h3,
        .article-html-content h4,.article-html-content h5 {
            font-family:'Instrument Sans',system-ui,sans-serif;
            color:#0f172a;margin-top:2rem;margin-bottom:.75rem;line-height:1.3;
        }
        .article-html-content h2 { font-size:1.25rem;font-weight:700; }
        .article-html-content h3 { font-size:1.0625rem;font-weight:700; }
        .article-html-content p { margin:0 0 1.2rem; }
        .article-html-content table {
            width:100%;border-collapse:collapse;margin:1.5rem 0;font-size:.9rem;
            font-family:'Instrument Sans',system-ui,sans-serif;
        }
        .article-html-content th {
            background:#1e3a8a;color:#fff;padding:.6rem .875rem;text-align:left;font-size:.8125rem;
        }
        .article-html-content td { padding:.55rem .875rem;border-bottom:1px solid #e2e8f0; }
        .article-html-content tr:nth-child(even) td { background:#f8fafc; }
        .article-html-content figure { margin:2rem 0;text-align:center; }
        .article-html-content figure img { max-width:100%;border-radius:.5rem;box-shadow:0 4px 16px rgba(0,0,0,.1); }
        .article-html-content figcaption {
            font-size:.8125rem;color:#64748b;margin-top:.5rem;
            font-family:'Instrument Sans',system-ui,sans-serif;font-style:italic;
        }
        .article-html-content img { max-width:100%;height:auto; }
        .article-html-content blockquote {
            border-left:3px solid #cbd5e1;padding-left:1.25rem;margin:1.5rem 0;
            color:#475569;font-style:italic;
        }
        .article-html-content a { color:#2563eb;text-decoration:underline; }
        .article-html-content sup { font-size:.7em;line-height:0; }
        .article-html-content .references, .article-html-content #references {
            font-size:.875rem;line-height:1.7;
        }
    </style>

    <script>
        var baseFontSize = 17;
        function changeFontSize(delta) {
            baseFontSize = Math.max(13, Math.min(24, baseFontSize + delta));
            document.getElementById('htmlBody').style.fontSize = baseFontSize + 'px';
        }
        // Show issue button on desktop
        (function() {
            var btn = document.getElementById('btn-issue');
            function applyWidth() { if (btn) btn.style.display = window.innerWidth >= 768 ? 'inline-flex' : 'none'; }
            applyWidth();
            window.addEventListener('resize', applyWidth);
        })();
    </script>

    {{-- ── PDF FRAME ────────────────────────────────────────────────────── --}}
    @elseif($pdfUrl)
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

    {{-- ── Floating WhatsApp Contact Button ──────────────────────────────── --}}
    @php
        $waRaw = $journal->wa_contact ?? $journal->contact_phone ?? '';
        $waNumber = preg_replace('/[^0-9]/', '', $waRaw);
        if (str_starts_with($waNumber, '0')) {
            $waNumber = '62' . substr($waNumber, 1);
        }
        $waMsg = urlencode('Halo, saya ingin bertanya mengenai artikel: ' . ($article->submission->title ?? '') . ' yang dipublikasikan di ' . ($journal->name ?? ''));
    @endphp

    @if($waNumber)
    <a href="https://wa.me/{{ $waNumber }}?text={{ $waMsg }}"
       target="_blank" rel="noopener"
       title="Hubungi pengelola jurnal via WhatsApp"
       style="
           position:fixed;
           bottom:1.5rem;right:1.5rem;
           z-index:9999;
           display:inline-flex;align-items:center;gap:.5rem;
           background:#25d366;color:#fff;
           font-family:'Instrument Sans',sans-serif;
           font-size:.8125rem;font-weight:700;
           padding:.55rem 1rem .55rem .75rem;
           border-radius:2rem;
           text-decoration:none;
           box-shadow:0 4px 16px rgba(37,211,102,.45);
           transition:transform .15s,box-shadow .15s;
       "
       onmouseover="this.style.transform='scale(1.05)';this.style.boxShadow='0 6px 20px rgba(37,211,102,.55)'"
       onmouseout="this.style.transform='scale(1)';this.style.boxShadow='0 4px 16px rgba(37,211,102,.45)'">
        {{-- WhatsApp icon --}}
        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" style="flex-shrink:0;">
            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.890-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
        </svg>
        Hubungi Pengelola
    </a>
    @endif

</body>
</html>
