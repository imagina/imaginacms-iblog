<?php

namespace Modules\Iblog\Repositories;

use Modules\Core\Repositories\BaseRepository;

/**
 * Interface PostRepository
 * @package Modules\Iblog\Repositories
 */
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

    /**
     * @param $param
     * @return mixed
     */
    public function search($param);

    /**
     * @param $params
     * @return mixed
     */
    public function getItemsBy($params);

    /**
     * @param $criteria
     * @param $params
     * @return mixed
     */
    public function getItem($criteria, $params);

    /**
     * @param $criteria
     * @param $data
     * @param $params
     * @return mixed
     */
    public function updateBy($criteria, $data, $params);

    /**
     * @param $criteria
     * @param $params
     * @return mixed
     */
    public function deleteBy($criteria, $params);

}
