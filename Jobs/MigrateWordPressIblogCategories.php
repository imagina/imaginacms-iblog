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
    $categories = \DB::connection('wordpress')
      ->table('wp_terms')
      ->join('wp_term_taxonomy', 'wp_terms.term_id', '=', 'wp_term_taxonomy.term_id')
      ->where('wp_term_taxonomy.taxonomy', 'category')
      ->select('wp_terms.term_id', 'wp_terms.name', 'wp_terms.slug', 'wp_term_taxonomy.parent')
      ->offset($this->offset)
      ->limit($this->limit)
      ->orderBy('wp_term_taxonomy.parent', 'ASC')
      ->get();

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
