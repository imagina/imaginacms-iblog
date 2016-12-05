<?php

namespace Modules\Iblog\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Iblog\Entities\Category;
use Modules\Iblog\Entities\Post;
use Modules\Iblog\Entities\Tag;
use Modules\Iblog\Repositories\Cache\CacheCategoryDecorator;
use Modules\Iblog\Repositories\Cache\CachePostDecorator;
use Modules\Iblog\Repositories\Cache\CacheTagDecorator;
use Modules\Iblog\Repositories\CategoryRepository;
use Modules\Iblog\Repositories\Eloquent\EloquentCategoryRepository;
use Modules\Iblog\Repositories\Eloquent\EloquentPostRepository;
use Modules\Iblog\Repositories\Eloquent\EloquentTagRepository;
use Modules\Iblog\Repositories\PostRepository;
use Modules\Iblog\Repositories\TagRepository;
use Modules\Core\Traits\CanPublishConfiguration;

class IblogServiceProvider extends ServiceProvider
{
    use CanPublishConfiguration;
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerBindings();
    }

    public function boot()
    {
        $this->publishConfig('iblog', 'config');
        //$this->publishConfig('iblog', 'settings');
        $this->publishConfig('iblog', 'permissions');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array();
    }

    private function registerBindings()
    {
        $this->app->bind(PostRepository::class, function () {
            $repository = new EloquentPostRepository(new Post());

            if (config('app.cache') === false) {
                return $repository;
            }

            return new CachePostDecorator($repository);
        });

        $this->app->bind(CategoryRepository::class, function () {
            $repository = new EloquentCategoryRepository(new Category());

            if (config('app.cache') === false) {
                return $repository;
            }

            return new CacheCategoryDecorator($repository);
        });

        $this->app->bind(TagRepository::class, function () {
            $repository = new EloquentTagRepository(new Tag());

            if (config('app.cache') === false) {
                return $repository;
            }

            return new CacheTagDecorator($repository);
        });

    }
}
