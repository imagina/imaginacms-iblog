<?php

use Modules\Iblog\Entities\Category;
use Modules\Iblog\Entities\Post;
use Modules\Iblog\Entities\Tag;
use Modules\User\Entities\Sentinel\User;
use Modules\Iblog\Entities\Status;

if (!function_exists('get_posts')) {

    function get_posts($options = array())
    {

        $default_options = array(
            'categories' => null,// categoria o categorias que desee llamar, se envia como arreglo ['categories'=>[1,2,3]]
            'users' => null, //usuario o usuarios que desea llamar, se envia como arreglo ['users'=>[1,2,3]]
            'include' => array(),//id de post a para incluir en una consulta, se envia como arreglo ['id'=>[1,2,3]]
            'exclude' => array(),// post, categorias o usuarios, que desee excluir de una consulta metodo de llmado category=>'', posts=>'' , users=>''
            'exclude_categories' => null,// categoria o categorias que desee Excluir, se envia como arreglo ['exclude_categories'=>[1,2,3]]
            'exclude_users' => null, //usuario o usuarios que desea Excluir, se envia como arreglo ['users'=>[1,2,3]]
            'created_at' => ['operator' => '<=', 'date' => date('Y-m-d H:i:s')],
            'take' => 5, //Numero de posts a obtener,
            'skip' => 0, //Omitir Cuantos post a llamar
            'order' => 'desc',//orden de llamado
            'status' => Status::PUBLISHED
        );

        $options = array_merge($default_options, $options);

        $posts = Post::with(['user', 'categories', 'category'])->select('*', 'iblog__posts.id as id','iblog__posts.category_id as category_id');

        if (!empty($options['categories']) || isset($options['exclude_categories'])) {

            $posts->leftJoin('iblog__post__category', 'iblog__post__category.post_id', '=', 'iblog__posts.id');
        }

        if (!empty($options['categories'])) {

            $posts->whereIn('iblog__post__category.category_id', $options['categories']);

        }
        if (isset($options['exclude_categories'])) {

            $posts->whereNotIn('iblog__post__category.category_id', $options['exclude_categories']);
        }

        if (!empty($options['users'])) {
            $posts->whereHas('user', function ($query) use ($options) {
                $query->whereIn('user_id', $options['users']);
            });
        }
        if (!empty($options['exclude'])) {
            $posts->whereNotIn('iblog__posts.id', $options['exclude']);
        }

        if (isset($options['exclude_users'])) {
            $posts->whereHas('user', function ($query) use ($options) {
                $query->whereNotIn('user_id', $options['exclude_users']);
            });
        }
        if (isset($options['created_at'])) {
            $posts->where('created_at', $options['created_at']['operator'], $options['created_at']['date']);
        }

        $posts->whereStatus($options['status'])
            ->skip($options['skip'])
            ->take($options['take']);

        $posts->orderBy('created_at', $options['order']);

        if (!empty($options['include'])) {
            $posts->orWhere(function ($query) use ($options) {
                $query->whereIn('iblog__posts.id', $options['include']);
            });

        }
        return $posts->get();

    }
}

if (!function_exists('all_users_by_rol')) {

    function all_users_by_rol($roleName)
    {

        $users = User::with(['roles'])
            ->whereHas('roles', function ($query) use ($roleName) {
                $query->where('name', '=', $roleName);
            })->latest()->get();

        return $users;

    }
}

if (!function_exists('get_categories')) {

    function get_categories($options = array())
    {
        $default_options = array(
            'include' => array(),//id de Categorias  para incluir en una consulta, se envia como arreglo ['include'=>[1,2,3]]
            'exclude' => array(),//id de categorias  que desee excluir de una consulta metodo de llmado category=>'[1,2]'
            'parent' => '0', //id de categorias  padre que desee mostrar en una consulta metodo de llmado category=>'[1,2]'
            'take' => 5, //Numero de posts a obtener,
            'skip' => 0, //Omitir Cuantos post a llamar
            'order' => 'desc',//orden de llamado
        );

        $options = array_merge($default_options, $options);

        $categories = Category::query();
        if (!empty($options['exclude'])) {
            $categories->whereNotIn('id', $options['exclude']);
        }
        if (!empty($options['parent'])) {
            $categories->where('parent_id', $options['parent']);
        }
        $categories->skip($options['skip'])
            ->take($options['take'])
            ->orderBy('created_at', $options['order']);
        if (!empty($options['include'])) {
            $categories->orWhere(function ($query) use ($options) {
                $query->whereIn('id', $options['include']);;
            });
        }
        return $categories->get();

    }
}

