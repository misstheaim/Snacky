<?php

namespace App\Providers;

use App\Contracts\Filament\Snack\FormTemplate;
use App\Contracts\Filament\Snack\TableTemplate;
use App\Contracts\Filament\Snack\ViewTemplate;
use App\Contracts\HttpCategoriesReceiver;
use App\Contracts\HttpProductReceiver;
use App\Filament\Resources\Templates\SnackFormTemplate;
use App\Filament\Resources\Templates\SnackTableTemplate;
use App\Filament\Resources\Templates\SnackViewTemplate;
use App\Models\User;
use App\Services\UzumHttpCategoriesReceiver;
use App\Services\UzumHttpProductReceiver;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use SocialiteProviders\Microsoft\Provider;

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
        TableTemplate::class => SnackTableTemplate::class,
        FormTemplate::class => SnackFormTemplate::class,
        ViewTemplate::class => SnackViewTemplate::class,
    ];

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        ResetPassword::createUrlUsing(function (object $notifiable, string $token) {
            return config('app.frontend_url') . "/password-reset/$token?email={$notifiable->getEmailForPasswordReset()}";
        });

        Event::listen(function (\SocialiteProviders\Manager\SocialiteWasCalled $event) {
            $event->extendSocialite('microsoft', Provider::class);
        });

        Gate::define('viewLogViewer', function (?User $user) {
            return $user->isAdmin();
        });

        Gate::define('viewTelescope', function (?User $user) {
            return $user->isAdmin();
        });
    }
}
