<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Send task deadline reminders daily at 8 AM
Schedule::command('tasks:send-deadline-reminders')
    ->dailyAt('08:00')
    ->description('Send task deadline reminder notifications');
