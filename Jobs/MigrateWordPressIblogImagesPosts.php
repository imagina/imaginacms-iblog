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

class MigrateWordPressIblogImagesPosts implements ShouldQueue
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
    $data = $migrationService->getImagePost($this->offset, $this->limit);

    $imagesUrls = $data->pluck('main_image')->unique();
    $externalPostIds = $data->pluck('ID')->unique();

    $postRepository = app('Modules\Iblog\Repositories\PostRepository');
    $fileService = app("Modules\Media\Services\FileService"); //Instance file service

    $postParams = ['filter' => ['external_id' => ['where' => 'in', 'value' => $externalPostIds]]];
    $localPosts = $postRepository->getItemsBy(json_decode(json_encode($postParams)));

    foreach ($imagesUrls as $imageUrl) {
      $relatedPosts = $data->where('main_image', $imageUrl);
      $relatedPostsIds = $relatedPosts->pluck('ID')->unique();
      $locPost = $localPosts->whereIn('external_id', $relatedPostsIds);
      $fixedUrl = str_replace('…', '%E2%80%A6', $imageUrl);
      //Get base64 file
      $uploadedFile = getUploadedFileFromUrl($fixedUrl);
      //Create file
      $file = $fileService->store($uploadedFile, 0, 'publicmedia');
      //set file if
      $fileId = $file->id;

      foreach ($locPost as $post) {
        $post->files()->attach($fileId, ['zone' => 'mainimage']);
      }
    }
  }
}
