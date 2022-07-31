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
        Schema::table('articles', function (Blueprint $table) {
            // 外部キー成約
            $table->foreign('user_id')->references('id')->on('users');
            // $table->foreign('article_tag_id')->references('id')->on('article_tags');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('articles', function (Blueprint $table) {
            // 外部キー削除
            $table->dropForeign('articles_user_id_foreign');
            // $table->dropForeign('articles_article_tag_id_foreign');
        });
    }
};
