<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

use DB;

class Tag extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'user_id',
    ];

    public static function getUserAllTag($id)
    {

        return Tag::select('id','name')
        ->where('user_id','=',$id)
        ->get();
    }

    public static function store($userId,$tag)
    {
        // すでにdbに存在していないか確かめる
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

        // 保存する
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
}
