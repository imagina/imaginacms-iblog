<?php

namespace Modules\Iblog\Events;

use Modules\Iblog\Entities\Category;
use Modules\Media\Contracts\StoringMedia;

class CategoryWasDeleted implements StoringMedia
{

    /**
     * @var Category
     */
    public $entity;

    /**
     * @var disk
     */
    public $disk;

    public function __construct($category)
    {

        $this->entity = $category;
        $this->disk='publicmedia';
    }

    /**
     * Return the entity
     * @return Category
     */
    public function getEntity()
    {
        return $this->entity;
    }
}
