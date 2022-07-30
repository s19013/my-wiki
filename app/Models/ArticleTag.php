<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use DB;

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


    //記事に関連付けられたタグを取得
    public static function serveTagsRelatedToAricle($articleId,$userId)
    {
        // tagsターブルとくっつける
        // article_tags.tag_id = tags.id
        // 記事からはずされていないタグを取得

        // タグテーブルからログインユーザーの削除されていないタグを探す
        $subTagTable = DB::table('tags')
        ->select('id','name')
        ->where('user_id','=',':$userId')
        ->WhereNull('deleted_at')
        ->toSql();

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
