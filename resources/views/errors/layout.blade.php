<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') — GJS</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background: #0f172a;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
            color: #e2e8f0;
        }

        /* ── animated background ── */
        .bg-glow {
            position: fixed;
            inset: 0;
            pointer-events: none;
            overflow: hidden;
            z-index: 0;
        }
        .bg-glow::before {
            content: '';
            position: absolute;
            width: 800px; height: 800px;
            background: radial-gradient(circle, rgba(37,99,235,.18) 0%, transparent 70%);
            top: -200px; left: -200px;
            animation: drift1 12s ease-in-out infinite alternate;
        }
        .bg-glow::after {
            content: '';
            position: absolute;
            width: 600px; height: 600px;
            background: radial-gradient(circle, rgba(124,58,237,.15) 0%, transparent 70%);
            bottom: -100px; right: -100px;
            animation: drift2 15s ease-in-out infinite alternate;
        }
        @keyframes drift1 { from { transform: translate(0,0); } to { transform: translate(80px, 60px); } }
        @keyframes drift2 { from { transform: translate(0,0); } to { transform: translate(-60px, -80px); } }

        /* grid lines */
        .bg-grid {
            position: fixed; inset: 0; z-index: 0; pointer-events: none;
            background-image:
                linear-gradient(rgba(255,255,255,.025) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,.025) 1px, transparent 1px);
            background-size: 48px 48px;
        }

        /* ── card ── */
        .card {
            position: relative; z-index: 1;
            background: rgba(255,255,255,.04);
            border: 1px solid rgba(255,255,255,.08);
            border-radius: 24px;
            padding: 56px 64px;
            text-align: center;
            max-width: 520px;
            width: calc(100% - 48px);
            backdrop-filter: blur(20px);
            box-shadow:
                0 0 0 1px rgba(255,255,255,.04) inset,
                0 32px 64px rgba(0,0,0,.4);
            animation: cardIn .6s cubic-bezier(.22,1,.36,1) both;
        }
        @keyframes cardIn {
            from { opacity:0; transform: translateY(32px) scale(.96); }
            to   { opacity:1; transform: translateY(0) scale(1); }
        }

        /* code number */
        .code {
            font-size: clamp(80px, 18vw, 120px);
            font-weight: 900;
            letter-spacing: -4px;
            line-height: 1;
            background: @yield('code-gradient', 'linear-gradient(135deg, #3b82f6, #8b5cf6)');
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 8px;
            animation: pulse 3s ease-in-out infinite;
        }
        @keyframes pulse {
            0%, 100% { filter: brightness(1); }
            50%       { filter: brightness(1.15); }
        }

        .icon { font-size: 48px; margin-bottom: 12px; animation: wobble 2s ease-in-out infinite; }
        @keyframes wobble {
            0%,100% { transform: rotate(-3deg); }
            50%      { transform: rotate(3deg); }
        }

        h1 {
            font-size: 22px; font-weight: 700; color: #f1f5f9;
            margin-bottom: 10px;
        }
        p {
            font-size: 15px; color: #94a3b8; line-height: 1.7;
            margin-bottom: 32px;
        }

        .actions { display: flex; gap: 12px; justify-content: center; flex-wrap: wrap; }
        .btn {
            display: inline-flex; align-items: center; gap-6px;
            padding: 11px 24px; border-radius: 12px;
            font-size: 14px; font-weight: 600; text-decoration: none;
            transition: all .2s;
            gap: 6px;
        }
        .btn-primary {
            background: linear-gradient(135deg,#2563eb,#4f46e5);
            color: #fff;
            box-shadow: 0 4px 16px rgba(37,99,235,.35);
        }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(37,99,235,.45); }
        .btn-ghost {
            background: rgba(255,255,255,.07);
            color: #94a3b8;
            border: 1px solid rgba(255,255,255,.1);
        }
        .btn-ghost:hover { background: rgba(255,255,255,.12); color: #e2e8f0; transform: translateY(-2px); }

        .divider {
            margin: 32px auto 24px;
            height: 1px; width: 60%;
            background: linear-gradient(to right, transparent, rgba(255,255,255,.1), transparent);
        }
        .brand {
            font-size: 12px; color: #475569; letter-spacing: .5px;
        }
        .brand span { color: #3b82f6; font-weight: 600; }
    </style>
</head>
<body>
<div class="bg-glow"></div>
<div class="bg-grid"></div>

<div class="card">
    <div class="icon">@yield('icon')</div>
    <div class="code">@yield('code')</div>
    <h1>@yield('heading')</h1>
    <p>@yield('message')</p>

    <div class="actions">
        <a href="/" class="btn btn-primary">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            Beranda
        </a>
        <a href="javascript:history.back()" class="btn btn-ghost">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali
        </a>
    </div>

    <div class="divider"></div>
    <p class="brand">Jurnal Ilmiah &mdash; <span>{{ \App\Models\Setting::get('brand.abbrev', \App\Models\Setting::get('brand.site_name', config('app.name'))) }}</span></p>
</div>
</body>
</html>