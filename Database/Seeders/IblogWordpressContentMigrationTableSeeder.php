<?php

namespace Modules\Iblog\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Iblog\Jobs\MigrateWordPressIblogCategories;
use Modules\Iblog\Jobs\MigrateWordPressIblogImagesPosts;
use Modules\Iblog\Jobs\MigrateWordPressIblogPosts;
use Modules\Iblog\Services\WordPressMigrationService;

class IblogWordpressContentMigrationTableSeeder extends Seeder
{
  public function run()
  {
    if (config('asgard.iblog.config.wordpressMigration.enableMig')) {
      $migrationService = new WordPressMigrationService();

      // Categories Migration
      \Log::info('WordPress start migration');
      $totalCategories = $migrationService->getTotalCategories();
      $batchCatSize = 100;
      \Log::info('WordPress start Categories Migration');
      for ($offset = 0; $offset < $totalCategories; $offset += $batchCatSize) {
        MigrateWordPressIblogCategories::dispatch($offset, $batchCatSize)->onQueue('wordpress_migration');
      }
      \Log::info('WordPress categories migration jobs dispatched successfully.');

      // Posts Migration
      $totalPosts = $migrationService->getTotalPosts();
      $batchPostSize = 100;
      \Log::info('WordPress start Posts Migration');
      for ($offset = 0; $offset < $totalPosts; $offset += $batchPostSize) {
        MigrateWordPressIblogPosts::dispatch($offset, $batchPostSize)->onQueue('wordpress_migration');
      }
      \Log::info('WordPress posts migration jobs dispatched successfully.');

      // Images Main Post Migration
      $totalImages = $migrationService->getTotalImagePosts();
      $batchImgPostSize = config('asgard.iblog.config.wordpressMigration.post.images.batch') ?? 50;
      \Log::info('WordPress start Main Img Posts Migration');
      for ($offset = 0; $offset < $totalImages; $offset += $batchImgPostSize) {
        MigrateWordPressIblogImagesPosts::dispatch($offset, $batchImgPostSize)->onQueue('wordpress_migration');
      }

      \Log::info('WordPress Main Img posts migration jobs dispatched successfully.');
    }
  }
}
