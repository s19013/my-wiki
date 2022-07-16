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
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->interger('category',1);
            $table->text('title');
            $table->longText('body');
            $table->unsignedBigInteger('article_tag_id');
            $table->softDeletes();
            $table->timestamps();

            // 外部キー成約
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('article_tag_id')->references('id')->on('article_tags');

            //インデックス
            $table->index('user_id');
            $table->index('category');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('articles');
    }
};
