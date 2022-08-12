<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use DB;
use Carbon\Carbon;
use App\Http\Controllers\searchToolKit;

class Article extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'title',
        'body',
    ];

    //新規記事登録
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
            //紐付けられたタグをデータベースに登録するのに記事のidが必要なのでidだけを返す
            return $article->id;
        });
    }

    //記事更新
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

    //記事削除
    public static function deleteArticle($articleId)
    {
        // 論理削除
        DB::transaction(function () use($articleId){
            Article::where('id','=',$articleId)
            ->update(['deleted_at' => date(Carbon::now())]);
        });
    }

    //viewAricle用に指定された記事だけを取ってくる
    public static function serveArticle($articleId)
    {
        return Article::select('id','title','body')
        ->Where('id','=',$articleId)
        ->first();
    }

    public static function serveUserAllArticle($userId)
    {
        // まずはユーザーで絞った表を作る
        // whereで探すか副問合せで表を作るかどっちがよいか
        // 削除されていない記事を取って来る
        // 記事だからcategory = 2

        // whereの優先順位
        // 削除されてない､category=2,ログインユーザー = user_id

        $userTable = Article::select('id','title','body')
        -> WhereNull('deleted_at')
        -> where('user_id','=',$userId)
        -> orderBy('updated_at', 'desc')
        -> paginate(5);
        return $userTable;


        // return Article::select('id','title','body')
        // ->leftJoin('article_tags','articles.id','=', 'article_tags.article_id')
        // ->leftJoin('tags','article_tags.tag_id', '=' ,'tags.id')
        // ->get();
    }



    //検索する数
    public static function searchArticle($userId,$articleToSearch,$currentPage,$tagList,$searchTarget)
    {
        //一度にとってくる数
        $parPage = config('perPage');

        // %と_をエスケープ
        $escaped = searchToolKit::sqlEscape($articleToSearch);

        //and検索のために空白区切りでつくった配列を用意
        $wordListToSearch = searchToolKit::preparationToAndSearch($escaped);

        //タグも検索する場合
        if (!empty($tagList)) {
            //articleテーブルとarticle_tags,tagsを結合
            $subTable = DB::table('article_tags')
            ->select('articles.id','articles.title','articles.body','articles.updated_at')
            ->leftJoin('articles','articles.id','=','article_tags.article_id')
            ->leftJoin('tags','tags.id','=','article_tags.tag_id')
            ->where('articles.user_id','=',$userId)
            ->where(function($subTable) {
                //削除されてないものたちだけを取り出す
                $subTable->WhereNull('articles.deleted_at')
                         ->WhereNull('article_tags.deleted_at')
                         ->WhereNull('tags.deleted_at');
            })
            ->where(function($subTable) use($tagList) {
                foreach($tagList as $tag){
                    $subTable->orWhere('article_tags.tag_id','=',$tag);
                }
            });

            //ここの説明は別のドキュメントで
            $subTable->groupBy('articles.id')
            ->having(DB::raw('count(*)'), '=', count($tagList));

            //副問合せのテーブルから選択
            $query = DB::table($subTable,'sub')
            ->select('sub.id as id','sub.title as title','sub.body as body','sub.updated_at as updated_at');
        } else {
            //タグ検索が不要な場合
            $query = Article::select('id','title','body')
            ->where('user_id','=',$userId)
            ->whereNull('deleted_at');
        }

        // title名だけでlike検索する場合
        if ($searchTarget == "title") {
            foreach($wordListToSearch as $word){ $query->where('title','like',"%$word%"); }
        }

        // bodyだけでlike検索する場合
        if ($searchTarget == "body") {
            foreach($wordListToSearch as $word){ $query->where('body','like',"%$word%"); }
        }

        //ヒット件数取得
        $resultCount = $query->count();

        //ページ数計算
        $pageCount = (int)ceil($resultCount / $parPage);

        //何件目から取得するか
        $offset = $parPage*($currentPage-1);

        //ソート
        $sort = $query->orderBy('updated_at','desc');

        //検索
        $searchResults = $query->offset($offset)
        ->limit($parPage)
        ->get();

        return response()->json(
            [
                "articleList"  => $searchResults,
                "pageCount"    => $pageCount
            ],
            200
        );
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

        // 返り値がnull->削除されている
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
