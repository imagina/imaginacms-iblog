<?php

use Illuminate\Routing\Router;

$router->group(['prefix' => 'iblog'], function (Router $router) {
  
  //======  CATEGORIES
  require('ApiRoutes/categoryRoutes.php');
  
  //======  POSTS
  require('ApiRoutes/postRoutes.php');
    
});

