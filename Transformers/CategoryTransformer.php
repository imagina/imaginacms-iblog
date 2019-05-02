<?php

namespace Modules\Iblog\Transformers;

use Illuminate\Http\Resources\Json\Resource;
use Modules\User\Transformers\UserProfileTransformer;

class CategoryTransformer extends Resource
{
    public function toArray($request)
    {
        return [
            'id' => $this->when($this->id, $this->id),
            'title' => $this->when($this->title, $this->title),
            'slug' => $this->when($this->slug, $this->slug),
            'description' => $this->when($this->description, $this->description),
            'metatitle' => $this->when($this->metatitle, $this->metatitle),
            'metadescription' => $this->when($this->metadescription, $this->metadescription),
            'metakeywords' => $this->when($this->metakeywords, $this->metakeywords),
            'mainimage' => $this->mainimage,
            'secondaryimage' => $this->when($this->secondaryimage, $this->secondaryimage),
            'created_at' => $this->when($this->created_at, $this->created_at),
            'options' => $this->when($this->options, json_decode($this->option)),
            'parent' => new CategoryTransformer($this->whenLoaded('parent')),
            'translatable' => $this->whenLoaded('translations'),
            'children' => CategoryTransformer::collection($this->whenLoaded('children')),
        ];
    }
}