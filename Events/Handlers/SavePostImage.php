<?php


namespace Modules\Iblog\Events\Handlers;


use Modules\Iblog\Events\PostWasCreated;
use Modules\Iblog\Repositories\PostRepository;

class SavePostImage
{

    private $post;

    public function __construct(PostRepository $post)
    {
        $this->post = $post;
    }

    public function handle(PostWasCreated $event)
    {
        $id = $event->post->id;
        if (!empty($event->data['mainimage'])) {
            $mainimage = saveImage($event->data['mainimage'], "assets/iblog/post/" . $id . ".jpg");
            if(isset($event->data['options'])){
                $options=(array)$event->data['options'];
            }else{$options = array();}
            $options["mainimage"] = $mainimage;
            if (!empty($event->data['gallery']) && !empty($id)) {
                if (count(\Storage::disk('publicmedia')->files('assets/iblog/post/gallery/' . $event->data['gallery']))) {
                    \File::makeDirectory('assets/iblog/post/gallery/' . $id);
                    $success = rename('assets/iblog/post/gallery/' . $event->data['gallery'], 'assets/iblog/post/gallery/' . $id);
                }
            }
            $event->data['options'] = json_encode($options);
        }else{
            $event->data['options'] = json_encode($event->data['options']);
        }

        $this->post->update($event->post, $event->data);
    }

}