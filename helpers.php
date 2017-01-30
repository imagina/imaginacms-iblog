<?php

use Modules\Iblog\Entities\Category;
use Modules\Iblog\Entities\Post;
use Modules\User\Entities\Sentinel\User;
use Modules\Iblog\Entities\Status;

if (! function_exists('posts')) {

    function posts() {

        $posts = Category::find(1);

        return $posts;

    }
}

if(! function_exists('getPostsList')){

    /*
     * Function to get an array of Posts.
     */

    function getPostsList($skip,$take,$categoryID=-1){

        if($categoryID==-1){
            $posts = Post::with(['user','categories'])
                ->whereStatus(Status::PUBLISHED)
                ->skip($skip)
                ->take($take)
                ->latest()
                ->get();

            return $posts;
        } else {
            $posts = Post::with(['categories'])
                ->whereHas('categories', function ($query) use ($categoryID) {
                    $query->where('category_id', $categoryID);
                })
                ->whereStatus(Status::PUBLISHED)
                ->skip($skip)
                ->take($take)
                ->latest()
                ->get();
            return $posts;
        }

    }

}

if (! function_exists('all_users_by_rol')) {

    function all_users_by_rol($roleName) {

        $users = User::with(['roles'])
            ->whereHas('roles', function ($query) use ($roleName){
                $query->where('name', '=', $roleName);
            })->latest()->get();

        return $users;

    }
}

if(! function_exists('getPostsListUser')) {
    function getPostsListUser($ini,$limit,$usID){
        $posts = Post::with(['user','categories'])
            ->whereHas('user', function ($query) use ($usID){
                $query->where('user_id', "=" , $usID);})
            ->whereStatus(Status::PUBLISHED)
            ->skip($ini)
            ->take($limit)
            ->latest()
            ->get();

        return $posts;
    }
}


if (! function_exists('postgallery')){

	function postgallery($id){
		$images = Storage::disk('publicmedia')->files('assets/iblog/post/gallery/' . $id);
		return $images;
	}
}


