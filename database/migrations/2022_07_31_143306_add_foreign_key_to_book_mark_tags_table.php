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
        Schema::table('book_mark_tags', function (Blueprint $table) {
            // 外部キー成約
            $table->foreign('book_mark_id')->references('id')->on('book_marks');
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
        Schema::table('book_mark_tags', function (Blueprint $table) {
            // 外部キー削除
            $table->dropForeign('book_mark_tags_user_id_foreign');
            $table->dropForeign('book_mark_tags_tag_id_foreign');
        });
    }
};
