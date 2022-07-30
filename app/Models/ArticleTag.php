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
        'user_id',
        'tag_id',
    ];

    public static function storeArticleTag($tagId,$articleId,$userId)
    {
        DB::transaction(function () use($tagId,$articleId,$userId){
            ArticleTag::create([
                'article_id' => $articleId,
                'user_id'    => $userId,
                'tag_id'     => $tagId,
            ]);
        });
    }

    public static function deleteArticleTag($tagId,$articleId)
    {
        DB::transaction(function () use($tagId,$articleId){
            ArticleTag::where('article_id','=',$articleId)
            ->where('tag_id','=',$tagId)
            ->update(['deleted_at' => date(Carbon::now())]);
        });
    }

    public static function updateAricleTag($articleId,$updatedTagList)
    {

        // 消された､追加されたを確認する
        $originalTagList = [];

        // もとのタグを確認する
        $original = ArticleTag::select('tag_id')
        ->where('article_id','=',$articleId)
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
                ArticleTag::storeArticleTag(
                    tagId:$tag,
                    articleId:$articleId,
                    userId:\Auth::id()
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
    }

    //記事に関連付けられたタグを取得
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
        ->setBindings([
            ':$userId'   => $userId,
            ':$articleId'=> $articleId
        ])
        ->get();
    }
}
