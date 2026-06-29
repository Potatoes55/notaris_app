<!DOCTYPE html>
<html>
<head>
    <title>Reset PIN Aplikasi Notaris</title>
</head>
<body>
    <h3>Halo, {{ $user->name }}</h3>
    <p>Kami menerima permintaan untuk mereset PIN keamanan aplikasi Notaris Anda.</p>
    <p>Silakan klik link di bawah ini untuk membuat PIN baru:</p>
    <p>
        <a href="{{ $url }}" style="background: #28a745; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px; display: inline-block;">
            Reset PIN Saya
        </a>
    </p>
    <p>Link ini berlaku selama 60 menit. Jika Anda tidak merasa melakukan permintaan ini, abaikan saja email ini.</p>
    <br>
    <p>Salam,<br>Tim Aplikasi Notaris</p>
</body>
</html>