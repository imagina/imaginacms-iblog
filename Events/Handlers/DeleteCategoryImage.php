<?php


namespace Modules\Iblog\Events\Handlers;


use Modules\Iblog\Events\CategoryWasCreated;


class DeleteCategoryImage
{



    public function __construct()
    {

    }

    public function handle(CategoryWasDeleted $event)
    {
        $id = $event->entity->id;

        $destination_path =  "assets/iblog/category/" . $id . ".jpg";
        \Storage::disk($event->disk)->delete($destination_path);

    }

}