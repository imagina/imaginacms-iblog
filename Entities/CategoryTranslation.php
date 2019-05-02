<?php

namespace Modules\Iblog\Entities;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class CategoryTranslation extends Model
{
    use Sluggable;

    public $timestamps = false;
    protected $table = 'iblog__category_translations';
    protected $fillable = ['title', 'description', 'slug', 'metatitle', 'metadescription', 'metakeywords', 'translatableoption'];



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
    public function getMetadescriptionAttribute(){

        return $this->metadescription ?? substr(strip_tags($this->description??''),0,150);
    }

    /**
     * @return mixed
     */
    public function getMetatitleAttribute(){

        return $this->metatitle ?? $this->title;
    }

    public function getUrlAttribute() {

        return url($this->slug);

    }

}
