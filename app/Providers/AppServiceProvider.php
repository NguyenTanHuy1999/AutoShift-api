<?php

namespace app\Providers;

use App\Api\Entities\Node;
use App\Observers\NodeObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
    }

    /**
     * Register any application services.
     */
    public function register()
    {

        // set locale
        $request = app('request');
        $acceptLanguage = $request->header('accept-language');
        // if ($acceptLanguage) {
        //     $language = current(explode(',', $acceptLanguage));
        //     app('translator')->setLocale($language);
        // }

        if ($token = $request->get('authorization')) {
            $request->headers->set('Authorization', 'Bearer '.$token);
        }

        $this->app->bind('Illuminate\\Contracts\\Mail\\Mailer', function ($app) {
            return $app->make('mailer');
        });
    }
}
