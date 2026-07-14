<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Naskah Diterima untuk Tahap Review</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #f8fafc; margin: 0; padding: 20px; }
        .container { max-width: 560px; margin: 0 auto; background: #ffffff; border-radius: 12px; overflow: hidden; border: 1px solid #e2e8f0; }
        .header { background: linear-gradient(135deg, #2563eb, #059669); padding: 32px 32px 24px; }
        .header h1 { color: #ffffff; font-size: 20px; font-weight: 700; margin: 0 0 4px; }
        .header p { color: rgba(255,255,255,0.75); font-size: 14px; margin: 0; }
        .body { padding: 28px 32px; }
        .greeting { font-size: 16px; color: #1e293b; font-weight: 600; margin-bottom: 12px; }
        p { font-size: 14px; color: #475569; line-height: 1.6; margin: 0 0 14px; }
        .card { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 16px 20px; margin: 20px 0; }
        .card dt { font-size: 11px; text-transform: uppercase; letter-spacing: 0.05em; color: #94a3b8; margin-bottom: 3px; }
        .card dd { font-size: 14px; font-weight: 600; color: #1e293b; margin: 0 0 12px; }
        .card dd:last-child { margin-bottom: 0; }
        .footer { background: #f8fafc; border-top: 1px solid #e2e8f0; padding: 18px 32px; font-size: 12px; color: #94a3b8; text-align: center; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>{{ $submission->journal->name }}</h1>
        <p>Naskah Diterima untuk Tahap Review</p>
    </div>
    <div class="body">
        <p class="greeting">Yth. {{ $submission->submitter->first_name }} {{ $submission->submitter->last_name }},</p>

        <p>Kabar baik — naskah Anda telah lolos skrining awal editor dan resmi <strong>diterima untuk memasuki tahap review</strong> di {{ $submission->journal->name }}. Selanjutnya, editor akan menugaskan reviewer untuk mengevaluasi naskah Anda.</p>

        <div class="card">
            <dl>
                <dt>Judul Naskah</dt>
                <dd>{{ $submission->title }}</dd>
                <dt>Nomor Submission</dt>
                <dd>#{{ $submission->id }}</dd>
                <dt>Jurnal</dt>
                <dd>{{ $submission->journal->name }}</dd>
            </dl>
        </div>

        <p>Anda dapat memantau perkembangan naskah kapan saja melalui Dashboard Penulis.</p>

        <p>Jika ada pertanyaan, silakan hubungi kami di <a href="mailto:{{ $submission->journal->email }}" style="color:#2563eb;">{{ $submission->journal->email }}</a>.</p>
        <p style="margin-top:20px;">Salam hormat,<br><strong>Tim Editorial {{ $submission->journal->name }}</strong></p>
    </div>
    <div class="footer">
        &copy; {{ date('Y') }} {{ config('app.name') }} &mdash; Platform Jurnal Ilmiah Indonesia
    </div>
</div>
</body>
</html>
