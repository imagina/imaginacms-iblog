<?php

namespace Modules\Iblog\Repositories;

use Modules\Core\Repositories\BaseRepository;

interface PostRepository extends BaseRepository
{
    /**
     * Return the latest x iblog posts
     * @param int $amount
     * @return Collection
     */
    public function latest($amount = 5);

    /**
     * Get the previous post of the given post
     * @param object $post
     * @return object
     */
    public function getPreviousOf($post);

    /**
     * Get the next post of the given post
     * @param object $post
     * @return object
     */
    public function getNextOf($post);

    /**
     * Get the next post of the given post
     * @param object $id
     * @return object
     */
    public function find($id);

    /**
     * Get the next post of the given post
     * @param object $id
     * @return object
     */
    public function category($id);

    public function search($param);
  
    public function getItemsBy($params);
    
    public function getItem($criteria, $params);
    
    public function create($data);
    
    public function updateBy($criteria, $data, $params);
    
    public function deleteBy($criteria, $params);

}
