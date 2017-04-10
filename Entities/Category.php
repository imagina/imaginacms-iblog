<?php

namespace Modules\Iblog\Entities;

use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Modules\Bcrud\Support\Traits\CrudTrait;

class Category extends Model
{
    use CrudTrait;

    protected $table = 'iblog__categories';

    protected $fillable = ['title','description','slug','parent_id','options'];

    protected $fakeColumns = ['options'];
    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'options' => 'array'
    ];



    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */
    public function parent()
    {
        return $this->belongsTo('Modules\Iblog\Entities\Category', 'parent_id');
    }
    public function children()
    {
        return $this->hasMany('Modules\Iblog\Entities\Category', 'parent_id');
    }

    public function posts()
    {
        return $this->belongsToMany('Modules\Iblog\Entities\Post','iblog__post__category');
    }
    protected function setSlugAttribute($value){

        if(!empty($value)){
            $this->attributes['slug'] = str_slug($value,'-');
        }else{$this->attributes['slug'] = str_slug($this->title,'-');}



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

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */
    public function scopeFirstLevelItems($query)
    {
        return $query->where('depth', '1')
            ->orWhere('depth', null)
            ->orderBy('lft', 'ASC');
    }
}
