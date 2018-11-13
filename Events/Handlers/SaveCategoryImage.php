<?php


namespace Modules\Iblog\Events\Handlers;


use Modules\Iblog\Events\CategoryWasCreated;
use Modules\Iblog\Repositories\CategoryRepository;

class SaveCategoryImage
{

    private $category;

    public function __construct(CategoryRepository $category)
    {
        $this->category = $category;
    }

    public function handle(CategoryWasCreated $event)
    {

        $id = $event->entity->id;

        if (!empty($event->data['mainimage'])) {
            $mainimage = saveImage($event->data['mainimage'], "assets/iblog/category/" . $id . ".jpg");
            if(isset($event->data['options'])){
                $options=(array)$event->data['options'];
            }else{
                $options = array();
            }
            $options["mainimage"] = $mainimage;
            $event->data['options'] = json_encode($options);
        }else{
            $event->data['options'] = json_encode($event->data['options']);
        }

       $this->category->update($event->entity, $event->data);
    }

}