<?php

namespace Modules\Iblog\Repositories;

use Modules\Core\Repositories\BaseRepository;

/**
 * Interface CategoryRepository
 * @package Modules\Iblog\Repositories
 */
interface CategoryRepository extends BaseRepository
{
    public function getItemsBy($params);
    
      public function getItem($criteria, $params);
      
      public function create($data);
    
      public function updateBy($criteria, $data, $params);
    
      public function deleteBy($criteria, $params);
}
