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
            // ->update(['deleted_at' => date(Carbon::now())]);
            ->update(['deleted_at' => Carbon::now()]);
        });
    }

    //指定された記事だけを取ってくる
    //閲覧画面,編集画面で使う
    public static function serveArticle($articleId)
    {
        return Article::select('id','title','body')
        ->Where('id','=',$articleId)
        ->first();
    }

    //検索する数
    public static function searchArticle($userId,$articleToSearch,$currentPage,$tagList,$searchTarget)
    {
        //ページネーションをする

        //一度にとってくる数
        $parPage = (int)config('app.parPage');

        // %と_をエスケープ
        $escaped = searchToolKit::sqlEscape($articleToSearch);

        //and検索のために空白区切りでつくった配列を用意
        $wordListToSearch = searchToolKit::preparationToAndSearch($escaped);

        //タグも検索する場合
        if (!empty($tagList)) {
            $subTable = self::createSubTableForSearch($userId,$tagList);

            //副問合せのテーブルから選択
            $query = DB::table($subTable,'sub')
            ->select('*');
        } else {
            //タグ検索が不要な場合
            $query = Article::select('*')
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

    //検索時のサブテーブル作成
    public static function createSubTableForSearch($userId,$tagList)
    {
        //tags.idが
        //articleテーブルとarticle_tags,tagsを結合->参照元が論理削除されていないか確認するため
        $subTable = DB::table('article_tags')
        ->select('articles.*')
        ->leftJoin('articles','article_tags.article_id','=','articles.id')
        ->leftJoin('tags','article_tags.tag_id','=','tags.id')
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

        return $subTable;
    }

    // 削除済みかどうか確かめる
    public static function checkArticleDeleted($articleId)
    {
        //削除されていないなら 記事のデータが帰ってくるはず
        $article = Article::select('id')
        ->whereNull('deleted_at')
        ->where('id','=',$articleId)
        ->first();

        // $articleがnull->削除されている
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
        // true :自分のを覗こうとしている
        // false:他人のを覗こうとしている
        return ($article->original['user_id']) == $userId;
    }
}
