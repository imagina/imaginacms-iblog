<?php

namespace Modules\Iblog\Transformers;

use Illuminate\Http\Resources\Json\Resource;
use Modules\User\Transformers\UserProfileTransformer;

class PostTransformer extends Resource
{
  public function toArray($request)
  {
    $dateformat = config('asgard.iblog.config.dateformat');
    $includes = explode(",", $request->include);
    $options = $this->options;

    unset($options->mainimage, $options->metatitle, $options->metadescription);

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
      'gallery' => $this->gallery,
      'addedBy' => $this->user->present()->fullname(),
      'metatitle' => $this->metatitle ?? $this->title,
      'metadescription' => $this->metadescription ?? $this->summary,
      'options' => $options,
      'created_at' => format_date($this->created_at, $dateformat),
      'updated_at' => format_date($this->updated_at, $dateformat),
      'created_date' => [
        'day' => date('d', strtotime($this->created_at)),
        'mounth' => date('M', strtotime($this->created_at)),
        'year' => date('Y', strtotime($this->created_at))
      ],
      'breadCrum' => ["items" => [], "path" => '']
    ];

    /*Bread Crum*/
    //Items
    $this->category ? array_push($data['breadCrum']['items'], $this->category->slug) : false;
    array_push($data['breadCrum']['items'], $this->slug);
    //Path
    $data['breadCrum']['path'] = join('/', $data['breadCrum']['items']);


    /*Transform fields multimedia in fake field from options*/
    foreach ($data['options'] as $index => &$option) {
      if (strpos($index, 'media-') !== false) {
        $option = !empty($option) ? url('assets/media/' . $option) : $option;
      }
    }

    /*Transform Relation Ships*/
    if (in_array('category', $includes)) {
      $data['category'] = new FullCategoryTransformer($this->category);
    }

    if (in_array('categories', $includes)) {

      $data['categories'] = CategoryTransformer::collection($this->categories);
    }
    if (in_array('tags', $includes)) {

      $data['tags'] = TagTransformer::collection($this->tags);
    }
    if (in_array('category', $includes)) {
      if ($this->category) {
        $data['category'] = new CategoryTransformer($this->category);
      } else {
        $data['category'] = array();
      }

    }
    return $data;
  }
}