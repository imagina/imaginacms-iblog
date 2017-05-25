<?php

use Illuminate\Routing\Router;
use Modules\Iblog\Entities\Category as Category;
use Modules\Iblog\Entities\Tag as Tag;

/** @var Router $router */
if (! App::runningInConsole()) {
    foreach (Category::all()  as $category) {
        /** @var Router $router */
        $router->group(['prefix' => $category->slug], function (Router $router) use ($category) {
            $locale = LaravelLocalization::setLocale() ?: App::getLocale();

            $router->get('/', [
                'as' => $locale . '.iblog.' . $category->slug,
                'uses' => 'PublicController@index',
                //'middleware' => config('asgard.iblog.config.middleware'),
            ]);
            $router->get('{slug}', [
                'as' => $locale . '.iblog.' . $category->slug .'.slug',
                'uses' => 'PublicController@show',
                //'middleware' => config('asgard.iblog.config.middleware'),
            ]);
        });
    }


}


/** @var Router $router */
$router->group(['prefix' => trans('iblog::tag.uri')], function (Router $router) {
    $locale = LaravelLocalization::setLocale() ?: App::getLocale();

    $router->get('{slug}', [
        'as' => $locale . '.iblog.tag.slug',
        'uses' => 'PublicController@tag',
        //'middleware' => config('asgard.iblog.config.middleware'),
    ]);
});

