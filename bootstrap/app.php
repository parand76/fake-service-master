<?php

require_once __DIR__ . '/../vendor/autoload.php';

(new Laravel\Lumen\Bootstrap\LoadEnvironmentVariables(
    dirname(__DIR__)
))->bootstrap();

date_default_timezone_set(env('APP_TIMEZONE', 'UTC'));

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
    dirname(__DIR__)
);

$app->withFacades();

$app->withEloquent();

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
    MatinUtils\LogSystem\Handler::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

/*
|--------------------------------------------------------------------------
| Register Config Files
|--------------------------------------------------------------------------
|
| Now we will register the "app" configuration file. If the file exists in
| your configuration directory it will be loaded; otherwise, we'll load
| the default version. You may register other files below as needed.
|
*/

$app->configure('app');
$app->configure('queue');
$app->configure('lug');
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

// $app->middleware([
//     App\Http\Middleware\ExampleMiddleware::class
// ]);

// $app->routeMiddleware([
//     'auth' => App\Http\Middleware\Authenticate::class,
// ]);

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

// $app->register(App\Providers\AppServiceProvider::class);
// $app->register(App\Providers\AuthServiceProvider::class);
// $app->register(App\Providers\EventServiceProvider::class);
$app->register(Laravel\Tinker\TinkerServiceProvider::class);
$app->register(MatinUtils\LogSystem\ServiceProvider::class);
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

$app->router->group([
    'namespace' => 'App\Http\Controllers',
], function ($router) {
    require __DIR__ . '/../routes/web.php';
});

//parto
$app->router->group([
    'namespace' => 'App\Http\Controllers\Parto',
    'prefix' => 'parto',
], function ($router) {
    require __DIR__ . '/../routes/parto.php';
});

//amadeus
$app->router->group([
    'namespace' => 'App\Http\Controllers\Amadeus',
    'prefix' => 'amadeus',
    'middleware' => 'App\Http\Middleware\XmlRequests'
], function ($router) {
    require __DIR__ . '/../routes/amadeus.php';
    $router->post('/', ['uses' => "$controller@$action"]);
});

//amadeus new
$app->router->group([
    'namespace' => 'App\Http\Controllers\Amadeus',
    'prefix' => 'amadeus',
    'middleware' => 'App\Http\Middleware\AmadeusNewXml'
], function ($router) {
    require __DIR__ . '/../routes/amadeusNew.php';
    $router->post('/', ['uses' => "$controller@$action"]);
});

//TBO
$app->router->group([
    'namespace' => 'App\Http\Controllers\TBO',
    'prefix' => 'TBO',
    'middleware' => 'App\Http\Middleware\TboXmlRequestConvertor'

], function ($router) {
    require __DIR__ . '/../routes/TBO.php';
    // dd("$controller@$action");
    $router->post('/', ['uses' => "$controller@$action"]);
    $router->get('/', function () {
        return response("", 415);
    });
});

//IToursPast
$app->router->group([
    'namespace' => 'App\Http\Controllers\Itours',
    'prefix' => 'ItoursPast',
], function ($router) {
    require __DIR__ . '/../routes/Itours.php';
});

//ITours
$app->router->group([
    'namespace' => 'App\Http\Controllers\Itours',
    'prefix' => 'Itours',
], function ($router) {
    require __DIR__ . '/../routes/ItoursNew.php';
});

//isms
$app->router->group([
    'namespace' => 'App\Http\Controllers\ISMS',
    'prefix' => 'isms',
], function ($router) {
    require __DIR__ . '/../routes/sms.php';
});

//avtra
$app->router->group([
    'namespace' => 'App\Http\Controllers\Avtra',
    'prefix' => 'Avtra',
    'middleware' => 'App\Http\Middleware\AvtraXml'
], function ($router) {
    require __DIR__ . '/../routes/avtra.php';
});

//flyerbil
$app->router->post('FlyErbil/auth/realms/test-skywork-gds/protocol/openid-connect/token', "App\Http\Controllers\FlyErbil\FlyErbil@authorization");
$app->router->group([
    'namespace' => 'App\Http\Controllers\FlyErbil',
    'prefix' => 'FlyErbil',
    'middleware' => 'App\Http\Middleware\FlyErbilXml'

], function ($router) {
    require __DIR__ . '/../routes/flyErbil.php';
    // dd("$controller@$action");
    $router->post('test-skywork-gds/ota-ecom-saml', ['uses' => "$controller@$action"]);
});

//blog
$app->router->group([
    'namespace' => 'App\Http\Controllers\Blog',
    'prefix' => 'blog',
], function ($router) {
    require __DIR__ . '/../routes/blog.php';
});

//accelaero
$app->router->group([
    'namespace' => 'App\Http\Controllers\Accelaero',
    'prefix' => 'Accelaero',
    'middleware' => 'App\Http\Middleware\AccelaeroXml'
], function ($router) {
    require __DIR__ . '/../routes/accelaero.php';
    $router->post('/', ['uses' => "$controller@$action"]);
});

//citynet
$app->router->group([
    'namespace' => 'App\Http\Controllers\CityNet',
    'prefix' => 'city',
], function ($router) {
    require __DIR__ . '/../routes/citynet.php';
});

//sepehr
$app->router->group([
    'namespace' => 'App\Http\Controllers\Sepehr',
    'prefix' => 'sepehr',
], function ($router) {
    require __DIR__ . '/../routes/sepehr.php';
});

return $app;