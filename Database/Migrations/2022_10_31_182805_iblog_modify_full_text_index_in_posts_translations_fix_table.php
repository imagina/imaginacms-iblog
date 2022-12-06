<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class IblogModifyFullTextIndexInPostsTranslationsFixTable extends Migration
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
    if (!array_key_exists("fullTitle", $indexesFound)) {
      \DB::statement("ALTER TABLE iblog__post_translations ADD FULLTEXT fullTitle(title)");
    }
    if (!array_key_exists("fullTitleDescription", $indexesFound)) {
      \DB::statement("ALTER TABLE iblog__post_translations ADD FULLTEXT fullTitleDescription(title,description)");
    }
    if (!array_key_exists("fullTitleSummary", $indexesFound)) {
      \DB::statement("ALTER TABLE iblog__post_translations ADD FULLTEXT fullTitleSummary(title,summary)");
    }
    if (!array_key_exists("fullDescription", $indexesFound)) {
      \DB::statement("ALTER TABLE iblog__post_translations ADD FULLTEXT fullDescription(description)");
    }
    if (!array_key_exists("fullDescriptionSummary", $indexesFound)) {
      \DB::statement("ALTER TABLE iblog__post_translations ADD FULLTEXT fullDescriptionSummary(description,summary)");
    }
    if (!array_key_exists("fullSummary", $indexesFound)) {
      \DB::statement("ALTER TABLE iblog__post_translations ADD FULLTEXT fullSummary(summary)");
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
