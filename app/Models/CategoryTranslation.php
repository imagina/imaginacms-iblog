<?php

namespace Modules\Iblog\Models;

use Illuminate\Database\Eloquent\Model;

class CategoryTranslation extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'title',
        'description',
        'status', 'slug',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'translatable_options'
    ];
    protected $table = 'iblog__category_translations';

    protected $casts = [
        'translatable_options' => 'array',
    ];
}
