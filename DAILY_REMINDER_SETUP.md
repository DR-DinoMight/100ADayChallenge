# Daily Reminder System Setup

This guide explains how to set up and use the daily reminder system for your 100ADayChallenge application.

## 🔔 What It Does

The daily reminder system automatically sends you an email every day at 7:00 PM to remind you to log your daily reps. This helps maintain consistency and build the habit of daily tracking.

## 🚀 How It Works

1. **Scheduled Task**: Runs automatically every day at 7:00 PM
2. **Email Reminder**: Sends a beautiful, motivational email
3. **Direct Link**: Includes a button to log in and track reps
4. **Smart Targeting**: Only sends to authorized email addresses

## ⚙️ Setup Requirements

### 1. Email Configuration

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

### 2. Authorized Email

Make sure your email is set in `.env`:

```bash
AUTHORIZED_EMAIL=matt@deloughry.co.uk
```

## 🕐 Scheduling

The reminder is scheduled to run every day at 7:00 PM (19:00) using Laravel's task scheduler.

### Manual Testing

You can test the system manually by running:

```bash
php artisan reminder:daily
```

### View Scheduled Tasks

Check what's scheduled:

```bash
php artisan schedule:list
```

Expected output:
```
0 19 * * *  php artisan reminder:daily .................. Next Due: X hours from now
```

## 📧 Email Features

### Beautiful Design
- Professional, mobile-friendly layout
- Branded with your app name
- Motivational quotes and encouragement

### Content Includes
- Today's date
- Direct login button
- Motivational message
- Progress tracking reminder

### Email Subject
```
⏰ Daily Reminder: Log Your Reps!
```

## 🔧 Production Setup

### For Shared Hosting

If you're on shared hosting, you'll need to set up a cron job to run Laravel's scheduler:

```bash
# Add this to your crontab (crontab -e)
* * * * * cd /path/to/your/project && php artisan schedule:run >> /dev/null 2>&1
```

### For VPS/Dedicated Servers

Set up a system cron job:

```bash
# Edit crontab
crontab -e

# Add this line
* * * * * cd /path/to/your/project && php artisan schedule:run >> /dev/null 2>&1
```

### For Laravel Forge/Envoyer

These platforms automatically handle Laravel scheduling - no additional setup needed.

## 🧪 Testing the System

### 1. Test Command
```bash
php artisan reminder:daily
```

### 2. Check Email
- Look for the reminder email in your inbox
- Verify the login button works
- Check the email formatting

### 3. Test Scheduling
```bash
php artisan schedule:run
```

## 🎯 Customization

### Change Reminder Time

Edit `routes/console.php`:

```php
Schedule::command('reminder:daily')
    ->dailyAt('20:00')  // Change to 8:00 PM
    ->description('Send daily reminder emails to log reps')
    ->withoutOverlapping();
```

### Modify Email Content

Edit `resources/views/emails/daily-reminder.blade.php` to customize:
- Colors and styling
- Motivational messages
- Layout and branding

### Add More Reminders

Create additional scheduled commands:

```php
// Morning reminder
Schedule::command('reminder:morning')
    ->dailyAt('08:00')
    ->description('Send morning motivation');

// Weekly summary
Schedule::command('reminder:weekly')
    ->weekly()
    ->mondays()
    ->at('09:00')
    ->description('Send weekly progress summary');
```

## 🚨 Troubleshooting

### Emails Not Being Sent

1. **Check Mail Configuration**
   - Verify SMTP settings in `.env`
   - Test with `php artisan tinker`:
   ```php
   Mail::raw('Test', function($message) {
       $message->to('your-email@example.com')->subject('Test');
   });
   ```

2. **Check Scheduler**
   - Verify cron job is running
   - Check `php artisan schedule:list`
   - Test manually with `php artisan reminder:daily`

3. **Check Logs**
   - Review Laravel logs in `storage/logs/`
   - Check system cron logs

### Wrong Time Zone

Ensure your server timezone is correct:

```bash
# Check current timezone
date

# Set timezone if needed
sudo timedatectl set-timezone Europe/London
```

### Multiple Emails

The system prevents overlapping executions with `->withoutOverlapping()`, but if you're getting multiple emails, check:
- Multiple cron jobs
- Multiple server instances
- Timezone mismatches

## 📱 Mobile Experience

The reminder emails are fully responsive and look great on:
- Mobile phones
- Tablets
- Desktop email clients

## 🔒 Security

- Only sends to authorized emails
- Uses secure SMTP connections
- No sensitive data in emails
- Login links expire after 24 hours

## 📊 Monitoring

### Check Last Run
```bash
php artisan schedule:list
```

### View Command History
```bash
# Check if command has been run recently
php artisan reminder:daily --help
```

### Log Monitoring
Monitor your application logs for any scheduling errors:
```bash
tail -f storage/logs/laravel.log
```

---

**Need Help?** The daily reminder system is designed to be simple and reliable. If you encounter issues, check your mail configuration and cron job setup first.
