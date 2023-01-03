<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use App\Models\ArticleTag;
use App\Models\BookMarkTag;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // deleted_atがnullじゃないデータを消す
        $deleteList = ArticleTag::select('*')->whereNotNull("deleted_at")->get();
        if (!empty($deleteList)) {
            foreach($deleteList as $data){
                // 一意なidがないから間違えて消さないようにできるだけ細かく指定
                // タイムスタンプなどはもしもなにかのひょうしにずれてしまうと余計なデータがのこってしまうので指定しない
                ArticleTag::where('article_id','=',$data->article_id)
                ->where('tag_id','=',$data->tag_id)
                ->whereNotNull("deleted_at")
                ->delete();
            }
        }

        $deleteList = BookMarkTag::select('*')->whereNotNull("deleted_at")->get();
        if (!empty($deleteList)) {
            foreach($deleteList as $data){
                BookMarkTag::where('book_mark_id','=',$data->book_mark_id)
                ->where('tag_id','=',$data->tag_id)
                ->whereNotNull("deleted_at")
                ->delete();
            }
        }


        // 物理削除に変更する
        Schema::table('article_tags', function (Blueprint $table) {
            $table->dropColumn('deleted_at');
        });

        Schema::table('book_mark_tags', function (Blueprint $table) {
            $table->dropColumn('deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('hard', function (Blueprint $table) {
            //
        });
    }
};
