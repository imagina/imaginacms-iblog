<?php

namespace Modules\Iblog\Entities;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;


class PostTranslation extends Model
{
    use Sluggable;

    public $timestamps = false;
    protected $table = 'iblog__post_translations';
    protected $fillable = ['title','description','slug','summary','meta_title','meta_description','meta_keywords','translatable_option'];



    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
       public function sluggable()
       {
           return [
               'slug' => [
                   'source' => 'title'
               ]
        ];
       }


    /**
     * @param $value
     * @return
     */
    public function setSummaryAttribute($value)
    {
        $this->attributes['summary']= $value??isubstr(strip_tags($this->attributes['description']),150);
    }
    /**
     * @return mixed
     */
    public function getMetaDescriptionAttribute()
    {

        return $this->meta_description ?? $this->summary;
    }

    /**
     * @return mixed
     */
    public function getMetaTitleAttribute()
    {

        return $this->meta_title ?? $this->title;
    }


}
