<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Undangan Review</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #f8fafc; margin: 0; padding: 20px; }
        .container { max-width: 560px; margin: 0 auto; background: #ffffff; border-radius: 12px; overflow: hidden; border: 1px solid #e2e8f0; }
        .header { background: linear-gradient(135deg, #0891b2, #2563eb); padding: 32px 32px 24px; }
        .header h1 { color: #ffffff; font-size: 20px; font-weight: 700; margin: 0 0 4px; }
        .header p { color: #bae6fd; font-size: 14px; margin: 0; }
        .body { padding: 28px 32px; }
        .greeting { font-size: 16px; color: #1e293b; font-weight: 600; margin-bottom: 12px; }
        p { font-size: 14px; color: #475569; line-height: 1.6; margin: 0 0 14px; }
        .card { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 16px 20px; margin: 20px 0; }
        .card dt { font-size: 11px; text-transform: uppercase; letter-spacing: 0.05em; color: #94a3b8; margin-bottom: 3px; }
        .card dd { font-size: 14px; font-weight: 600; color: #1e293b; margin: 0 0 12px; }
        .card dd:last-child { margin-bottom: 0; }
        .deadline { background: #fef3c7; border: 1px solid #fde68a; border-radius: 8px; padding: 12px 16px; margin: 16px 0; font-size: 13px; color: #92400e; }
        .footer { background: #f8fafc; border-top: 1px solid #e2e8f0; padding: 18px 32px; font-size: 12px; color: #94a3b8; text-align: center; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>{{ $assignment->submission->journal->name }}</h1>
        <p>Undangan Reviewer</p>
    </div>
    <div class="body">
        <p class="greeting">Yth. {{ $assignment->reviewer->first_name }} {{ $assignment->reviewer->last_name }},</p>
        <p>Kami mengundang Anda untuk menjadi reviewer naskah berikut. Keahlian Anda sangat kami butuhkan untuk memastikan kualitas publikasi kami.</p>

        <div class="card">
            <dl>
                <dt>Judul Naskah</dt>
                <dd>{{ $assignment->submission->title }}</dd>
                <dt>Jurnal</dt>
                <dd>{{ $assignment->submission->journal->name }}</dd>
                @if($assignment->date_due)
                <dt>Batas Waktu Review</dt>
                <dd>{{ \Carbon\Carbon::parse($assignment->date_due)->format('d F Y') }}</dd>
                @endif
            </dl>
        </div>

        @if($assignment->date_due)
        <div class="deadline">
            ⏰ Mohon berikan tanggapan penerimaan undangan ini sebelum batas waktu yang ditentukan.
        </div>
        @endif

        <p>Jika Anda bersedia, silakan masuk ke sistem dan terima undangan review ini. Kami sangat menghargai kontribusi Anda terhadap pengembangan ilmu pengetahuan.</p>
        <p style="margin-top:20px;">Salam hormat,<br><strong>Tim Editorial {{ $assignment->submission->journal->name }}</strong></p>
    </div>
    <div class="footer">
        &copy; {{ date('Y') }} {{ config('app.name') }} &mdash; Platform Jurnal Ilmiah Indonesia
    </div>
</div>
</body>
</html>
