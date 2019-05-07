<?php

namespace Modules\Iblog\Repositories\Eloquent;

use Modules\Core\Repositories\Eloquent\EloquentBaseRepository;
use Modules\Iblog\Events\CategoryWasCreated;
use Modules\Iblog\Repositories\CategoryRepository;

class EloquentCategoryRepository extends EloquentBaseRepository implements CategoryRepository
{

    public function getItemsBy($params = false)
    {
        /*== initialize query ==*/
        $query = $this->model->query();

        /*== RELATIONSHIPS ==*/
        if (in_array('*', $params->include)) {//If Request all relationships
            $query->with([]);
        } else {//Especific relationships
            $includeDefault = ['translations'];//Default relationships
            if (isset($params->include))//merge relations with default relationships
                $includeDefault = array_merge($includeDefault, $params->include);
            $query->with($includeDefault);//Add Relationships to query
        }

        /*== FILTERS ==*/
        if (isset($params->filter)) {
            $filter = $params->filter;//Short filter
            if (isset($filter->parent)) {
                $query->where('parent_id',$filter->parent);
            }

            if (isset($filter->search)) { //si hay que filtrar por rango de precio
                $criterion = $filter->search;
                $param = explode(' ', $criterion);
                $query->where(function ($query) use ($param) {
                    foreach ($param as $index => $word) {
                        if ($index == 0) {
                            $query->where('title', 'like', "%" . $word . "%");
                            $query->orWhere('sku', 'like', "%" . $word . "%");
                        } else {
                            $query->orWhere('title', 'like', "%" . $word . "%");
                            $query->orWhere('sku', 'like', "%" . $word . "%");
                        }
                    }

                });
            }

            //Filter by date
            if (isset($filter->date)) {
                $date = $filter->date;//Short filter date
                $date->field = $date->field ?? 'created_at';
                if (isset($date->from))//From a date
                    $query->whereDate($date->field, '>=', $date->from);
                if (isset($date->to))//to a date
                    $query->whereDate($date->field, '<=', $date->to);
            }

            //Order by
            if (isset($filter->order)) {
                $orderByField = $filter->order->field ?? 'created_at';//Default field
                $orderWay = $filter->order->way ?? 'desc';//Default way
                $query->orderBy($orderByField, $orderWay);//Add order to query
            }
        }

        /*== FIELDS ==*/
        if (isset($params->fields) && count($params->fields))
            $query->select($params->fields);

        /*== REQUEST ==*/
        if (isset($params->page) && $params->page) {
            return $query->paginate($params->take);
        } else {
            $params->take ? $query->take($params->take) : false;//Take
            return $query->get();
        }
    }

    public function getItem($criteria, $params = false)
    {
        //Initialize query
        $query = $this->model->query();

        /*== RELATIONSHIPS ==*/
        if (in_array('*', $params->include)) {//If Request all relationships
            $query->with([]);
        } else {//Especific relationships
            $includeDefault = [];//Default relationships
            if (isset($params->include))//merge relations with default relationships
                $includeDefault = array_merge($includeDefault, $params->include);
            $query->with($includeDefault);//Add Relationships to query
        }

        /*== FILTER ==*/
        if (isset($params->filter)) {
            $filter = $params->filter;

            if (isset($filter->field))//Filter by specific field
                $field = $filter->field;
        }

        /*== FIELDS ==*/
        if (isset($params->fields) && count($params->fields))
            $query->select($params->fields);

        /*== REQUEST ==*/
        return $query->where($field ?? 'id', $criteria)->first();
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
        return $this->model->where('slug', $slug)->firstOrFail();
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
