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

$router->group(['prefix' => 'api'], function() use ($router) {

    $router->post('register', 'AuthController@register');
    $router->post('login', 'AuthController@login');

    $router->group(['prefix' => 'user'], function() use($router) {
        $router->get('/', 'UserController@index');
    });

    $router->group(['prefix' => 'post'], function() use($router) {
        $router->get('/', 'PostController@index');
        $router->get('all', 'PostController@all');
        $router->post('upload', 'PostController@upload');
        $router->get('{id}', 'PostController@detail');
        $router->post('{id}/update', 'PostController@update');
        $router->get('{id}/delete', 'PostController@delete');

        $router->group(['prefix' => 'comment/{id}'], function() use($router) {
            $router->post('/', 'RatingController@add');
            $router->post('/update', 'RatingController@update');
            $router->get('/delete', 'RatingController@delete');
        });
    });
});

