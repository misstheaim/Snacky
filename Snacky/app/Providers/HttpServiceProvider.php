<?php

namespace App\Providers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\ServiceProvider;

class HttpServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Http::macro('getUzumToken', function() {
            return Http::withHeaders([
                'Content-Length' => 0,
                'Host' => 'id.uzum.uz',
                'User-Agent' => config('uzum.user_agent_header'),
                'Accept-Encoding' => config('uzum.accept_encodeing_header'),
                'Accept-Language' => 'ru',
                'Referer' => config('uzum.referer_header'),
                'x-iid' => config('uzum.x_iid_header'),
            ])
                ->baseUrl(config('uzum.token_url'))
                ->throw()
                ->retry(2, 1000);
        });


        Http::macro('getUzumProductGraphQl', function() {
            return Http::withHeaders([
                'Host' => 'graphql.uzum.uz',
                'User-Agent' => config('uzum.user_agent_header'),
                'Content-Type' => 'application/json',
                'x-iid' => config('uzum.x_iid_header'),
            ])
                ->baseUrl(config('uzum.graphql_url'))
                ->throw()
                ->retry(2, 1000);
        });


        Http::macro('getUzumProduct', function() {
            return Http::withHeaders([
                'Host' => 'api.uzum.uz',
                'User-Agent' => config('uzum.user_agent_header'),
                'x-iid' => config('uzum.x_iid_header'),
            ])
                ->baseUrl(config('uzum.product_url'))
                ->throw()
                ->retry(2, 1000);
        });


        Http::macro('getUzumCategories', function() {
            return Http::withHeaders([
                'Host' => 'api.uzum.uz',
                'Authorization' => 'Bearer 1',
                'User-Agent' => config('uzum.user_agent_header'),
                'x-iid' => config('uzum.x_iid_header'),
            ])
                ->baseUrl(config('uzum.categories_url'))
                ->throw()
                ->retry(2, 1000);
        });
    }
}
