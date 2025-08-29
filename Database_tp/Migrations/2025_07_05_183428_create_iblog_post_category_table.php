<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  /**
   * Run the migrations.
   */
  public function up(): void
  {
    Schema::create('iblog__post_category', function (Blueprint $table) {
      $table->id();
      $table->integer('post_id')->unsigned();
      $table->foreign('post_id')->references('id')->on('iblog__posts')->onDelete('cascade');
      $table->integer('category_id')->unsigned();
      $table->foreign('category_id')->references('id')->on('iblog__categories')->onDelete('cascade');
      $table->index(['post_id', 'category_id']);

      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('iblog__post_category');
  }
};
