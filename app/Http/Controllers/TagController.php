<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Tag;
use Auth;

class TagController extends Controller
{
    //新規タグ登録
    public function tagStore(Request $request)
    {
        // CSRFトークンを再生成して、二重送信対策
        $request->session()->regenerateToken();

        $result = Tag::store(
            userId:Auth::id(),
            tag   :$request->tag,
        );

        // 登録できた
        if ($result) {
            return response()->json(
                ["message" => "stored"],
                200
            );
        }

        // すでに登録していた
        return response()->json(
            ["message" => "already exists"],
            400
        );
    }

    public function tagUpdate(Request $request)
    {
        // CSRFトークンを再生成して、二重送信対策
        $request->session()->regenerateToken();

        $result = Tag::updateTag(
            userId:Auth::id(),
            tagId :$request->id,
            name  :$request->name
        );


        // 更新できた
        if ($result) {
            return response()->json(
                ["message" => "updated"],
                200
            );
        }

        // 既に登録している名前と被った
        return response()->json(
            ["message" => "already exists"],
            400
        );
    }

    public function tagDelete()
    {
        Tag::delete(tagId :$request->id);
    }



    //タグ検索
    public function tagSearch(Request $request)
    {
        return Tag::search(
            userId:Auth::id(),
            tag   :$request->tag
        );
    }
}
