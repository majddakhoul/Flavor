<!DOCTYPE html>
<html lang="en" >
<head>
    <meta charset="UTF-8" />
    <title>Login Alert</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap');

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f0f4f8;
            margin: 0;
            padding: 0;
            color: #333;
        }

        .email-container {
            max-width: 600px;
            margin: 40px auto;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 14px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .email-header {
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            color: #fff;
            text-align: center;
            padding: 30px 20px;
        }

        .email-header h1 {
            margin: 0;
            font-weight: 600;
            font-size: 2.4rem;
        }

        .email-body {
            padding: 30px 25px;
        }

        .email-body h2 {
            margin-top: 0;
            font-weight: 600;
            font-size: 22px;
            color: #1f2937;
        }

        .login-details {
            background-color: #f9fafb;
            border-radius: 6px;
            border-left: 4px solid #4f46e5;
            padding: 20px;
            margin: 25px 0;
            color: #1f2937;
        }

        .login-details ul {
            list-style: none;
            margin: 0;
            padding-left: 0;
        }

        .login-details li {
            margin-bottom: 12px;
            font-size: 16px;
        }

        .login-details li strong {
            color: #4f46e5;
        }

        .warning {
            background-color: #fef2f2;
            border: 1px solid #fca5a5;
            color: #b91c1c;
            padding: 15px;
            border-radius: 6px;
            margin-top: 20px;
            font-size: 14px;
            font-weight: 500;
        }

        .signature {
            margin-top: 30px;
            font-size: 14px;
            color: #4b5563;
            line-height: 1.6;
        }

        .email-footer {
            text-align: center;
            font-size: 12px;
            color: #6b7280;
            padding: 20px;
            border-top: 1px solid #e5e7eb;
            background-color: #f9fafb;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <h1>{{ $appName ?? 'Flavor' }}</h1>
        </div>

        <div class="email-body">
            <h2>👋 Hello {{ $user->first_name }}!</h2>
            <p>We're letting you know that a login to your account was made on:</p>

            <div class="login-details">
                <ul>
                    <li><strong>🕒 Time:</strong> {{ $loginTime }}</li>
                    <li><strong>👤 Name:</strong> {{ $user->first_name }} {{ $user->last_name }}</li>
                    <li><strong>📧 Email:</strong> {{ $user->email }}</li>
                </ul>
            </div>

            <div class="warning">
                ⚠️ If this wasn't you, please <strong>change your password immediately</strong> to secure your account.
            </div>

            <p class="signature">
                Thanks for staying with <strong>{{ $appName ?? 'Flavor' }}</strong>!<br/>
                Best regards,<br/>
                The {{ $appName ?? 'Flavor' }} Team
            </p>
        </div>

        <div class="email-footer">
            &copy; {{ date('Y') }} {{ $appName ?? 'Flavor' }}. All rights reserved.
        </div>
    </div>
</body>
</html>
