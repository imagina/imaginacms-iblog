<?php

namespace Modules\Iblog\Repositories\Cache;

use Modules\Iblog\Repositories\PostRepository;
use Imagina\Icore\Repositories\Cache\CoreCacheDecorator;

class CachePostDecorator extends CoreCacheDecorator implements PostRepository
{
    public function __construct(PostRepository $post)
    {
        parent::__construct();
        $this->entityName = 'iblog.posts';
        $this->repository = $post;
    }
}
