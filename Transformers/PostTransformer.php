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
            'metatitle' => $this->when($this->metatitle, $this->metatitle),
            'metadescription' => $this->when($this->metadescription, $this->metadescription),
            'metakeywords' => $this->when($this->metakeywords, $this->metakeywords),
            'mainimage' => $this->mainimage,
            'secondaryimage' => $this->when($this->secondaryimage, $this->secondaryimage),
            'gallery' => $this->gallery,
            'created_at' => $this->when($this->created_at, $this->created_at),
            'options' => $this->when($this->options, json_decode($this->option)),
            'category' => new CategoryTransformer($this->whenLoaded('category')),
            'editor' => new UserProfileTransformer($this->whenLoaded('user')),
            'categories' => CategoryTransformer::collection($this->whenLoaded('categories')),
            'tags' => TagTransformer::collection($this->whenLoaded('tags'))

        ];
        $translatables = $this->whenLoaded('translations')->groupBy('locale');
       if(isset($translatables) && !empty($translatables)){
           foreach ($translatables as $locale => $items) {
               $data[$locale] = $items->first();
           }
       }


        return $data;

    }
}