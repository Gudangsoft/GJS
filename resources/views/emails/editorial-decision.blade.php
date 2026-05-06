<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Keputusan Editorial</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #f8fafc; margin: 0; padding: 20px; }
        .container { max-width: 560px; margin: 0 auto; background: #ffffff; border-radius: 12px; overflow: hidden; border: 1px solid #e2e8f0; }
        .header-accepted  { background: linear-gradient(135deg, #059669, #0284c7); padding: 32px 32px 24px; }
        .header-revision  { background: linear-gradient(135deg, #d97706, #ea580c); padding: 32px 32px 24px; }
        .header-declined  { background: linear-gradient(135deg, #dc2626, #9333ea); padding: 32px 32px 24px; }
        .header-default   { background: linear-gradient(135deg, #475569, #334155); padding: 32px 32px 24px; }
        .header h1 { color: #ffffff; font-size: 20px; font-weight: 700; margin: 0 0 4px; }
        .header p { color: rgba(255,255,255,0.75); font-size: 14px; margin: 0; }
        .body { padding: 28px 32px; }
        .greeting { font-size: 16px; color: #1e293b; font-weight: 600; margin-bottom: 12px; }
        p { font-size: 14px; color: #475569; line-height: 1.6; margin: 0 0 14px; }
        .card { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 16px 20px; margin: 20px 0; }
        .card dt { font-size: 11px; text-transform: uppercase; letter-spacing: 0.05em; color: #94a3b8; margin-bottom: 3px; }
        .card dd { font-size: 14px; font-weight: 600; color: #1e293b; margin: 0 0 12px; }
        .card dd:last-child { margin-bottom: 0; }
        .message-box { background: #f0f9ff; border: 1px solid #bae6fd; border-radius: 8px; padding: 14px 18px; margin: 16px 0; font-size: 14px; color: #0369a1; line-height: 1.6; }
        .footer { background: #f8fafc; border-top: 1px solid #e2e8f0; padding: 18px 32px; font-size: 12px; color: #94a3b8; text-align: center; }
    </style>
</head>
<body>
<div class="container">
    @php
        $headerClass = match($decision) {
            'accepted' => 'header-accepted',
            'revision_required' => 'header-revision',
            'declined' => 'header-declined',
            default => 'header-default',
        };
        $decisionLabel = match($decision) {
            'accepted' => 'Naskah Diterima',
            'revision_required' => 'Revisi Diperlukan',
            'declined' => 'Naskah Tidak Dapat Diterima',
            default => 'Keputusan Editorial',
        };
    @endphp
    <div class="{{ $headerClass }}">
        <h1>{{ $submission->journal->name }}</h1>
        <p>{{ $decisionLabel }}</p>
    </div>
    <div class="body">
        <p class="greeting">Yth. {{ $submission->submitter->first_name }} {{ $submission->submitter->last_name }},</p>

        @if($decision === 'accepted')
        <p>Kami dengan senang hati menyampaikan bahwa naskah Anda telah <strong>diterima</strong> untuk diterbitkan di {{ $submission->journal->name }}. Selamat!</p>
        @elseif($decision === 'revision_required')
        <p>Setelah melalui proses review, naskah Anda memerlukan beberapa <strong>revisi</strong> sebelum dapat diterbitkan. Mohon perhatikan masukan dari reviewer berikut ini.</p>
        @elseif($decision === 'declined')
        <p>Setelah melalui proses review yang cermat, dengan menyesal kami menyampaikan bahwa naskah Anda <strong>tidak dapat diterima</strong> untuk diterbitkan di jurnal kami saat ini.</p>
        @endif

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

        @if($message)
        <div class="message-box">
            <strong>Pesan dari Editor:</strong><br>
            {{ $message }}
        </div>
        @endif

        @if($decision === 'revision_required')
        <p>Silakan masuk ke Dashboard Penulis untuk melihat detail revisi yang diperlukan dan mengunggah naskah revisi Anda.</p>
        @elseif($decision === 'declined')
        <p>Kami mendorong Anda untuk terus berkarya dan mempertimbangkan jurnal lain yang mungkin lebih sesuai dengan topik naskah Anda.</p>
        @endif

        <p>Jika ada pertanyaan, silakan hubungi kami di <a href="mailto:{{ $submission->journal->email }}" style="color:#2563eb;">{{ $submission->journal->email }}</a>.</p>
        <p style="margin-top:20px;">Salam hormat,<br><strong>Tim Editorial {{ $submission->journal->name }}</strong></p>
    </div>
    <div class="footer">
        &copy; {{ date('Y') }} {{ config('app.name') }} &mdash; Platform Jurnal Ilmiah Indonesia
    </div>
</div>
</body>
</html>
