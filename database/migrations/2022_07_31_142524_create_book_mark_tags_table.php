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
        Schema::create('book_mark_tags', function (Blueprint $table) {
            $table->unsignedBigInteger('book_mark_id');
            $table->unsignedBigInteger('tag_id')->nullable()->default(null);
            $table->softDeletes();
            $table->timestamps();

            //インデックス
            $table->index('book_mark_id');
            $table->index('tag_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('book_mark_tags');
    }
};
