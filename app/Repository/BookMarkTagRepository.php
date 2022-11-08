<?php
namespace App\Repository;

use DB;
use Carbon\Carbon;

use App\Models\BookMarkTag;

class BookMarkTagRepository
{
    //ブックマークに紐付けらたタグを登録
    public  function store($tagId,$bookMarkId)
    {
        DB::transaction(function () use($tagId,$bookMarkId){
            BookMarkTag::create([
                'book_mark_id' => $bookMarkId,
                'tag_id'     => $tagId,
            ]);
        });
    }

    //ブックマークからはずされたタグを削除
    public  function delete($tagId,$bookMarkId)
    {
        DB::transaction(function () use($tagId,$bookMarkId){
            BookMarkTag::where('book_mark_id','=',$bookMarkId)
            ->where('tag_id','=',$tagId)
            ->update(['deleted_at' => date(Carbon::now())]);
        });
    }

    //ブックマークに紐付けられているタグを更新
    public  function update($bookMarkId,$updatedTagList)
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
                $this->store(
                    tagId:$tag,
                    bookMarkId:$bookMarkId,
                );
            }
        }

        //削除
        if (!empty($deletedTagList)) {
            foreach($deletedTagList as $tag) {
                $this->delete(
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
    public  function getOrignalTag($bookMarkId){
        $temp = [];

        $original = BookMarkTag::select('tag_id')
        ->where('book_mark_id','=',$bookMarkId)
        ->whereNull('deleted_at')
        ->get();

        //元のデータに紐付けられているタグを配列に入れる
        foreach ($original as $tag){array_push($temp,$tag->tag_id);}

        return $temp;
    }

    //元のブックマークにタグがついてない場合の処理
    public  function procesOriginalBookMarkDoesNotHaveAnyTags($originalTagList,$bookMarkId,$updatedTagList)
    {
        // 仕様としてタグをつけてない場合はtag_idにnullが入る<-超大事
        if (is_null($originalTagList[0])) {
            //元のブックマークにタグはついてないし､新しくタグも設定されていない場合
            // この関数の処理を終わらせる
            if (is_null($updatedTagList[0])) {return true;}
            else {
                // 更新前はブックマークにタグが1つもついていなくて
                // 更新後にはタグが紐付けられていたら
                // 更新前の$bookMarkIdのtag_idがnullのデータを論理削除
                $this->delete(
                    tagId:null,
                    bookMarkId:$bookMarkId,
                );
            }
        }
    }

    //ブックマークのタグをすべて消した時の処理
    public  function procesOriginalBookMarkDeleteAllTags($originalTagList,$bookMarkId,$isAddedTagListEmpty,$deletedTagList)
    {
        // 紐付けられていたタグすべて削除されたのならtag_id = nullのデータをついか
        // もともとブックマークにタグがついていたかと,
        // 新しく紐付けられたタグが1つもないことを確認
        if ($originalTagList[0] != null && $isAddedTagListEmpty==true) {
            //もともとついていたタグがすべてはずされたか確認
            $isAllDeleted = array_diff($originalTagList,$deletedTagList);
            if (empty($isAllDeleted)) {
                $this->store(
                    tagId:null,
                    bookMarkId:$bookMarkId,
                );
            }
        }
    }

    //ブックマークに関連付けられたタグを取得
    public  function serveTagsRelatedToBookMark($bookMarkId,$userId)
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
