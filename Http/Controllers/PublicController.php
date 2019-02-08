<?php

namespace Modules\Iblog\Http\Controllers;

use Log;
use Mockery\CountValidator\Exception;
use Modules\Core\Http\Controllers\BasePublicController;
use Modules\Iblog\Repositories\CategoryRepository;
use Modules\Iblog\Repositories\PostRepository;
use Modules\Iblog\Repositories\TagRepository;
use Modules\Ifeeds\Support\SupportFeed;
use Request;
use Route;

class PublicController extends BasePublicController
{
    /**
     * @var PostRepository
     */
    private $post;
    private $category;
    private $tag;

    public function __construct(PostRepository $post, CategoryRepository $category, TagRepository $tag)
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
        $ttpl = 'iblog.index';

        if (view()->exists($ttpl)) $tpl = $ttpl;

        if (empty($uri)) {
            //Root
        } else {
            $category = $this->category->findBySlug($uri);
            $posts = $this->post->whereCategory($category->id);
            //Get Custom Template.

            $ptpl = "iblog.category.{$category->parent_id}.index";
            if ($category->parent_id != 0 && view()->exists($ptpl)) {
                $tpl = $ptpl;
            }
            $ctpl = "iblog.category.{$category->id}.index";
            if (view()->exists($ctpl)) $tpl = $ctpl;

        }

        return view($tpl, compact('posts', 'category'));

    }

    public function show($slug)
    {
        $tpl = 'iblog::frontend.show';
        $ttpl = 'iblog.show';

        if (view()->exists($ttpl)) $tpl = $ttpl;

        $post = $this->post->findBySlug($slug);
        $category = $post->category;
        $tags = $post->tags()->get();

        $ptpl = "iblog.category.{$category->parent_id}.show";
        if ($category->parent_id != 0 && view()->exists($ptpl)) {
            $tpl = $ptpl;
        }
        //Get Custom Template.
        $ctpl = "iblog.category.{$category->id}.show";


        if (view()->exists($ctpl)) $tpl = $ctpl;


        return view($tpl, compact('post', 'category', 'tags'));
    }

    public function tag($slug)
    {

        //Default Template
        $tpl = 'iblog::frontend.tag';
        $ttpl = 'iblog.tag';

        if (view()->exists($ttpl)) $tpl = $ttpl;

        $tag = $this->tag->findBySlug($slug);
        $posts = $this->post->whereTag($tag->id);

        //Get Custom Template.
        $ctpl = "iblog.tag.{$tag->id}";
        if (view()->exists($ctpl)) $tpl = $ctpl;


        return view($tpl, compact('posts', 'tag'));

    }

    public function feed($format)
    {
        $posts_per_feed = config('asgard.iblog.config.posts_per_feed');
        $posts = $this->post->whereFilters((object)['status' => 'publicado', 'take' => $posts_per_feed]);
        $feed = new SupportFeed($format, $posts);
        $feed_logo = config('asgard.iblog.config.logo');
        return $feed->feed($feed_logo);

    }

}