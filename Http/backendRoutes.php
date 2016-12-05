<?php

use Illuminate\Routing\Router;
/** @var Router $router */

$router->group(['prefix' =>'/iblog'], function (Router $router) {

    \CRUD::resource('iblog','category', 'CategoryController');
    \CRUD::resource('iblog','post', 'PostController');
    \CRUD::resource('iblog','tag', 'TagController');
});

