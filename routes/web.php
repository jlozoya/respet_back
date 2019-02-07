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
 * @api {post} /user Resgistra un nuevo usuario.
 * @apiVersion 0.0.1
 * @apiName Store
 * @apiGroup User
 * @apiPermission none
 *
 * @apiParam {String} name Nombre de usuario.
 * @apiParam {String} first_name Primer nombre.
 * @apiParam {String} last_name Apellido.
 * @apiParam {String} email Email del nuevo usuario.
 * @apiParam {String} source Fuente con la que se crea
 * la cuenta 'facebook' | 'google' | 'app'.
 * @apiParam {String} [gender] Genero del usuario 'male' | 'female' | 'other'.
 * @apiParam {String} [lang] Idioma del usuario.
 * @apiParam {Media} [media] Imagen del nuevo usuario a
 * registrar {media: {url: String}.
 *
 * @apiSuccess (201) {Number} id Id del usuario.
 * @apiSuccess (201) {String} token Token con la sesión
 * del usuario.
 * 
 * @apiError (401) {String} SERVER.USER_ALREADY_EXISTS
 * Cuando el usuario ya existe.
 * @apiError (401) {String} SERVER.UNAUTHORIZED Cuando
 * el usuario no está autorizado.
 * @apiError (406) {QueryException} error Error al
 * ejecutar la consulta.
 */
$router->post('/user', ['uses' => 'UserController@store']);
/**
 * @api {post} /password/email Recupera una contraseña con un email.
 * @apiVersion 0.0.1
 * @apiName PostEmail
 * @apiGroup Password
 * @apiPermission none
 *
 * @apiParam {String} source Nombre de la fuente con la que se
 * inicia sesión 'facebook' | 'google' | 'app'.
 * @apiParam {String} email Email del usuario.
 *
 * @apiSuccess (200) {String} SERVER.EMAIL_READY Confirmación
 * de que se envió un correo para recuperar la contraseña.
 * 
 * @apiError (404) {String} SERVER.WRONG_USER Cuando no se
 * encontró la información del usuario.
 */
$router->post('/password/email', 'PasswordController@postEmail');
/**
 * @api {get} /password/reset/:token Habré una vista para
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
 * @apiParam {String} password_confirmation Confirmación de la
 * nueva contraseña el usuario.
 * @apiParam {String} token Token para resetear la contraseña.
 * @apiParam {String} source Nombre de la fuente con la que se
 * inicia sesión 'facebook' | 'google' | 'app'.
 *
 * @apiSuccess (200) {Redirect} redirect Redirección a la página principal.
 * 
 * @apiError (400) {String} SERVER.RESET_FAIL En caso de que falle
 * el reseteo de la contraseña.
 */
$router->post('/password/reset', ['as' => 'password.reset', 'uses' => 'PasswordController@postReset']);
/**
 * @api {get} /user/confirm/email Para confirmar una cuenta de usuario.
 * @apiVersion 0.0.1
 * @apiName ConfirmEmail
 * @apiGroup User
 * @apiPermission none
 * 
 * @apiParam {String} token Token para confirmar un email.
 *
 * @apiSuccess (200) {Redirection} APP_REDIRECTS_LINK Redirección
 * a la página principal.
 * 
 * @apiError (406) {String} SERVER.TOKEN_EXPIRED En caso de que el
 * token está expirado.
 * @apiError (406) {String} SERVER.WRONG_TOKEN En caso de que lo
 * token sea invalido.
 */
$router->get('/user/confirm/email', ['as' => 'user.confirm.email', 'uses' => 'UserController@confirmEmail']);
/**
 * @api {post} /contact/send Enviar un correo de contacto a administradores.
 * @apiVersion 0.0.1
 * @apiName CreateSupport
 * @apiGroup Visitor
 * @apiPermission none
 * 
 * @apiParam {Number} name Nombre del usuario.
 * @apiParam {Number} phone Teléfono del usuario.
 * @apiParam {Number} email Email del usuario.
 * @apiParam {Number} [lang] Idioma del usuario.
 *
 * @apiSuccess (201) {User} user Es la información del usuario
 * que envió la solicitud.
 */
$router->post('/support', ['uses' => 'SupportController@create']);

/**
 * @api {get} /bulletins Consultar noticias por pagina.
 * @apiVersion 0.0.1
 * @apiName GetBulletins
 * @apiGroup Bulletin
 * @apiPermission none
 * 
 * @apiParam {Number} page Número de la página a consultar.
 *
 * @apiSuccess (200) {Pagination} pagination Noticias paginadas.
 */
$router->get('/bulletins', ['uses' => 'BulletinController@getBulletins']);

/**
 * @api {get} /bulletins Consultar noticias por id.
 * @apiVersion 0.0.1
 * @apiName ShowBulletin
 * @apiGroup Bulletin
 * @apiPermission none
 * 
 * @apiParam {Number} id Id de la noticia a consultar.
 *
 * @apiSuccess (200) {Bulletin} bulletin Una noticia.
 */
$router->get('/bulletin/{id}', ['uses' => 'BulletinController@show']);

$router->post('/pets', ['uses' => 'PetController@index']);
$router->get('/pet/{id}', ['uses' => 'PetController@show']);

$router->get('/user/contact/{id}', ['uses' => 'UserHistory@getUserContactInfo']);
$router->get('/user/history/{id}', ['uses' => 'UserHistory@index']);

$router->group(['middleware' => ['auth:api']], function () use ($router) {
    /**
     * @api {get} /user Obtiene la información del usuario propio.
     * @apiVersion 0.0.1
     * @apiName index
     * @apiGroup User
     * @apiPermission user
     * 
     * @apiHeader (Auth) {String} Authorization Token de autorización.
     *
     * @apiSuccess (200) {Object} user Información del usuario.
     * @apiSuccess (200) {Number} user.id Id del usuario.
     * @apiSuccess (200) {String} user.name Nombre de usuario.
     * @apiSuccess (200) {String} user.first_name Primer nombre del usuario.
     * @apiSuccess (200) {String} user.last_name Apellido del usuario.
     * @apiSuccess (200) {String} [user.gender] Genero del usuario.
     * @apiSuccess (200) {String} user.email Email del usuario.
     * @apiSuccess (200) {Number} [user.media_id] Id de la imagen del usuario.
     * @apiSuccess (200) {Object} [user.media] Información de la imagen del usuario.
     * @apiSuccess (200) {Number} [user.media.id] Id de la imagen del usuario.
     * @apiSuccess (200) {String} [user.media.type] Tipo de la imagen del usuario.
     * @apiSuccess (200) {String} [user.media.url] Url de la imagen del usuario.
     * @apiSuccess (200) {String} [user.media.alt] Alt de la imagen del usuario.
     * @apiSuccess (200) {Number} [user.media.width] Ancho de la imagen del usuario.
     * @apiSuccess (200) {Number} [user.media.height] Alto de la imagen del usuario.
     * @apiSuccess (200) {String} [user.phone] Telefono del usuario.
     * @apiSuccess (200) {String} user.lang Idioma del usuario.
     * @apiSuccess (200) {String} [user.birthday] Fecha de nacimiento del usuario.
     * @apiSuccess (200) {Boolean} [user.confirmed] Si el correo del usuario está confirmado.
     * @apiSuccess (200) {String} user.source Fuente desde la que se registro el usuario.
     * @apiSuccess (200) {String} user.role Rol del usuario.
     * @apiSuccess (200) {Number} [user.direction_id] Id de la dirección del usuario.
     * @apiSuccess (200) {Object} [user.direction] Dirección del usuario.
     * @apiSuccess (200) {String} [user.direction.id] Información del usuario.
     * @apiSuccess (200) {String} [user.direction.contry] País de la dirección del usuario.
     * @apiSuccess (200) {String} [user.direction.administrative_area_level_1] Estado de la dirección del usuario.
     * @apiSuccess (200) {String} [user.direction.administrative_area_level_2] Municipio de la dirección del usuario.
     * @apiSuccess (200) {String} [user.direction.route] Calle de la dirección del usuario.
     * @apiSuccess (200) {Number} [user.direction.street_number] Numero de la dirección del usuario.
     * @apiSuccess (200) {Number} [user.direction.postal_code] Código postal de la dirección del usuario.
     * @apiSuccess (200) {Number} [user.direction.lat] Latitud de la dirección del usuario.
     * @apiSuccess (200) {Number} [user.direction.lng] Longitud postal de la dirección del usuario.
     * @apiSuccess (200) {String} user.created_at Información del usuario.
     * 
     * @apiError (406) {String} SERVER.USER_NOT_FOUND En caso de que
     * no se encuentre el usuario relacionado el token.
     */
    $router->get('/user', ['uses' => 'UserController@index']);
    /**
     * @api {post} /user/confirm/email Re enviar un correo para
     * confirmar la dirección de correo.
     * @apiVersion 0.0.1
     * @apiName ReSendConfirmEmail
     * @apiGroup User
     * @apiPermission user
     * 
     * @apiHeader (Auth) {String} Authorization Token de autorización.
     *
     * @apiParam {Number} id Id del usuario.
     * 
     * @apiSuccess (200) {String} SERVER.USER_ALREADY_CONFIRMED En caso
     * de que el email del usuario ya este confirmado.
     * @apiSuccess (200) {String} SERVER.EMAIL_SEND Email de confirmación enviado.
     * 
     * @apiError (404) {String} SERVER.USER_NOT_FOUND No se encontró el
     * usuario a confirmar.
     * @apiError (400) {String} SERVER.EMAIL_FAIL Cuando no se logra
     * enviar le email.
     */
    $router->post('/user/confirm/email', ['uses' => 'UserController@reSendConfirmEmail']);
    /**
     * @api {put} /user/avatar Establecer el avatar del usuario propio.
     * @apiVersion 0.0.1
     * @apiName SetAvatar
     * @apiGroup User
     * @apiPermission user
     * 
     * @apiHeader (Auth) {String} Authorization Token de autorización.
     *
     * @apiParam {File} file Es el archivo a almacenar.
     * @apiParam {String} file_name Nombre del archivo.
     * @apiParam {String} [type] Define el tipo de archivo, en caso de
     * ser base64 se debe indicar.
     * 
     * @apiSuccess (202) {String} fileUrl Url donde se almaceno el archivo.
     */
    $router->put('/user/avatar', ['uses' => 'UserController@updateAvatar']);
    /**
     * @api {put} /user/social/link Establecer el avatar del usuario propio.
     * @apiVersion 0.0.1
     * @apiName CreateSocialLink
     * @apiGroup User
     * @apiPermission user
     * 
     * @apiHeader (Auth) {String} Authorization Token de autorización.
     *
     * @apiParam {String} source Es el nombre de la red social a vincular.
     * @apiParam {String} extern_id Es la id del usuario correspondiente
     * a el usuario en dicha red social.
     * @apiParam {String} accessToken Token emitido por la red social.
     * 
     * @apiSuccess (202) {Object} socialLink Contenido del registro
     * del nuevo vínculo con la red social.
     * @apiSuccess (202) {Number} socialLink.id Id del nuevo registro.
     * @apiSuccess (202) {Number} socialLink.user_id Id del usuario.
     * @apiSuccess (202) {Number} socialLink.extern_id Id del usuario en la red social correspondiente.
     * @apiSuccess (202) {String} socialLink.source Fuente del vinculo con la red social.
     * 
     * @apiError (401) {String} SERVER.USER_SOCIAL_ALREADY_USED Cuando ya
     * está en uso ese vínculo por otro usuario.
     * @apiError (404) {String} SERVER.WRONG_USER Cuando no se localiza el usuario.
     * @apiError (406) {String} SERVER.WRONG_TOKEN Cuando el token de la red
     * social es invalido.
     */
    $router->put('/user/social/link', ['uses' => 'UserController@createSocialLink']);
    /**
     * @api {put} /user Actualizar la información del usuario propio.
     * @apiVersion 0.0.1
     * @apiName UpdateUser
     * @apiGroup User
     * @apiPermission user
     * 
     * @apiHeader (Auth) {String} Authorization Token de autorización.
     *
     * @apiParam {String} [name] Nombre de usuario.
     * @apiParam {String} [first_name] Primer nombre.
     * @apiParam {String} [last_name] Apellido.
     * @apiParam {String} [gender] Genero del usuario 'male' | 'female' | 'other'.
     * @apiParam {String} [phone] Número de teléfono del usuario.
     * 
     * @apiSuccess (201) {Object} user Información del usuario.
     * @apiSuccess (201) {Number} user.id Id del usuario.
     * @apiSuccess (201) {String} user.name Nombre de usuario.
     * @apiSuccess (201) {String} user.first_name Primer nombre del usuario.
     * @apiSuccess (201) {String} user.last_name Apellido del usuario.
     * @apiSuccess (201) {String} [user.gender] Genero del usuario.
     * @apiSuccess (201) {String} user.email Email del usuario.
     * @apiSuccess (201) {Number} [user.media_id] Id de la imagen del usuario.
     * @apiSuccess (201) {String} [user.phone] Telefono del usuario.
     * @apiSuccess (201) {String} user.lang Idioma del usuario.
     * @apiSuccess (201) {String} [user.birthday] Fecha de nacimiento del usuario.
     * @apiSuccess (201) {Boolean} [user.confirmed] Si el correo del usuario está confirmado.
     * @apiSuccess (201) {String} user.source Fuente desde la que se registro el usuario.
     * @apiSuccess (201) {String} user.role Rol del usuario.
     * @apiSuccess (201) {Number} [user.direction_id] Id de la dirección del usuario.
     * @apiSuccess (201) {String} user.created_at Información del usuario.
     * 
     * @apiError (406) {QueryException} error Error al ejecutar la consulta.
     */
    $router->put('/user', ['uses' => 'UserController@updateUser']);
    /**
     * @api {put} /user/email Actualizar el email del usuario propio.
     * @apiVersion 0.0.1
     * @apiName UpdateUserEmail
     * @apiGroup User
     * @apiPermission user
     * 
     * @apiHeader (Auth) {String} Authorization Token de autorización.
     *
     * @apiParam {String} email Nuevo email del usuario.
     * @apiParam {String} source Nombre de la fuente con la que se inicia sesión
     * 'facebook' | 'google' | 'app'.
     * 
     * @apiSuccess (201) {Object} user Información del usuario.
     * @apiSuccess (201) {Number} user.id Id del usuario.
     * @apiSuccess (201) {String} user.name Nombre de usuario.
     * @apiSuccess (201) {String} user.first_name Primer nombre del usuario.
     * @apiSuccess (201) {String} user.last_name Apellido del usuario.
     * @apiSuccess (201) {String} [user.gender] Genero del usuario.
     * @apiSuccess (201) {String} user.email Email del usuario.
     * @apiSuccess (201) {Number} [user.media_id] Id de la imagen del usuario.
     * @apiSuccess (201) {String} [user.phone] Telefono del usuario.
     * @apiSuccess (201) {String} user.lang Idioma del usuario.
     * @apiSuccess (201) {String} [user.birthday] Fecha de nacimiento del usuario.
     * @apiSuccess (201) {Boolean} [user.confirmed] Si el correo del usuario está confirmado.
     * @apiSuccess (201) {String} user.source Fuente desde la que se registro el usuario.
     * @apiSuccess (201) {String} user.role Rol del usuario.
     * @apiSuccess (201) {Number} [user.direction_id] Id de la dirección del usuario.
     * @apiSuccess (201) {String} user.created_at Información del usuario.
     * 
     * @apiError (406) {String} SERVER.USER_EMAIL_ALREADY_EXISTS Cuando un usuario
     * ya tiene un correo registrado.
     * @apiError (406) {QueryException} error Error al ejecutar la consulta.
     */
    $router->put('/user/email', ['uses' => 'UserController@updateUserEmail']);
    /**
     * @api {put} /user/lang Actualizar el idioma del usuario propio.
     * @apiVersion 0.0.1
     * @apiName UpdateUserLang
     * @apiGroup User
     * @apiPermission user
     * 
     * @apiHeader (Auth) {String} Authorization Token de autorización.
     *
     * @apiParam {String} lang Nuevo idioma del usuario.
     * 
     * @apiSuccess (202) {String} lang Regresa el idioma que se registro.
     * 
     * @apiError (404) {String} SERVER.USER_NOT_REGISTRED Cuando el
     * usuario no fue localizado.
     */
    $router->put('/user/lang', ['uses' => 'UserController@updateUserLang']);
    /**
     * @api {put} /user/direction Actualizar la dirección del usuario propio.
     * @apiVersion 0.0.1
     * @apiName UpdateUserDirection
     * @apiGroup User
     * @apiPermission user
     * 
     * @apiHeader (Auth) {String} Authorization Token de autorización.
     *
     * @apiParam {String} [country] País del usuario.
     * @apiParam {String} [administrative_area_level_1] Estado del ususario.
     * @apiParam {String} [administrative_area_level_2] Ciudad del usuario.
     * @apiParam {String} [route] Calle del usuario.
     * @apiParam {String} [street_number] Número del domicilio del usuario.
     * @apiParam {String} [postal_code] Código postal del usuario.
     * @apiParam {String} [lat] Latitud del usuario.
     * @apiParam {String} [lng] Longitud del usuario.
     * 
     * @apiSuccess (201) {Object} [direction] userDirección del usuario.
     * @apiSuccess (201) {String} [direction.id] Información del usuario.
     * @apiSuccess (201) {String} [direction.contry] País de la dirección del usuario.
     * @apiSuccess (201) {String} [direction.administrative_area_level_1] Estado de la dirección del usuario.
     * @apiSuccess (201) {String} [direction.administrative_area_level_2] Municipio de la dirección del usuario.
     * @apiSuccess (201) {String} [direction.route] Calle de la dirección del usuario.
     * @apiSuccess (201) {Number} [direction.street_number] Numero de la dirección del usuario.
     * @apiSuccess (201) {Number} [direction.postal_code] Código postal de la dirección del usuario.
     * @apiSuccess (201) {Number} [direction.lat] Latitud de la dirección del usuario.
     * @apiSuccess (201) {Number} [direction.lng] Longitud postal de la dirección del usuario.
     * 
     * @apiError (406) {QueryException} error Error al ejecutar la consulta.
     */
    $router->put('/user/direction', ['uses' => 'UserController@updateUserDirection']);
    /**
     * @api {delete} /user/social/link/:id Eliminar un vínculo propio
     * con una red social.
     * @apiVersion 0.0.1
     * @apiName DeleteSocialLink
     * @apiGroup User
     * @apiPermission user
     * 
     * @apiHeader (Auth) {String} Authorization Token de autorización.
     *
     * @apiParam {Number} id Id del registro del vínculo con la red social.
     * 
     * @apiSuccess (202) {String} SERVER.SOCIAL_LINK_DELETED Cuando se
     * logró eliminar un vínculo con una red social.
     * 
     * @apiError (404) {String} SERVER.WRONG_SOCIAL_LINK_ID Cuando no se
     * localizó el vínculo con una red social.
     */
    $router->delete('/user/social/link/{id}', ['uses' => 'UserController@deleteSocialLink']);
    /**
     * @api {delete} /user/logout Eliminar un token de autorización propio.
     * @apiVersion 0.0.1
     * @apiName Logout
     * @apiGroup User
     * @apiPermission user
     * 
     * @apiHeader (Auth) {String} Authorization Token de autorización.
     *
     * @apiSuccess (202) {String} SERVER.LOGGEDOUT Cuando se logró eliminar
     * un token de autorización.
     */
    $router->delete('/user/logout', ['uses' => 'UserController@logout']);
    /**
     * @api {delete} /user Eliminar una cuenta de usuario propia.
     * @apiVersion 0.0.1
     * @apiName DeleteUser
     * @apiGroup User
     * @apiPermission user
     * 
     * @apiHeader (Auth) {String} Authorization Token de autorización.
     *
     * @apiSuccess (202) {String} SERVER.USER_DELETED Cuando se eliminó un
     * usuario correctamente.
     * 
     * @apiError (404) {String} SERVER.USER_NOT_FOUND Cuando no se encontró
     * la cuenta de usuario.
     */
    $router->delete('/user', ['uses' => 'UserController@deleteUser']);
    /**
     * @api {post} /pay Para emitir pagos.
     * @apiVersion 0.0.1
     * @apiName CreatePay
     * @apiGroup Pay
     * @apiPermission none
     * 
     * @apiParam {String} id Token de la tarjeta emitidito por mercado pago.
     * @apiParam {Number} installments Numero de plazos.
     * @apiParam {Number} issuer_id Id del banco emisor.
     * @apiParam {Number} payment_method_id Id del método de pago.
     *
     * @apiSuccess (202) {MercadoPagoPayResponse} response Objeto de respuesta
     * emitido por mercado pago.
     * 
     * @apiError (406) {BadResponseException} error Un error obtenido del api
     * del mercado pago en caso de una solicitud errónea.
     */
    $router->post('/user/pay', ['uses' => 'PayController@createPay']);

    $router->post('/pet', ['uses' => 'PetController@store']);
    $router->put('/pet/file', ['uses' => 'PetController@storeFile']);
    $router->put('/pet/{id}', ['uses' => 'PetController@update']);
    $router->delete('/pet/{id}', ['uses' => 'PetController@destroy']);

    $router->get('/health_stats', ['uses' => 'HealthStatController@index']);
    $router->post('/health_stat', ['uses' => 'HealthStatController@store']);
    $router->get('/health_stat/{id}', ['uses' => 'HealthStatController@show']);
    $router->put('/health_stat/{id}', ['uses' => 'HealthStatController@update']);
    $router->delete('/health_stat/{id}', ['uses' => 'HealthStatController@destroy']);

    $router->group(['middleware' => ['isAdmin']], function () use ($router) {
        /**
         * @api {get} /user/:id Obtiene la información de un usuario por su id.
         * @apiVersion 0.0.1
         * @apiName GetUserById
         * @apiGroup Admin
         * @apiPermission admin
         * 
         * @apiHeader (Auth) {String} Authorization Token de autorización.
         *
         * @apiParam {Number} id Id del usuario.
         * 
         * @apiSuccess (200) {Object} user Información del usuario.
         * @apiSuccess (200) {Number} user.id Id del usuario.
         * @apiSuccess (200) {String} user.name Nombre de usuario.
         * @apiSuccess (200) {String} user.first_name Primer nombre del usuario.
         * @apiSuccess (200) {String} user.last_name Apellido del usuario.
         * @apiSuccess (200) {String} [user.gender] Genero del usuario.
         * @apiSuccess (200) {String} user.email Email del usuario.
         * @apiSuccess (200) {Number} [user.media_id] Id de la imagen del usuario.
         * @apiSuccess (200) {Object} [user.media] Información de la imagen del usuario.
         * @apiSuccess (200) {Number} [user.media.id] Id de la imagen del usuario.
         * @apiSuccess (200) {String} [user.media.type] Tipo de la imagen del usuario.
         * @apiSuccess (200) {String} [user.media.url] Url de la imagen del usuario.
         * @apiSuccess (200) {String} [user.media.alt] Alt de la imagen del usuario.
         * @apiSuccess (200) {Number} [user.media.width] Ancho de la imagen del usuario.
         * @apiSuccess (200) {Number} [user.media.height] Alto de la imagen del usuario.
         * @apiSuccess (200) {String} [user.phone] Telefono del usuario.
         * @apiSuccess (200) {String} user.lang Idioma del usuario.
         * @apiSuccess (200) {String} [user.birthday] Fecha de nacimiento del usuario.
         * @apiSuccess (200) {Boolean} [user.confirmed] Si el correo del usuario está confirmado.
         * @apiSuccess (200) {String} user.source Fuente desde la que se registro el usuario.
         * @apiSuccess (200) {String} user.role Rol del usuario.
         * @apiSuccess (200) {Number} [user.direction_id] Id de la dirección del usuario.
         * @apiSuccess (200) {Object} [user.direction] Dirección del usuario.
         * @apiSuccess (200) {String} [user.direction.id] Información del usuario.
         * @apiSuccess (200) {String} [user.direction.contry] País de la dirección del usuario.
         * @apiSuccess (200) {String} [user.direction.administrative_area_level_1] Estado de la dirección del usuario.
         * @apiSuccess (200) {String} [user.direction.administrative_area_level_2] Municipio de la dirección del usuario.
         * @apiSuccess (200) {String} [user.direction.route] Calle de la dirección del usuario.
         * @apiSuccess (200) {Number} [user.direction.street_number] Numero de la dirección del usuario.
         * @apiSuccess (200) {Number} [user.direction.postal_code] Código postal de la dirección del usuario.
         * @apiSuccess (200) {Number} [user.direction.lat] Latitud de la dirección del usuario.
         * @apiSuccess (200) {Number} [user.direction.lng] Longitud postal de la dirección del usuario.
         * @apiSuccess (200) {String} user.created_at Información del usuario.
         * 
         * @apiError (406) {String} SERVER.USER_NOT_FOUND En caso de que no se
         * encuentre el usuario relacionado el token.
         */
        $router->get('/user/{id}', ['uses' => 'UserController@getUserById']);
        /**
         * @api {post} /users Obtiene la lista de usuario.
         * @apiVersion 0.0.1
         * @apiName GetUsers
         * @apiGroup Admin
         * @apiPermission admin
         * 
         * @apiHeader (Auth) {String} Authorization Token de autorización.
         *
         * @apiParam {Number} page Número de la página a consultar.
         * @apiParam {String} search Texto en caso en caso de querer realizar una búsqueda.
         * 
         * @apiSuccess (200) {Object[]} user Información del usuario.
         * @apiSuccess (200) {Number} user.id Id del usuario.
         * @apiSuccess (200) {String} user.name Nombre de usuario.
         * @apiSuccess (200) {String} user.first_name Primer nombre del usuario.
         * @apiSuccess (200) {String} user.last_name Apellido del usuario.
         * @apiSuccess (200) {String} [user.gender] Genero del usuario.
         * @apiSuccess (200) {String} user.email Email del usuario.
         * @apiSuccess (200) {Number} [user.media_id] Id de la imagen del usuario.
         * @apiSuccess (200) {Object} [user.media] Información de la imagen del usuario.
         * @apiSuccess (200) {Number} [user.media.id] Id de la imagen del usuario.
         * @apiSuccess (200) {String} [user.media.type] Tipo de la imagen del usuario.
         * @apiSuccess (200) {String} [user.media.url] Url de la imagen del usuario.
         * @apiSuccess (200) {String} [user.media.alt] Alt de la imagen del usuario.
         * @apiSuccess (200) {Number} [user.media.width] Ancho de la imagen del usuario.
         * @apiSuccess (200) {Number} [user.media.height] Alto de la imagen del usuario.
         * @apiSuccess (200) {String} [user.phone] Telefono del usuario.
         * @apiSuccess (200) {String} user.lang Idioma del usuario.
         * @apiSuccess (200) {String} [user.birthday] Fecha de nacimiento del usuario.
         * @apiSuccess (200) {Boolean} [user.confirmed] Si el correo del usuario está confirmado.
         * @apiSuccess (200) {String} user.source Fuente desde la que se registro el usuario.
         * @apiSuccess (200) {String} user.role Rol del usuario.
         * @apiSuccess (200) {Number} [user.direction_id] Id de la dirección del usuario.
         * @apiSuccess (200) {Object} [user.direction] Dirección del usuario.
         * @apiSuccess (200) {String} [user.direction.id] Información del usuario.
         * @apiSuccess (200) {String} [user.direction.contry] País de la dirección del usuario.
         * @apiSuccess (200) {String} [user.direction.administrative_area_level_1] Estado de la dirección del usuario.
         * @apiSuccess (200) {String} [user.direction.administrative_area_level_2] Municipio de la dirección del usuario.
         * @apiSuccess (200) {String} [user.direction.route] Calle de la dirección del usuario.
         * @apiSuccess (200) {Number} [user.direction.street_number] Numero de la dirección del usuario.
         * @apiSuccess (200) {Number} [user.direction.postal_code] Código postal de la dirección del usuario.
         * @apiSuccess (200) {Number} [user.direction.lat] Latitud de la dirección del usuario.
         * @apiSuccess (200) {Number} [user.direction.lng] Longitud postal de la dirección del usuario.
         * @apiSuccess (200) {String} user.created_at Información del usuario.
         */
        $router->post('/users', ['uses' => 'UserController@getUsers']);
        /**
         * @api {put} /user/:id Actualizar la información de un usuario por su id.
         * @apiVersion 0.0.1
         * @apiName UpdateUserById
         * @apiGroup Admin
         * @apiPermission admin
         * 
         * @apiHeader (Auth) {String} Authorization Token de autorización.
         *
         * @apiParam {Number} id Id del usuario a actualizar.
         * @apiParam {String} [name] Nombre de usuario.
         * @apiParam {String} [first_name] Primer nombre.
         * @apiParam {String} [last_name] Apellido.
         * @apiParam {String} [gender] Genero del usuario 'male' | 'female' | 'other'.
         * @apiParam {String} [phone] Número de teléfono del usuario.
         * 
         * @apiSuccess (201) {Object} user Información del usuario.
         * @apiSuccess (201) {Number} user.id Id del usuario.
         * @apiSuccess (201) {String} user.name Nombre de usuario.
         * @apiSuccess (201) {String} user.first_name Primer nombre del usuario.
         * @apiSuccess (201) {String} user.last_name Apellido del usuario.
         * @apiSuccess (201) {String} [user.gender] Genero del usuario.
         * @apiSuccess (201) {String} user.email Email del usuario.
         * @apiSuccess (201) {Number} [user.media_id] Id de la imagen del usuario.
         * @apiSuccess (201) {String} [user.phone] Telefono del usuario.
         * @apiSuccess (201) {String} user.lang Idioma del usuario.
         * @apiSuccess (201) {String} [user.birthday] Fecha de nacimiento del usuario.
         * @apiSuccess (201) {Boolean} [user.confirmed] Si el correo del usuario está confirmado.
         * @apiSuccess (201) {String} user.source Fuente desde la que se registro el usuario.
         * @apiSuccess (201) {String} user.role Rol del usuario.
         * @apiSuccess (201) {Number} [user.direction_id] Id de la dirección del usuario.
         * @apiSuccess (201) {String} user.created_at Información del usuario.
         * 
         * @apiError (406) {QueryException} error Error al ejecutar la consulta.
         */
        $router->put('/user/{id}', ['uses' => 'UserController@updateUserById']);
        /**
         * @api {put} /user/avatar/:id Establecer el avatar de un usuario por su id.
         * @apiVersion 0.0.1
         * @apiName SetAvatarById
         * @apiGroup Admin
         * @apiPermission admin
         * 
         * @apiHeader (Auth) {String} Authorization Token de autorización.
         *
         * @apiParam {Number} id Id del usuario a actualizar.
         * @apiParam {String} file Es el archivo a almacenar, puede ser de tipo archivo
         * se recomienda usar.
         * @apiParam {String} file_name Nombre del archivo.
         * @apiParam {String} [type] Define el tipo de archivo, en caso de
         * ser base64 se debe indicar.
         * 
         * @apiSuccess (202) {String} fileUrl Url donde se almaceno el archivo.
         */
        $router->put('/user/avatar/{id}', ['uses' => 'UserController@updateAvatarById']);
        /**
         * @api {put} /user/email/:id Actualizar el email de un usuario por su id.
         * @apiVersion 0.0.1
         * @apiName UpdateUserEmailById
         * @apiGroup Admin
         * @apiPermission admin
         * 
         * @apiHeader (Auth) {String} Authorization Token de autorización.
         *
         * @apiParam {Number} id Id del usuario a actualizar.
         * @apiParam {String} email Nuevo email del usuario.
         * @apiParam {String} source Nombre de la fuente con la que se inicia sesión
         * 'facebook' | 'google' | 'app'.
         * 
         * @apiSuccess (201) {Object} user Información del usuario.
         * @apiSuccess (201) {Number} user.id Id del usuario.
         * @apiSuccess (201) {String} user.name Nombre de usuario.
         * @apiSuccess (201) {String} user.first_name Primer nombre del usuario.
         * @apiSuccess (201) {String} user.last_name Apellido del usuario.
         * @apiSuccess (201) {String} [user.gender] Genero del usuario.
         * @apiSuccess (201) {String} user.email Email del usuario.
         * @apiSuccess (201) {Number} [user.media_id] Id de la imagen del usuario.
         * @apiSuccess (201) {String} [user.phone] Telefono del usuario.
         * @apiSuccess (201) {String} user.lang Idioma del usuario.
         * @apiSuccess (201) {String} [user.birthday] Fecha de nacimiento del usuario.
         * @apiSuccess (201) {Boolean} [user.confirmed] Si el correo del usuario está confirmado.
         * @apiSuccess (201) {String} user.source Fuente desde la que se registro el usuario.
         * @apiSuccess (201) {String} user.role Rol del usuario.
         * @apiSuccess (201) {Number} [user.direction_id] Id de la dirección del usuario.
         * @apiSuccess (201) {String} user.created_at Información del usuario.
         * 
         * @apiError (406) {String} SERVER.USER_EMAIL_ALREADY_EXISTS Cuando un usuario
         * ya tiene un correo registrado.
         * @apiError (406) {QueryException} error Error al ejecutar la consulta.
         */
        $router->put('/user/email/{id}', ['uses' => 'UserController@updateUserEmailById']);
        /**
         * @api {put} /user/lang/:id Actualizar el idioma de un usuario por su id.
         * @apiVersion 0.0.1
         * @apiName UpdateUserLangById
         * @apiGroup Admin
         * @apiPermission admin
         * 
         * @apiHeader (Auth) {String} Authorization Token de autorización.
         *
         * @apiParam {Number} id Id del usuario a actualizar.
         * @apiParam {String} lang Nuevo idioma del usuario.
         * 
         * @apiSuccess (202) {String} lang Regresa el idioma que se registro.
         * 
         * @apiError (404) {String} SERVER.USER_NOT_REGISTRED Cuando el
         * usuario no fue localizado.
         */
        $router->put('/user/lang/{id}', ['uses' => 'UserController@updateUserLangById']);
        /**
         * @api {put} /user/direction/:id Actualizar la dirección de un usuario por su id.
         * @apiVersion 0.0.1
         * @apiName UpdateUserDirectionById
         * @apiGroup Admin
         * @apiPermission admin
         * 
         * @apiHeader (Auth) {String} Authorization Token de autorización.
         *
         * @apiParam {Number} id Id del usuario a actualizar.
         * @apiParam {String} [country] País del usuario.
         * @apiParam {String} [administrative_area_level_1] Estado del ususario.
         * @apiParam {String} [administrative_area_level_2] Ciudad del usuario.
         * @apiParam {String} [route] Calle del usuario.
         * @apiParam {String} [street_number] Número del domicilio del usuario.
         * @apiParam {String} [postal_code] Código postal del usuario.
         * @apiParam {String} [lat] Latitud del usuario.
         * @apiParam {String} [lng] Longitud del usuario.
         * 
         * @apiSuccess (201) {Direction} userDirection Información de la
         * dirección del usuario.
         * 
         * @apiError (406) {QueryException} error Error al ejecutar la consulta.
         */
        $router->put('/user/direction/{id}', ['uses' => 'UserController@updateUserDirectionById']);
        /**
         * @api {put} /user/role/:id Actualizar el rol del usuario por su id.
         * @apiVersion 0.0.1
         * @apiName UpdateUserDirectionById
         * @apiGroup Admin
         * @apiPermission admin
         * 
         * @apiHeader (Auth) {String} Authorization Token de autorización.
         *
         * @apiParam {Number} id Id del usuario a actualizar.
         * @apiParam {String} [role] Nuevo rol del usuario.
         * 
         * @apiSuccess (202) {String} role Nuevo rol del usuario.
         * 
         * @apiError (404) {String} SERVER.USER_NOT_REGISTRED Cuando el usuario no está registrado.
         */
        $router->put('/user/role/{id}', ['uses' => 'UserController@setUserRoleById']);
        /**
         * @api {delete} /user Eliminar una cuenta de usuario por su id.
         * @apiVersion 0.0.1
         * @apiName DeleteUser
         * @apiGroup User
         * @apiPermission user
         * 
         * @apiHeader (Auth) {String} Authorization Token de autorización.
         *
         * @apiParam {Number} id Id del usuario a actualizar.
         * 
         * @apiSuccess (202) {String} SERVER.USER_DELETED Cuando se eliminó un
         * usuario correctamente.
         * 
         * @apiError (404) {String} SERVER.USER_NOT_FOUND Cuando no se encontró
         * la cuenta de usuario.
         */
        $router->delete('/user/{id}', ['uses' => 'UserController@deleteUserById']);
        /**
         * @api {post} /bulletin Crear una nueva noticia.
         * @apiVersion 0.0.1
         * @apiName Create Bulletin
         * @apiGroup Bulletin
         * @apiPermission admin
         * 
         * @apiHeader (Auth) {String} Authorization Token de autorización.
         *
         * @apiParam {String} title Título de la noticia.
         * @apiParam {String} description Descripción de la noticia.
         * @apiParam {String} date Fecha de la noticia.
         * 
         * @apiSuccess (202) {Bulletin} bulletin Noticia creada.
         */
        $router->post('/bulletin', ['uses' => 'BulletinController@create']);
        /**
         * @api {put} /bulletin/img Establece la imagen de la noticia.
         * @apiVersion 0.0.1
         * @apiName SetImg
         * @apiGroup Bulletin
         * @apiPermission admin
         * 
         * @apiHeader (Auth) {String} Authorization Token de autorización.
         *
         * @apiParam {File} file Es el archivo a almacenar.
         * @apiParam {String} file_name Nombre del archivo.
         * @apiParam {String} [type] Define el tipo de archivo, en caso de
         * ser base64 se debe indicar.
         * 
         * @apiSuccess (202) {Bulletin} bulletin Noticia creada.
         */
        $router->put('/bulletin/img', ['uses' => 'BulletinController@setImg']);
        /**
         * @api {put} /bulletin Actualizar una noticia.
         * @apiVersion 0.0.1
         * @apiName UpdateBulletin
         * @apiGroup Bulletin
         * @apiPermission admin
         * 
         * @apiHeader (Auth) {String} Authorization Token de autorización.
         *
         * @apiParam {String} [title] Título de la noticia.
         * @apiParam {String} [description] Descripción de la noticia.
         * @apiParam {String} [date] Fecha de la noticia.
         * @apiParam {Number} [media_id] Referencia a un archivo de medios.
         * 
         * @apiSuccess (202) {Bulletin} bulletin Noticia creada.
         */
        $router->put('/bulletin', ['uses' => 'BulletinController@updateBulletin']);
        /**
         * @api {delete} /bulletin/:id Eliminar una noticia por su id.
         * @apiVersion 0.0.1
         * @apiName DeleteBulletin
         * @apiGroup Bulletin
         * @apiPermission admin
         * 
         * @apiHeader (Auth) {String} Authorization Token de autorización.
         *
         * @apiParam {Number} id Id de la noticia.
         * 
         * @apiSuccess (202) {String} SERVER.BULLETIN_DELETED Noticia creada.
         * 
         * @apiError (404) {String} SERVER.BULLETIN_NOT_FOUND Cuando no se
         * encontró una noticia
         */
        $router->delete('/bulletin/{id}', ['uses' => 'BulletinController@deleteBulletin']);
        /**
         * @api {get} /analytics Obtener las analíticas de los usuario en la base de datos.
         * @apiVersion 0.0.1
         * @apiName GetBasicAnalytics
         * @apiGroup Analytics
         * @apiPermission admin
         * 
         * @apiHeader (Auth) {String} Authorization Token de autorización.
         *
         * @apiSuccess (200) {Object} analytics Objeto con la información
         * de los usuarios en la base de datos.
         * @apiSuccess (200) {Number} analytics.users_number Numero de usuarios.
         * @apiSuccess (200) {Object} analytics.gender Objeto con la información del genero
         * de los usuarios.
         * @apiSuccess (200) {Number} analytics.gender.male_number Numero de hombres.
         * @apiSuccess (200) {Number} analytics.gender.female_number Numero de mujeres.
         * @apiSuccess (200) {Number} analytics.supports_number Numero de solicitudes de soporte.
         * @apiSuccess (200) {Object} analytics.ages Objeto con la información
         * de la edad de los usuarios.
         * @apiSuccess (200) {Number} analytics.ages.children Numero de niños.
         * @apiSuccess (200) {Number} analytics.ages.teens Numero de adolecentes.
         * @apiSuccess (200) {Number} analytics.ages.young_adults Numero de adultos jovenes
         * @apiSuccess (200) {Number} analytics.ages.unknown Numero de usuarios con edad desconocida.
         * @apiSuccess (200) {Object} analytics.sources Objeto con la información
         * del origen desde donde se registraron los usuarios.
         * @apiSuccess (200) {Number} analytics.sources.app Numero de ususarios registrados desde la aplicación.
         * @apiSuccess (200) {Number} analytics.sources.facebook Numero de ususarios registrados desde facebook.
         * @apiSuccess (200) {Number} analytics.sources.google Numero de ususarios registrados desde google.
         */
        $router->get('/analytics', ['uses' => 'AnalyticController@getBasicAnalytics']);
        /**
         * @api {post} /analytics/users/registration Obtener la cantidad de usuario
         * registrados en la base de datos.
         * @apiVersion 0.0.1
         * @apiName GetUsersRegistration
         * @apiGroup Analytics
         * @apiPermission admin
         * 
         * @apiHeader (Auth) {String} Authorization Token de autorización.
         *
         * @apiParam {String} interval Intervalo de tiempo a consultar.
         * 
         * @apiSuccess (200) {Object[]} analytics Objeto con la información
         * de los usuarios en la base de datos.
         * @apiSuccess (200) {String} analytics.created_at Fecha en que se registraron.
         * @apiSuccess (200) {Number} analytics.users Numero de ususario.
         */
        $router->post('/analytics/users/registration', ['uses' => 'AnalyticController@getUsersRegistration']);
    });
});