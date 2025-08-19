<?php

namespace Modules\Iblog\Models;

use Illuminate\Database\Eloquent\Model;

class PostTranslation extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'title',
        'description',
        'slug',
        'summary',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'translatable_options',
        'status',
    ];
    protected $table = 'iblog__post_translations';

    protected $casts = [
        'translatable_options' => 'json',
        'meta_keywords' => 'array',
    ];

}
