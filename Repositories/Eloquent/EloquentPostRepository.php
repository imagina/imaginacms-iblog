<?php

namespace Modules\Iblog\Repositories\Eloquent;

use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Laracasts\Presenter\PresentableTrait;
use Modules\Core\Repositories\Eloquent\EloquentBaseRepository;
use Modules\Iblog\Entities\Post;
use Modules\Iblog\Entities\Status;
use Modules\Iblog\Events\PostWasCreated;
use Modules\Iblog\Events\PostWasDeleted;
use Modules\Iblog\Events\PostWasUpdated;
use Modules\Iblog\Repositories\PostRepository;
use Modules\Iblog\Entities\Category;
use Modules\Ihelpers\Events\CreateMedia;
use Modules\Ihelpers\Events\DeleteMedia;
use Modules\Ihelpers\Events\UpdateMedia;
use Illuminate\Support\Str;

class EloquentPostRepository extends EloquentBaseRepository implements PostRepository
{

  /**
   * @inheritdoc
   */
  public function findBySlug($slug)
  {
    if (method_exists($this->model, 'translations')) {
      return $this->model->whereHas('translations', function (Builder $q) use ($slug) {
        $q->where('slug', $slug);
      })->with('translations', 'category', 'categories', 'tags', 'user')
        ->whereRaw("id IN (SELECT post_id from iblog__post_translations where status = ".(Status::PUBLISHED)." and locale = '".($params->filter->locale ?? locale())."')")
        ->firstOrFail();
    }

    return $this->model->where('slug', $slug)->with('category', 'categories', 'tags', 'user')
  ->whereRaw("id IN (SELECT post_id from iblog__post_translations where status = ".(Status::PUBLISHED)." and locale = '".($params->filter->locale ?? locale())."')")

      ->firstOrFail();;
  }

  /**
   * @param object $id
   * @return object
   */
  public function whereCategory($id)
  {
    $query = $this->model->with('categories', 'category', 'tags', 'user', 'translations');
    $query->whereHas('categories', function ($q) use ($id) {
      $q->where('category_id', $id);
    })
      ->whereRaw("id IN (SELECT post_id from iblog__post_translations where status = ".(Status::PUBLISHED)." and locale = '".($params->filter->locale ?? locale())."')")
      ->where('created_at', '<=', date('Y-m-d H:i:s'))->orderBy('created_at', 'DESC');
    
    return $query->paginate(setting('iblog::posts-per-page'));
  }
  
  public function whereTag($slug)
  {
    /*== initialize query ==*/
    $query = $this->model->query();
    
    /*== RELATIONSHIPS ==*/
    if (in_array('*', $params->include ?? [])) {//If Request all relationships
      $query->with(['categories', 'category', 'tags', 'user', 'translations']);
    } else {//Especific relationships
      $includeDefault = ['categories', 'category', 'tags', 'user', 'translations'];//Default relationships
      if (isset($params->include))//merge relations with default relationships
        $includeDefault = array_merge($includeDefault, $params->include);
      $query->with($includeDefault);//Add Relationships to query
    }
    $query->whereTag($slug);
    return $query->paginate(setting('iblog::posts-per-page'));
  }
  
  
  /**
   * Find post by id
   * @param int $id
   * @return object
   */
  public function find($id)
  {
    return $this->model->with('translations', 'category', 'categories', 'tags', 'user')->find($id);
  }
  
  /**
   * @return \Illuminate\Database\Eloquent\Collection
   */
  public function all()
  {
    return $this->model->with('tags', 'translations')->orderBy('created_at', 'DESC')->get();
  }
  
  
  /**
   * create a resource
   * Create a iblog post
   * @param array $data
   * @return Post
   */
  public function create($data)
  {
    //Event creating model
    if (method_exists($this->model, 'creatingCrudModel'))
      $this->model->creatingCrudModel(['data' => $data]);
    
    $post = $this->model->create($data);
    
    //Event created model
    if (method_exists($post, 'createdCrudModel'))
      $post->createdCrudModel(['data' => $data]);
    
    $post->categories()->sync(array_merge(Arr::get($data, 'categories', []), [$post->category_id]));
    event(new PostWasCreated($post, $data));
    $post->setTags(Arr::get($data, 'tags', []));
    return $post;
  }
  
