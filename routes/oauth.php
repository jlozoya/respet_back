<?php
/**
 * @api {post} /oauth/token Solicita una sesión al servidor.
 * @apiVersion 0.0.1
 * @apiName Login
 * @apiGroup User
 * @apiPermission none
 *
 * @apiParam {Number} client_id Id del cliente con el que se desea acceder.
 * @apiParam {String} client_secret Contraseña del cliente con el que se desea acceder.
 * @apiParam {String} grant_type Nombre de la fuente con la que
 * se inicia sesión 'facebook' | 'google' | 'password'.
 * @apiParam {String} email Email del usuario.
 * @apiParam {String} password Contraseña del usuario.
 *
 * @apiSuccess (200) {Number} id Id del usuario.
 * @apiSuccess (200) {String} token Token con la sesión del usuario.
 * 
 * @apiError (404) {String} SERVER.WRONG_USER Cuando no se
 * encontró la información del usuario.
 * @apiError (404) {String} SERVER.USER_NOT_REGISTRED Cuando
 * el email no está registrado.
 * @apiError (406) {String} SERVER.INCORRECT_USER Cuando el
 * usuario o contraseña no están registrados.
 * @apiError (406) {String} SERVER.WRONG_TOKEN Cuando el token
 * enviado es incorrecto.
 */
$router->post('/oauth/token', ['uses' => 'OAuth\OAuthController@issueToken']);
/**
 * @api {post} /password/email Recupera una contraseña con un email.
 * @apiVersion 0.0.1
 * @apiName PostEmail
 * @apiGroup Password
 * @apiPermission none
 *
 * @apiParam {String} grant_type Nombre de la fuente con la que se
 * inicia sesión 'facebook' | 'google' | 'password'.
 * @apiParam {String} email Email del usuario.
 *
 * @apiSuccess (200) {String} SERVER.EMAIL_READY Confirmación
 * de que se envió un correo para recuperar la contraseña.
 * 
 * @apiError (404) {String} SERVER.WRONG_USER Cuando no se
 * encontró la información del usuario.
 */
$router->post('/password/email', 'OAuth\PasswordController@postEmail');
/**
 * @api {get} /password/reset Habré una vista para
 * resetear la contraseña.
 * @apiVersion 0.0.1
 * @apiName ShowResetForm
 * @apiGroup Password
 * @apiPermission none
 *
 * @apiParam {String} token Token para resetear la contraseña.
 * @apiParam {String} email Email del usuario.
 *
 * @apiSuccess (200) {String} view Vista con el formulario
 * para resetear la contraseña.
 */
$router->get('/password/reset', ['uses' => 'OAuth\PasswordController@showResetForm']);
/**
 * @api {put} /password/reset Para actualizar la contraseña.
 * @apiVersion 0.0.1
 * @apiName PostReset
 * @apiGroup Password
 * @apiPermission none
 * 
 * @apiParam {String} email Email del usuario.
 * @apiParam {String} password Nueva contraseña el usuario.
 * @apiParam {String} password_confirmation Confirmación de la
 * nueva contraseña el usuario.
 * @apiParam {String} token Token para resetear la contraseña.
 * @apiParam {String} grant_type Nombre de la fuente con la que se
 * inicia sesión 'password'.
 *
 * @apiSuccess (200) {Redirect} redirect Redirección a la página principal.
 * 
 * @apiError (200) {View} auth.emails.password En caso de que falle
 * el reseteo de la contraseña.
 */
$router->post('/password/reset', ['as' => 'password.reset', 'uses' => 'OAuth\PasswordController@putReset']);