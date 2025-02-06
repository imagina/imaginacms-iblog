<?php

namespace Modules\Iblog\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Modules\Iblog\Entities\CategoryTranslation;
use Modules\Iblog\Entities\Post;
use Modules\Iblog\Entities\Category;
use Modules\Iblog\Entities\PostTranslation;

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
      $existingPost = PostTranslation::where('slug', $post->post_name)->first();
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
        $newPost = Post::where('id', $existingPost->post_id)->first();
      }

      $categories = \DB::connection('wordpress')->table('wp_terms')
        ->join('wp_term_taxonomy', 'wp_terms.term_id', '=', 'wp_term_taxonomy.term_id')
        ->join('wp_term_relationships', 'wp_term_taxonomy.term_taxonomy_id', '=', 'wp_term_relationships.term_taxonomy_id')
        ->where('wp_term_relationships.object_id', $post->ID)
        ->where('wp_term_taxonomy.taxonomy', 'category')
        ->pluck('wp_terms.name');

      foreach ($categories as $category) {
//        $cat = CategoryTranslation::firstOrCreate(['title' => $category, 'description' => $category]);
        $cat = CategoryTranslation::where('title', $category)->first();
        if (is_null($cat)){
          $newCategory = Category::create([
            'title' => $category,
            'description' => $category
          ]);
          $newPost->categories()->attach($newCategory->id);
        } else {
          $newPost->categories()->attach($cat->category_id);
        }
      }
    }
  }
}
