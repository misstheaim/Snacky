<?php

namespace App\Console\Commands;

use App\Models\Notification;
use App\Notifications\SnackNotification;
use Illuminate\Console\Command;

class SendCommentsNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:com_notify';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        // Uncomment this if you want to specify which email box to use //
        $notifycations = Notification::where('type', 'COMMENTED')->where('sended', false)->with('user')->with(['snack' => fn ($query) => $query->with('user')])->whereHas('user', fn ($query) => $query->whereRaw('SUBSTRING_INDEX(email,\'@\',-1) = "gmail.com" or SUBSTRING_INDEX(email,\'@\',-1) = "ventionteams.com"'))->get();
        $notifyToSend = [];
        foreach ($notifycations as $notifycation) {
            if (array_key_exists($notifycation->user_id, $notifyToSend)) {
                $notifyToSend[$notifycation->user_id]->count += 1;
                if (! in_array($notifycation->snack->id, $notifyToSend[$notifycation->user_id]->snacks)) {
                    $notifyToSend[$notifycation->user_id]->snacks[] = $notifycation->snack->id;
                }
            } else {
                $notifyToSend[$notifycation->user_id] = (object) [
                    'user' => $notifycation->user,
                    'count' => 1,
                    'type' => 'COMMENTED',
                    'snacks' => [$notifycation->snack->id],
                ];
            }
            $notifycation->sended = true;
            $notifycation->save();
        }
        $delay = 0;
        foreach ($notifyToSend as $notification) {
            $notification->user->notify((new SnackNotification($notification))->delay(now()->addSeconds($delay)));
            $delay += 5;
        }
    }
}
