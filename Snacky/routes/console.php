<?php

use App\Console\Commands\SendEmails;
use Illuminate\Support\Facades\Schedule;

Schedule::command(SendEmails::class)->everyFiveMinutes();

Schedule::command('queue:work --stop-when-empty')->everyFiveMinutes();
