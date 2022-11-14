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
    public  function store($title,$url,$userId)
    {
        // タイトルが産められてなかったら日時で埋める
        if ($title == '') { $title = Carbon::now() ;}

        $bookMark = BookMark::create([
            'user_id'  => $userId,
            'title'    => $title,
            'url'     => $url,
        ]);
        return $bookMark->id;
    }

    //ブックマーク更新
    public  function update($bookMarkId,$title,$url)
    {
        // タイトルが産められてなかったら日時で埋める
        if ($title == '') { $title = Carbon::now() ;}

        // 登録済みかどうかを確認していない->ちょっと危ないかも
        // 登録してあるurlとidが一致してない->既に登録してあると判断
        //
        // 1:google.com <-既にあるやつ
        // 上のidと受け取ったbookmarkidが一致しない->ダブり



        BookMark::where('id','=',$bookMarkId)
            ->update([
                'title' => $title,
                'url'  => $url,
            ]);
    }

    //ブックマーク削除
    public  function delete($bookMarkId)
    {
        // 論理削除
        BookMark::where('id','=',$bookMarkId)
        ->update(['deleted_at' => Carbon::now()]);
    }

    //指定された記事だけを取ってくる
    //編集画面で使う
    public  function serve($bookMarkId)
    {
        return BookMark::select('*')
        ->Where('id','=',$bookMarkId)
        ->first();
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
            $query = $this->createSubTableForSearch($userId,$tagList);

        } else {
            //タグ検索が不要な場合
            $query = BookMark::select('*')
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
        // dd($query->get());
        return [
            'data' => $query->get()->toArray(),
            'current_page'=> (int)$page,
            'last_page'   => $lastPage
        ];
    }

    //検索時のサブテーブル作成
    public  function createSubTableForSearch($userId,$tagList)
    {
        //articleテーブルとarticle_tags,tagsを結合
        $subTable = BookMarkTag::select('book_marks.*')
        ->leftjoin('book_marks','book_mark_tags.book_mark_id','=','book_marks.id')
        ->leftjoin('tags','book_mark_tags.tag_id','=','tags.id')
        ->where('book_marks.user_id','=',$userId)
        ->where(function($subTable) {
            $subTable->WhereNull('book_marks.deleted_at')
                     ->WhereNull('book_mark_tags.deleted_at')
                     ->WhereNull('tags.deleted_at');
        })
        ->where(function($subTable) use($tagList) {
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
