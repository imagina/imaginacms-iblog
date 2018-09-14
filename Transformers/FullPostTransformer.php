<?php

namespace Modules\Iblog\Transformers;

use Illuminate\Http\Resources\Json\Resource;
use Modules\User\Transformers\UserProfileTransformer;

class FullPostTransformer extends Resource
{
    public function toArray($request)
    {
        $dateformat= config('asgard.iblog.config.dateformat');
        $includes=explode(",",$request->include);
        $options=$this->options;
        unset($options->mainimage,$options->metatitle,$options->metadescription);
       $data = [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'url' => $this->url,
            'description' => $this->description,
            'summary' => $this->summary,
            'mainimage' => $this->mainimage,
            'mediumimage'=>$this->mediumimage,
            'thumbails'=>$this->thumbails,
            'gallery'=>$this->gallery,
            'metatitle'=>$this->metatitle??$this->title,
            'metadescription'=>$this->metadescription??$this->summary,
            'options'=>$options,
            'created_at'=>format_date($this->created_at,$dateformat),
            'updated_at'=>format_date($this->updated_at,$dateformat)
        ];

        if (in_array('categories',$includes)) {

                $data['categories']= CategoryTransformer::collection($this->categories);
        }
        if (in_array('tags', $includes)) {

            $data['tags']= TagTransformer::collection($this->tags);
        }
        if (in_array('category',$includes)) {
        if ($this->category) {
            $data['category'] = new CategoryTransformer($this->category);
        } else {
            $data['category'] = array();
        }

    }
        return $data;
    }
}