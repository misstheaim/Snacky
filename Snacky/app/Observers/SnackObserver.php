<?php

namespace App\Observers;

use App\Filament\Resources\Helpers\HelperFunctions;
use App\Models\Notification;
use App\Models\Snack;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class SnackObserver
{
    /**
     * Handle the Snack "created" event.
     */
    public function created(Snack $snack): void
    {
        if (HelperFunctions::isUser(Auth::user())->isDev()) {
            $managers = User::whereHas('roles', fn (Builder $query) => $query->where('role', config('app.manager_role')))->get();
            foreach($managers as $manager) {
                Notification::create([
                    'user_id' => $manager->id,
                    'snack_id' => $snack->id,
                    'type' => 'SUBMISSION'
                ]);
            }
        }
    }

    /**
     * Handle the Snack "updated" event.
     */
    public function updating(Snack $snack): void
    {
        $old_status = $snack->getOriginal('status');
        if ($old_status !== $snack->status) {
            $status = match ($snack->status) {
                'APPROVED' => 'APPROVED',
                'DISAPPROVED' => 'REJECTED',
                default => false
            };

            if ($status) {
                Notification::whereIn('type', ['APPROVED', 'REJECTED',])->updateOrCreate(
                    [
                        'user_id' => $snack->user->id,
                        'snack_id' => $snack->id,
                    ],
                    [
                        'type' => $status,
                        'sended' => false,
                    ]
                    );
            } else {
                Notification::where('user_id', $snack->user->id)->where('snack_id', $snack->id)->whereIn('type', ['APPROVED', 'REJECTED',])->delete();
            }

            
        }
    }

    /**
     * Handle the Snack "deleted" event.
     */
    public function deleted(Snack $snack): void
    {
        //
    }

    /**
     * Handle the Snack "restored" event.
     */
    public function restored(Snack $snack): void
    {
        //
    }

    /**
     * Handle the Snack "force deleted" event.
     */
    public function forceDeleted(Snack $snack): void
    {
        //
    }
}