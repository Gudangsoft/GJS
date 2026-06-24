<!DOCTYPE html>
<html lang="id">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<style>
body{font-family:Arial,sans-serif;font-size:14px;color:#333;background:#f5f5f5;margin:0;padding:0;}
.wrap{max-width:600px;margin:30px auto;background:#fff;border-radius:8px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,.1);}
.header{background:linear-gradient(135deg,#1e3a5f,#1e40af);padding:24px 28px;}
.header h1{color:#fff;font-size:18px;margin:0;font-weight:700;}
.header p{color:#bfdbfe;font-size:13px;margin:4px 0 0;}
.body{padding:28px;}
.body p{line-height:1.8;white-space:pre-line;}
.footer{background:#f8fafc;padding:16px 28px;border-top:1px solid #e2e8f0;font-size:11px;color:#94a3b8;text-align:center;}
</style>
</head>
<body>
<div class="wrap">
  <div class="header">
    <h1>{{ $journal->name }}</h1>
    <p>{{ $journal->publisher }}</p>
  </div>
  <div class="body">
    <p>{{ $blastMessage }}</p>
  </div>
  <div class="footer">
    Email ini dikirim oleh redaksi {{ $journal->name }} &bull;
    @if($journal->url)<a href="{{ $journal->url }}" style="color:#60a5fa;">{{ $journal->url }}</a>@endif
  </div>
</div>
</body>
</html>