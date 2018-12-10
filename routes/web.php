<?php
/**
 * @api {get} / Obtiene la versión de laravel lumen.
 * @apiVersion 0.0.1
 * @apiName Version
 * @apiGroup Api
 * @apiPermission none
 *
 * @apiSuccess (200) {String} version Versión de laravel lumen.
 */
$router->get('/', function () use ($router) {
    return $router->app->version();
});
/**
 * @api {post} /user/signup Resgistra un nuevo ususario.
 * @apiVersion 0.0.1
 * @apiName Signup
 * @apiGroup User
 * @apiPermission none
 *
 * @apiParam {String} name Nombre de usuario.
 * @apiParam {String} first_name Primer nombre.
 * @apiParam {String} last_name Apellido.
 * @apiParam {String} email Email del nuevo usuario.
 * @apiParam {String} source Fuente con la que se crea la cuenta 'facebook' | 'google' | 'app'.
 * @apiParam {String} [gender] Genero del ususario 'male' | 'female' | 'other'.
 * @apiParam {String} [lang] Idioma del ususario.
 * @apiParam {Object} [media] Imagen del nuevo usuario a registrar {media: {url: String}.
 *
 * @apiSuccess (201) {Number} id Id del usuario.
 * @apiSuccess (201) {String} token Token con la sesión del usuario.
 * 
 * @apiError (401) {String} SERVER.USER_ALREADY_EXISTS Cuando el usuario ya existe.
 * @apiError (401) {String} SERVER.UNAUTHORIZED Cuando el usuario no está autorizado.
 * @apiError (406) {QueryException} error Mensaje de error.
 */
$router->post('/user/signup', ['uses' => 'UserController@signup']);
/**
 * @api {post} /user/login Solicita una sesión al servidor.
 * @apiVersion 0.0.1
 * @apiName Login
 * @apiGroup User
 * @apiPermission none
 *
 * @apiParam {String} source Nombre de la fuente con la que se inicia sesión 'facebook' | 'google' | 'app'.
 * @apiParam {String} email Email del usuario.
 * @apiParam {String} password Contraseña del usuario.
 *
 * @apiSuccess (200) {Number} id Id del usuario.
 * @apiSuccess (200) {String} token Token con la sesión del usuario.
 * 
 * @apiError (404) {String} SERVER.WRONG_USER Cuando no se encontró la información del usuario.
 * @apiError (404) {String} SERVER.USER_NOT_REGISTRED Cuando el email no está registrado.
 * @apiError (406) {String} SERVER.INCORRECT_USER Cuando el usuario o contraseña no están registrados.
 * @apiError (406) {String} SERVER.WRONG_TOKEN Cuando el token enviado es incorrecto.
 */
$router->post('/user/login', ['uses' => 'UserController@login']);

/**
 * @api {post} /password/email Recupera una contraseña con un email.
 * @apiVersion 0.0.1
 * @apiName PostEmail
 * @apiGroup Password
 * @apiPermission none
 *
 * @apiParam {String} source Nombre de la fuente con la que se inicia sesión 'facebook' | 'google' | 'app'.
 * @apiParam {String} email Email del usuario.
 *
 * @apiSuccess (200) {String} SERVER.EMAIL_READY Confirmación de que se envió un correo para recuperar la contraseña.
 * 
 * @apiError (404) {String} SERVER.WRONG_USER Cuando no se encontró la información del usuario.
 */
$router->post('/password/email', 'PasswordController@postEmail');
/**
 * @api {get} /password/reset/:token Habré una vista para resetear la contraseña.
 * @apiVersion 0.0.1
 * @apiName ShowResetForm
 * @apiGroup Password
 * @apiPermission none
 *
 * @apiParam {String} token Token para resetear la contraseña.
 * @apiParam {String} email Email del usuario.
 *
 * @apiSuccess (200) {String} view Vista con el formulario para resetear la contraseña.
 */
$router->get('/password/reset/{token}', ['uses' => 'PasswordController@showResetForm']);
/**
 * @api {post} /password/reset Para resetear la contraseña.
 * @apiVersion 0.0.1
 * @apiName PostReset
 * @apiGroup Password
 * @apiPermission none
 * 
 * @apiParam {String} email Email del usuario.
 * @apiParam {String} password Nueva contraseña el usuario.
 * @apiParam {String} password_confirmation Confirmación de la nueva contraseña el usuario.
 * @apiParam {String} token Token para resetear la contraseña.
 * @apiParam {String} source Nombre de la fuente con la que se inicia sesión 'facebook' | 'google' | 'app'.
 *
 * @apiSuccess (200) {Redirect} redirect Redirección a la página principal.
 * 
 * @apiError (400) {String} SERVER.RESET_FAIL En caso de que falle el reseteo de la contraseña.
 */
$router->post('/password/reset', ['as' => 'password.reset', 'uses' => 'PasswordController@postReset']);
/**
 * @api {get} /user/confirm/email Para obtener un email de confirmación de email.
 * @apiVersion 0.0.1
 * @apiName ConfirmEmail
 * @apiGroup User
 * @apiPermission none
 * 
 * @apiParam {Number} id Id del usuario.
 *
 * @apiSuccess (200) {String} SERVER.USER_ALREADY_CONFIRMED Correo ya confirmado.
 * @apiSuccess (200) {String} SERVER.EMAIL_SEND Email de confirmación enviado.
 * 
 * @apiError (404) {String} SERVER.USER_NOT_FOUND No se encontró el usuario a confirmar.
 * @apiError (400) {String} SERVER.EMAIL_FAIL Cuando no se logra enviar le email.
 */
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