<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use DB;
use Carbon\Carbon;
use App\Http\Controllers\searchToolKit;

class BookMark extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'title',
        'url',
    ];

    //新規ブックマーク作成
    public static function storeBookMark($title,$url,$userId)
    {
        // タイトルが産められてなかったら日時で埋める
        if ($title == '') { $title = Carbon::now() ;}

        return DB::transaction(function () use($title,$url,$userId){
            $bookMark = BookMark::create([
                'user_id'  => $userId,
                'title'    => $title,
                'url'     => $url,
            ]);
            return $bookMark->id;
        });
    }

    //ブックマーク更新
    public static function updateBookMark($bookMarkId,$title,$url)
    {
        // タイトルが産められてなかったら日時で埋める
        if ($title == '') { $title = Carbon::now() ;}

        DB::transaction(function () use($bookMarkId,$title,$url){
            BookMark::where('id','=',$bookMarkId)
            ->update([
                'title' => $title,
                'url'  => $url,
            ]);
        });
    }

    //ブックマーク削除
    public static function deleteBookMark($bookMarkId)
    {
        // 論理削除
        DB::transaction(function () use($bookMarkId){
            BookMark::where('id','=',$bookMarkId)
            // ->update(['deleted_at' => date(Carbon::now())]);
            ->update(['deleted_at' => Carbon::now()]);
        });
    }

    //指定された記事だけを取ってくる
    //編集画面で使う
    public static function serveBookMark($bookMarkId)
    {
        return BookMark::select('id','title','url')
        ->Where('id','=',$bookMarkId)
        ->first();
    }

    public static function searchBookMark($userId,$bookMarkToSearch,$currentPage,$tagList)
    {
        //ページネーションをする

        //一度にとってくる数
        $parPage = (int)config('app.parPage');

        // %と_をエスケープ
        $escaped = searchToolKit::sqlEscape($bookMarkToSearch);
        //and検索のために空白区切りでつくった配列を用意
        $wordListToSearch = searchToolKit::preparationToAndSearch($escaped);

        //タグも検索する場合
        if (!empty($tagList)) {

            $subTable = self::createSubTableForSearch($userId,$tagList);

            //副問合せのテーブルから選択
            $query = DB::table($subTable,'sub')
            ->select('*');
        } else {
            $query = BookMark::select('*')
            ->where('user_id','=',$userId)
            ->whereNull('deleted_at');
        }

        // title名をlikeけんさく
        foreach($wordListToSearch as $word){
            $query->where('title','like',"%$word%");
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
                "bookMarkList" => $searchResults,
                "pageCount"    => $pageCount
            ],
            200
        );
    }

    //検索時のサブテーブル作成
    public static function createSubTableForSearch($userId,$tagList)
    {
        //articleテーブルとarticle_tags,tagsを結合
        $subTable = DB::table('book_mark_tags')
        ->select('book_marks.*')
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
    public static function checkBookMarkDeleted($bookMarkId)
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

    //他人の覗こうとしてないか確かめる
    public static function preventPeep($bookMarkId,$userId)
    {
        $bookMark = BookMark::select('user_id')
        ->whereNull('deleted_at')
        ->where('id','=',$bookMarkId)
        ->first();

        //記事に紐づけられているuserIdとログイン中のユーザーのidを比較する
        // falseなら他人のを覗こうとしている
        return ($bookMark->original['user_id']) == $userId ;
    }

    // ログインユーザーが既に登録していないか確かめる
    public static function  isAllreadyExists($userId,$url)
    {
        return $url_exists = BookMark::where('user_id','=',$userId)
        ->where('url','=',$url)
        ->whereNull('deleted_at')
        ->exists();//existsでダブっていればtrue
    }
}
