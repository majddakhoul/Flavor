<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="UTF-8" />
    <title>Your Reservation Code</title>
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
            overflow: hidden;
            box-shadow: 0 4px 14px rgba(0,0,0,0.1);
        }
        .email-header {
            background: linear-gradient(135deg, #16a34a, #22c55e);
            color: #fff;
            text-align: center;
            padding: 30px 20px;
            font-weight: 600;
            font-size: 26px;
            letter-spacing: 1.2px;
        }
        .email-body {
            padding: 30px;
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
            letter-spacing: 6px;
            padding: 20px 0;
            background-color: #f9fafb;
            border-radius: 6px;
            margin: 30px 0;
            color: #1f2937;
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
        }
    </style>
</head>
<body>
<div class="email-container">
    <div class="email-header">
        {{ config('app.name') }} - Reservation
    </div>

    <div class="email-body">
        
        <p>Thank you for making a reservation with us. Here is your unique reservation code:</p>

        <div class="otp-box">
            {{ $reservation->reservation_code }}
        </div>

        <p class="note">Keep this code safe. You may need it to confirm or manage your reservation.</p>
        <p class="note">If you did not make this reservation, you can safely ignore this email.</p>
    </div>

    <div class="email-footer">
        &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
    </div>
</div>
</body>
</html>
