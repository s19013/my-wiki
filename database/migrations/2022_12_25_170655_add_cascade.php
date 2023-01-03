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
        // ユーザーが退会したら
        // そのユーザーが作ったブックマーク､記事､タグが消えるようにする
        Schema::table('book_marks', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->foreign('user_id')
            ->references('id')
            ->on('users')
            ->cascadeOnDelete();
        });

        Schema::table('tags', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->foreign('user_id')
            ->references('id')
            ->on('users')
            ->cascadeOnDelete();
        });

        Schema::table('articles', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->foreign('user_id')
            ->references('id')
            ->on('users')
            ->cascadeOnDelete();
        });

        // ブックマークが物理削除されたら
        // 中間テーブルからそのブックマークに関係があるデータを消す
        Schema::table('book_mark_tags', function (Blueprint $table) {
            $table->dropForeign(['book_mark_id']);
            $table->foreign('book_mark_id')
            ->references('id')
            ->on('book_marks')
            ->cascadeOnDelete();

            $table->dropForeign(['tag_id']);
            $table->foreign('tag_id')
            ->references('id')
            ->on('tags')
            ->cascadeOnDelete();
        });

        // 記事が物理削除されたら
        // 中間テーブルからその記事に関係があるデータを消す
        Schema::table('article_tags', function (Blueprint $table) {
            $table->dropForeign(['article_id']);
            $table->foreign('article_id')
            ->references('id')
            ->on('articles')
            ->cascadeOnDelete();

            $table->dropForeign(['tag_id']);
            $table->foreign('tag_id')
            ->references('id')
            ->on('tags')
            ->cascadeOnDelete();
        });
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
};
