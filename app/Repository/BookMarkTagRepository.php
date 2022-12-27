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
        BookMarkTag::create([
            'book_mark_id' => $bookMarkId,
            'tag_id'     => $tagId,
        ]);
    }

    //ブックマークからはずされたタグを削除
    public  function delete($tagId,$bookMarkId)
    {
        BookMarkTag::where('book_mark_id','=',$bookMarkId)
            ->where('tag_id','=',$tagId)
            ->delete();
    }

    //ブックマークに紐付けられているタグを更新
    public  function update($bookMarkId,$updatedTagList)
    {
        // 更新前のブックマークに紐付けられていたタグを取得
        $originalTagList = $this->getOrignalTag($bookMarkId);

        // 更新前のブックマークにタグが1つでもついていたかいなかったかで処理を分ける
        if (empty($originalTagList)) {$this->ProcessingifOriginalHasNoTags($bookMarkId,$updatedTagList,$originalTagList);}
        else {$this->ProcessingifOriginalHasAnyTags($bookMarkId,$originalTagList,$updatedTagList);}
    }

    // タグが1つもついてなかった
    public function ProcessingifOriginalHasNoTags($bookMarkId,$updatedTagList,$originalTagList)
    {
        // 追加されたタグ
        $addedTagList = array_diff($updatedTagList, $originalTagList);

        // なにか新しくタグが紐づけられていた場合
        if (!empty($addedTagList)) {
            // 追加
            foreach($addedTagList as $tag) {
                $this->store(
                    tagId:$tag,
                    bookMarkId:$bookMarkId,
                );
            }

            // nullの削除
            $this->delete(
                tagId:null,
                bookMarkId:$bookMarkId,
            );
        }
    }

    // タグがついていた
    public function ProcessingifOriginalHasAnyTags($bookMarkId,$originalTagList,$updatedTagList)
    {
        // 追加されたタグ
        $addedTagList = array_diff($updatedTagList, $originalTagList);

        // 削除されたタグ
        $deletedTagList = array_diff($originalTagList, $updatedTagList);

        //削除
        if (!empty($deletedTagList)) {
            foreach($deletedTagList as $tag) {
                $this->delete(
                    tagId:$tag,
                    bookMarkId:$bookMarkId,
                );
            }
        }

        // ブックマークのタグをすべて消した時の処理
        // 新しく追加されたタグがない場合
        if (empty($addedTagList)) {
            //もともとついていたタグがすべてはずされたか確認
            $isAllDeleted = array_diff($originalTagList,$deletedTagList);

            if (empty($isAllDeleted)) {
                // 紐付けられていたタグすべて削除されたのならtag_id = nullのデータをついか
                $this->store(
                    tagId:null,
                    bookMarkId:$bookMarkId,
                );
            }
        } else {
            // 新しく追加されたタグがある場合
            foreach($addedTagList as $tag) {
                $this->store(
                    tagId:$tag,
                    bookMarkId:$bookMarkId,
                );
            }
        }
    }

    // ブックマークに紐付けらているタグを取得
    public  function getOrignalTag($bookMarkId){
        // ここDB::にして
        // convertAssociativeArrayToSimpleArrayを使った方がよいかも
        $original = DB::table('book_mark_tags')
        ->select('tag_id')
        ->where('book_mark_id','=',$bookMarkId)
        ->get();

        $convertedOriginal = $this->convertAssociativeArrayToSimpleArray($original);
        if (is_null($convertedOriginal[0])) {return [];}
        else {return $convertedOriginal;}

        // これで[null]が返されなくなったけど
        // ProcessingifOriginalHasNoTagsの方で色々やっているから問題ないかな?
    }

    //記事に関連付けられたタグの名前とidを取得
    public  function serveTagsRelatedToBookMark($bookMarkId,$userId)
    {
        // 記事についているタグidの名前などをとってくる
        $relatingTagList = $this->getOrignalTag($bookMarkId);

        // tagテーブルからタグの名前とIdを取ってくる
        $tagList = DB::table('tags')
        ->select('id','name')
        ->whereIn('id',$relatingTagList)
        ->orderBy('name')
        ->get();

        return $tagList;
    }

    // [[tag_id => 1],[tag_id => 2],...]みたいな形になるので
    // whereInで使うために[1,2,...]みたいな形にする
    public function convertAssociativeArrayToSimpleArray($array)
    {
        $temp = [];
        foreach($array as $element){array_push($temp,$element->tag_id);}
        return $temp;
    }
}
