<?php

namespace App\Policies;

use App\Models\User;

class CategoryPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function viewAny(User $user) :bool
    {
        return $user->isManager() || $user->isAdmin();
    }

    public function create(User $user) :bool
    {
        return false;
    }

    public function delete(User $user) :bool
    {
        return false;
    }
}
