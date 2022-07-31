<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;
use Carbon\Carbon;

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
            $bookmark = BookMark::create([
                'user_id'  => $userId,
                'title'    => $title,
                'url'     => $url,
            ]);
            return $bookmark->id;
        });
    }

    public static function updateBookMark($bookmarkId,$title,$url)
    {
        // タイトルが産められてなかったら日時で埋める
        if ($title == '') { $title = Carbon::now() ;}

        DB::transaction(function () use($bookmarkId,$title,$url){
            BookMark::where('id','=',$bookmarkId)
            ->update([
                'title' => $title,
                'url'  => $url,
            ]);
        });
    }

    //viewAricle用に指定された記事だけを取ってくる
    public static function serveBookMark($bookmarkId)
    {
        return BookMark::select('id','title','url','category')
        ->Where('id','=',$bookmarkId)
        ->first();
    }

    public static function serveUserAllBookMark($userId,$category)
    {
        // まずはユーザーで絞った表を作る
        // whereで探すか副問合せで表を作るかどっちがよいか
        // 削除されていない記事を取って来る
        // 記事だからcategory = 2

        // whereの優先順位
        // 削除されてない､category=2,ログインユーザー = user_id

        $userTable = BookMark::select('id','title','url')
        -> WhereNull('deleted_at')
        -> where('category','=',$category)
        -> where('user_id','=',$userId)
        -> orderBy('updated_at', 'desc')
        -> paginate(5);
        return $userTable;


        // return BookMark::select('id','title','url')
        // ->leftJoin('article_tags','articles.id','=', 'article_tags.article_id')
        // ->leftJoin('tags','article_tags.tag_id', '=' ,'tags.id')
        // ->get();
    }

    public static function deleteBookMark($bookmarkId)
    {
        // 論理削除
        DB::transaction(function () use($bookmarkId){
            BookMark::where('id','=',$bookmarkId)
            ->update(['deleted_at' => date(Carbon::now())]);
        });
    }

    // 削除済みか確かめる
    public static function checkBookMarkDeleted($bookmarkId)
    {
        //削除されていないなら 記事のデータが帰ってくるはず
        //つまり帰り値がnullなら削除済みということ
        $bookmark = BookMark::select('id')
        ->whereNull('deleted_at')
        ->where('id','=',$bookmarkId)
        ->first();

        // 帰り値がnull->削除済みならtrue
        if ($bookmark == null) {return true;}
        else {return false;}
    }

    //他人の覗こうとしてないか確かめる
    public static function preventPeep($bookmarkId,$userId)
    {
        $bookmark = BookMark::select('user_id')
        ->whereNull('deleted_at')
        ->where('id','=',$bookmarkId)
        ->first();

        //記事に紐づけられているuserIdとログイン中のユーザーのidを比較する
        // falseなら他人のを覗こうとしている
        return ($bookmark->original['user_id']) == $userId ;
    }
}
