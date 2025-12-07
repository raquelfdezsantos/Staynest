<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Programar comandos de expiración de reservas (cada minuto para demo)
Schedule::command('reservations:send-expiration-reminders')->everyMinute();
Schedule::command('reservations:expire-pending')->everyMinute();
