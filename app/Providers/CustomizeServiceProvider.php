<?php

namespace app\Providers;

use Alchemy\BinaryDriver\Listeners\DebugListener;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Libraries\Utilities\Encrypt;
use Libraries\Utilities\Thirdparty\Tenlua;
use Libraries\Utilities\Thirdparty\Tinhte;
use Monolog\Logger;

/**
 * Class GmaServiceProvider.
 **/
class CustomizeServiceProvider extends ServiceProvider
{
    /**
     */
    public function boot()
    {
    }

    /**
     * Register the service provider.
     */
    public function register()
    {

        // Lib Encrypt
        $this->app->singleton('lib_encrypt', function ($app) {
            $lib_encrypt = new Encrypt();

            return $lib_encrypt;
        });
    }
}
