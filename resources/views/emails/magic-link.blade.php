<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Magic Link</title>
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
        .button {
            display: inline-block;
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            color: white;
            padding: 16px 32px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            margin: 20px 0;
            text-align: center;
            box-shadow: 0 4px 6px rgba(59, 130, 246, 0.25);
        }
        .button:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 8px rgba(59, 130, 246, 0.3);
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            color: #9ca3af;
            font-size: 14px;
        }
        .warning {
            background-color: #fef3c7;
            border: 1px solid #f59e0b;
            border-radius: 8px;
            padding: 16px;
            margin: 20px 0;
            color: #92400e;
        }
        .warning strong {
            color: #78350f;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">100ADayChallenge</div>
            <div class="title">Your Magic Link is Here!</div>
        </div>

        <div class="description">
            Hi there! You've requested access to your 100ADayChallenge task tracker.
            Click the button below to securely log in to your account.
        </div>

        <div style="text-align: center;">
            <a href="{{ $loginUrl }}" class="button">
                🔐 Login to Task Tracker
            </a>
        </div>

        <div class="warning">
            <strong>Security Notice:</strong> This link will expire in 24 hours and can only be used once.
            If you didn't request this link, please ignore this email.
        </div>

        <div class="footer">
            <p>This magic link was sent to {{ $magicLink->email }}</p>
            <p>Link expires: {{ $magicLink->expires_at->format('M j, Y \a\t g:i A') }}</p>
            <p>&copy; {{ date('Y') }} 100ADayChallenge. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
