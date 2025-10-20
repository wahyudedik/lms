<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Email - Laravel LMS</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f8fafc;
        }

        .container {
            background: white;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #3b82f6;
            margin-bottom: 10px;
        }

        .title {
            font-size: 20px;
            color: #1f2937;
            margin-bottom: 20px;
        }

        .content {
            margin-bottom: 30px;
        }

        .button {
            display: inline-block;
            background-color: #3b82f6;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 500;
            margin: 20px 0;
        }

        .button:hover {
            background-color: #2563eb;
        }

        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            color: #6b7280;
            font-size: 14px;
        }

        .warning {
            background-color: #fef3c7;
            border: 1px solid #f59e0b;
            border-radius: 6px;
            padding: 15px;
            margin: 20px 0;
            color: #92400e;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <div class="logo">📚 Laravel LMS</div>
            <h1 class="title">Verifikasi Email Anda</h1>
        </div>

        <div class="content">
            <p>Halo <strong>{{ $user->name }}</strong>!</p>

            <p>Terima kasih telah mendaftar di Laravel LMS. Sebelum dapat menggunakan akun Anda, silakan verifikasi
                alamat email Anda.</p>

            <p>Klik tombol di bawah ini untuk memverifikasi email Anda:</p>

            <div style="text-align: center;">
                <a href="{{ $verificationUrl }}" class="button">Verifikasi Email</a>
            </div>

            <div class="warning">
                <strong>⚠️ Penting:</strong> Link verifikasi akan berlaku selama 60 menit. Jika link sudah expired,
                silakan request verifikasi email baru.
            </div>

            <p>Jika Anda tidak dapat mengklik tombol di atas, salin dan tempel URL berikut ke browser Anda:</p>
            <p
                style="word-break: break-all; background-color: #f3f4f6; padding: 10px; border-radius: 4px; font-family: monospace;">
                {{ $verificationUrl }}
            </p>

            <p>Jika Anda tidak membuat akun ini, Anda dapat mengabaikan email ini.</p>
        </div>

        <div class="footer">
            <p>Email ini dikirim dari Laravel LMS</p>
            <p>© {{ date('Y') }} Laravel LMS. All rights reserved.</p>
        </div>
    </div>
</body>

</html>
