<?php

namespace Modules\Iblog\Entities;

use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Laracasts\Presenter\PresentableTrait;
use Modules\Core\Traits\NamespacedEntity;
use Modules\Media\Entities\File;
use Modules\Media\Support\Traits\MediaRelation;

class Category extends Model
{
    use Translatable, MediaRelation, PresentableTrait, NamespacedEntity;

    protected $table = 'iblog__categories';
    protected static $entityNamespace = 'iblog/category';

    protected $fillable = ['title', 'description', 'slug', 'parent_id', 'options', 'translatableoption'];

    public $translatedAttributes = ['title', 'description', 'slug', 'metatitle', 'metadescription', 'metakeywords', 'translatableoption'];

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
        return $this->belongsToMany('Modules\Iblog\Entities\Post', 'iblog__post__category');
    }

    public function getOptionsAttribute($value)
    {
        return json_decode(json_decode($value));
    }

    public function getSecundaryimageAttribute()
    {
        $thumbnail = $this->files()->where('zone', 'secundaryimage')->first()->path??null;

        if ($thumbnail === null) {
            return 'modules/iblog/img/post/default.jpg';
        }

        return $thumbnail;
    }
    public function getMainimageAttribute()
    {
        $thumbnail = $this->files()->where('zone', 'mainimage')->first()->path??null;

        if ($thumbnail === null) {
            return 'modules/iblog/img/post/default.jpg';
        }

        return $thumbnail;
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
