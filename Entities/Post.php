<?php

namespace Modules\Iblog\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Bcrud\Support\Traits\CrudTrait;
use Laracasts\Presenter\PresentableTrait;
use Modules\Iblog\Presenters\PostPresenter;
//use Modules\Bcrud\Support\ModelTraits\SpatieTranslatable\Sluggable;
//use Modules\Bcrud\Support\ModelTraits\SpatieTranslatable\SluggableScopeHelpers;

class Post extends Model
{
    use CrudTrait,  PresentableTrait;
    //use Sluggable, SluggableScopeHelpers;

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
     * Return the sluggable configuration array for this model.
     *
     * @return array

    public function sluggable()
    {
        return [
            'slug' => [
                'source' => 'slug_or_title',
            ],
        ];
    }
*/
    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected function setSlugAttribute($value){

        if(!empty($value)){
            $this->attributes['slug'] = str_slug($value,'-');
        }else{
            $this->attributes['slug'] = str_slug($this->attributes['title'],'-');
        }


    }

    protected function setSummaryAttribute($value){

        if(!empty($value)){
            $this->attributes['summary'] = $value;
        } else {
            $this->attributes['summary'] = isubstr(strip_tags($this->attributes['description']),150);
        }

    }


    protected function getSummaryAttribute(){

           return $this->summary ?? isubstr(strip_tags($this->description),150);
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
     * get main image
     * @return string
     */
    public function getMainimageAttribute()
    {

        return url($this->options->mainimage  ?? 'modules/iblog/img/post/default.jpg' . '?v=' . $this->updated_at);
    }

    /**
     * get medium image
     * @return mixed|string
     */

    public function getMediumimageAttribute()
    {

        return url(str_replace('.jpg', '_mediumThumb.jpg', $this->options->mainimage  ?? 'modules/iblog/img/post/default.jpg') . '?v=' . $this->updated_at);
    }

    /**
     * get small image
     * @return mixed|string
     */
    public function getThumbailsAttribute()
    {

        return url(str_replace('.jpg', '_smallThumb.jpg', $this->options->mainimage  ?? 'modules/iblog/img/post/default.jpg') . '?v=' .$this->updated_at);
    }

    /**
     * @return mixed
     */
    public function getMetadescriptionAttribute()
    {

        return $this->options->metadescription ?? $this->summary;
    }

    /**
     * @return mixed
     */
    public function getMetatitleAttribute()
    {

        return $this->options->metatitle ?? $this->title;
    }

    /**
     * get Gallery
     * @return string
     */
    public function getGalleryAttribute()
    {

        $images = \Storage::disk('publicmedia')->files('assets/iblog/post/gallery/' . $this->id);
        if (count($images)) {
            return $images;
        }
        return null;
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
