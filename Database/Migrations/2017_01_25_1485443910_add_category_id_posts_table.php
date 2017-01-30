<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCategoryIdPostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table('iblog__posts', function (Blueprint $table) {

            $table->integer('category_id')->unsigned()->nullable();
            $table->foreign('category_id')->references('id')->on('iblog__categories')->onDelete('restrict');

            $table->dropForeign('iblog__posts_user_id_foreign');
            $table->foreign('user_id')->references('id')->on(config('auth.table', 'users'))->onDelete('restrict');

        });

        //Update category_id with first category in system
        $sql = 'UPDATE `iblog__posts` SET `category_id` = (SELECT category_id FROM `iblog__post__category` WHERE post_id = `iblog__posts`.id LIMIT 1)';
        DB::connection()->getPdo()->exec($sql);

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('iblog__posts', function (Blueprint $table) {

            $table->dropForeign('iblog__posts_category_id_foreign');
            $table->dropColumn('category_id');

        });
    }
}
