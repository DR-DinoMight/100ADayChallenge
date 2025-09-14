<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule daily reminder emails at 7 PM
Schedule::command('reminder:daily')
    ->dailyAt('19:00')
    ->description('Send daily reminder emails to log reps')
    ->withoutOverlapping();

// Schedule magic link cleanup every hour
Schedule::command('magic-links:cleanup')
    ->hourly()
    ->description('Clean up expired and blocked magic links')
    ->withoutOverlapping();
