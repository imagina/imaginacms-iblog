<?php

namespace Modules\Iblog\Repositories\Cache;

use Modules\Iblog\Repositories\CategoryRepository;
use Modules\Core\Repositories\Cache\BaseCacheDecorator;

class CacheCategoryDecorator extends BaseCacheDecorator implements CategoryRepository
{
    public function __construct(CategoryRepository $category)
    {
        parent::__construct();
        $this->entityName = 'categories';
        $this->repository = $category;
    }
}
