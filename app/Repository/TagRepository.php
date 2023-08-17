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

        // 検索につかうようにエスケープしたり､空白区切りで配列化
        $wordListToSearch = $searchToolKit->preparationToAndSearch($keyword);

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

    //編集画面用の検索ツール
    public function searchInEdit(
            $userId,$keyword,$page,$searchQuantity=10,$sortType="name_asc"
        )
    {
        // ツールを実体化
        $searchToolKit = new searchToolKit();

        // 検索につかうようにエスケープしたり､空白区切りで配列化
        $wordListToSearch = $searchToolKit->preparationToAndSearch($keyword);

        //ログインユーザーのタグを探す
        $query = Tag::select('*')
        ->where('user_id','=',$userId);

        // 削除されてないタグを探す
        $query->WhereNull('deleted_at');

        // tag名をlikeけんさく
        foreach($wordListToSearch as $word){
            $query->where('name','like',"%$word%");
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

        return [
            'data' => $query->get(),
            'current_page'=> (int)$page,
            'last_page'   => $lastPage
        ];
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
            case "name_desc":
                return $query->orderBy('name','desc');
                break;
            case "name_asc":
                return $query->orderBy('name');
                break;
            case "count_desc":
                return $query->orderBy('count','desc');
                break;
            case "count_asc":
                return $query->orderBy('count');
                break;
        }
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

    public static function countUp($tagId){
        $tag = Tag::where('id', $tagId)->first();
        $tag->count += 1;
        $tag->timestamps = false;
        $tag->save();
    }

    public static function countDown($tagId)
    {
        $tag = Tag::where('id', $tagId)->first();
        if ($tag->count > 0) {
            $tag->count -= 1;
            $tag->timestamps = false;
            $tag->save();
        }
    }
}
