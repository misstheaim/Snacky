<?php

use App\Console\Commands\SendCommentsNotification;
use App\Console\Commands\SendEmails;
use Illuminate\Support\Facades\Schedule;

Schedule::command(SendEmails::class)->everyFiveMinutes();

Schedule::command('queue:work --stop-when-empty')->everyFiveMinutes();

Schedule::command(SendCommentsNotification::class)->hourly();

Schedule::command('telescope:prune --hours=48')->daily();
