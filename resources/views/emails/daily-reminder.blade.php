<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daily Reminder</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f8fafc;
        }
        .container {
            background-color: white;
            border-radius: 12px;
            padding: 40px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo {
            font-size: 28px;
            font-weight: bold;
            color: #3b82f6;
            margin-bottom: 10px;
        }
        .title {
            font-size: 24px;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 20px;
        }
        .description {
            color: #6b7280;
            margin-bottom: 30px;
            font-size: 16px;
        }
        .highlight-box {
            background: linear-gradient(135deg, #fef3c7, #fde68a);
            border: 1px solid #f59e0b;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            text-align: center;
        }
        .highlight-text {
            font-size: 18px;
            font-weight: 600;
            color: #92400e;
            margin-bottom: 10px;
        }
        .highlight-subtext {
            color: #78350f;
            font-size: 14px;
        }
        .button {
            display: inline-block;
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            padding: 16px 32px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            margin: 20px 0;
            text-align: center;
            box-shadow: 0 4px 6px rgba(16, 185, 129, 0.25);
        }
        .button:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 8px rgba(16, 185, 129, 0.3);
        }
        .motivation {
            background-color: #f3f4f6;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            text-align: center;
        }
        .motivation-text {
            font-style: italic;
            color: #4b5563;
            font-size: 16px;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            color: #9ca3af;
            font-size: 14px;
        }
        .stats {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin: 20px 0;
        }
        .stat-item {
            text-align: center;
            padding: 15px;
            background-color: #f8fafc;
            border-radius: 8px;
        }
        .stat-number {
            font-size: 24px;
            font-weight: bold;
            color: #3b82f6;
        }
        .stat-label {
            font-size: 12px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">100ADayChallenge</div>
            <div class="title">⏰ Daily Reminder</div>
        </div>

        <div class="description">
            Hi there! It's <strong>{{ $today }}</strong> and it's time to log your daily reps.
            Don't let today slip away without tracking your progress!
        </div>

        <div class="highlight-box">
            <div class="highlight-text">🎯 Today's Goal</div>
            <div class="highlight-subtext">Log your reps and keep your streak alive!</div>
        </div>

        <div style="text-align: center;">
            <a href="{{ $loginUrl }}" class="button">
                📝 Log Today's Reps
            </a>
        </div>

        <div class="motivation">
            <div class="motivation-text">
                "The difference between try and triumph is just a little umph!"<br>
                <small>- Marvin Phillips</small>
            </div>
        </div>

        <div class="stats">
            <div class="stat-item">
                <div class="stat-number">7</div>
                <div class="stat-label">Days in a Week</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">30</div>
                <div class="stat-label">Days in a Month</div>
            </div>
        </div>

        <div class="footer">
            <p>This reminder was sent to {{ $email }}</p>
            <p>You're receiving this because you're tracking your daily progress</p>
            <p>&copy; {{ date('Y') }} 100ADayChallenge. Keep pushing forward! 💪</p>
        </div>
    </div>
</body>
</html>
