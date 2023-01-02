<?php
namespace App\Repository;

use DB;
use Carbon\Carbon;
use App\Tools\searchToolKit;

use App\Models\BookMark;
use App\Models\BookMarkTag;

use App\Tools\NullAvoidanceToolKit;

class BookMarkRepository
{
    private $nullAvoidanceToolKit;

    public function __construct()
    {
        $this->nullAvoidanceToolKit = new NullAvoidanceToolKit();
    }

    //新規ブックマーク作成 登録したブックマークのIDを返す
    public  function store($title,$url,$userId,$timezone="UTC")
    {
        $bookMark = BookMark::create([
            // タイトルが産められてなかったら日時で埋める
            'user_id'  => $userId,
            'title'    => $this->nullAvoidanceToolKit->ifnull($title,Carbon::now($timezone)),
            'url'      => $url,
        ]);
        return $bookMark->id;
    }

    //ブックマーク更新
    public  function update($bookMarkId,$title,$url,$timezone="UTC")
    {
        BookMark::where('id','=',$bookMarkId)
            ->update([
                'title' => $this->nullAvoidanceToolKit->ifnull($title,Carbon::now($timezone)),
                'url'   => $url,
            ]);
    }

    //ブックマーク削除
    public  function delete($bookMarkId)
    {
        // 論理削除
        BookMark::where('id','=',$bookMarkId)->delete();
    }

    //指定された記事だけを取ってくる
    //編集画面で使う
    public  function serve($bookMarkId)
    {
        return BookMark::select('*')
        ->Where('id','=',$bookMarkId)
        ->first();
    }

    // urlとユーザーからidを探す
    // 更新でurlを変更した時に使う
    public  function serveBookMarkId($url,$userId)
    {
        $temp = BookMark::select("id")
        ->where('user_id','=',$userId)
        ->where('url','=',$url)
        ->whereNull('deleted_at')
        ->first();


        if (is_null($temp)) {return null;}

        return $temp->id;
    }

    public  function search($userId,$keyword,$page,$tagList,$searchTarget)
    {
        // ツールを実体化
        $searchToolKit = new searchToolKit();

        //一度にとってくる数
        $parPage = (int)config('app.parPage');

        // %と_をエスケープ
        $escaped = $searchToolKit->sqlEscape($keyword);
        //and検索のために空白区切りでつくった配列を用意
        $wordListToSearch = $searchToolKit->preparationToAndSearch($escaped);

        //タグも検索する場合
        if (!empty($tagList)) {

            //副問合せのテーブルから選択
            $subTable = $this->createSubTableForSearch($userId,$tagList);
            $query = DB::table($subTable,"book_marks");

            // 副問合せを使わないver(個人的にわかりにくいと思う)
            // $query = $this->createSubTableForSearch($userId,$tagList);
        } else {
            //タグ検索が不要な場合
            $query = DB::table("book_marks")
            ->select('*')
            ->where('user_id','=',$userId)
            ->whereNull('deleted_at');
        }

        // title名だけでlike検索する場合
        if ($searchTarget == "title") {
            foreach($wordListToSearch as $word){ $query->where('title','like',"%$word%"); }
        }

        // urlだけでlike検索する場合
        if ($searchTarget == "url") {
            foreach($wordListToSearch as $word){ $query->where('url','like',"%$word%"); }
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
        // dd($query->toSql());
        return [
            'data' => $query->get(),
            'current_page'=> (int)$page,
            'last_page'   => $lastPage
        ];
    }

    //検索時のサブテーブル作成
    public  function createSubTableForSearch($userId,$tagList)
    {
        //articleテーブルとarticle_tags,tagsを結合
        $subTable = BookMark::select('book_marks.*')
        ->leftjoin('book_mark_tags','book_marks.id','=','book_mark_tags.book_mark_id')
        ->leftjoin('tags','book_mark_tags.tag_id','=','tags.id')
        ->where('book_marks.user_id','=',$userId)
        // (a or b) and (c or d)みたいなsqlを書くには{}で囲む必要がある
        ->where(function($subTable) {
            //削除されてないものたちだけを取り出す
            $subTable->WhereNull('book_marks.deleted_at')
                    ->WhereNull('tags.deleted_at');
        })
        ->where(function($subTable) use($tagList) {
            // orなのは (a and b)みたいにすると
            // tag_idがaでありbであるという矛盾したデータを取ってくることになる
            // 詳しくはドキュメントみて
            foreach($tagList as $tag){
                $subTable->orWhere('book_mark_tags.tag_id','=',$tag);
            }
        });

        $subTable->groupBy('book_marks.id')
        ->having(DB::raw('count(*)'), '=', count($tagList));

        return $subTable;
    }

    // 削除済みか確かめる
    public  function isDeleted($bookMarkId)
    {
        //削除されていないなら 記事のデータが帰ってくるはず
        //つまり帰り値がnullなら削除済みということ
        $bookMark = BookMark::select('id')
        ->whereNull('deleted_at')
        ->where('id','=',$bookMarkId)
        ->first();

        // 帰り値がnull->削除済みならtrue
        if ($bookMark == null) {return true;}
        else {return false;}
    }

    //ログインユーザーのブックマークかどうか確認する
    public  function isSameUser($bookMarkId,$userId)
    {
        $bookMark = BookMark::select('user_id')
        ->whereNull('deleted_at')
        ->where('id','=',$bookMarkId)
        ->first();

        //ブックマークに紐づけられているuserIdとログイン中のユーザーのidを比較する
        // true :自分のを覗こうとしている
        // false:他人のを覗こうとしている
        return ($bookMark->user_id) == $userId ;
    }

    // ログインユーザーが既に登録していないか確かめる
    public  function  isAllreadyExists($userId,$url)
    {
        return $url_exists = BookMark::where('user_id','=',$userId)
        ->where('url','=',$url)
        ->whereNull('deleted_at')
        ->exists();//existsでダブっていればtrue
    }
}