if (!function_exists('get_tags')) {

    function get_tags($options = array())
    {
        $default_options = array(
            'include' => array(),//id de Categorias  para incluir en una consulta, se envia como arreglo ['include'=>[1,2,3]]
            'exclude' => array(),//id de categorias  que desee excluir de una consulta metodo de llmado category=>'[1,2]'
            'take' => 5, //Numero de posts a obtener,
            'skip' => 0, //Omitir Cuantos post a llamar
            'order' => 'desc',//orden de llamado
        );

        $options = array_merge($default_options, $options);

        $tags = Tag::query();

        if (!empty($options['exclude'])) {
            $tags->whereNotIn('id', $options['exclude']);
        }
        if (!empty($options['parent'])) {
            $tags->where('parent_id', $options['parent']);
        }
        $tags->skip($options['skip'])
            ->take($options['take'])
            ->orderBy('created_at', $options['order']);
        if (!empty($options['include'])) {
            $tags->orWhere(function ($query) use ($options) {
                $query->whereIn('id', $options['include']);;
            });
        }
        return $tags->get();


    }
}

if (!function_exists('format_date')) {
    /**
     * Format date according to local module configuration.
     * @param object $date
     * @param string $format
     *
     * @return string
     **/

    function format_date($date, $format = '%A, %B %d, %Y')
    {
        return strftime($format, strtotime($date));
    }

}

if (!function_exists('postgallery')) {

    function postgallery($id)
    {
        $images = Storage::disk('publicmedia')->files('assets/iblog/post/gallery/' . $id);
        return $images;
    }
}

date_default_timezone_set(config('asgard.iblog.config.timezone', 'UTC'));
setlocale(LC_TIME, config('asgard.iblog.config.localetime', 'en_US.UTF-8'));


if(!function_exists('get_status')){
    function get_status($status)
    {
        switch ($status) {
            case trans('iblog::common.status.draft'):
                return Status::DRAFT;
                break;
            case trans('iblog::common.status.pending'):
                return Status::PENDING;
                break;
            case trans('iblog::common.status.published'):
                return Status::PUBLISHED;
                break;
            case trans('iblog::common.status.unpublished'):
                return Status::UNPUBLISHED;
                break;
            default:
                return Status::PUBLISHED;

        }

    }
}


if (!function_exists(' saveImage')) {

    function saveImage($value, $destination_path, $disk='publicmedia', $size = array(), $watermark = array())
    {

        $default_size = [
            'imagesize' => [
                'width' => 1024,
                'height' => 768,
                'quality'=>80
            ],
            'mediumthumbsize' => [
                'width' => 400,
                'height' => 300,
                'quality'=>80
            ],
            'smallthumbsize' => [
                'width' => 100,
                'height' => 80,
                'quality'=>80
            ],
        ];
        $default_watermark=[
            'activated' => false,
            'url' => 'modules/ihelpers/img/watermark/watermark.png',
            'position' => 'top-left',
            'x' => 10,
            'y' => 10,
        ];
        $size = json_decode(json_encode(array_merge($default_size, $size)));
        $watermark = json_decode(json_encode(array_merge($default_watermark, $watermark)));

        //Defined return.
        if (ends_with($value, '.jpg')) {
            return $value;
        }

        // if a base64 was sent, store it in the db
        if (starts_with($value, 'data:image')) {
            // 0. Make the image
            $image = \Image::make($value);
            // resize and prevent possible upsizing

            $image->resize($size->imagesize->width, $size->imagesize->height, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
            if ($watermark->activated) {
                $image->insert($watermark->url, $watermark->position, $watermark->x, $watermark->y);
            }
            // 2. Store the image on disk.
            \Storage::disk($disk)->put($destination_path, $image->stream('jpg', $size->imagesize->quality));


            // Save Thumbs
            \Storage::disk($disk)->put(
                str_replace('.jpg', '_mediumThumb.jpg', $destination_path),
                $image->fit($size->mediumthumbsize->width, $size->mediumthumbsize->height)->stream('jpg', $size->mediumthumbsize->quality)
            );

            \Storage::disk($disk)->put(
                str_replace('.jpg', '_smallThumb.jpg', $destination_path),
                $image->fit($size->smallthumbsize->width, $size->smallthumbsize->height)->stream('jpg', $size->smallthumbsize->quality)
            );

            // 3. Return the path
            return $destination_path;
        }

        // if the image was erased
        if ($value == null) {
            // delete the image from disk
            \Storage::disk($disk)->delete($destination_path);

            // set null in the database column
            return null;
        }


    }
}
