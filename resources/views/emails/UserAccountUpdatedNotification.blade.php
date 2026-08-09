<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="UTF-8" />
    <title>Account Updated</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap');

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f3f4f6;
            margin: 0;
            padding: 0;
            color: #111827;
        }

        .email-container {
            max-width: 600px;
            margin: 40px auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
        }

        .email-header {
            background: linear-gradient(135deg, #3b82f6, #6366f1);
            color: #fff;
            text-align: center;
            padding: 32px 24px;
            font-size: 28px;
            font-weight: 600;
        }

        .email-body {
            padding: 30px;
            line-height: 1.7;
        }

        .email-body h2 {
            font-size: 22px;
            margin-top: 0;
        }

        .email-body p {
            font-size: 16px;
            margin: 16px 0;
        }

        .info-box {
            background-color: #eff6ff;
            border-left: 6px solid #3b82f6;
            padding: 18px;
            border-radius: 8px;
            margin: 24px 0;
            font-size: 16px;
            color: #1e40af;
        }

        .email-footer {
            font-size: 13px;
            text-align: center;
            color: #9ca3af;
            padding: 20px;
            border-top: 1px solid #e5e7eb;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            {{ $appName }}
        </div>

        <div class="email-body">
            <h2>Hello {{ $user->first_name }} 👋</h2>

            <p>We wanted to let you know that your account information was successfully updated. 🔄</p>

            <div class="info-box">
                Your profile details were changed on {{ now()->format('F j, Y \a\t g:i A') }}.
            </div>

            <p>If you didn’t make this change, please contact our support team immediately to secure your account.</p>

            <p>Thank you for keeping your information up-to-date. 😊</p>

            <p>— The {{ $appName }} Team</p>
        </div>

        <div class="email-footer">
            &copy; {{ date('Y') }} {{ $appName }}. All rights reserved.
        </div>
    </div>
</body>
</html>
