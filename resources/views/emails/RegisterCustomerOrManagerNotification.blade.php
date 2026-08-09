<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="UTF-8" />
    <title>Welcome Notification</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap');

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f0f4f8;
            margin: 0;
            padding: 0;
            color: #374151;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 14px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            color: white;
            text-align: center;
            padding: 30px 20px;
            font-weight: 600;
            font-size: 28px;
            letter-spacing: 1.5px;
        }
        .content {
            padding: 30px 30px 40px 30px;
            line-height: 1.6;
        }
        .content h2 {
            margin-top: 0;
            font-weight: 600;
            font-size: 24px;
            color: #1f2937;
        }
        .welcome-message p {
            font-size: 16px;
            margin: 15px 0;
            color: #374151;
        }
        .account-details {
            background: #f9fafb;
            padding: 20px;
            border-radius: 6px;
            margin: 25px 0;
            border-left: 4px solid #7c3aed;
            color: #374151;
        }
        .account-details ul {
            list-style: none;
            padding-left: 0;
            margin: 0;
        }
        .account-details li {
            margin: 8px 0;
            font-weight: 500;
        }
        .account-details strong {
            color: #4f46e5;
        }
        p {
            font-size: 16px;
            margin: 15px 0;
            color: #374151;
        }
        .footer {
            text-align: center;
            font-size: 12px;
            color: #6b7280;
            padding: 20px;
            border-top: 1px solid #e5e7eb;
            user-select: none;
            background-color: #fafafa;
        }
        .cta-button {
            display: inline-block;
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            color: white !important;
            text-decoration: none;
            padding: 12px 28px;
            border-radius: 6px;
            margin: 20px 0;
            font-weight: 600;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }
        .cta-button:hover {
            background-color: #4338ca;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            {{ config('app.name') }}
        </div>

        <div class="content">
            <h2>Welcome Aboard, {{ $user->first_name }}! 👋</h2>

            <div class="welcome-message">
                <p>Thank you for registering with us on <strong>{{ $registerDate }}</strong>.</p>
                <p>We're excited to have you as part of our community!</p>
            </div>

            <div class="account-details">
                <p><strong>Your account details:</strong></p>
                <ul>
                    <li><strong>Name:</strong> {{ $user->first_name }} {{ $user->last_name }}</li>
                    <li><strong>Email:</strong> {{ $user->email }}</li>
                    <li><strong>Registration Date:</strong> {{ $registerDate }}</li>
                </ul>
            </div>

            <p>If you have any questions, feel free to contact our support team.</p>

            <p>Best regards,<br>The {{ config('app.name') }} Team</p>
        </div>

        <div class="footer">
            &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
        </div>
    </div>
</body>
</html>
