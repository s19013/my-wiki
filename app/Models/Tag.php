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

        $allTag = Tag::select('id','name')
        ->where('user_id','=',$id)
        ->get();

        return $allTag;
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
}
