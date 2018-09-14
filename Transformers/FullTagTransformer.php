<?php

namespace Modules\Iblog\Transformers;

use Illuminate\Http\Resources\Json\Resource;
use Modules\Iblog\Events\PostWasCreated;
use Modules\User\Transformers\UserProfileTransformer;

class FullTagTransformer extends Resource
{
    public function toArray($request)
    {
        $dateformat = config('asgard.iblog.config.dateformat');
        $includes=explode(",",$request->include);
        $data= [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'url' => $this->url,
            'created_at' => format_date($this->created_at, $dateformat),
            'updated_at' => format_date($this->updated_at, $dateformat)
        ];

        if (in_array('post',$includes)) {

            $data['post']= PostTransformer::collection($this->posts);
        }
        return $data;
    }
}