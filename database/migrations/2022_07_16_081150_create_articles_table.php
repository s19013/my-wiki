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
            // $table->tinyInteger('category')->comment('リンク:1 記事:2');
            $table->string('title');
            $table->longText('body');
            // $table->unsignedBigInteger('article_tag_id');
            $table->softDeletes();
            $table->timestamps();

            //インデックス
            $table->index('user_id');
            // $table->index('category');
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
