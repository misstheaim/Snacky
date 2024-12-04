<?php

namespace App\Observers;

use App\Models\CustomFilamentComment;
use App\Models\Notification;

class FilamentCommentObserver
{
    /**
     * Handle the FilamentComment "created" event.
     */
    public function created(CustomFilamentComment $filamentComment): void
    {
        /** @var \App\Models\Snack $snack */
        $snack = $filamentComment->subject;
        $managers = $snack->approvedByUser()->get();
        foreach ($managers as $manager) {
            Notification::create([
                'user_id' => $manager->id,
                'snack_id' => $snack->id,
                'comment_id' => $filamentComment->id,
                'type' => 'COMMENTED',
            ]);
        }
    }

    /**
     * Handle the FilamentComment "updated" event.
     */
    public function updated(CustomFilamentComment $filamentComment): void
    {
        //
    }

    /**
     * Handle the FilamentComment "deleted" event.
     */
    public function deleted(CustomFilamentComment $filamentComment): void
    {
        //
    }

    /**
     * Handle the FilamentComment "restored" event.
     */
    public function restored(CustomFilamentComment $filamentComment): void
    {
        //
    }

    /**
     * Handle the FilamentComment "force deleted" event.
     */
    public function forceDeleted(CustomFilamentComment $filamentComment): void
    {
        //
    }
}
