<?php

namespace App\Console\Commands;

use App\Mail\DailyReminderMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;

class SendDailyReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminder:daily';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send daily reminder emails to log today\'s reps';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Sending daily reminder emails...');

        // Get authorized emails from config
        $authorizedEmails = Config::get('auth.authorized_emails', []);

        if (empty($authorizedEmails)) {
            $this->error('No authorized emails configured. Please set AUTHORIZED_EMAIL in your .env file.');
            return 1;
        }

        $sentCount = 0;
        $failedCount = 0;

        foreach ($authorizedEmails as $email) {
            try {
                Mail::to($email)->send(new DailyReminderMail($email));
                $this->info("✅ Reminder sent to: {$email}");
                $sentCount++;
            } catch (\Exception $e) {
                $this->error("❌ Failed to send reminder to {$email}: " . $e->getMessage());
                $failedCount++;
            }
        }

        $this->info("\n📧 Daily reminders completed:");
        $this->info("   Sent: {$sentCount}");
        $this->info("   Failed: {$failedCount}");

        return $sentCount > 0 ? 0 : 1;
    }
}
