<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;
use Carbon\Carbon;

class Article extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'category',
        'title',
        'body',
    ];

    public static function storeArticle($title,$body,$userId,$category)
    {
        // タイトルが産められてなかったら日時で埋める
        if ($title == '') { $title = Carbon::now() ;}

        return DB::transaction(function () use($title,$body,$userId,$category){
            $article = Article::create([
                'user_id'  => $userId,
                'title'    => $title,
                'body'     => $body,
                'category' => $category]);
            return $article->id;
        });
    }

    //viewAricle用に指定された記事だけを取ってくる
    public static function serveArticle($articleId)
    {
        return Article::select('title','body')
        ->Where('id','=',$articleId)
        ->get();
    }

    public static function serveUserAllArticle($userId)
    {
        // まずはユーザーで絞った表を作る
        // whereで探すか副問合せで表を作るかどっちがよいか
        // 削除されていない記事を取って来る
        // 記事だからcategory = 2

        $userTable = Article::select('id','title','body')
        -> where('user_id','=',$userId)
        -> where('category','=',2)
        ->get();
        return $userTable;


        // return Article::select('id','title','body')
        // ->leftJoin('article_tags','articles.id','=', 'article_tags.article_id')
        // ->leftJoin('tags','article_tags.tag_id', '=' ,'tags.id')
        // ->get();
    }
}
