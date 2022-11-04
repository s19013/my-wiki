<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Tag;
use App\Repository\TagRepository;
use Auth;

class TagController extends Controller
{

    private $TagRepository;

    public function __construct(TagRepository $tagRepository)
    {
        $this->tagRepository = $tagRepository;
    }

    //新規タグ登録
    public function store(Request $request)
    {
        // CSRFトークンを再生成して、二重送信対策
        //$request->session()->regenerateToken();

        $result = $this->tagRepository->store(
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

    public function update(Request $request)
    {
        // CSRFトークンを再生成して、二重送信対策
        //$request->session()->regenerateToken();

        $result = $this->tagRepository->update(
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

    public function delete()
    {
        $this->tagRepository->delete(tagId :$request->id);
    }



    //タグ検索
    public function search(Request $request)
    {
        return $this->tagRepository->search(
            userId:Auth::id(),
            tag   :$request->tag
        );
    }
}
