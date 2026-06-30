<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Reset PIN - NOTARIS APP</title>
</head>

<body style="margin:0;padding:0;background:#f4f7fb;font-family:'Segoe UI',Arial,sans-serif;">

<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="padding:40px 20px;">
    <tr>
        <td align="center">

            <table role="presentation" width="600" cellpadding="0" cellspacing="0"
                style="background:#ffffff;border-radius:14px;overflow:hidden;box-shadow:0 8px 24px rgba(0,0,0,.08);">

                <!-- Header -->
                <tr>
                    <td align="center" style="background:#fb6340;padding:35px 30px;">

                        <h1 style="margin:0;color:#ffffff;font-size:30px;font-weight:700;letter-spacing:.5px;">
                            NOTARIS APP
                        </h1>

                        <p style="margin:10px 0 0;color:#ffe9e3;font-size:15px;">
                            Sistem Manajemen Notaris & PPAT
                        </p>

                    </td>
                </tr>

                <!-- Body -->
                <tr>
                    <td style="padding:40px 38px;">

                        <h2 style="margin:0 0 25px;color:#344767;font-size:24px;">
                            Reset PIN
                        </h2>

                        <p style="margin:0 0 18px;color:#67748e;font-size:15px;line-height:1.8;">
                            Halo <strong>{{ $user->username }}</strong>,
                        </p>

                        <p style="margin:0 0 18px;color:#67748e;font-size:15px;line-height:1.8;">
                            Kami menerima permintaan untuk mengatur ulang PIN akun NOTARIS APP Anda.
                        </p>

                        <p style="margin:0 0 30px;color:#67748e;font-size:15px;line-height:1.8;">
                            Klik tombol di bawah ini untuk membuat PIN baru. Demi keamanan akun, tautan ini hanya dapat digunakan satu kali dan akan kedaluwarsa dalam waktu <strong>60 menit</strong>.
                        </p>

                        <div style="text-align:center;margin:35px 0;">

                            <a href="{{ $url }}"
                                style="background:#fb6340;color:#ffffff;text-decoration:none;padding:15px 36px;border-radius:8px;font-size:15px;font-weight:600;display:inline-block;">
                                Reset PIN
                            </a>

                        </div>

                        <p style="margin:0 0 12px;color:#67748e;font-size:14px;line-height:1.8;">
                            Apabila tombol di atas tidak berfungsi, salin dan buka tautan berikut melalui browser:
                        </p>

                        <p style="margin:0 0 30px;word-break:break-all;">
                            <a href="{{ $url }}"
                                style="color:#fb6340;font-size:13px;text-decoration:none;">
                                {{ $url }}
                            </a>
                        </p>

                        <table width="100%" cellpadding="0" cellspacing="0"
                            style="background:#fff7f5;border:1px solid #ffd9cf;border-radius:8px;">
                            <tr>
                                <td style="padding:18px;">

                                    <p style="margin:0;color:#d35400;font-size:14px;line-height:1.8;">
                                        Jika Anda tidak merasa melakukan permintaan reset PIN, abaikan email ini.
                                        Tidak ada perubahan pada akun Anda sampai PIN baru berhasil dibuat.
                                    </p>

                                </td>
                            </tr>
                        </table>

                    </td>
                </tr>

                <!-- Footer -->
                <tr>
                    <td style="padding:28px 35px;background:#fafafa;border-top:1px solid #ececec;text-align:center;">

                        <p style="margin:0;color:#344767;font-size:16px;font-weight:700;">
                            NOTARIS APP
                        </p>

                        <p style="margin:8px 0 0;color:#67748e;font-size:13px;line-height:1.7;">
                            Sistem Manajemen Notaris & PPAT
                        </p>

                        <p style="margin:18px 0 0;color:#98a2b3;font-size:12px;line-height:1.8;">
                            Email ini dikirim secara otomatis oleh sistem.
                            Mohon jangan membalas email ini karena alamat pengirim tidak menerima balasan.
                        </p>

                        <p style="margin:10px 0 0;color:#98a2b3;font-size:12px;">
                            © {{ date('Y') }} NOTARIS APP. Seluruh hak cipta dilindungi.
                        </p>

                    </td>
                </tr>

            </table>

        </td>
    </tr>
</table>

</body>

</html>