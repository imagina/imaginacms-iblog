<?php

namespace Modules\Iblog\Repositories\Eloquent;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\App;
use Modules\Iblog\Repositories\TagRepository;
use Modules\Core\Repositories\Eloquent\EloquentBaseRepository;

class EloquentTagRepository extends EloquentBaseRepository implements TagRepository
{
    /**
     * Find a tag by its name
     * @param $name
     * @return mixed
     */
    public function findByName($name)
    {
        $tags = $this->model->where('name', 'like', "%$name%")->get();

        return $this->setLocaleAsKey($tags);
    }

    private function setLocaleAsKey($tags)
    {
        $cleanedTags = [];
        foreach ($tags as $tag) {
            foreach ($tag->translations as $tagTranslation) {
                if (App::getLocale() == $tagTranslation->locale) {
                    $cleanedTags[] = [
                        'id' => $tag->id,
                        'name' => $tagTranslation->name,
                    ];
                }
            }
        }

        return $cleanedTags;
    }

    /**
     * Create the tag for the specified language
     * @param  string $lang
     * @param  array  $name
     * @return mixed
     */
    public function createForLanguage($lang = 'en', $name)
    {
        $data = [
            $lang => [
                'name' => $name,
                'slug' => str_slug($name),
            ],
        ];

        return $this->create($data);
    }

    public function findBySlug($slug)
    {
        return $this->model->where('slug', "$slug")->firstOrFail();
    }

    public function whereFilters($filters,$includes=array())
    {
        $query=count($includes)!==0?$this->model->with($includes):$this->model;
        if (!empty($filters->exclude)) {
            $query->whereNotIn('id', $filters->exclude);
        }

        if (!empty($filters->include)) {
            $query->orWhere(function ($query) use ($filters) {
                $query->whereIn('id', $filters->include);
            });

        }
        if (isset($filters->search)) { //si hay que filtrar por rango de precio
            $criterion = $filters->search;

            $param = explode(' ', $criterion);

            $query->whereHas(function ($query) use ($param) {
                foreach ($param as $index => $word) {
                    if ($index == 0) {
                        $query->where('title', 'like', "%" . $word . "%");
                    } else {
                        $query->orWhere('title', 'like', "%" . $word . "%");
                    }
                }

            });
        }

        if (isset($filters->order)) { //si hay que filtrar por rango de precio
            $orderby = $filters->order->by ?? 'created_at';
            $ordertype = $filters->order->type ?? 'desc';
        } else {
            $orderby = 'created_at';
            $ordertype = 'desc';
        }


        $query->skip($filters->skip ?? 0);
        $query->orderBy($orderby, $ordertype);
        if (isset($filter->take)) {
            $query->take($filter->take ?? 5);
            return $query->get();
        } elseif (isset($filter->paginate) && is_integer($filters->paginate)) {
            return $query->paginate($filters->paginate);
        } else {
            return $query->paginate(12);
        }
    }
}