  /**
   * Update a resource
   * @param $post
   * @param array $data
   * @return mixed
   */
  public function update($post, $data)
  {
    
    
    $post->update($data);
    
    
    $post->categories()->sync(array_merge(Arr::get($data, 'categories', []), [$post->category_id]));
    
    event(new PostWasUpdated($post, $data));
    $post->setTags(Arr::get($data, 'tags', []));
    
    return $post;
  }
  
  /**
   * Delete a resource
   * @param $model
   * @return mixed
   */
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
    if (in_array('*', $params->include ?? [])) {//If Request all relationships
      $query->with(['translations', 'files', 'category']);
    } else {//Especific relationships
      $includeDefault = ['translations', 'files', 'category'];//Default relationships
      if (isset($params->include))//merge relations with default relationships
        $includeDefault = array_merge($includeDefault, $params->include);
      $query->with($includeDefault);//Add Relationships to query
    }
    
    /*== FILTERS ==*/
    if (isset($params->filter)) {
      $filter = $params->filter;//Short filter
      
      if (isset($filter->id)) {
        !is_array($filter->id) ? $filter->id = [$filter->id] : false;
        $query->whereIn('iblog__posts.id', $filter->id);
      }
      
      // add filter by Categories 1 or more than 1, in array
      if (isset($filter->categories) && !empty($filter->categories)) {
        is_array($filter->categories) ? true : $filter->categories = [$filter->categories];
        $query->where(function ($query) use ($filter){
          $query->whereRaw("iblog__posts.id IN (SELECT post_id from iblog__post__category where category_id IN (".(join(",",$filter->categories))."))")
            ->orWhereIn('iblog__posts.category_id', $filter->categories);
        });
        
      }


      //Filter by featured
      if (isset($filter->featured) && is_bool($filter->featured)) {
        $query->where("featured", $filter->featured);
      }
      
      //Filter by category
      if (isset($filter->category) && !empty($filter->category)) {
        
        $categories = Category::descendantsAndSelf($filter->category);
        
        if ($categories->isNotEmpty()) {
          $query->where(function ($query) use ($categories) {
            $query->where(function ($query) use ($categories) {
              $query->whereRaw("iblog__posts.id IN (SELECT post_id from iblog__post__category where category_id IN (".(join(",",$categories->pluck("id")->toArray()))."))")
                ->orWhereIn('iblog__posts.category_id', $categories->pluck("id"));
            });
          });
          
        }
      }

      //Filter by category ID
      if (isset($filter->categoryId) && !empty($filter->categoryId)) {
        !is_array($filter->categoryId) ? $filter->categoryId = [$filter->categoryId] : false;
        
        $query->whereIn("iblog__posts.category_id", $filter->categoryId);
        
      }

      if (isset($filter->tagId)) {
        
        $query->whereTag($filter->tagId, "id");
        
        
      }
      
      if (isset($filter->tagSlug)) {
        
        $query->whereTag($filter->tagSlug);
        
        
      }
      
      if (isset($filter->users) && !empty($filter->users)) {
        $users = is_array($filter->users) ? $filter->users : [$filter->users];
        $query->whereIn('user_id', $users);
      }
      
      if (isset($filter->include) && !empty($filter->include)) {
        $include = is_array($filter->include) ? $filter->include : [$filter->include];
        $query->whereIn('id', $include);
      }
      if (isset($filter->exclude) && !empty($filter->exclude)) {
        $exclude = is_array($filter->exclude) ? $filter->exclude : [$filter->exclude];
        $query->whereNotIn('id', $exclude);
      }
      
      if (isset($filter->exclude_categories) && !empty($filter->exclude_categories)) {
        
        $exclude_categories = is_array($filter->exclude_categories) ? $filter->exclude_categories : [$filter->exclude_categories];
        $query->whereRaw("iblog__posts.id NOT IN (SELECT post_id from iblog__post__category where category_id IN (".(join(",",$exclude_categories))."))");
        
      }
      
      if (isset($filter->exclude_users) && !empty($filter->exclude_users)) {
        $exclude_users = is_array($filter->exclude_users) ? $filter->exclude_users : [$filter->exclude_users];
        $query->whereNotIn('user_id', $exclude_users);
      }
      
      if (isset($filter->tag) && !empty($filter->tag)) {
        
        $query->whereTag($filter->tag);
      }
  
      
      // add filter by search
      if (isset($filter->search) && !empty($filter->search)) {

        $orderSearchResults = json_decode(setting("iblog::orderSearchResults"));

        // removing symbols used by MySQL
        $filter->search = preg_replace("/[^a-zA-Z0-9]+/", " ", $filter->search);
        $words = explode(" ", $filter->search);//Explode

          //Search query
          $query->leftJoin(\DB::raw(
            "(SELECT MATCH (" . implode(',',json_decode(setting('iblog::selectSearchFieldsPosts'))) . ") AGAINST ('(\"" . $filter->search . "\")' IN NATURAL LANGUAGE MODE) scoreSearch1, post_id, title, " .
            " MATCH (" . implode(',',json_decode(setting('iblog::selectSearchFieldsPosts'))) . ") AGAINST ('(" . $filter->search . ")' IN NATURAL LANGUAGE MODE) scoreSearch2 " .
            "from iblog__post_translations " .
            "where `locale` = '".($filter->locale ?? locale())."') as ptrans"
          ), 'ptrans.post_id', 'iblog__posts.id')
            ->where(function ($query){
              $query->where('scoreSearch1', '>', 0)
              ->orWhere('scoreSearch2', '>', 0);
            });
    
          foreach ($orderSearchResults ?? [] as $orderSearch){
            $query->orderBy($orderSearch, 'desc');
          }
          

        unset($filter->order);
      }
     
      //Filter by date
      if (isset($filter->date) && !empty($filter->date)) {
        $date = $filter->date;//Short filter date
        $date->field = $date->field ?? 'created_at';
        if (isset($date->from))//From a date
          $query->whereDate($date->field, '>=', $date->from);
        if (isset($date->to))//to a date
          $query->whereDate($date->field, '<=', $date->to);
      }
      if (is_module_enabled('Marketplace')) {
        if (isset($filter->store) && !empty($filter->store)) {
          $query->where('store_id', $filter->store);
        }
      }
      
      //Order by
      if (isset($filter->order) && !empty($filter->order)) {
        $order = is_array($filter->order) ? $filter->order : [$filter->order];
        
        foreach ($order as $orderObject) {
          if (isset($orderObject->field) && isset($orderObject->way)) {
            if (in_array($orderObject->field, $this->model->translatedAttributes)) {
              $query->join('iblog__post_translations as ftranslations', 'ftranslations.post_id', '=', 'iblog__posts.id');
              $query->orderBy("translations.$orderObject->field", $orderObject->way);
            } else
              $query->orderBy("iblog__posts.".$orderObject->field, $orderObject->way);
          }
          
        }
      }
      
      if (isset($filter->status) && !empty($filter->status)) {
        !is_array($filter->status) ? $filter->status = [$filter->status] : false;
        $query->whereRaw("id IN (SELECT post_id from iblog__post_translations where status = ".join($filter->status)." and locale = '".($filter->locale ?? locale())."')");
      }
  
      // add filter by Categories Intersected 1 or more than 1, in array
      if (isset($filter->categoriesIntersected) && !empty($filter->categoriesIntersected)) {
        is_array($filter->categoriesIntersected) ? true : $filter->categoriesIntersected = [$filter->categoriesIntersected];
        $query->where(function ($query) use ($filter) {
          foreach ($filter->categoriesIntersected as $categoryId)
            $query->whereRaw("iblog__posts.id IN (SELECT post_id from iblog__post__category where category_id = $categoryId)");
        });
      }

      if (isset($filter->withoutInternal)) {
        $query->whereRaw("iblog__posts.category_id IN (SELECT id from iblog__categories where internal = 0)");
      }

    }

