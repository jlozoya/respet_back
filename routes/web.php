<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Aquí es donde puede registrar todas las rutas para una aplicación.
| Es una brisa. Simplemente dile a Lumen los URI a los que debería responder
| y darle el Cierre para llamar cuando se solicita ese URI.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->get('/key', function () use ($router) {
    return str_random(32);
});

$router->post('/user/login', ['uses' => 'UserController@login']);
$router->post('/user/signup', ['uses' => 'UserController@signup']);

$router->post('/password/email', 'PasswordController@postEmail');
$router->get('/password/reset/{token}', ['uses' => 'PasswordController@showResetForm']);
$router->post('/password/reset', ['as' => 'password.reset', 'uses' => 'PasswordController@postReset']);

$router->get('/user/confirm/email', ['as' => 'user.confirm.email', 'uses' => 'UserController@confirmEmail']);

$router->post('/contact/send', ['uses' => 'ContactController@sendContact']);

$router->get('/bulletins', ['uses' => 'BulletinController@showBulletins']);
$router->get('/bulletin/{id}', ['uses' => 'BulletinController@showOneBulletinById']);

$router->post('/pay', ['uses' => 'MercadoPagoController@createPay']);

$router->group(['middleware' => ['auth:api']], function () use ($router) {
    $router->get('/user', ['uses' => 'UserController@getUser']);
    $router->post('/user/confirm/email', ['uses' => 'UserController@reSendConfirmEmail']);
    $router->post('/user/set/avatar', ['uses' => 'UserController@saveAvatar']);
    $router->put('/user/social/link', ['uses' => 'UserController@createSocialLink']);
    $router->put('/user/update', ['uses' => 'UserController@updateUser']);
    $router->put('/user/update/email', ['uses' => 'UserController@updateUserEmail']);
    $router->put('/user/update/lang', ['uses' => 'UserController@updateUserLang']);
    $router->put('/user/update/direction', ['uses' => 'UserController@updateUserDirection']);
    $router->delete('/user/social/link/{id}', ['uses' => 'UserController@deleteSocialLink']);
    $router->delete('/user/logout', ['uses' => 'UserController@logout']);
    $router->delete('/user', ['uses' => 'UserController@deleteUser']);

    $router->post('/user/pay', ['uses' => 'PayController@createPay']);

    $router->group(['middleware' => ['isAdmin']], function () use ($router) {
        $router->get('/user/{id}', ['uses' => 'UserController@getUserById']);
        $router->post('/users', ['uses' => 'UserController@getUsers']);
        $router->post('/user/set/avatar/{id}', ['uses' => 'UserController@saveAvatarById']);
        $router->put('/user/update/{id}', ['uses' => 'UserController@updateUserById']);
        $router->put('/user/update/email/{id}', ['uses' => 'UserController@updateUserEmailById']);
        $router->put('/user/update/lang/{id}', ['uses' => 'UserController@updateUserLangById']);
        $router->put('/user/update/direction/{id}', ['uses' => 'UserController@updateUserDirectionById']);
        $router->put('/user/role/{id}', ['uses' => 'UserController@setUserRoleById']);
        $router->delete('/user/delete/{id}', ['uses' => 'UserController@deleteUserById']);

        $router->post('/bulletin', ['uses' => 'BulletinController@createBulletin']);
        $router->post('/bulletin/set/img', ['uses' => 'BulletinController@setImg']);
        $router->put('/bulletin', ['uses' => 'BulletinController@updateBulletin']);
        $router->delete('/bulletin/{id}', ['uses' => 'BulletinController@deleteBulletin']);

        $router->get('/analytics', ['uses' => 'AnalyticController@getBasicAnalytics']);
        $router->post('/analytics/users/registration', ['uses' => 'AnalyticController@getUsersRegistration']);
    });
});