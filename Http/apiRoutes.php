<?php

use Illuminate\Routing\Router;

Route::prefix('iblog/v1')->group(function (Router $router) {
    //======  CATEGORIES
    require 'ApiRoutes/categoryRoutes.php';

    //======  POSTS
    require 'ApiRoutes/postRoutes.php';

  $router->apiCrud([
    'module' => 'iblog',
    'prefix' => 'statuses',
    'staticEntity' => 'Modules\Iblog\Entities\Status',
    //'middleware' => ['create' => [], 'index' => [], 'show' => [], 'update' => [], 'delete' => [], 'restore' => []]
  ]);
  //append
});
