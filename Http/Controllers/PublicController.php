<?php

namespace Modules\Iblog\Http\Controllers;

use Mockery\CountValidator\Exception;
use Modules\Iblog\Repositories\CategoryRepository;
use Modules\Iblog\Repositories\PostRepository;
use Modules\Core\Http\Controllers\BasePublicController;
use Route;
use Request;
use Log;

class PublicController extends BasePublicController
{
    /**
     * @var PostRepository
     */
    private $post;
    private $category;

    public function __construct(PostRepository $post,CategoryRepository $category)
    {
        parent::__construct();
        $this->post = $post;
        $this->category = $category;
    }

    public function index()
    {
        //Search category.
        $uri = Route::current()->uri();

        //Default Template
        $tpl = 'iblog.index';
        if(empty($uri)) {
            //Root
        } else {
            $category = $this->category->findBySlug($uri);
            $posts = $this->post->whereCategory($category->id);

            //Get Custom Template.
            $ctpl = "iblog.category.{$category->id}.index";
            if(view()->exists($ctpl)) $tpl = $ctpl;
        }


        return view($tpl, compact('posts','category'));

    }

    public function show($slug)
    {

        $tpl = 'iblog.show';
        $post = $this->post->findBySlug($slug);
        $category = $post->categories()->get()->first();
        //Get Custom Template.
        $ctpl = "iblog.category.{$post->category_id}.show";
        if(view()->exists($ctpl)) $tpl = $ctpl;

        return view($tpl, compact('post','category'));
    }
}
