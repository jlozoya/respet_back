<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
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

$router->group(['middleware' => ['auth']], function () use ($router) {
    $router->get('/user/id/{id}', ['uses' => 'UserController@getUserById']);
    $router->post('/user/confirm/email', ['uses' => 'UserController@reSendConfirmEmail']);
    $router->post('/user/set/avatar', ['uses' => 'UserController@saveAvatar']);
    $router->put('/user/update', ['uses' => 'UserController@updateUser']);
    $router->put('/user/update/email', ['uses' => 'UserController@updateUserEmail']);
    $router->put('/user/update/direction', ['uses' => 'UserController@updateUserDirection']);
});