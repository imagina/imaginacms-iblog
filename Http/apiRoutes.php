<?php

use Illuminate\Routing\Router;

$router->group(['prefix' => 'iblog'], function (Router $router) {

    $router->group(['prefix' => 'posts'], function (Router $router) {

        /*Update 2018-10-16. Routes Index and Show for posts*/
        $router->get('/', [
            'as' => 'iblog.api.posts.index',
            'uses' => 'PostController@index',
        ]);
        $router->get('/{param}', [
            'as' => 'iblog.api.posts.show',
            'uses' => 'PostController@show',
        ]);

        $router->bind('apipost', function ($id) {
            return app(\Modules\Iblog\Repositories\PostRepository::class)->find($id);
        });
        $router->get('/', [
            'as' => 'iblog.api.posts',
            'uses' => 'PostController@posts',
        ]);
        $router->get('{apipost}', [
            'as' => 'iblog.api.post',
            'uses' => 'PostController@post',
        ]);
        $router->post('/', [
            'as' => 'iblog.api.posts.store',
            'uses' => 'PostController@store',
            'middleware' => ['api.token', 'token-can:iblog.posts.create']
        ]);
        $router->post('gallery', [
            'as' => 'iblog.api.posts.gallery.store',
            'uses' => 'PostController@galleryStore',
            'middleware' => ['api.token', 'token-can:iblog.posts.create']
        ]);
        $router->post('gallery/delete', [
            'as' => 'iblog.api.posts.gallery.delete',
            'uses' => 'PostController@galleryDelete',
            'middleware' => ['api.token', 'token-can:iblog.posts.create']
        ]);
        $router->put('{apipost}', [
            'as' => 'iblog.api.posts.update',
            'uses' => 'PostController@update',
            'middleware' => ['api.token', 'token-can:iblog.posts.edit']
        ]);
        $router->delete('{apipost}', [
            'as' => 'iblog.api.posts.delete',
            'uses' => 'PostController@delete',
            'middleware' => ['api.token', 'token-can:iblog.posts.destroy']
        ]);
    });
    $router->group(['prefix' => 'categories'], function (Router $router) {

        $router->bind('apiblogcat', function ($id) {
            return app(\Modules\Iblog\Repositories\CategoryRepository::class)->find($id);
        });
        $router->get('/', [
            'as' => 'iblog.api.categories.index',
            'uses' => 'CategoryController@index',
        ]);
        $router->get('/{slug}', [
            'as' => 'iblog.api.categories.show',
            'uses' => 'CategoryController@show',
        ]);

        $router->get('{apiblogcat}/posts', [
            'as' => 'iblog.api.categories.posts',
            'uses' => 'CategoryController@posts',
        ]);
        $router->post('/', [
            'as' => 'iblog.api.categories.store',
            'uses' => 'CategoryController@store',
            'middleware' => ['api.token', 'token-can:iblog.categories.create']
        ]);
        $router->put('{apiblogcat}', [
            'as' => 'iblog.api.categories.update',
            'uses' => 'CategoryController@update',
            'middleware' => ['api.token', 'token-can:iblog.categories.edit']
        ]);
        $router->delete('{apiblogcat}', [
            'as' => 'iblog.api.categories.delete',
            'uses' => 'CategoryController@delete',
            'middleware' => ['api.token', 'token-can:iblog.categories.destroy']
        ]);
    });


    $router->group(['prefix' => 'tags'], function (Router $router) {

        $router->bind('apiblogtag', function ($id) {
            return app(\Modules\Iblog\Repositories\TagRepository::class)->find($id);
        });
        $router->get('/', [
            'as' => 'iblog.api.tags',
            'uses' => 'TagController@tags',
        ]);
        $router->get('{apiblogtag}', [
            'as' => 'iblog.api.tag',
            'uses' => 'TagController@tag',
        ]);
        $router->post('/', [
            'as' => 'iblog.api.tags.store',
            'uses' => 'TagController@store',
            'middleware' => ['api.token', 'token-can:iblog.tags.create']
        ]);
        $router->put('{apiblogtag}', [
            'as' => 'iblog.api.tags.update',
            'uses' => 'TagController@update',
            'middleware' => ['api.token', 'token-can:iblog.tags.edit']
        ]);
        $router->delete('{apiblogtag}', [
            'as' => 'iblog.api.tags.delete',
            'uses' => 'TagController@delete',
            'middleware' => ['api.token', 'token-can:iblog.tags.destroy']
        ]);
    });

});

