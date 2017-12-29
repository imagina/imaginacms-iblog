<?php

use Illuminate\Routing\Router;
/** @var Router $router */

$router->group(['prefix' =>'/iblog'], function (Router $router) {

    $router->post('post/upload/image', [
        'as' => 'iblog.gallery.upload',
        'uses' => 'PostController@uploadGalleryimage',
    ]);
    $router->post('post/delete/img', [
        'as' => 'iblog.gallery.delete',
        'uses' => 'PostController@deleteGalleryimage',
    ]);
    \CRUD::resource('iblog','category', 'CategoryController');
    \CRUD::resource('iblog','post', 'PostController');
    \CRUD::resource('iblog','tag', 'TagController');
});

