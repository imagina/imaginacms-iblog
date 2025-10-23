<?php

namespace Modules\Iblog\Repositories\Eloquent;

use Modules\Iblog\Repositories\PostRepository;
use Imagina\Icore\Repositories\Eloquent\EloquentCoreRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class EloquentPostRepository extends EloquentCoreRepository implements PostRepository
{
  /**
   * Filter names to replace
   * @var array
   */
  protected array $replaceFilters = ['categoryId'];

  /**
   * Relation names to replace
   * @var array
   */
  protected array $replaceSyncModelRelations = [];

  /**
   * Attribute to define default relations
   * all apply to index and show
   * index apply in the getItemsBy
   * show apply in the getItem
   * @var array
   */
  protected array $with = [/*all => [] ,index => [],show => []*/];

  /**
   * @param Builder $query
   * @param object $filter
   * @param object $params
   * @return Builder
   */
  public function filterQuery(Builder $query, object $filter, object $params): Builder
  {

    /**
     * Note: Add filter name to replaceFilters attribute before replace it
     *
     * Example filter Query
     * if (isset($filter->status)) $query->where('status', $filter->status);
     *
     */

    if (!empty($filter->categorySlug)) {
      $slug = $filter->categorySlug;

      $query->where(function ($q) use ($slug) {
        // 🔹 1. belongsTo -> category
        $q->whereHas('category', function ($q2) use ($slug) {
          $q2->whereHas('translations', function ($qt) use ($slug) {
            $qt->where('slug', $slug);
          });
        });

        // 🔹 2. belongsToMany -> categories
        $q->orWhereHas('categories', function ($q2) use ($slug) {
          $q2->whereHas('translations', function ($qt) use ($slug) {
            $qt->where('slug', $slug);
          });
        });
      });
    }

    if (!empty($filter->categoryId)) {
      $catId = $filter->categoryId;

      $query->where(function ($q) use ($catId) {
        // 🔹 1. belongsTo -> category
        $q->where('category_id', $catId);

        // 🔹 2. belongsToMany -> categories
        $q->orWhereHas('categories', function ($q2) use ($catId) {
          $q2->where('iblog__categories.id', $catId);
        });
      });
    }


    //Response
    return $query;
  }

  /**
   * @param Model $model
   * @param array $data
   * @return Model
   */
  public function syncModelRelations(Model $model, array $data): Model
  {
    //Get model relations data from model attributes
    //$modelRelationsData = ($model->modelRelations ?? []);

    /**
     * Note: Add relation name to replaceSyncModelRelations attribute before replace it
     *
     * Example to sync relations
     * if (array_key_exists(<relationName>, $data)){
     *    $model->setRelation(<relationName>, $model-><relationName>()->sync($data[<relationName>]));
     * }
     *
     */

    //Response
    return $model;
  }
}
