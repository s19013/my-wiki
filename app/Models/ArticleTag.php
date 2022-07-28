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

    public static function serveAricleTag($articleId)
    {
        // tagsターブルとくっつける
        // article_tags.tag_id = tags.id
        // タグ自体消されていないタグと記事からはずされていないタグを取得
        return ArticleTag::select('tags.name')
        ->leftJoin('tags','article_tags.tag_id','=','tags.id')
        ->WhereNull('tags.deleted_at')
        ->WhereNull('article_tags.deleted_at')
        ->Where('article_tags.article_id','=',$articleId)
        ->get();
    }
}
