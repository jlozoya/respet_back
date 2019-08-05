<?php

require_once __DIR__.'/../vendor/autoload.php';

use App\LumenPassport\LumenPassport;
use App\LumenPassport\PassportServiceProvider;

(new Laravel\Lumen\Bootstrap\LoadEnvironmentVariables(
    dirname(__DIR__)
))->bootstrap();

/*
|--------------------------------------------------------------------------
| Crear la aplicación
|--------------------------------------------------------------------------
|
| Aquí cargaremos el entorno y crearemos la instancia de la aplicación
| eso sirve como la pieza central de este marco. Usaremos esto
| como un contenedor y enrutador "IoC" para este marco.
|
*/

$app = new Laravel\Lumen\Application(
    dirname(__DIR__)
);

$app->withFacades();

$app->withEloquent();

$app->configure('services');

$app->configure('mail');

$app->alias('mailer', \Illuminate\Contracts\Mail\Mailer::class);

$app->register(\Illuminate\Notifications\NotificationServiceProvider::class);

$app->register(\Illuminate\Auth\Passwords\PasswordResetServiceProvider::class);

$app->register(App\Providers\AppServiceProvider::class);

$app->register(\Illuminate\Mail\MailServiceProvider::class);

$app->register(Intervention\Image\ImageServiceProvider::class);

$app->register(Srmklive\PayPal\Providers\PayPalServiceProvider::class);

/*
|--------------------------------------------------------------------------
| Registrar enlaces de contenedor
|--------------------------------------------------------------------------
|
| Ahora registraremos algunas vinculaciones en el contenedor de servicios. Lo haremos
| registrar el manejador de excepciones y el kernel de la consola. Puedes agregar
| sus propios enlaces aquí si lo desea o puede hacer otro archivo.
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
| A continuación, registraremos el middleware con la aplicación. Estos pueden
| ser middleware global que se ejecuta antes y después de cada solicitud en un
| ruta o middleware que se asignará a algunas rutas específicas.
|
*/

$app->routeMiddleware([
    'auth' => App\Http\Middleware\Authenticate::class,
    'hasRole' => App\Http\Middleware\HasRole::class,
]);

$app->middleware([
    Vluzrmos\LumenCors\CorsMiddleware::class
]);

/*
|--------------------------------------------------------------------------
| Registro de OAuth
|--------------------------------------------------------------------------
|
| Registra las clases necesarias para manejar OAuth.
|
*/

$app->configure('auth');

$app->register(Laravel\Passport\PassportServiceProvider::class);

$app->register(PassportServiceProvider::class);

LumenPassport::allowMultipleTokens();
/*
|--------------------------------------------------------------------------
| Registrar proveedores de servicios
|--------------------------------------------------------------------------
|
| Aquí registraremos todos los proveedores de servicios de la aplicación que
| se utilizan para vincular servicios en el contenedor. Los proveedores de servicios son
| totalmente opcional, por lo que no es necesario descomentar esta línea.
|
*/

$app->register(App\Providers\AppServiceProvider::class);
$app->register(App\Providers\AuthServiceProvider::class);
// $app->register(App\Providers\EventServiceProvider::class);

$app->register(Appzcoder\LumenRoutesList\RoutesCommandServiceProvider::class);

/*
|--------------------------------------------------------------------------
| Cargue las rutas de la aplicación
|--------------------------------------------------------------------------
|
| A continuación, incluiremos el archivo de rutas para que todos puedan agregarse a
| la aplicación. Esto proporcionará todas las URL de la aplicación
| puede responder, así como los controladores que pueden manejarlos.
|
*/

$app->router->group([
    'namespace' => 'App\Http\Controllers',
], function ($router) {
    require __DIR__.'/../routes/api.php';
    require __DIR__.'/../routes/user.php';
    require __DIR__.'/../routes/oauth.php';
});

return $app;
