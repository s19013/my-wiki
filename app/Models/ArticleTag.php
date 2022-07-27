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
}
