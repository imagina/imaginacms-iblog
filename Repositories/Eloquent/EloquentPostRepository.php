<?php

namespace Modules\Iblog\Repositories\Eloquent;

use Illuminate\Database\Eloquent\Builder;
use Modules\Iblog\Entities\Post;
use Modules\Iblog\Entities\Status;
use Modules\Iblog\Repositories\Collection;
use Modules\Iblog\Repositories\PostRepository;
use Modules\Core\Repositories\Eloquent\EloquentBaseRepository;
use Laracasts\Presenter\PresentableTrait;

class EloquentPostRepository extends EloquentBaseRepository implements PostRepository
{
    /**
     * @param  int $id
     * @return object
     */
    public function find($id)
    {
        return $this->model->with('tags')->find($id);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all()
    {
        return $this->model->with('tags')->orderBy('created_at', 'DESC')->get();
    }

    /**
     * Update a resource
     * @param $post
     * @param  array $data
     * @return mixed
     */
    public function update($post, $data)
    {
        $post->update($data);

        $post->tags()->sync(array_get($data, 'tags', []));

        //event(new PostWasUpdated($post, $data));

        return $post;
    }

    /**
     * Create a iblog post
     * @param  array $data
     * @return Post
     */
    public function create($data)
    {
        $post = $this->model->create($data);

        $post->tags()->sync(array_get($data, 'tags', []));

        event(new PostWasCreated($post, $data));

        return $post;
    }

    public function destroy($model)
    {
        //event(new PostWasDeleted($model->id, get_class($model)));

        return $model->delete();
    }


    /**
     * Return the latest x iblog posts
     * @param int $amount
     * @return Collection
     */
    public function latest($amount = 5)
    {
        return $this->model->whereStatus(Status::PUBLISHED)->orderBy('created_at', 'desc')->take($amount)->get();
    }

    /**
     * Get the previous post of the given post
     * @param object $post
     * @return object
     */
    public function getPreviousOf($post)
    {
        return $this->model->where('created_at', '<', $post->created_at)
            ->whereStatus(Status::PUBLISHED)->orderBy('created_at', 'desc')->first();
    }

    /**
     * Get the next post of the given post
     * @param object $post
     * @return object
     */
    public function getNextOf($post)
    {
        return $this->model->where('created_at', '>', $post->created_at)
            ->whereStatus(Status::PUBLISHED)->first();
    }

    /**
     * Find a resource by the given slug
     *
     * @param  string $slug
     * @return object
     */
    public function findBySlug($slug)
    {

        return $this->model->where('slug', $slug)->whereStatus(Status::PUBLISHED)->firstOrFail();
    }

    public function whereCategory($id)
    {

        return $this->model->leftJoin('iblog__post__category', 'iblog__post__category.post_id', '=', 'iblog__posts.id')
            ->whereIn('iblog__post__category.category_id', $id)
            ->whereStatus(Status::PUBLISHED)->where('created_at', '<', date('Y-m-d H:i:s'))->orderBy('created_at', 'DESC')->paginate(12);

    }

    public function whereTag($id)
    {

        return $this->model->leftJoin('iblog__post__tag', 'iblog__post__tag.post_id', '=', 'iblog__posts.id')
            ->whereIn('iblog__post__tag.tag_id', $id)
                ->whereStatus(Status::PUBLISHED)->where('created_at', '<', date('Y-m-d H:i:s'))->orderBy('created_at', 'DESC')->paginate(12);

    }
}
