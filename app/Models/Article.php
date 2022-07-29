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
        return Article::select('id','title','body')
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
        -> WhereNull('deleted_at')
        ->get();
        return $userTable;


        // return Article::select('id','title','body')
        // ->leftJoin('article_tags','articles.id','=', 'article_tags.article_id')
        // ->leftJoin('tags','article_tags.tag_id', '=' ,'tags.id')
        // ->get();
    }

    public static function deleteArticle($articleId)
    {
        // 論理削除
        Article::where('id','=',$articleId)
        ->update(['deleted_at' => date(Carbon::now())]);
    }

    // 削除済みか確かめる
    public static function checkArticleDeleted($articleId)
    {
        //削除されていないなら 記事のデータが帰ってくるはず
        //つまり帰り値がnullなら削除済みということ
        return Article::select('id')
        ->whereNull('deleted_at')
        ->where('id','=',$articleId)
        ->first();
    }

    //他人の覗こうとしてないか確かめる
    public static function illegalPeep($articleId,$userId)
    {
        $peep = Article::select('user_id')
        ->whereNull('deleted_at')
        ->where('id','=',$articleId)
        ->first();

        //記事に紐づけられているuserIdとログイン中のユーザーのidを比較する
        // falseなら他人のを覗こうとしている
        return ($peep->original['user_id']) == $userId ;
    }
}
