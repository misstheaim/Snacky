<?php

namespace App\Providers;

use App\Contracts\HttpCategoriesReceiver;
use App\Contracts\HttpProductReceiver;
use App\Services\UzumHttpCategoriesReceiver;
use App\Services\UzumHttpProductReceiver;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\ServiceProvider;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    public $bindings = [
        HttpCategoriesReceiver::class => UzumHttpCategoriesReceiver::class,
        HttpProductReceiver::class => UzumHttpProductReceiver::class,
    ];

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        ResetPassword::createUrlUsing(function (object $notifiable, string $token) {
            return config('app.frontend_url')."/password-reset/$token?email={$notifiable->getEmailForPasswordReset()}";
        });
    }
}
