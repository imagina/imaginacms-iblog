<?php

namespace Modules\Iblog\Repositories\Eloquent;

use Modules\Core\Repositories\Eloquent\EloquentBaseRepository;
use Modules\Iblog\Events\CategoryWasCreated;
use Modules\Iblog\Repositories\CategoryRepository;

class EloquentCategoryRepository extends EloquentBaseRepository implements CategoryRepository
{

    /**
     * @inheritdoc
     */
    public function find($id)
    {
        if (method_exists($this->model, 'translations')) {
            return $this->model->with('translations')->find($id);
        }

        return $this->model->with('parent','children')->find($id);
    }
    /**
     * Find a resource by the given slug
     *
     * @param  string $slug
     * @return object
     */
    public function findBySlug($slug)
    {
        return $this->model->where('slug', "$slug")->firstOrFail();
    }

    public function create($data)
    {

       $category= $this->model->create($data);

       event(new CategoryWasCreated($category, $data));

       return $this->find($category->id);
    }

    public function whereFilters($filters,$includes=array())
    {
        $query=count($includes)!==0?$this->model->with($includes) : $this->model->with('parent') ;
        if (!empty($filters->parent)) {
            $query->where('id', $filters->parent);
        }

        if (!empty($filters->exclude)) {
            $query->whereNotIn('id', $filters->exclude);
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
                $query->whereIn('id', $filters->include);
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


        $query->skip($filters->skip ?? 0);
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
