<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Account Deleted</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap');
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f9fafb;
            margin: 0;
            padding: 0;
            color: #1f2937;
        }
        .container {
            max-width: 600px;
            margin: 50px auto;
            background-color: #fff;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.05);
        }
        .header {
            background: linear-gradient(to right, #f43f5e, #ec4899);
            padding: 25px;
            text-align: center;
            color: white;
            border-radius: 10px 10px 0 0;
            font-size: 24px;
            font-weight: bold;
        }
        .content {
            padding: 20px;
        }
        .content p {
            line-height: 1.6;
        }
        .footer {
            font-size: 12px;
            text-align: center;
            color: #9ca3af;
            margin-top: 30px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            {{ $appName }}
        </div>
        <div class="content">
            <h2>Goodbye {{ $user->first_name }} 👋</h2>
            <p>We’re sorry to see you go. Your account was deleted on:</p>
            <p><strong>{{ $deletedAt }}</strong></p>

            <p>If you didn’t request this or believe this was a mistake, please contact us immediately.</p>

            <p>Thank you for being part of {{ $appName }}. We hope to see you again someday.</p>

            <p>— The {{ $appName }} Team</p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} {{ $appName }}. All rights reserved.
        </div>
    </div>
</body>
</html>
