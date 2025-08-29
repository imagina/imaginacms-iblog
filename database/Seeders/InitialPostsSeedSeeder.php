<?php

namespace Modules\Iblog\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Iblog\Models\Post;
use Modules\Iblog\Repositories\PostRepository;

class InitialPostsSeedSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    if (Post::count() === 0) {
      $posts = [
        [
          'category_id' => 1,
          'featured' => true,
          'en' => [
            'title' => 'Welcome to our Blog',
            'slug' => 'welcome-blog',
            'status' => 1,
            'summary' => 'An introduction to our content',
            'description' => '<p>This is the first post on our blog. Stay tuned for updates!</p>',
            'meta_title' => 'Welcome Blog',
            'meta_description' => 'Introductory blog post',
            'meta_keywords' => 'blog, welcome, introduction',
            'translatable_options' => [],
          ],
          'es' => [
            'title' => 'Bienvenido a nuestro Blog',
            'slug' => 'bienvenido-blog',
            'status' => 1,
            'summary' => 'Una introducción a nuestro contenido',
            'description' => '<p>Este es el primer post de nuestro blog. ¡Atento a las novedades!</p>',
            'meta_title' => 'Blog Bienvenida',
            'meta_description' => 'Post introductorio del blog',
            'meta_keywords' => 'blog, bienvenida, introducción',
            'translatable_options' => [],
          ]
        ],
        [
          'category_id' => 2,
          'featured' => false,
          'en' => [
            'title' => 'Product Update August',
            'slug' => 'product-update-august',
            'status' => 1,
            'summary' => 'Latest improvements in our platform',
            'description' => '<p>We’ve rolled out several new features this month.</p>',
            'meta_title' => 'August Update',
            'meta_description' => 'New platform features',
            'meta_keywords' => 'product, update, features',
            'translatable_options' => [],
          ],
          'es' => [
            'title' => 'Actualización de Producto Agosto',
            'slug' => 'actualizacion-producto-agosto',
            'status' => 1,
            'summary' => 'Últimas mejoras en nuestra plataforma',
            'description' => '<p>Este mes hemos lanzado nuevas funcionalidades.</p>',
            'meta_title' => 'Actualización Agosto',
            'meta_description' => 'Nuevas funcionalidades de la plataforma',
            'meta_keywords' => 'producto, actualización, mejoras',
            'translatable_options' => [],
          ]
        ]
      ];
      $repository = app(PostRepository::class);
      foreach ($posts as $post) {
        $repository->create($post);
      }
    }
  }
}
