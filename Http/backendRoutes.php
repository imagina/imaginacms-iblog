<?php

use Illuminate\Routing\Router;
/** @var Router $router */

Route::post('/iblog/category/upload/image','PostController@uploadGalleryimage');
Route::post('/iblog/category/delete/img','PostController@deleteGalleryimage');

$router->group(['prefix' =>'/iblog'], function (Router $router) {

    \CRUD::resource('iblog','category', 'CategoryController');
    \CRUD::resource('iblog','post', 'PostController');
    \CRUD::resource('iblog','tag', 'TagController');
});

