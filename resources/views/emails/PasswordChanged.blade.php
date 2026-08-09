<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="UTF-8" />
    <title>Password Changed</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap');
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f0f4f8;
            margin: 0;
            padding: 0;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            color: #333;
        }
        .email-container {
            max-width: 600px;
            margin: 40px auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 14px rgba(0,0,0,0.1);
        }
        .email-header {
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            color: #fff;
            text-align: center;
            padding: 30px 20px;
            font-weight: 600;
            font-size: 28px;
            letter-spacing: 1.5px;
        }
        .email-body {
            padding: 30px 30px 40px 30px;
            line-height: 1.6;
        }
        .email-body h2 {
            margin-top: 0;
            font-weight: 600;
            font-size: 24px;
            color: #1f2937;
        }
        .email-body p {
            font-size: 16px;
            margin: 15px 0;
            color: #374151;
        }
        .success-box {
            font-size: 20px;
            font-weight: 600;
            text-align: center;
            padding: 22px;
            background-color: #ecfdf5;
            border: 1px solid #10b981;
            color: #065f46;
            border-radius: 6px;
            margin: 30px 0;
        }
        .note {
            font-size: 14px;
            color: #6b7280;
            margin-top: 15px;
            line-height: 1.4;
        }
        .email-footer {
            text-align: center;
            font-size: 12px;
            color: #9ca3af;
            padding: 20px;
            border-top: 1px solid #e5e7eb;
            user-select: none;
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

            <p>We wanted to let you know that your password has been successfully changed.</p>

            <div class="success-box">
                Your password was updated successfully 🔐
            </div>

            <p class="note">If you didn't make this change, please contact our support team immediately.</p>

            <p>— The {{ $appName }} Team</p>
        </div>

        <div class="email-footer">
            &copy; {{ date('Y') }} {{ $appName }}. All rights reserved.
        </div>
    </div>
</body>
</html>
