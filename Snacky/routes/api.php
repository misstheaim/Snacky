<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/categories', function() {
    $response = Http::withHeaders([
        'Accept' => 'application/json',
        'Content-Type' => 'application/json',
        'Authorization' => '',
        'User-Agent' => 'Chrome',
        'Host' => 'api.uzum.uz'
    ])->get('https://api.uzum.uz/api/main/root-categories', [
        'eco' => false
    ]);

    return [
        'status' => $response->status(),
        'data' => $response->json()
    ];
});


Route::get('/test', function() {
    return [
        'status' => 200
    ];
});