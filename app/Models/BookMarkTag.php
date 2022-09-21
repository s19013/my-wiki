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
        $addedTagList    = [];
        $deletedTagList  = [];

        // 更新前のブックマークに紐付けられていたタグを取得
        $originalTagList = self::getOrignalTag($bookMarkId);

        //元のブックマークにタグはついてないし､新しくタグも設定されていない場合
        // この関数の処理を終わらせる
        if(self::procesOriginalBookMarkDoesNotHaveAnyTags($originalTagList,$bookMarkId,$updatedTagList) == true) {return;}

        // 追加されたタグ
        $addedTagList = array_diff($updatedTagList, $originalTagList);

        // 削除されたタグ
        // 元データがからではないときのみ動かす
        if ($originalTagList[0] != null) {
            $deletedTagList = array_diff($originalTagList, $updatedTagList);
        }

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

        //ブックマークのタグをすべて消した時の処理
        self::procesOriginalBookMarkDeleteAllTags(
            originalTagList:$originalTagList,
            deletedTagList :$deletedTagList,
            isAddedTagListEmpty:empty($addedTagList),
            bookMarkId:$bookMarkId,
            );
    }

    // 更新前のブックマークに紐付けられていたタグを取得
    public static function getOrignalTag($bookMarkId){
        $temp = [];

        $original = BookMarkTag::select('tag_id')
        ->where('book_mark_id','=',$bookMarkId)
        ->whereNull('deleted_at')
        ->get();

        //元のデータに紐付けられているタグを配列に入れる
        foreach ($original as $tag){array_push($temp,$tag->original["tag_id"]);}

        return $temp;
    }

    //元のブックマークにタグがついてない場合の処理
    public static function procesOriginalBookMarkDoesNotHaveAnyTags($originalTagList,$bookMarkId,$updatedTagList)
    {
        if (is_null($originalTagList[0])) {
            //元のブックマークにタグはついてないし､新しくタグも設定されていない場合
            // この関数の処理を終わらせる
            if (empty($updatedTagList)) {return true;}
            else {
                // 更新前はブックマークにタグが1つもついていなくて
                // 更新後にはタグが紐付けられていたら
                // 更新前の$bookMarkIdのtag_idがnullのデータを論理削除
                BookMarkTag::deleteBookMarkTag(
                    tagId:null,
                    bookMarkId:$bookMarkId,
                );
                return false;
            }
        }
    }

    //ブックマークのタグをすべて消した時の処理
    public static function procesOriginalBookMarkDeleteAllTags($originalTagList,$bookMarkId,$isAddedTagListEmpty,$deletedTagList)
    {
        // 紐付けられていたタグすべて削除されたのならtag_id = nullのデータをついか
        // もともとブックマークにタグがついていたかと,
        // 新しく紐付けられたタグが1つもないことを確認
        if ($originalTagList[0] != null && $isAddedTagListEmpty==true) {
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

    //ブックマークに関連付けられたタグを取得
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

        // ブックマークからはずされていないタグを取得
        return BookMarkTag::select('sub_tags.id as id','sub_tags.name as name')
        ->leftJoin(DB::raw('('.$subTagTable.') AS sub_tags'),'book_mark_tags.tag_id','=','sub_tags.id')
        ->WhereNull('book_mark_tags.deleted_at') // ブックマークからはずされていないタグのみを取得
        ->Where('book_mark_tags.book_mark_id','=',':$bookMarkId')
        ->orderBy('name')
        ->setBindings([
            ':$userId'   => $userId,
            ':$bookMarkId'=> $bookMarkId
        ])
        ->get();
    }
}
