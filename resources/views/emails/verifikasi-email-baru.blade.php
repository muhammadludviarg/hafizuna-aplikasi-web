<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Email - Hafizuna</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f3f4f6;
            margin: 0;
            padding: 0;
        }

        .email-container {
            max-width: 600px;
            margin: 40px auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .email-header {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            color: #ffffff;
            padding: 40px 30px;
            text-align: center;
        }

        .logo {
            font-size: 32px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .tagline {
            font-size: 14px;
            opacity: 0.9;
        }

        .email-body {
            padding: 40px 30px;
            color: #374151;
        }

        .greeting {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 20px;
            color: #1f2937;
        }

        .message {
            line-height: 1.6;
            margin-bottom: 30px;
            font-size: 15px;
        }

        .button-container {
            text-align: center;
            margin: 30px 0;
        }

        .action-button {
            display: inline-block;
            padding: 14px 32px;
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            box-shadow: 0 4px 6px rgba(5, 150, 105, 0.3);
            transition: transform 0.2s;
        }

        .action-button:hover {
            transform: translateY(-2px);
        }

        .warning {
            background-color: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 15px;
            margin-top: 20px;
            border-radius: 4px;
            font-size: 14px;
            color: #92400e;
        }

        .email-footer {
            background-color: #f9fafb;
            padding: 30px;
            text-align: center;
            font-size: 13px;
            color: #6b7280;
        }
    </style>
</head>

<body>
    <div class="email-container">
        <div class="email-header">
            <div class="logo">HAFIZUNA</div>
            <div class="tagline">Manajemen Hafalan Terpadu</div>
        </div>
        <div class="email-body">
            <div class="greeting">Assalamu'alaikum {{ $user->nama_lengkap ?? '' }},</div>
            <div class="message">
                <p>Kami menerima permintaan untuk mengganti alamat email akun Anda di sistem Hafizuna menjadi
                    <strong>{{ $emailBaru }}</strong>.</p>
                <p>Untuk mengonfirmasi perubahan ini, silakan klik tombol di bawah ini:</p>
            </div>
            <div class="button-container">
                <a href="{{ $url }}" class="action-button">✔️ Verifikasi Email Baru</a>
            </div>
            <div class="warning">
                <strong>⚠️ Penting:</strong> Link verifikasi ini akan kadaluarsa dalam <strong>10 menit</strong>. Jika
                Anda tidak merasa meminta perubahan email, abaikan pesan ini. Akun Anda tetap aman.
            </div>
        </div>
        <div class="email-footer">
            <p>Email ini dikirim otomatis oleh sistem Hafizuna.</p>
            <p>© {{ date('Y') }} Hafizuna. All rights reserved.</p>
        </div>
    </div>
</body>

</html>