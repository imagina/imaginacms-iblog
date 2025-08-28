<?php

namespace Modules\Iblog\Repositories\Cache;

use Modules\Iblog\Repositories\CategoryRepository;
use Imagina\Icore\Repositories\Cache\CoreCacheDecorator;

class CacheCategoryDecorator extends CoreCacheDecorator implements CategoryRepository
{
    public function __construct(CategoryRepository $category)
    {
        parent::__construct();
        $this->entityName = 'iblog.categories';
        $this->repository = $category;
    }
}
