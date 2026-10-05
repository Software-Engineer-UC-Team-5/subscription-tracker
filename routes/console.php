<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Scheduler Pengingat Otomatis (UC12 / FR-006 / NFR-003: berjalan setiap menit)
Illuminate\Support\Facades\Schedule::command('app:check-reminders')->everyMinute();

