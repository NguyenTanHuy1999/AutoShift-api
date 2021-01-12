<?php

namespace app\Providers;

use Illuminate\Support\ServiceProvider;
use Exception;

/**
 * Class ExceptionsServiceProvider.
 **/
class ExceptionsServiceProvider extends ServiceProvider
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
        /*app('Dingo\Api\Exception\Handler')->register(function (Exception $e) {
            // We should log the original exception here
            $message = 'Unknown Error';
            $response = array(
                'error_code' => 500,
                'message' => $message,
                'data' => array(),
            );

            return response()->json($response);

        });*/
    }
}
