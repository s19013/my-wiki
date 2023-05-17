<?php
namespace App\Repository;

use DB;
use Carbon\Carbon;
use App\Tools\searchToolKit;

use App\Models\BookMark;
use App\Models\BookMarkTag;

class BookMarkRepository
{
    //新規ブックマーク作成 登録したブックマークのIDを返す
    public  function store($title,$url,$userId,$timezone)
    {
        $bookMark = BookMark::create([
            // タイトルが産められてなかったら日時で埋める
            'user_id'  => $userId,
            'title'    => \NullAvoidance::ifnull(
                $title,
                Carbon::now($timezone).
                "(".\NullAvoidance::ifnull($timezone,"UTC").")"
            ),
            'url'      => $url,
        ]);
        return $bookMark->id;
    }

    //ブックマーク更新
    public  function update($bookMarkId,$title,$url,$timezone)
    {
        BookMark::where('id','=',$bookMarkId)
            ->update([
                'title'    => \NullAvoidance::ifnull(
                    $title,
                    Carbon::now($timezone).
                    "(".\NullAvoidance::ifnull($timezone,"UTC").")"
                ),
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

    public  function search(
        $userId,$keyword,$page,$tagList,$searchTarget,
        $searchQuantity=10,$sortType="updated_at_desc",
        $isSearchUntagged=false
        )
    {
        // ツールを実体化
        $searchToolKit = new searchToolKit();

        // %と_をエスケープ
        $escaped = $searchToolKit->sqlEscape($keyword);
        //and検索のために空白区切りでつくった配列を用意
        $wordListToSearch = $searchToolKit->preparationToAndSearch($escaped);

        //タグも検索する場合
        if (!empty($tagList)) {

            //副問合せのテーブルから選択
            $subTable = $this->searchByTag($userId,$tagList);
            $query = DB::table($subTable,"book_marks");

            // 副問合せを使わないver(個人的にわかりにくいと思う)
            // $query = $this->searchByTag($userId,$tagList);
        } else if ($isSearchUntagged == true) {
            // タグがついてないのだけ検索
            $query = $this->searchUntagged($userId);
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

    //タグを使って検索する時に使う関数
    public  function searchByTag($userId,$tagList)
    {
        //book_markテーブルとbook_mark_tags,tagsを結合
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

    //タグを使って検索する時に使う関数
    public function searchUntagged($userId)
    {
        //tags.idが
        //book_markテーブルとbook_mark_tags,tagsを結合->参照元が論理削除されていないか確認するため

        $subTable = DB::table('book_marks')
        ->select('book_marks.id','book_marks.title','book_marks.user_id','book_marks.count','book_marks.created_at','book_marks.updated_at')
        ->leftJoin('book_mark_tags','book_marks.id','=','book_mark_tags.book_mark_id')
        ->leftJoin('tags','book_mark_tags.tag_id','=','tags.id')
        ->where('book_marks.user_id','=',$userId)
        // (a or b) and (c or d)みたいなsqlを書くには{}で囲む必要がある
        ->where(function($subTable) {
            //削除されてないものたちだけを取り出す
            $subTable->WhereNull('book_marks.deleted_at')
                     ->WhereNull('tags.deleted_at');
        })
        ->whereNull('book_mark_tags.tag_id');

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

    public function countUp($bookMarkId)
    {
        $bookMark = BookMark::where('id', $bookMarkId)->first();
        $bookMark->count += 1;
        $bookMark->timestamps = false;
        $bookMark->save();

        // incrementを使えば簡単だが､今回はこの動きでupdated_atが変化すると不都合が起きる
    }
}
