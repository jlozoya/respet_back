<?php

$router->get('/paypal/checkout-success', 'Store\PayPalController@getExpressCheckoutSuccess');

$router->group(['middleware' => ['auth:api']], function () use ($router) {

    $router->post('/order', ['uses' => 'Store\OrderController@store']);
    $router->put('/order/{id}', ['uses' => 'Store\OrderController@update']);
    $router->delete('/order/{id}', ['uses' => 'Store\OrderController@destroy']);
    
    $router->get('/order/product/{id}', ['uses' => 'Store\OrderProductController@show']);
    $router->post('/order/product', ['uses' => 'Store\OrderProductController@store']);
    $router->put('/order/product/{id}', ['uses' => 'Store\OrderProductController@update']);
    $router->delete('/order/product/{id}', ['uses' => 'Store\OrderProductController@destroy']);

    $router->get('/order', ['uses' => 'Store\OrderController@showLastOrder']);
    $router->get('/order/{id}', ['uses' => 'Store\OrderController@show']);
    $router->get('/order/product/{id}', ['uses' => 'Store\ProductController@show']);

    $router->get('/paypal/checkout/order/{id}', 'Store\PayPalController@getExpressCheckoutByOrderId');
    $router->get('/paypal/adaptive-pay', 'Store\PayPalController@getAdaptivePay');
    $router->post('/paypal/notify', 'Store\PayPalController@notify');

    $router->get('/warehouses', ['uses' => 'Store\WarehouseController@index']);
    $router->get('/warehouse/{id}', ['uses' => 'Store\WarehouseController@show']);
    
    $router->get('/products', ['uses' => 'Store\ProductController@index']);
    $router->get('/product/{id}', ['uses' => 'Store\ProductController@show']);
    
    $router->group(['middleware' => ['hasRole:roundsman,supervisor,admin']], function () use ($router) {

        $router->get('/orders', ['uses' => 'Store\OrderController@index']);
        $router->put('/order/state/{id}', ['uses' => 'Store\OrderController@updateState']);

    });

    $router->group(['middleware' => ['hasRole:supervisor,admin']], function () use ($router) {

        $router->post('/warehouse', ['uses' => 'Store\WarehouseController@store']);
        $router->put('/warehouse/{id}', ['uses' => 'Store\WarehouseController@update']);
        $router->put('/warehouse/file/{id}', ['uses' => 'Store\WarehouseController@storeFile']);

        $router->post('/product', ['uses' => 'Store\ProductController@store']);
        $router->put('/product/{id}', ['uses' => 'Store\ProductController@update']);
        $router->put('/product/file/{id}', ['uses' => 'Store\ProductController@storeFile']);
        $router->delete('/product/file/{id}', ['uses' => 'Store\ProductController@destroyFile']);

    });

    $router->group(['middleware' => ['hasRole:admin']], function () use ($router) {
        
        $router->delete('/warehouse/{id}', ['uses' => 'Store\WarehouseController@destroy']);

        $router->delete('/product/{id}', ['uses' => 'Store\ProductController@destroy']);
        
    });
});