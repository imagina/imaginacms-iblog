<?php

namespace Modules\Iblog\Transformers;

use Illuminate\Http\Resources\Json\Resource;
use Modules\Tag\Transformers\TagTransformer;
use Modules\User\Transformers\UserProfileTransformer;
use Illuminate\Support\Arr;

class PostTransformer extends Resource
{
  public function toArray($request)
  {
    $data = [
      'id' => $this->when($this->id, $this->id),
      'title' => $this->when($this->title, $this->title),
      'slug' => $this->when($this->slug, $this->slug),
      'summary' => $this->when($this->summary, $this->summary),
      'description' => $this->when($this->description, $this->description),
      'status' => $this->when($this->status, intval($this->status)),
      'statusName' => $this->when($this->status, $this->present()->status),
      'metaTitle' => $this->when($this->meta_title, $this->meta_title),
      'metaDescription' => $this->when($this->meta_description, $this->meta_description),
      'metaKeywords' => $this->when($this->meta_keywords, $this->meta_keywords),
      'mainImage' => $this->main_image,
      'secondaryImage' => $this->when($this->secondary_image, $this->secondary_image),
      'gallery' => $this->gallery,
      'createdAt' => $this->when($this->created_at, $this->created_at),
      'options' => $this->when($this->options, $this->option),
      'category' => new CategoryTransformer($this->whenLoaded('category')),
      'editor' => new UserProfileTransformer($this->whenLoaded('user')),
      'categories' => CategoryTransformer::collection($this->whenLoaded('categories')),
      'tags' => TagTransformer::collection($this->whenLoaded('tags'))
    
    ];

    foreach ($this->tags as $tag) {
      $data['tags'][] = $tag->name;
    }
    $filter = json_decode($request->filter);

    // Return data with available translations
    if (isset($filter->allTranslations) && $filter->allTranslations) {
      // Get langs avaliables
      $languages = \LaravelLocalization::getSupportedLocales();

      foreach ($languages as $lang => $value) {
        $data[$lang]['title'] = $this->hasTranslation($lang) ?
          $this->translate("$lang")['title'] : '';
        $data[$lang]['description'] = $this->hasTranslation($lang) ?
          $this->translate("$lang")['description'] ?? '' : '';
        $data[$lang]['summary'] = $this->hasTranslation($lang) ?
          $this->translate("$lang")['summary'] : '';
        $data[$lang]['metaTitle'] = $this->hasTranslation($lang) ?
          $this->translate("$lang")['meta_title'] : '';
        $data[$lang]['metaDescription'] =$this->hasTranslation($lang) ?
          $this->translate("$lang")['meta_description'] : '';
        $data[$lang]['metaKeywords'] =$this->hasTranslation($lang) ?
          $this->translate("$lang")['meta_keywords'] : '';

      }
    }

    return $data;
    
  }
}