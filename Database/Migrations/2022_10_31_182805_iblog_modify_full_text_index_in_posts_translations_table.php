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
      \DB::statement("ALTER TABLE iblog__post_translations ADD FULLTEXT fullTitle(title)");
      \DB::statement("ALTER TABLE iblog__post_translations ADD FULLTEXT fullTitleDescription(title,description)");
      \DB::statement("ALTER TABLE iblog__post_translations ADD FULLTEXT fullTitleSummary(title,summary)");
      \DB::statement("ALTER TABLE iblog__post_translations ADD FULLTEXT fullDescription(description)");
      \DB::statement("ALTER TABLE iblog__post_translations ADD FULLTEXT fullDescriptionSummary(description,summary)");
      \DB::statement("ALTER TABLE iblog__post_translations ADD FULLTEXT fullSummary(summary)");
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
