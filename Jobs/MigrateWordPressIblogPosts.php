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
use Modules\Iblog\Entities\Status;
use Modules\Iblog\Services\WordPressMigrationService;

class MigrateWordPressIblogPosts implements ShouldQueue
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
    $migrationService = new WordPressMigrationService();
    $posts = $migrationService->getPosts($this->offset, $this->limit);
    $postsIds = $posts->pluck('ID');

    $categories = $migrationService->getPostsCat($postsIds);

    $categoriesByPost = $categories->groupBy('object_id');
    $externalCatIds = $categories->pluck('term_id')->unique()->toArray();

    $postRepository = app('Modules\Iblog\Repositories\PostRepository');
    $categoryRepository = app('Modules\Iblog\Repositories\CategoryRepository');

    $catParams = ['filter' => ['external_id' => ['where' => 'in', 'value' => $externalCatIds]]];
    $localCat = $categoryRepository->getItemsBy(json_decode(json_encode($catParams)));

    foreach ($posts as $post) {
      $params = ['filter' => ['field' => 'external_id']];
      $existingPost = $postRepository->getItem($post->ID, json_decode(json_encode($params)));
      if (is_null($existingPost) && (isset($existingPost->post_name) && !empty($existingPost->post_name))) {
        $statusMap = [
          'auto-draft' => Status::DRAFT,
          'draft'      => Status::DRAFT,
          'publish'    => Status::PUBLISHED,
          'trash'      => Status::UNPUBLISHED,
        ];
        $status = $statusMap[$post->post_status] ?? Status::PENDING;

        $postToCreate = [
          'title' => $post->post_title,
          'description' => $post->post_content,
          'summary' => $post->post_title,
          'slug' => $post->post_name,
          'user_id' => 1,
          'status' => $status,
          'created_at' => $post->post_date,
          'updated_at' => $post->post_modified,
          'external_id' => $post->ID,
        ];

        $wpCategories = $categoriesByPost->get($post->ID);
        $wpCategoriesByPost = $wpCategories ? $wpCategories->pluck('term_id')->toArray() : [];
        $categoriesOfPost = [];
        if(!empty($wpCategoriesByPost)) {
          $categoriesOfPost = $localCat->whereIn('external_id', $wpCategoriesByPost)->pluck('id')->toArray();
          if(!empty($categoriesOfPost)) {
            $postToCreate['category_id'] = $categoriesOfPost[0];
          }
        }

        $newPost = Post::create($postToCreate);
        if(!empty($categoriesOfPost)) $newPost->categories()->attach($categoriesOfPost);
      }
    }
  }
}
