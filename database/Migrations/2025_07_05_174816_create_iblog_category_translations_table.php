<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('iblog__category_translations', function (Blueprint $table) {
      $table->engine = 'InnoDB';
      $table->increments('id');
      $table->text('title');
      $table->string('slug')->index();
      $table->integer('status')->default(0)->unsigned();
      $table->text('description');
      $table->text('meta_title')->nullable();
      $table->text('meta_description')->nullable();
      $table->text('meta_keywords')->nullable();
      $table->json('translatable_options')->nullable();
      $table->unique(['slug', 'locale'])->change();

      $table->integer('category_id')->unsigned();
      $table->string('locale')->index();
      $table->unique(['category_id', 'locale']);
      $table->foreign('category_id')->references('id')->on('iblog__categories')->onDelete('cascade');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::table('iblog__category_translations', function (Blueprint $table) {
      $table->dropForeign(['category_id']);
    });
    Schema::dropIfExists('iblog__category_translations');
  }
};
