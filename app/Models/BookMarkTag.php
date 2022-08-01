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

    public static function storeBookMarkTag($tagId,$bookmarkId)
    {
        DB::transaction(function () use($tagId,$bookmarkId){
            BookMarkTag::create([
                'book_mark_id' => $bookmarkId,
                'tag_id'     => $tagId,
            ]);
        });
    }

    public static function deleteBookMarkTag($tagId,$bookmarkId)
    {
        DB::transaction(function () use($tagId,$bookmarkId){
            BookMarkTag::where('book_mark_id','=',$bookmarkId)
            ->where('tag_id','=',$tagId)
            ->update(['deleted_at' => date(Carbon::now())]);
        });
    }

    public static function updateAricleTag($bookmarkId,$updatedTagList)
    {

        // 消された､追加されたを確認する
        $originalTagList = [];

        // もとのタグを確認する
        $original = BookMarkTag::select('tag_id')
        ->where('book_mark_id','=',$bookmarkId)
        ->get();

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
                    bookmarkId:$bookmarkId,
                    userId:\Auth::id()
                );
            }
        }

        //削除
        if (!empty($deletedTagList)) {
            foreach($deletedTagList as $tag) {
                BookMarkTag::deleteBookMarkTag(
                    tagId:$tag,
                    bookmarkId:$bookmarkId,
                );
            }
        }
    }

    //記事に関連付けられたタグを取得
    public static function serveTagsRelatedToAricle($bookmarkId,$userId)
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
        ->Where('book_mark_tags.book_markid','=',':$bookmarkId')
        ->setBindings([
            ':$userId'   => $userId,
            ':$bookmarkId'=> $bookmarkId
        ])
        ->get();
    }
}
