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
        'title',
        'body',
    ];

    public static function storeArticle($title,$body,$userId)
    {
        // タイトルが産められてなかったら日時で埋める
        if ($title == '') { $title = Carbon::now() ;}

        return DB::transaction(function () use($title,$body,$userId){
            $article = Article::create([
                'user_id'  => $userId,
                'title'    => $title,
                'body'     => $body,
            ]);
            return $article->id;
        });
    }

    public static function updateArticle($articleId,$title,$body)
    {
        // タイトルが産められてなかったら日時で埋める
        if ($title == '') { $title = Carbon::now() ;}

        DB::transaction(function () use($articleId,$title,$body){
            Article::where('id','=',$articleId)
            ->update([
                'title' => $title,
                'body'  => $body,
            ]);
        });
    }

    //viewAricle用に指定された記事だけを取ってくる
    public static function serveArticle($articleId)
    {
        return Article::select('id','title','body','category')
        ->Where('id','=',$articleId)
        ->first();
    }

    public static function serveUserAllArticle($userId,$category)
    {
        // まずはユーザーで絞った表を作る
        // whereで探すか副問合せで表を作るかどっちがよいか
        // 削除されていない記事を取って来る
        // 記事だからcategory = 2

        // whereの優先順位
        // 削除されてない､category=2,ログインユーザー = user_id

        $userTable = Article::select('id','title','body')
        -> WhereNull('deleted_at')
        -> where('category','=',$category)
        -> where('user_id','=',$userId)
        -> orderBy('updated_at', 'desc')
        -> paginate(5);
        return $userTable;


        // return Article::select('id','title','body')
        // ->leftJoin('article_tags','articles.id','=', 'article_tags.article_id')
        // ->leftJoin('tags','article_tags.tag_id', '=' ,'tags.id')
        // ->get();
    }

    public static function deleteArticle($articleId)
    {
        // 論理削除
        DB::transaction(function () use($articleId){
            Article::where('id','=',$articleId)
            ->update(['deleted_at' => date(Carbon::now())]);
        });
    }

    // 削除済みか確かめる
    public static function checkArticleDeleted($articleId)
    {
        //削除されていないなら 記事のデータが帰ってくるはず
        //つまり帰り値がnullなら削除済みということ
        $article = Article::select('id')
        ->whereNull('deleted_at')
        ->where('id','=',$articleId)
        ->first();

        // 帰り値がnull->削除済みならtrue
        if ($article == null) {return true;}
        else {return false;}
    }

    //他人の覗こうとしてないか確かめる
    public static function preventPeep($articleId,$userId)
    {
        $article = Article::select('user_id')
        ->whereNull('deleted_at')
        ->where('id','=',$articleId)
        ->first();

        //記事に紐づけられているuserIdとログイン中のユーザーのidを比較する
        // falseなら他人のを覗こうとしている
        return ($article->original['user_id']) == $userId ;
    }
}
