<?php
$router->group(['middleware' => ['auth:api']], function () use ($router) {

    $router->get('/order/product/{id}', ['uses' => 'Store\OrderProductController@show']);
    $router->post('/order/product', ['uses' => 'Store\OrderProductController@store']);
    $router->put('/order/product/{id}', ['uses' => 'Store\OrderProductController@update']);
    $router->delete('/order/product/{id}', ['uses' => 'Store\OrderProductController@destroy']);

    $router->group(['middleware' => ['hasRole:roundsman,supervisor,admin']], function () use ($router) {

        $router->get('/warehouses', ['uses' => 'Store\WarehouseController@index']);
        $router->get('/warehouse/{id}', ['uses' => 'Store\WarehouseController@show']);
        
        $router->get('/products', ['uses' => 'Store\ProductController@index']);
        $router->get('/product/{id}', ['uses' => 'Store\ProductController@show']);

        $router->get('/orders', ['uses' => 'Store\OrderController@index']);
        $router->get('/order/{id}', ['uses' => 'Store\OrderController@show']);
        $router->put('/order/state/{id}', ['uses' => 'Store\OrderController@updateState']);

        $router->get('/order/product/{id}', ['uses' => 'Store\ProductController@show']);

    });

    $router->group(['middleware' => ['hasRole:supervisor,admin']], function () use ($router) {

        $router->post('/warehouse', ['uses' => 'Store\WarehouseController@store']);
        $router->put('/warehouse/{id}', ['uses' => 'Store\WarehouseController@update']);
        $router->put('/warehouse/file/{id}', ['uses' => 'Store\WarehouseController@storeFile']);

        $router->post('/product', ['uses' => 'Store\ProductController@store']);
        $router->put('/product/{id}', ['uses' => 'Store\ProductController@update']);
        $router->put('/product/file/{id}', ['uses' => 'Store\ProductController@storeFile']);
        $router->delete('/product/file/{id}', ['uses' => 'Store\ProductController@destroyFile']);

        $router->post('/order', ['uses' => 'Store\OrderController@store']);
        $router->put('/order/{id}', ['uses' => 'Store\OrderController@update']);
        $router->delete('/order/{id}', ['uses' => 'Store\OrderController@destroy']);

    });

    $router->group(['middleware' => ['hasRole:admin']], function () use ($router) {
        
        $router->delete('/warehouse/{id}', ['uses' => 'Store\WarehouseController@destroy']);

        $router->delete('/product/{id}', ['uses' => 'Store\ProductController@destroy']);
        
    });
});