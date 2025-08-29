<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('iblog__posts', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->json('options')->nullable();
            $table->boolean('featured')->default(false);
            $table->date('date_available')->nullable();
            $table->integer('category_id')->unsigned()->nullable();
            $table->foreign('category_id')->references('id')->on('iblog__categories')->onDelete('restrict');

            // Audit fields
            $table->timestamps();
            $table->auditStamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('iblog__posts');
    }
};
