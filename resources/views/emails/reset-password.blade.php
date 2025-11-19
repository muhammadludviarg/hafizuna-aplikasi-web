<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Hafizuna</title>
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

        .reset-button {
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

        .reset-button:hover {
            transform: translateY(-2px);
        }

        .divider {
            border-top: 1px solid #e5e7eb;
            margin: 30px 0;
        }

        .alternative-link {
            background-color: #f9fafb;
            padding: 20px;
            border-radius: 8px;
            margin-top: 20px;
            font-size: 13px;
        }

        .alternative-link p {
            margin: 0 0 10px 0;
            color: #6b7280;
        }

        .url-text {
            word-break: break-all;
            color: #059669;
            font-size: 12px;
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

        .footer-links {
            margin-top: 15px;
        }

        .footer-links a {
            color: #059669;
            text-decoration: none;
            margin: 0 10px;
        }
    </style>
</head>

<body>
    <div class="email-container">
        <!-- Header -->
        <div class="email-header">
            <div class="logo">🕌 HAFIZUNA</div>
            <div class="tagline">Manajemen Hafalan Terpadu</div>
        </div>

        <!-- Body -->
        <div class="email-body">
            <div class="greeting">Assalamu'alaikum,</div>

            <div class="message">
                <p>Kami menerima permintaan untuk mereset password akun Anda di sistem Hafizuna.</p>

                <p>Silakan klik tombol di bawah ini untuk membuat password baru:</p>
            </div>

            <div class="button-container">
                <a href="{{ $url }}" class="reset-button">
                    🔐 Reset Password
                </a>
            </div>

            <div class="alternative-link">
                <p><strong>Link tidak berfungsi?</strong></p>
                <p>Salin dan tempel URL berikut ke browser Anda:</p>
                <p class="url-text">{{ $url }}</p>
            </div>

            <div class="warning">
                <strong>⚠️ Penting:</strong> Link reset password ini akan kadaluarsa dalam <strong>60 menit</strong>.
                Jika Anda tidak meminta reset password, abaikan email ini dan password Anda tidak akan berubah.
            </div>

            <div class="divider"></div>

            <p style="color: #6b7280; font-size: 14px;">
                <strong>Tips Keamanan:</strong><br>
                • Jangan bagikan link ini kepada siapa pun<br>
                • Gunakan password yang kuat dan unik<br>
                • Kombinasikan huruf besar, kecil, angka, dan simbol
            </p>
        </div>

        <!-- Footer -->
        <div class="email-footer">
            <p>Email ini dikirim otomatis oleh sistem Hafizuna.</p>
            <p>Jika Anda mengalami kesulitan, hubungi administrator.</p>

            <div class="footer-links">
                <a href="#">Bantuan</a> |
                <a href="#">Kebijakan Privasi</a> |
                <a href="#">Syarat & Ketentuan</a>
            </div>

            <p style="margin-top: 20px; color: #9ca3af;">
                © {{ date('Y') }} Hafizuna. All rights reserved.
            </p>
        </div>
    </div>
</body>

</html>