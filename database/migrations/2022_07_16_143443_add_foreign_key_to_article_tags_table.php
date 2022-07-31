<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('article_tags', function (Blueprint $table) {
            // 外部キー成約
            $table->foreign('article_id')->references('id')->on('articles');
            // $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('tag_id')->references('id')->on('tags');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('article_tags', function (Blueprint $table) {
            // 外部キー削除
            $table->dropForeign('article_tags_user_id_foreign');
            $table->dropForeign('article_tags_tag_id_foreign');
        });
    }
};
