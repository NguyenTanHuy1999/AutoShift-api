<?php

require_once __DIR__.'/../vendor/autoload.php';

try {
    (new Dotenv\Dotenv(__DIR__.'/../'))->load();
} catch (Dotenv\Exception\InvalidPathException $e) {
    //
}

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| Here we will load the environment and create the application instance
| that serves as the central piece of this framework. We'll use this
| application as an "IoC" container and router for this framework.
|
*/

$app = new Laravel\Lumen\Application(
    realpath(__DIR__.'/../')
);

config([
    'filesystems' => [
        'default' => 'local',
        'disks' => [
            'local' => [
                'driver' => 'local',
                'root' => storage_path('app'),
            ],
        ],
    ],
]);

// phpunit
$app->withFacades();
//mail

//class_alias('Jenssegers\Mongodb\Eloquent\Model', 'mail');
 //$app->withEloquent();
// class_alias('Jenssegers\Mongodb\Eloquent\Model', 'mail');
// $app->withEloquent();

$app->register('Moloquent\MongodbServiceProvider');
$app->withEloquent();

// Load additional config files
// jwt
$app->configure('jwt');

// repository
$app->configure('repository');

// services
$app->configure('services');

$app->configure('database');

// Queue
$app->configure('queue');

// App
$app->configure('app');


//Timezone
date_default_timezone_set('Asia/Ho_Chi_Minh');

// $app->make('cache');
/*
|--------------------------------------------------------------------------
| Register Container Bindings
|--------------------------------------------------------------------------
|
| Now we will register a few bindings in the service container. We will
| register the exception handler and the console kernel. You may add
| your own bindings here if you like or you can make another file.
|
*/
$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

/*
|--------------------------------------------------------------------------
| Register Middleware
|--------------------------------------------------------------------------
|
| Next, we will register the middleware with the application. These can
| be global middleware that run before and after each request into a
| route or middleware that'll be assigned to some specific routes.
|
*/

$app->middleware([
    App\Http\Middleware\Cors::class,
]);

$app->routeMiddleware([
    'auth' => App\Http\Middleware\Authenticate::class,
    'api.admin' => App\Http\Middleware\AdminMiddleware::class,
    'api.locale' => App\Http\Middleware\UserLocale::class,
    'manager.role' => App\Http\Middleware\ManagerRole::class,
]);

/*
|--------------------------------------------------------------------------
| Register Service Providers
|--------------------------------------------------------------------------
|
| Here we will register all of the application's service providers which
| are used to bind services into the container. Service providers are
| totally optional, so you are not required to uncomment this line.
|
*/

$app->register(App\Providers\AppServiceProvider::class);
$app->register(App\Providers\AuthServiceProvider::class);
$app->register(Illuminate\Redis\RedisServiceProvider::class);
$app->register(App\Providers\RepositoryServiceProvider::class);

// Add extra commands for repository
$app->register(App\Providers\CommandServiceProvider::class);

//repository
$app->register(Prettus\Repository\Providers\LumenRepositoryServiceProvider::class);

// dingo/api
$app->register(Dingo\Api\Provider\LumenServiceProvider::class);

//jwt
$app->register(Tymon\JWTAuth\Providers\LumenServiceProvider::class);

// CustomExceptionHandler
$app->register(App\Providers\ExceptionsServiceProvider::class);

// All Custom GMA Services
$app->register(App\Providers\CustomizeServiceProvider::class);

// Queue MongoDB
$app->register(Moloquent\MongodbQueueServiceProvider::class);


app('Dingo\Api\Auth\Auth')->extend('jwt', function ($app) {
    return new Dingo\Api\Auth\Provider\JWT($app['Tymon\JWTAuth\JWTAuth']);
});

//Injecting auth
$app->singleton(Illuminate\Auth\AuthManager::class, function ($app) {
    return $app->make('auth');
});


//Database Table
// $app->register(Yajra\DataTables\DataTablesServiceProvider::class);
// $app->withFacades();
/*
|--------------------------------------------------------------------------
| Load The Application Routes
|--------------------------------------------------------------------------
|
| Next we will include the routes file so that they can all be added to
| the application. This will provide all of the URLs the application
| can respond to, as well as the controllers that may handle them.
|
*/

$app->group(['namespace' => 'App\Http\Controllers'], function ($app) {
    require __DIR__.'/../routes/user.php';
    require __DIR__.'/../routes/shop.php';
    require __DIR__.'/../routes/student.php';
    require __DIR__.'/../routes/branch.php';
    require __DIR__.'/../routes/department.php';

    require __DIR__.'/../routes/position.php';

    require __DIR__.'/../routes/shift.php';
    require __DIR__.'/../routes/empshift.php';

});
return $app;
