<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Email</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f8f9fa;
        }
        .container {
            background: white;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #3490dc;
            margin-bottom: 10px;
        }
        .title {
            color: #2d3748;
            font-size: 20px;
            margin-bottom: 20px;
        }
        .button {
            display: inline-block;
            background: #3490dc;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            margin: 20px 0;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
            font-size: 12px;
            color: #718096;
            text-align: center;
        }
        .link-fallback {
            word-break: break-all;
            color: #3490dc;
            margin: 10px 0;
            padding: 10px;
            background: #f7fafc;
            border-radius: 4px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">{{ config('app.name') }}</div>
        </div>
        
        <h1 class="title">Verifikasi Alamat Email Anda</h1>
        
        <p>Halo {{ $user->name }},</p>
        
        <p>Terima kasih telah mendaftar di {{ config('app.name') }}. Untuk mengaktifkan akun Anda dan mulai menggunakan layanan kami, silakan verifikasi alamat email Anda dengan mengklik tombol di bawah ini:</p>
        
        <div style="text-align: center;">
            <a href="{{ $verificationUrl }}" class="button">Verifikasi Email Saya</a>
        </div>
        
        <p>Jika tombol di atas tidak berfungsi, Anda dapat menyalin dan menempelkan link berikut ke browser Anda:</p>
        
        <div class="link-fallback">
            {{ $verificationUrl }}
        </div>
        
        <p><strong>Catatan Penting:</strong></p>
        <ul>
            <li>Link verifikasi ini akan kedaluwarsa dalam 60 menit</li>
            <li>Jika Anda tidak membuat akun ini, abaikan email ini</li>
            <li>Untuk keamanan, jangan bagikan link ini kepada orang lain</li>
        </ul>
        
        <div class="footer">
            <p>Email ini dikirim secara otomatis. Mohon jangan membalas email ini.</p>
            <p>© {{ date('Y') }} {{ config('app.name') }}. Semua hak dilindungi.</p>
            <p>{{ config('app.name') }}</p>
        </div>
    </div>
</body>
</html>