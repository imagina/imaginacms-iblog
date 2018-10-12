<?php

namespace Modules\Iblog\Transformers;

use Illuminate\Http\Resources\Json\Resource;
use Modules\User\Transformers\UserProfileTransformer;

class CategoryTransformer extends Resource
{
    public function toArray($request)
    {
        $dateformat= config('asgard.iblog.config.dateformat');
        $options=$this->options;
        unset($options->mainimage,$options->metatitle,$options->metadescription);
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'url' => $this->url,
            'description' => $this->description,
            'summary' => $this->summary,
            'mainimage' => $this->mainimage,
            'mediumimage' => $this->mediumimage,
            'thumbails' => $this->thumbails,
            'metatitle'=>$this->metatitle??$this->title,
            'metadescription'=>$this->metadescription??$this->summary,
            'options' => $options,
            //'created_at' => format_date($this->created_at, $dateformat),
            //'updated_at' => format_date($this->updated_at, $dateformat)
        ];
    }
}