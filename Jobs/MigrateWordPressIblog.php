<?php

namespace Modules\Iblog\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Modules\Iblog\Entities\Post;
use Modules\Iblog\Entities\Category;

class MigrateWordPressIblog implements ShouldQueue
{
  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

  protected $offset;
  protected $limit;

  /**
   * Create a new job instance.
   */
  public function __construct($offset, $limit)
  {
    $this->offset = $offset;
    $this->limit = $limit;
  }

  /**
   * Execute the job.
   */
  public function handle(): void
  {
    $posts = \DB::connection('wordpress')->table('wp_posts')
      ->where('post_type', 'post')
      ->where('post_status', 'publish')
      ->select('ID', 'post_title', 'post_content', 'post_date', 'post_name')
      ->offset($this->offset)
      ->limit($this->limit)
      ->get();

    foreach ($posts as $post) {
      $params = ['filter' => ['field' => 'slug']];
      $existingPost = app('Modules\Iblog\Repositories\PostRepository')->getItem($post->post_name, json_decode(json_encode($params)));
      if (is_null($existingPost)) {
        $newPost = Post::create([
          'title' => $post->post_title,
          'description' => $post->post_content,
          'summary' => $post->post_title,
          'slug' => $post->post_name,
          'user_id' => 1,
          'created_at' => $post->post_date,
        ]);
      } else {
        $newPost = $existingPost;
      }

      $categories = \DB::connection('wordpress')->table('wp_terms')
        ->join('wp_term_taxonomy', 'wp_terms.term_id', '=', 'wp_term_taxonomy.term_id')
        ->join('wp_term_relationships', 'wp_term_taxonomy.term_taxonomy_id', '=', 'wp_term_relationships.term_taxonomy_id')
        ->where('wp_term_relationships.object_id', $post->ID)
        ->where('wp_term_taxonomy.taxonomy', 'category')
        ->pluck('wp_terms.name');

      foreach ($categories as $category) {
        $params = ['filter' => ['field' => 'title']];
        $cat = app('Modules\Iblog\Repositories\CategoryRepository')->getItem($category, $params);
        if (is_null($cat)) {
          $newCategory = Category::create([
            'title' => $category,
            'description' => $category
          ]);
          $categoryIds[] = $newCategory->id;
        } else {
          $categoryIds[] = $cat->id;
        }
      }
      $newPost->categories()->attach($categoryIds);
      if (!empty($categoryIds)) {
        $newPost->update(['category_id' => $categoryIds[0]]);
      }
    }
  }
}
