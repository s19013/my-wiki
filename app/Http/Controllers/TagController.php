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

        // すでに登録していた
        if ($result == false) {
            return response()->json(
                ["message" => "already exists"],
                400
            );
        }

        // 登録できた
        return response()->json(
            ["message" => "stored"],
            200
        );
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