    if (isset($params->setting) && isset($params->setting->fromAdmin) && $params->setting->fromAdmin) {

    } else {
      //Pre filters by default
    $this->defaultPreFilters($query,$params);
    }



    // ORDER
    if (isset($params->order) && $params->order) {
      
      $order = is_array($params->order) ? $params->order : [$params->order];
      
      foreach ($order as $orderObject) {
        if (isset($orderObject->field) && isset($orderObject->way)) {
          if (in_array($orderObject->field, $this->model->translatedAttributes)) {
            $query->orderByTranslation($orderObject->field, $orderObject->way);
          } else
            $query->orderBy($orderObject->field, $orderObject->way);
        }
        
      }
    } else {
      //Order by "Sort order"
      if (!isset($filter->search) && !isset($params->filter->order) && (!isset($params->filter->noSortOrder) || !$params->filter->noSortOrder)) {
        $query->orderBy('sort_order', 'asc');//Add order to query
      }
    }

    /*== FIELDS ==*/
    if (isset($params->fields) && count($params->fields))
      $query->select($params->fields);

   // if(isset($params->filter->categories))
   //dd($params,$query->toSql(),$query->getBindings(),$query->get());
    /*== REQUEST ==*/
    if (isset($params->onlyQuery) && $params->onlyQuery) {
      return $query;
    } else
      if (isset($params->page) && $params->page) {
        //return $query->paginate($params->take);
        return $query->paginate($params->take, ['*'], null, $params->page);
      } else {
        isset($params->take) && $params->take ? $query->take($params->take) : false;//Take
        
        return $query->get();
      }
  }
  
  
  public function defaultPreFilters($query, $params){
  
    //pre-filter date_available
    $query->where(function ($query) {
      $query->where("date_available", "<=", date("Y-m-d", strtotime(now())));
      $query->orWhereNull("date_available");
    });
  
    $query->whereRaw("iblog__posts.category_id IN (SELECT id from iblog__category_translations where status = 1)");
  
  
    //pre-filter status
      $query->whereRaw("id IN (SELECT post_id from iblog__post_translations where status = 2 and locale = '".($params->filter->locale ?? locale())."')");


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
    if (in_array('*', $params->include ?? [])) {//If Request all relationships
      $query->with(['translations', 'files', 'category']);
    } else {//Especific relationships
      $includeDefault = ['translations', 'files', 'category'];//Default relationships
      if (isset($params->include))//merge relations with default relationships
        $includeDefault = array_merge($includeDefault, $params->include);
      $query->with($includeDefault);//Add Relationships to query
    }
    
    /*== FILTER ==*/
    if (isset($params->filter)) {
      $filter = $params->filter;
      
      if (isset($filter->field))//Filter by specific field
        $field = $filter->field;
      
      // add filter by Categories 1 or more than 1, in array/*
      if (isset($filter->categories) && !empty($filter->categories)) {
        is_array($filter->categories) ? true : $filter->categories = [$filter->categories];
        $query->where(function ($query) use ($filter){
          $query->whereRaw("iblog__posts.id IN (SELECT post_id from iblog__post__category where category_id IN (".(join(",",$filter->categories))."))")
            ->orWhereIn('iblog__posts.category_id', $filter->categories);
        });
      }
      
      // find translatable attributes
      $translatedAttributes = $this->model->translatedAttributes;
      
      // filter by translatable attributes
      if (isset($field) && in_array($field, $translatedAttributes))//Filter by slug
        $query->whereHas('translations', function ($query) use ($criteria, $filter, $field) {
          $query->where('locale', $filter->locale ?? \App::getLocale())
            ->where($field, $criteria);
        });
      else
        // find by specific attribute or by id
        $query->where($field ?? 'id', $criteria);
    }
    
    if (isset($params->setting) && isset($params->setting->fromAdmin) && $params->setting->fromAdmin) {
    
    } else {
      //Pre filters by default
      $this->defaultPreFilters($query,$params);
      
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
    
    //Event updating model
    if (method_exists($this->model, 'updatingCrudModel'))
      $this->model->updatingCrudModel(['data' => $data, 'params' => $params, 'criteria' => $criteria]);
    
    /*== FILTER ==*/
    if (isset($params->filter)) {
      $filter = $params->filter;
      
      //Update by field
      if (isset($filter->field))
        $field = $filter->field;
    }
    
    /*== REQUEST ==*/
    $model = $query->where($field ?? 'id', $criteria)->first();
    
    if (isset($model->id)) {
      $model->update((array)$data);
      //Event updated model
      if (method_exists($model, 'updatedCrudModel'))
        $model->updatedCrudModel(['data' => $data, 'params' => $params, 'criteria' => $criteria]);
      
      if (isset($data["categories"]) && $model) {
        $model->categories()->sync(array_merge(Arr::get($data, 'categories', []), [$model->category_id]));
      }
      event(new PostWasUpdated($model, $data));
      $model->setTags(Arr::get($data, 'tags', []));
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
    $model ? $model->delete() : false;
    event(new DeleteMedia($model->id, get_class($model)));
    
  }
  
  
}
