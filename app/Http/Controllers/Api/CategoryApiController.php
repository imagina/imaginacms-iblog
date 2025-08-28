<?php

namespace Modules\Iblog\Http\Controllers\Api;

use Imagina\Icore\Http\Controllers\CoreApiController;
//Model
use Modules\Iblog\Models\Category;
use Modules\Iblog\Repositories\CategoryRepository;

class CategoryApiController extends CoreApiController
{
  public function __construct(Category $model, CategoryRepository $modelRepository)
  {
    parent::__construct($model, $modelRepository);
  }
}
