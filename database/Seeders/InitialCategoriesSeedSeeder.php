<?php

namespace Modules\Iblog\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Iblog\Models\Category;
use Modules\Iblog\Repositories\CategoryRepository;

class InitialCategoriesSeedSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    if (Category::count() === 0) {
      $categories = [
        [
          'featured' => true,
          'en' => [
            'title' => 'News',
            'slug' => 'news',
            'status' => 1,
            'description' => 'Latest news and stories',
            'meta_title' => 'News',
            'meta_description' => 'Latest blog news',
            'meta_keywords' => 'news, blog, updates',
          ],
          'es' => [
            'title' => 'Noticias',
            'slug' => 'noticias',
            'status' => 1,
            'description' => 'Últimas noticias y artículos',
            'meta_title' => 'Noticias',
            'meta_description' => 'Últimas noticias del blog',
            'meta_keywords' => 'noticias, blog, actualizaciones',
          ]
        ],
        [
          'featured' => false,
          'en' => [
            'title' => 'Updates',
            'slug' => 'updates',
            'status' => 1,
            'description' => 'Platform and product updates',
            'meta_title' => 'Updates',
            'meta_description' => 'All about updates',
            'meta_keywords' => 'updates, releases, changelog',
          ],
          'es' => [
            'title' => 'Actualizaciones',
            'slug' => 'actualizaciones',
            'status' => 1,
            'description' => 'Actualizaciones de la plataforma y producto',
            'meta_title' => 'Actualizaciones',
            'meta_description' => 'Todo sobre actualizaciones',
            'meta_keywords' => 'actualizaciones, cambios, producto',
          ]
        ]
      ];
      $repository = app(CategoryRepository::class);
      foreach ($categories as $category) {
        $repository->create($category);
      }
    }
  }
}
