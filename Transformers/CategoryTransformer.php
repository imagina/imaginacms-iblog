<?php

namespace Modules\Iblog\Transformers;

use Illuminate\Http\Resources\Json\Resource;
use Modules\User\Transformers\UserProfileTransformer;
use Modules\Iblog\Transformers\PostTransformer;

class CategoryTransformer extends Resource
{
  public function toArray($request)
  {

    $dateformat = config('asgard.iblog.config.dateformat');
    $includes = explode(",", $request->include);
    $options = $this->options;
    unset($options->mainimage, $options->metatitle, $options->metadescription);

    //Format Data
    $data = [
      'id' => $this->id,
      'title' => $this->title,
      'slug' => $this->slug,
      'url' => $this->url,
      'description' => $this->description,
      'summary' => $this->summary,
      'mainimage' => $this->mainimage,
      'mediumimage' => $this->mediumimage,
      'thumbails' => $this->thumbails,
      'metatitle' => $this->metatitle ?? $this->title,
      'metadescription' => $this->metadescription ?? $this->summary,
      'options' => $options,
      'created_at' => format_date($this->created_at, $dateformat),
      'updated_at' => format_date($this->updated_at, $dateformat)
    ];

    /*Transform Relation Ships*/
    if (in_array('children', $includes)) {
      $data['children'] = FullCategoryTransformer::collection($this->children);
    }

    if (in_array('posts', $includes)) {
      $data['posts'] = PostTransformer::collection($this->posts);
    }

    if (in_array('parent', $includes)) {
      $data['parent'] = new CategoryTransformer($this->parent);
    }

    /*Return Data*/
    return $data;
  }
}