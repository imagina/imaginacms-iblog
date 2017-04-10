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

    protected $fillable = ['title','description','slug','user_id','status', 'summary','category_id','options'];
    protected $presenter = PostPresenter::class;
    protected $fakeColumns = ['options'];


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



    public function setGalleryAttribute($value){

        $this->saveImageGallery($value,"assets/iblog/post/gallery/". $this->data['id'] ."/". rand() .".jpg");

    }

    public function getOptionsAttribute($value) {

        return json_decode(json_decode($value));

    }

    /*
     * Function to get the URL for the Post.
     */
    public function getUrlAttribute() {


        if(!isset($this->category->slug)) {
            $this->category = Category::take(1)->get()->first();
        }


        return \URL::route(\LaravelLocalization::getCurrentLocale() . '.iblog.'.$this->category->slug.'.slug', [$this->slug]);

    }



    public function saveImageGallery($value,$destination_path){

        $disk = "publicmedia";

        // 0. Make the image
        $image = \Image::make($value);

        $image->resize(config('asgard.iblog.config.imagesize.width'), config('asgard.iblog.config.imagesize.height'), function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });

        // 2. Store the image on disk.
        \Storage::disk($disk)->put($destination_path, $image->stream('jpg','80'));


        //Small Thumb
        \Storage::disk($disk)->put(
            str_replace('.jpg','_smallThumb.jpg',$destination_path),
            $image->fit(config('asgard.iblog.config.smallthumbsize.width'),config('asgard.iblog.config.smallthumbsize.height'))->stream('jpg','80')
        );



        return $destination_path;

    }


}
