<?php

namespace Modules\Iblog\Repositories\Eloquent;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\App;
use Modules\Iblog\Repositories\TagRepository;
use Modules\Core\Repositories\Eloquent\EloquentBaseRepository;

class EloquentTagRepository extends EloquentBaseRepository implements TagRepository
{
  /**
   * Find a tag by its name
   * @param $name
   * @return mixed
   */
  public function findByName($name)
  {
    $tags = $this->model->where('name', 'like', "%$name%")->get();
    
    return $this->setLocaleAsKey($tags);
  }
  
  private function setLocaleAsKey($tags)
  {
    $cleanedTags = [];
    foreach ($tags as $tag) {
      foreach ($tag->translations as $tagTranslation) {
        if (App::getLocale() == $tagTranslation->locale) {
          $cleanedTags[] = [
            'id' => $tag->id,
            'name' => $tagTranslation->name,
          ];
        }
      }
    }
    
    return $cleanedTags;
  }
  
  /**
   * Create the tag for the specified language
   * @param  string $lang
   * @param  array $name
   * @return mixed
   */
  public function createForLanguage($lang = 'en', $name)
  {
    $data = [
      $lang => [
        'name' => $name,
        'slug' => str_slug($name),
      ],
    ];
    
    return $this->create($data);
  }
  
  public function findBySlug($slug)
  {
    return $this->model->where('slug', "$slug")->firstOrFail();
  }
  
  public function whereFilters($filters, $includes = array())
  {
    $query = count($includes) !== 0 ? $this->model->with($includes) : $this->model;
    if (!empty($filters->exclude)) {
      $query->whereNotIn('id', $filters->exclude);
    }
    
    if (!empty($filters->include)) {
      $query->orWhere(function ($query) use ($filters) {
        $query->whereIn('id', $filters->include);
      });
      
    }
    if (isset($filters->search)) { //si hay que filtrar por rango de precio
      $criterion = $filters->search;
      
      $param = explode(' ', $criterion);
      
      $query->whereHas(function ($query) use ($param) {
        foreach ($param as $index => $word) {
          if ($index == 0) {
            $query->where('title', 'like', "%" . $word . "%");
          } else {
            $query->orWhere('title', 'like', "%" . $word . "%");
          }
        }
        
      });
    }
    
    if (isset($filters->order)) { //si hay que filtrar por rango de precio
      $orderby = $filters->order->by ?? 'created_at';
      $ordertype = $filters->order->type ?? 'desc';
    } else {
      $orderby = 'created_at';
      $ordertype = 'desc';
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
  
  
  /**
   * Standard Api Method
   * @param bool $params
   * @return mixed
   */
  public function getItemsBy($params = false)
  {
    /*== initialize query ==*/
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
    
    /*== FILTERS ==*/
    if (isset($params->filter)) {
      $filter = $params->filter;//Short filter
      
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
      
      //add filter by search
      if (isset($filter->search)) {
        //find search in columns
        $query->where('id', 'like', '%' . $filter->search . '%')
          ->orWhere('updated_at', 'like', '%' . $filter->search . '%')
          ->orWhere('created_at', 'like', '%' . $filter->search . '%');
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
  
  /**
   * Standard Api Method
   * @param $criteria
   * @param bool $params
   * @return mixed
   */
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
   * Standard Api Method
   * @param $criteria
   * @param $data
   * @param bool $params
   * @return bool
   */
  public function updateBy($criteria, $data, $params = false)
  {
    /*== initialize query ==*/
    $query = $this->model->query();
    
    /*== FILTER ==*/
    if (isset($params->filter)) {
      $filter = $params->filter;
      
      //Update by field
      if (isset($filter->field))
        $field = $filter->field;
    }
    
    /*== REQUEST ==*/
    $model = $query->where($field ?? 'id', $criteria)->first();
    return $model ? $model->update((array)$data) : false;
  }
  
  /**
   * Standard Api Method
   * @param $criteria
   * @param bool $params
   */
  public function deleteBy($criteria, $params = false)
  {
    /*== initialize query ==*/
    $query = $this->model->query();
    
    /*== FILTER ==*/
    if (isset($params->filter)) {
      $filter = $params->filter;
      
      if (isset($filter->field))//Where field
        $field = $filter->field;
    }
    
    /*== REQUEST ==*/
    $model = $query->where($field ?? 'id', $criteria)->first();
    $model ? $model->delete() : false;
  }
  
  /**
   * Standard Api Method
   * @param $data
   * @return mixed
   */
  public function create($data)
  {
    return $this->model->create($data);
  }
}
