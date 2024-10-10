<?php

namespace App\Policies;

use App\Models\Snack;
use App\Models\User;

class SnackPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function update(User $user, Snack $snack) :bool
    {
        return $user->isAdmin() || $user->isManager() || $user->id === $snack->user_id;
    }

    public function delete(User $user, Snack $snack) :bool
    {
        return $user->isAdmin() || $user->isManager() || $user->id === $snack->user_id;
    }
}
