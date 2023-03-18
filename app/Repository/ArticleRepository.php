<?php
namespace App\Repository;

use DB;
use Carbon\Carbon;
use App\Tools\searchToolKit;

use App\Models\Article;
use App\Models\ArticleTag;

use App\Tools\NullAvoidanceToolKit;

class ArticleRepository
{
    private $nullAvoidanceToolKit;

    public function __construct()
    {
        $this->nullAvoidanceToolKit = new NullAvoidanceToolKit();
    }
    //新規記事登録 登録した記事のIdを返す
    public function store($title,$body,$userId,$timezone)
    {
        $article = Article::create([
            // タイトルが産められてなかったら日時で埋める
            'user_id'  => $userId,
            'title'    => $this->nullAvoidanceToolKit->ifnull(
                $title,
                $this->nullAvoidanceToolKit->ifnull($timezone,Carbon::now("UTC"))
            ),
            'body'     => $this->nullAvoidanceToolKit->ifnull($body,''),
        ]);

        //紐付けられたタグをデータベースに登録するのに記事のidが必要なのでidだけを返す
        return $article->id;
    }

    //記事更新
    public function update($articleId,$title,$body,$timezone)
    {
        Article::where('id','=',$articleId)
            ->update([
                // タイトルが産められてなかったら日時で埋める
                'title'    => $this->nullAvoidanceToolKit->ifnull(
                    $title,
                    $this->nullAvoidanceToolKit->ifnull($timezone,Carbon::now("UTC"))
                ),
                'body'     => $this->nullAvoidanceToolKit->ifnull($body,''),
            ]);
    }

    //記事削除
    public function delete($articleId)
    {
        // 論理削除
        Article::where('id','=',$articleId)->delete();
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
    public function search(
        $userId,$keyword,$page,$tagList,$searchTarget,
        $searchQuantity=10,$sortType="updated_at_desc"
        )
    {
        // ツールを実体化
        $searchToolKit = new searchToolKit();

        // %と_をエスケープ
        $escaped = $searchToolKit->sqlEscape($keyword);

        //and検索のために空白区切りでつくった配列を用意
        $wordListToSearch = $searchToolKit->preparationToAndSearch($escaped);


        // このwhere句をわける部分別の関数にしようかな?

        //タグも検索する場合
        if (!empty($tagList)) {
            //副問合せのテーブルから選択
            $query = $this->createSubTableForSearch($userId,$tagList);
        } else {
            //タグ検索が不要な場合
            $query = DB::table("articles")
            ->select('id','title','user_id','count','created_at','updated_at')
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
        $lastPage = (int)ceil($total / $searchQuantity);

        // 一度にいくつ取ってくるか
        $query->limit($searchQuantity);

        //何件目から取得するか
        $query->offset($searchQuantity*($page-1));

        //ソート
        $query = $this->sort($query,$sortType);

        //検索
        // dd($query->toSql());
        return [
            'data' => $query->get(),
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
        $subTable = DB::table('articles')
        ->select('articles.id','articles.title','articles.user_id','articles.count','articles.created_at','articles.updated_at')
        ->leftJoin('article_tags','articles.id','=','article_tags.article_id')
        ->leftJoin('tags','article_tags.tag_id','=','tags.id')
        ->where('articles.user_id','=',$userId)
        // (a or b) and (c or d)みたいなsqlを書くには{}で囲む必要がある
        ->where(function($subTable) {
            //削除されてないものたちだけを取り出す
            $subTable->WhereNull('articles.deleted_at')
                    ->WhereNull('tags.deleted_at');
        })
        ->where(function($subTable) use($tagList) {
            // orなのは (a and b)みたいにすると
            // tag_idがaでありbであるという矛盾したデータを取ってくることになる
            // 詳しくはドキュメントみて
            foreach($tagList as $tag){
                $subTable->orWhere('article_tags.tag_id','=',$tag);
            }
        });

        //ここの説明は別のドキュメントで
        $subTable->groupBy('articles.id')
        ->having(DB::raw('count(*)'), '=', count($tagList));

        return $subTable;
    }

    // ソート
    public function sort($query,$type)
    {
        switch ($type) {
            case "updated_at_desc":
                return $query->orderBy('updated_at','desc');
                break;
            case "updated_at_asc":
                return $query->orderBy('updated_at');
                break;
            case "created_at_desc":
                return $query->orderBy('created_at','desc');
                break;
            case "created_at_asc":
                return $query->orderBy('created_at');
                break;
            case "title_desc":
                return $query->orderBy('title','desc');
                break;
            case "title_asc":
                return $query->orderBy('title');
                break;
            case "count_desc":
                return $query->orderBy('count','desc');
                break;
            case "count_asc":
                return $query->orderBy('count');
                break;
            case "random":
                return $query->inRandomOrder();
                break;
        }
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

    public function countUp($articleId)
    {
        $article = Article::where('id', $articleId)->first();
        $article->count += 1;
        $article->timestamps = false;
        $article->save();

        // incrementを使えば簡単だが､今回はこの動きでupdated_atが変化すると不都合が起きる
        // Article::where('id','=',$articleId)->increment('count');
    }
}


