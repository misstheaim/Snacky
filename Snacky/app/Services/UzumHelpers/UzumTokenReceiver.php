<?php

namespace App\Services\UzumHelpers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class UzumTokenReceiver
{
    public static function receiveToken() :string
    {
        $response = Http::getUzumToken()->post('/');

        return $response->cookies()->getCookieByName('access_token')->getValue();
    }

    public static function getToken() :string {
        return Cache::remember('uzum-api-token', 240, fn() => self::receiveToken());
    }
}