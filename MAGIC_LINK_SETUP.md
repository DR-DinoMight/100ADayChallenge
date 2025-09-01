# Magic Link Authentication Setup

This guide explains how to set up the magic link authentication system for your 100ADayChallenge application.

## 🔐 How It Works

The magic link system provides secure, passwordless access to your task tracker:

1. **Request Access**: Enter your email on the login page
2. **Receive Link**: A secure link is sent to your email
3. **Click to Login**: Click the link to automatically log in
4. **Secure Session**: Stay logged in for 24 hours

## 🚀 Quick Setup

### 1. Configure Your Email

Add your email address to the `.env` file:

```bash
AUTHORIZED_EMAIL=your-actual-email@example.com
```

**Important**: Replace `your-actual-email@example.com` with your real email address.

### 2. Configure Mail Settings

Ensure your mail settings are configured in `.env`:

```bash
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-username
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@example.com
MAIL_FROM_NAME="${APP_NAME}"
```

### 3. Test the System

1. Visit your application (it will redirect to `/login`)
2. Enter your authorized email address
3. Check your email for the magic link
4. Click the link to log in

## 🔒 Security Features

- **Email Verification**: Only authorized emails can request links
- **One-Time Use**: Each link can only be used once
- **24-Hour Expiry**: Links automatically expire after 24 hours
- **Session Expiry**: Logged-in sessions expire after 24 hours
- **Secure Tokens**: 64-character random tokens for each link

## 📧 Email Templates

The system sends beautifully formatted emails with:
- Clear branding and instructions
- Secure login button
- Expiry information
- Security warnings

## 🛠️ Customization

### Adding More Authorized Emails

To allow multiple people access, edit `config/auth.php`:

```php
'authorized_emails' => [
    env('AUTHORIZED_EMAIL', 'your-email@example.com'),
    'another-person@example.com',
    'team-member@example.com',
],
```

### Changing Link Expiry

Modify the expiry time in `app/Models/MagicLink.php`:

```php
'expires_at' => now()->addHours(24), // Change 24 to desired hours
```

### Customizing Email Content

Edit `resources/views/emails/magic-link.blade.php` to modify the email template.

## 🚨 Troubleshooting

### Links Not Being Sent

1. Check your mail configuration in `.env`
2. Verify the email address is in `authorized_emails`
3. Check your application logs for errors

### Can't Log In

1. Ensure the link hasn't expired (24 hours)
2. Check that you haven't already used the link
3. Verify your session hasn't expired

### Session Expiring Too Quickly

1. Check the session expiry time in the middleware
2. Ensure your server time is correct
3. Verify session configuration

## 🔄 Maintenance

### Clean Up Expired Links

The system automatically handles expired links, but you can clean up old records:

```bash
php artisan tinker
```

```php
// Delete expired magic links
\App\Models\MagicLink::where('expires_at', '<', now())->delete();

// Delete used magic links older than 7 days
\App\Models\MagicLink::where('used', true)
    ->where('created_at', '<', now()->subDays(7))
    ->delete();
```

## 📱 Mobile Friendly

The login page and emails are fully responsive and work great on mobile devices.

## 🎨 Styling

The system uses Tailwind CSS for consistent, modern styling that matches your application theme.

---

**Need Help?** Check the application logs or review the middleware and controller code for debugging information.
