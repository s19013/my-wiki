<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

use DB;
use App\Http\Controllers\searchToolKit;

class Tag extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'user_id',
    ];


    public static function getUserAllTag($userId)
    {
        return Tag::select('id','name')
        ->where('user_id','=',$userId)
        ->orderBy('name')
        ->get();
    }

    //新規タグ登録
    public static function store($userId,$tag)
    {
        // ログインユーザーが既に登録していないか確かめる
        $tag_exists = Tag::where('user_id','=',$userId)
        ->where('name','=',$tag)
        ->exists();//existsでダブっていればtrue

        // すでにあったらエラーを返す
        if ($tag_exists == true) {
            return response()->json(
                ["message" => "already exists"],
                400
            );
        }

        // かぶってなかったら保存する
        DB::transaction(function () use($userId,$tag){
            Tag::create([
                'user_id' => $userId,
                'name'    => $tag
            ]);
        });

        return response()->json(
            ["message" => "stored"],
            200
        );
    }

    public static function serveAddedTag($userId)
    {
        // 一番新しく追加したタグだけを取り出す
        return Tag::select('id','name')
        ->where('user_id','=',$userId)
        ->orderBy('created_at', 'desc')
        ->first();
    }

    //タグを検索する
    public static function search($userId,$tag)
    {
        // %と_をエスケープ
        $escaped = searchToolKit::sqlEscape($tag);

        //and検索のために空白区切りでつくった配列を用意
        $wordListToSearch = searchToolKit::preparationToAndSearch($escaped);

        //ログインユーザーのタグを探す
        $query = Tag::select('id','name')
        ->where('user_id','=',$userId);

        // tag名をlikeけんさく
        foreach($wordListToSearch as $word){
            $query->where('name','like',"%$word%");
        }

        $query->orderBy('name');

        return $query->get();
    }
}
