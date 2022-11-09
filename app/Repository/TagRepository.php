<?php
namespace App\Repository;

use DB;
use Carbon\Carbon;
use App\Tools\searchToolKit;

use App\Models\Tag;

class TagRepository
{

    //新規タグ登録
    public function store($userId,$tag)
    {
        // すでにあったらエラーを返す
        if (self::isAllreadyExists($userId,$tag) == true) {return false;}

        // かぶってなかったら保存する
        Tag::create([
            'user_id' => $userId,
            'name'    => $tag
        ]);

        return true;
    }

    // タグ編集
    public function update($userId,$tagId,$name)
    {
        // 失敗 -> false
        if (self::isAllreadyExists($userId,$name) == true) {return false;}

        // 成功 -> true
        Tag::where('id','=',$tagId) -> update(['name' => $name]);
        return true;
    }

    // delete
    public function delete($tagId)
    {
        Tag::where('id','=',$tagId)->update(['deleted_at' => Carbon::now()]);
    }

    //タグを検索する
    public function search($userId,$tag)
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
    public function  isAllreadyExists($userId,$tag)
    {
        return Tag::where('user_id','=',$userId)
        ->where('name','=',$tag)
        ->whereNull('deleted_at')
        ->exists();//existsでダブっていればtrue
    }

    //ログインユーザーのタグかどうか確認する
    public  function isSameUser($tagId,$userId)
    {
        $tag = Tag::select('user_id')
        ->whereNull('deleted_at')
        ->where('id','=',$tagId)
        ->first();

        //タグに紐づけられているuserIdとログイン中のユーザーのidを比較する
        // true :自分のを覗こうとしている
        // false:他人のを覗こうとしている
        return ($tag->user_id) == $userId ;
    }
}
