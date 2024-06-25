<?php

namespace Modules\Iblog\Entities;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Laracasts\Presenter\PresentableTrait;
use Modules\Core\Icrud\Entities\CrudModel;
use Modules\Core\Traits\NamespacedEntity;
use Modules\Iblog\Presenters\PostPresenter;
use Modules\Media\Entities\File;
use Modules\Media\Support\Traits\MediaRelation;
use Modules\Tag\Contracts\TaggableInterface;
use Modules\Tag\Traits\TaggableTrait;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;
use Modules\Isite\Traits\Typeable;
use Modules\Core\Icrud\Traits\hasEventsWithBindings;
use Modules\Isite\Traits\RevisionableTrait;
use Modules\Iqreable\Traits\IsQreable;

use Modules\Core\Support\Traits\AuditTrait;

class Post extends CrudModel implements TaggableInterface
{
  use Translatable, PresentableTrait, NamespacedEntity,
    TaggableTrait, MediaRelation, BelongsToTenant,
    Typeable, IsQreable;

  protected static $entityNamespace = 'asgardcms/post';

  public $transformer = 'Modules\Iblog\Transformers\PostTransformer';
  public $entity = 'Modules\Iblog\Entities\Post';
  public $repository = 'Modules\Iblog\Repositories\PostRepository';
  public $requestValidation = [
    'create' => 'Modules\Iblog\Http\Requests\CreatePostRequest',
    'update' => 'Modules\Iblog\Http\Requests\UpdatePostRequest',
  ];

  protected $table = 'iblog__posts';

  protected $fillable = [
    'options',
    'category_id',
    'user_id',
    'featured',
    'sort_order',
    'external_id',
    'created_at',
    'date_available'
  ];
  public $translatedAttributes = [
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
  protected $presenter = PostPresenter::class;

  protected $dates = [
    'date_available'
  ];

  protected $with = [
    'tags','files'
  ];

  protected $casts = [
    'options' => 'array'
  ];

  protected $revisionEnabled = true;
  protected $revisionCleanup = true;
  protected $historyLimit = 100;
  protected $revisionCreationsEnabled = true;

  public function categories()
  {
    return $this->belongsToMany(Category::class, 'iblog__post__category');
  }

  public function category()
  {
    return $this->belongsTo(Category::class);
  }

  public function user()
  {
    $driver = config('asgard.user.config.driver');

    return $this->belongsTo("Modules\\User\\Entities\\{$driver}\\User");
  }

  public function getOptionsAttribute($value)
  {
    $response = json_decode($value);

    if(is_string($response)) {
      $response = json_decode($response);
    }

    return $response;
  }

  /**
   *  main image url used to meta
   */
  public function getMainImageAttribute()
  {

    //Default
    $image = [
      'mimeType' => 'image/jpeg',
      'path' => url('modules/iblog/img/post/default.jpg')
    ];

    //Get and Set mainimage
    $mainimageFile = null;
    if($this->relationLoaded('files')) {
      foreach ($this->files as $file) {
        if ($file->pivot->zone == "mainimage") $mainimageFile = $file;
      }
    }

    if(!is_null($mainimageFile)){
      $image = [
        'mimeType' => $mainimageFile->mimetype,
        'path' => $mainimageFile->path_string
      ];
    }

    return json_decode(json_encode($image));

  }


  /**
   * URL post
   * @return string
   */
  public function getUrlAttribute($locale = null)
  {


    if (empty($this->slug)) {
      $post = $this->getTranslation(\LaravelLocalization::getDefaultLocale());
      $this->slug = $post->slug ?? "";
    }

    $currentLocale = $locale ?? locale();
    if (!is_null($locale)) {
      $this->slug = $this->getTranslation($currentLocale)->slug;
      $this->category = $this->category->getTranslation($currentLocale);
    }

    if (empty($this->slug)) return "";

    $currentDomain = !empty($this->organization_id) ? tenant()->domain ?? tenancy()->find($this->organization_id)->domain :
      parse_url(config('app.url'), PHP_URL_HOST);

    if (config("app.url") != $currentDomain) {
      $savedDomain = config("app.url");
      config(["app.url" => "https://" . $currentDomain]);
    }

    if (isset($this->options->urlCoder) && !empty($this->options->urlCoder) && $this->options->urlCoder == "onlyPost") {

      $url = \LaravelLocalization::localizeUrl('/' . $this->slug, $currentLocale);

    } else {
      if (empty($this->category->slug)) $url = "";
      else $url = \LaravelLocalization::localizeUrl('/' . $this->category->slug . '/' . $this->slug, $currentLocale);
    }

    if (isset($savedDomain) && !empty($savedDomain)) config(["app.url" => $savedDomain]);

    return $url;

  }

  /**
   * Magic Method modification to allow dynamic relations to other entities.
   * @return string
   * @var $destination_path
   * @var $value
   */
  public function __call($method, $parameters)
  {
    #i: Convert array to dot notation
    $config = implode('.', ['asgard.iblog.config.relations.post', $method]);

    #i: Relation method resolver
    if (config()->has($config)) {
      $function = config()->get($config);

      return $function($this);
    }

    #i: No relation found, return the call to parent (Eloquent) to handle it.
    return parent::__call($method, $parameters);
  }

}