<?php

use Modules\Iblog\Entities\Category;

if (! function_exists('posts')) {

    function posts() {

        $posts = Category::find(1);

        return $posts;

    }
}


