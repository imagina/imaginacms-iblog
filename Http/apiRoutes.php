<?php

use Illuminate\Routing\Router;

$router->group(['prefix' => 'iblog/v1'], function (Router $router) {

  $router->apiCrud([
    'module' => 'iblog',
    'prefix' => 'posts',
    'controller' => 'PostApiController',
    //'middleware' => ['create' => [], 'index' => [], 'show' => [], 'update' => [], 'delete' => [], 'restore' => []]
  ]);

  $router->apiCrud([
    'module' => 'iblog',
    'prefix' => 'categories',
    'controller' => 'CategoryApiController',
    //'middleware' => ['create' => [], 'index' => [], 'show' => [], 'update' => [], 'delete' => [], 'restore' => []]
  ]);

  $router->apiCrud([
    'module' => 'iblog',
    'prefix' => 'statuses',
    'staticEntity' => 'Modules\Iblog\Entities\Status',
    //'middleware' => ['create' => [], 'index' => [], 'show' => [], 'update' => [], 'delete' => [], 'restore' => []]
  ]);
  //append
});

