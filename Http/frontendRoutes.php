<?php

use Illuminate\Routing\Router;
use Modules\Iblog\Entities\Category as Category;

/** @var Router $router */
if (! App::runningInConsole()) {
    foreach (Category::query()->where('parent_id', 0)->get() as $category) {

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
