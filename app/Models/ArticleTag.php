<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use DB;
use Carbon\Carbon;

class ArticleTag extends Model
{
    use HasFactory;
    protected $fillable = [
        'article_id',
        'tag_id',
    ];

    //記事に紐付けらたタグを登録
    public static function storeArticleTag($tagId,$articleId)
    {
        DB::transaction(function () use($tagId,$articleId){
            ArticleTag::create([
                'article_id' => $articleId,
                'tag_id'     => $tagId,
            ]);
        });
    }

    //記事からはずされたタグを削除
    public static function deleteArticleTag($tagId,$articleId)
    {
        DB::transaction(function () use($tagId,$articleId){
            ArticleTag::where('article_id','=',$articleId)
            ->where('tag_id','=',$tagId)
            ->update(['deleted_at' => date(Carbon::now())]);
        });
    }

    //記事に紐付けられているタグを更新
    public static function updateAricleTag($articleId,$updatedTagList)
    {
        $originalTagList = []; //元データに紐付けられていたタグ
        $addedTagList    = [];
        $deletedTagList  = [];

        // 更新前の記事に紐付けられていたタグを取得
        $originalTagList = self::getOrignalTag($articleId);


        //元の記事にタグはついてないし､新しくタグも設定されていない場合
        // この関数の処理を終わらせる
        if(self::procesOriginalArticleDoesNotHaveAnyTags($originalTagList,$articleId,$updatedTagList) == true) {return;}


        // 追加されたタグ
        $addedTagList = array_diff($updatedTagList, $originalTagList);

        // 削除されたタグ
        // 元データがからではないときのみ動かす
        if ($originalTagList[0] != null) {$deletedTagList = array_diff($originalTagList, $updatedTagList);}


        //追加
        if (!empty($addedTagList)) {
            foreach($addedTagList as $tag) {
                ArticleTag::storeArticleTag(
                    tagId:$tag,
                    articleId:$articleId,
                );
            }
        }

        //削除
        if (!empty($deletedTagList)) {
            foreach($deletedTagList as $tag) {
                ArticleTag::deleteArticleTag(
                    tagId:$tag,
                    articleId:$articleId,
                );
            }
        }

        //記事のタグをすべて消した時の処理
        self::procesOriginalArticleDeleteAllTags(
            originalTagList:$originalTagList,
            deletedTagList :$deletedTagList,
            isAddedTagListEmpty:empty($addedTagList),
            articleId:$articleId,
            );

    }

    // 更新前の記事に紐付けられていたタグを取得
    public static function getOrignalTag($articleId){
        $temp = [];

        $original = ArticleTag::select('tag_id')
        ->where('article_id','=',$articleId)
        ->whereNull('deleted_at')
        ->get();

        //元のデータに紐付けられているタグを配列に入れる
        foreach ($original as $tag){array_push($temp,$tag->original["tag_id"]);}

        return $temp;
    }

    //元の記事にタグがついてない場合の処理
    public static function procesOriginalArticleDoesNotHaveAnyTags($originalTagList,$articleId,$updatedTagList)
    {
        if (is_null($originalTagList[0])) {
            //元の記事にタグはついてないし､新しくタグも設定されていない場合
            // この関数の処理を終わらせる
            if (empty($updatedTagList)) {return true;}
            else {
                // 更新前は記事にタグが1つもついていなくて
                // 更新後にはタグが紐付けられていたら
                // 更新前の$articleIdのtag_idがnullのデータを論理削除
                ArticleTag::deleteArticleTag(
                    tagId:null,
                    articleId:$articleId,
                );
            }
        }
    }

    //記事のタグをすべて消した時の処理
    public static function procesOriginalArticleDeleteAllTags($originalTagList,$articleId,$isAddedTagListEmpty,$deletedTagList)
    {
        // もともと記事にタグがついていたかと,
        // 新しく紐付けられたタグが1つもないことを確認
        if ($originalTagList[0] != null && $isAddedTagListEmpty==true) {
            //もともとついていたタグがすべてはずされたか確認
            $isAllDeleted = array_diff($originalTagList,$deletedTagList);
            // 紐付けられていたタグすべて削除されたのならtag_id = nullのデータをついか
            if (empty($isAllDeleted)) {
                ArticleTag::storeArticleTag(
                    tagId:null,
                    articleId:$articleId,
                );
            }
        }
    }


    //記事に関連付けられたタグの名前とidを取得
    public static function serveTagsRelatedToAricle($articleId,$userId)
    {
        // tagsターブルとくっつける
        // article_tags.tag_id = tags.id

        // タグテーブルからログインユーザーの削除されていないタグを探す
        $subTagTable = DB::table('tags')
        ->select('id','name')
        ->where('user_id','=',':$userId')
        ->WhereNull('deleted_at')
        ->toSql();

        // 記事からはずされていないタグを取得
        return ArticleTag::select('sub_tags.id as id','sub_tags.name as name')
        ->leftJoin(DB::raw('('.$subTagTable.') AS sub_tags'),'article_tags.tag_id','=','sub_tags.id')
        ->WhereNull('article_tags.deleted_at') // 記事からはずされていないタグのみを取得
        ->Where('article_tags.article_id','=',':$articleId')
        ->orderBy('name')
        ->setBindings([
            ':$userId'   => $userId,
            ':$articleId'=> $articleId
        ])
        ->get();
    }


}
