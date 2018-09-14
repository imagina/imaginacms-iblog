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
        }else{$this->attributes['slug'] = str_slug($this->attributes['title'],'-');}

    }
    public function getOptionsAttribute($value) {
        return json_decode(json_decode($value));
    }

    /**
     * get main image
     * @return string
     */
    public function getMainimageAttribute(){

        return ($this->options->mainimage ?? 'modules/iblog/img/post/default.jpg').'?v='.format_date($this->updated_at,'%u%w%g%k%M%S');
    }

    /**
     * get medium image
     * @return mixed|string
     */
    public function getMediumimageAttribute(){

        return str_replace('.jpg','_mediumThumb.jpg',$this->options->mainimage ?? 'modules/iblog/img/post/default.jpg').'?v='.format_date($this->updated_at,'%u%w%g%k%M%S');
    }

    /**
     * get small image
     * @return mixed|string
     */
    public function getThumbailsAttribute(){

        return str_replace('.jpg','_smallThumb.jpg',$this->options->mainimage?? 'modules/iblog/img/post/default.jpg').'?v='.format_date($this->updated_at,'%u%w%g%k%M%S');
    }
    public function getMetadescriptionAttribute(){

        return $this->options->metadescription ?? substr(strip_tags($this->description),0,150);
    }

    /**
     * @return mixed
     */
    public function getMetatitleAttribute(){

        return $this->options->metatitle ?? $this->title;
    }

    public function getUrlAttribute() {

        return url($this->slug);

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
