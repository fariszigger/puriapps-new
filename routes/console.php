<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

use Illuminate\Support\Facades\Schedule;
use App\Models\User;

Schedule::call(function () {
    User::where('is_online', '1')
        ->where('last_activity_at', '<', now()->subMinutes(20))
        ->update(['is_online' => '0']);
})->everyMinute();
