<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class IblogModifyFullTextIndexInPostsTranslationsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    $sm = Schema::getConnection()->getDoctrineSchemaManager();
    $indexesFound = $sm->listTableIndexes('iblog__post_translations');
    if (!array_key_exists("full", $indexesFound)) {
      \DB::statement("ALTER TABLE `iblog__post_translations` DROP INDEX `full`;");
      \DB::statement("ALTER TABLE iblog__post_translations ADD FULLTEXT full(title,description,summary)");
    }
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    //
  }
}
