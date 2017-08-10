<?php

use Modules\Iblog\Entities\Category;
use Modules\Iblog\Entities\Post;
use Modules\Iblog\Entities\Tag;
use Modules\User\Entities\Sentinel\User;
use Modules\Iblog\Entities\Status;

if (! function_exists('get_posts')) {

    function get_posts($options=array())
    {

        $default_options = array(
            'categories' => null,// categoria o categorias que desee llamar, se envia como arreglo ['categories'=>[1,2,3]]
            'users' => null, //usuario o usuarios que desea llamar, se envia como arreglo ['users'=>[1,2,3]]
            'include' => array(),//id de post a para incluir en una consulta, se envia como arreglo ['id'=>[1,2,3]]
            'exclude' => array(),// post, categorias o usuarios, que desee excluir de una consulta metodo de llmado category=>'', posts=>'' , users=>''
            'exclude_categories' => null,// categoria o categorias que desee Excluir, se envia como arreglo ['exclude_categories'=>[1,2,3]]
            'exclude_users' => null, //usuario o usuarios que desea Excluir, se envia como arreglo ['users'=>[1,2,3]]
            'created_at'=>['operator'=>'<=','date'=>date('Y-m-d H:i:s')],
            'take' => 5, //Numero de posts a obtener,
            'skip' => 0, //Omitir Cuantos post a llamar
            'order' => 'desc',//orden de llamado
            'status' => Status::PUBLISHED
        );

        $options = array_merge($default_options, $options);

        $posts = Post::with(['user', 'categories']);

        if (!empty($options['categories'])) {
            $posts->whereHas('categories', function ($query) use ($options) {
                $query->whereIn('category_id', $options['categories']);
            });
        }
        if (!empty($options['users'])) {
            $posts->whereHas('user', function ($query) use ($options) {
                $query->whereIn('user_id', $options['users']);
            });
        }
        if (!empty($options['include'])) {
            $posts->whereIn('id', $options['include']);
        }
        if (!empty($options['exclude'])) {
            $posts->whereNotIn('id', $options['exclude']);
        }
        if (isset($options['exclude_categories'])) {
            $posts->whereHas('categories', function ($query) use ($options) {
                $query->whereNotIn('category_id', $options['exclude_categories']);
            });
        }
        if (isset($options['exclude_users'])) {
            $posts->whereHas('user', function ($query) use ($options) {
                $query->whereNotIn('user_id', $options['exclude_users']);
            });
        }
        if (isset($options['created_at'])) {
            $posts->where('created_at',$options['created_at']['operator'], $options['created_at']['date']);
        }

        $posts->whereStatus($options['status'])
            ->skip($options['skip'])
            ->take($options['take'])
            ->orderBy('created_at', $options['order']);

        return $posts->get();

    }
}

if (! function_exists('all_users_by_rol')) {

    function all_users_by_rol($roleName) {

        $users = User::with(['roles'])
            ->whereHas('roles', function ($query) use ($roleName){
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
        if (!empty($options['include'])) {
            $categories->whereIn('id', $options['include']);
        }
        if (!empty($options['exclude'])) {
            $categories->whereNotIn('id', $options['exclude']);
        }
        if (!empty($options['parent'])) {
            $categories->where('parent_id', $options['parent']);
        }
        $categories->skip($options['skip'])
            ->take($options['take'])
            ->orderBy('created_at', $options['order']);

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
        if (!empty($options['include'])) {
            $tags->whereIn('id', $options['include']);
        }
        if (!empty($options['exclude'])) {
            $tags->whereNotIn('id', $options['exclude']);
        }
        if (!empty($options['parent'])) {
            $tags->where('parent_id', $options['parent']);
        }
        $tags->skip($options['skip'])
            ->take($options['take'])
            ->orderBy('created_at', $options['order']);

        return $tags->get();


    }
}

if(! function_exists('format_date')){
/**
 * Format date according to local module configuration.
 * @param object $date
 * @param string $format
 *
 * @return string
 **/

    function format_date($date, $format='%A, %B %d, %Y'){
        return strftime($format,strtotime($date));
    }

}

if (! function_exists('postgallery')){

    function postgallery($id){
        $images = Storage::disk('publicmedia')->files('assets/iblog/post/gallery/' . $id);
        return $images;
    }
}

date_default_timezone_set(config('asgard.iblog.config.timezone','UTC'));
setlocale(LC_TIME, config('asgard.iblog.config.localetime','en_US.UTF-8'));
