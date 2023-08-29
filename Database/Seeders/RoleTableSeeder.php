<?php

namespace Modules\Iblog\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

use Modules\Isite\Jobs\ProcessSeeds;

class RoleTableSeeder extends Seeder
{ 

  private $profileRoleRepository;

  public function __construct()
  {
    $this->profileRoleRepository = app("Modules\Iprofile\Repositories\RoleApiRepository");
  }

  public function run()
  {
    Model::unguard();
    $rolesId = [];

    //Roles
    $roles = [
      [
        'name' => 'Editor',
        'slug' => 'editor',
        'permissions' => [
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
        ]
      ],
      [
        'name' => 'Author',
        'slug' => 'author',
        'permissions' => [
          'profile.api.login' => true,
          'profile.user.index' => true,
          'iblog.posts.manage' => true,
          'iblog.posts.index' => true,
          'iblog.posts.create' => true,
          'iblog.posts.edit' => true,
        ]
      ]
    ];

    //Create Roles
    foreach ($roles as $role) {

      $params = [
        "filter" => [
          "field" => "slug",
        ],
        "include" => [],
        "fields" => [],
      ];

      $roleExist = $this->profileRoleRepository->getItem($role["slug"], json_decode(json_encode($params)));
      if (!isset($roleExist->id)) {
        $this->profileRoleRepository->create($role);
      }
     
    }

    //To update de IprofileSetting 'assignedRoles'
    ProcessSeeds::dispatch([
      "baseClass" => "\Modules\Iprofile\Database\Seeders",
      "seeds" => ["RolePermissionsSeeder"]
    ]);
    

  }
}
