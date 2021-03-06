<?php
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
 * @apiParam {String} grant_type Fuente con la que se crea
 * la cuenta 'facebook' | 'google' | 'password'.
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
$router->post('/user', ['uses' => 'User\UserController@store']);
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
$router->get('/user/confirm/email', ['as' => 'user.confirm.email', 'uses' => 'User\UserController@confirmEmail']);

$router->get('/user/contact/{id}', ['uses' => 'User\UserContactController@getUserContactById']);

$router->group(['middleware' => ['auth:api']], function () use ($router) {
    $router->get('/user/permissions', ['uses' => 'User\UserPermissionsController@getUserPermissions']);
    $router->put('/user/permissions', ['uses' => 'User\UserPermissionsController@setUserPermissions']);

    $router->post('/user/cat/emails', ['uses' => 'User\UserCatEmailsPhonesController@addEmails']);
    $router->post('/user/cat/phones', ['uses' => 'User\UserCatEmailsPhonesController@addPhones']);
    $router->delete('/user/cat/email/{id}', ['uses' => 'User\UserCatEmailsPhonesController@deleteEmail']);
    $router->delete('/user/cat/phone/{id}', ['uses' => 'User\UserCatEmailsPhonesController@deletePhone']);

    $router->get('/user/contact', ['uses' => 'User\UserContactController@getUserContact']);
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
     * @apiSuccess (200) {String} user.grant_type Fuente desde la que se registro el usuario.
     * @apiSuccess (200) {String} user.role Rol del usuario.
     * @apiSuccess (200) {Number} [user.address_id] Id de la dirección del usuario.
     * @apiSuccess (200) {Object} [user.address] Dirección del usuario.
     * @apiSuccess (200) {String} [user.address.id] Información del usuario.
     * @apiSuccess (200) {String} [user.address.contry] País de la dirección del usuario.
     * @apiSuccess (200) {String} [user.address.administrative_area_level_1] Estado de la dirección del usuario.
     * @apiSuccess (200) {String} [user.address.administrative_area_level_2] Municipio de la dirección del usuario.
     * @apiSuccess (200) {String} [user.address.route] Calle de la dirección del usuario.
     * @apiSuccess (200) {Number} [user.address.street_number] Numero de la dirección del usuario.
     * @apiSuccess (200) {Number} [user.address.postal_code] Código postal de la dirección del usuario.
     * @apiSuccess (200) {Number} [user.address.lat] Latitud de la dirección del usuario.
     * @apiSuccess (200) {Number} [user.address.lng] Longitud postal de la dirección del usuario.
     * @apiSuccess (200) {String} user.created_at Información del usuario.
     * 
     * @apiError (406) {String} SERVER.USER_NOT_FOUND En caso de que
     * no se encuentre el usuario relacionado el token.
     */
    $router->get('/user', ['uses' => 'User\UserController@index']);
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
     * @apiSuccess (200) {String} SERVER.USER_ALREADY_CONFIRMED En caso
     * de que el email del usuario ya este confirmado.
     * @apiSuccess (200) {String} SERVER.EMAIL_SEND Email de confirmación enviado.
     * 
     * @apiError (404) {String} SERVER.USER_NOT_FOUND No se encontró el
     * usuario a confirmar.
     * @apiError (400) {String} SERVER.EMAIL_FAIL Cuando no se logra
     * enviar le email.
     */
    $router->post('/user/confirm/email', ['uses' => 'User\UserController@reSendConfirmEmail']);
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
    $router->put('/user/avatar', ['uses' => 'User\UserController@updateAvatar']);
    /**
     * @api {put} /user/social/link Establecer el avatar del usuario propio.
     * @apiVersion 0.0.1
     * @apiName CreateSocialLink
     * @apiGroup User
     * @apiPermission user
     * 
     * @apiHeader (Auth) {String} Authorization Token de autorización.
     *
     * @apiParam {String} grant_type Es el nombre de la red social a vincular.
     * @apiParam {String} extern_id Es la id del usuario correspondiente
     * a el usuario en dicha red social.
     * @apiParam {String} accessToken Token emitido por la red social.
     * 
     * @apiSuccess (202) {Object} socialLink Contenido del registro
     * del nuevo vínculo con la red social.
     * @apiSuccess (202) {Number} socialLink.id Id del nuevo registro.
     * @apiSuccess (202) {Number} socialLink.user_id Id del usuario.
     * @apiSuccess (202) {Number} socialLink.extern_id Id del usuario en la red social correspondiente.
     * @apiSuccess (202) {String} socialLink.grant_type Fuente del vinculo con la red social.
     * 
     * @apiError (401) {String} SERVER.USER_SOCIAL_ALREADY_USED Cuando ya
     * está en uso ese vínculo por otro usuario.
     * @apiError (404) {String} SERVER.WRONG_USER Cuando no se localiza el usuario.
     * @apiError (406) {String} SERVER.WRONG_TOKEN Cuando el token de la red
     * social es invalido.
     */
    $router->put('/user/social/link', ['uses' => 'User\UserController@createSocialLink']);
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
     * @apiSuccess (201) {String} user.grant_type Fuente desde la que se registro el usuario.
     * @apiSuccess (201) {String} user.role Rol del usuario.
     * @apiSuccess (201) {Number} [user.address_id] Id de la dirección del usuario.
     * @apiSuccess (201) {String} user.created_at Información del usuario.
     * 
     * @apiError (406) {QueryException} error Error al ejecutar la consulta.
     */
    $router->put('/user', ['uses' => 'User\UserController@updateUser']);
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
     * @apiParam {String} grant_type Nombre de la fuente con la que se inicia sesión
     * 'facebook' | 'google' | 'password'.
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
     * @apiSuccess (201) {String} user.grant_type Fuente desde la que se registro el usuario.
     * @apiSuccess (201) {String} user.role Rol del usuario.
     * @apiSuccess (201) {Number} [user.address_id] Id de la dirección del usuario.
     * @apiSuccess (201) {String} user.created_at Información del usuario.
     * 
     * @apiError (406) {String} SERVER.USER_EMAIL_ALREADY_EXISTS Cuando un usuario
     * ya tiene un correo registrado.
     * @apiError (406) {QueryException} error Error al ejecutar la consulta.
     */
    $router->put('/user/email', ['uses' => 'User\UserController@updateUserEmail']);
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
    $router->put('/user/lang', ['uses' => 'User\UserController@updateUserLang']);
    /**
     * @api {put} /user/address Actualizar la dirección del usuario propio.
     * @apiVersion 0.0.1
     * @apiName UpdateUserAddress
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
     * @apiSuccess (201) {Object} [address] userDirección del usuario.
     * @apiSuccess (201) {String} [address.id] Información del usuario.
     * @apiSuccess (201) {String} [address.contry] País de la dirección del usuario.
     * @apiSuccess (201) {String} [address.administrative_area_level_1] Estado de la dirección del usuario.
     * @apiSuccess (201) {String} [address.administrative_area_level_2] Municipio de la dirección del usuario.
     * @apiSuccess (201) {String} [address.route] Calle de la dirección del usuario.
     * @apiSuccess (201) {Number} [address.street_number] Numero de la dirección del usuario.
     * @apiSuccess (201) {Number} [address.postal_code] Código postal de la dirección del usuario.
     * @apiSuccess (201) {Number} [address.lat] Latitud de la dirección del usuario.
     * @apiSuccess (201) {Number} [address.lng] Longitud postal de la dirección del usuario.
     * 
     * @apiError (406) {QueryException} error Error al ejecutar la consulta.
     */
    $router->put('/user/address', ['uses' => 'User\UserController@updateUserAddress']);
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
     * @apiSuccess (204) {Null} Null Cuando se
     * logró eliminar un vínculo con una red social.
     * 
     * @apiError (404) {String} SERVER.WRONG_SOCIAL_LINK_ID Cuando no se
     * localizó el vínculo con una red social.
     */
    $router->delete('/user/social/link/{id}', ['uses' => 'User\UserController@deleteSocialLink']);
    /**
     * @api {delete} /user/sesion Eliminar un token de autorización propio.
     * @apiVersion 0.0.1
     * @apiName Logout
     * @apiGroup User
     * @apiPermission user
     * 
     * @apiHeader (Auth) {String} Authorization Token de autorización.
     *
     * @apiSuccess (204) {Null} Null Cuando se logró eliminar
     * un token de autorización.
     */
    $router->delete('/user/sesion', ['uses' => 'User\UserController@logout']);
    /**
     * @api {delete} /user Eliminar una cuenta de usuario propia.
     * @apiVersion 0.0.1
     * @apiName DeleteUser
     * @apiGroup User
     * @apiPermission user
     * 
     * @apiHeader (Auth) {String} Authorization Token de autorización.
     *
     * @apiSuccess (204) {Null} Null Cuando se eliminó un
     * usuario correctamente.
     * 
     * @apiError (404) {String} SERVER.USER_NOT_FOUND Cuando no se encontró
     * la cuenta de usuario.
     */
    $router->delete('/user', ['uses' => 'User\UserController@deleteUser']);

    $router->group(['middleware' => ['hasRole:admin']], function () use ($router) {
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
         * @apiSuccess (200) {String} user.grant_type Fuente desde la que se registro el usuario.
         * @apiSuccess (200) {String} user.role Rol del usuario.
         * @apiSuccess (200) {Number} [user.address_id] Id de la dirección del usuario.
         * @apiSuccess (200) {Object} [user.address] Dirección del usuario.
         * @apiSuccess (200) {String} [user.address.id] Información del usuario.
         * @apiSuccess (200) {String} [user.address.contry] País de la dirección del usuario.
         * @apiSuccess (200) {String} [user.address.administrative_area_level_1] Estado de la dirección del usuario.
         * @apiSuccess (200) {String} [user.address.administrative_area_level_2] Municipio de la dirección del usuario.
         * @apiSuccess (200) {String} [user.address.route] Calle de la dirección del usuario.
         * @apiSuccess (200) {Number} [user.address.street_number] Numero de la dirección del usuario.
         * @apiSuccess (200) {Number} [user.address.postal_code] Código postal de la dirección del usuario.
         * @apiSuccess (200) {Number} [user.address.lat] Latitud de la dirección del usuario.
         * @apiSuccess (200) {Number} [user.address.lng] Longitud postal de la dirección del usuario.
         * @apiSuccess (200) {String} user.created_at Información del usuario.
         * 
         * @apiError (406) {String} SERVER.USER_NOT_FOUND En caso de que no se
         * encuentre el usuario relacionado el token.
         */
        $router->get('/user/{id}', ['uses' => 'User\UserController@getUserById']);
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
         * @apiSuccess (200) {String} user.grant_type Fuente desde la que se registro el usuario.
         * @apiSuccess (200) {String} user.role Rol del usuario.
         * @apiSuccess (200) {Number} [user.address_id] Id de la dirección del usuario.
         * @apiSuccess (200) {Object} [user.address] Dirección del usuario.
         * @apiSuccess (200) {String} [user.address.id] Información del usuario.
         * @apiSuccess (200) {String} [user.address.contry] País de la dirección del usuario.
         * @apiSuccess (200) {String} [user.address.administrative_area_level_1] Estado de la dirección del usuario.
         * @apiSuccess (200) {String} [user.address.administrative_area_level_2] Municipio de la dirección del usuario.
         * @apiSuccess (200) {String} [user.address.route] Calle de la dirección del usuario.
         * @apiSuccess (200) {Number} [user.address.street_number] Numero de la dirección del usuario.
         * @apiSuccess (200) {Number} [user.address.postal_code] Código postal de la dirección del usuario.
         * @apiSuccess (200) {Number} [user.address.lat] Latitud de la dirección del usuario.
         * @apiSuccess (200) {Number} [user.address.lng] Longitud postal de la dirección del usuario.
         * @apiSuccess (200) {String} user.created_at Información del usuario.
         */
        $router->get('/users', ['uses' => 'User\UserController@getUsers']);
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
         * @apiSuccess (201) {String} user.grant_type Fuente desde la que se registro el usuario.
         * @apiSuccess (201) {String} user.role Rol del usuario.
         * @apiSuccess (201) {Number} [user.address_id] Id de la dirección del usuario.
         * @apiSuccess (201) {String} user.created_at Información del usuario.
         * 
         * @apiError (406) {QueryException} error Error al ejecutar la consulta.
         */
        $router->put('/user/{id}', ['uses' => 'User\UserController@updateUserById']);
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
        $router->put('/user/avatar/{id}', ['uses' => 'User\UserController@updateAvatarById']);
        /**
         * @api {put} /user/email/:id Actualizar el email de un usuario por su id.
         * @apiVersion 0.0.1
         * @apiName UpdateUserEmailById
         * @apiGroup Admin
         * @apiPermission admin
         * 
         * @apiHeader (Auth) {String} Authorization Token de autorización.
         *
         * @apiParam {String} email Nuevo email del usuario.
         * @apiParam {String} grant_type Nombre de la fuente con la que se inicia sesión
         * 'facebook' | 'google' | 'password'.
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
         * @apiSuccess (201) {String} user.grant_type Fuente desde la que se registro el usuario.
         * @apiSuccess (201) {String} user.role Rol del usuario.
         * @apiSuccess (201) {Number} [user.address_id] Id de la dirección del usuario.
         * @apiSuccess (201) {String} user.created_at Información del usuario.
         * 
         * @apiError (406) {String} SERVER.USER_EMAIL_ALREADY_EXISTS Cuando un usuario
         * ya tiene un correo registrado.
         * @apiError (404) {String} SERVER.USER_NOT_FOUND Cuando no se encontró
         */
        $router->put('/user/email/{id}', ['uses' => 'User\UserController@updateUserEmailById']);
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
        $router->put('/user/lang/{id}', ['uses' => 'User\UserController@updateUserLangById']);
        /**
         * @api {put} /user/address/:id Actualizar la dirección de un usuario por su id.
         * @apiVersion 0.0.1
         * @apiName UpdateUserAddressById
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
         * @apiSuccess (201) {Address} userAddress Información de la
         * dirección del usuario.
         * 
         * @apiError (406) {QueryException} error Error al ejecutar la consulta.
         */
        $router->put('/user/address/{id}', ['uses' => 'User\UserController@updateUserAddressById']);
        /**
         * @api {put} /user/role/:id Actualizar el rol del usuario por su id.
         * @apiVersion 0.0.1
         * @apiName UpdateUserAddressById
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
        $router->put('/user/role/{id}', ['uses' => 'User\UserController@setUserRoleById']);
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
         * @apiSuccess (204) {Null} Null Cuando se eliminó un
         * usuario correctamente.
         * 
         * @apiError (404) {String} SERVER.USER_NOT_FOUND Cuando no se encontró
         * la cuenta de usuario.
         */
        $router->delete('/user/{id}', ['uses' => 'User\UserController@deleteUserById']);
    });
});