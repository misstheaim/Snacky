<?php

namespace App\TwoFA;

use Illuminate\Support\Facades\Auth;

trait HasTwoFALoginCustom
{
    public function isTwoFaVerfied(?string $session_id = null): bool
    {
        return $this->twoFaVerifis()->where('user_id', Auth::user()->id)->exists();
    }
}
