<?php 

namespace App\Filament\Resources\Helpers;

use App\Models\User;

class HelperFunctions
{
    public static $buffer;

    public static function isUser(User $user) :User {
        return $user;
    }
}