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
use Modules\Iblog\Services\WordPressMigrationService;

class MigrateWordPressIblogCategories implements ShouldQueue
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
    $categories = $migrationService->getCategories($this->offset, $this->limit);

    $categoryRepository = app('Modules\Iblog\Repositories\CategoryRepository');

    foreach ($categories as $category) {
      $params = ['filter' => ['field' => 'external_id']];
      $existingCategory = $categoryRepository->getItem($category->term_id, json_decode(json_encode($params)));
      if (!isset($existingCategory)) {
        $dataToCreate = [
          'title' => $category->name,
          'description' => $category->name,
          'slug' => $category->slug,
          'status' => 1,
          'external_id' => $category->term_id
        ];
        if(isset($category->parent) && $category->parent){
          $parentCat = $categoryRepository->getItem($category->parent, json_decode(json_encode($params)));
          if(isset($parentCat)) $dataToCreate['parent_id'] = $parentCat->id;
        }
        Category::create($dataToCreate);
      }
    }
  }
}
