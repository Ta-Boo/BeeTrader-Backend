<?php

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->group(['prefix' => 'api'], function () use ($router) {
    $router->post(
        'auth/login', ['uses' => 'UserController@authenticate']
    );

    $router->post('register', 'AuthController@register');
    $router->post('login', 'AuthController@login');
    $router->post('logout', 'AuthController@logout');

    $router->get('listings/inRadius',  ['uses' => 'ListingController@listingInRadius']);


    $router->get('users',  ['uses' => 'UserController@getUsers']);
    $router->post('user',  ['uses' => 'UserController@updateUser']);
    $router->get('user/{id}', ['uses' => 'UserController@getUser']);
    $router->get('userByEmail', ['uses' => 'UserController@getUserByEmail']);
    $router->get('user/detail/{id}', ['uses' => 'UserController@getUserDetail']);
    $router->get('user/address/{id}', ['uses' => 'UserController@getUserAddress']);
});
