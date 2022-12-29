<?php
namespace App\Repository;

use DB;
use Carbon\Carbon;
use App\Models\ArticleTag;

class ArticleTagRepository
{
    //記事に紐付けらたタグを登録
    public  function store($tagId,$articleId)
    {
        ArticleTag::create([
            'article_id' => $articleId,
            'tag_id'     => $tagId,
        ]);
    }

    //記事からはずされたタグを削除
    public  function delete($tagId,$articleId)
    {
        ArticleTag::where('article_id','=',$articleId)
            ->where('tag_id','=',$tagId)
            ->delete();
    }

    //記事に紐付けられているタグを更新
    public  function update($articleId,$updatedTagList)
    {
        // 更新前のブックマークに紐付けられていたタグを取得
        $originalTagList = $this->getOrignalTag($articleId);

        // 更新前のブックマークにタグが1つでもついていたかいなかったかで処理を分ける
        if (empty($originalTagList)) {$this->ProcessingifOriginalHasNoTags($articleId,$updatedTagList,$originalTagList);}
        else {$this->ProcessingifOriginalHasAnyTags($articleId,$originalTagList,$updatedTagList);}
    }

    // タグが1つもついてなかった
    public function ProcessingifOriginalHasNoTags($articleId,$updatedTagList,$originalTagList)
    {
        // 追加されたタグ
        $addedTagList = array_diff($updatedTagList, $originalTagList);

        // なにか新しくタグが紐づけられていた場合
        if (!empty($addedTagList)) {
            // 追加
            foreach($addedTagList as $tag) {
                $this->store(
                    tagId:$tag,
                    articleId:$articleId,
                );
            }

            // nullの削除
            $this->delete(
                tagId:null,
                articleId:$articleId,
            );
        }
    }

    // タグがついていた
    public function ProcessingifOriginalHasAnyTags($articleId,$originalTagList,$updatedTagList)
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
                    articleId:$articleId,
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
                    articleId:$articleId,
                );
            }
        } else {
            // 新しく追加されたタグがある場合
            foreach($addedTagList as $tag) {
                $this->store(
                    tagId:$tag,
                    articleId:$articleId,
                );
            }
        }
    }

    // 更新前の記事に紐付けられていたタグを取得
    public  function getOrignalTag($articleId){
        $temp = [];

        $original = ArticleTag::select('tag_id')
        ->where('article_id','=',$articleId)
        ->get();

        //元のデータに紐付けられているタグを配列に入れる
        foreach ($original as $tag){array_push($temp,$tag->tag_id);}

        return $temp;
    }

    //記事に関連付けられたタグの名前とidを取得
    public  function serveTagsRelatedToArticle($articleId,$userId)
    {
        // 記事についているタグidの名前などをとってくる
        $relatingTagList = DB::table('article_tags')
        ->select('tag_id')
        ->where('article_id','=',$articleId)
        ->get();

        // [[tag_id => 1],[tag_id => 2],...]みたいな形になるので
        // whereInで使うために[1,2,...]みたいな形にする
        $convertedRelatingTagList = $this->convertAssociativeArrayToSimpleArray($relatingTagList);

        // 何もタグがついてなかったらから配列を返す
        if (is_null($convertedRelatingTagList[0])) {return [];}

        // tagテーブルからタグの名前とIdを取ってくる
        $tagList = DB::table('tags')
        ->select('id','name')
        ->whereIn('id',$convertedRelatingTagList)
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
