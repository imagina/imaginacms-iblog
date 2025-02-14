<?php

namespace Modules\Iblog\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Iblog\Jobs\MigrateWordPressIblogCategories;
use Modules\Iblog\Jobs\MigrateWordPressIblogPosts;

class IblogWordpressContentMigrationTableSeeder extends Seeder
{
  public function run()
  {
    if (config('asgard.iblog.config.wordpressMigration.enableMig')) {
      \Log::info('WordPress start migration');
      $totalCategories = \DB::connection('wordpress')
        ->table('wp_terms')
        ->join('wp_term_taxonomy', 'wp_terms.term_id', '=', 'wp_term_taxonomy.term_id')
        ->where('wp_term_taxonomy.taxonomy', 'category')
        ->count();

      $batchCatSize = 100;

      \Log::info('WordPress start Categories Migration');
      for ($offset = 0; $offset < $totalCategories; $offset += $batchCatSize) {
        MigrateWordPressIblogCategories::dispatch($offset, $batchCatSize)->onQueue('wordpress_migration');
      }

      \Log::info('WordPress categories migration jobs dispatched successfully.');

      $wpStatusWhere = config('asgard.iblog.config.wordpressMigration.post.status');
      // Convert an array if is a string
      $wpStatusArray = is_string($wpStatusWhere) ? [$wpStatusWhere] : (array) $wpStatusWhere;
      $queryPost = \DB::connection('wordpress')->table('wp_posts')
        ->where('post_type', 'post');
      // If the array contain 'all',don't apply whereIn()
      if (!in_array('all', $wpStatusArray, true)) {
        $queryPost->whereIn('post_status', $wpStatusArray);
      }
      $totalPosts = $queryPost->count();

      $batchPostSize = 100;

      \Log::info('WordPress start Posts Migration');
      for ($offset = 0; $offset < $totalPosts; $offset += $batchPostSize) {
        MigrateWordPressIblogPosts::dispatch($offset, $batchPostSize)->onQueue('wordpress_migration');
      }

      \Log::info('WordPress posts migration jobs dispatched successfully.');
    }
  }
}
