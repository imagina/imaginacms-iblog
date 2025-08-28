<?php

namespace Modules\Iblog\Models;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Imagina\Icore\Models\CoreModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class Category extends CoreModel
{
  use Translatable;

  protected $table = 'iblog__categories';
  public string $transformer = 'Modules\Iblog\Transformers\CategoryTransformer';
  public string $repository = 'Modules\Iblog\Repositories\CategoryRepository';
  public array $requestValidation = [
    'create' => 'Modules\Iblog\Http\Requests\CreateCategoryRequest',
    'update' => 'Modules\Iblog\Http\Requests\UpdateCategoryRequest',
  ];
  //Instance external/internal events to dispatch with extraData
  public array $dispatchesEventsWithBindings = [
    //eg. ['path' => 'path/module/event', 'extraData' => [/*...optional*/]]
    'created' => [
      ['path' => 'Modules\Imedia\Events\CreateMedia']
    ],
    'creating' => [],
    'updated' => [
      ['path' => 'Modules\Imedia\Events\UpdateMedia']
    ],
    'updating' => [],
    'deleting' => [
      ['path' => 'Modules\Imedia\Events\DeleteMedia']
    ],
    'deleted' => []
  ];
  public array $translatedAttributes = [
    'title',
    'status',
    'description',
    'slug',
    'meta_title',
    'meta_description',
    'meta_keywords',
    'translatable_options'
  ];
  protected $fillable = [
    'parent_id',
    'featured',
    'options'
  ];

  protected $casts = [
    'options' => 'json'
  ];

  /**
   * Media Fillable
   */
  public $mediaFillable = [
    'mainimage' => 'single',
    'secondaryimage' => 'single',
    'iconimage' => 'single',
    'gallery' => 'multiple',
    'breadcrumbimage' => 'single',
  ];

  /**
   * Relation Media
   * Make the Many-To-Many Morph
   */
  public function files()
  {
    if (isModuleEnabled('Imedia')) {
      return app(\Modules\Imedia\Relations\FilesRelation::class)->resolve($this);
    }
    return new \Imagina\Icore\Relations\EmptyRelation();
  }

  public function parent(): BelongsTo
  {
    return $this->belongsTo(Category::class, 'parent_id');
  }

  public function children(): HasMany
  {
    return $this->hasMany(Category::class, 'parent_id');
  }

  public function posts(): BelongsToMany
  {
    return $this->belongsToMany(Post::class, 'iblog__post_category')->as('posts')->with('category');
  }

  public function url($locale = null): Attribute
  {
    return Attribute::get(function () use ($locale) {
      if ($this->internal) return "";

      if (empty($this->slug)) {
        $category = $this->getTranslation(\LaravelLocalization::getDefaultLocale());
        $this->slug = $category->slug ?? '';
      }

      $currentLocale = $locale ?? locale();
      if (!is_null($currentLocale)) {
        $this->slug = $this->getTranslation($currentLocale)->slug ?? "";
      }

      if (empty($this->slug)) return "";

      return \LaravelLocalization::localizeUrl('/' . $this->slug, $currentLocale);
    });
  }

}
