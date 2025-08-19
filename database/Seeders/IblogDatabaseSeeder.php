<?php

namespace Modules\Iblog\Database\Seeders;

use Illuminate\Database\Seeder;

class IblogDatabaseSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    $this->call([InitialCategoriesSeedSeeder::class, InitialPostsSeedSeeder::class]);
  }
}
