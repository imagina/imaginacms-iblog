<?php

namespace Modules\Iblog\Http\Controllers;

use Mockery\CountValidator\Exception;
use Modules\Iblog\Repositories\CategoryRepository;
use Modules\Iblog\Repositories\PostRepository;
use Modules\Iblog\Repositories\TagRepository;
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
    private $tag;

    public function __construct(PostRepository $post,CategoryRepository $category, TagRepository $tag)
    {
        parent::__construct();
        $this->post = $post;
        $this->category = $category;
        $this->tag = $tag;
    }

    public function index()
    {
        //Search category.
        $uri = Route::current()->uri();

        //Default Template
        $tpl = 'iblog::frontend.index';
        $ttpl='iblog.index';

        if(view()->exists($ttpl)) $tpl = $ttpl;

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
        $tpl='iblog::frontend.show';
        $ttpl = 'iblog.show';

        if(view()->exists($ttpl)) $tpl = $ttpl;

        $post = $this->post->findBySlug($slug);
        $category = $post->categories()->first();
        //$tag = $post->tags()->get();

        //Get Custom Template.
        $ctpl = "iblog.category.{$category->id}.show";


        if(view()->exists($ctpl)) $tpl = $ctpl;


        return view($tpl, compact('post','category'));
    }
}
