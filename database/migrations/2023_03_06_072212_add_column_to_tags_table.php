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
        Schema::table('tags', function (Blueprint $table) {
            // 個数行を追加
            $table->unsignedBigInteger('count')->default(0)->after('user_id');
        });

        // 今までのタグの個数を集計する

        // article,bookmarkで使われているタグのid
        $artilceTags = DB::table('article_tags')
        ->select('tag_id')
        ->whereNotNull('tag_id');

        $bookMarkTags = DB::table('book_mark_tags')
        ->select('tag_id')
        ->whereNotNull('tag_id');

        // 合体
        $unioned = $bookMarkTags->unionAll($artilceTags)->toSql();

        // タグの数を数える
        $counted = DB::table(DB::raw('('.$unioned.') AS unioned'))
        ->select('unioned.tag_id',DB::raw('count(*) as count'))
        ->groupBy('unioned.tag_id');

        $tags = $counted->get();

        // データ移植
        foreach($tags as $tag){
            DB::table('tags')
            ->where('id','=',$tag->tag_id)
            ->update(['count' => $tag->count]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tags', function (Blueprint $table) {
            $table->dropColumn('count');
        });
    }
};
