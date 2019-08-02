<?php

namespace Modules\Iblog\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;


class RoleTableSeeder extends Seeder
{

  public function run()
  {
    Model::unguard();
    $rolesId = [];

    //Roles
    $roles = [
      [
        'name' => 'Editor',
        'slug' => 'editor',
        'permissions' => json_encode([
          'profile.api.login' => true,
          'profile.user.index' => true,
          'iblog.categories.manage' => true,
          'iblog.categories.index' => true,
          'iblog.categories.create' => true,
          'iblog.categories.edit' => true,
          'iblog.categories.destroy' => true,
          'iblog.posts.manage' => true,
          'iblog.posts.index' => true,
          'iblog.posts.create' => true,
          'iblog.posts.edit' => true,
          'iblog.posts.destroy' => true,
        ])
      ],
      [
        'name' => 'Author',
        'slug' => 'author',
        'permissions' => json_encode([
          'profile.api.login' => true,
          'profile.user.index' => true,
          'iblog.posts.manage' => true,
          'iblog.posts.index' => true,
          'iblog.posts.create' => true,
          'iblog.posts.edit' => true,
        ])
      ]
    ];

    //Create Roles
    foreach ($roles as $role) {
      array_push($rolesId, DB::table('roles')->insertGetId($role));
    }

    //Assign Role to admin user
    \DB::table('role_users')->insert([
      ['user_id' => 1, 'role_id' => $rolesId[0]],
      ['user_id' => 1, 'role_id' => $rolesId[1]]
    ]);
  }
}
