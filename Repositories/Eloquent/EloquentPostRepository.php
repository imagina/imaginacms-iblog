<?php

namespace Modules\Iblog\Repositories\Eloquent;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Laracasts\Presenter\PresentableTrait;
use Modules\Core\Repositories\Eloquent\EloquentBaseRepository;
use Modules\Iblog\Entities\Post;
use Modules\Iblog\Entities\Status;
use Modules\Iblog\Events\PostWasCreated;
use Modules\Iblog\Events\PostWasUpdated;
use Modules\Iblog\Events\PostWasDeleted;
use Modules\Iblog\Repositories\PostRepository;
use Modules\Ihelpers\Events\CreateMedia;
use Modules\Ihelpers\Events\DeleteMedia;
use Modules\Ihelpers\Events\UpdateMedia;

class EloquentPostRepository extends EloquentBaseRepository implements PostRepository
{

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
   * @inheritdoc
   */
  public function findBySlug($slug)
  {
    if (method_exists($this->model, 'translations')) {
      return $this->model->whereHas('translations', function (Builder $q) use ($slug) {
        $q->where('slug', $slug);
      })->with('translations', 'category', 'categories', 'tags', 'user')->whereStatus(Status::PUBLISHED)->firstOrFail();
    }
    
    return $this->model->where('slug', $slug)->with('category', 'categories', 'tags', 'user')->whereStatus(Status::PUBLISHED)->firstOrFail();;
  }
  
  public function whereCategory($id)
  {
    
    return $this->model->select('*', 'iblog__posts.id as id')
      ->leftJoin('iblog__post__category', 'iblog__post__category.post_id', '=', 'iblog__posts.id')
      ->where('iblog__post__category.category_id', $id)
      ->with('category', 'categories', 'tags', 'user', 'translations')
      ->whereStatus(Status::PUBLISHED)->where('created_at', '<', date('Y-m-d H:i:s'))->orderBy('created_at', 'DESC')->paginate(12);
    
  }

  public function whereTag($id)
  {

    return $this->model->leftJoin('iblog__post__tag', 'iblog__post__tag.post_id', '=', 'iblog__posts.id')
      ->whereIn('iblog__post__tag.tag_id', [$id])
      ->whereStatus(Status::PUBLISHED)->where('created_at', '<', date('Y-m-d H:i:s'))->orderBy('created_at', 'DESC')->paginate(12);

  }

/*  public function whereFilters($filters, $includes = array())
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
      $query->where(function ($query) use ($param, $criterion) {
        foreach ($param as $index => $word) {
          $query->where('title', 'like', "%" . $criterion . "%")->orWhere('id', 'like', "%" . $criterion . "%");
          if ($index == 0) {
            $query->where('title', 'like', "%" . $word . "%")->orWhere('id', 'like', "%" . $word . "%");
          } else {
            $query->orWhere('title', 'like', "%" . $word . "%")->orWhere('id', 'like', "%" . $word . "%");
          }
        }
        
      });
    }
    $query->whereStatus(get_status($filters->status ?? '1'))
      ->skip($filters->skip ?? 0);
    $query->orderBy($orderby, $ordertype);
    if (isset($filters->take)) {
      $query->take($filter->take ?? 5);
      return $query->get();
    } elseif (isset($filters->paginate) && is_integer($filters->paginate)) {
      return $query->paginate($filters->paginate);
    } else {
      return $query->paginate(12);
    }
  }*/
  
  public function category($id)
  {
    return $this->model->where('category_id', $id)->whereStatus(Status::PUBLISHED)->where('created_at', '<', date('Y-m-d H:i:s'))->orderBy('created_at', 'DESC')->get();
  }




    /**
     * @param  int $id
     * @return object
     */
    public function find($id)
    {
        return $this->model->with('translations','category', 'categories', 'tags', 'user')->find($id);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all()
    {
        return $this->model->with('tags','translations')->orderBy('created_at', 'DESC')->get();
    }


    /**
     * @param $param
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function search($keys)
    {
        $query = $this->model->query();
        $criterion = $keys;
        $param = explode(' ', $criterion);
        $query->whereHas('translations', function (Builder $q) use ($criterion) {
            $q->where('title', 'like', "%{$criterion}%");
        });
        //$query->orWhere('title', 'like', "%{$criterion}%");
        $query->orWhere('id', $criterion);

        return $query->orderBy('created_at', 'desc')->paginate(20);
    }

    /**
     * Standard Api Method
     * Create a iblog post
     * @param  array $data
     * @return Post
     */
    public function create($data)
    {
        $post = $this->model->create($data);
        $post->categories()->sync(array_get($data, 'categories', []));
        event(new PostWasCreated($post, $data));
        $post->setTags(array_get($data, 'tags', []));
        return $post;
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

        $post->categories()->sync(array_get($data, 'categories', []));

        event(new PostWasUpdated($post, $data));
        $post->setTags(array_get($data, 'tags', []));

        return $post;
    }



    public function destroy($model)
    {
        $model->untag();
        event(new PostWasDeleted($model->id, get_class($model)));

        return $model->delete();
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
      $query->with(['translations']);
    } else {//Especific relationships
      $includeDefault = ['translations'];//Default relationships
      if (isset($params->include))//merge relations with default relationships
        $includeDefault = array_merge($includeDefault, $params->include);
      $query->with($includeDefault);//Add Relationships to query
    }
    
    /*== FILTERS ==*/
    if (isset($params->filter)) {
      $filter = $params->filter;//Short filter

      if(isset($filter->categories)){

          $categories=is_array($filter->categories)?$filter->categories:[$filter->categories];
          $query->whereHas('categories',function ($q) use($categories){
              $q->whereIn('category_id',$categories);
          });
      }
        if(isset($filter->user)){
            $query->where('user_id',$filter->user);
        }
      if (isset($filter->search)) { //si hay que filtrar por rango de precio
        $criterion = $filter->search;

          $query->whereHas('translations', function (Builder $q) use ($criterion) {
              $q->where('title', 'like', "%{$criterion}%");
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
      $query->with(['translations']);
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

        // find translatable attributes
        $translatedAttributes = $this->model->translatedAttributes;

        // filter by translatable attributes
        if (isset($field) && in_array($field, $translatedAttributes))//Filter by slug
            $query->whereHas('translations', function ($query) use ($criteria, $filter, $field) {
                $query->where('locale', $filter->locale)
                    ->where($field, $criteria);
            });
        else
            // find by specific attribute or by id
            $query->where($field ?? 'id', $criteria);
    }
    
    /*== FIELDS ==*/
    if (isset($params->fields) && count($params->fields))
      $query->select($params->fields);
    
    /*== REQUEST ==*/
    return $query->first();

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

     if($model) {
       $model->update((array)$data);
       $model->categories()->sync(array_get($data, 'categories', []));
       $model->setTags(array_get($data, 'tags', []));
       event(new UpdateMedia($model,$data));
      }

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
    if($model) {
      $model->untag();
      $model->delete();
      event(new DeleteMedia($model->id, get_class($model)));
    }

  }
  
}
