<?php

namespace Modules\Iblog\Repositories\Eloquent;

use Modules\Core\Repositories\Eloquent\EloquentBaseRepository;
use Modules\Iblog\Events\CategoryWasCreated;
use Modules\Iblog\Repositories\CategoryRepository;

class EloquentCategoryRepository extends EloquentBaseRepository implements CategoryRepository
{

    /**
     * Return categories by parameters
     *
     * @param $page
     * @param $take
     * @param $filter
     * @param $include
     */
    public function index($page, $take, $filter, $include)
    {
        //Initialize Query
        $query = $this->model->query();

        /*== RELATIONSHIPS ==*/
        if (count($include)) {
            //Include relationships for default
            $includeDefault = [];
            $query->with(array_merge($includeDefault, $include));
        }

        /*== FILTER ==*/
        if ($filter) {
            //Filter by slug
            if (isset($filter->slug)) {
                $query->where('slug', $filter->slug);
            }

            //Filter by parent_id
            if (isset($filter->parentId) && is_array($filter->parentId)) {
                $query->whereIn('parent_id', $filter->parentId);
            }

            //Filter by parent_slug
            if (isset($filter->parentSlug) && is_array($filter->parentSlug)) {
                $query->whereIn('parent_id', function ($query) use ($filter) {
                    $query->select('iblog__categories.id')
                        ->from('iblog__categories')
                        ->whereIn('iblog__categories.slug', $filter->parentSlug);
                });
            }

            //Filter excluding categories by ID
            if (isset($filter->excludeById) && is_array($filter->excludeById)) {
                $query->whereNotIn('id', $filter->excludeById);
            }

            //Get specific categories by ID
            if (isset($filter->includeById) && is_array($filter->includeById)) {
                $query->whereIn('id', $filter->includeById);
            }

            //Search filter
            if (isset($filter->search) && !empty($filter->search)) {
                //Get the words separately from the criterion
                $words = explode(' ', trim($filter->search));

                //Add condition of search to query
                $query->where(function ($query) use ($words) {
                    foreach ($words as $index => $word) {
                        $query->where('title', 'like', "%" . $word . "%")
                            ->orWhere('description', 'like', "%" . $word . "%");
                    }
                });
            }

            //Add order By
            $orderBy = isset($filter->orderBy) ? $filter->orderBy : 'created_at';
            $orderType = isset($filter->orderType) ? $filter->orderType : 'desc';
            $query->orderBy($orderBy, $orderType);
        }

        /*=== REQUEST ===*/
        if ($page) {//Return request with pagination
            $take ? true : $take = 12; //If no specific take, query default take is 12
            return $query->paginate($take);
        } else {//Return request without pagination
            $take ? $query->take($take) : false; //Set parameter take(limit) if is requesting
            return $query->get();
        }
    }

    /**
     * Return category data
     *
     * @param $slug
     * @param $include
     * @return mixed
     */
    public function show($param, $include)
    {
        $isID = (int)$param >= 1 ? true : false;

        //Initialize Query
        $query = $this->model->query();

        if ($isID) {//if is by ID
            $query = $this->model->where('id', $param);
        } else {//if is by Slug
            $query = $this->model->where('slug', $param);
        }

        /*== RELATIONSHIPS ==*/
        if (count($include)) {
            //Include relationships for default
            $includeDefault = [];
            $query->with(array_merge($includeDefault, $include));
        }

        /*=== REQUEST ===*/
        return $query->first();
    }


    /**
     * @inheritdoc
     */
    public function find($id)
    {
        if (method_exists($this->model, 'translations')) {
            return $this->model->with('translations')->find($id);
        }

        return $this->model->with('parent', 'children')->find($id);
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

        $category = $this->model->create($data);

        event(new CategoryWasCreated($category, $data));

        return $this->find($category->id);
    }

    public function whereFilters($filters, $includes = array())
    {
        $query = count($includes) !== 0 ?
            $this->model->with($includes) : $this->model->with('parent');

        if (!empty($filters->parent) || $filters->parent === 0) {
            $query->where('parent_id', $filters->parent);
        }

        if (!empty($filters->exclude)) {
            $query->whereNotIn('id', $filters->exclude);
        }


        if (isset($filters->order)) {
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
        if (isset($filters->search)) {
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
