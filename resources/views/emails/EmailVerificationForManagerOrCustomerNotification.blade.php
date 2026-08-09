<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="UTF-8" />
    <title>Verify Your Email</title>
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
        .otp-box {
            font-size: 28px;
            font-weight: 600;
            text-align: center;
            letter-spacing: 8px;
            padding: 22px 0;
            background-color: #f9fafb;
            border-radius: 6px;
            margin: 30px 0;
            color: #1f2937;
            user-select: all;
            border: 1px solid #d1d5db;
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
        .button {
            display: inline-block;
            background-color: #4f46e5;
            color: #ffffff !important;
            padding: 12px 28px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            font-size: 16px;
            margin-top: 25px;
            transition: background-color 0.3s ease;
        }
        .button:hover {
            background-color: #4338ca;
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

            <p>{{ $customMessage }}</p>

            <div class="otp-box">
                {{ $otp }}
            </div>

            <p class="note">This code is valid for 60 minutes. Do not share it with anyone.</p>
            <p class="note">If you did not request this code, please ignore this email.</p>

            <!-- Example button if needed -->
            <!-- <a href="#" class="button">Verify Now</a> -->
        </div>

        <div class="email-footer">
            &copy; {{ date('Y') }} {{ $appName }}. All rights reserved.
        </div>
    </div>
</body>
</html>
