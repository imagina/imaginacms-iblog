<?php

use Illuminate\Routing\Router;

$router->group(['prefix' => 'categories'], function (Router $router) {
  
  //Route create
  $router->post('/', [
    'as' => 'api.iblog.category.create',
    'uses' => 'CategoryController@create',
    'middleware' => ['auth:api']
  ]);
  
  //Route index
  $router->get('/', [
    'as' => 'api.iblog.category.get.items.by',
    'uses' => 'CategoryController@index',
    //'middleware' => ['auth:api']
  ]);
  
  //Route show
  $router->get('/{criteria}', [
    'as' => 'api.iblog.category.get.item',
    'uses' => 'CategoryController@show',
    //'middleware' => ['auth:api']
  ]);
  
  //Route update
  $router->put('/{criteria}', [
    'as' => 'api.iblog.category.update',
    'uses' => 'CategoryController@update',
    'middleware' => ['auth:api']
  ]);
  
  //Route delete
  $router->delete('/{criteria}', [
    'as' => 'api.iblog.category.delete',
    'uses' => 'CategoryController@delete',
    'middleware' => ['auth:api']
  ]);
});