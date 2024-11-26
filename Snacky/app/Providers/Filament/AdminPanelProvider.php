<?php

namespace App\Providers\Filament;

use App\Filament\Auth\Register;
use App\Filament\Pages\CommentedSnacks;
use App\Filament\Resources\NotificationResource;
use App\Models\CustomFilamentComment;
use App\Models\Notification;
use DutchCodingCompany\FilamentSocialite\FilamentSocialitePlugin;
use DutchCodingCompany\FilamentSocialite\Provider;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Laravel\Socialite\Contracts\User as SocialiteUserContract;
use Solutionforest\FilamentEmail2fa\FilamentEmail2faPlugin;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->registration(Register::class)
            ->profile()
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->userMenuItems([
                MenuItem::make()
                    ->icon('heroicon-o-chat-bubble-left-ellipsis')
                    ->color(fn () => Notification::where('user_id', Auth::user()->id)->where('status', 'NOT_SEEN')->where('type', "COMMENTED")->count() !== 0 ? 'primary' : 'gray')
                    ->label(function () { 
                        $notCount = Notification::where('user_id', Auth::user()->id)->where('status', 'NOT_SEEN')->where('type', "COMMENTED")->count();
                        return 'Commented snacks ' . ($notCount !== 0 ? '- '.$notCount : '');
                    })
                    ->url(function () {
                        $nots = Notification::select('snack_id')->where('user_id', Auth::user()->id)->where('type', "COMMENTED")->get();
                        $snacks = array();
                        foreach ($nots as $not) {
                            $snacks[] = $not->snack_id;
                        }
                        return CommentedSnacks::getUrl(['snacks' => $snacks]);
                    }),
                MenuItem::make()
                    ->icon('heroicon-o-bell')
                    ->color(fn () => Notification::where('user_id', Auth::user()->id)->where('status', 'NOT_SEEN')->count() !== 0 ? 'primary' : 'gray')
                    ->label(function () { 
                        $notCount = Notification::where('user_id', Auth::user()->id)->where('status', 'NOT_SEEN')->count();
                        return 'Notifications ' . ($notCount !== 0 ? '- '.$notCount : '');
                    })
                    ->url(fn () => NotificationResource::getUrl('index')),
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->brandName('Snacky')
            ->plugins([
                FilamentEmail2faPlugin::make(),
                FilamentSocialitePlugin::make()
                    // (required) Add providers corresponding with providers in `config/services.php`. 
                    ->providers([
                        // Create a provider 'gitlab' corresponding to the Socialite driver with the same name.
                        // Provider::make('google')
                        //     ->label('Google')
                        //     ->icon('fab-google')
                        //     ->color(Color::hex('#2f2a6b'))
                        //     ->outlined(false)
                        //     ->stateless(false)
                        //     // ->scopes(['...'])
                        //     ->with(['...']),
                        Provider::make('microsoft')
                            ->label('Microsoft')
                            ->icon('fab-microsoft')
                            ->color(Color::hex('#2f2a6b'))
                            ->outlined(false)
                            ->stateless(false)
                            // ->scopes(['...'])
                            ->with(['...']),
                    ])
                    ->domainAllowList(['ventionteams.com'])
                    // ->authorizeUserUsing(function (FilamentSocialitePlugin $plugin, SocialiteUserContract $oauthUse) {
                    //     return FilamentSocialitePlugin::checkDomainAllowList($plugin, $oauthUse);
                    // })
                    // (optional) Override the panel slug to be used in the oauth routes. Defaults to the panel ID.
                    // ->slug('admin')
                    // (optional) Enable/disable registration of new (socialite-) users.
                    ->registration(true)
                    // // (optional) Enable/disable registration of new (socialite-) users using a callback.
                    // // In this example, a login flow can only continue if there exists a user (Authenticatable) already.
                    // ->registration(fn (string $provider, SocialiteUserContract $oauthUser, ?Authenticatable $user) => (bool) $user)
                    // // (optional) Change the associated model class.
                    // ->userModelClass(\App\Models\User::class)
                    // // (optional) Change the associated socialite class (see below).
                    // ->socialiteUserModelClass(\App\Models\SocialiteUser::class)
            ]);
    }
}
