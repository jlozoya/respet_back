<?php
/**
 * @api {post} /oauth/token Solicita una sesión al servidor.
 * @apiVersion 0.0.1
 * @apiName Login
 * @apiGroup User
 * @apiPermission none
 *
 * @apiParam {String} source Nombre de la fuente con la que
 * se inicia sesión 'facebook' | 'google' | 'app'.
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
$router->post('/oauth/token', ['uses' => 'OAuthController@issueToken']);