<?php

namespace Modules\Iblog\Repositories\Eloquent;

use Illuminate\Database\Eloquent\Builder;
use Laracasts\Presenter\PresentableTrait;
use Modules\Core\Repositories\Eloquent\EloquentBaseRepository;
use Modules\Iblog\Entities\Post;
use Modules\Iblog\Entities\Status;
use Modules\Iblog\Events\PostWasCreated;
use Modules\Iblog\Repositories\Collection;
use Modules\Iblog\Repositories\PostRepository;

class EloquentPostRepository extends EloquentBaseRepository implements PostRepository
{
    /**
     * @param  int $id
     * @return object
     */
    public function find($id)
    {
        return $this->model->with('category', 'categories', 'tags', 'user')->find($id);
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

        return $this->model->with('category', 'categories', 'tags', 'user')->where('slug', $slug)->whereStatus(Status::PUBLISHED)->firstOrFail();
    }

    public function whereCategory($id)
    {

        return $this->model->leftJoin('iblog__post__category', 'iblog__post__category.post_id', '=', 'iblog__posts.id')
            ->whereIn('iblog__post__category.category_id', [$id])
            ->with('category', 'categories', 'tags', 'user')
            ->whereStatus(Status::PUBLISHED)->where('created_at', '<', date('Y-m-d H:i:s'))->orderBy('created_at', 'DESC')->paginate(12);

    }

    public function whereTag($id)
    {

        return $this->model->leftJoin('iblog__post__tag', 'iblog__post__tag.post_id', '=', 'iblog__posts.id')
            ->whereIn('iblog__post__tag.tag_id', [$id])
            ->whereStatus(Status::PUBLISHED)->where('created_at', '<', date('Y-m-d H:i:s'))->orderBy('created_at', 'DESC')->paginate(12);

    }

    public function whereFilters($filters, $includes = array())
    {

        $query = count($includes) !== 0 ? $this->model->with($includes) : $this->model->with('user');

        if (!empty($filters->categories) || isset($filters->exclude_categories)) {

            $query->leftJoin('iblog__post__category', 'iblog__post__category.post_id', '=', 'iblog__posts.id');
        }

        if (!empty($filters->categories)) {
            is_array($filters->categories) ? true : $filters->categories = [$filters->categories];

            $query->whereIn('iblog__post__category.category_id', $filters->categories);

        }
        if (isset($filters->exclude_categories)) {

            $query->whereNotIn('iblog__post__category.category_id', $filters->exclude_categories);
        }

        if (!empty($filters->users)) {
            $query->whereHas('user', function ($query) use ($filters) {
                $query->whereIn('user_id', $filters->users);
            });
        }
        if (!empty($filters->exclude)) {
            $query->whereNotIn('iblog__posts.id', $filters->exclude);
        }

        if (isset($filters->exclude_users)) {
            $query->whereHas('user', function ($query) use ($filters) {
                $query->whereNotIn('user_id', $filters->exclude_users);
            });
        }
        if (isset($filters->created_at)) {
            $query->where('created_at', $filters->created_at['operator'], $filters->created_at['date']);
        }

        if (isset($filters->order)) { //si hay que filtrar por rango de precio

            $orderby = $filters->order->by ?? 'created_at';
            $ordertype = $filters->order->type ?? 'desc';
        } else {
            $orderby = 'created_at';
            $ordertype = 'desc';
        }
        if (!empty($filters->include)) {
            $query->orWhere(function ($query) use ($filters) {
                $query->whereIn('iblog__posts.id', $filters->include);
            });

        }

        if (isset($filters->search)) { //si hay que filtrar por rango de precio
            $criterion = $filters->search;
            $param = explode(' ', $criterion);
            $query->where(function ($query) use ($param) {
                foreach ($param as $index => $word) {
                    if ($index == 0) {
                        $query->where('title', 'like', "%" . $word . "%")->orWhere('description', 'like', "%" . $word . "%");
                    } else {
                        $query->orWhere('title', 'like', "%" . $word . "%")->orWhere('description', 'like', "%" . $word . "%");
                    }
                }

            });
        }
        $query->whereStatus(get_status($filters->status ?? '1'))
            ->skip($filters->skip ?? 0);
        $query->orderBy($orderby, $ordertype);
        if (isset($filter->take)) {
            $query->take($filter->take ?? 5);
            return $query->get();
        } elseif (isset($filter->paginate) && is_integer($filters->paginate)) {
            return $query->paginate($filters->paginate);
        } else {
            return $query->paginate(12);
        }
    }

}
