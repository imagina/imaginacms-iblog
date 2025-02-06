<?php

namespace Modules\Iblog\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Iblog\Jobs\MigrateWordPressIblog;

class IblogWordpressContentMigrationTableSeeder extends Seeder
{
  public function run()
  {
    $totalPosts = \DB::connection('wordpress')->table('wp_posts')
      ->where('post_type', 'post')
      ->where('post_status', 'publish')
      ->count();

    $batchSize = 100;

    for ($offset = 0; $offset < $totalPosts; $offset += $batchSize) {
      MigrateWordPressIblog::dispatch($offset, $batchSize)->onQueue('wordpress_migration');
    }

    \Log::info('WordPress posts migration jobs dispatched successfully.');
  }
}
