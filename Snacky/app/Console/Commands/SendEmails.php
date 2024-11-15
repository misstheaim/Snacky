<?php

namespace App\Console\Commands;

use App\Models\Notification;
use App\Notifications\SnackNotification;
use DateInterval;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class SendEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:notify';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send notifications via mail';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $delay = 0;
        $currentDate = now();
        $periodInterval = DateInterval::createFromDateString('1 minute');
        $startDate = $currentDate->sub($periodInterval);                                                                                      // Uncomment this if you want to specify which email box to use //
        $notifycations = Notification::where('sended', false)->where('updated_at', '<', $startDate)->with('user')->with('snack')/* ->whereHas('user', fn ($query) => $query->whereRaw('SUBSTRING_INDEX(email,\'@\',-1) = "gmail.com"')) */->get();
        foreach($notifycations as $notifycation) {
            $notifycation->user->notify((new SnackNotification($notifycation))->delay(now()->addSeconds($delay)));
            $notifycation->sended = true;
            $notifycation->save();
            $delay += 5;
        }
        Artisan::call('queue:work --stop-when-empty');
    }
}
