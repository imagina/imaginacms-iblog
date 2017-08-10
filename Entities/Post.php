<?php

namespace Modules\Iblog\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Bcrud\Support\Traits\CrudTrait;
use Modules\Iblog\Entities\Feature;
use Laracasts\Presenter\PresentableTrait;
use Modules\Iblog\Presenters\PostPresenter;

class Post extends Model
{
    use CrudTrait,  PresentableTrait;

    protected $table = 'iblog__posts';

    protected $fillable = ['title','description','slug','user_id','status', 'summary','category_id','options','created_at'];
    protected $presenter = PostPresenter::class;
    protected $fakeColumns = ['options'];

    protected $casts = [
        'options' => 'array'
    ];

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'iblog__post__category');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'iblog__post__tag');
    }

    public function user()
    {
        $driver = config('asgard.user.config.driver');

        return $this->belongsTo("Modules\\User\\Entities\\{$driver}\\User");
    }


    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected function setSlugAttribute($value){

        if(!empty($value)){
            $this->attributes['slug'] = str_slug($value,'-');
        }else{
            $this->attributes['slug'] = str_slug($this->title,'-');
        }


    }

    protected function setSummaryAttribute($value){

        if(!empty($value)){
            $this->attributes['summary'] = $value;
        } else {
            $this->attributes['summary'] = substr(strip_tags($this->description),0,150);
        }

    }

    public function getOptionsAttribute($value) {
        return json_decode(json_decode($value));

    }

    /**
     * URL post
     * @return string
     */
    public function getUrlAttribute() {

        if(!isset($this->category->slug)) {
            $this->category = Category::take(1)->get()->first();
        }


        return \URL::route(\LaravelLocalization::getCurrentLocale() . '.iblog.'.$this->category->slug.'.slug', [$this->slug]);

    }

    /**
     * Magic Method modification to allow dynamic relations to other entities.
     * @var $value
     * @var $destination_path
     * @return string
     */


    public function __call($method, $parameters)
    {
        #i: Convert array to dot notation
        $config = implode('.', ['asgard.iblog.config.relations', $method]);

        #i: Relation method resolver
        if (config()->has($config)) {
            $function = config()->get($config);

            return $function($this);
        }

        #i: No relation found, return the call to parent (Eloquent) to handle it.
        return parent::__call($method, $parameters);
    }

}
