<?php

use Modules\Iblog\Entities\Category;

if (! function_exists('posts')) {

    function posts($id) {

        $posts = Category::find($id);

        return $posts;

    }
}

if (! function_exists('postgallery')){

	function postgallery($id){
		$images = Storage::disk('publicmedia')->files('assets/iblog/post/gallery/' . $id);
		return $images;
	}
}


