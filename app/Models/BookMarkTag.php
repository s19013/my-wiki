<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use DB;
use Carbon\Carbon;

class BookMarkTag extends Model
{
    use HasFactory;
    protected $fillable = [
        'book_mark_id',
        'tag_id',
    ];

    //ブックマークに紐付けらたタグを登録
    public static function storeBookMarkTag($tagId,$bookMarkId)
    {
        DB::transaction(function () use($tagId,$bookMarkId){
            BookMarkTag::create([
                'book_mark_id' => $bookMarkId,
                'tag_id'     => $tagId,
            ]);
        });
    }

    //ブックマークからはずされたタグを削除
    public static function deleteBookMarkTag($tagId,$bookMarkId)
    {
        DB::transaction(function () use($tagId,$bookMarkId){
            BookMarkTag::where('book_mark_id','=',$bookMarkId)
            ->where('tag_id','=',$tagId)
            ->update(['deleted_at' => date(Carbon::now())]);
        });
    }

    //ブックマークに紐付けられているタグを更新
    public static function updateBookMarkTag($bookMarkId,$updatedTagList)
    {
        $originalTagList = []; //元データに紐付けられていたタグを入れるリスト

        // もとのタグを確認する
        $original = BookMarkTag::select('tag_id')
        ->where('book_mark_id','=',$bookMarkId)
        ->get();

        // 元のタグが1つもついていなくて､新しくタグをつけようとしていたら
        // アプデ前の$book_mark_Idのtag_idがnullのデータを論理削除
        if ($original[0]->original["tag_id"] == null && !empty($updatedTagList) ) {
            BookMarkTag::deleteBookMarkTag(
                tagId:null,
                bookMarkId:$bookMarkId,
            );
        }

        foreach ($original as $tag){
            array_push($originalTagList,$tag->original["tag_id"]);
        }

        // 追加されたタグ
        $addedTagList = array_diff($updatedTagList, $originalTagList);

        // 削除されたタグ
        $deletedTagList = array_diff($originalTagList, $updatedTagList);

        //追加
        if (!empty($addedTagList)) {
            foreach($addedTagList as $tag) {
                BookMarkTag::storeBookMarkTag(
                    tagId:$tag,
                    bookMarkId:$bookMarkId,
                );
            }
        }

        //削除
        if (!empty($deletedTagList)) {
            foreach($deletedTagList as $tag) {
                BookMarkTag::deleteBookMarkTag(
                    tagId:$tag,
                    bookMarkId:$bookMarkId,
                );
            }
        }

        // 紐付けられていたタグすべて削除されたのならtag_id = nullのデータをついか
        // もともとブックマークにタグがついていたか確認
        if ($original[0]->original["tag_id"] != null) {
            //もともとついていたタグがすべてはずされたか確認
            $isAllDeleted = array_diff($originalTagList,$deletedTagList);
            if (empty($isAllDeleted)) {
                BookMarkTag::storeBookMarkTag(
                    tagId:null,
                    bookMarkId:$bookMarkId,
                );
            }
        }
    }

    //記事に関連付けられたタグを取得
    public static function serveTagsRelatedToAricle($bookMarkId,$userId)
    {
        // tagsターブルとくっつける
        // book_mark_tags.tag_id = tags.id

        // タグテーブルからログインユーザーの削除されていないタグを探す
        $subTagTable = DB::table('tags')
        ->select('id','name')
        ->where('user_id','=',':$userId')
        ->WhereNull('deleted_at')
        ->toSql();

        // 記事からはずされていないタグを取得
        return BookMarkTag::select('sub_tags.id as id','sub_tags.name as name')
        ->leftJoin(DB::raw('('.$subTagTable.') AS sub_tags'),'book_mark_tags.tag_id','=','sub_tags.id')
        ->WhereNull('book_mark_tags.deleted_at') // 記事からはずされていないタグのみを取得
        ->Where('book_mark_tags.book_mark_id','=',':$bookMarkId')
        ->orderBy('name')
        ->setBindings([
            ':$userId'   => $userId,
            ':$bookMarkId'=> $bookMarkId
        ])
        ->get();
    }
}
