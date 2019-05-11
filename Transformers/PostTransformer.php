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
      'status' => $this->when($this->status, $this->present()->status),
      'metaTitle' => $this->when($this->meta_title, $this->meta_title),
      'metaDescription' => $this->when($this->meta_description, $this->meta_description),
      'metaKeywords' => $this->when($this->meta_keywords, $this->meta_keywords),
      'mainImage' => $this->mainimage,
      'secondaryImage' => $this->when($this->secondaryImage, $this->secondaryImage),
      'gallery' => $this->gallery,
      'createdAt' => $this->when($this->created_at, $this->created_at),
      'options' => $this->when($this->options, json_decode($this->option)),
      'category' => new CategoryTransformer($this->whenLoaded('category')),
      'editor' => new UserProfileTransformer($this->whenLoaded('user')),
      'categories' => CategoryTransformer::collection($this->whenLoaded('categories')),
      'tags' => TagTransformer::collection($this->whenLoaded('tags'))
    
    ];
    $locales = $this->whenLoaded('translations')->groupBy('locale');
    if (isset($locales) && !empty($locales)) {
      foreach ($locales as $locale => $items) {
        $data[$locale] = $items->first();
      }
    }
    
    
    return $data;
    
  }
}