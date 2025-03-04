<?php

namespace Modules\Iblog\Services;

use Illuminate\Support\Facades\DB;

class WordPressMigrationService
{
  protected $connection;

  public function __construct()
  {
    $this->connection = DB::connection('wordpress');
  }

  /**
   * Retrieve the status array from configuration.
   */
  protected function getStatusArray()
  {
    $wpStatusWhere = config('asgard.iblog.config.wordpressMigration.post.status');
    return is_string($wpStatusWhere) ? [$wpStatusWhere] : (array)$wpStatusWhere;
  }

  /**
   * Get the base query for posts filtered by status.
   */
  public function getPostQuery()
  {
    $wpStatusArray = $this->getStatusArray();

    $query = $this->connection->table('wp_posts')
      ->where('post_type', 'post');

    if (!in_array('all', $wpStatusArray, true)) {
      $query->whereIn('post_status', $wpStatusArray);
    }

    return $query;
  }

  /**
   * Get the total number of filtered posts.
   */
  public function getTotalPosts()
  {
    return $this->getPostQuery()->count();
  }

  /**
   * Get paginated posts with the required fields.
   */
  public function getPosts($offset, $limit)
  {
    return $this->getPostQuery()
      ->select('ID', 'post_title', 'post_content', 'post_date', 'post_name', 'post_modified', 'post_status')
      ->offset($offset)
      ->limit($limit)
      ->get();
  }

  /**
   * Get paginated posts with the required fields.
   */
  public function getPostsCat($postsIds)
  {
    return $this->getCategoryQuery()
      ->join('wp_term_relationships', 'wp_term_taxonomy.term_taxonomy_id', '=', 'wp_term_relationships.term_taxonomy_id')
      ->whereIn('wp_term_relationships.object_id', $postsIds)
      ->select('wp_terms.term_id', 'wp_term_relationships.object_id')
      ->get();
  }
  /**
   * Get the base query for categories.
   */
  public function getCategoryQuery()
  {
    return $this->connection->table('wp_terms')
      ->join('wp_term_taxonomy', 'wp_terms.term_id', '=', 'wp_term_taxonomy.term_id')
      ->where('wp_term_taxonomy.taxonomy', 'category');
  }

  /**
   * Get the total number of categories.
   */
  public function getTotalCategories()
  {
    return $this->getCategoryQuery()->count();
  }

  /**
   * Get paginated categories with the required fields.
   */
  public function getCategories($offset, $limit)
  {
    return $this->getCategoryQuery()
      ->select('wp_terms.term_id', 'wp_terms.name', 'wp_terms.slug', 'wp_term_taxonomy.parent')
      ->offset($offset)
      ->limit($limit)
      ->orderBy('wp_term_taxonomy.parent', 'ASC')
      ->get();
  }

  /**
   * Get the base query for image posts filtered by status.
   */
  public function getImagePostQuery()
  {
    $wpStatusArray = $this->getStatusArray();

    $query = $this->connection->table('wp_posts as p')
      ->leftJoin('wp_postmeta as pm', function ($join) {
        $join->on('p.ID', '=', 'pm.post_id')
          ->where('pm.meta_key', '=', '_thumbnail_id');
      })
      ->leftJoin('wp_posts as img', 'pm.meta_value', '=', 'img.ID')
      ->whereNotNull('img.guid')
      ->where('p.post_type', 'post')
      ->orderBy('p.post_date', 'desc');

    if (!in_array('all', $wpStatusArray, true)) {
      $query->whereIn('p.post_status', $wpStatusArray);
    }

    return $query;
  }

  /**
   * Get the total number of image posts filtered.
   */
  public function getTotalImagePosts()
  {
    $limit = config('asgard.iblog.config.wordpressMigration.post.images.limit') ?? 0;
    return $this->getImagePostQuery()->limit($limit)->count();
  }

  /**
   * Get paginated image posts with the required fields.
   */
  public function getImagePost($offset, $limit)
  {
    return $this->getImagePostQuery()
      ->select('p.ID', 'p.guid', 'p.post_date', 'img.guid as main_image')
      ->offset($offset)
      ->limit($limit)
      ->get();
  }

  public function replaceDescription($text) {
    $text = str_replace(["\r\n", "\n", "\r"], "<br>", $text);
    $text = preg_replace(
      '/\[embed\](https?:\/\/[^\s]+)\[\/embed\]/i',
      '<iframe src="$1" width="100%" height="150" frameborder="0" allow="autoplay"></iframe>',
      $text
    );
    return $text;
  }
}
