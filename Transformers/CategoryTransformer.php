<?php

namespace Modules\Iblog\Transformers;

use Illuminate\Http\Resources\Json\Resource;
use Modules\User\Transformers\UserProfileTransformer;
use Modules\Media\Image\Imagy;

class CategoryTransformer extends Resource
{
    /**
     * @var Imagy
     */
    private $imagy;
    /**
     * @var ThumbnailManager
     */
    private $thumbnailManager;

    public function __construct($resource)
    {
        parent::__construct($resource);

        $this->imagy = app(Imagy::class);
    }

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
            'main_image' => $this->mainimage,
            //'small_thumb' => $this->imagy->getThumbnail($this->mainimage, 'smallThumb'),
            //'medium_thumb' => $this->imagy->getThumbnail($this->mainimage, 'mediumThumb'),
            'secondaryimage' => $this->when($this->secondaryimage, $this->secondaryimage),
            'created_at' => $this->when($this->created_at, $this->created_at),
            'options' => $this->when($this->options, json_decode($this->option)),
            'parent' => new CategoryTransformer($this->whenLoaded('parent')),
            'translatable' => $this->whenLoaded('translations')->groupBy('locale'),
            'children' => CategoryTransformer::collection($this->whenLoaded('children')),
        ];
    }
}