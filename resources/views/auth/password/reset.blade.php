<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password Baru - Notaris App</title>
    <!-- Tambahkan CSS Anda di sini -->
    <style>
        body { font-family: sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; background: #f3f4f6; }
        .card { background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); width: 100%; max-width: 400px; }
        .form-group { margin-bottom: 1.2rem; }
        .form-group label { display: block; margin-bottom: 0.5rem; font-weight: bold; }
        .form-control { width: 100%; padding: 0.6rem; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        .btn { width: 100%; padding: 0.7rem; background: #10b981; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 1rem; }
        .btn:hover { background: #059669; }
        .alert { padding: 0.75rem; border-radius: 4px; margin-bottom: 1rem; }
        .alert-danger { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; list-style-type: none; padding-left: 1rem; }
        .error-text { color: #dc2626; font-size: 0.85rem; margin-top: 0.25rem; }
    </style>
</head>
<body>

<div class="card">
    <h2>Reset Password</h2>
    <p style="color: #6b7280; margin-bottom: 1.5rem;">Silakan buat password baru untuk akun Anda.</p>

    <!-- Tampilkan Error Global jika ada masalah token -->
    @if ($errors->any() && !$errors->has('password'))
        <div class="alert alert-danger">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </div>
    @endif

    <form action="{{ route('password.update') }}" method="POST">
        @csrf

        <!-- Token & Email Tersembunyi (Wajib diikutkan agar API mengenali user) -->
        <input type="hidden" name="token" value="{{ $token }}">
        <input type="hidden" name="email" value="{{ $email }}">

        <!-- Menampilkan Info Email (Read-Only) sebagai konfirmasi ke user -->
        <div class="form-group">
            <label>Email Akun</label>
            <input type="text" class="form-control" value="{{ $email }}" disabled style="background-color: #e5e7eb; cursor: not-allowed;">
        </div>

        <!-- Input Password Baru -->
        <div class="form-group">
            <label for="password">Password Baru</label>
            <input type="password" id="password" name="password" class="form-control" required autofocus placeholder="Minimal 8 karakter">
            @if ($errors->has('password'))
                <div class="error-text">{{ $errors->first('password') }}</div>
            @endif
        </div>

        <!-- Input Konfirmasi Password -->
        <div class="form-group">
            <label for="password_confirmation">Konfirmasi Password Baru</label>
            <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required placeholder="Ulangi password baru">
        </div>

        <button type="submit" class="btn">Perbarui Password</button>
    </form>
</div>

</body>
</html>