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
}
