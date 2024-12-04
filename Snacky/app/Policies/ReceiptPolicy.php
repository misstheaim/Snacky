<?php

namespace App\Policies;

use App\Models\User;

class ReceiptPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function viewAny(User $user): bool
    {
        return $user->isManager() || $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isManager() || $user->isAdmin();
    }

    public function delete(User $user): bool
    {
        return $user->isManager() || $user->isAdmin();
    }

    public function update(User $user): bool
    {
        return $user->isManager() || $user->isAdmin();
    }
}
