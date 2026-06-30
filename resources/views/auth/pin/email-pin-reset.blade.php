<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Reset PIN - WhoAmI</title>
</head>

<body style="margin:0;padding:0;background:#f4f7fb;font-family:'Segoe UI',Arial,sans-serif;">

<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="padding:40px 20px;">
    <tr>
        <td align="center">

            <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:14px;overflow:hidden;box-shadow:0 8px 24px rgba(0,0,0,.08);">

                <tr>
                    <td align="center" style="background:#fb6340;padding:28px;">
                        <h1 style="margin:0;color:#ffffff;font-size:28px;font-weight:700;">
                            WhoAmI
                        </h1>

                        <p style="margin:8px 0 0;color:#fff;font-size:15px;">
                            Aplikasi Manajemen Notaris & PPAT
                        </p>
                    </td>
                </tr>

                <tr>
                    <td style="padding:35px;">

                        <h2 style="margin:0 0 20px;color:#344767;">
                            Reset PIN
                        </h2>

                        <p style="margin:0 0 15px;color:#67748e;line-height:1.7;">
                            Halo <strong>{{ $user->name }}</strong>,
                        </p>

                        <p style="margin:0 0 15px;color:#67748e;line-height:1.7;">
                            Kami menerima permintaan untuk mereset PIN akun Anda.
                        </p>

                        <p style="margin:0 0 30px;color:#67748e;line-height:1.7;">
                            Klik tombol di bawah ini untuk membuat PIN baru.
                        </p>

                        <div style="text-align:center;">
                            <a href="{{ $url }}" style="display:inline-block;background:#fb6340;color:#ffffff;text-decoration:none;padding:14px 32px;border-radius:8px;font-weight:600;">
                                Reset PIN
                            </a>
                        </div>

                        <p style="margin:30px 0 10px;color:#67748e;line-height:1.7;">
                            Tautan ini berlaku selama <strong>60 menit</strong>.
                        </p>

                        <p style="margin:0;color:#67748e;line-height:1.7;">
                            Jika Anda tidak melakukan permintaan ini, abaikan email ini. PIN Anda tidak akan berubah.
                        </p>

                    </td>
                </tr>

                <tr>
                    <td style="padding:20px 35px;background:#f8f9fa;border-top:1px solid #e9ecef;">

                        <p style="margin:0;color:#8392ab;font-size:13px;">
                            Email ini dikirim secara otomatis oleh sistem.
                        </p>

                        <p style="margin:8px 0 0;color:#344767;font-weight:600;">
                            Tim WhoAmI
                        </p>

                    </td>
                </tr>

            </table>

        </td>
    </tr>
</table>

</body>
</html>