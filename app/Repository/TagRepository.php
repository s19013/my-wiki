<?php
namespace App\Repository;

use DB;
use Carbon\Carbon;
use App\Tools\searchToolKit;

use App\Models\Tag;

class TagRepository
{

    //新規タグ登録
    public function store($userId,$name)
    {
        Tag::create([
            'user_id' => $userId,
            'name'    => $name
        ]);
    }

    // タグ編集
    public function update($userId,$tagId,$name)
    {
        Tag::where('id','=',$tagId) -> update(['name' => $name]);
    }

    // delete
    public function delete($tagId)
    {
        Tag::where('id','=',$tagId)->delete();
    }

    //タグを検索する
    public function search($userId,$keyword)
    {
        // ツールを実体化
        $searchToolKit = new searchToolKit();

        // %と_をエスケープ
        $escaped = $searchToolKit->sqlEscape($keyword);

        //and検索のために空白区切りでつくった配列を用意
        $wordListToSearch = $searchToolKit->preparationToAndSearch($escaped);

        //ログインユーザーのタグを探す
        $query = Tag::select('id','name','user_id')
        ->where('user_id','=',$userId);

        // 削除されてないタグを探す
        $query->WhereNull('deleted_at');

        // tag名をlikeけんさく
        foreach($wordListToSearch as $word){
            $query->where('name','like',"%$word%");
        }

        $query->orderBy('name');

        return $query->get();
    }

    // urlとユーザーからidを探す
    // 更新でurlを変更した時に使う
    public  function serveTagId($name,$userId)
    {
        $temp = Tag::select("id")
        ->where('user_id','=',$userId)
        ->where('name','=',$name)
        ->whereNull('deleted_at')
        ->first();


        if (is_null($temp)) {return null;}

        return $temp->id;
    }

    // idからタグの名前などを取得する
    public function findFromId($userId,$tagId)
    {
        //ログインユーザーのタグを探す
        $query = Tag::select('id','name')
        ->where('user_id','=',$userId);

        // 削除されてないタグを探す
        $query->WhereNull('deleted_at');

        $result = $query->find($tagId);
        // null(削除されたタグ)の場合
        if (is_null($result)) {
            return (object)[
                'id'   => null,
                'name' => null
            ];
        };
        return $result;
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
