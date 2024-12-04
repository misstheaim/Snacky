<?php

namespace App\Policies;

use Illuminate\Contracts\Auth\Authenticatable;
use Parallax\FilamentComments\Models\FilamentComment;

class FilamentCommentPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
    }

    public function viewAny(Authenticatable $user): bool
    {
        return true;
    }

    public function view(Authenticatable $user, FilamentComment $filamentComment): bool
    {
        return true;
    }

    public function create(Authenticatable $user): bool
    {
        return true;
    }

    public function update($user, FilamentComment $filamentComment): bool
    {
        /** @phpstan-ignore property.notFound */
        return $user->id === $filamentComment->user_id;
    }

    public function delete($user, FilamentComment $filamentComment): bool
    {
        /** @phpstan-ignore property.notFound */
        return $user->isAdmin() || $user->isManager() || $user->id === $filamentComment->user_id;
    }

    public function deleteAny($user): bool
    {
        return $user->isAdmin() || $user->isManager();
    }

    public function restore($user, FilamentComment $filamentComment): bool
    {
        /** @phpstan-ignore property.notFound */
        return $user->isAdmin() || $user->isManager() || $user->id === $filamentComment->user_id;
    }

    public function restoreAny($user): bool
    {
        return $user->isAdmin() || $user->isManager();
    }

    public function forceDelete($user, FilamentComment $filamentComment): bool
    {
        /** @phpstan-ignore property.notFound */
        return $user->isAdmin() || $user->isManager() || $user->id === $filamentComment->user_id;
    }

    public function forceDeleteAny($user): bool
    {
        return $user->isAdmin() || $user->isManager();
    }
}
