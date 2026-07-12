<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('presensi:mark-alpha')->dailyAt('00:05');
Schedule::command('presensi:mark-absent')->dailyAt('00:10');
Schedule::command('cuti:reset-bulanan')->monthlyOn(1, '00:01');
