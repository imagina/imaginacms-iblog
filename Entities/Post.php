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

    protected $fillable = ['title','description','slug','user_id','status', 'summary','options'];
    protected $presenter = PostPresenter::class;
    protected $fakeColumns = ['options'];


    /**
     * The attributes that should be casted to native types.
     *
     * @var array
    */
    protected function setSlugAttribute($value){

        if($value != ''){
            $this->attributes['slug'] = str_slug($value,'-');
          }

        $this->attributes['slug'] = str_slug($this->title,'-');

    }

    protected function setSummaryAttribute($value){

        if($value != ''){
            $this->attributes['summary'] = $value;
        }

        $this->attributes['summary'] = substr($this->description,0,150);

    }



    protected $casts = [
        'options' => 'array'
    ];

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'iblog__post__category');
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


    public function setOptionsAttribute($value) {

        $options = json_decode($value);
        //var_dump($value); die();
        if(!empty($options->mainimage)) $options->mainimage = $this->saveImage($options->mainimage,"assets/iblog/post/".$this->id.".jpg");

        $this->attributes['options'] = json_encode(json_encode($options));


    }

    public function getOptionsAttribute($value) {

        return json_decode(json_decode($value));

    }



    public function saveImage($value,$destination_path)
    {


        $disk = "publicmedia";

        //Defined return.
        if(ends_with($value,'.jpg')) {
            return $value;
        }

        // if a base64 was sent, store it in the db
        if (starts_with($value, 'data:image'))
        {
            // 0. Make the image
            $image = \Image::make($value);
            // 2. Store the image on disk.
            \Storage::disk($disk)->put($destination_path, $image->stream());


            // Save Thumbs

            \Storage::disk($disk)->put(
                str_replace('.jpg','_mediumThumb.jpg',$destination_path),
                $image->fit('400','300')->stream()
            );

            \Storage::disk($disk)->put(
                str_replace('.jpg','_smallThumb.jpg',$destination_path),
                $image->fit('100','80')->stream()
            );

            // 3. Save the path to the database

            return $destination_path;
        }

        // if the image was erased
        if ($value==null) {
            // delete the image from disk
            \Storage::disk($disk)->delete($destination_path);

            // set null in the database column
            return null;
        }


    }


}
