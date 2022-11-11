<?php
namespace App\Repository;

use DB;
use Carbon\Carbon;
use App\Tools\searchToolKit;

use App\Models\Article;
use App\Models\ArticleTag;

class ArticleRepository
{
    //新規記事登録 登録した記事のIdを返す
    public function store($title,$body,$userId)
    {
        // タイトルが産められてなかったら日時で埋める
        if ($title == '') { $title = Carbon::now() ;}

        $article = Article::create([
            'user_id'  => $userId,
            'title'    => $title,
            'body'     => $body,
        ]);

        //紐付けられたタグをデータベースに登録するのに記事のidが必要なのでidだけを返す
        return $article->id;
    }

    //記事更新
    public function update($articleId,$title,$body)
    {
        // タイトルが産められてなかったら日時で埋める
        if ($title == '') { $title = Carbon::now() ;}

        Article::where('id','=',$articleId)
            ->update([
                'title' => $title,
                'body'  => $body,
            ]);
    }

    //記事削除
    public function delete($articleId)
    {
        // 論理削除
        Article::where('id','=',$articleId)
        ->update(['deleted_at' => Carbon::now()]);
    }

    //指定された記事だけを取ってくる
    //閲覧画面,編集画面で使う
    public function serve($articleId)
    {
        return Article::select('*')
        ->Where('id','=',$articleId)
        ->first();
    }

    //検索する数
    public function search($userId,$keyword,$page,$tagList,$searchTarget)
    {
        // ツールを実体化
        $searchToolKit = new searchToolKit();

        //一度にとってくる数
        $parPage = (int)config('app.parPage');

        // %と_をエスケープ
        $escaped = $searchToolKit->sqlEscape($keyword);

        //and検索のために空白区切りでつくった配列を用意
        $wordListToSearch = $searchToolKit->preparationToAndSearch($escaped);


        // このwhere句をわける部分別の関数にしようかな?

        //タグも検索する場合
        if (!empty($tagList)) {
            $subTable = self::createSubTableForSearch($userId,$tagList);

            //副問合せのテーブルから選択
            $query = $this->createSubTableForSearch($userId,$tagList);
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
        $total = (int)$query->count();

        //ページ数計算(最後は何ページ目か)
        $lastPage = (int)ceil($total / $parPage);

        // 一度にいくつ取ってくるか
        $query->limit($parPage);

        //何件目から取得するか
        $query->offset($parPage*($page-1));

        //ソート
        $sort = $query->orderBy('updated_at','desc');

        //検索
        // dd($query->get());
        return [
            'data' => $query->get()->toArray(),
            'current_page'=> (int)$page,
            'last_page'   => $lastPage
        ];
    }

    //検索時のサブテーブル作成
    public function createSubTableForSearch($userId,$tagList)
    {
        //tags.idが
        //articleテーブルとarticle_tags,tagsを結合->参照元が論理削除されていないか確認するため

        // なぜarticle_tagsをメインにしているのか
        // -> article_tagsが2つを外部参照しているから
        $subTable = ArticleTag::select('articles.*')
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
    public function isDeleted($articleId)
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

    //ログインユーザーの記事かどうか確認する
    public function isSameUser($articleId,$userId)
    {
        $article = Article::select('user_id')
        ->whereNull('deleted_at')
        ->where('id','=',$articleId)
        ->first();

        // dd(printf($article));

        //記事に紐づけられているuserIdとログイン中のユーザーのidを比較する
        // true :自分のを覗こうとしている
        // false:他人のを覗こうとしている
        return ($article->user_id) == $userId;
    }
}


