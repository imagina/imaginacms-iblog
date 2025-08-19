<?php

namespace Modules\Iblog\Http\Controllers\Api;

use Imagina\Icore\Http\Controllers\CoreApiController;
//Model
use Modules\Iblog\Models\Post;
use Modules\Iblog\Repositories\PostRepository;

class PostApiController extends CoreApiController
{
  public function __construct(Post $model, PostRepository $modelRepository)
  {
    parent::__construct($model, $modelRepository);
  }
}
