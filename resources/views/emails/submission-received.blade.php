<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Naskah Diterima</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #f8fafc; margin: 0; padding: 20px; }
        .container { max-width: 560px; margin: 0 auto; background: #ffffff; border-radius: 12px; overflow: hidden; border: 1px solid #e2e8f0; }
        .header { background: linear-gradient(135deg, #2563eb, #4f46e5); padding: 32px 32px 24px; }
        .header h1 { color: #ffffff; font-size: 20px; font-weight: 700; margin: 0 0 4px; }
        .header p { color: #bfdbfe; font-size: 14px; margin: 0; }
        .body { padding: 28px 32px; }
        .greeting { font-size: 16px; color: #1e293b; font-weight: 600; margin-bottom: 12px; }
        p { font-size: 14px; color: #475569; line-height: 1.6; margin: 0 0 14px; }
        .card { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 16px 20px; margin: 20px 0; }
        .card dt { font-size: 11px; text-transform: uppercase; letter-spacing: 0.05em; color: #94a3b8; margin-bottom: 3px; }
        .card dd { font-size: 14px; font-weight: 600; color: #1e293b; margin: 0 0 12px; }
        .card dd:last-child { margin-bottom: 0; }
        .badge { display: inline-block; background: #dbeafe; color: #1d4ed8; font-size: 12px; font-weight: 600; padding: 3px 10px; border-radius: 999px; }
        .footer { background: #f8fafc; border-top: 1px solid #e2e8f0; padding: 18px 32px; font-size: 12px; color: #94a3b8; text-align: center; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>{{ $submission->journal->name }}</h1>
        <p>Sistem Manajemen Jurnal</p>
    </div>
    <div class="body">
        <p class="greeting">Yth. {{ $submission->submitter->first_name }} {{ $submission->submitter->last_name }},</p>
        <p>Naskah Anda telah berhasil diterima oleh sistem dan akan segera diproses oleh tim editorial. Terima kasih atas kontribusi Anda.</p>

        <div class="card">
            <dl>
                <dt>Judul Naskah</dt>
                <dd>{{ $submission->title }}</dd>
                <dt>Nomor Submission</dt>
                <dd><span class="badge">#{{ $submission->id }}</span></dd>
                <dt>Jurnal Tujuan</dt>
                <dd>{{ $submission->journal->name }}</dd>
                <dt>Tanggal Dikirim</dt>
                <dd>{{ $submission->submitted_at?->format('d F Y, H:i') }} WIB</dd>
            </dl>
        </div>

        <p>Anda dapat memantau perkembangan naskah melalui <strong>Dashboard Penulis</strong>. Tim editorial akan menghubungi Anda jika ada informasi lebih lanjut.</p>
        <p>Jika Anda memiliki pertanyaan, silakan hubungi redaksi di <a href="mailto:{{ $submission->journal->email }}" style="color:#2563eb;">{{ $submission->journal->email }}</a>.</p>
        <p style="margin-top:20px;">Salam hormat,<br><strong>Tim Editorial {{ $submission->journal->name }}</strong></p>
    </div>
    <div class="footer">
        &copy; {{ date('Y') }} {{ config('app.name') }} &mdash; Platform Jurnal Ilmiah Indonesia
    </div>
</div>
</body>
</html>
