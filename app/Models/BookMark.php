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

    public static function deleteBookMark($bookMarkId)
    {
        // 論理削除
        DB::transaction(function () use($bookMarkId){
            BookMark::where('id','=',$bookMarkId)
            ->update(['deleted_at' => date(Carbon::now())]);
        });
    }

    //viewAricle用に指定された記事だけを取ってくる
    public static function serveBookMark($bookMarkId)
    {
        return BookMark::select('id','title','url')
        ->Where('id','=',$bookMarkId)
        ->first();
    }

    public static function serveUserAllBookMark($userId)
    {
        // まずはユーザーで絞った表を作る
        // whereで探すか副問合せで表を作るかどっちがよいか
        // 削除されていない記事を取って来る
        // 記事だからcategory = 2

        // whereの優先順位
        // 削除されてない､category=2,ログインユーザー = user_id

        $userTable = BookMark::select('id','title','url')
        -> WhereNull('deleted_at')
        -> where('user_id','=',$userId)
        -> orderBy('updated_at', 'desc')
        -> paginate(5);
        return $userTable;


        // return BookMark::select('id','title','url')
        // ->leftJoin('article_tags','articles.id','=', 'article_tags.article_id')
        // ->leftJoin('tags','article_tags.tag_id', '=' ,'tags.id')
        // ->get();
    }

    public static function searchBookMark($userId,$bookMarkToSearch,$currentPage,$tagList)
    {
        //一度にとってくる数
        $parPage = 1;

        // %と_をエスケープ
        $escaped = searchToolKit::sqlEscape($bookMarkToSearch);
        //and検索のために空白区切りでつくった配列を用意
        $wordListToSearch = searchToolKit::preparationToAndSearch($escaped);

        //タグも検索する場合
        if (!empty($tagList)) {
            //book_markテーブルとbook_mark_tagsを結合
            $subTable = DB::table('book_mark_tags')
            ->select('book_marks.id','book_marks.title','book_marks.url')
            ->leftJoin('book_marks','book_marks.id','=','book_mark_tags.book_mark_id')
            ->where('book_marks.user_id','=',$userId)
            ->WhereNull('book_marks.deleted_at')
            ->WhereNull('book_mark_tags.deleted_at');

            $isFirst = true;
            foreach($tagList as $tag){
                // 最初だけand検索
                if ($isFirst == true) {
                    $subTable->Where('book_mark_tags.tag_id','=',$tag);
                    $isFirst = false;
                }
                $subTable->orWhere('book_mark_tags.tag_id','=',$tag);
            }

            $subTable->groupBy('book_marks.id')
            ->having(DB::raw('count(*)'), '=', count($tagList));

            //副問合せのテーブルから選択
            $query = DB::table($subTable,'sub')
            ->select('sub.id as id','sub.title as title','sub.url as url');
        } else {
            $query = BookMark::select('id','title')
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
}
