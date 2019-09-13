<?php

use Illuminate\Routing\Router;
if(Request::path()!='backend') {
    /** @var Router $router */
    $router->group(['prefix' => '{slug}'], function (Router $router) {
        $locale = LaravelLocalization::setLocale() ?: App::getLocale();

        $router->get('/', [
            'as' => $locale . '.iblog.category',
            'uses' => 'PublicController@index',
            'middleware' => config('asgard.iblog.config.middleware'),
        ]);
        $router->get('{slugp}', [
            'as' => $locale . '.iblog.post',
            'uses' => 'PublicController@show',
            'middleware' => config('asgard.iblog.config.middleware'),
        ]);
    });

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


/** @var Router $router */
$router->group(['prefix' => 'iblog/feed'], function (Router $router) {
    $locale = LaravelLocalization::setLocale() ?: App::getLocale();

    $router->get('{format}', [
        'as' => $locale . '.iblog.feed.format',
        'uses' => 'PublicController@feed',

    ]);
});