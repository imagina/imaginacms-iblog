<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndiceInPostTranslationsTable extends Migration
{
  /**
   * Run the migrations.
   */
  public function up(): void
  {
    Schema::create('iblog__post__category', function (Blueprint $table) {
      $table->index(['status']);
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    //
  }
};