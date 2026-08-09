<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>You're Hired!</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap');
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f9fafb;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }
        h2 {
            color: #1f2937;
        }
        p {
            color: #4b5563;
            line-height: 1.6;
        }
        .info-box {
            background-color: #e0f2fe;
            padding: 15px;
            border-left: 5px solid #0284c7;
            margin: 20px 0;
            border-radius: 6px;
        }
        .footer {
            font-size: 12px;
            color: #9ca3af;
            text-align: center;
            margin-top: 30px;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Hello {{ $user->first_name }}, 👋</h2>
    <p>Congratulations! You have been officially hired at <strong>{{ $appName }}</strong>.</p>

    <div class="info-box">
        <p><strong>Your Login Email:</strong> {{ $user->email }}</p>
        <p><strong>Your Temporary Password:</strong> {{ $password }}</p>
    </div>

    <p>Please change your password after your first login for security reasons.</p>

    <p>You can start working at the restaurant <strong>on or after {{ \Carbon\Carbon::parse($startDate)->addDay()->format('F j, Y') }}</strong>.</p>
    <p>If needed, you can also join within a week from the hire date: <strong>{{ \Carbon\Carbon::parse($startDate)->addWeek()->format('F j, Y') }}</strong>.</p>

    <p>We look forward to working with you!</p>

    <p>— The {{ $appName }} Team</p>

    <div class="footer">
        &copy; {{ date('Y') }} {{ $appName }}. All rights reserved.
    </div>
</div>
</body>
</html>
