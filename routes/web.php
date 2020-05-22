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
        $router->post('add', 'PostController@add');
        $router->post('search', 'PostController@search');
        $router->get('{id}', 'PostController@detail');
        $router->post('{id}/update', 'PostController@update');
        $router->get('{id}/delete', 'PostController@delete');

        $router->group(['prefix' => '{id}/comment'], function() use($router) {
            $router->get('/', 'CommentController@index');
            $router->post('/add', 'CommentController@add');
            $router->post('/update', 'CommentController@update');
            $router->get('/delete', 'CommentController@delete');
        });

        $router->get('image/{id}/delete', 'PostController@deleteImage');
    });

    $router->group(['prefix' => 'category'], function() use($router) {
        $router->get('/', 'CategoryController@index');
        $router->post('/add', 'CategoryController@add');
        $router->post('/{id}/update', 'CategoryController@update');
        $router->get('/{id}/delete', 'CategoryController@delete');
    });
});

