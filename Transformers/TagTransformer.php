<?php

namespace Modules\Iblog\Transformers;

use Illuminate\Http\Resources\Json\Resource;
use Modules\User\Transformers\UserProfileTransformer;

class TagTransformer extends Resource
{
    public function toArray($request)
    {
        $dateformat = config('asgard.iblog.config.dateformat');
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'url' => $this->url,
            'created_at' => format_date($this->created_at, $dateformat),
            'updated_at' => format_date($this->updated_at, $dateformat)
        ];
    }
}