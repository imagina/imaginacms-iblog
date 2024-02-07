<?php

namespace Modules\Iblog\Repositories\Cache;

use Modules\Core\Icrud\Repositories\Cache\BaseCacheCrudDecorator;
use Modules\Iblog\Repositories\Collection;
use Modules\Iblog\Repositories\PostRepository;

class CachePostDecorator extends BaseCacheCrudDecorator implements PostRepository
{
  public function __construct(PostRepository $post)
  {
    parent::__construct();
    $this->entityName = 'iblog.posts';
    $this->repository = $post;
  }
  
}
