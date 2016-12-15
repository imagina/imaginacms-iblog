<?php

namespace Modules\Iblog\Entities;

use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Modules\Bcrud\Support\Traits\CrudTrait;
use Modules\Iblog\Entities\Post;
use Modules\Iblog\Entities\Feature;

class Tag extends Model
{
    use CrudTrait;

    protected $table = 'iblog__tags';

    protected $fillable = ['title','slug'];



    /**
     * The attributes that should be casted to native types.
     *
     * @var array
    */



    protected function setSlugAttribute($value){

        if(!empty($value)){
            $this->attributes['slug'] = str_slug($value,'-');
        } else {
            $this->attributes['slug'] = str_slug($this->title,'-');
        }



    }

    public function posts()
    {
        return $this->belongsToMany(Post::class, 'iblog__post__tag');
    }



}
