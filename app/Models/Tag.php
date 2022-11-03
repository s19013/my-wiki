<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

use DB;
use Carbon\Carbon;
use App\Tools\searchToolKit;

class Tag extends Model
{
    use HasFactory;
    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
    ];

    //新規タグ登録
    public static function store($userId,$tag)
    {
        // すでにあったらエラーを返す
        if (self::isAllreadyExists($userId,$tag) == true) {return false;}

        // かぶってなかったら保存する
        DB::transaction(function () use($userId,$tag){
            Tag::create([
                'user_id' => $userId,
                'name'    => $tag
            ]);
        });

        return true;
    }

    // タグ編集
    public static function updateTag($userId,$tagId,$name)
    {
        // 失敗 -> false
        if (self::isAllreadyExists($userId,$name) == true) {return false;}

        // 成功 -> true
        DB::transaction(function () use($tagId,$name){
            Tag::where('id','=',$tagId) -> update(['name' => $name]);
        });
        return true;
    }

    // delete
    public static function deleteTag($tagId)
    {
        DB::transaction(function () use($tagId){
            Tag::where('id','=',$tagId)->update(['deleted_at' => Carbon::now()]);
        });
    }

    //タグを検索する
    public static function search($userId,$tag)
    {
        // ツールを実体化
        $searchToolKit = new searchToolKit();

        // %と_をエスケープ
        $escaped = $searchToolKit->sqlEscape($tag);

        //and検索のために空白区切りでつくった配列を用意
        $wordListToSearch = $searchToolKit->preparationToAndSearch($escaped);

        //ログインユーザーのタグを探す
        $query = Tag::select('id','name','user_id')
        ->where('user_id','=',$userId);

        // tag名をlikeけんさく
        foreach($wordListToSearch as $word){
            $query->where('name','like',"%$word%");
        }

        $query->orderBy('name');

        return $query->get();
    }

    // ログインユーザーが既に登録していないか確かめる
    public static function  isAllreadyExists($userId,$tag)
    {
        return Tag::where('user_id','=',$userId)
        ->where('name','=',$tag)
        ->whereNull('deleted_at')
        ->exists();//existsでダブっていればtrue
    }
}
