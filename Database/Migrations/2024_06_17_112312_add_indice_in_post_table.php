<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndiceInPostTable extends Migration
{
  /**
   * Run the migrations.
   */
  public function up(): void
  {
    Schema::table('iblog__posts', function (Blueprint $table) {
      $table->index(['date_available']);
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    //
  }
}
