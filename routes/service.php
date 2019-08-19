<?php
/**
 * @api {get} / Obtiene la versión de laravel lumen.
 * @apiVersion 0.0.1
 * @apiName Version
 * @apiGroup Service
 * @apiPermission none
 *
 * @apiSuccess (200) {String} version Versión de laravel lumen.
 */
$router->get('/', function () use ($router) {
    return $router->app->version();
});

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
$router->post('/support', ['uses' => 'Service\SupportController@create']);

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
$router->get('/bulletins', ['uses' => 'Service\BulletinController@getBulletins']);

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
$router->get('/bulletin/{id}', ['uses' => 'Service\BulletinController@show']);

$router->get('/posts', ['uses' => 'Service\WallController@index']);
$router->get('/post/{id}', ['uses' => 'Service\WallController@show']);

$router->group(['middleware' => ['auth:api']], function () use ($router) {

    $router->post('/post', ['uses' => 'Service\WallController@store']);
    $router->delete('/post/file/{id}', ['uses' => 'Service\WallController@destroyFile']);
    $router->put('/post/file', ['uses' => 'Service\WallController@storeFile']);
    $router->put('/post/{id}', ['uses' => 'Service\WallController@update']);
    $router->delete('/post/{id}', ['uses' => 'Service\WallController@destroy']);

    $router->group(['middleware' => ['hasRole:admin']], function () use ($router) {

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
        $router->post('/bulletin', ['uses' => 'Service\BulletinController@create']);
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
        $router->put('/bulletin/img', ['uses' => 'Service\BulletinController@setImg']);
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
        $router->put('/bulletin', ['uses' => 'Service\BulletinController@updateBulletin']);
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
         * @apiSuccess (204) {Null} Null Noticia eliminada.
         * 
         * @apiError (404) {String} SERVER.BULLETIN_NOT_FOUND Cuando no se
         * encontró una noticia
         */
        $router->delete('/bulletin/{id}', ['uses' => 'Service\BulletinController@deleteBulletin']);
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
         * @apiSuccess (200) {Object} analytics.grant_types Objeto con la información
         * del origen desde donde se registraron los usuarios.
         * @apiSuccess (200) {Number} analytics.grant_types.app Numero de ususarios registrados desde la aplicación.
         * @apiSuccess (200) {Number} analytics.grant_types.facebook Numero de ususarios registrados desde facebook.
         * @apiSuccess (200) {Number} analytics.grant_types.google Numero de ususarios registrados desde google.
         */
        $router->get('/analytics', ['uses' => 'Service\AnalyticController@getBasicAnalytics']);
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
        $router->get('/analytics/users/registration', ['uses' => 'Service\AnalyticController@getUsersRegistration']);
    });
});